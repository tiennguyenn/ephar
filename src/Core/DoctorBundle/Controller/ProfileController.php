<?php

namespace DoctorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use DoctorBundle\Form\ProfileType;
use DoctorBundle\Form\ChangePasswordType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use UtilBundle\Entity\Bank;
use UtilBundle\Entity\BankAccount;
use UtilBundle\Entity\Doctor;
use UtilBundle\Entity\Clinic;
use UtilBundle\Entity\ClinicAddress;
use UtilBundle\Entity\Phone;
use UtilBundle\Entity\Address;
use UtilBundle\Entity\DoctorGstSetting;
use AdminBundle\Form\DoctorAdminType;
use UtilBundle\Utility\Common;
use UtilBundle\Utility\Constant;
use UtilBundle\Utility\Utils;
use UtilBundle\Utility\MsgUtils;

class ProfileController extends Controller
{
    /**
     * Doctor Password Change Ajax
     * @Route("/profile/ajax-change-password", name="ajax_doctor_change_password")
     * @author toan.le
     */
    public function ajaxChangePasswordAction(Request $request)
    {
        $gmedUser = $this->getUser();
        $userId = $gmedUser->getLoggedUser()->getId();

        $params = $request->request->all();
        $data = $params['ChangePasswordBundle_doctor'];
        $user = $this->getDoctrine()->getRepository('UtilBundle:User')->isMatchPassword($data['current_password'],$userId);
        $results = [];

        if($user != null){
            if($data['new_password'] == $data['confirm_password']){
                $this->getDoctrine()->getRepository('UtilBundle:User')->updatePassword($user, $data['new_password']);
                $results = $results = [
                    'success'   => true,
                    'message'   => 'Change password successful.'
                ];
            }else{
                $results = [
                    'success'   => false,
                    'message'   => 'Confirm password does not match.'
                ];
            }
        } else {
            $results = [
                'success'   => false,
                'message'   => 'Current password does not match.'
            ];
        }

        return new JsonResponse($results);
    }

    /**
     * Doctor Change Password
     * @Route("/change-password", name="doctor_change_password")
     * @author toan.le
     */
    public function changePasswordAction(Request $request)
    {
        $optionsChangePassword = array(
            'attr'               => array(
                'id'    => 'change-password-form',
                'class' => 'form-horizontal'
                ),
            'method'             => 'POST'
        );
        $formChangePassword = $this->createForm('DoctorBundle\Form\ChangePasswordType', array(), $optionsChangePassword);
        return $this->render('DoctorBundle:profile:change-password.html.twig',[
            'formChangePassword' => $formChangePassword->createView(),
            ]);
    }

    /**
     * get request filter
     * @param $request
     * @return array
     * @author toan.le
     */
    private function getFilter($request)
    {
        $params = array(
            'page'    => $request->get('page', Constant::PAGE_DEFAULT),
            'perPage' => $request->get('perPage', Constant::PER_PAGE_DEFAULT),
            'userType' => $request->get('userType', Constant::USER_TYPE_DOCTOR),
            'term' => $request->get('term', ''),
            'status' => $request->get('status', 'all'),
            'sorting' => $request->get('sorting', ''),
            'from_date' => $request->get('from_date', ''),
            'to_date' => $request->get('to_date', '')
        );
        //additional filters
        $filters = $request->get('ps_filter', array());
        if(!empty($filters)){
            foreach($filters as $k=>$v){
                $params[$k] = $v;
            }
        }
        return $params;
    }

