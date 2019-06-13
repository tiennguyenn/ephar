<?php

namespace UtilBundle\Repository;

use Doctrine\ORM\EntityRepository;
use UtilBundle\Utility\Constant;

/**
 * TransactionListingRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TransactionListingRepository extends EntityRepository 
{
	public function getList($params)
	{
		$qb = $this->createQueryBuilder('tl');
        $qb->innerJoin('tl.rx','rx');
        $qb->leftJoin('rx.batchRxes', 'brx')
            ->leftJoin('brx.xeroBatch','bath');

		if (!empty($params['search'])) {
			$qb->innerJoin('UtilBundle:TransactionListingCode', 'tlc', 'WITH', 'tl.id = tlc.transactionListing');
			$qb->andWhere($qb->expr()->like('tlc.gmedesCode', ':search'))
				->setParameter('search', '%' . $params['search'] . '%');
		}
		
		if (is_numeric($params['status']) && $params['status'] != 2) {
			$qb->innerJoin('UtilBundle:TransactionListingStatus', 'tls', 'WITH', 'tl.id = tls.transactionListing');
			if (isset($params['is_date_range']) && $params['is_date_range']) {
				$qb->andWhere($qb->expr()->eq('tls.batchStatus', ':status'))
					->setParameter('status', $params['status']);
			} else {	
				$qb->andWhere($qb->expr()->eq('tls.syncStatus', ':status'))
					->setParameter('status', $params['status']);
			}
		}
		
		if (isset($params['start']) && $params['start']) {
			$qb->andWhere($qb->expr()->gte('bath.batchDate', ':start'))
				->setParameter('start', $params['start']);
		}
		
		if (isset($params['end']) && $params['end']) {
			$qb->andWhere($qb->expr()->lte('bath.batchDate', ':end'))
				->setParameter('end', $params['end']);
		}
		
		if (isset($params['destination']) && in_array($params['destination'], array(Constant::XERO_MAPPING_LOCAL, Constant::XERO_MAPPING_OVERSEAS))) {
			$qb->andWhere($qb->expr()->eq('tl.location', ':location'))
				->setParameter('location', $params['destination']);
		}
		
		$qb->andWhere("tl.datePatientPay IS NOT NULL");
		
		$qb->orderBy('tl.updatedOn', 'ASC')
        ->groupBy('tl.id');
		
		return $qb->getQuery()->execute();
	}
}