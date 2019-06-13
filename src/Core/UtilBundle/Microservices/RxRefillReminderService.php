<?php

/**
 * author luyen nguyen
 * date 09/29/2017
 */

namespace UtilBundle\Microservices;

use \DateTime;
use UtilBundle\Utility\Constant;

class RxRefillReminderService {

    protected $container;
    protected $em;

    /**
     * Construction
     * @param type $container
     * @param type $em
     */
    public function __construct($container, $em) {
        $this->container = $container;
        $this->em = $em;
    }

    /**
     * Send email to patient
     */
    public function sendEmailToPatient() {
        // Get email patient for reminder
        $results = $this->em->getRepository('UtilBundle:RxRefillReminder')
                ->getRxs();
        $sendResults = array();
        if ($results != null) {
            $basicUrl = $this->container->getParameter('base_url');
            foreach ($results as $result) {
                $rx = $result['rx'];
                $info['patient'] = $rx->getPatient();
                $info['prescribeDate'] = $rx->getCreatedOn()->format('j F Y');
                $info['basicUrl'] = $basicUrl;
                $info['orderNumber'] = $rx->getOrderNumber();
                $params = array(
                    'doctorId' => $rx->getDoctor()->getId(),
                    'rx' => $rx
                );
                $info['clinicInformation'] = $this->em->getRepository('UtilBundle:Rx')->getClinicInformation($params);
                $info['doctorInformation'] = $this->em->getRepository('UtilBundle:Rx')->getDoctorInformation($params);
                $body = $this->container->get('templating')
                        ->render('PatientBundle:Email:reminderPatient.html.twig', $info);
                $title = 'Refill Reminder Order Number ' . $rx->getOrderNumber();

                $patient = $rx->getPatient();
                $arrTo = array();
                if ($patient->getUseCaregiver() && $patient->getIsSendMailToCaregiver()) {
                    $caregiver = $patient->getCaregivers()->first();
                    if ($caregiver) {
                        $arrTo[] = $caregiver->getPersonalInformation()->getEmailAddress();
                    }
                }
                if (empty($arrTo)) {
                    $arrTo[] = $patient->getPersonalInformation()->getEmailAddress();
                }
                $arrTo = array_unique($arrTo);

                $params = array(
                    'title' => $title,
                    'body' => $body,
                    'to' => $arrTo
                );

                $send = $this->container->get('microservices.sendgrid.email')->sendEmail($params);
                if ($send) {
                    $rx->setLastestReminder(new \DateTime);
                    $this->em->persist($rx);
                    $updateparams['id'] = $result['refillReminderId'];
                    $updateparams['hasBeenReminded'] = 1;
                    $updateresult = $this->em->getRepository('UtilBundle:RxRefillReminder')->update($updateparams);
                    if ($updateresult) {
                        array_push($sendResults, "Refill Reminder SUCCESSFUL with Order Number: " . $rx->getOrderNumber());
                        $this->sendSMSToPatient($patient, $info);
                    } else {
                        array_push($sendResults, "Update RxRefillReminder FAIL with Order Number: " . $rx->getOrderNumber());
                    }
                } else {
                    array_push($sendResults, "Refill Reminder FAIL with Order Number: " . $rx->getOrderNumber());
                }
            }
        } else {
            array_push($sendResults, "No rx to refill reminder");
        }
        return $sendResults;
    }
	
