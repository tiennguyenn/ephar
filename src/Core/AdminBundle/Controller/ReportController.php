<?php

namespace AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;
use UtilBundle\Utility\Common;
use UtilBundle\Utility\Constant;
use Symfony\Component\HttpFoundation\JsonResponse;
use Dompdf\Dompdf;
use UtilBundle\Utility\MonthlyPdfHelper;
use UtilBundle\Entity\Rx;

class ReportController extends Controller
{
    /**
     * @Route("/report/transaction-history", name="admin_report_transaction_history")
     */
    public function transactionHistoryAction(Request $request)
    {
        $em      = $this->getDoctrine()->getManager();
        $results = $em->getRepository('UtilBundle:Country')->getList();

        return $this->render('AdminBundle:rx\reports:transaction_history.html.twig');
    }

    /**
     * Get list orders by ajax
     * @Route("/report/transaction-history-ajax", name="admin_report_transaction_history_ajax")
     */
    public function ajaxTransactionListingAction(Request $request)
    {

        $params = array(
            'page'         => $request->get('page', Constant::PAGE_DEFAULT),
            'perPage'      => $request->get('perPage', Constant::PER_PAGE_DEFAULT),
            'doctorId'     => $this->getUser()->getId(),
            'fromDate'     => $request->get('fromDate', ''),
            'toDate'       => $request->get('toDate', ''),
            'patientType'  => $request->get('patientType', 'all'), // 1 is local, 0 is overseas,
            'orderValueGte'=> $request->get('orderValueGte', ''),
            'orderValueLte'=> $request->get('orderValueLte', ''),
            'sortInfo'     => $request->get('sortInfo', array()),
            'patientTerm'  => $request->get('patientTerm', ''),
            'doctorTerm'   => $request->get('doctorTerm', ''),
			'orderTerm'    => $request->get('orderTerm', ''),
            'month'        => $request->get('month', ''),
            'ispdf'        => $request->get('ispdf', null),
            'countryCode'  => $request->get('countryCode', ''),
            'transReport'  => true
        );

        $em      = $this->getDoctrine()->getManager();
        $results = $em->getRepository('UtilBundle:Rx')->getTransactionListingReport($params);

        //process data
        $data = $results['data'];

        $template = 'AdminBundle:rx\reports:ajax_transaction_history.html.twig';

        //paging
        $totalPages = isset($results['totalPages']) ? $results['totalPages'] : Constant::TOTAL_PAGE_DEFAULT;

        //get current link for paging
        $pageUrl = $this->generateUrl('admin_report_transaction_history_ajax', $params);

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
            'paginationHTML' => $paginationHTML,
            'currentPage'    => $params['page'],
            'perPage'        => $params['perPage'],
            'totalResult'    => $results['totalResult'],
            'sortInfo'       => isset($params['sortInfo']) ? $params['sortInfo'] : array()
        ));
    }


    /**
     * @Route("/report/transaction-history/csv", name="admin_report_transaction_history_csv")
     */
    public function ajaxDownloadSalesReport(Request $request){
        $params = array(
            'doctorId'          => $this->getUser()->getId(),
            'fromDate'          => $request->get('fromDate', ''),
            'toDate'            => $request->get('toDate', ''),
            'patientType'       => $request->get('patientType', 'all'), // 1 is local, 0 is overseas,
            'doctorFeeGte'      => $request->get('doctorFeeGte', ''),
            'doctorFeeLte'      => $request->get('doctorFeeLte', ''),
            'sortInfo'          => $request->get('sortInfo', array()),
            'term'              => $request->get('term', array()),
            'month'             => $request->get('month', ''),
            'isCsv'             => true,
            'ispdf'             => $request->get('ispdf', null)
        );

        $em      = $this->getDoctrine()->getManager();
        // $results = $em->getRepository('UtilBundle:Rx')->getAdminRxTransactionHistory($params);
        $results = $em->getRepository('UtilBundle:Rx')->getTransactionListingReport($params);
        
        
        $data = [];
        $data[] = implode(',', [
                'Order Id',
                'Confirmed Order Date',
                'Doctor Code',
                'Doctor Name',
                'Patient Code',
                'Patient Name',
                'Order Value'
            ]);

        if (isset($results['data'])) {
            foreach ($results['data'] as $item) {
                $data[] = implode(',', [
                    $item['orderNumber'],
                    $item['paidOn']->format('d M y'),
                    $item['doctorCode'],
                    $item['doctorName'],
                    $item['patientName'],
                    $item['patientCode'],
                    $item['orderValue']
                ]);
            }
        }

        return $this->get('util.common')->downloadCSV(implode("\n", $data), 'sale_reports_doctor');
    }

    /**
     * @Route("/order/{rxId}", name="admin_rx_transaction_history_detail")
     */
    public function ajaxGetOrderDetailAction(Request $request, $rxId) {
        $em         = $this->getDoctrine()->getManager();

        $rxRepo = $em->getRepository('UtilBundle:Rx');
        $rxDetail    = $rxRepo->getAdminRxDetail($rxId);

        $template = 'AdminBundle:rx\reports:_rx_detail.html.twig';


        return $this->render($template, array(
            'data'   => $rxDetail,
            'refund' => $request->get('refund')
        ));

        return new JsonResponse($rxLines);
    }

    /**
     * for suggestion
     * @param Request $request
     * @Route("/report/autoSuggest", name="admin_rx_report_auto_suggest_ajax")
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
     * @Route("/report/doctor", name="admin_report_doctor")
     */
    public function doctorReportAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('AdminBundle:doctor:report.html.twig');
    }

    /**
     * Get doctor report by ajax
     * @Route("/report/doctor-ajax", name="admin_report_doctor_ajax")
     */
    public function ajaxDoctorReportAction(Request $request)
    {

        $params = array(
            'page'         => $request->get('page', Constant::PAGE_DEFAULT),
            'perPage'      => $request->get('perPage', Constant::PER_PAGE_DEFAULT),
            'doctorId'     => $this->getUser()->getId(),
            'fromDate'     => $request->get('fromDate', ''),
            'toDate'       => $request->get('toDate', ''),
            'patientType'  => $request->get('patientType', 'all'), // 1 is local, 0 is overseas,
            'orderValueGte'=> $request->get('orderValueGte', ''),
            'orderValueLte'=> $request->get('orderValueLte', ''),
            'sortInfo'     => $request->get('sortInfo', array()),
            'patientTerm'  => $request->get('patientTerm', ''),
            'doctorTerm'   => $request->get('doctorTerm', ''),
			'orderTerm'   => $request->get('orderTerm', ''),
            'month'        => $request->get('month', ''),
        );

        $params['isDoctorReport'] = true;

        $em      = $this->getDoctrine()->getManager();
        $results = $em->getRepository('UtilBundle:Rx')->getTransactionListingReport($params);

        //process data
        $data = $results['data'];

        $template = 'AdminBundle:doctor:ajax_doctor_report.html.twig';

        //paging
        $totalPages = isset($results['totalPages']) ? $results['totalPages'] : Constant::TOTAL_PAGE_DEFAULT;

        //get current link for paging
        $pageUrl = $this->generateUrl('admin_report_doctor_ajax', $params);

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
            'paginationHTML' => $paginationHTML,
            'currentPage'    => $params['page'],
            'perPage'        => $params['perPage'],
            'totalResult'    => $results['totalResult'],
            'sortInfo'       => isset($params['sortInfo']) ? $params['sortInfo'] : array()
        ));
    }

    /**
     * @Route("/report/doctor/csv", name="admin_report_doctor_csv")
     */
    public function ajaxDownloadDoctorReport(Request $request){
        $params = array(
            'doctorId'          => $this->getUser()->getId(),
            'fromDate'          => $request->get('fromDate', ''),
            'toDate'            => $request->get('toDate', ''),
            'patientType'       => $request->get('patientType', 'all'), // 1 is local, 0 is overseas,
            'doctorFeeGte'      => $request->get('doctorFeeGte', ''),
            'doctorFeeLte'      => $request->get('doctorFeeLte', ''),
            'sortInfo'          => $request->get('sortInfo', array()),
            'term'              => $request->get('term', array()),
            'month'             => $request->get('month', ''),
            'isCsv'             => true,
            'ispdf'             => $request->get('ispdf', null)
        );

        $params['isDoctorReport'] = true;

        $em      = $this->getDoctrine()->getManager();
        $results = $em->getRepository('UtilBundle:Rx')->getTransactionListingReport($params);

        $data = [];
        $data[] = implode(',', [
                'Order Id',
                'Confirmed Order Date',
                'Doctor Code',
                'Doctor Name',
                'Patient Code',
                'Patient Name',
                'Total Fee',
                "Doctor's Fee"
            ]);

        if (isset($results['data'])) {
            foreach ($results['data'] as $item) {
                $rx = isset($item[0]) ? $item[0] : array();
                $data[] = implode(',', [
                    $item['orderNumber'],
                    $item['paidOn']->format('d M y'),
                    $item['doctorCode'],
                    $item['doctorName'],
                    $item['patientName'],
                    $item['patientCode'],
                    $item['orderValue'],
                    $rx['doctorMedicineFee'] + $rx['doctorServiceFee']
                ]);
            }
        }

        return $this->get('util.common')->downloadCSV(implode("\n", $data), 'sale_reports_doctor');
    }

    /**
     * @Route("/report/agent", name="admin_report_agent")
     */
    public function agentReportAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('AdminBundle:agents:report.html.twig');
    }

    /**
     * Get agent report by ajax
     * @Route("/report/agent-ajax", name="admin_report_agent_ajax")
     */
    public function ajaxAgentReportAction(Request $request)
    {

        $params = array(
            'page'         => $request->get('page', Constant::PAGE_DEFAULT),
            'perPage'      => $request->get('perPage', Constant::PER_PAGE_DEFAULT),
            'doctorId'     => 22,
            'fromDate'     => $request->get('fromDate', ''),
            'toDate'       => $request->get('toDate', ''),
            'patientType'  => $request->get('patientType', 'all'), // 1 is local, 0 is overseas,
            'orderValueGte'=> $request->get('orderValueGte', ''),
            'orderValueLte'=> $request->get('orderValueLte', ''),
            'sortInfo'     => $request->get('sortInfo', array()),
            'patientTerm'  => $request->get('patientTerm', ''),
            'doctorTerm'   => $request->get('doctorTerm', ''),
			'orderTerm'    => $request->get('orderTerm', ''),
            'month'        => $request->get('month', ''),
        );

        $params['isAgentReport'] = true;

        $em      = $this->getDoctrine()->getManager();
        $results = $em->getRepository('UtilBundle:Rx')->getTransactionListingReport($params);


        //process data
        $data = $results['data'];

        $template = 'AdminBundle:agents:ajax_agent_report.html.twig';

        //paging
        $totalPages = isset($results['totalPages']) ? $results['totalPages'] : Constant::TOTAL_PAGE_DEFAULT;

        //get current link for paging
        $pageUrl = $this->generateUrl('admin_report_agent_ajax', $params);

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
            'paginationHTML' => $paginationHTML,
            'currentPage'    => $params['page'],
            'perPage'        => $params['perPage'],
            'totalResult'    => $results['totalResult'],
            'sortInfo'       => isset($params['sortInfo']) ? $params['sortInfo'] : array()
        ));
    }

    /**
     * Get agent breakdown by ajax
     * @Route("/report/agent-breakdown/{id}", name="admin_report_agent_breakdown_ajax")
     */
    public function ajaxBreakdownAction(Request $request, Rx $rx)
    {
        $params = array(
            'invoiceNo' => $rx->getOrderNumber(),
            'data' => $rx->getRxLines()
        );

        return $this->render('AdminBundle:agents:breakdown_modal.html.twig', $params);
    }

    /**
     * @Route("/report/agent/csv", name="admin_report_agent_csv")
     */
    public function ajaxDownloadAgentReport(Request $request){
        $params = array(
            'doctorId'          => $this->getUser()->getId(),
            'fromDate'          => $request->get('fromDate', ''),
            'toDate'            => $request->get('toDate', ''),
            'patientType'       => $request->get('patientType', 'all'), // 1 is local, 0 is overseas,
            'doctorFeeGte'      => $request->get('doctorFeeGte', ''),
            'doctorFeeLte'      => $request->get('doctorFeeLte', ''),
            'sortInfo'          => $request->get('sortInfo', array()),
            'term'              => $request->get('term', array()),
            'month'             => $request->get('month', ''),
            'isCsv'             => true,
            'ispdf'             => $request->get('ispdf', null)
        );

        $params['isAgentReport'] = true;

        $em      = $this->getDoctrine()->getManager();
        $results = $em->getRepository('UtilBundle:Rx')->getTransactionListingReport($params);

        $data = [];
        $data[] = implode(',', [
                'Order Id',
                'Confirmed Order Date',
                'Doctor Code',
                'Doctor Name',
                'Patient Code',
                'Patient Name',
                'Total Fee',
                "Agent's Fee"
            ]);

        if (isset($results['data'])) {
            foreach ($results['data'] as $item) {
                $rx = isset($item[0]) ? $item[0] : array();
                $data[] = implode(',', [
                    $item['orderNumber'],
                    $item['paidOn']->format('d M y'),
                    $item['doctorCode'],
                    $item['doctorName'],
                    $item['patientName'],
                    $item['patientCode'],
                    $item['orderValue'],
                    $rx['agentMedicineFee'] + $rx['agentServiceFee']
                ]);
            }
        }

        return $this->get('util.common')->downloadCSV(implode("\n", $data), 'sale_reports_doctor');
    }

    /**
     * @Route("/report/rx-refunds", name="admin_report_rx_refunds")
     */
    public function rxRefundsAction(Request $request)
    {
        $em      = $this->getDoctrine()->getManager();
        $results = $em->getRepository('UtilBundle:Country')->getList();

        return $this->render('AdminBundle:rx\reports:rx_refunds.html.twig');
    }

    /**
     * Get list orders by ajax
     * @Route("/report/rx-refunds-ajax", name="admin_report_rx_refunds_ajax")
     */
    public function ajaxRxRefundsAction(Request $request)
    {

        $params = array(
            'page'         => $request->get('page', Constant::PAGE_DEFAULT),
            'perPage'      => $request->get('perPage', Constant::PER_PAGE_DEFAULT),
            'fromDate'     => $request->get('fromDate', ''),
            'toDate'       => $request->get('toDate', ''),
            'patientType'  => $request->get('patientType', 'all'), // 1 is local, 0 is overseas,
            'orderValueGte'=> $request->get('orderValueGte', ''),
            'orderValueLte'=> $request->get('orderValueLte', ''),
            'sortInfo'     => $request->get('sortInfo', array()),
            'patientTerm'  => $request->get('patientTerm', ''),
            'doctorTerm'   => $request->get('doctorTerm', ''),
            'orderTerm'    => $request->get('orderTerm', ''),
            'countryCode'  => $request->get('countryCode', '')
        );

        $em      = $this->getDoctrine()->getManager();
        $results = $em->getRepository('UtilBundle:RxPaymentLog')->getRxRefunds($params);

        //process data
        $data = $results['data'];

        $template = 'AdminBundle:rx\reports:ajax_rx_refunds.html.twig';

        //paging
        $totalPages = isset($results['totalPages']) ? $results['totalPages'] : Constant::TOTAL_PAGE_DEFAULT;

        //get current link for paging
        $pageUrl = $this->generateUrl('admin_report_rx_refunds_ajax', $params);

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
            'paginationHTML' => $paginationHTML,
            'currentPage'    => $params['page'],
            'perPage'        => $params['perPage'],
            'totalResult'    => $results['totalResult'],
            'sortInfo'       => isset($params['sortInfo']) ? $params['sortInfo'] : array()
        ));
    }


    /**
     * @Route("/report/rx-refunds/csv", name="admin_report_rx_refunds_csv")
     */
    public function ajaxDownloadRxRefundsReport(Request $request){
        $params = array(
            'fromDate'          => $request->get('fromDate', ''),
            'toDate'            => $request->get('toDate', ''),
            'patientType'       => $request->get('patientType', 'all'), // 1 is local, 0 is overseas,
            'doctorFeeGte'      => $request->get('doctorFeeGte', ''),
            'doctorFeeLte'      => $request->get('doctorFeeLte', ''),
            'sortInfo'          => $request->get('sortInfo', array()),
            'term'              => $request->get('term', array()),
            'isCsv'             => true,
        );

        $em      = $this->getDoctrine()->getManager();
        $results = $em->getRepository('UtilBundle:RxPaymentLog')->getRxRefunds($params);

        $data = [];
        $data[] = implode(',', [
                'Order Id',
                'Confirmed Order Date',
                'Doctor Code',
                'Doctor Name',
                'Patient Code',
                'Patient Name',
                'Order Value'
            ]);

        if (isset($results['data'])) {
            foreach ($results['data'] as $item) {
                $data[] = implode(',', [
                    $item['orderNumber'],
                    $item['paidOn']->format('d M y'),
                    $item['doctorCode'],
                    $item['doctorName'],
                    $item['patientName'],
                    $item['patientCode'],
                    $item['orderValue']
                ]);
            }
        }

        return $this->get('util.common')->downloadCSV(implode("\n", $data), 'rx_refunds');
    }


    /**
     * @Route(
     *     "/media/pdf/{any}",
     *     requirements={
     *         "any": ".+",
     *     },
     *     name="get_pdf_file"
     * )
     */
    public function getFileAction(Request $request)
    {
        $loggedInUser = $this->getUser();
        if (!$loggedInUser) {
            $deniedTemplate = $this->renderView('AdminBundle:error:403.html.twig',[ ]);
            Common::restrictFileAccess($this->container, $deniedTemplate);
        }

        $kernelDir = $this->container->get('kernel')->getRootDir();
        $fileName = rtrim(trim($request->getRequestUri()), '.pdf'). '.pdf';
        $fileTarget = $kernelDir ."/../web" . $fileName;
        if (!file_exists($fileTarget)) {
            return $this->redirectToRoute('not_found');
        }

        $pdfAuth = $this->getParameter('core_media_pdf');
        $userPdf = $pdfAuth[0];
        $arrContextOptions = array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
            'http' => array(
                'method' => "GET",
                'header' => "Authorization: Basic " . base64_encode($userPdf['username'] . ":" . $userPdf['password'])
            )
        );
        stream_context_get_default($arrContextOptions);

        $content = file_get_contents($fileTarget);
        $response = new Response($content);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Cache-Control', 'private, max-age=0, must-revalidate');
        $response->headers->set('Content-Length', strlen($content));
        $response->headers->set('Content-Disposition', 'inline; filename="'.$fileName.'"');

        return $response;
    }
}
