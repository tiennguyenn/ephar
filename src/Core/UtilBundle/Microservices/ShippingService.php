<?php

/**
 * Created by PhpStorm.
 * User: phuc.duong
 * Date: 8/18/17
 * Time: 10:15 AM
 */

namespace UtilBundle\Microservices;

class ShippingService {

    /**
     * Cal shipping fee
     * @param type $courierRates
     * @param type $postalCode
     * @return type
     * author luyen nguyen
     * Reference document: doc\02.Specification\00.PRD\Doctor_Statement_GMEDES_calculation_(Tin update)_v1
     */
    public static function calShippingFee($courierRates, $postalCode, $rx = null) {        
        if (count($courierRates) == 1) {
            return array("list" => $courierRates[0]->getList(),
                "cost" => $courierRates[0]->getCost(),
                "deliveryTime" => $courierRates[0]->getEstimatedDeliveryTimeline());
        }

        $result = array();
        $cheapestRate = array();
        foreach ($courierRates as $courierRate) {
            $type = $courierRate->getType();
            if ($type) {
                $isColdChain = $rx->isColdChain();
                if ($isColdChain) {
                    $result = $courierRate;
                }
                continue;
            }

            // Is this courier suitable to carry these products? If no, skip
            if ($courierRate->getFromPostcode() == null) {
                $cheapestRate = array("list" => $courierRate->getList(),
                    "cost" => $courierRate->getCost(),
                    "deliveryTime" => $courierRate->getEstimatedDeliveryTimeline());
                return $cheapestRate;
            }

            if ($postalCode > $courierRate->getToPostcode()) {
                // do nothing
                continue;
            }
            if ($postalCode < $courierRate->getFromPostcode()) {
                // do nothing
                continue;
            }
            // is this safe to assume that the postcode is within the range?
            $cheapestRate = array("list" => $courierRate->getList(),
                "cost" => $courierRate->getCost(),
                "deliveryTime" => $courierRate->getEstimatedDeliveryTimeline());
        }

        if ($result) {
            $cheapestRate = array(
                "list" => $result->getList(),
                "cost" => $result->getCost(),
                "deliveryTime" => $result->getEstimatedDeliveryTimeline()
            );
        }

        return $cheapestRate;
    }

}
