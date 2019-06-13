<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\AgentFeeSettingType;
use AdminBundle\Form\FeeSettingType;
use AdminBundle\Form\PlatformSettingGstCodeType;
use AdminBundle\Form\PlatformSettingType;
use AdminBundle\Form\PlatformShareFeeType;
use AdminBundle\Form\PlatformSharePercentageType;
use AdminBundle\Form\IndonesiaTaxType;
use AdminBundle\Form\MinFeeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\DateTime;
use UtilBundle\Entity\Agent3paFee;
use UtilBundle\Entity\FeeSetting;
use UtilBundle\Entity\IndonesiaTax;
use UtilBundle\Utility\Common;
use UtilBundle\Utility\Constant;
use UtilBundle\Utility\MsgUtils;
use UtilBundle\Entity\PlatformSettingGstCode;
use Dompdf\Dompdf;
use Symfony\Component\HttpFoundation\Response;
use UtilBundle\Utility\Utils;

class PaymentSettingController extends Controller
{
    /**
     * payment schedule
     * @Route("/admin/schedule", name="payment_schedule")
     */
    public function paymentScheduleAction(Request $request)
    {
        // get platform setting repository
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('UtilBundle:PlatformSettings');
        $psObj = $repo->getPSOption('payment_schedule');
        $psObj['formType'] = 'schedule';

        $formTypeParams = array(
            'dayOfWeekList' => Constant::$dayOfWeek,
            'timeSlotList'  => Common::getTimeSlotList()
        );

        $form = $this->createForm(new PlatformSettingType($formTypeParams), $psObj, array(
            'method' => "post",
            'action' => $this->generateUrl('payment_schedule')
        ));

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $params = array(
                    'operationsCountryId' => $data['operationsCountryId'],
                    'agentStatementDate' => $data['agentStatementDate'],
                    'doctorStatementDate' => $data['doctorStatementDate'],
                    'pharmacyWeeklyPoDay' => $data['pharmacyWeeklyPoDay'],
                    'pharmacyWeeklyPoTime' => $data['pharmacyWeeklyPoTime'],
                    'deliveryFortnightlyPoDay' => $data['deliveryFortnightlyPoDay'],
                    'deliveryFortnightlyPoTime' => $data['deliveryFortnightlyPoTime']
                );

                $oldValue = $repo->getPSOption('payment_schedule');
                $results = $repo->update($params);
                $newValue = $repo->getPSOption('payment_schedule');
                //insert logs
                $logParams = $params;
                $logParams['id'] = $data['operationsCountryId'];
                $logParams['title'] = 'Statements and PO Schedule';
                $logParams['module'] = 'payment_schedule';
                $this->saveLog($oldValue, $newValue, $logParams);

                if(!empty($results))
                    $this->get('session')->getFlashBag()->add('success', MsgUtils::generate('msgUpdatedSuccess', 'Payment Schedule'));
                else
                    $this->get('session')->getFlashBag()->add('danger',MsgUtils::generate('msgCannotEdited', 'Payment Schedule'));
            }
        }

        return $this->render('AdminBundle:payment_setting:schedule.html.twig', array(
            'data' => $psObj,
            'form' => $form->createView()
        ));
    }

    /**
     * gross margin share
     * @Route("/admin/gross-margin-share", name="payment_gross_margin_share")
     */
    public function grossMarginShareAction(Request $request)
    {
        // get platform share percentages
        $em = $this->getDoctrine()->getManager();
        $flagPlatformShareFee = $this->getParameter('platform_share_fee');

        $repo = $em->getRepository('UtilBundle:PlatformSharePercentages');
        $formParams = array(
            'method' => "post",
            'action' => $this->generateUrl('payment_gross_margin_share')
        );

        $localMedicine1 = $repo->getPSPercentageByType(Constant::AREA_TYPE_LOCAL, Constant::MST_MEDICINE);
        $localMedicine2 = $repo->getPSPercentageByType(Constant::AREA_TYPE_OVERSEA, Constant::MST_MEDICINE);
        $localMedicine1['title'] = 'medication_gross_margin_share - local_patients';
        $formMedicine1 = $this->createForm(
            new PlatformSharePercentageType(),
            $localMedicine1,
            $formParams);


        $formService1Data = $repo->getPSPercentageByType(Constant::AREA_TYPE_LOCAL, Constant::MST_SERVICE);
        $formService1Data['title'] = 'prescribing_fee_sharing - local_patients';
        $formService1 = $this->createForm(
            new PlatformSharePercentageType(),
            $formService1Data,
            $formParams);


        $formCustomCAF1Data = $repo->getPSPercentageByType(Constant::AREA_TYPE_LOCAL, Constant::MST_CUSTOM_CAF);
        $formCustomCAF1Data['title'] = 'customs_clearance_admin_fee - local patients';
        $formCustomCAF1 = $this->createForm(
            new PlatformSharePercentageType(),
            $formCustomCAF1Data,
            $formParams);


        $formLiveConsult1Data = $repo->getPSPercentageByType(Constant::AREA_TYPE_LOCAL, Constant::MST_LIVE_CONSULT);
        $formLiveConsult1Data['title'] = 'liveConsult_fee_sharing - local_patients';
        $formLiveConsult1 = $this->createForm(
            new PlatformSharePercentageType(),
            $formLiveConsult1Data,
            $formParams);

        $localMedicine2['title'] = 'medication_gross_margin_share - overseas_patients';
        $formMedicine2 = $this->createForm(
            new PlatformSharePercentageType(),
            $localMedicine2,
            $formParams);

        $formService2Data = $repo->getPSPercentageByType(Constant::AREA_TYPE_OVERSEA, Constant::MST_SERVICE);
        $formService2Data['title'] = 'prescribing_fee_sharing - overseas_patients';

        $formService2 = $this->createForm(
            new PlatformSharePercentageType(),
            $formService2Data,
            $formParams);



        $formLiveConsult2Data = $repo->getPSPercentageByType(Constant::AREA_TYPE_OVERSEA, Constant::MST_LIVE_CONSULT);
        $formLiveConsult2Data['title'] = 'liveConsult_fee_sharing - overseas_patients';
        $formLiveConsult2 = $this->createForm(
            new PlatformSharePercentageType(),
            $formLiveConsult2Data,
            $formParams);


        $formCustomCAF2Data = $repo->getPSPercentageByType(Constant::AREA_TYPE_OVERSEA, Constant::MST_CUSTOM_CAF);
        $formCustomCAF2Data['title'] = 'customs_clearance_admin_fee - overseas_patients';

        $formCustomCAF2 = $this->createForm(
            new PlatformSharePercentageType(),
            $formCustomCAF2Data,
            $formParams);

        if ($request->getMethod() === 'POST') {
            $psPercentage = $request->get('ps_percentage');
            $areaType = $psPercentage['areaType'];
            $params = array();
            switch ($psPercentage['marginShareType']):
                case Constant::MST_MEDICINE:
                    if($areaType == Constant::AREA_TYPE_LOCAL) {
                        $formMedicine1->handleRequest($request);
                        if ($formMedicine1->isValid())
                            $params = $formMedicine1->getData();
                    } else {
                        $formMedicine2->handleRequest($request);
                        if ($formMedicine2->isValid())
                            $params = $formMedicine2->getData();
                    }
                    break;
                case Constant::MST_SERVICE:
                    if($areaType == Constant::AREA_TYPE_LOCAL) {
                        $formService1->handleRequest($request);
                        if ($formService1->isValid())
                            $params = $formService1->getData();
                    } else {
                        $formService2->handleRequest($request);
                        if ($formService2->isValid())
                            $params = $formService2->getData();
                    }
                    break;
                case Constant::MST_CUSTOM_CAF:
                    if($areaType == Constant::AREA_TYPE_LOCAL) {
                        $formCustomCAF1->handleRequest($request);
                        if ($formCustomCAF1->isValid())
                            $params = $formCustomCAF1->getData();
                    } else {
                        $formCustomCAF2->handleRequest($request);
                        if ($formCustomCAF2->isValid())
                            $params = $formCustomCAF2->getData();
                    }
                    break;
                case Constant::MST_LIVE_CONSULT:
                    if($areaType == Constant::AREA_TYPE_LOCAL) {
                        $formLiveConsult1->handleRequest($request);
                        if ($formLiveConsult1->isValid())
                            $params = $formLiveConsult1->getData();
                    } else {
                        $formLiveConsult2->handleRequest($request);
                        if ($formLiveConsult2->isValid())
                            $params = $formLiveConsult2->getData();
                    }
                    break;
            endswitch;

            if(!empty($params)) {
                $oldValue = $repo->getPSPercentageById($params['id']);
                $results  = $repo->update($params);
                $newValue = $repo->getPSPercentageById($params['id']);

                $arr = array('module' => 'agents', 'title' =>'status_changed');
                $loggerUser = $this->getUser()->getLoggedUser();
                $author = $loggerUser->getFirstName() . ' ' . $loggerUser->getLastName();
                Utils::saveLog($oldValue, $newValue, $author, $params, $em);
                // insert log price
                $posts = $request->get('ps_percentage');
                $this->saveLogPriceMarginSharing(
                        $psPercentage['marginShareType'],
                        $posts,
                        $oldValue,
                        $em
                );

                if(!empty($results))
                    $this->get('session')->getFlashBag()->add('success', MsgUtils::generate('msgUpdatedSuccess', 'Gross Margin Share'));
                else
                    $this->get('session')->getFlashBag()->add('danger',MsgUtils::generate('msgCannotEdited', 'Gross Margin Share'));

                return $this->redirectToRoute('payment_gross_margin_share');
            }
        }

        return $this->render('AdminBundle:payment_setting:gross_margin_share.html.twig', array(
            'formMedicine1'    => $formMedicine1->createView(),
            'formService1'     => $formService1->createView(),
            'formCustomCAF1'   => $formCustomCAF1->createView(),
            'formLiveConsult1' => $formLiveConsult1->createView(),
            'formMedicine2'    => $formMedicine2->createView(),
            'formService2'     => $formService2->createView(),
            'formCustomCAF2'   => $formCustomCAF2->createView(),
            'formLiveConsult2' => $formLiveConsult2->createView(),
            'isActiveLocal'    => !empty($localMedicine1)? $localMedicine1['isActive']: false,
            'isActiveOversea'  => !empty($localMedicine2)? $localMedicine2['isActive']: false,
            'flagPlatformShareFee' => $flagPlatformShareFee
        ));
    }

    /**
     * insert logs into log table
     * @param  array $oldData
     * @param  array $newData
     * @param  array $params
     * @return
     */
    public function saveLog($oldData, $newData, $params) {
        $encodeOldData = json_encode($oldData);
        $encodeNewData = json_encode($newData);
        $params['entityId'] = $params['id'];
        $params['action']   = 'update';
        $params['oldValue'] = $encodeOldData;
        $params['newValue'] = $encodeNewData;
        $params['createdBy'] = $this->getUser()->getLoggedUser()->getFirstName() . ' ' .
                $this->getUser()->getLoggedUser()->getLastName();

        // insert data into log table
        $this->getDoctrine()->getManager()
                ->getRepository('UtilBundle:Log')->insert($params);
    }

    /**
     * update status on active of margin share
     * @Route("/admin/gms-update-active", name="payment_gms_update_active")
     */
    public function updateStatusOnActiveAction()
    {
        $request = $this->get('request');
        $isActive = $request->get('is_active', null);
        $areaType = $request->get('area_type', null);
        $em = $this->getDoctrine()->getManager();
        $results = $em->getRepository('UtilBundle:PlatformSharePercentages')->updateOnActive($areaType, $isActive);

        return new JsonResponse($results);
    }

    /**
     * gst rate
     * @Route("/admin/gst-rate", name="payment_gst_rate")
     */
    public function gstRateAction(Request $request)
    {
        // get platform setting repository
        $authorId   = $this->getUser()->getLoggedUser()->getId();
        $em         = $this->getDoctrine()->getManager();
        $gstRateObj = $em->getRepository('UtilBundle:PlatformSettings')->getGstRate();

        $gstRateObj['formType'] = 'gst_rate';

        $gstRateForm = $this->createForm(new PlatformSettingType(), $gstRateObj, array(
            'method' => "post",
            'action' => $this->generateUrl('payment_gst_rate')
        ));
        $pSForm = $this->createForm(new PlatformSettingType(), $gstRateObj, array(
            'method' => "post",
            'action' => $this->generateUrl('payment_gst_rate')
        ));

        //gst code fee
        $srsCodeId = 0;
        $gstCodeList = $em->getRepository('UtilBundle:GstCode')->getAll();
        foreach ($gstCodeList as $value) {
            if (Constant::GST_SRSGM == $value['code']) {
                $srsCodeId = $value['id'];
                break;
            }
        }

        $pSGstCodeList = $em->getRepository('UtilBundle:PlatformSettingGstCode')->getAll();
        foreach ($pSGstCodeList as &$value) {
            if (Constant::GM_PGB_MDR == $value['feeCode']) {
                $value['gstCode'] = $srsCodeId;
                break;
            }
        }

        $gstCodeGmedsForm = $this->createForm(new PlatformSettingGstCodeType($gstCodeList,  'gmeds'), $pSGstCodeList, array(
            'method' => "post",
            'action' => $this->generateUrl('payment_gst_rate')
        ));

        $gstCodeLocalForm = $this->createForm(new PlatformSettingGstCodeType($gstCodeList, 'doctor'), $pSGstCodeList, array(
            'method' => "post",
            'action' => $this->generateUrl('payment_gst_rate')
        ));

        $gstCodeOverseaForm = $this->createForm(new PlatformSettingGstCodeType($gstCodeList, 'doctor'), $pSGstCodeList, array(
            'method' => "post",
            'action' => $this->generateUrl('payment_gst_rate')
        ));

        if ($request->getMethod() === 'POST') {
            $table      = null;
            $field      = null;
            $updateFlag = true;
            $inputs     = [];
            $params     = $request->request->all();

            // update platform_setting_gst_code
            if (isset($params['platform_seting_gst_code'])) {
                $logTitle = $params['gst_type'];
                if (isset($params['gst_type']) && $params['gst_type'] == 'doctor_to_patient_local') {
                    $gstCodeLocalForm->handleRequest($request);
                    $isValid = $gstCodeLocalForm->isValid();
                } elseif (isset($params['gst_type']) && $params['gst_type'] == 'doctor_to_patient_oversea') {
                    $gstCodeOverseaForm->handleRequest($request);
                    $isValid = $gstCodeOverseaForm->isValid();
                } else {
                    $gstCodeGmedsForm->handleRequest($request);
                    $isValid = $gstCodeGmedsForm->isValid();
                }

                if ($isValid == true) {
                    try {
                        $oldValue[$params['gst_type']] = array();
                        $psGstCodeParams = $params['platform_seting_gst_code'];
                        $repoGstCode     = $em->getRepository('UtilBundle:PlatformSettingGstCode');

                        foreach ($psGstCodeParams as $key => $val) {
                            $psGstCode    = $repoGstCode->findOneBy(array('feeCode' => $key));
                            $oldGstCodeId = $psGstCode->getGstCode()->getId();
                            $oldGstType   = $psGstCode->getGstType();
                            $id           = $psGstCode->getId();

                            $oldValue[$params['gst_type']][$key] = array(
                                'id'          => $id,
                                'feeCode'     => $key,
                                'gstCode'     => $psGstCode->getGstCode()->getCode(),
                                'gstType'     => $oldGstType,
                                'description' => $psGstCode->getDescription()
                            );

                            $gstCode = $em->getRepository('UtilBundle:GstCode')->find($val);
                            $psGstCode->setGstCode($gstCode);
                            $psGstCode->setGstType($params['gst_type']);
                            $psGstCode->setUpdatedOn(new \DateTime('now'));

                            $em->persist($psGstCode);
                            $em->flush();

                            $newGstCodeId = $gstCode->getId();
                            $newGstType   = $params['gst_type'];

                            $newValue[$params['gst_type']][$key] = array(
                                'id'          => $psGstCode->getId(),
                                'feeCode'     => $key,
                                'gstCode'     => $gstCode->getCode(),
                                'gstType'     => $newGstType,
                                'description' => $psGstCode->getDescription()
                            );
                            //insert logs price
                            $isGst = isset($params['platform_setting']['isGst']) ? $params['platform_setting']['isGst'] : null;
                            if($oldGstCodeId !== $newGstCodeId)
                            {
                                $table      = 'platform_setting_gst_code';
                                $field      = 'gst_code_id';
                                $effectedOn = null;
                                if(isset($params['platform_setting']['gstAffectDate']))
                                {
                                    $effectedOn = new \DateTime($params['platform_setting']['gstAffectDate']);
                                }

                                $inputs[] = [
                                    'tableName'  => $table,
                                    'fieldName'  => $field,
                                    'entityId'   => $id,
                                    'oldPrice'   => $oldGstCodeId,
                                    'newPrice'   => $newGstCodeId,
                                    'createdBy'  => $authorId,
                                    'em'         => $em,
                                    'effectedOn' => $effectedOn
                                ];
                            }
                        }
                        if(isset($inputs[0]))
                            Utils::saveLogPrice($inputs);

                    } catch (Exception $e) {
                        $updateFlag = false;
                    }
                }
            }
            // update platform setting
            if (isset($params['platform_setting'])) {
                if (isset($params['gst_type'])) {
                    $pSForm->handleRequest($request);
                    $isValid = $pSForm->isValid();
                } else {
                    $gstRateForm->handleRequest($request);
                    $isValid = $gstRateForm->isValid();
                    $logTitle = 'gst_rate';
                }

                if ($isValid == true) {
                    try {
                        $repo                 = $em->getRepository('UtilBundle:PlatformSettings');
                        $oldGstRate           = null;
                        $oldNewGstRate        = null;
                        $oldGstRateAffectDate = null;
                        $oldIsGst             = null;
                        $inputs               = null;
                        $oldGstAffectDate     = null;

                        if($repo->getGstRate()) {
                            $oldGstRate           = $repo->getGstRate()['gstRate'];
                            $oldNewGstRate        = $repo->getGstRate()['newGstRate'];
                            $oldGstRateAffectDate = $repo->getGstRate()['gstRateAffectDate']->format('Y-m-d');
                            $oldIsGst             = $repo->getGstRate()['isGst'];
                            $oldGstAffectDate     = $repo->getGstRate()['gstAffectDate']->format('Y-m-d');
                        }

                        $platformSettingParams     = $params['platform_setting'];
                        $platformSettingParamsData = $gstRateForm->getData();

                        $platformSettingParams['operationsCountryId'] = $platformSettingParamsData['operationsCountryId'];

                        if (isset($params['gst_type'])) {
                            $oldValue['platformSetting'] = $repo->getGstRate();
                        } else {
                            $oldValue = $repo->getGstRate();
                        }

                        // $oldValue = $repo->getGstRate();
                        $gstRateObj = $repo->updateGstRate($platformSettingParams);
                        if (isset($params['gst_type'])) {
                            $newValue['platformSetting'] = $repo->getGstRate();
                        } else {
                            $newValue = $repo->getGstRate();
                        }
                        //insert logs price
                        $newGstAffectDate = null;
                        $isGst            = isset($params['platform_setting']['isGst']) ? $params['platform_setting']['isGst'] : null;
                        if(isset($newValue['platformSetting']['gstAffectDate'])) {
                            $newGstAffectDate = $newValue['platformSetting']['gstAffectDate']->format('Y-m-d');
                        }
                        if($isGst != $oldIsGst && $isGst !== null || ($oldGstAffectDate != $newGstAffectDate && $newGstAffectDate != null))
                        {
                            $inputs[] = [
                                'tableName'  => 'platform_setting',
                                'fieldName'  => 'is_gst',
                                'entityId'   => $newValue['platformSetting']['operationsCountryId'],
                                'oldPrice'   => $oldIsGst,
                                'newPrice'   => $isGst,
                                'createdBy'  => $authorId,
                                'em'         => $em,
                                'effectedOn' => $newValue['platformSetting']['gstAffectDate']
                            ];
                        }
                        if(!isset($params['platform_seting_gst_code'])) {
                            $entityId   = $newValue['operationsCountryId'];
                            $effectDate = $newValue['gstRateAffectDate'];
                            $newPrice   = $newValue['newGstRate'];
                            if($effectDate->format('Y-m-d') != $oldGstRateAffectDate || $oldNewGstRate != $newPrice) {
                                $inputs[] = [
                                    'tableName'  => 'platform_setting',
                                    'fieldName'  => 'gst_rate',
                                    'entityId'   => $entityId,
                                    'oldPrice'   => $oldGstRate,
                                    'newPrice'   => $newPrice,
                                    'createdBy'  => $authorId,
                                    'em'         => $em,
                                    'effectedOn' => $effectDate
                                ];
                            }
                        }
                        Utils::saveLogPrice($inputs);
                    } catch (Exception $e) {
                        $updateFlag = false;
                    }
                }
            }

            $logParams = array(
                'title'  => $logTitle,
                'module' => Constant::GST_RATE_MODULE_NAME
            );
            $loggerUser = $this->getUser()->getLoggedUser();
            $author     = $loggerUser->getFirstName() . ' ' . $loggerUser->getLastName();
            Utils::saveLog($oldValue, $newValue, $author, $logParams, $em);

            if ($updateFlag == true) {
                $this->get('session')->getFlashBag()->add('success', MsgUtils::generate('msgUpdateSuccessShort'));
            } else {
                $this->get('session')->getFlashBag()->add('danger', MsgUtils::generate('msgCannotEditedShort'));
            }
        }

        // return
        return $this->render('AdminBundle:payment_setting:gst_rate.html.twig', array(
            'data'               => $gstRateObj,
            'gstRateForm'        => $gstRateForm->createView(),
            'gstCodeLocalForm'   => $gstCodeLocalForm->createView(),
            'gstCodeOverseaForm' => $gstCodeOverseaForm->createView(),
            'gstCodeGmedsForm'   => $gstCodeGmedsForm->createView(),
            'pSForm'             => $pSForm->createView()
        ));
    }

    /**
     * indonesia import tax
     * @Route("/admin/indonesia-import-tax", name="payment_indonesia_import_tax")
     */
    public function getIndonesiaImportTaxAction(Request $request)
    {
        $authorId   = $this->getUser()->getLoggedUser()->getId();
        $em         = $this->getDoctrine()->getManager();
        $indTaxList = $em->getRepository('UtilBundle:IndonesiaTax')->findBy([]);
        $form = $this->createForm(new IndonesiaTaxType(), $indTaxList, array(
            'method' => "post",
            'action' => $this->generateUrl('payment_indonesia_import_tax')
        ));

        if ($request->getMethod() === 'POST') {
            $postRes = $request->get('it', array());
            if (!empty($postRes)) {
                $form->handleRequest($request);
                $isValid = $form->isValid();
                if ($isValid == true) {
                    $now        = new \DateTime();
                    $effectDate = null;
                    $inputs     = null;
                    try {
                        foreach ($indTaxList as $obj) {
                            $oldTaxValue    = $obj->getTaxValue();
                            $oldTaxValueNew = $obj->getTaxValueNew();
                            $oldEffectDate  = $obj->getEffectDate();
                            $taxName        = $obj->getTaxName();
                            $effectDate     = new \DateTime($postRes[$taxName . 'Date']);

                            $obj->setTaxValueNew($postRes[$taxName]);
                            $obj->setEffectDate($effectDate);

                            $newPrice = floatval($postRes[$taxName]);

                            if ($now->format('Y-m-d') >= $effectDate->format('Y-m-d')) {
                                $obj->setTaxValue($postRes[$taxName]);
                            }
                            $obj->setUpdatedOn(new \DateTime());
                            $em->persist($obj);

                            //insert logs
                            $oldValue = array(
                                'taxName' => $taxName,
                                'description' => $obj->getDescription(),
                                'taxValue' => $oldTaxValueNew,
                                'effectDate' => $oldEffectDate->format("d M Y")
                            );
                            $newValue = array(
                                'taxName' => $taxName,
                                'description' => $obj->getDescription(),
                                'taxValue' => $postRes[$taxName],
                                'effectDate' => $effectDate->format("d M Y")
                            );
                            $logParams['id'] = null;
                            $logParams['title'] = 'indonesia_import_tax_changed';
                            $logParams['module'] = Constant::INDONESIA_IMPORT_TAX_MODULE_NAME;
                            $this->saveLog($oldValue, $newValue, $logParams);

                            $newTaxValue    = $obj->getTaxValue();
                            $newTaxValueNew = $obj->getTaxValueNew();

                            if($oldEffectDate->format("Y-m-d") != $effectDate->format("Y-m-d") || $oldTaxValueNew != $newTaxValueNew) {
                                $inputs[] = [
                                    'tableName'  => 'indonesia_tax',
                                    'fieldName'  => 'tax_value',
                                    'entityId'   => $obj->getId(),
                                    'oldPrice'   => $oldTaxValue,
                                    'newPrice'   => $newPrice,
                                    'createdBy'  => $authorId,
                                    'effectedOn' => $effectDate,
                                    'em'         => $em,
                                ];
                            }
                        }
                        $em->flush();

                        // insert logs price
                        Utils::saveLogPrice($inputs);

                        $this->get('session')->getFlashBag()->add('success', MsgUtils::generate('msgUpdatedSuccess', 'Indonesia Import Tax'));
                    } catch (Exception $e) {
                        $this->get('session')->getFlashBag()->add('danger', MsgUtils::generate('msgCannotEditedShort'));
                    }
                } else {
                    $this->get('session')->getFlashBag()->add('danger', MsgUtils::generate('msgCannotEditedShort'));
                }
            } else {
                $this->get('session')->getFlashBag()->add('danger', MsgUtils::generate('msgNoData'));
            }
        }

        return $this->render('AdminBundle:payment_setting:indonesia_import_tax.html.twig', array(
            'form'    => $form->createView()
        ));
    }

    public function saveLogPriceMarginSharing($type, $posts, $oldData, $em)
    {
        try
        {
            $oldEffectDate = isset($oldData['takeEffectOn']) ? $oldData['takeEffectOn']->format('Y-m-d') : null;
            $authorId      = $this->getUser()->getLoggedUser()->getId();
            $table         = 'platform_share_percentages';
            $entityId      = $posts['id'];
            $effectDate    = new \DateTime( date('Y-m-d', strtotime( $posts['takeEffectOn'] )));
            $inputs        = [];

            $arrTmp = [
                'agent_percentage'    => ['agentPercentage', 'newAgentPercentage'],
                'platform_percentage' => ['platformPercentage', 'newPlatformPercentage'],
                'doctor_percentage'   => ['doctorPercentage', 'newDoctorPercentage']
            ];
            foreach ($arrTmp as $field => $val) {
                $oldValue    = $oldData[$val[0]];
                $oldValueNew = $oldData[$val[1]];
                $newValue    = $posts[$val[0]];

                if($oldEffectDate != $effectDate->format('Y-m-d') || $oldValueNew != $newValue) {
                    $inputs[] = [
                        'tableName'  => $table,
                        'fieldName'  => $field,
                        'entityId'   => $entityId,
                        'oldPrice'   => $oldValue,
                        'newPrice'   => $newValue,
                        'createdBy'  => $authorId,
                        'em'         => $em,
                        'effectedOn' => $effectDate
                    ];
                }
            }
            Utils::saveLogPrice($inputs);
        }
        catch (\Exception $e)
        {
            return new Response($e->getMessage());
        }
    }

    /**
     * @author Thang Do
     * Global setting for margin share and fees
     * @Route("/admin/global-margin-share-fee", name="payment_global_margin_share_fee")
     */
    public function globalMarginShareFeeAction(Request $request)
    {
        // get platform share percentages
        $em = $this->getDoctrine()->getManager();
        $repoFee = $em->getRepository('UtilBundle:PlatformShareFee');

        $formParams = array(
                'method' => "post",
                'action' => $this->generateUrl('payment_global_margin_share_fee')
        );

        $localMedicine1 = $repoFee->getPSPercentageByType(Constant::AREA_TYPE_LOCAL, Constant::MST_MEDICINE);
        $localMedicine1['title'] = 'medication_gross_margin_share - local_patients';
        $localMedicine1['medicine_flag'] = true;

        $formMedicine1 = $this->createForm(
                new PlatformShareFeeType(),
                $localMedicine1,
                $formParams);

        $formService1Data = $repoFee->getPSPercentageByType(Constant::AREA_TYPE_LOCAL, Constant::MST_SERVICE);
        $formService1Data['title'] = 'prescribing_fee_sharing - local_patients';


        $formService1 = $this->createForm(
                new PlatformShareFeeType(),
                $formService1Data,
                $formParams);


        $formLiveConsult1Data = $repoFee->getPSPercentageByType(Constant::AREA_TYPE_LOCAL, Constant::MST_LIVE_CONSULT);
        $formLiveConsult1Data['title'] = 'liveConsult_fee_sharing - local_patients';
        $formLiveConsult1 = $this->createForm(
                new PlatformShareFeeType(),
                $formLiveConsult1Data,
                $formParams);

        $localMedicine2 = $repoFee->getPSPercentageByType(Constant::AREA_TYPE_OVERSEA, Constant::MST_MEDICINE);
        $localMedicine2['title'] = 'medication_gross_margin_share - overseas_patients';
        $localMedicine2['medicine_flag'] = true;

        $formMedicine2 = $this->createForm(
                new PlatformShareFeeType(),
                $localMedicine2,
                $formParams);

        $formService2Data = $repoFee->getPSPercentageByType(Constant::AREA_TYPE_OVERSEA, Constant::MST_SERVICE);
        $formService2Data['title'] = 'prescribing_fee_sharing - overseas_patients';

        $formService2 = $this->createForm(
                new PlatformShareFeeType(),
                $formService2Data,
                $formParams);

        $formLiveConsult2Data = $repoFee->getPSPercentageByType(Constant::AREA_TYPE_OVERSEA, Constant::MST_LIVE_CONSULT);
        $formLiveConsult2Data['title'] = 'liveConsult_fee_sharing - overseas_patients';
        $formLiveConsult2 = $this->createForm(
                new PlatformShareFeeType(),
                $formLiveConsult2Data,
                $formParams);

        // 3RD AGENT
        $repository = $this->getDoctrine()->getRepository(Agent3paFee::class);
        // medicine fee type =1

        $fee3ardMedicineLocal =  $repository->findOneBy(['area' => Constant::GMS_TYPE_3RD_AGENT_LOCAL, 'feeType' => Constant::GMS_FEE_3RD_AGENT_MEDICINE,'agent' => null]);

        if(empty($fee3ardMedicineLocal)){
            $fee3ardMedicineLocal = new Agent3paFee();
            $fee3ardMedicineLocal->setArea(Constant::GMS_TYPE_3RD_AGENT_LOCAL);
            $fee3ardMedicineLocal->setFeeType(Constant::GMS_FEE_3RD_AGENT_MEDICINE);
            $feeSetting = new FeeSetting();
            $feeSetting->setFee(0);
            $feeSetting->setNewFee(0);
            $feeSetting->setEffectDate(new \DateTime('now'));
            $fee3ardMedicineLocal->setFeeSetting($feeSetting);
            $em->persist($fee3ardMedicineLocal);
            $em->flush();

        }

        $form_local_medicine = $this->createForm(new AgentFeeSettingType(), ['fee'=> $fee3ardMedicineLocal->getFeeSetting()], []);
        $fee3ardMedicineLocalFee = -1;
        if(!empty($fee3ardMedicineLocal->getFeeSetting()->getEffectDate())&& strtotime($fee3ardMedicineLocal->getFeeSetting()->getEffectDate()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            $fee3ardMedicineLocalFee = $fee3ardMedicineLocal->getFeeSetting()->getFee();
        }


        $fee3ardMedicineOversea = $repository->findOneBy(['area' => Constant::GMS_TYPE_3RD_AGENT_OVERSEAS, 'feeType' => Constant::GMS_FEE_3RD_AGENT_MEDICINE,'agent' => null]);
        if(empty($fee3ardMedicineOversea)){
            $fee3ardMedicineOversea = new Agent3paFee();
            $fee3ardMedicineOversea->setArea(Constant::GMS_TYPE_3RD_AGENT_OVERSEAS);
            $fee3ardMedicineOversea->setFeeType(Constant::GMS_FEE_3RD_AGENT_MEDICINE);
            $feeSetting = new FeeSetting();
            $feeSetting->setFee(0);
            $feeSetting->setNewFee(0);
            $feeSetting->setEffectDate(new \DateTime('now'));
            $fee3ardMedicineOversea->setFeeSetting($feeSetting);
            $em->persist($fee3ardMedicineOversea);
            $em->flush();

        }

        $form_oversea_medicine = $this->createForm(new AgentFeeSettingType(), ['fee'=> $fee3ardMedicineOversea->getFeeSetting()], []);
        $fee3ardMedicineOverseaFee = '-1';
        if(!empty($fee3ardMedicineOversea->getFeeSetting()->getEffectDate())&& strtotime($fee3ardMedicineOversea->getFeeSetting()->getEffectDate()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            $fee3ardMedicineOverseaFee = $fee3ardMedicineOversea->getFeeSetting()->getFee();
        }


        // prescription fee type = 2
        $fee3ardDescriptionLocal = $repository->findOneBy(['area' => Constant::GMS_TYPE_3RD_AGENT_LOCAL, 'feeType' => Constant::GMS_FEE_3RD_AGENT_PRESCRIPTION,'agent' => null]);
        if(empty($fee3ardDescriptionLocal)){
            $fee3ardDescriptionLocal = new Agent3paFee();
            $fee3ardDescriptionLocal->setArea(Constant::GMS_TYPE_3RD_AGENT_LOCAL);
            $fee3ardDescriptionLocal->setFeeType(Constant::GMS_FEE_3RD_AGENT_PRESCRIPTION);
            $feeSetting = new FeeSetting();
            $feeSetting->setFee(0);
            $feeSetting->setNewFee(0);
            $feeSetting->setEffectDate(new \DateTime('now'));
            $fee3ardDescriptionLocal->setFeeSetting($feeSetting);
            $em->persist($fee3ardDescriptionLocal);
            $em->flush();

        }

        $form_local_description = $this->createForm(new AgentFeeSettingType(), ['fee'=> $fee3ardDescriptionLocal->getFeeSetting()], []);
        $fee3ardDescriptionLocalFee = -1;
        if(!empty($fee3ardDescriptionLocal->getFeeSetting()->getEffectDate())&& strtotime($fee3ardDescriptionLocal->getFeeSetting()->getEffectDate()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            $fee3ardDescriptionLocalFee = $fee3ardDescriptionLocal->getFeeSetting()->getFee();
        }

        $fee3ardDescriptionOversea = $repository->findOneBy(['area' => Constant::GMS_TYPE_3RD_AGENT_OVERSEAS, 'feeType' => Constant::GMS_FEE_3RD_AGENT_PRESCRIPTION,'agent' => null]);

        if(empty($fee3ardDescriptionOversea)){
            $fee3ardDescriptionOversea = new Agent3paFee();
            $fee3ardDescriptionOversea->setArea(Constant::GMS_TYPE_3RD_AGENT_OVERSEAS);
            $fee3ardDescriptionOversea->setFeeType(Constant::GMS_FEE_3RD_AGENT_PRESCRIPTION);
            $feeSetting = new FeeSetting();
            $feeSetting->setFee(0);
            $feeSetting->setNewFee(0);
            $feeSetting->setEffectDate(new \DateTime('now'));
            $fee3ardDescriptionOversea->setFeeSetting($feeSetting);
            $em->persist($fee3ardDescriptionOversea);
            $em->flush();

        }

        $form_oversea_description = $this->createForm(new AgentFeeSettingType(), ['fee'=> $fee3ardDescriptionOversea->getFeeSetting()], []);
        $fee3ardDescriptionOverseaFee = '-1';
        if(!empty($fee3ardDescriptionOversea->getFeeSetting()->getEffectDate())&& strtotime($fee3ardDescriptionOversea->getFeeSetting()->getEffectDate()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            $fee3ardDescriptionOverseaFee = $fee3ardDescriptionOversea->getFeeSetting()->getFee();
        }
        // liveconsult type =3
        $fee3ardConsultLocal = $repository->findOneBy(['area' => Constant::GMS_TYPE_3RD_AGENT_LOCAL, 'feeType' => Constant::GMS_FEE_3RD_AGENT_LIVECONSULT,'agent' => null]);
        if(empty($fee3ardConsultLocal)){
            $fee3ardConsultLocal = new Agent3paFee();
            $fee3ardConsultLocal->setArea(Constant::GMS_TYPE_3RD_AGENT_LOCAL);
            $fee3ardConsultLocal->setFeeType(Constant::GMS_FEE_3RD_AGENT_LIVECONSULT);
            $feeSetting = new FeeSetting();
            $feeSetting->setFee(0);
            $feeSetting->setNewFee(0);
            $feeSetting->setEffectDate(new \DateTime('now'));
            $fee3ardConsultLocal->setFeeSetting($feeSetting);
            $em->persist($fee3ardConsultLocal);
            $em->flush();

        }

        $form_local_consult = $this->createForm(new AgentFeeSettingType(), ['fee'=> $fee3ardConsultLocal->getFeeSetting()], []);
        $fee3ardConsultLocalFee = -1;
        if(!empty($fee3ardConsultLocal->getFeeSetting()->getEffectDate())&& strtotime($fee3ardConsultLocal->getFeeSetting()->getEffectDate()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            $fee3ardConsultLocalFee = $fee3ardConsultLocal->getFeeSetting()->getFee();
        }

        $fee3ardConsultOversea = $repository->findOneBy(['area' => Constant::GMS_TYPE_3RD_AGENT_OVERSEAS, 'feeType' => Constant::GMS_FEE_3RD_AGENT_LIVECONSULT,'agent' => null]);
        if(empty($fee3ardConsultOversea)){
            $fee3ardConsultOversea = new Agent3paFee();
            $fee3ardConsultOversea->setArea(Constant::GMS_TYPE_3RD_AGENT_OVERSEAS);
            $fee3ardConsultOversea->setFeeType(Constant::GMS_FEE_3RD_AGENT_LIVECONSULT);
            $feeSetting = new FeeSetting();
            $feeSetting->setFee(0);
            $feeSetting->setNewFee(0);
            $feeSetting->setEffectDate(new \DateTime('now'));
            $fee3ardConsultOversea->setFeeSetting($feeSetting);
            $em->persist($fee3ardConsultOversea);
            $em->flush();

        }

        $form_oversea_consult = $this->createForm(new AgentFeeSettingType(), ['fee'=> $fee3ardConsultOversea->getFeeSetting()], []);
        $fee3ardConsultOverseaFee = '-1';
        if(!empty($fee3ardConsultOversea->getFeeSetting()->getEffectDate())&& strtotime($fee3ardConsultOversea->getFeeSetting()->getEffectDate()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            $fee3ardConsultOverseaFee = $fee3ardConsultOversea->getFeeSetting()->getFee();
        }

        $agentFees = $em->getRepository('UtilBundle:AgentMininumFeeSetting')->findAll();
        $agentFeePrimary = array(
            'local'=> '',
            'feeIndo'=> '',
            'feeEastMalay'=> '',
            'feeWestMalay'=> '',
            'feeInternational'=> '',
        );
        $agentFeeSecondary = array(
            'local'=> '',
            'feeIndo'=> '',
            'feeEastMalay'=> '',
            'feeWestMalay'=> '',
            'feeInternational'=> '',
        );
        foreach ($agentFees as $fee) {
           if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_PRIMARY_lOCAL){
                $agentFeePrimary['local'] =  $fee->getFeeValue();
            }
            if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_PRIMARY_INDONESIA){
                $agentFeePrimary['feeIndo'] =  $fee->getFeeValue();
            }
            if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_PRIMARY_EAST_MALAY){
                $agentFeePrimary['feeEastMalay'] =  $fee->getFeeValue();
            } if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_PRIMARY_WEST_MALAY){
                $agentFeePrimary['feeWestMalay'] =  $fee->getFeeValue();
            }
            if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_PRIMARY_INTERNATIONAL){
                $agentFeePrimary['feeInternational'] =  $fee->getFeeValue();
            }
            if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_SECONDARY_lOCAL){
                $agentFeeSecondary['local'] =  $fee->getFeeValue();
            }
            if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_SECONDARY_INDONESIA){
                $agentFeeSecondary['feeIndo'] =  $fee->getFeeValue();
            }
            if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_SECONDARY_EAST_MALAY){
                $agentFeeSecondary['feeEastMalay'] =  $fee->getFeeValue();
            }
            if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_SECONDARY_WEST_MALAY){
                $agentFeeSecondary['feeWestMalay'] =  $fee->getFeeValue();
            }
            if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_SECONDARY_INTERNATIONAL){
                $agentFeeSecondary['feeInternational'] =  $fee->getFeeValue();
            }
        }
        $primaryMinMarginForm = $this->createForm(
            new MinFeeType([]),
            $agentFeePrimary,
            array(
                'method' => "post",
                'action' => $this->generateUrl('admin_others_fee'),
            )
        );
        $secondaryMinMarginForm = $this->createForm(
            new MinFeeType([]),
            $agentFeeSecondary,
            array(
                'method' => "post",
                'action' => $this->generateUrl('admin_others_fee'),
            )
        );

        if ($request->getMethod() === 'POST') {
            $ps = array_merge($request->get('ps_percentage', $request->get('ps_fee')));

            $areaType = $ps['areaType'];
            $params = array();
            switch ($ps['marginShareType']):
                case Constant::MST_MEDICINE:
                    if($areaType == Constant::AREA_TYPE_LOCAL) {
                        $formMedicine1->handleRequest($request);
                        if ($formMedicine1->isValid())
                            $params = $formMedicine1->getData();
                    } else {
                        $formMedicine2->handleRequest($request);
                        if ($formMedicine2->isValid())
                            $params = $formMedicine2->getData();
                    }
                    break;
                case Constant::MST_SERVICE:
                    if($areaType == Constant::AREA_TYPE_LOCAL) {
                        $formService1->handleRequest($request);
                        if ($formService1->isValid())
                            $params = $formService1->getData();
                    } else {
                        $formService2->handleRequest($request);
                        if ($formService2->isValid())
                            $params = $formService2->getData();
                    }
                    break;
                case Constant::MST_LIVE_CONSULT:
                    if($areaType == Constant::AREA_TYPE_LOCAL) {
                        $formLiveConsult1->handleRequest($request);
                        if ($formLiveConsult1->isValid())
                            $params = $formLiveConsult1->getData();
                    } else {
                        $formLiveConsult2->handleRequest($request);
                        if ($formLiveConsult2->isValid())
                            $params = $formLiveConsult2->getData();
                    }
                    break;
            endswitch;

            if(!empty($params)) {
                $oldValue = $repoFee->getPSPercentageById($params['id']);
                $results  = $repoFee->update($params);
                $newValue = $repoFee->getPSPercentageById($params['id']);

                // insert logs
                $arr = array('module' => 'agents', 'title' =>'status_changed');
                $loggerUser = $this->getUser()->getLoggedUser();
                $author = $loggerUser->getFirstName() . ' ' . $loggerUser->getLastName();
                $abc = Utils::saveLog($oldValue, $newValue, $author, $params, $em);
                // insert log price
                $posts = $ps;
                $this->saveLogPriceMarginSharing(
                        $ps['marginShareType'],
                        $posts,
                        $oldValue,
                        $em
                );


                if(!empty($results))
                    $this->get('session')->getFlashBag()->add('success', MsgUtils::generate('msgManyUpdatedSuccess', 'Global Medicine Margin Share And Fees'));
                else
                    $this->get('session')->getFlashBag()->add('danger',MsgUtils::generate('msgCannotEdited', 'Global Medicine Margin Share And Fees'));
                
                return $this->redirectToRoute('payment_global_margin_share_fee');
            }
        }

        return $this->render('AdminBundle:payment_setting:global_margin_share_fee.html.twig', array(
                'ajaxURL'       => 'admin_agent_fee_ajax',
                'formMedicine1'    => $formMedicine1->createView(),
                'formService1'     => $formService1->createView(),
                'formLiveConsult1' => $formLiveConsult1->createView(),
                'formMedicine2'    => $formMedicine2->createView(),
                'formService2'     => $formService2->createView(),
                'formLiveConsult2' => $formLiveConsult2->createView(),
                'isActiveLocal'    => $localMedicine1['isActive'],
                'isActiveOversea'  => $localMedicine2['isActive'],
                'agentFormLocalMedicine' => $form_local_medicine->createView(),
                'fee3ardMedicineLocalFee' => $fee3ardMedicineLocalFee,
                'agentFormOverseaMedicine' => $form_oversea_medicine->createView(),
                'fee3ardMedicineOverseaFee' => $fee3ardMedicineOverseaFee,

                'agentFormLocalDescription' => $form_local_description->createView(),
                'fee3ardDescriptionLocalFee' => $fee3ardDescriptionLocalFee,
                'agentFormOverseaDescription' => $form_oversea_description->createView(),
                'fee3ardDescriptionOverseaFee' => $fee3ardDescriptionOverseaFee,

                'agentFormLocalConsult' => $form_local_consult->createView(),
                'fee3ardConsultLocalFee' => $fee3ardConsultLocalFee,
                'agentFormOverseaConsult' => $form_oversea_consult->createView(),
                'fee3ardConsultOverseaFee' => $fee3ardConsultOverseaFee,
                'primaryMinMarginForm' => $primaryMinMarginForm->createView(),
                'secondaryMinMarginForm' => $secondaryMinMarginForm->createView(),

        ));
    }

    /**
     * @author Thang Do
     * update status on active of margin share
     * @Route("/admin/gms-new-update-active", name="payment_gms_new_update_active")
     */
    public function newUpdateStatusOnActiveAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $isActive = $request->get('is_active', null);
        $areaType = $request->get('area_type', null);
        $servePage = $request->get('serve_page', null);

