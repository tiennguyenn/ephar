<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AdminBundle\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use Google\Authenticator\GoogleAuthenticator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use AdminBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use AdminBundle\Security\GmedsUser;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use UtilBundle\Entity\MasterProxyAccount;
use UtilBundle\Entity\User;
use UtilBundle\Entity\UserRole;
use UtilBundle\Entity\UserActors;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Symfony\Component\Security\Http\RememberMe\TokenBasedRememberMeServices;
use UtilBundle\Utility\Common;
use UtilBundle\Utility\Constant;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use AgentBundle\Form\ChangePasswordType;
use UtilBundle\Utility\MsgUtils;
use Symfony\Component\HttpFoundation\Cookie;
use UtilBundle\Utility\Utils;

class PublicController extends BaseController
{

    /**
     * @author Toan Le
     * @Route("/single-session", name="single_session")
     */
    public function checkSingleSessionAction()
    {
        //debug
        //return;

        $em = $this->getDoctrine()->getManager();
        $curSid = $_COOKIE['PHPSESSID'];
        $curUser = $this->getUser();
        if($curUser != null){
            $userSid = $em->getRepository('UtilBundle:User')->find($curUser->getLoggedUser()->getId());
            if($userSid != null && $curSid != $userSid->getSessionId()){
                $session = $this->get('session');

                $session->remove('_security_secured_area');
                $session->remove('login_session_expired');
                $this->get('security.context')->setToken(null);
                
                return new JsonResponse([
                    'redirectUrl' => $this->generateUrl('login'),
                    'status'      => "1",
                ]);
            }
        } else {
            return new JsonResponse([
                'status'      => "2",
            ]);
        }
        return new JsonResponse([
            'status'    => "0"
        ]);
    }

