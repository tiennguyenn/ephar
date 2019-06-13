<?php

namespace AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use UtilBundle\Entity\Agent;
use UtilBundle\Entity\AgentDoctor;
use UtilBundle\Entity\Clinic;
use UtilBundle\Entity\Doctor;
use UtilBundle\Entity\Issue;
use UtilBundle\Entity\Log;
use UtilBundle\Entity\MasterProxyAccount;
use UtilBundle\Utility\Constant;
use UtilBundle\Utility\Common;

class BaseController extends Controller
{
    /*
     * save upload file
     */
    protected function uploadfile($file,$filename, $target_dir = "uploads/")
    {
        $fileSection = explode('/', $filename);
        array_pop($fileSection);
        $locationDir = $this->container->getParameter('upload_directory') . '/' . implode('/', $fileSection);
        if (Common::createDirIfNotExists($locationDir)) {
            $target_file = $target_dir . basename($file["name"]);
            $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
            if(empty($imageFileType)){
                $imageFileType = "txt";
            }
            $upload = move_uploaded_file($file['tmp_name'],$target_dir.$filename.'.'.$imageFileType);
            if($upload)
            {
                return $target_dir.$filename.'.'.$imageFileType;
            }
        }
        return '';
    }

    protected function executeCommand($command,$params){
        $kernel = $this->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $inputs = array_merge(['command' => $command],$params);
        $input = new ArrayInput(
            $inputs
        );
//        $input = new ArrayInput(array(
//            'command' => 'xero:update:account-code',
//            'coaId'  => $id,
//
//        ));
        $output = new BufferedOutput();
        $application->run($input, $output);
        $content = $output->fetch();
        return $content;
    }

    protected function saveLogRedispense($id,$status){
        $user = $this->getUser()->getDisplayName();
        $message = '';
        switch ($status){
            case 0:
                $message = "Send re-dispense request to Gmedes pharmacist";
                break;
            case 1:
                $message = "Reviewed the medicines to be re-dispensed";
                break;
            case 2:
                $message = "Previewed dispensing order";
                break;
            case 3:
                $message = "Uploaded prescription intervention to share point";
                break;


        }
        $param =[
            'module' => Constant::MODULE_CC,
            'entityId' => $id,
            'title' => 're-dispense-log',
            'action' => 're-dis-log',
            'newValue' => $message,
            'oldValue' => '',
            'createdBy' => $user
        ];
        $this->saveLogData($param);
    }

    protected function saveLogData($param){
        /*
         $param =[
            'module' => '',
            'entityId' => '',
            'title' => '',
            'action' => '',
            'newValue' => '',
            'oldValue' => '',
            'createdBy' => ''
        ]
         */
        $em = $this->getDoctrine()->getManager();
        $createdBy = isset($param['createdBy'])? $param['createdBy']: '';
        $entityId = isset($param['entityId'])? $param['entityId']: '';
        $module = isset($param['module'])? $param['module']: '';
        $title = isset($param['title'])? $param['title']: '';
        $action = isset($param['action'])? $param['action']: '';
        $newValue = isset($param['newValue'])? $param['newValue']: '';
        $oldValue = isset($param['oldValue'])? $param['oldValue']: '';
        $log = new Log();
        $log->setCreatedBy($createdBy);
        $log->setEntityId($entityId);
        $log->setModule($module);
        $log->setTitle($title);
        $log->setAction($action);
        $log->setNewValue($newValue);
        $log->setOldValue($oldValue);

        $em->persist($log);
        $em->flush();
    }

    protected function getResolveRedispense ($rx)
    {
        $resolves = $rx->getResolves();
        $dispense = '';

        foreach ($resolves as $resolve){
            if($resolve->getStatus() != Constant::RESOVLVE_STATUS_ACTIVE) {
                continue;
            }

            $dispenses = $resolve->getResolveRedispenses();
            if(count($dispenses) > 0) {
                $dispense = $dispenses->first();
                break;
            }

        }

        return $dispense;
    }

    protected function checkLocalPatient($rx) {
        $em = $this->container->get('doctrine')->getManager();
        $setting = $em->getRepository('UtilBundle:PlatformSettings')->getPlatFormSetting();
        $address = $rx->getShippingAddress();
        if(empty($address)) {
            return false;
        }
        $city = $address->getCity();
        if(empty($city)){
            return false;
        }
        $country = $city->getCountry();
        if(empty($country)){
            return false;
        }
        if($setting['operationsCountryId'] == $country->getId()) {
            return true;
        }

        return false;
    }

