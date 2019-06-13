<?php

namespace UtilBundle\Repository;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\DBAL\Connection;
use Doctrine\Common\Collections\Criteria;
use UtilBundle\Entity\Log;
use UtilBundle\Entity\Patient;
use UtilBundle\Entity\Rx;
use UtilBundle\Entity\RxLine;
use UtilBundle\Entity\Issue;
use UtilBundle\Entity\RxStatusLog;
use UtilBundle\Entity\DoctorMedicalFavourite;
use UtilBundle\Entity\RxRefillReminder;
use UtilBundle\Entity\DosageFormAction;
use UtilBundle\Entity\RxReminderSetting;
use UtilBundle\Utility\Common;
use UtilBundle\Utility\Constant;
use UtilBundle\Utility\MonthlyPdfHelper;
use UtilBundle\Utility\MsgUtils;
use UtilBundle\Microservices\TaxService;
use UtilBundle\Utility\Utils;
/**
 * RxRepository
 * Author Luyen Nguyen
 * Date: 08/14/2017
 */
class RxRepository extends EntityRepository
{
    private $container;

    public function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * Get Transaction Listing Report
     * The function will be used for Admin
     * @param  array $params
     * @author  thu.tranq
     * @return  array
     */
    public function getTransactionListingReport($params) {
        $drugType = Constant::RX_LINE_TYPE_DRUG;
        $serviceType = Constant::RX_LINE_TYPE_SERVICE;

        // get platform setting repository
        $psRepository = $this
            ->getEntityManager()
            ->getRepository('UtilBundle:PlatformSettings');
        $psObj = $psRepository->getPlatFormSetting();

        $pgfRepo = $this->getEntityManager()->getRepository('UtilBundle:PaymentGatewayFee');
        $visaMasterFee = $pgfRepo->getFeeSettingBy(Constant::PAY_METHOD_VISA_MASTER);
        $revpayFpxFee = $pgfRepo->getFeeSettingBy(Constant::PAY_METHOD_REVPAY_FPX);

        // find all order from rx table by $doctorId
        $queryBuilder = $this->createQueryBuilder('r');
        $selectStr = " r.orderNumber as orderNumber,
                       r.paidOn as paidOn,
                       r.status as status,
                       r.id as rxId,
                       ifelse(pi.firstName is null, pi.lastName, CONCAT(pi.firstName, ' ', pi.lastName)) AS doctorName,
                       ifelse(pi2.firstName is null, pi2.lastName, CONCAT(pi2.firstName, ' ', pi2.lastName)) AS patientName,
                       r.orderValue as orderValue,
                       d.doctorCode as doctorCode,
                       p.patientCode as patientCode,
                       pl.payMethod,
                       r.igPermitFee,
                       r.taxIncome,
                       r.taxIncomeWithoutTax,
                       r.taxVat,
                       r.taxImportDuty,
                       r.agent3paServiceFee,
                       r.agent3paMedicineFee
                       ";

        $queryBuilder->select($selectStr)
                            ->innerJoin('r.doctor', 'd')
                            ->innerJoin('d.personalInformation', 'pi')
                            ->innerJoin('r.patient', 'p')
                            ->innerJoin('p.personalInformation', 'pi2')
                            ->leftJoin('r.shippingAddress', 'a')
                            ->innerJoin('a.city', 'ci')
                            ->innerJoin('ci.country', 'c')
                            ->leftJoin('UtilBundle:RxPaymentLog', 'pl', 'WITH', 'pl.orderRef = r.orderNumber')
                            ->where('r.deletedOn is null')
                            ->andWhere('pl.paymentResult = 1')
                            ->andWhere('r.paidOn is not null');

        if (empty($params['transReport'])) {
            $queryBuilder
                ->andWhere('r.status != :failStatus')
                ->andWhere('r.status != :deadStatus')
                ->andWhere('r.status != :pfailStatus')
                ->setParameter('failStatus', Constant::RX_STATUS_FAILED)
                ->setParameter('deadStatus', Constant::RX_STATUS_DEAD)
                ->setParameter('pfailStatus', Constant::RX_STATUS_PAYMENT_FAILED);
        }

        if (!empty($params['isAgentReport'])) {
            $queryBuilder->addSelect('(r.agentMedicineFee + r.agentServiceFee ) as agentFee', 'r.agentMedicineFee', 'r.agentServiceFee')
                ->addSelect('r')
                ->innerJoin('UtilBundle:RxLine', 'rxl', 'WITH', 'r=rxl.rx')
                ->groupBy('r.id')
                ->andWhere('r.status != :failStatus')
                ->andWhere('r.status != :deadStatus')
                ->andWhere('r.status != :pfailStatus')
                ->setParameter('failStatus', Constant::RX_STATUS_FAILED)
                ->setParameter('deadStatus', Constant::RX_STATUS_DEAD)
                ->setParameter('pfailStatus', Constant::RX_STATUS_PAYMENT_FAILED);
        }

        if (!empty($params['isDoctorReport'])) {
            $queryBuilder->addSelect('ms.doctorAmount')
                ->addSelect('r')
                ->innerJoin('UtilBundle:MarginShare', 'ms', 'WITH', 'r=ms.rx')
                ->innerJoin('UtilBundle:RxLine', 'rxl', 'WITH', 'r=rxl.rx')
                ->groupBy('r.id')
                ->andWhere('r.status != :failStatus')
                ->andWhere('r.status != :deadStatus')
                ->andWhere('r.status != :pfailStatus')
                ->setParameter('failStatus', Constant::RX_STATUS_FAILED)
                ->setParameter('deadStatus', Constant::RX_STATUS_DEAD)
                ->setParameter('pfailStatus', Constant::RX_STATUS_PAYMENT_FAILED);
        }

        // filter by patient type: local or oversea
        if ( isset($params['patientType']) and !empty($params['patientType'])) {
            if ((int)$params['patientType'] == Constant::AREA_TYPE_LOCAL) {
                $queryBuilder->andWhere("p.primaryResidenceCountry = :operationsCountryId")
                             ->setParameter('operationsCountryId', $psObj['operationsCountryId']);
            } else if ((int)$params['patientType'] == Constant::AREA_TYPE_OVERSEA) {
                $queryBuilder->andWhere("p.primaryResidenceCountry != :operationsCountryId")
                         ->setParameter('operationsCountryId', $psObj['operationsCountryId']);
            }
        }
        // filter by country
        if ( isset($params['countryCode']) and !empty($params['countryCode'])) {
            $queryBuilder->andWhere("c.code = :countryCode")
                         ->setParameter('countryCode', $params['countryCode']);
        }

        //filter by date
        if(isset($params['fromDate']) && !empty($params['fromDate'])){
            $startDate = new \DateTime($params['fromDate']);
            $endDate = new \DateTime($params['toDate']);
            $endDate->modify('tomorrow');
            $endDate->modify('1 second ago');
            $queryBuilder
                ->andWhere('r.paidOn <= :end AND r.paidOn >= :start')
                ->setParameter('start', $startDate->format("Y-m-d H:i:s"))
                ->setParameter('end', $endDate->format("Y-m-d H:i:s"));
        }

        // filter by doctor feed
        $orderValueGte = isset($params['orderValueGte']) ? $params['orderValueGte'] : '';
        $orderValueLte = isset($params['orderValueLte']) ? $params['orderValueLte'] : '';

        $fee = 'r.orderValue';
        if (!empty($params['isAgentReport'])) {
            $fee = '(r.agentMedicineFee + r.agentServiceFee)';
        }
        if (!empty($params['isDoctorReport'])) {
            $fee = 'ms.doctorAmount';
        }

        if (!empty($orderValueGte) && !empty($orderValueLte)) {
            $queryBuilder->andWhere($fee . ' >= :orderValueGte')
                        ->setParameter('orderValueGte', $orderValueGte)
                        ->andWhere($fee . ' <= :orderValueLte')
                        ->setParameter('orderValueLte', $orderValueLte);
        } elseif (!empty($orderValueGte) && empty($orderValueLte)) {

            $queryBuilder->andWhere($fee . ' >= :orderValueGte')
                        ->setParameter('orderValueGte', $orderValueGte);

        } elseif (empty($orderValueGte) && !empty($orderValueLte)) {
            $queryBuilder->andWhere($fee . ' <= :orderValueLte')
                        ->setParameter('orderValueLte', $orderValueLte);
        }

        //search on patient: code, name
        if(isset($params['patientTerm']) && !empty($params['patientTerm'])) {
            $term = trim(strtolower($params['patientTerm']));

            $searchIn = $queryBuilder->expr()->like(
                            $queryBuilder->expr()->concat('pi2.firstName', $queryBuilder->expr()->concat($queryBuilder->expr()->literal(' '), 'pi2.lastName')),
                            $queryBuilder->expr()->literal( '%' . $term . '%')
                        );

            $queryBuilder
                ->andWhere($searchIn ." OR LOWER(p.patientCode) LIKE :patientTerm")
                ->setParameter('patientTerm', '%' . $term . '%');
        }

        //search on doctor: code, name
        if(isset($params['doctorTerm']) && !empty($params['doctorTerm'])) {
            $term = trim(strtolower($params['doctorTerm']));

            $searchIn = $queryBuilder->expr()->like(
                            $queryBuilder->expr()->concat('pi.firstName', $queryBuilder->expr()->concat($queryBuilder->expr()->literal(' '), 'pi.lastName')),
                            $queryBuilder->expr()->literal( '%' . $term . '%')
                        );

            $queryBuilder
                ->andWhere($searchIn ." OR LOWER(d.doctorCode) LIKE :doctorTerm")
                ->setParameter('doctorTerm', '%' . $term . '%');
        }

        //search on doctor: code, name
        if(isset($params['orderTerm']) && !empty($params['orderTerm'])) {
            $term = trim(strtolower($params['orderTerm']));

            $literal = $queryBuilder->expr()->literal("%$term%");
            $exp  = $queryBuilder->expr()->like('r.orderNumber', $literal);
            $exp1 = $queryBuilder->expr()->like('r.orderPhysicalNumber', $literal);
            $queryBuilder->andWhere($queryBuilder->expr()->orX($exp, $exp1));
        }

        // header fields sorting
        if (isset($params['sortInfo']) and !empty($params['sortInfo'])) {
            $queryBuilder->orderBy($params['sortInfo']['column'], $params['sortInfo']['direction']);
        } else {
            if (!empty($params['isAgentReport'])) {
                $queryBuilder->orderBy('r.paidOn', 'ASC');
            }
        }

        if (isset($params['isCsv'])) {
            return array(
                'data' => $queryBuilder->getQuery()->getArrayResult()
            );
        }


        $perPage     = ($params['perPage'] > 0)? $params['perPage']: Constant::PER_PAGE_DEFAULT;
        $page        = ($params['page'] > 0)? $params['page']: Constant::PAGE_DEFAULT;
        $startRecord = $perPage * $page;

        $totalResult = count($queryBuilder->getQuery()->getArrayResult());

        $queryBuilder
            ->setFirstResult($startRecord)
            ->setMaxResults($perPage);

        if (!empty($params['isAgentReport'])) {
            $result = $queryBuilder->getQuery()->getResult();
            foreach ($result as &$value) {
                $rx = isset($value[0]) ? $value[0] : array();
                $rxLines = $rx->getRxLines();
                $costOfMdc = 0;
                $totalListPrice = 0;
                $prescribingFeeT = 0;
                foreach ($rxLines as $line) {
                    if ($drugType == $line->getLineType()) {
                        $costOfMdc += $line->getOriginPrice() * $line->getQuantity();
                        $totalListPrice += $line->getListPrice();
                    }
                    if ($serviceType == $line->getLineType()) {
                        $prescribingFeeT += $line->getListPrice();
                    }
                }
                $value['costOfMdc'] = $costOfMdc;
                $value['grossMarginT'] = $totalListPrice - $costOfMdc;
                $value['prescribingFeeT'] = $prescribingFeeT;
            }
        } elseif (!empty($params['isDoctorReport'])) {
            $result = $queryBuilder->getQuery()->getResult();
            foreach ($result as $key => &$value) {
                $rx = isset($value[0]) ? $value[0] : array();
                $rxLines = $rx->getRxLines();
                $value['rxLines'] = $rxLines;
                $value['rx'] = $rx;
                $value['ccFee'] = $visaMasterFee[Constant::GATEWAY_CODE_GST] / 100;
                $value['fpxFee'] = $revpayFpxFee[Constant::GATEWAY_CODE_FIX_GST] / 100;
            }
        } else {
            $result = $queryBuilder->getQuery()->getArrayResult();
        }

        return array(
            'totalResult' => $totalResult,
            'totalPages' => ceil($totalResult/$perPage),
            'data' => $result
        );
    }

    /**
     * get detail of order on transaction listing report page
     * @param  integer $rxId
     * @author  thu.tranq
     * @return array
     */
    public function getAdminRxDetail($rxId) {
        $queryBuilder = $this->createQueryBuilder('r');
        $selectStr = "
            r.id as rxId, r.agent3paServiceFee, r.agent3paMedicineFee, r.feeGst, r.customsTax, r.customTaxByCourier, r.paymentGatewayFeeBankGst, r.paymentGatewayFeeBankMdr, r.paymentGatewayFeeFixed, r.paymentGatewayFeeVariable, r.customsClearancePlatformFee, r.customsClearanceDoctorFee, r.orderValue, r.receiptNo, r.status, r.shippingCost, r.shippingList,
            SUM(ms.doctorAmount) as doctorAmount , ms.agentAmount as agentAmount, SUM(ms.platformAmount) as platformAmount,
            CONCAT(s.name, ', ' ,c.name) as destination,
            rl.agentMedicineFee, rl.doctorMedicineFee, rl.quantity, rl.originPrice, rl.platformMedicineFee, rl.agentServiceFee, rl.doctorServiceFee, rl.listPrice, rl.lineType, rl.name, rl.platformServiceFee,
            pl.payMethod,
            pl.paymentResult,
            r.paymentGate,
            r.gstRate,
            r.igPermitFee, r.igPermitFeeByCourier, c.code as countryCode,
            r.taxVat, r.taxIncome, r.taxIncomeWithoutTax, r.taxImportDuty,
            r.agentMedicineFee as rxAMF,
            r.platformServiceFee as rxPSF,
            ms.agentAmount as msAgentAmount
        ";

        $queryBuilder->select($selectStr)
                            ->innerJoin('UtilBundle:RxLine', 'rl', 'WITH', 'rl.rx = r.id')
                            ->leftJoin('r.shippingAddress', 'a')
                            ->innerJoin('a.city', 'ci')
                            ->innerJoin('ci.country', 'c')
                            ->innerJoin('UtilBundle:MarginShare', 'ms', 'WITH', 'ms.rx = r.id')
                            ->innerJoin('ci.state', 's')
                            ->leftJoin('UtilBundle:RxPaymentLog', 'pl', 'WITH', 'pl.orderRef = r.orderNumber')
                            ->where('r.id = :rxId')
                            ->setParameter('rxId', $rxId)
                            ->groupBy('rl.id')
                            ->orderBy('rl.lineType');

       $result = $queryBuilder->getQuery()->getArrayResult();

        $data = array();
        if (isset($result) && count($result) > 0) {
            $pgfRepo = $this->getEntityManager()->getRepository('UtilBundle:PaymentGatewayFee');
            $visaMasterFee = $pgfRepo->getFeeSettingBy(Constant::PAY_METHOD_VISA_MASTER);
            $revpayFpxFee = $pgfRepo->getFeeSettingBy(Constant::PAY_METHOD_REVPAY_FPX);

            foreach ($result as $item) {
                $tmp = $item;
                $remove = ['rxId', 'feeGst', 'customsTax', 'paymentGatewayFeeBankGst', 'paymentGatewayFeeBankMdr', 'paymentGatewayFeeFixed','paymentGatewayFeeVariable',
                'customsClearancePlatformFee', 'customsClearanceDoctorFee', 'orderValue', 'receiptNo',
                'status', 'shippingCost', 'shippingList', 'doctorAmount', 'agentAmount', 'countryName'];

                $tmp = array_diff_key($tmp, array_flip($remove));
                $data['rxLines'][] = $tmp;

                //
                $bankGST = "";
                $bankGST = "";
                $fixedGST = "";
                if($item['payMethod'] == Constant::PAY_METHOD_VISA_MASTER) { //CC
                    $iFee = $visaMasterFee[Constant::GATEWAY_CODE_GST] / 100;
                    $bankGST = $item['paymentGatewayFeeBankMdr'] * $iFee;
                    $methodLabel = "CC";
                } elseif($item['payMethod'] == Constant::PAY_METHOD_REVPAY_FPX) { //REVPAY-FPX
                    $iFee = $visaMasterFee[Constant::GATEWAY_CODE_FIX_GST] / 100;
                    $fixedGST = $item['paymentGatewayFeeFixed'] * $iFee;
                }

                // if (isset($data[$item['rxId']]) and !isset($data[$item['rxId']]['rx'])) {
                if (!isset($data['info'])) {
                    $rxInfo =
                        array(
                            'rxId'        => $item['rxId'],
                            'paymentGate' => $item['paymentGate'],
                            'invoiceNo'   => $item['receiptNo'],
                            'feeGst'      => $item['feeGst'],
                            'customsTax'  => $item['customsTax'],
                            'destination' => $item['destination'],
                            'status'      => Constant::getRXStatus($item['status']),
                            'shippingFee' => array($item['shippingList'], 0, 0, 0, $item['shippingCost'], $item['shippingList']),
                            'gst'         => array($item['feeGst'], 0, $item['feeGst'], 0, 0, 0),
                            'customsTax'                => array($item['customsTax'], 0, 0, 0, $item['customTaxByCourier'], 0),
                            'paymentGatewayFeeMDR'      => array(0, 0, $item['paymentGatewayFeeBankMdr'], 0, 0, $item['paymentGatewayFeeBankMdr']),
                            'paymentGatewayFeeGST'      => array(0, 0, $bankGST, 0, 0, $bankGST),
                            'paymentGatewayFeeVariable' => array(0, 0, $item['paymentGatewayFeeVariable'], 0, 0, $item['paymentGatewayFeeVariable']),
                            'paymentGatewayFeeFixed'    => array(0, 0, $item['paymentGatewayFeeFixed'], 0, 0, $item['paymentGatewayFeeFixed']),
                            'paymentGatewayFeeFixedGST' => array(0, 0, $fixedGST, 0, 0, $fixedGST),
                            'customsClearanceAdminFee'  => array($item['customsClearancePlatformFee'], 0, $item['customsClearanceDoctorFee'], 0, 0, (float)$item['customsClearancePlatformFee'] - (float)$item['customsClearanceDoctorFee']),
                            'sgigPermitFee'             => array($item['igPermitFee'], 0, 0, 0, $item['igPermitFeeByCourier'], 0),
                            'subTotal'                  => array($item['orderValue'], $item['agentAmount'], $item['doctorAmount'], 0, $item['shippingCost'],  $item['platformAmount'], $item['msAgentAmount']),
                            'payMethod'                 => $item['payMethod'],
                            'paymentResult' => $item['paymentResult'],
                            'countryCode'               => $item['countryCode'],
                            'importDuty'                => $item['taxImportDuty'],
                            'taxIncome'                 => $item['taxIncome'],
                            'taxIncomeWithoutTax'       => $item['taxIncomeWithoutTax'],
                            'taxVat'                    => $item['taxVat'],
                            'rxAMF' => $item['rxAMF'],
                            'rxPSF' => $item['rxPSF'],
                            'agent3paServiceFee' => $item['agent3paServiceFee'],
                            'agent3paMedicineFee' => $item['agent3paMedicineFee']
                          );
                    $data['info'] = $rxInfo;
                }

             }
        }

       return $data;
    }


    /**
     * the function will be used for downloading csv on user click on Rx Transaction History button
     * @param  array $params
     * @return array
     */
    public function getAdminRxTransactionHistory($params) {
        $drugType = Constant::RX_LINE_TYPE_DRUG;
        $serviceType = Constant::RX_LINE_TYPE_SERVICE;

        $queryBuilder = $this->createQueryBuilder('r');

        return array();
    }

    /**
     * get rx details for transaction report pages
     * @param  $rxId
     * @author  thu.tranq
     * @return array
     */
    public function getRxDetails($rxId) {
        $drugType = Constant::RX_LINE_TYPE_DRUG;
        $serviceType = Constant::RX_LINE_TYPE_SERVICE;

        $queryBuilder = $this->createQueryBuilder('r');
        $selectStr = "
            rl.id, r.id rxId, r.hasRxReviewFee,
            rl.lineType, rl.listPrice as totalFee, rl.costPrice, rl.name,
            rl.doctorMedicineFee, rl.doctorServiceFee,
            rl.platformServiceFee, rl.agentServiceFee,
            r.customsClearanceDoctorFee
        ";

        $queryBuilder->select($selectStr)
                            ->innerJoin('UtilBundle:RxLine', 'rl', 'WITH', 'rl.rx = r.id')
                            ->innerJoin('r.patient', 'p')
                            ->where('r.id = :rxId')
                            ->orderBy('rl.id', 'desc')
                            ->setParameter('rxId', $rxId);

       $result = $queryBuilder->getQuery()->getArrayResult();

       return $result;
    }

    /**
     * get orders in a month
     * @param  array $params
     * @author  thu.tranq
     * @return
     */
    public function getRxsByMonth($params) {
        // get platform setting repository
        $em           = $this->getEntityManager();
        $psRepository = $em->getRepository('UtilBundle:PlatformSettings');
        $psObj = $psRepository->getPlatFormSetting();


        $drugType = Constant::RX_LINE_TYPE_DRUG;
        $serviceType = Constant::RX_LINE_TYPE_SERVICE;
        // find rxs of a doctor by month
        $queryBuilder = $this->createQueryBuilder('r');
        $selectStr = "
            rl.id, r.id rxId, r.hasRxReviewFee, r.paidOn, r.orderNumber, p.patientCode, p.taxId,
            gc.code, r.createdOn,
            ifelse(pi.firstName is null, pi.lastName, CONCAT(pi.firstName, ' ', pi.lastName)) AS fullName,
            rl.lineType, rl.name, rl.quantity, rl.listPrice, rl.listPriceGst, rl.originPrice , pt.name as packingType,
            (p.primaryResidenceCountry) as primaryResidenceCountry,
            rl.doctorMedicineFee, rl.doctorServiceFee, rl.platformMedicineFee, rl.platformServiceFee,
            rl.agentMedicineFee, rl.platformMedicineFee, rl.agentServiceFee, rl.costPrice, rl.costPriceGst,
            r.paymentGatewayFeeBankMdr,
            r.paymentGatewayFeeBankGst,
            r.paymentGatewayFeeBankMdrGstCode,
            r.paymentGatewayFeeVariable,
            r.paymentGatewayFeeVariableGst,
            r.paymentGatewayFeeVariableGstCode,
            r.paymentGatewayFeeFixed,
            r.paymentGatewayFeeFixedGst,
            r.paymentGatewayFeeFixedGstCode,
            r.shippingCost as shippingFee,
            r.shippingList as shippingList,
            r.shippingListGst as shippingListGst,
            r.toDoctorShippingGstCode,
            r.feeGst as feeGst,
            r.customsTax as rxCustomsTax,
            r.customsTaxGst as rxCustomsTaxGst,
            r.customsTaxGstCode as rxcustomsTaxGstCode,
            r.customsClearanceDoctorFee,
            r.customsClearanceDoctorFeeGst,
            r.customsClearanceDoctorFeeGstCode,
            r.prescribingRevenueFeeGst,
            r.prescribingRevenueFeeGstCode,
            r.customsClearancePlatformFee,
            r.customsClearancePlatformFeeGst,
            r.customsClearancePlatformFeeGstCode,
            r.igPermitFee,
            r.igPermitFeeGst,
            r.igPermitFeeGstCode,
            r.medicineGrossMarginGst,
            r.medicineGrossMarginGstCode,
            r.paymentGate,
            pl.payMethod,
            r.doctorMedicinePercentage,
            co.codeAthree as countryName,
            co.code as countryCode,
            co.customsTax as customsTax,
            pl.updatedOn as refundedOn,
            r.taxIncome,
            r.taxIncomeWithoutTax,
            r.taxVat,
            r.taxImportDuty,
            r.gstRate,            
            co.id as shippingCountryId,
            rl.costPriceToClinic
        ";

        $queryBuilder->select($selectStr)
                            ->innerJoin('UtilBundle:RxLine', 'rl', 'WITH', 'rl.rx = r.id')
                            ->leftJoin('UtilBundle:Drug', 'dr', 'WITH', 'dr.id = rl.drug')
                            ->leftJoin('UtilBundle:PackingType', 'pt', 'WITH', 'dr.packingType = pt.id')
                            ->leftJoin('UtilBundle:GstCode', 'gc', 'WITH', 'gc.id = dr.gstCode')
                            ->leftJoin('UtilBundle:RxPaymentLog', 'pl', 'WITH', 'pl.orderRef = r.orderNumber AND pl.paymentType = :paymentType AND pl.status = :refundStatus')
                            ->innerJoin('r.patient', 'p')
                            ->innerJoin('p.personalInformation', 'pi')
                            ->innerJoin('UtilBundle:Address', 'ad', 'WITH', 'ad.id = r.shippingAddress')
                            ->innerJoin('UtilBundle:City', 'ci', 'WITH', 'ci.id = ad.city')
                            ->innerJoin('UtilBundle:Country', 'co', 'WITH', 'co.id = ci.country')

                            ->where('r.doctor = :doctorId and (MONTH(r.paidOn) = :month and YEAR(r.paidOn) = :year OR MONTH(pl.updatedOn) = :month AND YEAR(pl.updatedOn) = :year)')
                            ->andWhere($queryBuilder->expr()->notIn('r.status',[Constant::RX_STATUS_FAILED, Constant::RX_STATUS_PAYMENT_FAILED, Constant::RX_STATUS_DEAD]))
                            ->andWhere('r.deletedOn IS NULL')
                            ->setParameter('doctorId', $params['doctorId'])
                            ->setParameter('month', $params['month'])
                            ->setParameter('year', $params['year'])
                            ->setParameter('paymentType', Constant::PAYMENT_TYPE_REFUND)
                            ->setParameter('refundStatus', Constant::REFUND_STATUS_SUCCESS)
                            ->addOrderBy('r.createdOn',  'asc')
                            ->addOrderBy('rl.lineType',  'asc');

        $result = $queryBuilder->getQuery()->getArrayResult();

        $pfGSTCode = $em->getRepository('UtilBundle:PlatformSettingGstCode')
                            ->getPlaformGSTCode();
        $gMGMSFeeCode = '';
        if (isset($pfGSTCode[Constant::GM_MGMS])) {
            $gMGMSFeeCode = $pfGSTCode[Constant::GM_MGMS];
        }

         // group rx_line by rx id
        $data = array();
        if (isset($result) && count($result) > 0) {
            $tmp1 = 0;
            $tmp2 = 0;

            $em = $this->getEntityManager();
            $pfRate = $em->getRepository('UtilBundle:PlatformSettings')
                ->getGstRate(true);

            foreach ($result as $item) {
                // fixeissue STRIKE-543
                if (isset($params['isTaxInvoice'])) {
                    $item['code'] = $gMGMSFeeCode;
                }
                $data[$item['rxId']]['presDetail'][] = MonthlyPdfHelper::buildRxLineInfo($item, $pfRate);

                if (isset($data[$item['rxId']])) {
                    $rxInfo = MonthlyPdfHelper::buildStatementInfo($item, $em);
                    if (isset($params['isTaxInvoice'])) {
                        $rxInfo = MonthlyPdfHelper::buildTaxInvoiceInfo($item, $pfRate, $em);
                    }

                    $data[$item['rxId']]['rx'] = $rxInfo;
                    $data[$item['rxId']]['shippingCountryId'] = $item['shippingCountryId'];
                }
             }

        }


        // seperate into local and oversea
        if (!empty($data)) {
          $tmp = array();
          $totalAmount = 0;
          foreach ($data as $key => $v) {
            /**
             * Insert gmeds service fee into right after the last rxLine having lineType = 1
             */
            $num = 0;
            $num1 = 0;
            $num2 = 0;
            $num3 = 0;
            $num4 = 0;
            $num5 = 0;
            $num6 = 0;
            foreach ($v['presDetail'] as $rxLine) {
                if ($rxLine['lineType'] == 1) {
                    // for tax invoice pdf
                    $num1 += ($rxLine['listPrice'] - $rxLine['quantity'] * $rxLine['originPrice']) * (100 - $v['rx']['doctorMedicinePercentage'])/100;

                    $num += ($rxLine['listPrice'] - $rxLine['quantity'] * $rxLine['originPrice']) * (100 - $v['rx']['doctorMedicinePercentage'])/100;
                    $num4 += $rxLine['originPrice'] * $rxLine['quantity'];
                    $num5 += $rxLine['originPriceGST'] * $rxLine['quantity'];
                    $num6 += $rxLine['costPriceToClinic'] * $rxLine['quantity'];
                } else {
                    $num3 += $rxLine['agentServiceFee'] + $rxLine['platformServiceFee'];
                    $num4 += $rxLine['listPrice'] - $rxLine['doctorServiceFee'];
                }

            }
            $v['rx']['serviceFeeTotal'] = $num3;

            if (isset($v['rx']['medicineGrossMarginGst'])) {
                $num2 = round($v['rx']['medicineGrossMarginGst'], 2) - $num1;
            }

            // gmedsServiceFee
            $num = round($num, 2);
            $num1 = round($num1, 2);
            if (isset($params['isTaxInvoice']) ) {
                $gServiceFee = array('lineType' => 100, 'values' => array($num1, $num2, $num1 + $num2));
            } else {
                $gServiceFee = array('lineType' => 100, 'values' => array(0, $num, -$num));
            }

            $lastRxline = end($v['presDetail']);
            if ($lastRxline['lineType'] == 2) {
                array_splice($v['presDetail'], count($v['presDetail']) - 1 , 0, array($gServiceFee));
            } else {
                array_splice($v['presDetail'], count($v['presDetail']) , 0, array($gServiceFee));
            }
            /**
             * end of inserting gmeds service fee
             */

            $timestamp = $v['rx']['paidOn']->getTimestamp();
            if (!isset($v['rx']['refundedOn']) || (date('m', $timestamp) == $params['month'] && date('Y', $timestamp) == $params['year'])) {
                if ((int)$v['shippingCountryId'] != $psObj['operationsCountryId']) {
                  $tmp['overSea'][] = $v;
                } else {
                  $tmp['local'][] = $v;
                }
            }
     
            // Adjustment
            if (isset($v['rx']['refundedOn'])) {
                $timestamp = $v['rx']['refundedOn']->getTimestamp();
                if (date('m', $timestamp) == $params['month'] && date('Y', $timestamp) == $params['year']) {
                    if ((int)$v['shippingCountryId'] != $psObj['operationsCountryId']) {
                      $tmp['ajm']['overSea'][] = $v;
                    } else {
                      $tmp['ajm']['local'][] = $v;
                    }
                    
                }
            }

            
            if ($num6) {
                $totalAmount += $num6;
            } else {
                $totalAmount += $num1;
            }
            if (isset($params['isTaxInvoice']) && $params['isTaxInvoice']) {
                $totalAmount += $num2;
            }
            if (isset($params['isTaxInvoice'])) {
                if ($params['isTaxInvoice']) {
                    $totalAmount += $num5;
                } else {
                    $totalAmount += $num4;
                }
            }
            if ($num6) {
                $totalAmount += $num3;
                $totalAmount -= $num4;
            }
            if (isset($params['isTaxInvoice']) && $params['isTaxInvoice']) {
                if (isset($v['rx']['prescribingRevenueFeeGst'])) {
                    $totalAmount += $v['rx']['prescribingRevenueFeeGst'];
                }
            }
            if (isset($params['isTaxInvoice'])) {
                if ($params['isTaxInvoice']) {
                    $totalAmount += $v['rx']['customCAF'][2];
                } else {
                    $totalAmount += $v['rx']['customCAF'][4];
                }
            }
            if (isset($params['isTaxInvoice'])) {
                if ($params['isTaxInvoice']) {
                    $totalAmount += $v['rx']['customsTax'][2];
                } else {
                    $totalAmount += $v['rx']['customsTax'][0];
                }
            }
            if (isset($params['isTaxInvoice'])) {
                if ($params['isTaxInvoice']) {
                    $totalAmount += $v['rx']['shippingList'][2];
                } else {
                    $totalAmount += $v['rx']['shippingList'][0];
                }
            }
            if (isset($params['isTaxInvoice'])) {
                if ($params['isTaxInvoice']) {
                    $totalAmount += $v['rx']['paymentGatewayFeeBankMdr'][2];
                } else {
                    $totalAmount += $v['rx']['paymentGatewayFeeBankMdr'][0];
                }
            }
            if (isset($params['isTaxInvoice'])) {
                if ($params['isTaxInvoice']) {
                    $totalAmount += $v['rx']['paymentGatewayFeeVariable'][2];
                } else {
                    $totalAmount += $v['rx']['paymentGatewayFeeVariable'][0];
                }
            }
            if (isset($params['isTaxInvoice'])) {
                if ($params['isTaxInvoice']) {
                    $totalAmount += $v['rx']['paymentGatewayFeeFixed'][2];
                } else {
                    $totalAmount += $v['rx']['paymentGatewayFeeFixed'][0];
                }
            }
            if (isset($params['isTaxInvoice'])) {
                if ($params['isTaxInvoice']) {
                    $totalAmount += isset($v['rx']['igPermitFee'][2]) ? $v['rx']['igPermitFee'][2] : 0;
                } else {
                    $totalAmount += isset($v['rx']['igPermitFee'][0]) ? $v['rx']['igPermitFee'][0] : 0;
                }
            }
            if (isset($params['isTaxInvoice']) && !$params['isTaxInvoice']) {
                $totalAmount += $v['rx']['paymentGatewayFeeBankGst'][2];
            }
            if (isset($params['isTaxInvoice']) && !$params['isTaxInvoice']) {
                $totalAmount += $v['rx']['paymentGatewayFeeFixedGst'][2];
            }
          }
          $tmp['totalAmount'] = $totalAmount;
          $data = $tmp;
          $data['currencyCode'] = $psObj['currencyCode'];
        }

        

        return $data;
    }


