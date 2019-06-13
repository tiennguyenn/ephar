<?php

/**
 * Created by PhpStorm.
 * User: phuc.duong
 * Date: 8/18/17
 * Time: 10:16 AM
 */

namespace UtilBundle\Microservices;

use UtilBundle\Utility\Constant;

class TaxService {

    /**
     * Cal sub total medication
     * @param type $rxLines
     * Reference document: doc\02.Specification\00.PRD\Doctor_Statement_GMEDES_calculation_(Tin update)_v1
     * author luyen nguyen
     */
    public static function calSubTotalMedication($rxLines) {
        $subtotalMedication = 0;
        foreach ($rxLines as $rxLine) {
            if ($rxLine->getLineType() == Constant::RX_LINE_TYPE_ONE) {
                $subtotalMedication += $rxLine->getListPrice();
            }
        }
        return $subtotalMedication;
    }

    /**
     * Cal sub total service
     * @param type $rxLines
     * Reference document: doc\02.Specification\00.PRD\Doctor_Statement_GMEDES_calculation_(Tin update)_v1
     * author luyen nguyen
     */
    public static function calSubTotalService($rxLines) {
        $subtotalService = 0;
        foreach ($rxLines as $rxLine) {
            if ($rxLine->getLineType() == Constant::RX_LINE_TYPE_TWO) {
                $subtotalService += $rxLine->getListPrice();
            }
        }
        return $subtotalService;
    }

    /**
     * Cal rx gst fee
     * @param type $rxLines
     * @return int
     * Reference document: doc\02.Specification\00.PRD\Doctor_Statement_GMEDES_calculation_(Tin update)_v1
     * author luyen nguyen
     */
    public static function calRxGstFee($rxLines, $shippingList, $gstRate
            , $doctorGst, $ccAdminFee, $countryCode
            , $gstMedicineCode, $gstServiceCode) {
        $gstFee = 0;
        if ($doctorGst) {
            foreach ($rxLines as $rxLine) {
                if ($rxLine->getLineType() == Constant::RX_LINE_TYPE_ONE
                        && TaxService::isCalGst($rxLine->getDrug()->getGstCode()->getCode())
                        && $gstMedicineCode) {
                    // Gst for drug
                    $gstFee += $rxLine->getListPrice();
                } else if ($rxLine->getLineType() == Constant::RX_LINE_TYPE_TWO
                        && $gstServiceCode) {
                    // Gst for doctor
                    $gstFee += $rxLine->getListPrice();
                }
            }
            $shippingGST = $rxLine->getRx()->getToPatientShippingGstCode();
            if (!TaxService::isCalGst($shippingGST)) {
                $shippingList = Constant::ZERO_NUMBER;
            }
            $ccGST = $rxLine->getRx()->getCustomsClearanceDoctorFeeGstCode();
            if (!TaxService::isCalGst($ccGST)) {
                $ccAdminFee = Constant::ZERO_NUMBER;
            }
            $gstFee += $shippingList + $ccAdminFee;
            $gstFee *= $gstRate / Constant::ONE_HUNDRE_NUMBER;
        }
        return round($gstFee, 2);
    }

    /**
     * Is Cal Gst
     * @param type $gstCode
     * @return boolean
     */
    public static function isCalGst($gstCode) {
        if ($gstCode == Constant::GST_SRS || $gstCode == Constant::GST_SRSGM) {
            return true;
        }
        return false;
    }

    /**
     * Cal Gst Fee for Singapore and Indonesia
     * @param type $gstCodeFee
     * @param type $ccAdminFee
     * @return type
     * Reference document: doc\02.Specification\00.PRD\Doctor_Statement_GMEDES_calculation_(Tin update)_v1
     * author luyen nguyen
     */
    public static function calGstCCAdminFee($countryCode, $gstCodeFee, $ccAdminFee) {
        $codeFee = '';
        if ($countryCode == Constant::SINGAPORE_CODE) {
            $codeFee = $gstCodeFee[Constant::CCAF_SG];
        } else {
            $codeFee = $gstCodeFee[Constant::CCAF_ID];
        }
        if ($codeFee == Constant::GST_SRS || $codeFee == Constant::GST_SRSGM) {
            return $ccAdminFee;
        }
        return Constant::ZERO_NUMBER;
    }

