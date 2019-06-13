<?php

namespace DoctorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use UtilBundle\Entity\Rx;
use UtilBundle\Entity\Issue;
use UtilBundle\Entity\RxCounter;
use UtilBundle\Entity\RxNote;
use UtilBundle\Entity\RxLineAmendment;
use UtilBundle\Entity\RxStatusLog;
use UtilBundle\Utility\Constant;
use UtilBundle\Utility\Common;
use Dompdf\Dompdf;
use UtilBundle\Utility\Utils;

class RxController extends Controller
{
    /**
     * @Route("/rx", name="index_rx")
     */
    public function indexAction()
    {
        return $this->render('DoctorBundle:rx:index.html.twig');
    }

    /**
     * @Route("/rx/ajax-get-list-patient", name="ajax_get_list_patient")
     */
    public function ajaxGetListPatientAction(Request $request)
    {
        $rxRepository = $this->getRXRepository();

        $params = array(
            'doctorId' => $this->getDoctorId(),
            'query' => trim($request->get('term'))
        );
        $params['baseUrl'] = $this->generateUrl('create_rx');
        $list = $rxRepository->getListPatientOfDoctor($params);

        return new JsonResponse($list);
    }

    /**
     * @Route("/rx/create/{patientId}", name="create_rx")
     */
    public function createAction(Request $request, $patientId = 0)
    {
        $rxRepository = $this->getRXRepository();

        $patientRepository = $this->getDoctrine()->getRepository('UtilBundle:Patient');
        $params = array(
            'doctorId' => $this->getDoctorId(),
            'patientId' => $patientId,
            'otherDiagnosisValues' => $patientRepository->getOtherDiagnosticValues($patientId)
        );

        if (false == $rxRepository->isExistsPatient($params)) {
            throw $this->createAccessDeniedException();
        }

        $parameters = array();
        $parameters['patientInformation'] = $rxRepository->getPatientInformation($params);
        $parameters['dateTabs'] = $rxRepository->getListDateTab($params);
        $parameters['patientId'] = $patientId;
        $parameters['orderNumber'] = $rxRepository->generateOrderNumber($params);
        $parameters['proformaInvoiceNo'] = $rxRepository->generateProformaInvoiceNo($params);
        $parameters['doctorFee'] = $rxRepository->getDoctorFee($params);
        $parameters['isCreate'] = 'true';
        $parameters['rxData'] = new Rx();
        $gmedUser = $this->getUser();
        $parameters['showSaveAsDraft'] = true;
        $parameters['showConfirmRx'] = true;
        $parameters['activeDoctor'] = true;

        $roles = $gmedUser->getRoles();
        if (in_array(Constant::TYPE_MPA, $roles)) {
            $parameters['showConfirmRx'] = false;
            $parameters['showForwardRx'] = true;
            if ($gmedUser->hasPermission('send_to_patient')) {
                $parameters['showConfirmRx'] = true;
                $parameters['showForwardRx'] = false;


            }
            $doctorId = $gmedUser->getId();
            $doctor =  $this->getDoctrine()->getRepository('UtilBundle:Doctor')->find($doctorId);
            if(empty($doctor->getSignatureUrl())){
                $parameters['activeDoctor'] = false;
            }
        }

        return $this->render('DoctorBundle:rx:create.html.twig', $parameters);
    }

     /**
     * @Route("/rx/ajax-get-check-stock", name="ajax_get_check_stock")
     */
    public function ajaxGetCheckStockAction(Request $request)
    {
        $drugsText = $request->get('drugIds', '');
        $drugs = explode(',',$drugsText);
        $drugRepos = $this->getDoctrine()->getRepository('UtilBundle:Drug');
        $stockDrug = [];
        foreach ($drugs as $dr) {
            $drug = $drugRepos->find($dr);
            if(!empty($drug)){
                if(!empty($drug->getStockStatus()) && $drug->getStockStatus()->getName() == "Stock when Ordered"){
                    $stockDrug[] =  $drug->getName();
                }
            }
        }
        return  new JsonResponse(['status'=> true, 'data' => $stockDrug, 'total' => count($stockDrug) ]);
    }

    /**
     * @Route("/rx/ajax-get-rx-drug/{rxId}", name="ajax_get_rx_drug")
     */
    public function ajaxGetRXDrugAction(Request $request, $rxId)
    {
        $rxRepository = $this->getRXRepository();

        $params = array(
            'rxId' => $rxId,
            'sorting' => $request->get('sorting'),
            'doctorId' => $this->getDoctorId(),
            'patientId' => $request->get('patientId')
        );

        $isLocalPatient = $rxRepository->isLocalPatient($params);
        $params['isLocalPatient'] = $isLocalPatient;

        $list = $rxRepository->getRXDrug($params);

        $parameters = array(
            'drugs' => $list
        );
        $parameters['isLocalPatient'] = $isLocalPatient;
        $content = $this->renderView('DoctorBundle:rx:_list-rx-drug.html.twig', $parameters);

        return new Response($content);
    }

    /**
     * @Route("/rx/ajax-get-drug", name="ajax_get_drug")
     */
    public function ajaxGetDrugAction(Request $request)
    {
        $rxRepository = $this->getRXRepository();

        $params = array(
            'query' => $request->get('query'),
            'orderBy' => $request->get('orderBy'),
            'patientId' => $request->get('patientId'),
            'sorting' => $request->get('sorting'),
            'doctorId' => $this->getDoctorId()
        );

        $isLocalPatient = $rxRepository->isLocalPatient($params);
        $params['isLocalPatient'] = $isLocalPatient;

        $list = $rxRepository->getDrug($params);

        $parameters = array(
            'drugs' => $list
        );
        $parameters['isLocalPatient'] = $isLocalPatient;
        $content = $this->renderView('DoctorBundle:rx:_list-drug.html.twig', $parameters);

        return new Response($content);
    }

    /**
     * @Route("/rx/ajax-get-top30", name="ajax_get_top30")
     */
    public function ajaxGetTop30Action(Request $request)
    {
        $rxRepository = $this->getRXRepository();

        $params = array(
            'doctorId'  => $this->getDoctorId(),
            'patientId' => $request->get('patientId'),
            'sorting'   => $request->get('sorting')
        );

        $isLocalPatient = $rxRepository->isLocalPatient($params);
        $params['isLocalPatient'] = $isLocalPatient;

        $list = $rxRepository->getTop30($params);

        $parameters = array();
        $parameters['top30'] = $list;
        $parameters['isLocalPatient'] = $isLocalPatient;
        $content = $this->renderView('DoctorBundle:rx:_top30.html.twig', $parameters);

        return new Response($content);

    }

    /**
     * @Route("/rx/ajax-get-favorites", name="ajax_get_favorites")
     */
    public function ajaxGetFavoritesAction(Request $request)
    {
        $rxRepository = $this->getRXRepository();

        $params = array(
            'doctorId'  => $this->getDoctorId(),
            'patientId' => $request->get('patientId'),
            'sorting'   => $request->get('sorting')
        );

        $isLocalPatient = $rxRepository->isLocalPatient($params);
        $params['isLocalPatient'] = $isLocalPatient;

        $list = $rxRepository->getFavorites($params);

        $parameters = array();
        $parameters['drugs'] = $list;
        $parameters['isLocalPatient'] = $isLocalPatient;
        $content = $this->renderView('DoctorBundle:rx:_list-favorites.html.twig', $parameters);

        return new Response($content);
    }

    /**
     * @Route("/rx/ajax-handle-favorite/{drugId}", name="ajax_handle_favorite")
     */
    public function handleFavoriteAction(Request $request, $drugId)
    {
        $rxRepository = $this->getRXRepository();

        $params = array(
            'doctorId' => $this->getDoctorId(),
            'drugId' => $drugId
        );

        $data = $rxRepository->handleFavoriteDrug($params);

        return new JsonResponse($data);
    }

    /**
     * @Route("/rx/ajax-get-step2-content", name="ajax_get_step2_content")
     */
    public function ajaxGetStep2ContentAction(Request $request)
    {
        $rxRepository = $this->getRXRepository();

        $rxDrugs = $request->get('rxDrugIds');
        $drugs = $request->get('drugIds');
        $patientId = $request->get('patientId');
        if (strpos($patientId, $prefix = Constant::HASHING_PREFIX) !== false)
            $patientId = Common::decodeHex($patientId);

        $params = array(
            'rxDrugIds' => $rxDrugs,
            'drugIds'   => $drugs,
            'patientId' => $patientId,
            'doctorId'  => $this->getDoctorId()
        );
        $params['isLocalPatient'] = $rxRepository->isLocalPatient($params);

        $list = $rxRepository->getStep2Data($params);

        $actions = $rxRepository->getListAction();
        $doseUnits = $rxRepository->getListDoseUnit();
        $durationUnits = $rxRepository->getListDurationUnit();
        $rxData = $rxRepository->getRXInformation($params);

        $parameters = array(
            'actions' => $actions,
            'doseUnits' => $doseUnits,
            'durationUnits' => $durationUnits,
            'drugs' => $list,
            'rxData' => $rxData
        );
        $content = $this->renderView('DoctorBundle:rx:_step2.html.twig', $parameters);

        return new Response($content);
    }

