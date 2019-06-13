<?php

namespace AdminBundle\Controller;

use AdminBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use UtilBundle\Entity\Phone;
use UtilBundle\Entity\User;
use UtilBundle\Entity\UserActors;
use UtilBundle\Entity\Log;
use UtilBundle\Utility\Common;
use UtilBundle\Utility\Constant;
use UtilBundle\Utility\Utils;
use AdminBundle\Form\AgentLoginAdminType;
use AdminBundle\Form\DoctorLoginAdminType;

/* 
 * Author: Tuan Nguyen
 *
 */
class LoginController extends BaseController 
{
    /**
     * @Route("/admin/agent/{id}/agent-login", name="admin_agent_login")
	 * @Route("/admin/sub-agent/{id}/sub-agent-login", name="admin_sub_agent_login")
     */
    public function agentLoginAction(Request $request,$id)
    {
        $em = $this->getDoctrine()->getEntityManager();  
        $agent = $em->getRepository('UtilBundle:Agent')->find($id);
        $parameters=  array(
            'ajaxURL' => 'admin_login_ajax',
            'updateStatusUrl' => 'admin_login_ajax',
            'agentId' => $id,
			'agent' => $agent,
            'agentName' => $agent->getPersonalInformation()->getFullName() 
            );

        return $this->render('AdminBundle:login:agent_login.html.twig',$parameters);
    }
	
    /**
     * @Route("/admin/agent/{id}/agent-login/create", name="admin_agent_create_login")
	 * @Route("/admin/sub-agent/{id}/sub-agent-login/create", name="admin_sub_agent_create_login")
    */
    public function agentLoginCreateAction(Request $request, $id)
    {
		$em = $this->getDoctrine()->getManager();
		
		$agent = $em->getRepository('UtilBundle:Agent')->find($id);
		if (!$agent) {
			throw new NotFoundHttpException('Agent Not Found');
		}
		
		$form = $this->createForm(new AgentLoginAdminType(array('agent_login' => null)), array(), array());       
        
		$form->handleRequest($request);
		
		$errors = array();
		
		if ($form->isSubmitted() && $form->isValid()) {  
			$data = $request->request->get('admin_agent_login');
			$data = Common::removeSpaceOf($data);
			if (!isset($_FILES['admin_agent_login']['name']['photo']) 
				|| empty($_FILES['admin_agent_login']['name']['photo']) 
				|| $_FILES["admin_agent_login"]['error']['photo']) {
				$errors['photo'] = 'This field is required.';
			}

			$pi = $em->getRepository('UtilBundle:PersonalInformation')->findOneByEmailAddress($data['email']);
			$user = $em->getRepository('UtilBundle:User')->findOneByEmailAddress($data['email']);

			if ($pi || $user) {
				$errors['email'] = 'This email address is existed already.';
			}
			
			if (empty($errors))
			{
                $user = new User();
				$user->setFirstName($data['firstName']);
				$user->setLastName($data['lastName']);
                $user->setEmailAddress($data['email']);
				$user->setGender($data['gender']);
				$password = Utils::generatePassword();
                $user->setPasswordHash(md5($password));
                $user->setGlobalId(1);
                $user->setIsSuperUser(0);
                $user->setIsActive(1);
                $user->setIsLockedOut(0);
                $user->setIsLockoutEnabled(true);
				$roleId = $agent->getParent() ? Constant::SUB_AGENT_ROLE : Constant::AGENT_ROLE;
                $role = $em->getRepository('UtilBundle:Role')->find($roleId);
                $user->addRole($role);
                $user->setUserIp('password_not_set');
				
				$userActor = new UserActors();
				$userActor->setRole($role);
				$userActor->setEntityId($id);
				if (isset($data['privilege']) && !empty($data['privilege'])) {
					$userActor->setPrivilege($data['privilege']);
				}
				
				$em->beginTransaction();
				try {
					$em->persist($user);
					$em->flush();
					
					$userActor->setUser($user);
					$em->persist($userActor);
					$em->flush();
					
					$em->commit();
				} catch (\Exception $ex) {
					$em->rollback();
					$errors['error'] = $ex->getMessage();
				}
				
				if (empty($errors)) {
					$file = array();
					$file['name'] = $_FILES["admin_agent_login"]['name']['photo'];
					$file['type'] = $_FILES["admin_agent_login"]['type']['photo'];
					$file['tmp_name'] = $_FILES["admin_agent_login"]['tmp_name']['photo'];
					$file['error'] = $_FILES["admin_agent_login"]['error']['photo'];
					$file['size'] = $_FILES["admin_agent_login"]['size']['photo'];
					$profile = $this->uploadfile($file, 'agent/login_profile_' . $user->getId());                    
					if(!empty($profile)) {
						$user->setProfilePhotoUrl($profile);
						$em->persist($user);
						$em->flush();
					}
					
					$this->sendLoginEmail($user, 'agent', $password, 'sub_login');
					
					if ($agent->getParent()) {
						$request->getSession()
							->getFlashBag()
							->add("success", "A sub agent login has been created and emailed to \"{$data['email']}\".");
							
						return $this->redirectToRoute('admin_sub_agent_login', array('id' => $id)); 
					} else {
						$request->getSession()
							->getFlashBag()
							->add("success", "An agent login has been created and emailed to \"{$data['email']}\".");
							
						return $this->redirectToRoute('admin_agent_login', array('id' => $id)); 
					}
				}
			}
		}
		
        $parameters = array(
            'form' => $form->createView(),   
            'successUrl' => $agent->getParent() ? 'admin_sub_agent_login' : 'admin_agent_login',
            'agentId' => $id,
			'user' => null,
			'agentLogin' => null,
			'agent' => $agent,
            'title' => $agent->getParent() ? 'Register Sub Agent Login' : 'Register Agent Login',
			'errors' => $errors
        );
        
        return $this->render('AdminBundle:login:agent_login_form.html.twig', $parameters);
	}
	
