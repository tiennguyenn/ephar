<?php

namespace UtilBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Patient Medication Allergy
 * Author Luyen Nguyen
 * Date: 08/14/2017
 */
class PatientMedicationAllergyRepository extends EntityRepository
{   
    /**
     * Get Patient Medication Allergy
     * @param $patient_id
     * @author Luyen Nguyen
     */
    public function getPatientMedicationAllergy($patient_id) {
        $queryBuilder = $this->createQueryBuilder('f');
        $queryBuilder->select('f.medicationAllergy')
                ->where('f.patient = (:patient_id)')
                ->setParameter('patient_id', $patient_id);
        $resultQuery = $queryBuilder->getQuery()->getResult();                
        return $resultQuery;
    }
}