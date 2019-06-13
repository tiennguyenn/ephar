<?php

namespace UtilBundle\Repository;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\DBAL\Connection;
use Doctrine\Common\Collections\Criteria;
use UtilBundle\Entity\Patient;
use UtilBundle\Entity\Rx;
use UtilBundle\Entity\RxLine;
use UtilBundle\Entity\RxStatusLog;
use UtilBundle\Entity\DoctorMedicalFavourite;
use UtilBundle\Entity\RxRefillReminder;
use UtilBundle\Entity\DosageFormAction;
use UtilBundle\Entity\RxReminderSetting;
use UtilBundle\Utility\Common;
use UtilBundle\Utility\Constant;
use UtilBundle\Utility\MonthlyPdfHelper;

/**
 * RxStatusLogRepository
 */
class RxStatusLogRepository extends EntityRepository
{
    /**
     * get list tracking order
     * @author  vinh.nguyen
     */
    public function getTrackingOrder($params)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb ->select("l.id, l.status, l.notes, l.createdOn, l.createdBy")
            ->from('UtilBundle:RxStatusLog', 'l')
            ->innerJoin('l.rx', 'rx')
            ->andWhere('rx.orderNumber=:orderNumber AND l.status IN(:inStatus)')
            ->setParameter('orderNumber', $params['orderNumber'])
            ->setParameter('inStatus', $params['inStatus'])
            ->groupBy('l.status');

        //count total items
        $totalResult = count($qb->getQuery()->getArrayResult());

        //pagination
        $perPage = ($params['perPage'] > 0)? $params['perPage']: Constant::PER_PAGE_DEFAULT;
        $page = ($params['page'] > 0)? $params['page']: Constant::PAGE_DEFAULT;

        $qb->setFirstResult($perPage*$page)->setMaxResults($perPage);

        return array(
            'totalResult' => $totalResult,
            'totalPages'  => ceil($totalResult/$perPage),
            'data'        => $qb->getQuery()->getArrayResult()
        );
    }
}