    protected function checkResolveOrder($id){
        $em = $this->getDoctrine()->getManager();
        $checkResolve =  $em->getRepository('UtilBundle:Rx')->getResolveStatus($id);
        $check = true;
        foreach ($checkResolve as $record){
            foreach ($record as $item){
                if($item == 2){
                    $check = false;
                }
            }
        }
        if($check){
            $rx = $em->getRepository('UtilBundle:Rx')->find($id);
            $rx->setIsOnHold(2);
            $this->createIssueWhenResolve($rx);
            $em->persist($rx);
            $em->flush();

            $this->sendEmailNotifyToPharmacy($rx->getOrderNumber(), 'Status: Resolved');

            $log = new Log();
            $log->setCreatedBy("bien ca");
            $log->setEntityId($id);
            $log->setModule("tracking");
            $log->setTitle("sendEmailWhenResolve");
            $log->setAction('inv-log');
            $log->setNewValue("Resolve success");
            $em->persist($log);
            $em->flush();

        }
        return $check;

    }

    /*
     * Send email to pharmacy
     */
    protected function sendEmailNotifyToPharmacy($number, $note)
    {
        $base = $this->container->getParameter('base_url');
        $pharmacy = $this->container->getParameter('report_resolve_issue');
        $mailTemplatep = 'CustomerCareBundle:email:pharmacy.html.twig';
        $mailParamsp = array(
            'logoUrl' => $base . '/bundles/admin/assets/pages/img/logo.png',
            'number' => $number,
            'base' => $base,
            'note' => $note
        );
        $dataSendMailp = array(
            'title' => "Reported issue has been resolved",
            'body' => $this->container->get('templating')->render($mailTemplatep, $mailParamsp),
            'to' => $pharmacy,
        );
        $this->container->get('microservices.sendgrid.email')->sendEmail($dataSendMailp);
    }


    /*
    * Send email to pharmacy
    */
    protected function sendEmailNotifyToAgent($doctors, $mpa)
    {
        $base = $this->container->getParameter('base_url');
        $toes = $this->container->getParameter('sale_agent');
        foreach ($doctors as $doctor) {
            $clinics = $doctor->getClinics();
            $mainClinic = '';
            foreach ( $clinics as $clinic){
                if($clinic->getIsPrimary()){
                    $mainClinic = $clinic->getBusinessName();
                }
            }


            $mailTemplatep = 'AdminBundle:emails:mpa-notify-agent.html.twig';
            $doctorName = '';
            if($doctor->getPersonalInformation()){
                $doctorName = $doctor->getPersonalInformation()->getTitle() . ' ' .$doctor->getPersonalInformation()->getFullName();
            }

            $mpaName = $mpa->getGivenName();
            $mailParamsp = array(
                'logoUrl' => $base . '/bundles/admin/assets/pages/img/logo.png',
                'mainClinic' => $mainClinic,
                'mpaName' => $mpaName,
                'doctorName' => $doctorName,
                'mpaFullName' => $mpa->getGivenName() . ' ' . $mpa->getFamilyName(),
                'mpaClinic' => $mpa->getClinicName()

            );
            foreach ($toes as $to){
                $dataSendMailp = array(
                    'title' =>  $doctorName. " is added in ".$mpaName."'s Doctor List",
                    'body' => $this->container->get('templating')->render($mailTemplatep, $mailParamsp),
                    'to' => $to,
                );
                $this->container->get('microservices.sendgrid.email')->sendEmail($dataSendMailp);
            }

        }
    }

    protected function createIssueWhenResolve($rx){
        $issue = new Issue();
        $issue->setStatus(Constant::ISSUE_STATUS_ACTIVE);
        $issue->setRemarks("Status: Resolved");
        $issue->setIsResolution(1);
        $user = $this->getUser();
        if(!empty($user)){
            $issue->setCreatedBy($user->getDisplayName());
        } else {
            $issue->setCreatedBy('Ask the Pharmacist');
        }

        $rx->addIssue($issue);
    }
    protected  function getPluralPrase($phrase){
        $plural='';

        for($i=0;$i<strlen($phrase);$i++){
            if($i==strlen($phrase)-1){
                $plural.=($phrase[$i]=='y')? 'ies':(($phrase[$i]=='s'|| $phrase[$i]=='x' || $phrase[$i]=='z' || $phrase[$i]=='ch' || $phrase[$i]=='sh')? $phrase[$i].'es' :$phrase[$i].'s');
            }else{
                $plural.=$phrase[$i];
            }
        }
        return $plural;
    }
}
