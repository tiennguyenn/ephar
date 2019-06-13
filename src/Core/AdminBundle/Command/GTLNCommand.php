<?php

namespace AdminBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use UtilBundle\Entity\RxStatusLog;
use UtilBundle\Entity\CourierPoDailyLine;
use UtilBundle\Microservices\GmedRequest;
use Unirest\Request\Body;
use UtilBundle\Utility\Constant;
class GTLNCommand extends ContainerAwareCommand
{
    /**
     * GLTN API to tracking order status
     * app:track-order-status
     * related to STRIKE-806, STRIKE-896
     */
    protected function configure()
    {
        $this->setName('app:track-order-status')
            ->setDescription('GTLN api to track order status');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $package = array();
        $date = new \DateTime();
        $output->writeln($date->format("Y-m-d H:i:s").': Start updating Track Order Status');

        $em = $this->getContainer()->get('doctrine')->getManager();
        $paramsBag = array(
            'countryCode' => ['ID'],
            'ignoreBoxStatus' => [Constant::BAG_DELIVERED]
        );
        $lists = $em->getRepository('UtilBundle:Bag')->getListBy($paramsBag);

        if (empty($lists)) {
            $output->writeln('No tracking nunber found');
            return;
        }
        $output->writeln(' Total db records: '.count($lists));

        // build parameter for gltn api
        foreach ($lists as $item) {
            $package[] = array(
                'tracking_number' => $item['trackingNumber'],
                'mawb' => $item['masterAwbCode']
            );
        }

        // call gltn api to tracking order status
        $gtlnParams = $this->getContainer()->getParameter('gtln');
        $apiUrl = $gtlnParams['url'];
        $apiKey = $gtlnParams['api_key'];
        $params = array(
            'token' => $gtlnParams['token_id'],
            'timestamp' => date('c', $date->getTimestamp()),
            'party_id' => $gtlnParams['party_id'],
            'package' => $package
        );

        $output->writeln(' Start to get data from gltn API...');
        $output->writeln(' Tracking number info...'. json_encode($package));
        
        $response = $this->getDataAPI($apiUrl, $params, $apiKey);

        //error
        if(isset($response->response->error_reason)) {
            $output->writeln(' Error gltn API: '.$apiUrl);
            $output->writeln(' Error Reason: '.$response->response->error_reason);
            return ;
        }

        if ($response->response->success_record->total_records == 0) {
            $output->writeln(' No success record from gltn api found');
            return ;
        }
        
        $output->writeln(' Total gltn records: '.$response->response->success_record->total_records);
        $output->writeln(' Updating BOX...');

        $data = $response->response->success_record->record_number;
        $strCond = array(); // build condition for finding box in db
        $arrRx = array();
        foreach ($data as $item) {
            $info           = $item->data;
            $lastSmile = $info->runsheet->lastmile;
            if (!empty($lastSmile)) {
                $trackingNumber = $info->tracking_number;
                // $trackingNumber = '609091700009610';
                $mawb           = $info->mawb;
                // $mawb = '123456789';
                $strCond[] =  $trackingNumber . '-' . $mawb;

                $max =  new \DateTime($lastSmile[0]->status_date);
                $lastStatus = strtoupper($lastSmile[0]->status);
                $bagStatuses = Constant::BAG_STATUSES;
                
                foreach ($lastSmile as $key => $item) {
                    if ($key != 0 ) {
                        $var = new \DateTime($item->status_date);
                        if ($var > $max) {
                            $max = $var;
                            //$lastStatus = strtoupper($item->status);
                            if(isset($bagStatuses[strtoupper($item->status)])){
                                $lastStatus = strtoupper($item->status); break;
                            }
                        }
                    }
                }                
                
                if (isset($bagStatuses[$lastStatus])) {
                    $lastStatus = Constant::BAG_DELIVERED;
                } else {
                    $lastStatus = Constant::BAG_OTHER;
                }

                //update box status
                $this->updateBox($output, $trackingNumber, $lastStatus, $arrRx);
            }
        }
        if(!empty($arrRx)) {
            $output->writeln(' Updating RX...');
            foreach($arrRx as $rxId) {
                $this->updateRx($output, $rxId);
            }
        }
        $output->writeln($date->format("Y-m-d H:i:s").': END');
    }

