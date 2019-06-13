<?php

namespace UtilBundle\Repository;

use Doctrine\ORM\EntityRepository;
use UtilBundle\Utility\Constant;

class GstCodeFeeRepository extends EntityRepository
{
    /**
     * update gst code fee
     * @author vinh.nguyen
     */
    public function update($feeCode, $gstCode)
    {
        $gcfObj = $this->findOneBy(array('feeCode' => $feeCode));
        if($gcfObj == null) {
            return null;
        }
        $gcfObj->setGstCode($gstCode);

        $em = $this->getEntityManager();
        $em->persist($gcfObj);
        $em->flush();

        return $gcfObj;
    }

    /**
     * update list
     * @author vinh.nguyen
     */
    public function updateBy($list)
    {
        foreach($list as $feeCode=>$gstCode) {
            $this->update($feeCode, $gstCode);
        }
    }

    /**
     * get gst code fee
     * @author vinh.nguyen
     */
    public function getAll()
    {
        $qb = $this->createQueryBuilder('g');
        $qb->select('g.id, g.feeCode, g.gstCode, g.description, g.updatedOn');

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * Get Gst Code Fee
     * author luyen nguyen
     */
    public function getGstCodeFee() {
        $qb = $this->createQueryBuilder('g');
        $qb->select('g.feeCode, g.gstCode')
            ->where($qb->expr()->orX(
                $qb->expr()->eq('g.feeCode', ':ccaf_sg'),
                $qb->expr()->eq('g.feeCode', ':ccaf_id'),
                $qb->expr()->eq('g.feeCode', ':sgcig_pf'),
                $qb->expr()->eq('g.feeCode', ':pgb_gst'),
                $qb->expr()->eq('g.feeCode', ':sf_gmedes')
            ))
            ->setParameter('ccaf_sg', Constant::CCAF_SG)
            ->setParameter('ccaf_id', Constant::CCAF_ID)
            ->setParameter('sgcig_pf', Constant::SGCIG_PF)
            ->setParameter('pgb_gst', Constant::PGB_GST)
            ->setParameter('sf_gmedes', Constant::SF_GMEDES);
        $results = $qb->getQuery()->getArrayResult();
        $arrayresults = array();
        foreach($results as $result) {
            $arrayresults[$result['feeCode']] = $result['gstCode'];
        }
        return $arrayresults;
    }

}