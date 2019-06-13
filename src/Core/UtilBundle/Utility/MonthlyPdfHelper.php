<?php

namespace UtilBundle\Utility;

use UtilBundle\Utility\Constant;
use UtilBundle\Microservices\TaxService;
use UtilBundle\Utility\Utils;

class MonthlyPdfHelper
{
    /**
     * get some info for balace section on montly report pdf
     * @param  EntityManager $em
     * @param  array $params
     * @author  thu.tranq
     * @return array
     */
    public static function getBalanceInfo($em, $params) {
        $data = array('thisMonth' => array(), 'lastMonth' => array());
        $date = new \DateTime();

        // get some info of current month
        $doctorAmount = $em->getRepository('UtilBundle:MarginShare')->getDoctorFeeByMonth($params);
        $paymentInfo = $em->getRepository('UtilBundle:PaymentStatus')->getPaymentByMonth($params);

        // get info of last month
        $lastMonth        = $date->modify('last month');
        $params['month']  = (int)$lastMonth->format('m');
        $params['year']   = (int)$lastMonth->format('Y');
        $lastDoctorAmount = $em->getRepository('UtilBundle:MarginShare')->getDoctorFeeByMonth($params);
        $lastPaymentInfo  = $em->getRepository('UtilBundle:PaymentStatus')->getPaymentByMonth($params);

        // set data for current and last month
        $data['thisMonth'] = array('amount' => $doctorAmount, 
                                   'datePaid' => !empty($paymentInfo) ? $paymentInfo['datePaid'] : null,
                                   'amountPaid' => !empty($paymentInfo) ? $paymentInfo['amountPaid'] : null);

        $monthName = $lastMonth->format('F');
        $data['lastMonth']['statementDate'] = "{$params['statementDateNumber']} {$monthName} {$params['year']}";
        
        $data['lastMonth']['amount'] = null;
        if (isset($lastDoctorAmount) and !empty($lastPaymentInfo)) {
            $data['lastMonth']['amount'] = $lastDoctorAmount - $lastPaymentInfo['amountPaid'];
        }

        return $data;
    }

    /**
     * get some info for balace section on montly report pdf
     * @param  EntityManager $em
     * @param  array $params
     * @author  toan.le
     * @return array
     */
    public static function getBalanceInfoForAgent($em, $params) {
        $data = array('thisMonth' => array(), 'lastMonth' => array());

        $date = new \DateTime( $params['year'] . '-' . $params['month']  );

        $agentAmount = $em->getRepository('UtilBundle:MarginShare')->getAgentFeeByMonth($params);
        // get amount paid
        $paymentInfo = $em->getRepository('UtilBundle:PaymentStatus')->getPaymentByMonth($params,Constant::USER_TYPE_AGENT);


        $lastMonth  = $date->modify('last month');
        $params['month']  = (int)$lastMonth->format('m');
        $params['year']   = (int)$lastMonth->format('Y');

        $lastAgentAmount = $em->getRepository('UtilBundle:MarginShare')->getAgentFeeByMonth($params);
        $lastPaymentInfo = $em->getRepository('UtilBundle:PaymentStatus')->getPaymentByMonth($params,Constant::USER_TYPE_AGENT);

        $data['thisMonth'] = array('amount' => $agentAmount, 
                                   'datePaid' => !empty($paymentInfo) ? $paymentInfo['datePaid'] : null,
                                   'amountPaid' => !empty($paymentInfo) ? $paymentInfo['amountPaid'] : null);

        $monthName = $lastMonth->format('F');
        $data['lastMonth'] = array('statementDate' => "{$params['statementDateNumber']} {$monthName} {$params['year']}");

        if (isset($lastAgentAmount) and !empty($lastPaymentInfo)) {
            $data['lastMonth']['amount'] = $lastAgentAmount - $lastPaymentInfo['amountPaid'];
        }

        return $data;
    }