    /**
     * Doctor Profile Personal Information
     * @Route("/profile", name="doctor_profile_view")
     * @author Rifky Anzar
     */
    public function viewAction(Request $request)
    {
        $gmedUser = $this->getUser();

        $id = $gmedUser->getId();
        $em = $this->getDoctrine()->getEntityManager();
        if (!Common::isMainDoctorLogin($gmedUser, $em)) {
            return $this->redirectToRoute('access_denied');
        }
        $countries = $em->getRepository('UtilBundle:Country')->getByPreferredCountry(true);
        $country = array('0'=>'','empty'=>'-');
        $phoneCode = array('0'=>'','empty'=>'-');
        foreach ($countries as $c) {
            $country[$c['id']]= $c['name'];
            $phoneCode[$c['id']] = $c['phoneCode'];
        }
        $doctor = $em->getRepository('UtilBundle:Doctor')->find($id);
        if (is_object($doctor) && $doctor->getId()) {
            $currentspec = array();
            $spec = $doctor->getMedicalSpecialty();
            foreach ($spec as $item) {
                array_push($currentspec, $item->getName());
            }
            $doctorInfo["spec"] = $currentspec;
            $doctorInfo["gstServiceCode"] = $doctor->getGstServiceCode();
            $doctorInfo["title"] = $doctor->getPersonalInformation()->getTitle();
            $doctorInfo["firstname"] = $doctor->getPersonalInformation()->getFirstName();
            $doctorInfo["lastname"] = $doctor->getPersonalInformation()->getLastName();
            $doctorInfo["email"] = $doctor->getPersonalInformation()->getEmailAddress();
            $doctorInfo["rxViewFee"] = $doctor->getRxReviewFee();
            $doctorInfo["rxReviewFeeLocal"] = $doctor->getRxReviewFeeLocal();
            $doctorInfo["rxReviewFeeInternational"] = $doctor->getRxReviewFeeInternational();
            $doctorInfo["rxFeeLiveConsultLocal"] = $doctor->getRxFeeLiveConsultLocal();
            $doctorInfo["rxFeeLiveConsultInternational"] = $doctor->getRxFeeLiveConsultInternational();
            $doctorInfo["paymentGate"] = $doctor->getCurrentPaymentGate();
            $doctorInfo["agentId"] = $doctor->getAgentDoctors()->last()->getAgent()->getId();
            $doctorInfo["gender"] = $doctor->getPersonalInformation()->getGender();
            $phoneObj = $doctor->getDoctorPhones()->first()->getContact();
            $doctorInfo["phoneLocation"] = $phoneObj->getCountry()->getId();
            $doctorInfo["phoneArea"] = $phoneObj->getAreaCode();
            $doctorInfo["phoneNumber"] = $phoneObj->getNumber();
            $medicalLicense = $doctor->getMedicalLicense();
            $doctorInfo["medicalNumber"] = $medicalLicense->getRegistrationNumber();
            $doctorInfo["medicalIssueDate"] = $medicalLicense->getIssuingDate()->format('Y');
            $doctorInfo["medicalCountry"] = $medicalLicense->getIssuingCountryId();
            $iden = $doctor->getIdentification()->first();
            $doctorInfo["passportNum"] = $iden->getIdentityNumber();
            if ($iden->getIssueDate()) {
                $doctorInfo["passportdate"] = date('d M y', strtotime($iden->getIssueDate()));
            }
            if ($iden->getIssuingCountryId()) {
                $doctorInfo["passportCountry"] = $iden->getIssuingCountryId();
            }
            $bankA = $doctor->getBankAccount();
            if ($bankA) {
                $doctorInfo["accountName"] = $bankA->getAccountName();
                $doctorInfo["accountNum"] = $bankA->getAccountNumber();
                $bank = $bankA->getBank();
                $doctorInfo["bankName"] = $bank->getName();
                $doctorInfo["bankCountry"] = $bank->getCountry()->getId();
                $doctorInfo["bankcode"] = $bank->getSwiftCode();
            }
            $clinics = $doctor->getClinics();
            $clinic = '';
            foreach ($clinics as $cl) {
                if ($cl->getIsPrimary()) {
                    $clinic = $cl;
                    break;
                }
            }
            $clinicInfo["gstNo"] = $doctor->getGstNo();
            if ($doctor->getGstEffectDate()) {
                $clinicInfo["gstDate"] = $doctor->getGstEffectDate()->format('d M y');
            }
            $clinicInfo["clinicSetting"] = $doctor->getIsGst();
            $areas = ['local', 'overseas'];
            foreach ($areas as $area) {
                foreach ($doctor->getGstSettings() as $item) {
                    switch ($item->getFeeType()) {
                        case Constant::SETTING_GST_REVIEW:
                            if($area == $item->getArea()){
                                $settingGstReviewInternational = $item;
                            }else{
                                $settingGstReviewLocal = $item;
                            }
                            break;
                        case Constant::SETTING_GST_CONSULT:
                            if($area == $item->getArea()){
                                $settingGstConsultInternational = $item;
                            }else{
                                $settingGstConsultLocal = $item;
                            }
                            break;
                        case Constant::SETTING_GST_MEDICINE:
                            if($area == $item->getArea()){
                                $settingGstMedicineInternational = $item;
                            }else{
                                $settingGstMedicineLocal = $item;
                            }
                            break;
                    }
                }
            }
            $clinicInfo['applyGstReviewLocal'] = isset($settingGstReviewLocal) && is_object($settingGstReviewLocal) ? $settingGstReviewLocal->getIsHasGst() : 0;
            $clinicInfo['gstCodeReviewLocal'] = isset($settingGstReviewLocal) ? $settingGstReviewLocal->getNewGst() != null ? $settingGstReviewLocal->getNewGst() : $settingGstReviewLocal->getGst() : '';
            $clinicInfo['reviewLocalDate'] = isset($settingGstReviewLocal) && $settingGstReviewLocal->getEffectiveDate() != null ? $settingGstReviewLocal->getEffectiveDate()->format('d M y') : '';
            $clinicInfo['applyGstConsultLocal'] = isset($settingGstConsultLocal) && is_object($settingGstConsultLocal) ? $settingGstConsultLocal->getIsHasGst() : false;
            $clinicInfo['gstCodeConsultLocal'] = isset($settingGstConsultLocal) ? $settingGstConsultLocal->getNewGst() == null ? $settingGstConsultLocal->getGst() : $settingGstConsultLocal->getNewGst() : '';
            $clinicInfo['consultLocalDate'] = isset($settingGstConsultLocal) && $settingGstConsultLocal->getEffectiveDate() != null ? $settingGstConsultLocal->getEffectiveDate()->format('d M y') : '';
            $clinicInfo['gstCodeMedicineInternational'] = isset($settingGstMedicineInternational) ? $settingGstMedicineInternational->getNewGst() == null ? $settingGstMedicineInternational->getGst() : $settingGstMedicineInternational->getNewGst() : '';
            $clinicInfo['medicineInternationalDate'] = isset($settingGstMedicineInternational) && $settingGstMedicineInternational->getEffectiveDate() != null ? $settingGstMedicineInternational->getEffectiveDate()->format('d M y') : '';
            $clinicInfo['applyGstReviewInternational'] = isset($settingGstReviewInternational) && is_object($settingGstReviewInternational) ? $settingGstReviewInternational->getIsHasGst() : false;
            $clinicInfo['gstCodeReviewInternational'] = isset($settingGstReviewInternational) ? $settingGstReviewInternational->getNewGst() == null ? $settingGstReviewInternational->getGst() : $settingGstReviewInternational->getNewGst() : '';
            $clinicInfo['reviewInternationalDate'] = isset($settingGstReviewInternational) && $settingGstReviewInternational->getEffectiveDate() != null ? $settingGstReviewInternational->getEffectiveDate()->format('d M y') : '';
            $clinicInfo['applyGstConsultInternational'] = isset($settingGstConsultInternational) && is_object($settingGstConsultInternational) ? $settingGstConsultInternational->getIsHasGst() : false;
            $clinicInfo['gstCodeConsultInternational'] = isset($settingGstConsultInternational) ? $settingGstConsultInternational->getNewGst() == null ? $settingGstConsultInternational->getGst() : $settingGstConsultInternational->getNewGst() : '';
            $clinicInfo['consultInternationalDate'] = isset($settingGstConsultInternational) && $settingGstConsultInternational->getEffectiveDate() != null ? $settingGstConsultInternational->getEffectiveDate()->format('d M y') : '';
            if ($clinic) {
                $clinicInfo["clinicName"] = $clinic->getBusinessName();
                $clinicInfo["clinicLogo"] = $clinic->getBusinessLogoUrl();
                $clinicInfo["clinicEmail"] = $clinic->getEmail();                
                $clinicAdress = $clinic->getBusinessAddress();

                //address
                $mainAddress = $clinicAdress->getAddress();
                $clinicInfo["mainZipCode"] = $mainAddress->getPostalCode();
                $clinicInfo["mainLine1"] = $mainAddress->getLine1();
                $clinicInfo["mainLine2"] = $mainAddress->getLine2();
                $clinicInfo["mainLine3"] = $mainAddress->getLine3();
                $city = $mainAddress->getCity();
                $clinicInfo["cityMainClinicId"] = $city->getId();
                
                $mainCountry = $city->getCountry()->getId();
                if (empty($city->getState())) {
                    $mainState = '';
                } else {
                    $mainState = $city->getState()->getName();
                }

                $clinicInfo['clinicCountry'] = $mainCountry;
                $clinicInfo['clinicCity'] = $city->getName();
                $clinicInfo['clinicState'] = $mainState;
                $mainPhone = $clinicAdress->getBusinessPhone();
                $clinicInfo["mainLocation"] = $mainPhone->getCountry()->getId();
                $clinicInfo["mainArea"] = $mainPhone->getAreaCode();
                $clinicInfo["mainNumber"] = $mainPhone->getNumber();
            }
        }
        $titleList = array(
            'Dr' => 'Doctor',
            'Prof' => 'Professor',
            'A/Prof' => 'Associate Professor'
        );

        $listGstCode = [];
        $gstCode = $em->getRepository('UtilBundle:GstCode')->findAll();
        foreach ($gstCode as $item) {
            $listGstCode[$item->getId()] = $item->getCode();
        }

        $countryList = [];
        $subClinics = $em->getRepository('UtilBundle:Doctor')->getClinicData($id);
        $countryList[]  =  $clinicInfo['clinicCountry'];
        foreach ($subClinics as $cl){
            $countryList[] = $cl['country'];
        }

        $listCity = $em->getRepository('UtilBundle:City')->getListCity($countryList);
        $listState = $em->getRepository('UtilBundle:State')->getListState($countryList);
        
        $parameters = array(
            'doctor'    => $doctor,
            'doctorInfo'    => $doctorInfo,
            'clinic'    => $clinicInfo,
            'subClinics'=> $subClinics,
            'titleList' => $titleList,
            'gender' => array('1' => 'Male', '0' => 'Female'),
            'gst' => array('1' => 'Yes', '0' => 'No'),
            'listGstCode'   => $listGstCode,
            'country'   => $country,
            'city'      => $listCity,
            'state'     => $listState,
            'phoneCode' => $phoneCode
        );

        return $this->render('DoctorBundle:profile:view-profile.html.twig', $parameters);
    }

