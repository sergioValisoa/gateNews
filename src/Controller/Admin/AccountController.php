<?php

namespace App\Controller\Admin;

use App\Service\MetierManagerBundle\Utils\ServiceName;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\GnUserRepository;
use App\Entity\GnUser;
use App\Repository\GnRoleRepository;
use App\Repository\NousContacterRepository;
use App\Repository\GnPostCommentRepository;
use App\Repository\GnPostRepository;
use App\Form\GnAdminAccountType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
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
     * @Route("/account", name="account_list")
     */
    public function index(GnUserRepository $user, Request $request): Response
    {
      $msg = null;
      if($request->getSession()->getFlashBag()->has('success_account')){
        $msg = $request->getSession()->getFlashBag()->get('success_account')[0];
      }
      else{
         $msg = null;
      }
      
          $users = $user->findBy(['isDeleted' => 0]);
        //$users = $users->findAll();

        return $this->render('admin/account/index.html.twig',['users' => $users, 'message' => $this->message,'nbr_comm_non_approuve' => $this->nbrComment,'nbr_post_non_approuve' => $this->nbrPost,'nbr_message_non_lu' => $this->nbrMessage,'msg' => $msg]);
     
    	
    }

    /**
     * @Route("/account_admin", name="account_admin")
     */
    public function accountAdmin(GnUserRepository $users, Request $request,GnRoleRepository $rolerepo, UserPasswordEncoderInterface $pwd_encode): Response
    {
        $_account_manager = $this->get(ServiceName::SRV_METIER_ACCOUNT);
        $user = new GnUser();
        $form = $this->createForm(GnAdminAccountType::class,$user);
        $msg = null;
        $form->handleRequest($request);//préparer capter la requête
        if($form->isSubmitted() && $form->isValid()){
            $roles = new ArrayCollection();
            // Create an ArrayCollection of the current Tag objects in the database
            foreach ($form->getData()->getGnRoles() as $role) {
                $roles->add($role);
            }
            $_account_manager->addAccount($user, $form, $request, $pwd_encode, $roles);
            $msg = $request->getSession()->getFlashBag()->add('success_account','administrateur ajouter avec succès');
            return $this->redirectToRoute('account_list', ['msg' => $msg]);
        }
        return $this->render('admin/account/account_admin.html.twig',['form_admin' => $form->createView(), 'message' => $this->message,'nbr_comm_non_approuve' => $this->nbrComment,'nbr_post_non_approuve' => $this->nbrPost,'nbr_message_non_lu' => $this->nbrMessage,'msg' => $msg]);
        
    }

    /**
     * @Route("/update_user/{id<[0-9]+>}", name="upadate_user")
     */
    public function updateUser(Request $request,GnUser $user, EntityManagerInterface $manager, UserPasswordEncoderInterface $pwd_encode): Response
    {
        $utils_manager = $this->get(ServiceName::SRV_METIER_UTILS);
        $_user_upload_manager = $this->get(ServiceName::SRV_METIER_USER_UPLOAD);
        $form = $this->createForm(GnAdminAccountType::class,$user);
        $msg = null;
        $form->handleRequest($request);//préparer capter la requête
        if($form->isSubmitted() && $form->isValid()){
            $user->setUpdateAt(new \DateTime());
            $pwd = $request->request->get('gn_admin_account')['userPassword'];
            $user->setUserPassword($pwd_encode->encodePassword($user,$pwd));
            // Traitement photo
            //$_img_photo = $form['usrPhoto']->getData();
            // S'il y a un nouveau fichier ajouté, on supprime l'ancien fichier puis on enregistre ce nouveau
//            if ($_img_photo) {
//                $_user_upload_manager->deleteOnlyImageById($user->getId());
//                $_user_upload_manager->upload($user, $_img_photo);
//            }
            $utils_manager->saveEntity($user, 'update');
             $msg = $request->getSession()->getFlashBag()->add('success_account','utilisateur modifier avec succès');
            //if ($request->getSession()->get('user_name')->getGnRoles()->getSnashot()[0]->getId() == 2) {
              return $this->redirectToRoute('account_list', ['msg' => $msg]);
            //}
            //return $this->redirectToRoute('home', ['msg' => $msg]);
            
        }

        return $this->render('admin/account/user_update.html.twig',['user' => $user, 'form' => $form->createView(), 'message' => $this->message,'nbr_comm_non_approuve' => $this->nbrComment,'nbr_post_non_approuve' => $this->nbrPost,'nbr_message_non_lu' => $this->nbrMessage,'msg' => $msg]);
            
        
    }

    /**
     * Ajax suppression fichier image utilisateur
     * @Route("/delete-image", name="user_delete_image_ajax")
     * @param Request $_request
     * @return JsonResponse
     */
    public function deleteImageAjaxAction(Request $_request) {
        // Récupérer manager
        $_user_upload_manager = $this->get(ServiceName::SRV_METIER_USER_UPLOAD);

        // Récuperation identifiant fichier
        $_data = $_request->request->all();
        $_id   = $_data['id'];

        // Suppression fichier image
        $_response = $_user_upload_manager->deleteImageById($_id);

        return new JsonResponse($_response);
    }

    /**
     * @Route("/delete_user/{id}", name="delete_user")
     */
    public function deleteuser(Request $request,GnUserRepository $userrepo, $id): Response
    {
      $msg = null;
        $manager = $this->getDoctrine()->getManager();
        $userrepo = $this->getDoctrine()->getRepository(GnUser::class);
        $user = $userrepo->find($id);
        if($this->isCsrfTokenValid('user_delete', $request->request->get('csrf_token'))){
            $user->setIsDeleted(1);
            //$manager->remove($user);//supprimer l'article
            $manager->flush();
        }
         $msg = $request->getSession()->getFlashBag()->add('success_account','utilisateur supprimer avec succès');
            return $this->redirectToRoute('account_list', ['msg' => $msg]);
        
    }
}