    /**
     * Cal Imported TAX/VAT fee in Singapore
     * @param type $subTotalMedication
     * @param type $countryCode
     * Reference document: doc\02.Specification\00.PRD\Doctor_Statement_GMEDES_calculation_(Tin update)_v1
     * author luyen nguyen
     */
    public static function calRxSingaporeImportedTaxFee($subTotalMedication, $igPermitFee
                                                    , $rateExchange, $bufferRate, $thresholdTax) {
        $importedTaxFee = 0;
        // Convert from MYR to SGD
        $totalMedicationSGD = $subTotalMedication / $rateExchange * $bufferRate;
        // $igPermitFeeBuffer = Constant::SINGAPORE_IG_PERMIT_FEE_BUFFER;
        if (!is_null($thresholdTax) && $totalMedicationSGD >= $thresholdTax) {
            $importedTaxFee = ($totalMedicationSGD + ($igPermitFee * $bufferRate)) *
                    (Constant::SINGAPORE_IMPORTED_TAX / Constant::ONE_HUNDRE_NUMBER);
        }
        // Convert from SGD to MYR
        $importedTaxFee *= $rateExchange;
        return round($importedTaxFee, 2);
    }

    /**
     * Cal Imported TAX/VAT fee in Indonesia
     * @param type $subTotalMedication
     * @param type $rateExchange
     * @param type $bufferRate
     * @return type
     * Reference document: doc\02.Specification\00.PRD\Doctor_Statement_GMEDES_calculation_(Tin update)_v1
     * author luyen nguyen
     */
    public static function calRxIndonesiaImportedTaxFee($subTotalMedication, $rateExchange, $bufferRate) {
        // Convert from MYR to SGD
        $totalMedicationSGD = $subTotalMedication / $rateExchange[Constant::CURRENCY_MALAYSIA] * $bufferRate;
        // Convert from  SGD to IDR
        $totalMedicationIDR = $totalMedicationSGD * $rateExchange[Constant::CURRENCY_INDONESIA];
        $importedTaxFeeIDR = $totalMedicationIDR
                * (Constant::INDONESIA_IMPORTED_TAX / Constant::ONE_HUNDRE_NUMBER);
        // Convert from  IDR to SGD
        $importedTaxFeeSGD = $importedTaxFeeIDR / $rateExchange[Constant::CURRENCY_INDONESIA];
        // Convert from  SGD to MYR
        $importedTaxFeeMYR = $importedTaxFeeSGD * $rateExchange[Constant::CURRENCY_MALAYSIA];
        return round($importedTaxFeeMYR, 2);
    }

    /**
     * Cal Customs Clearance Admin Fee
     * @param type $subTotalMedication
     * @param type $ccAdminFeePercentage
     * Reference document: doc\02.Specification\00.PRD\Doctor_Statement_GMEDES_calculation_(Tin update)_v1
     * author luyen nguyen
     */
    public static function calCCAdminFee($subTotalMedication, $ccAdminFeePercentage) {
        $ccAdminFee = 0;
        if ($ccAdminFeePercentage != null) {
            $ccAdminFee = $subTotalMedication * ($ccAdminFeePercentage / Constant::ONE_HUNDRE_NUMBER);
        }
        return round($ccAdminFee, 2);
    }

