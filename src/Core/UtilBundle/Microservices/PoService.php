<?php
namespace UtilBundle\Microservices;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\RequestContext;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Response;
use UtilBundle\Utility\Common;
use UtilBundle\Utility\MsgUtils;
use UtilBundle\Utility\Constant;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class PoService
{
    protected $container;
    protected $em;
    protected $templating;
    protected $baseUrl;
    private $pharmacyCode = '';
    public function __construct($container, EntityManager $em, $templating)
    {
        $this->container  = $container;
        $this->em = $em;
        $this->baseUrl = $this->container->getParameter('base_url');
        $currentPharmacy = $em->getRepository('UtilBundle:Pharmacy')->getUsedPharmacy();
        $this->pharmacyCode = $currentPharmacy->getPharmacyCode();
    }

    /**
     * courier PO daily
     * @author vinh.nguyen
     */
    public function courierPoDaily($day = null)
    {
        if(empty($day))
            $day = 'yesterday';
        
        $response = array();
        $start = new \DateTime($day);
        $cycleDate = clone $start;
        if ('friday' != strtolower($cycleDate->format('l'))) {
            $cycleDate->modify('next friday');
        }
        $params = array(
            'start'  => $start,
            'cycle'  => $cycleDate->format("Y").".".$cycleDate->format("W"),
            'status' => Constant::RX_STATUS_DELIVERED
        );

        //get daily items
        $rxItems = $this->em->getRepository('UtilBundle:CourierPoDaily')->getRxBy($params);

        if(!empty($rxItems)) {
            $listItems = array();
            $courierObj = $this->em->getRepository('UtilBundle:Courier')->getUsedCourer();
            $courierInfo = $this->em->getRepository('UtilBundle:Courier')->getCourier($courierObj->getId());
            $platformSettingObj = $this->em->getRepository('UtilBundle:PlatformSettings')->getGstRate();
            $psGstRate = $platformSettingObj['gstRate'] / 100;
            $orderId = "";
            $listOrderIds = array();

            foreach($rxItems as $item) {
                $code = $item['courierRateCode'];
                
                $excludeGSTAmount = $item['shippingCost'] + $item['customTaxByCourier'] + $item['igPermitFeeByCourier'];
                $gstAmount = ($item['isGst'] == 1)? ($excludeGSTAmount * $psGstRate): 0;                
                    
                if(!isset($listItems[$code])) {
                    if($item['courierRateId'] != null)
                        $courierRateObj = $this->em->getRepository('UtilBundle:CourierRate')->find($item['courierRateId']);
                    else
                        $courierRateObj = null;
                    
                    $poDate = $params['start'];
                    $poRunningNumber = $this->em->getRepository('UtilBundle:CourierPoDaily')->getPORunningNumber($poDate);
                    $poNumber = Common::generatePONumber(array(
                        'type' => 'delivery',
                        'poDate' => $poDate,
                        'courierRateCode' => $item['courierRateCode'],
                        'prefix' => '',
                        'poRunningNumber' => $poRunningNumber,
                        'pharmacyCode' => $this->pharmacyCode
                    ));

                    $customerReference = str_replace('/DP', '', $poNumber);
                    $customerReference = str_replace('/', '', $customerReference);

                    $listItems[$code] = array(
                        'courierRate'      => $courierRateObj,
                        'poDate'           => $params['start'],
                        'cycle'            => $params['cycle'],
                        'poNumber'         => $poNumber,
                        'excludeGSTAmount' => $excludeGSTAmount,
                        'gstAmount'        => $gstAmount,
                        'amount'           => $excludeGSTAmount + $gstAmount,
                        'courierName'      => $item['courierName'],
                        'courierEmail'     => $item['courierEmail'],
                        'isGst'            => $item['isGst'],
                        'postCode'         => $item['postCodeShippingAddress'],
                        'customerReference' => $customerReference
                    );
                    $orderId = $item['rxId'];                    
                    if(!in_array($item['rxId'], $listOrderIds)){
                        array_push($listOrderIds, $item['rxId']);
                    }

                } else {
                    if ($orderId != $item['rxId'] && !in_array($item['rxId'], $listOrderIds)) {                        
                        array_push($listOrderIds, $item['rxId']);                                        
                        $listItems[$code]['excludeGSTAmount'] += $excludeGSTAmount;
                        $listItems[$code]['gstAmount'] += $gstAmount;
                        $listItems[$code]['amount'] += $excludeGSTAmount + $gstAmount;
                    }
                    $orderId = $item['rxId'];
                }
                $listItems[$code]['dailyLineId'][] = $item['dailyLineId'];
                $listItems[$code]['boxId'][] = $item['boxId'];                
            }

            $courierPoDailyResult = array();
            $courierPoDailyLineResult = array();
            $files = array();
            foreach($listItems as $daily) { 
                //daily create
                $dailyObj = $this->em->getRepository('UtilBundle:CourierPoDaily')->create($daily);

                if($dailyObj)
                    $courierPoDailyResult[] = $dailyObj->getId();

                //get list box
                $listBox = $this->em->getRepository('UtilBundle:CourierPoDaily')->getBoxBy($daily['boxId']);
                
                //daily line update
                foreach ($daily['dailyLineId'] as $lineId) {
                    //daily line update
                    $courierPoDailyLineResult[] = $this->em->getRepository('UtilBundle:CourierPoDailyLine')->update($lineId, $dailyObj);
                }

                //create PDF file
                $poNumber = $daily['poNumber'];
                $filename = str_replace("/","",$poNumber).".pdf";
                $fs = new Filesystem();
                $parammeters = $this->container->getParameter('media');
                $path = $this->container->get('kernel')->getRootDir()."/../".$parammeters['po_path'];
                if (!$fs->exists($path)) {
                    $fs->mkdir($path);
                }
                $pdfFileDir = $path."/".$filename;

                $template = 'AdminBundle:pdf:delivery-daily-po.html.twig';
                $pdfData = array(
                    'info' => $daily,
                    'lists' => $listBox,
                    'psGstRate' => $psGstRate,
                    'courier' => $courierInfo,
                    'logoUrl' => 'web/bundles/admin/assets/pages/img/logo.png'
                );
                $html = $this->container->get('templating')->render($template, $pdfData);

                //clone a html
                $pathHtml = $path.'/html';
                $fileNameHtml = str_replace('.pdf', '.html', $filename);
                $contentHtml = str_replace("web/bundles", "bundles", $html);
                if (!$fs->exists($pathHtml)) {
                    $fs->mkdir($pathHtml);
                }
                file_put_contents($pathHtml . "/{$fileNameHtml}", $contentHtml);

                $options = new Options();
                $options->set('isRemoteEnabled', TRUE);
                $options->set('isHtml5ParserEnabled', true);
                $options->set('isPhpEnabled', true);

                $dompdf = new Dompdf($options);
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                $canvas = $dompdf->getCanvas();
                $canvas->page_script('
                    $font = $fontMetrics->getFont("helvetica");
                    if($PAGE_NUM == 1) {
                        $pdf->text(535, 155, "$PAGE_NUM of $PAGE_COUNT", $font, 9, array(0,0,0));
                    }
                ');
                $output = $dompdf->output();
                file_put_contents($pdfFileDir, $output);

                //send mail
                if(file_exists($pdfFileDir)) {

                    //update $filename
                    $dailyObj->setFilename($filename);
                    $dailyObj->setUpdatedOn(new \DateTime());
                    $this->em->persist($dailyObj);
                    $this->em->flush();

                    $files[] = $filename;
                    $emailTo = $courierInfo['emailAddress'];
                
                    $mailTemplate = 'AdminBundle:emails:courier-po-daily.html.twig';
                    $mailParams = array(
                        'logoUrl' => $this->baseUrl.'/bundles/admin/assets/pages/img/logo.png',
                        'date' => $start->format("d M Y"),
                        'name' => $courierInfo['name'],
                        'courierName' => $daily['courierName'],
                        'baseUrl' => $this->baseUrl
                    );
                    $dataSendMail = array(
                        'title'  => "Courier PO Daily",
                        'body'   => $this->container->get('templating')->render($mailTemplate, $mailParams),
                        'from'   => $this->container->getParameter('primary_email'),
                        'to'     => array($emailTo, 'accounts@gmedes.com'),
                        'attach' => $pdfFileDir
                    );
                    $this->container->get('microservices.sendgrid.email')->sendEmail($dataSendMail);
                }
            }
            $response['totalDaily'] = count($courierPoDailyResult);
            $response['totalDailyLine'] = count($courierPoDailyLineResult);
            $response['files'] = implode(", ", $files);
        }  else {
            $response['message'] = "No data created.";
        }
        return $response;
    }

    /**
     * courier PO weekly
     * @author vinh.nguyen
     */
    public function courierPoWeekly($week = null, $targetDate = null)
    {
        $response = array();

        if(empty($week)) {
            $wPoDate = new \DateTime();

            if(!empty($targetDate)) {
                $weeklyPoDate = clone $targetDate;
                $lastWeek = clone $weeklyPoDate;
                $lastWeek->modify("last week");
                $lastWeek->modify("last week");
            } else {
                $weeklyPoDate = clone $wPoDate;
                $lastWeek = clone $weeklyPoDate;
                $lastWeek->modify("last week");
            }
            $week = $lastWeek->format("Y-m-d");
        } else {
            $wPoDate = new \DateTime($week);
            $psObj = $this->em->getRepository('UtilBundle:PlatformSettings')->getPaymentSchedule();
            $deliveryDay = (int)$psObj['deliveryFortnightlyPoDay'];
            $settingDay = Constant::$dayOfWeek[$deliveryDay];
            $weeklyPoDate = $wPoDate->modify($settingDay);
            $lastWeek = clone $weeklyPoDate;
            $lastWeek->modify("last week");
            $week = $lastWeek->format("Y-m-d");
        }

        $dates = Common::getWeek($week);

        //get daily items
        $dailyItems = $this->em->getRepository('UtilBundle:CourierPoDaily')->getPODailyBy($dates);

        if(!empty($dailyItems)) {
            $response['dates'] = $dates;
            
            $courierObj = $this->em->getRepository('UtilBundle:Courier')->getUsedCourer();
            $courierInfo = $this->em->getRepository('UtilBundle:Courier')->getCourier($courierObj->getId());
            $amount = 0;
            foreach ($dailyItems as $item) {
                $amount += $item['amount'];
            }
            $poNumber = Common::generatePONumber(array(
                'type'            => 'delivery',
                'poDate'          => $dates['end'],
                'courierRateCode' => '',
                'prefix'          => 'W',
                'pharmacyCode' => $this->pharmacyCode
            ));
                     
            $projectedPaymentDate = $this->em->getRepository('UtilBundle:DoctorMonthlyStatement')->calculateProjectedPaymentDate(4, $weeklyPoDate->format("Y-m-d"));

            $customerReference = str_replace('/DP', '', $poNumber);
            $customerReference = str_replace('/', '', $customerReference);

            $weeklyItems = array(
                'cycle'         => $dates['cycle'],
                'poNumber'      => $poNumber,
                'amount'        => $amount,
                'cycleFromDate' => $dates['start'],
                'cycleToDate'   => $dates['end'],
                'weeklyPoDate'  => $weeklyPoDate,
                'projectedPaymentDate' => $projectedPaymentDate,
                'customerReference' => $customerReference
            );

            //weekly storage
            $weeklyObj = $this->em->getRepository('UtilBundle:CourierPoWeekly')->create($weeklyItems);

            if($weeklyObj)
                $response['total_weekly'] = 1;
            
            //daily update
            $courierPoDailyResult = array();
            foreach ($dailyItems as $item) {
                $poDaily = array(
                    'id' => $item['id'],
                    'poWeekly' => $weeklyObj
                );
                $courierPoDailyResult[] = $this->em->getRepository('UtilBundle:CourierPoDaily')->update($poDaily);
            }
            $response['total_daily'] = count($courierPoDailyResult);

            //create PDF file
            $filename = str_replace("/", "", $poNumber) . ".pdf";
            $fs = new Filesystem();
            $parammeters = $this->container->getParameter('media');
            $path = $this->container->get('kernel')->getRootDir()."/../".$parammeters['po_path'];
            if (!$fs->exists($path)) {
                $fs->mkdir($path);
            }
            $pdfFileDir = $path."/".$filename;

            $template = 'AdminBundle:pdf:delivery-weekly-po.html.twig';
            $pdfData = array(
                'info'    => $weeklyItems,
                'lists'   => $dailyItems,
                'courier' => $courierInfo,
                'logoUrl' => 'web/bundles/admin/assets/pages/img/logo.png'
            );
            
            $html = $this->container->get('templating')->render($template, $pdfData);

            //clone a html
            $pathHtml = $path.'/html';
            $fileNameHtml = str_replace('.pdf', '.html', $filename);
            $contentHtml = str_replace("web/bundles", "bundles", $html);
            if (!$fs->exists($pathHtml)) {
                $fs->mkdir($pathHtml);
            }
            file_put_contents($pathHtml . "/{$fileNameHtml}", $contentHtml);

            $options = new Options();
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $canvas = $dompdf->getCanvas();
            $canvas->page_script('
                $font = $fontMetrics->getFont("helvetica");
                if($PAGE_NUM == 1) {
                    $pdf->text(535, 135, "$PAGE_NUM of $PAGE_COUNT", $font, 9, array(0,0,0));
                }
            ');
            $output = $dompdf->output();
            file_put_contents($pdfFileDir, $output);

            //send mail
            if(file_exists($pdfFileDir)) {

                //update $filename
                $weeklyObj->setFilename($filename);
                $weeklyObj->setUpdatedOn(new \DateTime());
                $this->em->persist($weeklyObj);
                $this->em->flush();

                $response['files'] = $filename;
                $emailTo = $courierInfo['emailAddress'];
                $courierName = $courierInfo['name'];
                $mailTemplate = 'AdminBundle:emails:courier-po-weekly.html.twig';
                $mailParams = array(
                    'logoUrl' => $this->baseUrl.'/bundles/admin/assets/pages/img/logo.png',
                    'startDate' => $dates['start']->format("d"),
                    'endDate' => $dates['end']->format("d M Y"),
                    'name' => $courierName,
                    'baseUrl' => $this->baseUrl
                );
                $dataSendMail = array(
                    'title'  => "Courier PO Weekly (".$mailParams['startDate']." - ".$mailParams['endDate'].")",
                    'body'   => $this->container->get('templating')->render($mailTemplate, $mailParams),
                    'from'   => $this->container->getParameter('primary_email'),
                    'to'     => $emailTo,
                    'attach' => $pdfFileDir
                );
                $this->container->get('microservices.sendgrid.email')->sendEmail($dataSendMail);
            }
        }

        return $response;
    }

    /**
     * pharmacy PO daily
     * @author vinh.nguyen
     */
    public function pharmacyPoDaily($day = null)
    {
        if(empty($day))
            $day = 'yesterday';
        
        $response = array();
        $start = new \DateTime($day);
        $cycleDate = clone $start;
        if ('friday' != strtolower($cycleDate->format('l'))) {
            $cycleDate->modify('next friday');
        }

        $params = array(
            'start'  => $start,
            'cycle'  => $cycleDate->format("Y").".".$cycleDate->format("W"),
            'status' => Constant::RX_STATUS_COLLECTED,
            'lineType' => Constant::RX_LINE_TYPE_ONE //1: medicine
        );

        $rxItems = $this->em->getRepository('UtilBundle:PharmacyPoDaily')->getRxBy($params);
        if(!empty($rxItems)) {
            $platformSettingObj = $this->em->getRepository('UtilBundle:PlatformSettings')->getGstRate();
            $psGstRate = $platformSettingObj['gstRate'] / 100;
            $totalAmount = 0;
            $excludeGstAmount = 0;
            $gstAmount = 0;

            foreach ($rxItems as $item) {
                $itemTotal = $item['costPrice'] * $item['quantity'];
                $totalAmount += $item['orderValue'];
                $excludeGstAmount += $itemTotal;
                
                if($item['gstCode'] == 'SRS' || $item['gstCode'] == 'SRP') {
                    $gstAmount += $itemTotal;
                }
            }

            //total of exclude_gst_amount x platform_setting.gst_rate
            $gstAmount = $gstAmount * $psGstRate;

            //exclude_gst_amount+ gst_amount
            $includeGstAmount = $excludeGstAmount + $gstAmount;

            $pharmacyObj = $this->em->getRepository('UtilBundle:Pharmacy')->getUsedPharmacy();

            $poDate = $params['start'];
            $poRunningNumber = $this->em->getRepository('UtilBundle:PharmacyPoDaily')->getPORunningNumber($poDate);
            $poNumber = Common::generatePONumber(array(
                'type'   => 'pharmacy',
                'poDate' => $poDate,
                'courierRateCode' => '',
                'prefix' => '',
                'poRunningNumber' => $poRunningNumber,
                'pharmacyCode' => $this->pharmacyCode
            ));
            $dailyItems = array(
                'pharmacy'         => $pharmacyObj,
                'poDate'           => $poDate,
                'cycle'            => $params['cycle'],
                'poNumber'         => $poNumber,
                'totalAmount'      => $totalAmount,
                'excludeGstAmount' => $excludeGstAmount,
                'includeGstAmount' => $includeGstAmount,
                'gstAmount'        => $gstAmount,
                'customerReference' => str_replace('/', '', $poNumber)
            );

            //daily create
            $dailyObj = $this->em->getRepository('UtilBundle:PharmacyPoDaily')->create($dailyItems);

            if($dailyObj)
                $response['totalDaily'] = 1;

            //daily line update
            $listDailyLineId = array();
            foreach ($rxItems as $item) {
                $listDailyLineId[] = $this->em->getRepository('UtilBundle:PharmacyPoDailyLine')->update($item["dailyLineId"], $dailyObj);
            }

            $response['totalDailyLine'] = count($listDailyLineId);

            $pharmacyInfo = $this->em->getRepository('UtilBundle:Pharmacy')->getPharmacy($pharmacyObj->getId());
            $filename = str_replace("/","",$poNumber).".pdf";
            $fs = new Filesystem();
            $parammeters = $this->container->getParameter('media');
            $path = $this->container->get('kernel')->getRootDir()."/../".$parammeters['po_path'];
            if (!$fs->exists($path)) {
                $fs->mkdir($path);
            }
            $pdfFileDir = $path."/".$filename;

            // STRIKE-1139
            $rep = $this->em->getRepository('UtilBundle:PharmacyPoDaily');
            $totalAmountGst = 0;
            $totalGst = 0;
            $listTotalGst = 0;
            foreach ($rxItems as &$item) {
                $item['trackingNumber'] = $rep->getTrackingNumberByRx($item['rxLineId']);
                if($item['gstCode'] == 'SRS' || $item['gstCode'] == 'SRP') {
                    $item['gstPrice'] = number_format( $item['costPrice']*(1 + $psGstRate),2);
                    $totalGst +=  $item['costPrice']* $item['quantity']* $psGstRate;
                    $gstValue = number_format( $item['costPrice']* $item['quantity']*(1 + $psGstRate ) , 2);
                    $listTotalGst += $gstValue;
                    $item['lineAmount'] =  $gstValue;
                } else {
                    $item['gstPrice'] = number_format( $item['costPrice'],2);
                    $item['lineAmount'] =  $item['gstPrice']* $item['quantity'];
                }
               
                $totalAmountGst += $item['lineAmount'];

            }

            $totalAmount = $totalAmountGst - $totalGst;
            $totalAmountGst = number_format ($totalAmountGst, 2);
            $totalGst = number_format ($totalGst, 2);
            $totalAmount = number_format ($totalAmount, 2);         

            $template = 'AdminBundle:pdf:pharmacy-daily-po.html.twig';
            $pdfData = array(
                'info' => $dailyItems,
                'lists' => $rxItems,
                'totalAmount' => $totalAmount,
                'totalGst' => $totalGst,
                'totalAmountGst' => $totalAmountGst,
                'pharmacy' => $pharmacyInfo,
                'logoUrl' => 'web/bundles/admin/assets/pages/img/logo.png'
            );
            
            $html = $this->container->get('templating')->render($template, $pdfData);

            //clone a html
            $pathHtml = $path.'/html';
            $fileNameHtml = str_replace('.pdf', '.html', $filename);
            $contentHtml = str_replace("web/bundles", "bundles", $html);
            if (!$fs->exists($pathHtml)) {
                $fs->mkdir($pathHtml);
            }
            file_put_contents($pathHtml . "/{$fileNameHtml}", $contentHtml);

            $options = new Options();
            $options->set('isRemoteEnabled', TRUE);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $canvas = $dompdf->getCanvas();
            $canvas->page_script('
                $font = $fontMetrics->getFont("helvetica");
                if($PAGE_NUM == 1) {
                    $pdf->text(535, 140, "$PAGE_NUM of $PAGE_COUNT", $font, 9, array(0,0,0));
                }
                $pdf->page_text(20, 5, "Daily PO: '.$dailyItems["poNumber"].'", $font, 9, array(0,0,0));
                $pdf->page_text(540, 5, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 9, array(0,0,0));
            ');
            $output =   $dompdf->output();
            file_put_contents($pdfFileDir, $output );

            //send mail
            if(file_exists($pdfFileDir)) {
                
                //update $filename
                $dailyObj->setFilename($filename);
                $dailyObj->setUpdatedOn(new \DateTime());
                $this->em->persist($dailyObj);
                $this->em->flush();

                $response['files'] = $filename;
                $emailTo = $pharmacyObj->getEmailAddress();
                $pharmacyName = $pharmacyInfo['name'];
                $mailTemplate = 'AdminBundle:emails:pharmacy-po-daily.html.twig';
                $mailParams = array(
                    'logoUrl' => $this->baseUrl.'/bundles/admin/assets/pages/img/logo.png',
                    'date' => $start->format("d M Y"),
                    'name' => $pharmacyName,
                    'baseUrl' => $this->baseUrl
                );
                $dataSendMail = array(
                    'title'  => "Pharmacy PO Daily",
                    'body'   => $this->container->get('templating')->render($mailTemplate, $mailParams),
                    'from'   => $this->container->getParameter('primary_email'),
                    'to'     => array($emailTo, 'accounts@gmedes.com'),
                    'attach' => $pdfFileDir
                );
                $this->container->get('microservices.sendgrid.email')->sendEmail($dataSendMail);
            }
        } else {
            $response['message'] = "No data created.";
        }
        return $response;
    }

    /**
     * pharmacy PO weekly
     * @author vinh.nguyen
     */
    public function pharmacyPoWeekly($week = null, $targetDate = null)
    {
        $response = array();
        if(empty($week)) {
            $wPoDate = new \DateTime();

            if(!empty($targetDate)) {
                $weeklyPoDate = clone $targetDate;
                $lastWeek = clone $weeklyPoDate;
                $lastWeek->modify("last week");
                $lastWeek->modify("last week");
            } else {
                $weeklyPoDate = clone $wPoDate;
                $lastWeek = clone $weeklyPoDate;
                $lastWeek->modify("last week");
            }
            $week = $lastWeek->format("Y-m-d");
        } else {
            $wPoDate = new \DateTime($week);
            $psObj = $this->em->getRepository('UtilBundle:PlatformSettings')->getPaymentSchedule();
            $pharmacyDay = (int)$psObj['pharmacyWeeklyPoDay'];
            $settingDay = Constant::$dayOfWeek[$pharmacyDay];
            $weeklyPoDate = $wPoDate->modify($settingDay);
            $lastWeek = clone $weeklyPoDate;
            $lastWeek->modify("last week");
            $week = $lastWeek->format("Y-m-d");
        }
        
        $dates = Common::getWeek($week);

        //get daily items
        $dailyItems = $this->em->getRepository('UtilBundle:PharmacyPoDaily')->getPODailyBy($dates);

        if(!empty($dailyItems)) {
            $response['dates'] = $dates;
            $amount = 0;
            foreach ($dailyItems as $item) {
                $amount += $item['includeGstAmount'];
            }
            
            $pharmacyObj = $this->em->getRepository('UtilBundle:Pharmacy')->getUsedPharmacy();
            
            $poNumber = Common::generatePONumber(array(
                'type' => 'pharmacy',
                'poDate' => $dates['end'],
                'courierRateCode' => '',
                'prefix' => 'W',
                'pharmacyCode' => $this->pharmacyCode
            ));

            $projectedPaymentDate = $this->em->getRepository('UtilBundle:DoctorMonthlyStatement')->calculateProjectedPaymentDate(3, $weeklyPoDate->format("Y-m-d"));
            $weeklyItems = array(
                'cycle'         => $dates['cycle'],
                'poNumber'      => $poNumber,
                'cycleFromDate' => $dates['start'],
                'cycleToDate'   => $dates['end'],
                'weeklyPoDate'  => $weeklyPoDate,
                'amount'        => $amount,
                'projectedPaymentDate' => $projectedPaymentDate,
                'customerReference' => str_replace('/', '', $poNumber)
            );

            //weekly storage
            $weeklyObj = $this->em->getRepository('UtilBundle:PharmacyPoWeekly')->create($weeklyItems);

            if($weeklyItems) {
                $response['total_weekly'] = 1;
            }

            //daily update
            $pharmacyPoDailyResult = array();
            foreach ($dailyItems as $item) {
                $poDaily = array(
                    'id' => $item['id'],
                    'poWeekly' => $weeklyObj
                );
                $pharmacyPoDailyResult[] = $this->em->getRepository('UtilBundle:PharmacyPoDaily')->update($poDaily);
            }
            $response['total_daily'] = count($pharmacyPoDailyResult);

            //create PDF file
            $pharmacyInfo = $this->em->getRepository('UtilBundle:Pharmacy')->getPharmacy($pharmacyObj->getId());
            $filename = str_replace("/","",$poNumber).".pdf";
            $fs = new Filesystem();
            $parammeters = $this->container->getParameter('media');
            $path = $this->container->get('kernel')->getRootDir()."/../".$parammeters['po_path'];
            if (!$fs->exists($path)) {
                $fs->mkdir($path);
            }
            $pdfFileDir = $path."/".$filename;

            $template = 'AdminBundle:pdf:pharmacy-weekly-po.html.twig';
            $pdfData = array(
                'info' => $weeklyItems,
                'lists' => $dailyItems,
                'pharmacy' => $pharmacyInfo,
                'logoUrl' => 'web/bundles/admin/assets/pages/img/logo.png'
            );
            $html = $this->container->get('templating')->render($template, $pdfData);

            //clone a html
            $pathHtml = $path.'/html';
            $fileNameHtml = str_replace('.pdf', '.html', $filename);
            $contentHtml = str_replace("web/bundles", "bundles", $html);
            if (!$fs->exists($pathHtml)) {
                $fs->mkdir($pathHtml);
            }
            file_put_contents($pathHtml . "/{$fileNameHtml}", $contentHtml);

            $options = new Options();
            $options->set('isRemoteEnabled', TRUE);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $canvas = $dompdf->getCanvas();
            $canvas->page_script('
                $font = $fontMetrics->getFont("helvetica");
                if($PAGE_NUM == 1) {
                    $pdf->text(527, 131, "$PAGE_NUM of $PAGE_COUNT", $font, 9, array(0,0,0));
                }
                $pdf->page_text(20, 18, "Weekly PO: '.$weeklyItems["poNumber"].'", $font, 9, array(0,0,0));
                $pdf->page_text(530, 18, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 9, array(0,0,0));
            ');
            $output =   $dompdf->output();
            file_put_contents($pdfFileDir, $output );

            //send mail
            if(file_exists($pdfFileDir)) {

                //update $filename
                $weeklyObj->setFilename($filename);
                $weeklyObj->setUpdatedOn(new \DateTime());
                $this->em->persist($weeklyObj);
                $this->em->flush();

                $response['files'] = $filename;
                $emailTo = $pharmacyObj->getEmailAddress();
                $pharmacyName = $pharmacyInfo['name'];
                $mailTemplate = 'AdminBundle:emails:pharmacy-po-weekly.html.twig';
                $mailParams = array(
                    'logoUrl' => $this->baseUrl.'/bundles/admin/assets/pages/img/logo.png',
                    'startDate' => $dates['start']->format("d M Y"),
                    'endDate' => $dates['end']->format("d M Y"),
                    'name' => $pharmacyName,
                    'baseUrl' => $this->baseUrl
                );
                $dataSendMail = array(
                    'title'  => "Pharmacy PO Weekly (".$mailParams['startDate']." - ".$mailParams['endDate'].")",
                    'body'   => $this->container->get('templating')->render($mailTemplate, $mailParams),
                    'from'   => $this->container->getParameter('primary_email'),
                    'to'     => $emailTo,
                    'attach' => $pdfFileDir
                );
                $this->container->get('microservices.sendgrid.email')->sendEmail($dataSendMail);
            }
        }

        return $response;
    }

    /**
     * Send statement to doctors
     * @author vinh.nguyen
     */
    public function sendStatementToDoctor($day = null)
    {
        $response = array();
        $restrictListEmail = array();

        // STRIKE 1199
        if (empty($day)) {
            $preMonth = new \DateTime('-1 month');
        } else {
            $preMonth = new \DateTime($day);
        }
        $params = array(
            'month' => $preMonth->format('m'),
            'year' => $preMonth->format('Y')
        );

        $doctors = $this->em->getRepository('UtilBundle:Doctor')->getDoctorForStatement($restrictListEmail, $params);

        $siteUrl = $this->container->getParameter('sites');
        $baseUrl = "";

        foreach($doctors as $doctor) {            
            //for response
            $response[] = $doctor['name'];

            if('parkway' == strtolower($doctor['siteName'])){ 
                $baseUrl = $siteUrl['parkway'];
            } else {
                $baseUrl = $siteUrl['non_parkway'];
            }

            $emailTo = $doctor['emailAddress'];
            $mailTemplate = 'AdminBundle:emails:statement-to-doctor.html.twig';
            $mailParams = array(
                'logoUrl' => $this->baseUrl . '/bundles/admin/assets/pages/img/logo.png',
                'date' => $preMonth,
                'name' => $doctor['name'],
                'baseUrl' => $baseUrl
            );
            $dataSendMail = array(
                'title' => "Statement to Doctor",
                'body' => $this->container->get('templating')->render($mailTemplate, $mailParams),
                'from'   => $this->container->getParameter('primary_email'),
                'to' => $emailTo
            );
            $this->container->get('microservices.sendgrid.email')->sendEmail($dataSendMail);
        }

        return $response;
    }

    /**
     * Send statement to agent
     * @author vinh.nguyen
     */
    public function sendStatementToAgent($day = null)
    {
        $response = array();
        $restrictListEmail = array();
        $preMonth = empty($day)? new \DateTime('-1 month'): new \DateTime($day);
        $params = array(
            'month' => $preMonth->format('m'),
            'year'  => $preMonth->format('Y')
        );
        $agents = $this->em->getRepository('UtilBundle:Agent')->getAgentForStatement($restrictListEmail, $params);
        $siteUrl = $this->container->getParameter('sites');
        $baseUrl = "";

        if(!empty($agents)) {
            foreach($agents as $agent) {

                //for response
                $response[] = $agent['name'];

                $emailTo = $agent['emailAddress'];

                if('parkway' == strtolower($agent['siteName'])){ 
                    $baseUrl = $siteUrl['parkway'];
                } else {
                    $baseUrl = $siteUrl['non_parkway'];
                }

                $mailTemplate = 'AdminBundle:emails:statement-to-agent.html.twig';
                $mailParams = array(
                    'logoUrl' => $this->baseUrl . '/bundles/admin/assets/pages/img/logo.png',
                    'date' => $preMonth,
                    'name' => $agent['name'],
                    'baseUrl' => $baseUrl
                );
                $dataSendMail = array(
                    'title' => "Statement to Agent",
                    'body' => $this->container->get('templating')->render($mailTemplate, $mailParams),
                    'from'   => $this->container->getParameter('primary_email'),
                    'to' => $emailTo
                );
                $this->container->get('microservices.sendgrid.email')->sendEmail($dataSendMail);
            }
        }

        return $response;
    }
}
