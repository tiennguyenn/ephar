<?php

namespace AdminBundle\Controller;

use Dompdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UtilBundle\Entity\Log;
use UtilBundle\Entity\RxReminderSetting;
use UtilBundle\Utility\Constant;

class RxOrderSettingController extends Controller
{
    /**
     * @Route("/admin/rx-order/notification-setting", name="admin_rx_order_notification_setting")
     */
    public function rxOrderNotificationSettingAction()
    {
        $em = $this->getDoctrine()->getManager();
        $key = Constant::CODE_NEW_RX_ORDER;
        $rxNotificationSetting = $em->getRepository('UtilBundle:RxReminderSetting')->find($key);

        $responseParams = array(
            'title' => 'New Rx Orders Notification',
            'rx_notification_setting' => $rxNotificationSetting
        );

        return $this->render('AdminBundle:rx/new_order_notification:index.html.twig', $responseParams);
    }

    /**
     * @Route("/admin/rx-order/future-rx-order-notification-setting", name="admin_future_rx_order_notification_setting")
     */
    public function futureRxOrderNotificationSettingAction()
    {
        $em = $this->getDoctrine()->getManager();
        $key = Constant::CODE_FUTURE_RX_ORDER;
        $futureRxOrderNotificationSetting = $em->getRepository('UtilBundle:RxReminderSetting')->find($key);

        $responseParams = array(
            'title' => 'Patient\'s Welcome Email (Future RX Orders)',
            'rx_notification_setting' => $futureRxOrderNotificationSetting
        );

        return $this->render('AdminBundle:rx/future_order_notification:index.html.twig', $responseParams);
    }

    /**
     * @Route("/rx-order-notification-log/print", name="rx_order_notification_print_logs")
     */
    public function printLogsAction() {
        $params['title'] = 'rx_order_notification_setting';
        $params['module'] = 'rx_order_notification_setting';
        $logs = $this->getDoctrine()->getRepository('UtilBundle:Log')->getLogs($params);

        for ($i = 0; $i < count($logs); $i++) {
            $logs[$i]['oldValue'] = json_decode($logs[$i]['oldValue'], true);
            $logs[$i]['newValue'] = json_decode($logs[$i]['newValue'], true);
        }
        $title = 'Rx Order Notification Settings Log';
        $html = $this->renderView('AdminBundle:rx/new_order_notification:rx_order_notification_print_log.html.twig', array("logs" => $logs, 'title' => $title));

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
    }

    /**
     * @Route("/rx-order-notification-log", name="rx_order_notification_logs")
     */
    public function ajaxLogs() {
        $params['title'] = 'rx_order_notification_setting';
        $params['module'] = 'rx_order_notification_setting';
        $logs = $this->getDoctrine()->getManager()->getRepository('UtilBundle:Log')->getLogs($params);
        // Decode json
        for ($i = 0; $i < count($logs); $i++) {
            $logs[$i]['oldValue'] = json_decode($logs[$i]['oldValue'], true);
            $logs[$i]['newValue'] = json_decode($logs[$i]['newValue'], true);
        }
        return $this->render('AdminBundle:rx/new_order_notification:rx_order_notification_logs.html.twig'
            , array("logs" => $logs));
    }

    /**
     * @Route("/admin/rx-order/ajax-save-notification-setting", name="admin_rx_order_ajax_save_notification_setting")
     */
    public function ajaxSaveRxOrderNotificationSettingAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();