    /**
     * Doctor Edit Profile Personal Information
     * @Route("/profile/edit", name="doctor_profile")
     * @author toan.le
     */
    public function indexAction(Request $request)
    {
        $gmedUser = $this->getUser();
        $id = $gmedUser->getId();
        $em = $this->getDoctrine()->getEntityManager();
        if (!Common::isMainDoctorLogin($gmedUser, $em)) {
            return $this->redirectToRoute('access_denied');
        }
        $agent =  $em->getRepository('UtilBundle:Agent')->getAgentForEditDoctor();
        $specality =  $em->getRepository('UtilBundle:MedicalSpecialty')->getMedicalSpecality();
        $country = $em->getRepository('UtilBundle:Country')->getByPreferredCountry(true);
        $doctor = $em->getRepository('UtilBundle:Doctor')->find($id);

        $isTnCUpdated = false;
        $doctorAgreement = $em->getRepository('UtilBundle:FileDocument')->getContentForClient(Constant::DOCUMENT_NAME_DOCTOR_AGREEMENT, Common::getCurrentSite($this->container));
        if ($doctorAgreement) {
            if ($doctorAgreement['createdAt'] > $doctor->getUpdatedTermCondition()) {
                $isTnCUpdated = true;
            }
        }

        $clinics = $doctor->getClinics();
        $clinic = '';
        foreach ($clinics as $cl) {
            if($cl->getIsPrimary()) {
                $clinic = $cl;
                break;
            }
        }
        $gst = 1;
        if($doctor->getIsGst()) {
            $gst = 2;
        }
        $dependentData= array();
        $dependentData['country'] = $country;
        $dependentData['cityRepo'] = $em->getRepository('UtilBundle:City');
        $form = $this->createForm(new DoctorAdminType(array('agent' => $agent,'specality' => $specality,'doctor'=>$doctor,'depend'=>$dependentData, 'entity_manager' => $this->get('doctrine.orm.entity_manager'))), array(), array());
        $parameters = array(
            'form' => $form->createView(),
            'ajaxURL' => 'ajax_doctor_edit',
            'ajaxDependent' => 'doctor_profile_create_getdependent',
            'successUrl' => 'doctor_profile_view',
            'doctorId' => $id,
            'doctor' => $doctor,
            'clinic' => $clinic,
            'doctorGst' => $gst,
            'title' => 'Edit doctor',
            'isConfirmed' => $doctor->getIsConfirmed(),
			'isTnCUpdated' => $isTnCUpdated,
			'signaturUrl' => $this->container->getParameter('signature_url')
        );
        return $this->render('DoctorBundle:profile:personal-information.html.twig',$parameters);
    }

    /**
    * @Route("/doctor-accept-tandc", name="ajax_doctor_accept_tandc")
    */
    public function acceptTermsAndConditionsAction(Request $request){
        $em = $this->getDoctrine()->getEntityManager();
        $gmedUser = $this->getUser();
        $id = $gmedUser->getId();
        $type = $request->get('type', 1);

        $today = new \DateTime();
        $doctor = $em->getRepository('UtilBundle:Doctor')->find($id);
        if($doctor->getIsConfirmed() == Constant::STATUS_CONFIRM && $type == 1){
            $doctor->setIsConfirmed(Constant::STATUS_ACCEPT_TANDC);
            $gmedUser->setIsConfirmed(Constant::STATUS_ACCEPT_TANDC);
        }
        $gmedUser->setUpdatedTermCondition($today);
        $doctor->setUpdatedTermCondition($today);
        $em->persist($doctor);
        $em->flush();
        $url = $this->generateUrl('logout');

        return new JsonResponse([
            'status'    => true,
            'url'       => $url
        ]);
    }

