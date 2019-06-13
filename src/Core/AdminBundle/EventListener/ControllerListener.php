<?php
namespace AdminBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Security\Acl\Exception\NoAceFoundException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Http\RememberMe\TokenBasedRememberMeServices;
use UtilBundle\Utility\Common;
use UtilBundle\Utility\RouterAuthent;
use UtilBundle\Utility\Constant;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class ControllerListener
{
    private $securityContext;
    private $router;
    private $container;
	private $userProvider;

    public function __construct(SecurityContextInterface $securityContext, Router $router, $container, $userProvider)
    {
        $this->securityContext = $securityContext;
        $this->router = $router;
        $this->container = $container;
		$this->userProvider = $userProvider;
	}

    /**
     * @author Toan Le
     */
    public function onKernelController(FilterControllerEvent $event)
    {

        $request = $event->getRequest();

        /* Decrypt urlId to ID */
        $attributes = $request->attributes->all();
        $routeParams = $request->attributes->get('_route_params');

        if ($this->container->hasParameter('help_url')) {
            $helpGmedsUrl = $this->container->getParameter('help_url');
            if (isset($attributes['_route'])) {
                $helpGmedsRoutes = Constant::HELP_GMEDS_ROUTES;
                $currentHost = $this->container->get('request')->getHost();
                if ($currentHost == $helpGmedsUrl) {
                    if (!in_array($attributes['_route'], $helpGmedsRoutes)) {
                        throw new NotFoundHttpException();
                    }
                } else {
                    if (in_array($attributes['_route'], $helpGmedsRoutes)) {
                        throw new NotFoundHttpException();
                    }
                }
            }
        }

        if ($routeParams) {
            foreach ($attributes as $key => $value) {
                if (!empty($value) && is_string($value) && array_key_exists($key, $routeParams) && $value == $routeParams[$key] && preg_match('/^'.Constant::HASHING_PREFIX.'(.)+$/', $value)) {
                    $value = Common::decodeHex($value);
                    $routeParams[$key] = $value;
                    $request->attributes->set($key, $value);
                }
            }
            $request->attributes->set('_route_params', $routeParams);
        }

        $publicRoutes = Constant::PUBLIC_ROUTES;
        $queries = $request->query->all();
        $params = $request->request->all();
        $requestParams = array_merge($params, $queries);
        if ($requestParams) {
          
            foreach ($requestParams as $key => $value) {
                if (!empty($value) && is_string($value) && preg_match('/^'.Constant::HASHING_PREFIX.'(.)+$/', $value)) {
                    $value = Common::decodeHex($value);
                    $request->query->set($key, $value);
                }

                if (!empty($value) && isset($attributes['_route']) &&in_array($attributes['_route'], $publicRoutes)) {
                    
                    if ($key == 'orderNumber' || $attributes['_route'] == 'failed_index') {
                        $value = Common::decryptTripleDes($value, $this->container->getParameter('tripledes_hashphrase'));
          
                        $request->query->set($key, $value);
                    }
                }
            }
        }
        

        /* End */

        // Matched route
        $_route  = $request->attributes->get('_route');

        $user = '';
        $redirectUrl = '';
        $pattern = '/(_(profiler|wdt)|css|images|js|assetic)/';
        // Get token authenticate
        $token = $this->securityContext->getToken();

        //Public route
        $publicRoutes = Constant::PUBLIC_ROUTES;
        if (empty($request->headers->get('user-agent'))) {
            $publicRoutes[] = 'pdf_rx';
            $publicRoutes[] = 'pdf_cif';
        }

        if (null !== $token) {
            $user = $token->getUser();
        }
		//echo serialize($token);die;
		if (!is_object($user)) {
			$providerKey = 'secured_area';
			$securityKey = $this->container->getParameter('secret');
			$rememberMeService = new TokenBasedRememberMeServices(
							array($this->userProvider),
							$securityKey, 
							$providerKey, 
							array(
							'path' => '/',
							'name' => 'GmedsRememberMeCookie',
							'domain' => null,
							'secure' => false,
							'httponly' => true,
							'lifetime' => 604800,
							'always_remember_me' => true,
							'remember_me_parameter' => '_remember_me')
						);

			$token = $rememberMeService->autoLogin($request);

			if (null !== $token) {
				$user = $token->getUser();

				$session = $request->getSession();
                $session->set('_security_secured_area', serialize($token));

                $this->container->get('security.context')->setToken($token);

                // set expired time to session
				$expiredTime = $this->container->getParameter('expire_time_login');
                $session->set('login_session_expired', time() + $expiredTime);
			}
		}

        //TODO : remove after release
        $matchesRoute = preg_match($pattern, $_route, $matches, PREG_OFFSET_CAPTURE);

        if(in_array($_route, $publicRoutes) || $_route == null || $matchesRoute){
            // Do some thing for public pages
        } else{
            // Note: Method use to check authen
            // RouterAuthent::checkRoute($this->container);
            if ( !is_object($user) ) {
                $redirectUrl = $this->router->generate('login');
            }else{

                $userRoles = $user->getRoles();
                $userRole = $userRoles[0];
                
                $privilege = $user->getPermissions();
                
                $found = false;
                foreach ($privilege as $route) {
                    if ($_route == $route || preg_match('/^(.+\,)?' . $_route . '(\,.+)?$/', $route)) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $redirectUrl = $this->router->generate('access_denied');
                }
                

                //Check doctor confirmed                    
                //Check doctor confirmed
                if(isset($userRole) && $userRole == Constant::TYPE_DOCTOR_NAME){
                    $em = $this->container->get('doctrine.orm.entity_manager');
                    $isTnCUpdated = false;
                    $doctor = $em->getRepository('UtilBundle:Doctor')->find($user->getId());
                    $doctorAgreement = $em->getRepository('UtilBundle:FileDocument')->getContentForClient(Constant::DOCUMENT_NAME_DOCTOR_AGREEMENT, Common::getCurrentSite($this->container));
                    if ($doctorAgreement) {
                        if ($doctorAgreement['createdAt'] > $doctor->getUpdatedTermCondition()) {
                            $isTnCUpdated = true;
                        }
                    }
                    $excludeRoutes = array('doctor_profile_create_getdependent', 'ajax_doctor_accept_tandc', 'ajax_doctor_edit', 'doctor_profile', 'admin_doctor_create_getdependent');
                    if( ($user->getIsConfirmed() <= Constant::STATUS_ACCEPT_TANDC || $user->getIsConfirmed() == null || $isTnCUpdated) && !in_array($_route, $excludeRoutes)){
                        $redirectUrl = $this->router->generate('doctor_profile');
                    }
                }
            }
        }

        if($redirectUrl != ''){
            $event->setController(function() use ($redirectUrl) {
                return new RedirectResponse($redirectUrl);
            });
        }
        
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        // You get the exception object from the received event
        // $exception = $event->getException();
        // if($exception->getStatusCode()){
        //     switch ($exception->getStatusCode()) {
        //         case 404:
        //             $response = new Response($this->container->get('templating')->render('AdminBundle:error:404.html.twig', array(
        //                 'exception' => $exception
        //             )));
        //             $event->setResponse($response);
        //             break;
        //         case 403:
        //             $response = new Response($this->container->get('templating')->render('AdminBundle:error:403.html.twig', array(
        //                 'exception' => $exception
        //             )));
        //             $event->setResponse($response);
        //             break;                
        //         case 500:
        //             $response = new Response($this->container->get('templating')->render('AdminBundle:error:500.html.twig', array(
        //                 'exception' => $exception
        //             )));
        //             $event->setResponse($response);
        //             break;  
        //         default:
        //             # code...
        //             break;
        //     }
        // }
    }
}
