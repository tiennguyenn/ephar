<?php

namespace UtilBundle\Microservices;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BaseService
{
    protected $container;
    protected $em;
    protected $success = true;
    protected $data = null;
    protected $message = null;
    protected $totalResult = null;
    protected $totalPages = null;

    public function __construct($container, EntityManager $em)
    {
        $this->container = $container;
        $this->em = $em;
    }

    /**
     * show results
     * @param $params
     * @return array
     */
    protected function getResults()
    {
        $response = array(
            'success' => $this->success,
            'data'    => $this->data,
            'message' => $this->message,
        );

        if($this->totalResult !== null && $this->totalResult >= 0)
            $response['totalResult'] = $this->totalResult;

        if($this->totalPages !== null && $this->totalPages >=0)
            $response['totalPages'] = $this->totalPages;

        return $response;
    }
} 