    /**
    * @Route("/doctor-edit-submit", name="ajax_doctor_edit")
    */
    public function doctorEditSubmitAction(Request $request)
    {
        $gmedUser = $this->getUser();
        $em = $this->getDoctrine()->getEntityManager();
        if (!Common::isMainDoctorLogin($gmedUser, $em)) {
            return $this->redirectToRoute('access_denied');
        }
        $data       = $request->request->get('admin_doctor');
        $data       = Common::removeSpaceOf($data);
        $clinicdata = $request->request->get('clinics',array());
        $clinicdata = Common::removeSpaceOf($clinicdata);
        $id         = $request->request->get('doctor-id');
        $doctor     = $em->getRepository('UtilBundle:Doctor')->find($id);
        $authorId   = $this->getUser()->getLoggedUser()->getId();
        $arrFileds  = [
            'rx_review_fee'                     => $doctor->getRxReviewFee(),
            'rx_review_fee_local'               => $doctor->getRxReviewFeeLocal(),
            'rx_review_fee_international'       => $doctor->getRxReviewFeeInternational(),
            'rx_fee_live_consult_local'         => $doctor->getRxFeeLiveConsultLocal(),
            'rx_fee_live_consult_international' => $doctor->getRxFeeLiveConsultInternational(),
            'is_gst'                            => $doctor->getIsGst()
        ];

        $oldData    = $this->getDoctorValue($doctor);
        $common     = $this->get('util.common');

        if($doctor->getIsConfirmed() == Constant::STATUS_ACCEPT_TANDC){
            $doctor->setIsConfirmed(Constant::STATUS_UPDATE_PROFILE);
            $currentUser = $this->getUser();
            $currentUser->setIsConfirmed(Constant::STATUS_UPDATE_PROFILE);
        }

        // update personal infomation
        $personalInfo = $doctor->getPersonalInformation();
        $personalInfo->setTitle($data['title']);
        $personalInfo->setFirstName($data['firstName']);
        $personalInfo->setLastName($data['lastName']);
        $personalInfo->setGender($data['gender']);
        $personalInfo->setEmailAddress($data['email']);
        $personalInfo->setPassportNo($data['localIdPassport']);
        $doctor->setDisplayName($data['displayName']);
        
         // update doctor phone
        $doctorPhone = $doctor->getDoctorPhones()->first();
        $phone = $doctorPhone->getContact();
        $country = $em->getRepository('UtilBundle:Country')->find($data['phoneLocation']);
        $phone->setCountry($country);
        $phone->setPhoneType($em->getRepository('UtilBundle:PhoneType')->getPhone());
        $phone->setAreaCode($data['phoneArea']);
        $phone->setNumber($data['phone']);
        
        //update medical license
        $mlicense = $doctor->getMedicalLicense();
        $mlicense->setRegistrationNumber($data['localMedicalLicence']);
        $mlicense->setIssuingCountryId($data['localMedicalCountry']);
        $date = new \DateTime(date('Y-m-d', strtotime($data['localMedicalDate'])));
        $mlicense->setIssuingDate($date);
        
        // update identify
        $iden = $doctor->getIdentification()->first();
        $iden->setIdentityNumber($data['localIdPassport']);
        $iden->setIssuingCountryId($data['localIdPassportCountry']== 'empty' ? '': $data['localIdPassportCountry']);

        //doctor specialization
        foreach ($doctor->getMedicalSpecialty() as $m) {
            $doctor->removeMedicalSpecialty($m);
        }
        $spec = $data['specialization'];
        foreach ($spec as $val) {
            $obj = $em->getRepository('UtilBundle:MedicalSpecialty')->find($val);
            $doctor->addMedicalSpecialty($obj);
        }
        
        // doctor bankAccount
        $bankAcc = $doctor->getBankAccount();
        if (!$bankAcc) {
            $bankAcc = new BankAccount();
        }

        if ($data['bankCountryIssue'] == Constant::ID_SINGAPORE || $data['bankCountryIssue'] == Constant::ID_MALAYSIA) {
            $bank = $em->getRepository('UtilBundle:Bank')->find($data['bankName']);
        } else {
            $bank = $bankAcc->getBank();
            if (!$bank) {
                $bank = new Bank();
            }
            $bank->setName($data['bankName']);
            $bank->setCountry($em->getRepository('UtilBundle:Country')->find($data['bankCountryIssue']));
            $bank->setSwiftCode($data['bankSwiftCode']);
        }
        $bankAcc->setAccountName($data['accountName']);
        $bankAcc->setAccountNumber($data['accountNumber']);
        $bankAcc->setBank($bank);
        $doctor->setBankAccount($bankAcc);

        // Profile Photo
        if(isset($_FILES["profile"])) {
            $profileUrl = $common->uploadfile($_FILES["profile"], 'doctor/'.'profile'.$doctor->getId().time());
            if(!empty($profileUrl)) {
                $doctor->setProfilePhotoUrl($profileUrl);
            }
        }
        
        // Signature
        if( isset($_FILES["signature"])) {
            $signature = $common->uploadfile($_FILES["signature"], 'doctor/signature'.$doctor->getId().time());
            if(!empty($signature)) {
                $doctor->setSignatureUrl($signature);
            }
        }

        $clinics = $doctor->getClinics();
        $clinic = '';
        foreach ($clinics as $cl) {
            if($cl->getIsPrimary()) {
                $clinic = $cl;
                break;
            }
        }
        if( empty($clinic) ){
            $clinic = new Clinic();
            $clinic->setIsPrimary(1);
            $doctor->addClinic($clinic);
        }
        $clinic->setBusinessName($data['mainClinicName']);
        $clinic->setEmail($data['mainClinicEmail']);
        $inputs = [];
        $areas  = ['overseas', 'local'];
        foreach ($areas as $areaType) {
            foreach (Constant::GST_SETTING_TYPE as $type) {
                $entityId      = null;
                $effectDate    = null;
                $oldGstId      = null;
                $oldGstIdNew   = null;
                $effectiveDate = null;
                $gstType = $em->getRepository('UtilBundle:DoctorGstSetting')->findOneBy([
                    'doctor'    => $doctor,
                    'feeType'    => $type,
                    'area'      => $areaType,
                ]);

                if($gstType) {
                    $entityId      = $gstType->getId();
                    $effectiveDate = $gstType->getEffectiveDate();
                    if($gstType->getGst()) {
                        $oldGstId = $gstType->getGst()->getId();
                    }
                    if($gstType->getNewGst()) {
                        $oldGstIdNew = $gstType->getNewGst()->getId();
                    }
                }

                switch ($type) {
                    case Constant::SETTING_GST_REVIEW:
                        if($areaType == 'local'){
                            $applyGst = 'applyGstReviewLocal';
                            $date = 'reviewLocalDate';
                            $code = 'gstCodeReviewLocal';
                        }else{
                            $applyGst = 'applyGstReviewInternational';
                            $date = 'reviewInternationalDate';
                            $code = 'gstCodeReviewInternational';
                        }
                        break;
                    case Constant::SETTING_GST_CONSULT:
                        if($areaType == 'local'){
                            $applyGst = 'applyGstConsultLocal';
                            $date = 'consultLocalDate';
                            $code = 'gstCodeConsultLocal';
                        }else{
                            $applyGst = 'applyGstConsultInternational';
                            $date = 'consultInternationalDate';
                            $code = 'gstCodeConsultInternational';
                        }
                        break;
                    case Constant::SETTING_GST_MEDICINE:
                        if($areaType == 'overseas'){
                            $date = 'medicineInternationalDate';
                            $code = 'gstCodeMedicineInternational';
                        }
                        $applyGst = '';
                        break;
                }

                if($gstType == null){
                    $gstType = new DoctorGstSetting();
                    $gstType->setCreatedOn(new \DateTime());
                }
                $gstType->setDoctor($doctor);
                $gstType->setArea($areaType);
                $gstType->setUpdatedOn(new \DateTime());
                $gstType->setFeeType($type);

                $gstCode = $em->getRepository('UtilBundle:GstCode')->find($data[$code]);

                $effect = false;
                if(isset($data[$date]) && $data[$date] != '') {
                    $effectDate = new \DateTime(date('Y-m-d', strtotime($data[$date])));
                    $gstType->setEffectiveDate($effectDate);

                    if($effectDate <= new \DateTime("now") ){
                        $effect = true;
                    }
                }
                $newPrice = 0;
                if(!empty($gstCode)){
                    $newPrice = $gstCode->getId();
                }


                if($gstType->getGst() == null || $data[$code] == $gstType->getGst() || $effect) {
                    $gstType->setGst($gstCode);
                    $gstType->setNewGst(null);
                }else{
                    $gstType->setNewGst($gstCode);
                }

                if( ($applyGst != '' && isset($data[$applyGst])) || ($type == Constant::SETTING_GST_MEDICINE)){
                    $gstType->setIsHasGst(true);

                } else {
                    $gstType->setIsHasGst(false);
                }

                $doctor->addGstSetting($gstType);
                //insert logs price
                $newGstId    = null;
                $newGstIdNew = null;
                if($gstType->getGst()) {
                    $newGstId = $gstType->getGst()->getId();
                }
                if($gstType->getNewGst()) {
                    $newGstIdNew = $gstType->getNewGst()->getId();
                }
                if($effectiveDate != $gstType->getEffectiveDate() || $oldGstId != $newGstId || $oldGstIdNew != $newGstIdNew ) {
                    $inputs[] = [
                        'tableName'  => 'doctor_gst_setting',
                        'fieldName'  => 'gst_id',
                        'entityId'   => $entityId,
                        'oldPrice'   => $oldGstId,
                        'newPrice'   => $newPrice,
                        'createdBy'  => $authorId,
                        'effectedOn' => $effectDate
                    ];
                }
            }
        }

        //$this->saveLogPrice($inputs);

        $clinicAdd = $clinic->getBusinessAddress();
        $add = '';
        if(empty($clinicAdd)){
            // debug for error data
            $clinicAdd = new ClinicAddress();
            $add = new Address();
            $clinicAdd->setAddress($add);
            $clinic->setBusinessAddress($clinicAdd);
            $phone2 = new Phone();
            $clinicAdd->setBusinessPhone($phone2);
        } else {
            $add = $clinicAdd->getAddress();
        }


        $add->setCity($em->getRepository('UtilBundle:City')->find($data['mainClinicCity']));
        $add->setPostalCode($data['mainClinicZipCode']);
        $add->setLine1($data['mainClinicAddressLine1']);
        $add->setLine2($data['mainClinicAddressLine2']);
        $add->setLine3($data['mainClinicAddressLine3']);
        $doctor->setIsGst($data['gstSetting']);
        if($data['gstSetting']) {
            $doctor->setGstNo($data['mainClinicGstNo']);
            $doctor->setGstEffectDate(  new \DateTime(date('Y-m-d', strtotime($data['mainClinicGstDate']))) );
        } else {
            $doctor->setGstNo('');
            if(isset($data['mainClinicGstDate'])) {
                $doctor->setGstEffectDate( new \DateTime(date('Y-m-d', strtotime($data['mainClinicGstDate']))) );
            }
        }

        if($data['listRxReviewFeeLocal'] != 'Other'){
            $doctor->setRxReviewFeeLocal($data['listRxReviewFeeLocal']);
        }else{
            $doctor->setRxReviewFeeLocal($data['rxReviewFeeLocal']);
        }
        if($data['listRxReviewFeeInternational'] != 'Other'){
            $doctor->setRxReviewFeeInternational($data['listRxReviewFeeInternational']);
        }else{
            $doctor->setRxReviewFeeInternational($data['rxReviewFeeInternational']);
        }
        if($data['listRxFeeLiveConsultLocal'] != 'Other'){
            $doctor->setRxFeeLiveConsultLocal($data['listRxFeeLiveConsultLocal']);
        }else{
            $doctor->setRxFeeLiveConsultLocal($data['rxFeeLiveConsultLocal']);
        }
        if($data['listRxFeeLiveConsultInternational'] != 'Other'){
            $doctor->setRxFeeLiveConsultInternational($data['listRxFeeLiveConsultInternational']);
        }else{
            $doctor->setRxFeeLiveConsultInternational($data['rxFeeLiveConsultInternational']);
        }

        $phone2 = $clinicAdd->getBusinessPhone();

        $country2 = $em->getRepository('UtilBundle:Country')->find($data['mainClinicTelephoneLocation']);
        $phone2->setCountry($country2);
        $phone2->setAreaCode($data['mainClinicAreacode']);
        $phone2->setNumber($data['mainClinicPhone']);

         //primary logo
        if( isset($_FILES["clini-logo-1"])) {
            $logo = $common->uploadfile($_FILES["clini-logo-1"],'clinic/logo-primary-'.$doctor->getId().time());
            if(!empty($logo)) {
                $clinic->setBusinessLogoUrl($logo);
            }
        }
        $this->updateClinic($doctor,$clinicdata);
        $em->persist($doctor);
        $em->flush();

        // logging
        $newData = $this->getDoctorValue($doctor);
        $author = $this->getUser()->getLoggedUser()->getFirstName() . ' ' .
            $this->getUser()->getLoggedUser()->getLastName();
        $arr = array('module' => 'doctors',
                     'title'  =>'doctor_updated',
                     'id'     => $doctor->getId());
                     
        $this->saveLog($oldData, $newData, $author, $arr);

        // insert log price
        if($doctor)
        {
            //$this->logDoctorPrescribingFee($doctor, $arrFileds);
        }

        return new JsonResponse(array('success' => 1 ,'message' => 'update success'));
    }

