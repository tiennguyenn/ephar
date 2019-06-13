<?php

namespace UtilBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Patient Phone Repository
 * Author Luyen Nguyen
 * Date: 08/22/2017
 */
class PatientPhoneRepository extends EntityRepository {

    /**
     * Get patient phone
     * @param $patient_id
     * @author Luyen Nguyen
     */
    public function getPatientPhone($patient_id) {
        $queryBuilder = $this->createQueryBuilder('f');
        $queryBuilder->select('p as patientPhone')
                ->innerJoin('UtilBundle:Phone', 'p', 'WITH', 'p.id = f.phone')
                ->where('f.patient = (:patient_id)')
                ->setParameter('patient_id', $patient_id);
        $result = $queryBuilder->getQuery()->getOneOrNullResult();        
        return $result;
    }

}
