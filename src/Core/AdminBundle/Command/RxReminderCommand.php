<?php

namespace AdminBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Doctrine\DBAL\Connection;
use UtilBundle\Utility\Constant;
use UtilBundle\Entity\Rx;
use UtilBundle\Entity\RxReminderSetting;

class RxReminderCommand extends ContainerAwareCommand
{
    private $output;

    protected function configure()
    {
        $this->setName('app:rx-reminder');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $date = new \DateTime();
        $output->writeln('============='. $date->format("Y-m-d H:i:s") .' Start ===========');

        $em = $this->getContainer()->get('doctrine')->getManager();

        // Cylce 1 pending
        $criteria = array(
            'status' => Constant::RX_STATUS_PENDING,
            'isOnHold' => 0,
            'deletedOn' => null
        );
        $listRx = $em->getRepository('UtilBundle:Rx')->findBy($criteria);

        $output->writeln("Start for pending");
        foreach ($listRx as $value) {
            if ($value->getIsScheduledRx()) {
                if ($value->getScheduledSendDate()->format('Y-m-d') != date('Y-m-d')) {
                    continue;
                } else {
                    if ($value->getScheduledSentOn() == null) {
                        continue;
                    }
                }
            }

            $stage = $this->getRxReminderStage($value);
            if ($stage) {
                $funcname = 'reminder' . $stage;
                $this->$funcname($value);
            }
        }
        $output->writeln("End for pending");

        $em->flush();

        // Cycle 2 failed
        $arrStatus = array(Constant::RX_STATUS_PAYMENT_FAILED, Constant::RX_STATUS_DEAD);
        $listRx = $em->getRepository('UtilBundle:Rx')
            ->createQueryBuilder('rx')
            ->where('rx.status IN (:status)')
            ->andwhere('rx.deletedOn IS NULL')
            ->setParameter('status', $arrStatus, Connection::PARAM_STR_ARRAY)
            ->getQuery()
            ->getResult();

        $output->writeln("Start for failed");
        foreach ($listRx as $value) {
            $this->checkFailedRx($value);
        }
        $output->writeln("End for failed");

        $em->flush();

        $date = new \DateTime();
        $output->writeln('============= '. $date->format("Y-m-d H:i:s") .' End reminder ===========');
    }

    private function checkFailedRx(Rx $rx)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $criteria = array(
            'reminderCode' => Constant::REMINDER_CODE_C2_FOS
        );
        $rxReminder = $em->getRepository('UtilBundle:RxReminderSetting')->findOneBy($criteria);

        if (!$rxReminder) {
            return false;
        }

        $status = $rx->getStatus();

        $params = array(
            'rx' => $rx,
            'rxReminder' => $rxReminder
        );
        if (Constant::RX_STATUS_DEAD == $status) {
            $params['isExpire'] = true;
        }

        $valid = $this->checkDuration($params);
        if (!$valid) {
            return false;
        }

        if (Constant::RX_STATUS_PAYMENT_FAILED == $status) {
            $rx->setStatus(Constant::RX_STATUS_DEAD);

            // Write log
            $params = array('rxObj' => $rx, 'isCron' => true);
            $em->getRepository('UtilBundle:Rx')->manageRXStatusLog($params);

            $this->output->writeln("Change status for rx: " . $rx->getId() . " to " . Constant::RX_STATUS_DEAD);
        }
        if (Constant::RX_STATUS_DEAD == $status) {
            $rx->setDeletedOn(new \DateTime());
            $this->output->writeln("Delete rx: " . $rx->getId());
        }

