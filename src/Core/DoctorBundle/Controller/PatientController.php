<?php

namespace DoctorBundle\Controller;

use Dompdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use UtilBundle\Entity\Diagnosis;
use UtilBundle\Entity\PatientDiagnosis;
use UtilBundle\Entity\PatientNote;
use UtilBundle\Utility\Constant;
use UtilBundle\Utility\Common;
use UtilBundle\Entity\Patient;
use UtilBundle\Entity\PatientMedicationAllergy;
use UtilBundle\Entity\Phone;
use UtilBundle\Entity\CareGiver;
use UtilBundle\Utility\Utils;

/**
 * @Route("/patient")
 */
class PatientController extends Controller
{
    /**
     * @Route("/", name="patient_index")
     */
    public function indexAction(Request $request)
    {
        $parameters = array();
        $parameters['countries'] = $this->getDoctrine()->getRepository('UtilBundle:Country')
            ->getListContry();

        $ptRep = $this->getPatientRepository();
        $params = array(
            'doctorId' => $this->getDoctorId(),
            'rxStatus' => array(
                Constant::RX_STATUS_CONFIRMED,
                Constant::RX_STATUS_REVIEWING,
                Constant::RX_STATUS_APPROVED,
                Constant::RX_STATUS_DISPENSED,
                Constant::RX_STATUS_READY_FOR_COLLECTION,
                Constant::RX_STATUS_COLLECTED,
                Constant::RX_STATUS_DELIVERING,
                Constant::RX_STATUS_DELIVERED
            )
        );
        $results = $ptRep->getListPatient($params);
        $parameters['countData'] = $results['totalResult'];

        return $this->render('DoctorBundle:patient:index.html.twig', $parameters);
    }

    /**
     * @Route("/ajax-get-list", name="patient_ajax_get_list")
     */
    public function ajaxGetListPatientAction(Request $request)
    {
        $params = $request->query->all();
        $params['page'] = $request->get('page', 0);
        $params['perPage'] = $request->get('perPage', Constant::PER_PAGE_DEFAULT);
        $params['doctorId'] = $this->getDoctorId();
        $params['rxStatus'] = array(
            Constant::RX_STATUS_CONFIRMED,
            Constant::RX_STATUS_REVIEWING,
            Constant::RX_STATUS_APPROVED,
            Constant::RX_STATUS_DISPENSED,
            Constant::RX_STATUS_READY_FOR_COLLECTION,
            Constant::RX_STATUS_COLLECTED,
            Constant::RX_STATUS_DELIVERING,
            Constant::RX_STATUS_DELIVERED
        );

        $ptRep = $this->getPatientRepository();

        $results = $ptRep->getListPatient($params);

        //page url
        unset($params['doctorId']);
        $pageUrl = $this->generateUrl('patient_ajax_get_list', $params);

        //paging
        $totalPages = isset($results['totalPages']) ? $results['totalPages'] : Constant::TOTAL_PAGE_DEFAULT;

        //build paging
        $paginationHTML = Common::buildPagination(
            $this->container,
            $request,
            $totalPages,
            $params['page'],
            $params['perPage'],
            array('pageUrl' => $pageUrl)
        );

        $sortInfo = array('column' => 'p.createdOn', 'direction' => 'asc');
        if (isset($params['sorting']) && $params['sorting']) {
            list($sortInfo['column'], $sortInfo['direction']) = explode('-', $params['sorting']);
        }

      

        $view = 'DoctorBundle:patient:_ajax-list-patient.html.twig';
        $parameters = array(
            'data'           => $results['data'],
            'paginationHTML' => $paginationHTML,
            'currentPage'    => $params['page'],
            'perPage'        => $params['perPage'],
            'totalResult'    => $results['totalResult'],
            'sortInfo'       => $sortInfo
        );

        return $this->render($view, $parameters);
    }