    /**
     * get some info for payment section (right corner) on montly report pdf file
     * @param  array $params
     * @author thu.tranq
     * @return array
     */
    public static function getPaymentInfo($em, $params) {
        $daysInMonth   = cal_days_in_month(CAL_GREGORIAN, (int)$params['month'], (int)$params['year']);

        $date = new \DateTime( $params['year'] . '-' . $params['month']  );
        $monthName = $date->format('M');

        $date->modify('+1 month');
        $statementMonthName = $date->format('M');

        
        $paymentInfo['statementPeriod'] = "1 to {$daysInMonth} {$monthName} {$params['year']}";

        $platformSetting = $em->getRepository('UtilBundle:PlatformSettings')->getPlatFormSetting();
        $statementDateNumber = $platformSetting['doctorStatementDate'];

        $paymentInfo['statementDateNumber'] = $statementDateNumber;
        $paymentInfo['statementTxt'] = "Doctor's Fees Statement as of {$daysInMonth} {$monthName} {$params['year']}";
        $paymentInfo['payableTxt'] = "Total Fees payable to doctor as at {$daysInMonth} {$monthName} {$params['year']} ({$platformSetting['currencyCode']})";

        if (isset($params['isTaxInvoice'])) {
            return $paymentInfo;
        }

        // get doctor amount by month
        $dMSL = $em->getRepository('UtilBundle:DoctorMonthlyStatementLine')->getStatementLine($params['doctorId'], $params['month'], $params['year']);
        if (!empty($dMSL)) {
            $doctorAmount = $dMSL->getTotalAmount();
            $statementDate = $dMSL->getDoctorMonthlyStatement()->getStatementDate()->format('d M Y');
        } else { 
            // for case of data for current month not be generated 
            // (When the user click on Download Statement Button on browser for one month in one day that the day less than statement date  ) 
            $doctorAmount = $em->getRepository('UtilBundle:MarginShare')->getMonthlyDoctorFee($params);
            // get statement date
            $statementDate = self::getStatementDate($em, $params);
            $statementDate = $statementDate->format('d M Y');
        }
        $paymentInfo['amount'] = $doctorAmount;
        $paymentInfo['statementDate'] = $statementDate;

        return $paymentInfo;
    }


    /**
     * get statement date
     * @param   $em
     * @author thu.tranq
     * @return datetime
     */
    public static function getStatementDate($em, $params = array()) {
        //get platform settings
        $psObj = $em->getRepository('UtilBundle:PlatformSettings')->getPaymentSchedule();

        //get holiday list
        $publicHoliday = $em->getRepository('UtilBundle:PublicHoliday')->listPHDates();
        $listPHDate = array();
        foreach ($publicHoliday as $value) {
            $listPHDate[] = $value['publicDate'];
        }

        $statementDate = new \DateTime();
        $statementDate->setDate($params['year'], (int)$params['month'], 01);
        $statementDate->modify('+1 month');

        if (isset($params['isAgent'])) {
            $workingDate =  Utils::getWorkingDate($listPHDate, $psObj["agentStatementDate"], $statementDate);
        } else {
            $workingDate =  Utils::getWorkingDate($listPHDate, $psObj["doctorStatementDate"], $statementDate);
        }

        return $workingDate;
    }

