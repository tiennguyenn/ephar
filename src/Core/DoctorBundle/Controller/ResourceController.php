<?php
/**
 * Created by PhpStorm.
 * User: nanang
 * Date: 15/11/18
 * Time: 9:20
 */

namespace DoctorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use UtilBundle\Utility\Constant;
use UtilBundle\Utility\Common;
use UtilBundle\Entity\DoctorDrug;
use UtilBundle\Utility\Utils;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ResourceController extends Controller
{
    /**
     * @Route("/medicine-list", name="doctor_medicine_list")
     */
    public function medicineListAction(Request $request)
    {
        $doctorId = $this->getDoctorId();
        $drugs = $this->getDoctrine()->getRepository('UtilBundle:Drug')->getDrugAZList($doctorId);

        $params = array(
            'drugs' => $drugs
        );

        return $this->render('DoctorBundle:resources:medicine-list.html.twig', $params);
    }

    /**
     * @Route("/ajax-medicine-list", name="doctor_ajax_medicine_list")
     */
    public function ajaxGetMedicineListAction(Request $request)
    {
        $doctorId = $this->getDoctorId();
        $keyword = $request->query->get('keyword', '');
        $drugs = $this->getDoctrine()->getRepository('UtilBundle:Drug')->getDrugAZList($doctorId, $keyword);

        $params = array(
            'drugs' => $drugs
        );

        return $this->render('DoctorBundle:resources:ajax-medicine-list.html.twig', $params);
    }

    public function getDoctorId()
    {
        $user = $this->getUser();
        if ($user) {
            return $user->getId();
        }

        throw $this->createAccessDeniedException();
    }

    /**
     * @Route("/guide", name="doctor_user_guide")
     */
    public function DoctorUserGuideAction()
    {
        $em = $this->getDoctrine()->getManager();
        $doctorUserGuide = $em->getRepository('UtilBundle:FileDocument')->getContentForClient(Constant::DOCUMENT_NAME_DOCTOR_USER_GUIDE, Common::getCurrentSite($this->container));

        if ($doctorUserGuide) {
            $params = array(
                'title' => Constant::DOCUMENT_NAME_DOCTOR_USER_GUIDE,
                'content' => $doctorUserGuide['contentAfter']
            );
            return $this->render('AdminBundle:document_setting:document_output.html.twig', $params);
        }

        return $this->render('AdminBundle:document_setting:document_404.html.twig',[ 'documentName' => Constant::DOCUMENT_NAME_DOCTOR_USER_GUIDE ]);
    }

    /**
     * @Route("/custom-selling-prices", name="doctor_custom_selling_prices")
     */
    public function customSellingPricesAction(Request $request)
    {
        $gmedUser = $this->getUser();
        if (!$gmedUser->hasPermission('doctor_custom_selling_prices')) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('DoctorBundle:resources:favourite-drugs.html.twig');
    }

    /**
     * @Route("/custom-selling-prices/list", name="doctor_custom_selling_prices_list")
     */
    public function customSellingPricesListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $doctorId = $this->getDoctorId();

        $params = array(
            'name' => $request->query->get("name", ""),
            'sort' => $request->query->get("sort", ""),
            'dir' => $request->query->get("dir", ""),
            'page' => $request->query->get("page", 1),
            'limit' => $request->query->get("limit", Constant::PER_PAGE_DEFAULT)
        );

        $data = $em->getRepository('UtilBundle:Doctor')->getFavoriteDrugs($params, $doctorId);

        $drugAudits = $em->getRepository('UtilBundle:Doctor')->getFavoriteDrugsWithAudit($doctorId);
        foreach ($data['list'] as &$drug) {
            $drug["audits"] = isset($drugAudits[$drug['id']]) ? $drugAudits[$drug['id']] : array();
        }

        //paging
        $totalPages = isset($data['totalPages']) ? $data['totalPages'] : Constant::TOTAL_PAGE_DEFAULT;

        $page = $params['page'];
        $pages = $totalPages;
        $start = $page - 2 < 1 ? 1 : $page - 2;
        $end = $page + 2 > $pages ? $pages : $page + 2;
        if ($end - $start < 4) {
            $end = $start + 4 > $pages ? $pages : $start + 4;
        }
        if ($end - $start < 4) {
            $start = $end - 4 < 1 ? 1 : $end - 4;
        }

        $data['page'] = $page;
        $data['pages'] = $pages;
        $data['limit'] = $params['limit'];
        $data['total'] = $data['totalResult'];
        $data['start'] = $start;
        $data['end'] = $end;

        return $this->render('DoctorBundle:resources:ajax-favourite-drugs.html.twig', array(
            "data" => $data,
            "params" => $params
        ));
    }

    /**
     * @Route("/custom-selling-prices/update-price", name="doctor_custom_selling_prices_update_price")
     */
    public function customSellingPriceUpdatePriceAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $gmeds    = $this->getUser();
        $doctorId = $gmeds->getId();
        $userId   = $gmeds->getLoggedUser()->getId();

        $drugId = $request->request->get("id", 0);
        $field  = $request->request->get("field", "");

        $drug = $em->getRepository('UtilBundle:Drug')->find($drugId);
        $doctor = $em->getRepository('UtilBundle:Doctor')->find($doctorId);

        $list = $em->getRepository('UtilBundle:DoctorDrug')->findBy([
            'doctor' => $doctor,
            'drug' => $drug
        ]);

        $action = $request->request->get("action", "");

        if ($action == 'update_price') {
            $value  = $request->request->get("value", 0);
            $date   = $request->request->get("date", "");
            try {
                $date = new \DateTime($date);
            } catch (\Exception $ex) {
                $result['status'] = 402;
                $result['error']  = 'Effect date is invalid.';
            }

            if (empty($result['error'])) {
                $cdate = new \DateTime();
                $cdate->setTime(0, 0);
                if ($date < $cdate) {
                    $result['status'] = 402;
                    $result['error']  = 'Effect date must be later than current date.';
                } else {
                    $em->beginTransaction();
                    try {
                        foreach ($list as $item) {
                            if ($field == 'list_price_domestic') {
                                if ($item->getListPriceDomesticNew()) {
                                    $doctorDrug = $item;
                                }
                            } else {
                                if ($item->getListPriceInternationalNew()) {
                                    $doctorDrug = $item;
                                }
                            }
                        }

                        if (empty($doctorDrug)) {
                            $doctorDrug = new DoctorDrug();
                            $doctorDrug->setDoctor($doctor);
                            $doctorDrug->setDrug($drug);
                            $doctorDrug->setCreatedOn(new \DateTime());
                        }
                        $doctorDrug->setUpdatedOn(new \DateTime());

                        if ($field == 'list_price_domestic') {
                            $oldValue = $drug->getListPriceDomestic();
							$costPriceToClinic = $drug->getCostPriceToClinic();
                            $price = $doctorDrug->getListPriceDomestic();
                            $doctorDrug->setListPriceDomesticNew($value);
							
                            if ($date == $cdate) {
                                $doctorDrug->setListPriceDomestic($value);
                            }
                        } else {
                            $oldValue = $drug->getListPriceInternational();
							$costPriceToClinic = $drug->getCostPriceToClinicOversea();
                            $price = $doctorDrug->getListPriceInternational();
                            $doctorDrug->setListPriceInternationalNew($value);
                            if ($date == $cdate) {
                                $doctorDrug->setListPriceInternational($value);
                            }
                        }
                        $doctorDrug->setEffectiveDate($date);

                        $em->persist($doctorDrug);
                        $em->flush();
                        $em->commit();

                        $dataValue = $value;
                        if ($date > $cdate) {
                            if ($price) {
                                $dataValue = $price;
                            } else {
                                $dataValue = $oldValue;
                            }
                        }

                        $result['status'] = 200;
                        $info = '<a href="javascript:;" class="editableInline" data-action="update_price" data-id="' . $drugId . '" data-field="' . $field . '" data-name="' . $drug->getName() . '" data-value="'. number_format($dataValue, 2) .'" data-origin="'. $costPriceToClinic .'">' .number_format($dataValue, 2) . '</a>';
                        if ($date > $cdate) {
                            $info .= '<a href="javascript:;" data-toggle="tooltip" title="New value ' . number_format($value, 2, '.', ',') . ' will be effective on ' . $date->format('d M y') . '" class="icon-tooltip"><i class="fa fa-exclamation-circle"></i></a>';
                        }
                        $info .= '<a href="javascript:;" data-toggle="tooltip" title="Reset price to ' . number_format($oldValue, 2, '.', ',') . '" class="reset icon-tooltip" data-id="' . $drugId . '" data-field="' .  $field.'"><i class="fa fa-rotate-left"></i></a>';
                        $result['info'] = $info;
                        $result['message'] = 'Updated successfully.';

                        $inputs[] = [
                            'tableName'  => 'doctor_drug',
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
                }
            }

            return new JsonResponse($result);
        }

        if ($action == 'reset_price') {
            $em->beginTransaction();
            try {
                foreach ($list as $value) {
                    if ($field == 'list_price_domestic') {
                        if ($value->getListPriceDomesticNew()) {
                            $oldValue = $value->getListPriceDomesticNew();							
                            $em->remove($value);
                        }
                    } else {
                        if ($value->getListPriceInternationalNew()) {
                            $oldValue = $value->getListPriceInternationalNew();							
                            $em->remove($value);
                        }
                    }
                }

                $em->flush();
                $em->commit();

                if ($field == 'list_price_domestic') {
                    $value = $drug->getListPriceDomestic();
					$costPriceToClinic = $drug->getCostPriceToClinic();
                } else {
                    $value = $drug->getListPriceInternational();
					$costPriceToClinic = $drug->getCostPriceToClinicOversea();
                }

                $result['status'] = 200;
                $result['info'] = '<a href="javascript:;" class="editableInline" data-action="update_price" data-id="' . $drugId . '" data-field="' . $field . '" data-name="' . $drug->getName() . '" data-value="'. number_format($value, 2) .'"  data-origin="'. $costPriceToClinic .'">' .number_format($value, 2) . '</a>';
                $result['message'] = 'Resetted successfully.';

                $inputs[] = [
                    'tableName'  => 'doctor_drug',
                    'fieldName'  => $field,
                    'entityId'   => $drugId,
                    'oldPrice'   => $oldValue,
                    'newPrice'   => $value,
                    'createdBy'  => $userId,
                    'em'         => $em,
                    'effectedOn' => new \DateTime()
                ];
                Utils::saveLogPrice($inputs);
            } catch(\Exception $ex) {
                $em->rollback();
                $result['status'] = 500;
                $result['error'] = $ex->getMessage();
            }

            return new JsonResponse($result);
        }
    }

    /**
     * @Route("/custom-selling-prices/list-logs", name="doctor_custom_selling_prices_list_logs")
     */
    public function customSellingPricesListLogsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $gmeds = $this->getUser();
        $useId = $gmeds->getLoggedUser()->getId();

        $logs = $em->getRepository('UtilBundle:Doctor')->getCustomSellingPricesLogs($useId);

        return $this->render('AdminBundle:drug:logs.html.twig', array("logs" => $logs));
    }

    /**
     * @Route("/custom-selling-prices/logs", name="doctor_custom_selling_prices_logs")
     */
    public function customsellingPricesPrintLogsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $gmeds = $this->getUser();
        $useId = $gmeds->getLoggedUser()->getId();

        $template = 'AdminBundle:drug:print.html.twig';
        $logs = $this->getDoctrine()->getRepository('UtilBundle:Doctor')->getCustomSellingPricesLogs($useId);
        $html = $this->renderView($template, array(
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

    /**
     * @Route("/custom-selling-prices/download-excel", name="doctor_custom_selling_prices_download_excel")
     */
    public function downloadExcelFavoriteDrugsAction(Request $request)
    {
        $params = array(
            'page' => 1,
            'limit' => 0
        );
        $doctorId = $this->getDoctorId();

        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository('UtilBundle:Doctor')->getFavoriteDrugs($params, $doctorId);
        $drugAudits = $em->getRepository('UtilBundle:Doctor')->getFavoriteDrugsWithAudit($doctorId);
        foreach ($data['list'] as &$drug) {
            $drug["audits"] = isset($drugAudits[$drug['id']]) ? $drugAudits[$drug['id']] : array();
        }
        $total = $data['totalResult'] + 1;

        $spreadsheet = new Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0);

        $spreadsheet->getProperties()
            ->setCreator("G-MEDS")
            ->setLastModifiedBy("G-MEDS")
            ->setTitle("Favorite Medicine List")
            ->setSubject("")
            ->setDescription("");

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('The Medicine List');

        $sheet
            ->setCellValue('A1', 'PLU')
            ->setCellValue('B1', 'Product Name')
            ->setCellValue('C1', "Minium\nOrder Qty")
            ->setCellValue('D1', "No Of Unit\nPer PLU")
            ->setCellValue('E1', "Cost to clinic\n(Local Patient)")
            ->setCellValue('F1', "Selling price to\nLocal Patients (SGD)")
            ->setCellValue('G1', "Cost to clinic\n(Overseas Patient)")
            ->setCellValue('H1', "Selling price to\nOverseas Patients (SGD)");

        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(30);
        $sheet->getColumnDimension('G')->setWidth(30);
        $sheet->getColumnDimension('H')->setWidth(30);

        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $sheet->getStyle('A1:H1')->getFont()->setSize(12);
        $sheet->getStyle('A1:H1')->getAlignment()->setWrapText(true);
        $sheet->getStyle("F1:F$total")->getFill()->setFillType('solid')->getStartColor()->setRGB('efef30');
        $sheet->getStyle("H1:H$total")->getFill()->setFillType('solid')->getStartColor()->setRGB('efef30');

        $index = 2;
        foreach ($data['list'] as $value) {
            $sheet->setCellValue("A$index", $value['sku']);
            $sheet->setCellValue("B$index", $value['name']);
            $sheet->setCellValue("C$index", $value['minimumOrderQuantity']);
            $sheet->setCellValue("D$index", $value['packQuantity']);
            $sheet->setCellValue("E$index", $value['costPriceToClinic']);
            $sheet->getStyle("E$index")->getNumberFormat()->setFormatCode('0.00');

            $domestic = $value['listPriceDomestic'];
            if (isset($value['audits']['new_value'])) {
                $domestic = $value['audits']['new_value'];
            }
            if (empty($domestic) && !empty($value['audits']['old_value'])) {
                $domestic = $value['audits']['old_value'];
            }
            $sheet->setCellValue("F$index", $domestic);
            $sheet->getStyle("F$index")->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue("G$index", $value['costPriceToClinicOversea']);
            $sheet->getStyle("G$index")->getNumberFormat()->setFormatCode('0.00');

            $international = $value['listPriceInternational'];
            if (isset($value['audits']['inter_new_value'])) {
                $international = $value['audits']['inter_new_value'];
            }
            if (empty($international) && !empty($value['audits']['inter_old_value'])) {
                $international = $value['audits']['inter_old_value'];
            }
            $sheet->setCellValue("H$index", $international);
            $sheet->getStyle("H$index")->getNumberFormat()->setFormatCode('0.00');
            $index++;
        }

        $writer = new Xlsx($spreadsheet);

        $response = new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            }
        );

        $fileName = 'favorite-medicine-list.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $fileName
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * @Route("/custom-selling-prices/upload-favorite-drugs", name="doctor_custom_selling_prices_upload_favorite_drugs")
     */
    public function uploadFavoriteDrugsAction(Request $request)
    {
        $gmeds = $this->getUser();
        $userId = $gmeds->getLoggedUser()->getId();

        $data = [];
        $excelFile = isset($_FILES['excelFile']) ? $_FILES['excelFile'] : '';
        if (empty($excelFile)) {
            $data['message'] = '';
            return new JsonResponse($data);
        }

        $doctorId = $this->getDoctorId();

        try {
            $common = $this->get('util.common');
            $fileUrl = $common->uploadfile($excelFile,'doctor/'.$doctorId.'/favorite-medicine-list-'.time().'.xlsx', true);

            if (!file_exists($fileUrl)) {
                throw new \Exception("Upload error.");
            }

            $takeEffectDate = $request->get('takeEffectDate');
            $takeEffectDate = new \DateTime($takeEffectDate);

            $em = $this->getDoctrine()->getManager();

            $doctor = $em->getRepository('UtilBundle:Doctor')->find($doctorId);

            $params = array(
                'page' => 1,
                'limit' => 0
            );

            $data = $em->getRepository('UtilBundle:Doctor')->getFavoriteDrugs($params, $doctorId);

            $pluList = [];
            foreach ($data['list'] as $value) {
                $pluList[] = $value['sku'];
            }

            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($fileUrl);

            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            $haystack = [];
            foreach ($sheetData as $key => $item) {
                if (!$key) {
                    continue;
                }

                $haystack[] = $item['A'];
            }

            $em->beginTransaction();

            $cdate = new \DateTime();
            $cdate->setTime(0, 0);

            foreach ($sheetData as $key => $item) {
                if (!$key) {
                    continue;
                }

                $plu = $item['A'];

                if (!in_array($plu, $pluList)) {
                    continue;
                }

                $drug = $em->getRepository('UtilBundle:Drug')->findOneBySku($plu);
                if (empty($drug)) {
                    continue;
                }

                $priceDomestic = $item['F'];
                $priceInternational = $item['H'];

                $doctorDrugList = $em->getRepository('UtilBundle:DoctorDrug')->findBy(['doctor' => $doctorId, 'drug' => $drug]);

                $doctorDrug = $newDoctorDrug  = null;
                foreach ($doctorDrugList as $value) {
                    if ($value->getListPriceDomesticNew()) {
                        $doctorDrug = $value;
                    }
                    if ($value->getListPriceInternationalNew()) {
                        $newDoctorDrug = $value;
                    }
                }

                if (empty($doctorDrug)) {
                    $doctorDrug = new DoctorDrug();
                    $doctorDrug->setDoctor($doctor);
                    $doctorDrug->setDrug($drug);
                    $doctorDrug->setCreatedOn(new \DateTime());
                }

                if (empty($newDoctorDrug)) {
                    $newDoctorDrug = clone($doctorDrug);
                    $newDoctorDrug->setListPriceDomestic(null);
                    $newDoctorDrug->setListPriceDomesticNew(null);
                }

                $inputs = [];
                if ($priceDomestic > $drug->getListPriceDomestic()) {
                    $oldPrice = $doctorDrug->getListPriceDomesticNew();
                    if (empty($oldPrice)) {
                        $oldPrice = $doctorDrug->getListPriceDomestic();
                    }
                    if (empty($oldPrice)) {
                        $oldPrice = $drug->getListPriceDomestic();
                    }
                    $doctorDrug->setListPriceDomesticNew($priceDomestic);
                    if ($cdate == $takeEffectDate) {
                        $doctorDrug->setListPriceDomestic($priceDomestic);
                    }
                    $doctorDrug->setEffectiveDate($takeEffectDate);

                    $em->persist($doctorDrug);

                    $inputs[] = [
                        'tableName'  => 'doctor_drug',
                        'fieldName'  => 'list_price_domestic',
                        'entityId'   => $drug->getId(),
                        'oldPrice'   => $oldPrice,
                        'newPrice'   => $priceDomestic,
                        'createdBy'  => $userId,
                        'em'         => $em,
                        'effectedOn' => $takeEffectDate
                    ];
                    Utils::saveLogPrice($inputs);
                }

                // New for price international 
                $inputs = [];
                if ($priceInternational > $drug->getListPriceInternational()) {
                    $oldPrice = $doctorDrug->getListPriceInternationalNew();
                    if (empty($oldPrice)) {
                        $oldPrice = $doctorDrug->getListPriceInternational();
                    }
                    if (empty($oldPrice)) {
                        $oldPrice = $drug->getListPriceInternational();
                    }
                    $newDoctorDrug->setListPriceInternationalNew($priceInternational);
                    if ($cdate == $takeEffectDate) {
                        $newDoctorDrug->setListPriceInternational($priceInternational);
                    }

                    $newDoctorDrug->setEffectiveDate($takeEffectDate);

                    $em->persist($newDoctorDrug);

                    $inputs[] = [
                        'tableName'  => 'doctor_drug',
                        'fieldName'  => 'list_price_international',
                        'entityId'   => $drug->getId(),
                        'oldPrice'   => $oldPrice,
                        'newPrice'   => $priceInternational,
                        'createdBy'  => $userId,
                        'em'         => $em,
                        'effectedOn' => $takeEffectDate
                    ];
                    Utils::saveLogPrice($inputs);
                }

                $success = true;
            }

            $em->flush();
            $em->commit();

            $data['status'] = 200;
            $data['message'] = 'Upload successfully.';
            if (empty($success)) {
                $data['status'] = 500;
                $data['message'] = 'Upload error.';
            }
        }  catch(\Exception $ex) {
            $em->rollback();
            $data['status'] = 500;
            $data['message'] = $ex->getMessage();
        }

        return new JsonResponse($data);
    }
}