    /**
     * @author Toan Le
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request) {

        $session = $request->getSession();
        // save current url to redirect to after login
        $session->set('redirectUrl', $session->get('_security.secured_area.target_path'));
        if ($session->has('_security_secured_area')) {
            $expire = intval($session->get('login_session_expired', ''));

            if ($expire != 0 && $expire > time()) {
                $user = $this->getUser();
                foreach ($user->getRoles() as $role) {
                    $roles[] = $role;
                }
                return $this->redirectToRoute(Constant::REDIRECT_ROLE[$roles[0]]);
            } else {
                // clear user token
                 $session->remove('_security_secured_area');
                 $session->remove('login_session_expired');
                 $this->get('security.context')->setToken(null);
            }
        }

        $baseUrl = rtrim($request->getUriForPath('/'), '/');
        $sites = $this->getParameter('sites');
        if (array_search(substr($baseUrl, strpos($baseUrl, '://') + 3), $sites) == 'parkway') {
            
        } else {
            $template = 'AdminBundle:login:non_parkway_login.html.twig';
        }
        $template = 'AdminBundle:login:parkway_login.html.twig';

        return $this->render($template,[
                'logoLink' => Constant::LOGIN_LINK,
            ]);
    }

    /**
     * check user login
     * @author Toan Le
     * @Route("/login-check", name="login_check")
    */
    public function loginCheckAction(Request $request) {

        $token = $request->request->get('token');
        if (false == $this->isCsrfTokenValid('core-login', $token)) {
            $arrMsg = array(
                'error'=> 'Invalid Token'
            );
            $this->get('session')->getFlashBag()->add('msg', $arrMsg);
            return $this->redirectToRoute('login');
        }

        $data = $request->request->all();

        $em = $this->getDoctrine()->getManager();

        $params = $data;
        $params['maxCountLogin'] = $this->getParameter('max_count_login');
        $params['timeout'] = $this->getParameter('timeout');
        $result = $em->getRepository('UtilBundle:User')->canLogin($params);
        if (!$result) {
            $arrMsg = array(
                'error'=> 'Your account has been locked for security reasons. Please try again after 30 minutes.'
            );
            $this->get('session')->getFlashBag()->add('msg', $arrMsg);
            return $this->redirectToRoute('login');
        }

        $result = $em->getRepository('UtilBundle:User')->login($data);

        if( $result['status'] ){
            $userData = null;
            $userCheck = $result['data'];
			$role = '';
            $permissions = [];
            $listPermissions = Constant::PERMISSIONS;

            $item = $userCheck->getRoles()->first();
            if (!empty($item)) {
                $role = $item->getName();
                if(isset($listPermissions[$item->getName()])) {
                    $permissions = $listPermissions[$item->getName()];
                }

            }
            $roles = array($role);
			if (!empty($role)) {
				$em->getRepository('UtilBundle:User')->updateLastLogin($userCheck);
				
				$platformSetting = $em->getRepository('UtilBundle:PlatformSettings')->getPlatFormSetting($data);
				$email = !isset($data['_username']) ? $userCheck->getEmailAddress() : $data['_username'];
				$pwd = !isset($data['_password']) ? $userCheck->getPasswordHash() : md5($data['_password']);
				$user = new GmedsUser($email, $pwd, null, $roles, $permissions);
                $userEmail = null;
                if ($userData) {
                    $userEmail = $userData->getEmailAddress();
                }
				
				$expiredTime = $this->container->getParameter('expire_time_login');

				$displayName = "";
				if($role == Constant::TYPE_DOCTOR_NAME || $role == Constant::TYPE_AGENT_NAME || $role == Constant::TYPE_SUB_AGENT_NAME ){
					$entity = null;
                    $doctor = null;
					if ($role == Constant::TYPE_AGENT_NAME || $role == Constant::TYPE_SUB_AGENT_NAME) {

                        $userActor = $em->getRepository('UtilBundle:UserActors')->findOneBy(array(
                            'user' => $userCheck
                        ));
                        $userEmail = null;
                        $userData = $userActor->getUser();
                        if ($userData) {
                            $userEmail = $userData->getEmailAddress();
                        }
                       
                        if ($userActor) {
                            $entity = $em->getRepository('UtilBundle:Agent')->find($userActor->getEntityId());                          
                        } else {
                            $entity = $em->getRepository('UtilBundle:Agent')->findOneBy(array(
                                'user' => $userCheck
                            ));
                        }
						
						$user->setId($entity->getId());
						$user->setIsConfirmed($entity->getIsConfirmed());

                        $user->setAvatar($entity->getProfilePhotoUrl());
                        if ($entity->getPersonalInformation()->getEmailAddress() != $userEmail && $userEmail != null) {
                            $user->setAvatar($userActor->getUser()->getProfilePhotoUrl());
                        }

					} else if($role == Constant::TYPE_DOCTOR_NAME){

                        $doctor = $em->getRepository('UtilBundle:Doctor')->findOneBy(array(
                            'user' => $userCheck
                        ));
                        $entity = $doctor;

						// To disable otp : check if !isset
                        if (!isset($data['otp_code'])) {
                            $user->setId($doctor->getId());
                            $user->setIsConfirmed($doctor->getIsConfirmed());

                            $user->setAvatar($doctor->getProfilePhotoUrl());

                            $user->setUpdatedTermCondition($doctor->getUpdatedTermCondition());
                            if ($doctor->getPersonalInformation()->getEmailAddress() != $userEmail && $userEmail != null) {
                                $user->setAvatar($userActor->getUser()->getProfilePhotoUrl());
                            } else {
                                $user->setAvatar($doctor->getProfilePhotoUrl());
                            }
                            $user->setUserCode($doctor->getDoctorCode());
                            $em->getRepository('UtilBundle:User')->removeOtpCode($userCheck);
                        } else {
                            $this->createOtpCode($em, $userCheck, $entity->getId());
                            $redirectParams = array(
                                'key' => Common::encryptTripleDes(json_encode(array(
                                    'email_address' => $userCheck->getEmailAddress()
                                )), $this->getParameter('tripledes_hashphrase'))
                            );
                            $response = new RedirectResponse($this->container->get('router')->generate('confirm_otp', $redirectParams));
                            return $response;
                        }
					} 
					$personalInformation = $entity ? $entity->getPersonalInformation() : ($doctor ? $doctor->getPersonalInformation() : null);
					if (empty($displayName) && $personalInformation) {
						$displayName = trim($personalInformation->getFirstName() . ' ' . $personalInformation->getLastName());
					}
				} else if( $role == Constant::TYPE_MPA){
                    //MPA
                    $mpa = $em->getRepository('UtilBundle:MasterProxyAccount')->findOneBy(array(
                        'user' => $userCheck
                    ));
                    if (isset($data['otp_code'])) {

                        if(!empty($mpa->getDeletedOn() )) {
                            return $this->redirectToRoute('logout');
                        }
                        $displayName = $mpa->getFamilyName() . ' ' . $mpa->getGivenName();
                        $user->setAvatar($mpa->getDocument()->getUrl());
                        $em->getRepository('UtilBundle:User')->removeOtpCode($userCheck);
                    } else {
                      
                        $this->createOtpCodeMpa($userCheck, $mpa);
                        $redirectParams = array(
                            'key' => Common::encryptTripleDes(json_encode(array(
                                'email_address' => $userCheck->getEmailAddress()
                            )), $this->getParameter('tripledes_hashphrase'))
                        );
                        $response = new RedirectResponse($this->container->get('router')->generate('confirm_otp', $redirectParams));
                        return $response;
                    }

                } else {
				    if ($role == Constant::TYPE_ADMIN_NAME) {
                        
                        $googleAuth = new GoogleAuthenticator();
                        $googleAuthSecret = $userCheck->getGoogleAuthSecret();

                        $redirectParams = array(
                            'key' => Common::encryptTripleDes(json_encode(array(
                                'email_address' => $userCheck->getEmailAddress()
                            )), $this->getParameter('tripledes_hashphrase'))
                        );

                        if (isset($data['google_auth_code'])) {
				            $isValidGoogleAuth = $googleAuth->checkCode($googleAuthSecret, $data['google_auth_code']);
				            if (!$isValidGoogleAuth) {
                                $arrMsg = array(
                                    'error'=>  'Invalid Google Authenticator code',
                                );
                                $this->get('session')->getFlashBag()->add('msg', $arrMsg);

                                return $this->redirectToRoute('confirm_google_auth_code', $redirectParams);
                            }
                        } else {
                            if ($googleAuthSecret) {
                                $response = new RedirectResponse(
                                    $this->container->get('router')->generate('confirm_google_auth_code', $redirectParams)
                                );
                                return $response;
                            }
                        }
                        
                    }
					$displayName = $userCheck->getFirstName() . ' ' . $userCheck->getLastName();
					$user->setId($userCheck->getId());
					$user->setAvatar($userCheck->getProfilePhotoUrl());
				}

				$user->setLoggedUser($userCheck);
				$user->setDisplayName($displayName);
				$user->setEmail($email);
				$user->setExpireAt(time() + $expiredTime);
				$user->setPlatformSetting($platformSetting);

				$token = new UsernamePasswordToken($user, null, 'secured_area', $roles);

				$response = new RedirectResponse($this->get('router')->generate(Constant::REDIRECT_ROLE[$role]));
				
				if(isset($data['_remember_me'])){
					$providerKey = 'secured_area';
					$securityKey = $this->getParameter('secret');
					
					$token = new RememberMeToken($user, $providerKey, $this->getParameter('secret'));

					$rememberMeService = new TokenBasedRememberMeServices(
									array($this->get('gmeds.user_provider')),
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

					$rememberMeService->loginSuccess($request, $response, $token);
				}
				$session = $this->get("session");

				$session->set('_security_secured_area', serialize($token));

				$this->get('security.context')->setToken($token);

				// set expired time to session
				$session->set('login_session_expired', time() + $expiredTime);
				
				$em->getRepository('UtilBundle:User')->updateSessionId($userCheck, $session->getId());

				$em->getRepository('UtilBundle:User')->resetLogin($userCheck);

				return $response;
			} else {
				$arrMsg = array(
					'error'=>  "Unknown user",
				);
				$this->get('session')->getFlashBag()->add('msg', $arrMsg);
				return $this->redirectToRoute('logout');
			}
        }else{
            $arrMsg = array(
                'error'=>  $result['message'],
            );
            $this->get('session')->getFlashBag()->add('msg', $arrMsg);

            $params['userIp'] = $request->getClientIp();
            $em->getRepository('UtilBundle:User')->incrementFailedLoginCount($params);
        }

        return $this->redirectToRoute($result['screen']);
    }

    /**     
     * Create random OTP     
     * @author Toan Le
     * @param $user
     * @return string     
     */    
    private function createOtpCode($em, $user, $doctorId){  

        $otpCode = rand(100000,999999);
        $userCheckOtp = $em->getRepository('UtilBundle:User')->findOneBy([
            'otpCode'  => $otpCode
        ]);
        while ( $userCheckOtp != null ) {
            $otpCode = rand(100000,999999);
            $userCheckOtp = $em->getRepository('UtilBundle:User')->findOneBy([
                'otpCode'  => $otpCode
            ]);
        }

        $expiredTime = $this->container->getParameter('expire_time_otp');  

        $em->getRepository('UtilBundle:User')->updateOtpCode($user, $otpCode, $expiredTime);
        $doctor = $em->getRepository('UtilBundle:Doctor')->find($doctorId);
        $doctorEmail = $doctor->getPersonalInformation()->getEmailAddress();
        $userEmail = $user->getEmailAddress();

        $phoneNumber = '';
        if ($doctorEmail != $userEmail) {
            $userActor = $em->getRepository('UtilBundle:UserActors')->findOneBy(array('user' => $user->getId()));
            $userPhone = $userActor->getContact();
            if ($userPhone) {
                $code = $userPhone->getCountry()->getPhoneCode();
                $phoneNumber = '+' . $code . $userPhone->getAreaCode() . $userPhone->getNumber();
            }
        } else {
            $doctorPhone = $doctor->getDoctorPhones()->first();
            if ($doctorPhone) {
                $contact = $doctorPhone->getContact();
                $type = $contact->getPhoneType()->getType();
                $code = $contact->getCountry()->getPhoneCode();
                $phoneNumber = '+' . $code . $contact->getAreaCode() . $contact->getNumber();
            }
        }
        
        $params = [
            'to'        => $phoneNumber,
            'message'   => 'Your OTP code is '.$otpCode
        ];

        $this->get('microservices.sms')->sendMessage($params);
    }


      /**
     * Create random OTP for mpa
     * @author bien
     * @param $user
     * @return string
     */
    private function createOtpCodeMpa($user, $mpa){
        $em = $this->getDoctrine()->getManager();
        $otpCode = rand(100000,999999);
        $userCheckOtp = $em->getRepository('UtilBundle:User')->findOneBy([
            'otpCode'  => $otpCode
        ]);
        while ( $userCheckOtp != null ) {
            $otpCode = rand(100000,999999);
            $userCheckOtp = $em->getRepository('UtilBundle:User')->findOneBy([
                'otpCode'  => $otpCode
            ]);
        }

        $expiredTime = $this->container->getParameter('expire_time_otp');

        $em->getRepository('UtilBundle:User')->updateOtpCode($user, $otpCode, $expiredTime);
        if(empty($mpa->getPhone())){
            return;
        }
        $code = $mpa->getPhone()->getCountry()->getPhoneCode();
        $phoneNumber =  '+' . $code  . $mpa->getPhone()->getNumber();


        $params = [
            'to'        => $phoneNumber,
            'message'   => 'Your OTP code is '.$otpCode
        ];

        $this->get('microservices.sms')->sendMessage($params);
    }

    /**
     * @author Toan Le
     * @Route("/confirm-otp", name="confirm_otp")
     */
    public function confirmOtpAction(Request $request) {
        $key = $request->get('key');
        $request = Common::decryptTripleDes($key, $this->getParameter('tripledes_hashphrase'));
        $requestParams = json_decode($request);
        if (isset($requestParams->email_address)) {
            return $this->render('AdminBundle:login:confirm-otp.html.twig', array(
                'userEmail' => $requestParams->email_address
            ));
        } else {
            return new RedirectResponse($this->get('router')->generate('login'));
        }
    }

    /**
     * @author Nanang Cahya
     * @Route("/ajax-resend-otp", name="ajax_resend_otp")
     */
    public function ajaxResendOtpCode(Request $request)
    {
        $res = array('success' => true);
        try{
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('UtilBundle:User')->findOneBy(array('emailAddress' => $request->get('email')));
            $entity = $em->getRepository('UtilBundle:Doctor')->findOneBy(array(
                'user' => $user
            ));
            $this->createOtpCode($em, $user, $entity->getId());
        } catch (\Exception $exception) {
            $res['success'] = false;
        }

        return new JsonResponse($res);
    }

    /**
     * @author Nanang Cahya
     * @Route("/confirm-google-auth-code", name="confirm_google_auth_code")
     */
    public function confirmGoogleAuthCodeAction(Request $request) {
        $key = $request->get('key');
        $request = Common::decryptTripleDes($key, $this->getParameter('tripledes_hashphrase'));
        $requestParams = json_decode($request);
        if (isset($requestParams->email_address)) {
            $emailAddress = $requestParams->email_address;
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('UtilBundle:User')->findOneBy(array('emailAddress' => $emailAddress));
            return $this->render('AdminBundle:login:confirm-google-auth-code.html.twig', array(
                'userEmail' => $user->getEmailAddress()
            ));
        } else {
            return new RedirectResponse($this->get('router')->generate('login'));
        }
    }

    /**
     * @author Toan Le
     * @Route("/logout", name="logout")
     */
    public function logoutAction(){

        $session = $this->get('session');

        $session->remove('_security_secured_area');
        $session->remove('login_session_expired');
        $this->get('security.context')->setToken(null);
		
		$response = new RedirectResponse($this->get('router')->generate('login'));

		$response->headers->clearCookie('GmedsRememberMeCookie');
		
		$response->send();
		
        return $response;
    }

    /**
     * @author Toan Le
     * @Route("/forgot-password", name="forgot_password")
     */
    public function forgotPasswordAction(Request $request)
    {
        $token = $request->request->get('token');
        if (false == $this->isCsrfTokenValid('forgot-password', $token)) {
            $arrMsg = array(
                'error'=> 'Invalid Token'
            );
            $this->get('session')->getFlashBag()->add('msg', $arrMsg);
            return $this->redirectToRoute('login');
        }

        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();

        $expiredTime = $this->container->getParameter('expire_time_forgot_password');

        $user = $em->getRepository('UtilBundle:User')->findOneBy(['emailAddress' => $data['email']]);
        if($user != null){

            $em->getRepository('UtilBundle:User')->udpateExpiredTime($user, $expiredTime);

            //get display name
            $roles = [];
            foreach ($user->getRoles() as $role) {
                $roles[] = $role->getName();
            }

            $userType = 0; //
            if($roles[0] == Constant::TYPE_DOCTOR_NAME || $roles[0] == Constant::TYPE_AGENT_NAME || $roles[0] == Constant::TYPE_SUB_AGENT_NAME) {
                $dataName = $em->getRepository('UtilBundle:User')->getFullNameById([
                    'role' => $roles[0],
                    'userId' => $user->getId(),
                ]);
                $displayName = $dataName['fullName'];
                
                if (!$displayName) {
                    $displayName = $user->getFirstName()." ".$user->getLastName();
                }

                if($roles[0] == Constant::TYPE_DOCTOR_NAME){
                    $userType = 1;
                } elseif($roles[0] == Constant::TYPE_AGENT_NAME || $roles[0] == Constant::TYPE_SUB_AGENT_NAME){
                    $userType = 2;
                }
            } else {
                $displayName = $user->getFirstName()." ".$user->getLastName();
            }

            //key reset password
            $keyResetPwd = Common::encryptTripleDes($data['email'], $this->getParameter('tripledes_hashphrase'));

            $info = [
                'url' => $this->generateUrl('change_password',['key'=>$keyResetPwd],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                'logoUrl' => $this->getParameter('base_url').'/bundles/admin/assets/pages/img/logo.png',
                'name' => $displayName,
                'userType' => $userType
            ];

            $body = $this->container->get('templating')->render('AdminBundle:emails:forgot-password.html.twig', $info);
            $params = array(
                'title' => 'Forgot Password',
                'body' => $body,
                'to' => $data['email'],
            );
            $this->container->get('microservices.sendgrid.email')->sendEmail($params);

            $arrMsg = array(
                'success'=>  "Please check email to get reset password link.",
            );
            $this->get('session')->getFlashBag()->add('msg', $arrMsg);
        }else{
            $arrMsg = array(
                'error'=>  "Account does not exist.",
            );
            $this->get('session')->getFlashBag()->add('msg', $arrMsg);
        }

        $response = new RedirectResponse($this->container->get('router')->generate('login'));
        return $response;
    }

    /**
     * @author Toan Le
     * @Route("/change-password", name="change_password")
     */
    public function changePasswordAction(Request $request)
    {
        $key = $request->get('key', "");
        $dataResponse = array(
            'status' => null,
            'message' => null
        );
        if(!empty($key)) {
            $email = Common::decryptTripleDes($key, $this->getParameter('tripledes_hashphrase'));

            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('UtilBundle:User')->findOneBy(['emailAddress' => $email]);
            $expiredTime = $user->getExpiredPasswordChange();
            $now = new \DateTime();

            if($expiredTime != null && $expiredTime > $now) {
                if($request->isMethod('POST')) {
                    $data = $request->get('ChangePasswordBundle_public', array());
                    if ($data['new_password'] != $data['confirm_password']) {
                        $arrMsg = array(
                            'error' => "Password does not match.",
                        );
                        $this->get('session')->getFlashBag()->add('msg', $arrMsg);
                    } else {
                        $em->getRepository('UtilBundle:User')->updatePassword($user, $data['new_password']);
                        $em->getRepository('UtilBundle:User')->udpateExpiredTime($user, null);
                        $arrMsg = array(
                            'success' => "Password change success.",
                        );
                        $this->get('session')->getFlashBag()->add('msg', $arrMsg);

                        return new RedirectResponse($this->container->get('router')->generate('login'));
                    }
                }

                //get display name
                $roles = [];
                foreach ($user->getRoles() as $role) {
                    $roles[] = $role->getName();
                }

                if($roles[0] == Constant::TYPE_DOCTOR_NAME) {
                    $userActor = $em->getRepository('UtilBundle:UserActors')->findOneBy(array('user' => $user));
					if ($userActor) {
						$doctorId = $userActor->getEntityId();
						$doctor = $em->getRepository('UtilBundle:Doctor')->find($doctorId);
						$pi = $doctor->getPersonalInformation();
						$displayName = $pi ? trim($pi->getTitle() . " " . $pi->getFirstName()." ".$pi->getLastName()) : trim($user->getFirstName()." ".$user->getLastName());
					} else {
						$displayName = $user->getFirstName()." ".$user->getLastName();
					}
                } else {
                    $displayName = $user->getFirstName()." ".$user->getLastName();
                }

                $optionsChangePassword = array(
                    'attr'               => array(
                        'id'    => 'change-password-form',
                        'class' => 'form-horizontal',
                        'action'=> $this->container->get('router')->generate('change_password'),
                    ),
                    'method'             => 'POST'
                );
                $formChangePassword = $this->createForm('AdminBundle\Form\ChangePasswordType', array(), $optionsChangePassword);
                $dataResponse = array(
                    'status' => 'success',
                    'formChangePassword' => $formChangePassword->createView(),
                    'name' => $displayName,
                    'key' => $key
                );

            } else {
                $dataResponse['message'] = MsgUtils::generate('msgTokenExpired');
            }
        } else {
            $dataResponse['message'] = MsgUtils::generate('msgTokenExpired');
        }

        return $this->render('AdminBundle:login:change-password.html.twig', $dataResponse);
    }

    /**
     * Doctor Setting Password
     * @Route("/setting-password", name="doctor_setting_password")
     * @author toan.le
     */
    public function doctorSettingPasswordAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $object = '';
        $id = $request->get('id', '');
        $type = $request->get('type', '');

        $currentRole = '';

        switch ($type) {
            case 1 :
                $object = $em->getRepository('UtilBundle:Doctor')->find($id);
                $currentRole = Constant::ID_DOCTOR;
                break;

            case 2 :
                $object = $em->getRepository('UtilBundle:Agent')->find($id);
                if (empty($object->getParent())) {
                    $currentRole = Constant::ID_AGENT;
                } else {
                    $currentRole = Constant::ID_SUB_AGENT;
                }
                break;

            default :
                break;
        }

        // confirmed object have to have value equal 3
        if ($object->getIsConfirmed() == Constant::STATUS_CONFIRM || $object->getIsConfirmed() == Constant::STATUS_UPDATE_PROFILE) {
            return $this->redirectToRoute('login');
        }
		
		// Check if user has been created 
		$user = $em->getRepository('UtilBundle:User')->findOneByEmailAddress($object->getPersonalInformation()->getEmailAddress());
        if ($user) {
            return $this->redirectToRoute('login');
        }
		
        if($request->isMethod('POST')) {
            $data = $request->get('ChangePasswordBundle_public', null);
            if($data['new_password'] != $data['confirm_password']) {
                $arrMsg = array(
                    'error'=>  "Password does not match.",
                );
                $this->get('session')->getFlashBag()->add('msg', $arrMsg);
                $response = false;
            } else {
				$role = $em->getRepository('UtilBundle:Role')->find($currentRole);
				
                $personalInformation = $object->getPersonalInformation();
                $user = new User();
                $user->setFirstName($personalInformation->getFirstName());
                $user->setLastName($personalInformation->getLastName());
                $user->setEmailAddress($personalInformation->getEmailAddress());
                $user->setPasswordHash(md5($data['new_password']));
                $user->setGlobalId(1);
                $user->setIsSuperUser(0);
                $user->setIsActive(1);
                $user->setIsLockedOut(0);
                $user->setIsLockoutEnabled(true);
                $user->addRole($role);
				
				$userActor = new UserActors();
				$userActor->setEntityId($object->getId());
				$userActor->setRole($role);
				
				$em->beginTransaction();
				try {
					$em->persist($user);
					$em->flush();

					if ($currentRole == Constant::ID_DOCTOR) {
					    if ($object->getSignatureUrl() && $object->getProfilePhotoUrl() && $object->getUpdatedTermCondition()) {
                            $object->setIsConfirmed(Constant::STATUS_UPDATE_PROFILE);
                        } else {
                            $object->setIsConfirmed(Constant::STATUS_CONFIRM);
                        }
                    } else {
                        $object->setIsConfirmed(Constant::STATUS_CONFIRM);
                    }
					$object->setUser($user);
					$em->persist($object);
					
					$userActor->setUser($user);
					$em->persist($userActor);
					$em->flush();
					
					$em->commit();
					
					//send mail
					$emailTo = $object->getPersonalInformation()->getEmailAddress();
					$mailTemplate = 'AdminBundle:emails:setting-password.html.twig';
					$mailParams = array(
						'logoUrl' => $this->getParameter('base_url').'/bundles/admin/assets/pages/img/logo.png',
						'doctorTitle' => $object->getPersonalInformation()->getTitle(true),
						'name' => $object->getPersonalInformation()->getFullName(),
						'url' => $this->generateUrl('login',[], UrlGeneratorInterface::ABSOLUTE_URL),
						'userType' => $type,
						'baseUrl' => $this->getParameter('base_url')
					);
                    if (1 == $type) {
                        $mailParams['name'] = $object->showName();
                    }
					$dataSendMail = array(
						'title'  => "Your G-MEDS login",
						'body'   => $this->container->get('templating')->render($mailTemplate, $mailParams),
						'to'     => $emailTo
					);
					$this->container->get('microservices.sendgrid.email')->sendEmail($dataSendMail);

					$response = true;
				} catch (\Exception $ex) {
					$em->rollback();
					
					$arrMsg = array(
						'error'=>  "There is an error when updating your password. Please try later.",
					);
					$this->get('session')->getFlashBag()->add('msg', $arrMsg);
					
					$response = false;
				}
            }
            return new JsonResponse($response);
        } else {
            $dataName = $object->getPersonalInformation();

            $displayName = $dataName->getFirstName(). ' ' .$dataName->getLastName();
            $optionsChangePassword = array(
                'attr' => array(
                    'id' => 'change-password-form',
                    'class' => 'form-horizontal'
                ),
                'method' => 'POST',
                'action' => $this->generateUrl('doctor_setting_password')
            );
            $formChangePassword = $this->createForm('AdminBundle\Form\ChangePasswordType', array(), $optionsChangePassword);

			$displayTxt = "Hello, " . $object->getPersonalInformation()->getTitle() . ' ' . $displayName.". Welcome to your G-MEDS account setup.";
			if ($type == 1) {
				$displayTxt = $object->showName() . ", welcome to your G-MEDS account setup.";
			} elseif ($type == 2) {
				$displayTxt = $displayName . ", welcome to your G-MEDS account setup.";
			}
            
            $baseUrl = rtrim($request->getUriForPath('/'), '/');
            $sites = $this->getParameter('sites');
            if (array_search(substr($baseUrl, strpos($baseUrl, '://') + 3), $sites) == 'parkway') {
                $logo_parkway = true;
            } else {
                $logo_parkway = false;
            }
			
            return $this->render('AdminBundle:login:setting-password.html.twig', [
                'formChangePassword' => $formChangePassword->createView(),
                'name' => $object->getPersonalInformation()->getFullName(),
				'email' => $object->getPersonalInformation()->getEmailAddress(),
                'id' => $id,
                'type' => $type,
                'displayTxt' => $displayTxt,
                'logo_parkway' => $logo_parkway
            ]);
        }
    }


    /**
     * Doctor Setting Password
     * @Route("/setting-mpa", name="mpa_setting_password")
     * @author toan.le
     */
    public function mpaSettingPasswordAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id', '');
        $object = $em->getRepository('UtilBundle:MasterProxyAccount')->find($id);
       // $object = new MasterProxyAccount();
        if($object->getIsConfirmed() || !empty($object->getUser())){
            return $this->redirectToRoute('login');
        }


        if($request->isMethod('POST')) {
            $data = $request->get('ChangePasswordBundle_public', null);

            if($data['new_password'] != $data['confirm_password']) {
                $arrMsg = array(
                    'error'=>  "Password does not match.",
                );
                $this->get('session')->getFlashBag()->add('msg', $arrMsg);
                $response = false;
            } else {
                $role = $em->getRepository('UtilBundle:Role')->find(Constant::ID_MPA);
                $user = new User();
                $user->setFirstName($object->getGivenName());
                $user->setLastName($object->getFamilyName());
                $user->setEmailAddress($object->getEmailAddress());
                $user->setPasswordHash(md5($data['new_password']));
                $user->setGlobalId(1);
                $user->setIsSuperUser(0);
                $user->setIsActive(1);
                $user->setIsLockedOut(0);
                $user->setIsLockoutEnabled(true);
                $user->addRole($role);
                $object->setUser($user);
                $object->setIsConfirmed(true);
                $em->persist($object);
                $em->flush();
                $response =  true;
                //send mail
                $emailTo = $object->getEmailAddress();
                $mailTemplate = 'AdminBundle:emails:setting-password-mpa.html.twig';
                $mailParams = array(
                    'logoUrl' => $this->getParameter('base_url').'/bundles/admin/assets/pages/img/logo.png',
                    'name' =>trim($object->getGivenName() . ' '. $object->getFamilyName()),
                    'url' => $this->generateUrl('login',[], UrlGeneratorInterface::ABSOLUTE_URL),
                    'baseUrl' => $this->getParameter('base_url')
                );

                $dataSendMail = array(
                    'title'  => "Your G-MEDS login",
                    'body'   => $this->container->get('templating')->render($mailTemplate, $mailParams),
                    'to'     => $emailTo
                );
                $this->container->get('microservices.sendgrid.email')->sendEmail($dataSendMail);

            }
            return new JsonResponse($response);
        } else {

            $displayName = $object->getGivenName(). ' ' .$object->getFamilyName();
            $optionsChangePassword = array(
                'attr' => array(
                    'id' => 'change-password-form',
                    'class' => 'form-horizontal'
                ),
                'method' => 'POST',
                'action' => $this->generateUrl('mpa_setting_password')
            );
            $formChangePassword = $this->createForm('AdminBundle\Form\ChangePasswordType', array(), $optionsChangePassword);

            $displayTxt = "Hello, "  . $displayName.". Welcome to your G-MEDS account setup.";

            $baseUrl = $request->getUriForPath('');
            $sites = $this->getParameter('sites');
            if (array_search(substr($baseUrl, strpos($baseUrl, '://') + 3), $sites) == 'parkway') {
                $logo_parkway = true;
            } else {
                $logo_parkway = false;
            }

            return $this->render('AdminBundle:login:setting-mpa.html.twig', array(
                'formChangePassword' => $formChangePassword->createView(),
                'name' => $displayName,
                'email' => $object->getEmailAddress(),
                'id' => $id,
                'displayTxt' => $displayTxt,
                'logo_parkway' => $logo_parkway
            ));
        }
    }
    