    //update box status
    private function updateBox($output, $trackingNumber, $status, &$arrRx)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $boxObj = $em->getRepository('UtilBundle:Box')->findOneBy(array('trackingNumber' => $trackingNumber));
        if($boxObj != null) {
            $boxObj->setStatus($status);
            $boxObj->setUpdatedStatusOn(new \DateTime());
            $em->persist($boxObj);
            $em->flush();
            
            if($boxObj->getRx()) {
                $arrRx[$boxObj->getRx()->getId()] = $boxObj->getRx()->getId();
                
                //write log
                $log = new RxStatusLog();
                $log->setRx($boxObj->getRx());
                $log->setNotes("GTLN started delivering the parcel with tracking number ".$trackingNumber);
                $log->setStatus($boxObj->getRx()->getStatus());
                $log->setCreatedBy("GTLN Cronjob");
                $em->persist($log);
                $em->flush();
            }
            $output->writeln(' OK - updated box id: '.$boxObj->getId().' --> status: '.$status);
        } else {
            $output->writeln(' Fail - Not found with tracking number '.$trackingNumber);
        }
    }

    /**
     * update rx status [ 17: RX_STATUS_DELIVERED, 16: RX_STATUS_DELIVERING ]
     * 1. If all box of a rx is delivered, update rx.status == delivered
     * 2. If one of box of a rx is updated from GTLN but not all delivered, update rx.status== delivering
     * 3. If no boxes of a rx is updated from GTLN, keep rx.status unchanged
     **/
    private function updateRx($output, $rxId)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $rxObj = $em->getRepository('UtilBundle:Rx')->find($rxId);

        if($rxObj == null || $rxObj->getStatus() == Constant::RX_STATUS_DELIVERED) {
            $output->writeln(' Nothing to update');
            return;
        }
        
        $boxIds = array();
        $shippingAddress = $rxObj->getShippingAddress();
        $postalCode = $shippingAddress ? $shippingAddress->getPostalCode() : null;
        
        $city = $shippingAddress ? $shippingAddress->getCity() : null;
        $country = $city ? $city->getCountry() : null;
        $countryCode = $country ? $country->getCode() : null;
        
        $boxes = $em->getRepository('UtilBundle:Box')->findBy(['rx' => $rxId]);
        if(!empty($boxes)) {
            $count = 0;
            foreach ($boxes as $b) {
                if($b->getStatus() != Constant::BAG_DELIVERED)
                    $count++;
                    
                // add po courier daily line
                if (!$b->getDeletedOn()) {
                    $existed = $em->getRepository('UtilBundle:CourierPoDailyLine')->findOneBy(array(
                        'box' => $b
                    ));
                    if (!$existed) {
                        $dailyLine = new CourierPoDailyLine();     
                        if ($countryCode == 'MY') {
                            $dailyLine->setPostCodeShippingAddress($postalCode);
                        } else {
                            $dailyLine->setPostCodeShippingAddress(1);
                        }                       
                        $dailyLine->setBox($b);
                        $dailyLine->setCreatedOn(new \DateTime());
                        $dailyLines[] = $dailyLine;
                        $boxIds[] = $b->getId();
                    } 
                }  
                // end                
            }

            //update rx status...
            if($count == 0) {
                $rxObj->setStatus(Constant::RX_STATUS_DELIVERED);
                $rxObj->setUpdatedStatusOn(new \DateTime());
                $em->persist($rxObj);     
                // persist daily line
                foreach ($dailyLines as $dailyLine) {
                    $em->persist($dailyLine);
                }
                
                $em->flush();
                $output->writeln(' Add box id to daily line: '. implode(",", $boxIds));
                $output->writeln(' OK - updated rx id: '.$rxObj->getId().' --> status: '.Constant::RX_STATUS_DELIVERED);
            } else {
                $rxObj->setStatus(Constant::RX_STATUS_DELIVERING);
                $rxObj->setUpdatedStatusOn(new \DateTime());
                $em->persist($rxObj);
                $em->flush();
                $output->writeln(' OK - updated rx id: '.$rxObj->getId().' --> status: '.Constant::RX_STATUS_DELIVERING);
            }
        }
    }

    /**
     * get data from api
     */
    private function getDataAPI($url, $params, $apiKey)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "Content-Type: application/json",
                "Postman-Token: 3e5e227e-3e57-45e2-a729-2bfb050c2c97",
                "api-key: ".$apiKey
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        return json_decode($response);
    }

    /**
     * package for testing on DEV
     */
    private function packageTmp()
    {
        $package = array(
            array(
                'tracking_number' => "609041800000922",
                'mawb' => "DRAFT1526264780"
            ),
            array(
                'tracking_number' => "609021800000073",
                'mawb' => "DRAFT1526264780"
            )
        );
        return $package;
    }
}