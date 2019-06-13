<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace UtilBundle\Twig\Extensions;

use UtilBundle\Utility\Common;
use UtilBundle\Utility\Constant;
use \Twig_Extension;


/**
 * Twig Helper
 * @author thu.tranq
 */
class TwigHelper extends Twig_Extension
{
    private $router;
    private $container;
    private $session;
    private static $currency = '';

    private static $pharmacyName;
    private static $courierName;


    public function __construct($container, $em)
    {
        $this->container = $container;
        $this->em = $em;
    }

    public function getFunctions()
    {
        return array (
           new \Twig_SimpleFunction('countryDropDownList', [$this, 'countryDropDownList'], [
               'is_safe' => ['html'],
               'needs_environment' => true // Tell twig we need the environment
           ]),
           new \Twig_SimpleFunction('yearDropDownList', [$this, 'yearDropDownList'], [
               'is_safe' => ['html'],
               'needs_environment' => true // Tell twig we need the environment
           ]),
           new \Twig_SimpleFunction('monthDropDownList', [$this, 'monthDropDownList'], [
               'is_safe' => ['html'],
               'needs_environment' => true // Tell twig we need the environment
           ]),

           new \Twig_SimpleFunction('balanceInfo', [$this, 'balanceInfo'], [
               'is_safe' => ['html'],
               'needs_environment' => true // Tell twig we need the environment
           ]),
           new \Twig_SimpleFunction('countConfirmedPrescription', [$this, 'countConfirmedPrescription']),
           new \Twig_SimpleFunction('printPaymentMethod', [$this, 'printPaymentMethod']),
           new \Twig_SimpleFunction('get_message_total', [$this, 'getMessageTotal']),
		   new \Twig_SimpleFunction('pluralize', [$this, 'pluralize']),
           new \Twig_SimpleFunction('get_order_list_total', [$this, 'getCustomerCareOrderList']),
           new \Twig_SimpleFunction('get_order_is_total', [$this, 'getCustomerCareIssueAndResolution']),
            new \Twig_SimpleFunction('getRxListUnreadDoctor', [$this, 'getRxListUnreadDoctor']),
            new \Twig_SimpleFunction('getRxPendingUnreadDoctor', [$this, 'getRxPendingUnreadDoctor']),
            new \Twig_SimpleFunction('getRxConfirmUnreadDoctor', [$this, 'getRxConfirmUnreadDoctor']),
            new \Twig_SimpleFunction('getRxRecallUnreadDoctor', [$this, 'getRxRecallUnreadDoctor']),
            new \Twig_SimpleFunction('getRxReportUnreadDoctor', [$this, 'getRxReportUnreadDoctor']),
			new \Twig_SimpleFunction('getMonthByFormat', [$this, 'getMonthByFormat']),
            new \Twig_SimpleFunction('getPharmacyName', [$this, 'getPharmacyName']),
            new \Twig_SimpleFunction('getCourierName', [$this, 'getCourierName']),
            new \Twig_SimpleFunction('encodeHex', [$this, 'encodeHex']),
            new \Twig_SimpleFunction('getCurrency', [$this, 'getCurrency']),
            new \Twig_SimpleFunction('checkChineseFont', [$this, 'checkChineseFont']),
            new \Twig_SimpleFunction('isMainDoctorLogin', [$this, 'isMainDoctorLogin']),
            new \Twig_SimpleFunction('isMainAgentLogin', [$this, 'isMainAgentLogin']),
            new \Twig_SimpleFunction('get_dispensing_total', [$this, 'getCountDispensing']),
            new \Twig_SimpleFunction('get_completed_dispensing_total', [$this, 'getCountCompletedDispensing']),
            new \Twig_SimpleFunction('isFullEqual', [$this, 'isFullEqual']),
            new \Twig_SimpleFunction('getFAQUrl', [$this, 'getFAQUrl'])
        );
    }


