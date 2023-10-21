<?php

namespace App\Controller\Admin;

use App\Entity\GnPostComment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\GnPostCommentRepository;
use App\Repository\GnUserRepository;
use App\Repository\GnPostRepository;
use App\Form\GnPostCommentType;
use App\Repository\NousContacterRepository;

class CommentController extends AbstractController
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
     * @Route("/comment/addComment/{id}", name="addComment")
     */
    public function addComment(Request $request, EntityManagerInterface $em, GnUserrepository $user,GnPostrepository $post, $id): Response
    {
        $comment = new GnPostComment();
        $form = $this->createForm(GnPostCommentType::class,$comment);//créer la formulaire
        $msg = null;
        $form->handleRequest($request);//préparer capter la requête
        if($form->isSubmitted() && $form->isValid()){
            // $comment = $form->getData();
            $user = $user->findOneBy(["userName" => $request->getSession()->get('user_name')]);
            $post = $post->find($id);
            $comment->setUser($user);
            $comment->setPost($post);
            $comment->setIsApprouved(0);
            $comment->setIsDeleted(0);
            $em->persist($comment);
            $em->flush();
            $msg = $request->getSession()->getFlashBag()->add('success_comment','commentaires ajouter avec succès et en attente de validation');

            return $this->redirectToRoute('readComment',['id' => $post->getId(),'msg' => $msg]);
        }
        return $this->render('admin/comment/add_comment.html.twig',['form_comment' => $form->createView(), 'message' => $this->message,'nbr_comm_non_approuve' => $this->nbrComment,'nbr_post_non_approuve' => $this->nbrPost,'nbr_message_non_lu' => $this->nbrMessage,'msg' => $msg]);
    }

    /**
     * @Route("/comment/readComment/{id}", name="readComment", methods={"GET"})
     */
    public function readComment(GnPostCommentRepository $comm, Request $request, $id): Response
    {
      if($request->getSession()->getFlashBag()->has('success_comment')){
        $msg = $request->getSession()->getFlashBag()->get('success_comment')[0];
      }
      else{
         $msg = null;
      }
        
            $comm = $comm->findBy(['post' => $id,'isApprouved'=> 1,'isDeleted' => 0]);
            return $this->render('admin/comment/read_comment.html.twig', ['comments' => $comm, 'id' => $id, 'message' => $this->message,'nbr_comm_non_approuve' => $this->nbrComment,'nbr_post_non_approuve' => $this->nbrPost,'nbr_message_non_lu' => $this->nbrMessage,'msg' => $msg]);
          
    }

    /**
     * @Route("/comment/deleteComment/{id<[0-9]+>}" , name="deleteComment", methods={"GET", "POST"})
     */
    public function deleteAction(EntityManagerInterface $manager,GnPostRepository $post, GnPostComment $comment,Request $request,GnPostCommentRepository $commentrepo,$id): Response
    {
      $msg = null;
      $commentrepo = $commentrepo->find($id);
        if($this->isCsrfTokenValid('comment_delete', $request->request->get('csrf_token'))){
            
            $comment->setIsDeleted(1);//supprimer l'article
            $manager->flush();
          
        }

        $msg = $request->getSession()->getFlashBag()->add('success_comment','commentaires supprimer avec succès');

            return $this->redirectToRoute('readComment',['id' => $commentrepo->getPost()->getId(),'msg' => $msg]);
    }

    /**
     * @Route("/comment/updateAction/{id<[0-9]+>}" , name="updateAction")
     */
    public function updateAction(Request $request,GnPostRepository $post,GnPostComment $comment,EntityManagerInterface $manager,GnPostCommentRepository $commentrepo,$id)
    {
        $form = $this->createForm(GnPostCommentType::class,$comment);//créer la formulaire
        $msg = null;
        $commentrepo = $commentrepo->find($id);
        $form->handleRequest($request);//préparer capter la requête
        if($form->isSubmitted() && $form->isValid()){
            $manager->flush();

            $msg = $request->getSession()->getFlashBag()->add('success_comment','commentaires modifier avec succès');
            $post = $post->findOneBy(['postComments'  => $comment]);
            return $this->redirectToRoute('readComment',['id' => $commentrepo->getPost()->getId(),'msg' => $msg]);
        }

        return $this->render('admin/comment/update_comment.html.twig', ['comment' => $comment,'form_com_upd' => $form->createView(), 'message' => $this->message,'nbr_comm_non_approuve' => $this->nbrComment,'nbr_post_non_approuve' => $this->nbrPost,'nbr_message_non_lu' => $this->nbrMessage,'msg' => $msg]);
    }
}