    /**
     * get some info for payment section (right corner) on montly report pdf file
     * @param  array $params
     * @author toan.le
     * @return array
     */
    public static function getPaymentInfoForAgent($em, $params) {
        $params['isAgent'] = true;
        $daysInMonth   = cal_days_in_month(CAL_GREGORIAN, (int)$params['month'], (int)$params['year']);
        
        $date = new \DateTime( $params['year'] . '-' . $params['month']  );
        $monthName = $date->format('M');

        $date->modify('+1 month');
        $statementMonthName = $date->format('M');

        $paymentInfo['statementPeriod'] = "1 to {$daysInMonth} {$monthName} {$params['year']}";


        // get doctor amount by month
        $aMSL = $em->getRepository('UtilBundle:AgentMonthlyStatementLine')->getStatementLine($params['agentId'], $params['month'], $params['year']);
        if (!empty($aMSL)) {
            $agentAmount = $aMSL->getTotalAmount();
            $statementDate = $aMSL->getAgentMonthlyStatement()->getStatementDate()->format('d M Y');
        } else { 
            // for case of data for current month not be generated 
            // (When the user click on Download Statement Button on browser for one month in one day that the day less than statement date  ) 
            $agentAmount = $em->getRepository('UtilBundle:MarginShare')->getAgentFeeByMonth($params);
            // get statement date
            $statementDate = self::getStatementDate($em, $params);
            $statementDate = $statementDate->format('d M Y');
        }

        $paymentInfo['amount'] = $agentAmount;
        
        $platformSetting = $em->getRepository('UtilBundle:PlatformSettings')->getPlatFormSetting();
        $statementDateNumber = $platformSetting['agentStatementDate'];

        $paymentInfo['statementDate'] = $statementDate;
        $paymentInfo['statementDateNumber'] = $statementDateNumber;
        $paymentInfo['statementTxt'] = "Total Fees payable to agent as at {$daysInMonth} {$monthName} {$params['year']}";

        return $paymentInfo;
    }


    /**
     * build array data for rxLine 
     * @param  array $info
     * @author  thu.tranq
     * @return array
     */
    public static function buildRxLineInfo($info, $pfRate) {
        $data = array();

        $data['lineType']            = $info['lineType'];
        $data['hasRxReviewFee']      = $info['hasRxReviewFee'];
        $data['listPrice']           = $info['listPrice'];
        $data['doctorServiceFee']    = $info['doctorServiceFee'];
        $data['name']                = $info['name'];
        $data['originPrice']         = $info['originPrice'];
        $data['quantity']            = $info['quantity'];
        $data['costPrice']           = $info['costPrice'];
        $data['costPriceGst']        = $info['costPriceGst'];
        $data['listPriceGst']        = $info['listPriceGst'];
        $data['agentMedicineFee']    = $info['agentMedicineFee'];
        $data['platformMedicineFee'] = $info['platformMedicineFee'];
        $data['agentServiceFee']     = $info['agentServiceFee'];
        $data['platformServiceFee']  = $info['platformServiceFee'];
        $data['gstCode']             = $info['code'];
		$data['packingType']		 = $info['packingType'];
        $data['doctorMedicineFee']   = $info['doctorMedicineFee'];

        $gstValue = $data['originPrice'];
        if (TaxService::isCalGst($info['code'])) {
            $gstValue = $data['originPrice'] * (1 + $pfRate/100);
        }
        $data['originPriceGST']      = $gstValue;
        $data['originPriceNoGST']    = $data['originPriceGST'] - $data['originPrice'];

        $data['costPriceToClinic'] = $info['costPriceToClinic'];
        $gstValue = $data['costPriceToClinic'];
        if (TaxService::isCalGst($info['code'])) {
            $gstValue = $data['costPriceToClinic'] * (1 + $pfRate/100);
        }
        $data['costPriceToClinicGST'] = $gstValue;
        $data['costPriceToClinicNoGST'] = $data['costPriceToClinicGST'] - $data['costPriceToClinic'];

        return $data;
    }

