<?php

namespace AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UtilBundle\Utility\Constant;
use UtilBundle\Entity\RxReminderSetting;
use AdminBundle\Form\RxReminderSettingType;
use Dompdf\Dompdf;
use UtilBundle\Utility\MsgUtils;
use UtilBundle\Utility\Utils;

class RxReminderSettingController extends Controller
{
    private $cycleOne = array(
        Constant::REMINDER_CODE_C1_P1 => 'reminder1',
        Constant::REMINDER_CODE_C1_P2 => 'reminder2',
        Constant::REMINDER_CODE_C1_P3 => 'reminder3',
        Constant::REMINDER_CODE_C1_FP => 'failedetopatient',
        Constant::REMINDER_CODE_C1_FD =>'failedetodoctor',
    );

    private $cycleTwo = array(
        Constant::REMINDER_CODE_C2_FOS => 'fosetting',
        Constant::REMINDER_CODE_C2_GPS => 'gpsetting',
        Constant::REMINDER_CODE_C2_FP => 'c2failedpatient',
        Constant::REMINDER_CODE_C2_FD => 'c2faileddoctor',
        Constant::REMINDER_CODE_C2_FA => 'c2failedadmin'
    );

    /**
     * @Route("/admin/rx-reminder-setting", name="admin_rx_reminder_setting")
     */
    public function indexAction(Request $request)
    {
        $options = array(
            'attr' => array(
                'id' => 'cycleOneForm',
                'class' => 'form-horizontal'
            ),
            'action' => $this->generateUrl('admin_rx_reminder_setting_c1')
        );
        $formBuilder = $this->createMyFormBuilder($this->cycleOne, $options);
        $cycleOne = $formBuilder->getForm();

        $options = array(
            'attr' => array(
                'id' => 'cycleTwoForm',
                'class' => 'form-horizontal'
            ),
            'action' => $this->generateUrl('admin_rx_reminder_setting_c2')
        );
        $formBuilder = $this->createMyFormBuilder($this->cycleTwo, $options);
        $cycleTwo = $formBuilder->getForm();

        return $this->render('AdminBundle:rx\reminder_setting:index.html.twig', array(
            'cycleOne' => $cycleOne->createView(),
            'cycleTwo' => $cycleTwo->createView(),
        ));
    }

    /**
     * @Route("/admin/rx-reminder-setting-c1", name="admin_rx_reminder_setting_c1")
     */
    public function cycleOneAction(Request $request)
    {
        $oldData = $this->getRxReminderCycleOneSetting();
        $oldData = $this->parseData($oldData);
        $options = array(
            'attr' => array('id' => 'cycleOneForm'),
            'action' => $this->generateUrl('admin_rx_reminder_setting_c1')
        );
        $formBuilder = $this->createMyFormBuilder($this->cycleOne, $options);
        $cycleOne = $formBuilder->getForm();
        $cycleOne->handleRequest($request);

        if ($cycleOne->isSubmitted() && $cycleOne->isValid()) {
            $em = $this->getDoctrine()->getManager();
            foreach ($cycleOne->getData() as $value) {
                $value->setTemplateSms(substr($value->getTemplateSms(), 0, Constant::MAX_SMS_LENGTH));
                $em->persist($value);
            }
            $em->flush();

            $newData = $cycleOne->getData();
            $newData = $this->parseData($newData);
            $this->saveLog($oldData, $newData, true);

            $this->get('session')->getFlashBag()->add('success'
                        , MsgUtils::generate('msgUpdatedSuccess', 'Cycle 1 Reminders'));
        } else {
            $this->get('session')->getFlashBag()->add('danger'
                        , MsgUtils::generate('msgCannotEdited', 'Cycle 1 Reminders'));
        }

        return $this->redirectToRoute('admin_rx_reminder_setting');
    }

    /**
     * @Route("/admin/rx-reminder-setting-c2", name="admin_rx_reminder_setting_c2")
     */
    public function cycleTwoAction(Request $request)
    {
        $oldData = $this->getRxReminderCycleTwoSetting();
        $oldData = $this->parseData($oldData);
        $options = array(
            'attr' => array('id' => 'cycleTwoForm'),
            'action' => $this->generateUrl('admin_rx_reminder_setting_c2')
        );
        $formBuilder = $this->createMyFormBuilder($this->cycleTwo, $options);
        $cycleTwo = $formBuilder->getForm();
        $cycleTwo->handleRequest($request);

        if ($cycleTwo->isSubmitted() && $cycleTwo->isValid()) {
            $em = $this->getDoctrine()->getManager();
            foreach ($cycleTwo->getData() as $value) {
                $value->setTemplateSms(substr($value->getTemplateSms(), 0, Constant::MAX_SMS_LENGTH));
                $em->persist($value);
            }
            $em->flush();
            $newData = $cycleTwo->getData();
            $newData = $this->parseData($newData);
            $this->saveLog($oldData, $newData, false);
            $this->get('session')->getFlashBag()->add('success'
                        , MsgUtils::generate('msgUpdatedSuccess', 'Cycle 2 Reminder'));
        } else {
            $this->get('session')->getFlashBag()->add('danger'
                        , MsgUtils::generate('msgCannotEdited', 'Cycle 2 Reminder'));
        }

        return $this->redirectToRoute('admin_rx_reminder_setting');
    }