    /**
     * @Route("/admin/agent/{id}/agent-login/{loginId}/edit", name="admin_agent_login_edit")
	 * @Route("/admin/sub-agent/{id}/sub-agent-login/{loginId}/edit", name="admin_sub_agent_login_edit")
    */
    public function agentLoginEditAction(Request $request, $id, $loginId)
    {
		$em = $this->getDoctrine()->getManager();
		
		$userActor = $em->getRepository('UtilBundle:UserActors')->find($loginId);
		
		$user = $userActor->getUser();
		
		$agent = $em->getRepository('UtilBundle:Agent')->find($userActor->getEntityId());
		
		$form = $this->createForm(new AgentLoginAdminType(array('agent_login' => $userActor)), array(), array());       
		
		$form->handleRequest($request);
		
		$errors = array();
		
		if ($form->isSubmitted() && $form->isValid()) {  
			$data = $request->request->get('admin_agent_login');
			$data = Common::removeSpaceOf($data);
			if (isset($_FILES['admin_agent_login']['name']['photo']) 
				&& !empty($_FILES['admin_agent_login']['name']['photo']) 
				&& $_FILES["admin_agent_login"]['error']['photo']) {
				$errors['photo'] = 'Uploaded file has an error.';
			}
			
			if (empty($errors))
			{
				if (isset($_FILES["admin_agent_login"]['name']['photo'])) {
					$file = array();
					$file['name'] = $_FILES["admin_agent_login"]['name']['photo'];
					$file['type'] = $_FILES["admin_agent_login"]['type']['photo'];
					$file['tmp_name'] = $_FILES["admin_agent_login"]['tmp_name']['photo'];
					$file['error'] = $_FILES["admin_agent_login"]['error']['photo'];
					$file['size'] = $_FILES["admin_agent_login"]['size']['photo'];
					$profile = $this->uploadfile($file, 'agent/login_profile_' . $user->getId());                    
					if(!empty($profile)) {
						$user->setProfilePhotoUrl($profile);
					}
				}
				
				$user->setFirstName($data['firstName']);
				$user->setLastName($data['lastName']);
				$user->setGender($data['gender']);
				
				if (!isset($data['privilege']) || empty($data['privilege'])) {
					$userActor->setPrivilege(null);
				} else {
					$userActor->setPrivilege($data['privilege']);
				}
				
				$em->beginTransaction();
				try {
					$em->persist($user);
					$em->persist($userActor);
					$em->flush();
					
					$em->commit();
				} catch (\Exception $ex) {
					$em->rollback();
					$errors['error'] = $ex->getMessage();
				}
				
				if (empty($errors)) {
					if ($agent->getParent()) {
						$request->getSession()
							->getFlashBag()
							->add("success", "The sub agent login has been updated successful.");
							
						return $this->redirectToRoute('admin_sub_agent_login', array('id' => $id)); 
					} else {
						$request->getSession()
							->getFlashBag()
							->add("success", "The agent login has been updated successful.");
							
						return $this->redirectToRoute('admin_agent_login', array('id' => $id)); 
					}
				}
			}
		} else {
			$errors = $form->getErrors();
			foreach ($errors as $error) {
				$errors['error'] = $error->getMessage();
			}
		}
		
        $parameters = array(
            'form' => $form->createView(),      
            'successUrl' => $agent->getParent() ? 'admin_sub_agent_login' : 'admin_agent_login',
            'agentId' => $id,
			'user' => $user,
			'agentLogin' => $userActor,
			'agent' => $agent,
            'title' => $agent->getParent() ? 'Edit Sub Agent Login' : 'Edit Agent Login',
			'errors' => $errors
        );
        
        return $this->render('AdminBundle:login:agent_login_form.html.twig', $parameters);
	}
	

