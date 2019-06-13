<?php

namespace UtilBundle\Utility;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Common
{
    /**
     * Render paging.
     * This page very simple, no need to build one more template for it. I just add html code directly here.
     *
     * @author: Anh NguyenN
     *
     * How to use in ajax??
     * $(document).on("click","a.p_next", function(e){
     * e.preventDefault();
     * url = $(this).attr('href');
     * data = '';
     * method = 'GET';
     * loadingContainer = $('.content-table');
     * successCallback = function(data){
     * loadingContainer.html(data);
     * };
     * errorCallback =  function(){
     * alert('error');
     * };
     * dataType = 'html';
     *
     * DataService.callAPI(url, data, method, successCallback, errorCallback, loadingContainer, dataType);
     * });
     */
    public static function buildPagination(
        $container,
        $request,
        $totalPages,
        $currentPage,
        $itemPerPage,
        $option = array()
    ) {
        $strReturn = '';
        //get the curent url
        if (isset($option['pageUrl'])) {
            $urlInfo = parse_url($option['pageUrl']);
            if (isset($urlInfo['query'])) {
                parse_str($urlInfo['query'], $array);
                unset($array['page']);
                unset($array['perPage']);
                $urlInfo['query'] = http_build_query($array);
            }
            //url include domain name
            if (isset($urlInfo['scheme']) && isset($urlInfo['host'])) {
                $currentUrl = $urlInfo['scheme'] . '://' . $urlInfo['host'] . $urlInfo['path'] . (isset($urlInfo['query']) ? "?" . $urlInfo['query'] : '');
            } else//url without domain name
            {
                $currentUrl = $urlInfo['path'] . (isset($urlInfo['query']) ? "?" . $urlInfo['query'] : '');
            }
        } else {
            //just back up function 'cause $currentUrl is set above
            $currentRoute = $request->attributes->get('_route');
            $currentUrl = $container->get('router')
                ->generate($currentRoute, $option, true);
        }

        //how many page be display?
        $numPageLinksToDisplay = Constant::NUM_PAGE_LINKS_TO_DISPLAY;

        //if have paging
        if ($totalPages > 1) {
            //build url
            if ($currentPage > 0) {
                $preLink = $currentUrl . (strpos($currentUrl, '?') ? '&' : '?') . http_build_query(
                        array(
                            "page" => $currentPage - 1,
                            "perPage" => $itemPerPage
                        )
                    );
            } else {
                $preLink = '#';
            }
            
            $nextLink = $currentUrl . (strpos($currentUrl, '?') ? '&' : '?') . http_build_query(
                        array(
                            "page" => ($currentPage < $totalPages - 1) ? $currentPage + 1 : $currentPage,
                            "perPage" => $itemPerPage
                        )
                    );
            
            if(isset($option['onlyPrevNext'])) {
                if(($currentPage == 0)) {
                    $strReturn .= '
                      <span class="p_pre btn btn-sm blue btn-outline">
                          <i class="fa fa-angle-left"></i>
                      </span>';
                } else {
                    $strReturn .= '
                      <a href="'.$preLink.'" class="p_pre btn btn-sm blue btn-outline">
                          <i class="fa fa-angle-left"></i>
                      </a>';
                }
                
                if(($currentPage == $totalPages - 1)) {
                    $strReturn .= '
                      <span class="p_next btn btn-sm blue btn-outline">
                          <i class="fa fa-angle-right"></i>
                      </span>';
                } else {
                    $strReturn .= '
                      <a href="'.$nextLink.'"  rel="' . (($currentPage + 1 > $totalPages - 1) ? ($totalPages - 1) : ($currentPage + 1)) . '" class="p_next btn btn-sm blue btn-outline">
                          <i class="fa fa-angle-right"></i>
                      </a>';
                }
            } else {
            
                //html code to display paging
                $strReturn = '<ul class="pagination pagination-item-page">';
                /*
                 *
                    <a href="'.$currentUrl.'" class="btnFirst ' . (($currentPage == 0) ? 'disabled' : '') . '" rel="0">&lt;&lt;</a>
                    <a href="'.$preLink.'" class="btnPrev ' . (($currentPage == 0) ? 'disabled' : '') . '" rel="' . (($currentPage - 1 < 0) ? 0 : $currentPage - 1) . '">&lt;</a>
                 * */
                //try to catch and display few page in large page number
                if($currentPage > ($numPageLinksToDisplay - 2))
                    $numPageLinksToDisplay = 3;

                if ($totalPages > $numPageLinksToDisplay) {
                    if ($currentPage == 0) {
                        $firstPage = 1;
                        $lastPage = $numPageLinksToDisplay;
                    } elseif ($currentPage == $totalPages - 1) {
                        $firstPage = $currentPage - $numPageLinksToDisplay;
                        $lastPage = $totalPages;
                    } else {
                        $averageNumPageLinks = $numPageLinksToDisplay; // 2  %}
                        $firstPage = $currentPage - $averageNumPageLinks + 1;
                        $lastPage = $currentPage + $averageNumPageLinks + 1;

                        if ($firstPage <= 0) {
                            $firstPage = 1;
                            $lastPage = $firstPage + $numPageLinksToDisplay - 1;
                        }

                        if ($lastPage > $totalPages) {
                            $lastPage = $totalPages;
                            //$firstPage = $lastPage - $numPageLinksToDisplay + 1;
                        }
                    }
                } else {
                    $lastPage = $totalPages;
                    $firstPage = 1;
                }

                if(($currentPage == 0)) {
                    $strReturn .= '<li><span title="Prev"><i class="fa fa-angle-left"></i></span></li>';
                } else {
                    $strReturn .= '<li><a class="p_pre" href="' . $preLink . '" title="Prev"><i class="fa fa-angle-left"></i></a></li>';
                    if($firstPage > $numPageLinksToDisplay) {
                        $firstLink = $currentUrl . (strpos($currentUrl, '?') ? '&' : '?') . http_build_query(
                                array(
                                    "page" => 0,
                                    "perPage" => $itemPerPage
                                )
                            );
                        $strReturn .= '<li><a href="'.$firstLink.'" class="btnPage" rel="'.$firstPage.'">1</a></li>';
                        $strReturn .= '<li><a href="javascript:void();">...</a></li>';
                    }
                }

                //build url for each page
                for ($i = $firstPage; $i <= $lastPage; $i++) {
                    $pageUrl = $currentUrl . (strpos($currentUrl, '?') ? '&' : '?') . http_build_query(
                            array(
                                "page" => $i - 1,
                                "perPage" => $itemPerPage
                            )
                        );

                    if ($currentPage + 1 == $i) {
                        $strReturn .= '<li class="active"><span class="current">' . $i . '</span></li>';
                    } else {
                        $strReturn .= '<li><a href="' . $pageUrl . '" class="btnPage ' . (($currentPage + 1 == $i) ? 'active' : '') . '" rel="' . ($i - 1) . '">' . $i . '</a></li>';
                    }
                }
                //echo $totalPages;
                $lastLink = $currentUrl . (strpos($currentUrl, '?') ? '&' : '?') . http_build_query(
                    array(
                        "page" => $totalPages - 1,
                        "perPage" => $itemPerPage
                    )
                );

                if(($currentPage == $totalPages - 1)){
                    $strReturn .= '<li class="next"><span title="Next"><i class="fa fa-angle-right"></i></span></li>';
                } else {
                    if ($lastPage != $totalPages) {
                        $strReturn .= '<li><a href="javascript:void(0);">...</a></li>';
                        $strReturn .= '<li><a href="'.$lastLink.'" class="btnPage" rel="'.($totalPages - 1).'">'.$totalPages.'</a></li>';
                    }
                    $strReturn .= '<li class="next"><a class="p_next" rel="' . (($currentPage + 1 > $totalPages - 1) ? ($totalPages - 1) : ($currentPage + 1)) . '" href="' . $nextLink . '" title="Next"><i class="fa fa-angle-right"></i></a></li>';
                }

                /* $strReturn .= '<a href="'.$nextLink.'" class="btnNext ' . (($currentPage == $totalPages - 1) ? 'disabled' : '') . '" rel="' . (($currentPage + 1 > $totalPages - 1) ? ($totalPages - 1) : ($currentPage + 1)) . '">&gt;</a>
                   <a href="'.$lastLink.'" class="btnLast ' . (($currentPage == $totalPages - 1) ? 'disabled' : '') . '" rel="' . ($totalPages - 1) . '">&gt;&gt;</a>';*/
                $strReturn .= '</ul>';
            }

        }
        $strReturn .= '
                <input type="hidden" name="items_per_page" id="items_per_page" value="' . (int)$itemPerPage . '" />
                <input type="hidden" id="current_page" value="' . (int)$currentPage . '" />
                <input type="hidden" id="total_page" value="' . (int)$totalPages . '" />';

        return $strReturn;
    }

