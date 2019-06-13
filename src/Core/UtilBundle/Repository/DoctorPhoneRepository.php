<?php

namespace UtilBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Doctor Phone Repository
 * Author Luyen Nguyen
 * Date: 08/14/2017
 */
class DoctorPhoneRepository extends EntityRepository {

    /**
     * Get doctor phone
     * @param $doctor_id
     * @author Luyen Nguyen
     */
    public function getDoctorPhone($doctor_id) {
        $queryBuilder = $this->createQueryBuilder('f');
        $queryBuilder
                ->where('f.doctor = (:id)')
                ->setParameter('id', $doctor_id);
        $resultQuery = $queryBuilder->getQuery()->getSingleResult();
        return $resultQuery;
    }

    public function getPhoneByDoctor($id) {
        $queryBuilder = $this->createQueryBuilder('f')
                ->select('p.number', 'c.name as country')
                ->innerJoin('f.contact', 'p')
                ->innerJoin('p.country', 'c')
                ->where('f.doctor = :id')
                ->setParameter('id', $id)
                ->setFirstResult(0)
                ->setMaxResults(100);

        $resultQuery = $queryBuilder->getQuery()->getArrayResult();
        $totalResult = count($resultQuery);

        return array(
            'totalResult' => $totalResult,
            'data' => ($totalResult == 1) ? $resultQuery[0] : $resultQuery
        );
    }

}
