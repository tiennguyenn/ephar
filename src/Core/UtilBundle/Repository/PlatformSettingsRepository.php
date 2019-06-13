<?php

namespace UtilBundle\Repository;

use Doctrine\ORM\EntityRepository;
use UtilBundle\Utility\Constant;

/**
 * Platform Setting
 * Author Luyen Nguyen
 * Date: 08/14/2017
 */
class PlatformSettingsRepository extends EntityRepository
{
    /**
     * update platform setting
     * @author vinh.nguyen
     */
    public function update($params)
    {
        $psObj = $this->findOneBy(array('operationsCountryId'=>$params['operationsCountryId']));
        if($psObj == null) {
            return null;
        }

        if(isset($params['local']))
            $psObj->setLocal($params['local']);

        if(isset($params['overseas']))
            $psObj->setOverseas($params['overseas']);

        if(isset($params['pharmacyWeeklyPoDay']))
            $psObj->setPharmacyWeeklyPoDay($params['pharmacyWeeklyPoDay']);

        if(isset($params['pharmacyWeeklyPoTime']))
            $psObj->setPharmacyWeeklyPoTime($params['pharmacyWeeklyPoTime']);

        if(isset($params['deliveryFortnightlyPoDay']))
            $psObj->setDeliveryFortnightlyPoDay($params['deliveryFortnightlyPoDay']);

        if(isset($params['deliveryFortnightlyPoTime']))
            $psObj->setDeliveryFortnightlyPoTime($params['deliveryFortnightlyPoTime']);

        if(isset($params['agentStatementDate']))
            $psObj->setAgentStatementDate($params['agentStatementDate']);

        if(isset($params['doctorStatementDate']))
            $psObj->setDoctorStatementDate($params['doctorStatementDate']);

        if(isset($params['reminderRxRefill30']))
            $psObj->setReminderRxRefill30($params['reminderRxRefill30']);

        if(isset($params['reminderRxRefill60']))
            $psObj->setReminderRxRefill60($params['reminderRxRefill60']);

        if(isset($params['bufferRate']))
            $psObj->setBufferRate($params['bufferRate']);

        if(isset($params['scheduleDeclarationTime']))
            $psObj->setScheduleDeclarationTime($params['scheduleDeclarationTime']);
        if(isset($params['minFeeAgentForLocalRx']))
            $psObj->setMinFeeAgentForLocalRx($params['minFeeAgentForLocalRx']);
        if(isset($params['minFeeAgentForOverseasRx']))
            $psObj->setMinFeeAgentForOverseasRx($params['minFeeAgentForOverseasRx']);

        $em = $this->getEntityManager();
        $em->persist($psObj);
        $em->flush();

        return $this->getPlatFormSetting();
    }

    /**
     * get platform setting
     * @author  thu.tranq
     * @return array
     */
    public function getPlatFormSetting() {
        $queryBuilder = $this->createQueryBuilder('ps');
        $queryBuilder->select('ps.operationsCountryId',
            'ps.local',
            'ps.overseas',
            'ps.gstRate',
            'ps.agentStatementDate',
            'ps.doctorStatementDate',
            'ps.pharmacyWeeklyPoDay',
            'ps.pharmacyWeeklyPoTime',
            'ps.deliveryFortnightlyPoDay',
            'ps.deliveryFortnightlyPoTime',
            'ps.currencyCode',
            'ps.bufferRate',
            'ps.currencyNumber',
            'ps.reminderRxRefill30',
            'ps.reminderRxRefill60',
            'ps.isGst',
            'ps.gstAffectDate',
            'ps.gstNo',
            'ps.scheduleDeclarationTime',
            'ps.minFeeAgentForLocalRx',
            'ps.minFeeAgentForOverseasRx'
        )
            ->setMaxResults(1);

         return $queryBuilder->getQuery()->getSingleResult();
    }

