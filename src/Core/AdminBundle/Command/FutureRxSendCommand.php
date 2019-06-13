<?php

namespace AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\RouterInterface;
use UtilBundle\Utility\Constant;

class FutureRxSendCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:future-rx-send')
            ->setDescription('Send Future Rx Order Email and SMS Notification')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('============= Start ===========');
        $date = new \DateTime();
        $output->writeln($date->format("Y-m-d H:i:s"));

        $em = $this->getContainer()->get('doctrine')->getManager();
        $patientRepository = $em->getRepository('UtilBundle:Patient');
        $futureRxOrders = $em->getRepository('UtilBundle:Rx')->getFutureRxOrders();

        foreach ($futureRxOrders as $order) {

            $doctor = $order->getDoctor();
            if ($doctor) {
                $doctorId = $doctor->getId();
            } else {
                continue;
            }

            $patient = $order->getPatient();
            if ($patient) {
                $patientId = $patient->getId();
            } else {
                continue;
            }

            $params = array(
                'rxObj' => $order,
                'patientId' => $patientId,
                'otherDiagnosisValues' => $patientRepository->getOtherDiagnosticValues($patientId),
                'doctorId' => $doctorId,
                'doctor' => $doctor,
                'rx' => $order,
                'orderNumber' => $order->getOrderNumber()
            );

            $sent = $this->sendToPatient($params);

            if ($sent) {
                $order->setScheduledSentOn(new \DateTime());
                $em->persist($order);
                $em->flush();

                $output->writeln('OK        Send notification for order number: ' . $order->getOrderNumber());
            } else {
                $output->writeln('Failed    Send notification for order number: ' . $order->getOrderNumber() . ' failed to send.');
            }

        }

        $output->writeln('============= End ===========');
    }

    private function sendToPatient($params)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $baseUrl = $this->getContainer()->getParameter('base_url');

        $router = $this->getContainer()->get('router');

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
        $rxNotificationSetting = $em->getRepository('UtilBundle:RxReminderSetting')->find($key);

        $rxRepository = $em->getRepository('UtilBundle:Rx');
        $patientInformation = $rxRepository->getPatientInformation($params);
        $clinicInformation = $rxRepository->getClinicInformation($params);
        $doctorInformation = $rxRepository->getDoctorInformation($params);
        $orderNumber = $params['orderNumber'];

        $orderLink = $baseUrl . $router->generate('prescription_index', array('orderNumber' => $orderNumber));
        $button = '<a href="'. $orderLink .'" style="border-color: #249987;background-color: #249987;border-radius: 0 5px;display: inline-block;height:32px;line-height: 32px;padding: 0 12px;text-transform: uppercase;text-decoration: none;color: #ffffff;">
                  <font color="#ffffff" face="Arial, Helvetica, sans-serif" style="font-size: 12px">View and confirm your prescription</font>
                  </a>';

        $mailTemplate = "";
        $SMSTemplate = "";
        if ($rxNotificationSetting) {

            $mailSubject = $rxNotificationSetting->getTemplateSubjectEmail();
            $mailTemplate = $rxNotificationSetting->getTemplateBodyEmail();
            $SMSTemplate = $rxNotificationSetting->getTemplateSms();

            $bodyEmailParams = array(
                'patient_name' => $patientInformation['name'],
                'doctor_name' => $doctorInformation['name'],
                'confirm_prescription_button' => $button,
                'link' => "<a href='$orderLink'>$orderLink</a>",
                'clinic_name' => $clinicInformation['name']
            );

            foreach ($bodyEmailParams as $key => $value) {
                $mailTemplate = str_replace('{' . $key . '}', $value, $mailTemplate);
            }

            $bodySmsParams = array(
                'patient_name' => $patientInformation['name'],
                'doctor_name' => $doctorInformation['name'],
                'link' => $orderLink,
                'clinic_name' => $clinicInformation['name']
            );

            foreach ($bodySmsParams as $key => $value) {
                $SMSTemplate = str_replace('{' . $key . '}', $value, $SMSTemplate);
            }
        }

        $parameters['patientInformation'] = $rxRepository->getPatientInformation($params);
        $parameters['clinicInformation'] = $rxRepository->getClinicInformation($params);
        $parameters['doctorInformation'] = $rxRepository->getDoctorInformation($params);
        $parameters['orderNumber'] = $params['orderNumber'];
        $parameters['prescription_email'] = $mailTemplate;
        $parameters['baseUrl'] = $baseUrl;
        $body  = $this->getContainer()->get('templating')->render('DoctorBundle:emails:rx-order-confirmation.html.twig', $parameters);

        $mailParams = array(
            'title' => isset($mailSubject) ? $mailSubject : 'Confirm your prescription',
            'body'  => $body,
            'to'    => $arrTo
        );

        $timeToSend = new \DateTime();
        if ($this->checkTimeToSendMessage($rxId,'email')) {
            foreach ($mailParams['to'] as $value) {
                $emailSend = new \UtilBundle\Entity\EmailSend();
                $emailSend->setSubject($mailParams['title']);
                $emailSend->setContent($mailParams['body']);
                $emailSend->setFrom($this->getContainer()->getParameter('primary_email'));
                $emailSend->setTo($value);
                $emailSend->setTimeToSend($timeToSend);
                $emailSend->setCreatedOn(new \DateTime());
                $emailSend->setRxId($rxId);
                $em->persist($emailSend);
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
                $em->persist($smsSend);
            }
        }

        return true;
    }

    private function checkTimeToSendMessage($rxId, $type = 'email')
    {
        $timeNow = new \DateTime();
        $em = $this->getContainer()->get('doctrine')->getManager();

        if ($type == 'email') {
            $messages = $em->getRepository('UtilBundle:EmailSend')->findBy(['rxId' => $rxId]);
        } else {
            $messages = $em->getRepository('UtilBundle:SmsSend')->findBy(['rxId' => $rxId]);
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

}
