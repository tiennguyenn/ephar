<?php

namespace AdminBundle\Controller;

use AdminBundle\Controller\BaseController;
use AdminBundle\Form\AdminMpaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use UtilBundle\Entity\Doctor;
use UtilBundle\Entity\Document;
use UtilBundle\Entity\MasterProxyAccount;
use UtilBundle\Entity\MasterProxyAccountDoctor;
use UtilBundle\Entity\Phone;
use UtilBundle\Entity\RunningNumber;

use UtilBundle\Utility\Common;
use UtilBundle\Utility\Constant;
use UtilBundle\Utility\Utils;

class MasterProxyAccountController extends BaseController {



    /**
     * @Route("/admin/mpa-list", name="admin_mpa_dashboard")
     * @author bien
     */
    public function lisyAction(Request $request) {
        $parameters = array(
            'ajaxURL' => 'ajax_admin_mpa_dashboard'
        );

        return $this->render('AdminBundle:mpa:index.html.twig', $parameters );
    }

    /**
     * get data for chart
     * @Route("/admin/mpa-ajax-request", name="ajax_admin_mpa_dashboard")
     * @author bien
     */
    public function ajaxMpaAction(Request $request) {
        $type = $request->request->get('type');
        switch ($type) {
            case 1:
                // load mpa list
                return $this->loadListMpaAjax($request);

            case 2:
                // update mpa status
                return $this->updateMpaStatusAjax($request);

            case 3:
                // update mpa status
                return $this->deleteMpaAjax($request);

            case 4:
                // update mpa status
                return $this->resendMpaWelcomeMail($request);

            case 5:
                // load mpa list doctor
                return $this->loadListMpaDetail($request);

            case 6:
                // get list doctor for modal add doctor
                return $this->loadListActiveDoctor($request);

            case 7:
                // get list doctor for modal add doctor
                return $this->updateFilterMpaDoctor($request);
            case 11:
                // save doctor mpa asign
                return $this->updateMpaDoctor($request);

            case 8:
                // get role mpa
                return $this->updateMpaRoleAjax($request);
            case 9:
                // get role mpa
                return $this->removeDoctorFromMpaAjax($request);
            case 10:
                // get role mpa
                return $this->loadListMpaForDoctorAjax($request);
            case 12:
                // get role mpa
                return $this->resetSessionMpaDoctor();

            default:
                return new JsonResponse(['success' => false]);
        }
        return new JsonResponse($dataChart);
    }

    /**
     * @Route("/admin/mpa-account/{id}", name="admin_mpa_register", defaults={"id"=0})
     * @author bien
     */
    public function registerAction(Request $request, $id) {
        $profile = '';

        $title = "Register Master Proxy Account";
        if(empty($id)){
            $obj = new MasterProxyAccount();


        } else {
            $em = $this->getDoctrine()->getManager();

            $obj = $em->getRepository('UtilBundle:MasterProxyAccount')->find($id);
            $profile = $obj->getDocument()->getUrl();
            $title = "Edit Master Proxy Account";
        }
        if($request->isMethod('post')){

            return $this->registerMpa($request, $id);
        }


        $form = $this->createForm(new AdminMpaType(
            array(
                'depend'=>[],
                'entity_manager' => $this->get('doctrine.orm.entity_manager'),
                'data' =>$obj
            )
        ), array(), array());
        $parameters = array(
            'ajaxURL' => 'ajax_admin_mpa_dashboard',
            'form' => $form->createView(),
            'profile' => $profile,
            'id' => $obj->getId(),
            'title' => $title
        );




        return $this->render('AdminBundle:mpa:register.html.twig', $parameters);
    }


