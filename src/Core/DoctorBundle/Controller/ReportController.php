<?php

namespace DoctorBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use UtilBundle\Utility\Common;
use UtilBundle\Utility\Constant;
use Symfony\Component\HttpFoundation\JsonResponse;
use Dompdf\Dompdf;
use UtilBundle\Utility\MonthlyPdfHelper;

class ReportController extends Controller
{
    /**
     * @Route("/report/transaction-history", name="doctor_report_transaction_history")
     */
    public function transactionHistoryAction(Request $request)
    {
        $em      = $this->getDoctrine()->getManager();
        $doctor = $em->getRepository('UtilBundle:Doctor')->find($this->getUser()->getId());
        
        return $this->render('DoctorBundle:rx\reports:transaction_history.html.twig', array('doctor' => $doctor));
    }

    /**
     * Get list orders by ajax
     * @Route("/report/transaction-history-ajax", name="doctor_report_transaction_history_ajax")
     */
    public function ajaxTransactionHistoryAction(Request $request)
    {
        $params = array(
            'page'              => $request->get('page', Constant::PAGE_DEFAULT),
            'perPage'           => $request->get('perPage', Constant::PER_PAGE_DEFAULT),
            'doctorId'          => $request->get('doctorId') ? $request->get('doctorId','') : $this->getUser()->getId(),
            'fromDate'          => $request->get('fromDate', ''),
            'toDate'            => $request->get('toDate', ''),
            'patientType'       => $request->get('patientType', 'all'), // 1 is local, 0 is overseas,
            'doctorFeeGte'      => $request->get('doctorFeeGte', ''),
            'doctorFeeLte'      => $request->get('doctorFeeLte', ''),
            'sortInfo'          => $request->get('sortInfo', array()),
            'term'              => $request->get('term', array()),
			'orderTerm'         => $request->get('orderTerm', array()),
            'month'             => $request->get('month', ''),
            'ispdf'             => $request->get('ispdf', null)
        );
        $em      = $this->getDoctrine()->getManager();
        $results = $em->getRepository('UtilBundle:Rx')->getTransactionHistoryReport($params);

        $pgfRepo = $em->getRepository('UtilBundle:PaymentGatewayFee');
        $visaMasterFee = $pgfRepo->getFeeSettingBy(Constant::PAY_METHOD_VISA_MASTER);
        $revpayFpxFee = $pgfRepo->getFeeSettingBy(Constant::PAY_METHOD_REVPAY_FPX);

        //process data
        $data = $results['data'];

        $template = 'DoctorBundle:rx\reports:ajax_transaction_history.html.twig';

        //paging
        $totalPages = isset($results['totalPages']) ? $results['totalPages'] : Constant::TOTAL_PAGE_DEFAULT;

        //get current link for paging
        $pageUrl = $this->generateUrl('doctor_report_transaction_history_ajax', $params);

        //build paging
        $paginationHTML = Common::buildPagination(
            $this->container,
            $request,
            $totalPages,
            $params['page'],
            $params['perPage'],
            array('pageUrl' => $pageUrl)
        );

        return $this->render($template, array(
            'data'           => $data,
            'ccFee'          => $visaMasterFee[Constant::GATEWAY_CODE_GST] / 100,
            'fpxFee'         => $revpayFpxFee[Constant::GATEWAY_CODE_FIX_GST] / 100,
            'paginationHTML' => $paginationHTML,
            'currentPage'    => $params['page'],
            'perPage'        => $params['perPage'],
            'totalResult'    => $results['totalResult'],
            'sortInfo'       => isset($params['sortInfo']) ? $params['sortInfo'] : array(),
            'sgCountry' => Constant::ID_SINGAPORE
        ));
    }

    /**
     * @Route("/order/{rxId}", name="doctor_rx_transaction_history_detail")
     */
    public function ajaxGetOrderDetailAction($rxId) {
        $em         = $this->getDoctrine()->getManager();
        
        $rxRepo = $em->getRepository('UtilBundle:Rx');
        $rxLines    = $rxRepo->getRxDetails($rxId);
        foreach ($rxLines as &$value) {
            $value['totalFee'] = number_format($value['totalFee'], 2);
            $value['shareFee'] = $this->container->getParameter('platform_share_fee');
            if ($value['shareFee']) {
                $value['gmedesFixed'] = number_format($value['platformServiceFee'] + $value['agentServiceFee'], 2);
            }
        }

        return new JsonResponse($rxLines);
    }

    /**
     * for suggestion
     * @param Request $request
     * @Route("/report/autoSuggest", name="doctor_rx_report_auto_suggest_ajax")
     * @author thu.tranq
     */
    public function suggestionSearchAction(Request $request)
    {
		$personType = $request->get('personType', 'patient');
		
		if ($personType == 'patient' || $personType == 'doctor') {
			$params = array(
				'term' => $request->get('term', ''),
				'personType' => $personType
			);
			$em = $this->getDoctrine()->getManager();
			$results = $em->getRepository('UtilBundle:PersonalInformation')->getPersonName($params);
		} elseif ($personType == 'order') {
			$em = $this->getDoctrine()->getManager();
			$term = $request->get('term', '');
			$results = array(
				'data' => $em->getRepository('UtilBundle:Rx')->suggestionOrderNumber($term)
			);
		}

        return new JsonResponse($results['data']);
    }

