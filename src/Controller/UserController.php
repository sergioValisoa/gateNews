<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\GnUser;
use App\Entity\GnRole;
use App\Repository\GnRoleRepository;
use App\Repository\GnUserRepository;
use App\Form\GnAdminAccountType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Service\MetierManagerBundle\Utils\ServiceName;
use App\Repository\GnPostCategoryRepository;
use App\Repository\GnPostRepository;
use App\Repository\GnCountryRepository;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Symfony\Component\Security\Http\RememberMe\RememberMeServicesInterface;


class UserController extends AbstractController
{
    private $user;
    public $pays;
    public function __construct(GnUserRepository $userrepo,GnPostCategoryRepository $read, GnPostRepository $postRead,GnCountryRepository $country)
    {
        //$this->user = $userrepo;
        $this->data = $read->findAll();
        $this->footer = $postRead->getPostsByVideo();
        $this->pays = $country->findAll();
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
     * @Route("/auth", name="auth")
     */
    public function index(Request $request): Response
    {
        $msg = null;
        $msg = $request->getSession()->getFlashBag()->get('error');
        
        if($msg){
            $msg = $msg[0];
        }
        return $this->render('user/auth.html.twig',['error' => $msg]);
    }

     /**
     * @Route("/logout", name="logout")
     */
    public function logout(): Response
    {
        throw new \LogicException("Error Processing Request");  
        // dd("dze");   
    }

     /**
     * @Route("/register", name="create_account")
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $pwd_encode,GnRoleRepository $rolerepo): Response
    {
        $user = new GnUser();
        $form = $this->createForm(GnAdminAccountType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            //persister les données
            $rolerepo = $rolerepo->find(1);
          
            $pwd = $user->getUserPassword();
            $user->setUserPassword($pwd_encode->encodePassword($user,$pwd));
            $user->addGnRole($rolerepo);
            $user->setCreatedAt(new \DateTime());
            $user->setConfirmAt(new \DateTime());

            $manager->persist($user);
            $manager->flush();
            
            return $this->redirectToRoute('auth');
        }       
        return $this->render('user/register.html.twig',['form_user' => $form->createView()]);
    }

    /**
     * @Route("/forgot", name="forgot_password")
     */
    public function forgot(): Response
    {

        return $this->render('user/forgot.html.twig');
    }
    /**
     * @Route("/reset", name="reset_password")
     */
    public function reset(): Response
    {

        return $this->render('user/reset.html.twig');
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request,UserPasswordEncoderInterface $pwd): Response
    {
        $msg = null;
        $msg = $request->getSession()->getFlashBag()->get('error');

        if($msg){
            $msg = $msg[0];
        }
        return $this->render('user/auth.html.twig',['error' => $msg]);
        //return $this->redirectToRoute('auth');
       /* $password = $request->request->get('password');
       $identifiant = $request->request->get('email_username');
   
       $this->user = $this->user->findOneBy(['userEmail' => $request->request->get('email_username')]);
        if (!$this->user){
            return $this->redirectToRoute('auth');
        }

       if ($pwd->isPasswordValid($this->user,$password)){
            $msg = null;
            $request->getSession()->set('user_name', $this->user);
            return $this->redirectToRoute('home',['msg' => $msg]);
       } else {
            $msg = $request->getSession()->set('success_account','Identifiant incorrect');
           return $this->redirectToRoute('home',['msg' => $request->getSession()->get('success_account')]);
       }*/
       
    }

    /**
     * @Route("/app_login", name="app_login", methods={"POST"})
     */
    public function frontLogin(Request $request)
    {
        $user = $this->getUser();
        //dd($user);
        return $this->json([
                'user' => $this->getUser() ? $this->getUser()->getId() : null
            ]
        );
    }

    /**
     * @Route("/update_users/{id<[0-9]+>}", name="upadate_users")
     */
    public function updateUsers(Request $request,GnUser $user, EntityManagerInterface $manager, UserPasswordEncoderInterface $pwd_encode, $id): Response
    {
        if($request->getSession()->getFlashBag()->has('error_pwd')){
            $msg = $request->getSession()->getFlashBag()->get('error_pwd')[0];
          }
          else{
             $msg = null;
          }
          
        $utils_manager = $this->get(ServiceName::SRV_METIER_UTILS);
        // $_user_upload_manager = $this->get(ServiceName::SRV_METIER_USER_UPLOAD);
        $form = $this->createForm(GnAdminAccountType::class,$user);
        
        $form->handleRequest($request);//préparer capter la requête
        if($request->isMethod('POST') && $request->getSession()->has('user_name')){

            $user->setUpdateAt(new \DateTime());
            $pwd = $form->getData()->getUserPassword();
            $pwd_old = $request->request->get('old_password');
            $user_old = $request->getSession()->get('user_name');
            if ($pwd_encode->isPasswordValid($user_old,$pwd_old)){
                $user->setUserPassword($pwd_encode->encodePassword($user,$pwd));
            }else{
                $msg = $request->getSession()->getFlashBag()->add('error_pwd','Ancien mot de passe incorrect');
               return $this->redirectToRoute('upadate_users',['id' => $id]);
            }
            // foreach ($user_old->getGnRoles()->getValues() as $roles) {
            //   $user->addGnRole($roles);
            // }
            
            $utils_manager->saveEntity($user, 'update');
            $msg = $request->getSession()->getFlashBag()->add('success_account','utilisateur modifier avec succès');
            
            return $this->redirectToRoute('home', ['msg' => $msg]);
            
        }
        return $this->render('user/update_user.html.twig',[ 'recapData' => $this->data,'footer' => $this->footer,'form_user' => $form->createView(),'pays' => $this->pays,'user' => $user, 'msg' => $msg, 'title' => 'Modification']);
            
        
    }
   
}
