<?php
namespace UtilBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use UtilBundle\Entity\PersonalInformation;
use UtilBundle\Utility\Constant;

class PersonalInformationRepository extends EntityRepository
{
    /**
     * create new
     * @param $params
     * @return null|object
     */
    public function create($params) {
        $em = $this->getEntityManager();

        $obj = new PersonalInformation();

        $obj->setFirstname($params['firstName']);
        $obj->setLastname($params['lastName']);
        $obj->setEmailaddress($params['emailAddress']);
        $obj->setCreatedon(new \DateTime());
        $obj->setUpdatedon(new \DateTime());

        $em->persist($obj);
        $em->flush();

        return $this->findOneBy(array('id' => $obj->getId()));
    }

    /**
     * update
     * @param $params
     */
    public function update($params) {}

    /**
     * delete
     * @param $userId
     */
    public function delete($userId) { }

    /**
     * get patient name by term
     * This function is used for auto suggest function on transaction history page
     * @param  array $params
     * @return array
     */
    public function getPersonName($params) {
        $limit = isset($params['limit'])? $params['limit']: 5;

        $selectStr = "(CASE 
                          WHEN pi.lastName is not null and pi.lastName is not null
                            THEN CONCAT(pi.firstName, ' ',  pi.lastName)
                          WHEN pi.firstName is not null and pi.lastName is null
                            THEN pi.firstName    
                          WHEN pi.firstName is null and pi.lastName is not null 
                            THEN pi.lastName
                          ELSE ''
                       END) as fullName";

        if ($params['personType'] == 'patient') {
            $selectStr = $selectStr . ', p.patientCode';
        } else {
            $selectStr = $selectStr . ', d.doctorCode';
        }

        $queryBuilder = $this->createQueryBuilder('pi');
        $queryBuilder
            ->select($selectStr)
            ->distinct();
            if ($params['personType'] == 'patient') {
                $queryBuilder->innerJoin('UtilBundle:Patient', 'p', 'WITH', 'p.personalInformation = pi.id');
            } else {
                $queryBuilder->innerJoin('UtilBundle:Doctor', 'd', 'WITH', 'd.personalInformation = pi.id');
            }

            //filter on: code, name
            if(isset($params['term']) && !empty($params['term'])) {
                $term = trim(strtolower($params['term']));

                $searchIn = $queryBuilder->expr()->like(
                                $queryBuilder->expr()->concat('pi.firstName', $queryBuilder->expr()->concat($queryBuilder->expr()->literal(' '), 'pi.lastName')),
                                $queryBuilder->expr()->literal( '%' . $term . '%')
                            );
                if ($params['personType'] == 'patient') {
                    $queryBuilder   
                        ->andWhere($searchIn ." OR LOWER(p.patientCode) LIKE :term")
                        ->setParameter('term', '%' . $term . '%');
                } else {
                    $queryBuilder   
                        ->andWhere($searchIn ." OR LOWER(d.doctorCode) LIKE :term")
                        ->setParameter('term', '%' . $term . '%');
                }

            }

        $queryBuilder->setFirstResult(0)
                    ->setMaxResults($limit);

        $results = array();
        $resultQuery = $queryBuilder->getQuery()->getArrayResult();
        $column = $params['personType'] == 'patient' ? 'patientCode' : 'doctorCode';

        foreach($resultQuery as $v){
            if($column == $column && strpos(strtolower($v['fullName']), strtolower($term)) !== false){
                $column = 'fullName';
            }
            $results[]['name'] = $v[$column];
        }

        // return 
        return array(
            'data' => $results
        );
    }
} 