    /**
     * @Route("/report/monthly-statement", name="doctor_report_monthly_statement")
     */
    public function monthlyStatementAction(Request $request)
    {
        return $this->render('DoctorBundle:rx\reports:monthly_statement.html.twig');
    }

    /**
     * Get list monthly orders
     * @Route("/report/monthly-statement-ajax", name="doctor_report_monthly_statement_ajax")
     */
    public function ajaxMonthlyStatementAction(Request $request)
    {
        $params = array(
            'page'       => $request->get('page', Constant::PAGE_DEFAULT),
            'perPage'    => $request->get('perPage', Constant::PER_PAGE_DEFAULT),
            'doctorId'   => $this->getUser()->getId(),
            'fromDate'   => $request->get('fromDate', ''),
            'toDate'     => $request->get('toDate', ''),
            'sortInfo'   => $request->get('sortInfo', array()),
            'doctor'     => $this->getUser()->getId()
        );

        //dump($params); die;
        if(!empty($params['sortInfo']['direction'])){                         

            $params['sorting'] = "";            
            if($params['sortInfo']['column'] == 'monthly'){
                $params['sorting'] .= "ms.year";
            }
            if($params['sortInfo']['column'] == 'totalFee'){
                $params['sorting'] .= "msl.orderValue";
            }
            if($params['sortInfo']['column'] == 'doctorFee'){
                $params['sorting'] .= "msl.doctorMonthlyFee";
            }

            $params['sorting'] .= "-".$params['sortInfo']['direction'];
        }

        $em      = $this->getDoctrine()->getManager();        
        $results = $em->getRepository('UtilBundle:DoctorMonthlyStatementLine')->listStatementForDoctor($params);
        //dump($params);die;

        //process data
        $data = $results['data'];

        $template = 'DoctorBundle:rx\reports:ajax_monthly_statement.html.twig';

        $totalResult = count($results['data']);
        $totalPages  = $results['totalResult'] / $params['perPage'];

        //paging
        $totalPages = !empty($totalPages) ? $totalPages : Constant::TOTAL_PAGE_DEFAULT;

        //get current link for paging
        $pageUrl = $this->generateUrl('doctor_report_monthly_statement_ajax', $params);

        //build paging
        $paginationHTML = Common::buildPagination(
            $this->container,
            $request,
            $totalPages,
            $params['page'],
            $params['perPage'],
            array('pageUrl' => $pageUrl)
        );

        $media = $this->container->getParameter('media');
        $poFilePath = str_replace('web/', '/', $media['doctor_report_path']);

        return $this->render($template, array(
            'data'           => $data,
            'paginationHTML' => $paginationHTML,
            'currentPage'    => $params['page'],
            'perPage'        => $params['perPage'],
            'totalResult'    => $totalResult,
            'sortInfo'       => isset($params['sortInfo']) ? $params['sortInfo'] : array(),
            'doctorId'       => $params['doctorId'],
            'poFilePath'     => $poFilePath
        ));
    }