    /**
     * @Route("/delete/{patientId}", name="patient_delete")
     */
    public function deleteAction(Request $request, $patientId)
    {
        $ptRep = $this->getPatientRepository();

        $patient = $ptRep->find($patientId);
        if (null == $patient) {
            throw $this->createAccessDeniedException();
        }

        try {
            $patient->setDeletedOn(new \DateTime());
            $ptRep->getEntityManager()->persist($patient);
            $ptRep->getEntityManager()->flush($patient);
        } catch(\Exception $ex) {
        }

        return $this->redirectToRoute('patient_index');
    }

    /**
     * @Route("/list-rx-history/{patientId}", name="patient_list_rx_history")
     */
    public function listRXHistoryAction(Request $request, $patientId)
    {
        $rxRepository = $this->getRXRepository();

        $parameters = array();
        $parameters['patientId'] = $patientId;

        $params = array(
            'doctorId'  => $this->getDoctorId(),
            'patientId' => $patientId
        );
        $parameters['patientInformation'] = $rxRepository->getPatientInformation($params);

        return $this->render('DoctorBundle:patient:list-rx-history.html.twig', $parameters);
    }

    /**
     * @Route("/ajax-patient-info", name="patient_ajax_get_info")
     */
    public function ajaxGetPatientInfoAction(Request $request)
    {
        $currentType = $request->request->get('type');
        switch ($currentType) {
            case 1:
                return $this->loadNotePopup($request);
                break;

            case 2:
                return $this->addNoteAjax($request);
                break;
            case 3:
                return $this->deleteNoteAjax($request);
                break;

            default:
                return new JsonResponse(['success'=>false]);

        }

    }

    /**
     * @Route("/patient-note/{patientId}", name="patient_ajax_get_info_note_list")
     */
    public function exportNoteAjax(Request $request, $patientId = 0){

        $rxRepository = $this->getRXRepository();
        $patientRepo = $this->getPatientRepository();
        $params = array(
            'patientId' => $patientId,
            'otherDiagnosisValues' => $patientRepo->getOtherDiagnosticValues($patientId)
        );
        $parameters = [];
        $patientNotes = [];
        $patient = $patientRepo->find($patientId);
        if(empty($patient)){
            return new JsonResponse(['Patient is incorrect']);
        }
        $notes = $patient->getNotes();
        foreach ($notes as $note) {
            if(empty($note->getDeletedOn())){
                $patientNotes[] = ['time' =>$note->getCreatedOn()->format("d M y , h:i A"),'note' => $note->getNote(), 'id' => $note->getId() ];
            }

        }

        $parameters['patientInformation'] = $rxRepository->getPatientInformationForPatientNote($params);
        $parameters['notes'] = array_reverse($patientNotes);

        $template   = 'DoctorBundle:patient:export-patient-note.html.twig';
        $html = $this->container->get('templating')->render($template,$parameters);


        $dompdf = new Dompdf(array('isRemoteEnabled'=> true, 'isPhpEnabled' => true));
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $response = new Response();
        $response->setContent($dompdf->output());
        $response->setStatusCode(200);

        $response->headers->set('Content-Type', 'application/pdf');

        return $response;

    }


    private function addNoteAjax($request){
        $patientId = $request->request->get('patient');
        $patientId = Common::decodeHex($patientId);

        $patientRepo = $this->getPatientRepository();
        $patient = $patientRepo->find($patientId);
        $note = $request->request->get('note');
        if(empty(trim($note))){
            return new JsonResponse(['success' => false, 'message' => 'note content is empty']);
        }
        try {
            $pn = new PatientNote();
            $pn->setNote(nl2br($note));
            $patient->addNote($pn);

            $em = $this->getDoctrine()->getManager();

            $em->persist($patient);
            $em->flush();
            return new JsonResponse(['success' => true]);
        } catch (\Exception $ex) {
            return new JsonResponse(['success' => false]);
        }
    }

