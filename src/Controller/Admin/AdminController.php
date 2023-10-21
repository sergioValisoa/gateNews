<?php

namespace App\Controller\Admin;

use App\Service\MetierManagerBundle\Utils\ServiceName;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Message;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\GnPostCommentRepository;
use App\Repository\GnPostRepository;
use App\Repository\GnPostCategoryRepository;
use App\Repository\GnUserRepository;
use App\Repository\GnCountryRepository;
use App\Repository\NousContacterRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\GnPostComment;
use App\Entity\NousContacter;
class AdminController extends AbstractController
{
  protected $message;
    protected $nbrMessage;
    protected $nbrComment;
    protected $nbrPost;
    public function __construct(NousContacterRepository $nousContacter,GnPostCommentRepository $comments,GnPostRepository $posts)
    {
      $this->message = $nousContacter->findBy(['isView' => 0,'isDeleted' => 0],['createdAt'  => 'DESC'],3);
      $this->nbrComment = $comments->count(['isApprouved' => 0, 'isDeleted' => 0]);
      $this->nbrMessage = $nousContacter->count(['isDeleted' => 0,'isView' => 0]);
      $this->nbrPost = $posts->count(['isApprouved' => 0, 'isDeleted' => 0]);    
      
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [ // on merge le tableau des services par defaut avec notre tableau personnalisé
            'gn.manager.account' => 'App\Service\MetierManagerBundle\Metier\GnAccount\ServiceMetierGnAccount',
            'gn.manager.utils' => 'App\Service\MetierManagerBundle\Metier\Utils\ServiceMetierUtils',
            'gn.manager.user.upload' => 'App\Service\MetierManagerBundle\Metier\Upload\UploadManager',
        ]);
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function index(Request $request,GnUserRepository $users,GnPostRepository $posts,GnCountryRepository $pays,GnPostCategoryRepository $categories): Response
    {

        $posts = $posts->count(['isDeleted' => 0]);
        $pays = $pays->count([]);
        $user = $users->count(['isDeleted' => 0]);

        $journalists = count($users->getJournalist());

        $categories = $categories->count(['isDeleted' => 0]);
        return $this->render('admin/home.html.twig',[ 'message' => $this->message,'nbr_comm_non_approuve' => $this->nbrComment,'nbr_post_non_approuve' => $this->nbrPost,'nbr_message_non_lu' => $this->nbrMessage,'nbr_post' => $posts,'nbr_pays' => $pays,'nbr_user' => $user, 'nbr_journalist' => $journalists ,'nbr_category' => $categories]);


    }

    /**
     * @Route("/approuve_comment", name="approuve_comment")
     */
   public function adminComment(GnPostCommentRepository $comments)
   {
   		$comments = $comments->findBy(['isApprouved' => 0, 'isDeleted' => 0]);
   		return $this->render('admin/notification/commentaire.html.twig',['comments' => $comments , 'message' => $this->message,'nbr_comm_non_approuve' => $this->nbrComment,'nbr_post_non_approuve' => $this->nbrPost,'nbr_message_non_lu' => $this->nbrMessage]);
   }

   /**
     * @Route("/approuved_comment/{id<[0-9]+>}", name="approuved_comment")
     */
   public function approuveComment(GnPostComment $comments,GnPostCommentRepository $commentrepo, EntityManagerInterface $manager,GnPostrepository $post,$id,Request $request)
   {
   		
   		$commentrepo = $commentrepo->find($id);
      $comments->setIsApprouved(1);
      $manager->flush();
      $msg  = null;
   		$msg = $request->getSession()->getFlashBag()->add('success_comment','commentaires approuver avec succès');

            return $this->redirectToRoute('readComment',['id' => $commentrepo->getPost()->getId(),'msg' => $msg]);
   }

   /**
     * @Route("/approuve_post", name="approuve_post")
     */
   public function adminPost(GnPostRepository $post)
   {
      $post = $post->findBy(['isApprouved' => 0, 'isDeleted' => 0], ['postCreatedAt' => 'DESC']);
      return $this->render('admin/notification/article.html.twig',['posts' => $post , 'message' => $this->message,'nbr_comm_non_approuve' => $this->nbrComment,'nbr_post_non_approuve' => $this->nbrPost,'nbr_message_non_lu' => $this->nbrMessage]);
   }

   /**
     * @Route("/approuved_post/{id<[0-9]+>}", name="approuved_post")
     */
   public function approuvePost(GnPostRepository $post, $id , EntityManagerInterface $manager,Request $request)
   {
      $post = $post->find($id);
      $post->setIsApprouved(1);
      $manager->flush();
      $msg = null;
      $msg = $request->getSession()->getFlashBag()->add('success_post','articles approuver avec succès');
            
              return $this->redirectToRoute('list_post',['msg' => $msg]);
   }

