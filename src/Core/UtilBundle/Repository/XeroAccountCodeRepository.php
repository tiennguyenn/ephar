<?php

namespace UtilBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use UtilBundle\Utility\Constant;

/**
 * Description of XeroAccountCodeRepository
 *
 * @author sang.nguyen
 */
class XeroAccountCodeRepository extends EntityRepository
{
//    public function listAccountCode($params)
//    {
//        $perPage = ($params['perPage'] > 0)? $params['perPage']: Constant::PER_PAGE_DEFAULT;
//        $page = ($params['page'] > 0)? $params['page']: Constant::PAGE_DEFAULT;
//        $startRecord = $perPage*$page;
//
//        $queryBuilder = $this->createQueryBuilder('ac')
//            ->setFirstResult($startRecord)
//            ->setMaxResults($perPage);
//
//        if (isset($params['filterLocation']) && $params['filterLocation'] != "0") {
//            $queryBuilder->where($queryBuilder->expr()->eq('ac.location', $params['filterLocation']));
//        }
//        if (isset($params['search']) && $params['search'] != '') {
//            $search = $params['search'];
//            $queryBuilder->andWhere("ac.name LIKE '%$search%' OR ac.description LIKE '%$search%'");
//        }
//
//        $sort  = 'ac.id';
//        $order = 'desc';
//
//        if (!empty($params['sorting'])) {
//            list($sort, $order) = explode('-', $params['sorting']);
//            $queryBuilder->orderBy($sort, $order);
//        } else {
//            $queryBuilder->orderBy($sort, $order);
//        }
//
//        $query = $queryBuilder->getQuery();
//        $paginator = new Paginator($query, false);
//
//        $result = array(
//            'sort'  => $sort,
//            'order' => $order,
//            'data'  => $paginator
//        );
//
//        return $result;
//    }

    public function getListAccount($request){
        $search = strtolower($request->get('search', ''));
        $limit = $request->get('length', '');
        $sort = $request->get('sort', array());
        $search = strtolower($search);
        if ($limit == -1) {
            $limit = null;
        }
        $ofset = $request->get('page', 1);

        $query = $this->createQueryBuilder('a')
            ->innerJoin('a.xeroCodeType','ct')
            ->innerJoin('a.xeroTaxRate','tr');


        if(!empty($search)){
            $query->andWhere($query->expr()->like('LOWER(a.name)',":name"). ' OR ' .$query->expr()->like('LOWER(a.code)',":code") . ' OR ' . $query->expr()->like('LOWER(a.description)',":description")   )
                ->setParameter("name","%".$search."%")
                ->setParameter("code","%".$search."%")
                ->setParameter("description","%".$search."%");
        }

        $this->generateSort($query, $sort);

        $totalResult = count($query->getQuery()->getResult());
        $query->setMaxResults($limit)
            ->setFirstResult(($ofset - 1) * $limit);
        $accounts = $query->getQuery()->getResult();
        $data = array();
        foreach ($accounts as $obj) {

            array_push($data, array(
                    'id' => $obj->getId(),
                    'code' => $obj->getCode(),
                    'name' => $obj->getName(),
                    'type' => $obj->getXeroCodeType()->getName(),
                    'tax' => $obj->getXeroTaxRate()->getName(),
                    'description' => $obj->getDescription()
                )
            );
        }


        return array('data' => $data, 'total' => $totalResult);
    }
    private function generateSort($em, $data) {
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'Code':
                    $em->orderBy("a.code", $value);
                    break;
                case 'Type':
                    $em->orderBy("ct.name", $value);

                    break;
                case 'Name':
                    $em->orderBy("a.name", $value);
                    break;
                case 'TaxCode':
                    $em->orderBy("tr.name", $value);
                    break;

            }
        }
    }
}