    /**
     * Remind to patient and doctor
	 * @author Tuan Nguyen
     */
    public function remindPatientAndDoctor() 
	{
        //update RxRefillReminder first
        $this->updateRxRefillReminder();

        $list = $this->em->getRepository('UtilBundle:RxReminderSetting')->findBy(array(
			'reminderName' => 'reminder_emails'
		));
		$settings = array();
		foreach ($list as $item) {
			$settings[$item->getReminderCode()] = array(
				'days' => $item->getDurationTime(),
				'subject' => $item->getTemplateSubjectEmail(),
				'email' => $item->getTemplateBodyEmail(),
				'sms' => $item->getTemplateSms()
			);
		}
		
		// Get list of reminder which patient didn't open
		$list = $this->em->getRepository('UtilBundle:Rx')->getReminderRxs();
		
        $sendResults = array();
		
        if ($list != null) {
			foreach ($list as $item) {
				if (empty($item['type'])) {
					$lastestReminder = $item['lastestReminder']->getTimestamp();
					$reminderCode = "";
                    $reminderCodeD = "";
                    if ($item['reminderCode'] == Constant::REMINDER_CODE_EM_P1) {
                        if ($lastestReminder + $settings[Constant::REMINDER_CODE_EM_P2]['days'] * 86400 <= time()) {
                            $reminderCode = Constant::REMINDER_CODE_EM_P2;
                        }
                        if ($lastestReminder + $settings[Constant::REMINDER_CODE_EM_D1]['days'] * 86400 <= time()) {
                            $reminderCodeD = Constant::REMINDER_CODE_EM_D1;
                        }
                    } elseif ($item['reminderCode'] == Constant::REMINDER_CODE_EM_P2) {
                        if ($lastestReminder + $settings[Constant::REMINDER_CODE_EM_D2]['days'] * 86400 <= time()) {
                            $reminderCodeD = Constant::REMINDER_CODE_EM_D2;
                        }
                    } else {
                        if ($lastestReminder + $settings[Constant::REMINDER_CODE_EM_P1]['days'] * 86400 <= time()) {
                            $reminderCode = Constant::REMINDER_CODE_EM_P1;
                        }
                    }

                    $rx = $this->em->getRepository("UtilBundle:Rx")->find($item['id']);
                    $patterns = $this->getPatterns($rx);

					if (!empty($reminderCode)) {
						$patient = $rx->getPatient();
						
						//Send email to patient
						$title = $settings[$reminderCode]['subject'];
						$body = $settings[$reminderCode]['email'];
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
                            $arrTo[] = $patient->getPersonalInformation()->getEmailAddress();
                        }

                        $body = $this->replaceText($body, $patterns);
                        $body = nl2br($body);
                        $basicUrl = $this->container->getParameter('base_url');
                        $info['patient'] = $rx->getPatient();
                        $info['prescribeDate'] = $rx->getCreatedOn()->format('j F Y');
                        $info['basicUrl'] = $basicUrl;
                        $info['orderNumber'] = $rx->getOrderNumber();
                        $params = array(
                            'doctorId' => $rx->getDoctor()->getId(),
                            'rx' => $rx
                        );
                        $info['clinicInformation'] = $this->em->getRepository('UtilBundle:Rx')->getClinicInformation($params);
                        $info['doctorInformation'] = $this->em->getRepository('UtilBundle:Rx')->getDoctorInformation($params);
                        $info['body'] = $body;
                        $body = $this->container->get('templating')
                                ->render('PatientBundle:Email:reminderPatientTemplate.html.twig', $info);
						$params = array(
							'title' => $this->replaceText($title, $patterns),
							'body' => $body,
							'to' => $arrTo
						);

						$send = $this->container->get('microservices.sendgrid.email')->sendEmail($params);
						
						if ($send) {
							array_push($sendResults, "Reminded to patient with Order Number: " . $rx->getOrderNumber());
							
							// Update reminder time and reminder code
                            $object = $this->em->getRepository("UtilBundle:RxReminderSetting")->findOneBy(['reminderCode' => $reminderCode]);
                            $rx->setReminderCode($object);
							$rx->setLastestReminder(new \DateTime);
							$this->em->persist($rx);
    						$this->em->flush();
    						
    						// Send sms to patient
    						$message = $settings[$reminderCode]['sms'];
    						
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
    							if (!empty($phoneNumber)) {
    								$params = array(
    									'to' => $phoneNumber,
    									'message' => $this->replaceText($message, $patterns)
    								);
    								$this->container->get('microservices.sms')->sendMessage($params);
    							}
    						}
    					} else {
    						array_push($sendResults, "Failed to remind to patient with Order Number: " . $rx->getOrderNumber());
    					}
                    }
				}

                if (!empty($reminderCodeD)) {
                    $body = $settings[$reminderCodeD]['email'];
                    $body = $this->replaceText($body, $patterns);
                    $title = $settings[$reminderCodeD]['subject'];
                    $contentData = array(
                        'subject' => $this->replaceText($title, $patterns),
                        'body' => $body
                    );
                    $content = $this->em->getRepository('UtilBundle:MessageContent')->create($contentData);

                    $sender = $this->em->getRepository('UtilBundle:User')->findById(Constant::ADMIN_SYSTEM);
                    $senderName = $sender->getFirstName() . ' ' . $sender->getLastName();
                    $senderEmail = $sender->getEmailAddress();
                    $receiver = $rx->getDoctor()->getUser();
                    $doctorInfo = $rx->getDoctor()->getPersonalInformation();
                    $receiverName = $doctorInfo->getTitle() . '. ' . $doctorInfo->getFullName();
                    $receiverEmail = $doctorInfo->getEmailAddress();

                    $messageData = array(
                        'content'       => $content,
                        'sender'        => $sender,
                        'senderName'    => $senderName,
                        'senderEmail'   => $sender->getEmailAddress(),
                        'receiver'      => $receiver,
                        'receiverName'  => $receiverName,
                        'receiverEmail' => $receiverEmail,
                        'receiverType'  => 0,
                        'receiverGroup' => null,
                        'status'        => Constant::MESSAGE_INBOX,
                        'sentDate'      => new \DateTime(),
                    );

                    $this->em->getRepository('UtilBundle:Message')->create($messageData,[]);

                    array_push($sendResults, "Reminded to doctor with Order Number: " . $rx->getOrderNumber());

                    if ($reminderCodeD == Constant::REMINDER_CODE_EM_D2) {
                        $object = $this->em->getRepository("UtilBundle:RxReminderSetting")->findOneBy(['reminderCode' => $reminderCodeD]);
                        $rx->setReminderCode($object);
                        $rx->setLastestReminder(new \DateTime);
                        $this->em->persist($rx);
                        $this->em->flush();
                    }
                }
			}
        } else {
            array_push($sendResults, "No reminder to remind");
        }
		
        return $sendResults;
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
        $clinic = $this->em->getRepository('UtilBundle:Rx')->getClinicInformation(array('doctorId' => $rx->getDoctor()->getId()));
		$doctorAddress = $this->em->getRepository('UtilBundle:DoctorAddress')->findOneBy(array(
			'doctor' => $doctor
		));
		$address = $doctorAddress ? $doctorAddress->getAddress() : null;
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
		$doctorAddress = implode(" ", $addresses);
							
		$patient = $rx->getPatient()->getPersonalInformation();
		$patientAddress = $this->em->getRepository('UtilBundle:PatientAddress')->findOneBy(array(
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

        $link = $this->container->get('router')->generate('refill_entry', array('orderNumber' => $rx->getOrderNumber()));
        $link = $this->container->getParameter('base_url') . $link;
        $reminderUrl = $link;
        $link = '<a href="' . $link . '">link</a>';
        $responses = $this->container->get('templating')
            ->render('PatientBundle:Email:reminderTag.html.twig', array('reminderUrl' => $reminderUrl));

		$patterns = array(
			'{doctor title}' => $doctor->getTitle(),
			'{doctor first name}' => $doctor->getFirstName(),
			'{doctor last name}' => $doctor->getLastName(),
			'{doctor email}' => $doctor->getEmailAddress(),
			'{doctor address}' => $doctorAddress,
            '{doctor_name}' => $doctorName,
            '{clinic_name}' => $clinic['name'],
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
	
    /**
     * Cal Prescribe Dates
     * @param type $startOn
     * @param type $refillSupplyDuration
     * @return type
     */
    public function calPrescribeDate($startOn, $refillSupplyDuration) {
        list($year, $month, $day) = explode("-", $startOn->format('Y-m-d'));
        $refillDate = mktime(0, 0, 0, $month, $day + $refillSupplyDuration, $year);
        return date("j F Y", $refillDate);
    }

    /**
     * @param Patient $patient
     */
    private function sendSMSToPatient($patient, $info)
    {
        if (!$patient) {
            return false;
        }

        $params = array(
            'patient' => $patient, 
            'info' => $info
        );
        $params['link'] = $info['basicUrl'] . '/refill/' . $info['orderNumber'];
        $message = $this->container->get('templating')->render('PatientBundle:Prescription:smsTemplateRefill.html.twig', $params);

        $arrPhone = array();
        if ($patient->getUseCaregiver() && $patient->getIsSendMailToCaregiver()) {
            $caregiver = $patient->getCaregivers()->first();
            if ($caregiver) {
                $arrPhone[] = $caregiver->getPhones()->first();
            }
        }
        if (empty($arrPhone)) {
            $arrPhone[] = $patient->getPhones()->first();
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

    /**
     * update RxRefillReminder
     */
    private function updateRxRefillReminder()
    {
        $results = $this->em->getRepository('UtilBundle:RxRefillReminder')->getRxs();
        if ($results != null) {
            foreach ($results as $result) {
                $rx = $result['rx'];
                $rx->setLastestReminder(new \DateTime);
                $this->em->persist($rx);
                $updateparams['id'] = $result['refillReminderId'];
                $updateparams['hasBeenReminded'] = 1;
                $this->em->getRepository('UtilBundle:RxRefillReminder')->update($updateparams);
            }
        }
    }

}
