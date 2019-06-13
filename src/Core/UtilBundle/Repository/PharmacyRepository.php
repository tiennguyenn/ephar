<?php

namespace UtilBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PharmacyRepository extends EntityRepository {
    /*
     * get data to display pharmacy list
     * param : data get from request
     */

    public function getPharmacies($request) {
        $search = $request->get('search', '');
        $limit = $request->get('length', '');
        $search = strtolower($search);

        if ($limit == -1) {
            $limit = null;
        }
        $ofset = $request->get('page', 1);

        $query = $this->createQueryBuilder('p')
                ->andWhere('LOWER(p.name) like :name OR LOWER(p.pharmacyCode) like :code ')

                ->setParameter('name', '%' . $search . '%')
                ->setParameter('code', '%' . $search . '%')
                ->getQuery()
                ->setMaxResults($limit)
                ->setFirstResult(($ofset - 1) * $limit);

        $pharmacy = $query->getResult();

        $data = array();
        foreach ($pharmacy as $obj) {
            array_push($data, array('id' => $obj->getId(), 'code' => $obj->getPharmacyCode(), 'name' => $obj->getName(), 'num' => count($obj->getDrugs())));
        }
        $total = $this->createQueryBuilder('p')
                ->select('count(p.id)')
                ->andWhere('LOWER(p.name) like :name OR LOWER(p.pharmacyCode) like :code ')
                ->setParameter('name', '%' . $search . '%')
                ->setParameter('code', '%' . $search . '%')
                ->getQuery()
                ->getSingleScalarResult();


        return array('data' => $data, 'total' => $total);
    }

    /**
    * get pharmacy information
    * @author vinh.nguyen
    */
    public function getPharmacy($id)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select("p.id,
                p.pharmacyCode,
                p.name,
                p.contactFirstname,
                p.contactLastname,
                a.line1,
                a.line2,
                a.line3,
                a.postalCode,
                ci.name as city,
                st.name as state,
                co.name as country
           ")
            ->from('UtilBundle:pharmacy', 'p')
            ->innerJoin('p.physicalAddress', 'a')
            ->leftJoin('a.city', 'ci')
            ->leftJoin('ci.state', 'st')
            ->leftJoin('ci.country', 'co')
            ->andWhere('p.id=:id')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getUsedPharmacy(){

        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.id=:id')
            ->setParameter('id', 1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getUsedPhone($pharmacy)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('p')
           ->from('UtilBundle:Phone', 'p')
           ->innerJoin('UtilBundle:PharmacyPhone', 'p2', 'WITH', 'p2.phone = p.id')
           ->where('p2.pharmacy = :pharmacy')
           ->setParameter('pharmacy', $pharmacy)
           ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
