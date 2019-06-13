<?php

namespace AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use UtilBundle\Entity\DrugAudit;
use UtilBundle\Utility\Constant;
use Dompdf\Dompdf;
use UtilBundle\Utility\Utils;

class DrugController extends BaseController
{
    /**
     * @Route("/admin/pharmacy/{id}/list-products", name="pharmacy_list_products")
     */
    public function listAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $pharmacy = $em->getRepository('UtilBundle:Pharmacy')->find($id);

        $params = array(
            'pharmacy_id' => $id,
            'name' => $request->query->get("name", ""),
            'status' => $request->query->get("status", "all"),
            'sort' => $request->query->get("sort", "name"),
            'dir' => $request->query->get("dir", "desc"),
            'page' => $request->query->get("page", 1),
            'limit' => $request->query->get("limit", Constant::PER_PAGE_DEFAULT),
        );

        $page = $request->query->get("page", 1);
        $limit = $request->query->get("limit", Constant::PER_PAGE_DEFAULT);

        if ($limit != 'all') {
            $params['page'] = $page;
            $params['limit'] = $limit;
        }

        $data = $em->getRepository('UtilBundle:Drug')->listProducts($params);
        $drugAudits = $em->getRepository('UtilBundle:Drug')->listProductWithAudit($id);
        foreach ($data['list'] as &$drug) {
            $drug["audits"] = isset($drugAudits[$drug['id']]) ? $drugAudits[$drug['id']] : array();
        }
        $audits = array();
        $drugAudits = $em->getRepository('UtilBundle:DrugAudit')->findBy(array(
            "pharmacy" => $id,
            "status" => Constant::DRUG_AUDIT_APPROVED
        ));
        if ($drugAudits) {
            foreach ($drugAudits as $drugAudit) {
                $audits[$drugAudit->getPriceType()] = array(
                    "old_value" => $drugAudit->getOldCostPrice(),
                    "new_value" => $drugAudit->getNewCostPrice(),
                    "take_effect_on" => $drugAudit->getTakeEffectOn()
                );
            }
        }

        $pending = $em->getRepository('UtilBundle:Drug')->totalPendingProducts($id);
        $settings = null;
        $list = $em->getRepository('UtilBundle:PlatformSettings')->findAll();
        foreach ($list as $item) {
            $settings = $item;
            break;
        }

