<?php

namespace UtilBundle\Microservices;

use Doctrine\ORM\EntityManager;
use UtilBundle\Entity\User;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;

class DoctorService extends BaseService
{
    public function createDoctor($params)
    {
        try{
            $this->data = $this->em->getRepository('UtilBundle:Doctor')->create($params);
        } catch(Exception $ex) {
            $this->message = $ex->getMessage();
            $this->success = false;
        }
        return $this->getResults();
    }

    public function getDoctor($id)
    {
        try{
            $result = $this->em->getRepository('UtilBundle:Doctor')->get($id);
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

    public function getDoctors()
    {
        try{
            $params = array();
            $result = $this->em->getRepository('UtilBundle:Doctor')->getList($params);
            $this->data = $result['data'];
            $this->totalResult = $result['totalResult'];
        } catch(Exception $ex) {
            $this->message = $ex->getMessage();
            $this->success = false;
        }
        return $this->getResults();
    }


} 