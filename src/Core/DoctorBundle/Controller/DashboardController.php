<?php

namespace DoctorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use UtilBundle\Utility\Constant;
use UtilBundle\Utility\Common;
use UtilBundle\Utility\MsgUtils;
use Symfony\Component\HttpFoundation\JsonResponse;

class DashboardController extends Controller
{
    /**
     * @Route("/notification/close", name="doctor_dashboard_close_notification")
     */
    public function closeNotification(Request $request) {
        // refill reminder id
        $rrId  = $request->query->get('rrId');
        $msgId = $request->query->get('msgId');
        $data = array('status' => true, 'msg' => '');

        try {
            $em = $this->getDoctrine()->getEntityManager();

            if ($rrId) {
                $refillReminder = $this->getDoctrine()->getRepository('UtilBundle:RxRefillReminder')->find($rrId);
                $refillReminder->setDeletedOn(new \DateTime("now"));
                $em->persist($refillReminder);
            }

            if ($msgId) {
                $message = $this->getDoctrine()->getRepository('UtilBundle:Message')->find($msgId);
                $message->setReadDate(new \DateTime("now"));
                $em->persist($message);
            }

            $em->flush();
            $this->get('session')->getFlashBag()->add('success', MsgUtils::generate('msgUpdateSuccessShort'));
        } catch (Exception $e) {
            $data['status'] = false;
            $data['msg'] = $e->getMessage();
            $this->get('session')->getFlashBag()->add('danger', MsgUtils::generate('msgCannotEditedShort'));
        }

        return new JsonResponse($data);
    }
    /**
     * @Route("/", name="doctor_dashboard")
     */
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getEntityManager();    
        $params = $this->getFilter($request);
        $gmedUser = $this->getUser();
        $params['id'] = $gmedUser->getId();
        $userId = $gmedUser->getLoggedUser()->getId();
        $doctor = $em->getRepository('UtilBundle:Doctor')->find($params['id']);
        if (in_array(Constant::TYPE_MPA, $gmedUser->getRoles())) {
            if (!empty($doctor) && !empty($doctor->getUser())) {
                $userId = $doctor->getUser()->getId();
            }
        } else {
            if (empty($doctor->getIsCustomizeMedicineEnabled())) {
                $permissions = $gmedUser->getPermissions();
                foreach ($permissions as $key => $value) {
                    if ('doctor_custom_selling_prices' == $value) {
                        unset($permissions[$key]);
                    }
                }
                $gmedUser->setPermissions($permissions);
            }
        }
        $params['userId'] = $userId;
        $currMonth = new \DateTime("now");

        $totalFee = $em->getRepository('UtilBundle:Rx')->getTotalFeeRx(['doctorId' => $params['id']]);
        $currMonthFee = $em->getRepository('UtilBundle:Rx')->getTotalFeeRx(['doctorId' => $params['id'], 'currMonth' => $currMonth->format('m')]);
        $totalRx = $em->getRepository('UtilBundle:Rx')->getTotalComfirmedRx(['doctorId' => $params['id']]);
        $totalPatient = $em->getRepository('UtilBundle:Patient')->getListPatient(['doctorId' => $params['id']]);
        $rxs = $this->getDoctrine()->getRepository('UtilBundle:Rx')->getRxForDashboardDoctor($params);

        $orderId = $request->get('order-id', '');
        $rx = null;
        if ($orderId != '') {
            $rx = $em->getRepository('UtilBundle:Rx')->findOneBy(array('orderNumber' => $orderId));
            $isOrderScheduled = $rx->getIsScheduledRx();
            $scheduledOrderSendDate = $rx->getScheduledSendDate();
            if ($scheduledOrderSendDate) {
                $scheduledOrderSendDate = $scheduledOrderSendDate->format('d F Y');
            }
        }