        return $this->render('AdminBundle:drug:list.html.twig', array(
            "pharmacy" => $pharmacy,
            "data" => $data,
            "audits" => $audits,
            "pending" => $pending,
            "settings" => $settings,
            "params" => $params
        ));
    }

    /**
     * @Route("/admin/pharmacy/{id}/list-names", name="drug_list_names")
     */
    public function listNamesAction(Request $request, $id)
    {
        $params = array(
            'pharmacy_id' => $id,
            'name' => $request->get("term", ""),
            'group' => $request->get("gid", "")
        );

        $list = $this->getDoctrine()->getRepository('UtilBundle:Drug')->listNames($params);

        return new JsonResponse($list);
    }

    /**
     * @Route("/admin/pharmacy/{id}/update-prices", name="drug_update_prices")
     */
    public function updatePricesAction(Request $request, $id)
    {
        $result   = array("status" => 0);

        $action   = $request->request->get("action", "");

        $em       = $this->getDoctrine()->getManager();
        $pharmacy = $em->getRepository("UtilBundle:Pharmacy")->getUsedPharmacy();

        $gmeds    = $this->getUser();
        $userId   = $gmeds->getLoggedUser()->getId();
        $user     = $em->getRepository("UtilBundle:User")->find($userId);

        if ($action == 'update_price') {
            $drugId = $request->request->get("id", 0);
            $field  = $request->request->get("field", "");
            $value  = $request->request->get("value", 0);
            $date   = $request->request->get("date", "");
            try {
                $date = new \DateTime($date);
            } catch (\Exception $ex) {
                $result['status'] = 402;
                $result['error']  = 'Effect date is invalid.';
            }

            if (!isset($result['error']) || empty($result['error'])) {
                $cdate = new \DateTime();
                if ($date <= $cdate) {
                    $result['status'] = 402;
                    $result['error']  = 'Effect date must be later than current date.';
                } else {
                    $drug = $em->getRepository("UtilBundle:Drug")->find($drugId);
                    if ($drug && $drug->getPharmacy()->getId() == $pharmacy->getId()) {
                        $audits = $em->getRepository("UtilBundle:DrugAudit")->findBy(array(
                            'drug'      => $drug,
                            'priceType' => $field,
                            'status'    => Constant::DRUG_AUDIT_APPROVED
                        ));

                        $em->beginTransaction();
                        try {
                            $drugAudit = new DrugAudit();
                            $drugAudit->setUser($user);
                            $drugAudit->setDrug($drug);
                            $oldValue = 0;
                            if ($field == 'list_price_domestic') {
                                $oldValue = $drug->getListPriceDomestic();
                            } else {
                                $oldValue = $drug->getListPriceInternational();
                            }
                            $drugAudit->setOldCostPrice($oldValue);
                            $drugAudit->setNewCostPrice($value);
                            $drugAudit->setPriceType($field);
                            $drugAudit->setTakeEffectOn($date);
                            $drugAudit->setIsPercent(0);
                            $drugAudit->setStatus(Constant::DRUG_AUDIT_APPROVED);
                            $drugAudit->setCreatedOn(new \DateTime());
                            $em->persist($drugAudit);
                            $em->flush();

                            foreach ($audits as $audit) {
                                $audit->setStatus(Constant::DRUG_AUDIT_OVERWROTE);
                                $em->persist($audit);
                            }
                            $em->flush();
                            $em->commit();
                            $result['status'] = 200;
                            $result['info'] = '<a href="javascript:;" data-toggle="tooltip" title="New value ' . number_format($value, 2, '.', ',') . ' will be effective on ' . $date->format('d M y') . '" class="icon-tooltip"><i class="fa fa-exclamation-circle"></i></a>';
                            $result['message'] = "Updated successfully.";

                            $inputs[] = [
                                'tableName'  => 'drug_audit',
                                'fieldName'  => $field,
                                'entityId'   => $drugId,
                                'oldPrice'   => $oldValue,
                                'newPrice'   => round($value, 2),
                                'createdBy'  => $userId,
                                'em'         => $em,
                                'effectedOn' => $date
                            ];
                            Utils::saveLogPrice($inputs);

                        } catch (\Exception $ex) {
                            $em->rollback();
                            $result['status'] = 500;
                            $result['error'] = $ex->getMessage();
                        }
                    } else {
                        $result['status'] = 404;
                        $result['error'] = 'Product not found.';
                    }
                }
            }

            return new JsonResponse($result);
        } elseif ($action == 'update_prices') {
            //Deactivate

            $field = $request->request->get("field", "");
            $value = $request->request->get("value", 0);
            $date = $request->request->get("date", "");
            try {
                $date = new \DateTime($date);
            } catch (\Exception $ex) {
                $result['status'] = 402;
                $result['error'] = 'Effect date is invalid.';
            }

            if (!isset($result['error']) || empty($result['error'])) {
                $cdate = new \DateTime();
                if ($date <= $cdate) {
                    $result['status'] = 402;
                    $result['error'] = 'Effect date must be later than current date.';
                } else {
                    $groupDrug = $em->getRepository('UtilBundle:DrugGroup')->find($id);
                    $settings = null;
                    $list = $this->getDoctrine()->getRepository('UtilBundle:PlatformSettings')->findAll();
                    foreach ($list as $item) {
                        $settings = $item;
                        break;
                    }
                    if ($pharmacy) {
                        $audits = $em->getRepository("UtilBundle:DrugAudit")->findBy(array(
                            'pharmacy' => $pharmacy,
                            'priceType' => $field,
                            'status' => Constant::DRUG_AUDIT_APPROVED
                        ));

                        $em->beginTransaction();
                        try {
                            $drugAudit = new DrugAudit();
                            $drugAudit->setUser($user);
                            $drugAudit->setPharmacy($pharmacy);
                            $drugAudit->setDrugGroup($groupDrug);

                            $oldValue = 0;
                            if ($field == 'list_price_domestic') {
                                $oldValue = $groupDrug->getLocalPricePercentage();
                            } else {
                                $oldValue = $groupDrug->getOverseasPricePercentage();
                            }
                            $drugAudit->setOldCostPrice($oldValue);
                            $drugAudit->setNewCostPrice($value);
                            $drugAudit->setPriceType($field);
                            $drugAudit->setTakeEffectOn($date);
                            $drugAudit->setIsPercent(1);
                            $drugAudit->setStatus(Constant::DRUG_AUDIT_APPROVED);
                            $drugAudit->setCreatedOn(new \DateTime());
                            $em->persist($drugAudit);
                            $em->flush();

                            foreach ($audits as $audit) {
                                $audit->setStatus(Constant::DRUG_AUDIT_OVERWROTE);
                                $em->persist($audit);
                            }
                            $em->flush();
                            $em->commit();
                            $result['status'] = 200;
                            $result['info'] = '<a href="javascript:;" data-toggle="tooltip" title="New value ' . $value . '% will be effective on ' . $date->format('d M y') . '" class="icon-tooltip"><i class="fa fa-exclamation-circle"></i></a>';
                            $result['message'] = "Updated successfully.";

                        } catch (\Exception $ex) {
                            $em->rollback();
                            $result['status'] = 500;
                            $result['error'] = $ex->getMessage();
                        }
                    } else {
                        $result['status'] = 404;
                        $result['error'] = 'Pharmacy not found.';
                    }
                }
            }

            return new JsonResponse($result);
        } elseif ($action == 'approve_price') {
            $drugId = $request->request->get("id", 0);
            $date = $request->request->get("date", "");
            try {
                $date = new \DateTime($date);
            } catch (\Exception $ex) {
                $result['status'] = 402;
                $result['error'] = 'Effect date is invalid.';
            }

            if (!isset($result['error']) || empty($result['error'])) {
                $cdate = new \DateTime();
                if ($date <= $cdate) {
                    $result['status'] = 402;
                    $result['error'] = 'Effect date must be later than current date.';
                } else {
                    $drug = $em->getRepository("UtilBundle:Drug")->find($drugId);
                    if ($drug && $drug->getPharmacy()->getId() == $pharmacy->getId()) {
                        $drugAudit = $em->getRepository("UtilBundle:DrugAudit")->findOneBy(array(
                            'drug' => $drug,
                            'priceType' => 'cost_price',
                            'status' => Constant::DRUG_AUDIT_PENDING
                        ));
                        if ($drugAudit) {
                            try {
                                $drugAudit->setUser($user);
                                $drugAudit->setStatus(Constant::DRUG_AUDIT_APPROVED);
                                $drugAudit->setTakeEffectOn($date);
                                $drugAudit->setUpdatedOn(new \DateTime());
                                $em->persist($drugAudit);
                                $em->flush();
                                $result['status'] = 200;
                                $result['info'] = '<a href="javascript:;" data-toggle="tooltip" title="New value ' . number_format($drugAudit->getNewCostPrice(), 2, '.', ',') . ' will be effective on ' . $date->format('d M y') . '" class="icon-tooltip"><i class="fa fa-exclamation-circle"></i></a>';
                                $result['data'] = $this->getListDrug($pharmacy->getId(), 'pending');
                            } catch(\Exception $ex) {
                                $result['status'] = 500;
                                $result['error'] = $ex->getMessage();
                            }
                        } else {
                            $result['status'] = 404;
                            $result['error'] = 'Product not found.';
                        }
                    } else {
                        $result['status'] = 404;
                        $result['error'] = 'Product not found.';
                    }
                }
            }

            return new JsonResponse($result);
        } elseif ($action == 'reject_price') {
            $drugId = $request->request->get("id", 0);
            $drug = $em->getRepository("UtilBundle:Drug")->find($drugId);
            if ($drug && $drug->getPharmacy()->getId() == $pharmacy->getId()) {
                $drugAudit = $em->getRepository("UtilBundle:DrugAudit")->findOneBy(array(
                    'drug' => $drug,
                    'priceType' => 'cost_price',
                    'status' => Constant::DRUG_AUDIT_PENDING
                ));
                if ($drugAudit) {
                    try {
                        $drugAudit->setUser($user);
                        $drugAudit->setStatus(Constant::DRUG_AUDIT_REJECTED);
                        $drugAudit->setUpdatedOn(new \DateTime());
                        $em->persist($drugAudit);
                        $em->flush();
                        $result['status'] = 200;
                        $result['data'] = $this->getListDrug($pharmacy->getId(), 'pending');
                    } catch(\Exception $ex) {
                        $result['status'] = 500;
                        $result['error'] = $ex->getMessage();
                    }
                } else {
                    $result['status'] = 404;
                    $result['error'] = 'Product not found.';
                }
            } else {
                $result['status'] = 404;
                $result['error'] = 'Product not found.';
            }

            return new JsonResponse($result);
        }
    }

    /**
     * @Route("/admin/pharmacy/{id}/list-logs", name="drug_list_logs")
     */
    public function listLogsAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $logs = $em->getRepository('UtilBundle:DrugAudit')->listLogs(array("pharmacy_id" => $id));

        return $this->render('AdminBundle:drug:logs.html.twig', array("logs" => $logs));
    }

    /**
     * @Route("/admin/pharmacy/{id}/logs", name="drug_print_logs")
     */
    public function printLogsAction(Request $request, $id)
    {
        $pharmacy = $this->getDoctrine()->getRepository('UtilBundle:Pharmacy')->find($id);
        if (!$pharmacy) {
            throw new \Exception("Pharmacy not found.", 404);
        }

        $template = 'AdminBundle:drug:print.html.twig';
        $logs = $this->getDoctrine()->getRepository('UtilBundle:DrugAudit')->listLogs(array("pharmacy_id" => $id));
        $html = $this->renderView($template, array(
            "pharmacy" => $pharmacy,
            "logs" => $logs
        ));

        $dompdf = new Dompdf();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $response = new Response();
        $response->setContent($dompdf->output());
        $response->setStatusCode(200);
        $response->headers->set('Content-Disposition', 'attachment');
        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }

    private function getListDrug($id, $status)
    {
        $em = $this->getDoctrine()->getManager();

        $params = array(
            'pharmacy_id' => $id,
            'name' => "",
            'status' => $status,
            'sort' => "name",
            'dir' => "desc",
            'page' => 1,
            'limit' => Constant::PER_PAGE_DEFAULT,
        );

        $pharmacy = $em->getRepository('UtilBundle:Drug')->find($id);
        $data = $em->getRepository('UtilBundle:Drug')->listProducts($params);
        $drugAudits = $em->getRepository('UtilBundle:Drug')->listProductWithAudit($id);
        foreach ($data['list'] as &$drug) {
            $drug["audits"] = isset($drugAudits[$drug['id']]) ? $drugAudits[$drug['id']] : array();
        }
        $audits = array();
        $drugAudits = $em->getRepository('UtilBundle:DrugAudit')->findBy(array(
            "pharmacy" => $id,
            "status" => Constant::DRUG_AUDIT_APPROVED
        ));
        if ($drugAudits) {
            foreach ($drugAudits as $drugAudit) {
                $audits[$drugAudit->getPriceType()] = array(
                    "old_value" => $drugAudit->getOldCostPrice(),
                    "new_value" => $drugAudit->getNewCostPrice(),
                    "take_effect_on" => $drugAudit->getTakeEffectOn()
                );
            }
        }
        $pending = $em->getRepository('UtilBundle:Drug')->totalPendingProducts($id);
        $settings = null;
        $list = $this->getDoctrine()->getRepository('UtilBundle:PlatformSettings')->findAll();
        foreach ($list as $item) {
            $settings = $item;
            break;
        }

        return $this->renderView('AdminBundle:drug:list.html.twig', array(
            "pharmacy" => $pharmacy,
            "data" => $data,
            "audits" => $audits,
            "pending" => $pending,
            "settings" => $settings,
            "params" => $params
        ));
    }
}
