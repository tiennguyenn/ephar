<?php

namespace UtilBundle\Repository;

use Doctrine\ORM\EntityRepository;
use UtilBundle\Entity\IndonesiaTax;

/**
 * IndonesiaTaxRepository
 */
class IndonesiaTaxRepository extends EntityRepository {

    /**
     * get platform setting
     * @author  thu.tranq
     * @return array
     */
    public function getImportTaxs() {
        $queryBuilder = $this->createQueryBuilder('it');
        $queryBuilder->select(
            'it.taxName',
            'it.taxValue'
        );
        return $queryBuilder->getQuery()->getArrayResult();
    }
}