    /**
     * @Route("/admin/mpa-detail/{id}", name="admin_mpa_detail")
     * @author bien
     */
    public function mpaDetailAction(Request $request, $id) {
        $em = $this->getDoctrine()->getEntityManager();
        $mpa = $em->getRepository('UtilBundle:MasterProxyAccount')->find($id);

        $parameters = array(
            'ajaxURL' => 'ajax_admin_mpa_dashboard',
            'id' => $id,
            'title' => trim($mpa->getGivenName() . ' ' . $mpa->getFamilyName())
        );
        return $this->render('AdminBundle:mpa:detail.html.twig', $parameters);
    }


    /**
     * @Route("/admin/doctor/{id}/doctor-login", name="admin_doctor_login")
     */
    public function doctorLoginAction(Request $request,$id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $doctor = $em->getRepository('UtilBundle:Doctor')->find($id);
        $parameters=  array(
            'ajaxURL' => 'ajax_admin_mpa_dashboard',
            'id' => $id,
            'doctorName' => $doctor->getPersonalInformation()->getFullName()
        );

        return $this->render('AdminBundle:mpa:doctor_login.html.twig',$parameters);
    }


    private function loadListMpaAjax($request) {
        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository('UtilBundle:MasterProxyAccount')->getListAdmin($request->request);
        return new JsonResponse(['success' => true,'result' => $result]);
    }

    private function loadListMpaForDoctorAjax($request) {
        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository('UtilBundle:MasterProxyAccount')->getListForDoctorAdmin($request->request);
        return new JsonResponse(['success' => true,'result' => $result]);
    }

    private function loadListMpaDetail($request) {
        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository('UtilBundle:MasterProxyAccountDoctor')->getListDetailAdmin($request->request);
        return new JsonResponse(['success' => true,'result' => $result]);
    }

    private function loadListActiveDoctor($request) {
        $em = $this->getDoctrine()->getManager();
        $session = new Session();
        $listDoctor =  $session->get("listDoctor");
        if(empty($listDoctor)) {
            $type = 1;
            $doctors = $em->getRepository('UtilBundle:Doctor')->getActiveDoctorsAdmin($request->request);
            $listDoctor = $em->getRepository('UtilBundle:MasterProxyAccount')->getCurrentListDoctorAssign($request->request);
            $listDoctorId = array_keys($listDoctor);
            $result = [];
            foreach ($doctors as $d){

                if(in_array($d['id'], $listDoctorId)){
                    $d['select'] = true;
                }else {
                    $d['select'] = false;
                }
                $result[$d['id']] = $d;
            }
            foreach ($listDoctor as $d){
                $result[$d['id']] = $d;
            }
            $session = new Session();
            $session->set('listDoctor', $result);
        } else {
            $type = 2;
            $searchSelect = $request->get('search-select', '');
            $searchRemove = $request->get('select-remove', '');

            foreach ($listDoctor as $dt){
                if($dt['select']) {
                    $search = $searchRemove;
                } else {
                    $search = $searchSelect;
                }
                if(!empty($search) ) {
                    if($this->like($dt['name'], $search)){
                        $result[] = $dt;
                    }
                } else {
                    $result[] = $dt;
                }
            }
        }


        return new JsonResponse(array('success' => true,'result' =>$result,'type' => $type));
    }

    private function like($str, $searchTerm) {
        $searchTerm = strtolower($searchTerm);
        $str = strtolower($str);
        $pos = strpos($str, $searchTerm);
        if ($pos === false) {
            return false;
        }else{
            return true;
        }
    }

    private function resetSessionMpaDoctor(){
        $session = new Session();
        $session->set('listDoctor', []);
        return new JsonResponse(array('success' => true));
    }

