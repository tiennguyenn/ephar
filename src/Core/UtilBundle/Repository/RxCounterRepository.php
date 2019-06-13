<?php

namespace UtilBundle\Repository;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\DBAL\Connection;
use Doctrine\Common\Collections\Criteria;
use UtilBundle\Entity\RxCounter;
use UtilBundle\Utility\Constant;

/**
 * RxCounterRepository
 */
class RxCounterRepository extends EntityRepository
{
    /**
     * count
     * @author bien
     * @return
     */
    public function getCountUpdatedAndUnReadCustomerCare($type = 1){

        $queryBuilder = $this->createQueryBuilder('f')
            ->select('count(rx.id)')
            ->join('f.rx', 'rx');
        $queryBuilder->where('f.isCustomerCareRead = 0 ');
        if($type == 1) {
            $queryBuilder->andWhere('rx.deletedOn is  null ');
            $queryBuilder->andWhere('rx.status not in (1,30,41,3)');
        }
        if($type == 2) {
            $queryBuilder->andWhere('rx.deletedOn is  null ');
            $queryBuilder->andWhere('rx.status not in (1,30,41,3)');
            $queryBuilder->andWhere("rx.isOnHold in(1,2) or rx.status=8");
        }
        $result = $queryBuilder->getQuery()->getSingleScalarResult();
        return $result;
    }
    /**
     * count
     * @author bien
     * @return
     */
    public function getCountUpdatedAndUnReadDocotor($doctorId,$type ){

        $queryBuilder = $this->createQueryBuilder('f')
            ->select('count(rx.id)')
            ->join('f.rx', 'rx')
            ->join('rx.doctor', 'd');
        $queryBuilder->where('d.id ='.$doctorId)
                    ->andWhere('f.isDoctorRead = 0 ')
                    ->andWhere('rx.deletedOn is  null ');
        if($type == 2) {

            $queryBuilder->andWhere('rx.status not in (41)');
        }
        if($type == 3) {
            $queryBuilder->andWhere('rx.status =3')->andWhere('rx.isOnHold not in (1)');
        }
        if($type == 4) {
            $queryBuilder->andWhere('rx.status in (4,6,9,13,14,15,16,17)');
        }
        if($type == 5) {
            $queryBuilder->andWhere('rx.status = 3');
            $queryBuilder->andWhere("rx.isOnHold = 1");
        }
        if($type == 6) {
            $queryBuilder->andWhere("rx.isOnHold = 1");
            $queryBuilder->andWhere('rx.status not in (41,3)');
        }
        $result = $queryBuilder->getQuery()->getSingleScalarResult();
        return $result;
    }

    /**
     * get rx for customer care
     **/
    public function getCountRxPharmacist($type = 1)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $query = $qb->select('count(rx.id)')
            ->from('UtilBundle:Rx', 'rx')
            ->where('rx.deletedOn is  null ')
            ->andWhere('rx.status not in (1,3,30,41)');

        switch ($type) {
            //redispense
            case 1:
                $query->andWhere($query->expr()->in('rx.redispensingStatus',[Constant::REDISPENSE_STATE_STARTED,Constant::REDISPENSE_STATE_REVIEWED,Constant::REDISPENSE_STATE_PREVIEW_MEDICINE]));
                break;
            //completed
            case 2 :
                $query->andWhere('rx.redispensingStatus = :status')
                    ->setParameter('status', Constant::REDISPENSE_STATE_COMPLETE);
                break;

        }
        $result =  $query->getQuery()->getSingleScalarResult();

        return $result;

    }
}
