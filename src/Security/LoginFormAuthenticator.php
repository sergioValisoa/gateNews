<?php 
namespace App\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use App\Repository\GnUserRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\PasswordUpgradeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class LoginFormAuthenticator extends AbstractAuthenticator
{
	private $userrepo;
	private $url;
    private $user;
	public function __construct(GnUserRepository $userrepo, UrlGeneratorInterface $url)
	{
		$this->user = $userrepo;
		$this->url = $url;
	}
	 public function supports(Request $request): ?bool{
	 	return $request->attributes->get('_route') === 'auth' && $request->isMethod('POST');//page d'authentification
	 }

    /**
     * Create a passport for the current request.
     *
     * The passport contains the user, credentials and any additional information
     * that has to be checked by the Symfony Security system. For example, a login
     * form authenticator will probably return a passport containing the user, the
     * presented password and the CSRF token value.
     *
     * You may throw any AuthenticationException in this method in case of error (e.g.
     * a UserNotFoundException when the user cannot be found).
     *
     * @throws AuthenticationException
     */
    public function authenticate(Request $request): PassportInterface{
    	//page de vérification
        $this->user = $this->user->findByLoginOrEmail($request->request->get('user_email'));//change
        //dd($this->user);

        $request->getSession()->set('email_old',$request->request->get('user_email'));
        if (!$this->user){
    		throw new CustomUserMessageAuthenticationException('invalid');
    	}

    	return new Passport(
    	    //$this->user,
            new UserBadge($this->user->getUserEmail()),
    		new PasswordCredentials($request->request->get('user_password')),
            [
    			new CsrfTokenBadge('login',$request->request->get('csrf_token')),
                new RememberMeBadge
    		]
    	);
    }
    /**
     * Create an authenticated token for the given user.
     *
     * If you don't care about which token class is used or don't really
     * understand what a "token" is, you can skip this method by extending
     * the AbstractAuthenticator class from your authenticator.
     *
     * @see AbstractAuthenticator
     *
     * @param PassportInterface $passport The passport returned from authenticate()
     */
    // public function createAuthenticatedToken(PassportInterface $passport, string $firewallName): TokenInterface{

    // }

    /**
     * Called when authentication executed and was successful!
     *
     * This should return the Response sent back to the user, like a
     * RedirectResponse to the last page they visited.
     *
     * If you return null, the current request will continue, and the user
     * will be authenticated. This makes sense, for example, with an API.
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response{

//on teste le role de l'utilisateur connecté, et on redirige selon ce dernier
        $roles = $this->user->getGnRoles();
        //dd($roles);
        $roles_name = [];
        foreach ($roles as $role)
        {
            $roles_name[] = $role->getRoleName();
        }
        if (in_array('ROLE_ADMIN', $roles_name))
        {
            return new RedirectResponse($this->url->generate('admin'));
        }
        elseif (in_array('ROLE_JOURNALIST', $roles_name))
        {
            return new RedirectResponse($this->url->generate('journalist'));
        }
        elseif (in_array('ROLE_USER', $roles_name))
        {
            return new RedirectResponse($this->url->generate('home'));
        }
        elseif (in_array('ROLE_USER', $roles_name))
        {
            return new RedirectResponse($this->url->generate('home'));
        }
    }

    /**
     * Called when authentication executed, but failed (e.g. wrong username password).
     *
     * This should return the Response sent back to the user, like a
     * RedirectResponse to the login page or a 403 response.
     *
     * If you return null, the request will continue, but the user will
     * not be authenticated. This is probably not what you want to do.
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response{
        $request->getSession()->getFlashBag()->add('error','incorrect');
        return new RedirectResponse($this->url->generate('auth'));

    }
}

 ?>