    /**
     * get Transaction History Report by Doctor
     * @param  array $params
     * @author  thu.tranq
     * @return
     */
    public function getTransactionHistoryReport($params) {
        $drugType = Constant::RX_LINE_TYPE_DRUG;
        $serviceType = Constant::RX_LINE_TYPE_SERVICE;

        // get platform setting repository
        $psRepository = $this
            ->getEntityManager()
            ->getRepository('UtilBundle:PlatformSettings');
        $psObj = $psRepository->getPlatFormSetting();

        // find all order from rx table by $doctorId
        $queryBuilder = $this->createQueryBuilder('r');
        $selectStr = " r.orderNumber as orderNumber,
                       r.paidOn as paidOn,
                       r.id as rxId,
                       pi.id as perInfoId,
                       r.shippingList as shippingFee,
                       r.customsTax as customsTax,
                       ifelse(pi.firstName is null, pi.lastName, CONCAT(pi.firstName, ' ', pi.lastName)) AS fullName,
                       p.patientCode as patientCode,
                       r.orderValue as totalFee,
                       ( r.doctorMedicineFee + r.doctorServiceFee + r.feeGst + r.customsClearanceDoctorFee - IFELSE(r.paymentGatewayFeeBankGst IS NULL, 0, r.paymentGatewayFeeBankGst) - IFELSE(r.paymentGatewayFeeVariable IS NULL, 0, r.paymentGatewayFeeVariable) - IFELSE(r.paymentGatewayFeeFixed IS NULL, 0, r.paymentGatewayFeeFixed) as doctorFee,
                        r.feeGst as GST,
                        d.rxReviewFee,
                        -r.paymentGatewayFeeBankMdr as paymentGatewayFeeBankMdr,
                        -(r.paymentGatewayFeeBankGst - r.paymentGatewayFeeBankMdr) as paymentGatewayFeeBankGst,
                        -r.paymentGatewayFeeVariable as paymentGatewayFeeVariable,
                        -r.paymentGatewayFeeFixed as paymentGatewayFeeFixed,
                        r.customsClearanceDoctorFee,
                        r.customsClearancePlatformFee,
                        c.code countryCode,
                        c.id shippingCountryId,
                        pl.payMethod,
                        r.igPermitFee,
                        r.paymentGate,
                        r.gstRate,
                        r.taxIncome, r.taxIncomeWithoutTax, r.taxVat, r.taxImportDuty
                       ";

        $queryBuilder->select($selectStr)
                            ->innerJoin('UtilBundle:RxLine', 'rl', 'WITH', 'rl.rx = r.id')
                            ->innerJoin('UtilBundle:Drug', 'dr', 'WITH', 'dr.id = rl.drug')
                            ->innerJoin('r.doctor', 'd')
                            ->innerJoin('r.patient', 'p')
                            ->innerJoin('p.personalInformation', 'pi')
                            ->innerJoin('p.personalInformation', 'dpi')
                            ->leftJoin('r.shippingAddress', 'a')
                            ->innerJoin('a.city', 'ci')
                            ->innerJoin('ci.country', 'c')
                            ->leftJoin('UtilBundle:RxPaymentLog', 'pl', 'WITH', 'pl.orderRef = r.orderNumber')
                            ->where('r.doctor = :doctorId and r.deletedOn is null')
                            ->andWhere('r.paidOn is not null')
                            ->andWhere('r.deletedOn is null')
                            // STRIKE-1153, STRIKE-1164
                            ->andWhere('r.status <> ' . Constant::RX_STATUS_DEAD)
                            ->andWhere('r.status <> ' . Constant::RX_STATUS_FAILED)
                            ->andWhere('r.status <> ' . Constant::RX_STATUS_PAYMENT_FAILED)
                            // End STRIKE-1153, STRIKE-1164
                            ->setParameter('doctorId', $params['doctorId']);

        // filter by patient type: local or oversea
        if ( isset($params['patientType']) and !empty($params['patientType'])) {
            if ((int)$params['patientType'] == Constant::AREA_TYPE_LOCAL) {
                $queryBuilder->andWhere("p.primaryResidenceCountry = :operationsCountryId")
                             ->setParameter('operationsCountryId', $psObj['operationsCountryId']);
            } else if ((int)$params['patientType'] == Constant::AREA_TYPE_OVERSEA) {
                $queryBuilder->andWhere("p.primaryResidenceCountry != :operationsCountryId")
                         ->setParameter('operationsCountryId', $psObj['operationsCountryId']);
            }
        }
        $queryBuilder->groupBy('rl.rx');

        //filter by date
        if(isset($params['fromDate']) && !empty($params['fromDate'])){
            $startDate = new \DateTime($params['fromDate']);
            $endDate = new \DateTime($params['toDate']);
            $endDate->modify('tomorrow');
            $endDate->modify('1 second ago');
            $queryBuilder
                ->andWhere('r.paidOn <= :end AND r.paidOn >= :start')
                ->setParameter('start', $startDate->format("Y-m-d H:i:s"))
                ->setParameter('end', $endDate->format("Y-m-d H:i:s"));
        }

        // filter by doctor feed
        $doctorFeeGte = isset($params['doctorFeeGte']) ? $params['doctorFeeGte'] : '';
        $doctorFeeLte = isset($params['doctorFeeLte']) ? $params['doctorFeeLte'] : '';

        if (!empty($doctorFeeGte) && !empty($doctorFeeLte)) {
            $queryBuilder->having('doctorFee >= :doctorFeeGte')
                        ->setParameter('doctorFeeGte', $doctorFeeGte)
                        ->andHaving('doctorFee <= :doctorFeeLte')
                        ->setParameter('doctorFeeLte', $doctorFeeLte);
        } elseif (!empty($doctorFeeGte) && empty($doctorFeeLte)) {
            $queryBuilder->having('doctorFee >= :doctorFeeGte')
                        ->setParameter('doctorFeeGte', $doctorFeeGte);
        } elseif (empty($doctorFeeGte) && !empty($doctorFeeLte)) {
            $queryBuilder->having('doctorFee <= :doctorFeeLte')
                        ->setParameter('doctorFeeLte', $doctorFeeLte);
        }

        //search on patient: code, name
        if(isset($params['term']) && !empty($params['term'])) {
            $term = trim(strtolower($params['term']));

            $searchIn = $queryBuilder->expr()->like(
                            $queryBuilder->expr()->concat('pi.firstName', $queryBuilder->expr()->concat($queryBuilder->expr()->literal(' '), 'pi.lastName')),
                            $queryBuilder->expr()->literal( '%' . $term . '%')
                        );

            $queryBuilder
                ->andWhere($searchIn ." OR LOWER(p.patientCode) LIKE :term")
                ->setParameter('term', '%' . $term . '%');
        }

        if(isset($params['orderTerm']) && !empty($params['orderTerm'])) {
            $term = trim(strtolower($params['orderTerm']));

            $literal = $queryBuilder->expr()->literal("%$term%");
            $exp  = $queryBuilder->expr()->like('r.orderNumber', $literal);
            $exp1 = $queryBuilder->expr()->like('r.orderPhysicalNumber', $literal);
            $queryBuilder->andWhere($queryBuilder->expr()->orX($exp, $exp1));
        }

        // header fields sorting
        if (isset($params['sortInfo']) and !empty($params['sortInfo'])) {
            $queryBuilder->orderBy($params['sortInfo']['column'], $params['sortInfo']['direction']);
        }


        if (isset($params['isCsv'])) {
            return array('data' => $queryBuilder->getQuery()->getArrayResult());
        }

        $perPage = ($params['perPage'] > 0)? $params['perPage']: Constant::PER_PAGE_DEFAULT;
        $page = ($params['page'] > 0)? $params['page']: Constant::PAGE_DEFAULT;
        $startRecord = $perPage*$page;

        $totalResult = count($queryBuilder->getQuery()->getArrayResult());

        $queryBuilder
            ->setFirstResult($startRecord)
            ->setMaxResults($perPage);


         $result = $queryBuilder->getQuery()->getArrayResult();

        return array(
            'totalResult' => $totalResult,
            'totalPages' => ceil($totalResult/$perPage),
            'data' => $result
        );

    }

    /**
     * get monthly statement report
     * @param  array $params
     * @author  thu.tranq
     * @return
     */
    public function getMonthlyStatementReport($params) {
        $drugType            = Constant::RX_LINE_TYPE_DRUG;
        $serviceType         = Constant::RX_LINE_TYPE_SERVICE;
        $doctorType          = Constant::USER_TYPE_DOCTOR;
        $refundStatusSuccess = Constant::REFUND_STATUS_SUCCESS;
        $refundType          = Constant::PAYMENT_TYPE_REFUND;

        $queryBuilder = $this->createQueryBuilder('r');
        $selectStr = " STR_TO_DATE(CONCAT(YEAR(r.paidOn), '-', MONTH(r.paidOn), '-', '01'), '%Y-%c-%e %r' ) as monthly,
                       SUM(ifelse(pl.status IS NULL OR pl.status != '{$refundStatusSuccess}', r.orderValue , 0) ) AS totalFee,
                       SUM(ifelse( pl.status IS NULL OR pl.status != '{$refundStatusSuccess}',
                                    (r.doctorMedicineFee + r.doctorServiceFee + r.feeGst -
                                    IFELSE(r.paymentGatewayFeeBankGst IS NULL, 0, r.paymentGatewayFeeBankGst) -
                                    IFELSE(r.paymentGatewayFeeVariable IS NULL, 0, r.paymentGatewayFeeVariable) -
                                    IFELSE(r.paymentGatewayFeeFixed IS NULL, 0, r.paymentGatewayFeeFixed) +
                                    r.customsClearanceDoctorFee)
                                    , 0
                                )
                        ) AS doctorFee,
                        ps.status,
                        ps.datePaid as datePaid
                       ";

        $queryBuilder->select($selectStr)
                            ->leftJoin('UtilBundle:PaymentStatus', 'ps', 'WITH', "ps.userId = r.doctor AND ps.userType = {$doctorType} AND MONTH(r.paidOn) = MONTH(ps.datePaid)")
                            ->leftJoin('UtilBundle:RxPaymentLog', 'pl', 'WITH', "r.id = pl.rx and  pl.status = '{$refundStatusSuccess}' and pl.paymentType = '{$refundType}'")
                            ->where('r.doctor = :doctorId and r.deletedOn is null and r.paidOn is not null')
                            ->andWhere($queryBuilder->expr()->notIn('r.status',[Constant::RX_STATUS_FAILED, Constant::RX_STATUS_PAYMENT_FAILED , Constant::RX_STATUS_DEAD]))                            
                            ->setParameter('doctorId', $params['doctorId']);

        //filter by date
        if(isset($params['fromDate']) && isset($params['toDate']) && !empty($params['fromDate']) && !empty($params['toDate'])){
            $startDate = new \DateTime($params['fromDate']);
            $startDate->modify('first day of this month');
            $startDate->modify('midnight');

            $endDate = new \DateTime($params['toDate']);
            $endDate->modify('last day of this month');
            $endDate->modify('tomorrow');
            $endDate->modify('1 second ago');

        } elseif (isset($params['fromDate']) && !empty($params['fromDate'])){
            $startDate = new \DateTime($params['fromDate']);
            $startDate->modify('first day of this month');
            $startDate->modify('midnight');

            $endDate = new \DateTime();
            $endDate->modify('last day of previous month');
            $endDate->modify('tomorrow');
            $endDate->modify('1 second ago');

        } elseif (isset($params['toDate']) && !empty($params['toDate'])){

            $startDate = new \DateTime();
            $startDate->modify('first day of Jan');
            $startDate->modify('midnight');

            $endDate = new \DateTime($params['toDate']);
            $endDate->modify('last day of this month');
            $endDate->modify('tomorrow');
            $endDate->modify('1 second ago');
            // dump($params['toDate'], $endDate);die;
        } else {
            $startDate = new \DateTime();
            $startDate->modify('first day of Jan');
            $startDate->modify('midnight');

            $endDate = new \DateTime();
            $endDate->modify('last day of previous month'); // this is the right line code
            //$endDate->modify('last day of this month'); // this line for testing purpose
            $endDate->modify('tomorrow');
            $endDate->modify('1 second ago');
        }

        // get statement date
        $em                  = $this->getEntityManager();
        $platformSetting     = $em->getRepository('UtilBundle:PlatformSettings')->getPlatFormSetting();
        $statementDateNumber = $platformSetting['doctorStatementDate'];
        if ((int)date('d') <= $statementDateNumber) {
            $endDate->modify('last day of previous month');
        }

        $queryBuilder
            ->andWhere('r.paidOn <= :end AND r.paidOn >= :start')
            ->setParameter('start', $startDate->format("Y-m-d H:i:s"))
            ->setParameter('end', $endDate->format("Y-m-d H:i:s"));
        $queryBuilder->groupBy("monthly");


        // header fields sorting
        if (isset($params['sortInfo']) and !empty($params['sortInfo'])) {
            $queryBuilder->orderBy($params['sortInfo']['column'], $params['sortInfo']['direction']);
        }

        $perPage = ($params['perPage'] > 0)? $params['perPage']: Constant::PER_PAGE_DEFAULT;
        $page = ($params['page'] > 0)? $params['page']: Constant::PAGE_DEFAULT;
        $startRecord = $perPage*$page;

        $totalResult = count($queryBuilder->getQuery()->getArrayResult());

        $queryBuilder
            ->setFirstResult($startRecord)
            ->setMaxResults($perPage);


        $result = $queryBuilder->getQuery()->getArrayResult();
        return array(
            'totalResult' => $totalResult,
            'totalPages' => ceil($totalResult/$perPage),
            'data' => $result
        );

    }

    /**
     * Get rx lines
     * @param $orderNumber
     * @author Luyen Nguyen
     */
    public function getRxLines($orderNumber, $paid = false, $inStatus = '') {
        $queryBuilder = $this->createQueryBuilder('f');
        if ($paid) {
            $where = 'f.orderNumber = (:orderNumber) AND f.status = '
                    . Constant::RX_STATUS_CONFIRMED . ' AND f.isOnHold IN (0,2)';
        } else {
            //STRIKE-487: Payment failures workflow
            if ($inStatus == '') {
                $inStatus = Constant::RX_STATUS_PENDING.','.Constant::RX_STATUS_PAYMENT_FAILED;
            } else {
                $status = '';
                if (strpos($inStatus, ',')) {
                    $statuses = explode(',', $inStatus);
                    foreach($statuses as $item) {
                        $status .= is_numeric($item) ? $item . ',' : '';
                    }
                    $inStatus = rtrim($status, ',');
                } else {
                    if (!is_numeric($inStatus)) {
                        return null;
                    }
                }
            }
            $where = 'f.orderNumber = (:orderNumber)
                AND f.status IN ('. $inStatus .')
                AND f.isOnHold IN (0,2)';
        }
        $queryBuilder->select('rl')
                ->innerJoin('UtilBundle:RxLine', 'rl', 'WITH', 'rl.rx = f.id')
                ->where($where)
                ->setParameter('orderNumber', $orderNumber);
        $results = $queryBuilder->getQuery()->getResult();
        return $results;
    }

    public function getForDeclareForm($orderNumber)
    {
        $queryBuilder = $this->createQueryBuilder('f');
        $queryBuilder->select('rl')
                ->innerJoin('UtilBundle:RxLine', 'rl', 'WITH', 'rl.rx = f.id')
                ->where('f.orderNumber = (:orderNumber)')
                ->andWhere("f.status > :status")
                ->setParameter('status', Constant::RX_STATUS_PENDING)
                ->setParameter('orderNumber', $orderNumber);
        $results = $queryBuilder->getQuery()->getResult();
        return $results;
    }

    /**
     * Get rx
     * @param type $orderNumber
     * @author Luyen Nguyen
     * @return type
     */
    public function getRxByOrderNumber($orderNumber) {
        //STRIKE-487: Payment failures workflow
        $inStatus = Constant::RX_STATUS_PENDING.','. Constant::RX_STATUS_PAYMENT_FAILED;
        $queryBuilder = $this->createQueryBuilder('f');
        $queryBuilder->select('f')
                ->where('f.orderNumber = (:orderNumber)
                    AND f.status IN ('. $inStatus .')
                    AND f.isOnHold IN (0,2)')
                ->setParameter('orderNumber', $orderNumber);
        $result = $queryBuilder->getQuery()->getOneOrNullResult();
        return $result;
    }

    /*
     * count rx redispense
     * author Bien
     */

    public function countRedispense($id){
        $queryBuilder =  $this->createQueryBuilder('f')
            ->select('count(d.id)')
            ->innerJoin('f.resolves','r' )
            ->innerJoin('r.resolveRedispenses','d')
            ->where('f.id = :id' )
            ->andWhere('f.deletedOn is null')
            ->andWhere('r.status = '.Constant::RESOVLVE_STATUS_ACTIVE)
            ->setParameter('id', $id);
        $result = $queryBuilder->getQuery()->getSingleScalarResult();
        return $result;
    }

    /**
     * @author Tien Nguyen
     */
    public function getListPatientOfDoctor($params)
    {
        $doctorId = isset($params['doctorId']) ? $params['doctorId'] : '';
        $query = isset($params['query']) ? $params['query'] : '';

        if (!$doctorId || !$query) {
            return array();
        }

        $response = $this->getEntityManager()
            ->getRepository("UtilBundle\Entity\Patient")
            ->createQueryBuilder('p')
            ->innerJoin('p.personalInformation', 'info')
            ->where('p.doctor = :doctor')
            ->andWhere('info.firstName LIKE :query OR info.lastName LIKE :query')
            ->andWhere('p.deletedOn IS NULL')
            ->setParameter('doctor', $doctorId)
            ->setParameter('query', "%$query%")
            ->getQuery()
            ->getResult();

        $baseUrl = isset($params['baseUrl']) ? $params['baseUrl'] : '';

        $result = array();
        foreach ($response as $value) {
            if (null == $value) {
                continue;
            }

            $temp = array();
            $temp['url'] = $baseUrl . '/' . Common::encodeHex($value->getId());

            $info = $value->getPersonalInformation();
            if ($info) {
                $text = $info->getFirstName() . ' ' .
                    $info->getLastName();
            }
            $temp['text'] = isset($text) ? $text : '';

            $result[] = $temp;
        }

        return $result;
    }

    /**
     * @author Tien Nguyen
     */
    public function getListDateTab($params)
    {
        $doctorId    = isset($params['doctorId']) ? $params['doctorId'] : '';
        $patientId   = isset($params['patientId']) ? $params['patientId'] : '';
        $arrExStatus = array(Constant::RX_STATUS_DEAD, Constant::RX_STATUS_DRAFT);

        $queryBuilder = $this->createQueryBuilder('rx')
            ->select('rx, DATE(rx.createdOn) AS created_on')
            ->where('rx.doctor = :doctorId')
            ->andWhere('rx.patient = :patientId')
            ->andWhere('rx.deletedOn IS NULL')
            ->andWhere('rx.status NOT IN (:arrStatus)')
            ->orderBy('created_on', 'desc')
            ->groupBy('created_on')
            ->setParameter('doctorId', $doctorId)
            ->setParameter('patientId', $patientId)
            ->setParameter('arrStatus', $arrExStatus)
            ->setMaxResults(5);

        $result = $queryBuilder->getQuery()->getResult();

        $data = array();
        foreach ($result as $value) {
            $rx = $value[0];
            $temp = array();
            $temp['id'] = $rx->getId();
            $temp['date'] = date_format($rx->getCreatedOn(), 'd M y');
            $data[] = $temp;
        }

        return $data;
    }

    /**
     * @author Tien Nguyen
     */
    public function getRXDrug($params)
    {
        $rxId = isset($params['rxId']) ? $params['rxId'] : 0;
        $rx = $this->getEntityManager()->getRepository('UtilBundle:Rx')->find($rxId);
        $showDrugsFromEdit = isset($params['showDrugsFromEdit']) ? $params['showDrugsFromEdit'] : false;
        if ($showDrugsFromEdit) {
            $queryBuilder = $this->getEntityManager()->getRepository("UtilBundle:RxLine")
                ->createQueryBuilder('rxl')
                ->select('rxl, SUM(rxl.quantity) AS qty, SUM(rxl.listPrice) AS price')
                ->innerJoin('rxl.rx', 'rx')
                ->where('rxl.createdOn BETWEEN :createdOnStart AND :createdOnEnd')
                ->andWhere('rx.patient=:patientId')
                ->andWhere('rxl.lineType=:type')
                ->andWhere('rx.id=:rxId')
                ->groupBy('rxl.drug')
                ->setParameter('createdOnStart', $rx->getCreatedOn()->format('Y-m-d') . ' 00:00:00')
                ->setParameter('createdOnEnd', $rx->getCreatedOn()->format('Y-m-d') . ' 23:59:59')
                ->setParameter('patientId', $rx->getPatient()->getId())
                ->setParameter('type', Constant::RX_LINE_TYPE_DRUG)
                ->setParameter('rxId', $rx->getId());
        } else {
            $arrExStatus = array(Constant::RX_STATUS_DEAD, Constant::RX_STATUS_DRAFT);
            $queryBuilder = $this->getEntityManager()->getRepository("UtilBundle:RxLine")
                ->createQueryBuilder('rxl')
                ->select('rxl, SUM(rxl.quantity) AS qty, SUM(rxl.listPrice) AS price')
                ->innerJoin('rxl.rx', 'rx')
                ->where('rxl.createdOn BETWEEN :createdOnStart AND :createdOnEnd')
                ->andWhere('rx.patient=:patientId')
                ->andWhere('rxl.lineType=:type')
                ->andWhere('rx.status NOT IN (:arrStatus)')
                ->groupBy('rxl.drug')
                ->setParameter('createdOnStart', $rx->getCreatedOn()->format('Y-m-d') . ' 00:00:00')
                ->setParameter('createdOnEnd', $rx->getCreatedOn()->format('Y-m-d') . ' 23:59:59')
                ->setParameter('patientId', $rx->getPatient()->getId())
                ->setParameter('type', Constant::RX_LINE_TYPE_DRUG)
                ->setParameter('arrStatus', $arrExStatus);
        }

        if (isset($params['sorting']) && $params['sorting']) {
            list($sort, $order) = explode('-', $params['sorting']);
            $queryBuilder->orderBy($sort, $order);
        }

        $list = $queryBuilder->getQuery()->getResult();

        $result = array();
        foreach ($list as $value) {
            $rxLine = $value[0];
            $drugObj = $rxLine->getDrug();
            $temp = $this->formatDrugData($drugObj);
            $temp['id'] = $rxLine->getId();
            $temp['drugId'] = $drugObj->getId();

            $rxDrugData = $this->formatRXDrugData($rxLine);
            $temp = $temp + $rxDrugData;
            $temp['quantity'] = $value['qty'];
            $temp['price'] = $value['price'];
            $temp['doctorMargin'] = $rxLine->getDoctorMedicineFee();
            $temp['rxLineAmendment'] = $rxLine->getRxLineAmendment();

            $temp['costToClinic'] = $drugObj->getCostPriceToClinic();
            if (empty($params['isLocalPatient'])) {
                $temp['costToClinic'] = $drugObj->getCostPriceToClinicOversea();
            }

            $result[] = $temp;
        }

        return $result;
    }

    /**
     * @author Tien Nguyen
     */
    public function isExistsPatient($params)
    {
        $doctorId  = isset($params['doctorId']) ? $params['doctorId'] : 0;
        $patientId = isset($params['patientId']) ? $params['patientId'] : 0;

        $criteria = array(
            'id' => $patientId,
            'doctor' => $doctorId
        );

        $patientObj = $this->getEntityManager()->getRepository("UtilBundle\Entity\Patient")
            ->findOneBy($criteria);

        return $patientObj;
    }

    /**
     * @author Tien Nguyen
     */
    public function getDrug($params)
    {
        $query = isset($params['query']) ? $params['query'] : '';
        $doctorId = isset($params['doctorId']) ? $params['doctorId'] : 0;
        $stockStatus = array('Recalled');

        $queryBuilder = $this->getEntityManager()->getRepository("UtilBundle:Drug")
            ->createQueryBuilder('d')
            ->select('d', 'fav.id', 'st.name')
            ->leftJoin('UtilBundle:DoctorMedicalFavourite', 'fav', 'WITH', 'fav.drug=d AND fav.doctor=:doctorId')
            ->leftJoin('d.stockStatus', 'st')
            ->leftJoin('UtilBundle:DrugActiveIngredient', 'dai', 'WITH', 'dai.drug=d')
            ->leftJoin('dai.activeIngredient', 'ai')
            ->where('d.name LIKE :query OR ai.name LIKE :query')
            ->andWhere('d.deletedOn IS NULL')
            ->andWhere('st.name NOT IN (:status)')
            ->andWhere('d.deletedOn IS NULL')
            ->setParameter('query', "%$query%")
            ->setParameter('doctorId', $doctorId)
            ->setParameter('status', $stockStatus, Connection::PARAM_STR_ARRAY);

        if (isset($params['sorting']) && $params['sorting']) {
            list($sort, $order) = explode('-', $params['sorting']);
            $queryBuilder->orderBy($sort, $order);
        }

        $list = $queryBuilder->getQuery()->getResult();

        $result = array();
        foreach ($list as $value) {
            if (null == $value) {
                continue;
            }

            $drug = isset($value[0]) ? $value[0] : array();
            $temp = $this->formatDrugData($drug);
            $temp['id']    = $drug->getId();
            $temp['favId'] = $value['id'];
            $temp['stockStatus'] = $value['name'];

            $params['drug'] = $drug;
            $temp['price'] = $this->getDrugPriceByPatient($params);
            $temp['doctorMargin'] = $this->getDrugPriceByPatient($params, true);

            $temp['costToClinic'] = $drug->getCostPriceToClinic();
            if (empty($params['isLocalPatient'])) {
                $temp['costToClinic'] = $drug->getCostPriceToClinicOversea();
            }

            $result[] = $temp;
        }

        return $result;
    }

    /**
     * @author Tien Nguyen
     */
    public function getListAction()
    {
        $list = $this->getEntityManager()->getRepository("UtilBundle\Entity\DosageAction")
            ->findAll();

        $result = array();
        foreach ($list as $value) {
            $temp = array();
            $temp['id'] = $value->getId();
            $temp['name'] = $value->getName();
            $result[] = $temp;
        }

        return $result;
    }

    /**
     * @author Tien Nguyen
     */
    public function getListDoseUnit()
    {
        $list = $this->getEntityManager()->getRepository("UtilBundle:DosageForm")
            ->findAll();

        $result = array();
        foreach ($list as $value) {
            $temp = array();
            $temp['id'] = $value->getId();
            $temp['name'] = $value->getName();
            $temp['pluralName'] = $value->getPluralName();
            $result[] = $temp;
        }

        return $result;
    }

    /**
     * @author Tien Nguyen
     */
    public function getListDurationUnit()
    {
        $list = $this->getEntityManager()->getRepository("UtilBundle\Entity\DurationUnit")
            ->findAll();

        $result = array();
        foreach ($list as $value) {
            $temp = array();
            $temp['id'] = $value->getId();
            $temp['name'] = $value->getName();
            $result[] = $temp;
        }

        return $result;
    }

    /**
     * @author Tien Nguyen
     */
    public function getStep2Data($params)
    {
        $rxDrugIds = isset($params['rxDrugIds']) ? $params['rxDrugIds'] : 0;
        $drugIds = isset($params['drugIds']) ? $params['drugIds'] : 0;

        $rxDrugIds = array_map('trim', explode(',', $rxDrugIds));
        $drugIds = array_map('trim', explode(',', $drugIds));

        $result = array();

        $list = $this->getEntityManager()->getRepository("UtilBundle\Entity\RxLine")
            ->createQueryBuilder('rxD')
            ->where('rxD.id IN (:ids)')
            ->setParameter('ids', $rxDrugIds, Connection::PARAM_STR_ARRAY)
            ->getQuery()
            ->getResult();

        $wasteDrugs = array();
        foreach ($list as $value) {
            $temp = array();
            $drug = $value->getDrug();
            $temp = $this->formatDrugData($drug);

            $temp['id'] = $drug->getId();
            $temp['rxLineId'] = $value->getId();
            $temp['quantity'] = $value->getQuantity();
            $temp['action'] = $value->getDosageAction();
            $temp['dose'] = $value->getDosageQuantity();
            $temp['dosageForm'] = $value->getDosageForm();
            $temp['frequency'] = $value->getFrequencyQuantity();
            $temp['frequencyUnit'] = $value->getFrequencyDurationUnit();
            $temp['isTakenAsNeeded'] = $value->getIsTakenAsNeeded();
            $temp['isTakenWithFood'] = json_decode($value->getIsTakenWithFood());
            $temp['specialInstructions'] = $value->getSpecialInstructions();
            $temp['canCauseDrowsiness'] = $value->getCanCauseDrowsiness();
            $temp['isToCompleteCourse'] = $value->getIsToCompleteCourse();

            $params['drug'] = $drug;
            $temp['price'] = $this->getDrugPriceByPatient($params);
            $temp['doctorMargin'] = $this->getDrugPriceByPatient($params, true);

            $temp['costToClinic'] = $drug->getCostPriceToClinic();
            if (empty($params['isLocalPatient'])) {
                $temp['costToClinic'] = $drug->getCostPriceToClinicOversea();
            }

            $result[] = $temp;
            $wasteDrugs[] = $drug->getId();
        }

        $drugIds = array_diff($drugIds, $wasteDrugs);
        $list = $this->getEntityManager()->getRepository("UtilBundle\Entity\Drug")
            ->createQueryBuilder('d')
            ->where('d.id IN (:ids)')
            ->setParameter('ids', $drugIds, Connection::PARAM_STR_ARRAY)
            ->getQuery()
            ->getResult();

        foreach ($list as $value) {
            $temp = $this->formatDrugData($value);

            $temp['id'] = $value->getId();
            $temp['rxLineId'] = 0;
            $temp['quantity'] = 1;
            $temp['action'] = '';
            $temp['dose'] = '';
            $temp['dosageForm'] = '';
            $temp['frequency'] = '';
            $temp['frequencyUnit'] = '';
            $temp['isTakenAsNeeded'] = 0;
            $temp['isTakenWithFood'] = array();
            $temp['specialInstructions'] = '';
            $temp['canCauseDrowsiness'] = 0;
            $temp['isToCompleteCourse'] = 0;

            $params['drug'] = $value;
            $temp['price'] = $this->getDrugPriceByPatient($params);
            $temp['doctorMargin'] = $this->getDrugPriceByPatient($params, true);

            $temp['costToClinic'] = $value->getCostPriceToClinic();
            if (empty($params['isLocalPatient'])) {
                $temp['costToClinic'] = $value->getCostPriceToClinicOversea();
            }

            $result[] = $temp;
        }

        return $result;
    }
    /**
     * update rx when cc post issue
     * @author Bien Anh
     */
    public function UpdateRX($params, $messageTemplate = null)
    {
        $rxId = isset($params['rxId']) ? $params['rxId'] : 0;

        $rx = $this->find($rxId);
        $doctorPi = $rx->getDoctor()->getPersonalInformation();
        $response = array(
            'success' => true,
            'data'    => '',
            'message' => '',
        );

        $drugs = isset($params['drugs']) ? $params['drugs'] : array();
        $lines = $rx->getRxLines();
        $messages = [];
        foreach ($drugs as $key => $value) {

            $rxLine = '';
            $drugName = '';
            $message = [];
            foreach ($lines as $line){
                if(empty($line->getDrug())){
                    continue;
                }
                if($line->getDrug()->getId() == $key){
                    $rxLine = $line;
                    $drugName = $rxLine->getDrug()->getName();
                    break;
                }

            }
            if(empty($rxLine)){
                continue;
            }
            $message['old'] = Common::generateSIGPreview($rxLine,$this->getEntityManager());
            if($rxLine->getCanCauseDrowsiness()){
                $message['old'] .='<br/>May cause drowsiness. If affected, do not drive or operate machinery. Avoid alcohol';
            }
            if($rxLine->getIsToCompleteCourse()){
                $message['old'] .='<br/>Complete this course of medicine';
            }
            $message['old'] .='<br/><span class="txt-green-new">Special instruction: '.$rxLine->getSpecialInstructions() . '</span>';
            $this->updateRXDrug($value,$rxLine);
            $message['new'] = Common::generateSIGPreview($rxLine,$this->getEntityManager());
            if($rxLine->getCanCauseDrowsiness()){
                $message['new'] .='<br/>May cause drowsiness. If affected, do not drive or operate machinery. Avoid alcohol';
            }
            if($rxLine->getIsToCompleteCourse()){
                $message['new'] .='<br/>Complete this course of medicine';
            }
            $message['new'] .='<br/><span class="txt-green-new">Special instruction: '.$rxLine->getSpecialInstructions() . '</span>';
            $messages[$rxLine->getName()] = $message;

            $issue = new Issue();
            $doctorName = trim($doctorPi->getTitle() . ' ' . $doctorPi->getFirstName() . ' ' . $doctorPi->getLastName());
            $remarks = str_replace('[doctorName]', $doctorName, $messageTemplate);
            $remarks = str_replace('[drugName]', $drugName, $remarks);
            $remarks = str_replace('[oldInstruction]', $message['old'], $remarks);
            $remarks = str_replace('[newInstruction]', $message['new'], $remarks);
            $issue->setRemarks($remarks);
            $issue->setIssueType(Constant::DOCTOR_ROLE);
            $issue->setCreatedOn(new \DateTime('now'));
            $issue->setUpdatedOn(new \DateTime('now'));
            $issue->setIsResolution(false);
            $issue->setUpdatedBy($doctorName);
            $issue->setCreatedBy($doctorName);
            $rx->addIssue($issue);
        }
        $this->getEntityManager()->persist($rx);
        $this->getEntityManager()->flush();
        $response['data'] = $rx;
        $response['message'] = $messages;
        return $response;
    }

    /**
     * @author Bien
     */
    private function updateRXDrug($params,$rxDrug)
    {

        $action = isset($params['action']) ? $params['action'] : '';
        if ('others' == $action && !empty($params['otherAction'])) {
            $action = $params['otherAction'];
        }

        $dose = isset($params['dose']) ? $params['dose'] : '';
        if ('others' == $dose && !empty($params['otherDose'])) {
            $dose = $params['otherDose'];
        }

        $doseUnit = isset($params['doseUnit']) ? $params['doseUnit'] : '';
        if ('others' == $doseUnit && !empty($params['otherDoseUnit'])) {
            $doseUnit = $params['otherDoseUnit'];
        }

        $frequency = isset($params['frequency']) ? $params['frequency'] : '';
        if ('others' == $frequency && !empty($params['otherFrequency'])) {
            $frequency = $params['otherFrequency'];
        }

        $frequencyDuration = isset($params['frequencyDuration']) ? $params['frequencyDuration'] : '';
        if ('others' == $frequencyDuration && !empty($params['otherDurationUnit'])) {
            $frequencyDuration = $params['otherDurationUnit'];
        }

        $prn = isset($params['prn']) ? $params['prn'] : 0;
        $withMeal = isset($params['withMeal']) ? $params['withMeal'] : array();
        $specialInstructions = isset($params['specialInstructions']) ? $params['specialInstructions'] : '';

        $canCauseDrowsiness = isset($params['causeDrowsiness']) ? $params['causeDrowsiness'] : 0;
        $completeThisCourse = isset($params['completeThisCourse']) ? $params['completeThisCourse'] : 0;

        $rxDrug->setDosageAction($action);
        $rxDrug->setDosageQuantity($dose);
        $rxDrug->setDosageForm($doseUnit);
        $rxDrug->setFrequencyQuantity($frequency);
        $rxDrug->setFrequencyDurationUnit($frequencyDuration);
        $rxDrug->setIsTakenWithFood(json_encode($withMeal));
        $rxDrug->setIsTakenAsNeeded($prn);
        $rxDrug->setIsToCompleteCourse($completeThisCourse);
        $rxDrug->setCanCauseDrowsiness($canCauseDrowsiness);
        $rxDrug->setSpecialInstructions($specialInstructions);
    }


    /**
     * @author Tien Nguyen
     */
    public function createRX($params)
    {
        $doctorId = isset($params['doctorId']) ? $params['doctorId'] : 0;
        $patientId = isset($params['patientId']) ? $params['patientId'] : 0;
        $status = isset($params['status']) ? $params['status'] : 0;

        $doctorObj = $this->getEntityManager()->getRepository("UtilBundle:Doctor")->find($doctorId);
        $agentObj = null;
        if ($doctorObj) {
            $agentObj = $doctorObj->getAgentDoctors()->last();
            $agentObj = $agentObj->getAgent();
        }
        $patientObj = $this->getEntityManager()->getRepository("UtilBundle:Patient")->find($patientId);

        $rxId = isset($params['rxId']) ? $params['rxId'] : 0;
        if ($rxId) {
            $rx = $this->find($rxId);
            $rx->setUpdatedOn(new \DateTime());
            $orderNumber = $rx->getOrderNumber();
        } else {
            $rx = new Rx();
            $orderNumber = $this->generateOrderNumber($params);

            $proformaInvoiceNo = isset($params['proformaInvoiceNo'])? $params['proformaInvoiceNo']: $this->generateProformaInvoiceNo($params);
            $rx->setProformaInvoiceNo($proformaInvoiceNo);
            $rx->setCreatedOn(new \DateTime());
        }

        $rx->setDoctor($doctorObj);
        $rx->setAgent($agentObj);
        $rx->setPatient($patientObj);
        $rx->setStatus($status);
        $rx->setOrderSuffix('');
        $rx->setPatientNumber($patientObj->getPatientCode());
        $rx->setIsCareGiverAuthorised($patientObj->getUseCaregiver());

        $hasReviewFee = isset($params['chargeRXReviewFee']) ? $params['chargeRXReviewFee'] : true;
        $rx->setHasRxReviewFee($hasReviewFee);

        $rx->setShippingCost(0);
        $rx->setShippingCostGst(0);
        $rx->setCustomsTax(0);

        if (isset($params['lastUpdatedBy'])) {
            $rx->setLastUpdatedBy($params['lastUpdatedBy']);
        }

        if ( !isset($params['saveAsDraff']) ) { // do not update is_on_hold if come from save as draff
            if (isset($params['addIssue'])) {
                $rx->setIsOnHold(2); // change is_on_hold to 2 if sent to patient
            } else {
                $rx->setIsOnHold(0); // default set is_on_hold to 0 when edit a rx
            }
        }

        $rx->setOrderNumber($orderNumber);
        $rx->setOrderPhysicalNumber(str_replace('-', '', $orderNumber));

        $response = array(
            'success' => true,
            'data'    => '',
            'message' => '',
        );

        // STRIKE-318
        if (isset($params['parentId'])) {
            $parentRx = $this->find($params['parentId']);
            $rx->setParentRx($parentRx);

            $issue = new Issue();
            $remarks = 'The doctor has issued a replacement order for this RX. The new order ID is: ' . $orderNumber;
            $issue->setRemarks($remarks);
            $issue->setCreatedOn(new \DateTime('now'));
            $issue->setUpdatedOn(new \DateTime('now'));
            $issue->setIsResolution(true);

            $user = isset($params['displayName']) ? $params['displayName'] : '';
            $issue->setUpdatedBy($user);
            $issue->setCreatedBy($user);
            $parentRx->addIssue($issue);

            $this->getEntityManager()->persist($parentRx);

            $criteria = array(
                'type' => Constant::MSG_TYPE_REPLACEMENT_ORDER,
                'entityId' => $params['parentId']
            );
            $message = $this->getEntityManager()->getRepository('UtilBundle:Message')->findByCriteria($criteria);
            if ($message) {
                $message->setReadDate(new \DateTime());
                $this->getEntityManager()->persist($message);
            }
        }

        $settings = $this->getEntityManager()->getRepository('UtilBundle:PlatformSettings')
            ->getPlatFormSetting();

        $gstRate = $this->getEntityManager()
            ->getRepository('UtilBundle:PlatformSettings')
            ->getGstRate(true);

        $psp = array();
        $isLocalPatient = null;
        if (Constant::RX_STATUS_PENDING == $status) {
            $isLocalPatient = $this->isLocalPatient(array('patient' => $patientObj, 'settings' => $settings));
            $conditions = array(
                'isLocal' => $isLocalPatient,
                'types'   => array(Constant::MST_MEDICINE)
            );
            $psp = $this->getEntityManager()->getRepository('UtilBundle:PlatformSharePercentages')
                ->getDataForRX($conditions);

            $taxInvoiceNo = $this->generateTaxInvoiceNo($rx);
            $rx->setTaxInvoiceNo($taxInvoiceNo);
        }

        $drugPsp = array();
        if (isset($psp[Constant::MST_MEDICINE])) {
            $drugPsp = $psp[Constant::MST_MEDICINE];
            // Strike 673
            $rx->setDoctorMedicinePercentage($drugPsp->getDoctorPercentage());
            $rx->setPlatformMedicinePercentage($drugPsp->getPlatformPercentage());
        }

        $drugs = isset($params['drugs']) ? $params['drugs'] : array();
        $rxLines = $rx->getRxLines();
        $drugIds = array_keys($drugs);

        foreach ($rxLines as $value) {
            $drugObj = $value->getDrug();
            if (empty($drugObj)) {
                continue;
            }

            $drugId = $drugObj->getId();
            if (!in_array($drugId, $drugIds)) {
                $rxLines->removeElement($value);
            }
        }

        foreach ($drugs as $key => $value) {
            $value['rxObj']     = $rx;
            $value['drugId']    = $key;
            $value['doctorId']  = $doctorId;
            $value['patientId'] = $patientId;
            $value['psp']       = $drugPsp;
            $value['gstRate']   = $gstRate;
            $value['isLocalPatient'] = $isLocalPatient;

            $this->createRXDrug($value);
        }

        $params['rxObj'] = $rx;

        // Doctor fee
        if ($hasReviewFee) {
            $params['gstRate'] = $gstRate;
            $this->createRXService($params);
        } else {
            $this->deleteRXService($params);
        }

        // Of reprot
        $totalDMF = 0;
        $totalDSF = 0;
        $totalAMF = 0;
        $totalASF = 0;
        $totalPMF = 0;
        $totalPSF = 0;
        $cifValue = 0;
        $reviewFe = 0;
        $totalLin = 0;
        $temp = 0;
        $medicineValue = 0;
        foreach ($rx->getRxLines() as $value) {
            if ($value->getLineType() == Constant::RX_LINE_TYPE_ONE) {
                $temp += ($value->getListPrice() - ($value->getQuantity() * $value->getOriginPrice()));
                $medicineValue += $value->getListPrice(); // for Strike-712
            }
            $totalDMF += $value->getDoctorMedicineFee();
            $totalDSF += $value->getDoctorServiceFee();
            $totalAMF += $value->getAgentMedicineFee();
            $totalASF += $value->getAgentServiceFee();
            $totalPMF += $value->getPlatformMedicineFee();
            $totalPSF += $value->getPlatformServiceFee();
            if (Constant::RX_LINE_TYPE_DRUG == $value->getLineType()) {
                $cifValue += $value->getListPrice();
                $totalLin += ($value->getAgentMedicineFee() + $value->getPlatformMedicineFee());
            }
            if (Constant::RX_LINE_TYPE_SERVICE == $value->getLineType()) {
                $reviewFe += ($value->getAgentServiceFee() + $value->getPlatformServiceFee());
            }
        }

        $temp1 = round($temp*(100 - $rx->getDoctorMedicinePercentage())/100, 2);
        $rx->setDoctorMedicineFee($temp - $temp1);
        $rx->setDoctorServiceFee($totalDSF);

        $rx->setAgentMedicineFee($totalAMF);
        if (!empty($params['platformShareFlag'])) {
            $agent = $this->getEntityManager()
                ->getRepository('UtilBundle:AgentDoctor')
                ->findMasterAgent($doctorObj);
            $rx->setAgent($agent);

            $agentMedicineFee = $this->getEntityManager()
                ->getRepository('UtilBundle:Agent')
                ->getAgentFeeMedicine($agent, $rx);

            $_3pa = $doctorObj->get3pa();

                $minFeeAgent = $this->getEntityManager()
                    ->getRepository('UtilBundle:Agent')
                ->getAgentMiniumFee(
                    $agent,
                    $is3pa = false,
                    $isLocalPatient,
                    $patientObj->getPrimaryResidenceCountry()
                );

            foreach ($rx->getRxLines() as $value) {
                if (empty($value->getDrug())) {
                    continue;
                }

                if ($totalPMF <= $agentMedicineFee) {
                    $agentMedicineFee = 0;
                }

                if (isset($minFeeAgent) && $minFeeAgent > $totalPMF) {
                    $agentMedicineFee = 0;
                }

                $value->setAgentMedicineFee($agentMedicineFee);
                break;
            }
            $rx->setAgentMedicineFee($agentMedicineFee);

            if ($_3pa && $agentMedicineFee) {
                $platformMedicineFee = $totalPMF - $agentMedicineFee;

                $_3paMedicineFee = $this->getEntityManager()
                    ->getRepository('UtilBundle:Agent')
                    ->get3paFee($_3pa, Constant::GMS_FEE_3RD_AGENT_MEDICINE, $isLocalPatient);

                $minFee3pa = $this->getEntityManager()
                    ->getRepository('UtilBundle:Agent')
                    ->getAgentMiniumFee(
                        $_3pa,
                        $is3pa = true,
                        $isLocalPatient,
                        $patientObj->getPrimaryResidenceCountry()
                    );

                if ($platformMedicineFee <= $_3paMedicineFee) {
                    $_3paMedicineFee = 0;
                }

                if (isset($minFee3pa) && $minFee3pa > $platformMedicineFee) {
                    $_3paMedicineFee = 0;
                }

                $rx->setAgent3paMedicineFee($_3paMedicineFee);
            }

            if ($_3pa) {
                $_3paServiceFee = $this->getEntityManager()
                    ->getRepository('UtilBundle:Agent')
                    ->get3paFee($_3pa, Constant::GMS_FEE_3RD_AGENT_PRESCRIPTION, $isLocalPatient);
                $rx->setAgent3paServiceFee($_3paServiceFee);
                $rx->setSecondaryAgent($_3pa);
            }
        }
        $rx->setAgentServiceFee($totalASF);

        $rx->setPlatformMedicineFee($totalPMF);
        if (!empty($params['platformShareFlag'])) {
            $platformMedicineFee = $totalPMF - $agentMedicineFee;
            if (isset($_3paMedicineFee)) {
                $platformMedicineFee -= $_3paMedicineFee;
            }
            $rx->setPlatformMedicineFee($platformMedicineFee);
        }

        if (isset($_3paServiceFee)) {
            $totalPSF -= $_3paServiceFee;
        }
        $rx->setPlatformServiceFee($totalPSF);

        $rx->setCifValue($cifValue);

        // Of invoice to doctor
        if (Constant::RX_STATUS_PENDING == $status) {
            $shippingGSTCode = $this
                ->getEntityManager()
                ->getRepository('UtilBundle:Doctor')
                ->getShippingGSTCode($patientObj, $isLocalPatient);
            $rx->setToPatientShippingGstCode($shippingGSTCode);

            $pfGSTCode = $this->getEntityManager()->getRepository('UtilBundle:PlatformSettingGstCode')
                    ->getPlaformGSTCode();

            $hasGST = $this->getEntityManager()
                ->getRepository('UtilBundle:PlatformSettings')
                ->hasGST();

            if (isset($pfGSTCode[Constant::GM_SF]) && $hasGST) {
                $rx->setToDoctorShippingGstCode($pfGSTCode[Constant::GM_SF]);
            }

            $rx->setPaymentGatewayFeeBankMdrGstCode(Constant::GST_SRSGM);

            if (isset($pfGSTCode[Constant::GM_PGB_VARIABLE]) && $hasGST) {
                $rx->setPaymentGatewayFeeVariableGstCode($pfGSTCode[Constant::GM_PGB_VARIABLE]);
            }

            if (isset($pfGSTCode[Constant::GM_PGB_FIXED]) && $hasGST) {
                $rx->setPaymentGatewayFeeFixedGstCode($pfGSTCode[Constant::GM_PGB_FIXED]);
            }

            if (isset($pfGSTCode[Constant::GM_SGCIG_PF]) && $hasGST) {
                $rx->setIgPermitFeeGstCode($pfGSTCode[Constant::GM_SGCIG_PF]);
            }

            if (isset($pfGSTCode[Constant::GM_PFRS]) && $hasGST) {
                $rx->setPrescribingRevenueFeeGstCode($pfGSTCode[Constant::GM_PFRS]);
            }
            $gstValue = $reviewFe;
            if (TaxService::isCalGst($pfGSTCode[Constant::GM_PFRS])) {
                $gstValue = round($reviewFe * (1 + $gstRate/100), 2);
            }
            $rx->setPrescribingRevenueFeeGst($gstValue);

            if (isset($pfGSTCode[Constant::GM_MGMS]) && $hasGST) {
                $rx->setMedicineGrossMarginGstCode($pfGSTCode[Constant::GM_MGMS]);
            }
            $gstValue = $totalLin;
            if (TaxService::isCalGst($pfGSTCode[Constant::GM_MGMS])) {
                $gstValue = round($totalLin * (1 + $gstRate/100), 2);
            }
            $rx->setMedicineGrossMarginGst($gstValue);
        }

        $rx->setIsColdChain(0);
        if ($rx->isColdChain()) {
            $rx->setIsColdChain(1);
        }
        
        // get site
        $currentRxSite = $rx->getSite();
        if (!$currentRxSite) {
            $currentSite = Common::getCurrentSite($this->container);
            $site = $this->getEntityManager()->getRepository('UtilBundle:Site')->findOneBy(array('name' => $currentSite));
            if ($site) {
                $rx->setSite($site);
            }
        }

        if ($params['isScheduledRx']) {
            $rx->setIsScheduledRx(1);
            $rx->setScheduledSendDate(new \DateTime($params['scheduledSendDate']));
        } else {
            $rx->setIsScheduledRx(0);
        }

        try {
            $this->getEntityManager()->persist($rx);
            $this->getEntityManager()->flush();
            $response['data'] = $rx;

            // Rx refill remaider
            $this->createRXRefillRemaider($params);
        } catch (Exception $ex) {
            $response['message'] = $ex->getMessage();
            $response['success'] = false;
        }

        return $response;
    }

    /**
     * indonesiaTaxFormula, for #802
     * @param  object $rx
     */
    public function updateIndoTaxImport($rx) {
        $medicineValue = 0;
        foreach ($rx->getRxLines() as $value) {
            if ($value->getLineType() == Constant::RX_LINE_TYPE_ONE) {
                $medicineValue += $value->getListPrice(); // for Strike-712
            }
        }

        $em = $this->getEntityManager();
        $fxRate = $em->getRepository('UtilBundle:FxRate')
                      ->getRate(Constant::CURRENCY_SINGAPORE, Constant::CURRENCY_USD);
        $sgdToUsdRate = $fxRate->getRate();

        $indoTax = $em->getRepository('UtilBundle:IndonesiaTax')->getImportTaxs();

        $pm = $ppn = $pph = $pphWithTaxId = $pphWithoutTaxId = $freightCost = $insuranceVariable = 1;
        foreach ($indoTax as $val) {
            $taxName = $val['taxName'];
            $value = $val['taxValue'];
            if ($taxName == Constant::TAX_IMPORT_DUTY) {
                $pm = $value;
            } else if ($taxName == Constant::TAX_INCOME) {
                $pph = $value;
            } else if ($taxName == Constant::TAX_VAT) {
                $ppn = $value;
            } else if ($taxName == Constant::TAX_INCOME_WITHOUT_TAX_ID) {
                $pphWithoutTaxId = $value;
            } else if ($taxName == Constant::FREIGHT_COST) {
                $freightCost = $value;
            } else if ($taxName == Constant::INSURANCE_VARIABLE) {
                $insuranceVariable = $value;
            }
        }

        // from usd to sgd
        $fxRate = $em->getRepository('UtilBundle:FxRate')
                     ->getRate(Constant::CURRENCY_USD, Constant::CURRENCY_SINGAPORE);
        $usdToSgdRate = $fxRate->getRate();

        // Plat Form Settings
        $platformSettings = $em->getRepository('UtilBundle:PlatformSettings')->getPlatFormSetting();
        $bufferRate = $platformSettings['bufferRate'] / Constant::ONE_HUNDRE_NUMBER;

        $medicineValueUsdWithBuffer = ($medicineValue * $sgdToUsdRate) + ($medicineValue * $sgdToUsdRate * $bufferRate);

        $taxableMedicineValueInUsd  = ($medicineValueUsdWithBuffer + 2 * $freightCost) + (($medicineValueUsdWithBuffer + 2 * $freightCost) * $insuranceVariable);

        $taxableMedicineValueInSgd = $taxableMedicineValueInUsd * $usdToSgdRate;

        // 7.5% BM
        $importDuty = round($taxableMedicineValueInSgd * $pm/100, 2);

        // 10% PPN
        $taxVat = ($taxableMedicineValueInSgd + $importDuty) * $ppn/100;
        $taxVat = round($taxVat, 2);

        // 10% or 20% PPH
        $patient = $rx->getPatient();

        $taxIncome = $taxIncomeWithoutTaxId = null;
        if (!empty($patient->getTaxId())) { // have tax id
            $taxIncome  = ($taxableMedicineValueInSgd + $importDuty) * $pph/100 ;
            $taxIncome = round($taxIncome, 2);
        } else {
            $taxIncomeWithoutTaxId  = ($taxableMedicineValueInSgd + $importDuty) * $pphWithoutTaxId/100 ;
            $taxIncomeWithoutTaxId = round($taxIncomeWithoutTaxId, 2);

        }


        $rx->setTaxIncome($taxIncome);
        $rx->setTaxIncomeWithoutTax($taxIncomeWithoutTaxId);
        $rx->setTaxImportDuty($importDuty);
        $rx->setTaxVat($taxVat);

        $em->persist($rx);
        $em->flush();
    }

    /**
     * @author Tien Nguyen
     */
    public function getClinicInformation($params)
    {
        $doctorId = isset($params['doctorId']) ? $params['doctorId'] : 0;

        $list = $this->getEntityManager()->getRepository('UtilBundle\Entity\Clinic')
            ->findBy(array('doctor' => $doctorId));

        $result = array(
            'name' => '',
            'website' => '',
            'logo' => '',
            'address' => '',
            'phone' => '',
            'list' => array(),
            'isMY' => true
        );

        if (null == $list) {
            return $result;
        }

        foreach ($list as $clinic) {
            if ($clinic->getDeletedOn()) {
                continue;
            }

            $businessAddress = $clinic->getBusinessAddress();
            $address = $businessAddress->getAddress();
            $city    = $address->getCity();

            $arrAddress = array($address->getLine1());
            if ($address->getLine2()) {
                $arrAddress[] = $address->getLine2();
            }
            if ($address->getLine3()) {
                $arrAddress[] = $address->getLine3();
            }

            $arrAddress1 = array($address->getPostalCode());
            $arrAddress2 = array();
            if ($city->getCountry()->getId() != Constant::ID_SINGAPORE) {
                $arrAddress1[] = $city->getName();
                if ($city->getState()) {
                    $arrAddress2[] = $city->getState()->getName();
                }
            }
            $arrAddress2[] = $city->getCountry()->getName();

            $temp = array();
            $temp['name'] = $clinic->getBusinessName();
            $temp['website'] = $clinic->getWebsiteUrl();
            $temp['logo'] = $clinic->getBusinessLogoUrl();

            if ($city->getCountry()->getId() != Constant::ID_SINGAPORE) {
                $arrAddress3 = array(
                    implode(', ', $arrAddress),
                    implode(', ', $arrAddress1),
                    implode(', ', $arrAddress2)
                );
                $temp['address'] = implode("\n", $arrAddress3);
            } else {
                $arrAddress3 = array(
                    implode(', ', $arrAddress),
                    implode(', ', $arrAddress1)
                );
                $temp['address'] = implode("\n", $arrAddress3) . ', ' . $arrAddress2[0];
            }

            if ($clinic->getIsPrimary()) {
                $result = array_merge($result, $temp);
            } else {
                $result['list'][] = $temp;
            }
            }

        $settings = $this->getEntityManager()->getRepository('UtilBundle:PlatformSettings')
            ->getPlatFormSetting();

        $countryId = isset($settings['operationsCountryId']) ? $settings['operationsCountryId'] : 0;
        $country = $this->getEntityManager()->getRepository('UtilBundle:Country')->find($countryId);
        if ($country && Constant::SINGAPORE_CODE == $country->getCode()) {
            $result['isMY'] = false;
        }

        return $result;
    }

    /**
     * @author Tien Nguyen
     */
    public function getPatientInformation($params)
    {
        $patientId = isset($params['patientId']) ? $params['patientId'] : 0;

        $patientObj = $this->getEntityManager()->getRepository('UtilBundle\Entity\Patient')
            ->find($patientId);

        $result = array(
            'name'          => '',
            'patientCode'   => $patientObj->getPatientCode(),
            'globalId'      => '',
            'age'           => 0,
            'gender'        => '',
            'address'       => '',
            'allergies'     => '',
            'patientNumber' => '',
            'diagnosis'     => '',
            'taxId'         => $patientObj->getTaxId()
        );

        if (null == $patientObj) {
            return $result;
        }

        $personalInformation = $patientObj->getPersonalInformation();
        $name = $personalInformation->getFirstName() . ' '
                . $personalInformation->getLastName();
        $globalId = $patientObj->getPatientCode();

        $dateOfBirth = $personalInformation->getDateOfBirth();
        if (!$dateOfBirth) {
            $dateOfBirth = new \DateTime();
        } else {
            $result['dateOfBirth'] = $dateOfBirth;
        }
        $diff = date_diff(new \DateTime(), $dateOfBirth);
        $age = $diff->format('%y');
        $gender = $personalInformation->getGender();

        $countryId = $patientObj->getPrimaryResidenceCountry();
        $country = $this->getEntityManager()->getRepository('UtilBundle:Country')
            ->find($countryId);
        if ($country) {
            $result['address'] = $country->getName();
        }

        $result['name'] = $name;
        $result['globalId'] = $globalId;
        $result['age'] = $age;
        $result['gender'] = $gender ? 'Male' : 'Female';
        $result['patientNumber'] = $personalInformation->getPassportNo();

        $arrAller = array();
        $allergies = $patientObj->getAllergies();
        foreach ($allergies as $value) {
            $arrAller[] = $value->getMedicationAllergy();
        }
        $result['allergies'] = implode(', ', $arrAller);

        $arrDiag = array();
        $diagnosis = $patientObj->getDiagnosis();
        foreach ($diagnosis as $value) {
            if (strtolower($value->getDiagnosis()) == 'others') {
                $arrDiag[] = implode(', ', $params['otherDiagnosisValues']);
            } else {
                $arrDiag[] = $value->getDiagnosis();
            }
        }
        $result['diagnosis'] = implode(', ', $arrDiag);

        return $result;
    }


    /**
     * @author bien mai
     */
    public function getPatientInformationForPatientNote($params)
    {
        $patientId = isset($params['patientId']) ? $params['patientId'] : 0;

        $patientObj = $this->getEntityManager()->getRepository('UtilBundle\Entity\Patient')
            ->find($patientId);

        $result = array(
            'name'          => '',
            'patientCode'   => $patientObj->getPatientCode(),
            'globalId'      => '',
            'age'           => 0,
            'gender'        => '',
            'address'       => '',
            'allergies'     => '',
            'patientNumber' => '',
            'diagnosis'     => '',
            'taxId'         => $patientObj->getTaxId()
        );

        if (null == $patientObj) {
            return $result;
        }

        $personalInformation = $patientObj->getPersonalInformation();
        $name = $personalInformation->getFirstName() . ' '
            . $personalInformation->getLastName();
        $globalId = $patientObj->getPatientCode();

        $dateOfBirth = $personalInformation->getDateOfBirth();
        if (!$dateOfBirth) {
            $dateOfBirth = new \DateTime();
        } else {
            $result['dateOfBirth'] = $dateOfBirth->format("Y-m-d");
        }
        $diff = date_diff(new \DateTime(), $dateOfBirth);
        $age = $diff->format('%y');
        $gender = $personalInformation->getGender();

        $countryId = $patientObj->getPrimaryResidenceCountry();
        $country = $this->getEntityManager()->getRepository('UtilBundle:Country')
            ->find($countryId);
        if ($country) {
            $result['address'] = $country->getName();
        }

        $result['name'] = $name;
        $result['globalId'] = $globalId;
        $result['age'] = $age;
        $result['gender'] = $gender ? 'Male' : 'Female';
        $result['patientNumber'] = $personalInformation->getPassportNo();

        $arrAller = array();
        $allergies = $patientObj->getAllergies();
        foreach ($allergies as $value) {
            $arrAller[] = $value->getMedicationAllergy();
        }
        if(!empty($arrAller)) {
            $result['allergies'] = implode(', ', $arrAller);
        } else {
            $result['allergies'] = 'No known drug allergies';
        }

        $arrDiag = array();
        $diagnosis = $patientObj->getDiagnosis();
        foreach ($diagnosis as $value) {
            if (strtolower($value->getDiagnosis()) == 'others') {
                $arrDiag[] = implode(', ', $params['otherDiagnosisValues']);
            } else {
                $arrDiag[] = $value->getDiagnosis();
            }
        }
        $result['diagnosis'] = implode(', ', $arrDiag);

        return $result;
    }

    /**
     * @author Tien Nguyen
     */
    public function getDoctorInformation($params)
    {
        $doctorId = isset($params['doctorId']) ? $params['doctorId'] : 0;
        $doctor   = isset($params['doctor']) ? $params['doctor'] : array();
        $rx       = isset($params['rx']) ? $params['rx'] : array();

        if (!$doctor) {
            $doctor = $this->getEntityManager()->getRepository('UtilBundle\Entity\Doctor')
                ->find($doctorId);
        }

        $result = array(
            'name' => '',
            'license' => '',
            'phone' => '',
            'gstNo' => '',
            'signatureUrl' => '',
            'hasTax' => false,
            'hasReviewTax' => false
        );

        if (null == $doctor) {
            return $result;
        }

        $result['hasTax'] = $this->getEntityManager()
            ->getRepository('UtilBundle:Doctor')
            ->getDoctorGSTCode($doctor, true);

        if ($rx) {
            $result['hasReviewTax'] = $this->getEntityManager()
                ->getRepository('UtilBundle:Doctor')
                ->hasGST($rx, Constant::SETTING_GST_REVIEW);
        }

        $result['name'] = $doctor->showName();

        $license = $doctor->getMedicalLicense();
        if ($license) {
            $result['license'] = $license->getRegistrationNumber();
        }

        $result['signatureUrl'] = $doctor->getSignatureUrl();
        $result['gstNo'] = $doctor->getGstNo();

        $criteria = array(
            'doctor' => $doctorId
        );
        $doctorPhone = $this->getEntityManager()->getRepository('UtilBundle\Entity\DoctorPhone')
            ->findOneBy($criteria);

        if ($doctorPhone) {
            $phone   = $doctorPhone->getContact();
            $country = $phone->getCountry();

            $phoneNumber  = '+';
            $phoneNumber .= $country->getPhoneCode();
            if ($phone->getAreaCode()) {
                $phoneNumber .= " ". $phone->getAreaCode();
            }
            $phoneNumber .= " ". $phone->getNumber();
            $result['phone'] = $phoneNumber;
        }

        $result['doctorCode'] = $doctor->getDoctorCode();

        return $result;
    }

    /**
     * @author Tien Nguyen
     */
    public function getTop30($params)
    {
        $doctorId = isset($params['doctorId']) ? $params['doctorId'] : 0;
        $result   = array();

        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('rxl', 'count(rxl.drug) as total')
            ->from('UtilBundle:RxLine', 'rxl')
            ->innerJoin('rxl.rx', 'rx')
            ->innerJoin('rxl.drug', 'drug')
            ->where('rxl.lineType=:type')
            ->andWhere('rx.doctor=:doctorId')
            ->groupBy('rxl.drug')
            ->setMaxResults(30)
            ->setParameter('type', Constant::RX_LINE_TYPE_DRUG)
            ->setParameter('doctorId', $doctorId);

        if (isset($params['sorting']) && $params['sorting']) {
            list($sort, $order) = explode('-', $params['sorting']);
            $queryBuilder->orderBy($sort, $order);
            if ('total' == $sort) {
                $flag = true;
            }
        } else {
            $queryBuilder->orderBy('total', 'desc');
        }

        $list = $queryBuilder->getQuery()->getResult();

        $rank = count($list);
        if (isset($flag) && 'asc' == $order) {
            $rank  = 1;
            $flagR = true;
        }
        foreach ($list as $value) {
            $rxLine = $value[0];
            $drug   = $rxLine->getDrug();
            $temp   = $this->formatDrugData($drug);

            $params['drug'] = $drug;
            $price = $this->getDrugPriceByPatient($params);
            $temp['price'] = $price;
            $temp['rank'] = $rank;
            $temp['doctorMargin'] = $this->getDrugPriceByPatient($params, true);

            $temp['costToClinic'] = $drug->getCostPriceToClinic();
            if (empty($params['isLocalPatient'])) {
                $temp['costToClinic'] = $drug->getCostPriceToClinicOversea();
            }

            if (isset($flagR)) {
                $rank++;
            } else {
                $rank--;
            }

            $result[] = $temp;
        }

        return $result;
    }

    /**
     * @author Tien Nguyen
     */
    public function manageRXStatusLog($params)
    {
        $rxObj = isset($params['rxObj']) ? $params['rxObj'] : null;
        $logData = isset($params['logData']) ? $params['logData'] : null;
        $isPreviouslyOnHold = isset($params['isPreviouslyOnHold']) ? $params['isPreviouslyOnHold'] : null;
        if (empty($rxObj)) {
            return;
        }

        $doctorName = '';
        $doctorP = $rxObj->getDoctor()->getPersonalInformation();
        if ($doctorP) {
            $doctorName = $doctorP->getTitle() . " " . $doctorP->getFirstName() . ' ' . $doctorP->getLastName();
        }
        $patientName = '';
        $patientP = $rxObj->getPatient()->getPersonalInformation();
        if ($patientP) {
            $patientName = $patientP->getFirstName() . ' ' . $patientP->getLastName();
        }

        $orderNumber = $rxObj->getOrderNumber();
        $status = $rxObj->getStatus();
        $createdBy = $doctorName;

        $note = "Create RX $orderNumber for patient $patientName";
        if (isset($params['isEdit']) && $params['isEdit']) {
            $note = 'Update draft RX Order by doctor';
        }
        if (isset($params['isDelete']) && $params['isDelete']) {
            $note = 'Delete draft RX Order by doctor';
        }
        if (isset($params['isRecall']) && $params['isRecall']) {
            $createdOn = new \DateTime();
            $createdOn = $createdOn->format(Constant::GENERAL_DATE_FORMAT);

            if (isset($params['recallAction']) && $params['recallAction'] == 'edit') {
                $note = "Doctor $createdBy has recalled the prescription order on $createdOn";
            } else {
                $note = "Doctor $createdBy has recalled and cancelled the prescription order on $createdOn";
            }
        }
        if (isset($params['isConfirmed'])) {
            $note = 'RX Order sent by {doctor_name} to {patient_name}';
            $arrSearch  = array('{doctor_name}', '{patient_name}');
            $arrReplace = array($doctorName, $patientName);
            $note = str_replace($arrSearch, $arrReplace, $note);
        }
        if (isset($params['isRecalled'])) {
            $note = 'Set Recalled RX Order back to Draft by doctor.';
        }
        if (isset($params['isResend'])) {
            $note = 'Resent RX Order Failed to patient by doctor.';
        }
        if (isset($params['isPaymentSuccessful'])) {
            if ($params['isPaymentSuccessful']) {
                $status = '4';
                $note = 'RX Paid by the patient';
            } else {
                $status = '30';
                $note = 'RX Payment Failed by the patient';
            }
            $createdBy = $params['patientName'];
        }
        if (isset($params['isCron'])) {
            $note = 'Update rx status from crontab';
        }
        if (Constant::RX_STATUS_FOR_DOCTOR_REVIEW == $status) {
            if (isset($params['madeAmendment'])) {
                $note = "Make amendment and forward RX $orderNumber to $doctorName";
            } else {
                $note = "Forward RX $orderNumber to $doctorName";
            }
        }
        if (Constant::RX_STATUS_FOR_AMENDMENT == $status) {
            $note = "Request for amendment RX $orderNumber";
        }

        if (isset($params['createdBy'])) {
            $createdBy = $params['createdBy'];
        }

        $issueNotes = isset($params['note']) ? $params['note'] : '';

        $rxStatus = new RxStatusLog();
        $rxStatus->setRx($rxObj);
        $rxStatus->setStatus($status);
        $rxStatus->setNotes($note);
        $rxStatus->setIssueNotes($issueNotes);
        $rxStatus->setCreatedOn(new \DateTime());
        $rxStatus->setCreatedBy($createdBy);

        try {
            $this->getEntityManager()->persist($rxStatus);
            $this->getEntityManager()->flush();

            if ($isPreviouslyOnHold && $logData) {
                $rxStatusId = $rxStatus->getId();
                $log = new Log();
                $log->setEntityId($rxStatusId);
                $log->setTitle('update rx value');
                $log->setAction('update_rx');
                $log->setModule('updateRx');
                $log->setOldValue(json_encode($logData['old']));
                $log->setNewValue(json_encode($logData['new']));
                $log->setCreatedOn(new \DateTime());
                try {
                    $this->getEntityManager()->persist($log);
                    $this->getEntityManager()->flush();
                } catch (\Exception $exception) {

                }
            }

        } catch (Exception $ex) {
        }
    }

    /**
     * @author Tien Nguyen
     */
    public function formatDrugs($params)
    {
        $drugs = isset($params['drugs']) ? $params['drugs'] : array();

        $template = array(
            'name' => '',
            'sig' => '',
            'instructions' => '',
            'qty' => 0,
            'drowsiness' => '',
            'complete' => '',
            'unitPrice' => 0,
            'total' => 0,
            'currency' => 'MYR',
            'gstCode' => '',
            'ingredients' => '',
            'manufacturer' => '',
            'packQuantity' => 0,
            'doseUnit' => ''
        );

        $result = array();
        foreach ($drugs as $key => $value) {
            $drug = $this->getEntityManager()->getRepository('UtilBundle:Drug')
                ->find($key);
            if (null == $drug) {
                continue;
            }

            $drugData = $this->formatDrugData($drug);
            $template = $this->formatRXDrugData($value);
            $template = $template + $drugData;

            if (!$template['qty']) {
                continue;
            }

            $params['drug'] = $drug;
            $template['unitPrice'] = $this->getDrugPriceByPatient($params);
            $template['total'] = $template['unitPrice'] * $template['qty'];

            $result[] = $template;
        }

        return $result;
    }

    /**
     * @author Bien mai
     */
    public function formatDrugsPaid($params)
    {
        $drugs = isset($params['drugs']) ? $params['drugs'] : array();

        $template = array(
            'name' => '',
            'sig' => '',
            'instructions' => '',
            'qty' => 0,
            'drowsiness' => '',
            'complete' => '',
            'unitPrice' => 0,
            'total' => 0,
            'currency' => 'MYR',
            'gstCode' => '',
            'ingredients' => '',
            'manufacturer' => '',
            'packQuantity' => 0,
            'doseUnit' => ''
        );

        $result = array();
        foreach ($drugs as $key => $value) {
            $drug = $value->getDrug();
            if (null == $drug) {
                continue;
            }

            $drugData = $this->formatDrugData($drug);
            $template = $this->formatRXDrugData($value);
            $template = $template + $drugData;

            if (!$template['qty']) {
                continue;
            }

            $params['drug'] = $drug;
            $template['unitPrice'] = $value->getCostPrice();
            $template['total'] = $template['unitPrice'] * $template['qty'];

            $result[] = $template;
        }

        return $result;
    }

    /**
     * Get doctor review fee
     * @author Tien Nguyen
     */
    public function getDoctorFee($params)
    {
        $result = array(
            'unitPrice' => null,
            'gstCode' => '',
            'total' => 0,
            'adminServiceFee' => 0
        );

        $doctorId = isset($params['doctorId']) ? $params['doctorId'] : 0;
        $doctor   = $this->getEntityManager()->getRepository('UtilBundle:Doctor')
            ->find($doctorId);

        $rxId = isset($params['rxId']) ? $params['rxId'] : 0;

        if ($doctor) {
            $hasGST = $this->getEntityManager()
                ->getRepository('UtilBundle:Doctor')
                ->hasGST($rxId, Constant::SETTING_GST_REVIEW, $params);
            $result['gstCode'] = $hasGST ? Constant::GST_SRS : Constant::GST_ZRS;
        }

        if ($rxId) {
            $rxLine = $this->getEntityManager()->getRepository('UtilBundle:RxLine')
                ->findOneBy(array('rx' => $rxId, 'lineType' => Constant::RX_LINE_TYPE_SERVICE));
            $result['unitPrice'] = 0;
            if ($rxLine) {
                $result['unitPrice'] = $rxLine->getCostPrice();
            }
        }

        if (is_null($result['unitPrice']) && $doctor) {
            $result['unitPrice'] = $doctor->getRxReviewFeeInternational();
            if ($this->isLocalPatient($params)) {
                $result['unitPrice'] = $doctor->getRxReviewFeeLocal();
            }
        }

        $result['total'] = $result['unitPrice'];

        if(isset($params['rxReviewFee']) && $params['rxReviewFee'] >= 0) {
            $result['total'] = $result['unitPrice'] = $params['rxReviewFee'];
        }

        if (isset($params['subtotalServices'])) {
            $result['adminServiceFee'] = $params['subtotalServices'] - $result['unitPrice'];
            return $result;
        }

        $params = ['patientId' => $params['patientId']];
        $isLocalPatient = $this->isLocalPatient($params);

        $areaType = Constant::AREA_TYPE_OVERSEA;
        if ($isLocalPatient) {
            $areaType = Constant::AREA_TYPE_LOCAL;
        }
        $msType = Constant::MST_SERVICE;

        $criteria = [
            'areaType' => $areaType,
            'marginShareType' => $msType,
            'isActive' => true
        ];
        $psp = $this->getEntityManager()->getRepository('UtilBundle:PlatformShareFee')->findOneBy($criteria);
        $adminServiceFee = 0;
        if ($psp) {
            $adminServiceFee = $psp->getPlatformPercentage() + $psp->getAgentPercentage();
        }

        $result['adminServiceFee'] = $adminServiceFee;

        return $result;
    }

    /**
     * @author Tien Nguyen
     */
    public function getRXInformation($params)
    {
        $result = array(
            'id' => '',
            'date' => '',
            'orderNo' => '',
            'taxInvoiceNo' => '',
            'gstRegNo' => '',
            'estimateDelivery' => '',
            'refillDuration' => 0,
            'estimatedDate' => '',
            'hasRxReviewFee' => true,
            'shippingGSTCode' => Constant::GST_ZRS,
            'ccGSTCode' => Constant::GST_ZRS,
            'proformaInvoiceNo' => '',
            'status' => '',
            'receiptNo' => '',
            'confirmDate' => ''
        );

        $rx = isset($params['rx']) ? $params['rx'] : array();
        if (empty($rx) && isset($params['rxId']) && $params['rxId']) {
            $rx = $this->find($params['rxId']);
        }

        $date = date(Constant::GENERAL_DATE_FORMAT);
        if ($rx) {
            $date = $rx->getCreatedOn();
            $date = $date->format(Constant::GENERAL_DATE_FORMAT);

            $paidOn = $rx->getPaidOn();
            if ($paidOn) {
                $confirmDate = $paidOn->format(Constant::GENERAL_DATE_FORMAT);
                $result['confirmDate'] = $confirmDate;
            } else {
                $result['confirmDate'] = $date;
            }
        }
        $result['date'] = $date;

        if ($rx) {
            $result['taxInvoiceNo'] = $rx->getTaxInvoiceNo();
            $result['gstRegNo'] = $rx->getDoctor()->getGstNo();
        }

        if ($rx) {
            $rxRefillReminder = $this->getEntityManager()->getRepository('UtilBundle:RxRefillReminder')
                ->findOneBy(array('rx' => $rx->getId()));

            if ($rxRefillReminder) {
                $result['refillDuration'] = $rxRefillReminder->getRefillSupplyDuration();
            }
        }
        if (isset($params['refillReminder'])) {
            if ($params['refillReminder'] && isset($params['lengthOfSupply'])) {
                $result['refillDuration'] = $params['lengthOfSupply'];
            } else {
                $result['refillDuration'] = 0;
            }
        }

        if ($rx) {
            $result['hasRxReviewFee'] = $rx->getHasRxReviewFee();
        }
        if (isset($params['chargeRXReviewFee'])) {
            $result['hasRxReviewFee'] = true; //$params['chargeRXReviewFee'];
        }

        if (isset($params['proformaInvoiceNo'])) {
            $result['proformaInvoiceNo'] = $params['proformaInvoiceNo'];
        }

        if ($rx) {
            $result['orderNo'] = $rx->getOrderNumber();
            $result['proformaInvoiceNo'] = $rx->getProformaInvoiceNo();
        }
        if (isset($params['orderNumber'])) {
            $result['orderNo'] = $params['orderNumber'];
        }

        if ($rx) {
            $result['shippingGSTCode'] = $rx->getToPatientShippingGstCode();
            $result['ccGSTCode'] = $rx->getCustomsClearanceDoctorFeeGstCode();
        } else if (isset($params['isProforma'])) {
            $patient = $this->getEntityManager()
                ->getRepository('UtilBundle:Patient')
                ->find($params['patientId']);
            $result['shippingGSTCode'] = $this->getEntityManager()
                ->getRepository('UtilBundle:Doctor')
                ->getShippingGSTCode($patient);
        }

        if ($rx) {
            $result['status'] = $rx->getStatus();
            $result['receiptNo'] = $rx->getReceiptNo();
        }

        return $result;
    }

    /**
     * @author Tien Nguyen
     */
    public function getOrderTotal($params)
    {
        $result = array(
            'subTotalMedication' => 0,
            'subTotalService' => 0,
            'totalBeforeGST' => 0,
            'gst' => 0,
            'vat' => 0,
            'total' => 0,
        );

        $drugs = isset($params['drugs']) ? $params['drugs'] : array();
        foreach ($drugs as $value) {
            $unitPrice = isset($value['unitPrice']) ? $value['unitPrice'] : 0;
            $qty = isset($value['qty']) ? $value['qty'] : 0;
            $result['subTotalMedication'] += ($unitPrice * $qty);

            $total = isset($value['total']) ? $value['total'] : 0;
            $result['gst'] += $total;
        }

        $doctorFee = isset($params['doctorFee']) ? $params['doctorFee'] : array();
        $result['subTotalService'] = isset($doctorFee['unitPrice']) ? $doctorFee['unitPrice'] : 0;
        if (isset($doctorFee['adminServiceFee'])) {
            $result['subTotalService'] += $doctorFee['adminServiceFee'];
        }
        if (isset($doctorFee['total'])) {
            $result['gst'] += $doctorFee['total'];
        }

        $result['totalBeforeGST'] = $result['subTotalMedication'] + $result['subTotalService'];

        $result['gst'] = $result['gst'] - $result['subTotalMedication'] - $result['subTotalService'];
        $result['vat'] = 0;

        $result['total'] = $result['totalBeforeGST'] + $result['gst'] + $result['vat'];

        return $result;
    }

    /**
     * @author Tien Nguyen
     */
    public function getFavorites($params)
    {
        $doctorId = isset($params['doctorId']) ? $params['doctorId'] : 0;

        $queryBuilder = $this->getEntityManager()
            ->getRepository('UtilBundle:DoctorMedicalFavourite')
            ->createQueryBuilder('fav')
            ->innerJoin('fav.drug', 'drug')
            ->where('fav.doctor=:doctorId')
            ->setParameter('doctorId', $doctorId);

        if (isset($params['sorting']) && $params['sorting']) {
            list($sort, $order) = explode('-', $params['sorting']);
            $queryBuilder->orderBy($sort, $order);
        }

        $list = $queryBuilder->getQuery()->getResult();
        
        $result = array();
        foreach ($list as $value) {
            $drug = $value->getDrug();
            $temp = $this->formatDrugData($drug);

            $params['drug'] = $drug;
            $temp['price'] = $this->getDrugPriceByPatient($params);
            $temp['doctorMargin'] = $this->getDrugPriceByPatient($params, true);

            $temp['costToClinic'] = $drug->getCostPriceToClinic();
            if (empty($params['isLocalPatient'])) {
                $temp['costToClinic'] = $drug->getCostPriceToClinicOversea();
            }

            $result[] = $temp;
        }

        return $result;
    }

    /**
     * @author Tien Nguyen
     */
    public function handleFavoriteDrug($params)
    {
        $doctorId = isset($params['doctorId']) ? $params['doctorId'] : 0;
        $drugId   = isset($params['drugId']) ? $params['drugId'] : 0;

        $criteria = array(
            'doctor' => $doctorId,
            'drug' => $drugId
        );

        $favorite = $this->getEntityManager()->getRepository('UtilBundle:DoctorMedicalFavourite')
            ->findOneBy($criteria);

        $doctorDrug = $this->getEntityManager()->getRepository('UtilBundle:DoctorDrug')->findOneBy($criteria);

        $result = array(
            'succcess' => true
        );

        if ($favorite) {
            $result['isRemove'] = true;
            $this->getEntityManager()->remove($favorite);
            if ($doctorDrug) {
                $this->getEntityManager()->remove($doctorDrug);
            }
            $this->getEntityManager()->flush();
            return $result;
        } 
        // Get all favorite drugs
        $allFavDrugs = $this->getEntityManager()
            ->getRepository('UtilBundle:DoctorMedicalFavourite')
            ->createQueryBuilder('fav')
            ->innerJoin('fav.drug', 'drug')
            ->where('fav.doctor=:doctorId')
            ->setParameter('doctorId', $doctorId)->getQuery()->getResult();
        // Check the limit
        if (count($allFavDrugs) >= Constant::FAVORITE_DRUGS_LIMIT) {
            $result = array(
                'succcess'  => false,
                'errMsg'    => "Sorry, you have reached maximum " . Constant::FAVORITE_DRUGS_LIMIT . " favorite drugs."
            );
            return $result;
        }

        $doctor = $this->getEntityManager()->getRepository('UtilBundle:Doctor')
                ->find($doctorId);
        $drug = $this->getEntityManager()->getRepository('UtilBundle:Drug')
                ->find($drugId);

        $favorite = new DoctorMedicalFavourite();
        $favorite->setDoctor($doctor);
        $favorite->setDrug($drug);
        $favorite->setCreatedOn(new \DateTime());

        try {
            $this->getEntityManager()->persist($favorite);
            $this->getEntityManager()->flush();
        } catch(Exception $ex) {
        }

        return $result;
    }

    /**
     * @author Tien Nguyen
     */
    public function getListRX($params)
    {
        $doctorId = isset($params['doctorId']) ? $params['doctorId'] : 0;

        //get page
        $perPage = ($params['perPage'] > 0)? $params['perPage']: Constant::PER_PAGE_DEFAULT;
        $page = ($params['page'] > 0)? $params['page']: Constant::PAGE_DEFAULT;
        $startRecord = $perPage*$page;

        $arrGreen = array(
            // Constant::RX_STATUS_CONFIRMED,
            // Constant::RX_STATUS_APPROVED,
            // Constant::RX_STATUS_DISPENSED,
            Constant::RX_STATUS_DELIVERED,
            // Constant::RX_STATUS_COLLECTED,
            // Constant::RX_STATUS_TRANSFERRED
        );

        $arrRed = array(
            // Constant::RX_STATUS_PENDING,
            Constant::RX_STATUS_PAYMENT_FAILED,
            Constant::RX_STATUS_REVIEWING,
            // Constant::RX_STATUS_READY_FOR_COLLECTION,
            // Constant::RX_STATUS_DELIVERING,
            Constant::RX_STATUS_DELIVERY_FAILED,
            Constant::RX_STATUS_CANCELLED,
            Constant::RX_STATUS_REFUNDED,
            Constant::RX_STATUS_DAMAGED,
            Constant::RX_STATUS_RECALLED,
            Constant::RX_STATUS_PROCESSING_REFUND,
            Constant::RX_STATUS_ON_HOLD,
            Constant::RX_STATUS_FOR_DOCTOR_REVIEW,
        );

        $arrGray = array(
            // Constant::RX_STATUS_DRAFT,
            Constant::RX_STATUS_REJECTED,
            // Constant::RX_STATUS_ON_HOLD,
            // Constant::RX_STATUS_FOR_DOCTOR_REVIEW,
            Constant::RX_STATUS_FOR_AMENDMENT
        );

        $arrBlue = array(
            Constant::RX_STATUS_PENDING,
            Constant::RX_STATUS_CONFIRMED,
            Constant::RX_STATUS_APPROVED,
            Constant::RX_STATUS_DISPENSED,
            Constant::RX_STATUS_READY_FOR_COLLECTION,
            Constant::RX_STATUS_COLLECTED,
            Constant::RX_STATUS_DELIVERING,
            Constant::RX_STATUS_TRANSFERRED,
        );

        $arrYellow = array (
            Constant::RX_STATUS_DRAFT,
        );

        $availableStatusList = array_merge($arrGreen, $arrRed, $arrGray, $arrYellow, $arrBlue);

        if (isset($params['rxStatus']) && $params['rxStatus']) {
            $availableStatusList = $params['rxStatus'];
        }

        $queryBuilder = $this->createQueryBuilder('rx')
            ->select('rx', 'info.firstName', 'info.lastName','c.isDoctorRead as isDoctorRead')
            ->leftJoin('rx.rxCounter','c')
            ->innerJoin('rx.patient', 'p')
            ->innerJoin('p.personalInformation', 'info')
            ->where('rx.doctor = :doctor')
            ->andWhere('rx.deletedOn IS NULL')
            ->setParameter('doctor', $doctorId);

        if (isset($params['onHold']) && $params['onHold']) {
            $queryBuilder->addSelect('MAX(rl.createdOn) as reportDate');
            $queryBuilder->innerJoin('UtilBundle:RxStatusLog', 'rl', 'WITH', 'rl.rx=rx');
            $queryBuilder->andWhere('rx.isOnHold = true');

            if (isset($params['isReported']) && $params['isReported']) {
                $queryBuilder->andWhere('rx.status <> :status');
            } else {
                $queryBuilder->andWhere('rx.status = :status');
            }

            $queryBuilder->groupBy('rx.id');
            $queryBuilder->setParameter('status', Constant::RX_STATUS_PENDING);
        } else {
            $where = $queryBuilder->expr()->in('rx.status', $availableStatusList);
            $queryBuilder->andWhere($where);

            if (isset($params['isDeletedCancelled']) && $params['isDeletedCancelled']) {
                $queryBuilder->orWhere('rx.deletedOn IS NOT NULL AND rx.doctor = :doctor')->setParameter('doctor', $doctorId);
            } else {
                $queryBuilder->andWhere('rx.deletedOn IS NULL');
            }
        }

        if (isset($params['isPending']) && $params['isPending']) {
            $queryBuilder->andWhere('rx.isOnHold IN (0,2)');
        }

        if (isset($params['isConfirmed']) && $params['isConfirmed']) {
            $queryBuilder->addSelect('rxl.costPrice as doctorFee');
            $queryBuilder->leftJoin('UtilBundle:RxLine', 'rxl', 'WITH', 'rxl.rx=rx and rxl.lineType=' . Constant::RX_LINE_TYPE_SERVICE);
            $queryBuilder->groupBy('rx.id');
        }

        if (isset($params['isFailed']) && $params['isFailed']) {
            $queryBuilder->addSelect('MAX(rl.createdOn) as failedOn');
            $queryBuilder->leftJoin('UtilBundle:RxStatusLog', 'rl', 'WITH', 'rl.rx=rx and rl.status=' . Constant::RX_STATUS_PAYMENT_FAILED);
            $queryBuilder->groupBy('rx.id');
        }

        if (isset($params['isScheduled']) && $params['isScheduled']) {
            $queryBuilder->addSelect('rx.scheduledSendDate as sendDate');
            $queryBuilder->andWhere('rx.isScheduledRx = 1');
            $queryBuilder->andWhere('rx.scheduledSentOn IS NULL');
        }

        if (isset($params['sorting']) && $params['sorting']) {
            list($sort, $order) = explode('-', $params['sorting']);
            $queryBuilder->orderBy($sort, $order);
        } else {
            $queryBuilder->orderBy('rx.createdOn', 'desc');
        }

        if (isset($params['orderId']) && $params['orderId']) {
            $orderId = $params['orderId'];
            $literal = $queryBuilder->expr()->literal("%$orderId%");
            $exp  = $queryBuilder->expr()->like('rx.orderNumber', $literal);
            $exp1 = $queryBuilder->expr()->like('rx.orderPhysicalNumber', $literal);
            $queryBuilder->andWhere($queryBuilder->expr()->orX($exp, $exp1));
        }

        if (isset($params['patientName']) && $params['patientName']) {
            $or = $queryBuilder->expr()->orX();
            $patientName = $params['patientName'];
            $literal = $queryBuilder->expr()->literal("%$patientName%");
            $or->add($queryBuilder->expr()->like("CONCAT(info.firstName,' ',info.lastName)", $literal));
            // $or->add($queryBuilder->expr()->like('info.lastName', $literal));
            $or->add($queryBuilder->expr()->like('p.patientCode', $literal));
            $queryBuilder->andWhere($or);
        }

        if (isset($params['issueDate']) && $params['issueDate']) {
            $date = date_create_from_format(Constant::GENERAL_DATE_FORMAT, $params['issueDate']);
            $start = $date->setTime(0, 0, 0)->format('Y-m-d H:i:s');
            $end = $date->setTime(23, 59, 59)->format('Y-m-d H:i:s');

            $queryBuilder->andWhere('rx.createdOn BETWEEN :from AND :to');
            $queryBuilder->setParameter('from', $start);
            $queryBuilder->setParameter('to', $end);
        }

        $totalResult = count($queryBuilder->getQuery()->getResult());
        $queryBuilder->setFirstResult($startRecord)
            ->setMaxResults($perPage);

        return array(
            'totalResult' => $totalResult,
            'totalPages' => ceil($totalResult/$perPage),
            'data' => $queryBuilder->getQuery()->getResult(),
            'arrGreen' => $arrGreen,
            'arrGray' => $arrGray,
            'arrBlue' => $arrBlue,
            'arrYellow' => $arrYellow,
        );
    }

    /**
     * @author Tien Nguyen
     */
    public function getListActivitiesLog($params)
    {
        $rxId = isset($params['rxId']) ? $params['rxId'] : 0;

        $criteria = array(
            'rx' => $rxId
        );

        $activityLogs = $this->getEntityManager()->getRepository('UtilBundle:RxStatusLog')
            ->findBy($criteria);

        $result = array();
        foreach ($activityLogs as $log) {
            $rxStatusLog = array(
                'rx_status_log' => $log
            );
            $logDetail = $this->getEntityManager()->getRepository('UtilBundle:Log')->findOneBy(array('entityId' => $log->getId(), 'action' => 'update_rx'));
            if ($logDetail) {
                $oldValue = json_decode($logDetail->getOldValue(), true);
                $newValue = json_decode($logDetail->getNewValue(), true);
                $rxStatusLog['log_detail'] = array(
                    'old' => $oldValue,
                    'new' => $newValue
                );
            }

            $result[] = $rxStatusLog;
        }

        return $result;
    }

    /**
     * @author Tien Nguyen
     */
    public function getRXRefillReminder($params)
    {
        $rxId = isset($params['rxId']) ? $params['rxId'] : 0;

        $result = array(
            'lengthOfSupply' => 0,
            'refillId' => $rxId
        );

        $criteria = array(
            'rx' => $rxId
        );

        $data = $this->getEntityManager()->getRepository('UtilBundle:RxRefillReminder')
            ->findOneBy($criteria);

        if ($data) {
            $result['lengthOfSupply'] = $data->getRefillSupplyDuration();
        }

        return $result;
    }

    /**
     * @author Tien Nguyen
     */
    public function convertRXData($rxId)
    {
        $result = array(
            'drugs' => array(),
            'patientId' => 0,
            'rx' => array(),
            'rxId' => $rxId
        );

        $rx = $this->find($rxId);
        if ($rx) {
            $result['patientId'] = $rx->getPatient()->getId();
            $result['rx'] = $rx;
        }

        $criteria = array(
            'rx' => $rxId,
            'lineType' => Constant::RX_LINE_TYPE_DRUG
        );

        $rxDrugs = $this->getEntityManager()->getRepository('UtilBundle:RxLine')
            ->findBy($criteria);

        foreach ($rxDrugs as $value) {
            $drugId = $value->getDrug()->getId();
            $result['drugs'][$drugId] = $value;
        }

        return $result;
    }

    /**
     * @author bien mai
     */
    public function convertRXDataPaid($rxId)
    {
        $result = array(
            'drugs' => array(),
            'patientId' => 0,
            'rx' => array(),
            'rxId' => $rxId
        );

        $rx = $this->find($rxId);
        if ($rx) {
            $result['patientId'] = $rx->getPatient()->getId();
            $result['rx'] = $rx;
            $lines = $rx->getRxLines();
            foreach ($lines as $line) {
                if($line->getLineType() == Constant::RX_LINE_TYPE_DRUG) {
                    $drugId = $line->getDrug()->getId();
                    $result['drugs'][$drugId] = $line;
                }
            }
        }

        return $result;
    }

    /**
     * @author Tien Nguyen
     */
    public function generateOrderNumber($params)
    {
        $agent = null;
        $agentSite = null;
        $countryCode = null;
        $agentDoctor = $this->getEntityManager()->getRepository('UtilBundle:Doctor')->getDoctorActiveAgent($params['doctorId']);
        if ($agentDoctor) {
            $agent = $agentDoctor->getAgent();

            if ($agent->getParent()) {
                $agentSite = $agent->getParent()->getSite();
            } else {
                $agentSite = $agent->getSite();
            }
        }

        if ($agentSite) {
            if ($agentSite->getType() == Constant::SITE_PARKWAY_TYPE) {
                $settings = $this->getEntityManager()->getRepository('UtilBundle:PlatformSettings')
                    ->getPlatFormSetting();
                $countryId = isset($settings['operationsCountryId']) ? $settings['operationsCountryId'] : 0;

                $country = $this->getEntityManager()->getRepository('UtilBundle:Country')
                    ->find($countryId);
                if (null == $country) {
                    return '';
                }

                $countryCode = $country->getPhoneCode();
            } else {
                $countryCode = Constant::NON_PARKWAY_COUNTRY_PHONE_CODE;
            }
        }

        $patientId = isset($params['patientId']) ? $params['patientId'] : 0;
        $patient = $this->getEntityManager()->getRepository('UtilBundle:Patient')
            ->find($patientId);

        if (null == $patient) {
            return '';
        }

        $patientCode = 9;
        $params['settings'] = $settings;
        $params['patient']  = $patient;
        if ($this->isLocalPatient($params)) {
            $patientCode = 1;
        }

        $month = date('m');
        $year  = date('y');

        $latestOrderNumber = $this->getLatestOrderNumber();
        list(,,,$newNo) = explode('-', $latestOrderNumber);

        $jump  = isset($params['jump']) ? $params['jump'] : 1;
        $newNo = sprintf("%'.07d", $newNo + $jump);

        $arrCodes = array($countryCode, $patientCode, $month.$year, $newNo);

        $checkDigit = $this->generateCheckDigit(implode('', $arrCodes));

        $arrCodes[] = $checkDigit;

        $result = implode('-', $arrCodes);

        $rx = $this->findBy(array('orderNumber' => $result));
        if ($rx) {
            $params['jump'] = ++$jump;
            return $this->generateOrderNumber($params);
        }

        return $result;
    }

    /**
     * @author Tien Nguyen
     */
    public function replaceTemplateData(ContainerInterface $container, Rx $rx, RxReminderSetting $rxS, $string, $is_email = false, $is_reminder = true, $from_rx_create = false)
    {
        $arrData = array(
            '{doctor_name}',
            '{patient_name}',
            '{order_number}',
            '{expire_time_settings}',
            '{link}',
            '{confirm_prescription_button}',
            '{patient_tos_link}',
            '{patient_help_page_link}',
            '{clinic_name}',
            '{first_reminder_date}'
        );

        $router = $container->get('router');
        $personal = $rx->getPatient()->getPersonalInformation();
        $arrPName = array();
        if ($personal) {
            $arrPName[] = $personal->getFirstName();
            $arrPName[] = $personal->getLastName();
        }

        $patientName = implode(' ', $arrPName);
        if ($is_email) {
            $patientName = '<strong>' . $patientName . '</strong>';
        }

        $expiredTime = $rxS->getExpiredTime() . ' ' . $rxS->getTimeUnitExpire() . 's';

        $orderNumber = $rx->getOrderNumber();

        $rxSite = $rx->getSite();
        $scheduledRxDate = null;
        if ($rx->getIsScheduledRx()) {
            $scheduledRxDate = $rx->getScheduledSendDate();
            if ($scheduledRxDate) {
                $scheduledRxDate = $scheduledRxDate->format('d F Y');
            }

            $agentDoctor = $this->getEntityManager()->getRepository('UtilBundle:Doctor')->getDoctorActiveAgent($rx->getDoctor()->getId());
            if ($agentDoctor) {
                $agent = $agentDoctor->getAgent();

                if ($agent->getParent()) {
                    $rxSite = $agent->getParent()->getSite();
                } else {
                    $rxSite = $agent->getSite();
                }
            }
        }

        if ($rxSite) {
            $siteUrl = $router->getContext()->getScheme() . '://' . $rxSite->getUrl();
        } else {
            $siteUrl = $container->getParameter('base_url');
        }

        $link = $router->generate('prescription_index', array('orderNumber' => $orderNumber));
        $link = $siteUrl . $link;
        $emailLink = '<a style="border-color: #249987;background-color: #249987;border-radius: 0 5px;display: inline-block;height:32px;line-height: 32px;padding: 0 12px;text-transform: uppercase;text-decoration: none;color: #ffffff;margin: 10px 0 10px" href="' . $link . '"><font color="#ffffff" face="Arial, Helvetica, sans-serif" style="font-size: 12px">View and confirm your prescription</font></a>';
        if ($is_email & !$is_reminder) {
            $link = '<a href="'. $link .'">'. $link .'</a>';
        } else {
            $link = $is_email ? $emailLink : $link;
        }

        $clinicInformation = $this->getClinicInformation(array('doctorId' => $rx->getDoctor()->getId()));

        if ($from_rx_create) {
            $patientTosLink = '<a href="'. $siteUrl . $router->generate('patient_terms_of_use') .'" target="_blank">Patient Terms of Service</a>';
            $patientFAQLink = '<a href="'. $siteUrl . $router->generate('patient_faq') .'" target="_blank">G-MEDS Patient FAQ</a>';
        } else {
            $patientTosLink = '<a href="'. $siteUrl . $router->generate('patient_terms_of_use') .'" target="_blank">here</a>';
            $patientFAQLink = '<a href="'. $siteUrl . $router->generate('patient_faq') .'" target="_blank">Patient\'s Help Page</a>';
        }

        $arrRpl = array(
            $rx->getDoctor()->showName(),
            $patientName,
            $rx->getOrderNumber(),
            $expiredTime,
            $link,
            $emailLink,
            $patientTosLink,
            $patientFAQLink,
            $clinicInformation['name'],
            $scheduledRxDate,
        );

        $result = str_replace($arrData, $arrRpl, $string);

        return $result;
    }

    /**
     * @author Tien Nguyen
     */
    private function createRXDrug($params)
    {
        $em = $this->getEntityManager();

        $rxObj = isset($params['rxObj']) ? $params['rxObj'] : array();
        if (null == $rxObj) {
            return false;
        }

        $quantity = isset($params['quantity']) ? $params['quantity'] : 0;
        if (!$quantity) {
            return false;
        }

        $action = isset($params['action']) ? $params['action'] : '';
        if ('others' == $action && !empty($params['otherAction'])) {
            $action = $params['otherAction'];
        }

        $dose = isset($params['dose']) ? $params['dose'] : '';
        if ('others' == $dose && !empty($params['otherDose'])) {
            $dose = $params['otherDose'];
        }

        $doseUnit = isset($params['doseUnit']) ? $params['doseUnit'] : '';
        if ('others' == $doseUnit && !empty($params['otherDoseUnit'])) {
            $doseUnit = $params['otherDoseUnit'];
        }

        $frequency = isset($params['frequency']) ? $params['frequency'] : '';
        if ('others' == $frequency && !empty($params['otherFrequency'])) {
            $frequency = $params['otherFrequency'];
        }

        $frequencyDuration = isset($params['frequencyDuration']) ? $params['frequencyDuration'] : '';
        if ('others' == $frequencyDuration && !empty($params['otherDurationUnit'])) {
            $frequencyDuration = $params['otherDurationUnit'];
        }

        $prn = isset($params['prn']) ? $params['prn'] : 0;
        $withMeal = isset($params['withMeal']) ? $params['withMeal'] : array();
        $specialInstructions = isset($params['specialInstructions']) ? $params['specialInstructions'] : '';
        $canCauseDrowsiness = isset($params['causeDrowsiness']) ? $params['causeDrowsiness'] : 0;
        $completeThisCourse = isset($params['completeThisCourse']) ? $params['completeThisCourse'] : 0;

        $drugId  = isset($params['drugId']) ? $params['drugId'] : 0;
        $drugObj = $em->getRepository("UtilBundle:Drug")->find($drugId);
        if(!empty($drugObj->getStockStatus()) && $drugObj->getStockStatus()->getName() == "Stock when Ordered") {
            $rxObj->setIsSpecialIndent(1);
        }

        $criteria = array(
            'rx' => $rxObj->getId(),
            'drug' => $drugId
        );
        $rxDrug = $em->getRepository('UtilBundle:RxLine')
            ->findOneBy($criteria);

        if (null == $rxDrug) {
            $rxDrug = new RxLine();
            $isNew = true;
        }
        $rxDrug->setQuantity($quantity);
        $rxDrug->setDosageAction($action);
        $rxDrug->setDosageQuantity($dose);
        $rxDrug->setDosageForm($doseUnit);
        $rxDrug->setFrequencyQuantity($frequency);
        $rxDrug->setFrequencyDurationUnit($frequencyDuration);
        $rxDrug->setIsTakenWithFood(json_encode($withMeal));
        $rxDrug->setIsTakenAsNeeded($prn);
        $rxDrug->setIsToCompleteCourse($completeThisCourse);
        $rxDrug->setCanCauseDrowsiness($canCauseDrowsiness);
        $rxDrug->setSpecialInstructions($specialInstructions);
        $rxDrug->setRX($rxObj);
        $rxDrug->setDrug($drugObj);
        $rxDrug->setName($drugObj->getName());
        $rxDrug->setDescription($drugObj->getDescription());

        $params['drug'] = $drugObj;
        $costPrice = $this->getDrugPriceByPatient($params);
        $rxDrug->setCostPrice($costPrice);

        $listPrice = $costPrice * $quantity;
        $rxDrug->setListPrice($listPrice);

        $doctor  = $rxObj->getDoctor();
        $gstRate = isset($params['gstRate']) ? $params['gstRate'] : 0;
        $costPriceGst = $costPrice;
        $listPriceGst = $listPrice;

        $hasGST = $em->getRepository('UtilBundle:Doctor')->hasGST($rxObj, Constant::SETTING_GST_MEDICINE);
        $gstCode = $drugObj->getGstCode()->getCode();
        $arrAGst = array(Constant::GST_SRS, Constant::GST_SRSGM);
        if ($hasGST && in_array($gstCode, $arrAGst)) {
            $costPriceGst = $costPrice + $costPrice*$gstRate/100;
            $listPriceGst = $listPrice + $listPrice*$gstRate/100;
        }
        $rxDrug->setCostPriceGst($costPriceGst);
        $rxDrug->setListPriceGst($listPriceGst);

        $originPrice = $drugObj->getCostPrice();
        $rxDrug->setOriginPrice($originPrice);

        $rxDrug->setLineType(Constant::RX_LINE_TYPE_DRUG);
        $rxDrug->setCreatedOn($rxObj->getCreatedOn());
        $rxDrug->setUpdatedOn(new \DateTime());

        // Calculate data for report
        $platformShareFlag = $this->container->getParameter('platform_share_fee');
        $agentMF = $platformMF = $doctorMF = 0;
        if ($platformShareFlag) {
            $costPriceToClinic = $drugObj->getCostPriceToClinic();
            if (empty($params['isLocalPatient'])) {
                $costPriceToClinic = $drugObj->getCostPriceToClinicOversea();
            }
            $doctorMF = ($costPrice - $costPriceToClinic) * $quantity;

            $pharmacyCostPrice = $drugObj->getCostPrice();
            $platformMF = ($costPriceToClinic - $pharmacyCostPrice) * $quantity;

            $rxDrug->setCostPriceToClinic($costPriceToClinic);
        } else {
            $psp  = isset($params['psp']) ? $params['psp'] : array();
            $temp = $listPrice - $originPrice * $quantity;
            if ($psp) {
                $agentMF = round($temp * $psp->getAgentPercentage() / 100, 2);
                $platformMF = round($temp * $psp->getPlatformPercentage() / 100, 2);
                $doctorMF = $temp - round($temp * (100 - $psp->getDoctorPercentage())/100, 2);
            }
            $rxDrug->setAgentMedicineFee($agentMF);
        }

        $rxDrug->setPlatformMedicineFee($platformMF);
        $rxDrug->setDoctorMedicineFee($doctorMF);

        if (isset($isNew)) {
            $rxObj->addRxLine($rxDrug);
        }
    }

    /**
     * @author Tien Nguyen
     */
    private function createRXService($params)
    {
        $rxObj = isset($params['rxObj']) ? $params['rxObj'] : array();
        if (null == $rxObj) {
            return false;
        }

        $criteria = array (
            'rx' => $rxObj->getId(),
            'lineType' => Constant::RX_LINE_TYPE_SERVICE
        );
        $rxLine = $this->getEntityManager()->getRepository('UtilBundle\Entity\RxLine')
        ->findOneBy($criteria);

        if (null == $rxLine) {
            $rxLine = new RxLine();
            $isNew = true;
        }

        $rxLine->setRx($rxObj);
        $rxLine->setName('Doctor Fees (Prescription Review)');
        $rxLine->setDescription('');

        $doctor = $rxObj->getDoctor();
        $agent = $this->getEntityManager()
                ->getRepository('UtilBundle:AgentDoctor')
                ->findMasterAgent($doctor);

        $reviewFee = 0;
        if ($rxObj->getHasRxReviewFee()) {
            $reviewFee = $doctor->getRxReviewFeeInternational();
            if ($this->isLocalPatient($params)) {
                $reviewFee = $doctor->getRxReviewFeeLocal();
            }
        }

        //rxReviewFee
        if(isset($params['rxReviewFee']))
            $reviewFee = $params['rxReviewFee'];

        $rxLine->setCostPrice($reviewFee);
        $rxLine->setListPrice($reviewFee);
        $rxLine->setLineType(Constant::RX_LINE_TYPE_SERVICE);
        $rxLine->setCreatedOn(new \DateTime());
        $rxLine->setUpdatedOn(new \DateTime());

        // Calculate data for report
        $platformShareFlag = $this->container->getParameter('platform_share_fee');
        $em = $this->getEntityManager();
        if ($platformShareFlag) {
            $platformShare = $em->getRepository('UtilBundle:AgentPrimaryCustomFee')
                ->calculatePlatformShare($agent, $rxLine);
            $platformShare1 = $em->getRepository('UtilBundle:PlatformShareFee')->calculatePlatformShare($rxLine);
            $rxLine->setListPrice($platformShare['listPrice']);
        } else {
            $platformShare = $em->getRepository('UtilBundle:PlatformSharePercentages')->calculatePlatformShare($rxLine);
        }

        $agentSF = $platformShare['agentSF'];
        $platformSF = $platformShare['platformSF'];
        $doctorSF = $platformShare['doctorSF'];

        $rxLine->setAgentServiceFee($agentSF);
        $rxLine->setPlatformServiceFee($platformSF);
        $rxLine->setDoctorServiceFee($doctorSF);

        $gstRate = isset($params['gstRate']) ? $params['gstRate'] : 0;
        $reviewFeeGst = $rxLine->getCostPrice();
        $listPriceGst = $rxLine->getListPrice();

        $hasGST = $this->getEntityManager()
            ->getRepository('UtilBundle:Doctor')
            ->hasGST($rxObj, Constant::SETTING_GST_REVIEW);
        if ($hasGST) {
            $reviewFeeGst += round($reviewFeeGst*$gstRate/100, 2);
            $listPriceGst += round($listPriceGst*$gstRate/100, 2);
        }

        $rxLine->setCostPriceGst($reviewFeeGst);
        $rxLine->setListPriceGst($listPriceGst);

        if (isset($isNew)) {
            $rxObj->addRxLine($rxLine);
        }
    }

    /**
     * @author Tien Nguyen
     */
    private function deleteRXService($params)
    {
        $rxObj = isset($params['rxObj']) ? $params['rxObj'] : array();
        if (null == $rxObj) {
            return false;
        }

        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq("lineType", Constant::RX_LINE_TYPE_SERVICE));

        $rxService = $rxObj->getRxLines()->matching($criteria);

        foreach ($rxService as $value) {
            $rxObj->removeRxLine($value);
        }
    }

    /**
     * @author Tien Nguyen
     */
    private function createRXRefillRemaider($params)
    {
        $rxObj = isset($params['rxObj']) ? $params['rxObj'] : array();
        if (null == $rxObj) {
            return false;
        }

        if (!isset($params['refillReminder'])) {
            return false;
        }

        $rxRefillReminder = $this->getEntityManager()->getRepository('UtilBundle:RxRefillReminder')
            ->findOneBy(array('rx' => $rxObj->getId()));

        if (null == $rxRefillReminder) {
            $rxRefillReminder = new RxRefillReminder();
        }

        $length = 0;
        if ($params['refillReminder'] && isset($params['lengthOfSupply'])) {
            $length = $params['lengthOfSupply'];
        }
        
        if (!$length || $length == '') {
            $length = 0;
        }

        $rxRefillReminder->setRx($rxObj);
        $rxRefillReminder->setRefillSupplyDuration($length);
        $rxRefillReminder->setCreatedOn(new \DateTime());
        $rxRefillReminder->setUpdatedOn(new \DateTime());

        try {
            $this->getEntityManager()->persist($rxRefillReminder);
            $this->getEntityManager()->flush();
        } catch (Exception $ex) {
        }
    }

    /**
     * @author Tien Nguyen
     */
    private function getDrugPriceByPatient($params, $isDoctorMS = false)
    {
        $rxId = isset($params['rxId']) ? $params['rxId'] : null;
        $drug = isset($params['drug']) ? $params['drug'] : null;

        if (!$drug) {
            return 0;
        }

        if (empty($params['patientId'])) {
            $rx = $this->find($rxId);
            if ($rx) {
                $params['patientId'] = $rx->getPatient()->getId();
            }
        }
        if (empty($params['doctorId'])) {
            if (isset($rx)) {
                $params['doctorId'] = $rx->getDoctor()->getId();
            }
        }

        if ($this->isLocalPatient($params)) {
            $price = $this->getEntityManager()
                ->getRepository('UtilBundle:Drug')
                ->getDoctorDrugDomestic($drug, $params['doctorId']);
            $areaType = Constant::AREA_TYPE_LOCAL;
        } else {
            $price = $this->getEntityManager()
                ->getRepository('UtilBundle:Drug')
                ->getDoctorDrugInternational($drug, $params['doctorId']);
            $areaType = Constant::AREA_TYPE_OVERSEA;
        }

        //get doctor margin share
        //(drug.list_price_international - drug.cost_price)*platform_share_percentages.doctor_percentages/100
         if($isDoctorMS) {
             $PSPercentage = $this->getEntityManager()->getRepository('UtilBundle:PlatformSharePercentages')
                ->getPercentageByType($areaType, Constant::MST_MEDICINE);
            $price = ($price - $drug->getCostPrice()) * ($PSPercentage->getDoctorPercentage() / 100);
         }
        return $price;
    }

    /**
     * true : local patient
     * @author Tien Nguyen
     */
    public function isLocalPatient($params)
    {
        $patientId = isset($params['patientId']) ? $params['patientId'] : 0;
        $patient   = isset($params['patient']) ? $params['patient'] : array();

        if (!$patient) {
            $patient = $this->getEntityManager()->getRepository('UtilBundle:Patient')
                ->find($patientId);
        }

        if (null == $patient) {
            return true;
        }

        $settings = !empty($params['setttings']) ? $params['settings'] : array();

        if (!$settings) {
            $settings = $this->getEntityManager()->getRepository('UtilBundle:PlatformSettings')
                ->getPlatFormSetting();
        }

        $countryId = isset($settings['operationsCountryId']) ? $settings['operationsCountryId'] : 0;

        $patientCountry = $patient->getPrimaryResidenceCountry()->getId();

        return $countryId == $patientCountry;
    }

    /**
     * @author Tien Nguyen
     */
    private function getDrugIngredients($params)
    {
        $drugId = isset($params['drugId']) ? $params['drugId'] : 0;

        $criteria = array(
            'drug' => $drugId
        );

        $ingredients = $this->getEntityManager()
            ->getRepository('UtilBundle:DrugActiveIngredient')
            ->findBy($criteria);

        $result = array();
        foreach ($ingredients as $value) {
            $result[] = $value->getActiveIngredient();
        }

        return $result;
    }

    /**
     * @author Tien Nguyen
     */
    private function getLatestOrderNumber($params = array())
    {
        $doctorId = isset($params['doctorId']) ? $params['doctorId'] : 0;
        $criteria = array();
        if ($doctorId) {
            $criteria = array('doctor' => $doctorId);
        }
        $orderBy  = array('id' => 'desc');
        $rx = $this->findOneBy($criteria, $orderBy);

        if (null == $rx) {
            return '';
        }

        return $rx->getOrderNumber();
    }

    /**
     * @author Tien Nguyen
     */
    private function generateCheckDigit($input)
    {
        $arrMultiplier = array(
            3, 6, 11, 8, 4, 17, 10, 1, 16, 14, 9, 7, 2, 15
        );

        $string = str_split($input);
        $sum = 0;
        foreach ($string as $key => $value) {
            $sum += $value*$arrMultiplier[$key];
        }

        return $sum%6;
    }

    /**
     * @author Tien Nguyen
     */
    private function formatDrugData($drug)
    {
        $result = array(
            'id' => 0,
            'name' => '',
            'ingredients' => '',
            'manufacturer' => '',
            'price' => 0,
            'doseUnit' => '',
            'gstCode' => '',
            'packingType' => '',
            'minimumOrderQty' => '',
            'packQuantity' => 0,
            'extendName' => '',
            'isShortLife' => false,
            'shortDoseUnit' => ''
        );

        if (empty($drug)) {
            return $result;
        }

        $result['id'] = $drug->getId();
        $result['name'] = $drug->getName();
        $result['extendName'] = '';
        $result['isColdChain'] = $drug->getIsColdChain();

        if(!empty($drug->getStockStatus()) && $drug->getStockStatus()->getName() == "Stock when Ordered") {
            $result['extendName']= ' - '. Constant::LABEL_STOCK_DRUG;
        }
        if(!empty($drug->getIsShortLife()) ) {
            $result['isShortLife']= true;
        }
        $ingredients = $this->getDrugIngredients(array('drugId' => $drug->getId()));
        $arrTemp = array();
        foreach ($ingredients as $ingre) {
            $arrTemp[] = $ingre->getName();
        }
        $result['ingredients'] = implode(', ', $arrTemp);

        $result['manufacturer'] = $drug->getManufacturer()->getName();
        $result['packQuantity'] = $drug->getPackQuantity();
        $result['minimumOrderQty'] = $drug->getMinimumOrderQuantity();
        $result['price'] = $drug->getCostPrice();

        $dosageForm   = $drug->getDosageFormRoute()->getDosageForm();
        $doseUnit     = $dosageForm->getName();
        $packQuantity = $drug->getPackQuantity();
        if ($packQuantity > 1) {
            $doseUnit = $dosageForm->getPluralName();
        }
        $result['doseUnit'] = $doseUnit;
        $result['shortDoseUnit'] = $dosageForm->getShortName();

        $gstCode = $drug->getGstCode();
        if ($gstCode) {
            $result['gstCode'] = $gstCode->getCode();
        }

        $packingType = $drug->getPackingType();
        if ($packingType) {
            $result['packingType'] = $packingType->getName();
        }

        return $result;
    }

    /**
     * @author Tien Nguyen
     */
    public function formatRXDrugData($rxDrug)
    {
        $value = $rxDrug;
        if (is_object($rxDrug)) {
            $value = array();
            $value['prn'] = $rxDrug->getIsTakenAsNeeded();
            $value['causeDrowsiness'] = $rxDrug->getCanCauseDrowsiness();
            $value['completeThisCourse'] = $rxDrug->getIsToCompleteCourse();
            $value['specialInstructions'] = $rxDrug->getSpecialInstructions();
            $value['quantity'] = $rxDrug->getQuantity();
        }

        $template = array();

        $template['sig'] = Common::generateSIGPreview($rxDrug, $this->getEntityManager());

        $drowsiness = '';
        if (isset($value['causeDrowsiness']) && $value['causeDrowsiness'])  {
            $drowsiness = 'May cause drowsiness. If affected, do not drive or operate machinery. Avoid alcohol.';
        }
        $template['drowsiness'] = $drowsiness;

        $complete = '';
        if (isset($value['completeThisCourse']) && $value['completeThisCourse'])  {
            $complete = 'Complete this course of medicine';
        }
        $template['complete'] = $complete;

        $specialInstructions = '';
        if (isset($value['specialInstructions']) && $value['specialInstructions']) {
            $specialInstructions = $value['specialInstructions'];
        }
        $template['instructions'] = str_replace(PHP_EOL, ' ', $specialInstructions);

        $template['qty'] = isset($value['quantity']) ? $value['quantity'] : 0;

        return $template;
    }

    /**
     * @author toan.le
     */
    public function getTotalComfirmedRx($params){
        $queryBuilder = $this->createQueryBuilder('r');
        $selectStr = "count(r) as total";
        $queryBuilder->select($selectStr)
                    ->where('r.status IN (:status)')
                    ->andWhere('r.doctor = :doctorID')
                    ->andWhere('r.deletedOn is null')
                    ->setParameter('status', array(
                        Constant::RX_STATUS_CONFIRMED,
                        Constant::RX_STATUS_REVIEWING,
                        Constant::RX_STATUS_APPROVED,
                        Constant::RX_STATUS_DISPENSED,
                        Constant::RX_STATUS_READY_FOR_COLLECTION,
                        Constant::RX_STATUS_COLLECTED,
                        Constant::RX_STATUS_DELIVERING,
                        Constant::RX_STATUS_DELIVERED
                    ))
                    ->setParameter('doctorID', $params['doctorId']);

       $totalConfirmed = $queryBuilder->getQuery()->getOneOrNullResult();

       $queryBuilder->setParameter('status', array(Constant::RX_STATUS_PAYMENT_FAILED));
       $totalFailed = $queryBuilder->getQuery()->getOneOrNullResult();

       $queryBuilder->setParameter('status', array(Constant::RX_STATUS_DRAFT));
       $totalDraft = $queryBuilder->getQuery()->getOneOrNullResult();

       $queryBuilder->andWhere('r.isOnHold IN (0,2)');
       $queryBuilder->setParameter('status', array(Constant::RX_STATUS_PENDING));
       $totalPending = $queryBuilder->getQuery()->getOneOrNullResult();

       return [
        'totalConfirmed'  => $totalConfirmed['total'],
        'totalFailed'     => $totalFailed['total'],
        'totalDraft'      => $totalDraft['total'],
        'totalPending'    => $totalPending['total'],
       ];
    }

    /**
     * @author toan.le
     */
    public function getTotalFeeRx($params = array()){
        $queryBuilder = $this->createQueryBuilder('r');
        $selectStr = "
            SUM(
                IFELSE(r.platformMedicineFee is null, 0, r.platformMedicineFee) +
                IFELSE(r.platformServiceFee is null, 0, r.platformServiceFee) ) AS totalPlatformFee,
            SUM(
                IFELSE(r.agentMedicineFee is null, 0, r.agentMedicineFee) +
                IFELSE(r.agentServiceFee is null, 0, r.agentServiceFee) ) AS totalAgentFee,
            SUM(
                IFELSE(ms.doctorAmount is null, 0, ms.doctorAmount)
                ) AS totalDoctorFee
        ";
        $queryBuilder->select($selectStr)
                    ->innerJoin('UtilBundle:MarginShare', 'ms', 'WITH', 'ms.rx = r.id')
                    ->where('r.paidOn is not null')
                    ->andWhere('r.status != :failStatus')
                    ->andWhere('r.status != :deadStatus')
                    ->andWhere('r.status != :pfailStatus')
                    ->andWhere('r.deletedOn is null')
                    ->setParameter('failStatus', Constant::RX_STATUS_FAILED)
                    ->setParameter('deadStatus', Constant::RX_STATUS_DEAD)
                    ->setParameter('pfailStatus', Constant::RX_STATUS_PAYMENT_FAILED);

        if(isset($params['doctorId'])){
            $queryBuilder->andWhere('r.doctor = :doctorId')
                        ->setParameter('doctorId', $params['doctorId']);
        }

        if (isset($params['currMonth'])) {
            $queryBuilder->andWhere('MONTH(r.paidOn) = :month')
                        ->setParameter('month', $params['currMonth']);
        }
        $result = $queryBuilder->getQuery()->getOneOrNullResult();

        return $result;
    }

    /**
     * @author toan.le
     */
    public function getDataChart($params){
        if($params['feeType'] == Constant::FEE_DOCTOR){
            $columnMedicine = 'doctorMedicineFee';
            $columnService  = 'doctorServiceFee';
        } else {
            $columnMedicine = 'platformMedicineFee';
            $columnService  = 'platformServiceFee';
        }
        $queryBuilder = $this->createQueryBuilder('r');
        $selectStr = "
            CONCAT(
                MONTHNAME(STR_TO_DATE(MONTH(r.createdOn), '%m'))
                ,' '
                ,YEAR(r.createdOn)) as monthly,
            SUM(IFELSE(r.orderValue is null, 0, r.orderValue)) AS totalSales,
            SUM(
                IFELSE(ms.platformAmount is null, 0, ms.platformAmount)
                ) AS totalFee,
            SUM(
                IFELSE(ms.doctorAmount is null, 0, ms.doctorAmount)
                ) AS totalDoctorFee
        ";
        $queryBuilder->select($selectStr)
                    ->innerJoin('UtilBundle:MarginShare', 'ms', 'WITH', 'ms.rx = r.id')
                    ->where('r.paidOn is not null')
                    ->andWhere('r.status != :failStatus')
                    ->andWhere('r.paidOn != :deadStatus')
                    ->andWhere('r.paidOn != :pfailStatus')
                    ->andWhere('r.deletedOn is null')
                    ->setParameter('failStatus', Constant::RX_STATUS_FAILED)
                    ->setParameter('deadStatus', Constant::RX_STATUS_DEAD)
                    ->setParameter('pfailStatus', Constant::RX_STATUS_PAYMENT_FAILED);

        if(isset($params['doctorId'])){
            $queryBuilder->andWhere('r.doctor = :doctorId')
                        ->setParameter('doctorId', $params['doctorId']);
        }

        $queryBuilder->groupBy("monthly")
            ->orderBy("r.createdOn", "DESC")
            ->setMaxResults(12);
        $result = $queryBuilder->getQuery()->getArrayResult();

        $dateTime = new \DateTime('midnight first day of 11 month ago');

        // STRIKE 1257
        $startDate = clone $dateTime;
        if (isset($params['doctorId'])) {
            $doctorObject = $this->getEntityManager()
                ->getRepository('UtilBundle:Doctor')->find($params['doctorId']);
            $createdOn = $doctorObject->getCreatedOn();
            if ($createdOn > $startDate) {
                $startDate = $createdOn;
            }
        }

        $endDate = clone $startDate;
        $endDate->modify('last day of +11 month');

        $data = [];
        while ($startDate <= $endDate) {
            $temp['monthly'] = $startDate->format('M y');
            $temp['totalSales'] = 0;
            $temp['totalFee'] = 0;
            $temp['totalDoctorFee'] = 0;
            $data[] = $temp;
            $startDate->modify('first day of next month');
        }

        foreach ($result as $month) {
            $monthly = new \DateTime($month['monthly']);
            $monthly->modify('midnight first day of this month');
            if ($monthly < $dateTime) {
                continue;
            }

            foreach ($data as $key => $val) {
                $valMonthly = date_create_from_format('M y', $val['monthly']);
                $valMonthly->modify('midnight first day of this month');
                if($monthly == $valMonthly){
                    $val['totalSales'] += $month['totalSales'];
                    $val['totalFee'] += $month['totalFee'];
                    $val['totalDoctorFee'] += $month['totalDoctorFee'];
                }
                $data[$key] = $val;
            }
        }

        ksort($data);

        return array_values($data);
    }

    /**
     * @author toan.le
     */
    public function getRxForDashboardDoctor($params){
        $statusDraft = Constant::RX_STATUS_DRAFT;
        $typeNotification = Constant::MSG_TYPE_NOTIFICATION;
        $typeReplacementOrder = Constant::MSG_TYPE_REPLACEMENT_ORDER;
        $typeMaybe = Constant::MSG_TYPE_MAYBE;
        $typeYes = Constant::MSG_TYPE_YES;
        $doctorIssue = Constant::MSG_TYPE_DOCTOR_ISSUE;
        $doctorReview = Constant::MESSAGE_CONTENT_TYPE_DOCTOR_REVIEW;
        $amendments = Constant::MESSAGE_CONTENT_TYPE_AMENDMENTS;
        $doctorReviewStatus = Constant::RX_STATUS_FOR_DOCTOR_REVIEW;
        $amendmentStatus = Constant::RX_STATUS_FOR_AMENDMENT;
        $userId = $params['userId'];
        $doctorId = $params['id'];
        $em = $this->getEntityManager();

        $selectStr = "SELECT rr.id as rrId,
                           IF(p1_.first_name IS NULL, p1_.last_name, CONCAT(p1_.first_name, ' ', p1_.last_name)) AS fullName,
                           p.patient_code AS patientCode,
                           IF(r.status = $statusDraft, r.created_on, rr.updated_on) AS createdOn,
                           IF(r.status = $statusDraft, 1, 0) AS isDraft,
                           r.id AS id,
                           r.order_number,
                           r.is_scheduled_rx,
                           p.id AS patientId,
                           mc.type AS type,
                           mc.body AS body,
                           m.id as msgId,
                           m.sender_name as senderName
                    FROM rx r
                    INNER JOIN patient p ON r.patient_id = p.id
                    INNER JOIN personal_information p1_ ON p.personal_information_id = p1_.id
                    LEFT JOIN rx_refill_reminder rr ON (rr.rx_id = r.id)
                    LEFT JOIN message m ON rr.message_id = m.id
                    LEFT JOIN message_content mc ON m.content_id = mc.id
                    WHERE r.status = $statusDraft
                      AND p.doctor_id = $doctorId
                      AND r.deleted_on IS NULL
                      AND m.read_date IS NULL
                       UNION

                    SELECT NULL as rrId,
                           IF(p1_.first_name IS NULL, p1_.last_name, CONCAT(p1_.first_name, ' ', p1_.last_name)) AS fullName,
                           p.patient_code AS patientCode,
                           m.sent_date AS createdOn,
                           IF(r.status = $statusDraft, 1, 0) AS isDraft,
                           r.id AS id,
                           r.order_number,
                           r.is_scheduled_rx,
                           p.id AS patientId,
                           mc.type AS type,
                           mc.body AS body,
                           m.id as msgId,
                           m.sender_name as senderName
                    FROM rx r
                    INNER JOIN patient p ON r.patient_id = p.id
                    INNER JOIN personal_information p1_ ON p.personal_information_id = p1_.id
                    LEFT JOIN message m ON m.receiver_id = $userId
                    LEFT JOIN message_content mc ON m.content_id = mc.id
                    WHERE (mc.type IN ('$typeNotification','$typeReplacementOrder', '$typeMaybe', '$typeYes','$doctorIssue') OR (mc.type IN ('$doctorReview', '$amendments') AND r.status IN ($doctorReviewStatus, $amendmentStatus)))
                      AND mc.entity_id = r.id
                      AND p.doctor_id = $doctorId
                      AND r.deleted_on IS NULL
                      AND m.read_date IS NULL ";


        $stmt = $em->getConnection()->prepare($selectStr);

        $stmt->execute();
        $totalResult = count($stmt->fetchAll());

        if(isset($params['sorting']) && !empty($params['sorting'])){
            $arrSort= explode("_", $params['sorting']);
            if(isset($arrSort[0]) && isset($arrSort[1])){
                $selectStr .= "ORDER BY ".$arrSort[0]." ".strtoupper($arrSort[1]);
            }
        }else{
            $selectStr .= "ORDER BY createdOn DESC";
        }

        if(isset($params['perPage']) && isset($params['page'])){
            $perPage = ($params['perPage'] > 0)? $params['perPage']: Constant::PER_PAGE_DEFAULT;
            $page = ($params['page'] > 0)? $params['page']: Constant::PAGE_DEFAULT;
            $startRecord = $perPage*$page;
            $selectStr .= " LIMIT ".$perPage." OFFSET ".$startRecord;
            $totalPage = ceil($totalResult/$perPage);
        }

        $stmt = $em->getConnection()->prepare($selectStr);
        $stmt->execute();
        $result = $stmt->fetchAll();

        return array(
            'totalResult' => $totalResult,
            'totalPages' => isset($totalPage) ? $totalPage : 0,
            'data' => $result
        );
    }

    /**
     * Get content CIF
     * @param $orderNumber
     * @author vinh.nguyen
     */
    public function getContentCIF($orderNumber)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('rx.id, rx.orderNumber, rx.orderPhysicalNumber, rx.createdOn, d.displayName, rx.paidOn')
            ->addSelect('p.id as patientId, p.patientCode, d.id as doctorId, p.taxId')
            ->addSelect("ifelse(pi.firstName is null, pi.lastName, CONCAT(pi.firstName, ' ', pi.lastName)) AS patientName, pi.gender, pi.dateOfBirth, pi.passportNo")
            ->addSelect("ifelse(pid.firstName is null, pid.lastName, CONCAT(pid.firstName, ' ', pid.lastName)) AS doctorName, pid.title as doctorTitle, ml.registrationNumber")
            ->addSelect("c.number as doctorNumber, c.areaCode, co.phoneCode")
            ->addSelect('sco.code as countryCode, sco.name as countryName')
            ->addSelect('cos.code as countryCodeShipping, cos.name as countryNameShipping')
            ->from('UtilBundle:Rx', 'rx')
            ->leftJoin('rx.patient', 'p')
            ->leftJoin('rx.doctor', 'd')
            ->leftJoin('rx.shippingAddress', 'sa')
            ->innerJoin('sa.city', 'sci')
            ->innerJoin('sci.country', 'sco')
            ->leftJoin('p.personalInformation', 'pi')
            ->leftJoin('p.primaryResidenceCountry', 'prc')
            ->leftJoin('d.personalInformation', 'pid')
            ->leftJoin('d.medicalLicense', 'ml')
            ->leftJoin('UtilBundle:DoctorPhone', 'dp', 'WITH', 'dp.doctor = d.id')
            ->leftJoin('dp.contact', 'c')
            ->leftJoin('c.country', 'co')
            ->leftJoin('rx.shippingAddress', 'a')
            ->innerJoin('a.city', 'cs')
            ->innerJoin('cs.country', 'cos')
            ->where('rx.orderPhysicalNumber = :orderNumber AND rx.paidOn is not null')
            ->setParameter('orderNumber', $orderNumber)
        ;

        $info = $qb->getQuery()->getOneOrNullResult();

        if($info == null)
            return null;
        if(!empty($info['displayName'])){
            $info['doctorName'] = $info['displayName'];
        }

        $qb2 = $this->getEntityManager()->createQueryBuilder();
        $qb2->select('rl.name, rl.quantity, rl.costPrice, rl.listPrice, pt.name as namePackingType')
            ->addSelect('g.code')
            ->from('UtilBundle:RxLine', 'rl')
            ->leftJoin('rl.drug', 'dr')
            ->leftJoin('dr.packingType', 'pt')
            ->leftJoin('dr.gstCode', 'g')
            ->where('rl.rx = :rxId')
            ->andWhere("rl.lineType = 1")
            ->setParameter('rxId', $info['id']);

        $rxLines = $qb2->getQuery()->getArrayResult();

        //get clinic
        $qb3 = $this->getEntityManager()->createQueryBuilder();
        $qb3->select('c.businessName, c.businessLogoUrl, c.isPrimary')
            ->addSelect('a.line1, a.line2, a.line3, a.postalCode')
            ->addSelect('ci.name as city, st.name as state, co.name as country')
            ->from('UtilBundle:Clinic', 'c')
            ->leftJoin('c.businessAddress', 'b')
            ->leftJoin('b.address', 'a')
            ->leftJoin('a.city', 'ci')
            ->leftJoin('ci.state', 'st')
            ->leftJoin('ci.country', 'co')
            ->where('c.doctor = :doctorId')
            ->andWhere('c.deletedOn IS NULL')
            ->setParameter('doctorId', $info['doctorId'])
            ;

        $primaryClinic = clone $qb3;
        $primaryClinic->andWhere("c.isPrimary = true")->setMaxResults(1);
        $clinicResult = $primaryClinic->getQuery()->getOneOrNullResult();

        $subClinic = clone $qb3;
        $subClinic->andWhere("c.isPrimary is null");
        $subClinicResult = $subClinic->getQuery()->getArrayResult();

        if(empty($clinicResult) && !empty($subClinicResult))
            $clinicResult = $subClinicResult[0];

        //fxRate
        $fxRate = $this->getEntityManager()->createQueryBuilder();
        $fxRate->select('fr.currencyFrom, fr.currencyTo, fr.rate')
            ->from('UtilBundle:FxRate', 'fr');


        return array(
            'info'  => $info,
            'lists' => $rxLines,
            'clinic' => $clinicResult,
            'subClinic' => $subClinicResult,
            'fxRate' => $fxRate->getQuery()->getArrayResult()
        );
    }

