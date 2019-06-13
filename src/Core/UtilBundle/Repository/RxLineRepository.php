<?php

namespace UtilBundle\Repository;

use Doctrine\ORM\EntityRepository;
use UtilBundle\Utility\Constant;

/**
 * RxDrugRepository
 * Author Luyen Nguyen
 * Date: 08/14/2017
 */
class RxLineRepository extends EntityRepository {

    /**
     * Get list drugs of rx
     * @param $rx_id
     * @author Luyen Nguyen
     */
    public function getRxLines($rx_id) {
        $queryBuilder = $this->createQueryBuilder('f');
        $queryBuilder
                ->where('f.rx = (:rx_id)')
                ->setParameter('rx_id', $rx_id);
        $resultQuery = $queryBuilder->getQuery()->getResult();
        return $resultQuery;
    }

    public function getRxLineAmendments($doctorId, $rxId = null)
    {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder()
            ->select('amend')
            ->from('UtilBundle:RxLineAmendment', 'amend')
            ->innerJoin('amend.rxLine', 'line')
            ->innerJoin('line.rx', 'rx')
            ->where('rx.status = :status')
            ->setParameter('status', Constant::RX_STATUS_FOR_AMENDMENT)
            ->orderBy('amend.createdOn', 'desc');

        if ($doctorId) {
            $queryBuilder->andWhere('rx.doctor = :doctor')
                ->setParameter('doctor', $doctorId);
        }

        if ($rxId) {
            $queryBuilder->andWhere('rx = :rxId')
                ->setParameter('rxId', $rxId);
        }

        $list = $queryBuilder->getQuery()->getResult();

        $result = [];
        foreach ($list as $value) {
            $rxLine = $value->getRxLine();
            if (empty($rxLine)) {
                continue;
            }

            $rx = $rxLine->getRx();
            if (empty($rx)) {
                continue;
            }

            $patient = $rxLine->getRx()->getPatient();
            if (empty($patient)) {
                continue;
            }

            $personal = $patient->getPersonalInformation();
            $name = $personal->getFullName();

            $drug = $rxLine->getDrug();
            if (empty($drug)) {
                continue;
            }

            $drugName = $drug->getName();

            $temp = [];
            $temp['drugName'] = $drugName;
            $temp['text'] = $value->getAmendment();

            $result[$rx->getId()]['name'] = $name;
            $result[$rx->getId()]['amendment'][] = $temp;
        }

        foreach ($result as $key => $value) {
            $criteria = ['rx' => $key];
            $rxNote = $em->getRepository('UtilBundle:RxNote')->findOneBy($criteria);
            if (empty($rxNote)) {
                continue;
            }
            $result[$key]['rxNote'] = $rxNote->getNote();
            $result[$key]['createdOn'] = $rxNote->getCreatedOn();
        }

        return $result;
    }

}