    /**
     * Login Doctor / Agent Setting Password
     * @Route("/login-setting-password", name="login_doctor_setting_password")
     * @author Cecep Redi
     */
    public function loginDoctorSettingPasswordAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $object = '';
        $id = $request->get('id', '');
        $type = $request->get('type', '');
        
        // Check if user exist
		$user = $em->getRepository('UtilBundle:User')->find($id);
        if ($user == null) {
            return $this->redirectToRoute('login');
        }
        
        // Check if user has login
        if ($user->getLastLogin() != null) {
            return $this->redirectToRoute('login');
        }
        
        if($request->isMethod('POST')) {
            $data = $request->get('ChangePasswordBundle_public', null);
            if($data['new_password'] != $data['confirm_password']) {
                $arrMsg = array(
                    'error'=>  "Password does not match.",
                );
                $this->get('session')->getFlashBag()->add('msg', $arrMsg);
                $response = false;
            } else {
                $user->setPasswordHash(md5($data['new_password']));
                $user->setUserIp(NULL);
				$em->beginTransaction();
				try {
					$em->persist($user);
					$em->flush();
                    
					$em->commit();
					
					//send mail
					$emailTo = $user->getEmailAddress();
					$mailTemplate = 'AdminBundle:emails:setting-password.html.twig';
					$mailParams = array(
						'logoUrl' => $this->getParameter('base_url').'/bundles/admin/assets/pages/img/logo.png',
						'doctorTitle' => '',
						'name' => $user->getFirstName(). ' ' .$user->getLastName(),
						'url' => $this->generateUrl('login',[], UrlGeneratorInterface::ABSOLUTE_URL),
						'userType' => $type,
						'baseUrl' => $this->getParameter('base_url')
					);
					$dataSendMail = array(
						'title'  => "Your G-MEDS login",
						'body'   => $this->container->get('templating')->render($mailTemplate, $mailParams),
						'to'     => $emailTo
					);
					$this->container->get('microservices.sendgrid.email')->sendEmail($dataSendMail);

					$response = true;
				} catch (\Exception $ex) {
					$em->rollback();
					
					$arrMsg = array(
						'error'=>  "There is an error when updating your password. Please try later.",
					);
					$this->get('session')->getFlashBag()->add('msg', $arrMsg);
					
					$response = false;
				}
            }
            return new JsonResponse($response);
        } else {
            $displayName = $user->getFirstName(). ' ' .$user->getLastName();
            $optionsChangePassword = array(
                'attr' => array(
                    'id' => 'change-password-form',
                    'class' => 'form-horizontal'
                ),
                'method' => 'POST',
                'action' => $this->generateUrl('login_doctor_setting_password')
            );
            $formChangePassword = $this->createForm('AdminBundle\Form\ChangePasswordType', array(), $optionsChangePassword);
			$displayTxt = "Hello, " . $displayName.". Welcome to your G-MEDS account setup.";
            
            $baseUrl = rtrim($request->getUriForPath('/'), '/');
            $sites = $this->getParameter('sites');
            if (array_search(substr($baseUrl, strpos($baseUrl, '://') + 3), $sites) == 'parkway') {
                $logo_parkway = true;
            } else {
                $logo_parkway = false;
            }
			
            return $this->render('AdminBundle:login:setting-password.html.twig', [
                'formChangePassword' => $formChangePassword->createView(),
                'name' => $displayName,
				'email' => $user->getEmailAddress(),
                'id' => $id,
                'type' => $type,
                'displayTxt' => $displayTxt,
                'logo_parkway' => $logo_parkway
            ]);
        }
    }

    /**
     * @author toan.le
     * @Route("/403", name="access_denied")
     */
    public function accessDeniedAction(Request $request){
        return $this->render('AdminBundle:error:403.html.twig',[ ]);
    }

    /**
     * @author toan.le
     * @Route("/500", name="internal_server_error")
     */
    public function internalServerErrorAction(Request $request){
        return $this->render('AdminBundle:error:500.html.twig',[ ]);
    }

    /**
     * @author toan.le
     * @Route("/404", name="not_found")
     */
    public function notFoundAction(Request $request){
        return $this->render('AdminBundle:error:404.html.twig',[ ]);
    }

    /**
     * warning page
     * @Route("/device-warning", name="device_warning")
     */
    public function deviceWarningAction(Request $request)
    {
        $params = array(
            'name' => $request->get('name', null),
            'orderNumber' => $request->get('orderNumber', null)
        );
        return $this->render('AdminBundle:error:device_warning.html.twig', $params);
    }

    /**
     * pdf cif document
     * @Route("/cif/{orderNumber}", name="pdf_cif")
     * @author vinh.nguyen
     */
    public function pdfCIFAction(Request $request, $orderNumber)
    {
        $loggedInUser = $this->getUser();
        $orderNumber = str_replace(array('-','_cif'), '', strtolower($orderNumber));
        $copy = intval($request->get('copy', 0));
        $em = $this->getDoctrine()->getManager();
        $results = $em->getRepository('UtilBundle:Rx')->getContentCIF($orderNumber);
        $psObj = $em->getRepository('UtilBundle:PlatformSettings')->findOneBy([]);

        if (!$loggedInUser) {
            $deniedTemplate = $this->renderView('AdminBundle:error:403.html.twig',[ ]);
            Common::restrictFileAccess($this->container, $deniedTemplate);
        } else {
            $doctorId = $results['info']['doctorId'];
            $loggedInDoctorId = $loggedInUser->getId();
            if ($doctorId != $loggedInDoctorId) {
                return $this->redirectToRoute('not_found');
            }
        }

        if(!empty($results['info'])) {

            //format TaxId
            $results['info']['taxId'] = Utils::formatTaxId($results['info']['taxId']);
            //fx rate           
            $fxRate = array();
            foreach($results['fxRate'] as $item) {
                $currency = $item['currencyTo'];
                $fxRate[$currency] = $item['rate'];
            }
            $results['fxRate'] = $fxRate;
            $results['copy'] = ($copy > 0)? ($copy - 1): 0;
            $results['baseUrl'] = $this->getParameter('base_url');
            $results['psBufferRate'] = $psObj->getBufferRate();

            // STRIKE 705
            $results['hasZrsGST'] = false;
            $isLocalPatient = $em->getRepository('UtilBundle:Rx')
                ->isLocalPatient(array('patientId' => $results['info']['patientId']));
            if (!$isLocalPatient) {
                $doctorGstSetting = $em->getRepository('UtilBundle:DoctorGstSetting')
                    ->findOneBy(array(
                        'doctor' => $results['info']['doctorId'],
                        'feeType' => Constant::SETTING_GST_MEDICINE,
                        'area' => 'overseas'
                    ));

                if ($doctorGstSetting) {
                    $gst = $doctorGstSetting->getGst();
                    $results['hasZrsGST'] = $gst && $gst->getCode() == Constant::GST_ZRS ? true : false;
                }
            }
            // End STRIKE 705

            $template = 'AdminBundle:pdf:cif.html.twig';
            $html = $this->container->get('templating')->render($template, $results);

            $options = new Options();
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $output = $dompdf->output();

            $response = new Response();
            $response->setContent($output);
            $response->setStatusCode(200);
            $response->headers->set('Content-Type', 'application/pdf');

            return $response;
        }  else {
            return $this->redirectToRoute('not_found');
        }
    }

    /**
     * pdf shipping Normal | Cold Chain
     * @Route("/shipping-pdf/{orderNumber}", name="pdf_shipping_label")
     */
    public function pdfShippingLabelAction(Request $request, $orderNumber)
    {
        $orderNumber = strtolower($orderNumber);
        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository('UtilBundle:Rx')->getShippingLabel($orderNumber);
        if(!empty($result['info'])) {
            $template = 'AdminBundle:pdf:shipping-label.html.twig';
            $html = $this->container->get('templating')->render($template, $result);

            $options = new Options();
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $output = $dompdf->output();

            $response = new Response();
            $response->setContent($output);
            $response->setStatusCode(200);
            $response->headers->set('Content-Type', 'application/pdf');

            return $response;
        }  else {
            return $this->redirectToRoute('not_found');
        }
    }

    /**
     * testing
     * @Route("/atest", name="atest_url")
     */
    public function aTestAction(Request $request)
    {
        $type = $request->get('t', null);
        $id = $request->get('id', null);
        $em = $this->getDoctrine()->getManager();
        if($id) {
            echo Common::encodeHex($id);
        } else {
            $res = $this->get('microservices.fx')->getCurrencyExchangeMY();
            dump($res);
        }
        die;
        return $this->redirectToRoute('not_found');
    }
}