    /**
     * @Route("/rx/ajax-save-update-rx", name="ajax_save_update_rx")
     */
    public function ajaxSaveUpdateAction(Request $request)
    {
        $rxRepository = $this->getRXRepository();

        $params = $request->request->all();
        $params['doctorId'] = $this->getDoctorId();
        $params['patientId'] = $request->get('patientId');
        $em = $this->getDoctrine()->getManager();
        if (false == $rxRepository->isExistsPatient($params)) {
            throw $this->createAccessDeniedException();
        }
        $template = 'DoctorBundle:message:rx_issue_update.html.twig';
        $messageContent = $this->renderView($template);
        $result = $rxRepository->UpdateRX($params, $messageContent);


        $rx = $result['data'];
        $messages = $result['message'];
        $counter = $rx->getRxCounter();
        if(empty($counter)||  count($counter) == 0){
            $rxCounter = new RxCounter();
            $rxCounter->setIsPharmacyRead(0);
            $rxCounter->setIsCustomerCareRead(0);
            $rx->addRxCounter($rxCounter);
        } else {
            $rxCounter = $counter->first();
            $rxCounter->setIsPharmacyRead(0);
            $rxCounter->setIsCustomerCareRead(0);
        }
        $em->persist($rx);
        $em->flush();
        $mes = $em->getRepository('UtilBundle:Message')->find($params['mesageId']);
        $mes->setReadDate(new \DateTime('now'));
        $em->persist($mes);
        $em->flush();
        if($result['success']){
            $emailTarget = Common::getTargetEmail($em, Constant::MESSAGE_GROUP_CUSTOMER_CARE, 'doctor');
            //sender
            $gmedUser = $this->getUser()->getLoggedUser();
            $sender = $em->getRepository('UtilBundle:User')->find($gmedUser->getId());
            //content
            $body = 'Dear Customer Care, <br/><br/>';
            $body .= 'Doctor '.$rx->getDoctor()->getPersonalInformation()->getFullName().' updated Order #'.$rx->getOrderNumber().'. <br/>';
            $body .= 'Changes: <br/>';
            foreach ($messages as $key => $val){
                $body .= '<b>'.$key.'</b><br/>';
                $body .= '<blockquote style="margin: 0 0 0 40px; border: none; padding: 0px;">Old value</blockquote><br/>';
                $body .= '<blockquote style="margin: 0 0 0 40px; border: none; padding: 0px;"><blockquote style="margin: 0 0 0 40px; border: none; padding: 0px;">'.$val['old'].'</blockquote></blockquote><br/>';
                $body .= '<blockquote style="margin: 0 0 0 40px; border: none; padding: 0px;">New value</blockquote><br/>';
                $body .= '<blockquote style="margin: 0 0 0 40px; border: none; padding: 0px;"><blockquote style="margin: 0 0 0 40px; border: none; padding: 0px;">'.$val['new'].'</blockquote></blockquote><br/>';
            }
            $body .= 'Thank you.<br/>'.'This is a system generated email. Please do not reply.';
            $contentData = array(
                'subject' => 'Order '.$rx->getOrderNumber().' was updated by the Doctor',
                'body' =>$body
            );
            $content = $em->getRepository('UtilBundle:MessageContent')->create($contentData);
            if(!empty($emailTarget)) {
                //get display name of sender
                $roles = [];
                foreach ($sender->getRoles() as $role) {
                    $roles[] = $role->getName();
                }

                if($roles[0] == Constant::TYPE_DOCTOR_NAME
                    || $roles[0] == Constant::TYPE_AGENT_NAME
                    || $roles[0] == Constant::TYPE_SUB_AGENT_NAME) {
                    $dataName = $em->getRepository('UtilBundle:User')->getFullNameById([
                        'role' => $roles[0],
                        'userId' => $sender->getId(),
                    ]);
                    $senderName = $dataName['fullName'];
                    if(!empty($dataName['ucode']))
                        $senderName .= " (".$dataName['ucode'].")";
                } else {
                    $senderName = $sender->getFirstName()." ".$sender->getLastName();
                }

                foreach ($emailTarget as $item) {
                    $receiver = $em->getRepository('UtilBundle:User')->findOneBy(array('emailAddress'=>$item['email']));
                    if($receiver) {

                        //get display name of receiver
                        $receiverRoles = [];
                        foreach ($receiver->getRoles() as $role) {
                            $receiverRoles[] = $role->getName();
                        }

                        if($receiverRoles[0] == Constant::TYPE_DOCTOR_NAME
                            || $receiverRoles[0] == Constant::TYPE_AGENT_NAME
                            || $receiverRoles[0] == Constant::TYPE_SUB_AGENT_NAME) {
                            $dataName = $em->getRepository('UtilBundle:User')->getFullNameById([
                                'role' => $receiverRoles[0],
                                'userId' => $receiver->getId(),
                            ]);
                            $receiverName = $dataName['fullName'];
                            if(!empty($dataName['ucode']))
                                $receiverName .= " (".$dataName['ucode'].")";
                        } else {
                            $receiverName = $receiver->getFirstName()." ".$receiver->getLastName();
                        }

                        $messageData = array(
                            'content'       => $content,
                            'sender'        => $sender,
                            'senderName'    => $senderName,
                            'senderEmail'   => $sender->getEmailAddress(),
                            'receiver'      => $receiver,
                            'receiverName'  => $receiverName,
                            'receiverEmail' => $receiver->getEmailAddress(),
                            'receiverType'  => 0,
                            'receiverGroup' => isset($item['receiverGroup'])? $item['receiverGroup']: null,
                            'status'        => Constant::MESSAGE_INBOX,
                            'sentDate'      => new \DateTime(),
                        );

                        $em->getRepository('UtilBundle:Message')->create($messageData,[]);
                    }

                }

            }

        }

        $response = array(
            'data' => $this->generateUrl('list_rx'),
            'success' => $result['success']
        );

        return new JsonResponse($response);
    }