        $response = array('success' => true);
        try {
            $key = Constant::CODE_NEW_RX_ORDER;
            $rxNotificationSetting = $em->getRepository('UtilBundle:RxReminderSetting')->find($key);

            if (!$rxNotificationSetting) {
                $rxNotificationSetting = new RxReminderSetting();
                $rxNotificationSetting->setReminderCode($key);

                $oldLogs = null;
            } else {
                $oldLogs = array(
                    'duration_time' => $rxNotificationSetting->getDurationTime(),
                    'duration_unit' => $rxNotificationSetting->getTimeUnit(),
                    'email_subject' => $rxNotificationSetting->getTemplateSubjectEmail(),
                    'email_message' => $rxNotificationSetting->getTemplateBodyEmail(),
                    'sms_message' => $rxNotificationSetting->getTemplateSms()
                );
            }
            $rxNotificationSetting->setReminderName('newordernotification');
            $rxNotificationSetting->setDurationTime($data['delay_time']);
            $rxNotificationSetting->setTimeUnit($data['delay_time_unit']);
            $rxNotificationSetting->setTemplateSubjectEmail($data['email_subject']);
            $rxNotificationSetting->setTemplateBodyEmail($data['email_message']);
            $rxNotificationSetting->setTemplateSms($data['sms_message']);

            $newLogs = array(
                'duration_time' => $rxNotificationSetting->getDurationTime(),
                'duration_unit' => $rxNotificationSetting->getTimeUnit(),
                'email_subject' => $rxNotificationSetting->getTemplateSubjectEmail(),
                'email_message' => $rxNotificationSetting->getTemplateBodyEmail(),
                'sms_message' => $rxNotificationSetting->getTemplateSms()
            );
            $em->persist($rxNotificationSetting);
            $em->flush();

            $this->saveLog($oldLogs, $newLogs);

        } catch (\Exception $exception) {
            $response['success'] = false;
            $response['message'] = $exception->getMessage();
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/admin/rx-order/ajax-save-future-rx-order-notification-setting", name="admin_ajax_save_future_rx_order_notification_setting")
     */
    public function ajaxSaveFutureRxOrderNotificationSettingAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();

        $response = array('success' => true);
        try {
            $key = Constant::CODE_FUTURE_RX_ORDER;
            $rxNotificationSetting = $em->getRepository('UtilBundle:RxReminderSetting')->find($key);

            if (!$rxNotificationSetting) {
                $rxNotificationSetting = new RxReminderSetting();
                $rxNotificationSetting->setReminderCode($key);

                $oldLogs = null;
            } else {
                if ($data['type'] == 'email') {
                    $oldLogs = array(
                        'email_subject' => $rxNotificationSetting->getTemplateSubjectEmail(),
                        'email_message' => $rxNotificationSetting->getTemplateBodyEmail()
                    );
                } elseif ($data['type'] == 'sms') {
                    $oldLogs = array(
                        'sms_message' => $rxNotificationSetting->getTemplateSms()
                    );
                }
            }

            if ($data['type'] == 'email') {
                $rxNotificationSetting->setTemplateSubjectEmail($data['email_subject']);
                $rxNotificationSetting->setTemplateBodyEmail($data['email_message']);

                $newLogs = array(
                    'email_subject' => $rxNotificationSetting->getTemplateSubjectEmail(),
                    'email_message' => $rxNotificationSetting->getTemplateBodyEmail()
                );
            } elseif ($data['type'] == 'sms') {
                $rxNotificationSetting->setTemplateSms($data['sms_message']);

                $newLogs = array(
                    'sms_message' => $rxNotificationSetting->getTemplateSms()
                );
            }
            $rxNotificationSetting->setReminderName('futureordernotification');

            $em->persist($rxNotificationSetting);
            $em->flush();

            $this->saveLog($oldLogs, $newLogs);

        } catch (\Exception $exception) {
            $response['success'] = false;
            $response['message'] = $exception->getMessage();
        }

        return new JsonResponse($response);
    }

    public function saveLog($oldData, $newData) {
        if (serialize($oldData) == serialize($newData)) {
            return;
        }
        $params['title'] = 'rx_order_notification_setting';
        $params['action'] = 'update';
        $params['module'] = 'rx_order_notification_setting';
        $params['oldValue'] = json_encode($oldData);
        $params['newValue'] = json_encode($newData);

        $params['createdBy'] = $this->getUser()->getLoggedUser()->getFirstName() . ' ' .
            $this->getUser()->getLoggedUser()->getLastName();
        $this->insertLog($params);
    }

    public function insertLog($params) {
        $this->getDoctrine()->getManager()
            ->getRepository('UtilBundle:Log')->insert($params);
    }
}