    private function deleteNoteAjax($request){
        $patientId = $request->request->get('patient');
        $patientId = Common::decodeHex($patientId);
        $note = $request->request->get('note-id');
        if(empty(trim($note))){
            return new JsonResponse(['success' => false, 'message' => 'id is empty']);
        }
        try {
            $em = $this->getDoctrine()->getManager();
            $pn = $em->getRepository('UtilBundle:PatientNote')->find($note);

            if(!empty($pn)){
                if(empty($pn->getPatient()) || $pn->getPatient()->getId() != $patientId) {
                    return new JsonResponse(['success' => false, 'message' => 'patient and note is not match']);
                }
                $pn->setDeletedOn(new \DateTime());
                $em->persist($pn);
                $em->flush();
                return new JsonResponse(['success' => true]);
            } else {
                return new JsonResponse(['success' => false, 'message' => 'id is invalid']);
            }



        } catch (\Exception $ex) {
            return new JsonResponse(['success' => false,'message' => 'exception']);
        }
    }

    private function loadNotePopup($request){
        $patientId = $request->request->get('patient');
        $patientId = Common::decodeHex($patientId);
        $rxRepository = $this->getRXRepository();
        $patientRepo = $this->getPatientRepository();
        $params = array(
            'patientId' => $patientId,
            'otherDiagnosisValues' => $patientRepo->getOtherDiagnosticValues($patientId)
        );
        $parameters = [];
        $patientNotes = [];
        $patient = $patientRepo->find($patientId);
        $notes = $patient->getNotes();
        foreach ($notes as $note) {
            if(empty($note->getDeletedOn())){
                $patientNotes[] = ['time' =>$note->getCreatedOn()->format("d M y , h:i A"),'note' => $note->getNote(), 'id' => $note->getId() ];
            }

        }

        $parameters['patientInformation'] = $rxRepository->getPatientInformationForPatientNote($params);
        $parameters['notes'] = array_reverse($patientNotes);

        return new JsonResponse($parameters);
    }

    /**
     * @Route("/ajax-list-rx-history/{patientId}", name="patient_ajax_list_rx_history")
     */
    public function ajaxListRXHistoryAction(Request $request, $patientId)
    {
        $params = $request->query->all();
        $params['page'] = $request->get('page', 0);
        $params['perPage'] = $request->get('perPage', Constant::PER_PAGE_DEFAULT);
        $params['doctorId'] = $this->getDoctorId();
        $params['patientId'] = $patientId;

        $ptRep = $this->getPatientRepository();

        $results = $ptRep->getPatientRXHistory($params);

        //page url
        unset($params['doctorId']);
        $pageUrl = $this->generateUrl('patient_ajax_list_rx_history', $params);

        //paging
        $totalPages = isset($results['totalPages']) ? $results['totalPages'] : Constant::TOTAL_PAGE_DEFAULT;

        //build paging
        $paginationHTML = Common::buildPagination(
            $this->container,
            $request,
            $totalPages,
            $params['page'],
            $params['perPage'],
            array('pageUrl' => $pageUrl)
        );

        $sortInfo = array('column' => 'rx.createdOn', 'direction' => 'desc');
        if (isset($params['sorting']) && $params['sorting']) {
            list($sortInfo['column'], $sortInfo['direction']) = explode('-', $params['sorting']);
        }

        $view = 'DoctorBundle:patient:_ajax-rx-history.html.twig';
        $parameters = array(
            'data'           => $results['data'],
            'paginationHTML' => $paginationHTML,
            'currentPage'    => $params['page'],
            'perPage'        => $params['perPage'],
            'totalResult'    => $results['totalResult'],
            'pageUrl'        => $pageUrl,
            'sortInfo'       => $sortInfo
        );

        return $this->render($view, $parameters);
    }