    /**
     * @Route("/reminderlogs/{logType}", name="rx_reminder_logs")
     */
    public function ajaxLogs($logType) {
        $params['title'] = 'cylce_' . $logType . '_reminder_setting';
        $params['module'] = 'rx_reminder_setting';
        $logs = $this->getDoctrine()->getManager()->getRepository('UtilBundle:Log')->getLogs($params);
        // Decode json
        for ($i = 0; $i < count($logs); $i++) {
            $logs[$i]['oldValue'] = json_decode($logs[$i]['oldValue'], true);
            $logs[$i]['newValue'] = json_decode($logs[$i]['newValue'], true);
        }
        return $this->render('AdminBundle:rx\reminder_setting:reminderlogs.html.twig'
                        , array("logs" => $logs, "logType" => $logType));
    }

    /**
     * @Route("/reminderlogs/print/{logType}", name="rx_reminder_print_logs")
     */
    public function printLogsAction($logType) {
        $params['title'] = 'cylce_' . $logType . '_reminder_setting';
        $params['module'] = 'rx_reminder_setting';
        $logs = $this->getDoctrine()->getRepository('UtilBundle:Log')->getLogs($params);
        // Decode json
        for ($i = 0; $i < count($logs); $i++) {
            $logs[$i]['oldValue'] = json_decode($logs[$i]['oldValue'], true);
            $logs[$i]['newValue'] = json_decode($logs[$i]['newValue'], true);
        }
        $title = '';
        if ($logType == Constant::CYCLE_ONE) {
            $title = 'CYCLE 1 REMINDERS POLICY';
        } else if ($logType == Constant::CYCLE_TWO) {
            $title = 'CYCLE 2 - EXTENSION OF PRESCRIPTION ORDER (GRACE PERIOD SETTINGS)';
        }
        $html = $this->renderView('AdminBundle:rx\reminder_setting:reminderprint.html.twig'
                , array("logs" => $logs, 'title' => $title, 'logType' => $logType));

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
     * @author Tien Nguyen
     */
    private function createMyFormBuilder($formNames, $options)
    {
        $rxRSR = $this->getRxReminderSettingRepository();

        $options['csrf_protection'] = false;
        $formBuilder = $this->createFormBuilder(null, $options);

        foreach ($formNames as $key => $value) {
            $form = $formBuilder->create($value, new RxReminderSettingType);

            $setting = $rxRSR->find($key);
            if (!$setting) {
                $setting = new RxReminderSetting();
            }
            $setting->setReminderCode($key);
            $setting->setReminderName($value);
            $form->setData($setting);
            $formBuilder->add($form);
        }

        return $formBuilder;
    }

    /**
     * Get Rx Reminder Setting Repository
     * @return type
     */
    private function getRxReminderSettingRepository()
    {
        return $this->getDoctrine()->getRepository('UtilBundle:RxReminderSetting');
    }

    /**
     * Get Rx Reminder Cycle One Setting
     * @return type
     * author Luyen Nguyen
     */
    public function getRxReminderCycleOneSetting() {
        $oldData = $this->getDoctrine()->getRepository('UtilBundle:RxReminderSetting')
                ->getRxReminderCycleOneSetting();
        return $oldData;
    }

    /**
     * Get Rx Reminder Cycle Two Setting
     * @return type
     * author Luyen Nguyen
     */
    public function getRxReminderCycleTwoSetting() {
        $oldData = $this->getDoctrine()->getRepository('UtilBundle:RxReminderSetting')
                ->getRxReminderCycleTwoSetting();
        return $oldData;
    }

    /**
     * Parse log
     * @param type $datas
     * author Luyen Nguyen
     */
    public function saveLog($oldData, $newData, $cycleOne) {
        // just insert log into database if old data differ to new data
        if (serialize($oldData) == serialize($newData)) {
            return;
        }

        if ($cycleOne) {
            $params['title'] = 'cylce_one_reminder_setting';
        } else {
            $params['title'] = 'cylce_two_reminder_setting';
        }
        $params['action'] = 'update';
        $params['module'] = 'rx_reminder_setting';
        $params['oldValue'] = $oldData;
        $params['newValue'] = $newData;
        
        $params['createdBy'] = $this->getUser()->getLoggedUser()->getFirstName() . ' ' .
                $this->getUser()->getLoggedUser()->getLastName();
        $this->insertLog($params);
    }

    /**
     * End code data
     * @param type $datas
     * @return type
     * author Luyen Nguyen
     */
    public function parseData($datas) {
        $arrData = array();
        foreach ($datas as $data) {
            $arrData[$data->getReminderCode()] = array("reminderName" => $data->getReminderName()
                , "durationTime" => $data->getDurationTime()
                , "expiredTime" => $data->getExpiredTime()
                , "timeUnit" => $data->getTimeUnit()
                , "timeUnitExpire" => $data->getTimeUnitExpire()
                , "templateBodyEmail" => str_replace("<br>", "", $data->getTemplateBodyEmail())
                , "templateSubjectEmail" => $data->getTemplateSubjectEmail()
                , "templateSms" => str_replace("<br>", "", $data->getTemplateSms()));
        }
        ksort($arrData);
        return json_encode($arrData);
    }

    /**
     * Insert Log
     * @param type $params
     * author Luyen Nguyen
     */
    public function insertLog($params) {
        $this->getDoctrine()->getManager()
                ->getRepository('UtilBundle:Log')->insert($params);
    }

}
