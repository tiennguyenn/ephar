<?php

namespace AdminBundle\Controller;

use AdminBundle\Controller\BaseController;
use Dompdf\Dompdf;
use Dompdf\Options;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use UtilBundle\Entity\FileDocument;
use UtilBundle\Entity\FileDocumentLog;
use UtilBundle\Entity\FileDocumentNotification;
use UtilBundle\Utility\Common;
use UtilBundle\Utility\Constant;
use Symfony\Component\HttpFoundation\Response;

class DocumentController extends BaseController {

    /**
     * @Route("/admin/doctor-agreement-setting", name="admin_doctor_agreement_setting")
     */
    public function DoctorAgreementSettingAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('UtilBundle:Site')->findAll();
        $currentSite = Common::getCurrentSite($this->container);

        $site = $em->getRepository('UtilBundle:Site')->findOneBy(array('name' => $currentSite));
        $doctorAgreement = $em->getRepository('UtilBundle:FileDocumentLog')->getContentBeforeForAdmin(Constant::DOCUMENT_NAME_DOCTOR_AGREEMENT, $currentSite);
        $doctorAgreementNotification = $em->getRepository('UtilBundle:FileDocumentNotification')->findOneBy(array('documentName' => Constant::DOCUMENT_NAME_DOCTOR_AGREEMENT, 'site' => $site));

