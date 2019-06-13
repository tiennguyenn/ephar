<?php

namespace UtilBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use UtilBundle\Utility\Constant;

class PublicHolidayRepository extends EntityRepository
{
    public function listPH($params)
    {
        $perPage = ($params['perPage'] > 0)? $params['perPage']: Constant::PER_PAGE_DEFAULT;
        $page = ($params['page'] > 0)? $params['page']: Constant::PAGE_DEFAULT;
        $startRecord = $perPage*$page;

        $queryBuilder = $this->createQueryBuilder('ph')
            ->setFirstResult($startRecord)
            ->setMaxResults($perPage);

        $sort  = 'ph.publicDate';
        $order = 'desc';

        if (!empty($params['sorting'])) {
            list($sort, $order) = explode('-', $params['sorting']);
            $queryBuilder->orderBy($sort, $order);
        } else {
            $queryBuilder->orderBy($sort, $order);
        }

        $query = $queryBuilder->getQuery();
        $paginator = new Paginator($query, false);

        $result = array(
            'sort'  => $sort,
            'order' => $order,
            'data'  => $paginator
        );

        return $result;
    }
    
    public function listPHDates()
    {
        return $this->createQueryBuilder('ph')
            ->select('ph.publicDate')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }
}