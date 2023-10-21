<?php

namespace App\Controller\Admin;

use App\Entity\GnCountry;

use App\Service\MetierManagerBundle\Utils\ServiceName;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\GnCountryType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\NousContacterRepository;
use App\Repository\GnPostCommentRepository;
use App\Repository\GnPostRepository;

class CountryController extends AbstractController
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
            'gn.manager.country' => 'App\Service\MetierManagerBundle\Metier\GnCountry\ServiceMetierGnCountry',
            'gn.manager.utils' => 'App\Service\MetierManagerBundle\Metier\Utils\ServiceMetierUtils',
        ]);
    }
    /**
     * @Route("/country/createCountry", name="country", methods={"GET", "POST"})
     */
    public function createCountry(Request $request, EntityManagerInterface $manager): Response
    {
        // Récupérer manager
        $_utils_manager = $this->get(ServiceName::SRV_METIER_UTILS);
        $_country_manager   = $this->get(ServiceName::SRV_METIER_COUNTRY);

        $country = new GnCountry();
        $form = $this->createForm(GnCountryType::class,$country);
                     
        $form->handleRequest($request);//préparer capter la requête
        if($form->isSubmitted() && $form->isValid()){
            // Enregistrement pays
            $_country_manager->addCountry($country, $form);
            $_flash_message = "pays ajouter avec succès";
            $_utils_manager->setFlash('success', $_flash_message);
            return $this->redirectToRoute('readCountry');
        }
        
        return $this->render('admin/country/add_country.html.twig', ['form_country' => $form->createView(), 'message' => $this->message,'nbr_comm_non_approuve' => $this->nbrComment,'nbr_post_non_approuve' => $this->nbrPost,'nbr_message_non_lu' => $this->nbrMessage]);
    }

    /**
     * @Route("/country/readCountry", name="readCountry", methods={"GET", "POST"})
     */
    public function readCountry(EntityManagerInterface $em,  Request $request): Response
    {


             $read = $em->getRepository(GnCountry::class);
            $data = $read->findAll();
            return $this->render('admin/country/view_country.html.twig', ['data' => $data, 'message' => $this->message,'nbr_comm_non_approuve' => $this->nbrComment,'nbr_post_non_approuve' => $this->nbrPost,'nbr_message_non_lu' => $this->nbrMessage]);
          
    }

    /**
     * @Route("/country/deleteCountry/{id<[0-9]+>}" , name="deleteCountry", methods={"GET", "POST"})
     */
    public function deleteCountry(EntityManagerInterface $em, int $id): Response
    {
        // Récupérer manager
        $_utils_manager = $this->get(ServiceName::SRV_METIER_UTILS);
        $em = $this->getDoctrine()->getManager();
        $data = $this->getDoctrine()->getRepository(GnCountry::class);
        $data = $data->find($id);

        if (!$data) {
            throw $this->createNotFoundException(
                'There are no country with the following id: ' . $id
            );
        }
        $em->remove($data);
        $em->flush();

        
        $_utils_manager->setFlash('success', "pays supprimer avec succès");
        return $this->redirectToRoute('readCountry');
    }

    /**
     * @Route("/country/updateCountry/{id}" , name="updateCountry")
     */
    public function updateCountry(Request $request,GnCountry $country,EntityManagerInterface $manager)
    {
        // Récupérer manager
        $_utils_manager = $this->get(ServiceName::SRV_METIER_UTILS);
        $form = $this->createForm(GnCountryType::class,$country);
                     
        $form->handleRequest($request);//préparer capter la requête
        if($form->isSubmitted() && $form->isValid()){
            $manager->flush();

            
            $_utils_manager->setFlash('success', "pays modifier avec succès");
        
            return $this->redirectToRoute('readCountry');
        }
                               
        return $this->render('admin/country/update.html.twig', ['country' => $country,'form_update' => $form->createView(), 'message' => $this->message,'nbr_comm_non_approuve' => $this->nbrComment,'nbr_post_non_approuve' => $this->nbrPost,'nbr_message_non_lu' => $this->nbrMessage]);
    }
}
