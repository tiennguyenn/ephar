<?php

namespace UtilBundle\Repository;

use Doctrine\ORM\EntityRepository;
use UtilBundle\Utility\Constant;

/**
 * Sms Send
 */
class SmsSendRepository extends EntityRepository
{
    /**
    * update 
    */
    public function update($params=array())
    {
        $smsObj = $this->find($params['id']);
        if(null == $smsObj)
            return null;
        
        $smsObj->setSentOn(new \DateTime());
        
        if(isset($params['counter']))
            $smsObj->setCounter($params['counter']);
        
        if(isset($params['status']))
            $smsObj->setStatus($params['status']);
        
        $em = $this->getEntityManager();
        $em->persist($smsObj);
        $em->flush();

        return $smsObj->getId();
    }
    
    /**
     * get list send
     * @author vinh.nguyen
     */
    public function getListBy($params = array())
    {
        $em = $this->getEntityManager()->createQueryBuilder();
        $qb = $em->select('s.id, 
                s.from,
                s.to, 
                s.content,
                s.createdOn,
                s.timeToSend,
                s.sentOn,
                (case when s.counter is null then 0 else s.counter end) as counter,
                s.status')
            ->from('UtilBundle:SmsSend', 's')
            ->where('s.timeToSend <= :timeToSend 
                AND (s.status is null OR s.status != :status)
                AND (s.counter is null OR s.counter < :counterMax)
                ')
            ->setParameter('timeToSend', $params['timeToSend'])
            ->setParameter('status', $params['status'])
            ->setParameter('counterMax', $params['counterMax']);

        return $qb->getQuery()->getArrayResult();
    }
}