    public static function formatNameXero($name) {
        $name = str_replace(array("\\","/","'",'`',',','%',"&",'-',' ','(',')',':'), '',$name);
        return $name;
    }

    /**
     * get content from csv file
     * @return array
     * @author vinh.nguyen
     */
    public static function getContentCSV($csvFile)
    {
        $filePath = $csvFile->getPathName();
        $rows = array();
        if (($handle = fopen($filePath, "r")) !== FALSE) {
            while (($row = fgetcsv($handle, null, ";")) !== FALSE) {
                $rows[] = explode(",", $row[0]);
            }
            fclose($handle);
        }
        return $rows;
    }

    public static function pf($data)
    {
        echo "<pre>";
        if(is_array($data))
            print_r($data);
        else
            var_dump($data);
        echo "<pre>";
    }

    /**
     * get current router
     * @param $container
     * @return null|string
     * @author vinh.nguyen
     */
    public static function getCurrentRouter($container)
    {
        $router = null;
        $pathInfo = $container->get('request')->server->get('REQUEST_URI', '/');
        $arrRouter = explode('/', $pathInfo);

        if(isset($arrRouter[1])) {
            $arrExt = explode('.', $arrRouter[1]);
            if(end($arrExt) == 'php' && isset($arrRouter[2])) {
                $router = strtolower($arrRouter[2]);
            } else {
                $router = strtolower($arrRouter[1]);
            }
        }

        return $router;
    }

