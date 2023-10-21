<?php

namespace App\Controller\Admin;
use App\Service\MetierManagerBundle\Utils\ServiceName;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\GnPost;
use App\Form\GnPostType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\GnPostRepository;
use App\Repository\GnUserRepository;
use App\Repository\GnPostCategoryRepository;
use App\Repository\NousContacterRepository;
use App\Repository\GnPostCommentRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Security\Core\Security;
use function Symfony\Component\String\u;

class PostController extends AbstractController
{

    protected $message;
    protected $nbrMessage;
    protected $nbrComment;
    protected $nbrPost;
    private $security;
    public function __construct(NousContacterRepository $nousContacter,GnPostCommentRepository $comments,GnPostRepository $posts, Security $security)
    {
      $this->message = $nousContacter->findBy(['isView' => 0,'isDeleted' => 0],['createdAt'  => 'DESC'],3);
      $this->nbrComment = $comments->count(['isApprouved' => 0, 'isDeleted' => 0]);
      $this->nbrMessage = $nousContacter->count(['isDeleted' => 0,'isView' => 0]);
      $this->nbrPost = $posts->count(['isApprouved' => 0, 'isDeleted' => 0]);
      $this->security = $security;
      
    }
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [ // on merge le tableau des services par defaut avec notre tableau personnalisé
            'gn.manager.post' => 'App\Service\MetierManagerBundle\Metier\GnPost\ServiceMetierGnPost',
            'gn.manager.utils' => 'App\Service\MetierManagerBundle\Metier\Utils\ServiceMetierUtils',
        ]);
    }

    /**
     * @Route("/add_post", name="add_post", methods={"GET","POST"})
     */
    public function addPost(Request $request, EntityManagerInterface $manager, GnPostCategoryRepository $categoryRepository, GnUserRepository $user): Response
    {
            $_post_manager   = $this->get(ServiceName::SRV_METIER_POST);
            $post = new GnPost();
            $form = $this->createForm(GnPostType::class,$post,['allow_file_upload' => true]);
            /*$username = $request->getSession()->get('user_name')->getUsername();
            $user = $user->findBy(['userName' =>  $username]);*/

            $user = $this->security->getUser();
            $msg = null;
            $form->handleRequest($request);
           if($form->isSubmitted() && $form->isValid()){
               $categories = new ArrayCollection();
               // Create an ArrayCollection of the current Tag objects in the database
               foreach ($form->getData()->getCategories() as $tag) {
                   $categories->add($tag);
               }
                $_post_manager->addPost($post, $request, $user,$categories);

                 $msg = $request->getSession()->getFlashBag()->add('success_post','articles ajouter avec succès et en attente de validation');
            
              return $this->redirectToRoute('list_post',['msg' => $msg]);
           }
            $category = $categoryRepository->findAll();

        return $this->render('admin/post/add_post.html.twig', ['categories' => $category,'form_post' => $form->createView(), 'message' => $this->message,'nbr_comm_non_approuve' => $this->nbrComment,'nbr_post_non_approuve' => $this->nbrPost,'nbr_message_non_lu' => $this->nbrMessage,'msg' => $msg]);
    }


    /**
     * Retrieve quiz question ajax list
     * @Route("/list_post_ajax", name="list_post_ajax")
     * @param Request $_request
     * @param $_filtre_niveau
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function listAjaxAction(Request $_request)
    {
        $_page        = $_request -> query -> get('start');
        $_nb_max_page = $_request -> query -> get('length');
        $_search      = $_request -> query -> get('search')['value'];
        $_order_by    = $_request -> query -> get('order_by');

        $_post_manager = $this->get(ServiceName::SRV_METIER_POST);
        $_number_posts = $_post_manager->postListArray($_page, $_nb_max_page, $_search, $_order_by);

        return new JsonResponse([
            'recordsTotal'    => $_number_posts[1],
            'recordsFiltered' => $_number_posts[1],
            'data'            => array_map(function ($_val) {
                return array_values($_val);
            }, $_number_posts[0])
        ]);
    }

    /**
     * @Route("/list_post", name="list_post")
     */
    public function listPost(GnPostRepository $post, Request $request): Response
    {
            $_post_manager = $this->get(ServiceName::SRV_METIER_POST);
          if($request->getSession()->getFlashBag()->has('success_post')){
            $msg = $request->getSession()->getFlashBag()->get('success_post')[0];
          }
          else{
             $msg = null;
          }

          $posts = $post->findBy(['isDeleted' => 0,'isApprouved' => 1]);
        /*foreach ($posts as $key => $post) {
            //dump(u($post->getPostContent())->truncate(100, '...'));
            if(!empty($post->getPostContent()))
            {
                $_post_manager->updatePostMeta($post->getId(), u(html_entity_decode(strip_tags($post->getPostContent())))->truncate(100, '...'));
                //dd(u($post->getPostContent())->truncate(100, '...'));
            }
        }*/
        //dd($posts);
        //die();
      return $this->render('admin/post/list_post.html.twig',['posts' => $posts, 'message' => $this->message,'nbr_comm_non_approuve' => $this->nbrComment,'nbr_post_non_approuve' => $this->nbrPost,'nbr_message_non_lu' => $this->nbrMessage,'msg' => $msg]);
      
    }

    /**
     * @Route("/update_post/{id}", name="update_post")
     */
    public function updatePost(Request $request,GnPost $post, EntityManagerInterface $manager): Response
    {
        $utils_manager = $this->get(ServiceName::SRV_METIER_UTILS);
        $form = $this->createForm(GnPostType::class,$post);
        $msg = null;
        $form->handleRequest($request);//préparer capter la requête
        if($form->isSubmitted() && $form->isValid()){
            $post->setUpdatedAt(new \DateTime());
            $utils_manager->saveEntity($post, 'update');
            $msg = $request->getSession()->getFlashBag()->add('success_post','articles modifier avec succès');
            
              return $this->redirectToRoute('list_post',['msg' => $msg]);
        }
        return $this->render('admin/post/update_post.html.twig',['post' => $post, 'form' => $form->createView(), 'message' => $this->message,'nbr_comm_non_approuve' => $this->nbrComment,'nbr_post_non_approuve' => $this->nbrPost,'nbr_message_non_lu' => $this->nbrMessage,'msg' => $msg]);
    }

    /**
     * @Route("/delete_post/{id}", name="delete_post")
     */
    public function deletePost(Request $request, GnPost $post, EntityManagerInterface $manager): Response
    {
      $msg = null;
        //if($this->isCsrfTokenValid('post_delete', $request->request->get('csrf_token'))){
            $post->setIsDeleted(1);//supprimer l'article
            $manager->flush();
        //}
        $msg = $request->getSession()->getFlashBag()->add('success_post','articles supprimé avec succès');
            
              return $this->redirectToRoute('list_post',['msg' => $msg]);;
        
    }
}