    private function updateFilterMpaDoctor($request) {
        $session = new Session();
        $listDoctor = $session->get("listDoctor");
        $activeDoctors = $request->request->get('data');
        $newList = [];
        foreach ($listDoctor as $dt){
            if(in_array( $dt['id'],$activeDoctors)){
                $dt['select'] = true;
            }
            else {
                $dt['select'] = false;
            }
            $newList[] = $dt;
        }
        $session->set('listDoctor', $newList);
        return new JsonResponse(array('success' => true));
    }
    private function updateMpaDoctor($request) {
        $session = new Session();
        $em = $this->getDoctrine()->getManager();
        $defaultRoles = Utils::filterRoleMpa( Constant::DEFAULT_ROLE_MPA);
        $id = $request->request->get('id');
        $updateData =  $request->request->get('data');
        if(empty($updateData)) {
            $updateData = [];
        }
        $mpa = $em->getRepository('UtilBundle:MasterProxyAccount')->find($id);
        $listDoctor = [];
        $listMaps = $mpa->getMpaDoctors();
        foreach ($listMaps as $item) {
            $listDoctor[] = $item->getDoctor()->getId();
        }

        $news = array_diff($updateData, $listDoctor);
        $result = ['new' => [], 'delete' => []];
        $newsDoctors = [];
        foreach ($news as $doctorId){
            $item = new MasterProxyAccountDoctor();
            $doctor = $em->getRepository('UtilBundle:Doctor')->find($doctorId);
            if(empty($doctor)){
                continue;
            }
            if (empty($doctor->getIsCustomizeMedicineEnabled())) {
                foreach ($defaultRoles as $key => $value) {
                    if ('doctor_custom_selling_prices' == $value) {
                        unset($defaultRoles[$key]);
                    }
                }
            }
            $item->setDoctor($doctor);
            $item->setPrivilege(json_encode($defaultRoles));
            $mpa->addMpaDoctor($item);
            $newsDoctors[] = $doctor;
            $result['new'][] = $doctorId;
        }
        $deletes = array_diff($listDoctor, $updateData);
        foreach ($listMaps as $item) {
            $doctorId =  $item->getDoctor()->getId();
            if(in_array( $doctorId, $deletes)){
                $item->setDeletedOn(new \DateTime('now'));
                $result['delete'][] = $doctorId;
            }            
            $result['delete'][] = $doctorId;
        }

        $em->persist($mpa);
        $em->flush();
        $listDoctor = $session->get("listDoctor");

        $add = [];
        $remove = [];
        foreach (  $listDoctor as $d ){
            if(in_array($d['id'], $result['new'])) {
                $add[] = $d['name'];
            }
            if(in_array($d['id'], $result['delete'])) {
                $remove[] = $d['name'];
            }
        }
        $addMessage = '';
        $removeMessage = '';

        if(count($add) > 0) {
            $addMessage =  implode(', ',$add);
            if(count($add) > 1){
                $addMessage .= " were added in ". $mpa->getGivenName() . ' '. $mpa->getFamilyName() ."'s doctor list.";
            } else {
                $addMessage .= " was added in ". $mpa->getGivenName() . ' '. $mpa->getFamilyName() ."'s doctor list.";
            }
        }

        if(count($remove) > 0) {
            $removeMessage =  implode(', ',$remove);
            if(count($remove) > 1){
                $removeMessage .= " were removed  in ". $mpa->getGivenName() . ' '. $mpa->getFamilyName() ."'s doctor list.";
            } else {
                $removeMessage .= " was removed  in ". $mpa->getGivenName() . ' '. $mpa->getFamilyName() ."'s doctor list.";
            }
        }
        $session->set('listDoctor', []);

        $this->sendEmailNotifyToAgent($newsDoctors, $mpa);
        return new JsonResponse(['success' => true, 'result' => ['new' => $addMessage, 'remove' => $removeMessage]]);
    }

    private function updateMpaStatusAjax($request) {
        $id  = $request->request->get('id');
        $em = $this->getDoctrine()->getManager();
        $mpa = $em->getRepository('UtilBundle:MasterProxyAccount')->find($id);
        $status = $mpa->getStatus();
        $mpa->setStatus(!$status);
        $mpa->getUser()->setIsActive(!$status);
        $em->persist($mpa);
        $em->flush();

        return new JsonResponse(['success' => true,'result' => $mpa->getId()]);
    }