    private  function saveLog($oldData, $newData, $author, $params) {
        $em = $this->getDoctrine()->getManager();
        $isChanged = Utils::isChanged($oldData, $newData);
        if ($isChanged == false) {
            return;
        }
        $encodeOldData       = json_encode($oldData);
        $encodeNewData       = json_encode($newData);
        $params['entityId']  = isset($params['id']) ? $params['id'] : '';
        $params['action']    = isset($params['action']) ? $params['action'] : 'update';
        $params['oldValue']  = $encodeOldData;
        $params['newValue']  = $encodeNewData;
        $params['createdBy'] = $author;

        // insert data into log table
        $em->getRepository('UtilBundle:Log')->insert($params);
    }

    /**
     * insert logs into audit_trail_price table
     * @param  array $params
     * @param  array $options
     * @return number
     */
    private  function saveLogPrice($params, $options = []) {
        $em = $this->getDoctrine()->getEntityManager();
        try
        {
            if(!isset($params[0]))
                return false;

            if(!is_array($params[0]) || !array_filter($params))
                return false;

            $count = 0;
            foreach ($params as $key => $value) {
                $inputs               = [];

                $inputs['tableName']  = $value['tableName'];
                $inputs['fieldName']  = $value['fieldName'];
                $inputs['entityId']   = $value['entityId'];
                $inputs['oldPrice']   = $value['oldPrice'];
                $inputs['newPrice']   = $value['newPrice'];
                $inputs['createdBy']  = $value['createdBy'];
                $inputs['effectedOn'] = isset($value['effectedOn']) ? $value['effectedOn'] : null;

                $item = $em->getRepository('UtilBundle:AuditTrailPrice')->getLastItem();
                if($item)
                {
                    if(
                        $item->getTableName() == $inputs['tableName'] && $item->getFieldName() == $inputs['fieldName'] &&
                        $item->getEntityId() == $inputs['entityId'] && $item->getOldPrice() == $inputs['oldPrice'] &&
                        $item->getNewPrice() == $inputs['newPrice'] && $item->getEffectedOn() == $inputs['effectedOn']
                    )
                    {
                        continue;
                    }
                }

                $em->getRepository('UtilBundle:AuditTrailPrice')->insert($inputs);
                $count += 1;
            }
            return $count;
        }
        catch(\Exception $e)
        {
            return false;
        }
    }