    /**
     * Get order list
     * The function will be used for Customer Care
     * @param  array $params
     * @author  vinh.nguyen
     * @return  array
     */
    public function getOrderListForCustomerCare($params)
    {
        $draft = Constant::RX_STATUS_DRAFT;
        $failed = Constant::RX_STATUS_FAILED;

        // find all order from rx table
        $qb = $this->createQueryBuilder('r');
        $qb->select("r.id as rxId,
                r.orderNumber,
                r.paidOn as paidOn,
                ifelse(pi.firstName is null, pi.lastName, CONCAT(pi.firstName, ' ', pi.lastName)) AS doctorName,
                ifelse(pi2.firstName is null, pi2.lastName, CONCAT(pi2.firstName, ' ', pi2.lastName)) AS patientName,
                r.orderValue as orderValue,
                r.status,
                d.doctorCode as doctorCode,
                pi.emailAddress as doctorEmailAddress,
                cd.number as doctorNumber,
                cd.areaCode as doctorAreaCode,
                cod.phoneCode as doctorPhoneCode,
                p.patientCode as patientCode,
                pi2.emailAddress as patientEmailAddress,
                ppa.number as patientNumber,
                ppa.areaCode as patientAreaCode,
                cop.phoneCode as patientPhoneCode
            ")
            ->innerJoin('r.doctor', 'd')
            ->innerJoin('d.personalInformation', 'pi')
            ->innerJoin('r.patient', 'p')
            ->innerJoin('p.personalInformation', 'pi2')
            ->leftJoin('r.shippingAddress', 'a')
            ->innerJoin('a.city', 'ci')
            ->innerJoin('ci.country', 'c')
            ->leftJoin('UtilBundle:DoctorPhone', 'dp', 'WITH', 'dp.doctor = d.id')
            ->leftJoin('dp.contact', 'cd')
            ->leftJoin('cd.country', 'cod')
            ->leftJoin('UtilBundle:PatientPhone', 'pp', 'WITH', 'pp.patient = p.id')
            ->leftJoin('pp.phone', 'ppa')
            ->leftJoin('ppa.country', 'cop')
            ->where('r.deletedOn is null')
            ->andWhere('r.paidOn is not null')
            ->andWhere('r.status NOT IN('.$draft.','.$failed.')');

        // filter by doctor feed
        $orderValueGte = isset($params['orderValueGte']) ? $params['orderValueGte'] : '';
        $orderValueLte = isset($params['orderValueLte']) ? $params['orderValueLte'] : '';

        $fee = 'r.orderValue';
        if (!empty($params['isAgentReport'])) {
            $fee = '(r.agentMedicineFee + r.agentServiceFee)';
        }
        if (!empty($params['isDoctorReport'])) {
            $fee = 'ms.doctorAmount';
        }

        if (!empty($orderValueGte) && !empty($orderValueLte)) {
            $qb->andWhere($fee . ' >= :orderValueGte')
                ->setParameter('orderValueGte', $orderValueGte)
                ->andWhere($fee . ' <= :orderValueLte')
                ->setParameter('orderValueLte', $orderValueLte);
        } elseif (!empty($orderValueGte) && empty($orderValueLte)) {

            $qb->andWhere($fee . ' >= :orderValueGte')
                ->setParameter('orderValueGte', $orderValueGte);

        } elseif (empty($orderValueGte) && !empty($orderValueLte)) {
            $qb->andWhere($fee . ' <= :orderValueLte')
                ->setParameter('orderValueLte', $orderValueLte);
        }

        // search on rx: orderNumber
        if (isset($params['orderNumber']) && !empty($params['orderNumber'])) {
            $term = trim(strtolower($params['orderNumber']));

            $qb->andWhere("LOWER(r.orderNumber) LIKE :orderNumber")
                ->setParameter('orderNumber', '%' . $term . '%');
        }

        //search on patient: code, name
        if(isset($params['patient']) && !empty($params['patient'])) {
            $term = trim(strtolower($params['patient']));

            $searchIn = $qb->expr()->like(
                $qb->expr()->concat('pi2.firstName', $qb->expr()->concat($qb->expr()->literal(' '), 'pi2.lastName')),
                $qb->expr()->literal( '%' . $term . '%')
            );

            $qb
                ->andWhere($searchIn ." OR LOWER(p.patientCode) LIKE :patientTerm")
                ->setParameter('patientTerm', '%' . $term . '%');
        }

        //search on doctor: code, name
        if(isset($params['doctor']) && !empty($params['doctor'])) {
            $term = trim(strtolower($params['doctor']));

            $searchIn = $qb->expr()->like(
                $qb->expr()->concat('pi.firstName', $qb->expr()->concat($qb->expr()->literal(' '), 'pi.lastName')),
                $qb->expr()->literal( '%' . $term . '%')
            );

            $qb
                ->andWhere($searchIn ." OR LOWER(d.doctorCode) LIKE :doctorTerm")
                ->setParameter('doctorTerm', '%' . $term . '%');
        }

        // search on rx: status
        if (isset($params['status']) && $params['status'] != 'all') {
            $qb->andWhere("r.status = :status")
                ->setParameter('status', $params['status']);
        }

        // header fields sorting
        if(isset($params['sorting']) && !empty($params['sorting'])){
            $fieldName = "";
            $arrSort= explode("_", $params['sorting']);
            if(isset($arrSort[0]) && isset($arrSort[1])) {
                switch($arrSort[0]):
                    case 'orderNumber':
                        $fieldName = 'r.orderNumber';
                        break;
                    case 'paidOn':
                        $fieldName = 'r.paidOn';
                        break;
                    case 'doctorCode':
                        $fieldName = 'd.doctorCode';
                        break;
                    case 'doctorName':
                        $fieldName = 'doctorName';
                        break;
                    case 'patientCode':
                        $fieldName = 'p.patientCode';
                        break;
                    case 'patientName':
                        $fieldName = 'patientName';
                        break;
                    case 'status':
                        $fieldName = 'r.status';
                        break;
                endswitch;
                if(!empty($fieldName)) {
                    $qb->orderBy($fieldName, strtoupper($arrSort[1]));
                }
            }
        }
//
//        if (isset($params['sorting']) && !empty($params['sorting'])) {
//            $qb->orderBy($params['sortInfo']['column'], $params['sortInfo']['direction']);
//        }


        $perPage     = ($params['perPage'] > 0)? $params['perPage']: Constant::PER_PAGE_DEFAULT;
        $page        = ($params['page'] > 0)? $params['page']: Constant::PAGE_DEFAULT;
        $startRecord = $perPage * $page;

        $totalResult = count($qb->getQuery()->getArrayResult());

        $qb
            ->setFirstResult($startRecord)
            ->setMaxResults($perPage);


        $result = $qb->getQuery()->getArrayResult();

        return array(
            'totalResult' => $totalResult,
            'totalPages' => ceil($totalResult/$perPage),
            'data' => $result
        );
    }

    /**
     * @author Tien Nguyen
     */
    private function generateTaxInvoiceNo(Rx $rx)
    {
        $doctor  = $rx->getDoctor();
        $patient = $rx->getPatient();

        if (!$doctor || !$patient) {
            return '';
        }

        $arrResult = array();

        $doctorGSTCode = $this->getEntityManager()
            ->getRepository('UtilBundle:Doctor')
            ->getDoctorGSTCode($doctor);
        if (Constant::GST_SRS == $doctorGSTCode) {
            $arrResult[] = 'TINV';
        } else {
            $arrResult[] = 'INV';
        }

        $doctorId = 0;
        $doctorCode = $doctor->getDoctorCode();
        if ($doctorCode) {
            list(,,,$doctorId) = explode('-', $doctorCode);
        }
        $arrResult[] = sprintf("%'.04d", $doctorId);

        $settings = $this->getEntityManager()->getRepository('UtilBundle:PlatformSettings')
            ->getPlatFormSetting();
        $countryId = isset($settings['operationsCountryId']) ? $settings['operationsCountryId'] : 0;

        $countryCode = 0;
        $country = $this->getEntityManager()->getRepository('UtilBundle:Country')
            ->find($countryId);
        if ($country) {
            $countryCode = $country->getPhoneCode();
        }

        $patientCode = 9;
        $params['settings'] = $settings;
        $params['patient']  = $patient;
        if ($this->isLocalPatient($params)) {
            $patientCode = 1;
        }

        $arrResult[] = $countryCode . $patientCode;

        $orderId = 0;
        $latestOrderNumber = $rx->getOrderNumber();
        if ($latestOrderNumber) {
            list(,,,$orderId) = explode('-', $latestOrderNumber);
        }
        $arrResult[] = sprintf("%'.07d", $orderId);

        $sequenceNumber = $doctor->getSequenceNumbers();
        $newNo = 0;
        if ($sequenceNumber) {
            $newNo = $sequenceNumber->getTaxInvoice();
            $sequenceNumber->setTaxInvoice($newNo + 1);
            $sequenceNumber->setUpdatedOn(new \DateTime());
            $this->getEntityManager()->persist($sequenceNumber);
        }
        $arrResult[] = sprintf("%'.04d", $newNo);

        return implode('-', $arrResult);
    }

    /**
     * generate proforma invoice number
     * e.g: PINV-0218-609-0000001-0009
     */
    public function generateProformaInvoiceNo($params)
    {
        $em = $this->getEntityManager();

        $arrResult = array();
        $arrResult[] = Constant::PROFORMA_NUMBER_CODE;

        $now = new \DateTime();
        $arrResult[] = $now->format("my");

        $settings = $em->getRepository('UtilBundle:PlatformSettings')->getPlatFormSetting();
        $countryId = isset($settings['operationsCountryId']) ? $settings['operationsCountryId'] : 0;

        $countryCode = 0;
        $country = $this->getEntityManager()->getRepository('UtilBundle:Country')
            ->find($countryId);
        if ($country) {
            $countryCode = $country->getPhoneCode();
        }

        $patientCode = 9;
        $params['settings'] = $settings;
        if ($this->isLocalPatient($params)) {
            $patientCode = 1;
        }

        $arrResult[] = $countryCode . $patientCode;

        //running number
        $runningNumber = $em->getRepository('UtilBundle:RunningNumber')->findOneBy(array('runningNumberCode' => Constant::PROFORMA_NUMBER_CODE));
        $runningNumberValue = $runningNumber->getRunningNumberValue();
        $runningNumberValue = ++$runningNumberValue;
        $runningNumber->setRunningNumberValue($runningNumberValue);
        $em->persist($runningNumber);
        $em->flush();

        $arrResult[] = sprintf("%'.07d", $runningNumberValue);

        $newNo = 0;
        $doctorId = isset($params['doctorId']) ? $params['doctorId'] : 0;
        $doctor = $this->getEntityManager()->getRepository("UtilBundle:Doctor")->find($doctorId);
        if($doctor != null) {
            $sequenceNumber = $doctor->getSequenceNumbers();
            if ($sequenceNumber) {
                $newNo = $sequenceNumber->getTaxInvoice();
                $sequenceNumber->setTaxInvoice($newNo + 1);
                $sequenceNumber->setUpdatedOn(new \DateTime());
                $this->getEntityManager()->persist($sequenceNumber);
            }
        }
        $arrResult[] = sprintf("%'.04d", $newNo);

        return implode('-', $arrResult);
    }

    /**
     * Get order list
     * The function will be used for Customer Care
     */
    public function getOrderListForCustomerCareDetail($id, $params = array())
    {
        $draft = Constant::RX_STATUS_DRAFT;
        $failed = Constant::RX_STATUS_FAILED;

        // find all order from rx table
        $qb = $this->createQueryBuilder('r');
        $qb->select("r.id as rxId,
                r.orderNumber,
                r.paidOn as paidOn,
                ifelse(pi.firstName is null, pi.lastName, CONCAT(pi.firstName, ' ', pi.lastName)) AS doctorName,
                ifelse(pi2.firstName is null, pi2.lastName, CONCAT(pi2.firstName, ' ', pi2.lastName)) AS patientName,
                r.orderValue as orderValue,
                r.status,
                d.doctorCode as doctorCode,
                pi.emailAddress as doctorEmailAddress,
                cd.number as doctorNumber,
                cd.areaCode as doctorAreaCode,
                cod.phoneCode as doctorPhoneCode,
                p.patientCode as patientCode,
                pi2.emailAddress as patientEmailAddress,
                ppa.number as patientNumber,
                ppa.areaCode as patientAreaCode,
                cop.phoneCode as patientPhoneCode
            ")
            ->innerJoin('r.doctor', 'd')
            ->innerJoin('d.personalInformation', 'pi')
            ->innerJoin('r.patient', 'p')
            ->innerJoin('p.personalInformation', 'pi2')
            ->leftJoin('r.shippingAddress', 'a')
            ->innerJoin('a.city', 'ci')
            ->innerJoin('ci.country', 'c')
            ->leftJoin('UtilBundle:DoctorPhone', 'dp', 'WITH', 'dp.doctor = d.id')
            ->leftJoin('dp.contact', 'cd')
            ->leftJoin('cd.country', 'cod')
            ->leftJoin('UtilBundle:PatientPhone', 'pp', 'WITH', 'pp.patient = p.id')
            ->leftJoin('pp.phone', 'ppa')
            ->leftJoin('ppa.country', 'cop')
            ->where('r.id = :rxId')
            ->setParameter('rxId', $id);

        $result = $qb->getQuery()->getOneOrNullResult();

        return $result;
    }

    /**
     * suggestion search
     */
    public function suggestionOrderNumber($term)
    {
        $draft = Constant::RX_STATUS_DRAFT;
        $failed = Constant::RX_STATUS_FAILED;

        $term = trim(strtolower($term));
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select("rx.orderNumber as name")
            ->distinct()
            ->from('UtilBundle:Rx', 'rx')
            ->where('LOWER(rx.orderNumber) LIKE :orderNumber')
            ->andWhere('rx.deletedOn is null')
            ->andWhere('rx.paidOn is not null')
            ->andWhere('rx.status NOT IN('.$draft.','.$failed.')')
            ->setParameter('orderNumber', '%'.$term.'%')
            ->setMaxResults(5);
        $results = $qb->getQuery()->getArrayResult();

        return $results;
    }

    /**
     * Get rx to refill
     * @param type $orderNumber
     * @return type
     * author luyen nguyen
     */
    public function getRxRefill($orderNumber) {
        $queryBuilder = $this->createQueryBuilder('f');
        $queryBuilder->select('f')
                ->innerJoin('UtilBundle:RxRefillReminder', 'rf', 'WITH', 'rf.rx = f.id')
                ->where('f.orderNumber = (:orderNumber)')
                ->andWhere('rf.hasBeenReminded = (:hasBeenReminded)')
                ->andWhere('rf.message IS NULL')
                ->setParameter('orderNumber', $orderNumber)
                ->setParameter('hasBeenReminded', 1);
        $result = $queryBuilder->getQuery()->getOneOrNullResult();
        return $result;
    }

    /**
     * Get rx for Payment Settlement
     * @param type $orderNumber
     * @author vinh.nguyen
     */
    public function getRxOrderValue($orderNumber, $onlyValue = false)
    {
        $qb = $this->createQueryBuilder('rx');
        $qb->select('rx.orderValue,
                rx.paymentGatewayFeeBankMdr,
                rx.paymentGatewayFeeBankGst,
                rx.paymentGatewayFeeFixed,
                rx.paymentGatewayFeeFixedGst
            ')
           ->where('rx.orderNumber = (:orderNumber)')
           ->setParameter('orderNumber', $orderNumber);
        $result = $qb->getQuery()->getOneOrNullResult();

        if($onlyValue && $result != null)
            return $result['orderValue'];

        return $result;
    }

    /**
    * get information for recall
    * @author vinh.nguyen
    **/
    public function getInfoRecall($rxId)
    {
        $qb = $this->createQueryBuilder('rx');
        $qb->select("
                pi.title as doctorTitle,
                ifelse(pi.firstName is null, pi.lastName, CONCAT(pi.firstName, ' ', pi.lastName)) AS doctorName,
                ifelse(pi2.firstName is null, pi2.lastName, CONCAT(pi2.firstName, ' ', pi2.lastName)) AS patientName,
                pi.emailAddress as doctorEmailAddress,
                pi2.emailAddress as patientEmailAddress,
                c.businessName as clinicName
            ")
            ->innerJoin('rx.doctor', 'd')
            ->innerJoin('d.personalInformation', 'pi')
            ->innerJoin('rx.patient', 'p')
            ->innerJoin('p.personalInformation', 'pi2')
            ->innerJoin('UtilBundle:Clinic', 'c', 'WITH', 'c.doctor=d.id AND c.isPrimary=:isPrimary')
            ->where('rx.id=:rxId')
            ->setParameter('rxId', $rxId)
            ->setParameter('isPrimary', true);

        $result = $qb->getQuery()->getOneOrNullResult();
        return $result;
    }

    /**
     * @author Tuan Nguyen
     * Get list of reminder which patient didn't open
     */
    public function getReminderRxs() {
        $qb = $this->createQueryBuilder('rx');
        $qb->select("rx.id, s.reminderCode, rx.lastestReminder")
                ->leftJoin("UtilBundle:RxReminderSetting", "s", "WITH", "s.reminderCode = rx.reminderCode")
                ->leftJoin("UtilBundle:RxRefillReminder", "r", "WITH", "rx.id = r.rx")
                ->leftJoin("UtilBundle:Message", "m", "WITH", "m.id = r.message")
                ->leftJoin("UtilBundle:MessageContent", "mc", "WITH", "mc.id = m.content")
                ->where("rx.paidOn IS NOT NULL")
                ->andWhere('r.hasBeenReminded = 1')
                ->andWhere("rx.lastestReminder IS NOT NULL")
                ->andWhere("rx.reminderCode IS NULL OR rx.reminderCode <> '" . Constant::REMINDER_CODE_EM_D2 . "'")
                ->andWhere('mc.type IS NULL')
                ->groupBy("rx.id");

        $result = $qb->getQuery()->execute();

        return $result;
    }

    /*
    * author : bien
    */

    public function getResolveStatus($id){
        $data = $this->createQueryBuilder('a')
            ->select("tracking.uploadIncident,tracking.refund, tracking.redispense,tracking.replacementOrder, tracking.invoiceParty, tracking.changeDeliveryAddress,tracking.collectDestroyParcel ")
            ->innerJoin('a.resolves', 'resolve')
            ->innerJoin('resolve.resolveTrackings','tracking')
            ->where('a.deletedOn is  null ')
            ->andWhere('resolve.status = 1')
            ->andWhere('a.id = :id')
            ->setParameter('id',$id)
            ->getQuery()
            ->getResult();
        return $data;
    }


    /**
    * get rx for customer care
    * @author bien
    **/
    public function getCustomerCareRx($request) {

        $limit = $request->get('length', '');
        $statusColect =  $request->get('status', '');
        $sort = $request->get('sort', array());
        if (empty($sort)) {
            $sort = array('paidOn' => 'desc');
        }
        $redispenseStatus =  $request->get('redispensType', '');
        $id = strtolower($request->get('id', ''));
        $doctor = strtolower($request->get('doctor', ''));
        $patient = strtolower($request->get('patient', ''));
        $ofset = $request->get('page', 1);


        $query = $this->createQueryBuilder('a')
                ->join('a.doctor', 'd')
                ->join('a.patient', 'p')
                ->join('d.personalInformation', 'dp')
                ->join('p.personalInformation', 'pp');



        $query->where('a.deletedOn is  null ');
        $query->andWhere('a.status not in (1,30,41)');
        if(!empty($statusColect)) {
            $query->andWhere('a.status = :status')
                ->setParameter('status', $statusColect);
        }
        if(!empty($redispenseStatus)) {
            switch ($redispenseStatus) {
                //redispense
                case 1:
                    $query->andWhere($query->expr()->in('a.redispensingStatus',[Constant::REDISPENSE_STATE_STARTED,Constant::REDISPENSE_STATE_REVIEWED,Constant::REDISPENSE_STATE_PREVIEW_MEDICINE,Constant::REDISPENSE_STATE_APPROVE]))
                        ->innerJoin('a.resolves', 'r')
                        ->innerJoin('r.resolveRedispenses', 'rr')
                        ->andWhere($query->expr()->eq('r.status', Constant::RESOVLVE_STATUS_ACTIVE));
                    break;
                //completed
                case 2 :
                    $query->andWhere('a.redispensingStatus = :status')
                        ->innerJoin('a.resolves', 'r')
                        ->innerJoin('r.resolveRedispenses', 'rr')
                        ->andWhere($query->expr()->eq('r.status', Constant::RESOVLVE_STATUS_ACTIVE))
                        ->setParameter('status', Constant::REDISPENSE_STATE_COMPLETE);
                    break;

            }

        }
        $query->andWhere('a.orderNumber  lIKE :id AND (LOWER(d.doctorCode) LIKE :doctor OR LOWER(CONCAT(dp.firstName,\' \',dp.lastName)) LIKE :doctor )  AND  (LOWER(CONCAT(pp.firstName,\' \',pp.lastName))  lIKE :patient OR LOWER(p.patientCode) LIKE :patient)')
                ->setParameter('id', '%' . $id . '%')
                ->setParameter('doctor', '%' . $doctor . '%')
                ->setParameter('patient', '%' . $patient . '%')
                ->setMaxResults($limit)
                ->setFirstResult(($ofset - 1) * $limit);

        if(empty($sort)){
            $query->orderBy("a.paidOn", "DESC");
        } else {
            $this->generateSort($query, $sort);
        }

        $orders = $query->getQuery()->getResult();

        $data = $this->convertData($orders);
        $total = $this->createQueryBuilder('a')
                ->select('count(a.id)')
                ->join('a.doctor', 'd')
                ->join('a.patient', 'p')
                ->join('d.personalInformation', 'dp')
                ->join('p.personalInformation', 'pp');



        $total->where('a.deletedOn is  null ');
        $total->andWhere('a.status not in (1,30,41)');

        if(!empty($statusColect)) {
            $total->andWhere('a.status = :status')
                ->setParameter('status', $statusColect);
        }
        if(!empty($redispenseStatus)) {
            switch ($redispenseStatus) {
                //redispense
                case 1:
                    $total->andWhere($total->expr()->in('a.redispensingStatus',[Constant::REDISPENSE_STATE_STARTED,Constant::REDISPENSE_STATE_REVIEWED,Constant::REDISPENSE_STATE_PREVIEW_MEDICINE,Constant::REDISPENSE_STATE_APPROVE]))
                        ->innerJoin('a.resolves', 'r')
                        ->innerJoin('r.resolveRedispenses', 'rr')
                        ->andWhere($total->expr()->eq('r.status', Constant::RESOVLVE_STATUS_ACTIVE));
                    break;
                //completed
                case 2 :
                    $total->andWhere('a.redispensingStatus = :status')
                        ->innerJoin('a.resolves', 'r')
                        ->innerJoin('r.resolveRedispenses', 'rr')
                        ->andWhere($total->expr()->eq('r.status', Constant::RESOVLVE_STATUS_ACTIVE))
                        ->setParameter('status', Constant::REDISPENSE_STATE_COMPLETE);
                    break;

            }

        }
        $total->andWhere('a.orderNumber  lIKE :id AND (LOWER(d.doctorCode) LIKE :doctor OR LOWER(CONCAT(dp.firstName,\' \',dp.lastName)) LIKE :doctor )  AND  (LOWER(CONCAT(pp.firstName,\' \',pp.lastName))  lIKE :patient OR LOWER(p.patientCode) LIKE :patient)')
                ->setParameter('id', '%' . $id . '%')
                ->setParameter('doctor', '%' . $doctor . '%')
                ->setParameter('patient', '%' . $patient . '%');


        $fr = $total->getQuery()->getSingleScalarResult();


        return array('data' => $data, 'total' => $fr);

    }
    /*
     * Author bien
     */
    /*
     * conver data for customer care order list
     */
    private function convertData($orders) {
        $data = array();
        $arrayOrder = [];
        foreach ($orders as $obj) {

            if (!empty($obj->getPaidOn())) {
                $confirm = $obj->getPaidOn()->format('d M y');
            } else {
                $confirm = '';
            }


            $phoned = '';
            $emaild = '';
            $doc = $obj->getDoctor();

            $doctorName = $doc->getPersonalInformation()->getFirstName() . ' ' . $doc->getPersonalInformation()->getLastName();
            $doctorCode = $doc->getDoctorCode();

            $doctorPhone = $doc->getDoctorPhones()->first();
            if ($doctorPhone) {
                $phoneObjd = $doctorPhone->getContact();
                $phoneLocationd = $phoneObjd->getCountry()->getPhoneCode();
                $phoneAread = $phoneObjd->getAreaCode();
                $phoneNumberd = $phoneObjd->getNumber();
                if ($phoneAread) {
                    $phoned = '+' . $phoneLocationd . ' ' . $phoneAread . ' ' . $phoneNumberd;
                } else {
                    $phoned = '+' . $phoneLocationd . ' ' . $phoneNumberd;
                }
            }
            $emaild = $doc->getPersonalInformation()->getEmailAddress();

            $phonep = '';
            $emailp = '';
            $pat = $obj->getPatient();
            if (!empty($pat->getPersonalInformation())) {
                $patientName = $pat->getPersonalInformation()->getFirstName() . ' ' . $pat->getPersonalInformation()->getLastName();
                $patientCode = $pat->getPatientCode();
            } else {
                $patientName = '';
                $patientCode = '';
            }
            $phoneObjp = $pat->getPhones()->first();
            if ($phoneObjp) {
                $phoneLocationp = $phoneObjp->getCountry()->getPhoneCode();
                $phoneAreap = $phoneObjp->getAreaCode();
                $phoneNumberp = $phoneObjp->getNumber();
                if ($phoneAreap) {
                    $phonep = '+' . $phoneLocationp . ' ' . $phoneAreap . ' ' . $phoneNumberp;
                } else {
                    $phonep = '+' . $phoneLocationp . ' ' . $phoneNumberp;
                }
            }
            $emailp = $pat->getPersonalInformation()->getEmailAddress();


            $shippingAddress = $obj->getShippingAddress();
            $destination = '';
            if ($shippingAddress && $shippingAddress->getCity()) {

                $city = $shippingAddress->getCity();
                if (is_object($city->getState())) {
                    $destination = $city->getState()->getName() . ', ' . $city->getCountry()->getName();
                } else {
                    $destination = $city->getName() . ', ' . $city->getCountry()->getName();
                }
            }

            $statuslogs = [];
            $logs = $obj->getRxStatusLogs();
            foreach ($logs as $log) {
                $statuslogs[] = [
                    'note' => $log->getNotes(),
                    'status' => $log->getStatus(),
                    'time' => (empty($log->getCreatedOn()))? '':$log->getCreatedOn()->format("d M y h:i A"),
                    'by' => $log->getCreatedBy()
                ];
            }
            //check is read
            $isRead = 1;
            $counter = $obj->getRxCounter();
            if(!empty($counter) && !empty($counter->first())&&  $counter->first()->getIsCustomerCareRead() == 0 ){
                $isRead = 0;
            }

              // check special indent
            $specialIndient = [];
            if(!empty($obj->getIsSpecialIndent())){
                foreach ($obj->getRxLines() as $line) {
                    if($line->getIsDrugStockOrdered() == 1){
                        $specialIndient[] = ['name' => $line->getDrug()->getName(), 'pack' => $line->getDrug()->getPackingType()->getName(),'quantity' =>  $line->getQuantity()];
                    }
                }
            }

            $specialIndientText = [];
            foreach ($specialIndient as $i){
                $specialIndientText [] = $i['name'] . ' / '. $i['quantity'] . ' ' . $i['pack'] . '(s)';
            }

            $data[$obj->getOrderNumber()] = [
                'id' => $obj->getId(),
                'hashId' => Common::encodeHex($obj->getId()),
                'number' => $obj->getOrderNumber(),
                'orderDate' => $confirm,
                'doctorCode' => $doctorCode,
                'doctorName' => $doctorName,
                'patientCode' => $patientCode,
                'patientName' => $patientName,
                'orderValue' => number_format($obj->getOrderValue(), 2, '.', ','),
                'status' => $obj->getStatus(),
                'dispensingStatus' => $obj->getRedispensingStatus(),
                'isRead' => $isRead,
                'phoned' => $phoned,
                'emaild' => $emaild,
                'phonep' => $phonep,
                'emailp' => $emailp,
                'collapse' => [
                    'invoice' => ($obj->getTaxInvoiceNo() != null) ? $obj->getTaxInvoiceNo() : '',
                    'destination' => $destination,
                    'paymentMethod' => '',
                    'specialIndient' => $specialIndient,
                    'specialIndientText' => implode($specialIndientText, ', '),
                    'activeLogs' => $statuslogs
                ]
            ];
            //   array_push($arrayOrder, $obj->getOrderNumber());
        }
        // exit;
        $arrayOrder = array_keys($data);
        $dataFetch = $this->getRxPaymentLogId($arrayOrder);
        foreach ($dataFetch as $rec) {
            $orderNum = $rec['orderRef'];
            $pay = '';
            if( $rec['payMethod'] != null) {
                $pay = ($rec['payMethod'] == 'CC') ? 'VISA/MASTER' : $rec['payMethod'];
            }

            $data[$orderNum]['collapse']['paymentMethod'] = $pay;
        }
        return array_values($data);
    }
    /*
     * author bien
     */
    private function getRxPaymentLogId($arrayOrder){
        if(empty($arrayOrder)){
            return [];
        }
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select("s.orderRef, s.payMethod")
            ->from('UtilBundle:RxPaymentLog', 's')
            ->where($qb->expr()->in('s.orderRef',$arrayOrder))
            ->groupBy('s.orderRef');
        $result = $qb->getQuery()->getResult();

        return $result;
    }

    public function getCountRxPharmacist($type){

        $total = $this->createQueryBuilder('a')
            ->select('count(a.id)')
            ->join('a.doctor', 'd')
            ->join('a.patient', 'p')
            ->join('d.personalInformation', 'dp')
            ->join('p.personalInformation', 'pp')
            ->innerJoin('a.resolves', 'r')
            ->innerJoin('r.resolveRedispenses', 'rr');



        $total->where('a.deletedOn is  null ')
            ->andWhere('a.status not in (1,3,30,41)')
            ->andWhere($total->expr()->eq('r.status', Constant::RESOVLVE_STATUS_ACTIVE));
        if(!empty($type)) {
            switch ($type) {
                //redispense
                case 1:
                    $total->andWhere($total->expr()->in('a.redispensingStatus',[Constant::REDISPENSE_STATE_STARTED,Constant::REDISPENSE_STATE_REVIEWED,Constant::REDISPENSE_STATE_PREVIEW_MEDICINE,Constant::REDISPENSE_STATE_APPROVE]));
                    break;
                //completed
                case 2 :
                    $total->andWhere('a.redispensingStatus = :status')
                        ->andWhere($total->expr()->eq('DATE(rr.approvedOn)', "'".date("Y-m-d")."'"))
                        ->setParameter('status', Constant::REDISPENSE_STATE_COMPLETE);
                    break;
            }

        }
        $fr = $total->getQuery()->getSingleScalarResult();

        return $fr;
    }

    /*
     * Author bien
     */
    /**
     * get rx data by id
     */
     public function getCustomerCareRxData($obj) {
            try {
                $created = $obj->getCreatedOn()->format('d M y');
            }catch (Exception $e) {
                $id = 'Nil';
                $created = 'Nil';
            }
            try{
                $doc = $obj->getDoctor();
                $doctorName = $doc->getPersonalInformation()->getFirstName() . ' ' . $doc->getPersonalInformation()->getLastName();
                $doctorCode = $doc->getDoctorCode();


            } catch (Exception $e) {
                $doctorName = 'Nil';
            }
            try{
                $pat = $obj->getPatient();
                $patientName = $pat->getPersonalInformation()->getFirstName() . ' ' . $pat->getPersonalInformation()->getLastName();
                $patientCode = $pat->getPatientCode();
                $patientAddress =  $pat->getAddresses()->first();

            } catch (Exception $e) {
                $patientName = 'Nil';
            }
            $shippingAddress = '';
            try{

                $shippingAddress =  $obj->getShippingAddress();
                if(!is_object($shippingAddress)){
                    throw new Exception;
                }
                $destination = $shippingAddress->getCity()->getName().', '.$shippingAddress->getCity()->getCountry()->getName();

            } catch (Exception $e) {
                $destination = 'Nil';
            }
            try{
                $statuslogs = [];
                $logs = $obj->getRxStatusLogs();
                foreach ($logs as $log) {
                    $statuslogs[] = [
                        'note' => $log->getNotes(),
                        'status' => $log->getStatus(),
                        'time' => (empty($log->getCreatedOn()))? '':$log->getCreatedOn()->format(Constant::GENERAL_DATE_FORMAT),
                        'by' => $log->getCreatedBy()
                    ];
                }
            }  catch (Exception $e) {
                $statuslogs = [];
            }

            $issues = [];
            foreach ($obj->getIssues() as $is){
                if($is->getStatus() != Constant::ISSUE_STATUS_DRAF ){
                    $issues[] = $is;
                }
            }

            $parentNumber = '';
            $childNumber = '';
            $pr = $obj->getParentRx();
            if(!empty($pr)){
                $parentNumber = $pr->getOrderNumber();
            }
            $children = $obj->getChildren();
            if(count($children)){
                $child = $children->last();
                $childNumber = $child->getOrderNumber();
            }

            $data = [
                'id' =>  $obj->getId(),
                'number' => $obj->getOrderNumber(),
                'createDate' => $created,
                'doctorCode' => $doctorCode,
                'doctorName' =>$doctorName ,
                'patientCode' => $patientCode,
                'patientName' => $patientName,
                'paidOn' => ($obj->getPaidOn())?$obj->getPaidOn()->format(Constant::GENERAL_DATE_FORMAT):'',
                'orderValue' => number_format($obj->getOrderValue(), 2,'.',',') ,
                'status' => $obj->getStatus(),
                'onHold' => $obj->getIsOnHold(),
                'issue' => $issues,
                'shippingAddress'   => $obj->getShippingAddress(),
                'paymentGate' => $obj->getPaymentGate(),
                'destination' =>  Common::getAddressFromEntity($obj->getShippingAddress()),
                'childNumber' => $childNumber,
                'parentNumber' => $parentNumber,
                ];



        return $data;

    }
   /**
    * get issue rx for customer care
    * @author bien
    **/
    public function getCustomerCareIssueRx($request) {

        $limit = $request->get('length', '');
        $status =  $request->get('status', '');
        $state =  $request->get('state', '');
        $startConf = $request->get('startConfirm', '');
        $endConf = $request->get('endConfirm', '');
        $startReport = $request->get('startReport', '');
        $endReport = $request->get('endReport', '');
        $reporter = $request->get('reporter', '');
        $destin = $request->get('destinCountry', '');

        $sort = $request->get('sort', array());
        $id = strtolower($request->get('id', ''));
        $doctor = strtolower($request->get('doctor', ''));
        $patient = strtolower($request->get('patient', ''));
        $ofset = $request->get('page', 1);

        $query = $this->createQueryBuilder('a')
                ->innerJoin('a.doctor', 'd')
                ->innerJoin('a.patient', 'p')
                ->innerJoin('d.personalInformation', 'dp')
                ->innerJoin('p.personalInformation', 'pp');


        $query->select('a.id as rxId, rs.id as resolveId');
        $query->where('a.deletedOn is  null ');
        $query->andWhere('a.status not in (1,3,30,41)');
        if(strlen($state) && strlen($status)) {
            $substring =  "a.isOnHold in(1,2) or a.status=".$status;
        } else {
             if(strlen($state)){
                $substring =  "a.isOnHold in(".$state.")";
            } else {

                $substring = 'a.status='.$status;

            }
        }

        if(strlen($substring)){
            $query->andWhere($substring);
        }
        // add custome time confirm
        if(!empty($startConf) && !empty($endConf)) {
            $query->andWhere('a.paidOn between :startDate and :endDate')
                    ->setParameter('startDate', date('Y-m-d',strtotime($startConf)))
                    ->setParameter('endDate',  date('Y-m-d',strtotime($endConf)));
        }
        // add custom time report
        $query->leftJoin('a.issues', 'is')
            ->leftJoin('a.resolves', 'rs')
            ->andWhere('(rs.id is null or rs.status = 1)');
        if(!empty($startReport) && !empty($endReport)) {
            $query->andWhere($query->expr()->between('is.createdOn', date('Y-m-d',strtotime($startReport)), date('Y-m-d',strtotime($endReport))));

        }
        //reporter
        if(!empty($reporter)) {
            $query->andWhere($query->expr()->eq('is.createdBy', $reporter));

        }
        // destinatio
        if(!empty($destin)) {
            $query->leftJoin('a.shippingAddress' , 'add')
                ->join('add.city', 'city')
                ->join('city.country' , 'country')
                ->andWhere('country.id = :destination')
                ->setParameter('destination', $destin);
        }

        //value
        $query->andWhere('a.orderNumber  lIKE :id AND (LOWER(d.doctorCode) LIKE :doctor OR LOWER(CONCAT(dp.firstName,\' \',dp.lastName)) LIKE :doctor )  AND  (LOWER(CONCAT(pp.firstName,\' \',pp.lastName))  lIKE :patient OR LOWER(p.patientCode) LIKE :patient)')
                ->setParameter('id', '%' . $id . '%')
                ->setParameter('doctor', '%' . $doctor . '%')
                ->setParameter('patient', '%' . $patient . '%')   ;

        $query->groupBy('a.id, rs.id');
        $query->setMaxResults($limit)
                ->setFirstResult(($ofset - 1) * $limit);

        $this->generateSortIssue($query, $sort);

        $oders = $query->getQuery()->getResult();


        $data = $this->calculateDataIssue($oders);



        // get total
        $total = $this->createQueryBuilder('a')
                ->select('a.id')
                ->join('a.doctor', 'd')
                ->join('a.patient', 'p')
                ->join('d.personalInformation', 'dp')
                ->join('p.personalInformation', 'pp');

        $total->where('a.deletedOn is  null ');
        $total->andWhere('a.status not in  (1,3,30,41)');

        if(strlen($state) && strlen($status)) {
            $substring =  "a.isOnHold in(1,2) or a.status=".$status;
        } else {
             if(strlen($state)){
                $substring =  "a.isOnHold in(".$state.")";
            } else {

                $substring = 'a.status='.$status;

            }
        }
        $total->andWhere($substring);

        if(!empty($startConf) && !empty($endConf)) {
            $total->andWhere('a.paidOn between :startDate and :endDate')
                    ->setParameter('startDate', date('Y-m-d',strtotime($startConf)))
                    ->setParameter('endDate',  date('Y-m-d',strtotime($endConf)));
        }
        $total->leftJoin('a.issues', 'is')
            ->leftJoin('a.resolves', 'rs')
            ->andWhere('(rs.id is null or rs.status = 1)') ;
        if(!empty($startReport) && !empty($endReport)) {
            $total->andWhere($total->expr()->between('is.createdOn', date('Y-m-d',strtotime($startReport)), date('Y-m-d',strtotime($endReport))));

        }
        //reporter
        if(!empty($reporter)) {
            $total->andWhere($total->expr()->eq('is.createdBy', $reporter));

        }
            // destinatio
        if(!empty($destin)) {
            $total->join('a.shippingAddress' , 'add')
                ->join('add.city', 'city')
                ->join('city.country' , 'country')
                ->andWhere('country.id = :destination')
                ->setParameter('destination', $destin);

        }
        $total->andWhere('a.orderNumber  lIKE :id AND LOWER(d.doctorCode) LIKE :doctor AND LOWER(CONCAT(dp.firstName,\' \',dp.lastName)) LIKE :doctor   AND  LOWER(CONCAT(pp.firstName,\' \',pp.lastName))  lIKE :patient and LOWER(p.patientCode) LIKE :patient')
                ->setParameter('id', '%' . $id . '%')
                ->setParameter('doctor', '%' . $doctor . '%')
                ->setParameter('patient', '%' . $patient . '%');

        $total->groupBy('a.id, rs.id');
        $totalReCord = count($total->getQuery()->getResult());

        return array('data' => $data, 'total' => $totalReCord);

    }


     /*
     * conver data for customer care issue
     */
    private function calculateDataIssue($records) {
        $data = array();
        $arrayOrder = [];
        foreach ($records as $line) {
            $obj= $this->find($line['rxId']);
            if(!in_array($obj->getOrderNumber(),$arrayOrder)){
                array_push($arrayOrder, $obj->getOrderNumber());
            }
            if ($obj->getCreatedOn()) {
                $created = $obj->getCreatedOn()->format('d M y');
            } else {
                $created = '';
            }

            $doc = $obj->getDoctor();
            if ($doc->getPersonalInformation()) {
                $doctorName = $doc->getPersonalInformation()->getFirstName() . ' ' . $doc->getPersonalInformation()->getLastName();
            } else {
                $doctorName = '';
            }
            $doctorCode = $doc->getDoctorCode();



            $pat = $obj->getPatient();
            if ($pat->getPersonalInformation()) {
                $patientName = $pat->getPersonalInformation()->getFirstName() . ' ' . $pat->getPersonalInformation()->getLastName();
            } else {
                $patientName = '';
            }
            $patientCode = $pat->getPatientCode();


            $shippingAddress = $obj->getShippingAddress();
            $destination = '';
            if ($shippingAddress && $shippingAddress->getCity()) {
                $city = $shippingAddress->getCity();
                if (is_object($city->getState())) {
                    $destination = $city->getState()->getName() . ', ' . $city->getCountry()->getName();
                } else {
                    $destination = $city->getName() . ', ' . $city->getCountry()->getName();
                }
            }
            $statuslogs = [];
            $logs = $obj->getRxStatusLogs();
            foreach ($logs as $log) {
                $statuslogs[] = [
                    'note' => $log->getNotes(),
                    'status' => $log->getStatus(),
                    'time' => (empty($log->getCreatedOn()))? '':$log->getCreatedOn()->format(Constant::GENERAL_DATE_FORMAT),
                    'by' => $log->getCreatedBy()
                ];
            }

            $lastIssue = $obj->getIssues()->last();
            if (is_object($lastIssue)) {
                $reportDateRec = $lastIssue->getCreatedOn()->format(Constant::GENERAL_DATE_FORMAT);
                $reporterRec = $lastIssue->getCreatedBy();
            } else {
                $reportDateRec = '';
                $reporterRec = '';
            }

            //check is read
            $isRead = 1;
            $counter = $obj->getRxCounter();
            if(!empty($counter) && !empty($counter->first())&&  $counter->first()->getIsCustomerCareRead() == 0 ){
                $isRead = 0;
            }


            $data[] = [
                'id' => $obj->getId(),
                'idHash' => $obj->getId(),
                'resolveIdHash' => $line['resolveId'],
                'resolveId' => $line['resolveId'],           
                'number' => $obj->getOrderNumber(),
                'orderDate' => $created,
                'doctorCode' => $doctorCode,
                'doctorName' => $doctorName,
                'patientCode' => $patientCode,
                'patientName' => $patientName,
                'orderValue' => $obj->getOrderValue(),
                'status' => $obj->getStatus(),
                'state' => $obj->getIsOnHold(),
                'reportDate' => $reportDateRec,
                'reporter' => $reporterRec,
                'isRead' => $isRead,
                'collapse' => [
                    'invoice' => ($obj->getTaxInvoiceNo() != null) ? $obj->getTaxInvoiceNo() : '',
                    'destination' => $destination,
                    'paymentMethod' => '',
                    'activeLogs' => $statuslogs,
                ]
            ];

        }

        $dataFetch = $this->getRxPaymentLogId($arrayOrder);
        $result1 = [];
        foreach ($dataFetch as $rec) {
            $result1[$rec['orderRef']] = $rec['payMethod'];
        }

        foreach ($data as &$line) {
            $pay = '';
            if(  isset($result1[$line['number']]) && !empty($result1[$line['number']])) {
                $pay = ( $result1[$line['number']] == 'CC') ? 'VISA/MASTER' :  $result1[$line['number']];
            }

            $line['collapse']['paymentMethod'] = $pay;
        }

        return $data;
    }

    /*
     * conver data for customer care issue
     */
    private function calculateData($orders) {
        $data = array();
        $arrayOrder = [];
        foreach ($orders as $obj) {
            if ($obj->getCreatedOn()) {
                $created = $obj->getCreatedOn()->format('d M y');
            } else {
                $created = '';
            }

            $doc = $obj->getDoctor();
            if ($doc->getPersonalInformation()) {
                $doctorName = $doc->getPersonalInformation()->getFirstName() . ' ' . $doc->getPersonalInformation()->getLastName();
            } else {
                $doctorName = '';
            }
            $doctorCode = $doc->getDoctorCode();



            $pat = $obj->getPatient();
            if ($pat->getPersonalInformation()) {
                $patientName = $pat->getPersonalInformation()->getFirstName() . ' ' . $pat->getPersonalInformation()->getLastName();
            } else {
                $patientName = '';
            }
            $patientCode = $pat->getPatientCode();


            $shippingAddress = $obj->getShippingAddress();
            $destination = '';
            if ($shippingAddress && $shippingAddress->getCity()) {
                $city = $shippingAddress->getCity();
                if (is_object($city->getState())) {
                    $destination = $city->getState()->getName() . ', ' . $city->getCountry()->getName();
                } else {
                    $destination = $city->getName() . ', ' . $city->getCountry()->getName();
                }
            }
            $statuslogs = [];
            $logs = $obj->getRxStatusLogs();
            foreach ($logs as $log) {
                $statuslogs[] = [
                    'note' => $log->getNotes(),
                    'status' => $log->getStatus(),
                    'time' => (empty($log->getCreatedOn()))? '':$log->getCreatedOn()->format(Constant::GENERAL_DATE_FORMAT),
                    'by' => $log->getCreatedBy()
                ];
            }

            $lastIssue = $obj->getIssues()->last();
            if (is_object($lastIssue)) {
                $reportDateRec = $lastIssue->getCreatedOn()->format(Constant::GENERAL_DATE_FORMAT);
                $reporterRec = $lastIssue->getCreatedBy();
            } else {
                $reportDateRec = '';
                $reporterRec = '';
            }

            //check is read
            $isRead = 1;
            $counter = $obj->getRxCounter();
            if(!empty($counter) && !empty($counter->first())&&  $counter->first()->getIsCustomerCareRead() == 0 ){
                $isRead = 0;
            }


            $data[$obj->getOrderNumber()] = [
                'id' => Common::encodeHex($obj->getId()),
                'number' => $obj->getOrderNumber(),
                'orderDate' => $created,
                'doctorCode' => $doctorCode,
                'doctorName' => $doctorName,
                'patientCode' => $patientCode,
                'patientName' => $patientName,
                'orderValue' => $obj->getOrderValue(),
                'status' => $obj->getStatus(),
                'state' => $obj->getIsOnHold(),
                'reportDate' => $reportDateRec,
                'reporter' => $reporterRec,
                'isRead' => $isRead,
                'collapse' => [
                    'invoice' => ($obj->getTaxInvoiceNo() != null) ? $obj->getTaxInvoiceNo() : '',
                    'destination' => $destination,
                    'paymentMethod' => '',
                    'activeLogs' => $statuslogs,
                ]
            ];
            //   array_push($arrayOrder, $obj->getOrderNumber());
        }
        $arrayOrder = array_keys($data);
        $sql = "SELECT tx.order_ref, tx.pay_method FROM
        (SELECT * FROM `rx_payment_log` AS rx WHERE rx.`order_ref` IN('" . implode("','", $arrayOrder) . "') ORDER BY rx.`created_on` DESC ) AS tx GROUP BY tx.`order_ref`  ";

        $em = $this->getEntityManager();
        $dataFetch = $em->getConnection()->query($sql)->fetchAll();
        foreach ($dataFetch as $rec) {
            $orderNum = $rec['order_ref'];
            $pay = ($rec['pay_method'] == 'CC') ? 'VISA/MASTER' : $rec['pay_method'];
            $data[$orderNum]['collapse']['paymentMethod'] = $pay;
        }
        return array_values($data);
    }

    /*
     * Author bien
     */
    private function generateSort($em, $data) {
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'orderNumber':
                    $em->orderBy("a.orderNumber", $value);
                    break;
                case 'status':
                    $em->orderBy("a.status", $value);
                    break;
                case 'paidOn':
                    $em->orderBy("a.paidOn", $value);
                    break;
                case 'doctorCode':
                    $em->orderBy("d.doctorCode", $value);
                    break;
                case 'doctorName':
                    $em->orderBy("dp.firstName", $value);
                    $em->orderBy("dp.lastName", $value);
                    break;
                case 'patientCode':
                    $em->orderBy("p.patientCode", $value);
                    break;
                case 'patientName':
                    $em->orderBy("pp.firstName", $value);
                    $em->orderBy("pp.lastName", $value);
                    break;
                case 'orderValue':
                    $em->orderBy("a.orderValue", $value);
                    break;

            }
        }
    }
    /*
     * Author bien
     */
    private function generateSortIssue($em, $data) {
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'orderNumber':
                    $em->orderBy("a.orderNumber", $value);
                    break;
                case 'status':
                    $em->orderBy("a.status", $value);
                    break;
                case 'state':
                    $em->orderBy("a.isOnHold", $value);
                    break;


            }
        }
    }

