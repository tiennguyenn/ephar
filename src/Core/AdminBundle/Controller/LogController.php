<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AdminBundle\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AdminBundle\Controller\BaseController;
use UtilBundle\Utility\Common;
use UtilBundle\Utility\Constant;
use UtilBundle\Utility\Utils;

class LogController extends BaseController
{

    /**
     * @Route("/log/view", name="admin_view_logs")
     */
    public function ajaxViewLogs(Request $request) {
        $params['module']   = $request->request->get('module');
        $params['entityId'] = $request->request->get('entityId', null);
        $viewTemplate       = $request->request->get('viewLogTemplate');

        // build url for printLog
        $printTemplate      = $request->request->get('printLogTemplate');
        $fileName           = $request->request->get('fileName');
        $breadcrumbTitle    = $request->request->get('breadcrumbTitle');
        $breadcrumbUrl      = $request->request->get('breadcrumbUrl');

        $printUrl = $this->get('router')->generate('admin_print_logs', array('template' => $printTemplate, 'fileName' => $fileName));
        $printUrl =  $printUrl . "&module=" . $params['module'];
        if (isset($params['entityId'])) {
            $printUrl =  $printUrl . "&entityId=" . Common::encodeHex($params['entityId']);
        }
        if($request->request->get('module') == Constant::MODULE_CC){
            $logs = $this->getDoctrine()->getManager()->getRepository('UtilBundle:Log')->getLogsCustomerCare($params);
        } else if($request->request->get('module') == Constant::MODULE_RO){
            $logs = $this->getDoctrine()->getManager()->getRepository('UtilBundle:Log')->getLogsRedispenseOrder($params);
        }
        else if($request->request->get('module') == Constant::AGENT_FEE){
            $logs = $this->getDoctrine()->getManager()->getRepository('UtilBundle:Log')->geLogsAgentFeeMedicine($params);
        }
        else {
            $logs = $this->getDoctrine()->getManager()->getRepository('UtilBundle:Log')->getLogs($params);
        }

        $logs = Utils::decodeLog($logs);
        $logs = Utils::cleanLogs($logs);

        return $this->render($viewTemplate, array("logs" => $logs, 'printUrl' => $printUrl, 'breadcrumbTitle' => $breadcrumbTitle, 'entityId' => $params['entityId'], 'breadcrumbUrl' => $breadcrumbUrl));
    }


    /**
     * @Route("admin/log/print", name="admin_print_logs")
     */
    public function printLogsAction(Request $request) {
        $params['module']   = $request->query->get('module');
        $title   = $request->query->get('title', '');
        $params['entityId'] = $request->query->get('entityId', null);
        $template           = $request->query->get('template');
        $fileName           = $request->query->get('fileName');
        if (isset($params['entityId'])) {
            $fileName = $fileName . '_id_' . $params['entityId'];
        }
        if($request->query->get('module') == Constant::MODULE_CC){
            $logs = $this->getDoctrine()->getManager()->getRepository('UtilBundle:Log')->getLogsCustomerCare($params);
        }  else if($request->query->get('module') == Constant::MODULE_RO){
            $logs = $this->getDoctrine()->getManager()->getRepository('UtilBundle:Log')->getLogsRedispenseOrder($params);
        } else if($params['module'] == Constant::AGENT_FEE){
            $logs = $this->getDoctrine()->getManager()->getRepository('UtilBundle:Log')->geLogsAgentFeeMedicine($params);
        }  else {
            $logs = $this->getDoctrine()->getManager()->getRepository('UtilBundle:Log')->getLogs($params);
        }

        // Decode json
        for ($i = 0; $i < count($logs); $i++) {
			if (is_string($logs[$i]['oldValue'])) {
				$oldValue = json_decode($logs[$i]['oldValue'], true);
				if (is_array($oldValue)) {
					$logs[$i]['oldValue'] = $oldValue;
				}
			}

			if (is_string($logs[$i]['newValue'])) {
				$newValue = json_decode($logs[$i]['newValue'], true);
				if (is_array($newValue)) {
					if (isset($newValue['excludePaymentNote']) && !isset($newValue['note'])) {
						$newValue['note'] = $newValue['excludePaymentNote'];
					}
					$logs[$i]['newValue'] = $newValue;
				}
			}
        }
        $logs = Utils::cleanLogs($logs);


        $html = $this->renderView($template, array(
            "logs" => $logs,
            "title" => strtoupper($title),
            "module" => $params['module'],
            "entityId" => $params['entityId']
        ));

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $response = new Response();
        $response->setContent($dompdf->output());
        $response->setStatusCode(200);
        $response->headers->set('Content-Disposition', 'attachment');
        $response->headers->set('Content-Disposition', 'attachment; filename='. $fileName . '.pdf');
        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }


    /**
     * @Route("admin/log/write", name="admin_write_logs")
     */
    public function writeLog(Request $request ) {
        $em = $this->getDoctrine()->getManager();

        $params['module']    = $request->request->get('module');
        $params['entityId']  = $request->request->get('entityId', '');
        $params['newValue']  = $request->request->get('newValue', '');
        $params['createdBy'] = $this->getUser()->getDisplayName();
        $params['action']    = $request->request->get('action', '');

        try {
            $em->getRepository('UtilBundle:Log')->insert($params);

            return new JsonResponse(array('success' => 'inserted log successfully'));
        } catch (Exception $e) {
            return new JsonResponse(array('error' => $e->getMessage()));
        }
    }

}