    /**
    * get value of doctor
    */
    private function getDoctorValue($doctor)
    {
        $result = array();
        if (is_object($doctor) && $doctor->getId()) {
            $em = $this->getDoctrine()->getEntityManager();
            $countryList = $em->getRepository('UtilBundle:Country')->getListContry();
            $currentspec = array();
            $spec = $doctor->getMedicalSpecialty();
            foreach ($spec as $item) {
                array_push($currentspec, $item->getName());
            }
            $result['id'] = $doctor->getId();
            $result['title'] = $doctor->getPersonalInformation()->getTitle();
            $result['firstName'] = $doctor->getPersonalInformation()->getFirstName();
            $result['lastName'] = $doctor->getPersonalInformation()->getLastName();
            $result['displayName'] = $doctor->getDisplayName();
            $result['gender'] = $doctor->getPersonalInformation()->getGender() ? 'Male' : 'Female' ;
            $result['medicalSpecialty'] = implode(', ', $currentspec);
            $result['email'] = $doctor->getPersonalInformation()->getEmailAddress();
            $phoneObj = $doctor->getDoctorPhones()->first()->getContact();
            $result['doctorPhone'] = $phoneObj->getCountry()->getName().' (+'.$phoneObj->getCountry()->getPhoneCode().')'.$phoneObj->getAreaCode().' '.$phoneObj->getNumber();
            
            $medicalLicense = $doctor->getMedicalLicense();
            $result['medicalNumber'] = $medicalLicense->getRegistrationNumber();
            $result['medicalIssueDate'] = $medicalLicense->getIssuingDate()->format('Y');
            $medicalCountry = $medicalLicense->getIssuingCountryId();
            $result['medicalCountry'] = isset($countryList[$medicalCountry])? $countryList[$medicalCountry]: "";
            
            $iden = $doctor->getIdentification()->first();
            $result['passportNum'] = $iden->getIdentityNumber();
            $result['passportdate'] = $iden->getIssueDate()? date('d M y', strtotime($iden->getIssueDate())): "";
            $passportCountryId = $iden->getIssuingCountryId();
            $result['passportCountry'] = isset($countryList[$passportCountryId])? $countryList[$passportCountryId]: "";
            
            $bankA = $doctor->getBankAccount();
            if ($bankA) {
                $result['accountName'] = $bankA->getAccountName();
                $result['accountNum'] = $bankA->getAccountNumber();

                $bank = $bankA->getBank();
                $result['bankName'] = $bank->getName();
                $result['bankCountry'] = $bank->getCountry()->getName();
                $result['bankcode'] = $bank->getSwiftCode();
            }
            
            $result['listRxReviewFeeLocal'] = $doctor->getRxReviewFeeLocal();
            $result['listRxReviewFeeInternational'] = $doctor->getRxReviewFeeInternational();
            $result['listRxFeeLiveConsultLocal'] = $doctor->getRxFeeLiveConsultLocal();
            $result['listRxFeeLiveConsultInternational'] = $doctor->getRxFeeLiveConsultInternational();
            
            $clinics = $doctor->getClinics();
            $clinic = '';
            foreach ($clinics as $cl) {
                if ($cl->getIsPrimary()) {
                    $clinic = $cl;
                    break;
                }
            }
            $clinicSetting = $doctor->getIsGst();
            if ($clinic) {
                $result['clinicName'] = $clinic->getBusinessName();
                $result['clinicEmail'] = $clinic->getEmail();
                $result['clinicLogo'] = $clinic->getBusinessLogoUrl();
                $clinicAdress = $clinic->getBusinessAddress();

                //address
                $mainAddress = $clinicAdress->getAddress();
                $result['mainZipCode'] = $mainAddress->getPostalCode();
                $result['mainLine1'] = $mainAddress->getLine1();
                $result['mainLine2'] = $mainAddress->getLine2();
                $result['mainLine3'] = $mainAddress->getLine3();
                
                $city = $mainAddress->getCity();
                $result['clinicCountry'] = $city->getCountry()->getName();
                $result['clinicState'] = $city->getState()? $city->getState()->getName(): "";
                $result['clinicCity'] = $city->getName();

                $mainPhone = $clinicAdress->getBusinessPhone();
                $result['clinicPhone'] = $mainPhone->getCountry()->getName().' (+'.$mainPhone->getCountry()->getPhoneCode().')'.$mainPhone->getAreaCode().' '.$mainPhone->getNumber();
            }
            
            $result['signature'] = $doctor->getSignatureUrl();
            $result['profilePicture'] = $doctor->getProfilePhotoUrl();
                        
            $result['gstSetting'] = $doctor->getIsGst() ? 'Yes' : 'No';
            $result['gstNo'] = $doctor->getGstNo();
            $result['gstEffectDate'] = $doctor->getGstEffectDate() ? $doctor->getGstEffectDate()->format('d M y'): '';
            
            $result['gstFeesLocal'] = '';
            $result['gstFeesInternational'] = '';
            
            $listGstCode = [];
            $gstCode = $em->getRepository('UtilBundle:GstCode')->findAll();
            foreach ($gstCode as $item) {
                $listGstCode[$item->getId()] = $item->getCode();
            }
            
            $areas = ['local', 'overseas'];
            foreach ($areas as $area) {
                foreach ($doctor->getGstSettings() as $item) {
                    switch ($item->getFeeType()) {
                        case Constant::SETTING_GST_REVIEW:
                            if($area == $item->getArea()){
                                $settingGstReviewInternational = $item;
                            }else{
                                $settingGstReviewLocal = $item;
                            }
                            break;
                        case Constant::SETTING_GST_CONSULT:
                            if($area == $item->getArea()){
                                $settingGstConsultInternational = $item;
                            }else{
                                $settingGstConsultLocal = $item;
                            }
                            break;
                        case Constant::SETTING_GST_MEDICINE:
                            if($area == $item->getArea()){
                                $settingGstMedicineInternational = $item;
                            }else{
                                $settingGstMedicineLocal = $item;
                            }
                            break;
                    }
                }
            }
            $applyGstReviewLocal = isset($settingGstReviewLocal) && is_object($settingGstReviewLocal) ? $settingGstReviewLocal->getIsHasGst() : 0;
            $gstCodeReviewLocal = isset($settingGstReviewLocal) ? $settingGstReviewLocal->getNewGst() != null ? $settingGstReviewLocal->getNewGst() : $settingGstReviewLocal->getGst() : '';
            $reviewLocalDate = isset($settingGstReviewLocal) && $settingGstReviewLocal->getEffectiveDate() != null ? 
            $settingGstReviewLocal->getEffectiveDate()->format('d M y') : '';
            $applyGstReviewLocal = $applyGstReviewLocal ? 'Yes' : 'No';
            $gstCodeReviewLocal = is_object($gstCodeReviewLocal) ? $listGstCode[$gstCodeReviewLocal->getId()] : '';
            $result['gstFeesLocal'] .= 'Doctor Fee - Prescribing Fee (Has GST : '.$applyGstReviewLocal.', GST Code : '.$gstCodeReviewLocal.', Effective Date : '.$reviewLocalDate.')';
            
            $applyGstConsultLocal = isset($settingGstConsultLocal) && is_object($settingGstConsultLocal) ? $settingGstConsultLocal->getIsHasGst() : false;
            $gstCodeConsultLocal = isset($settingGstConsultLocal) ? $settingGstConsultLocal->getNewGst() == null ? $settingGstConsultLocal->getGst() : $settingGstConsultLocal->getNewGst() : '';
            $consultLocalDate = isset($settingGstConsultLocal) && $settingGstConsultLocal->getEffectiveDate() != null ? 
            $settingGstConsultLocal->getEffectiveDate()->format('d M y') : '';
            $applyGstConsultLocal = $applyGstConsultLocal ? 'Yes' : 'No';
            $gstCodeConsultLocal = is_object($gstCodeConsultLocal) ? $listGstCode[$gstCodeConsultLocal->getId()] : '';
            $result['gstFeesLocal'] .= ', Doctor Fee - Liveconsult (Has GST : '.$applyGstConsultLocal.', GST Code : '.$gstCodeConsultLocal.', Effective Date : '.$consultLocalDate.')';
            
            $gstCodeMedicineInternational = isset($settingGstMedicineInternational) ? $settingGstMedicineInternational->getNewGst() == null ? $settingGstMedicineInternational->getGst() : $settingGstMedicineInternational->getNewGst() : '';
            $medicineInternationalDate = isset($settingGstMedicineInternational) && $settingGstMedicineInternational->getEffectiveDate() != null ? $settingGstMedicineInternational->getEffectiveDate()->format('d M y') : '';
            $gstCodeMedicineInternational = is_object($gstCodeMedicineInternational) ? $listGstCode[$gstCodeMedicineInternational->getId()] : '';
            $result['gstFeesInternational'] .= 'Medicines (GST Code : '.$gstCodeMedicineInternational.', Effective Date : '.$medicineInternationalDate.')';
            
            $applyGstReviewInternational = isset($settingGstReviewInternational) && is_object($settingGstReviewInternational) ? $settingGstReviewInternational->getIsHasGst() : false;
            $gstCodeReviewInternational = isset($settingGstReviewInternational) ? $settingGstReviewInternational->getNewGst() == null ? $settingGstReviewInternational->getGst() : $settingGstReviewInternational->getNewGst() : '';
            $reviewInternationalDate = isset($settingGstReviewInternational) && $settingGstReviewInternational->getEffectiveDate() != null ? $settingGstReviewInternational->getEffectiveDate()->format('d M y') : '';
            $applyGstReviewInternational = $applyGstReviewInternational ? 'Yes' : 'No';
            $gstCodeReviewInternational = is_object($gstCodeReviewInternational) ? $listGstCode[$gstCodeReviewInternational->getId()] : '';
            $result['gstFeesInternational'] .= ', Doctor Fee - Prescribing Fee (Has GST : '.$applyGstReviewInternational.', GST Code : '.$gstCodeReviewInternational.', Effective Date : '.$reviewInternationalDate.')';
            
            $applyGstConsultInternational = isset($settingGstConsultInternational) && is_object($settingGstConsultInternational) ? $settingGstConsultInternational->getIsHasGst() : false;
            $gstCodeConsultInternational = isset($settingGstConsultInternational) ? $settingGstConsultInternational->getNewGst() == null ? $settingGstConsultInternational->getGst() : $settingGstConsultInternational->getNewGst() : '';
            $consultInternationalDate = isset($settingGstConsultInternational) && $settingGstConsultInternational->getEffectiveDate() != null ? $settingGstConsultInternational->getEffectiveDate()->format('d M y') : '';
            $applyGstConsultInternational = $applyGstConsultInternational ? 'Yes' : 'No';
            $gstCodeConsultInternational = is_object($gstCodeConsultInternational) ? $listGstCode[$gstCodeConsultInternational->getId()] : '';
            $result['gstFeesInternational'] .= ', Doctor Fee - Liveconsult (Has GST : '.$applyGstConsultInternational.', GST Code : '.$gstCodeConsultInternational.', Effective Date : '.$consultInternationalDate.')';
            
            $subClinics = $em->getRepository('UtilBundle:Doctor')->getClinicData($doctor->getId());
            $result['subClinics'] = '';
            $noSubsClinics = 1;
            if (count($subClinics) > 0) {
                foreach ($subClinics as $subs) {
                    if ($result['subClinics'] == '') {
                        $result['subClinics'] = 'Sub-Clinic #'.$noSubsClinics.' (Clinic Name : '.$subs['name'].', Clinic Email : '.$subs['email'].', Clinic Telephone Number : '.$subs['phoneLocationName'].'(+'.$subs['phoneLocationCode'].') '.$subs['phoneArea'].''.$subs['phoneNumber'].', Address Line 1 : '.$subs['line1'].', Address Line 2 : '.$subs['line2'].', Address Line 3 : '.$subs['line3'].', Country : '.$subs['countryName'].', State / Province : '.$subs['stateName'].', City : '.$subs['cityName'].', Zip / Postal Code : '.$subs['zipCode'].', Clinic Logo : '.$subs['logo'].')';
                    } else {
                        $result['subClinics'] .= ', Sub-Clinic #'.$noSubsClinics.' (Clinic Name : '.$subs['name'].', Clinic Email : '.$subs['email'].', Clinic Telephone Number : '.$subs['phoneLocationName'].'(+'.$subs['phoneLocationCode'].') '.$subs['phoneArea'].''.$subs['phoneNumber'].', Address Line 1 : '.$subs['line1'].', Address Line 2 : '.$subs['line2'].', Address Line 3 : '.$subs['line3'].', Country : '.$subs['countryName'].', State / Province : '.$subs['stateName'].', City : '.$subs['cityName'].', Zip / Postal Code : '.$subs['zipCode'].', Clinic Logo : '.$subs['logo'].')';
                    }
                    $noSubsClinics++;
                }
            }
        }
        return $result;
    }