        $em->persist($rx);
    }

    /**
     * Get rx reminder stage
     * result are 0, 1, 2, 3 or 4
     */
    private function getRxReminderStage(RX $rx)
    {
        if ($rx->getResentOn()) {
            return 4;
        }

        $result = 0;

        $reminderCode = $rx->getReminderCode();
        if ($reminderCode) {
            $reminderCode = $reminderCode->getReminderCode();
        }
        if (!$reminderCode) {
            $reminderCode = Constant::REMINDER_CODE_C1_P1;
        } elseif (Constant::REMINDER_CODE_C1_P1 == $reminderCode) {
            $reminderCode = Constant::REMINDER_CODE_C1_P2;
        } elseif (Constant::REMINDER_CODE_C1_P2 == $reminderCode) {
            $reminderCode = Constant::REMINDER_CODE_C1_P3;
        }

        $em = $this->getContainer()->get('doctrine')->getManager();

        $criteria = array(
            'reminderCode' => $reminderCode
        );
        $rxReminder = $em->getRepository('UtilBundle:RxReminderSetting')->findOneBy($criteria);

        if (!$rxReminder) {
            return $result;
        }

        $params = array(
            'rx' => $rx,
            'rxReminder' => $rxReminder,
            'isSentOn' => true
        );

        $valid = $this->checkDuration($params);
        if (!$valid) {
            return $result;
        }

        $reminderCode = $rx->getReminderCode();
        if ($reminderCode) {
            $reminderCode = $reminderCode->getReminderCode();
        }
        if (!$reminderCode) {
            $result = 1;
        }

        if (Constant::REMINDER_CODE_C1_P1 == $reminderCode) {
            $result = 2;
        }

        if (Constant::REMINDER_CODE_C1_P2 == $reminderCode) {
            $result = 3;
        }

        if (Constant::REMINDER_CODE_C1_P3 == $reminderCode) {
            $result = 4;
        }

        return $result;
    }

    private function checkDuration($params)
    {
        $rx = isset($params['rx']) ? $params['rx'] : array();
        $rxReminder = isset($params['rxReminder']) ? $params['rxReminder'] : array();

        if (!$rx || !$rxReminder) {
            return false;
        }

        $duration = $rxReminder->getDurationTime();
        $timeUnit = $rxReminder->getTimeUnit();
        if (isset($params['isExpire'])) {
            $duration = $rxReminder->getExpiredTime();
            $timeUnit = $rxReminder->getTimeUnitExpire();
        }

        if (is_null($duration)) {
            return false;
        }

        $startDate = $rx->getLastestReminder();
        if (!$startDate && isset($params['isSentOn'])) {
            $startDate = $rx->getSentOn();
        }

        if (!$startDate) {
            return false;
        }

        $time = 'T' . $duration . 'H';
        if ('day' == $timeUnit) {
            $time = $duration . 'D';
        }
        if ('month' == $timeUnit) {
            $time = $duration . 'M';
        }
        $endDate = $startDate->add(new \DateInterval('P' . $time));

        $curDate = new \DateTime();
        if ($curDate < $endDate) {
            return false;
        }

        return true;
    }

    private function reminder1(Rx $rx)
    {
        $this->output->writeln("Reminder 1");

        $criteria = array(
            'reminderCode' => Constant::REMINDER_CODE_C1_P1
        );

        $params = array(
            'rx' => $rx,
            'criteria' => $criteria
        );
        $this->reminder($params);

        $this->output->writeln("End Reminder 1");
    }

    private function reminder2(Rx $rx)
    {
        $this->output->writeln("Reminder 2");

        $criteria = array(
            'reminderCode' => Constant::REMINDER_CODE_C1_P2
        );

        $params = array(
            'rx' => $rx,
            'criteria' => $criteria
        );
        $this->reminder($params);

        $this->output->writeln("End Reminder 2");
    }

    private function reminder3(Rx $rx)
    {
        $this->output->writeln("Reminder 3");

        $criteria = array(
            'reminderCode' => Constant::REMINDER_CODE_C1_P3
        );

        $params = array(
            'rx' => $rx,
            'criteria' => $criteria
        );
        $this->reminder($params);

        $this->output->writeln("End Reminder 3");
    }

    private function reminder4(Rx $rx)
    {
        $this->output->writeln("Reminder 4");

        $em = $this->getContainer()->get('doctrine')->getManager();

        $reminderCode = Constant::REMINDER_CODE_C1_FP;
        if ($rx->getResentOn()) {
            $reminderCode = Constant::REMINDER_CODE_C2_GPS;
            $flag = true;
        }
        $criteria = array(
            'reminderCode' => $reminderCode
        );
        $rxReminder = $em->getRepository('UtilBundle:RxReminderSetting')->findOneBy($criteria);

        $params = array(
            'rx' => $rx,
            'rxReminder' => $rxReminder
        );
        if (empty($flag)) {
            $params['isExpire'] = true;
        }

        $valid = $this->checkDuration($params);
        if ($valid) {
            return false;
        }

        $rx->setStatus(Constant::RX_STATUS_PAYMENT_FAILED);

        // Write log
        $params = array('rxObj' => $rx, 'isCron' => true);
        $em->getRepository('UtilBundle:Rx')->manageRXStatusLog($params);

        $this->output->writeln("Change status of rx: " . $rx->getId() . " to " . Constant::RX_STATUS_PAYMENT_FAILED);

        $params = array(
            'rx' => $rx,
            'criteria' => $criteria,
            'rxReminder' => $rxReminder
        );
        $this->reminder($params);

        $params = array();
        $this->sendMessage($params);

        $this->output->writeln("End Reminder 4");
    }

    private function reminder($params)
    {
        $rx = isset($params['rx']) ? $params['rx'] : array();
        if (!$rx) {
            return false;
        }

        $em = $this->getContainer()->get('doctrine')->getManager();

        $criteria   = isset($params['criteria']) ? $params['criteria'] : array();
        $rxReminder = isset($params['rxReminder']) ? $params['rxReminder'] : array();
        if (!$rxReminder) {
            $rxReminder = $em->getRepository('UtilBundle:RxReminderSetting')->findOneBy($criteria);
        }

        $rxRepository = $this->getContainer()->get('doctrine')->getRepository('UtilBundle:Rx');

        $title = $rxReminder->getTemplateSubjectEmail();
        $body  = $rxReminder->getTemplateBodyEmail();
        $body  = $rxRepository->replaceTemplateData($this->getContainer(), $rx, $rxReminder, $body, true);

        $message = $rxReminder->getTemplateSms();
        $message = $rxRepository->replaceTemplateData($this->getContainer(), $rx, $rxReminder, $message);

        $params = array('doctorId' => $rx->getDoctor()->getId());
        $parameters = array();
        $parameters['clinicInformation'] = $rxRepository->getClinicInformation($params);
        $parameters['doctorInformation'] = $rxRepository->getDoctorInformation($params);
        $parameters['body'] = $body;
        $parameters['baseUrl'] = $this->getContainer()->getParameter('base_url');

        $view = 'AdminBundle:emails:rx-reminder.html.twig';
        $html = $this->getContainer()->get('twig')->render($view, $parameters);

        $patient  = $rx->getPatient();
        $arrPhone = array();
        $arrTo    = array();
        if ($patient->getUseCaregiver() && $patient->getIsSendMailToCaregiver()) {
            $caregiver = $patient->getCaregivers()->first();
            if ($caregiver) {
                $arrTo[] = $caregiver->getPersonalInformation()->getEmailAddress();
                $arrPhone[] = $caregiver->getPhones()->first();
            }
        }
        if (empty($arrTo)) {
            $personal = $patient->getPersonalInformation();
            $arrTo[] = $personal->getEmailAddress();
        }
        $arrTo = array_unique($arrTo);

        $patterns = $this->getPatterns($rx);

        $params = array(
            'title' =>  $this->replaceText($title, $patterns),
            'body'  => $html,
            'to'    => $arrTo
        );
        $this->getContainer()->get('microservices.sendgrid.email')->sendEmail($params);

        $rx->setLastestReminder(new \DateTime());
        $rx->setReminderCode($rxReminder);
        $em->persist($rx);

        $this->output->writeln("Send email to: " . implode(', ', $arrTo));
        $this->output->writeln("Change lastest reminder for rx: " . $rx->getId());

        if (!$message) {
            return false;
        }
        
        if (empty($arrPhone)) {
            $arrPhone[] = $rx->getPatient()->getPhones()->first();
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
            
            if ($phoneNumber && $message) {
                $dataSendSMS = array(
                    'to' => $phoneNumber,
                    'message' => $message
                );
                $this->getContainer()->get('microservices.sms')->sendMessage($dataSendSMS);
                $this->output->writeln("Send sms to: " . $phoneNumber);
            }
        }
    }

    private function sendMessage($params)
    {
        return true;
    }

    private function replaceText($text, $patterns)
    {
        foreach ($patterns as $pattern => $replace) {
            $text = str_replace($pattern, $replace, $text);
        }
        
        return $text;
    }
    
    private function getPatterns($rx)
    {
        $doctor = $rx->getDoctor()->getPersonalInformation();

        $em = $this->getContainer()->get('doctrine')->getManager();
                            
        $patient = $rx->getPatient()->getPersonalInformation();
        $patientAddress = $em->getRepository('UtilBundle:PatientAddress')->findOneBy(array(
            'patient' => $patient
        ));
        $address = $patientAddress ? $patientAddress->getAddress() : null;
        $addresses = array();
        if ($address) {
            $addresses[] = $address->getLine1() . " " . $address->getLine2() . " " . $address->getLine3() . ",";
            $city = $address->getCity();
            if ($city) {
                $addresses[] = $city->getName();
                $state = $city->getState();
                $country = $city->getCountry();
                if ($state) {
                    $addresses[] = $state->getName();
                }
                if ($country) {
                    $addresses[] = $country->getName();
                }
            }
            
            $addresses[] = $address->getPostalCode();
        }
        $patientAddress = implode(" ", $addresses);
        $patientPhone = "";
        $phone = $rx->getPatient()->getPhones()->first();
        if ($phone) {
            $arrPN = array('+');
            $country = $phone->getCountry();
            if ($country) {
                $arrPN[] = $country->getPhoneCode();
            }
            $arrPN[] = $phone->getAreaCode();
            $arrPN[] = $phone->getNumber();
            $patientPhone = implode('', $arrPN);
        }

        $doctorName = $rx->getDoctor()->showName();

        $patientName = implode(' ', array(
            $patient->getTitle(),
            $patient->getFirstName(),
            $patient->getLastName()
        ));

        $link = $this->getContainer()->get('router')->generate('refill_entry', array('orderNumber' => $rx->getOrderNumber()));
        $link = $this->getContainer()->getParameter('base_url') . $link;
        $reminderUrl = $link;
        $link = '<a href="' . $link . '">link</a>';
        $responses = $this->getContainer()->get('templating')
            ->render('PatientBundle:Email:reminderTag.html.twig', array('reminderUrl' => $reminderUrl));

        $patterns = array(            
            '{patient title}' => $patient->getTitle(),
            '{patient first name}' => $patient->getFirstName(),
            '{patient last name}' => $patient->getLastName(),
            '{patient email}' => $patient->getEmailAddress(),
            '{patient phone number}' => $patientPhone,
            '{patient address}' => $patientAddress,
            '{patient_name}' => $patientName,
            '{order number}' => $rx->getOrderNumber(),
            '{patient number}' => $rx->getPatientNumber(),
            '{prescription_date}' => $rx->getCreatedOn()->format('Y-m-d'),
            '{link}' => $link,
            '{order id}' => "(Order ID: ". $rx->getOrderNumber() .")",
            '{responses}' => $responses
        );
        
        return $patterns;
    }
}