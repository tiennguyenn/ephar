<?php
/**
 * Created by PhpStorm.
 * User: phuc.duong
 * Date: 8/18/17
 * Time: 10:16 AM
 */

namespace UtilBundle\Microservices;
use UtilBundle\Entity\SmsLog;

class SMSService {

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
     * Send message to user when create otp code
     * @author Toan Le
     * @param array $$params
     * @return $response     
     */    
    public function sendMessage($params = array()){
		$params['message'] = preg_replace('#<a href="([^"]+)">[^"]+</a>#', "$1", $params['message']);
		$params['message'] = str_replace("&nbsp;", "", $params['message']);
		$params['message'] 	= strip_tags($params['message']);
		
        $params['username'] = $this->container->getParameter('send_message_user');
        $params['password'] = $this->container->getParameter('send_message_password');
        $params['from']     = $this->container->getParameter('send_message_from_name');
        $api = $this->container->getParameter('send_message_api') .'?'. http_build_query($params);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json')); // Assuming you're requesting JSON
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        $this->container->get('logger')->addInfo("SMS ". json_encode($response));

        try {
            $sms_log = new SmsLog;
            $sms_log->setSendTo($params['to']);
            $sms_log->setSendFrom($params['from']);
            $sms_log->setSendDate(new \DateTime());
            $sms_log->setMessage($params['message']);

            $this->em->persist($sms_log);
            $this->em->flush();
        } catch (\Exception $e) {
        }

        return $response;
    }
		
} 