        $fullname =  $gmedUser->getDisplayName();
        return $this->render('DoctorBundle:dashboard:index.html.twig', [
            'totalPatient' => $totalPatient['totalResult'],
            'totalConfirmed' => $totalRx['totalConfirmed'],
            'totalFailed' => $totalRx['totalFailed'],
            'totalDraft' => $totalRx['totalDraft'],
            'totalPending' => $totalRx['totalPending'],
            'totalFee'  => $totalFee['totalDoctorFee'],
            'currMonthFee'  => $currMonthFee['totalDoctorFee'],
            'totalResult'    => $rxs['totalResult'],
            'orderId' => $rx ? $rx->getOrderNumber() : '',
            'isOrderScheduled' => isset($isOrderScheduled) ? $isOrderScheduled : false,
            'scheduledOrderSendDate' => isset($scheduledOrderSendDate) ? $scheduledOrderSendDate : null,
            'fullName'  => $fullname,
            ]);
    }

    /**
     * get data for chart
     * @Route("/get-chart", name="ajax_get_chart_data_doctor")
     * @author toan.le
     */
    public function ajaxDataChart(){
        $gmedUser = $this->getUser();
        $dataChart = $this->getDoctrine()->getRepository('UtilBundle:Rx')->getDataChart(['doctorId' => $gmedUser->getId(), 'feeType' => Constant::FEE_DOCTOR ]);
        return new JsonResponse($dataChart);
    }

    /**
     * Get list rx by ajax
     * @Route("/ajax-list-rx", name="ajax_list_rx")
     * @author toan.le
     */
    public function ajaxListRxAction(Request $request)
    {
        $params = $this->getFilter($request);
        $gmedUser = $this->getUser();
        $params['id'] = $gmedUser->getId();
        $userId = $gmedUser->getLoggedUser()->getId();
        if (in_array(Constant::TYPE_MPA, $gmedUser->getRoles())) {
            $doctor = $this->getDoctrine()->getRepository('UtilBundle:Doctor')->find($params['id']);
            if (!empty($doctor) && !empty($doctor->getUser())) {
                $userId = $doctor->getUser()->getId();
            }
        }
        $params['userId'] = $userId;
        $doctors = $this->getDoctrine()->getRepository('UtilBundle:Rx')->getRxForDashboardDoctor($params);

        //process data
        $data = $doctors['data'];

        $template = 'DoctorBundle:dashboard:ajax_list_rx.html.twig';

        //paging
        $totalPages = isset($doctors['totalPages']) ? $doctors['totalPages'] : Constant::TOTAL_PAGE_DEFAULT;
        //get current link for paging
        $pageUrl = $this->generateUrl('ajax_list_rx', $params);

        //build paging
        $paginationHTML = Common::buildPagination(
            $this->container,
            $request,
            $totalPages,
            $params['page'],
            $params['perPage'],
            array('pageUrl' => $pageUrl)
        );

        if($doctors) {
            return $this->render($template, array(
                'data'           => $data,
                'paginationHTML' => $paginationHTML,
                'currentPage'    => $params['page'],
                'perPage'        => $params['perPage'],
                'totalResult'    => $doctors['totalResult'],
                'sorting'        => $params['sorting'],
            ));
        } else {//API has problem
            return $this->render($template, array(
                'data'    => $data,
            ));
        }
    }

    /**
     * Get closed messages
     * @Route("/ajax-closed-messages", name="ajax_closed_messages")
     */
    public function ajaxClosedMessagesAction(Request $request)
    {
        $params = $this->getFilter($request);
        $gmedUser = $this->getUser();
        $userId = $gmedUser->getLoggedUser()->getId();
        $params['id'] = $gmedUser->getId();
        $params['userId'] = $userId;

        $response = $this->getDoctrine()->getRepository('UtilBundle:Message')->getDoctorClosedMessages($params);

        //process data
        $data = $response['data'];

        $template = 'DoctorBundle:dashboard:closed_messages.html.twig';

        return $this->render($template, array(
            'data'      => $data,
            'sorting'   => $params['sorting']
        ));
    }

    /**
     * get request filter
     * @param $request
     * @return array
     * @author vinh.nguyen
     */
    private function getFilter($request)
    {
        $params = array(
            'page'    => $request->get('page', Constant::PAGE_DEFAULT),
            'perPage' => $request->get('perPage', Constant::PER_PAGE_DEFAULT),
            'status' => $request->get('status', 'all'),
            'sorting' => $request->get('sorting', ''),
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
}