    public function encodeHex($id) {
        if(!empty($id)){
            return Common::encodeHex($id);
        }
        return  '';
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('country_code_format', array($this, 'countryCodeFormat')),
            new \Twig_SimpleFilter('save_total_amount', array($this, 'saveTotalAmount')),
            new \Twig_SimpleFilter('last_day', array($this, 'getTheLastDayOfMonth')),
            new \Twig_SimpleFilter('appendRightDot', array($this, 'appendRightDot')),
            new \Twig_SimpleFilter('isHtmlString', array($this, 'isHtmlString')),
            new \Twig_SimpleFilter('trimAppendDot', array($this, 'trimAppendDot'))
        );
    }
    
    public function checkChineseFont($utf8_str){
        return preg_match("/\p{Han}+/u", $utf8_str);
    }

    public function getCurrency(){
        if(empty(self::$currency)) {
            if ($this->container->hasParameter('currency_code')) {
                self::$currency = $this->container->getParameter('currency_code');
            } else {
                self::$currency =  "MYR";
            }
        }
        return self::$currency;
    }

    /**
     * getTheLastDayOfMonth
     * @param  DateTime $day
     * @return integer
     */
    public function getTheLastDayOfMonth($statementDay) {
        if (!isset($statementDay)) {
            return 'NIL';
        }
        $year  = $statementDay->format('Y');
        $month = (int)$statementDay->format('m');
        $day = new \DateTime( $year . '-' . $month  );
        $day->modify('-1 month');

        return (string)cal_days_in_month(CAL_GREGORIAN, (int)$day->format('m'), $day->format('Y')) . ' ' . $day->format('M Y');
     }
    /**
     * Generate balance info for montly statment (docotr/agent)
     * @author thu.tranq
     */
    public function balanceInfo(\Twig_Environment $environment, $params, $entityRepoStr)
    {
        $results = $this->em->getRepository($entityRepoStr)->getPreMonthsStatementInfo($params);
        if ($entityRepoStr == 'UtilBundle:DoctorMonthlyStatementLine') {
            $results['type'] = 'doctor';
            $isFirstStatement = $this->em->getRepository('UtilBundle:DoctorMonthlyStatement')->isTheFirstStatement($params);

        } else {
            $results['type'] = 'agent';
            $isFirstStatement = $this->em->getRepository('UtilBundle:AgentMonthlyStatement')->isTheFirstStatement($params);
        }
        $results['isFirstStatement'] = $isFirstStatement;
        $results['currencyCode'] = isset($params['currencyCode']) ? $params['currencyCode'] : 'SGD';

        return $environment->render('UtilBundle:monthly_statement:_balance_info.html.twig', array('result' => $results));
    }

    /*
     * @author Tien Nguyen
     */
    public function countryCodeFormat($code)
    {
        if (Constant::INDONESIA_CODE == $code) {
            return Constant::INDONESIA_DISPLAY;
        }
        return $code;
    }

    public function saveTotalAmount($value)
    {
        $this->container->get('session')->set('totalAmount', $value);
        return $value;
    }

    public function getPharmacyName(){
        if (empty(self::$pharmacyName)){
            $result = $this->em->getRepository('UtilBundle:Pharmacy')->getUsedPharmacy();
            self::$pharmacyName = $result->getShortName();
        }
        return self::$pharmacyName;
    }
    public function getCourierName(){
        if (empty(self::$courierName)){
            $result = $this->em->getRepository('UtilBundle:Courier')->getUsedCourer();
            self::$courierName = $result->getShortName();
        }
        return self::$courierName;
    }

    /*
     * @author Tien Nguyen
     */
    public function printPaymentMethod($orderNumber)
    {
        $criteria = array('orderRef' => $orderNumber);
        $log = $this->em->getRepository('UtilBundle:RxPaymentLog')->findOneBy($criteria);

        if ($log) {
            return $log->getPayMethod();
        }

        return 'VISA';
    }

    /**
     * build country dropdown list
     * @author thu.tranq
     */
    public function countryDropDownList(\Twig_Environment $environment, $elId = 'country_dropdown')
    {
        $results = $this->em->getRepository('UtilBundle:Country')->getList();

        return $environment->render('UtilBundle::_country_dropdown_list.html.twig', array('elId' => $elId,'data' => $results['data']));
    }

    /**
     * build year dropdownlist
     * @param  integer $backNumber
     * @author thu.tranq
     * @return
     */
    public function yearDropDownList(\Twig_Environment $environment, $elId = 'year_dd', $backNumber = 5) {
        $currentyear = date('Y');
        $startYear   = $currentyear - $backNumber;
        $data        = array('start' => $startYear, 'end' => $currentyear);

        return $environment->render('UtilBundle::_year_dropdown_list.html.twig', array('elId' => $elId,'data' => $data, 'val' => $currentyear));
    }

    /**
     * build month dropdownlist
     * @param  integer $backNumber
     * @author thu.tranq
     * @return
     */
    public function monthDropDownList(\Twig_Environment $environment, $elId = 'month_dd', $selectedVal = null) {
        $val = isset($selectedVal) ? $selectedVal : date('m');
        return $environment->render('UtilBundle::_month_dropdown_list.html.twig', array('elId' => $elId, 'val' => $val));
    }

    /**
     * count confirmed prescription
     * @author toan.le
     * @return
     */
    public function countConfirmedPrescription() {
        if (null === $token = $this->container->get('security.token_storage')->getToken()) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return;
        }
        $userId = $user->getLoggedUser()->getId();
        $doctorId = $user->getId();
        if (in_array(Constant::TYPE_MPA, $user->getRoles())) {
            $doctor = $this->em->getRepository('UtilBundle:Doctor')->find($doctorId);
            if (!empty($doctor) && !empty($doctor->getUser())) {
                $userId = $doctor->getUser()->getId();
            }
        }
        $rxs = $this->em->getRepository('UtilBundle:Rx')->getRxForDashboardDoctor(['id' => $doctorId, 'userId' => $userId]);
        return $rxs['totalResult'];
    }

    public function getMessageTotal($all=false)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $userId = $user->getLoggedUser()? $user->getLoggedUser()->getId(): $user->getId();
        $result = $this->em->getRepository('UtilBundle:Message')->getTotal($userId);
        if($all)
            return $result['total'];

        return $result;
    }

	public function pluralize($quantity, $singular)
	{
		if($quantity == 1 || empty($singular)) return $singular;

		$lastLetter = strtolower($singular[strlen($singular)-1]);
		switch($lastLetter) {
			case 'y':
				return substr($singular,0,-1).'ies';
			case 's':
			case 'x':
			case 'i':
				return $singular.'es';
			default:
				return $singular.'s';
		}
	}

    /**
     * count
     * @author bien
     * @return
     */
    public function getCustomerCareOrderList()
    {
        $result = $this->em->getRepository('UtilBundle:RxCounter')->getCountUpdatedAndUnReadCustomerCare(1);
        return $result;
    }
    /**
     * count
     * @author bien
     * @return
     */
    public function getCustomerCareIssueAndResolution()
    {
        $result = $this->em->getRepository('UtilBundle:RxCounter')->getCountUpdatedAndUnReadCustomerCare(2);
        return $result;
    }

    /**
     * count
     * @author bien
     * @return
     */
    public function getRxListUnreadDoctor()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user)) {
            return 0;
        }

        $result = $this->em->getRepository('UtilBundle:RxCounter')->getCountUpdatedAndUnReadDocotor($user->getId(),2);
        return $result;
    }
    /**
     * count
     * @author bien
     * @return
     */
    public function getRxPendingUnreadDoctor()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user)) {
            return 0;
        }

        $result = $this->em->getRepository('UtilBundle:RxCounter')->getCountUpdatedAndUnReadDocotor($user->getId(),3);
        return $result;
    }
    /**
     * count
     * @author bien
     * @return
     */
    public function getRxConfirmUnreadDoctor()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user)) {
            return 0;
        }

        $result = $this->em->getRepository('UtilBundle:RxCounter')->getCountUpdatedAndUnReadDocotor($user->getId(),4);
        return $result;
    }
    /**
     * count
     * @author bien
     * @return
     */
    public function getRxRecallUnreadDoctor()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user)) {
            return 0;
        }

        $result = $this->em->getRepository('UtilBundle:RxCounter')->getCountUpdatedAndUnReadDocotor($user->getId(),5);
        return $result;
    }
    /**
     * count
     * @author bien
     * @return
     */
    public function getRxReportUnreadDoctor()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user)) {
            return 0;
        }

        $result = $this->em->getRepository('UtilBundle:RxCounter')->getCountUpdatedAndUnReadDocotor($user->getId(),6);
        return $result;
    }
	
	/**
	 * Convert month from number to short or full namespace
	 * @author Tuan Nguyen
	 **/
	public function getMonthByFormat($month, $format) 
	{
		$names = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'November', 'December');
		
		switch ($format) {
			case 'F':
				$month--;
				return isset($names[$month]) ? $names[$month] : '';
			case 'M':
				$month--;
				return isset($names[$month]) ? substr($names[$month], 0, 3) : '';
			case 'm':
				return strlen('' . $month) < 2 ? '0' . $month : $month;
			case 'n':
				return ltrim('' . $month, '0');
			default:
				return $month;
		}
	}

    /**
     * Check if current login belongs to current doctor
     *
     **/
    public function isMainDoctorLogin($gmedUser = null) {
        if ($gmedUser == null) {
            $gmedUser = $this->container->get('security.token_storage')->getToken();
        }

        return Common::isMainDoctorLogin($gmedUser, $this->em);
    }

    /**
     * Check if current login belongs to current agent
     *
     **/
    public function isMainAgentLogin($gmedUser = null) {
        if ($gmedUser == null) {
            $gmedUser = $this->container->get('security.token_storage')->getToken();
        }

        return Common::isMainAgentLogin($gmedUser, $this->em);
    }

     /**
     * count rx for pharmacist
     */
    public function getCountDispensing()
    {
        $result = $this->em->getRepository('UtilBundle:Rx')->getCountRxPharmacist(1);
        return $result;
    }

    /**
     * count rx for pharmacist
     */
    public function getCountCompletedDispensing()
    {
        $result = $this->em->getRepository('UtilBundle:Rx')->getCountRxPharmacist(2);
        return $result;
    }

    /*
     * append right dot to sentence
     * */
    public function appendRightDot($str)
    {
        if (strlen(trim($str)) > 0) {
            return rtrim(trim($str), '.') . '.';
        } else {
            return '';
        }
    }

    public function isHtmlString($str)
    {
        if($str != strip_tags($str)) {
            return true;
        } else {
            return false;
        }
    }

    public function isFullEqual($str1, $str2)
    {
        return ($str1 === $str2);
    }

    public function getFAQUrl($route_name)
    {
        $request = $this->container->get('request');
        $route = $this->container->get('router');
        if ($this->container->hasParameter('help_url')) {
            $helpUrl = $this->container->getParameter('help_url');
            return $request->getScheme() . '://' . $helpUrl . $route->generate($route_name);
        } else {
            return $route->generate($route_name);
        }
    }
}