    /**
     * Cal Ig Permit Fee
     * @param type $countryCode
     * @param type $rateExchange
     * @param type $bufferRate
     * @return int
     * Reference document: doc\02.Specification\00.PRD\Doctor_Statement_GMEDES_calculation_(Tin update)_v1
     * author: luyen nguyen
     */
    public static function calIgPermitFee($igPermitFeeSGD, $rateExchange, $bufferRate, $subTotalMedication, $thresholdTax) {
        $totalMedicationSGD = $subTotalMedication / $rateExchange * $bufferRate;
        if (!is_null($thresholdTax) && $totalMedicationSGD >= $thresholdTax) {
            // Convert SGD to MYR
            $igPermitFee = $igPermitFeeSGD * $rateExchange * $bufferRate;
            return round($igPermitFee, 2);
        }
        return Constant::ZERO_NUMBER;
    }

    /**
     * Cal margin share
     * @param type $rxLines
     * @param type $rx
     * @param type $PSPercentage
     * Reference document: doc\02.Specification\00.PRD\Doctor_Statement_GMEDES_calculation_(Tin update)_v1
     * author luyen nguyen
     */
    public static function calMarginShare($rxLines, $rx, $PSPercentage, $platformShareFlag = false) {
        $agentAmount = 0;
        $doctorAmount = 0;
        $platformAmount = 0;
        $doctorMF = 0;
        $agentServiceFee = 0;
        $doctorMedicineFee = 0;
        $agentAmount3pa = 0;
        foreach ($rxLines as $rxLine) {
            // Agent
            $agentAmount += $rxLine->getAgentMedicineFee() + $rxLine->getAgentServiceFee();
            if ($platformShareFlag) {
                $agentServiceFee += $rxLine->getAgentServiceFee();
            }
            if ($rxLine->getLineType() == 1) {
                $temp = $rxLine->getListPrice() - $rxLine->getOriginPrice() * $rxLine->getQuantity();
                if ($rx->getDoctorMedicinePercentage()) {
                    $doctorMF += ($temp - ($temp * (100 - $rx->getDoctorMedicinePercentage())/100));
                }
            }
            $doctorAmount += $rxLine->getDoctorServiceFee();
            if ($platformShareFlag) {
                $doctorMedicineFee += $rxLine->getDoctorMedicineFee();
            }

            $platformAmount += $rxLine->getPlatformMedicineFee() + $rxLine->getPlatformServiceFee();
        }
        if ($platformShareFlag) {
            $doctorAmount = $doctorAmount + $doctorMedicineFee;
            $platformAmount = $rx->getPlatformMedicineFee() + $rx->getPlatformServiceFee();
            $agentAmount = $agentServiceFee + $rx->getAgentMedicineFee();
            $agentAmount3pa = $rx->getAgent3paMedicineFee() + $rx->getAgent3paServiceFee();
        } else {
            $doctorAmount = $doctorAmount + round($doctorMF, 2);
        }
        // Doctor
        $paymentGateWayFee = $rx->getPaymentGatewayFeeBankGst()
                + $rx->getPaymentGatewayFeeVariable()
                + $rx->getPaymentGatewayFeeFixed();
        $CCDoctorFee = round($rx->getCustomsClearancePlatformFee()
                * ($PSPercentage->getDoctorPercentage() / Constant::ONE_HUNDRE_NUMBER), 2);
        $doctorAmount += $rx->getFeeGst() - $paymentGateWayFee + $CCDoctorFee;
        // Platform
        $CCGemedsFee = round($rx->getCustomsClearancePlatformFee()
                * ($PSPercentage->getPlatformPercentage() / Constant::ONE_HUNDRE_NUMBER), 2);
        $shippingFee = $rx->getShippingList() - $rx->getShippingCost();
        $platformAmount += $shippingFee + $CCGemedsFee;

        return array('agentAmount' => $agentAmount,
            'doctorAmount' => $doctorAmount,
            'platformAmount' => $platformAmount,
            'CCDoctorFee' => $CCDoctorFee,
            'agentAmount3pa' => $agentAmount3pa
        );
    }

}