    /**
     * Get list Time Slot
     * @author vinh.nguyen
     * @return array
     */
    public static function getTimeSlotList()
    {
        $timeSlot = array();
        for ($i = 0; $i < 24; $i++) {
            $originalHour = $i;
            $hour = $i;
            for ($j = 0; $j < 2; $j++) {
                $moment = $j * 30;
                if ($moment < 10) {
                    $minutes = '0' . $moment;
                } else {
                    $minutes = $moment;
                }
                $datetime = intval($originalHour . $minutes);
                $datetimeString = $hour . ':' . $minutes;
                $datetimeString = \DateTime::createFromFormat('d-m-Y H: i', "01-01-1990 $datetimeString");
                $datetimeString = $datetimeString->format('h: i A');

                $timeSlot[$datetime] = $datetimeString;
            }
        }
        return $timeSlot;
    }

    /**
     * Get micro time in float
     *
     * @return float
     */
    public static function getMicroTimeInFloat()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float) $sec);
    }

    /**
     * XML to Array
     * @param $xml
     * @return mixed
     */
    public static function xml2Array($xml)
    {
        $result = array();
        function normalizeSimpleXML($obj, &$result) {
            $data = $obj;
            if (is_object($data)) {
                $data = get_object_vars($data);
            }
            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    $res = null;
                    normalizeSimpleXML($value, $res);
                    if (($key == '@attributes') && ($key)) {
                        $result = $res;
                    } else {
                        $result[$key] = $res;
                    }
                }
            } else {
                $result = $data;
            }
        }
        normalizeSimpleXML(simplexml_load_string($xml), $result);
        return $result;
    }

    /**
     * string to date
     * @author vinh.nguyen
     */
    public static function st2date($st, $isDateTime = false)
    {
        $res = "";
        $arr = str_split($st, 2);
        $count = count($arr);
        if($count == 7) {
            $res = implode("-", array($arr[2] . $arr[3], $arr[1], $arr[0])) . " " . implode(":", array($arr[4], $arr[5], $arr[6]));
            if($isDateTime)
                return new \DateTime($res);
        } elseif($count == 4) {
            $res = implode("-", array($arr[0].$arr[1],$arr[2],$arr[3]));
            if($isDateTime)
                return new \DateTime($res);
        }
        return $res;
    }

    /**
     * generate PO number
     * @author vinh.nguyen
     */
    public static function generatePONumber($params = array())
    {
        $poDate = $params['poDate'];
        $type = $params['type'];
        $prefix = $params['prefix'];
        $courierRateCode = $params['courierRateCode']; //"MYW"

        $y = $poDate->format("Y");
        $m = $poDate->format("m");
        $d = $poDate->format("d");
        
        //STRIKE-340 - Daily PO Running Number
        if(isset($params['poRunningNumber']) && !empty($params['poRunningNumber'])) {
            $number = $params['poRunningNumber'];
        } else {
            $number = !empty($prefix)? $poDate->format("W"): $d;
        }
        $number = sprintf("%'.04d", $number);

        if($type == 'pharmacy') {
            $pharmacyCode = isset($params['pharmacyCode'])?$params['pharmacyCode']:  "BJY";
            $poNo = array($prefix."PO", "SG", "PP", $pharmacyCode, $y, $m, $number);
        } else {
            //delivery: PO/DP/WTM/MYW/2017/09/03
          if($prefix == "W")
            $poNo = array("WPO", "DP", "WTM", $y, $m, $number);
          else 
            $poNo = array("PO", "SG", "DP", "WTM", $courierRateCode, $y, $m, $number);
        }
        return implode("/", $poNo);
    }
    
    /**
     * get range date of this week
     * @author vinh.nguyen
     */
    public static function getWeek($date = 'now', $isFormat = false, $weekCycle = 1)
    {
        $dateTime = new \DateTime($date);
        $start = clone $dateTime;
        $start->modify("last saturday");

        if($weekCycle == 1) {
          $end = clone($start);
          $end->modify('+6 days');
          
           if(strtotime($dateTime->format("Y-m-d")) > strtotime($end->format("Y-m-d")) ) {
               $start = clone $dateTime;
               $end = clone $start;
               $end->modify('+6 days');
           }
         
        } else {
            //2 weeks
            $start->modify('-6 days');
            $end = clone($dateTime);
            $end->modify('+7 days');
        }

        $end->setTime(23, 59);

        return array(
            'start' => $isFormat? $start->format("Y-m-d"): $start,
            'end'   => $isFormat? $end->format("Y-m-d"): $end,
            'cycle' => $end->format("Y").".".$end->format("W")
        );
    }

    /**
     * get range week of year
     * @author vinh.nguyen
     */
    public static function getYearWeeks()
    {
        $dateTime = new \DateTime('now');
        $end = clone $dateTime;
        $end->modify("+1 weeks");
        $start = clone $end;
        $start->modify("-1 year");

        $fweek = self::getWeek($start->format("Y-m-d"), true);
        $lweek = self::getWeek($end->format("Y-m-d"), true);

        $weekDates = [];
        while($fweek['end'] != $lweek['end']){
            $weekDates [] = $fweek;
            $date = new \DateTime($fweek['end']);
            $date->modify('next day');
            $fweek = self::getWeek($date->format("Y-m-d"), true);
        }
        $weekDates [] = $lweek;

        return $weekDates;
    }

    /**
     * build address
     * @param   $addressInfo [ the address info include line1, line2, line3, postalCode, city, country]
     * @return string
     */
    public static function getAddress($addressInfo, $ispdf = false) {
        if(empty($addressInfo))
            return "";

        $lines   = implode(' ', array_filter(array($addressInfo['line1'], $addressInfo['line2'], $addressInfo['line3'])) );
        if($ispdf) {
            if($addressInfo['country'] == Constant::SINGAPORE_NAME) {
                return implode(', ', array_filter(array($lines, $addressInfo['postalCode'])));
            } else {
                return implode(', ', array_filter(array($lines, $addressInfo['postalCode'], $addressInfo['city'], $addressInfo['state'])) );
            }
        }        
        $address = implode(', ', array_filter(array($lines, $addressInfo['postalCode'], $addressInfo['city'], $addressInfo['state'], $addressInfo['country'])) );
        
        return $address;
    }

    /**
     * @author Tien Nguyen
     * @param array|object $rxLine rxLine
     * @param object $entityManager EntityManager
     */
    public static function generateSIGPreview($rxLine, $entityManager)
    {
        $value = $rxLine;
        if (is_object($rxLine)) {
            $value = array();
            $value['prn'] = $rxLine->getIsTakenAsNeeded();
            $value['action'] = $rxLine->getDosageAction();
            $value['dose'] = $rxLine->getDosageQuantity();
            $value['doseUnit'] = $rxLine->getDosageForm();
            $value['frequency'] = $rxLine->getFrequencyQuantity();
            $value['frequencyDuration'] = $rxLine->getFrequencyDurationUnit();
            $value['withMeal'] = json_decode($rxLine->getIsTakenWithFood());
        }

        $sig = 'Take as needed';

        if (!empty($value['prn'])) {
            return $sig;
        }

        $action = $value['action'];
        if ('others' == $action && !empty($value['otherAction'])) {
            $action = $value['otherAction'];
        }
        $dose = $value['dose'];
        if ('others' == $dose && !empty($value['otherDose'])) {
            $dose = $value['otherDose'];
        }
        $doseUnit = $value['doseUnit'];
        if ('others' == $doseUnit && !empty($value['otherDoseUnit'])) {
            $doseUnit = $value['otherDoseUnit'];
        }
        if ($dose > 1) {
            $dosageForm = $entityManager
                ->getRepository('UtilBundle:DosageForm')
                ->findOneBy(array('name' => $doseUnit));
            if ($dosageForm) {
                $doseUnit = $dosageForm->getPluralName();
            }
        }

        $frequency = $value['frequency'];
        if ('others' == $frequency && !empty($value['otherFrequency'])) {
            $frequency = $value['otherFrequency'];
        }
        $frequencyDuration = $value['frequencyDuration'];
        if ('others' == $frequencyDuration && !empty($value['otherDurationUnit'])) {
            $frequencyDuration = $value['otherDurationUnit'];
        }

        $arrSig = array($action, $dose, $doseUnit);
        if ('n/a' != $frequency) {
            $arrSig[] = $frequency;
        }
        if ('n/a' != $frequencyDuration) {
            $arrSig[] = 'for';
            $arrSig[] = $frequencyDuration;
        }

        $withMeal = array(
            'Before Meal',
            'After Meal',
            'With Meal',
            'Take in the morning',
            'Take at night'
        );

        $result = array();
        if (isset($value['withMeal']) && is_array($value['withMeal'])) {
            foreach ($value['withMeal'] as $meal) {
                $result[] = $withMeal[$meal];
            }
        }

        if ($result) {
            $withMeal = '(' . implode(', ', $result) . ')';
            $arrSig[] = $withMeal;
        }

        $sig = implode(' ', $arrSig);

        return $sig;
    }

    /**
     * encrypt string
     * @author vinh.nguyen
     */
    public static  function encryptTripleDes($input, $ky)
    {
        $key = $ky;
        $size = mcrypt_get_block_size(MCRYPT_TRIPLEDES, 'ecb');
        $input = self::pkcs5_pad($input, $size);
        $td = mcrypt_module_open(MCRYPT_TRIPLEDES, '', 'ecb', '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = self::base64url_encode($data);
        // $data = urlencode($data); // push it out so i can check it works
        return $data;
    }

    public static function decryptTripleDes($crypt, $ky)
    {
        //  $crypt = urldecode($crypt);
        $crypt = self::base64url_decode($crypt);
        $key = $ky;
        $td = mcrypt_module_open(MCRYPT_TRIPLEDES, '', 'ecb', '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $decrypted_data = mdecrypt_generic($td, $crypt);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $decrypted_data = self::pkcs5_unpad($decrypted_data);
        $decrypted_data = rtrim($decrypted_data);
        return $decrypted_data;
    }

    public static function base64url_encode($s) {
        return str_replace(array('+', '/'), array('-', '_'), base64_encode($s));
    }

    public static function base64url_decode($s) {
        return base64_decode(str_replace(array('-', '_'), array('+', '/'), $s));
    }

    public static function pkcs5_pad($text, $blocksize)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    public static function pkcs5_unpad($text)
    {
        $pad = ord($text{strlen($text) - 1});
        if ($pad > strlen($text))
            return false;
        return substr($text, 0, - 1 * $pad);
    }


    /**
     * Get Rate
     * @param type $currencyFrom
     * @param type $currencyTo
     * @return type
     */
    public static function getRate($currencyFrom, $currencyTo, $em) {
        $rate = $em->getRepository('UtilBundle:FxRate')->getRate($currencyFrom, $currencyTo);
        
        return $rate->getRate();
    }
    
    /** 
    * message upload file
    */
    public static function messageUploadFile($container, UploadedFile $file)
    {
        $uploadDir = "/messages";
        $fileName = date('YmdHis')."-".trim(preg_replace('/\s+/','', $file->getClientOriginalName()));
        $ext = strtolower($file->guessExtension());

        $targetDir = $container->getParameter('upload_directory').$uploadDir;
        if (Common::createDirIfNotExists($targetDir)) {
            $upload = $file->move($targetDir, $fileName);
            if($upload) {
                $imageExt = array('gif', 'jpg', 'jpeg', 'png');
                return array(
                    'url' => "/uploads".$uploadDir."/".$fileName,
                    'name' => $fileName,
                    'isImage' => in_array($ext, $imageExt)? true: false
                );
            }
        }

        return null;
    }
    
    /**
    * get target email
    */
    public static function getTargetEmail($em, $data, $messageType='admin', $siteId = null)
    {
        //Gmedes Admin, All Agents, All Doctors, Customer Care
        $groupEmail = array(
            Constant::MESSAGE_GROUP_ADMIN, 
            Constant::MESSAGE_GROUP_AGENT, 
            Constant::MESSAGE_GROUP_DOCTOR, 
            Constant::MESSAGE_GROUP_CUSTOMER_CARE
        );
        $result = array();
        if(!empty($data)) {
            $list = array_map('strtolower', array_map('trim', explode(',', $data)));

            if (strtolower($messageType) == 'doctor' && in_array(Constant::MESSAGE_GROUP_PHARMACY_SERVICE, $list)) {
                $list = array_merge($list, array(Constant::MESSAGE_GROUP_ADMIN, Constant::MESSAGE_GROUP_CUSTOMER_CARE));
                $list = array_unique($list);
            }
            
            foreach ($list as $v) {
                $item = strtolower($v);
                
                //Force replace: Ask the Pharmacist -> Customer Care
                if($item == Constant::MESSAGE_GROUP_ASK_THE_PHARMACIST)
                    $item = Constant::MESSAGE_GROUP_CUSTOMER_CARE;
                    
                $receiverGroup = str_replace(' ', '_', $item);
                switch (strtolower($messageType)) {
                  case 'admin':
                    //admin -> doctor, agent, customer care
                    //role: 1-admin, 2-agent, 4-doctor, 6-customer care, 8-pharmacy service
                    $arrGroup = array(
                      Constant::MESSAGE_GROUP_DOCTOR, 
                      Constant::MESSAGE_GROUP_AGENT,
                      Constant::MESSAGE_GROUP_CUSTOMER_CARE,
                      Constant::MESSAGE_GROUP_PHARMACY_SERVICE
                    );
                    if(in_array($item, $arrGroup)) {
                        $roleId = 2;
                        if($item == Constant::MESSAGE_GROUP_DOCTOR)
                          $roleId = 4;
                          
                        if($item == Constant::MESSAGE_GROUP_CUSTOMER_CARE)
                          $roleId = 6;

                        if($item == Constant::MESSAGE_GROUP_PHARMACY_SERVICE)
                            $roleId = 8;
                          
                        $listItems = $em->getRepository('UtilBundle:User')->getUserByRole($roleId);
                        if(!empty($listItems)) {
                            $rxSite = null;
                            foreach ($listItems as $v) {
                                if ($siteId) {
                                    $agentDoctor = $em->getRepository('UtilBundle:Doctor')->getDoctorActiveAgentByEmail($v['email']);
                                    if ($agentDoctor) {
                                        $agent = $agentDoctor->getAgent();

                                        if ($agent->getParent()) {
                                            $rxSite = $agent->getParent()->getSite();
                                        } else {
                                            $rxSite = $agent->getSite();
                                        }
                                    } else {
                                        continue;
                                    }

                                    if ($rxSite && $rxSite->getId() != $siteId) {
                                        continue;
                                    }
                                }

                                $result[] = array(
                                  'email' => $v['email'],
                                  'receiverGroup' => $receiverGroup
                               );
                            }
                        }
                    } else {
                        $result[] = array('email' => $item);
                    }
                    break;
                    
                  case 'doctor':
                      //doctor -> admin, customer care
                      $arrGroup = array(
                        Constant::MESSAGE_GROUP_ADMIN, 
                        Constant::MESSAGE_GROUP_CUSTOMER_CARE,
                        Constant::MESSAGE_GROUP_PHARMACY_SERVICE
                      );
                      if(in_array($item, $arrGroup)) {
                          $roleId = 1;
                          if($item == Constant::MESSAGE_GROUP_CUSTOMER_CARE)
                            $roleId = 6;

                          if($item == Constant::MESSAGE_GROUP_PHARMACY_SERVICE)
                              $roleId = 8;
                            
                          $listItems = $em->getRepository('UtilBundle:User')->getUserByRole($roleId);
                          if(!empty($listItems)) {
                              foreach ($listItems as $v) {
                                  $result[] = array(
                                    'email' => $v['email'],
                                    'receiverGroup' => $receiverGroup
                                 );
                              }
                          }
                      }
                      break;
                    
                  case 'agent':
                      //agent -> admin
                      $arrGroup = array(
                        Constant::MESSAGE_GROUP_ADMIN
                      );
                      if(in_array($item, $arrGroup)) {
                          $roleId = 1;
                          
                          $listItems = $em->getRepository('UtilBundle:User')->getUserByRole($roleId);
                          
                          if(!empty($listItems)) {
                              foreach ($listItems as $v) {
                                  $result[] = array(
                                    'email' => $v['email'],
                                    'receiverGroup' => $receiverGroup
                                 );
                              }
                          }
                      }
                      break;
                      
                    case 'customer_care':
                      
                        $arrGroup = array(
                            Constant::MESSAGE_GROUP_ADMIN,
                            Constant::MESSAGE_GROUP_DOCTOR,
                            Constant::MESSAGE_GROUP_PHARMACY_SERVICE
                        );
                        
                        if (in_array($item, $arrGroup)) {
                            $roleId = 1;                          
                            if ($item == Constant::MESSAGE_GROUP_DOCTOR) {
                                $roleId = 4;
                            }
                            if($item == Constant::MESSAGE_GROUP_PHARMACY_SERVICE)
                                $roleId = 8;
                            $listItems = $em->getRepository('UtilBundle:User')->getValidListEmailByRole($roleId);
                            if (!empty($listItems)) {
                                foreach ($listItems as $v) {
                                    $result[] = array(
                                        'email' => $v['email'],
                                        'receiverGroup' => $receiverGroup
                                    );
                                }
                            }
                        } else {
                            $roles = [1, 4, 6];

                            $email = $em->getRepository('UtilBundle:User')->getValidEmailByRole($roles, $item);
                            if (!empty($email)) {
                                $result[] = array(
                                    'email' => $email['email'],
                                    'receiverGroup' => $receiverGroup
                                );
                            }
                        }

                        break;

                    case 'pharmacy_service':

                        $arrGroup = array(
                            Constant::MESSAGE_GROUP_ADMIN,
                            Constant::MESSAGE_GROUP_DOCTOR,
                            Constant::MESSAGE_GROUP_CUSTOMER_CARE
                        );

                        if (in_array($item, $arrGroup)) {
                            $roleId = 1;
                            if ($item == Constant::MESSAGE_GROUP_DOCTOR) {
                                $roleId = 4;
                            }
                            if($item == Constant::MESSAGE_GROUP_CUSTOMER_CARE)
                                $roleId = 6;
                            $listItems = $em->getRepository('UtilBundle:User')->getValidListEmailByRole($roleId);
                            if (!empty($listItems)) {
                                foreach ($listItems as $v) {
                                    $result[] = array(
                                        'email' => $v['email'],
                                        'receiverGroup' => $receiverGroup
                                    );
                                }
                            }
                        } else {
                            $roles = [1, 4, 8];

                            $email = $em->getRepository('UtilBundle:User')->getValidEmailByRole($roles, $item);
                            if (!empty($email)) {
                                $result[] = array(
                                    'email' => $email['email'],
                                    'receiverGroup' => $receiverGroup
                                );
                            }
                        }

                        break;
                }
                
            }
        }
        return $result;
    }
	
	public static function getAddressFromEntity($address, $break = false)
	{
		$result = "";
		if(empty($address)){
		    return $result;
        }
		$result .= trim($address->getLine1() . " " . trim($address->getLine2() . " " . $address->getLine3()));
		
		$city = $address->getCity();
		$state = $city ? $city->getState() : null;
		$country = $city ? $city->getCountry() : null;
		
		$city = trim ($address->getPostalCode() . " " . ($city ? $city->getName() : ''));
		$result .= !empty($city) ? ($break ? ",<br/>" . $city : ", " . $city) : '';
		$result .= $state ? ", " . $state->getName() : '';
		$result .= $country ? ", " . $country->getName() : '';
		
		return $result;
	}

    /**
     * get week period
     * $date = Y-m-d
     */
    public static function getWeekPeriod($date)
    {
        $dateTime = new \DateTime($date);
        $start = clone $dateTime;
        $start->modify("last saturday");
        $end = clone($start);
        $end->modify('+6 days');

        if(strtotime($dateTime->format("Y-m-d")) > strtotime($end->format("Y-m-d")) ) {
            $start = clone $dateTime;
            $end = clone $start;
            $end->modify('+6 days');
        }

        return array(
            'start' => $start,
            'end'   => $end
        );
    }

    /**
     * encode hex a string
     * @param $value
     * @return string
     */
    public static function encodeHex($value, $prefix = Constant::HASHING_PREFIX)
    {
        $paddedId = sprintf("%08s", dechex($value));
        $strArr = str_split($paddedId, 2);
        return $prefix.implode(array_reverse($strArr));
    }

    /**
     * decode hex a string
     * @param $value
     * @return int
     */
    public static function decodeHex($value, $prefix = Constant::HASHING_PREFIX)
    {
        if($prefix)
            $value = substr($value, strlen($prefix));
        $strArr = str_split($value, 2);
        $paddedId = implode(array_reverse($strArr));
        return hexdec($paddedId);
    }

    /**
     * remove space of $items
     */
    public static function removeSpaceOf($items)
    {
        $result = $items;
        if(is_array($items)) {
            foreach($items as $k=>$v) {
                if(is_string($v) || is_numeric($v))
                    $result[$k] = trim($v);
            }
        } elseif(is_string($items) || is_numeric($items)) {
            $result = trim($items);
        }
        return $result;
    }

    /*
     * Check if current login belongs to current doctor
     *
     * */
    public static function isMainDoctorLogin($gmedUser, $entityManager) {
        $doctorId = $gmedUser->getId();
        $doctor = $entityManager->getRepository('UtilBundle:Doctor')->find($doctorId);

        $loginEmail = $gmedUser->getEmail();
        $doctorEmail = $doctor->getPersonalInformation()->getEmailAddress();

        if ($loginEmail != $doctorEmail) {
            return false;
        }

        return true;
    }

    /*
     * Check if current login belongs to current agent
     *
     * */
    public static function isMainAgentLogin($gmedUser, $entityManager) {
        $doctorId = $gmedUser->getId();
        $agent = $entityManager->getRepository('UtilBundle:Agent')->find($doctorId);

        $loginEmail = $gmedUser->getEmail();
        $doctorEmail = $agent->getPersonalInformation()->getEmailAddress();

        if ($loginEmail != $doctorEmail) {
            return false;
        }

        return true;
    }
    public static function checkChineseFont($utf8_str){
        return preg_match("/\p{Han}+/u", $utf8_str);
    }

    public static function createDirIfNotExists($targetDir)
    {
        if (!is_dir($targetDir)) {
            return mkdir($targetDir, 0755, true);
        }

        return true;
    }

    public static function restrictFileAccess($container, $templating = null)
    {
        $httpAuthUsers = $container->getParameter('core_media_pdf');
        header('Cache-Control: no-cache, must-revalidate, max-age=0');
        $has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
        $loggedIn = self::httpAuthLoginCheck($httpAuthUsers, $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
        if (!$has_supplied_credentials || !$loggedIn) {
            header('HTTP/1.1 401 Authorization Required');
            header('WWW-Authenticate: Basic realm="Access denied"');
            if ($templating) {
                echo $templating;
            }
            exit;
        }
    }

    public static function httpAuthLoginCheck($httpAuthUsers, $userName, $password)
    {
        $userUsedIdx = array_search($userName, array_column($httpAuthUsers, 'username'));
        if ($userUsedIdx !== false) {
            $userUsed = $httpAuthUsers[$userUsedIdx];
            if ($userName == $userUsed['username'] && $password == $userUsed['password']) {
                return true;
            }
        }
        return false;
    }

    /*
     * append right dot to sentence
     * */
    public static function appendRightDot($str)
    {
        if (strlen(trim($str)) > 0) {
            return rtrim(trim(str_replace('&nbsp;', ' ', $str)), '.') . '.';
        } else {
            return '';
        }
    }

    public static function getCurrentSite($container)
    {
        $sitesParam = $container->getParameter('sites');
        $baseUrl = rtrim($container->get('request')->getUriForPath('/'), '/');
        $currentSite = array_search(substr($baseUrl, strpos($baseUrl, '://') + 3), $sitesParam);

        if ($currentSite == 'parkway') {
            return 'parkway';
        }

        return 'sg';
    }
}
