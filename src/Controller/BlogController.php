<?php

namespace App\Controller;

use App\Entity\GnPost;
use App\Entity\GnUser;
use App\Entity\GnPostComment;
use App\Entity\NousContacter;
use App\Entity\GnPostCategory;
use App\Form\GnPostCommentType;
use App\Form\NousContacterType;
use App\Form\GnAdminAccountType;
use Symfony\Component\Mime\Address;
use App\Repository\GnPostRepository;
use App\Repository\GnUserRepository;
use Doctrine\ORM\Query\Expr\OrderBy;
use App\Repository\GnCountryRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\GnPostCommentRepository;
use App\Repository\GnPostCategoryRepository;
use App\Repository\GnSubscriptionRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Security;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

use Symfony\Component\Routing\Annotation\Route;
use App\Service\MetierManagerBundle\Utils\EntityName;
use App\Service\MetierManagerBundle\Utils\ServiceName;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    public $footer;
    public $data;
    public $pays;
    private $security;

    public function __construct(GnPostCategoryRepository $read, GnCountryRepository $country, GnPostRepository $postRead, Security $security)
    {


        $cat1 = $read->find(94);
        $cat2 = $read->find(97);
        $cat3 = $read->find(84);
        $cat4 = $read->find(85);
        $cat5 = $read->find(100);
        $cat6 = $read->find(87);
        $cat7 = $read->find(99);
        $cat8 = $read->find(115);
        $cat9 = $read->find(90);
        $cat10 = $read->find(91);
        $this->data = array($cat1, $cat2, $cat3, $cat4, $cat5, $cat6, $cat7, $cat8, $cat9, $cat10);

        $this->pays = $country->findAll();
        $this->footer = $postRead->getPostsByVideo();
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
     * @Route("/", name="home")
     */
    public function index(GnPostRepository $postRead, Request $request, GnUserRepository $userrepo, GnPostCategoryRepository $categoryRead, GnSubscriptionRepository $subscri): Response
    {

        $postRead1 = $postRead->findBy(['isApprouved' => 1, 'isDeleted' => 0], ['postCreatedAt' => 'DESC'], 4);
        $postRead2 = $postRead->findBy(['isApprouved' => 1, 'isDeleted' => 0]);
        $count = $categoryRead->count([]);

        $post = array();
        for ($i = 1; $i <= $count; $i++) {
            array_push($post, $postRead->getPostsByCategory($i));
        }
        $countPost = count($post);
        //dd(array_push($post,$postRead->getPostsByCategory($i)));

        $postReadEconomie = $postRead->getAllPostParCategory(84);
        $postReadBusiness = $postRead->getAllPostParCategory(100);
        $postReadMarche = $postRead->getAllPostParCategory(94);
        $postReadFinance = $postRead->getAllPostParCategory(97);
        $postReadTech = $postRead->getAllPostParCategory(85);
        $postReadEntreprise = $postRead->getAllPostParCategory(99);
        $postReadTourisme = $postRead->getAllPostParCategory(90);
        $postReadEnviron = $postRead->getAllPostParCategory(98);
        $postReadMonde = $postRead->getAllPostParCategory(87);
        $postReadSport = $postRead->getAllPostParCategory(91);
        $postReadDrct = $postRead->getPostByDrct(80);
        $postReadStory = $postRead->getAllPostParCategory(93);
        $postReadVideo = $postRead->getPostsByVideo();

        //abonnement
        if ($request->getSession()->has('user_name')) {
            $user = $request->getSession()->get('user_name');
            //dd($userrepo->getOneUserNotAbonne($user->getId()));
            // if($userrepo->getOneUserNotAbonne($user->getId()) OR $userrepo->getOneAdmin($user->getId())){
            //vérifier si un user est confirmer

            return $this->render('publique/index.html.twig', [
                'recapData' => $this->data,
                'postRead1' => $postRead1,
                'postRead2' => $postRead2,
                'postReadVideo' => $postReadVideo,
                'post' => $post,
                'postReadEconomie' => $postReadEconomie,
                'postReadBusiness' => $postReadBusiness,
                'postReadMarche' => $postReadMarche,
                'postReadFinance' => $postReadFinance,
                'postReadTech' => $postReadTech,
                'postReadEntreprise' => $postReadEntreprise,
                'postReadTourisme' => $postReadTourisme,
                'postReadEnviron' => $postReadEnviron,
                'postReadMonde' => $postReadMonde,
                'postReadSport' => $postReadSport,
                'postReadDrct' => $postReadDrct,
                'postReadStory' => $postReadStory,
                'countPost' => $countPost,
                'pays' => $this->pays,
                'title' => '',
                'footer' => $this->footer
            ]);
            //}
            // else{
            //     $abonnement = $subscri->findAll();
            //     return $this->render('publique/Abonnement/abonnement.html.twig', [
            //         'recapData' => $this->data,
            //         'abonnements' => $abonnement,
            //         'title' => 'Abonnement',
            //         'pays' => $this->pays,
            //         'footer' => $this->footer]);
            // }
        }


        return $this->render('publique/index.html.twig', [
            'recapData' => $this->data,
            'postRead1' => $postRead1,
            'postRead2' => $postRead2,
            'postReadVideo' => $postReadVideo,
            'countPost' => $countPost,
            'post' => $post,
            'postReadEconomie' => $postReadEconomie,
            'postReadBusiness' => $postReadBusiness,
            'postReadMarche' => $postReadMarche,
            'postReadFinance' => $postReadFinance,
            'postReadTech' => $postReadTech,
            'postReadEntreprise' => $postReadEntreprise,
            'postReadTourisme' => $postReadTourisme,
            'postReadEnviron' => $postReadEnviron,
            'postReadMonde' => $postReadMonde,
            'postReadSport' => $postReadSport,
            'postReadDrct' => $postReadDrct,
            'postReadStory' => $postReadStory,
            'title' => '',
            'pays' => $this->pays,
            'footer' => $this->footer
        ]);
    }

    /**
     * @Route("/category/{slug}", name="category_link")
     */
    public function postParCategory(GnPostRepository $postRead, Request $request): Response
    {
        $_utils_manager       = $this->get(ServiceName::SRV_METIER_UTILS);
        $_slug_category = $request->get('slug');
        $categoryRead = $_utils_manager->getEntityByFilter(GnPostCategory::class, ['postCategoryUrl' => $_slug_category]);
        if (!$categoryRead) {
            return $this->redirectToRoute('home');
        }
        $id = $categoryRead->getId();
        $slug = $categoryRead->getPostCategoryUrl();
        //$post      = $_utils_manager->getEntityByFilter(GnPost::class, ['postCategoryUrl' => $_slug_post]);
        $postRead = $postRead->getFullPostParCategorySlug($_slug_category, $request->query->getInt('page', 1));
        return $this->render('publique/Rubriques/read_category.html.twig', ['recapData' => $this->data, 'pagination' => $postRead, 'category' =>  $categoryRead->getCategoryTitle(), 'pays' => $this->pays, 'title' => $categoryRead->getCategoryTitle(), 'id_cat' => $id, 'slug_cat' => $slug, 'route_home' => $_SERVER['HTTP_HOST'], 'route_actuel' => $request->attributes->get('_route'), 'footer' => $this->footer]);
    }

    /**
     * @Route("/post/user/{id<[0-9]+>}", name="post_user")
     */
    public function postParUser(GnUser $user, GnPostRepository $postRead, Request $request, $id): Response
    {

        $postReadUser = $postRead->getAllPostParUser($user->getId());

        return $this->render('publique/Rubriques/post_user.html.twig', ['recapData' => $this->data, 'postRead' => $postReadUser, 'pays' => $this->pays, 'title' => $user->getUserFullname(), 'user_id' => $id, 'route_home' => $_SERVER['HTTP_HOST'], 'route_actuel' => $request->attributes->get('_route'), 'user' => $user, 'footer' => $this->footer]);
    }

    /**
     * @Route("/{_year}/{_month}/{_day}/{_slug}", name="readMore")
     */
    public function readMore(RouterInterface $router, GnPostCommentRepository $commentaire, GnPostRepository $postrepo, Request $request, EntityManagerInterface $em, GnUserrepository $userrepo): Response
    {
        $_utils_manager       = $this->get(ServiceName::SRV_METIER_UTILS);
        $comment = new GnPostComment();
        $_slug_post = $request->get('_slug');
        $post      = $_utils_manager->getEntityByFilter(GnPost::class, ['postUrl' => $_slug_post]);
        $current_category = $postrepo->getDefaultCategory($post->getId());

        $form = $this->createForm(GnPostCommentType::class, $comment);
        $form->handleRequest($request); //préparer capter la requête
        if ($form->isSubmitted() && $form->isValid()) { //ajout commentaire
            // $comment = $form->getData();
            $user = $this->security->getUser();
            $user = $userrepo->findOneBy(["userEmail" => $user->getUserEmail()]);
            $comment->setUser($user);
            $comment->setPost($post);
            $comment->setIsApprouved(0);
            $comment->setIsDeleted(0);
            $em->persist($comment);
            $em->flush();
            return $this->redirectToRoute('home');
        }
        //si un utilisateur n'est pas connecté readmore inacessible
        $previousURL = $request->headers->get('referer');
        $server = $_SERVER['HTTP_HOST'];
        $path = explode($server, $previousURL);
        $route = null;
        if (is_array($path) && (!is_null($path[1])) && is_array($path[1]) && array_key_exists('_route', $path[1])) {
            $route = $router->match($path[1])['_route'];
        }

        if (preg_match('/category/', $path[1])) {
            $category = explode("/category/", $path[1]);
            $category_id = $category[1];
        }

        $related_posts = $postrepo->getRelatedPosts($current_category, $post->getId());
        $previous_post = $postrepo->getRecurrentPostSlug($post->getId(), 'previous');
        $next_post = $postrepo->getRecurrentPostSlug($post->getId());
        //afficher les commentaire approuver
        $commentaire = $commentaire->findBy(['post' => $post, 'isApprouved' => 1]);
        $user = $this->security->getUser();

        //if (!empty($user)){
        //if($userrepo->getOneUserNotAbonne($user->getId()) OR $userrepo->getOneAdmin($user->getId())){
        return $this->render('publique/Rubriques/read_more.html.twig', [
            'recapData'         => $this->data,
            'commentaire'       => $commentaire,
            'post'              => $post,
            'form_comment'      => $form->createView(),
            'pays'              => $this->pays,
            'title'             => $post->getPostTitle(),
            'related_posts'     => $related_posts,
            'previous_post'     => $previous_post,
            'next_post'         => $next_post,
            'route_home'        => $_SERVER['HTTP_HOST'],
            'route_actuel'      => $request->attributes->get('_route'),
            'post_url'          => $post->getPostUrl(),
            'footer'            => $this->footer
        ]);
        //}
        /*else{
                if (!is_null($route)) {
                    switch ($route) {
                        case 'aLaUne':
                            return $this->redirectToRoute('aLaUne');
                            break;
                        case 'successStory':
                            return $this->redirectToRoute('successStory');
                            break;
                        case 'reportageVideo':
                            return $this->redirectToRoute('reportageVideo');
                            break;
                        case 'link':
                            return $this->redirectToRoute('link', ['id' => $category_id]);
                            break;
                    }
                }
            }*/
        /* } else {
            if (!is_null($route)) {
                switch ($route) {
                    case 'aLaUne':
                        return $this->redirectToRoute('aLaUne');
                        break;
                    case 'successStory':
                        return $this->redirectToRoute('successStory');
                        break;
                    case 'reportageVideo':
                        return $this->redirectToRoute('reportageVideo');
                        break;
                    case 'link':
                        return $this->redirectToRoute('link', ['id' => $category_id]);
                        break;
                    default:
                        return $this->redirectToRoute('home');
                        break;
                }
            }
        }*/
    }

    /**
     * @Route("/aLaUne", name="aLaUne")
     */
    public function aLaUne(GnPostRepository $postRead, Request $_request): Response
    {
        $postRead = $postRead->getFullPostParCategory(83, $_request->query->getInt('page', 1));

        return $this->render('publique/aLaUne/read_aLaUne.html.twig', ['recapData' => $this->data, 'pagination' => $postRead, 'pays' => $this->pays, 'title' => 'A la une', 'footer' => $this->footer]);
    }

    /**
     * @Route("/search", name="search")
     * @param GnPostRepository $postRepository
     * @param GnPostCategoryRepository $categoryRepository
     * @param Request $request
     * @return Response
     */
    public function searchPage(GnPostRepository  $postRepository, GnPostCategoryRepository  $categoryRepository, Request $request): Response
    {
        $postRead = $postRepository->getAllPostByTerm($request->query->get('s', ''), $request->query->getInt('page', 1));
        $search_term = $request->query->get('s', '');
        return $this->render('publique/SearchPage/index.html.twig', ['recapData' => $this->data, 'search_term' => $search_term, 'pagination' => $postRead, 'pays' => $this->pays, 'title' => 'Success Strory', 'footer' => $this->footer]);
    }

    /**
     * @Route("/successStory", name="successStory")
     */
    public function successStory(GnPostRepository $postRead, GnPostCategoryRepository $categoryRead, Request $_request): Response
    {

        //$postRead = $postRead->getAllPostParCategory(93);
        $postRead = $postRead->getFullPostParCategory(93, $_request->query->getInt('page', 1));
        return $this->render('publique/SuccessStory/read_successStory.html.twig', ['recapData' => $this->data, 'pagination' => $postRead, 'pays' => $this->pays, 'title' => 'Success Strory', 'footer' => $this->footer]);
    }

    /**
     * @Route("/reportageVideo", name="reportageVideo")
     */
    public function reportageVideo(GnPostRepository $postRead, Request $_request): Response
    {
        $postRead = $postRead->getAllPostsByVideo($_request->query->getInt('page', 1));

        return $this->render('publique/ReportageVideo/read_ReportageVideo.html.twig', ['recapData' => $this->data, 'pagination' => $postRead, 'title' => 'Reportage Video', 'pays' => $this->pays, 'footer' => $this->footer]);
    }

    /**
     * @Route("/nousContacter", name="nousContacter")
     */
    public function nousContacter(Request $request, EntityManagerInterface $em): Response
    {
        $nousContacter = new NousContacter();
        $form = $this->createForm(NousContacterType::class, $nousContacter);
        $form->handleRequest($request); //préparer capter la requête
        if ($form->isSubmitted() && $form->isValid()) {
            // $comment = $form->getData();
            $nousContacter->setCreatedAt(new \DateTime());
            $nousContacter->setIsView(0);
            $nousContacter->setIsDeleted(0);
            $em->persist($nousContacter);
            $em->flush();
            return $this->redirectToRoute('nousContacter');
        }
        return $this->render('publique/nousContacter/nous_contacter.html.twig', ['recapData' => $this->data, 'nousContacter' => $form->createView(), 'pays' => $this->pays, 'title' => 'Nous Contacter', 'footer' => $this->footer]);
    }



    /**
     * @Route("/payemnt/{id<[0-9]+>}", name="payement")
     */
    public function payement(Request $request, GnUser $user, EntityManagerInterface $em, GnSubscriptionRepository $sub)
    {
        $numeroCarte = $request->request->get('compte');
        $montant  = $request->request->get('montant');
        $id_sub  = $request->request->get('subscription');;
        //validation de payement

        //inserer date confirm_at gnUser
        $user->setConfirmAt(new \DateTime);
        //liéer gnuser gnSubscription
        $sub  = $sub->find($id_sub);
        $user->setGnSubscription($sub);
        $em->flush();

        return $this->redirectToRoute('home');
    }
}