        return $this->render('AdminBundle:document_setting:doctor_agreement.html.twig', array(
            'title' => "Doctor's Subscriber Agreement Setting",
            'doctor_agreement' => $doctorAgreement,
            'doctor_agreement_notification' => $doctorAgreementNotification,
            'sites' => $sites,
            'current_site' => $currentSite
        ));
    }

    /**
     * @Route("/admin/ajax-get-doctor-agreement-setting", name="admin_get_doctor_agreement_setting")
     */
    public function ajaxGetDoctorAgreementSettingAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $site = $em->getRepository('UtilBundle:Site')->find($request->get('site'));
        $doctorAgreement = $em->getRepository('UtilBundle:FileDocumentLog')->getContentBeforeForAdmin(Constant::DOCUMENT_NAME_DOCTOR_AGREEMENT, $site->getName(), true);
        $doctorAgreementId = null;
        if ($doctorAgreement) {
            $doctorAgreementLog = $em->getRepository('UtilBundle:FileDocumentLog')->find($doctorAgreement['id']);
            $doctorAgreementId = $doctorAgreementLog->getFileDocument()->getId();
            $doctorAgreement['doctor_agreement_id'] = $doctorAgreementId;
        }

        $doctorAgreementNotification = $em->getRepository('UtilBundle:FileDocumentNotification')->findOneBy(array('documentName' => Constant::DOCUMENT_NAME_DOCTOR_AGREEMENT, 'site' => $site));
        if ($doctorAgreementNotification) {

            $sendDate = $doctorAgreementNotification->getSendDate();
            $doctorAgreementNotificationResponse = array(
                'id' => $doctorAgreementNotification->getId(),
                'content' => $doctorAgreementNotification->getContent(),
                'subject' => $doctorAgreementNotification->getSubject(),
                'send_date' => $sendDate ? $sendDate->format('Y/m/d H:i:s') : 'YYYY/MM/DD H:I:S'
            );
        }

        $response = array(
            'doctor_agreement' => $doctorAgreement,
            'doctor_agreement_notification' => $doctorAgreementNotificationResponse
        );
        return new JsonResponse($response);
    }

    /**
     * @Route("/admin/ajax-save-doctor-agreement-setting", name="admin_save_doctor_agreement_setting")
     */
    public function ajaxSaveDoctorAgreementSettingAction(Request $request)
    {
        $params = array(
            'siteId' => $request->get('site'),
            'documentName' => Constant::DOCUMENT_NAME_DOCTOR_AGREEMENT,
            'content' => $request->get('doctor_agreement'),
            'effectiveDate' => $request->get('effective_date')
        );

        $response = $this->saveDocumentSetting($params);

        return new JsonResponse($response);
    }

    /**
     * @Route("/admin/ajax-save-doctor-agreement-notification", name="admin_save_doctor_agreement_notification")
     */
    public function ajaxSaveDoctorAgreementNotificationAction(Request $request)
    {
        $response = array('success' => true);
        try {
            $em = $this->getDoctrine()->getManager();
            $siteId = $request->request->get('site');
            $site = $em->getRepository('UtilBundle:Site')->find($siteId);
            $fileDocumentNotificationId = $request->request->get('id');
            if ($fileDocumentNotificationId) {
                $doctorAgreementNotification = $em->getRepository('UtilBundle:FileDocumentNotification')->find($fileDocumentNotificationId);
            } else {
                $doctorAgreementNotification = $em->getRepository('UtilBundle:FileDocumentNotification')->findOneBy(array('documentName' => Constant::DOCUMENT_NAME_DOCTOR_AGREEMENT, 'site' => $site));
            }
            if (!$doctorAgreementNotification) {
                $doctorAgreementNotification = new FileDocumentNotification();
            }
            $doctorAgreementNotification->setDocumentName(Constant::DOCUMENT_NAME_DOCTOR_AGREEMENT);
            $doctorAgreementNotification->setSubject($request->get('subject'));
            $doctorAgreementNotification->setContent($request->get('content'));
            $doctorAgreementNotification->setSite($site);
            $em->persist($doctorAgreementNotification);
            $em->flush();
        } catch (\Exception $exception) {
            $response['success'] = false;
            $response['message'] = $exception->getMessage();
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/admin/ajax-send-doctor-agreement-notification", name="admin_send_doctor_agreement_notification")
     */
    public function ajaxSendDoctorAgreementNotificationAction(Request $request)
    {
        $response = array('success' => true);
        try {
            $em = $this->getDoctrine()->getManager();
            $siteId = $request->request->get('site_id');
            $site = $em->getRepository('UtilBundle:Site')->find($siteId);

            $doctorAgreementNotification = $em->getRepository('UtilBundle:FileDocumentNotification')->findOneBy(array('documentName' => Constant::DOCUMENT_NAME_DOCTOR_AGREEMENT, 'site' => $site));

            $documentName = Constant::DOCUMENT_NAME_DOCTOR_AGREEMENT;
            $messageContent = $doctorAgreementNotification->getContent();
            if ($site) {
                $siteUrl = $this->getRequest()->getScheme() . '://' . $site->getUrl();
            } else {
                $siteUrl = $this->container->getParameter('base_url');
            }
            $doctorAgreementLink = $siteUrl . $this->generateUrl('doctor_subscriber_agreement', array());
            $messageContent .= '<br /><br />' . "<a href='$doctorAgreementLink' target='_blank'>$documentName</a>";

            $postData = array(
                'to' => 'All Doctors',
                'subject' => $doctorAgreementNotification->getSubject(),
                'message' => $messageContent
            );

            //sender
            $gmedUser = $this->getUser();
            $sender = $em->getRepository('UtilBundle:User')->find($gmedUser->getId());

            //content
            $contentData = array(
                'subject' => $postData['subject'],
                'body' => $postData['message']
            );
            $content = $em->getRepository('UtilBundle:MessageContent')->create($contentData);

            $emailTarget = Common::getTargetEmail($em, $postData['to'], 'admin', $siteId);

            if(!empty($emailTarget)) {
                //get display name of sender
                $roles = [];
                foreach ($sender->getRoles() as $role) {
                    $roles[] = $role->getName();
                }

                if($roles[0] == Constant::TYPE_DOCTOR_NAME) {
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

                        if($receiverRoles[0] == Constant::TYPE_DOCTOR_NAME) {
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
                            'senderName'    => isset($item['sendAll'])? 'Gmedes Admin': $senderName,
                            'senderEmail'   => $sender->getEmailAddress(),
                            'receiver'      => $receiver,
                            'receiverName'  => $receiverName,
                            'receiverEmail' => $receiver->getEmailAddress(),
                            'receiverType'  => 0,
                            'receiverGroup' => isset($item['receiverGroup'])? $item['receiverGroup']: null,
                            'status'        => Constant::MESSAGE_INBOX,
                            'sentDate'      => new \DateTime(),
                        );
                        if(isset($postData['id']) && !empty($postData['id'])) {
                            $msgId = (int)$postData['id'];
                            $msgObj = $em->getRepository('UtilBundle:Message')->find($msgId);
                            if($msgObj != null && $msgObj->getStatus() == Constant::MESSAGE_DRAFT)
                                $messageData['id'] = $msgId;
                            else
                                $messageData['parentMessageId'] = $msgId;
                        }
                        $em->getRepository('UtilBundle:Message')->create($messageData);
                    }
                }
                $sendDate = new \DateTime('now');
                $doctorAgreementNotification->setSendDate($sendDate);
                $em->persist($doctorAgreementNotification);
                $em->flush();

                $response['send_date'] = $sendDate->format('Y/m/d H:i:s');
            }
        } catch (\Exception $exception) {
            $response['success'] = false;
            $response['message'] = $exception->getMessage();
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/admin/patient-terms-of-use-setting", name="admin_patient_terms_of_use_setting")
     */
    public function PatientTermsOfUseSettingAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('UtilBundle:Site')->findAll();
        $currentSite = Common::getCurrentSite($this->container);
        $patientTermsOfUse = $em->getRepository('UtilBundle:FileDocumentLog')->getContentBeforeForAdmin(Constant::DOCUMENT_NAME_PATIENT_TERM_OF_USE, $currentSite);

        return $this->render('AdminBundle:document_setting:document_setting_form.html.twig', array(
            'title' => "Patient's Terms Of Use Setting",
            'document_content' => $patientTermsOfUse,
            'sites' => $sites,
            'current_site' => $currentSite,
            'documentName' => Constant::DOCUMENT_NAME_PATIENT_TERM_OF_USE,
            'documentSelector' => 'patient_terms_of_use'
        ));
    }

    /**
     * @Route("/admin/ajax-get-patient-terms-of-use-setting", name="admin_get_patient_terms_of_use_setting")
     */
    public function ajaxGetPatientTermsOfUseSettingAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $site = $em->getRepository('UtilBundle:Site')->find($request->get('site'));
        $patientTermsOfUseRepo = $em->getRepository('UtilBundle:FileDocumentLog');
        $patientTermsOfUse = $patientTermsOfUseRepo->getContentBeforeForAdmin(Constant::DOCUMENT_NAME_PATIENT_TERM_OF_USE, $site->getName(), true);
        $patientTermsOfUseDocument = $patientTermsOfUseRepo->getContentBeforeForAdmin(Constant::DOCUMENT_NAME_PATIENT_TERM_OF_USE, $site->getName());;
        if ($patientTermsOfUseDocument) {
            $patientTermsOfUse['documentId'] = $patientTermsOfUseDocument->getFileDocument()->getId();
        }

        return new JsonResponse($patientTermsOfUse);
    }

    /**
     * @Route("/admin/ajax-save-patient-terms-of-use-setting", name="admin_save_patient_terms_of_use_setting")
     */
    public function ajaxSavePatientTermsOfUseSettingAction(Request $request)
    {
        $params = array(
            'siteId' => $request->get('site'),
            'documentName' => Constant::DOCUMENT_NAME_PATIENT_TERM_OF_USE,
            'content' => $request->get('document_content'),
            'effectiveDate' => $request->get('effective_date')
        );

        $response = $this->saveDocumentSetting($params);

        return new JsonResponse($response);
    }

    /**
     * @Route("/admin/doctor-user-guide-setting", name="admin_doctor_user_guide_setting")
     */
    public function DoctorUserGuideSettingAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('UtilBundle:Site')->findAll();
        $currentSite = Common::getCurrentSite($this->container);
        $doctorUserGuide = $em->getRepository('UtilBundle:FileDocumentLog')->getContentBeforeForAdmin(Constant::DOCUMENT_NAME_DOCTOR_USER_GUIDE, $currentSite);

        return $this->render('AdminBundle:document_setting:document_setting_form.html.twig', array(
            'title' => "Doctor's User Guide Setting",
            'document_content' => $doctorUserGuide,
            'sites' => $sites,
            'current_site' => $currentSite,
            'documentName' => Constant::DOCUMENT_NAME_DOCTOR_USER_GUIDE,
            'documentSelector' => 'doctor_user_guide'
        ));
    }

    /**
     * @Route("/admin/ajax-get-doctor-user-guide-setting", name="admin_get_doctor_user_guide_setting")
     */
    public function ajaxGetDoctorUserGuideSettingAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $site = $em->getRepository('UtilBundle:Site')->find($request->get('site'));
        $doctorUserGuideRepo = $em->getRepository('UtilBundle:FileDocumentLog');
        $doctorUserGuide = $doctorUserGuideRepo->getContentBeforeForAdmin(Constant::DOCUMENT_NAME_DOCTOR_USER_GUIDE, $site->getName(), true);
        $doctorUserGuideDocument = $doctorUserGuideRepo->getContentBeforeForAdmin(Constant::DOCUMENT_NAME_DOCTOR_USER_GUIDE, $site->getName());
        if ($doctorUserGuideDocument) {
            $doctorUserGuide['documentId'] = $doctorUserGuideDocument->getFileDocument()->getId();
        }

        return new JsonResponse($doctorUserGuide);
    }

    /**
     * @Route("/admin/ajax-save-doctor-user-guide-setting", name="admin_save_doctor_user_guide_setting")
     */
    public function ajaxSaveDoctorUserGuideSettingAction(Request $request)
    {
        $params = array(
            'siteId' => $request->get('site'),
            'documentName' => Constant::DOCUMENT_NAME_DOCTOR_USER_GUIDE,
            'content' => $request->get('document_content'),
            'effectiveDate' => $request->get('effective_date')
        );

        $response = $this->saveDocumentSetting($params);

        return new JsonResponse($response);
    }

    /**
     * @Route("/admin/patient-faq-setting", name="admin_patient_faq_setting")
     */
    public function PatientFAQSettingAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('UtilBundle:Site')->findAll();
        $currentSite = Common::getCurrentSite($this->container);
        $patientFaq = $em->getRepository('UtilBundle:FileDocumentLog')->getContentBeforeForAdmin(Constant::DOCUMENT_NAME_PATIENT_FAQ, $currentSite);

        return $this->render('AdminBundle:document_setting:document_setting_form.html.twig', array(
            'title' => "Patient's FAQ Setting",
            'document_content' => $patientFaq,
            'sites' => $sites,
            'current_site' => $currentSite,
            'documentName' => Constant::DOCUMENT_NAME_PATIENT_FAQ,
            'documentSelector' => 'patient_faq'
        ));
    }

    /**
     * @Route("/admin/ajax-get-patient-faq-setting", name="admin_get_patient_faq_setting")
     */
    public function ajaxGetPatientFAQSettingAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $site = $em->getRepository('UtilBundle:Site')->find($request->get('site'));
        $patientFaqRepo = $em->getRepository('UtilBundle:FileDocumentLog');
        $patientFaq = $patientFaqRepo->getContentBeforeForAdmin(Constant::DOCUMENT_NAME_PATIENT_FAQ, $site->getName(), true);
        $patientFaqDocument = $patientFaqRepo->getContentBeforeForAdmin(Constant::DOCUMENT_NAME_PATIENT_FAQ, $site->getName());
        if ($patientFaqDocument) {
            $patientFaq['documentId'] = $patientFaqDocument->getFileDocument()->getId();
        }

        return new JsonResponse($patientFaq);
    }

    /**
     * @Route("/admin/ajax-save-patient-faq-setting", name="admin_save_patient_faq_setting")
     */
    public function ajaxSavePatientFAQSettingAction(Request $request)
    {
        $params = array(
            'siteId' => $request->get('site'),
            'documentName' => Constant::DOCUMENT_NAME_PATIENT_FAQ,
            'content' => $request->get('document_content'),
            'effectiveDate' => $request->get('effective_date')
        );

        $response = $this->saveDocumentSetting($params);

        return new JsonResponse($response);
    }

    /**
     * @Route("/admin/doctor-faq-setting", name="admin_doctor_faq_setting")
     */
    public function DoctorFAQSettingAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('UtilBundle:Site')->findAll();
        $currentSite = Common::getCurrentSite($this->container);
        $doctorFaq = $em->getRepository('UtilBundle:FileDocumentLog')->getContentBeforeForAdmin(Constant::DOCUMENT_NAME_DOCTOR_FAQ, $currentSite);

        return $this->render('AdminBundle:document_setting:document_setting_form.html.twig', array(
            'title' => "Doctor's FAQ Setting",
            'document_content' => $doctorFaq,
            'sites' => $sites,
            'current_site' => $currentSite,
            'documentName' => Constant::DOCUMENT_NAME_DOCTOR_FAQ,
            'documentSelector' => 'doctor_faq'
        ));
    }

    /**
     * @Route("/admin/ajax-get-doctor-faq-setting", name="admin_get_doctor_faq_setting")
     */
    public function ajaxGetDoctorFAQSettingAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $site = $em->getRepository('UtilBundle:Site')->find($request->get('site'));
        $doctorFaqRepo = $em->getRepository('UtilBundle:FileDocumentLog');
        $doctorFaq = $doctorFaqRepo->getContentBeforeForAdmin(Constant::DOCUMENT_NAME_DOCTOR_FAQ, $site->getName(), true);
        $doctorFaqDocument = $doctorFaqRepo->getContentBeforeForAdmin(Constant::DOCUMENT_NAME_DOCTOR_FAQ, $site->getName());
        if ($doctorFaqDocument) {
            $doctorFaq['documentId'] = $doctorFaqDocument->getFileDocument()->getId();
        }

        return new JsonResponse($doctorFaq);
    }

    /**
     * @Route("/admin/ajax-save-doctor-faq-setting", name="admin_save_doctor_faq_setting")
     */
    public function ajaxSaveDoctorFAQSettingAction(Request $request)
    {
        $params = array(
            'siteId' => $request->get('site'),
            'documentName' => Constant::DOCUMENT_NAME_DOCTOR_FAQ,
            'content' => $request->get('document_content'),
            'effectiveDate' => $request->get('effective_date')
        );

        $response = $this->saveDocumentSetting($params);

        return new JsonResponse($response);
    }

    /**
     * @Route("/admin/ajax-upload-picture", name="admin_upload_picture")
     */
    public function uploadDocumentImageAction()
    {
        $response = array(
            "fileName" => null,
            "uploaded" => 0,
            "url" => null,
            "message" => ""
        );

        try {
            $common = $this->get('util.common');
            $image = isset($_FILES['upload']) ? $_FILES['upload'] : array();
            $response['fileName'] = $image['name'];
            if($image){
                $image = $common->uploadfile($image,'document_setting/' . $image['name'], true);
            }
            $response['url'] = '/' . $image;
            $response['uploaded'] = 1;
        } catch (\Exception $exception) {
            $response['message'] = $exception->getMessage();
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/admin/document-log-ajax", name="admin_document_log_list_ajax")
     */
    public function documentLogAjaxAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $documentId = $request->get('document_id', '');
        $limit = $request->get('limit');
        $page = $request->get('page');
        $result = $em->getRepository('UtilBundle:FileDocument')->getDocumentLog($documentId, $limit, $page);

        return new JsonResponse($result);
    }

    /**
     * @Route("/admin/view-document-log", name="admin_view_document_log")
     */
    public function viewDocumentLogAction(Request $request)
    {
        $documentLogId = $request->get('documentLogId', '');
        $isAfter = $request->get('isAfter', false);
        $em = $this->getDoctrine()->getManager();
        $documentLog = $em->getRepository('UtilBundle:FileDocumentLog')->find($documentLogId);

        $params = array(
            'title' => $documentLog->getFileDocument()->getName()
        );

        if ($isAfter) {
            $params['content'] = $documentLog->getContentAfter();
        } else {
            $params['content'] = $documentLog->getBeforeFileDocumentLog()->getContentAfter();
        }

        if ($documentLog->getFileDocument()->getName() == Constant::DOCUMENT_NAME_DOCTOR_FAQ || $documentLog->getFileDocument()->getName() == Constant::DOCUMENT_NAME_PATIENT_FAQ) {
            return $this->render('AdminBundle:document_setting:document_output_faq.html.twig', $params);
        } elseif ($documentLog->getFileDocument()->getName() == Constant::DOCUMENT_NAME_DOCTOR_USER_GUIDE) {
            if ($isAfter) {
                $docId = $documentLog->getId();
            } else {
                $docId = $documentLog->getBeforeFileDocumentLog()->getId();
            }
            $fileName = $docId . '_' . str_replace(" ", "_", Constant::DOCUMENT_NAME_DOCTOR_USER_GUIDE) . '.pdf';
            $locationDir = $this->container->getParameter('upload_directory') . '/doctor_guide';
            $fileLocation = $locationDir . '/'. $fileName;

            if (file_exists($fileLocation)) {
                $manager = $this->get('templating.helper.assets');
                $params = array(
                    'title' => Constant::DOCUMENT_NAME_DOCTOR_USER_GUIDE . '.pdf',
                    'fileUrl' => $manager->getUrl('uploads/doctor_guide/' . $fileName)
                );

                return $this->render('AdminBundle:document_setting:document_output_doctor_guide.html.twig', $params);
            } else {
                return $this->render('AdminBundle:document_setting:document_404.html.twig',[ 'documentName' => Constant::DOCUMENT_NAME_DOCTOR_USER_GUIDE ]);
            }
        } else {
            return $this->render('AdminBundle:document_setting:document_output.html.twig', $params);
        }
    }

    /**
     * @Route("/faq/patient", name="patient_faq")
     */
    public function PatientFAQAction()
    {
        $em = $this->getDoctrine()->getManager();
        $patientFaq = $em->getRepository('UtilBundle:FileDocument')->getContentForClient(Constant::DOCUMENT_NAME_PATIENT_FAQ, Common::getCurrentSite($this->container));

        $params = array(
            'title' => Constant::DOCUMENT_NAME_PATIENT_FAQ,
            'content' => $patientFaq['contentAfter']
        );
        return $this->render('AdminBundle:document_setting:document_output_faq.html.twig', $params);
    }


    /**
     * @Route("/faq/doctor", name="doctor_faq")
     */
    public function DoctorFAQAction()
    {
        $em = $this->getDoctrine()->getManager();
        $doctorFaq = $em->getRepository('UtilBundle:FileDocument')->getContentForClient(Constant::DOCUMENT_NAME_DOCTOR_FAQ, Common::getCurrentSite($this->container));

        $params = array(
            'title' => Constant::DOCUMENT_NAME_DOCTOR_FAQ,
            'content' => $doctorFaq['contentAfter']
        );
        return $this->render('AdminBundle:document_setting:document_output_faq.html.twig', $params);
    }

    public function saveDocumentSetting($params)
    {
        $response = array('success' => true);
        try {
            $em = $this->getDoctrine()->getManager();
            $site = $em->getRepository('UtilBundle:Site')->find($params['siteId']);
            $fileDocument = $em->getRepository('UtilBundle:FileDocument')->findOneBy(array(
                'name' => $params['documentName'],
                'site' => $site
            ));
            if (!$fileDocument) {
                $fileDocument = new FileDocument();
            }
            $fileDocument->setName($params['documentName']);
            $fileDocument->setSite($site);
            $em->persist($fileDocument);
            $em->flush();
            $response['file_document_id'] = $fileDocument->getId();

            $fileDocumentLogBefore =  $em->getRepository('UtilBundle:FileDocumentLog')->getContentBefore($fileDocument);
            $fileDocumentLog = new FileDocumentLog();
            $fileDocumentLog->setFileDocument($fileDocument);
            if ($fileDocumentLogBefore) {
                $fileDocumentLog->setBeforeFileDocumentLog($fileDocumentLogBefore);
            }
            $fileDocumentLog->setContentAfter($params['content']);
            $fileDocumentLog->setEffectiveDate(new \DateTime($params['effectiveDate']));
            $em->persist($fileDocumentLog);
            $em->flush();
            $response['file_document_log_id'] = $fileDocumentLog->getId();
        } catch (\Exception $exception) {
            $response['success'] = false;
            $response['message'] = $exception->getMessage();
        }

        return $response;
    }
}
