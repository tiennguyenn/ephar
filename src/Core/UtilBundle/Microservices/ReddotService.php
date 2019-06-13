<?php
namespace UtilBundle\Microservices;

use UtilBundle\Utility\Constant;
use UtilBundle\Utility\Common;
use Unirest\Request;

class ReddotService extends BaseReddot
{
    /**
     * get settlement Report
     * @return array
     * @author thu.tranq
     */
    public function getSettlementReport($em, $day)
    {
        $data = array();
        $rxRepo = $em->getRepository('UtilBundle:Rx');
        $listPHDate = array();
        $rxs = $rxRepo->getReddotSettlementReport($day, $listPHDate);

        $settlementDate = empty($day) ? new \DateTime() : new \DateTime($day);
        foreach ($rxs as $key => $rx) {
            $transactionDate = $rx['transactionDate']->format('Y-m-d') . ' 00:00:00';
            $transactionDate = new \DateTime($transactionDate);
            $item = array(
                    'transactionRef'   => $rx['transactionRef'],
                    'orderNumber'      => $rx['orderNumber'],
                    'expectedAmount'   => $rx['expectAmount'],
                    'settlementAmount' => $rx['settlementAmount'],
                    'transactionGrossAmount' => $rx['transactionGrossAmount'],
                    'settlementDate'   => $settlementDate,
                    'transactionDate'  => $transactionDate,
                    'status'           => $rx['status']
                );

            $data[] = $item;
        }

        return $data;
    }

    public function startPayment($data, $patientEmail)
    {
        $contents = $this->start_payment($data, $patientEmail);
        $response = $contents['response'];
        $result = $this->formatResponse($response);
        return array(
            'input_data' => $contents['input_data'],
            'result' => $result
        );
    }
    public function validateTransaction($transaction) {

        return $this->validate_payment($transaction);
    }
}