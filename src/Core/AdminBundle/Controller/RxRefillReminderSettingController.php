<?php

namespace AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UtilBundle\Utility\MsgUtils;
use AdminBundle\Form\RxRefillReminderSettingType;
use Symfony\Component\HttpFoundation\Response;
use Dompdf\Dompdf;
use UtilBundle\Utility\Constant;

/**
 * author: luyen nguyen
 * date: 09/28/2017
 */
class RxRefillReminderSettingController extends Controller {

    /**
     * Rx Refill Setting Reminder
     * @Route("/admin/rx_refill_setting", name="admin_rx_refill_setting")
     */
    public function rxRefillReminderAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $platformSetting = $em->getRepository('UtilBundle:PlatformSettings')->getPlatFormSetting();
		$emailSettings = $em->getRepository('UtilBundle:RxReminderSetting')->findBy(array(
			'reminderName' => 'reminder_emails'
		));
		$settings = array();
		foreach ($emailSettings as $item) {
			if ($item->getReminderCode() == Constant::REMINDER_CODE_EM_P1) {
				$settings['first_patient'] = array(
					'days' => $item->getDurationTime(),
					'subject' => $item->getTemplateSubjectEmail(),
					'email' => $item->getTemplateBodyEmail(),
					'sms' => $item->getTemplateSms()
				);
			} elseif ($item->getReminderCode() == Constant::REMINDER_CODE_EM_D1) {
				$settings['first_doctor'] = array(
					'days' => $item->getDurationTime(),
					'subject' => $item->getTemplateSubjectEmail(),
					'email' => $item->getTemplateBodyEmail(),
					'sms' => $item->getTemplateSms()
				);
			} elseif ($item->getReminderCode() == Constant::REMINDER_CODE_EM_P2) {
				$settings['second_patient'] = array(
					'days' => $item->getDurationTime(),
					'subject' => $item->getTemplateSubjectEmail(),
					'email' => $item->getTemplateBodyEmail(),
					'sms' => $item->getTemplateSms()
				);
			} elseif ($item->getReminderCode() == Constant::REMINDER_CODE_EM_D2) {
				$settings['second_doctor'] = array(
					'days' => $item->getDurationTime(),
					'subject' => $item->getTemplateSubjectEmail(),
					'email' => $item->getTemplateBodyEmail(),
					'sms' => $item->getTemplateSms()
				);
			}
		}
		
