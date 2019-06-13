<?php

namespace UtilBundle\Microservices;
use UtilBundle\Entity\EmailLog;

class SendGridService {

    protected $container;
    protected $em;

    /**
     * Construct
     * @param type $container
     * @param type $em
     */
    public function __construct($container, $em = null) {
        $this->container = $container;
        $this->em        = $em;
    }

    /**
     * Send email
     * @param array $params
     * @return $response
     */
    public function sendEmail($params) {
        
        $from = $this->container->getParameter('primary_email');
        if(!empty($params['from'])) {
            $from = $params['from'];
        }
        if(empty($params['title'])) {
            return false;
        }
        if(is_array($params['to']) && !isset($params['to'][0]['email'])) {
            $arrTo = [];
            if (!empty($params['to'])) {
                foreach ($params['to'] as $value) {
                    $arrTo[]['email'] = $value;
                }
                $params['to'] = $arrTo;
            } else {
                return false;
            }
        } elseif (!is_array($params['to'])) {
            if (!empty($params['to'])) {
                $stringTo = $params['to'];
                $params['to'] = [];
                $params['to'][]['email'] = $stringTo;
            } else {
                return false;
            }
        }
        if(!isset($params['body'])) {
            return false;
        }

        $sendgrid = $this->container->getParameter('sendgrid');
        $api      = $sendgrid['url'];
        $key      = $sendgrid['key'];
        $sendAt   = time();

        $repo            = $this->em->getRepository('UtilBundle:PlatformSettings');
        $platFormSetting = $repo->getPlatFormSetting();
        if (isset($params['scheduled']) && $params['scheduled']) {
            if(isset($platFormSetting['scheduleDeclarationTime'])) {
                $schedule = $platFormSetting['scheduleDeclarationTime'] < 0 ? 0 : $platFormSetting['scheduleDeclarationTime'];
                $sendAt   = time() + $schedule * 60 * 60;
            }
        }

        $data = array(
            'personalizations' => array(
                array(
                    'to'      => $params['to'],
                    'subject' => $params['title']
                )
            ),
            'from' => array(
                'email' => $from
            ),
            'content' => array(
                array(
                    'type'  => 'text/html',
                    'value' => $params['body']
                )
            ),
            'send_at' => $sendAt
        );

        if (isset($params['attach'])) {
            if (!is_array($params['attach'])) {
                $fileName = explode('/', $params['attach']);
                $data['attachments'] = array(
                    array(
                        'content' => base64_encode(file_get_contents($params['attach'])),
                        'filename' => end($fileName)
                    )
                );
            } else {
                foreach ($params['attach'] as $item) {
                    $fileName = explode('/', $item);
                    $data['attachments'][] = array(
                            'content' => base64_encode(file_get_contents($item)),
                            'filename' => end($fileName)
                        );
                }
            }
        }
        $this->container->get('logger')->addInfo('SendGrid data: ' . json_encode($data));

        try {
            $ch = curl_init($api);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $key)
            );
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            $response = curl_exec($ch);
            curl_close($ch);
            $this->container->get('logger')->addInfo('SendGrid response: ' . json_encode($response));
            if (isset($response['errors'])) {
                return false;
            } else {
                try {
                    $email_log = new EmailLog;
                    $email_log->setEmailTo(json_encode($params['to']));
                    $email_log->setEmailFrom($from);
                    $email_log->setSubject($params['title']);
                    $email_log->setSendDate(new \DateTime());
                    $email_log->setMessage($params['body']);
                    if (isset($data['attachments'])) {
                        $email_log->setAttachment(json_encode($data['attachments']));
                    }
                    $this->em->persist($email_log);
                    $this->em->flush();
                } catch (\Exception $e) {
                }
                return true;
            }

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            return false;
        }
    }
}