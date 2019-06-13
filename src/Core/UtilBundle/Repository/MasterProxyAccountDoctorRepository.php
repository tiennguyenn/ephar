<?php

namespace UtilBundle\Repository;
use UtilBundle\Entity\Doctor;

/**
 * MasterProxyAccountDoctorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MasterProxyAccountDoctorRepository extends \Doctrine\ORM\EntityRepository
{
    public function getListDetailAdmin($request ){
        $search = $request->get('search', '');
        $limit = $request->get('length', 10);
        $sort = $request->get('sort', array());
        $page = $request->get('page', 1);
        $id = $request->get('id', 0);


        $qb = $this->createQueryBuilder('map');

        $qb->innerJoin('map.masterProxyAccount', 'mpa')
            ->innerJoin('map.doctor','doc' )
            ->innerJoin('doc.personalInformation', 'p')
            ->where($qb->expr()->isNull('mpa.deletedOn'))
            ->andWhere($qb->expr()->isNull('map.deletedOn'))
            ->andWhere($qb->expr()->eq('mpa.id', $id));

        if (!empty($search)) {
            $qb->andWhere('LOWER(CONCAT(p.firstName,\' \',p.lastName))  lIKE :search OR LOWER(doc.doctorCode) like :search')
                ->setParameter('search', '%'. $search .'%');
        }

        $qb->groupBy('map.id');


        $this->generateSort($qb, $sort);


        $all = $qb->getQuery()->getResult();

        $qb->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit);

        $list = $qb->getQuery()->getResult();
        $result = array();
        foreach ($list as $record) {
            $line = array();
            $doctor = $record->getDoctor();
            $line['registerDate'] = $doctor->getCreatedOn()->format('d M y');
            $line['doctorName'] = trim($doctor->getPersonalInformation()->getFullName());
            $line['code'] = $doctor->getDoctorCode();
            $line['id'] = $doctor->getId();
            $line['roles'] = is_array($record->getPrivilege())? $record->getPrivilege() : json_decode($record->getPrivilege());
            $line['isCustomizeMedicineEnabled'] = $doctor->getIsCustomizeMedicineEnabled();
            $result[] = $line;

        }
        return array('data' => $result, 'total' => count($all));
    }
    private function generateSort($builder, $data) {
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'doctorCode':
                    $builder->orderBy("doc.doctorCode", $value);
                    break;
                case 'doctorName':
                    $builder->orderBy("p.lastName", $value);
                    $builder->orderBy("p.firstName", $value);

                    break;
                case 'registerDate':
                    $builder->orderBy("map.createdOn", $value);
                    break;

            }
        }
    }
}