    /**
     * get total sales by list doctors
     * @param $params
     * @return array
     * @author toan.le
     */
    public function getReportSalesByDoctors($params, $doctors, $sumFee = false, $agentIds = [])
    {
        $drugType = Constant::RX_LINE_TYPE_DRUG;
        $serviceType = Constant::RX_LINE_TYPE_SERVICE;
        $medicineMST = Constant::MST_MEDICINE;
        $serviceMST = Constant::MST_SERVICE;
        $results = [];
        try{
            $psRepository = $this->getEntityManager()
                                ->getRepository('UtilBundle:PlatformSettings');
            $psObj = $psRepository->getPlatFormSetting();
            if($sumFee){
                $selectStr = "
                        SUM(ifelse(rl.lineType = {$drugType}, rl.listPrice, 0)) + SUM(ifelse(rl.lineType = {$serviceType}, rl.listPrice, 0)) as patientFee,
                        SUM(ifelse(rl.agentMedicineFee is not null, rl.agentMedicineFee, 0) + ifelse(rl.agentServiceFee is not null, rl.agentServiceFee, 0)) as agentFee";
            }else{
                $selectStr = "
                        rx.orderNumber,
                        rx.receiptNo,
                        rx.paidOn,
                        SUM(ifelse(rl.lineType = {$drugType}, rl.listPrice, 0)) + SUM(ifelse(rl.lineType = {$serviceType}, rl.listPrice, 0)) as totalFee,
                        pi.id as perInfoId,
                        ifelse(pi.firstName is null, pi.lastName, CONCAT(pi.firstName, ' ', pi.lastName)) AS fullName,
                        d.globalId,
                        d.doctorCode,

                        SUM(ifelse(rl.lineType = {$drugType}, rl.originPrice*rl.quantity, 0)
                        ) as costMedicine,

                        SUM(ifelse(rl.lineType = {$serviceType}, rl.listPrice, 0)
                        ) as costService,

                        SUM(ifelse(rl.lineType = {$drugType}, rl.listPrice, 0)) - SUM(ifelse(rl.lineType = {$drugType}, rl.originPrice*rl.quantity, 0)) as grossMarginMedicine,

                        ifelse(rx.agentMedicineFee is not null, rx.agentMedicineFee, 0) +
                        ifelse(rx.agentServiceFee is not null, rx.agentServiceFee, 0)
                        as agentFee,

                        ifelse(rx.agent3paMedicineFee is not null, rx.agent3paMedicineFee, 0) +
                        ifelse(rx.agent3paServiceFee is not null, rx.agent3paServiceFee, 0)
                        as secondaryAgentFee,

                        ifelse(rx.agentMedicineFee is not null, rx.agentMedicineFee, 0) as marginShareCostMedicine,

                        ifelse(rx.agentServiceFee is not null, rx.agentServiceFee, 0) as marginShareCostService,

                        ifelse(rx.agent3paMedicineFee is not null, rx.agent3paMedicineFee, 0) as agent3paMedicineFee,

                        ifelse(rx.agent3paServiceFee is not null, rx.agent3paServiceFee, 0) as agent3paServiceFee,

                        rx.id,
                        agent.id as agentId,
                        secondaryAgent.id as secondaryAgentId,
                        rx.createdOn";
            }

            $queryBuilder = $this->createQueryBuilder('rx')
                                ->select($selectStr)
                                ->innerJoin('rx.doctor', 'd')
                                ->innerJoin('d.personalInformation', 'pi')
                                ->leftJoin('rx.agent', 'agent')
                                ->leftJoin('rx.secondaryAgent', 'secondaryAgent')
                                ->innerJoin('UtilBundle:RxLine', 'rl', 'WITH', 'rl.rx = rx.id')
                                ->innerJoin('rx.patient', 'p');

            if (isset($params['allSubAgentId'])) {
                $queryBuilder->Where('rx.agent = :id OR rx.agent IN (:ids) OR secondaryAgent.id IN (:agentIds)')->setParameter('id', $params['id'])->setParameter('ids', $params['allSubAgentId']);
            } else {
                if ($params['doctor_id'] != '') {
                    $queryBuilder->Where('rx.doctor = :id')->setParameter('id', $params['doctor_id']);
                } else {
                    $queryBuilder->where('rx.doctor IN (:ids)')->setParameter('ids', $doctors);
                }
            }

            $queryBuilder->andWhere('rx.paidOn is not null')
                        ->andWhere('rx.status != :failStatus')
                        ->andWhere('rx.status != :deadStatus')
                        ->andWhere('rx.status != :pfailStatus')
                        ->andWhere('rx.deletedOn is null')
                        ->andWhere('d.deletedOn is null')
                        ->setParameter('failStatus', Constant::RX_STATUS_FAILED)
                        ->setParameter('deadStatus', Constant::RX_STATUS_DEAD)
                        ->setParameter('pfailStatus', Constant::RX_STATUS_PAYMENT_FAILED)
                        ->setParameter('agentIds', $agentIds);

            if(!$sumFee){
                $queryBuilder->groupBy('rx.id');
            }

            // filter by patient type: local or oversea
            if ( isset($params['patient_type']) && $params['patient_type'] != 'all' ) {
                if ($params['patient_type'] == '1') {
                    $queryBuilder->andWhere("p.primaryResidenceCountry = :operationsCountryId")
                                 ->setParameter('operationsCountryId', $psObj['operationsCountryId']);
                } else if ($params['patient_type'] == '0') {
                    $queryBuilder->andWhere("p.primaryResidenceCountry != :operationsCountryId")
                             ->setParameter('operationsCountryId', $psObj['operationsCountryId']);
                }
            }

            //filter on: code, name
            if(isset($params['term']) && !empty($params['term'])) {
                $term = trim(strtolower($params['term']));
                $searchIn = $queryBuilder->expr()->like(
                                $queryBuilder->expr()->concat('pi.firstName', $queryBuilder->expr()->concat($queryBuilder->expr()->literal(' '), 'pi.lastName')),
                                $queryBuilder->expr()->literal( '%' . $term . '%')
                            );

                $queryBuilder
                    ->andWhere($searchIn  ." OR LOWER(d.doctorCode) LIKE :term")
                    ->setParameter('term', '%' . $term . '%');
            }

            //filter by date
            if(isset($params['from_date']) && !empty($params['from_date'])){
                $startDate = new \DateTime($params['from_date']);
                $endDate = new \DateTime($params['to_date']);
                $endDate->modify('tomorrow');
                $endDate->modify('1 second ago');
                $queryBuilder
                    ->andWhere('rx.createdOn <= :endDate AND rx.createdOn >= :startDate')
                    ->setParameter('startDate', $startDate->format("Y-m-d H:i:s"))
                    ->setParameter('endDate', $endDate->format("Y-m-d H:i:s"));
            }

            //filter by agent's fee
            if(isset($params['from_fee']) && $params['from_fee'] != ''){
                $queryBuilder
                    ->having('agentFee <= :endFee AND agentFee >= :startFee')
                    ->setParameter('startFee', $params['from_fee'])
                    ->setParameter('endFee', $params['to_fee']);
            }
            $totalResult = count($queryBuilder->getQuery()->getArrayResult());
            //sorting
            if(isset($params['sorting']) && !empty($params['sorting'])){
                $arrSort= explode("_", $params['sorting']);
                if(isset($arrSort[0]) && isset($arrSort[1])){
                    if($arrSort[0] == 'firstName' || $arrSort[0] == 'email' ){
                        $queryBuilder->orderBy('pi.'.$arrSort[0], strtoupper($arrSort[1]));
                    }elseif($arrSort[0] == 'doctorCode'){
                        $queryBuilder->orderBy('d.'.$arrSort[0], strtoupper($arrSort[1]));
                    }elseif($arrSort[0] == 'createdOn' || $arrSort[0] == 'receiptNo'){
                        $queryBuilder->orderBy('rx.'.$arrSort[0], strtoupper($arrSort[1]));
                    }else{
                        $queryBuilder->orderBy($arrSort[0], strtoupper($arrSort[1]));
                    }
                }
            } else {
                $queryBuilder->orderBy('rx.receiptNo', 'ASC');
            }

            //get page
            if($params['perPage']){
                $perPage = isset($params['perPage']) && ($params['perPage'] > 0)? $params['perPage']: 1;
                $page = isset($params['page']) && ($params['page'] > 0)? $params['page']: Constant::PAGE_DEFAULT;
                $startRecord = $perPage*$page;

                $queryBuilder
                    ->setFirstResult($startRecord)
                    ->setMaxResults($perPage);
            }

            $resultQuery =  $queryBuilder->getQuery()->getArrayResult();
            $results['success'] = true;
            $results['totalResult'] = $totalResult;
            if($params['perPage']){
                $results['totalPages'] = ceil($results['totalResult']/$params['perPage']);
            }
            $results['data'] = $resultQuery;
            // STRIKE-1114: [AGENT] - Changes under Agent Sales Report Page
            $patientFee = 0;
            $totalAgentFee = 0;
            foreach($results['data'] as $result) {
                if(!empty($result['totalFee'])) {
                    $patientFee += floatval($result['totalFee']);
                }

                if (in_array($result['secondaryAgentId'], $agentIds)) {
                    $totalAgentFee += floatval($result['secondaryAgentFee']);
                } else {
                    $totalAgentFee += floatval($result['agentFee']);
                }
            }
            $results['totalFee'] = array('agentFee' => $totalAgentFee, 'patientFee' => $patientFee);
            // End STRIKE-1114: [AGENT] - Changes under Agent Sales Report Page
            if(null == $resultQuery) {
                $results['message'] = MsgUtils::generate('msgNoData');
            } else {
                $results['message'] = null;
            }
        }catch(Exception $ex){
            $results['message'] = $ex->getMessage();
            $results['success'] = false;
        }
        return $results;
    }

