<?php

/**
 * Created by PhpStorm.
 * User: phuc.duong
 * Date: 8/18/17
 * Time: 10:15 AM
 */

namespace UtilBundle\Microservices;

use UtilBundle\Entity\FxRateLog;
use UtilBundle\Utility\Constant;
use \UtilBundle\Entity\FxRate;

class FxService {

    protected $container;
    protected $em;

    public function __construct($container, $em) {
        $this->container = $container;
        $this->em = $em;
    }

    /**
     * Currency Exchange
     * author luyen nguyen
     */
    public function updateCurrencyExchange($date) {
        $apiService = $this->container->get('microservices.api');
        $currencyExchanges = $apiService->getCurrencyExchange($date);
        if ($currencyExchanges != null) {
            if($this->saveData($currencyExchanges)) {
                return true;
            }
        } else {
            //insert rate log
            //sgd -> myr
            $fxRateMyr = $this->em->getRepository('UtilBundle:FxRate')
                ->getRate(Constant::CURRENCY_SINGAPORE, Constant::CURRENCY_MALAYSIA);
            $rateMYR = ($fxRateMyr != null)? $fxRateMyr->getRate(): 0;
            $this->insertLog(Constant::CURRENCY_SINGAPORE, Constant::CURRENCY_MALAYSIA, $rateMYR);

            //sgd -> idr
            $fxRateIdr = $this->em->getRepository('UtilBundle:FxRate')
                ->getRate(Constant::CURRENCY_SINGAPORE, Constant::CURRENCY_INDONESIA);
            $rateIDR = ($fxRateIdr != null)? $fxRateIdr->getRate(): 0;
            $this->insertLog(Constant::CURRENCY_SINGAPORE, Constant::CURRENCY_INDONESIA, $rateIDR);

            //sgd -> usd
            $fxRateUsd = $this->em->getRepository('UtilBundle:FxRate')
                ->getRate(Constant::CURRENCY_SINGAPORE, Constant::CURRENCY_USD);
            $rateUSD = ($fxRateUsd != null)? $fxRateUsd->getRate(): 0;
            $this->insertLog(Constant::CURRENCY_SINGAPORE, Constant::CURRENCY_USD, $rateUSD);

            //usd -> sgd
            $fxRateUsdSgd = $this->em->getRepository('UtilBundle:FxRate')
                ->getRate(Constant::CURRENCY_USD, Constant::CURRENCY_SINGAPORE);
            $rateUSDSGD = ($fxRateUsdSgd != null)? $fxRateUsdSgd->getRate(): 0;
            $this->insertLog(Constant::CURRENCY_USD, Constant::CURRENCY_SINGAPORE, $rateUSDSGD);

            $this->em->flush();
        }
        return false;
    }

    /**
     * Save Fx Rate
     * @param type $currencyExchanges
     */
    public function saveData($currencyExchanges)
    {
        //sgd -> myr
        $fxRateMyr = $this->em->getRepository('UtilBundle:FxRate')
                ->getRate(Constant::CURRENCY_SINGAPORE, Constant::CURRENCY_MALAYSIA);
        $rateMyr = Constant::ONE_NUMBER/($currencyExchanges['myr_sgd_100'] / Constant::ONE_HUNDRE_NUMBER);
        $rateMyr = round($rateMyr, 2);
        if ($fxRateMyr != null) {
            $this->update($fxRateMyr, $rateMyr);
        } else {
            $this->insert(Constant::CURRENCY_SINGAPORE, Constant::CURRENCY_MALAYSIA, $rateMyr);
        }
        //insert rate log
        $this->insertLog(Constant::CURRENCY_SINGAPORE, Constant::CURRENCY_MALAYSIA, $rateMyr);

        //sgd -> idr
        $fxRateIdr = $this->em->getRepository('UtilBundle:FxRate')
                ->getRate(Constant::CURRENCY_SINGAPORE, Constant::CURRENCY_INDONESIA);
        $rateIdr = Constant::ONE_NUMBER/($currencyExchanges['idr_sgd_100'] / Constant::ONE_HUNDRE_NUMBER);
        $rateIdr = round($rateIdr, 2);
        if ($fxRateIdr != null) {
            $this->update($fxRateIdr, $rateIdr);
        } else {
            $this->insert(Constant::CURRENCY_SINGAPORE, Constant::CURRENCY_INDONESIA, $rateIdr);
        }
        //insert rate log
        $this->insertLog(Constant::CURRENCY_SINGAPORE, Constant::CURRENCY_INDONESIA, $rateIdr);

        //sgd -> usd
        $fxRateUsd = $this->em->getRepository('UtilBundle:FxRate')
            ->getRate(Constant::CURRENCY_SINGAPORE, Constant::CURRENCY_USD);
        $rateUsd = Constant::ONE_NUMBER/$currencyExchanges['usd_sgd'];
        $rateUsd = round($rateUsd, 2);
        if ($fxRateUsd != null) {
            $this->update($fxRateUsd, $rateUsd);
        } else {
            $this->insert(Constant::CURRENCY_SINGAPORE, Constant::CURRENCY_USD, $rateUsd);
        }
        //insert rate log
        $this->insertLog(Constant::CURRENCY_SINGAPORE, Constant::CURRENCY_USD, $rateUsd);

        //usd -> sgd
        $fxRateUsdSgd = $this->em->getRepository('UtilBundle:FxRate')
            ->getRate(Constant::CURRENCY_USD, Constant::CURRENCY_SINGAPORE);
        $rateUsdSgd = round($currencyExchanges['usd_sgd'], 2);
        if ($fxRateUsdSgd != null) {
            $this->update($fxRateUsdSgd, $rateUsdSgd);
        } else {
            $this->insert(Constant::CURRENCY_USD, Constant::CURRENCY_SINGAPORE, $rateUsdSgd);
        }
        //insert rate log
        $this->insertLog(Constant::CURRENCY_USD, Constant::CURRENCY_SINGAPORE, $rateUsdSgd);

        $this->em->flush();
        return true;
    }

    /**
     * Update
     * @param type $fxRate
     * @param type $rate
     */
    public function update($fxRate, $rate) {
        $fxRate->setRate($rate);
        $fxRate->setUpdatedOn(new \DateTime());
        $this->em->persist($fxRate);
    }

    /**
     * Insert
     * @param type $currencyFrom
     * @param type $currencyTo
     * @param FxRate $rate
     */
    public function insert($currencyFrom, $currencyTo, $rate) {
        $fxRate = new FxRate();
        $fxRate->setCurrencyFrom($currencyFrom);
        $fxRate->setCurrencyTo($currencyTo);
        $fxRate->setRate($rate);
        $fxRate->setCreateOn(new \DateTime());
        $fxRate->setUpdatedOn(new \DateTime());
        $this->em->persist($fxRate);
    }

    /**
     * insert rate log
     * @param $currencyFrom
     * @param $currencyTo
     * @param $rate
     */
    public function insertLog($currencyFrom, $currencyTo, $rate)
    {
        $fxRateLog = new FxRateLog();
        $fxRateLog->setCurrencyFrom($currencyFrom);
        $fxRateLog->setCurrencyTo($currencyTo);
        $fxRateLog->setRate($rate);
        $fxRateLog->setCreateOn(new \DateTime());
        $this->em->persist($fxRateLog);
    }

}
