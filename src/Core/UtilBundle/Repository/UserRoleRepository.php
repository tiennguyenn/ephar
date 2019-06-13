<?php
namespace UtilBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use UtilBundle\Entity\User;

class UserRoleRepository extends EntityRepository
{
    public function getUserRole($userId){
        $role = $this->createQueryBuilder('r')
                        ->where('r.user = :userId')
                        ->setParameter('userId', $userId)
                        ->getQuery()
                        ->getResult();

        return $role;
    }
} 