    /**
     * @Route("/rx/ajax-save-as-draft", name="ajax_save_as_draft")
     */
    public function ajaxSaveAsDraftAction(Request $request)
    {
        $rxRepository = $this->getRXRepository();

        $params = $request->request->all();
        $params['doctorId'] = $this->getDoctorId();
        $params['patientId'] = $request->get('patientId');

        if (false == $rxRepository->isExistsPatient($params)) {
            throw $this->createAccessDeniedException();
        }

        $params['status'] = Constant::RX_STATUS_DRAFT;
        $params['saveAsDraff'] = true;

        $result = $rxRepository->createRX($params);

        $data = isset($result['data']) ? $result['data'] : null;

        $get_type = $request->get('get_type','');
        if ($get_type == 'autosave') {
            $response = array(
                'data' => $this->generateUrl('list_rx', array('save-success-draft' => 1), true),
                'rx_id' => $data ? $data->getId() : ''
            );
        } else {
            // Update status log
            $params = array('rxObj' => $data);
            if ($request->get('rxId')) {
                $params['isEdit'] = true;
            }

            $gmedUser = $this->getUser();
            if (in_array(Constant::TYPE_MPA, $gmedUser->getRoles())) {
                $params['createdBy'] = $gmedUser->getDisplayName();
            }
            $rxRepository->manageRXStatusLog($params);
            
            $response = array(
                'data' => $this->generateUrl('list_rx', array('save-success-draft' => 1), true),
                'rx_id' => $data ? $data->getId() : ''
            );
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/rx/review", name="review_rx")
     */
    public function ajaxReviewAction(Request $request)
    {
        $rxRepository = $this->getRXRepository();

        $params = $request->request->all();
        $params['doctorId'] = $this->getDoctorId();

        $parameters = array();
        $parameters['list'] = $rxRepository->formatDrugs($params);
        $parameters['rxData'] = $rxRepository->getRXInformation($params);
        $parameters['reviewFee'] = $rxRepository->getDoctorFee($params);

        $browser = "";
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $agent = $_SERVER['HTTP_USER_AGENT'];
            if (strlen(strstr($agent, 'Firefox')) > 0) {
                $browser = 'firefox';
            }
        }
        $parameters['browser'] = $browser;
        $parameters['showProforma'] = empty($params['rxId'])? true: false;
        if (!empty($params['rxId'])) {
            $rx = $rxRepository->find($params['rxId']);
            if (Constant::RX_STATUS_DRAFT == $rx->getStatus() || Constant::RX_STATUS_FOR_DOCTOR_REVIEW == $rx->getStatus() || Constant::RX_STATUS_FOR_AMENDMENT == $rx->getStatus()) {
                $parameters['showProforma'] = true;
            }
        }

        return $this->render('DoctorBundle:rx:_review.html.twig', $parameters);
    }

    /**
     * determine whether creating a issue from editting rx or not
     * @param  array $params
     * @author  thu.tranq
     * @return boolean
     */
    public function isAllowedToCreateIssue($params) {

        if (isset($params['rxId']) and !empty($params['rxId'])) {
            $rxId         = $params['rxId'];
            $rxRepository = $this->getRXRepository();
            $rx           = $rxRepository->find($rxId);

            if( $rx->getIsOnHold() == 1) {
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * @Route("/rx/confirm", name="confirm_rx")
     */
    public function confirmRXAction(Request $request)
    {
        $rxRepository = $this->getRXRepository();

        $params = $request->request->all();
        $params['doctorId'] = $this->getDoctorId();

        if (false == $rxRepository->isExistsPatient($params)) {
            throw $this->createAccessDeniedException();
        }

        $oldValue = array();
        $newValue = array();
        $params['isPreviouslyOnHold'] = false;
        $rxId = isset($params['rxId']) ? $params['rxId'] : null;
        if ($rxId) {
            $oldRxLineObj = $rxRepository->find($rxId)->getRxLines();

            if ($oldRxLineObj) {
                $params['isPreviouslyOnHold'] = $oldRxLineObj[0]->getRx()->getIsOnHold();
                foreach ($oldRxLineObj as $rxLine) {
                    if (!$rxLine->getDrug()) {
                        continue;
                    }

                    $rxDrug = $rxRepository->formatRXDrugData($rxLine);
                    $oldRxValue = array(
                        'drugName' => $rxLine->getDrug()->getName(),
                        'qty' => $rxDrug['qty'] . ' ' . $rxDrug['packingType'],
                        'sig_preview' => $rxDrug['sig'] . '<br />'
                    );
                    if ($rxDrug['instructions']) {
                        $oldRxValue['sig_preview'] .= $rxDrug['instructions'] . '<br />';
                    }
                    if ($rxDrug['drowsiness']) {
                        $oldRxValue['sig_preview'] .= $rxDrug['drowsiness'] . '<br />';
                    }
                    if ($rxDrug['complete']) {
                        $oldRxValue['sig_preview'] .= $rxDrug['complete'] . '<br />';
                    }

                    $oldValue[] = $oldRxValue;
                }
            }
        }

        // STRIKE 455
        $this->updateMessageContent($params);

        $detectMadeIssue = $this->isAllowedToCreateIssue($params);
        if ($detectMadeIssue == true) {
            $params['addIssue'] = true;
        }

        $params['status'] = Constant::RX_STATUS_PENDING;

        $user = $this->getUser()->getDisplayName();
        $params['displayName'] = $user;

        $params['platformShareFlag'] = $this->getParameter('platform_share_fee');
        $result = $rxRepository->createRX($params);

        if (empty($result['success'])) {
            throw $this->createAccessDeniedException();
        }

        $data = isset($result['data']) ? $result['data'] : null;

        $newRxLines = $data->getRxLines();
        if ($newRxLines) {
            foreach ($newRxLines as $rxLine) {
                if (!$rxLine->getDrug()) {
                    continue;
                }

                $rxDrug = $rxRepository->formatRXDrugData($rxLine);
                $newRxValue = array(
                    'drugName' => $rxLine->getDrug()->getName(),
                    'qty' => $rxDrug['qty'] . ' ' . $rxDrug['packingType'],
                    'sig_preview' => $rxDrug['sig'] . '<br />'
                );
                if ($rxDrug['instructions']) {
                    $newRxValue['sig_preview'] .= $rxDrug['instructions'] . '<br />';
                }
                if ($rxDrug['drowsiness']) {
                    $newRxValue['sig_preview'] .= $rxDrug['drowsiness'] . '<br />';
                }
                if ($rxDrug['complete']) {
                    $newRxValue['sig_preview'] .= $rxDrug['complete'] . '<br />';
                }

                $newValue[] = $newRxValue;
            }
        }

        $params['rxObj'] = $data;
        $params['isConfirmed'] = true;

        // Update status log
        $params['logData'] = array(
            'old' => $oldValue,
            'new' => $newValue
        );
        $rxRepository->manageRXStatusLog($params);

        // Send email to patient
        $orderNumber = $data->getOrderNumber();
        $params['orderNumber'] = $orderNumber;

        if ($params['isScheduledRx'] == false) {
            $sent = $this->sendToPatient($params);
        } else {
            $sent = $this->sendFutureRxOrderNotification($params);
        }

        if ($sent) {
            try {
                // strike 388: add issue resolution when editting a recalled rx
                if ($detectMadeIssue == true) {
                    $issue = new Issue();
                    $issue->setRemarks(Constant::ISSUE_MSG_FOR_RECALLED_RX);
					$issue->setIssueType(Constant::DOCTOR_ROLE);
                    $issue->setCreatedOn(new \DateTime('now'));
                    $issue->setUpdatedOn(new \DateTime('now'));
                    $issue->setIsResolution(true);

                    $issue->setUpdatedBy($user);
                    $issue->setCreatedBy($user);
                    $data->addIssue($issue);

                    //Notification via email: STRIKE-482
                    $rxRepository = $this->getRXRepository();
                    $patientInformation = $rxRepository->getPatientInformation($params);
                    $doctorInformation = $rxRepository->getDoctorInformation($params);
                    $notify = array(
                        'orderNumber' => $params['orderNumber'],
                        'notifyText'  => 'resolved',
                        'note'        => 'Doctor amended RX order. Patient accepted and paid',
                        'doctorName'  => $doctorInformation['name'],
                        'doctorCode'  => $doctorInformation['doctorCode'],
                        'patientName' => $patientInformation['name'],
                        'patientCode' => $patientInformation['patientCode'],
                        'baseUrl'     => $this->container->getParameter('base_url'),
                        'logoUrl'     => $this->container->getParameter('base_url').'/bundles/admin/assets/pages/img/logo.png'
                    );
                    $notifyBody = $this->renderView('DoctorBundle:emails:doctor-notification.html.twig', $notify);
                    $notifyDataSendMail = array(
                        'title'  => "Updates to RX Order ".$params['orderNumber'],
                        'body'   => $notifyBody,
                        'from'   => $this->container->getParameter('primary_email'),
                        'to'     => $this->container->getParameter('askpharmacist_email')
                    );
                    $this->container->get('microservices.sendgrid.email')->sendEmail($notifyDataSendMail);

                    //update rx counter
                    if (isset($params['rxObj']) && $params['rxObj'] != null) {
                        $rxObj = $params['rxObj'];
                        $counter = $rxObj->getRxCounter();
                        if(empty($counter)||  count($counter) == 0){
                            $rxCounter = new RxCounter();
                            $rxCounter->setIsDoctorRead(1);
                            $rxObj->addRxCounter($rxCounter);
                        } else {
                            $rxCounter = $counter->first();
                            $rxCounter->setIsDoctorRead(1);
                        }

                        $em = $this->getDoctrine()->getManager();
                        $em->persist($rxObj);
                        $em->flush();
                    }
                }

                $data->setSentOn(new \DateTime());

                $em = $this->getDoctrine()->getManager();
                $em->persist($data);
                $em->flush();
            } catch(\Exception $ex) {
            }
        }

        $parameters = array(
            'order-id' => $orderNumber
        );
        return $this->redirectToRoute('doctor_dashboard', $parameters);
    }

    public function sendFutureRxOrderNotification($params)
    {
        $rxObj = isset($params['rxObj']) ? $params['rxObj'] : null;
        if (null == $rxObj) {
            return false;
        }

        $patientObj = $rxObj->getPatient();
        if (null == $patientObj) {
            return false;
        }

        if ($patientObj->getHasFutureRx()) {
            return true;
        } else {
            $patientObj->setHasFutureRx(1);
            $this->getDoctrine()->getManager()->persist($patientObj);
            $this->getDoctrine()->getManager()->flush();
        }

        $arrPhone = array();
        $arrTo = array();
        if ($patientObj->getUseCaregiver() && $patientObj->getIsSendMailToCaregiver()) {
            $caregiver = $patientObj->getCaregivers()->first();
            if ($caregiver) {
                $arrTo[] = $caregiver->getPersonalInformation()->getEmailAddress();
                $arrPhone[] = $caregiver->getPhones()->first();
            }
        }
        if (empty($arrTo)) {
            $arrTo[] = $patientObj->getPersonalInformation()->getEmailAddress();
        }
        $arrTo = array_unique($arrTo);

        // load notification template
        $key = Constant::CODE_FUTURE_RX_ORDER;
        $rxNotificationSetting = $this->getDoctrine()->getManager()->getRepository('UtilBundle:RxReminderSetting')->find($key);

        $rxRepository = $this->getRXRepository();

        $mailTemplate = "";
        $SMSTemplate = "";
        if ($rxNotificationSetting) {
            $mailSubject = $rxNotificationSetting->getTemplateSubjectEmail();
            $mailTemplate = $rxNotificationSetting->getTemplateBodyEmail();
            $SMSTemplate = $rxNotificationSetting->getTemplateSms();
            $mailTemplate = $rxRepository->replaceTemplateData($this->container, $rxObj, $rxNotificationSetting, $mailTemplate, true, false, true);
            $SMSTemplate = $rxRepository->replaceTemplateData($this->container, $rxObj, $rxNotificationSetting, $SMSTemplate, false, false);
        }

        $parameters['patientInformation'] = $rxRepository->getPatientInformation($params);
        $parameters['clinicInformation'] = $rxRepository->getClinicInformation($params);
        $parameters['doctorInformation'] = $rxRepository->getDoctorInformation($params);
        $parameters['orderNumber'] = $params['orderNumber'];
        $parameters['notification_email_content'] = $mailTemplate;
        $parameters['baseUrl'] = $this->container->getParameter('base_url');
        $body  = $this->renderView('DoctorBundle:emails:rx-order-notification.html.twig', $parameters);

        $mailParams = array(
            'title' => isset($mailSubject) ? $mailSubject : 'Welcome to G-MEDS!',
            'body'  => $body,
            'to'    => $arrTo
        );

        $mailSend = $this->container->get('microservices.sendgrid.email')->sendEmail($mailParams);

        // send SMS
        $message = strip_tags($SMSTemplate);

        // STRIKE-698
        if (empty($arrPhone)) {
            $arrPhone[] = $patientObj->getPhones()->first();
        }

        $smsSend = null;
        foreach ($arrPhone as $phone) {
            if (empty($phone)) {
                continue;
            }

            $arrPN = array('+');
            $country = $phone->getCountry();
            if ($country) {
                $arrPN[] = $country->getPhoneCode();
            }
            $arrPN[] = $phone->getAreaCode();
            $arrPN[] = $phone->getNumber();
            $phoneNumber = implode('', $arrPN);

            if (empty($phoneNumber) || empty($message)) {
                continue;
            }

            $smsSend = $this->container->get('microservices.sms')->sendMessage(array(
                'to'      => $phoneNumber,
                'message' => $message
            ));
        }

        if ($smsSend && $mailSend) {
            return true;
        }

        return false;
    }

    /**
     * @Route("/rx/pdf/{rxId}", name="pdf_rx")
     */
    public function pdfAction(Request $request, $rxId = 0)
    {
        // STRIKE-964
        $user = $this->getUser();
        if ($user) {
            if ($user->getId() != $this->getDoctorId($rxId)) {
                throw $this->createAccessDeniedException();
            }
        } else {
            $deniedTemplate = $this->renderView('AdminBundle:error:403.html.twig',[ ]);
            Common::restrictFileAccess($this->container, $deniedTemplate);
        }
        // End

        $params = $request->request->all();
		$params['hasWatermark'] = $request->get('watermark', false);
        $params['isInvoice'] = $request->get('is-invoice', false);
        $params['isProforma'] = $request->get('is-proforma', false);
        $params['copy'] = $request->get('copy', 0);
        $params['isInvoicePaid'] = Constant::ZERO_NUMBER;
        if ($params['isInvoice']) {
            $params['isInvoicePaid'] = Constant::ONE_NUMBER;
        }
        $params['rxRepository'] = $this->getRXRepository();
        $params['doctorId'] = $this->getDoctorId($rxId);
        $params['em'] = $this->getDoctrine()->getManager();

        return $this->exportPdf($params, $rxId);
    }

    /**
     * @Route("/rx/pdf-proforma/{rxId}", name="pdf_proforma_rx")
     */
    public function pdfProformaAction(Request $request, $rxId = 0)
    {
        $params = $request->request->all();
        $params['hasWatermark'] = $request->get('watermark', false);
        $params['isInvoice'] = $request->get('is-invoice', false);
        $params['isProforma'] = $request->get('is-proforma', false);
        $params['copy'] = $request->get('copy', 0);
        $params['isInvoicePaid'] = Constant::ZERO_NUMBER;
        if ($params['isInvoice']) {
            $params['isInvoicePaid'] = Constant::ONE_NUMBER;
        }
        $params['rxRepository'] = $this->getRXRepository();
        $params['doctorId'] = $this->getDoctorId($rxId);
        return $this->exportPdf($params, $rxId);
    }

    private function processAddress($rx, $em)
    {
        $address = null;
        $shippingAdress = $rx->getShippingAddress();
        if ($shippingAdress) {
            $address = ucwords(strtolower($shippingAdress->getLine1()));
            
            if ($shippingAdress->getLine2()) {
                $address .= ', '.ucwords(strtolower($shippingAdress->getLine2()));
            }
            
            if ($shippingAdress->getLine3()) {
                $address .= ', '.ucwords(strtolower($shippingAdress->getLine3()));
            }
            
            if ($shippingAdress->getArea()) {
                $address .= ', '.ucwords(strtolower($shippingAdress->getArea()->getArea()));
            }
            
            $city = $shippingAdress->getCity();
            if ($city) {
                $country = $city->getCountry();
                if (Constant::ID_SINGAPORE != $country->getId()) {
                    if ($city->getName()) {
                        $address .= ', '.ucwords(strtolower($city->getName()));
                    }
                    
                    if ($city->getState()) {
                        $address .= ', '.ucwords(strtolower($city->getState()->getName()));
                    }
                }
                
                if ($country->getName()) {
                    $address .= ', '.ucwords(strtolower($country->getName()));
                }
            }
            
            
            if ($shippingAdress->getPostalCode()) {
                $address .= ', '.$shippingAdress->getPostalCode();
            }
        }
        
        return $address;
    }
    
    /**
     * Export Pdf file
     * @param type $paramsRequest
     * @param type $rxId
     * @return Response
     */
    public function exportPdf($paramsRequest, $rxId) {
        $rxRepository = $paramsRequest['rxRepository'];
        $params = $paramsRequest;
        $em = $params['em'];
       if ($rxId) {
            $params = $rxRepository->convertRXDataPaid($rxId);
            $parameters['drugs'] = $rxRepository->formatDrugsPaid($params);
        } else {
            $parameters['drugs'] = $rxRepository->formatDrugs($params);
        }

        $params['doctorId'] = $paramsRequest['doctorId'];
        $params['isInvoice'] = $paramsRequest['isInvoice'];
        $params['isProforma'] = isset($paramsRequest['isProforma']) ? $paramsRequest['isProforma'] : false;
        $params['isInvoicePaid'] = $paramsRequest['isInvoicePaid'];
        $parameters['hasWatermark'] = isset($paramsRequest['hasWatermark']) ? $paramsRequest['hasWatermark'] : false;
        $parameters['patientInformation'] = $rxRepository->getPatientInformation($params);
        $parameters['clinicInformation'] = $rxRepository->getClinicInformation($params);
        $parameters['doctorInformation'] = $rxRepository->getDoctorInformation($params);
        $parameters['rxInformation'] = $rxRepository->getRXInformation($params);        
        $parameters['doctorFee'] = $rxRepository->getDoctorFee($params);
		$parameters['status'] = isset($params['rx']) && $params['rx'] ? $params['rx']->getStatus() : 1;
        $parameters['shippingAddress'] = isset($params['rx']) && $params['rx'] ? $this->processAddress($params['rx'], $em) : null;

        $arrTotal = array('doctorFee' => $parameters['doctorFee']) + array('drugs' => $parameters['drugs']);
        $parameters['orderTotal'] = $rxRepository->getOrderTotal($arrTotal);

        $fileName = 'PrescriptionAdvice.pdf';
        $template = 'DoctorBundle:rx:_review-rx-advice.html.twig';
        if ($params['isInvoice']) {
            $parameters['isInvoicePaid'] = $params['isInvoicePaid'];
            $template = 'DoctorBundle:rx:_review-rx-invoice.html.twig';
        } elseif($params['isProforma']) {
            $parameters['isInvoicePaid'] = $params['isInvoicePaid'];
            $fileName = 'ProformaInvoice.pdf';
            $template = 'DoctorBundle:rx:_review-rx-proforma-invoice.html.twig';
        }

        // STRIKE 705
        $parameters['hasZrsGST'] = false;
        $isLocalPatient = $em->getRepository('UtilBundle:Rx')
                ->isLocalPatient(array('patientId' => $params['patientId']));
        if (!$isLocalPatient) {
            $doctorGstSetting = $em->getRepository('UtilBundle:DoctorGstSetting')->findOneBy(array(
                    'doctor' => $params['doctorId'],
                    'feeType' => Constant::SETTING_GST_MEDICINE,
                    'area' => 'overseas'
                ));

            if ($doctorGstSetting) {
                $gst = $doctorGstSetting->getGst();
                $parameters['hasZrsGST'] = $gst && $gst->getCode() == Constant::GST_ZRS ? true : false;
            }
        }
        // End STRIKE 705

        if ($params['isInvoicePaid']) {
            $rx = $params['rx'];

            $countryEnity = $rx->getShippingAddress()->getCity()->getCountry();
            $countryCode = $countryEnity->getCode();

            if ($countryCode == Constant::INDONESIA_CODE) {
                $parameters['country'] = Constant::INDONESIA_DISPLAY;
                if(isset($parameters['patientInformation']['taxId'])) {
                    $parameters['taxIdFormat'] = Utils::formatTaxId($parameters['patientInformation']['taxId']);
                }
                $parameters['isIndo'] = true;
            } else {
                $parameters['country'] = $countryCode;
            }

            $parameters['shippingList'] = $rx->getShippingList();
            $parameters['ccAdminFee'] = $rx->getCustomsClearancePlatformFee();
            $parameters['igPermitFee'] = $rx->getIgPermitFee();
            $parameters['feeGst'] = $rx->getFeeGst();
            $parameters['deliveryTime'] = $rx->getEstimatedDeliveryTimeline();
            $parameters['totalBeforeGst'] = $parameters['orderTotal']['subTotalMedication']
                    + $parameters['orderTotal']['subTotalService']
                    + $parameters['shippingList']
                    + $parameters['ccAdminFee']
                    + $parameters['igPermitFee'];
            $parameters['total'] = $rx->getOrderValue();
            $parameters['importTaxFee'] = $rx->getCustomsTax();
            $parameters['isInvoicePaid'] = $params['isInvoicePaid'];
            $template = 'DoctorBundle:rx:_review-rx-invoice.html.twig';
            if (empty($params['isInvoice'])) {
                return array('template' => $template, 'parameters' => $parameters);
            }
        }

        $copy = isset($paramsRequest['copy']) ? $paramsRequest['copy'] : 0;
        $parameters['copy'] = ($copy > 0) ? ($copy - 1) : 0;

        $html = $this->renderView($template, $parameters);

        $dompdf = new Dompdf(array('isPhpEnabled' => true));

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        if ($params['isInvoice']) {
            $text = "Invoice No: " . $parameters['rxInformation']['taxInvoiceNo'];
            if ($parameters['doctorInformation']['hasTax']) {
                $text = "Tax " . $text;
            }
            $GLOBALS['text']  =  $text;
            $GLOBALS['text1'] = "Date: " . $parameters['rxInformation']['date'];

            $canvas = $dompdf->get_canvas();
            $canvas->page_script('
                if ($PAGE_NUM > 1) {
                    $font = $fontMetrics->getFont("sans-serif");
                    $pdf->text(15, 26, $GLOBALS["text"], $font, 9);
                    $pdf->text(15, 40, $GLOBALS["text1"], $font, 9);
                }
            ');
        }

        if ($params['isProforma']) {
            $text = "Proforma Invoice No: " . $parameters['rxInformation']['proformaInvoiceNo'];
            $GLOBALS['text']  =  $text;
            $GLOBALS['text1'] = "Date: TBC";

            $canvas = $dompdf->get_canvas();
            $canvas->page_script('
                if ($PAGE_NUM > 1) {
                    $font = $fontMetrics->getFont("sans-serif");
                    $pdf->text(15, 26, $GLOBALS["text"], $font, 9);
                    $pdf->text(15, 40, $GLOBALS["text1"], $font, 9);
                }
            ');
        }

        $response = new Response();
        $response->setContent($dompdf->output());
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/pdf');

        if (isset($params['rx']) && is_object($params['rx']) && $params['rx']->getOrderPhysicalNumber()) {
            $fileName = $params['rx']->getOrderPhysicalNumber() . '_' . $fileName;
        }
        $response->headers->set('Content-Disposition', "filename=$fileName");

        return $response;
    }

    /**
     * @Route("/rx/list", name="list_rx")
     */
    public function listAction(Request $request)
    {
        $parameters = array(
            'isAll' => true,
            'isSaveSuccessDraft' => $request->get('save-success-draft')
        );

        return $this->render('DoctorBundle:rx:list.html.twig', $parameters);
    }

    /**
     * @Route("/rx/list-draft", name="list_draft_rx")
     */
    public function listOfDraftAction(Request $request)
    {
        $parameters = array(
            'isDraft' => true,
            'tab' => $request->get('tab')
        );
        return $this->render('DoctorBundle:rx:list.html.twig', $parameters);
    }

    /**
     * @Route("/rx/list-scheduled", name="list_scheduled_rx")
     */
    public function listOfScheduledAction(Request $request)
    {
        $parameters = array(
            'isScheduled' => true,
            'tab' => $request->get('tab')
        );
        return $this->render('DoctorBundle:rx:list.html.twig', $parameters);
    }


    /**
     * @Route("/rx/list-reported", name="list_reported_rx")
     */
    public function listOfReportedAction(Request $request)
    {
        $parameters = array(
            'isReported' => true
        );
        return $this->render('DoctorBundle:rx:list.html.twig', $parameters);
    }

    /**
     * @Route("/rx/list-failed", name="list_failed_rx")
     */
    public function listOfFailedAction(Request $request)
    {
        $parameters = array(
            'isFailed' => true
        );
        return $this->render('DoctorBundle:rx:list.html.twig', $parameters);
    }

    /**
     * @Route("/rx/list-deleted-cancelled", name="list_deleted_cancelled_rx")
     */
    public function listOfDeletedCancelledAction(Request $request)
    {
        $parameters = array(
            'isDeletedCancelled' => true
        );
        return $this->render('DoctorBundle:rx:list.html.twig', $parameters);
    }

    /**
     * @Route("/rx/list-recalled", name="list_recalled_rx")
     */
    public function listOfRecalledAction(Request $request)
    {
        $parameters = array(
            'isRecalled' => true
        );
        return $this->render('DoctorBundle:rx:list.html.twig', $parameters);
    }

    /**
     * @Route("/rx/list-pending", name="list_pending_rx")
     */
    public function listOfPendingAction(Request $request)
    {
        $parameters = array(
            'isPending' => true
        );
        return $this->render('DoctorBundle:rx:list.html.twig', $parameters);
    }

    /**
     * @Route("/rx/list-confirmed", name="list_confirmed_rx")
     */
    public function listOfConfirmedAction(Request $request)
    {
        $parameters = array(
            'isConfirmed' => true
        );
        return $this->render('DoctorBundle:rx:list.html.twig', $parameters);
    }

    /**
     * @Route("/rx/ajax-get-list-rx", name="ajax_get_list_rx")
     */
    public function ajaxGetListRXAction(Request $request)
    {
        $rxRepository = $this->getRXRepository();

        $params = array();
        $params['page'] = $request->get('page', 0);
        $params['perPage'] = $request->get('perPage', Constant::PER_PAGE_DEFAULT);
        $params['doctorId'] = $this->getDoctorId();
        $params['orderId'] = $request->get('orderId');
        $params['issueDate'] = $request->get('issueDate');
        $params['patientName'] = $request->get('patientName');
        $params['rxStatus'] = $request->get('rxStatus');
        $params['sorting'] = $request->get('sorting', 'rx.createdOn-desc');

        if ($request->get('isDraft')) {
            $params['isDraft'] = 1;
            if ($request->get('tab')) {
                $params['rxStatus'] = array(Constant::RX_STATUS_FOR_AMENDMENT);
            }
            if (empty($params['rxStatus'])) {
                $params['rxStatus'] = array(Constant::RX_STATUS_DRAFT, Constant::RX_STATUS_FOR_DOCTOR_REVIEW, Constant::RX_STATUS_FOR_AMENDMENT);
            }
        } else if ($request->get('isFailed')) {
            $params['rxStatus'] = array(Constant::RX_STATUS_PAYMENT_FAILED, Constant::RX_STATUS_FAILED, Constant::RX_STATUS_DEAD);
            $params['isFailed'] = true;
        } else if ($request->get('isDeletedCancelled')) {
            $params['rxStatus'] = array(Constant::RX_STATUS_CANCELLED, Constant::RX_STATUS_DELETED);
            $params['isDeletedCancelled'] = true;
        } else if ($request->get('isRecalled')) {
            $params['onHold'] = true;
            $params['isRecalled'] = true;
        } else if ($request->get('isPending')) {
            $params['rxStatus']  = array(Constant::RX_STATUS_PENDING);
            $params['isPending'] = true;
        } else if ($request->get('isConfirmed')) {
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
            $params['isConfirmed'] = true;
        } else if ($request->get('isReported')) {
            $params['onHold'] = true;
            $params['isReported'] = true;
        } else if ($request->get('isScheduled')) {
            $params['isScheduled'] = true;
        }

        $results = $rxRepository->getListRX($params);
        //paging
        $totalPages = isset($results['totalPages']) ? $results['totalPages'] : Constant::TOTAL_PAGE_DEFAULT;

        //get current link for paging
        unset($params['doctorId']);
        if (empty($params['orderId'])) {
            unset($params['orderId']);
        }
        if (empty($params['issueDate'])) {
            unset($params['issueDate']);
        }
        if (empty($params['patientName'])) {
            unset($params['patientName']);
        }
        if (empty($params['rxStatus'])) {
            unset($params['rxStatus']);
        }
        if (empty($params['sorting'])) {
            unset($params['sorting']);
        }
        $pageUrl = $this->generateUrl('ajax_get_list_rx', $params);

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

        $template = 'DoctorBundle:rx:_ajax-list-rx.html.twig';

        if ($request->get('isReported')) {
            $template = 'DoctorBundle:rx:_ajax-list-reported-rx.html.twig';
        }

        if ($request->get('isDraft')) {
            $template = 'DoctorBundle:mpa:mpa-draft-rx-list.html.twig';
            $rxRepository->formatMPADraftList($results['data']);
        }

        if ($request->get('isScheduled')) {
            $template = 'DoctorBundle:rx:_ajax-list-scheduled-rx.html.twig';
        }

        return $this->render($template, array(
            'data'           => $results['data'],
            'paginationHTML' => $paginationHTML,
            'currentPage'    => $params['page'],
            'perPage'        => $params['perPage'],
            'totalResult'    => $results['totalResult'],
            'isDraft'        => $request->get('isDraft'),
            'isPending'      => $request->get('isPending'),
            'isFailed'       => $request->get('isFailed'),
            'isRecalled'     => $request->get('isRecalled'),
            'sortInfo'       => $sortInfo,
            'arrGreen'       => $results['arrGreen'],
            'arrGray'        => $results['arrGray'],
            'arrBlue'        => $results['arrBlue'],
            'arrYellow'        => $results['arrYellow']
        ));
    }

    /**
     * @Route("/rx/delete/{rxId}", name="delete_rx")
     */
    public function deleteAction(Request $request, $rxId)
    {
        $rxRepository = $this->getRXRepository();

        $rx = $rxRepository->find($rxId);
        if (null == $rx) {
            throw $this->createAccessDeniedException();
        }

        try {
            $rx->setStatus(Constant::RX_STATUS_DELETED);
            $rx->setDeletedOn(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($rx);
            $em->flush($rx);
        } catch(\Exception $ex) {
        }

        // Update status log
        $params = array('rxObj' => $rx, 'isEdit' => true);
        $rxRepository->manageRXStatusLog($params);

        if ($rx->getIsScheduledRx()) {
            return $this->redirectToRoute('list_scheduled_rx');
        } else {
            return $this->redirectToRoute('list_draft_rx');
        }
    }
    /**
     * @Route("/rx/update/{messageId}/{rxId}", name="update_rx")
     */
    public function updateAction(Request $request, $rxId, $messageId)
    {
        $rxRepository = $this->getRXRepository();
        $em = $this->getDoctrine()->getManager();
        $rx = $rxRepository->find($rxId);
        $doctorId = $rx->getDoctor()->getId();
        $logInId  = $this->getDoctorId();
        if ($doctorId != $logInId) {
            throw $this->createAccessDeniedException();
        }
        $counter = $rx->getRxCounter();
        if(empty($counter)||  count($counter) == 0){
            $rxCounter = new RxCounter();
            $rxCounter->setIsDoctorRead(1);
            $rx->addRxCounter($rxCounter);
        } else {
            $rxCounter = $counter->first();
            $rxCounter->setIsDoctorRead(1);

        }
        if($rx->getHasRxReviewFee() != true)
            $rx->setHasRxReviewFee(true);
        $em->persist($rx);
        $em->flush();
        $patientId = $rx->getPatient()->getId();
        $params = array(
            'doctorId'  => $doctorId,
            'patientId' => $patientId,
            'rxId'      => $rxId
        );

        $parameters = array();
        $parameters['patientInformation'] = $rxRepository->getPatientInformation($params);
        $parameters['dateTabs'] = $rxRepository->getListDateTab($params);
        $parameters['patientId'] = $patientId;
        $parameters['rxData'] = $rx;
        $parameters['refill'] = $rxRepository->getRXRefillReminder($params);
        $parameters['orderNumber'] = $rx->getOrderNumber();
        $parameters['doctorFee'] = $rxRepository->getDoctorFee($params);

        $rxDrugs = $rxRepository->getRXDrug($params);
        $arrTemp = array();
        $arrIds  = array();
        foreach ($rxDrugs as $value) {
            $arrTemp[] = $value['id'];
            $arrIds[]  = $value['drugId'];
        }
        $parameters['rxDrugs'] = implode(',', $arrTemp);
        $parameters['drugIds'] = implode(',', $arrIds);
        $parameters['mesageId'] = $messageId;


        return $this->render('DoctorBundle:rx:update.html.twig', $parameters);
    }
    /**
     * @Route("/rx/edit/{rxId}", name="edit_rx")
     */
    public function editAction(Request $request, $rxId)
    {
        $rxRepository = $this->getRXRepository();

        $rx = $rxRepository->find($rxId);
        $rxStatus = $rx->getStatus();

        $allowEditStatuses = array(Constant::RX_STATUS_DRAFT, Constant::RX_STATUS_PENDING, Constant::RX_STATUS_FOR_DOCTOR_REVIEW, Constant::RX_STATUS_FOR_AMENDMENT);

        if (null === $rx || !in_array($rxStatus, $allowEditStatuses) ) {
            throw $this->createAccessDeniedException();
        }

        $doctorId = $rx->getDoctor()->getId();

        $logInId  = $this->getDoctorId();
        if ($doctorId != $logInId) {
            throw $this->createAccessDeniedException();
        }

        $patientId = $rx->getPatient()->getId();
        $patientRepository = $this->getDoctrine()->getRepository('UtilBundle:Patient');
        $params = array(
            'doctorId'  => $doctorId,
            'patientId' => $patientId,
            'rxId'      => $rxId,
            'otherDiagnosisValues' => $patientRepository->getOtherDiagnosticValues($patientId),
            'showDrugsFromEdit' => true
        );

        $parameters = array();
        $parameters['patientInformation'] = $rxRepository->getPatientInformation($params);
        $parameters['dateTabs'] = $rxRepository->getListDateTab($params);
        $parameters['patientId'] = $patientId;
        $parameters['rxData'] = $rx;
        $parameters['refill'] = $rxRepository->getRXRefillReminder($params);
        $parameters['orderNumber'] = $rx->getOrderNumber();
        $parameters['doctorFee'] = $rxRepository->getDoctorFee($params);

        $rxDrugs = $rxRepository->getRXDrug($params);
        $arrTemp = array();
        $arrIds  = array();
        foreach ($rxDrugs as $value) {
            $arrTemp[] = $value['id'];
            $arrIds[]  = $value['drugId'];
        }
        $parameters['rxDrugs'] = implode(',', $arrTemp);
        $parameters['drugIds'] = implode(',', $arrIds);
        $parameters['proformaInvoiceNo'] = $rxRepository->generateProformaInvoiceNo($params);

        $parameters['showSaveAsDraft'] = true;
        if ($rxStatus == Constant::RX_STATUS_FOR_DOCTOR_REVIEW ||
                $rxStatus == Constant::RX_STATUS_FOR_AMENDMENT) {
            $parameters['showSaveAsDraft'] = false;
        }

        $roles = $this->getUser()->getRoles();
        if (in_array(Constant::TYPE_DOCTOR_NAME, $roles) && 
                $rxStatus == Constant::RX_STATUS_FOR_DOCTOR_REVIEW) {
            $parameters['showRequestAmend'] = true;
            $parameters['rxNote'] = $rx->getRxNote();
            $parameters['list'] = $rxDrugs;
        }

        $parameters['showConfirmRx'] = true;
        $parameters['activeDoctor'] = true;

        if (in_array(Constant::TYPE_MPA, $roles)) {
            $parameters['showConfirmRx'] = false;
            $parameters['showForwardRx'] = true;
            if ($this->getUser()->hasPermission('send_to_patient')) {
                $parameters['showConfirmRx'] = true;
                $parameters['showForwardRx'] = false;
            }
            if ($rxStatus == Constant::RX_STATUS_FOR_DOCTOR_REVIEW) {
                $parameters['showConfirmRx'] = false;
                $parameters['showForwardRx'] = false;
            }
            if ($rxStatus == Constant::RX_STATUS_FOR_AMENDMENT) {
                $parameters['rxAmendments'] = $this->getDoctrine()
                    ->getRepository('UtilBundle:RxLine')
                    ->getRxLineAmendments(null, $rx->getId());
            }
            $gmedUser = $this->getUser();
            $doctorId = $gmedUser->getId();
            $doctor =  $this->getDoctrine()->getRepository('UtilBundle:Doctor')->find($doctorId);
            if(empty($doctor->getSignatureUrl())){
                $parameters['activeDoctor'] = false;
            }
        }

        return $this->render('DoctorBundle:rx:create.html.twig', $parameters);
    }

    /**
     * @Route("/rx/copy/{rxId}", name="copy_rx")
     */
    public function copyAction(Request $request, $rxId)
    {
        $rxRepository = $this->getRXRepository();

        $rx = $rxRepository->find($rxId);
        if (null === $rx) {
            throw $this->createAccessDeniedException();
        }

        $doctorId = $rx->getDoctor()->getId();
        $logInId  = $this->getDoctorId();
        if ($doctorId != $logInId) {
            throw $this->createAccessDeniedException();
        }

        $patientId = $rx->getPatient()->getId();
        $patientRepository = $this->getDoctrine()->getRepository('UtilBundle:Patient');
        $params = array(
            'doctorId'  => $doctorId,
            'patientId' => $patientId,
            'rxId'      => $rxId,
            'otherDiagnosisValues' => $patientRepository->getOtherDiagnosticValues($patientId)
        );

        $parameters = array();
        $parameters['patientInformation'] = $rxRepository->getPatientInformation($params);
        $parameters['dateTabs'] = $rxRepository->getListDateTab($params);
        $parameters['patientId'] = $patientId;
        $parameters['refill'] = $rxRepository->getRXRefillReminder($params);
        $parameters['orderNumber'] = $rxRepository->generateOrderNumber($params);
        $parameters['doctorFee'] = $rxRepository->getDoctorFee($params);
        $parameters['rxData'] = $rxRepository->getRXInformation($params);

        $rxDrugs = $rxRepository->getRXDrug($params);
        $arrTemp = array();
        foreach ($rxDrugs as $value) {
            $arrTemp[] = $value['id'];
        }
        $parameters['rxDrugs'] = implode(',', $arrTemp);
        $parameters['isRefill'] = $request->get('is-refill');
        $parameters['replacement']  = $request->query->get('replacement', false);
        $parameters['parentId']  = $rxId;
        $parameters['proformaInvoiceNo'] = $rxRepository->generateProformaInvoiceNo($params);
        $parameters['showSaveAsDraft'] = true;
        $parameters['showConfirmRx'] = true;

        $roles = $this->getUser()->getRoles();
        $parameters['activeDoctor'] = true;
        if (in_array(Constant::TYPE_MPA, $roles)) {
            $parameters['showConfirmRx'] = false;
            $parameters['showForwardRx'] = true;
            if ($this->getUser()->hasPermission('send_to_patient')) {
                $parameters['showConfirmRx'] = true;
                $parameters['showForwardRx'] = false;
            }
            $gmedUser = $this->getUser();
            $doctorId = $gmedUser->getId();
            $doctor =  $this->getDoctrine()->getRepository('UtilBundle:Doctor')->find($doctorId);
            if(empty($doctor->getSignatureUrl())){
                $parameters['activeDoctor'] = false;
            }
        }

        return $this->render('DoctorBundle:rx:create.html.twig', $parameters);
    }

    /**
     * @Route("/rx/view/{rxId}", name="view_rx")
     */
    public function viewAction(Request $request, $rxId)
    {
        $rxRepository = $this->getRXRepository();

        $rx = $rxRepository->find($rxId);
        if (null == $rx) {
            throw $this->createAccessDeniedException();
        }
        $counter = $rx->getRxCounter();
        if(!empty($counter) && count($counter) > 0){
            $em = $this->getDoctrine()->getManager();
            $rxCounter = $counter->first();
            $rxCounter->setIsDoctorRead(1);
            $em->persist($rx);
            $em->flush();
        }

        $patientRepository = $this->getDoctrine()->getRepository('UtilBundle:Patient');
        $params = array(
            'patientId' => $rx->getPatient()->getId(),
            'rxId' => $rxId,
            'otherDiagnosisValues' => $patientRepository->getOtherDiagnosticValues($rx->getPatient()->getId())
        );

        $parameters = array();
        $parameters['rxData'] = $rx;
        $parameters['patientInformation'] = $rxRepository->getPatientInformation($params);
        $parameters['refill'] = $rxRepository->getRXRefillReminder($params);
		$issues = $rx->getIssues();
		$parameters['issues'] = array();
		foreach ($issues as $issue) {
			if ($issue->getIssueType() == Constant::DOCTOR_ROLE && !$issue->getIsResolution()) {
				$parameters['issues'][] = $issue;
			}
		}

        $status = $rx->getStatus();
        if ($status == Constant::RX_STATUS_CONFIRMED) {
            $parameters['isConfirmed'] = true;
        }
        if ($status < Constant::RX_STATUS_READY_FOR_COLLECTION) {
            $parameters['isShowReport'] = true;
        }

        if ($status == Constant::RX_STATUS_PENDING) {
            $parameters['showProforma'] = true;
        }

        if ($status == Constant::RX_STATUS_REFUNDED) {
            $parameters['isRefund'] = true;
            $note = $rxRepository->getRefundCreditNote($rxId);
            if(!empty($note)){
                $parameters['noteRefund'] = $this->getParameter('base_url').'/'.$note['url'];
            }

        } else {
            $parameters['isRefund'] = false;
        }
        $parameters['rxStatus'] = Constant::getRXStatus($status);
        $parameters['isLocalPatient'] = $this->isLocalPatient($rx);
        $parameters['replacement']  = $request->query->get('replacement', false);
        $parameters['patientId'] = $rx->getPatient()->getId();
        $parameters['paidOn'] = $rx->getPaidOn();
        $externalUrl = $this->getParameter('adverse_url');
        $parameters['externalUrl'] = $externalUrl[Constant::SINGAPORE_CODE];
        $parameters['ajaxAdverseUrl'] = 'ajax_set_adverse_activities';

        return $this->render('DoctorBundle:rx:view.html.twig', $parameters);
    }

    private function isLocalPatient($rx) {
        $em =  $this->getDoctrine();
        $platform = $em->getRepository('UtilBundle:PlatformSettings')->getPlatFormSetting();
        if(!empty($rx->getShippingAddress()) && !empty($platform)) {
            if($platform['operationsCountryId'] ==  $rx->getShippingAddress()->getCity()->getCountry()->getId()) {
                return true;
            }
        }
        return false;

    }
    
    /**
     * @Route("/rx/ajax-set-adverse-activities", name="ajax_set_adverse_activities")
     */
    public function ajaxSetAdverseActivities(Request $request)
    {
        $rxRepository = $this->getRXRepository();
        $rxId = $request->get('rxId', '');

        $rx = $rxRepository->find($rxId);
        if (null == $rx) {
            return new JsonResponse();
        }
        
        $externalUrl = $this->getParameter('adverse_url');
        $note = "Doctor accessed Adverse Drug Reaction form";
        $note .= " <a href='" . $externalUrl[Constant::SINGAPORE_CODE]['full'] . "' target='_blank'>Link</a>";
        try {
            $issue = new Issue();
            $issue->setRemarks($note);
			$issue->setIssueType(Constant::DOCTOR_ROLE);
            $issue->setCreatedOn(new \DateTime('now'));
            $issue->setUpdatedOn(new \DateTime('now'));
            $issue->setIsResolution(false);

            $user = $this->getUser()->getDisplayName();
            $issue->setUpdatedBy($user);
            $issue->setCreatedBy($user);

            $rx->addIssue($issue);

            $em = $this->getDoctrine()->getManager();
            $em->persist($rx);
            $em->flush();
        } catch(\Exception $ex) {
            return new JsonResponse();
        }
        return new JsonResponse(array(
            'success' => true
        ));
    }
    
    /**
     * @Route("/rx/ajax-get-activities-log/{rxId}", name="ajax_get_activities_log")
     */
    public function ajaxGetActivitiesLogAction(Request $request, $rxId)
    {
        $rxRepository = $this->getRXRepository();

        $params = array('rxId' => $rxId);
        $list   = $rxRepository->getListActivitiesLog($params);
        $parameters = array(
            'list' => $list
        );
        $content = $this->renderView('DoctorBundle:rx:_ajax-activities-log.html.twig', $parameters);

        return new Response($content);
    }

    /**
     * @Route("/rx/ajax-recall/{rxId}", name="ajax_recall")
     */
    public function ajaxRecallAction(Request $request, $rxId)
    {
        $rxRepository = $this->getRXRepository();

        $rx = $rxRepository->find($rxId);
        if (null == $rx) {
            return new JsonResponse();
        }

        $params = array(
            'patientId' => $rx->getPatient()->getId()
        );
        $patient = $rxRepository->getPatientInformation($params);

        $patientName = isset($patient['name']) ? $patient['name'] : '';
        $orderNumber = $rx->getOrderNumber();
        $recallUrl   = $this->generateUrl('recall_rx', array('rxId' => $rxId));

        $data = array(
            'patientName' => $patientName,
            'orderNumber' => $orderNumber,
            'recallUrl'   => $recallUrl
        );

        return new JsonResponse($data);
    }

    /**
     * @Route("/rx/recall/{rxId}", name="recall_rx")
     */
    public function recallAction(Request $request, $rxId)
    {
        $rxRepository = $this->getRXRepository();

        $rx = $rxRepository->find($rxId);
        if (null == $rx) {
            throw $this->createAccessDeniedException();
        }

        $note = $request->get('reasonForRecall', '');
        $recallAction = $request->get('recallAction', '');

        // params for rx status log
        $params = array(
            'rxObj' => $rx,
            'isRecall' => true,
            'note' => $note
        );

        try {

            if ($recallAction == 'cancel') {
                $params['recallAction'] = 'cancel';
            } elseif ($recallAction == 'edit') {
                $params['recallAction'] = 'edit';
            }

            $rx->setIsOnHold(true);
            $rx->setUpdatedOn(new \DateTime());

            $issue = new Issue();
            $issue->setRemarks($note);
			$issue->setIssueType(Constant::DOCTOR_ROLE);
            $issue->setCreatedOn(new \DateTime('now'));
            $issue->setUpdatedOn(new \DateTime('now'));
            $issue->setIsResolution(false);

            $user = $this->getUser()->getDisplayName();
            $issue->setUpdatedBy($user);
            $issue->setCreatedBy($user);

            $rx->addIssue($issue);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
        } catch(\Exception $ex) {
        }

        // Update status log
        $rxRepository->manageRXStatusLog($params);

        //send Mail, SMS to patient
        if($rx->getStatus() == Constant::RX_STATUS_PENDING) {
            $patientObj = $rx->getPatient();
            if (null == $patientObj) {
                return false;
            }

            $arrPhone = array();
            $arrTo = array();
            if ($patientObj->getUseCaregiver() && $patientObj->getIsSendMailToCaregiver()) {
                $caregiver = $patientObj->getCaregivers()->first();
                if ($caregiver) {
                    $arrTo[] = $caregiver->getPersonalInformation()->getEmailAddress();
                    $arrPhone[] = $caregiver->getPhones()->first();
                }
            }
            if (empty($arrTo)) {
                $arrTo[] = $patientObj->getPersonalInformation()->getEmailAddress();
            }
            $arrTo = array_unique($arrTo);

            $queryParams = array(
                'patientId' => $rx->getPatient()->getId(),
                'doctorId' => $rx->getDoctor()->getId(),
            );

            $mailParams['patientInformation'] = $rxRepository->getPatientInformation($queryParams);
            $mailParams['clinicInformation'] = $rxRepository->getClinicInformation($queryParams);
            $mailParams['doctorInformation'] = $rxRepository->getDoctorInformation($queryParams);
            $mailParams['baseUrl'] = $this->container->getParameter('base_url');
            $mailParams['isCancel'] = $recallAction == 'cancel' ? true : false;

            $body  = $this->renderView('DoctorBundle:emails:doctor-recall.html.twig', $mailParams);

            $dataSendMail = array(
                'title'  => "Important information about your prescription order",
                'body'   => $body,
                'from'   => $this->container->getParameter('primary_email'),
                'to'     => $arrTo
            );

            if ($this->checkTimeToSendMessage($rx->getId(),'email')) {
                $this->container->get('microservices.sendgrid.email')->sendEmail($dataSendMail);
            }

            //update rx counter
            try{
                $em = $this->getDoctrine()->getManager();
                if ($recallAction == 'cancel') {
                    $rx->setStatus(Constant::RX_STATUS_CANCELLED);
                    $rx->setIsOnHold(false);
                } else {
                    $rx->setStatus(Constant::RX_STATUS_DRAFT);
                }
                $em->persist($rx);
                $em->flush();

                $rxCounter = new RxCounter();
                $rxCounter->setRx($rx);
                $rxCounter->setIsDoctorRead(1);
                $rxCounter->setCreatedOn(new \DateTime('now'));
                $em = $this->getDoctrine()->getManager();
                $em->persist($rxCounter);
                $em->flush();
            } catch(\Exception $ex) {
            }

            // send SMS
            $message = $this->renderView('DoctorBundle:emails:doctor-recall-sms.html.twig', $mailParams);

            if (empty($arrPhone)) {
                $arrPhone[] = $patientObj->getPhones()->first();
            }
            foreach ($arrPhone as $phone) {
                if ($phone) {
                    $phoneNumber = '';
                    $arrPN = array('+');
                    $country = $phone->getCountry();
                    if ($country) {
                        $arrPN[] = $country->getPhoneCode();
                    }
                    $arrPN[] = $phone->getAreaCode();
                    $arrPN[] = $phone->getNumber();
                    $phoneNumber = implode('', $arrPN);

                    if ($this->checkTimeToSendMessage($rx->getId(),'sms')) {
                        if ($phoneNumber && $message) {
                            $dataSendSMS = array(
                                'to' => $phoneNumber,
                                'message' => $message
                            );
                            $this->container->get('microservices.sms')->sendMessage($dataSendSMS);
                        }
                    }
                }
            }
        }

        //Notification via email: STRIKE-482
        $notifyParams = array(
            'patientId' => $rx->getPatient()->getId(),
            'doctorId' => $rx->getDoctor()->getId(),
        );
        $patientInformation = $rxRepository->getPatientInformation($notifyParams);
        $doctorInformation = $rxRepository->getDoctorInformation($notifyParams);
        $notify = array(
            'orderNumber' => $rx->getOrderNumber(),
            'notifyText'  => 'put on hold',
            'note'        => $note,
            'doctorName'  => $doctorInformation['name'],
            'doctorCode'  => $doctorInformation['doctorCode'],
            'patientName' => $patientInformation['name'],
            'patientCode' => $patientInformation['patientCode'],
            'baseUrl'     => $this->container->getParameter('base_url'),
            'logoUrl'     => $this->container->getParameter('base_url').'/bundles/admin/assets/pages/img/logo.png'
        );
        $notifyBody = $this->renderView('DoctorBundle:emails:doctor-notification.html.twig', $notify);
        $notifyDataSendMail = array(
            'title'  => "Updates to RX Order ".$rx->getOrderNumber(),
            'body'   => $notifyBody,
            'from'   => $this->container->getParameter('primary_email'),
            'to'     => $this->container->getParameter('askpharmacist_email')
        );
        $this->container->get('microservices.sendgrid.email')->sendEmail($notifyDataSendMail);

		$redirect = $request->get('redirect', 1);

		if ($redirect) {
			$route = 'list_pending_rx';
			if ($request->get('isConfirmed')) {
				$route = 'list_confirmed_rx';
			}

			return $this->redirectToRoute($route);
		} else {
			$result = array(
				'success' => 1,
				'date' => $issue->getCreatedOn()->format('l, F d, Y h:ia'),
				'reporter' => $issue->getCreatedBy(),
				'status' => 'On Hold - Doctor Issue',
				'note' => $issue->getRemarks()
			);

			return new JsonResponse($result);
		}
    }

    /**
     * @Route("/rx/ajax-resend/{id}", name="ajax_resend")
     */
    public function resendAction(Request $request, Rx $rx)
    {
        if ($rx->getReSentOn()) {
            throw $this->createAccessDeniedException();
        }

        $sent = $this->resendRxToPatient($rx);

        if ($sent) {
            try {
                $rx->setStatus(Constant::RX_STATUS_PENDING);
                $rx->setResentOn(new \DateTime());
                $rx->setLastestReminder(new \DateTime());

                $em = $this->getDoctrine()->getManager();
                $em->persist($rx);

                // Update status log
                $params = array(
                    'rxObj' => $rx,
                    'isResend' => true
                );
                $rxRepository = $this->getRXRepository();
                $rxRepository->manageRXStatusLog($params);
            } catch(\Exception $ex) {
            }
        }

        $data = array();
        return new JsonResponse($data);
    }

    /**
     * @Route("/rx/ajax-edit-recalled-rx/{id}", name="ajax_edit_recalled_rx")
     */
    public function editRecalledRXAction(Request $request, Rx $rx)
    {
        $rxRepository = $this->getRXRepository();
        try {
            $rx->setStatus(Constant::RX_STATUS_DRAFT);
            $this->getDoctrine()->getManager()->persist($rx);

            // Update status log
            $params = array(
                'rxObj' => $rx,
                'isRecalled' => true
            );
            $rxRepository->manageRXStatusLog($params);
        } catch(\Exception $ex) {
        }

        $data = array(
            'url' => $this->generateUrl('edit_rx', array('rxId' => $rx->getId()))
        );
        return new JsonResponse($data);
    }

    private function updateMessageContent($params)
    {
        if (empty($params['refillId'])) {
            return;
        }

        $rxId = $params['refillId'];

        $em = $this->getDoctrine()->getManager();
        $rxRefillReminder = $em->getRepository('UtilBundle:RxRefillReminder')->findOneBy(['rx' => $rxId]);
        if (empty($rxRefillReminder)) {
            return;
        }

        $message = $rxRefillReminder->getMessage();
        if (empty($message)) {
            return;
        }

        $content = $message->getContent();
        if (empty($content)) {
            return;
        }

        $type = $content->getType();
        if (Constant::MESSAGE_CONTENT_TYPE_YES != $type) {
            return;
        }

        $content->setType(Constant::MESSAGE_CONTENT_TYPE_REFILLED);
        $em->persist($content);
    }

    private function sendToPatient($params)
    {
        $rxObj = isset($params['rxObj']) ? $params['rxObj'] : null;
        if (null == $rxObj) {
            return false;
        }

        $patientObj = $rxObj->getPatient();
        if (null == $patientObj) {
            return false;
        }
        $rxId = $rxObj->getId();

        $arrPhone = array();
        $arrTo = array();
        if ($patientObj->getUseCaregiver() && $patientObj->getIsSendMailToCaregiver()) {
            $caregiver = $patientObj->getCaregivers()->first();
            if ($caregiver) {
                $arrTo[] = $caregiver->getPersonalInformation()->getEmailAddress();
                $arrPhone[] = $caregiver->getPhones()->first();
            }
        }
        if (empty($arrTo)) {
            $arrTo[] = $patientObj->getPersonalInformation()->getEmailAddress();
        }
        $arrTo = array_unique($arrTo);

        // load notification template
        $key = Constant::CODE_NEW_RX_ORDER;
        $rxNotificationSetting = $this->getDoctrine()->getManager()->getRepository('UtilBundle:RxReminderSetting')->find($key);
        $rxRepository = $this->getRXRepository();

        $mailTemplate = "";
        $SMSTemplate = "";
        $delayPeriod = $this->getParameter('email_delay_period');
        if ($rxNotificationSetting) {
            $delayTime = (string)$rxNotificationSetting->getDurationTime();
            $delayUnit = ($rxNotificationSetting->getTimeUnit() == 'hour') ? 'H' : 'M';

            $delayPeriod = $delayTime . $delayUnit;
            $mailSubject = $rxNotificationSetting->getTemplateSubjectEmail();
            $mailTemplate = $rxNotificationSetting->getTemplateBodyEmail();
            $SMSTemplate = $rxNotificationSetting->getTemplateSms();

            $mailTemplate = $rxRepository->replaceTemplateData($this->container, $rxObj, $rxNotificationSetting, $mailTemplate, true, false);

            $SMSTemplate = $rxRepository->replaceTemplateData($this->container, $rxObj, $rxNotificationSetting, $SMSTemplate);
        }

        $parameters['patientInformation'] = $rxRepository->getPatientInformation($params);
        $parameters['clinicInformation'] = $rxRepository->getClinicInformation($params);
        $parameters['doctorInformation'] = $rxRepository->getDoctorInformation($params);
        $parameters['orderNumber'] = $params['orderNumber'];
        $parameters['prescription_email'] = $mailTemplate;
        $parameters['baseUrl'] = $this->container->getParameter('base_url');
        $body  = $this->renderView('DoctorBundle:emails:rx-order-confirmation.html.twig', $parameters);

        $mailParams = array(
            'title' => isset($mailSubject) ? $mailSubject : 'Confirm your prescription',
            'body'  => $body,
            'to'    => $arrTo
        );

        $timeToSend = new \DateTime();
        $timeToSend->add(new \DateInterval('PT' . $delayPeriod));
        if ($this->checkTimeToSendMessage($rxId,'email')) {
            foreach ($mailParams['to'] as $value) {
                $emailSend = new \UtilBundle\Entity\EmailSend();
                $emailSend->setSubject($mailParams['title']);
                $emailSend->setContent($mailParams['body']);
                $emailSend->setFrom($this->getParameter('primary_email'));
                $emailSend->setTo($value);
                $emailSend->setTimeToSend($timeToSend);
                $emailSend->setCreatedOn(new \DateTime());
                $emailSend->setRxId($rxId);
                $this->getDoctrine()->getManager()->persist($emailSend);
            }
        }

        // send SMS
        $message = strip_tags($SMSTemplate);

        // STRIKE-698
        if (empty($arrPhone)) {
            $arrPhone[] = $patientObj->getPhones()->first();
        }

        foreach ($arrPhone as $phone) {
            if (empty($phone)) {
                continue;
            }

            $phoneNumber = '';
            $arrPN = array('+');
            $country = $phone->getCountry();
            if ($country) {
                $arrPN[] = $country->getPhoneCode();
            }
            $arrPN[] = $phone->getAreaCode();
            $arrPN[] = $phone->getNumber();
            $phoneNumber = implode('', $arrPN);

            if (empty($phoneNumber) || empty($message)) {
                continue;
            }

            if ($this->checkTimeToSendMessage($rxId,'sms')) {
                $smsSend = new \UtilBundle\Entity\SmsSend();
                $smsSend->setTo($phoneNumber);
                $smsSend->setContent($message);
                $smsSend->setTimeToSend($timeToSend);
                $smsSend->setCreatedOn(new \DateTime());
                $smsSend->setRxId($rxId);
                $this->getDoctrine()->getManager()->persist($smsSend);
            }
        }

        return true;
    }

    private function resendRxToPatient(RX $rx)
    {
        $rxRepository = $this->getRXRepository();

        $criteria = array(
            'reminderCode' => Constant::REMINDER_CODE_C2_GPS
        );

        $rxReminder = $this->getDoctrine()->getRepository('UtilBundle:RxReminderSetting')->findOneBy($criteria);

        $title = $rxReminder->getTemplateSubjectEmail();
        $body  = $rxReminder->getTemplateBodyEmail();
        $body  = $rxRepository->replaceTemplateData($this->container, $rx, $rxReminder, $body, true);

        $message = $rxReminder->getTemplateSms();
        $message = $rxRepository->replaceTemplateData($this->container, $rx, $rxReminder, $message);

        $params = array('doctorId' => $rx->getDoctor()->getId());
        $parameters = array();
        $parameters['clinicInformation'] = $rxRepository->getClinicInformation($params);
        $parameters['doctorInformation'] = $rxRepository->getDoctorInformation($params);
        $parameters['body'] = $body;
        $parameters['baseUrl'] = $this->getParameter('base_url');

        $view = 'AdminBundle:emails:rx-reminder.html.twig';
        $html = $this->get('twig')->render($view, $parameters);

        $patient = $rx->getPatient();
        $arrTo = array();
        $arrPhone = array();
        if ($patient->getUseCaregiver() && $patient->getIsSendMailToCaregiver()) {
            $caregiver = $patient->getCaregivers()->first();
            if ($caregiver) {
                $arrTo[] = $caregiver->getPersonalInformation()->getEmailAddress();
                $arrPhone[] = $caregiver->getPhones()->first();
            }
        }
        if (empty($arrTo)) {
            $personal = $patient->getPersonalInformation();
            if ($personal) {
                $arrTo[] = $personal->getEmailAddress();
            }
        }
        $arrTo = array_unique($arrTo);

        $params = array(
            'title' => $title,
            'body'  => $html,
            'to'    => $arrTo
        );

        if ($this->checkTimeToSendMessage($rx->getId(),'email')) {
            $sent = $this->container->get('microservices.sendgrid.email')->sendEmail($params);
        }

        if (!$message) {
            return false;
        }

        if (empty($arrPhone)) {
            $arrPhone[] = $patient->getPhones()->first();
        }

        foreach ($arrPhone as $phone) {
            if (empty($phone)) {
                continue;
            }

            $arrPN = array('+');
            $country = $phone->getCountry();
            if ($country) {
                $arrPN[] = $country->getPhoneCode();
            }
            $arrPN[] = $phone->getAreaCode();
            $arrPN[] = $phone->getNumber();
            $phoneNumber = implode('', $arrPN);

            if ($this->checkTimeToSendMessage($rx->getId(),'sms')) {
                if ($phoneNumber) {
                    $params = array(
                        'to' => $phoneNumber,
                        'message' => $message
                    );
                    $this->container->get('microservices.sms')->sendMessage($params);
                }
            }
        }

        return $sent;
    }

    private function getDoctorId($rxId = 0)
    {
        if ($rxId) {
            $rx = $this->getDoctrine()
                ->getRepository('UtilBundle:Rx')
                ->find($rxId);
            if ($rx) {
                $doctor = $rx->getDoctor();
                if ($doctor) {
                    return $doctor->getId();
                }
            }
        }

        $user = $this->getUser();
        if ($user) {
            return $user->getId();
        }

        throw $this->createAccessDeniedException();
    }

    private function getRXRepository()
    {
        $repository = $this->getDoctrine()->getRepository('UtilBundle:Rx');
        $repository->setContainer($this->container);

        return $repository;
    }

    /**
     * @Route("/rx/print-activities-logs/{rxId}", name="doctor_rx_activities_print_logs")
     */
    public function printActivitiesLogsAction($rxId) {
        try {
            $rxRepository = $this->getRXRepository();

            $params     = array('rxId' => intval($rxId));
            $list       = $rxRepository->getListActivitiesLog($params);
            $fileName   = 'doctor_rx_activities_logs_id_'. intval($rxId);
            $parameters = array(
                'list'  => $list,
                'title' => 'ACTIVITIES LOG'
            );

            $html = $this->renderView('DoctorBundle:rx:print-activities-logs.html.twig', $parameters);

            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $response = new Response();
            $response->setContent($dompdf->output());
            $response->setStatusCode(200);
            $response->headers->set('Content-Disposition', 'attachment');
            $response->headers->set('Content-Disposition', 'attachment; filename='. $fileName . '.pdf');
            $response->headers->set('Content-Type', 'application/pdf');

            return $response;
        } catch (\Exception $e) {
            dei($e->getMessage());
        }
    }
    
    private function checkTimeToSendMessage($rxId, $type = 'email')
    {
        $timeNow = new \DateTime();
        
        if ($type == 'email') {
            $messages = $this->getDoctrine()->getRepository('UtilBundle:EmailSend')->findBy(['rxId' => $rxId]);
        } else {
            $messages = $this->getDoctrine()->getRepository('UtilBundle:SmsSend')->findBy(['rxId' => $rxId]);
        }
        
        if ($messages != null) {
            foreach ($messages as $message) {
                if ($message->getTimeToSend() > $timeNow) {
                    return false;
                }
            }
        }
        
        return true;
    }

    /**
     * @Route("/rx/forward-rx-to-doctor", name="doctor_rx_forward_rx_to_doctor")
     */
    public function forwardRxToDoctorAction(Request $request)
    {
        $data = [
            'success' => false
        ];

        $rxRepository = $this->getRXRepository();

        $params = $request->request->all();
        $params['doctorId'] = $this->getDoctorId();

        if (false == $rxRepository->isExistsPatient($params)) {
            return new JsonResponse($data);
        }

        $gmedUser = $this->getUser();
        if (empty($gmedUser)) {
            return new JsonResponse($data);
        }

        if (isset($params['rxId'])) {
            $rxObj = $rxRepository->find($params['rxId']);
            if ($rxObj) {
                $rxStatus = $rxObj->getStatus();
            }
            if (isset($rxStatus) && Constant::RX_STATUS_FOR_AMENDMENT == $rxStatus) {
                $updateFlag = true;
            }
        }

        $params['status'] = Constant::RX_STATUS_FOR_DOCTOR_REVIEW;
        $params['lastUpdatedBy'] = $gmedUser->getDisplayName();
        $result = $rxRepository->createRX($params);

        if (empty($result['success']) || empty($result['data'])) {
            return new JsonResponse($data);
        }

        // Update status log
        $params['rxObj'] = $result['data'];
        $params['createdBy'] = $gmedUser->getDisplayName();
        if (isset($updateFlag)) {
            $params['madeAmendment'] = true;
        }
        $rxRepository->manageRXStatusLog($params);

        // Send message to doctor
        $contentType = Constant::MESSAGE_CONTENT_TYPE_DOCTOR_REVIEW;
        if (isset($updateFlag)) {
            $contentType = Constant::MESSAGE_CONTENT_TYPE_AMENDMENTS;
            $criteria = array(
                'entityId' => $params['rxObj']->getId(),
                'type' => $contentType
            );
            $message = $this->getDoctrine()->getRepository('UtilBundle:Message')->findByCriteria($criteria);
        }

        if (isset($message)) {
            $message->setSentDate(new \DateTime());
            $this->getDoctrine()->getManager()->persist($message);
            $this->getDoctrine()->getManager()->flush();

            $data['success'] = true;
            return new JsonResponse($data);
        }

        $contentData = array(
            'subject' => '',
            'body' => ''
        );
        $content = $this->getDoctrine()->getRepository('UtilBundle:MessageContent')->create($contentData);
        $content->setType($contentType);
        $content->setEntityId($params['rxObj']->getId());

        $userId = $gmedUser->getLoggedUser()->getId();
        $sender = $this->getDoctrine()->getRepository('UtilBundle:User')->find($userId);
        $mpa = $this->getDoctrine()->getRepository('UtilBundle:MasterProxyAccount')->findOneBy(['user' => $sender]);
//        $senderName = $mpa->getGivenName(); // Old: only firstName;
        $senderName = $mpa->getGivenName() . ' '. $mpa->getFamilyName();
        $senderEmail = $mpa->getEmailAddress();

        $receiver = $params['rxObj']->getDoctor()->getUser();

        $messageData = array(
            'content'       => $content,
            'sender'        => $sender,
            'senderName'    => $senderName,
            'senderEmail'   => $senderEmail,
            'receiver'      => $receiver,
            'status'        => Constant::MESSAGE_INBOX,
            'sentDate'      => new \DateTime(),
        );

        $this->getDoctrine()->getRepository('UtilBundle:Message')->create($messageData);

        $data['success'] = true;
        return new JsonResponse($data);
    }

    /**
     * @Route("/rx/request-assistant-amend-rx", name="doctor_rx_request_assistant_amend_rx")
     */
    public function requestAssistantAmendRxAction(Request $request)
    {
        $data = [
            'success' => false
        ];

        $em = $this->getDoctrine()->getManager();

        $note = $request->get('rxNote');
        $rxLineAmendment = $request->get('rxLineAmendment');

        $rxId = $request->get('rxId');
        $rx = $em->getRepository('UtilBundle:Rx')->find($rxId);

        if (empty($rx)) {
            return new JsonResponse($data);
        }

        $doctor = $rx->getDoctor();
        if (empty($doctor)) {
            return new JsonResponse($data);
        }

        $rxNote = $rx->getRxNote();
        if (empty($rxNote)) {
            $rxNote = new RxNote();
            $rxNote->setRx($rx);
            $rxNote->setCreatedOn(new \DateTime());
        }
        $rxNote->setNote($note);

        $em->persist($rxNote);

        $rxLines = $rx->getRxLines();
        foreach ($rxLines as $line) {
            if (false == $line->getDrug()) {
                continue;
            }

            $rxLineId = $line->getId();
            $amendment = $line->getRxLineAmendment();
            if (empty($amendment)) {
                $amendment = new RxLineAmendment();
                $amendment->setRxLine($line);
                $amendment->setCreatedOn(new \DateTime());
            }
            if (isset($rxLineAmendment[$rxLineId])) {
                $amendment->setAmendment($rxLineAmendment[$rxLineId]);
            }
            $em->persist($amendment);
        }

        $rx->setStatus(Constant::RX_STATUS_FOR_AMENDMENT);

        $rx->setLastUpdatedBy($doctor->getPersonalInformation()->getFullName());
        $em->persist($rx);
        $em->flush();

        $params['rxObj'] = $rx;
        $em->getRepository('UtilBundle:Rx')->manageRXStatusLog($params);

        $data['success'] = true;
        return new JsonResponse($data);
    }

    /**
     * @Route("/rx/check-edit-rx-session", name="check_edit_rx_session")
     */
    public function checkEditRxSessionAction(Request $request){
        $em          = $this->getDoctrine()->getManager();
        $rxId        = $request->get('rxId');
        $curUserId   = $request->get('curUser');
        $curUserType = $request->get('curUserType');
        $curUserName = "";

        if($curUserId){
            $curUser = $em->getRepository('UtilBundle:User')->find($curUserId);
            if($curUserType == Constant::TYPE_DOCTOR_NAME){
                $doc = $em->getRepository('UtilBundle:Doctor')->findOneBy(array('user'=>$curUserId));
                $curUserName = $doc->getPersonalInformation()->getFullName();
            }
            else
                $curUserName = $curUser->getFirstName() . ' ' . $curUser->getLastName();
        }

        if($rxId){
            $now = new \DateTime('now');
            $rx  = $this->getRXRepository()->find($rxId);
            $rxStatus = $rx->getStatus();
            $latestActivityOn = $rx->getLatestActivityOn();

            $lockStatuses = array(Constant::RX_STATUS_DRAFT, Constant::RX_STATUS_FOR_AMENDMENT);

            if (null !== $rx && (in_array($rxStatus, $lockStatuses) || $curUserType == Constant::TYPE_DOCTOR_NAME)) { //TH stt 1 và 5 && MPA
                $dbUser = $rx->getLastUpdatedBy();
                if($curUserName == $dbUser || $dbUser == null){
                    $rx->setLatestActivityOn($now);
                    $rx->setLastUpdatedBy($curUserName);
                    $this->getDoctrine()->getManager()->persist($rx);
                    $this->getDoctrine()->getManager()->flush();

                    return new JsonResponse(array(
                            'status' => "0",
                    ));
                }else{
                    if($latestActivityOn->getTimestamp() + Constant::RX_LOCKING_INTERVAL > $now->getTimestamp()){
                        return new JsonResponse(array(
                                'currentEdit' => $dbUser,
                                'status' => "1"
                        ));
                    }else if($latestActivityOn->getTimestamp() + Constant::RX_LOCKING_INTERVAL <= $now->getTimestamp()){
                        $rx->setLatestActivityOn($now);
                        $rx->setLastUpdatedBy($curUserName);
                        $this->getDoctrine()->getManager()->persist($rx);
                        $this->getDoctrine()->getManager()->flush();
                        return new JsonResponse(array(
                                'status' => "0"
                        ));
                    }
                }
            }
            else{
                return new JsonResponse(array(
                        'status' => "0",
                ));
            }
        }

        return new JsonResponse(array(
                'status' => "0",
        ));

    }
}