   /**
     * @Route("/message_nousContacter", name="msgNousContacter")
     */
   public function msgNousContacter(NousContacterRepository $nousContacter,Request $request)
   {
      if($request->getSession()->getFlashBag()->has('success_message')){
        $msg = $request->getSession()->getFlashBag()->get('success_message')[0];
      }
      else{
         $msg = null;
      }
      $messages = $nousContacter->findBy(['isDeleted' => 0]);
      return $this->render('admin/notification/message.html.twig',['messages' => $messages ,'message' => $this->message,'nbr_comm_non_approuve' => $this->nbrComment,'nbr_post_non_approuve' => $this->nbrPost,'nbr_message_non_lu' => $this->nbrMessage,'msg' => $msg]);
   }

    /**
     * @Route("/send-mail-to-users", name="send_mail_to_users")
     */
    public function sendMailsToUsers(MailerInterface $mailer, Request $request)
    {
        if($request->getSession()->getFlashBag()->has('success_message')){
            $msg = $request->getSession()->getFlashBag()->get('success_message')[0];
        }
        else{
            $msg = null;
        }
        // Récupérer manager
        $account_manager = $this->get(ServiceName::SRV_METIER_ACCOUNT);
        //dd($account_manager->getAllUsersEmail());
        foreach ($account_manager->getAllUsersEmail() as $key => $user) {
            //
            $email = (new TemplatedEmail())
                ->from(new Address('admin@gate-news.com', 'Fetraharijaona RAMAHANDRISOA'))
                ->to($user['userEmail'])
                ->subject('Votre mot de passe sur le nouveau site web de Gate Of Africa')
                ->htmlTemplate('reset_password/email_info.html.twig')
                ->context([
                    'expiration_date' => new \DateTime('+7 days'),
                    'login' => $user['userEmail'],
                    'fullName' => $user['userFullName']
                ])
            ;

            try {
                if ($user['userEmail'] == 'fetrahar@gmail.com') {
                    $mailer->send($email);
                }
            } catch (TransportExceptionInterface $e) {
                // some error prevented the email sending; display an
                // error message or try to resend the message
                die($e->getMessage());
            }
        }
        return $this->render('admin/notification/message.html.twig',['messages' => [] ,'message' => '', 'nbr_comm_non_approuve' => 0, 'nbr_post_non_approuve' => 0, 'nbr_message_non_lu' => 0, 'msg' => $msg]);
    }

   /**
     * @Route("/messageNousContacter/{id<[0-9]+>}", name="msgNousContacterView")
     */
   public function msgNousContacterView(NousContacter $nousContacter, EntityManagerInterface $em)
   {
      $nousContacter->setIsView(1);
      $em->persist($nousContacter);
      $em->flush();
      return $this->render('admin/notification/message_view.html.twig',['messages' => $nousContacter ,'message' => $this->message,'nbr_comm_non_approuve' => $this->nbrComment,'nbr_post_non_approuve' => $this->nbrPost,'nbr_message_non_lu' => $this->nbrMessage]);
   }

    /**
     * @Route("/phpinfo", name="easyadmin_phpinfo")
     */
    public function phpInfoAction(): Response
    {
        if ($this->container->has('profiler')) {
            $this->container->get('profiler')->disable();
        }
        ob_start();
        phpinfo();
        $str = ob_get_contents();
        ob_get_clean();

        return new Response($str);
    }

   /**
     * @Route("/delete/{id<[0-9]+>}", name="delete_message")
     */
   public function deletemsgNousContacterView(NousContacter $nousContacter, EntityManagerInterface $em,Request $request)
   {
      $nousContacter->setIsDeleted(1);
      $em->persist($nousContacter);
      $em->flush();

      $msg = $request->getSession()->getFlashBag()->add('success_message','messages supprimer avec succès');
      return $this->redirectToRoute('msgNousContacter',['msg' => $msg]);
   }

    /**
     * @Route("/admin-journalist", name="list-journalist")
     */
    public function journalist(GnUserRepository $users,Request $request): Response
    {


        $journalist = $users->getJournalist();


        return $this->render('admin/journalist/journalist-list.php',[ 'message' => $this->message,'nbr_comm_non_approuve' => $this->nbrComment,'nbr_post_non_approuve' => $this->nbrPost,'nbr_message_non_lu' => $this->nbrMessage, 'journalist' => $journalist, 'msg' => '' ]);
    }

}
