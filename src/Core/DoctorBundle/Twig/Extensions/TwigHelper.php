<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace DoctorBundle\Twig\Extensions;
use \Twig_Extension;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 * Twig Helper
 * @author thu.tranq
 */
class TwigHelper extends Twig_Extension
{
    private $container;
    private $em;

    public function __construct($container, $em)
    {
        $this->container = $container;
        $this->em = $em;
    }
    
    public function getFunctions()
    {
        return array (
            'doctorPaymentStatus' => new \Twig_Function_Method($this, 'doctorPaymentStatus'),
            'getMPADoctors' => new \Twig_Function_Method($this, 'getMPADoctors'),
            'getMPANotifications' => new \Twig_Function_Method($this, 'getMPANotifications'),
        );

    }

    /**
     * get payment status for doctor
     * @param  integer $doctorId
     * @param  integer $month
     * @param  integer $year
     * @return string
     */
    public function doctorPaymentStatus($agentId, $month, $year) {
        return $this->em->getRepository('UtilBundle:DoctorMonthlyStatementLine')->getPaymentStatus($agentId, (int)$month, $year);
    }

    public function getMPADoctors()
    {
        $token = $this->container->get('security.token_storage')->getToken();
        if (empty($token)) {
            return [];
        }

        $user = $token->getUser();
        if(!is_object($user)) {
            return [];
        }

        $loggerUser = $user->getLoggedUser();
        $mpa = $this->em->getRepository('UtilBundle:MasterProxyAccount')->findOneBy(['user' => $loggerUser]);

        if (!is_object($mpa)) {
            return [];
        }

        $doctors = [];

        $mpaDoctors = $mpa->getMpaDoctors();
        foreach ($mpaDoctors as $value) {
            $doctors[] = $value->getDoctor();
        }

        return $doctors;
    }

    public function getMPANotifications()
    {
        $token = $this->container->get('security.token_storage')->getToken();
        if (empty($token)) {
            return [];
        }

        $user = $token->getUser();
        if(!is_object($user)) {
            return [];
        }

        $doctorId = $user->getId();

        $result = $this->em->getRepository('UtilBundle:RxLine')
            ->getRxLineAmendments($doctorId);

        return $result;
    }
}