    /**
     * @Route("/admin/doctor/{id}/doctor-login/create", name="admin_doctor_create_login")
    */
    public function doctorLoginCreateAction(Request $request, $id)
    {
		$em = $this->getDoctrine()->getManager();
		
		$doctor = $em->getRepository('UtilBundle:Doctor')->find($id);
		if (!$doctor) {
			throw new NotFoundHttpException('Doctor Not Found');
		}

        $country = $em->getRepository('UtilBundle:Country')->getByPreferredCountry(true);
		$form = $this->createForm(new DoctorLoginAdminType(array(
		    'doctor_login' => null,
            'phone_country' => $country
        )), array(), array());
        
		$form->handleRequest($request);
		
		$errors = array();
		
		if ($form->isSubmitted() && $form->isValid()) {  
			$data = $request->request->get('admin_doctor_login');
			$data = Common::removeSpaceOf($data);
			if (!isset($_FILES['admin_doctor_login']['name']['photo']) 
				|| empty($_FILES['admin_doctor_login']['name']['photo']) 
				|| $_FILES["admin_doctor_login"]['error']['photo']) {
				$errors['photo'] = 'This field is required.';
			}
			
			$pi = $em->getRepository('UtilBundle:PersonalInformation')->findOneByEmailAddress($data['email']);
			$user = $em->getRepository('UtilBundle:User')->findOneByEmailAddress($data['email']);

			if ($pi || $user) {
				$errors['email'] = 'This email address is existed already.';
			}
			
			if (empty($errors))
			{
                $user = new User();
				$user->setFirstName($data['firstName']);
				$user->setLastName($data['lastName']);
                $user->setEmailAddress($data['email']);
				$user->setGender($data['gender']);
				$password = Utils::generatePassword();
                $user->setPasswordHash(md5($password));
                $user->setGlobalId(1);
                $user->setIsSuperUser(0);
                $user->setIsActive(1);
                $user->setIsLockedOut(0);
                $user->setIsLockoutEnabled(true);
                $role = $em->getRepository('UtilBundle:Role')->find(Constant::DOCTOR_ROLE);
                $user->addRole($role);
                $user->setUserIp('password_not_set');

                $phone = new Phone();
                $country = $em->getRepository('UtilBundle:Country')->find($data['phoneLocation']);
                $phone->setCountry($country);
                $phone->setPhoneType($em->getRepository('UtilBundle:PhoneType')->getPhone());
                $phone->setAreaCode($data['phoneArea']);
                $phone->setNumber($data['phone']);
				
				$userActor = new UserActors();
				$userActor->setRole($role);
				$userActor->setEntityId($id);
				$userActor->setContact($phone);
				if (isset($data['privilege']) && !empty($data['privilege'])) {
					$userActor->setPrivilege($data['privilege']);
				}
				
				$em->beginTransaction();
				try {
					$em->persist($user);
					$em->flush();
					
					$userActor->setUser($user);
					$em->persist($userActor);
					$em->flush();
					
					$em->commit();
				} catch (\Exception $ex) {
					$em->rollback();
					$errors['error'] = $ex->getMessage();
				}
				
				if (empty($errors)) {
					$file = array();
					$file['name'] = $_FILES["admin_doctor_login"]['name']['photo'];
					$file['type'] = $_FILES["admin_doctor_login"]['type']['photo'];
					$file['tmp_name'] = $_FILES["admin_doctor_login"]['tmp_name']['photo'];
					$file['error'] = $_FILES["admin_doctor_login"]['error']['photo'];
					$file['size'] = $_FILES["admin_doctor_login"]['size']['photo'];
					$profile = $this->uploadfile($file, 'doctor/login_profile_' . $user->getId());                    
					if(!empty($profile)) {
						$user->setProfilePhotoUrl($profile);
						$em->persist($user);
						$em->flush();
					}
					
					$this->sendLoginEmail($user, 'doctor', $password);
					
					$request->getSession()
						->getFlashBag()
						->add("success", "A doctor login has been created and emailed to \"{$data['email']}\".");
						
					return $this->redirectToRoute('admin_doctor_login', array('id' => $id)); 
				}
			}
		}
		
        $parameters = array(
            'form' => $form->createView(),   
            'successUrl' => 'admin_doctor_login',
            'doctorId' => $id,
			'user' => null,
			'doctorLogin' => null,
            'title' => 'Register Doctor Login',
			'errors' => $errors
        );
        
        return $this->render('AdminBundle:login:doctor_login_form.html.twig', $parameters);
	}
	
