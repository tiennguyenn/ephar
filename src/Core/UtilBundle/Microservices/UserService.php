<?php

namespace UtilBundle\Microservices;

use Doctrine\ORM\EntityManager;
use UtilBundle\Entity\User;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

class UserService extends BaseService
{
    public function createUser($params, $userManager)
    {
        try{
            $this->data = $this->em->getRepository('UtilBundle:User')->create($params, $userManager);
        } catch(Exception $ex) {
            $this->message = $ex->getMessage();
            $this->success = false;
        }
        return $this->getResults();
    }

    public function getUser($id)
    {
        try{
            $result = $this->em->getRepository('UtilBundle:User')->get($id);
            if($result != null) {
                $this->data =  $result;
            } else {
                $this->message = "Data not found";
                $this->success = false;
            }
        } catch(Exception $ex) {
            $this->message = $ex->getMessage();
            $this->success = false;
        }
        return $this->getResults();
    }

    public function getUsers()
    {
        try{
            $params = array();
            $result = $this->em->getRepository('UtilBundle:User')->getList($params);
            $this->data = $result['data'];
            $this->totalResult = $result['totalResult'];
        } catch(Exception $ex) {
            $this->message = $ex->getMessage();
            $this->success = false;
        }
        return $this->getResults();
    }
} 