//
        if($servePage == 'CAF'){
            $results = $em->getRepository('UtilBundle:PlatformSharePercentages')->updateOnlyCAFOnActive($areaType, $isActive);
        }
        else{
            //Percentage
            $results = $em->getRepository('UtilBundle:PlatformSharePercentages')->updateOnlyMedicineOnActive($areaType, $isActive);

            //Fee
            $results = $em->getRepository('UtilBundle:PlatformShareFee')->updateOnActive($areaType, $isActive);
//
        }

        return new JsonResponse('abc');
    }


    /**
     * @Route("/admin/ajax-agent-3rd-fee", name="admin_agent_fee_ajax")
     */
    public function ajaxAgent3rdFeeAction(Request $request) {
        $result     = array('success' => FALSE);
        $em = $this->getDoctrine()->getManager();
        if($request->getMethod() == 'POST') {

            $data       = $request->request->get('admin_agent_fee');

            if( strtotime($data['date']) >= strtotime(date("Y-m-d"))) {
                $effectDate = new \DateTime( date('Y-m-d', strtotime($data['date'])));
                $fee        = $em->getRepository('UtilBundle:FeeSetting')->find($data['id']);

                $fee->setNewFee( floatval($data['value']));
                $fee->setEffectDate($effectDate);
                $curentValue = $fee->getFee();
                if( strtotime($data['date']) == strtotime(date("Y-m-d"))){
                    $fee->setFee( $fee->getNewFee());
                    $curentValue = '';
                }

                $em->persist($fee);
                $em->flush();

                $result['success'] = true;
                $result['value']   = $curentValue;
                $result['message'] = '';
            } else {
                $result['success'] = false;
                $result['value']   = '';
                $result['message'] = 'Effect date is invalid';
            }

        }



        return new JsonResponse($result);
    }

}
