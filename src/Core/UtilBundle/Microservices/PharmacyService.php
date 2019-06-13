<?php

namespace UtilBundle\Microservices;

use Doctrine\ORM\EntityManager;
use UtilBundle\Entity\User;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;

class PharmacyService extends BaseService
{
    private $container;
    private $em;

    public function __construct($container, EntityManager $em)
    {
        $this->container = $container;
        $this->em = $em;
    }

    public function createPharmacy($params)
    {
        $data = array();
        try{
            $this->data = $this->em->getRepository('UtilBundle:Pharmacy')->create($data);
        } catch(Exception $ex) {
            $this->message = $ex->getMessage();
            $this->success = false;
        }
        return $this->getResults();
    }
}