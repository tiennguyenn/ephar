<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace AdminBundle\Twig\Extensions;
use \Twig_Extension;
use UtilBundle\Utility\Common;
use UtilBundle\Utility\Constant;
use UtilBundle\Utility\RouterAuthent;
use UtilBundle\Utility\MsgUtils;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 * Twig Helper
 * @author Luyen Nguyen
 */
class TwigHelper extends Twig_Extension
{
    private $router;
    private $container;
    private $session;

    public function __construct($container, $session, $router)
    {
        $this->container = $container;
        $this->session = $session;
        $this->router = $router;
    }
    
    public function getFunctions()
    {
        return array (
            'authent' => new \Twig_Function_Method($this, 'authentLogin'),
            'getMessageContent' => new \Twig_Function_Method($this, 'getMessageContent'),
            'generateMessage' => new \Twig_Function_Method($this, 'generateMessage'),
            'jsonPageParams' => new \Twig_Function_Method($this, 'jsonPageParams'),
            'menu_focus' => new \Twig_Function_Method($this, 'getMenuFocus'),
            'display_messages' => new \Twig_Function_Method($this, 'displayMessages'),
            'convertRXStatus' => new \Twig_Function_Method($this, 'convertRXStatus'),
            'user_type_label' => new \Twig_Function_Method($this, 'userTypeLabel'),
            'getCurrencyCode' => new \Twig_Function_Method($this, 'getCurrencyCode'),
            'year_weeks' => new \Twig_Function_Method($this, 'getYearWeeks'),
            'age_calculate' => new \Twig_Function_Method($this, 'ageCalculate'),
            'is_show_menu_left' => new \Twig_Function_Method($this, 'isShowMenuLeft'),
            'get_current_user_role' => new \Twig_Function_Method($this, 'getCurrentUserRole'),
            'get_constant_value'    => new \Twig_Function_Method($this, 'getConstantValue'),
            'format_full_date'    => new \Twig_Function_Method($this, 'formatFullDate'),
            'get_timeslot'    => new \Twig_Function_Method($this, 'getTimeSlot'),
            'get_day_of_week'    => new \Twig_Function_Method($this, 'getDayOfWeek'),
            'item_log'    => new \Twig_Function_Method($this, 'itemLog'),
			'isImage' => new \Twig_Function_Method($this, 'isImage'),
			'hasImage' => new \Twig_Function_Method($this, 'hasImage'),
            'party_list' => new \Twig_Function_Method($this, 'partyList'),
            'check_exist' => new \Twig_Function_Method($this, 'checkExist'),
            'getGACode' => new \Twig_Function_Method($this, 'getGACode')
        );
    }
	
    public function getTests()
    {
        return array (
			'instanceof' =>  new \Twig_Function_Method($this, 'isInstanceof')
        );
    }
    /**
     * Authentication Login
     * @return type
     * author luyen nguyen
     */
    public function authentLogin()
    {
        return RouterAuthent::checkRoute($this->container);
    }

    /**
     * Get Message Content List
     * @return array
     */
    public function getMessageContent()
    {
        return json_encode(MsgUtils::$messageContent);
    }

    /**
     * Generate Message
     * @author vinh.nguyen
     * @return string
     */
    public function generateMessage()
    {
        $messageContent = "";
        $args = func_get_args();
        $msgId = array_shift($args);
        if (isset(MsgUtils::$messageContent[$msgId])) {
            $messageContent = count($args) > 0
                ? vsprintf(MsgUtils::$messageContent[$msgId], $args)
                : MsgUtils::$messageContent[$msgId];
        }
        return $messageContent;
    }
    
    /**
     * Create page url for pagination
     * @author Tuan Nguyen
     * @return string
     */
    public function jsonPageParams($page, $params)
    {
        $params['page'] = $page;
        return json_encode($params);
    }

    /**
     * Control Menu is {open/close} focused.
     * @author vinh.nguyen
     */
    public function getMenuFocus()
    {
        $result = null;
        $currentRouter = Common::getCurrentRouter($this->container);

        foreach(Constant::$menuList as $name=>$value) {
            if(in_array($currentRouter, $value)) {
                $result = trim(strtolower($name));
                break;
            }
        }

        return $result;
    }

    public function isShowMenuLeft($router)
    {

        return Common::isShowLeftMenu($router, $this->container);
    }

    public function getCurrentUserRole()
    {
        return Common::getCurrentUserRole($this->container);
    }