    private function updateClinic($doctor,$dataPost)
    {

        $em = $this->getDoctrine()->getEntityManager();
        $clinics = $doctor->getClinics();
        $common = $this->get('util.common');
        $listClinics = array();
        foreach ($clinics as $cl) {
            if( !$cl->getIsPrimary()) {
                $cl->setDeletedOn(new \DateTime(date('Y-m-d')));
                $listClinics[$cl->getId()] = $cl;
            }
        }

        foreach ($dataPost as $key=>$data) {
            if(isset($data['id'])) {
                $clinic = $listClinics[$data['id']];
                $clinic->setDeletedOn(null);
                $clinic->setBusinessName($data['name']);
                $clinic->setEmail($data['email']);
                $clinicAdd = $clinic->getBusinessAddress();
                $add = $clinicAdd->getAddress();
                $add->setCity($em->getRepository('UtilBundle:City')->find($data['city']));
                $add->setPostalCode($data['zipCode']);
                $add->setLine1($data['address1']);
                $add->setLine2($data['address2']);
                $add->setLine3($data['address3']);
                $phone2 =  $clinicAdd->getBusinessPhone();
                $country2 = $em->getRepository('UtilBundle:Country')->find($data['phoneLocation']);
                $phone2->setCountry($country2);
                $phone2->setPhoneType($em->getRepository('UtilBundle:PhoneType')->getPhone());
                $phone2->setAreaCode($data['phoneArea']);
                $phone2->setNumber($data['phoneNumber']);
                if(isset($_FILES["clini-logo-".$key])) {
                    $logo = $common->uploadfile($_FILES["clini-logo-".$key],'clinic/logo-'.$key.'-'.$doctor->getId());
                    if(!empty($logo)) {
                        $clinic->setBusinessLogoUrl($logo);
                    }
                }

            } else {
                $clinic = new Clinic();
                $clinic->setBusinessName($data['name']);
                $clinic->setEmail($data['email']);
                $clinicAdd = new ClinicAddress();
                $add = new Address();
                $add->setCity($em->getRepository('UtilBundle:City')->find($data['city']));
                $add->setPostalCode($data['zipCode']);
                $add->setLine1($data['address1']);
                $add->setLine2($data['address2']);
                $add->setLine3($data['address3']);
                $clinicAdd->setAddress($add);

                $phone2 = new Phone();
                $country2 = $em->getRepository('UtilBundle:Country')->find($data['phoneLocation']);
                $phone2->setCountry($country2);
                $phone2->setPhoneType($em->getRepository('UtilBundle:PhoneType')->getPhone());
                $phone2->setAreaCode($data['phoneArea']);
                $phone2->setNumber($data['phoneNumber']);
                $clinicAdd->setBusinessPhone($phone2);
                $clinic->setBusinessAddress($clinicAdd);
                if(isset($_FILES["clini-logo-".$key])) {
                    $logo = $common->uploadfile($_FILES["clini-logo-".$key],'clinic/logo-'.$key.'-'.$doctor->getId());
                    if(!empty($logo)) {
                        $clinic->setBusinessLogoUrl($logo);
                    }
                }
                $doctor->addClinic($clinic);
            }
        }
    }

