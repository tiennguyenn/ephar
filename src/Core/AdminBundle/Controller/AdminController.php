<?php

namespace AdminBundle\Controller;

use AdminBundle\Controller\BaseController;
use AdminBundle\Form\MinFeeType;
use AdminBundle\Form\PlatformSharePercentageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use AdminBundle\Form\PharmacyType;
use AdminBundle\Form\DoctorAdminType;
use AdminBundle\Form\FeeSettingType;
use AdminBundle\Form\DeliveryAdminType;
use AdminBundle\Form\CustomAdminFeeType;
use AdminBundle\Form\OthersFeeType;
use AdminBundle\Form\OthersSettingType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use UtilBundle\Entity\AgentMininumFeeSetting;
use UtilBundle\Entity\Doctor;
use UtilBundle\Entity\PersonalInformation;
use UtilBundle\Entity\Phone;
use UtilBundle\Entity\Address;
use UtilBundle\Entity\DoctorPhone;
use UtilBundle\Entity\Clinic;
use UtilBundle\Entity\ClinicAddress;
use UtilBundle\Entity\CourierRate;
use UtilBundle\Entity\AgentDoctor;
use UtilBundle\Entity\Bank;
use UtilBundle\Entity\Courier;
use UtilBundle\Entity\SequenceNumber;
use UtilBundle\Entity\BankAccount;
use UtilBundle\Entity\MedicalLicense;
use UtilBundle\Entity\Identification;
use UtilBundle\Entity\Pharmacy;
use UtilBundle\Entity\PaymentGatewayFee;
use UtilBundle\Entity\CustomClearanceAdminFee;
use UtilBundle\Entity\FeeSetting;
use UtilBundle\Entity\DoctorGstSetting;
use UtilBundle\Entity\Log;
use UtilBundle\Utility\Common;
use UtilBundle\Utility\Constant;
use Dompdf\Dompdf;
use Symfony\Component\HttpFoundation\Response;
use UtilBundle\Utility\Utils;
use UtilBundle\Utility\MsgUtils;

class AdminController extends BaseController {

    /**
     * @Route("/", name="homepage")
     */
    public function homepageAction(Request $request) {
        return $this->redirectToRoute('login');
    }

    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function dashboardAction(Request $request) {
        $em = $this->getDoctrine()->getEntityManager();
        $params = $request->request->all();
        $totalDoctor = $em->getRepository('UtilBundle:Doctor')->getDoctorsAdmin($request);
        $totalAgent = $em->getRepository('UtilBundle:Agent')->getAgentsAdmin($request);
        return $this->render('AdminBundle:admin:dashboard.html.twig', [
                    'totalDoctor' => $totalDoctor['total'],
                    'totalAgent' => $totalAgent['total'],
                    'totalFee' => 0,
        ]);
    }

    /**
     * get data for chart
     * @Route("/admin/get-chart", name="ajax_get_chart_data_admin")
     * @author toan.le
     */
    public function ajaxDataChart() {
        // $agent = $this->getUser();
        $dataChart = $this->getDoctrine()->getRepository('UtilBundle:Rx')->getDataChart(['feeType' => Constant::FEE_PLATFORM]);

        $totalFee = 0;
        foreach ($dataChart as $value) {
            $totalFee += $value['totalFee'];
        }

        $data = [
            'dataChart' => $dataChart,
            'totalFee' => number_format($totalFee, 2)
        ];
        return new JsonResponse($data);
    }

    /**
     * @Route("/admin/doctor", name="admin_doctor_list")
     */
    public function doctorAction(Request $request) {
        $parameters = array(
            'ajaxURL' => 'admin_doctor_list_ajax',
            'updateStatusUrl' => 'admin_doctor_update_status_ajax',
        );
//        $em = $this->getDoctrine()->getEntityManager();
//        $doctor = $em->getRepository('UtilBundle:Doctor')->find(30);
//        $this->senDoctorEmail($doctor);
        return $this->render('AdminBundle:admin:doctor.html.twig', $parameters);
    }

    /**
     * @Route("/admin/doctor-update-status", name="admin_doctor_update_status_ajax")
     */
    public function doctorUpdateStatusAction(Request $request) {
        $em = $this->getDoctrine()->getEntityManager();
        $id = $request->request->get('id');
        $type = $request->request->get('type');
        if ($type == 1) {
            $doctor = $em->getRepository('UtilBundle:Doctor')->find($id);
            $status = $doctor->getIsActive();
            $newStatus = ($status + 1) % 2;
            $doctor->setIsActive($newStatus);

			$users = array();
			$userActors = $em->getRepository('UtilBundle:UserActors')->findBy(array(
				'entityId' => $doctor->getId(),
				'role' => Constant::DOCTOR_ROLE
			));
			if ($userActors) {
				foreach ($userActors as $userActor) {
					$users[] = $userActor->getUser();
				}
			}

			$em->beginTransaction();
			try {
				$em->persist($doctor);
				$em->flush();

				if (!empty($users)) {
					foreach ($users as $user) {
						$user->setIsActive($newStatus);
						$em->persist($user);
					}
					$em->flush();
				}

				$em->commit();
			} catch (\Exception $ex) {
				$em->rollback();
			}

            // logging
            $oldData = array('isActive' => $status);
            $newData = array('isActive' => $newStatus);
            $author = $this->getUser()->getLoggedUser()->getFirstName() . ' ' .
                $this->getUser()->getLoggedUser()->getLastName();
            $arr = array('module' => 'doctors',
                         'title'  =>'doctor_status_changed',
                         'id'     => $doctor->getId());
            Utils::saveLog($oldData, $newData, $author, $arr, $em);
        } elseif ($type == 2) {
            $deletedOn = new \DateTime("now");
            $doctor = $em->getRepository('UtilBundle:Doctor')->find($id);
            $doctor->setDeletedOn($deletedOn);

			$users = array();
			$userActors = $em->getRepository('UtilBundle:UserActors')->findBy(array(
				'entityId' => $doctor->getId(),
				'role' => Constant::DOCTOR_ROLE
			));

			$em->beginTransaction();
			try {
				$em->persist($doctor);
				$em->flush();

				if (!empty($userActors)) {
					foreach ($userActors as $userActor) {
						$users[] = $userActor->getUser();
						$userActor->setDeletedOn($deletedOn);
						$em->persist($userActor);
					}
					$em->flush();
				}

				if (!empty($users)) {
					foreach ($users as $user) {
						$user->setIsActive(0);
						$em->persist($user);
					}
					$em->flush();
				}

				$em->commit();
			} catch (\Exception $ex) {
				$em->rollback();
			}

            // logging
            $oldData = array();
            $newData = array('deletedOn' => $newStatus);
            $author = $this->getUser()->getLoggedUser()->getFirstName() . ' ' .
                $this->getUser()->getLoggedUser()->getLastName();
            $arr = array('module' => 'doctors',
                         'title'  =>'doctor_deleted',
                         'id'     => $doctor->getId());
            Utils::saveLog($oldData, $newData, $author, $arr, $em);
        }

        return new JsonResponse(array('success' => true));
    }

      /**
     * @Route("/admin/validate-email-mpa", name="admin_validate_email_mpa")
     */
    public function validateEmailMpaAction(Request $request) {
        $value = $request->request->get('data');

        $em = $this->getDoctrine()->getEntityManager();

        $obj = $em->getRepository('UtilBundle:PersonalInformation')->findByEmailAddress($value);
        $objMpa = $em->getRepository('UtilBundle:MasterProxyAccount')->findByEmailAddress($value);
        $total = count($objMpa) + count($obj);
        $result = array('success' => true, 'total' => $total);
        if($total >= 1){
            $result = array('success' => false, 'total' => $total);
//            return new JsonResponse($result);
        }
        return new JsonResponse($result);
    }

    
    /**
     * @Route("/admin/doctor/{id}/reactivate-doctor", name="admin_reactivate_doctor")
     */
    public function reactivateDoctorAction(Request $request, $id)
    {
        try{
            $em = $this->getDoctrine()->getEntityManager();
            $doctor = $em->getRepository('UtilBundle:Doctor')->find($id);
            $deletedOn = null;
            $doctor->setDeletedOn($deletedOn);
            $doctor->setIsActive(true);

            $users = array();
            $userActors = $em->getRepository('UtilBundle:UserActors')->findBy(array(
                'entityId' => $doctor->getId(),
                'role' => Constant::DOCTOR_ROLE
            ));

            $em->beginTransaction();
            try {
                $em->persist($doctor);
                $em->flush();

                if (!empty($userActors)) {
                    foreach ($userActors as $userActor) {
                        $users[] = $userActor->getUser();
                        $userActor->setDeletedOn($deletedOn);
                        $em->persist($userActor);
                    }
                    $em->flush();
                }

                if (!empty($users)) {
                    foreach ($users as $user) {
                        $user->setIsActive(1);
                        $em->persist($user);
                    }
                    $em->flush();
                }

                $em->commit();
            } catch (\Exception $ex) {
                $em->rollback();
            }

            return new JsonResponse(array('success' => true));
        } catch (\Exception $exception) {
            return new JsonResponse(array('success' => false));
        }
    }

    /**
     * @Route("/admin/doctor-auto-complete", name="admin_doctor_autocomplete")
     */
    public function doctorAutocompleteAction(Request $request) {
        $data = array();
        $data['text'] = $request->request->get('term');
        $data['status'] = $request->request->get('status', 2);
        $em = $this->getDoctrine()->getEntityManager();
        $result = $em->getRepository('UtilBundle:Doctor')->selectDoctorAutoComplete($data);

        return new JsonResponse($result);
    }

    /**
     * @Route("/admin/doctor-ajax", name="admin_doctor_list_ajax")
     */
    public function doctorAjaxAction(Request $request) {
        $em = $this->getDoctrine()->getEntityManager();
        $result = $em->getRepository('UtilBundle:Doctor')->getDoctorsAdmin($request->request);

        return new JsonResponse($result);
    }

    /**
     * @Route("/admin/doctor/create", name="admin_doctor_create")
     */
    public function doctorCreatetAction(Request $request) {
        $em = $this->getDoctrine()->getEntityManager();
        $agent = $em->getRepository('UtilBundle:Agent')->getAgentForEditDoctor();
        $secondaryAgent =  $em->getRepository('UtilBundle:Agent')->get3rdAgentForEditDoctor();
        $specality = $em->getRepository('UtilBundle:MedicalSpecialty')->getMedicalSpecality();
        $country = $em->getRepository('UtilBundle:Country')->getByPreferredCountry(true);
        $dependentData = array();
        $dependentData['country'] = $country;
        $doctor = new Doctor();
        $clinic = new Clinic();

        $form = $this->createForm(new DoctorAdminType(
            array(
                'agent' => $agent,
                'doctor' => $doctor,
                'secondaryAgent' => $secondaryAgent,
                'specality' => $specality,
                'depend' => $dependentData,
                'entity_manager' => $this->get('doctrine.orm.entity_manager')
            )
        ), array(), array());
        $parameters = array(
            'form' => $form->createView(),
            'ajaxURL' => 'admin_doctor_create_ajax',
            'ajaxDependent' => 'admin_doctor_create_getdependent',
            'successUrl' => 'admin_doctor_list',
            'doctorId' => '',
            'doctor' => $doctor,
            'clinic' => $clinic,
            'doctorGst' => 0,
            'title' => 'REGISTER DOCTOR',
            'message' => ''
        );

        return $this->render('AdminBundle:admin:doctor-create.html.twig', $parameters);
    }