    /**
     * Creates a new patient entity.
     *
     * @Route("/new", name="patient_new")
     */
    public function newAction(Request $request)
    {
        $patient = new Patient();

        $pPhone = new Phone();
        $patient->addPhone($pPhone);

        $allergy = new PatientMedicationAllergy();
        $patient->addAllergy($allergy);

        $phone = new Phone();
        $caregiver = new CareGiver();
        $caregiver->addPhone($phone);
        $patient->addCaregiver($caregiver);
        $patient->setUseCaregiver(false);

        $form = $this->createForm('DoctorBundle\Form\PatientType', $patient, array('entity_manager' => $this->get('doctrine.orm.entity_manager')));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $doctorId = $this->getDoctorId();
            $doctor   = $this->getDoctrine()->getRepository('UtilBundle:Doctor')->find($doctorId);
            $patient->setDoctor($doctor);
            $patient->setCreatedOn(new \DateTime());

            $ptRep = $this->getPatientRepository();
            $patientCode = $ptRep->generatePatientCode($patient);
            $patient->setPatientCode($patientCode);
            $patient->setGlobalId(1);

            if (!$request->get('knowMedication', 0)) {
                $patient->getAllergies()->clear();
            }

            if (false == $patient->getUseCaregiver()) {
                $patient->getCaregivers()->clear();
            }

            foreach ($patient->getAllergies() as $value) {
                if (empty($value->getMedicationAllergy())) {
                    $patient->getAllergies()->removeElement($value);
                }
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($patient);
            $em->flush();

            // logging
            $arr = array('module' => 'patients',
            'title'  =>'new_patient_created',
            'action' => 'create',
            'id'     => $patient->getId());
            $author = $this->getUser()->getDisplayName();
            Utils::saveLog(array(), array(), $author, $arr, $em);

            $otherDiagnosis = $request->request->get('other_values');
            $this->getPatientRepository()->updateJoinTables($patient, $otherDiagnosis);

            if ($request->get('andCreateRx')) {
                return $this->redirectToRoute('create_rx', array('patientId' => $patient->getId()));
            }

            $permissions = $this->getUser()->getPermissions();
            if(in_array('patient_index',$permissions )){
                return $this->redirectToRoute('patient_index');
            } else {
                return $this->redirectToRoute('doctor_dashboard');
            }
        }

        return $this->render('DoctorBundle:patient:new.html.twig', array(
            'patient' => $patient,
            'form' => $form->createView(),
            'andCreateRx' => true,
            'action' => 'new',
            'otherDiagnosisValues' => ''
        ));
    }

    /**
     * Displays a form to edit an existing patient entity.
     *
     * @Route("/edit/{id}", name="patient_edit")
     */
    public function editAction(Request $request, Patient $patient)
    {
        $oldData = $this->getPatientValue($patient);

        if ($patient->getPhones()->isEmpty()) {
            $pPhone = new Phone();
            $patient->addPhone($pPhone);
        }

        if ($patient->getCaregivers()->isEmpty()) {
            $phone = new Phone();
            $caregiver = new CareGiver();
            $caregiver->addPhone($phone);
            $patient->addCaregiver($caregiver);
        }

        $editForm = $this->createForm('DoctorBundle\Form\PatientType', $patient, array('entity_manager' => $this->get('doctrine.orm.entity_manager')));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            if (false == $patient->getUseCaregiver()) {
                $patient->getCaregivers()->clear();
            }

            if (!$request->get('knowMedication', 0)) {
                $patient->getAllergies()->clear();
            }

            foreach ($patient->getAllergies() as $value) {
                if (empty($value->getMedicationAllergy())) {
                    $patient->getAllergies()->removeElement($value);
                }
            }
            $patient->setUpdatedOn(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $otherDiagnosis = $request->request->get('other_values');
            $this->getPatientRepository()->updateJoinTables($patient, $otherDiagnosis);

            $newData = $this->getPatientValue($patient);
            $author = $this->getUser()->getDisplayName();
            $arr = array('module' => 'patients',
                        'title'  =>'patient_updated',
                        'id'     => $patient->getId());
            Utils::saveLog($oldData, $newData, $author, $arr, $em);

            return $this->redirectToRoute('patient_index');
        }

        $otherDiagnosis = $this->getPatientRepository()->getOtherDiagnosticValues($patient->getId());
        return $this->render('DoctorBundle:patient:new.html.twig', array(
            'patient' => $patient,
            'form' => $editForm->createView(),
            'action' => 'edit',
            'otherDiagnosisValues' => $otherDiagnosis
        ));
    }

    private function getDoctorId()
    {
        $user = $this->getUser();
        if ($user) {
            return $user->getId();
        }

        return 22;
    }

    private function getPatientRepository()
    {
        return $this->getDoctrine()->getRepository('UtilBundle:Patient');
    }

