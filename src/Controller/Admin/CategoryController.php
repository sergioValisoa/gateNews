<?php

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\GnPostCategoryType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\GnPostCategory;
use App\Repository\NousContacterRepository;
use App\Repository\GnPostCommentRepository;
use App\Repository\GnPostRepository;
use App\Repository\GnPostCategoryRepository;

class CategoryController extends AbstractController
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
    /**
     * @Route("/category/createCategory", name="category", methods={"GET", "POST"})
     */
    public function createCategory(Request $request, EntityManagerInterface $manager): Response
    {
        $category = new GnPostCategory();
        $form = $this->createForm(GnPostCategoryType::class,$category);
        $msg = null;
        $form->handleRequest($request);//préparer capter la requête
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($category);
            $manager->flush();
            $msg = $request->getSession()->getFlashBag()->add('success_category','catégories ajouter avec succès');
       
            return $this->redirectToRoute('readCategory',['msg' => $msg]);
        }
        
        return $this->render('admin/category/category.html.twig', ['form_create' => $form->createView(), 'message' => $this->message,'nbr_comm_non_approuve' => $this->nbrComment,'nbr_post_non_approuve' => $this->nbrPost,'nbr_message_non_lu' => $this->nbrMessage,'msg' => $msg]);
    }

    /**
     * @Route("/category/readCategory", name="readCategory", methods={"GET", "POST"})
     */
    public function readCategory(EntityManagerInterface $em,GnPostCategoryRepository $category,  Request $request): Response
    {
       if($request->getSession()->getFlashBag()->has('success_category')){
        $msg = $request->getSession()->getFlashBag()->get('success_category')[0];
      }
      else{
         $msg = null;
      }

       
            $data = $category->findBy(['isDeleted' => 0]);
            return $this->render('admin/category/view_category.html.twig', ['data' => $data, 'message' => $this->message,'nbr_comm_non_approuve' => $this->nbrComment,'nbr_post_non_approuve' => $this->nbrPost,'nbr_message_non_lu' => $this->nbrMessage,'msg' => $msg]);
          
    }

    /**
     * @Route("/category/deleteAction/{id<[0-9]+>}" , name="daleteCategory", methods={"GET", "POST"})
     */
    public function deleteCategory(EntityManagerInterface $manager, GnPostCategory $category): Response
    {
      $msg = null;
        if($this->isCsrfTokenValid('category_delete', $request->request->get('csrf_token'))){
            
            $category->setIsDeleted(1);//supprimer l'article
            $manager->flush();
          
        }
        $msg = $request->getSession()->getFlashBag()->add('success_category','catégories supprimer avec succès');
       
            return $this->redirectToRoute('readCategory',['msg' => $msg]);
    }

    /**
     * @Route("/category/updateAction/{id<[0-9]+>}" , name="updateCategory")
     */
    public function updateCategory(Request $request,GnPostCategory $category,EntityManagerInterface $manager)
    {
        $form = $this->createForm(GnPostCategoryType::class,$category);
        $msg = null;
        $form->handleRequest($request);//préparer capter la requête
        if($form->isSubmitted() && $form->isValid()){
            $manager->flush();
        
            $msg = $request->getSession()->getFlashBag()->add('success_category','catégories modifier avec succès');
       
            return $this->redirectToRoute('readCategory',['msg' => $msg]);
        }
                               
        return $this->render('admin/category/update.html.twig', ['category' => $category,'form_update' => $form->createView(), 'message' => $this->message,'nbr_comm_non_approuve' => $this->nbrComment,'nbr_post_non_approuve' => $this->nbrPost,'nbr_message_non_lu' => $this->nbrMessage,'msg' => $msg]);
    }
}
