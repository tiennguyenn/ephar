<?php

namespace UtilBundle\Microservices;

use UtilBundle\Microservices\ApiService;
use UtilBundle\Utility\Constant;
use UtilBundle\Utility\Common;

class AsiaPayService extends ApiService
{
    /**
     * get settlement
     * @return mixed
     * @author vinh.nguyen
     */
    public function getSettlementReport($em, $day)
    {
        $url = $this->asiaPayAPI['settlement_url'];
        
        if(empty($day))
            $day = '-1 day';
        $date = new \DateTime($day);
        
        $params = array(
            'startDate' => $date->format("dmY000000"),
            'endDate'   => $date->format("dmY235959"),
            'queryType' => 'O' //O,S
        );
        $params['loginId'] = $this->asiaPayAPI['login'];
        $params['password'] = $this->asiaPayAPI['password'];
        $params['merchantId'] = $this->asiaPayAPI['merchant_id'];

        $result = $this->_getData($url, $params);
        
        return $this->formatPGSettlement($em, $result);
    }
    
    /**
    * format data of PG Settlement
    * @author vinh.nguyen
    */
    private function formatPGSettlement($em, $data)
    {
        $result = array();
        $data = Common::xml2Array($data);
        if($data != null) {
            foreach ($data['record'] as $item) {
                $orderNumber = $item['merref'];
                //PG settlement status, 0: no updated, 1: updated, 2: PG issue
                $orderValue = $em->getRepository('UtilBundle:Rx')->getRxOrderValue($orderNumber, true);
                $status = ($orderValue != null && $orderValue == $item['originalamt'])? 1: 2;

                $result[] = array(
                    'transactionRef'   => $item['payref'],
                    'orderNumber'      => $orderNumber,
                    'expectedAmount'   => $item['originalamt'],
                    'settlementAmount' => $item['amt'],
                    'settlementDate'   => Common::st2date($item['settledate'], true),
                    'transactionDate'  => Common::st2date($item['authdate'], true),
                    'paymentMethod'    => $item['paymethod'],
                    'status'           => $status
                );
            }
        }
        return $result;
    }
}