<?php

namespace AdminBundle\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;
use UtilBundle\Utility\Common;
use UtilBundle\Utility\Constant;
use UtilBundle\Utility\MsgUtils;

class PaymentController extends Controller
{
    /**
     * get payment status
     * @Route("/admin/payment-status/{userType}", name="payment_status")
     * @author vinh.nguyen
     */
    public function paymentStatusAction($userType = Constant::USER_TYPE_DOCTOR)
    {
        return $this->render('AdminBundle:payment:payment_status.html.twig', array(
            'userType' => $userType
        ));
    }

    /**
     * @param Request $request
     * @Route("/admin/payment-status-detail", name="payment_status_detail")
     * @author vinh.nguyen
     */
    public function paymentStatusDetailAction(Request $request)
    {
        $results = array();
        $userType = $request->get('userType', null);

        switch($userType):
            case Constant::USER_TYPE_DOCTOR:
            case Constant::USER_TYPE_AGENT:
                $em = $this->getDoctrine()->getManager();
                $items = $request->get('items', "");
                $multi = $request->get('multi', 0);
                if($multi == 1 && !empty($items)) {
                    $arrItems = explode(",", $items);
                    foreach($arrItems as $item) {
                        $arrRow = explode("|", $item);
                        $params = array(
                            'userId'   => $arrRow[0],
                            'userType' => $arrRow[1],
                            'datePaid' => $arrRow[2],
                        );
                        $results[] = $em->getRepository('UtilBundle:PaymentStatus')->getPSDetailBy($params);
                    }
                } else {
                    $params = array(
                        'userId' => $request->get('userId', null),
                        'userType' => $userType,
                        'datePaid' => $request->get('datePaid', null),
                    );
                    $results[] = $em->getRepository('UtilBundle:PaymentStatus')->getPSDetailBy($params);
                }
                break;

            case Constant::USER_TYPE_PHARMACY:
                $em = $this->getDoctrine()->getManager();
                $params = array(
                    'id' => $request->get('id', null),
                    'userType' => $userType
                );
                $results[] = $em->getRepository('UtilBundle:PharmacyPoWeekly')->getDetailBy($params);
                break;

            case Constant::USER_TYPE_DELIVERY:
                $em = $this->getDoctrine()->getManager();
                $params = array(
                    'id' => $request->get('id', null),
                    'userType' => $userType
                );
                $results[] = $em->getRepository('UtilBundle:CourierPoWeekly')->getDetailBy($params);
                break;
        endswitch;

        return $this->render('AdminBundle:payment:payment_status_form.html.twig', array(
            'data' => $results
        ));
    }

    /**
     * payment status update
     * @Route("/admin/payment-status-update", name="payment_status_update")
     * @author vinh.nguyen
     */
    public function paymentStatusUpdateAction(Request $request)
    {
        $results = array();
        $userType = $request->get('userType', null);
        switch($userType):
            case Constant::USER_TYPE_DOCTOR:
            case Constant::USER_TYPE_AGENT:
                $params = array(
                    'id'         => $request->get('id', null),
                    'amountPaid' => $request->get('amountPaid', null),
                    'remark'     => $request->get('remark', null),
                    'userType'   => $userType,
                    'userId'     => $request->get('userId', null),
                    'datePaid'   => $request->get('datePaid', null)
                );
                $em = $this->getDoctrine()->getManager();
                $results = $em->getRepository('UtilBundle:PaymentStatus')->update($params);
                break;

            case Constant::USER_TYPE_PHARMACY:
                $params = array(
                    'id'         => $request->get('id', null),
                    'amountPaid' => $request->get('amountPaid', null),
                    'remark'     => $request->get('remark', null),
                    'userType'   => $userType
                );
                $em = $this->getDoctrine()->getManager();
                $results = $em->getRepository('UtilBundle:PharmacyPoWeekly')->update($params);
                break;

            case Constant::USER_TYPE_DELIVERY:
                $params = array(
                    'id'         => $request->get('id', null),
                    'amountPaid' => $request->get('amountPaid', null),
                    'remark'     => $request->get('remark', null),
                    'userType'   => $userType
                );
                $em = $this->getDoctrine()->getManager();
                $results = $em->getRepository('UtilBundle:CourierPoWeekly')->update($params);
                break;
        endswitch;

        return new JsonResponse($results);
    }