    /**
     * @Route("/admin/doctor/{id}/edit", name="admin_doctor_edit")
     */
    public function doctorEditAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $agent = $em->getRepository('UtilBundle:Agent')->getAgentForEditDoctor();
        $secondaryAgent =  $em->getRepository('UtilBundle:Agent')->get3rdAgentForEditDoctor();
        $specality = $em->getRepository('UtilBundle:MedicalSpecialty')->getMedicalSpecality();
        $country = $em->getRepository('UtilBundle:Country')->getByPreferredCountry(true);
        $doctor = $em->getRepository('UtilBundle:Doctor')->find($id);
        if (empty($doctor)) {
            return $this->redirectToRoute('admin_doctor_create');
        }
        $clinics = $doctor->getClinics();
        $clinic = '';
        foreach ($clinics as $cl) {
            if ($cl->getIsPrimary()) {
                $clinic = $cl;
                break;
            }
        }
        $gst = 1;
        if ($doctor->getIsGst()) {
            $gst = 2;
        }
        $message = '';
        $paymentGates = [Constant::PAYMENT_GATE_MOLPAY => 'MOL Pay', Constant::PAYMENT_GATE_IPAY => 'iPay88'];
        if(!empty($doctor->getNewPaymentGate()) && $doctor->getNewPaymentGate() != $doctor->getPaymentGate()  && strtotime($doctor->getPaymentGateEffective()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            $message = "New value is ". $paymentGates[$doctor->getNewPaymentGate()]." - effect from ".$doctor->getPaymentGateEffective()->format('M Y');
        }
        $dependentData = array();
        $dependentData['country'] = $country;
        $dependentData['cityRepo'] = $em->getRepository('UtilBundle:City');
        $form = $this->createForm(new DoctorAdminType(
            array(
                'agent' => $agent,
                'specality' => $specality,
                'secondaryAgent' => $secondaryAgent,
                'doctor' => $doctor,
                'depend' => $dependentData,
                'entity_manager' => $this->get('doctrine.orm.entity_manager')
            )
        ), array(), array());
        $parameters = array(
            'form' => $form->createView(),
            'ajaxURL' => 'admin_doctor_edit_ajax',
            'ajaxDependent' => 'admin_doctor_create_getdependent',
            'successUrl' => 'admin_doctor_list',
            'doctorId' => $id,
            'doctor' => $doctor,
            'clinic' => $clinic,
            'doctorGst' => $gst,
            'title' => 'Edit doctor',
            'message' => $message
        );

        return $this->render('AdminBundle:admin:doctor-create.html.twig', $parameters);
    }