        // Init form reminder
        $formreminderthirty = $this->createForm(new RxRefillReminderSettingType($platformSetting, 'formreminderthirty'));
        $formremindersixty = $this->createForm(new RxRefillReminderSettingType($platformSetting, 'formremindersixty'));
		$formreminderemails = $this->createForm(new RxRefillReminderSettingType($settings, 'formreminderemails'));
        $params['operationsCountryId'] = $platformSetting['operationsCountryId'];
        $message = '';
        // Handle request
        if ($request->isMethod('POST') && isset($request->request->get('rx_refill_reminder_setting')['reminderthirtydays'])) {
            $formreminderthirty->handleRequest($request);
            if ($formreminderthirty->isSubmitted() && $formreminderthirty->isValid()) {
                $oldValue = $platformSetting['reminderRxRefill30'];
                $title = 'reminder_rx_refill_30';
                $data = $this->parserData($formreminderthirty->getData());
                $params['reminderRxRefill30'] = $data['reminderthirtydays'];
                $message = 'Reminders for supply length of 30 days';
                $result = $em->getRepository('UtilBundle:PlatformSettings')->update($params);
                $newValue = $data['reminderthirtydays'];
            }
        } else if ($request->isMethod('POST') && isset($request->request->get('rx_refill_reminder_setting')['remindersixtydays'])) {
            $formremindersixty->handleRequest($request);
            if ($formremindersixty->isSubmitted() && $formremindersixty->isValid()) {
                $oldValue = $platformSetting['reminderRxRefill60'];
                $title = 'reminder_rx_refill_60';
                $data = $this->parserData($formremindersixty->getData());
                $params['reminderRxRefill60'] = $data['remindersixtydays'];
                $message = 'Reminders for supply length of 60 days and above';
                $result = $em->getRepository('UtilBundle:PlatformSettings')->update($params);
                $newValue = $data['remindersixtydays'];
            }
		} else if ($request->isMethod('POST') && isset($request->request->get('rx_refill_reminder_setting')['reminderemails'])) {
            $formreminderemails->handleRequest($request);
            if ($formreminderemails->isSubmitted() && $formreminderemails->isValid()) {
				$title = 'reminder_emails_for_patient_and_doctor';
				$em->beginTransaction();
				try {
					$data = $this->parserData($formreminderemails->getData());
					$oldValue = array();
					$newValue = array();
					foreach ($emailSettings as $item) {
						$oldValue[$item->getReminderCode()] = array(
							'duration_time' => $item->getDurationTime(),
							'template_subject_email' => $item->getTemplateSubjectEmail(),
							'template_body_email' => $item->getTemplateBodyEmail(),
							'template_sms' => $item->getTemplateSms()
						);
						
						if ($item->getReminderCode() == Constant::REMINDER_CODE_EM_P1) {
							$item->setDurationTime($data['first_patient_days']);
							$item->setTemplateSubjectEmail($data['first_patient_subject']);
							$item->setTemplateBodyEmail($data['first_patient_email']);
							$item->setTemplateSms(substr($data['first_patient_sms'], 0, Constant::MAX_SMS_LENGTH));
						} elseif ($item->getReminderCode() == Constant::REMINDER_CODE_EM_D1) {
							$item->setDurationTime($data['first_doctor_days']);
							$item->setTemplateSubjectEmail($data['first_doctor_subject']);
							$item->setTemplateBodyEmail($data['first_doctor_email']);
						} elseif ($item->getReminderCode() == Constant::REMINDER_CODE_EM_P2) {
							$item->setDurationTime($data['second_patient_days']);
							$item->setTemplateSubjectEmail($data['second_patient_subject']);
							$item->setTemplateBodyEmail($data['second_patient_email']);
							$item->setTemplateSms(substr($data['second_patient_sms'], 0, Constant::MAX_SMS_LENGTH));
						} elseif ($item->getReminderCode() == Constant::REMINDER_CODE_EM_D2) {
							$item->setDurationTime($data['second_doctor_days']);
							$item->setTemplateSubjectEmail($data['second_doctor_subject']);
							$item->setTemplateBodyEmail($data['second_doctor_email']);
						}
						$em->persist($item);
						$em->flush();
						
						$newValue[$item->getReminderCode()] = array(
							'duration_time' => $item->getDurationTime(),
							'template_subject_email' => $item->getTemplateSubjectEmail(),
							'template_body_email' => $item->getTemplateBodyEmail(),
							'template_sms' => $item->getTemplateSms()
						);
					}
					
					$em->commit();
					$result = 'Emails';
					$message = 'Reminder emails for patient and doctor';
				} catch (\Exception $ex) {
					$result = false;
					$em->rollback();
					$message = $ex->getMessage();
				}
            }
        }
        // Display message
        if (isset($result)) {
            if ($result) {
				if ($result == 'Emails') {
					// Insert logs
					$params['entityId'] = $platformSetting['operationsCountryId'];
					$params['title'] = isset($title) ? $title : '';
					$params['action'] = 'update';
					$params['module'] = 'rx_reminder_setting';
					$params['oldValue'] = isset($oldValue) ? json_encode($oldValue) : '';
					$params['newValue'] = isset($newValue) ? json_encode($newValue) : '';
					$params['createdBy'] = $this->getUser()->getLoggedUser()->getFirstName() . ' ' .
							$this->getUser()->getLoggedUser()->getLastName();
					$this->insertLog($params);
				} else {
					// Insert logs
					$params['entityId'] = $platformSetting['operationsCountryId'];
					$params['title'] = isset($title) ? $title : '';
					$params['action'] = 'update';
					$params['module'] = 'platform_settings';
					$params['oldValue'] = isset($oldValue) ? $oldValue : '';
					$params['newValue'] = isset($newValue) ? $newValue : '';
					$params['createdBy'] = $this->getUser()->getLoggedUser()->getFirstName() . ' ' .
							$this->getUser()->getLoggedUser()->getLastName();
					$this->insertLog($params);
				}
				
                $this->get('session')->getFlashBag()->add('success'
                        , MsgUtils::generate('msgUpdatedSuccess', $message));
            } else {
                $this->get('session')->getFlashBag()->add('danger'
                        , MsgUtils::generate('msgCannotEdited', $message));
            }
        }
        $info['formreminderthirty'] = $formreminderthirty->createView();
        $info['formremindersixty'] = $formremindersixty->createView();
		$info['formreminderemails'] = $formreminderemails->createView();
        return $this->render('AdminBundle:rx\reminder_setting:rx_refill_reminder_setting.html.twig', $info);
    }

    /**
     * Rx Refill Setting Reminder
     * @Route("/logs/{logType}", name="rx_refill_reminder_logs")
     */
    public function ajaxLogs($logType) {
        $em = $this->getDoctrine()->getManager();
        $params['title'] = 'reminder_rx_refill_' . $logType;
        $params['module'] = 'platform_settings';
        $logs = $em->getRepository('UtilBundle:Log')->getLogs($params);
        return $this->render('AdminBundle:rx\reminder_setting:logs.html.twig', array("logs" => $logs));
    }

    /**
     * @Route("/logs/print/{logType}", name="rx_refill_reminder_print_logs")
     */
    public function printLogsAction($logType) {
        if ($logType == Constant::REMINDER_30_DAYS || $logType == Constant::REMINDER_60_DAYS) {
            $params['title'] = 'reminder_rx_refill_' . $logType;
            $params['module'] = 'platform_settings';
            $logs = $this->getDoctrine()->getRepository('UtilBundle:Log')->getLogs($params);
            $title = '';
            if ($logType == Constant::REMINDER_30_DAYS) {
                $title = 'REMINDERS FOR SUPPLY LENGTH OF 30 DAYS';
            } else if ($logType == Constant::REMINDER_60_DAYS) {
                $title = 'REMINDERS FOR SUPPLY LENGTH OF 60 DAYS AND ABOVE';
            }
            $html = $this->renderView('AdminBundle:rx\reminder_setting:print.html.twig'
                    , array("logs" => $logs, 'title' => $title));

            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $response = new Response();
            $response->setContent($dompdf->output());
            $response->setStatusCode(200);
            $response->headers->set('Content-Disposition', 'attachment');
            $response->headers->set('Content-Type', 'application/pdf');

            return $response;
        } else {
            return $this->redirectToRoute('not_found');
        }
    }

    /**
     * parser data
     * @param type $formData
     */
    public function parserData($formData) {
        $data = [];
        if (!empty($formData)) {
            foreach ($formData as $key => $value) {
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /**
     * Insert Log
     * @param type $params
     */
    public function insertLog($params) {
        $this->getDoctrine()->getManager()
                ->getRepository('UtilBundle:Log')->insert($params);
    }

}
