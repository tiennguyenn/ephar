<?php

namespace UtilBundle\Repository;

use UtilBundle\Entity\MasterProxyAccount;
use UtilBundle\Entity\User;
use UtilBundle\Utility\Common;

/**
 * MasterProxyAccountRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MasterProxyAccountRepository extends \Doctrine\ORM\EntityRepository
{

    public function getListAdmin($request){
        $search = $request->get('search', '');
        $limit = $request->get('length', 10);
        $sort = $request->get('sort', array());
        $page = $request->get('page', 1);

        $qb = $this->createQueryBuilder('mpa');
        $qb->leftJoin('mpa.mpaDoctors', 'map')
            ->leftJoin('map.doctor','doc' )
            ->Where($qb->expr()->isNull('mpa.deletedOn'));

        if (!empty($search)) {
            $qb->andWhere('LOWER(CONCAT(mpa.givenName,\' \',mpa.familyName))  lIKE :search')
                ->setParameter('search', '%'. $search .'%');
        }

        $qb->groupBy('mpa.id');


        if (!empty($sort)) {
            foreach ($sort as $key => $value) {
                $qb->orderBy("mpa." . $key, $value);
            }
        }


        $all = $qb->getQuery()->getResult();

        $qb->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit);

        $list = $qb->getQuery()->getResult();
        $result = array();
        foreach ($list as $record) {
            $line = array();
            $line['registerDate'] = $record->getCreatedOn()->format('d M y');
            $line['name'] = trim($record->getGivenName() . ' '. $record->getFamilyName());
            $line['status'] = $record->getStatus();
            $line['code'] = $record->getMpaCode();
            $line['isConfirm'] = $record->getIsConfirmed();
            $line['id'] = $record->getId();
            $line['hashId'] = Common::encodeHex($record->getId());
            $line['accountStatus'] = false;
            $line['lastLogin'] = "NA";
            $user = $record->getUser();
            if(!empty($user)) {
                $lastLogin = $user->getLastLogin();
                if(!empty($lastLogin)){
                    $line['lastLogin'] = $lastLogin->format('d M y');
                }
                $line['accountStatus'] = true;
            }
            $result[] = $line;

        }
        return array('data' => $result, 'total' => count($all));
    }

    public function getCurrentListDoctorAssign($request){
        $id = $request->get('id', 0);

        $search = $request->get('select-remove', '');

        $qb = $this->createQueryBuilder('mpa')
        ->select('doc.id as id,CONCAT(p.firstName,\' \',p.lastName) as name');

        $qb->innerJoin('mpa.mpaDoctors', 'map')
            ->innerJoin('map.doctor','doc' )
            ->join('doc.personalInformation', 'p')
            ->where($qb->expr()->isNull('mpa.deletedOn'))
            ->andWhere($qb->expr()->eq('mpa.id', $id))
            ->andWhere($qb->expr()->isNull('map.deletedOn'));

        if (!empty($search)) {
            $qb->andWhere('LOWER(CONCAT(p.firstName,\' \',p.lastName))  lIKE :search')
                ->setParameter('search', '%'. $search .'%');
        }
        $qb->groupBy('doc.id');
        $result = $qb->getQuery()->getResult();
        $list = array();
        foreach ($result as $item){
            $item['select'] = true;
            $list[$item['id']] = $item;
        }
        return $list;
    }


    public function getListForDoctorAdmin($request){
        $search = $request->get('search', '');
        $limit = $request->get('length', 10);
        $sort = $request->get('sort', array());
        $page = $request->get('page', 1);
        $id = $request->get('id', 0);
        $status = $request->get('status', 2);

        $qb = $this->createQueryBuilder('mpa');
        $qb->innerJoin('mpa.mpaDoctors', 'map')
            ->innerJoin('map.doctor','doc' )
            ->where($qb->expr()->isNull('mpa.deletedOn'))
            ->andWhere($qb->expr()->eq('doc.id',$id));

        if (!empty($search)) {
            $qb->andWhere('LOWER(CONCAT(mpa.givenName,\' \',mpa.familyName))  lIKE :search or mpa.emailAddress LIKE :search')
                ->setParameter('search', '%'. $search .'%');
        }
        if ($status != 2) {
            $qb->andWhere($qb->expr()->eq('mpa.status',$status));
        }

        $qb->groupBy('mpa.id');


        if (!empty($sort)) {
            foreach ($sort as $key => $value) {
                $qb->orderBy("mpa." . $key, $value);
            }
        }


        $all = $qb->getQuery()->getResult();

        $qb->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit);

        $list = $qb->getQuery()->getResult();
        $result = array();
        foreach ($list as $record) {
            $line = array();
            $line['registerDate'] = $record->getCreatedOn()->format('d M y');
            $line['name'] = trim($record->getGivenName() . ' '. $record->getFamilyName());
            $line['email'] = $record->getEmailAddress();

            $line['id'] = $record->getId();
            $line['hashId'] = Common::encodeHex($record->getId());

            $line['lastLogin'] = "NA";
            $user = $record->getUser();
            if(!empty($user)) {
                $lastLogin = $user->getLastLogin();
                if(!empty($lastLogin)){
                    $line['lastLogin'] = $lastLogin->format('d M y');
                }

            }
            $result[] = $line;

        }
        return array('data' => $result, 'total' => count($all));
    }

}
