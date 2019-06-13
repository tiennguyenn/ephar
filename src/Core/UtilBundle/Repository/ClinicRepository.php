<?php

namespace UtilBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ClinicRepository
 * Author Luyen Nguyen
 * Date: 08/14/2017
 */
class ClinicRepository extends EntityRepository {

    /**
     * Get detail clinic
     * @param $doctor_id
     * @author Luyen Nguyen
     */
    public function getClinic($doctor_id) {
        $queryBuilder = $this->createQueryBuilder('f');
        // clinics and doctor phone
        $queryBuilder->select('f as clinic, p as doctorPhone')
                ->innerJoin('UtilBundle:DoctorPhone', 'dp', 'WITH', 'dp.doctor = (:doctor_id)')
                ->innerJoin('UtilBundle:Phone', 'p', 'WITH', 'p.id = dp.contact')
                ->where('f.doctor = (:doctor_id)')
                ->andWhere('f.deletedOn IS NULL')
                ->orderBy('f.isPrimary', 'DESC')
                ->setParameter('doctor_id', $doctor_id);
        $results = $queryBuilder->getQuery()->getResult();
        $clinics = array();
        $doctorPhone = '';
        foreach ($results as $result) {
            if (isset($result['clinic'])) {
                array_push($clinics, $result['clinic']);
            } else if (isset($result['doctorPhone'])) {
                $doctorPhone = $result['doctorPhone'];
            }
        }
        $arrResult = array("clinics" => $clinics, "doctorPhone" => $doctorPhone);
        return $arrResult;
    }

}