    /**
     * @Route("/report/monthly-statement/pdf", name="doctor_report_monthly_statement_pdf")
     */
    public function downloadPdfByMonth(Request $request) {
        $params          = $request->query->all();
        $date            = new \DateTime($params['datetime']);
        $params['month'] = (int)$date->format('m');
        $params['year']  = (int)$date->format('Y');

        // STRIKE-964
        $user = $this->getUser();
        if ($user && isset($params['doctorId'])) {
            if ($user->getId() != $params['doctorId']) {
                throw $this->createAccessDeniedException();
            }
        }
        // End

        $breadcrumb = "";
        if(isset($params['mId']) && !empty($params['mId'])) {
            $breadcrumb = $this->generateUrl('finance_doctor_detail_monthly', array('id' => $params['mId']), true);
        }

        $em      = $this->getDoctrine()->getManager();

        $results = $em->getRepository('UtilBundle:Rx')->getRxsByMonth($params);
        $template   = 'DoctorBundle:rx\reports\pdf:monthly_statement.html.twig';
        // add check platform is_gst
        $checkBankGst = !$em->getRepository('UtilBundle:PlatformSettings')->hasGST();
        
     
        
        $doctorInfo = $em->getRepository('UtilBundle:Doctor')->getDoctorInfoForMontlyStatementPdf($params);

        $doctorInfo['doctorCountry'] = $em->getRepository('UtilBundle:Country')->getPlatformSettingCountryName();

        
        $paymentInfo                   = MonthlyPdfHelper::getPaymentInfo($em, $params);
        $params['statementDateNumber'] = $paymentInfo['statementDateNumber'];
        $balanceInfo                   = MonthlyPdfHelper::getBalanceInfo($em, $params);
        $params['currencyCode'] = isset($results['currencyCode']) ? $results['currencyCode'] : null;

        // STRIKE-912
        $monthYear = $date->format('my');
        $serialNumber = $doctorInfo['serialNumber'] + 1;
        $serialNumberFormat = sprintf("%'.04d", $serialNumber);
        $pieces = array($doctorInfo['doctorCode'], $monthYear, $serialNumberFormat);
        $statementNo = implode('-', $pieces);
        $em->getRepository('UtilBundle:Doctor')
            ->updateSerialNumber($doctorInfo['snId'], $serialNumber);

        $html = $this->container->get('templating')->render($template, 
            array(
                'data' => $results, 
                'doctor' => $doctorInfo, 
                'paymentInfo' => $paymentInfo,
                'balanceInfo' => $balanceInfo,
                'isGst' => $checkBankGst,
                'breadcrumb' => $breadcrumb,
                'params'     => $params,
                'statementNo' => $statementNo
        ));


        $fileName = $doctorInfo['doctorCode'] . '_doctor_statements_'. $date->format('mY') . '.pdf';


        $dompdf = new Dompdf(array('isRemoteEnabled'=> true, 'isPhpEnabled' => true));
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $canvas = $dompdf->get_canvas();
        $canvas->page_script('
            $font = $fontMetrics->getFont("sans-serif");
            if ($PAGE_NUM == 1) {
                $pdf->text(515, 230, "Page {$PAGE_NUM} of {$PAGE_COUNT}", $font, 8);
            } else {
                $pdf->text(535, 800, "Page {$PAGE_NUM} of {$PAGE_COUNT}", $font, 8);
            }
        ');

        $response = new Response();
        $response->setContent($dompdf->output());

        $response->setStatusCode(200);
        $response->headers->set('Content-Disposition', 'attachment; filename='. $fileName);
        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }

    /**
     * @Route("/report/transaction-history/csv", name="doctor_report_transaction_history_csv")
     */
    public function ajaxDownloadSalesReport(Request $request){
        $params = array(
            'page'          => $request->get('page', Constant::PAGE_DEFAULT),
            'perPage'       => $request->get('perPage', Constant::PER_PAGE_DEFAULT),
            'doctorId'      => $this->getUser()->getId(),
            'fromDate'      => $request->get('fromDate', ''),
            'toDate'        => $request->get('toDate', ''),
            'patientType'   => $request->get('patientType', 'all'), // 1 is local, 0 is overseas,
            'doctorFeeGte'  => $request->get('doctorFeeGte', ''),
            'doctorFeeLte'  => $request->get('doctorFeeLte', ''),
            'term'          => $request->get('term', array()),
            'isCsv'         => true
        );

        $em      = $this->getDoctrine()->getManager();
        $results = $em->getRepository('UtilBundle:Rx')->getTransactionHistoryReport($params);
        
        $data = [];

        //set headers
        $data[] = implode(',', [
            '# Order ID',
            'Confirmed Order Date',
            'Patient Name & Code',
            'Total Fee (SGD)',
            'Doctor\'s Fee (SGD)',
        ]);
        foreach ($results['data'] as $item) {
            $data[] = implode(',', [
                $item['orderNumber'],
                $item['paidOn']->format('d M y'),
                $item['fullName'] . '(' . $item['patientCode'] . ')',
                '"SGD ' . number_format($item['totalFee'], 2,'.',',') .'"',
                '"SGD ' . number_format($item['doctorFee'], 2,'.',',').'"'
            ]);
        }

        return $this->get('util.common')->downloadCSV(implode("\n", $data), 'sale_reports_admin');
    }

    /**
     * @Route("/report/download-invoice", name="doctor_report_download_invoice")
     */
    public function downloadInvoiceAction(Request $request)
    {
        set_time_limit(0);

        $kernel = $this->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $doctorId = $request->get('doctorId', 0);
        $month    = $request->get('month', 0);
        $year     = $request->get('year', 0);

        $doctor = $this->getDoctrine()->getRepository('UtilBundle:Doctor')->find($doctorId);

        $hasGST = $this->getDoctrine()->getRepository('UtilBundle:PlatformSettings')->hasGST();
        $baseName = '_invoice_';
        if ($hasGST) {
            $baseName = '_tax_invoice_';
        }
        $fileName = $doctor->getDoctorCode() . $baseName . sprintf("%'.02d", $month) . $year . '.pdf';

        $rootDir = $this->container->get('kernel')->getRootDir();
        $rootDir = str_replace("app", "", $rootDir);

        $parammeters = $this->container->getParameter('media');
        $path = $rootDir . $parammeters['doctor_report_path'] . '/' . $fileName;

        $fs = new Filesystem();
        $fs->remove(array($path));

        $input = new ArrayInput(array(
           'command'   => 'doctor:montly-tax-invoice:export-pdf',
           'day'       => implode('-', array($year, $month, 1)),
           'doctor_id' => $doctorId,
           'is_tax_invoice' => $hasGST
        ));

        $output = new NullOutput();
        $application->run($input, $output);

        while(!$fs->exists($path)) {
            sleep(1);
        }

        $response = new BinaryFileResponse($path);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename='. $fileName);

        return $response;
    }
}
