<?php

namespace UtilBundle\Repository;

use Doctrine\ORM\EntityRepository;

class GstCodeRepository extends EntityRepository
{
    /**
     * get list gst code
     * @return array
     */
    public function getAll()
    {
        $qb = $this->createQueryBuilder('g');
        $qb->select('g.id, g.code, g.description');

        return $qb->getQuery()->getArrayResult();
    }

}