<?php

namespace App\Controller;

use App\Service\MetierManagerBundle\Utils\ServiceName;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\GnPostRepository;
use App\Repository\GnUserRepository;
use App\Repository\GnPostCategoryRepository;
use App\Repository\GnPostCommentRepository;
use App\Entity\GnPost;
use App\Entity\GnUser;
use App\Entity\GnPostComment;
use App\Form\GnPostType;
use App\Form\GnPostCommentType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Security;


class JournalistController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
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
     * @Route("/journalist/articles", name="journalist")
     */
    public function index(Request $request, EntityManagerInterface $manager, GnPostCategoryRepository $category, GnUserRepository $user): Response
    {
     //ajout post
            $post = new GnPost();
            $_post_manager   = $this->get(ServiceName::SRV_METIER_POST);
            $form = $this->createForm(GnPostType::class,$post);
            //$username = $request->getSession()->get('user_name')->getUserName();
            //$user = $user->findBy(['userName' =>  $username]);
            $user = $this->security->getUser();
            $msg = null;
            $form->handleRequest($request);
            //dump($form->getData()->getImageFile());die('test');
           if($form->isSubmitted() && $form->isValid()){
               $categories = new ArrayCollection();
               // Create an ArrayCollection of the current Tag objects in the database
               foreach ($form->getData()->getCategories() as $tag) {
                   $categories->add($tag);
               }
                $_post_manager->addPost($post, $request, $user,$categories);

                 $msg = $request->getSession()->getFlashBag()->add('success_post','article ajouté avec succès et en attente de validation');
                 return $this->redirectToRoute('list_journalist', ['msg' => $msg
               ]);
              }
            $category = $category->findAll(); 
       
          return $this->render('journalist/add_post.html.twig', ['categories' => $category,'form_post' => $form->createView()]);
        
        
        
    
    }

    /**
     * @Route("/journalist/listes", name="list_journalist")
     */
    public function listPostJournalist(GnPostRepository $post, Request $request, GnUserRepository $user): Response
    {
        
        		//$user = $user->findBy(['userName' => $request->getSession()->get('user_name')->getUserName()]);
        		$user = $this->security->getUser();
              //$post = $post->findBy(['user' => $user,'isApprouved' => 1,'isDeleted' => 0]);
              $post = $post->findBy(['user' => $user,'isDeleted' => 0]);
              // dd($post);
              return $this->render('journalist/list_post.html.twig',['posts' => $post]);
      
    }

    /**
     * @Route("/update_postJour/{id<[0-9]+>}", name="update_post_journalist")
     */
    public function updatePost(Request $request, GnPost $post, EntityManagerInterface $manager): Response
    {

        $utils_manager = $this->get(ServiceName::SRV_METIER_UTILS);
        $form = $this->createForm(GnPostType::class, $post);
        $msg = null;
        $form->handleRequest($request); //préparer capter la requête
        if ($form->isSubmitted() && $form->isValid()) {
            $post->setUpdatedAt(new \DateTime());
            $utils_manager->saveEntity($post, 'update');
            $msg = $request->getSession()->getFlashBag()->add('success_post', 'articles modifier avec succès');

            return $this->redirectToRoute('list_journalist', ['msg' => $msg]);
        }

        return $this->render('journalist/update_post.html.twig', ['post' => $post, 'form' => $form->createView()]);
    }

    /**
     * @Route("/comment/readCommentJour/{id<[0-9]+>}", name="readCommentJournalist", methods={"GET"})
     */
    public function readComment(GnPostCommentRepository $comm, Request $request, GnPost $post, $id): Response
    {

        $comm = $comm->findBy(['post' => $post, 'isApprouved' => 1, 'isDeleted' => 1]);
        return $this->render('journalist/read_comment.html.twig', ['comments' => $comm, 'post' => $post]);
    }

    /**
     * @Route("/comment/addCommentJour/{id<[0-9]+>}", name="addCommentJour")
     */
    public function addComment(Request $request, EntityManagerInterface $em, GnUserRepository $user, GnPostRepository $post, $id): Response
    {
        $comment = new GnPostComment();
        $form = $this->createForm(GnPostCommentType::class, $comment); //créer la formulaire

        $form->handleRequest($request); //préparer capter la requête
        if ($form->isSubmitted() && $form->isValid()) {
            // $comment = $form->getData();
            //$user = $user->findOneBy(["userName" => $request->getSession()->get('user_name')->getUserName()]);

            $user = $this->security->getUser();
            $post = $post->find($id);
            $comment->setUser($user);
            $comment->setPost($post);
            $comment->setIsApprouved(0);
            $comment->setIsDeleted(0);
            $em->persist($comment);
            $em->flush();
            $msg = $request->getSession()->getFlashBag()->add('success_post', 'commentaires ajouter avec succès et en attente de validation de l\'administrateur');

            return $this->redirectToRoute('list_journalist', ['msg' => $msg]);
        }
        return $this->render('journalist/add_comment.html.twig', ['form_comment' => $form->createView()]);
    }
}