    /**
     * The helper is build to support for getRxByMonths function of RxRepository
     * @param  array $rxInfo RX information
     * @param  object $em Entity manager
     * @return array
     */
    public static function buildStatementInfo($rxInfo, $em) {
        $data = array();
        $data['paymentGate'] = $rxInfo['paymentGate'];
        $data['payMethod']   = $rxInfo['payMethod'];
        $data['paidOn']      = $rxInfo['paidOn'];
        $data['createdOn']   = $rxInfo['createdOn'];
        $data['fullName']    = $rxInfo['fullName'];
        $data['patientCode'] = $rxInfo['patientCode'];
        $data['taxId']       = $rxInfo['taxId'];
        $data['orderNumber'] = $rxInfo['orderNumber'];
        $data['refundedOn']  = $rxInfo['refundedOn'];
        $data['doctorMedicinePercentage'] = isset($rxInfo['doctorMedicinePercentage']) ? $rxInfo['doctorMedicinePercentage'] : 0;

        // custom tax
        if($rxInfo['rxCustomsTax'] != 0){
            $countryCode = $rxInfo['countryCode'];
            if ($countryCode == Constant::INDONESIA_CODE) {
                $countryCode = Constant::INDONESIA_DISPLAY;
            }
            $customsTaxName = 'Import Tax / VAT - ' . $countryCode . " ({$rxInfo['customsTax']}%)";
            $data['customsTaxName'] = $customsTaxName;
            $data['customsTax'][] = $rxInfo['rxCustomsTax'];
            $data['customsTax'][] = $rxInfo['rxCustomsTax'];
            $data['customsTax'][] = 0;
        }

        $data['importDuty'] = $rxInfo['taxImportDuty'];
        $data['taxIncome'] = $rxInfo['taxIncome'];
        $data['taxIncomeWithoutTax'] = $rxInfo['taxIncomeWithoutTax'];
        $data['taxVat'] = $rxInfo['taxVat'];

        if ($rxInfo['countryCode'] == 'SG' and isset($rxInfo['igPermitFee'])) {
            $data['igPermitFee'][] = $rxInfo['igPermitFee'];
            $data['igPermitFee'][] = $rxInfo['igPermitFee'];
            $data['igPermitFee'][] = 0;
        }

        // shipping fee
        $data['shippingList'][] = $rxInfo['shippingList'];
        $data['shippingList'][] = $rxInfo['shippingList'];
        $data['shippingList'][] = 0;


        // custom clearance admin fee
        $data['customCAF'][0] = $rxInfo['customsClearancePlatformFee'];
        $data['customCAF'][2] = $rxInfo['customsClearanceDoctorFee'];
        $data['customCAF'][1] = $data['customCAF'][0] - $data['customCAF'][2];
        $data['countryCode'] = $rxInfo['countryCode'];

        // fee gst
        $data['feeGst'][] = $rxInfo['feeGst'];
        $data['feeGst'][] = 0;
        $data['feeGst'][] = $rxInfo['feeGst'];

        $paymentMethod = $em->getRepository('UtilBundle:RxPaymentLog')->findByOrderRef($rxInfo['orderNumber']);
        if ($paymentMethod) {
            $paymentMethod = $paymentMethod->getPayMethod();
        }

        $paymentGatewayFeeBankMdr = 0;
        if (Constant::PAY_METHOD_VISA_MASTER == $paymentMethod) {
            $paymentGatewayFeeBankMdr = $rxInfo['paymentGatewayFeeBankMdr'];
        }

        // payment gateway fee bank mdr
        $data['paymentGatewayFeeBankMdr'][] = 0;
        $data['paymentGatewayFeeBankMdr'][] = $paymentGatewayFeeBankMdr;
        $data['paymentGatewayFeeBankMdr'][] = -$paymentGatewayFeeBankMdr;

        $temp = 0;
        if ($paymentGatewayFeeBankMdr) {
            $feeSetting = $em->getRepository('UtilBundle:FeeSetting')->getFeeSettingByCode(Constant::GATEWAY_CODE_GST, $paymentMethod, Constant::MALAYSIA_CODE, $rxInfo['paymentGate']);
            $temp = $paymentGatewayFeeBankMdr * $feeSetting / 100;
            $temp = round($temp, 2);
        }
        $data['paymentGatewayFeeBankGst'][] = 0;
        $data['paymentGatewayFeeBankGst'][] = $temp;
        $data['paymentGatewayFeeBankGst'][] = -$temp;

        // payment gateway fee variable
        $data['paymentGatewayFeeVariable'][] = 0;
        $data['paymentGatewayFeeVariable'][] = $rxInfo['paymentGatewayFeeVariable'];
        $data['paymentGatewayFeeVariable'][] = -$rxInfo['paymentGatewayFeeVariable'];

        $paymentGatewayFeeFixed = 0;
        if (Constant::PAY_METHOD_REVPAY_FPX == $paymentMethod || Constant::PAYMENT_GATE_REDDOT == $data['paymentGate']) {
            $paymentGatewayFeeFixed = $rxInfo['paymentGatewayFeeFixed'];
        }

        $data['paymentGatewayFeeFixed'][] = 0;
        $data['paymentGatewayFeeFixed'][] = $paymentGatewayFeeFixed;
        $data['paymentGatewayFeeFixed'][] = -$paymentGatewayFeeFixed;

        $temp = 0;
        if ($paymentGatewayFeeFixed) {
            $feeSetting = $em->getRepository('UtilBundle:FeeSetting')->getFeeSettingByCode(Constant::GATEWAY_CODE_FIX_GST, $paymentMethod, Constant::MALAYSIA_CODE, $rxInfo['paymentGate']);
            $temp = $paymentGatewayFeeFixed * $feeSetting / 100;
        }
        $data['paymentGatewayFeeFixedGst'][] = 0;
        $data['paymentGatewayFeeFixedGst'][] = $temp;
        $data['paymentGatewayFeeFixedGst'][] = -$temp;

        // primary Residence Country
        $data['primaryResidenceCountry'] = $rxInfo['primaryResidenceCountry'];

        return $data;
    }