    /**
     * @Route("/admin/doctor-edit-submit", name="admin_doctor_edit_ajax")
     */
    public function doctorEditSubmitAction(Request $request) {
        $em         = $this->getDoctrine()->getEntityManager();
        $data       = $request->request->get('admin_doctor');
        $data = Common::removeSpaceOf($data);
        $clinicdata = $request->request->get('clinics', array());
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

        // update personal infomation
        $personalInfo = $doctor->getPersonalInformation();
        $personalInfo->setFirstName($data['firstName']);
        $personalInfo->setLastName($data['lastName']);
        $personalInfo->setGender($data['gender']);
        $doctor->setDisplayName($data['displayName']);
        $personalInfo->setPassportNo($data['localIdPassport']);
        // update doctor phone
        $doctorPhone = $doctor->getDoctorPhones()->first();
        $phone       = $doctorPhone->getContact();
        $country     = $em->getRepository('UtilBundle:Country')->find($data['phoneLocation']);
        $phone->setCountry($country);
        $phone->setPhoneType($em->getRepository('UtilBundle:PhoneType')->getPhone());
//        $phone->setAreaCode($data['phoneArea']);
        $phone->setNumber($data['phone']);
        //update medical license
        $mlicense = $doctor->getMedicalLicense();
        $mlicense->setRegistrationNumber($data['localMedicalLicence']);
        $mlicense->setIssuingCountryId($data['localMedicalCountry']);
        $date = new \DateTime(date('Y-m-d', strtotime('01-01-'.$data['localMedicalDate'])));
        $mlicense->setIssuingDate($date);
        // update identify

        $iden = $doctor->getIdentification()->first();
        $iden->setIdentityNumber($data['localIdPassport']);
        $iden->setIssuingCountryId($data['localIdPassportCountry']);

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
        $bankAcc->setAccountName($data['accountName']);
        $bankAcc->setAccountNumber($data['accountNumber']);

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

        $bankAcc->setBank($bank);
        $doctor->setBankAccount($bankAcc);
        //payment gateway
        if((date('m')+2) <= 12) {
            $effectDate = date("Y") . '-' . (date('m') + 2) . '-01' ;
        } else {
            $effectDate = (date("Y")+ 1) . '-' . (date('m') -10) . '-01' ;
        }
        if(empty($doctor->getPaymentGateEffective())){
            $doctor->setNewPaymentGate($data['paymentGate']);
            $doctor->setPaymentGateEffective(new \DateTime($effectDate));
        } else {
            $effect = $doctor->getPaymentGateEffective()->format('Y-m-d');
            if(strtotime($effect) < strtotime(date('Y-m-d'))){
                $doctor->setPaymentGate($doctor->getNewPaymentGate());
            }
            $doctor->setNewPaymentGate($data['paymentGate']);
            $doctor->setPaymentGateEffective(new \DateTime($effectDate));
        }

        $agentId = '';
        $secondaryAgentId ='';
        $listAgentMaps = $doctor->getAgentDoctors();
        foreach ($listAgentMaps as $it){
            if(!$it->getIsActive () || !empty($it->getDeletedOn()) ){
                continue;
            }
            if($it->getIsPrimary()){
                $agentId = $it->getAgent()->getId();
            } else {
                $secondaryAgentId = $it->getAgent()->getId();
            }
        }
        // doctor agent

        if ($agentId != $data['agentId']) {
            foreach ($listAgentMaps as $it){
                if(!$it->getIsActive () || !empty($it->getDeletedOn()) ){
                    continue;
                }
                if($it->getIsPrimary()){
                    $it->setIsActive(0);
                    $it->setDeletedOn(new \DateTime(date('Y-m-d H:i:s')));
                    $em->persist($it);
                }
            }
            $agentDoctor = new AgentDoctor();
            $agentDoctor->setAgent($em->getRepository('UtilBundle:Agent')->find($data['agentId']));
            $agentDoctor->setIsActive(1);
            $agentDoctor->setIsPrimary(true);
            $doctor->addAgentDoctor($agentDoctor);
        }
        if(isset($data['check3rd']) && $data['check3rd']) {
            $doctor->setIsApply3rdAgent($data['check3rd']);
            if ($secondaryAgentId != $data['secondaryAgentId']) {
                foreach ($listAgentMaps as $it){
                    if(!$it->getIsActive () || !empty($it->getDeletedOn()) ){
                        continue;
                    }
                    if(!$it->getIsPrimary()){
                        $it->setIsActive(0);
                        $it->setDeletedOn(new \DateTime(date('Y-m-d H:i:s')));
                        $em->persist($it);
                    }
                }
                $agentDoctor = new AgentDoctor();
                $agentDoctor->setAgent($em->getRepository('UtilBundle:Agent')->find($data['secondaryAgentId']));
                $agentDoctor->setIsActive(1);
                $agentDoctor->setIsPrimary(false);
                $doctor->addAgentDoctor($agentDoctor);
            }

        } else {
            $doctor->setIsApply3rdAgent(false);
            foreach ($listAgentMaps as $it){
                if(!$it->getIsActive () || !empty($it->getDeletedOn()) ){
                    continue;
                }
                if(!$it->getIsPrimary()){
                    $it->setIsActive(0);
                    $it->setDeletedOn(new \DateTime(date('Y-m-d H:i:s')));
                    $em->persist($it);
                }
            }
        }

        $clinics = $doctor->getClinics();
        $clinic = '';
        foreach ($clinics as $cl) {
            if ($cl->getIsPrimary()) {
                $clinic = $cl;
                break;
            }
        }
        $clinic->setBusinessName($data['mainClinicName']);
        $clinic->setEmail($data['mainClinicEmail']);

        //primary logo
        if (isset($_FILES["clini-logo-1"])) {
            $logo = $this->uploadfile($_FILES["clini-logo-1"], 'clinic/logo-primary-' . $doctor->getId());
            if (!empty($logo)) {
                $clinic->setBusinessLogoUrl($logo);
            }
        }
        $clinicAdd = $clinic->getBusinessAddress();

        $areas = ['overseas', 'local'];
        $inputs = [];
        foreach ($areas as $areaType) {
            foreach (Constant::GST_SETTING_TYPE as $type) {
                $entityId      = null;
                $effectDate    = null;
                $oldGstId      = null;
                $oldGstIdNew   = null;
                $effectiveDate = null;

                $gstType = $em->getRepository('UtilBundle:DoctorGstSetting')->findOneBy([
                    'doctor'  => $doctor,
                    'feeType' => $type,
                    'area'    => $areaType,
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
                            $date     = 'reviewLocalDate';
                            $code     = 'gstCodeReviewLocal';
                        }else{
                            $applyGst = 'applyGstReviewInternational';
                            $date     = 'reviewInternationalDate';
                            $code     = 'gstCodeReviewInternational';
                        }
                        break;
                    case Constant::SETTING_GST_CONSULT:
                        if($areaType == 'local'){
                            $applyGst = 'applyGstConsultLocal';
                            $date     = 'consultLocalDate';
                            $code     = 'gstCodeConsultLocal';
                        }else{
                            $applyGst = 'applyGstConsultInternational';
                            $date     = 'consultInternationalDate';
                            $code     = 'gstCodeConsultInternational';
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
                $gstType->setIsHasGst(true);
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

                $newPrice = $gstCode ? $gstCode->getId() : null;
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
                        'effectedOn' => $effectDate,
                        'em'         => $em,
                    ];
                }

            }
        }

       //$this->saveLogPrice($inputs);

        $add = $clinicAdd->getAddress();
        $add->setCity($em->getRepository('UtilBundle:City')->find($data['mainClinicCity']));
        $add->setPostalCode($data['mainClinicZipCode']);
        $add->setLine1($data['mainClinicAddressLine1']);
        $add->setLine2($data['mainClinicAddressLine2']);
        $add->setLine3($data['mainClinicAddressLine3']);
        $doctor->setIsGst($data['gstSetting']);
        if ($data['gstSetting']) {
            $doctor->setGstNo($data['mainClinicGstNo']);
            $doctor->setGstEffectDate(new \DateTime(date('Y-m-d', strtotime($data['mainClinicGstDate']))));
            if (isset($data['mainClinicGstDate'])) {
                $doctor->setGstEffectDate(new \DateTime(date('Y-m-d', strtotime($data['mainClinicGstDate']))));
            }
        } else {
            $doctor->setGstNo('');
            $doctor->setGstEffectDate(null);
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

        $phone2   = $clinicAdd->getBusinessPhone();
        $country2 = $em->getRepository('UtilBundle:Country')->find($data['mainClinicTelephoneLocation']);
        $phone2->setCountry($country2);
//        $phone2->setAreaCode($data['mainClinicAreacode']);
        $phone2->setNumber($data['mainClinicPhone']);

        $doctor->setIsCustomizeMedicineEnabled(isset($data['isCustomizeMedicineEnabled']));

        $this->updateClinic($doctor, $clinicdata);
        $doctor->setUpdatedon(new \DateTime());
        $em->persist($doctor);
        $em->flush();

        // logging
        $newData = $this->getDoctorValue($doctor);
        $author  = $this->getUser()->getLoggedUser()->getFirstName() . ' ' .
        $this->getUser()->getLoggedUser()->getLastName();
        $arr = array('module' => 'doctors',
                     'title'  =>'doctor_updated',
                     'id'     => $doctor->getId());
        //$this->saveLog($oldData, $newData, $author, $arr);

        // insert log price
        if($doctor) {
            //$this->logDoctorPrescribingFee($doctor, $arrFileds);
        }
        return new JsonResponse(array('success' => 1, 'message' => 'update success'));
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
        $em = $this->getDoctrine()->getManager();
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
     * @Route("/admin/doctor-create-submit", name="admin_doctor_create_ajax")
     */
    public function doctorSubmitAction(Request $request) {

        $em = $this->getDoctrine()->getEntityManager();
        $data = $request->request->get('admin_doctor');
        $data = Common::removeSpaceOf($data);

        $clinicdata = $request->request->get('clinics');
        $clinicdata = Common::removeSpaceOf($clinicdata);

        $doctor = new Doctor();
        $doctor->setCreatedon(new \DateTime());
        $doctor->setUpdatedon(new \DateTime());
        //doctor personal infomation
        $personalInfo = new PersonalInformation();
        $personalInfo->setTitle($data['title']);
        $personalInfo->setFirstName($data['firstName']);
        $personalInfo->setLastName($data['lastName']);
        $personalInfo->setGender($data['gender']);
        $personalInfo->setEmailAddress($data['email']);
        $personalInfo->setPassportNo($data['localIdPassport']);
        $doctor->setDisplayName($data['displayName']);


        // doctor phone
        $phone = new Phone();
        $country = $em->getRepository('UtilBundle:Country')->find($data['phoneLocation']);
        $phone->setCountry($country);
        $phone->setPhoneType($em->getRepository('UtilBundle:PhoneType')->getPhone());
        $phone->setNumber($data['phone']);
        $doctorPhone = new DoctorPhone();
        $doctorPhone->setContact($phone);
        //doctor medical License
        $mlicense = new MedicalLicense();
        $mlicense->setRegistrationNumber($data['localMedicalLicence']);
        $mlicense->setIssuingCountryId($data['localMedicalCountry']);
        $mlicense->setMedicalRegistrationType($em->getRepository('UtilBundle:MedicalRegistrationType')->find(1));
        $date = new \DateTime(date('Y-m-d', strtotime('01-01-'.$data['localMedicalDate'])));
        $mlicense->setIssuingDate($date);

        //doctor identification
        $iden = new Identification();
        $iden->setIdentityNumber($data['localIdPassport']);
        $iden->setIssuingCountryId($data['localIdPassportCountry']);

        // if ($data['localIdPassportDate']) {
        //     $iden->setIssueDate(date('Y-m-d', strtotime($data['localIdPassportDate'])));
        // }
        //doctor specialization
        $spec = $data['specialization'];
        foreach ($spec as $val) {
            $obj = $em->getRepository('UtilBundle:MedicalSpecialty')->find($val);
            $doctor->addMedicalSpecialty($obj);
        }

        // if bank country is malaysia or singapore then use bank from the database else create new record for bank
        if ($data['bankCountryIssue'] == Constant::ID_SINGAPORE || $data['bankCountryIssue'] == Constant::ID_MALAYSIA) {
            $bank = $em->getRepository('UtilBundle:Bank')->find($data['bankName']);
        } else {
            //doctor bank
            $bank = new Bank();
            $bank->setName($data['bankName']);
            $bank->setCountry($em->getRepository('UtilBundle:Country')->find($data['bankCountryIssue']));
            // $bank->setCity($em->getRepository('UtilBundle:City')->find($data['bankCityIssue']));
            $bank->setSwiftCode($data['bankSwiftCode']);
        }

        $bankAcc = new BankAccount();
        $bankAcc->setBank($bank);
        $bankAcc->setAccountName($data['accountName']);
        $bankAcc->setAccountNumber($data['accountNumber']);
        //payment gateway
        $doctor->setPaymentGate($data['paymentGate']);

        $sequen = new SequenceNumber();
        $sequen->setTaxInvoice(1);
        $sequen->setReceipt(1);

        //save primary clinic
        $clinic = new Clinic();
        $clinic->setIsPrimary(true);
        $clinic->setBusinessName($data['mainClinicName']);
        $clinic->setEmail($data['mainClinicEmail']);
          //primary logo
        $logo = $this->uploadfile($_FILES["clini-logo-1"], 'clinic/logo-primary-' . $doctor->getId() . time());
        if (!empty($logo)) {
            $clinic->setBusinessLogoUrl($logo);
        }
        $clinicAdd = new ClinicAddress();
        $add = new Address();
        $add->setCity($em->getRepository('UtilBundle:City')->find($data['mainClinicCity']));
        $add->setPostalCode($data['mainClinicZipCode']);
        $add->setLine1($data['mainClinicAddressLine1']);
        $add->setLine2($data['mainClinicAddressLine2']);
        $add->setLine3($data['mainClinicAddressLine3']);
        $clinicAdd->setAddress($add);

        $areas = ['overseas', 'local'];
        foreach ($areas as $areaType) {
            foreach (Constant::GST_SETTING_TYPE as $type) {
                $gstType = $em->getRepository('UtilBundle:DoctorGstSetting')->findOneBy([
                    'doctor'    => $doctor,
                    'feeType'    => $type,
                    'area'      => $areaType,
                ]);

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

                if($gstType->getGst() == null || $data[$code] == $gstType->getGst() || $effect) {
                    $gstType->setGst($gstCode);
                }else{
                    $gstType->setNewGst($gstCode);
                }

                if( ($applyGst != '' && isset($data[$applyGst])) || ($type == Constant::SETTING_GST_MEDICINE)){
                    $gstType->setIsHasGst(true);

                } else {
                    $gstType->setIsHasGst(false);
                }
                $doctor->addGstSetting($gstType);
            }
        }

        $doctor->setIsGst($data['gstSetting']);
        if ($data['gstSetting']) {
            $doctor->setGstNo($data['mainClinicGstNo']);
            $doctor->setGstEffectDate(new \DateTime(date('Y-m-d', strtotime($data['mainClinicGstDate']))));
            if(isset($data['mainClinicGstDate'])) {
                $doctor->setGstEffectDate( new \DateTime(date('Y-m-d', strtotime($data['mainClinicGstDate']))) );
            }
        } else {
            $doctor->setGstNo('');
            $doctor->setGstEffectDate(null);
        }
        $phone2 = new Phone();
        $country2 = $em->getRepository('UtilBundle:Country')->find($data['mainClinicTelephoneLocation']);
        $phone2->setCountry($country2);
        $phone2->setPhoneType($em->getRepository('UtilBundle:PhoneType')->getPhone()); 
        $phone2->setNumber($data['mainClinicPhone']);
        $clinicAdd->setBusinessPhone($phone2);
        $clinic->setBusinessAddress($clinicAdd);
        $doctor->addClinic($clinic);

        $doctor->setIsApply3rdAgent($data['check3rd']);
        $agentDoctor = new AgentDoctor();
        $agentDoctor->setAgent($em->getRepository('UtilBundle:Agent')->find($data['agentId']));
        $agentDoctor->setIsActive(1);
        $agentDoctor->setIsPrimary(true);
        $doctor->addAgentDoctor($agentDoctor);

        if(isset($data['secondaryAgentId'])){
            $agentDoctor = new AgentDoctor();
            $agentDoctor->setAgent($em->getRepository('UtilBundle:Agent')->find($data['secondaryAgentId']));
            $agentDoctor->setIsActive(1);
            $agentDoctor->setIsPrimary(false);
            $doctor->addAgentDoctor($agentDoctor);
        }

        $doctor->setIsCustomizeMedicineEnabled(isset($data['isCustomizeMedicineEnabled']));

        $doctor->setSequenceNumbers($sequen);

        $doctor->setBankAccount($bankAcc);
        $doctor->addIdentification($iden);
        $doctor->addDoctorPhone($doctorPhone);
        $doctor->setPersonalInformation($personalInfo);
        $doctor->setMedicalLicense($mlicense);
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

        $platforms = $em->getRepository('UtilBundle:PlatformSettings')->findAll();
        $currentPlat = $platforms[0];
        $code = $this->generateDoctorCode($currentPlat->getOperationsCountryId());
        $doctor->setDoctorCode($code);
        $doctor->setIsActive(1);
        $em->persist($doctor);
        $em->flush();
        if (is_array($clinicdata)) {
            $this->addClinic($doctor, $clinicdata);
        }

      
        $em->persist($doctor);
        $em->flush();
        $this->senDoctorEmail($doctor);

        // logging
        $arr = array('module' => 'doctors',
                     'title'  =>'new_doctor_created',
                     'action' => 'create',
                     'id'     => $doctor->getId());
        $loggerUser = $this->getUser()->getLoggedUser();
        $author = $loggerUser->getFirstName() . ' ' . $loggerUser->getLastName();
        Utils::saveLog(array(), array(), $author, $arr, $em);

        return new JsonResponse(array('success' => 1, 'message' => 'insert success'));
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
            //$cityRepo = $em->getRepository('UtilBundle:City');
            $currentspec = array();
            $spec = $doctor->getMedicalSpecialty();
            foreach ($spec as $item) {
                array_push($currentspec, $item->getName());
            }
            $result['id'] = $doctor->getId();
            $result['medicalSpecialty'] = implode(', ', $currentspec);
            $result['email'] = $doctor->getPersonalInformation()->getEmailAddress();
            $result['displayName'] = $doctor->getDisplayName();

            $listRxReviewFeeLocal = Constant::LIST_RX_REVIEW_FEE_LOCAL;
            $listRxReviewFeeInternational = Constant::LIST_RX_REVIEW_FEE_INTERNATIONAL;
            $listRxFeeLiveConsultLocal = Constant::LIST_RX_FEE_LIVE_CONSULT_LOCAL;
            $listRxFeeLiveConsultInternational = Constant::LIST_RX_FEE_LIVE_CONSULT_INTERNATIONAL;

            $phoneObj = $doctor->getDoctorPhones()->first()->getContact();
            $doctorPhone = array();
            $doctorPhone[] = $phoneObj->getCountry()->getName();
            $doctorPhone[] = $phoneObj->getCountry()->getPhoneCode();
            $doctorPhone[] = $phoneObj->getAreaCode();
            $doctorPhone[] = $phoneObj->getNumber();
            $result['doctorPhone'] = implode(', ', $doctorPhone);

            $medicalLicense = $doctor->getMedicalLicense();
            $result['medicalNumber'] = $medicalLicense->getRegistrationNumber();
            $result['medicalIssueDate'] = $medicalLicense->getIssuingDate()->format('Y');
            $result['medicalCountry'] = $medicalLicense->getIssuingCountryId();
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
                /*$bankCity = $bank->getCity();
                $result['bankState'] = $bankCity->getState() ? $bankCity->getState()->getName() : "";
                $result['bankCity'] = $bank->getCity()->getName();*/
                $result['bankcode'] = $bank->getSwiftCode();
            }
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
                $clinicPhone = array();
                $clinicPhone[] = $mainPhone->getCountry()->getId();
                $clinicPhone[] = $mainPhone->getAreaCode();
                $clinicPhone[] = $mainPhone->getNumber();
            }
            $result['signature'] = $doctor->getSignatureUrl();
        }
        return $result;
    }

    private function generateDoctorCode($id) {

        $em = $this->getDoctrine()->getEntityManager();
        $country = $em->getRepository('UtilBundle:Country')->find($id);
        if ($country) {
            $code = $country->getCodeAthree();
            return $em->getRepository('UtilBundle:Doctor')->generateCode($code);
        }
        return '';
    }

    private function addClinic($doctor, $dataPost) {
        $em = $this->getDoctrine()->getEntityManager();
        foreach ($dataPost as $key => $data) {
            $clinic = new Clinic();
            $logo = $this->uploadfile($_FILES["clini-logo-" . $key], 'clinic/logo-' . $key . '-' . $doctor->getId());
            if (!empty($logo)) {
                $clinic->setBusinessLogoUrl($logo);
            }

            $clinic->setIsPrimary(FALSE);
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

            $doctor->addClinic($clinic);
        }
    }

    private function updateClinic($doctor, $dataPost) {

        $em = $this->getDoctrine()->getEntityManager();
        $clinics = $doctor->getClinics();
        $listClinics = array();
        foreach ($clinics as $cl) {
            if (!$cl->getIsPrimary()) {
                $cl->setDeletedOn(new \DateTime(date('Y-m-d')));
                $listClinics[$cl->getId()] = $cl;
            }
        }

        foreach ($dataPost as $key => $data) {
            if (isset($data['id'])) {
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
                $phone2 = $clinicAdd->getBusinessPhone();
                $country2 = $em->getRepository('UtilBundle:Country')->find($data['phoneLocation']);
                $phone2->setCountry($country2);
                $phone2->setPhoneType($em->getRepository('UtilBundle:PhoneType')->getPhone());
                $phone2->setAreaCode($data['phoneArea']);
                $phone2->setNumber($data['phoneNumber']);
                if (isset($_FILES["clini-logo-" . $key])) {
                    $logo = $this->uploadfile($_FILES["clini-logo-" . $key], 'clinic/logo-' . $key . '-' . $doctor->getId());
                    if (!empty($logo)) {
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
                if (isset($_FILES["clini-logo-" . $key])) {
                    $logo = $this->uploadfile($_FILES["clini-logo-" . $key], 'clinic/logo-' . $key . '-' . $doctor->getId());
                    if (!empty($logo)) {
                        $clinic->setBusinessLogoUrl($logo);
                    }
                }
                $doctor->addClinic($clinic);
            }
        }
    }

    /**
     * @Route("/admin/doctor-create-get-dependent", name="admin_doctor_create_getdependent")
     */
    public function getDependentData(Request $request) {

        $em = $this->getDoctrine()->getEntityManager();
        $result = array();
        if ($request->request->get('type') == 1) {
            $result = $em->getRepository('UtilBundle:City')->getStateByCountry($request->request->get('data'));
        }
        if ($request->request->get('type') == 2) {
            $result = $em->getRepository('UtilBundle:State')->getCityByState($request->request->get('data'));
        }
        if ($request->request->get('type') == 3) {
            $result = $em->getRepository('UtilBundle:Country')->getByPreferredCountry(true);
        }
        if ($request->request->get('type') == 4) {
            $countryList= [];
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
            $clinics = $request->request->get("clinics",[]);
            $countries = $em->getRepository('UtilBundle:Country')->getByPreferredCountry(true);
            $country = array('0' => '', 'empty' => '-');



            $agents = $em->getRepository('UtilBundle:Agent')->findAll();
            $agent = array('0' => '', 'empty' => '-');
            foreach ($agents as $obj) {
                $agent[$obj->getId()] = $obj->getPersonalInformation()->getFullName();
            }
            $phoneCode = array('0' => '', 'empty' => '-');
            foreach ($countries as $c) {
                $country[$c['id']] = $c['name'];
                $phoneCode[$c['id']] = $c['phoneCode'];
            }
            $special = array('0' => '', 'empty' => '-');
            $specials = $em->getRepository('UtilBundle:MedicalSpecialty')->findAll();

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
            $paymentGate = [Constant::PAYMENT_GATE_MOLPAY =>'MOL Pay', Constant::PAYMENT_GATE_IPAY =>'iPay88', Constant::PAYMENT_GATE_REDDOT => 'Reddot'];
            $city = $em->getRepository('UtilBundle:City')->getListCity($countryList);
            $state = $em->getRepository('UtilBundle:State')->getListState($countryList);
            return $this->render('AdminBundle:admin:doctor-create-3.html.twig', array(
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
                        'paymentGate' => $paymentGate
            ));
        }
        if ($request->request->get('type') == 5) {

            $parameters = $request->request->get('data');
            $data = $em->getRepository('UtilBundle:Doctor')->getClinicData($parameters);
            $result = array('status' => 1, 'total' => count($data), 'data' => $data);
        }
        if ($request->request->get('type') == 6) {
            $result = $em->getRepository('UtilBundle:Country')->getCityByCountry($request->request->get('data'));
        }
        // get bank list by country
        if ($request->request->get('type') == 7) {
            $result = $em->getRepository('UtilBundle:Bank')->getBankByCountry($request->request->get('data'));
        }
        // get bank swift code
        if ($request->request->get('type') == 8) {
            $bank = $em->getRepository('UtilBundle:Bank')->find($request->request->get('data'));
            if ($bank) {
                $result = array('status' => true, 'swift_code' => $bank->getSwiftCode());
            }
        }
        return new JsonResponse($result);
    }


    /**
     * @Route("/admin/messages", name="admin_messages")
     */
    public function messagesAction(Request $request) {

        $parameters = array(
            'ajaxURL' => 'admin_ajax_pharmacy'
        );
        return $this->render('AdminBundle:admin:messages.html.twig', $parameters);
    }

    /**
     * @Route("/admin/delivery-partner", name="admin_delivery_partner")
     */
    public function deliveryPartnerAction(Request $request) {

        $parameters = array(
            'ajaxURL' => 'admin_courier_list_ajax'
        );

        return $this->render('AdminBundle:admin:delivery.html.twig', $parameters);
    }

    /**
     * @Route("/admin/courier-ajax", name="admin_courier_list_ajax")
     */
    public function deliveryPartnerAjaxAction(Request $request) {
        $em = $this->getDoctrine()->getEntityManager();
        $result = $em->getRepository('UtilBundle:Courier')->getCouriersAdmin($request->request);

        return new JsonResponse($result);
    }

    /**
     * @Route("/admin/delivery-partner/create", name="admin_delivery_partner_create")
     */
    public function deliveryPartnerCreateAction(Request $request) {

        $em = $this->getDoctrine()->getEntityManager();
        $country = $em->getRepository('UtilBundle:Country')->getByPreferredCountry(true);
        $dependentData = array();
        $dependentData['country'] = $country;
        $dependentData['cityRepo'] = $em->getRepository('UtilBundle:City');
        $delivery = new Courier();
        $form = $this->createForm(new DeliveryAdminType(array('delivery' => $delivery, 'depend' => $dependentData)), array(), array());
        $eror = array();

        if ($request->getMethod() == 'POST') {
            $data = $request->request->get('admin_agent');
            $data = Common::removeSpaceOf($data);
            $personalInfo = new PersonalInformation();
            $personalInfo->setEmailAddress($data['email']);
            $delivery->setPersonalInformation($personalInfo);

            $add = new Address();
            $add->setCity($em->getRepository('UtilBundle:City')->find($data['city']));

            $add->setLine1($data['addressLine1']);
            $add->setLine2($data['addressLine2']);
            $add->setLine3($data['addressLine3']);
            $delivery->addAddress($add);

            $phone = new Phone();
            $country = $em->getRepository('UtilBundle:Country')->find($data['phoneLocation']);
            $phone->setCountry($country);
            $phone->setPhoneType($em->getRepository('UtilBundle:PhoneType')->getPhone());
            $phone->setAreaCode($data['phoneArea']);
            $phone->setNumber($data['phone']);
            $delivery->setPhone($phone);

            $delivery->setBusinessRegistrationNumber($data['businessName']);
            $delivery->setIsGst($data['gstSetting']);
            if ($data['gstSetting']) {
                $delivery->setGstNo($data['gstNum']);
            }
            $delivery->setMargin(0);
            $delivery->setName($data['deliveryName']);

            $bankAcc = new BankAccount();
            if ($data['bankCountry'] == Constant::ID_SINGAPORE || $data['bankCountry'] == Constant::ID_MALAYSIA) {
                $bank = $em->getRepository('UtilBundle:Bank')->find($data['bankName']);
            } else {
                $bank = new Bank();
                $bank->setName($data['bankName']);
                $bank->setCountry($em->getRepository('UtilBundle:Country')->find($data['bankCountry']));
                $bank->setSwiftCode($data['bankSwiftCode']);
            }

            $bankAcc->setAccountName($data['accountName']);
            $bankAcc->setAccountNumber($data['accountNumber']);
            $bankAcc->setBank($bank);
            $delivery->setBankAccount($bankAcc);
            $em->persist($delivery);
            $em->flush();

            $arr = array('module' => Constant::DELIVERY_PARTNER_MODULE_NAME,
                         'title'  =>'new_delivery_partner_created',
                         'action' => 'create',
                         'id'     => $delivery->getId());
            $loggerUser = $this->getUser()->getLoggedUser();
            $author = $loggerUser->getFirstName() . ' ' . $loggerUser->getLastName();
            Utils::saveLog(array(), array(), $author, $arr, $em);

            return $this->redirectToRoute('admin_delivery_partner');
        }

        $parameters = array(
            'form'          => $form->createView(),
            'ajaxURL'       => 'admin_doctor_create_ajax',
            'ajaxDependent' => 'admin_doctor_create_getdependent',
            'successUrl'    => 'admin_agent',
            'delivery'      => '',
            'deliveryId'    => '',
            'title'         => 'Add a Delivery Partner',
            'action'        => 'create'
        );
        return $this->render('AdminBundle:admin:delivery-create.html.twig', $parameters);
    }

    /**
     * @Route("/admin/delivery-partner/{id}/edit", name="admin_delivery_partner_edit")
     */
    public function deliveryPartnerEditAction(Request $request, $id) {

        $em = $this->getDoctrine()->getEntityManager();
        $cityRepo = $em->getRepository('UtilBundle:City');
        $country = $em->getRepository('UtilBundle:Country')->getByPreferredCountry(true);
        $dependentData = array();
        $dependentData['country'] = $country;
        $dependentData['cityRepo'] = $cityRepo;
        $delivery = $em->getRepository('UtilBundle:Courier')->find($id);
        //$delivery = new Agent();
        $form = $this->createForm(new DeliveryAdminType(array('delivery' => $delivery, 'depend' => $dependentData)), array(), array());
        $eror = array();

        if ($request->getMethod() == 'POST') {
            $oldValue = $this->getDeliveryValue($delivery, $cityRepo);

            $data = $request->request->get('admin_agent');
            $data = Common::removeSpaceOf($data);
            $personalInfo = $delivery->getPersonalInformation();
            $personalInfo->setEmailAddress($data['email']);

            $add = $delivery->getAddresses()->first();
            $add->setCity($em->getRepository('UtilBundle:City')->find($data['city']));

            $add->setLine1($data['addressLine1']);
            $add->setLine2($data['addressLine2']);
            $add->setLine3($data['addressLine3']);

            $phone = $delivery->getPhone();
            $country = $em->getRepository('UtilBundle:Country')->find($data['phoneLocation']);
            $phone->setCountry($country);
            $phone->setPhoneType($em->getRepository('UtilBundle:PhoneType')->getPhone());
            $phone->setAreaCode($data['phoneArea']);
            $phone->setNumber($data['phone']);

            $delivery->setBusinessRegistrationNumber($data['businessName']);

            $delivery->setIsGst($data['gstSetting'] == '1' ? true : false);
            if ($data['gstSetting']) {
                $delivery->setGstNo($data['gstNum']);
            } else {
                $delivery->setGstNo(NULL);
            }
            $delivery->setMargin(0);
            $delivery->setName($data['deliveryName']);

            $bankAcc = $delivery->getBankAccount();
            if (!$bankAcc) {
                $bankAcc = new BankAccount();
            }
            if ($data['bankCountry'] == Constant::ID_SINGAPORE || $data['bankCountry'] == Constant::ID_MALAYSIA) {
                $bank = $em->getRepository('UtilBundle:Bank')->find($data['bankName']);
            } else {
                $bank = $bankAcc->getBank();
                if (!$bank) {
                    $bank = new Bank();
                }
                $bank->setName($data['bankName']);
                $bank->setSwiftCode($data['bankSwiftCode']);
                $bankCountry = $data['bankCountry'];
                if (empty($bankCountry)) {
                    $bankCountry = $data['country'];
                }
                if (empty($bankCountry)) {
                    $bankCountry = $country;
                }
                $bank->setCountry($em->getRepository('UtilBundle:Country')->find($bankCountry));

            }

            $bankAcc->setAccountName($data['accountName']);
            $bankAcc->setAccountNumber($data['accountNumber']);
            $bankAcc->setBank($bank);
            $delivery->setBankAccount($bankAcc);

            $em->persist($delivery);
            $em->flush();
            $newValue = $this->getDeliveryValue($delivery, $cityRepo);
            //insert logs
            $logParams = array(
                'id' => $id,
                'title' => 'delivery partner',
                'module' => 'delivery_partner',
            );

            $loggerUser = $this->getUser()->getLoggedUser();
            $author = $loggerUser->getFirstName() . ' ' . $loggerUser->getLastName();
            Utils::saveLog($oldValue, $newValue, $author, $logParams, $em);

            return $this->redirectToRoute('admin_delivery_partner');
        }

        $parameters = array(
            'form'          => $form->createView(),
            'ajaxURL'       => 'admin_doctor_create_ajax',
            'ajaxDependent' => 'admin_doctor_create_getdependent',
            'successUrl'    => 'admin_agent',
            'delivery'      => '',
            'deliveryId'    => $id,
            'title'         => 'Edit a Delivery Partner',
            'action'        => 'edit'
        );
        return $this->render('AdminBundle:admin:delivery-create.html.twig', $parameters);
    }

    /**
    * get value of delivery
    */
    private function getDeliveryValue($delivery, $cityRepo)
    {
        $result = array();
        if (is_object($delivery) && $delivery->getId()) {
            $phoneObj = $delivery->getPhone();
            $address = $delivery->getAddresses()->first();

            $cityObj = $address->getCity();
            $countryDelivery = $cityObj->getCountry()->getId();

            $result['id']            = $delivery->getId();
            $result['gstNum']        = $delivery->getGstNo();
            $result['gst']           = $delivery->getIsGst();
            $result['deliveryName']  = $delivery->getName();
            $result['businessName']  = $delivery->getBusinessRegistrationNumber();
            $result['email']         = $delivery->getPersonalInformation()->getEmailAddress();
            $result['phoneLocation'] = $phoneObj->getCountry()->getId();
            $result['phoneCode'] = $phoneObj->getCountry()->getPhoneCode();
            $result['phoneArea'] = $phoneObj->getAreaCode();
            $result['phoneNumber'] = "+".$phoneObj->getCountry()->getPhoneCode()." ".$phoneObj->getAreaCode()." ".$phoneObj->getNumber();
            $result['line1'] = $address->getLine1();
            $result['line2'] = $address->getLine2();
            $result['line3'] = $address->getLine3();
            $result['city'] = $cityObj->getName();
            $result['state'] = $cityObj->getState()? $cityObj->getState()->getName(): "";
            $result['country'] = $cityObj->getCountry()->getName();
        }
        return $result;
    }

    /**
     * @Route("/admin/delivery-partner/log/{act}", name="admin_delivery_partner_log")
     */
    public function deliveryPartnerLogsAction(Request $request, $act=null)
    {
        $params['module'] = 'delivery_partner';
        $logs = $this->getDoctrine()->getManager()->getRepository('UtilBundle:Log')->getLogs($params);
        // Decode json
        for ($i = 0; $i < count($logs); $i++) {
            $logs[$i]['oldValue'] = json_decode($logs[$i]['oldValue'], true);
            $logs[$i]['newValue'] = json_decode($logs[$i]['newValue'], true);
        }
        $template = 'AdminBundle:admin/logs:delivery_partner_log.html.twig';
        if($act == 'print') {
            $html = $this->renderView($template, array("logs" => $logs, "act" => $act));
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $response = new Response();
            $response->setContent($dompdf->output());
            $response->setStatusCode(200);
            $response->headers->set('Content-Disposition', 'attachment; filename='. "delivery-partner-logs.pdf");
            $response->headers->set('Content-Type', 'application/pdf');

            return $response;
        }

        return $this->render($template, array("logs" => $logs, "act" => $act));
    }

    /**
     * @Route("/admin/delivery-auto-complete", name="admin_delivery_autocomplete")
     */
    public function deliveryAutocompleteAction(Request $request) {
        $data = array();
        $data['text'] = $request->request->get('term');
        $em = $this->getDoctrine()->getEntityManager();
        $result = $em->getRepository('UtilBundle:Courier')->selectCouriersAutoComplete($data);

        return new JsonResponse($result);
    }

    /**
     * @Route("/admin/delivery-update", name="admin_delivery_update")
     */
    public function updateDeliveryAction(Request $request) {

        $type       = $request->request->get('type');
        $id         = $request->request->get('id');
        $date       = $request->request->get('date');
        $value      = $request->request->get('value');
        $em         = $this->getDoctrine()->getEntityManager();
        $result     = array('success' => false);
        $authorId    = $this->getUser()->getLoggedUser()->getId();
        $table       = 'courier_rate';
        $field       = null;
        $oldValue    = null;
        $oldValueNew = null;

        switch ($type) {
            case 1:
                $obj         = $em->getRepository('UtilBundle:CourierRate')->find($id);
                $oldValue    = $obj->getCost();
                $oldValueNew = $obj->getNewCost();
                $field       = 'cost';
                $obj->setCostEffectDate(new \DateTime(date('Y-m-d', strtotime($date))));
                $obj->setNewCost($value);
                $em->flush($obj);
                $result = array('success' => true);
                break;
            case 2:
                $obj         = $em->getRepository('UtilBundle:CourierRate')->find($id);
                $oldValue    = $obj->getList();
                $oldValueNew = $obj->getNewList();
                $field       = 'list';
                $obj->setListEffectDate(new \DateTime(date('Y-m-d', strtotime($date))));
                $obj->setNewList($value);
                $em->flush($obj);
                $result = array('success' => true);
                break;
            case 3:
                $obj         = $em->getRepository('UtilBundle:CourierRate')->find($id);
                $oldValue    = $obj->getIgPermitFee();
                $oldValueNew = $obj->getNewIgPermitFee();
                $field       = 'ig_permit_fee';
                $obj->setIgPermitListEffectDate(new \DateTime(date('Y-m-d', strtotime($date))));
                $obj->setNewIgPermitFee($value);
                $em->flush($obj);
                $result = array('success' => true);
                break;
            case 4:
                $obj         = $em->getRepository('UtilBundle:Courier')->find($id);
                $oldValue    = $obj->getMargin();
                $oldValueNew = $obj->getNewMargin();
                $table       = 'courier';
                $field       = 'margin';
                $obj->setMarginEffectDate(new \DateTime(date('Y-m-d', strtotime($date))));
                $obj->setNewMargin($value);
                $em->flush($obj);
                $result = array('success' => true);
                break;
            case 5:
                $obj = $em->getRepository('UtilBundle:Courier')->find($id);
                $obj->setDeletedOn(new \DateTime(date('Y-m-d')));
                $em->flush($obj);
                $result = array('success' => true);
                break;
            case 6:
                $obj = $em->getRepository('UtilBundle:CourierRate')->find($id);
                $obj->setEstimatedDeliveryTimeline($value);
                $em->flush($obj);
                $result = array('success' => true);
                break;
            case 7:
                $obj         = $em->getRepository('UtilBundle:CourierRate')->find($id);
                $oldValue    = $obj->getCollectionRate();
                $oldValueNew = $obj->getNewCollectionRate();
                $field       = 'collection_rate';
                $obj->setNewCollectionRate($value);
                $obj->setCollectionRateEffectDate(new \DateTime(date('Y-m-d', strtotime($date))));
                $em->flush($obj);
                $result = array('success' => true);
                break;
        }

        //insert logs price
        if($table && $field && $oldValueNew != $value) {
            $inputs[] = [
                'tableName'  => $table,
                'fieldName'  => $field,
                'entityId'   => $id,
                'oldPrice'   => $oldValue,
                'newPrice'   => $value,
                'createdBy'  => $authorId,
                'em'         => $em,
                'effectedOn' => $date ? new \DateTime(date('Y-m-d', strtotime($date))) : null
            ];
            Utils::saveLogPrice($inputs);
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/admin/resend-doctor-welcome-email", name="admin_resend_doctor_welcome_email")
     */
    public function resendDoctorWelcomeMail(Request $request){
        $em = $this->getDoctrine()->getEntityManager();
        $id = $request->get('id');
        $doctor = $em->getRepository('UtilBundle:Doctor')->find($id);
        if($doctor->getIsConfirmed() < Constant::STATUS_CONFIRM){
            $this->senDoctorEmail($doctor);
            return new JsonResponse(true);
        }else{
            return new JsonResponse(false);
        }
    }

    private function senDoctorEmail($doctor = null) {

        $base = $this->container->get('request')->getSchemeAndHttpHost();
        $host = 'http://'; 
        if (strpos($base, 'https://') !== false) {
            $host = 'https://'; 
        }
        $agentDoctor = $doctor->getAgentDoctors()->last();
        if ($agentDoctor) {
            $agent = $agentDoctor->getAgent();
            $url_agent = '';
            if ($agent) {
                if ($agent->getParent()) {
                    $url_agent = $host.$agent->getParent()->getSite()->getUrl();
                } else {
                    if (count($agent->getChild()) == 0) {
                        $url_agent = $host.$agent->getSite()->getUrl();
                    }
                }
            }
            if ($url_agent != '') {
                $base = $url_agent;
            }
        }
        $emailTo = $doctor->getPersonalInformation()->getEmailAddress();

        $mailTemplate = 'AdminBundle:admin:email/register-doctor.html.twig';
        $mailParams = array(
            'logoUrl' => $base . '/bundles/admin/assets/pages/img/logo.png',
            'name' => $doctor->showName(),
            'id' => $doctor->getId(),
            'base' => $base
        );
        $dataSendMail = array(
            'title' => "Welcome to G-MEDS",
            'body' => $this->container->get('templating')->render($mailTemplate, $mailParams),
            'to' => $emailTo,
        );
        $this->container->get('microservices.sendgrid.email')->sendEmail($dataSendMail);
    }

    /**
     * @Route("/admin/validate-email", name="admin_validate_email")
     */
    public function validateEmailAction(Request $request)
	{
        $value = $request->request->get('data');
        $curId = $request->request->get('id');
        $type = $request->request->get('type');
        $em = $this->getDoctrine()->getEntityManager();
        $result = array('success' => false);

        $pi = $em->getRepository('UtilBundle:PersonalInformation')->findByEmailAddress($value);
		$user = $em->getRepository('UtilBundle:User')->findByEmailAddress($value);
        $found = $pi || $user ? true : false;
        if(empty($curId)) {
            $result = array('success' => !$found, 'total' => count($pi) + count($user));
            return new JsonResponse($result);
        } else {
            $id = '';
            if(count($pi) > 1){
                 $result = array('success' => false, 'total' => count($pi));
                 return new JsonResponse($result);
            }
            switch ($type) {
                case 1:
                    $doctor = $em->getRepository('UtilBundle:Doctor')->find($curId);
                    $id = $doctor->getPersonalInformation()->getId();
                    break;
                case 2:
                    $agent = $em->getRepository('UtilBundle:Agent')->find($curId);
                    $id = $agent->getPersonalInformation()->getId();
                    break;
            }

            if(!empty($pi) && $pi[0]->getId() == $id ) {
                $result = array('success' => true, 'total' => count($pi));
                return new JsonResponse($result);
            }
            
            $result = array('success' => !$found, 'total' => count($pi) + count($user));

        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/admin/admin-fee", name="admin_custom_clearance_fee")
     */
    public function clearanceFeeAction(Request $request) {
        $em = $this->getDoctrine()->getEntityManager();
        $flagPlatformShareFee = $this->getParameter('platform_share_fee');



        $sin = $em->getRepository('UtilBundle:CustomClearanceAdminFee')->findOneByCountry(Constant::ID_SINGAPORE);
        $in = $em->getRepository('UtilBundle:CustomClearanceAdminFee')->findOneByCountry(Constant::ID_INDONESIA);

        $data['fee1'] = $sin->getFeeSetting()->getNewFee();
        $data['date1'] = ($sin->getFeeSetting()->getEffectDate()) ? $sin->getFeeSetting()->getEffectDate()->format(Constant::GENERAL_DATE_FORMAT) : '';
        $data['fee2'] = $in->getFeeSetting()->getNewFee();
        $data['date2'] = ($in->getFeeSetting()->getEffectDate()) ? $in->getFeeSetting()->getEffectDate()->format(Constant::GENERAL_DATE_FORMAT) : '';
        $form = $this->createForm(new CustomAdminFeeType(array('config'=>$data)), array(), array());

        if($flagPlatformShareFee == 1){
            //New Clearance Admin Fee
            $repo = $em->getRepository('UtilBundle:PlatformSharePercentages');

            $formParams = array(
                    'method' => "post",
                    'action' => $this->generateUrl('admin_custom_clearance_fee')
            );

            $formCustomCAF1Data          = $repo->getPSPercentageByType(Constant::AREA_TYPE_LOCAL, Constant::MST_CUSTOM_CAF);
            $formCustomCAF1Data['title'] = 'customs_clearance_admin_fee - local patients';
            $formCustomCAF1Data['flag']  = $flagPlatformShareFee;
            $formCustomCAF1              = $this->createForm(
                    new PlatformSharePercentageType(),
                    $formCustomCAF1Data,
                    $formParams);

            $formCustomCAF2Data          = $repo->getPSPercentageByType(Constant::AREA_TYPE_OVERSEA, Constant::MST_CUSTOM_CAF);
            $formCustomCAF2Data['title'] = 'customs_clearance_admin_fee - overseas_patients';
            $formCustomCAF2Data['flag']  = $flagPlatformShareFee;
            $formCustomCAF2              = $this->createForm(
                    new PlatformSharePercentageType(),
                    $formCustomCAF2Data,
                    $formParams);

            if($request->getMethod() === 'POST'){
                $ps       = $request->get('ps_percentage');
                $areaType = $ps['areaType'];
                $params   = array();

                if($areaType = Constant::MST_CUSTOM_CAF){
                    if($areaType == Constant::AREA_TYPE_LOCAL){
                        $formCustomCAF1->handleRequest($request);
                        if($formCustomCAF1->isValid())
                            $params = $formCustomCAF1->getData();
                    }else{
                        $formCustomCAF2->handleRequest($request);
                        if($formCustomCAF2->isValid())
                            $params = $formCustomCAF2->getData();
                    }
                }

                if(!empty($params)){
                    $oldValue = $repo->getPSPercentageById($params['id']);
                    $results  = $repo->update($params);
                    $newValue = $repo->getPSPercentageById($params['id']);
                }

                // insert logs
                $arr        = array('module' => 'agents', 'title' => 'status_changed');
                $loggerUser = $this->getUser()->getLoggedUser();
                $author     = $loggerUser->getFirstName() . ' ' . $loggerUser->getLastName();

                $params['module'] = Constant::CUSTOM_CLEARANCE_ADMIN_FEE_MODULE_NAME;
                Utils::saveLog($oldValue, $newValue, $author, $params, $em);
//
//                // insert log price
                $posts = $request->get('ps_percentage');
                $this->saveLogPriceMarginSharing(
                        $ps['marginShareType'],
                        $posts,
                        $oldValue,
                        $em
                );
                if(!empty($results))
                    $this->get('session')->getFlashBag()->add('success', MsgUtils::generate('msgUpdatedSuccess', 'Customs Clearance Admin Fee'));
                else
                    $this->get('session')->getFlashBag()->add('danger',MsgUtils::generate('msgCannotEdited', 'Customs Clearance Admin Fee'));

                return $this->redirectToRoute('admin_custom_clearance_fee');
            }

        }



        $parameters = array(
            'country1'=>$sin->getId(),
            'country2'=>$in->getId(),
            'form' => $form->createView(),
            'formCustomCAF1' => isset($formCustomCAF1)? $formCustomCAF1->createView(): null,
            'formCustomCAF2' => isset($formCustomCAF2)? $formCustomCAF2->createView(): null,
            'title' => 'Custom Clearance Admin Fee',
            'ajaxURL' => 'admin_fee_ajax',
            'fee1' => '',
            'fee2' => '',
            'isActiveLocal'    => !empty($formCustomCAF1Data)? $formCustomCAF1Data['isActive']: false,
            'isActiveOversea'  => !empty($formCustomCAF2Data)? $formCustomCAF2Data['isActive']: false,
            'flagPlatformShareFee'  => $flagPlatformShareFee
        );
        if(strtotime($sin->getFeeSetting()->getEffectDate()->format("Y-m-d")) >= strtotime(date("Y-m-d"))) {
            $parameters['fee1'] = $sin->getFeeSetting()->getFee();
        }
        if(strtotime($in->getFeeSetting()->getEffectDate()->format("Y-m-d")) >= strtotime(date("Y-m-d"))) {
            $parameters['fee2'] = $in->getFeeSetting()->getFee();
        }
        return $this->render('AdminBundle:admin:admin-fee.html.twig',$parameters);
    }

    /**
     * @Route("/payment-margin-gst/log/view", name="admin_payment_margin_gst_view_logs")
     */
    public function ajaxLogs(Request $request) {
        $params['module'] = $request->request->get('module');
        $logs = $this->getDoctrine()->getManager()->getRepository('UtilBundle:Log')->getLogs($params);

        // Decode json
        for ($i = 0; $i < count($logs); $i++) {
            $logs[$i]['oldValue'] = json_decode($logs[$i]['oldValue'], true);
            $logs[$i]['newValue'] = json_decode($logs[$i]['newValue'], true);
        }
        $template = '';
        switch ($params['module']) {
            case Constant::CUSTOM_CLEARANCE_ADMIN_FEE_MODULE_NAME:
                $template = 'AdminBundle:payment_setting\Logs:caf_log.html.twig';
                break;
            case Constant::PAYMENT_GATEWAY_FEE_MODULE_NAME:
            $template = 'AdminBundle:payment_setting\Logs:gateway_fee_log.html.twig';
            default:
                # code...
                break;
        }
        return $this->render($template, array("logs" => $logs));
    }

    /**
     * @Route("/gateway_fee/logs/print", name="admin_payment_margin_gst_print_logs")
     */
    public function printLogsAction(Request $request) {
        $params['module'] = $request->query->get('module');
        $logs = $this->getDoctrine()->getRepository('UtilBundle:Log')->getLogs($params);
        // Decode json
        for ($i = 0; $i < count($logs); $i++) {
            $logs[$i]['oldValue'] = json_decode($logs[$i]['oldValue'], true);
            $logs[$i]['newValue'] = json_decode($logs[$i]['newValue'], true);
        }

        $template = '';
        switch ($params['module']) {
            case Constant::CUSTOM_CLEARANCE_ADMIN_FEE_MODULE_NAME:
                $template = 'AdminBundle:payment_setting\Logs:caf_pdf.html.twig';
                $fileName = 'CustomClearanceAdminFee';
                break;
            case Constant::PAYMENT_GATEWAY_FEE_MODULE_NAME:
            $template = 'AdminBundle:payment_setting\Logs:gateway_fee_pdf.html.twig';
            $fileName = 'PaymentGatewayFeesHistory';
            default:
                # code...
                break;
        }
        $html = $this->renderView($template, array("logs" => $logs));

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $response = new Response();
        $response->setContent($dompdf->output());
        $response->setStatusCode(200);
        $response->headers->set('Content-Disposition', 'attachment; filename='. $fileName);
        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }

    /**
     * insert logs into log table
     * @param  array $oldData
     * @param  array $newData
     * @param  array $params
     * @return
     */
    public function saveLogs($oldData, $newData, $params) {
        $encodeOldData = json_encode($oldData);
        $encodeNewData = json_encode($newData);
        $params['entityId'] = $params['id'];
        $params['action']   = 'update';
        $params['oldValue'] = $encodeOldData;
        $params['newValue'] = $encodeNewData;
        $params['createdBy'] = $this->getUser()->getLoggedUser()->getFirstName() . ' ' .
                $this->getUser()->getLoggedUser()->getLastName();

        // insert data into log table
        $this->getDoctrine()->getManager()
                ->getRepository('UtilBundle:Log')->insert($params);
    }

     /**
     * @Route("/admin/gateway-fee", name="admin_gateway_fee")
     */
    public function gatewayFeeAction(Request $request) {

        $em = $this->getDoctrine()->getEntityManager();

        $gateWayFees = $em->getRepository('UtilBundle:PaymentGatewayFee')->getCurrentGateWay();
        $vm_mdr = '';
        foreach ($gateWayFees as $item){

            //molpay
           if($item->getPaymentMethod() == Constant::PAY_METHOD_VISA_MASTER && $item->getCode() == Constant::GATEWAY_CODE_MDR && $item ->getPaymentGate() == Constant::PAYMENT_GATE_MOLPAY  ){
                $vm_mdr = $item;
            }
            if($item->getPaymentMethod() == Constant::PAY_METHOD_VISA_MASTER && $item->getCode() == Constant::GATEWAY_CODE_GST && $item ->getPaymentGate() == Constant::PAYMENT_GATE_MOLPAY  ){
                $vm_gst = $item;
            }
            if($item->getPaymentMethod() == Constant::PAY_METHOD_REVPAY_FPX && $item->getCode() == Constant::GATEWAY_CODE_FIX_GST && $item ->getPaymentGate() == Constant::PAYMENT_GATE_MOLPAY  ){
                $rf_gst = $item;
            }
            if($item->getPaymentMethod() == Constant::PAY_METHOD_REVPAY_FPX && $item->getCode() == Constant::GATEWAY_CODE_FIX && $item ->getPaymentGate() == Constant::PAYMENT_GATE_MOLPAY  ){
                $rf_fix = $item;
            }
            if($item->getCode() == Constant::GATEWAY_REFUND_CHARGE && $item ->getPaymentGate() == Constant::PAYMENT_GATE_MOLPAY  ){
                $rf_charge = $item;
            }

            // ipay88
            if($item->getPaymentMethod() == Constant::PAY_METHOD_VISA_MASTER && $item->getCode() == Constant::GATEWAY_CODE_MDR && $item ->getPaymentGate() == Constant::PAYMENT_GATE_IPAY  ){
                $vm_mdr_i = $item;
            }
            if($item->getPaymentMethod() == Constant::PAY_METHOD_VISA_MASTER && $item->getCode() == Constant::GATEWAY_CODE_GST && $item ->getPaymentGate() == Constant::PAYMENT_GATE_IPAY  ){
                $vm_gst_i = $item;
            }
            if($item->getPaymentMethod() == Constant::PAY_METHOD_REVPAY_FPX && $item->getCode() == Constant::GATEWAY_CODE_FIX_GST && $item ->getPaymentGate() == Constant::PAYMENT_GATE_IPAY  ){
                $rf_gst_i = $item;
            }
            if($item->getPaymentMethod() == Constant::PAY_METHOD_REVPAY_FPX && $item->getCode() == Constant::GATEWAY_CODE_FIX && $item ->getPaymentGate() == Constant::PAYMENT_GATE_IPAY  ){
                $rf_fix_i = $item;
            }
            if($item->getCode() == Constant::GATEWAY_REFUND_CHARGE && $item ->getPaymentGate() == Constant::PAYMENT_GATE_IPAY  ){
                $rf_charge_i = $item;
            }

            // reddot
            if($item->getPaymentMethod() == Constant::PAY_METHOD_VISA_MASTER && $item->getCode() == Constant::GATEWAY_CODE_VAR && $item ->getPaymentGate() == Constant::PAYMENT_GATE_REDDOT  ){
                $vm_mdr_r = $item;
            }
            if($item->getPaymentMethod() == Constant::PAY_METHOD_VISA_MASTER && $item->getCode() == Constant::GATEWAY_CODE_FIX && $item ->getPaymentGate() == Constant::PAYMENT_GATE_REDDOT  ){
                $rf_fix_r = $item;
            }
        }
        //molpay
        $form_vm_mdr = $this->createForm(new FeeSettingType(array('config'=>$vm_mdr->getFeeSetting())), array('title' => 'payment_method:_VISA_OR_MASTERCARD - BANK_MDR'), array());
        $form_vm_gst = $this->createForm(new FeeSettingType(array('config'=>$vm_gst->getFeeSetting())), array('title' => 'payment_method:_VISA_OR_MASTERCARD - BANK_GST'), array());
        $form_rf_gst = $this->createForm(new FeeSettingType(array('config'=>$rf_gst->getFeeSetting())), array('title' => 'payment_method:_MYCLEARFPX - BANK_GST'), array());
        $form_rf_fix = $this->createForm(new FeeSettingType(array('config'=>$rf_fix->getFeeSetting())), array('title' => 'payment_method:_MYCLEARFPX - Fixed'), array());
        $form_molpay = $this->createForm(new FeeSettingType(array('config'=>$rf_charge->getFeeSetting())), array('title' => 'payment_method:_MYCLEARFPX - Fixed'), array());

        // ipay88
        $form_vm_mdr_i = $this->createForm(new FeeSettingType(array('config'=>$vm_mdr_i->getFeeSetting())), array('title' => 'payment_method:_VISA_OR_MASTERCARD - BANK_MDR'), array());
        $form_vm_gst_i = $this->createForm(new FeeSettingType(array('config'=>$vm_gst_i->getFeeSetting())), array('title' => 'payment_method:_VISA_OR_MASTERCARD - BANK_GST'), array());
        $form_rf_gst_i = $this->createForm(new FeeSettingType(array('config'=>$rf_gst_i->getFeeSetting())), array('title' => 'payment_method:_MYCLEARFPX - BANK_GST'), array());
        $form_rf_fix_i = $this->createForm(new FeeSettingType(array('config'=>$rf_fix_i->getFeeSetting())), array('title' => 'payment_method:_MYCLEARFPX - Fixed'), array());
        $form_ipay = $this->createForm(new FeeSettingType(array('config'=>$rf_charge_i->getFeeSetting())), array('title' => 'payment_method:_MYCLEARFPX - refund charge'), array());

        // reddot
        $form_vm_mdr_r = $this->createForm(new FeeSettingType(array('config'=>$vm_mdr_r->getFeeSetting())), array('title' => 'payment_method:_VISA_OR_MASTERCARD - Payment_Gateway_Variable'), array());
        $form_rf_fix_r = $this->createForm(new FeeSettingType(array('config'=>$rf_fix_r->getFeeSetting())), array('title' => 'payment_method:_VISA_OR_MASTERCARD - BANK_GST'), array());

        $parameters = array(
            'ajaxURL'       => 'admin_fee_ajax',
            'form_vm_mdr'   => $form_vm_mdr->createView(),
            'form_vm_gst'   => $form_vm_gst->createView(),
            'form_rf_gst'   => $form_rf_gst->createView(),
            'form_rf_fix'   => $form_rf_fix->createView(),
            'form_molpay'   => $form_molpay->createView(),
            'form_vm_mdr_i' => $form_vm_mdr_i->createView(),
            'form_vm_gst_i' => $form_vm_gst_i->createView(),
            'form_rf_gst_i' => $form_rf_gst_i->createView(),
            'form_rf_fix_i' => $form_rf_fix_i->createView(),
            'form_vm_mdr_r' => $form_vm_mdr_r->createView(),
            'form_rf_fix_r' => $form_rf_fix_r->createView(),
            'form_ipay'     => $form_ipay->createView(),
            'title'         => 'Payment Gateway Fees',
            'vm_mdr'        => '',
            'vm_gst'        => '',
            'vm_mdr_r'      => '',
            'rf_fix_r'      => '',
            'rf_gst'        => '',
            'rf_fix'        => '',
            'molpay_charge' => '',
            'vm_mdr_i'      => '',
            'vm_gst_i'      => '',
            'rf_gst_i'      => '',
            'rf_fix_i'      => '',
            'ipay_charge'   => '',
        );
        //molpay
        if(!empty($vm_mdr->getFeeSetting()->getEffectDate())&& strtotime($vm_mdr->getFeeSetting()->getEffectDate()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            $parameters['vm_mdr'] = $vm_mdr->getFeeSetting()->getFee();
        }
        if(!empty($vm_gst->getFeeSetting()->getEffectDate()) && strtotime($vm_gst->getFeeSetting()->getEffectDate()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            $parameters['vm_gst'] = $vm_gst->getFeeSetting()->getFee();
        }

        if(!empty($rf_gst->getFeeSetting()->getEffectDate()) && strtotime($rf_gst->getFeeSetting()->getEffectDate()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            $parameters['rf_gst'] = $rf_gst->getFeeSetting()->getFee();
        }
        if(!empty($rf_fix->getFeeSetting()->getEffectDate()) && strtotime($rf_fix->getFeeSetting()->getEffectDate()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            $parameters['rf_fix'] = $rf_fix->getFeeSetting()->getFee();
        }
        if(!empty($rf_charge->getFeeSetting()->getEffectDate()) && strtotime($rf_charge->getFeeSetting()->getEffectDate()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            $parameters['molpay_charge'] = $rf_charge->getFeeSetting()->getFee();
        }
        //ipay88
        if(!empty($vm_mdr_i->getFeeSetting()->getEffectDate()) &&strtotime($vm_mdr_i->getFeeSetting()->getEffectDate()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            $parameters['vm_mdr_i'] = $vm_mdr_i->getFeeSetting()->getFee();
        }
        if(!empty($vm_gst_i->getFeeSetting()->getEffectDate()) && strtotime($vm_gst_i->getFeeSetting()->getEffectDate()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            $parameters['vm_gst_i'] = $vm_gst_i->getFeeSetting()->getFee();
        }

        if(!empty($rf_gst_i->getFeeSetting()->getEffectDate()) && strtotime($rf_gst_i->getFeeSetting()->getEffectDate()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            $parameters['rf_gst_i'] = $rf_gst_i->getFeeSetting()->getFee();
        }
        if(!empty($rf_fix_i->getFeeSetting()->getEffectDate()) && strtotime($rf_fix_i->getFeeSetting()->getEffectDate()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            $parameters['rf_fix_i'] = $rf_fix_i->getFeeSetting()->getFee();
        }
        if(!empty($rf_charge_i->getFeeSetting()->getEffectDate()) && strtotime($rf_charge_i->getFeeSetting()->getEffectDate()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            $parameters['ipay_charge'] = $rf_charge_i->getFeeSetting()->getFee();
        }

        //reddot
        if(!empty($vm_mdr_r->getFeeSetting()->getEffectDate())&& strtotime($vm_mdr_r->getFeeSetting()->getEffectDate()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            $parameters['vm_mdr_r'] = $vm_mdr_r->getFeeSetting()->getFee();
        }
        if(!empty($rf_fix_r->getFeeSetting()->getEffectDate()) && strtotime($rf_fix_r->getFeeSetting()->getEffectDate()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            $parameters['rf_fix_r'] = $rf_fix_r->getFeeSetting()->getFee();
        }
        return $this->render('AdminBundle:admin:gateway-fee.html.twig',$parameters);

    }

    /**
     * @Route("/admin/ajax-admin-fee", name="admin_fee_ajax")
     */
    public function ajaxAdminFeeAction(Request $request) {
        $loggerUser = $this->getUser()->getLoggedUser();
        $author     = $loggerUser->getFirstName() . ' ' . $loggerUser->getLastName();
        $authorId   = $this->getUser()->getLoggedUser()->getId();
        $result     = array('success' => FALSE);

        if($request->getMethod() == 'POST') {
            $em    = $this->getDoctrine()->getEntityManager();
            $id    = $request->request->get('id');
            $type  = $request->request->get('type');
            $table = 'fee_setting';
            $field = 'new_fee';

            switch ($type) {
                case 1:
                    $obj        = $em->getRepository('UtilBundle:CustomClearanceAdminFee')->find($id);
                    $fee        = $obj->getFeeSetting();
                    $oldValue   = $em->getRepository('UtilBundle:FeeSetting')->getFeeSettingById($fee->getId());
                    $data       = $request->request->get('admin_fee');
                    $data = Common::removeSpaceOf($data);
                    $effectDate = new \DateTime( date('Y-m-d', strtotime($data['date1'])));
                    $dateTime   = new \DateTime('now');
                    $fee->setNewFee($data['fee1']);
                    $fee->setEffectDate($effectDate);
                    if($dateTime->format('Y-m-d') >= $effectDate->format('Y-m-d')) {
                        $fee->setFee($data['fee1']);
                    }
                    $em->persist($obj);
                    $em->flush();

                    // logging
                    $newValue       = $em->getRepository('UtilBundle:FeeSetting')->getFeeSettingById($fee->getId());
                    $data['module'] = Constant::CUSTOM_CLEARANCE_ADMIN_FEE_MODULE_NAME;
                    $data['title']  = 'for_singapore';
                    $data['id']     = $fee->getId();

                    Utils::saveLog($oldValue, $newValue, $author, $data, $em);
                    // insert log price
                    $oldFee        = isset($oldValue['fee']) ? $oldValue['fee'] : null;
                    $oldNewFee     = isset($oldValue['newFee']) ? $oldValue['newFee'] : null;
                    $oldEffectDate = isset($oldValue['effectDate']) ? $oldValue['effectDate'] : null;

                    $newPrice  = $data['fee1'];
                    if($effectDate->format('Y-m-d') != $oldEffectDate->format('Y-m-d') || $oldNewFee != $fee->getNewFee()) {
                        $inputs[]  = [
                            'tableName'  => $table,
                            'fieldName'  => $field,
                            'entityId'   => $data['id'],
                            'oldPrice'   => $oldFee,
                            'newPrice'   => $newPrice,
                            'createdBy'  => $authorId,
                            'em'         => $em,
                            'effectedOn' => $effectDate
                        ];
                        Utils::saveLogPrice($inputs);
                    }

                    $result['success'] = true;
                    $result['value']   = $fee->getFee();
                    break;

                 case 2:
                    $obj        = $em->getRepository('UtilBundle:CustomClearanceAdminFee')->find($id);
                    $fee        = $obj->getFeeSetting();
                    $oldValue   = $em->getRepository('UtilBundle:FeeSetting')->getFeeSettingById($fee->getId());
                    $data       = $request->request->get('admin_fee');
                    $data = Common::removeSpaceOf($data);
                    $effectDate = new \DateTime( date('Y-m-d', strtotime($data['date2'])));
                    $dateTime   = new \DateTime('now');
                    $fee->setNewFee( $data['fee2']);
                    $fee->setEffectDate($effectDate);
                    if($dateTime->format('Y-m-d') >= $effectDate->format('Y-m-d')) {
                        $fee->setFee($data['fee2']);
                    }
                    $em->persist($obj);
                    $em->flush();

                    $newValue       = $em->getRepository('UtilBundle:FeeSetting')->getFeeSettingById($fee->getId());
                    $data['module'] = Constant::CUSTOM_CLEARANCE_ADMIN_FEE_MODULE_NAME;
                    $data['title']  = 'for_indonesia';
                    $data['id']     = $fee->getId();

                    Utils::saveLog($oldValue, $newValue, $author, $data, $em);
                    // insert log price
                    $oldFee        = isset($oldValue['fee']) ? $oldValue['fee'] : null;
                    $oldNewFee     = isset($oldValue['newFee']) ? $oldValue['newFee'] : null;
                    $oldEffectDate = isset($oldValue['effectDate']) ? $oldValue['effectDate'] : null;
                    $newPrice      = $data['fee2'];

                    if($effectDate->format('Y-m-d') != $oldEffectDate->format('Y-m-d') || $oldNewFee != $fee->getNewFee()) {
                        $inputs[]  = [
                            'tableName'  => $table,
                            'fieldName'  => $field,
                            'entityId'   => $data['id'],
                            'oldPrice'   => $oldFee,
                            'newPrice'   => $newPrice,
                            'createdBy'  => $authorId,
                            'em'         => $em,
                            'effectedOn' => $effectDate
                        ];
                        Utils::saveLogPrice($inputs);
                    }

                    $result['success'] = true;
                    $result['value']   = $fee->getFee();
                    break;
                 case 3:
                    $data       = $request->request->get('admin_fee');
                     $data = Common::removeSpaceOf($data);
                    $effectDate = new \DateTime( date('Y-m-d', strtotime($data['date'])));
                    $dateTime   = new \DateTime('now');
                    $oldValue   = $em->getRepository('UtilBundle:FeeSetting')->getFeeSettingById($data['id']);
                    $fee        = $em->getRepository('UtilBundle:FeeSetting')->find($data['id']);
                    $fee->setNewFee( $data['fee']);
                    $fee->setEffectDate($effectDate);
                    if($dateTime->format('Y-m-d') >= $effectDate->format('Y-m-d')) {
                        $fee->setFee($data['fee']);
                    }
                    $em->persist($fee);
                    $em->flush();

                    $newValue = $em->getRepository('UtilBundle:FeeSetting')->getFeeSettingById($data['id']);
                    Utils::saveLog($oldValue, $newValue, $author, $data, $em);
                    // insert log price
                    $oldFee        = isset($oldValue['fee']) ? $oldValue['fee'] : null;
                    $oldNewFee     = isset($oldValue['newFee']) ? $oldValue['newFee'] : null;
                    $oldEffectDate = isset($oldValue['effectDate']) ? $oldValue['effectDate'] : null;

                    $newPrice  = $data['fee'];

                    if($effectDate->format('Y-m-d') != $oldEffectDate->format('Y-m-d') || $oldNewFee != $fee->getNewFee()) {
                        $inputs[] = [
                            'tableName'  => $table,
                            'fieldName'  => $field,
                            'entityId'   => $data['id'],
                            'oldPrice'   => $oldValue['fee'],
                            'newPrice'   => $newPrice,
                            'createdBy'  => $authorId,
                            'em'         => $em,
                            'effectedOn' => $effectDate
                        ];
                        Utils::saveLogPrice($inputs);
                    }

                    $result['success'] = true;
                    $result['value']   = $fee->getFee();
                    break;
            }

        }
        return new JsonResponse($result);
    }

    /**
     * Create admin users
     * @Route("/admin/create-admin", name="admin_create")
     * @author toan.le
     */
    public function createAdminAction(Request $request){
        return $this->render('AdminBundle:admin:admin-create.html.twig');
    }

    /**
    * @Route("/admin/others-fee", name="admin_others_fee")
    */
    public function othersFeeAction(Request $request)
    {
        try
        {
            $title           = 'Other Fees';
            $em              = $this->getDoctrine()->getManager();
            $repo            = $em->getRepository('UtilBundle:PlatformSettings');
            $platFormSetting = $repo->getPlatFormSetting();

            if(!isset($platFormSetting['bufferRate'])) {
                return new Response('Platform Settings not found.');
            }

            $bufferRate          = $platFormSetting['bufferRate'];
            $operationsCountryId = $platFormSetting['operationsCountryId'];
            $formData = array(
                'operationsCountryId' => $operationsCountryId,
                'bufferRate'          => $bufferRate,

            );
            $agentFees = $em->getRepository('UtilBundle:AgentMininumFeeSetting')->findAll();
            $agenFeePrimary = [
                'local'=> '',
                'feeIndo'=> '',
                'feeEastMalay'=> '',
                'feeWestMalay'=> '',
                'feeInternational'=> '',
            ];
            $agenFeeSecondary = [
                'local'=> '',
                'feeIndo'=> '',
                'feeEastMalay'=> '',
                'feeWestMalay'=> '',
                'feeInternational'=> '',
            ];
            foreach ($agentFees as $fee) {
               if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_PRIMARY_lOCAL){
                    $agenFeePrimary['local'] =  $fee->getFeeValue();
                }
                if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_PRIMARY_INDONESIA){
                    $agenFeePrimary['feeIndo'] =  $fee->getFeeValue();
                }
                if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_PRIMARY_EAST_MALAY){
                    $agenFeePrimary['feeEastMalay'] =  $fee->getFeeValue();
                } if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_PRIMARY_WEST_MALAY){
                    $agenFeePrimary['feeWestMalay'] =  $fee->getFeeValue();
                }
                if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_PRIMARY_INTERNATIONAL){
                    $agenFeePrimary['feeInternational'] =  $fee->getFeeValue();
                }
                if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_SECONDARY_lOCAL){
                    $agenFeeSecondary['local'] =  $fee->getFeeValue();
                }
                if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_SECONDARY_INDONESIA){
                    $agenFeeSecondary['feeIndo'] =  $fee->getFeeValue();
                }
                if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_SECONDARY_EAST_MALAY){
                    $agenFeeSecondary['feeEastMalay'] =  $fee->getFeeValue();
                }
                if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_SECONDARY_WEST_MALAY){
                    $agenFeeSecondary['feeWestMalay'] =  $fee->getFeeValue();
                }
                if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_PRIMARY_INTERNATIONAL){
                    $agenFeeSecondary['feeInternational'] =  $fee->getFeeValue();
                }
            }


            $form = $this->createForm(new OthersFeeType([]), $formData, [
                        'method' => "post",
                        'action' => $this->generateUrl('admin_others_fee')
                    ]);
            $minForm = $this->createForm(new MinFeeType([]), $agenFeePrimary, [
                'method' => "post",
                'action' => $this->generateUrl('admin_others_fee')
            ]);
            $secondaryForm = $this->createForm(new MinFeeType([]), $agenFeeSecondary, [
                'method' => "post",
                'action' => $this->generateUrl('admin_others_fee')
            ]);
            if ($request->getMethod() === 'POST') {
                $jsonResponse = array('success' => false);
                $type = $request->request->get('type',0);
                if($type == 1){
                    $form->handleRequest($request);
                    if ($form->isValid()) {
                        $data   = $form->getData();
                        $params = [
                            'operationsCountryId' => $data['operationsCountryId'],
                            'bufferRate'          => $data['bufferRate']
                        ];

                        $results  = $repo->update($params);
                        $newValue = $repo->getPlatFormSetting()['bufferRate'];

                        if(!isset($newValue)) {
                            $this->get('session')->getFlashBag()->add('danger',MsgUtils::generate('msgCannotEdited', $title));
                        }
                        else
                        {
                            //insert logs
                            $logParams           = $params;
                            $logParams['title']  = $title;
                            $logParams['module'] = 'admin_others_fee';
                            $logParams['id']     = $operationsCountryId;
                            $this->saveLogs(
                                ['bufferRate' => $bufferRate],
                                ['bufferRate' => $newValue],
                                $logParams
                            );
                            $authorId = $this->getUser()->getLoggedUser()->getId();
                            //insert logs price
                            if($bufferRate != $newValue) {
                                $inputs[] = [
                                    'tableName'  => 'platform_settings',
                                    'fieldName'  => 'buffer_rate',
                                    'entityId'   => $operationsCountryId,
                                    'oldPrice'   => $bufferRate,
                                    'newPrice'   => $newValue,
                                    'createdBy'  => $authorId,
                                    'em'         => $em
                                ];
                                Utils::saveLogPrice($inputs);
                            }

                            $this->get('session')->getFlashBag()->add('success', MsgUtils::generate('msgUpdatedSuccess', $title));
                        }
                    }
                }
                if($type == 2){
                    $minForm->handleRequest($request);
                    if ($minForm->isValid()) {
                        $data   = $minForm->getData();
                        $oldValues= [];
                        $agentFeeRepo = $em->getRepository('UtilBundle:AgentMininumFeeSetting');
                        $feeLocal =  $agentFeeRepo->findOneBy(['feeCode' => Constant::AGENT_MINUMUM_FEE_PRIMARY_lOCAL]);
                        $oldValues['local'] = $feeLocal->getFeeValue();
                        $feeLocal->setFeeValue($data['feeLocal']);
                        $em->persist($feeLocal);

                        $feeIndo =  $agentFeeRepo->findOneBy(['feeCode' => Constant::AGENT_MINUMUM_FEE_PRIMARY_INDONESIA]);
                        $oldValues['indonesia'] = $feeIndo->getFeeValue();
                        $feeIndo->setFeeValue($data['feeIndo']);
                        $em->persist($feeIndo);

                        $feeEastMalay =  $agentFeeRepo->findOneBy(['feeCode' => Constant::AGENT_MINUMUM_FEE_PRIMARY_EAST_MALAY]);
                        $oldValues['eastMalaysia'] = $feeEastMalay->getFeeValue();
                        $feeEastMalay->setFeeValue($data['feeEastMalay']);
                        $em->persist($feeEastMalay);

                        $feeWestMalay =  $agentFeeRepo->findOneBy(['feeCode' => Constant::AGENT_MINUMUM_FEE_PRIMARY_WEST_MALAY]);
                        $oldValues['westMalaysia'] = $feeWestMalay->getFeeValue();
                        $feeWestMalay->setFeeValue($data['feeWestMalay']);
                        $em->persist($feeWestMalay);

                        $feeInternational =  $agentFeeRepo->findOneBy(['feeCode' => Constant::AGENT_MINUMUM_FEE_PRIMARY_INTERNATIONAL]);
                        $oldValues['international'] = $feeInternational->getFeeValue();
                        $feeInternational->setFeeValue($data['feeInternational']);
                        $em->persist($feeInternational);
                        $em->flush();

                        //insert logs
                        $logParams           = [];
                        $logParams['title']  = $title;
                        $logParams['module'] = 'admin_others_fee';
                        $logParams['id']     = $operationsCountryId;
                        $this->saveLogs(
                            $oldValues,
                            [
                                'local' => floatval($feeLocal->getFeeValue()),
                                'indonesia' => floatval($feeIndo->getFeeValue()),
                                'eastMalaysia' => floatval($feeEastMalay->getFeeValue()),
                                'westMalaysia' => floatval($feeWestMalay->getFeeValue()),
                                'international' => floatval($feeInternational->getFeeValue()),

                            ],
                            $logParams
                        );

                        $jsonResponse['success'] = true;
                        $jsonResponse['message'] = 'Global Minimum Medicine Margins For Payout To Primary Agents';
                    }
                }
                if($type == 3){
                    $secondaryForm->handleRequest($request);
                    if ($secondaryForm->isValid()) {
                        $data   = $secondaryForm->getData();
                        $oldValues= [];
                        $agentFeeRepo = $em->getRepository('UtilBundle:AgentMininumFeeSetting');
                        $feeLocal =  $agentFeeRepo->findOneBy(['feeCode' => Constant::AGENT_MINUMUM_FEE_SECONDARY_lOCAL]);
                        $oldValues['local'] = $feeLocal->getFeeValue();
                        $feeLocal->setFeeValue($data['feeLocal']);
                        $em->persist($feeLocal);

                        $feeIndo =  $agentFeeRepo->findOneBy(['feeCode' => Constant::AGENT_MINUMUM_FEE_SECONDARY_INDONESIA]);
                        $oldValues['indonesia'] = $feeIndo->getFeeValue();
                        $feeIndo->setFeeValue($data['feeIndo']);
                        $em->persist($feeIndo);

                        $feeEastMalay =  $agentFeeRepo->findOneBy(['feeCode' => Constant::AGENT_MINUMUM_FEE_SECONDARY_EAST_MALAY]);
                        $oldValues['eastMalaysia'] = $feeEastMalay->getFeeValue();
                        $feeEastMalay->setFeeValue($data['feeEastMalay']);
                        $em->persist($feeEastMalay);

                        $feeWestMalay =  $agentFeeRepo->findOneBy(['feeCode' => Constant::AGENT_MINUMUM_FEE_SECONDARY_WEST_MALAY]);
                        $oldValues['westMalaysia'] = $feeWestMalay->getFeeValue();
                        $feeWestMalay->setFeeValue($data['feeWestMalay']);
                        $em->persist($feeWestMalay);

                        $feeInternational =  $agentFeeRepo->findOneBy(['feeCode' => Constant::AGENT_MINUMUM_FEE_SECONDARY_INTERNATIONAL]);
                        $oldValues['international'] = $feeInternational->getFeeValue();
                        $feeInternational->setFeeValue($data['feeInternational']);
                        $em->persist($feeInternational);
                        $em->flush();

                        //insert logs
                        $logParams           = [];
                        $logParams['title']  = $title;
                        $logParams['module'] = 'admin_others_fee';
                        $logParams['id']     = $operationsCountryId;
                        $this->saveLogs(
                            $oldValues,
                            [
                                'local' => floatval($feeLocal->getFeeValue()),
                                'indonesia' => floatval($feeIndo->getFeeValue()),
                                'eastMalaysia' => floatval($feeEastMalay->getFeeValue()),
                                'westMalaysia' => floatval($feeWestMalay->getFeeValue()),
                                'international' => floatval($feeInternational->getFeeValue()),

                            ],
                            $logParams
                        );

                        $jsonResponse['success'] = true;
                        $jsonResponse['message'] = 'Global Minimum Medicine Margins For Payout To Secondary Agents';
                    }
                }
            }
            if($request->isXmlHttpRequest()) {
                return new JsonResponse($jsonResponse);
            }

            $params = [
                'title' => $title,
                'form'  => $form->createView(),
                'minForm' => $minForm->createView(),
                'secondaryForm' => $secondaryForm->createView()
            ];
            return $this->render('AdminBundle:admin:others-fee.html.twig', $params);

        }
        catch (\Exception $e)
        {
            return new Response($e->getMessage());
        }
    }

    public function logDoctorPrescribingFee($doctor, $arrFileds)
    {
        $em = $this->getDoctrine()->getManager();
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
    * @Route("/admin/others-setting", name="admin_others_setting")
    */
    public function othersSettingAction(Request $request)
    {
        try
        {
            $title           = 'Other Settings';
            $em              = $this->getDoctrine()->getManager();
            $repo            = $em->getRepository('UtilBundle:PlatformSettings');
            $platFormSetting = $repo->getPlatFormSetting();

            if(!isset($platFormSetting['operationsCountryId'])) {
                return new Response('Platform Settings not found.');
            }

            $scheduleDeclarationTime = $platFormSetting['scheduleDeclarationTime'];
            $operationsCountryId     = $platFormSetting['operationsCountryId'];
            $formData = array(
                'operationsCountryId'     => $operationsCountryId,
                'scheduleDeclarationTime' => $scheduleDeclarationTime
            );

            $form = $this->createForm(new OthersSettingType([]), $formData, [
                        'method' => "post",
                        'action' => $this->generateUrl('admin_others_setting')
                    ]);

            if ($request->getMethod() === 'POST') {
                $form->handleRequest($request);
                if ($form->isValid()) {
                    $data   = $form->getData();
                    $params = [
                        'operationsCountryId'     => $data['operationsCountryId'],
                        'scheduleDeclarationTime' => $data['scheduleDeclarationTime']
                    ];

                    $results  = $repo->update($params);
                    $newValue = $repo->getPlatFormSetting()['scheduleDeclarationTime'];

                    if(!isset($newValue)) {
                        $this->get('session')->getFlashBag()->add('danger',MsgUtils::generate('msgCannotEdited', $title));
                    } else {
                        //insert logs
                        $logParams           = $params;
                        $logParams['title']  = $title;
                        $logParams['module'] = 'admin_others_setting';
                        $logParams['id']     = $operationsCountryId;

                        $this->saveLogs(
                            ['scheduleDeclarationTime' => $scheduleDeclarationTime],
                            ['scheduleDeclarationTime' => $newValue],
                            $logParams
                        );
                        $this->get('session')->getFlashBag()->add('success', MsgUtils::generate('msgUpdatedSuccess', $title));
                    }
                }
            }

            $params = [
                'title' => $title,
                'form'  => $form->createView()
            ];
            return $this->render('AdminBundle:admin:others-setting.html.twig', $params);

        }
        catch (\Exception $e)
        {
            return new Response($e->getMessage());
        }
    }

    public function saveLogPriceMarginSharing($type, $posts, $oldData, $em)
    {
        try
        {
            $oldEffectDate = isset($oldData['takeEffectOn']) ? $oldData['takeEffectOn']->format('Y-m-d') : null;
            $authorId      = $this->getUser()->getLoggedUser()->getId();
            $table         = 'platform_share_percentages';
            $entityId      = $posts['id'];
            $effectDate    = new \DateTime( date('Y-m-d', strtotime( $posts['takeEffectOn'] )));
            $inputs        = [];

            $arrTmp = [
                    'agent_percentage'    => ['agentPercentage', 'newAgentPercentage'],
                    'platform_percentage' => ['platformPercentage', 'newPlatformPercentage'],
                    'doctor_percentage'   => ['doctorPercentage', 'newDoctorPercentage']
            ];
            foreach ($arrTmp as $field => $val) {
                $oldValue    = $oldData[$val[0]];
                $oldValueNew = $oldData[$val[1]];
                $newValue    = $posts[$val[0]];

                if($oldEffectDate != $effectDate->format('Y-m-d') || $oldValueNew != $newValue) {
                    $inputs[] = [
                            'tableName'  => $table,
                            'fieldName'  => $field,
                            'entityId'   => $entityId,
                            'oldPrice'   => $oldValue,
                            'newPrice'   => $newValue,
                            'createdBy'  => $authorId,
                            'em'         => $em,
                            'effectedOn' => $effectDate
                    ];
                    return $inputs;
                }
            }
            Utils::saveLogPrice($inputs);
        }
        catch (\Exception $e)
        {
            return new Response($e->getMessage());
        }
    }
}
