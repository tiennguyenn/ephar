<?php

namespace UtilBundle\Microservices;

use UtilBundle\Microservices\ApiService;
use UtilBundle\Utility\Constant;
use UtilBundle\Utility\Common;

class MOLPayService extends ApiService
{
    /**
     * get settlement 
     * @return mixed
     * @author vinh.nguyen
     */
    public function getSettlementReport($em, $day, $vmMdr, $vmGst)
    {
        $url = $this->molPayAPI['base_url'].'/MOLPay/API/settlement/report.php';
        if(empty($day))
            $day = '-1 day';//'2017-08-17'
        $dateTime = new \DateTime($day);
        $date = $dateTime->format('Y-m-d');

        $params = array(
            'date' => $date,
            'merchant_id' => $this->molPayAPI['merchant_id'],
            'token' => md5($this->molPayAPI['merchant_id'] . $this->molPayAPI['secret_key'] . $date),
            'format' => 'json', 
            'version' => '3.0'
        );
        $result = $this->_getData($url, $params);

        return $this->formatPGSettlement($em, $result, $vmMdr, $vmGst);
    }
    
    /**
    * format data of PG Settlement
    * @author vinh.nguyen
    */
    private function formatPGSettlement($em, $data, $vmMdr, $vmGst)
    {
        $result = array();
        $data = json_decode($data, true);
        if($data != null) {
            $settlementDate = null;
            
            foreach ($data as $k=>$item) {
                if($k == 0) {
                    if(!isset($item['SettlementDate']))
                        return $result;

                    $settlementDate = $item['SettlementDate'];
                    continue;
                }

                $orderNumber = $item['OrderId'];
				$fee = round(($item['TransactionGrossAmount'] / 100) * $vmMdr / 100, 2);
				$gstFee = round($fee * $vmGst / 100, 2);
				$expectedAmount = ($item['TransactionGrossAmount'] / 100) - ($fee + $gstFee);
				
                $settlementAmount = $item['SettlementNetAmountInProcessingCurrency'] / 100;

                //Status -> 0: Non Updated, 1: Updated, 2: For Investigation
				$differenceAmount = floor(($expectedAmount - $settlementAmount) * 100)/100;
                $status = $differenceAmount == 0 ? 1 : 2;
				$rx = $em->getRepository('UtilBundle:Rx')->findOneBy(array('orderNumber' => $orderNumber));
				if (!$rx) {
					$status = 0;
				}
				
                $result[] = array(
                    'transactionRef'   => $item['AcquirerReference'],
                    'orderNumber'      => $orderNumber,
                    'expectedAmount'   => $expectedAmount,
                    'settlementAmount' => $settlementAmount,
                    'transactionGrossAmount' => $item['TransactionGrossAmount'] / 100,
                    'settlementDate'   => Common::st2date($settlementDate, true),
                    'transactionDate'  => Common::st2date($item['TransactionDate'], true),
                    'status'           => $status
                );
            }
        }
        return $result;
    }
    public function executeRefundAction($params = [])
    {
        $url = $this->molPayAPI['base_url'].'/MOLPay/API/refundAPI/index.php';

        $params['RefundType'] = 'P';
        $params['Signature'] = md5($params['RefundType'].$params['MerchantID'].$params['RefID'].$params['TxnID'].$params['Amount'].$this->molPayAPI['secret_key']);

        $result = $this->_getData($url, $params);
        return json_decode($result);
    }
}