    /**
     * @Route("/admin/doctor/{id}/doctor-login/{loginId}/edit", name="admin_doctor_login_edit")
    */
    public function doctorLoginEditAction(Request $request, $id, $loginId)
    {
		$em = $this->getDoctrine()->getManager();
		
		$userActor = $em->getRepository('UtilBundle:UserActors')->find($loginId);
		
		$user = $userActor->getUser();

        $country = $em->getRepository('UtilBundle:Country')->getByPreferredCountry(true);
		$form = $this->createForm(new DoctorLoginAdminType(array(
		    'doctor_login' => $userActor,
            'phone_country' => $country
        )), array(), array());
		
		$form->handleRequest($request);
		
		$errors = array();
		
		if ($form->isSubmitted() && $form->isValid()) {  
			$data = $request->request->get('admin_doctor_login');
			$data = Common::removeSpaceOf($data);
			if (isset($_FILES['admin_doctor_login']['name']['photo']) 
				&& !empty($_FILES['admin_doctor_login']['name']['photo']) 
				&& $_FILES["admin_doctor_login"]['error']['photo']) {
				$errors['photo'] = 'Uploaded file has an error.';
			}
			
			if (empty($errors))
			{
				if (isset($_FILES["admin_doctor_login"]['name']['photo'])) {
					$file = array();
					$file['name'] = $_FILES["admin_doctor_login"]['name']['photo'];
					$file['type'] = $_FILES["admin_doctor_login"]['type']['photo'];
					$file['tmp_name'] = $_FILES["admin_doctor_login"]['tmp_name']['photo'];
					$file['error'] = $_FILES["admin_doctor_login"]['error']['photo'];
					$file['size'] = $_FILES["admin_doctor_login"]['size']['photo'];
					$profile = $this->uploadfile($file, 'doctor/login_profile_' . $user->getId());                    
					if(!empty($profile)) {
						$user->setProfilePhotoUrl($profile);
					}
				}
				
				$user->setFirstName($data['firstName']);
				$user->setLastName($data['lastName']);
				$user->setGender($data['gender']);
				
				if (!isset($data['privilege']) || empty($data['privilege'])) {
					$userActor->setPrivilege(null);
				} else {
					$userActor->setPrivilege($data['privilege']);
				}
                $phone = $userActor->getContact();
				if (!$phone) {
                    $phone = new Phone();
                }
                $country = $em->getRepository('UtilBundle:Country')->find($data['phoneLocation']);
                $phone->setCountry($country);
                $phone->setPhoneType($em->getRepository('UtilBundle:PhoneType')->getPhone());
                $phone->setAreaCode($data['phoneArea']);
                $phone->setNumber($data['phone']);
                $userActor->setContact($phone);

				$em->beginTransaction();
				try {
					$em->persist($user);
					$em->persist($userActor);
					$em->flush();
					
					$em->commit();
				} catch (\Exception $ex) {
					$em->rollback();
					$errors['error'] = $ex->getMessage();
				}
				
				if (empty($errors)) {
					$request->getSession()
						->getFlashBag()
						->add("success", "A doctor login has been updated successful.");
						
					return $this->redirectToRoute('admin_doctor_login', array('id' => $id)); 
				}
			}
		} else {
			$errors = $form->getErrors();
			foreach ($errors as $error) {
				$errors['error'] = $error->getMessage();
			}
		}
		
        $parameters = array(
            'form' => $form->createView(),      
            'successUrl' => 'admin_doctor_login',
            'doctorId' => $id,
			'user' => $user,
			'doctorLogin' => $userActor,
            'title' => 'Edit Doctor Login',
			'errors' => $errors
        );
        
        return $this->render('AdminBundle:login:doctor_login_form.html.twig', $parameters);
	}
	