    /**
     * @Route("/doctor-edit-get-dependent", name="doctor_profile_create_getdependent")
    */
    public function getDependentData(Request $request)
    {

        $em = $this->getDoctrine()->getEntityManager();
        $result = array();
        if($request->request->get('type') == 1) {
            $result =  $em->getRepository('UtilBundle:City')->getStateByCountry($request->request->get('data'));
        }
        if($request->request->get('type') == 2) {
            $result =  $em->getRepository('UtilBundle:State')->getCityByState($request->request->get('data'));

        }
        if($request->request->get('type') == 3) {
            $result = $em->getRepository('UtilBundle:Country')->getByPreferredCountry(true);
        }
        if($request->request->get('type') == 4) {

            $parameters = $request->request->get('admin_doctor');
            if ($parameters['bankCountryIssue'] == Constant::ID_SINGAPORE || $parameters['bankCountryIssue'] == Constant::ID_MALAYSIA) {
                $bank = $em->getRepository('UtilBundle:Bank')->find($parameters['bankName']);
                if ($bank) {
                    $bankName = $bank->getName();
                    $bankBranch = $bank->getBranchName();
                    if ($bankBranch) {
                        $bankName .= ' '. $bankBranch;
                    }
                    $parameters['bankName'] = $bankName;
                }
            }
            $countryList = [];
            $clinics = $request->request->get("clinics",[]);
            $countries = $em->getRepository('UtilBundle:Country')->getByPreferredCountry(true);
            $country = array('0'=>'','empty'=>'-');
            $agents =  $em->getRepository('UtilBundle:Agent')->findAll();
            $agent = array('0'=>'','empty'=>'-');
            foreach ($agents as $obj) {
                $agent[$obj->getId()] = $obj->getPersonalInformation()->getFullName();
            }
            $phoneCode = array('0'=>'','empty'=>'-');
            foreach ($countries as $c) {
                $country[$c['id']]= $c['name'];
                $phoneCode[$c['id']] = $c['phoneCode'];
            }
            $special = array('0'=>'','empty'=>'-');
            $specials =  $em->getRepository('UtilBundle:MedicalSpecialty')->findAll();

            foreach ($specials as $obj) {
                $special[$obj->getId()] = $obj->getName();
            }

            $listGstCode = [];
            $gstCode = $em->getRepository('UtilBundle:GstCode')->findAll();
            foreach ($gstCode as $item) {
                $listGstCode[$item->getId()] = $item->getCode();
            }

            $countryList[]  =  $parameters['mainClinicCountry'];
            foreach ($clinics as $cl){
                $countryList[] = $cl['country'];
            }

            $city = $em->getRepository('UtilBundle:City')->getListCity($countryList);
            $state = $em->getRepository('UtilBundle:State')->getListState($countryList);
            return $this->render('DoctorBundle:profile:review-profile.html.twig', array(
                        'form' => $parameters,
                        'clinics' => $clinics,
                        'gender' => array('1' => 'Male',
                            '0' => 'Female'),
                        'country' => $country,
                        'state' => $state,
                        'city' => $city,
                        'phoneCode' => $phoneCode,
                        'special' => $special,
                        'agent' => $agent,
                        'gst' => array('1' => 'Yes',
                            '0' => 'No'),
                        'listGstCode'   => $listGstCode,
            ));
        }
        if($request->request->get('type') == 5) {

            $parameters = $request->request->get('data');
            $data = $em->getRepository('UtilBundle:Doctor')->getClinicData($parameters);
            $result = array('status'=>1,'total'=>count($data),'data'=>$data);

        }
        if($request->request->get('type') == 6) {
            $result =  $em->getRepository('UtilBundle:Country')->getCityByCountry($request->request->get('data'));
        }
        return new JsonResponse($result);

    }

    public function logDoctorPrescribingFee($em, $doctor, $arrFileds)
    {
        $em = $this->getDoctrine()->getEntityManager();
        try
        {
            $authorId = $this->getUser()->getLoggedUser()->getId();
            $table    = 'doctor';
            $inputs   = [];
            $tmp      = [
                'tableName'  => $table,
                'fieldName'  => null,
                'entityId'   => $doctor->getId(),
                'oldPrice'   => null,
                'newPrice'   => null,
                'createdBy'  => $authorId,
                'em'         => $em,
                'effectedOn' => null
            ];

            foreach ($arrFileds as $key => $value) {
                switch ($key) {
                    case 'rx_review_fee':
                        if($doctor->getRxReviewFee() == $value) {
                            break;
                        }
                        $inputs[] = array_merge($tmp, [
                            'fieldName' => $key,
                            'oldPrice'  => $value,
                            'newPrice'  => $doctor->getRxReviewFee(),
                        ]);
                        break;

                    case 'rx_review_fee_local':
                        if($doctor->getRxReviewFeeLocal() == $value) {
                            break;
                        }
                        $inputs[] = array_merge($tmp, [
                            'fieldName' => $key,
                            'oldPrice'  => $value,
                            'newPrice'  => $doctor->getRxReviewFeeLocal(),
                        ]);
                        break;

                    case 'rx_review_fee_international':
                        if($doctor->getRxReviewFeeInternational() == $value) {
                            break;
                        }
                        $inputs[] = array_merge($tmp, [
                            'fieldName' => $key,
                            'oldPrice'  => $value,
                            'newPrice'  => $doctor->getRxReviewFeeInternational(),
                        ]);
                        break;

                    case 'rx_fee_live_consult_local':
                        if($doctor->getRxFeeLiveConsultLocal() == $value) {
                            break;
                        }
                        $inputs[] = array_merge($tmp, [
                            'fieldName' => $key,
                            'oldPrice'  => $value,
                            'newPrice'  => $doctor->getRxFeeLiveConsultLocal(),
                        ]);
                        break;

                    case 'rx_fee_live_consult_international':
                        if($doctor->getRxFeeLiveConsultInternational() == $value) {
                            break;
                        }
                        $inputs[] = array_merge($tmp, [
                            'fieldName' => $key,
                            'oldPrice'  => $value,
                            'newPrice'  => $doctor->getRxFeeLiveConsultInternational(),
                        ]);
                        break;

                    case 'is_gst':
                        if($doctor->getIsGst() == $value) {
                            break;
                        }
                        $inputs[] = array_merge($tmp, [
                            'fieldName'  => $key,
                            'oldPrice'   => $value,
                            'newPrice'   => $doctor->getIsGst(),
                            'effectedOn' => $doctor->getGstEffectDate()
                        ]);
                        break;
                }
            }
            $this->saveLogPrice($inputs);
        }
        catch(\Exception $e)
        {
            return false;
        }
    }

    /**
     * @Route("/doctor-subscriber-agreement", name="doctor_subscriber_agreement")
     */
    public function DoctorAgreementAction(Request $request, $isPlain = false)
    {
        $isPlain = $request->get('is_plain', $isPlain);
        $em = $this->getDoctrine()->getManager();
        $doctorAgreement = $em->getRepository('UtilBundle:FileDocument')->getContentForClient(Constant::DOCUMENT_NAME_DOCTOR_AGREEMENT, Common::getCurrentSite($this->container));

        if ($isPlain) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(array(
                    'success' => true,
                    'data' => $doctorAgreement['contentAfter']
                ));
            }
            return new Response($doctorAgreement['contentAfter']);
        } else {
            $params = array(
                'title' => Constant::DOCUMENT_NAME_DOCTOR_AGREEMENT,
                'content' => $doctorAgreement['contentAfter']
            );
            return $this->render('AdminBundle:document_setting:document_output.html.twig', $params);
        }
    }

}