    /**
     * for suggestion
     * @param Request $request
     * @Route("/admin/payment-status-suggestion", name="payment_status_suggestion")
     * @author vinh.nguyen
     */
    public function paymentStatusSuggestionAction(Request $request)
    {
        $userType = $request->get('userType', Constant::USER_TYPE_DOCTOR);
        $term = $request->get('term', '');

        $results = array();
        $em = $this->getDoctrine()->getManager();
        switch($userType):
            case Constant::USER_TYPE_DOCTOR;
                $results = $em->getRepository('UtilBundle:Doctor')->suggestionSearch($term);
                break;
            case Constant::USER_TYPE_AGENT;
                $results = $em->getRepository('UtilBundle:Agent')->suggestionSearch($term);
                break;
        endswitch;

        return new JsonResponse($results);
    }

    /**
     * Get list payment status by ajax
     * @Route("/admin/ajax-payment-status", name="ajax_payment_status")
     * @author vinh.nguyen
     */
    public function ajaxPaymentStatusAction()
    {
        $request = $this->get('request');
        $params = $this->getPaymentStatusFilter($request);

        //process data
        $results = $this->getPSResults($params);
        $data = $results['data'];
        $template = 'AdminBundle:payment:ajax_payment_status.html.twig';

        //paging
        $totalPages = isset($results['totalPages']) ? $results['totalPages'] : Constant::TOTAL_PAGE_DEFAULT;

        //get current link for paging
        $pageUrl = $this->generateUrl('ajax_payment_status', $params);

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
            'sorting'        => $params['sorting'],
            'userType'       => $params['userType']
        ));
    }

    /**
     * get payment status data
     * @author vinh.nguyen
     */
    protected function getPSResults($params)
    {
        switch($params['userType']):
            case Constant::USER_TYPE_DOCTOR:
            case Constant::USER_TYPE_AGENT:
                $em = $this->getDoctrine()->getManager();
                $results = $em->getRepository('UtilBundle:PaymentStatus')->getPSListBy($params);
                break;

            case Constant::USER_TYPE_PHARMACY:
                $em = $this->getDoctrine()->getManager();
                $results = $em->getRepository('UtilBundle:PharmacyPoWeekly')->getListBy($params);
                break;

            case Constant::USER_TYPE_DELIVERY:
                $em = $this->getDoctrine()->getManager();
                $results = $em->getRepository('UtilBundle:CourierPoWeekly')->getListBy($params);
                break;

            default:
                $results = array(
                    'data' => array(),
                    'totalPages' => 0,
                    'totalResult' => 0
                );
                break;
        endswitch;

        return $results;
    }

    /**
     * get request filter of payment status
     * @param $request
     * @return array
     * @author vinh.nguyen
     */
    private function getPaymentStatusFilter($request)
    {
        $dateTime = new \DateTime('now');
        $cycle = clone $dateTime;
        $params = array(
            'page'     => $request->get('page', Constant::PAGE_DEFAULT),
            'perPage'  => $request->get('perPage', Constant::PER_PAGE_DEFAULT),
            'userType' => $request->get('userType', Constant::USER_TYPE_DOCTOR),
            'term'     => $request->get('term', ''),
            'date'     => $request->get('date', ''),
            'cycle'    => $request->get('cycle', $cycle->format("Y").".".$cycle->format("W")),
            'status'   => $request->get('status', 'all'),
            'sorting'  => $request->get('sorting', '')
        );
        //additional filters
        $filters = $request->get('ps_filter', array());
        if(!empty($filters)){
            foreach($filters as $k=>$v){
                $params[$k] = $v;
            }
        }
        return $params;
    }    

    /**
     * get request filter of pg settlement
     * @param $request
     * @return array
     * @author vinh.nguyen
     */
    private function getPGSettlementFilter($request)
    {
        $params = array(
            'page'    => $request->get('page', Constant::PAGE_DEFAULT),
            'perPage' => $request->get('perPage', Constant::PER_PAGE_DEFAULT),
            'start_date' => $request->get('start_date', ''),
            'end_date' => $request->get('end_date', ''),
            'sorting' => $request->get('sorting', '')
        );
        //additional filters
        $filters = $request->get('pg_settlement_filter', array());
        if(!empty($filters)){
            foreach($filters as $k=>$v){
                $params[$k] = $v;
            }
        }
        return $params;
    }
}