    public function getConstantValue($constantName)
    {
        return Constant::$constantName;
    }

    /**
     * display flash messages
     * @author vinh.nguyen
     */
    public function displayMessages()
    {
        //type: danger, success
        $output = "";
        foreach ($this->container->get('session')->getFlashBag()->all() as $type => $messages) {
            foreach ($messages as $message) {
                $output .= "<div class='alert alert-".$type."'><button class='close' data-dismiss='alert'></button> $message </div>";
            }
        }
        return $output;
    }

    /**
     * @author Tien Nguyen
     */
    public function convertRXStatus($status)
    {
        return Constant::getRXStatus($status);
    }

    /**
     * @param $typeKey
     * @return mixed
     * @author vinh.nguyen
     */
    public function userTypeLabel($typeKey)
    {
        return Constant::$userTypes[$typeKey];
    }

    /**
     * @author Tien Nguyen
     */
    public function getCurrencyCode()
    {
        if ($this->container->hasParameter('currency_code')) {
            $currency_code = $this->container->getParameter('currency_code');
        } else {
            $currency_code =  "MYR";
        }
        
        return $currency_code;
    }

    /**
     * get list week in year
     * @author vinh.nguyen
     */
    public function getYearWeeks()
    {
        $weekList = Common::getYearWeeks();
        krsort($weekList);
        return $weekList;
    }

    /**
     * get age of a person
     * @param \DateTime $dateOfBirth
     * @return int
     * @author vinh.nguyen
     */
    public function ageCalculate(\DateTime $dateOfBirth)
    {
        $now = new \DateTime();
        $interval = $now->diff($dateOfBirth);

        return $interval->y;
    }
    
    /**
    * format full date
    * author vinh.nguyen
    */
    public function formatFullDate($stDate)
    {
        if(!empty($stDate)) {
            $date = new \DateTime($stDate);
            return $date->format('m/d/Y H:i:s A');
        }
    }
    
    public function getTimeSlot($key=null)
    {
        $list = Common::getTimeSlotList();
        return isset($list[$key])? $list[$key]: $key;
    }
    
    public function getDayOfWeek($key=null)
    {
        $list = Constant::$dayOfWeek;
        return isset($list[$key])? $list[$key]: $key;
    }
    
    public function itemLog($item, $field, $label, $unit = '')
    {
        $old = $item['oldValue'];
        $new = $item['newValue'];
        
        if(is_array($field)) {
            $oldItems = $newItems = array();
            foreach ($field as $f) {
                if(isset($old[$f]))
                    $oldItems[] = $old[$f];
                if(isset($new[$f]))
                    $newItems[] = $new[$f]; 
            }
            if(implode(',', $oldItems) != implode(',', $newItems)) {
                return $label." is updated<br />Old Value: ".implode(', ', $oldItems)."{$unit}<br />New Value: ".implode(', ', $newItems)."{$unit}<br /><br />";
            }
                
        } else if(isset($old[$field]) 
             &&  isset($new[$field]) 
             && $old[$field] != $new[$field]) {
            $result = $label." is updated<br />Old Value: $old[$field]{$unit}<br />New Value: $new[$field]{$unit}<br /><br />";;
            if(Common::checkChineseFont($old[$field]) || Common::checkChineseFont($new[$field])) {
                return '<span class="font-chinese">'. $result .'</span>';
            }
            return $result;
        }
    }
	
	public function isImage($name)
	{
		$ext = strtolower(substr($name, strrpos($name, ".") + 1));
		
		if (in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
			return true;
		}
		
		return false;
	}
	
	public function hasImage($attachments)
	{
		foreach ($attachments as $attachment) {
			$url = $attachment['urlAttachment'];
			$ext = strtolower(substr($url, strrpos($url, ".") + 1));
			
			if (in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
				return true;
			}
		}
		
		return false;
	}

	public function partyList()
    {
        return Constant::PARTY_LIST;
    }

    public function checkExist($fileUrl)
    {
        $webPath = $this->container->get('kernel')->getRootDir()."/../web";
        $fs = new Filesystem();
        if($fs->exists($webPath."/". $fileUrl))
            return true;

        return false;
    }
	
	public function isInstanceof($var, $instance) {
		return  $var instanceof $instance;
    }
    
    public function getGACode()
    {
        $gaCode = $this->container->getParameter('ga_code_patient_pageview');

        return $gaCode;
    }
}