    private function getRXRepository()
    {
        return $this->getDoctrine()->getRepository('UtilBundle:Rx');
    }

    private function getPatientValue($patient)
    {
        $result = array();
        if (is_object($patient) && $patient->getId()) {
            $em = $this->getDoctrine()->getEntityManager();
            $countryList = $em->getRepository('UtilBundle:Country')->getListContry();
            $result['title'] = $patient->getPersonalInformation()->getTitle();
            $result['firstName'] = $patient->getPersonalInformation()->getFirstName();
            $result['lastName'] = $patient->getPersonalInformation()->getLastName();
            $result['gender'] = $patient->getPersonalInformation()->getGender() ? "Male" : "Female";
            $result['isAssessed'] = $patient->getIsAssessed();
            $result['isEnrolled'] = $patient->getIsEnrolled();
            $result['emailAddress'] = $patient->getPersonalInformation()->getEmailAddress();
            $patientPhoneObj = $patient->getPhones()->first();
            $patientPhone = array();
            $patientPhone[] = $patientPhoneObj->getCountry()->getName();
            $patientPhone[] = $patientPhoneObj->getCountry()->getPhoneCode();
            $patientPhone[] = $patientPhoneObj->getAreaCode();
            $patientPhone[] = $patientPhoneObj->getNumber();
            $result['phones'] = implode(', ', $patientPhone);
            $result['dateOfBirth'] = $patient->getPersonalInformation()->getDateOfBirth()->format('d-m-Y');
            $result['primaryResidenceCountry'] = $countryList[$patient->getPrimaryResidenceCountry()->getId()];
            $result['passportNo'] = $patient->getPersonalInformation()->getPassportNo();
            $result['issueCountry'] = $countryList[$patient->getIssueCountry()->getId()];
            $result['nationality'] = $countryList[$patient->getNationality()->getId()];
            $result['taxId'] = $result['nationality'] == "Indonesia" ? $patient->getTaxId() : "";
            $currentAllergies = array();
            $allergies = $patient->getAllergies();
            foreach ($allergies as $item) {
                array_push($currentAllergies, $item->getMedicationAllergy());
            }
            $result['allergies'] = implode(', ', $currentAllergies);
            $currentDiag = array();
            $diag = $patient->getDiagnosis();
            foreach ($diag as $itemD) {
                array_push($currentDiag, $itemD->getDiagnosis());
            }
            $result['diagnosis'] = implode(', ', $currentDiag);
            $result['useCaregiver'] = (bool)$patient->getUseCaregiver();
            $result['isSendMailToCaregiver'] = "";
            $result['caregiverRelationshipType'] = "";
            $result['caregiverTitle'] = "";
            $result['caregiverFirstName'] = "";
            $result['caregiverLastName'] = "";
            $result['caregiverEmailAddress'] = "";
            $result['caregiverPhone'] = "";
            if ($result['useCaregiver']) {
                $result['isSendMailToCaregiver'] = $patient->getIsSendMailToCaregiver();
                $caregiverPI = $patient->getCaregivers()->first()->getPersonalInformation();
                $caregiverPhoneObj = $patient->getCaregivers()->first()->getPhones()->first();
                $caregiverPhone = array(); 
                $caregiverPhone[] = $caregiverPhoneObj->getCountry()->getName();
                $caregiverPhone[] = $caregiverPhoneObj->getCountry()->getPhoneCode();
                $caregiverPhone[] = $caregiverPhoneObj->getAreaCode();
                $caregiverPhone[] = $caregiverPhoneObj->getNumber();
                $result['caregiverRelationshipType'] = $patient->getCaregivers()->first()->getRelationshipType()->getName();
                $result['caregiverTitle'] = $caregiverPI->getTitle();
                $result['caregiverFirstName'] = $caregiverPI->getFirstName();
                $result['caregiverLastName'] = $caregiverPI->getLastName();
                $result['caregiverEmailAddress'] = $caregiverPI->getEmailAddress();
                $result['caregiverPhone'] = implode(', ', $caregiverPhone);
            }
        }
        return $result;
    }
}