	/**
     * @Route("/admin/login-ajax", name="admin_login_ajax")
     */
    public function loginAjaxAction(Request $request)
    {
		$action = $request->get('action', '');
		switch ($action) {
			case 'get_list':
				return $this->getListLogin($request);
			case 'get_suggestion':
				return $this->getSuggestion($request);
			case 'change_status':
				return $this->changeStatus($request);
			case 'delete':
				return $this->deleteLogin($request);
		}
	}

    /**
     * @Route("/admin/resend-welcome-email", name="admin_resend_welcome_email")
     */
    public function resendWelcomeEmailAction(Request $request)
    {
        $loginId = $request->get('loginId', 0);
        $role = $request->get('role', 'doctor');

        $em = $this->getDoctrine()->getManager();
        $userActor = $em->getRepository('UtilBundle:UserActors')->find($loginId);

        $user = $userActor->getUser();
        $password = Utils::generatePassword();
        $result = array('success' => 0);

        try {
            $this->sendLoginEmail($user, $role, $password, 'sub_login');
            $result['success'] = 1;
        } catch (\Exception $ex) {
            $result['success'] = 0;
        }

        return new JsonResponse($result);
    }
	
	private function getListLogin($request)
	{
        $em = $this->getDoctrine()->getManager();
		
		$result = array();
		
		$role = $request->get('role', '');
		if ($role == 'agent') {
			$result = $em->getRepository('UtilBundle:UserActors')->getAgentLogins($request);
		} elseif ($role == 'doctor') {
			$result = $em->getRepository('UtilBundle:UserActors')->getDoctorLogins($request);
		}

        return new JsonResponse($result);
	}
	
	private function getSuggestion($request)
	{
		$em = $this->getDoctrine()->getManager();
		
		$result = $em->getRepository('UtilBundle:UserActors')->getSuggestion($request);
		
		return new JsonResponse($result);
	}
	
	private function changeStatus($request)
	{
		$result = array('success' => 0);
		$em = $this->getDoctrine()->getManager();
		
		$id = $request->get('id', null);
		$role = $request->get('role', '');
		$userActor = $em->getRepository('UtilBundle:UserActors')->find($id);
		if ($userActor) {
			$user = $userActor->getUser();
            $status = $user->getIsActive();
			$oldData = array('isActive' => $status);
            $status = $status ? 0 : 1;
			$newData = array('isActive' => $status);
            $user->setIsActive($status);
            
            // logging
            $author = $this->getUser()->getLoggedUser()->getFirstName() . ' ' .
			$this->getUser()->getLoggedUser()->getLastName();
			$log = new Log();
			$log->setEntityId($id);
			$log->setTitle($role . '_login_status_changed');
			$log->setAction("update");
			$log->setModule($role . '_login');
			$log->setOldValue(json_encode($oldData));
			$log->setNewValue(json_encode($newData));
			$log->setCreatedBy($author);
			$log->setCreatedOn(new \DateTime());
			
			$em->beginTransaction();
			try {
				$em->persist($user);
				$em->persist($log);
				$em->flush();
				
				$em->commit();
				$result['success'] = 1;
			} catch (\Exception $ex) {
				$em->rollback();
				$result['message'] = "There is an error when updating $role login status";
			}
		} else {
			$result['message'] = ucfirst($role) . " Login not found.";
		}
		
		return new JsonResponse($result);
	}
	
