<?php

namespace UtilBundle\Repository;

use Doctrine\ORM\EntityRepository;
use UtilBundle\Utility\Constant;

class PlatformSettingGstCodeRepository extends EntityRepository
{
    /**
     * get feecode
     * @author  thu.tranq
     * @return array
     */
    public function getAll()
    {
        $qb = $this->createQueryBuilder('g');
        $data = $qb->getQuery()->getResult();
        $result = array();
        foreach ($data as $value) {
            $result[] = array('feeCode' => $value->getFeeCode(), 'gstCode' => $value->getGstCode()->getId());
        }

        return $result;
    }

    /**
     * get platform gst code
     * @return array
     */
    public function getPlaformGSTCode()
    {
        $qb = $this->createQueryBuilder('g');
        $data = $qb->getQuery()->getResult();

        $result = array();
        foreach ($data as $value) {
            $feeCode = $value->getFeeCode();
            $result[$feeCode] = $value->getGstCode()->getCode();
        }

        return $result;
    }
}