    /**
     * get monthly statement report
     * @param  array $params
     * @author  toan.le
     * @return
     */
    public function getAgentMonthlyStatementReport($params, $doctors, $agentIds) {
        $agentType = Constant::USER_TYPE_AGENT;
        $drugType = Constant::RX_LINE_TYPE_DRUG;
        $serviceType = Constant::RX_LINE_TYPE_SERVICE;
        $refundStatusSuccess = Constant::REFUND_STATUS_SUCCESS;
        $refundType          = Constant::PAYMENT_TYPE_REFUND;

        $queryBuilder = $this->createQueryBuilder('r');
        $selectStr = " STR_TO_DATE(CONCAT(YEAR(r.createdOn), '-', MONTH(r.createdOn), '-', '01'), '%Y-%c-%e %r' ) as monthly,
                        SUM(ifelse(pl.status IS NULL
                              OR pl.status != '{$refundStatusSuccess}', ifelse(rl.lineType = {$drugType}, rl.listPrice, 0) + ifelse(rl.lineType = {$serviceType}, rl.listPrice, 0) , 0) ) AS totalFee,
                       SUM(ifelse(pl.status IS NULL
                              OR pl.status != '{$refundStatusSuccess}', ifelse(rl.agentMedicineFee IS NOT NULL, rl.agentMedicineFee,0) + ifelse(rl.agentServiceFee IS NOT NULL, rl.agentServiceFee,0) , 0) ) AS agentFee,
                        ps.status as status,
                        ps.datePaid as datePaid
                       ";
        
        if (isset($params['allSubAgentId'])) {
            $queryBuilder->select($selectStr)
                    ->innerJoin('r.doctor', 'd')
                    ->innerJoin('UtilBundle:RxLine', 'rl', 'WITH', 'rl.rx = r.id')
                    ->leftJoin('UtilBundle:PaymentStatus', 'ps', 'WITH', "ps.userId = r.doctor AND ps.userType = {$agentType} AND MONTH(r.createdOn) = MONTH(ps.datePaid)")
                    ->leftJoin('UtilBundle:RxPaymentLog', 'pl', 'WITH', "r.id = pl.rx and  pl.status = '{$refundStatusSuccess}' and pl.paymentType = '{$refundType}'")
                    ->where('r.agent = :id OR r.agent IN (:ids) OR r.secondaryAgent IN (:agentIds)')
                    ->andWhere('r.paidOn is not null')
                    ->andWhere('d.deletedOn is null')
                    ->andWhere('r.status != :failStatus')
                    ->andWhere('r.status != :deadStatus')
                    ->andWhere('r.status != :pfailStatus')
                    ->andWhere('r.deletedOn is null')
                    ->setParameter('id', $params['id'])
                    ->setParameter('ids', $params['allSubAgentId'])
                    ->setParameter('agentIds', $agentIds)
                    ->setParameter('failStatus', Constant::RX_STATUS_FAILED)
                    ->setParameter('deadStatus', Constant::RX_STATUS_DEAD)
                    ->setParameter('pfailStatus', Constant::RX_STATUS_PAYMENT_FAILED);
        } else {
            $queryBuilder->select($selectStr)
                    ->innerJoin('r.doctor', 'd')
                    ->innerJoin('UtilBundle:RxLine', 'rl', 'WITH', 'rl.rx = r.id')
                    ->leftJoin('UtilBundle:PaymentStatus', 'ps', 'WITH', "ps.userId = r.doctor AND ps.userType = {$agentType} AND MONTH(r.createdOn) = MONTH(ps.datePaid)")
                    ->leftJoin('UtilBundle:RxPaymentLog', 'pl', 'WITH', "r.id = pl.rx and  pl.status = '{$refundStatusSuccess}' and pl.paymentType = '{$refundType}'")
                    ->leftJoin('r.agent', 'agent')
                    ->leftJoin('r.secondaryAgent', 'secondaryAgent')
                    ->where('r.doctor IN (:ids)')
                    ->andWhere('r.paidOn is not null')
                    ->andWhere('d.deletedOn is null')
                    ->andWhere('r.status != :failStatus')
                    ->andWhere('r.status != :deadStatus')
                    ->andWhere('r.status != :pfailStatus')
                    ->andWhere('r.deletedOn is null')
                    ->andWhere('agent.id IS NULL OR agent.id IN (:agentIds) OR secondaryAgent.id IN (:agentIds)')
                    ->setParameter('ids', $doctors)
                    ->setParameter('failStatus', Constant::RX_STATUS_FAILED)
                    ->setParameter('deadStatus', Constant::RX_STATUS_DEAD)
                    ->setParameter('pfailStatus', Constant::RX_STATUS_PAYMENT_FAILED)
                    ->setParameter('agentIds', $agentIds);
        }

        $queryBuilder->groupBy("monthly");

        //filter by date
        if(isset($params['from_date']) && isset($params['to_date']) && !empty($params['from_date']) && !empty($params['to_date'])){
            $startDate = new \DateTime($params['from_date']);
            $startDate->modify('first day of this month');
            $startDate->modify('midnight');

            $endDate = new \DateTime($params['to_date']);
            $endDate->modify('last day of this month');
            $endDate->modify('tomorrow');
            $endDate->modify('1 second ago');
        }elseif(isset($params['from_date']) && !empty($params['from_date'])){
            $startDate = new \DateTime($params['from_date']);
            $startDate->modify('first day of this month');
            $startDate->modify('midnight');

            $endDate = new \DateTime();
            $endDate->modify('last day of previous month');
            $endDate->modify('tomorrow');
            $endDate->modify('1 second ago');
        }elseif(isset($params['to_date']) && !empty($params['to_date'])){
            $startDate = new \DateTime();
            $startDate->modify('first day of Jan');
            $startDate->modify('midnight');

            $endDate = new \DateTime($params['to_date']);
            $endDate->modify('first day of this month');
            $endDate->modify('midnight');
        }else{
            $startDate = new \DateTime();
            $startDate->modify('first day of Jan');
            $startDate->modify('midnight');

            $endDate = new \DateTime();
            $endDate->modify('last day of previous month'); // this is the right line code
            //$endDate->modify('last day of next month'); // this line for testing purpose
            $endDate->modify('tomorrow');
            $endDate->modify('1 second ago');
        }

        $em                  = $this->getEntityManager();
        $platformSetting     = $em->getRepository('UtilBundle:PlatformSettings')->getPlatFormSetting();
        $statementDateNumber = $platformSetting['agentStatementDate'];
        if ((int)date('d') <= $statementDateNumber) {
            $endDate->modify('last day of previous month');
        }

        $queryBuilder
            ->andWhere('r.createdOn <= :end AND r.createdOn >= :start')
            ->setParameter('start', $startDate->format("Y-m-d H:i:s"))
            ->setParameter('end', $endDate->format("Y-m-d H:i:s"));


        //sorting
        if(isset($params['sorting']) && !empty($params['sorting'])){
            $arrSort= explode("_", $params['sorting']);
            if(isset($arrSort[0]) && isset($arrSort[1])){
                $queryBuilder->orderBy($arrSort[0], strtoupper($arrSort[1]));
            }
        }

        $perPage = ($params['perPage'] > 0)? $params['perPage']: Constant::PER_PAGE_DEFAULT;
        $page = ($params['page'] > 0)? $params['page']: Constant::PAGE_DEFAULT;
        $startRecord = $perPage*$page;

        $totalResult = count($queryBuilder->getQuery()->getArrayResult());

        $queryBuilder
            ->setFirstResult($startRecord)
            ->setMaxResults($perPage);


        $result = $queryBuilder->getQuery()->getArrayResult();
        return array(
            'totalResult' => $totalResult,
            'totalPages' => ceil($totalResult/$perPage),
            'data' => $result
        );

    }