    private function deleteMpaAjax($request) {
        $id  = $request->request->get('id');
        $em = $this->getDoctrine()->getManager();
        $mpa = $em->getRepository('UtilBundle:MasterProxyAccount')->find($id);
        $mpa->setDeletedOn(new \DateTime('now'));
        $em->persist($mpa);
        $em->flush();

        return new JsonResponse(['success' => true,'result' => $mpa->getId()]);
    }

    private function removeDoctorFromMpaAjax($request) {
        $id  = $request->request->get('id');
        $doctorId = $request->request->get('doctor-id');
        $em = $this->getDoctrine()->getManager();
        $mpa = $em->getRepository('UtilBundle:MasterProxyAccount')->find($id);
        $mpaDs = $mpa->getMpaDoctors();
        $removeDoctor = "";
        foreach ($mpaDs as $mpaD) {
            if($mpaD->getDoctor()->getId() == $doctorId){
                $mpaD->setDeletedOn(new \DateTime('now'));
                $removeDoctor = $mpaD->getDoctor();
            }
        }
        $message =  $removeDoctor->getPersonalInformation()->getFullName(). " was removed in ".$mpa->getGivenName() . ' ' . $mpa->getFamilyName()."'s doctor list.";
        $em->persist($mpa);
        $em->flush();

        return new JsonResponse(['success' => true,'result' => ['remove' => $message , 'new' => '']]);
    }

    private function updateMpaRoleAjax($request) {
        $rolesIndex = $request->request->get('roles');

        $updateRole = Utils::filterRoleMpa($rolesIndex);


        $id  = $request->request->get('id');
        $doctorId  = $request->request->get('doctor-id');
        $em = $this->getDoctrine()->getManager();
        $mpa = $em->getRepository('UtilBundle:MasterProxyAccount')->find($id);
        $mpaDs = $mpa->getMpaDoctors();
        foreach ($mpaDs as $mpaD) {
            if($mpaD->getDoctor()->getId() == $doctorId){
                $mpaD->setPrivilege(json_encode($updateRole));
            }
        }
        $em->persist($mpa);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }

    private function registerMpa($request, $id) {

        $em = $this->getDoctrine()->getEntityManager();
        $data = $request->request->get('admin_mpa');


        if(empty($id)){
            $obj = new MasterProxyAccount();
            $obj->setClinicName($data['clinicName']);
            $obj->setFamilyName($data['familyName']);
            $obj->setGivenName($data['givenName']);
            $obj->setEmailAddress($data['email']);
            $obj->setMpaCode($this->createMpaCode());
            $obj->setStatus(true);

            $file = isset($_FILES['admin_mpa']) ? $_FILES['admin_mpa'] : null;


            $fileName   = $file['name']['profile'].time() . "_" . uniqid();

            $target = $this->getParameter('upload_directory_profile_mpa');
            $profileUrl = $this->uploadProfile($file, $fileName, $target);

            $document = new Document();
            $document->setUrl($profileUrl);
            $obj->setDocument($document);
            $phone = new Phone();
            $phone->setCountry($em->getRepository('UtilBundle:Country')->find($data['phoneLocation']));
            $phone->setNumber($data['phone']);
            $obj->setPhone($phone);
            $em->persist($obj);
            $em->flush();
            $this->get('session')->getFlashBag()->set("success", "MPA is created successfully.");
            $this->sendMpaEmail($obj);


        } else{

            $obj = $em->getRepository('UtilBundle:MasterProxyAccount')->find($id);
            $obj->setClinicName($data['clinicName']);
            $obj->setFamilyName($data['familyName']);
            $obj->setGivenName($data['givenName']);
            $file = isset($_FILES['admin_mpa']) ? $_FILES['admin_mpa'] : null;
            if(!empty($file['name']['profile'])){
                $fileName   = $file['name']['profile']."_".time() . "_" . uniqid();
                $target = $this->getParameter('upload_directory_profile_mpa');
                $profileUrl = $this->uploadProfile($file, $fileName, $target,'profile');
                $document = $obj->getDocument();
                $document->setUrl($profileUrl);
            }

            $phone =  $obj->getPhone();
            $phone->setCountry($em->getRepository('UtilBundle:Country')->find($data['phoneLocation']));
            $phone->setNumber($data['phone']);
           // $this->get('session')->getFlashBag()->set("success", "MPA is updated successfully.");
            $em->persist($obj);
            $em->flush();
        }



        return $this->redirectToRoute('admin_mpa_dashboard');
    }

