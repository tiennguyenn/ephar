<?php

namespace DoctorBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Http\RememberMe\TokenBasedRememberMeServices;
use UtilBundle\Utility\RouterAuthent;
use UtilBundle\Utility\Constant;

class DoctorListener {

    private $securityContext;
    private $router;
    private $container;
    private $userProvider;

    public function __construct(SecurityContextInterface $securityContext, Router $router, $container, $userProvider) {
        $this->securityContext = $securityContext;
        $this->router = $router;
        $this->container = $container;
        $this->userProvider = $userProvider;
    }

    /**
     * @author Bien
     */
    public function onKernelLoading(FilterControllerEvent $event) {

        $request = $event->getRequest();
        $token = $this->securityContext->getToken();
        $limit =  4*60*60 ;
        if (!empty($token)) {
            $user = $token->getUser();
            if(is_object($user)) {
                $userRole = $user->getRoles();
              
                if(in_array(Constant::TYPE_DOCTOR_NAME, $userRole)) {
                    $session = $this->container->get('session');
                    $lastRequest = $session->get('lastRequest');
                    $time = new \DateTime();             
                    if(empty($lastRequest)) {
                        $lastRequest = $time;
                        $session->set('lastRequest',$time);
                        $session->save();
                    }   
                    $diff = $time->getTimestamp()- $lastRequest->getTimestamp();
                    if($diff > $limit ) {
                        $session->remove('lastRequest');
                        $session->save();
                        $redirectUrl = $this->router->generate('logout');
                        $event->setController(function() use ($redirectUrl) {
                            return new RedirectResponse($redirectUrl);
                        });
                    } else {
                        $session->set('lastRequest',$time);
                        $session->save();
                    }
                    
                }
                
            }
        }
    }
}