    /**
     * get data for chart
     * @param $params
     * @return array
     * @author toan.le
     */
    public function getAgentDataChart($agentId, $doctors, $useAgent = array(), $agentIds = []){

        // find all order from rx table by $doctorId
        $queryBuilder = $this->createQueryBuilder('rx');

        $selectStr = "
                    CONCAT(MONTHNAME(STR_TO_DATE(MONTH(rx.createdOn), '%m')),' ',YEAR(rx.createdOn)) as monthly,
                    SUM(ifelse(rl.listPrice is not null, rl.listPrice, 0)) AS totalSales,
                    ifelse(rx.agentMedicineFee IS NOT NULL, rx.agentMedicineFee, 0) + ifelse(rx.agentServiceFee IS NOT NULL, rx.agentServiceFee, 0) AS agentFee, 
                    ifelse(rx.agent3paMedicineFee IS NOT NULL, rx.agent3paMedicineFee, 0) + ifelse(rx.agent3paServiceFee IS NOT NULL, rx.agent3paServiceFee, 0) as secondaryAgentFee, agent.id as agentId, secondaryAgent.id as secondaryAgentId, rx.id
                    ";
        if (isset($useAgent['useAgent'])) {
            $queryBuilder->select($selectStr)
                    ->innerJoin('rx.doctor', 'd')
                    ->innerJoin('UtilBundle:RxLine', 'rl', 'WITH', 'rl.rx = rx.id')
                    ->leftJoin('rx.agent', 'agent')
                    ->leftJoin('rx.secondaryAgent', 'secondaryAgent')
                    ->where('rx.agent = :id OR rx.agent IN (:ids) OR secondaryAgent.id IN (:agentIds)')
                    ->andWhere('rx.paidOn is not null')
                    ->andWhere('d.deletedOn is null')
                    ->andWhere('rx.status != :failStatus')
                    ->andWhere('rx.status != :deadStatus')
                    ->andWhere('rx.status != :pfailStatus')
                    ->andWhere('rx.deletedOn is null')
                    ->setParameter('id', $agentId)
                    ->setParameter('ids', $useAgent['allSubAgentId'])
                    ->setParameter('agentIds', $agentIds)
                    ->setParameter('failStatus', Constant::RX_STATUS_FAILED)
                    ->setParameter('deadStatus', Constant::RX_STATUS_DEAD)
                    ->setParameter('pfailStatus', Constant::RX_STATUS_PAYMENT_FAILED);
        } else {
            $queryBuilder->select($selectStr)
                    ->innerJoin('rx.doctor', 'd')
                    ->innerJoin('UtilBundle:RxLine', 'rl', 'WITH', 'rl.rx = rx.id')
                    ->leftJoin('rx.agent', 'agent')
                    ->leftJoin('rx.secondaryAgent', 'secondaryAgent')
                    ->where('rx.doctor IN (:ids)')
                    ->andWhere('rx.paidOn is not null')
                    ->andWhere('d.deletedOn is null')
                    ->andWhere('rx.status != :failStatus')
                    ->andWhere('rx.status != :deadStatus')
                    ->andWhere('rx.status != :pfailStatus')
                    ->andWhere('rx.deletedOn is null')
                    ->andWhere('agent.id IS NULL OR agent.id IN (:agentIds) OR secondaryAgent.id IN (:agentIds)')
                    ->setParameter('ids', $doctors)
                    ->setParameter('agentIds', $agentIds)
                    ->setParameter('failStatus', Constant::RX_STATUS_FAILED)
                    ->setParameter('deadStatus', Constant::RX_STATUS_DEAD)
                    ->setParameter('pfailStatus', Constant::RX_STATUS_PAYMENT_FAILED);
        }

        $queryBuilder->groupBy("rx.id");

        //filter by date
        $startDate = new \DateTime('midnight first day of 11 month ago');

        // STRIKE 1257
        $agentObject = $this->getEntityManager()
            ->getRepository('UtilBundle:Agent')->find($agentId);
        $createdOn = $agentObject->getCreatedOn();
        if ($createdOn > $startDate) {
            $startDate = $createdOn;
        }

        $endDate = clone $startDate;
        $endDate->modify('last day of +11 month');

        $queryBuilder
            ->andWhere('rx.createdOn <= :end AND rx.createdOn >= :start')
            ->setParameter('start', $startDate->format("Y-m-d H:i:s"))
            ->setParameter('end', $endDate->format("Y-m-t H:i:s"));
        $queryBuilder->orderBy("rx.createdOn", "ASC");

        $result = $queryBuilder->getQuery()->getArrayResult();

        $data = [];
        while ($startDate <= $endDate) {
            $temp['monthly'] = $startDate->format('M y');
            $temp['totalSales'] = 0;
            $temp['agentFee'] = 0;
            $data[] = $temp;
            $startDate->modify('next month');
        }

        foreach ($result as $month) {
            $monthly = new \DateTime($month['monthly']);
            $month['monthly'] = $monthly->format('M y');

            foreach ($data as $key => $val) {
                if($month['monthly'] == $val['monthly']) {
                    $val['totalSales'] += $month['totalSales'];
                    if (in_array($month['secondaryAgentId'], $agentIds)) {
                        $val['agentFee'] += $month['secondaryAgentFee'];
                    } else {
                        $val['agentFee'] += $month['agentFee'];
                    }
                }
                $data[$key] = $val;
            }
        }

        if ($agentObject->getIsGst() == true) {
            $platforms = $this->getEntityManager()->getRepository('UtilBundle:PlatformSettings')->findAll();
            $gstRate = 0;
            if (!empty($platforms)) {
                $platforms = $platforms[0];
                $gstRate = $platforms->getGstRate();
            }

            foreach ($data as &$value) {
                $feeGst = $value['agentFee'] * $gstRate/100;
                $feeGst = round($feeGst, 2);
                $value['agentFee'] += $feeGst;
            }
        }

        return array(
            'data' => $data
        );
    }
    /*
     * get refund credit note url
     */
    public function getRefundCreditNote($id){
        $queryBuilder = $this->createQueryBuilder('r');
        $url = $queryBuilder->innerJoin('r.resolves', 'resolve')
            ->innerJoin('resolve.resolveRefunds','rsRefund')
            ->innerJoin('rsRefund.creditNotes','note')
            ->select('note.url as url')
            ->where($queryBuilder->expr()->eq('r.id',$id))
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        return $url;
    }