    /**
     * The helper is build to support for getRxByMonths function of RxRepository
     * @param  [type] $item [description]
     * @return [type]       [description]
     */
    public static function buildTaxInvoiceInfo($rxInfo, $pfRate=0, $em) {

        $data = array();

        $data['paidOn']    = $rxInfo['paidOn'];
        $data['payMethod'] = $rxInfo['payMethod'];
        $data['paymentGate'] = $rxInfo['paymentGate'];
        $data['createdOn']   = $rxInfo['createdOn'];
        $data['fullName']    = $rxInfo['fullName'];
        $data['patientCode'] = $rxInfo['patientCode'];
        $data['taxId']       = $rxInfo['taxId'];
        $data['orderNumber'] = $rxInfo['orderNumber'];

        $data['prescribingRevenueFeeGst'] = $rxInfo['prescribingRevenueFeeGst'] ? $rxInfo['prescribingRevenueFeeGst'] : $rxInfo['listPriceGst'];
        $data['prescribingRevenueFeeGstCode'] = $rxInfo['prescribingRevenueFeeGstCode'] ? $rxInfo['prescribingRevenueFeeGstCode'] : Constant::GST_ZRSGM;
        $data['medicineGrossMarginGst'] = $rxInfo['medicineGrossMarginGst'] ? $rxInfo['medicineGrossMarginGst'] : 0;
        $data['medicineGrossMarginGstCode'] = $rxInfo['medicineGrossMarginGstCode'] ? $rxInfo['medicineGrossMarginGstCode'] : Constant::GST_ZRSGM;
        $data['doctorMedicinePercentage'] = isset($rxInfo['doctorMedicinePercentage']) ? $rxInfo['doctorMedicinePercentage'] : 0;

        // custom tax
        $countryName = $rxInfo['countryCode'];
        if ($countryName == Constant::INDONESIA_CODE) {
            $countryName = Constant::INDONESIA_DISPLAY;
        }
        $customsTaxName = 'Import Tax / VAT - ' . $countryName . " ({$rxInfo['customsTax']}%)";
        $data['customsTaxName'] = $customsTaxName;
        $data['customsTax'][0] = $rxInfo['rxCustomsTax'];
        $rxCustomsTaxGst = $rxInfo['rxCustomsTaxGst'];
        if (!$rxCustomsTaxGst) {
            $rxCustomsTaxGst = $rxInfo['rxCustomsTax'] * (1 + $pfRate);
        }
        $data['customsTax'][2] = $rxCustomsTaxGst;
        $data['customsTax'][1] = $data['customsTax'][2] - $data['customsTax'][0];
        $rxcustomsTaxGstCode = $rxInfo['rxcustomsTaxGstCode'] ? $rxInfo['rxcustomsTaxGstCode'] : Constant::GST_ZRSGM;
        if (strlen($rxcustomsTaxGstCode) == 3) {
            $rxcustomsTaxGstCode .= 'GM';
        }
        $data['customsTax'][3] = $rxcustomsTaxGstCode;

        $data['importDuty'] = $rxInfo['taxImportDuty'];
        $data['taxIncome'] = $rxInfo['taxIncome'];
        $data['taxIncomeWithoutTax'] = $rxInfo['taxIncomeWithoutTax'];
        $data['taxVat'] = $rxInfo['taxVat'];
        $data['gstRate'] = $rxInfo['gstRate'];

        if ($rxInfo['countryCode'] == 'SG' and isset($rxInfo['igPermitFee'])) {
            $data['igPermitFee'][] = $rxInfo['igPermitFee'];
            $data['igPermitFee'][] = $rxInfo['igPermitFeeGst'] - $rxInfo['igPermitFee'];
            $data['igPermitFee'][] = $rxInfo['igPermitFeeGst'];
            $data['igPermitFee'][] = $rxInfo['igPermitFeeGstCode'] ? $rxInfo['igPermitFeeGstCode'] : Constant::GST_ZRSGM;
        }

        // shipping fee
        $data['shippingList'][3] = $rxInfo['toDoctorShippingGstCode'] ? $rxInfo['toDoctorShippingGstCode'] : Constant::GST_ZRSGM;
        $data['shippingList'][0] = $rxInfo['shippingList'];
        $shippingListGst = $rxInfo['shippingListGst'];
        if (!$shippingListGst) {
            $shippingListGst = $rxInfo['shippingList'] * (1 + $pfRate);
        }
        $data['shippingList'][2] = $shippingListGst;
        $data['shippingList'][1] = $data['shippingList'][2] - $data['shippingList'][0];


        // custom clearance admin fee
        $data['customCAF'][0] = $rxInfo['customsClearanceDoctorFee'];
        $customsClearanceDoctorFeeGst = $rxInfo['customsClearanceDoctorFeeGst'];
        if (!$customsClearanceDoctorFeeGst) {
            $customsClearanceDoctorFeeGst = $rxInfo['customsClearanceDoctorFee'] * (1 + $pfRate);
        }
        $data['customCAF'][2] = $customsClearanceDoctorFeeGst;
        $data['customCAF'][1] = $data['customCAF'][2] - $data['customCAF'][0];
        $customsClearanceDoctorFeeGstCode = $rxInfo['customsClearanceDoctorFeeGstCode'] ? $rxInfo['customsClearanceDoctorFeeGstCode'] : Constant::GST_ZRSGM;
        if (strlen($customsClearanceDoctorFeeGstCode) == 3) {
            $customsClearanceDoctorFeeGstCode .= 'GM';
        }
        $data['customCAF'][3] = $customsClearanceDoctorFeeGstCode;
        $data['customCAF'][4] = $rxInfo['customsClearancePlatformFee'] - $rxInfo['customsClearanceDoctorFee'];
        $data['countryCode'] = $rxInfo['countryCode'];

        // fee gst
        $data['feeGst'][] = $rxInfo['feeGst'];
        $data['feeGst'][] = 0;
        $data['feeGst'][] = $rxInfo['feeGst'];

        $paymentMethod = $em->getRepository('UtilBundle:RxPaymentLog')->findByOrderRef($rxInfo['orderNumber']);
        if ($paymentMethod) {
            $paymentMethod = $paymentMethod->getPayMethod();
        }

        $paymentGatewayFeeBankMdr = 0;
        if (Constant::PAY_METHOD_VISA_MASTER == $paymentMethod) {
            $paymentGatewayFeeBankMdr = $rxInfo['paymentGatewayFeeBankMdr'];
        }

        // payment gateway fee bank mdr
        $data['paymentGatewayFeeBankMdr'][] = $paymentGatewayFeeBankMdr;

        $feeSetting = $em->getRepository('UtilBundle:FeeSetting')->getFeeSettingByCode(Constant::GATEWAY_CODE_GST, $paymentMethod, Constant::MALAYSIA_CODE, $rxInfo['paymentGate']);
        $gstValue = $paymentGatewayFeeBankMdr * $feeSetting / 100;
        $data['paymentGatewayFeeBankMdr'][] = $gstValue;
        $data['paymentGatewayFeeBankMdr'][] = $gstValue  + $paymentGatewayFeeBankMdr;
        $data['paymentGatewayFeeBankMdr'][] = $rxInfo['paymentGatewayFeeBankMdrGstCode'] ? $rxInfo['paymentGatewayFeeBankMdrGstCode'] : Constant::GST_ZRSGM;


        $temp = 0;
        if (Constant::PAY_METHOD_VISA_MASTER == $paymentMethod) {
            $temp = $rxInfo['paymentGatewayFeeBankGst'] - $paymentGatewayFeeBankMdr;
        }
        // payment gateway fee bank gst
        $data['paymentGatewayFeeBankGst'][] = 0;
        $data['paymentGatewayFeeBankGst'][] = 0;
        $data['paymentGatewayFeeBankGst'][] = $temp;

        // payment gateway fee variable
        $data['paymentGatewayFeeVariable'][] = $rxInfo['paymentGatewayFeeVariable'];
        $data['paymentGatewayFeeVariable'][] = $rxInfo['paymentGatewayFeeVariableGst'] - $rxInfo['paymentGatewayFeeVariable'];
        $data['paymentGatewayFeeVariable'][] = $rxInfo['paymentGatewayFeeVariableGst'];
        $data['paymentGatewayFeeVariable'][] = $rxInfo['paymentGatewayFeeVariableGstCode'] ? $rxInfo['paymentGatewayFeeVariableGstCode'] : Constant::GST_ZRSGM;

        $paymentGatewayFeeFixed = 0;
        if (Constant::PAY_METHOD_REVPAY_FPX == $paymentMethod || Constant::PAYMENT_GATE_REDDOT == $data['paymentGate']) {
            $paymentGatewayFeeFixed = $rxInfo['paymentGatewayFeeFixed'];
        }

        $data['paymentGatewayFeeFixed'][] = $paymentGatewayFeeFixed;

        $temp = 0;
        if ($paymentGatewayFeeFixed) {
            $feeSetting = $em->getRepository('UtilBundle:FeeSetting')->getFeeSettingByCode(Constant::GATEWAY_CODE_FIX_GST, $paymentMethod, Constant::MALAYSIA_CODE, $rxInfo['paymentGate']);
            $temp = $paymentGatewayFeeFixed * $feeSetting / 100;
        }

        $data['paymentGatewayFeeFixed'][] = $temp;
        $data['paymentGatewayFeeFixed'][] = $temp + $paymentGatewayFeeFixed;

        if (Constant::PAYMENT_GATE_REDDOT != $data['paymentGate']) {
            $data['paymentGatewayFeeFixed'][] = $rxInfo['paymentGatewayFeeFixedGstCode'] ? $rxInfo['paymentGatewayFeeFixedGstCode'] : Constant::GST_ZRSGM;
        } else { // for REDDOT
            $data['paymentGatewayFeeFixed'][] = '';
        }

        $temp = 0;
        if (Constant::PAY_METHOD_REVPAY_FPX == $paymentMethod) {
            $temp = $rxInfo['paymentGatewayFeeFixedGst'] - $rxInfo['paymentGatewayFeeFixed'];
        }
        // payment gateway fee bank gst
        $data['paymentGatewayFeeFixedGst'][] = 0;
        $data['paymentGatewayFeeFixedGst'][] = 0;
        $data['paymentGatewayFeeFixedGst'][] = $temp;

        // primary Residence Country
        $data['primaryResidenceCountry'] = $rxInfo['primaryResidenceCountry'];

        $data['costPriceToClinic'] = $rxInfo['costPriceToClinic'];

        return $data;
    }
}
