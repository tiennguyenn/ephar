<?php
namespace UtilBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use UtilBundle\Entity\User;
use UtilBundle\Utility\Constant;

class IssueRepository extends EntityRepository
{


    public function getReporter(){
        $query = $this->createQueryBuilder('i')
                  ->select('i.createdBy as name') 
                  ->groupBy('name')
                  ->getQuery()->getResult();
        
        return $query;
 
    }
} 