	private function deleteLogin($request)
	{
		$result = array('success' => 0);

		$em = $this->getDoctrine()->getManager();
		
		$id = $request->get('id', null);
		$role = $request->get('role', '');
		
		$userActor = $em->getRepository('UtilBundle:UserActors')->find($id);
		if ($userActor) {
			$em->beginTransaction();
			try {
				$user = $userActor->getUser();
				$user->setIsActive(0);
				$userActor->setDeletedOn(new \DateTime());
				
				$em->persist($user);
				$em->persist($userActor);
				$em->flush();
				
				$em->commit();
				$result['success'] = 1;
				$result['message'] = ucfirst($role) . ' Login "' . trim($user->getFirstName() . ' ' . $user->getLastName()) . ' (' . $user->getEmailAddress() . ')' . '" has been deleted successful.';
			} catch (\Exception $ex) {
				$em->rollback();
				$result['message'] = "There is an error when deleting $role login.";
			}
		} else {
			$result['message'] = ucfirst($role) . " Login not found.";
		}
		
		return new JsonResponse($result);
	}
	
    private function sendLoginEmail($user, $role, $password, $otherDesc = '') {
     
        $base = $this->container->get('request')->getSchemeAndHttpHost();
        $emailTo = $user->getEmailAddress();
        $em = $this->getDoctrine()->getManager();
        
        $userActor = $em->getRepository('UtilBundle:UserActors')->findOneBy(['user' => $user->getId()]);
        $mailTemplate = 'AdminBundle:login:email/agent_login.html.twig';
        $subject = "G-MEDS Agent Account Setup";
        $masterName = '';
        $masterComName = '';
        $masterAgentName = '';
		if ($role == 'doctor') {
			$mailTemplate = 'AdminBundle:login:email/doctor_login.html.twig';
            if ($userActor) {
                $doctor = $em->getRepository('UtilBundle:Doctor')->find($userActor->getEntityId());
                if ($doctor) {
                    $masterName = $doctor->getPersonalInformation()->getFullName(true);
                }
            }
            $subject = "G-MEDS Clinic Assistant account for ".$masterName;
		} else {
            if ($userActor) {
                $agent = $em->getRepository('UtilBundle:Agent')->find($userActor->getEntityId());
                if ($agent) {
                    if(empty($agent->getParent())) {
                        $agentCompany = $em->getRepository('UtilBundle:AgentCompany')->findOneBy(['agent' => $agent->getId()]);
                    } else {
                        $agentCompany = $em->getRepository('UtilBundle:AgentCompany')->findOneBy(['agent' => $agent->getParent()->getId()]);
                    }
                    if ($agentCompany) {
                        $masterComName = $agentCompany->getCompanyName();
                    }
                    if ($otherDesc == 'sub_login') {
                        $subject = "Setting G-MEDS agent user account (sub-login) setup";
                        $masterAgentName = $agent->getPersonalInformation()->getFullName();
                    }
                }
            }
        }
        $mailParams = array(
			'user' => $user,
			'password' => $password,
            'logoUrl' => $base.'/bundles/admin/assets/pages/img/logo.png',
            'name' =>  trim($user->getFirstName() . ' ' . $user->getLastName()),
            'base' =>$base,
            'masterName' => $masterName,
            'masterComName' => $masterComName,
            'masterAgentName' => $masterAgentName
        );
        
        $dataSendMail = array(
            'title'  => $subject,
            'body'   => $this->container->get('templating')->render($mailTemplate, $mailParams),
            'to'     => $emailTo,
       
        );

        $this->container->get('microservices.sendgrid.email')->sendEmail($dataSendMail);
    }
}