    public function getTransactions()
    {
        $qb = $this->createQueryBuilder('rx');
        $qb->leftJoin('UtilBundle:TransactionListing', 'tl', 'WITH', 'rx.id = tl.rxId');
        $qb->where("rx.deletedOn IS NULL");
        $qb->andWhere("rx.paidOn IS NOT NULL");
        $qb->andWhere("(tl.id IS NULL OR tl.doctorInvoiceNumberFromGmedes IS NULL)");

        return $qb->select("rx.id, tl.id AS report_id")->getQuery()->execute();
    }


    /**
     * collect info from rx for settlement report
     * @param  string $day settlement day
     * @author thu.tranq
     * @return array
     */
    public function getReddotSettlementReport($day, &$listPHDate) {
        //get holiday list
        $publicHoliday = $this
            ->getEntityManager()
            ->getRepository('UtilBundle:PublicHoliday')->listPHDates();

        foreach ($publicHoliday as $value) {
            $listPHDate[] = $value['publicDate'];
        }
        $transactionDate = empty($day) ? new \DateTime() : new \DateTime($day);
        $i = 1;
        do {
            $transactionDate->modify('-1 day');
            $dayNum = (int)$transactionDate->format('N');
            if (!in_array($dayNum, array(6, 7)) && !in_array($transactionDate, $listPHDate)) {
                $i ++;
            }
        } while ($i <= 4); // the number 4 is defined by strike-700

        $qb = $this->createQueryBuilder('rx');
        $qb->select("rx.orderNumber,
                    rx.paidOn as transactionDate,
                    (rx.orderValue - (rx.paymentGatewayFeeFixed + rx.paymentGatewayFeeBankMdr)) as expectAmount,
                    '' as settlementAmount,
                    rx.orderValue as transactionGrossAmount,
                    '' as settlementType,
                    '' as remark,
                    rpl.transactionId as transactionRef,
                    rpl.payMethod paymentMethod,
                    '' as status
                    ")
            ->innerJoin('UtilBundle:RxPaymentLog', 'rpl', 'WITH', 'rpl.orderRef = rx.orderNumber')
            ->where("rx.paidOn is not null and date_format(rx.paidOn, '%Y-%m-%d') = :transactionDate")
            ->andWhere('rx.paymentGate = :paymentGate')
            ->setParameter('transactionDate', $transactionDate->format('Y-m-d'))
            ->setParameter('paymentGate', Constant::PAYMENT_GATE_REDDOT)
            ;

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * Get shipping label
     * @param $orderNumber
     */
    public function getShippingLabel($orderNumber)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('rx.id,
            rx.orderNumber,
            rx.orderPhysicalNumber,
            rx.isColdChain,
            d.id as doctorId,
            a.line1,
            a.line2,
            a.line3,
            a.postalCode,
            cos.name as countryName
            ')
            ->from('UtilBundle:Rx', 'rx')
            ->leftJoin('rx.doctor', 'd')
            ->leftJoin('rx.shippingAddress', 'a')
            ->innerJoin('a.city', 'cs')
            ->innerJoin('cs.country', 'cos')
            ->where('rx.orderPhysicalNumber = :orderNumber AND rx.paidOn is not null')
            ->setParameter('orderNumber', $orderNumber)
        ;

        $info = $qb->getQuery()->getOneOrNullResult();

        if($info == null)
            return null;

        //get clinic
        $qb3 = $this->getEntityManager()->createQueryBuilder();
        $qb3->select('c.businessName, c.businessLogoUrl, c.isPrimary')
            ->addSelect('a.line1, a.line2, a.line3, a.postalCode')
            ->addSelect('ci.name as city, st.name as state, co.name as country')
            ->from('UtilBundle:Clinic', 'c')
            ->leftJoin('c.businessAddress', 'b')
            ->leftJoin('b.address', 'a')
            ->leftJoin('a.city', 'ci')
            ->leftJoin('ci.state', 'st')
            ->leftJoin('ci.country', 'co')
            ->where('c.doctor = :doctorId')
            ->andWhere('c.deletedOn IS NULL')
            ->setParameter('doctorId', $info['doctorId'])
        ;

        $primaryClinic = clone $qb3;
        $primaryClinic->andWhere("c.isPrimary = true")->setMaxResults(1);
        $clinicResult = $primaryClinic->getQuery()->getOneOrNullResult();

        $subClinic = clone $qb3;
        $subClinic->andWhere("c.isPrimary is null");
        $subClinicResult = $subClinic->getQuery()->getArrayResult();

        if(empty($clinicResult) && !empty($subClinicResult))
            $clinicResult = $subClinicResult[0];

        return array(
            'info'  => $info,
            'clinic' => $clinicResult,
            'subClinic' => $subClinicResult
        );
    }

    public function formatMPADraftList($data)
    {
        $em = $this->getEntityManager();

        foreach ($data as $value) {
            $rxObj = $value[0];
            if (empty($rxObj)) {
                continue;
            }

            $rxId = $rxObj->getId();
            $rxLines = $rxObj->getRxLines();

            foreach ($rxLines as $line) {
                $drug = $line->getDrug();
                if (empty($drug)) {
                    $rxObj->doctorFee = $line->getCostPrice();
                    continue;
                }
                $line->detail = $this->formatDrugData($drug);
                $line->detail['quantity'] = $line->getQuantity();
            }

            $rxObj->refill = $this->getRXRefillReminder(['rxId' => $rxId]);
            $rxObj->rxAmendments = $em->getRepository('UtilBundle:RxLine')->getRxLineAmendments(null, $rxId);

            $rxStatus = $rxObj->getStatus();
            if (Constant::RX_STATUS_DRAFT == $rxStatus) {
                $rxObj->draft = true;
            } else if (Constant::RX_STATUS_FOR_DOCTOR_REVIEW == $rxStatus) {
                $rxObj->forDoctorReview = true;
            } else if (Constant::RX_STATUS_FOR_AMENDMENT == $rxStatus) {
                $rxObj->forAmendment = true;
            }
        }
    }

    public function getFutureRxOrders()
    {
        $today = date('Y-m-d');
        $qb = $this->createQueryBuilder('rx')
            ->where('rx.isScheduledRx = 1')
            ->andWhere('rx.scheduledSendDate = :today')
            ->andWhere('rx.scheduledSentOn IS NULL')
            ->andWhere('rx.deletedOn IS NULL')
            ->setParameter('today', $today);

        return $qb->getQuery()->getResult();
    }
}