    /**
     * get platform setting option
     * @author  vinh.nguyen
     * @return array
     */
    public function getPSOption($option = null)
    {
        $qb = $this->createQueryBuilder('ps');
        $qb->select('ps.operationsCountryId');
        if(!empty($option)){
            switch($option):
                case 'payment_schedule':
                    $qb->addSelect('ps.agentStatementDate',
                        'ps.doctorStatementDate',
                        'ps.pharmacyWeeklyPoDay',
                        'ps.pharmacyWeeklyPoTime',
                        'ps.deliveryFortnightlyPoDay',
                        'ps.deliveryFortnightlyPoTime');
                    break;

                case 'product_margin':
                    $qb->addSelect('ps.local', 'ps.overseas');
                    break;
            endswitch;
        }
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * update gst rate
     * @author vinh.nguyen
     */
    public function updateGstRate($params)
    {
        $psObj = $this->find($params['operationsCountryId']);
        if($psObj == null)
            return null;

        if(isset($params['newGstRate']))
            $psObj->setNewGstRate($params['newGstRate']);

        if(isset($params['gstRateAffectDate']) && !empty($params['gstRateAffectDate']))
            $psObj->setGstRateAffectDate(new \DateTime($params['gstRateAffectDate']));

        if(isset($params['gstAffectDate']) && !empty($params['gstAffectDate'])){
            $psObj->setGstAffectDate(new \DateTime($params['gstAffectDate']));
        }

        if(isset($params['gstNo']) && !empty($params['gstNo']))
            $psObj->setGstNo($params['gstNo']);

        if(isset($params['isGst']) )
            $psObj->setIsGst($params['isGst']);

        $em = $this->getEntityManager();
        $em->persist($psObj);
        $em->flush();

        return $this->getGstRate();
    }

    /**
     * get gst rate
     * @author vinh.nguyen
     */
    public function getGstRate($gstRateOnly = false)
    {
        $qb = $this->createQueryBuilder('ps');
        $qb->select('
            ps.operationsCountryId,
            ps.gstRate,
            ps.newGstRate,
            ps.gstRateAffectDate,
            ps.gstNo,
            ps.gstAffectDate,
            ps.isGst
        ')->setMaxResults(1);
        $items = $qb->getQuery()->getOneOrNullResult();

        if(null != $items && !empty($items['gstRateAffectDate'])) {
            $dateTime = new \DateTime();
            $currentDate = $dateTime->format("Y-m-d");
            $affectDate = $items['gstRateAffectDate']->format("Y-m-d");

            if($items['gstRate'] != $items['newGstRate'] && $currentDate >= $affectDate) {
                $items['gstRate'] = $items['newGstRate'];
                //update gstRate
                $psObj = $this->find($items['operationsCountryId']);
                if($psObj != null) {
                    $psObj->setGstRate($items['gstRate']);
                    $em = $this->getEntityManager();
                    $em->persist($psObj);
                    $em->flush();
                }
            }
        }

        if($gstRateOnly)
            return $items['gstRate'];

        return $items;
    }

    /**
     * get payment schedule
     * @author vinh.nguyen
     */
    public function getPaymentSchedule()
    {
        $qb = $this->createQueryBuilder('ps');
        $qb->select(
            'ps.operationsCountryId as id',
            'ps.doctorStatementDate',
            'ps.agentStatementDate',
            'ps.pharmacyWeeklyPoDay',
            'ps.pharmacyWeeklyPoTime',
            'ps.pharmacyTargetDate',
            'ps.deliveryFortnightlyPoDay',
            'ps.deliveryFortnightlyPoTime',
            'ps.deliveryTargetDate
        ')->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * To get shipping gst code
     *
     * @param $patient Patient
     * @param $isLocalPatient boolean
     */
    public function getShippingGSTCode($patient, $isLocalPatient = null)
    {
        $settings = $this->getPlatFormSetting();

        $params = array(
            'patient'  => $patient,
            'settings' => $settings
        );

        if (!isset($isLocalPatient)) {
            $rxRes = $this->getEntityManager()->getRepository('UtilBundle:Rx');
            $isLocalPatient = $rxRes->isLocalPatient($params);
        }

        $feeCode = Constant::SF_PATIENT_OVERSEA;
        if ($isLocalPatient) {
            $feeCode = Constant::SF_PATIENT_LOCAL;
        }

        $criteria = array('feeCode' => $feeCode);
        $psgc = $this->getEntityManager()->getRepository('UtilBundle:PlatformSettingGstCode')
            ->findOneBy($criteria);

        if (!$psgc) {
            return Constant::GST_ZRS;
        }

        $gstCode = $psgc->getGstCode();
        if (!$gstCode) {
            return Constant::GST_ZRS;
        }

        return $gstCode->getCode();
    }

    /**
     * To get platform customs clearance gst code
     *
     * @param $rx Rx
     */
    public function getCCGSTCode($rx)
    {
        if (!$rx) {
            return Constant::GST_ZRS;
        }

        $shippingAddress = $rx->getShippingAddress();
        if (!$shippingAddress) {
            return Constant::GST_ZRS;
        }

        $shippingCountryCode = $shippingAddress->getCity()
            ->getCountry()->getCode();

        $feeCode = null;
        if (Constant::SINGAPORE_CODE == $shippingCountryCode) {
            $feeCode = Constant::GM_CCAF_SG;
        }
        if (Constant::INDONESIA_CODE == $shippingCountryCode) {
            $feeCode = Constant::GM_CCAF_ID;
        }

        if (!$feeCode) {
            return Constant::GST_ZRS;
        }

        $criteria = array('feeCode' => $feeCode);
        $psgc = $this->getEntityManager()->getRepository('UtilBundle:PlatformSettingGstCode')
            ->findOneBy($criteria);

        if (!$psgc) {
            return Constant::GST_ZRS;
        }

        $gstCode = $psgc->getGstCode();
        if (!$gstCode) {
            return Constant::GST_ZRS;
        }

        return $gstCode->getCode();
    }

    /**
     * To get platform customs tax gst code
     *
     * @param $rx Rx
     */
    public function getCTGSTCode($rx)
    {
        if (!$rx) {
            return Constant::GST_ZRS;
        }

        $shippingAddress = $rx->getShippingAddress();
        if (!$shippingAddress) {
            return Constant::GST_ZRS;
        }

        $shippingCountryCode = $shippingAddress->getCity()
            ->getCountry()->getCode();

        $feeCode = Constant::CCAF_ID;
        if (Constant::SINGAPORE_CODE == $shippingCountryCode) {
            $feeCode = Constant::GM_CT_SG;
        }
        if (Constant::INDONESIA_CODE == $shippingCountryCode) {
            $feeCode = Constant::GM_CT_ID;
        }

        $criteria = array('feeCode' => $feeCode);
        $psgc = $this->getEntityManager()->getRepository('UtilBundle:PlatformSettingGstCode')
            ->findOneBy($criteria);

        if (!$psgc) {
            return Constant::GST_ZRS;
        }

        $gstCode = $psgc->getGstCode();
        if (!$gstCode) {
            return Constant::GST_ZRS;
        }

        return $gstCode->getCode();
    }

    /**
     * To get to has GST
     *
     */
    public function hasGST()
    {
        $settings = $this->getPlatFormSetting();

        if (empty($settings) || empty($settings['isGst']) || empty($settings['gstAffectDate'])) {
            return false;
        }

        $today = new \DateTime();
        $today->setTime(23, 59, 59);
        if ($settings['gstAffectDate'] > $today) {
            return false;
        }

        return true;
    }
}
