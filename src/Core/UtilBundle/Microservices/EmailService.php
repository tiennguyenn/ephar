<?php

/**
 * Created by PhpStorm.
 * User: phuc.duong
 * Date: 8/18/17
 * Time: 10:16 AM
 */

namespace UtilBundle\Microservices;

class EmailService {

    protected $container;
    protected $em;

    /**
     * Construct 
     * @param type $container
     * @param type $em
     */
    public function __construct($container, $em) {
        $this->container = $container;
        $this->em = $em;
    }

    /**
     * @param array $params
     */
    public function sendEmail($params) {

        if( !empty($params['from']) ){
            $from = $params['from'];
        } else {
            $from = $this->container->getParameter('primary_email');
        }

        $to   = isset($params['to']) ? $params['to'] : '';
        if (!$to) {
            return false;
        }

        $body = isset($params['body']) ? $params['body'] : '';
        if (!$body) {
            return false;
        }

        $title = isset($params['title']) ? $params['title'] : '';
		
        $message = (new \Swift_Message($title))
                ->setFrom($from)
                ->setTo($to)
				
                ->setBody($body, 'text/html');
		
		// add bcc		
		if ($this->container->hasParameter('mailer_bcc'))
		{
			$message->setBcc(array($this->container->getParameter('mailer_bcc')));
		}

        // Optionally add any attachments
        if(isset($params['attach'])) {
            if(is_array($params['attach'])) {
                foreach ($params['attach'] as $item) {
                    $message->attach(\Swift_Attachment::fromPath($item));
                }
            } else {
                $message->attach(\Swift_Attachment::fromPath($params['attach']));
            }
        }
        $sent = $this->container->get('mailer')->send($message);
        //$sent = $this->container->get('mailer')->newInstance($this->ignoreSSL())->send($message);

        $this->container->get('logger')->addInfo("Status ". $sent);
        $this->container->get('logger')->addInfo("To ". json_encode($to));

        return $sent;
    }

    /**
     * ignore SSL
     * @author vinh.nguyen
     */
    private function ignoreSSL()
    {
        $https['ssl']['verify_peer'] = FALSE;
        $https['ssl']['verify_peer_name'] = FALSE;

        $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
            ->setUsername($this->container->getParameter('mailer_user'))
            ->setPassword($this->container->getParameter('mailer_password'))
            ->setStreamOptions($https);
        return $transport;
    }
}