    private function resendMpaWelcomeMail(Request $request){
        $em = $this->getDoctrine()->getEntityManager();
        $id = $request->get('id');
        $mpa = $em->getRepository('UtilBundle:MasterProxyAccount')->find($id);

        $mpa->getIsConfirmed();
        if(!$mpa->getIsConfirmed()){
            $this->sendMpaEmail($mpa);
            return new JsonResponse(true);
        }else{
            return new JsonResponse(false);
        }
    }

    private function sendMpaEmail($mpa) {

        $base = $this->container->get('request')->getSchemeAndHttpHost();
        //$base = str_replace('https://', 'http://', $base);
        //$base = "http://core.gmeds.s3corp.com.vn";
        $emailTo = $mpa->getEmailAddress();
      //  $mpa = new MasterProxyAccount();
        $mailTemplate = 'AdminBundle:admin:email/register-mpa.html.twig';
        $mailParams = array(
            'logoUrl' => $base . '/bundles/admin/assets/pages/img/logo.png',
            'name' => trim($mpa->getGivenName() . ' '. $mpa->getFamilyName()),
            'id' => $mpa->getId(),
            'base' => $base
        );
        $dataSendMail = array(
            'title' => "Setting Master Proxy Account Information",
            'body' => $this->container->get('templating')->render($mailTemplate, $mailParams),
            'to' => $emailTo,
        );
        $this->container->get('microservices.sendgrid.email')->sendEmail($dataSendMail);
    }

    private function createMpaCode(){
        $code = "M-".date('my')."-".Constant::MPA_CODE.'-';
        $em = $this->getDoctrine()->getEntityManager();
        $runningNumber = $em->getRepository('UtilBundle:RunningNumber')->findOneByRunningNumberCode('MPA');


        $invID =  1;
        if(empty($runningNumber)){
            $runningNumber = new RunningNumber();
            $runningNumber->setRunningNumberCode('MPA');
            $runningNumber->setRunningNumberValue('1');
            $em->persist($runningNumber);
            $em->flush();
        } else {
            $invID = $runningNumber->getRunningNumberValue();
        }
        $invID ++;
        $code .= strval( str_pad($invID, 4, '0', STR_PAD_LEFT));
        $runningNumber->setRunningNumberValue($invID);
        $em->persist($runningNumber);
        $em->flush();
        return $code;
//        MPA code
//M-0618-MYS-0001
//
//M : luôn là M đầu tiên
//0618 : <month><year>
//        MY : xài code MYS
//SG : xài code SGP
//0001 : running number
    }

    private function uploadProfile($file,$filename, $target_dir = "uploads/", $index = 'profile')
    {
        $fileSection = explode('/', $filename);
        array_pop($fileSection);
        $locationDir = $this->container->getParameter('upload_directory') . '/' . implode('/', $fileSection);
        if (Common::createDirIfNotExists($locationDir)) {
            $target_file = $target_dir . basename($file["name"][$index]);
            $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
            if(empty($imageFileType)){
                $imageFileType = "txt";
            }
            $upload = move_uploaded_file($file['tmp_name'][$index],$target_dir.$filename.'.'.$imageFileType);
            if($upload)
            {
                return $target_dir.$filename.'.'.$imageFileType;
            }
        }
        return '';
    }

}
