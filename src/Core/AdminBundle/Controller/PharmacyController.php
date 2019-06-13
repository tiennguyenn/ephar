<?php

namespace AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AdminBundle\Form\PharmacyType;
use UtilBundle\Entity\DrugGroup;
use UtilBundle\Entity\Drug;
use UtilBundle\Entity\Pharmacy;
use UtilBundle\Entity\Phone;
use UtilBundle\Entity\Address;
use UtilBundle\Entity\Bank;
use UtilBundle\Entity\BankAccount;
use UtilBundle\Utility\Constant;
use UtilBundle\Utility\Utils;
use UtilBundle\Utility\Common;
use UtilBundle\Utility\MsgUtils;

class PharmacyController extends BaseController
{
    /**
     * @Route("/admin/pharmacy", name="admin_pharmacy_list")
     */
    public function pharmacyListAction(Request $request)
    {
        $parameters = array(
            'ajaxURL' => 'admin_ajax_pharmacy',
        );
        return $this->render('AdminBundle:pharmacy:pharmacy-list.html.twig', $parameters);
    }

    /**
     * Get list pharmacy by ajax
     * @Route("/admin/ajax-pharmacy", name="admin_ajax_pharmacy")
     * @author vinh.nguyen
     */
    public function ajaxPharmacyAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $result = $em->getRepository('UtilBundle:Pharmacy')->getPharmacies($request->request);

        return new JsonResponse($result);
    }

    /**
     * pharmacy group drug
     * @Route("/admin/pharmacy/group-drug", name="admin_pharmacy_group_drug")
     * @author vinh.nguyen
     */
    public function groupDrugAction(Request $request)
    {
        //get current pharmacy
        $em = $this->getDoctrine()->getEntityManager();
        $pharmacy = $em->getRepository('UtilBundle:Pharmacy')->getUsedPharmacy();

        return $this->render('AdminBundle:pharmacy:group-drug.html.twig', array(
            'pharmacy' => $pharmacy
        ));
    }

    /**
     * list pharmacy group drug by ajax
     * @Route("/admin/pharmacy/group-drug/list", name="admin_pharmacy_group_drug_list")
     * @author vinh.nguyen
     */
    public function ajaxGroupDrugAction()
    {
        $request = $this->get('request');
        $params = $this->filter($request);

        //process data
        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository('UtilBundle:DrugGroup')->getListBy($params);

        //paging
        $totalPages = isset($result['totalPages']) ? $result['totalPages'] : Constant::TOTAL_PAGE_DEFAULT;

        //build paging
        $paginationHTML = Common::buildPagination(
            $this->container,
            $request,
            $totalPages,
            $params['page'],
            $params['perPage'],
            array('pageUrl' =>  $this->generateUrl('admin_pharmacy_group_drug_list', $params))
        );

        return $this->render('AdminBundle:pharmacy:ajax-group-drug.html.twig', array(
            'data'           => $result['data'],
            'paginationHTML' => $paginationHTML,
            'currentPage'    => $params['page'],
            'perPage'        => $params['perPage'],
            'totalResult'    => $result['totalResult'],
            'sorting'        => $params['sorting']
        ));
    }

    /**
     * form pharmacy group drug
     * @Route("/admin/pharmacy/group-drug/form", name="admin_pharmacy_group_drug_form")
     * @author vinh.nguyen
     */
    public function formGroupDrugAction(Request $request)
    {
        $id = (int)$request->get('id', null);
        $f = (int)$request->get('f', 0);
        $em = $this->getDoctrine()->getManager();
        $dgObj = $em->getRepository('UtilBundle:DrugGroup')->find($id);
        $groupList = array();

        switch ($f) {
            case 1:
                $drugs = $em->getRepository('UtilBundle:Drug')->findBy(array(
                    'group' => $dgObj
                ));
                break;
            case 2:
                $drugs = $request->get('drugid', array());
                $groupList = $em->getRepository('UtilBundle:DrugGroup')->findAll();
                break;
            default:
                $drugs = $em->getRepository('UtilBundle:Drug')->getSelectionDrug($dgObj);
                break;
        }

        return $this->render('AdminBundle:pharmacy:group-drug-form.html.twig', array(
            'data'     => $dgObj,
            'formType' => $f,
            'drugs'    => $drugs,
            'groupList'=> $groupList
        ));
    }

    /**
     * delete pharmacy group drug
     * @Route("/admin/pharmacy/group-drug/delete", name="admin_pharmacy_group_drug_delete")
     * @author vinh.nguyen
     */
    public function deleteGroupDrugAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $res = array(
                'status' => false
            );
            $f = (int)$request->get('f', 0);
            $id = $request->get('id', null);
            $em = $this->getDoctrine()->getManager();

            if($f == 1) { //remove group drug
                $dgObj = $em->getRepository('UtilBundle:DrugGroup')->find($id);
                if($dgObj != null && $dgObj->getId() != Constant::DRUG_GROUP_DEFAULT) {
                    //remove group in drug_audit
                    $drugAudits = $em->getRepository('UtilBundle:DrugAudit')->findBy(array(
                        "drugGroup" => $dgObj
                    ));
                    foreach ($drugAudits as $daObj) {
                        $daObj->setDrugGroup(null);
                        $em->persist($daObj);
                        $em->flush();
                    }
                    //remove group
                    $drugObj = $em->getRepository('UtilBundle:Drug')->findBy(array(
                        'group' => $dgObj));
                    if($drugObj == null) {
                        $newValue = array(
                            'group drug id: ' => $dgObj->getId()
                        );
                        $em->remove($dgObj);
                        $em->flush();
                        $res = array(
                            'status' => true,
                            'msg' => 'Group is removed'
                        );
                        //logging
                        $params = array(
                            'module' => 'group_drug',
                            'title' => 'product group deleted',
                            'newValue' => json_encode($newValue),
                            'createdBy' => $this->getUser()->getDisplayName()
                        );
                        $em->getRepository('UtilBundle:Log')->insert($params);
                    } else {
                        $res['msg'] = 'Group is being used';
                    }
                } else {
                    $res['msg'] = 'Group not found';
                }
            } else {//remove group of per drug
                $dgDefault = $em->getRepository('UtilBundle:DrugGroup')->find(Constant::DRUG_GROUP_DEFAULT);
                $drugObj = $em->getRepository('UtilBundle:Drug')->find($id);
                if($drugObj != null) {
                    $drugObj->setGroup($dgDefault);
                    $this->calListPrice($drugObj, $dgDefault, $em);
                    $em->persist($drugObj);
                    $em->flush();
                    $res = array(
                        'status' => true,
                        'msg' => 'drug is removed successful'
                    );
                }
            }
            return new JsonResponse($res);
        } else {
            return $this->redirectToRoute('not_found');
        }
    }

    /**
     * update pharmacy group drug
     * @Route("/admin/pharmacy/group-drug/update", name="admin_pharmacy_group_drug_update")
     * @author vinh.nguyen
     */
    public function updateGroupDrugAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $res                        = null;
            $id                         = (int)$request->get('id', null);
            $name                       = $request->get('name', null);
            $authorId                   = $this->getUser()->getLoggedUser()->getId();
            $oldLocalPricePercentage    = null;
            $oldOverseasPricePercentage = null;

            //check exist name of group
            $em = $this->getDoctrine()->getManager();
            $checkGroup = $em->getRepository('UtilBundle:DrugGroup')->findOneBy(array(
                'name' => $name
            ));
            if($checkGroup && $checkGroup->getId() != $id) {
                $res = 'existed';
                return new JsonResponse($res);
            }

            if($checkGroup) {
                $oldLocalPricePercentage    = $checkGroup->getLocalPricePercentage();
                $oldOverseasPricePercentage = $checkGroup->getOverseasPricePercentage();
            }

            $drugs  = $request->get('drugs', array());
            $params = array(
                'name'                    => $name,
                'description'             => $request->get('description', null),
                'localPricePercentage'    => $request->get('localPricePercentage', null),
                'overseasPricePercentage' => $request->get('overseasPricePercentage', null)
            );
            $dgObj = $em->getRepository('UtilBundle:DrugGroup')->updateBy($id, $params);

            //update DrugGroup to drug
            if($dgObj != null) {
                $res = true;
                //remove un-selected group
                $existDrugs = $em->getRepository('UtilBundle:Drug')->findBy(array(
                    'group' => $dgObj
                ));

                //insert logs price
                if($checkGroup) {
                    if($oldOverseasPricePercentage != $dgObj->getOverseasPricePercentage()) {
                        $inputs[] = [
                            'tableName'  => 'drug_group',
                            'fieldName'  => 'overseas_price_percentage',
                            'entityId'   => $id,
                            'oldPrice'   => $oldOverseasPricePercentage,
                            'newPrice'   => $params['overseasPricePercentage'],
                            'createdBy'  => $authorId,
                            'em'         => $em
                        ];
                    }
                    if($oldLocalPricePercentage != $dgObj->getLocalPricePercentage()) {
                        $inputs[] = [
                            'tableName'  => 'drug_group',
                            'fieldName'  => 'local_price_percentage',
                            'entityId'   => $id,
                            'oldPrice'   => $oldLocalPricePercentage,
                            'newPrice'   => $params['localPricePercentage'],
                            'createdBy'  => $authorId,
                            'em'         => $em
                        ];
                    }
                    Utils::saveLogPrice($inputs);
                }

                if($existDrugs != null) {
                    $dgDefault = $em->getRepository('UtilBundle:DrugGroup')->find(Constant::DRUG_GROUP_DEFAULT);
                    foreach ($existDrugs as $obj) {
                        if(!in_array($obj->getId(), $drugs)) {
                            $obj->setGroup($dgDefault);
                            $this->calListPrice($obj, $dgDefault, $em);
                            $em->persist($obj);
                        }
                    }
                    $em->flush();
                }
                //update new group
                $arrDrugs = array();
                if($dgObj->getId() != Constant::DRUG_GROUP_DEFAULT) {
                    $drugList = $em->getRepository('UtilBundle:Drug')->getDrugBy($drugs);
                    if($drugList != null){
                        foreach ($drugList as $drugObj) {
                            try {
                                $drugObj->setGroup($dgObj);
                                $this->calListPrice($drugObj, $dgObj, $em);
                                $arrDrugs[] = $drugObj->getId();
                                $em->persist($drugObj);
                            } catch (\Exception $ex) {
                                return new JsonResponse($drugObj->getId());
                            }
                        }
                        $em->flush();
                    }
                }
                //logging
                $newValue = array(
                    'name'                    => $dgObj->getName(),
                    'description'             => $dgObj->getDescription(),
                    'localPricePercentage'    => $dgObj->getLocalPricePercentage(),
                    'overseasPricePercentage' => $dgObj->getOverseasPricePercentage()
                );
                if(!empty($arrDrugs))
                    $newValue['drugs'] = 'changed group on drug: '.implode(',', $arrDrugs);

                $params = array(
                    'entityId'  => $dgObj->getId(),
                    'module'    => 'group_drug',
                    'title'     => 'product group',
                    'newValue'  => json_encode($newValue),
                    'createdBy' => $this->getUser()->getDisplayName()
                );
                $em->getRepository('UtilBundle:Log')->insert($params);
            }
            return new JsonResponse($res);
        } else {
            return $this->redirectToRoute('not_found');
        }
    }

    /**
    * calcalate list price
    **/
    private function calListPrice(&$obj, $group, $em)
    {
        $id                        = $obj->getId();
        $oldListPriceDomestic      = $obj->getListPriceDomestic();
        $oldListPriceInternational = $obj->getListPriceInternational();

        $authorId                  = $this->getUser()->getLoggedUser()->getId();
        $costPrice                 = $obj->getCostPrice();
        $percentLocal              = $group->getLocalPricePercentage();
        $percentOverseas           = $group->getOverseasPricePercentage();

        $listPriceDomestic = round($costPrice + ($percentLocal * $costPrice / 100), 2);
        $obj->setListPriceDomestic($listPriceDomestic);
        $listPriceInternational = round($costPrice + ($percentOverseas * $costPrice / 100), 2);
        $obj->setListPriceInternational($listPriceInternational);
        //insert logs price
        $inputs[] = [
            'tableName'  => 'drug',
            'fieldName'  => 'list_price_domestic',
            'entityId'   => $id,
            'oldPrice'   => $oldListPriceDomestic,
            'newPrice'   => $listPriceDomestic,
            'createdBy'  => $authorId,
            'em'         => $em
        ];
        $inputs[] = [
            'tableName'  => 'drug',
            'fieldName'  => 'list_price_international',
            'entityId'   => $id,
            'oldPrice'   => $oldListPriceInternational,
            'newPrice'   => $listPriceInternational,
            'createdBy'  => $authorId,
            'em'         => $em
        ];
        //Utils::saveLogPrice($inputs);
    }

    /**
     * move pharmacy group drug
     * @Route("/admin/pharmacy/group-drug/move", name="admin_pharmacy_group_drug_move")
     * @author vinh.nguyen
     */
    public function moveGroupDrugAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $res = null;
            $id = $request->get('group', null);
            $drugs = $request->get('drugs', array());

            $em = $this->getDoctrine()->getManager();
            $dgObj = $em->getRepository('UtilBundle:DrugGroup')->find($id);

            //update DrugGroup to drug
            if($dgObj != null && !empty($drugs)) {
                $res = $dgObj->getId();
                //update new group
                $arrDrugs = array();
                foreach ($drugs as $drugId) {
                    $drugObj = $em->getRepository('UtilBundle:Drug')->find($drugId);
                    if($drugObj != null) {
                        $drugObj->setGroup($dgObj);
                        $this->calListPrice($drugObj, $dgObj, $em);
                        $em->persist($drugObj);
                        $em->flush();
                        $arrDrugs[] = $drugObj->getId();
                    }
                }
                //logging
                $newValue = array(
                    'note' => 'list drug: '.implode(',', $arrDrugs)
                );
                $params = array(
                    'entityId' => $dgObj->getId(),
                    'module' => 'group_drug_detail',
                    'title' => 'move drug to another group',
                    'newValue' => json_encode($newValue),
                    'createdBy' => $this->getUser()->getDisplayName()
                );
                $em->getRepository('UtilBundle:Log')->insert($params);
            }
            return new JsonResponse($res);
        } else {
            return $this->redirectToRoute('not_found');
        }
    }

    /**
     * group check exist
     * @Route("/admin/pharmacy/group-check", name="admin_pharmacy_group_check")
     * @author vinh.nguyen
     */
    public function checkGroupDrugAction(Request $request)
    {
        $res = false;
        $name = trim($request->get('name', ''));
        if(!empty($name)) {
            $em = $this->getDoctrine()->getManager();
            $dgObj = $em->getRepository('UtilBundle:DrugGroup')->findBy(array(
                'name' => $name
            ));
            if($dgObj != null)
                $res = true;
        }
        return new JsonResponse($res);
    }

    /**
     * filter
     */
    private function filter($request)
    {
        $params = array(
            'page'     => $request->get('page', Constant::PAGE_DEFAULT),
            'perPage'  => $request->get('perPage', Constant::PER_PAGE_DEFAULT),
            'term'     => $request->get('term', ''),
            'sorting'  => $request->get('sorting', '')
        );
        $filters = $request->get('p_filter', array());
        if(!empty($filters)){
            foreach($filters as $k=>$v){
                $params[$k] = $v;
            }
        }
        return $params;
    }

    /**
     * @Route("/admin/pharmacy/{id}/products", name="pharmacy_products")
     */
    public function productAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $groupDrug = $em->getRepository('UtilBundle:DrugGroup')->find($id);
        if (!$groupDrug)
            return $this->redirectToRoute('not_found');

        //get current pharmacy
        $pharmacy = $em->getRepository('UtilBundle:Pharmacy')->getUsedPharmacy();

        $params = array(
            'group_id' => $groupDrug->getId(),
            'name' => $request->query->get("name", ""),
            'status' => $request->query->get("status", "all"),
            'sort' => $request->query->get("sort", ""),
            'dir' => $request->query->get("dir", ""),
            'page' => $request->query->get("page", 1),
            'limit' => $request->query->get("limit", Constant::PER_PAGE_DEFAULT),
        );

        $pending = $em->getRepository('UtilBundle:Drug')->totalPendingProducts($pharmacy->getId(), $groupDrug->getId());
        $settings = null;
        $list = $em->getRepository('UtilBundle:PlatformSettings')->findAll();
        foreach ($list as $item) {
            $settings = $item;
            break;
        }

        return $this->render('AdminBundle:pharmacy:product.html.twig', array(
            "pharmacy" => $pharmacy,
            "group"    => $groupDrug,
            "pending" => $pending,
            "settings" => $settings,
            "params" => $params,
        ));
    }

    /**
     * @Route("/admin/pharmacy/{id}/products-list", name="pharmacy_products_list")
     * @author vinh.nguyen
     */
    public function productListAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $groupDrug = $em->getRepository('UtilBundle:DrugGroup')->find($id);
        if (!$groupDrug)
            return $this->redirectToRoute('not_found');

        //get current pharmacy
        $pharmacy = $em->getRepository('UtilBundle:Pharmacy')->getUsedPharmacy();

        $params = array(
            'group_id' => $groupDrug->getId(),
            'name' => $request->query->get("name", ""),
            'status' => $request->query->get("status", "all"),
            'sort' => $request->query->get("sort", ""),
            'dir' => $request->query->get("dir", ""),
            'page' => $request->query->get("page", 1),
            'limit' => $request->query->get("limit", Constant::PER_PAGE_DEFAULT),
        );

        $page = $request->query->get("page", 1);
        $limit = $request->query->get("limit", Constant::PER_PAGE_DEFAULT);

        if ($limit != 'all') {
            $params['page'] = $page;
            $params['limit'] = $limit;
        }

        $data = $em->getRepository('UtilBundle:Drug')->getListBy($params);

        $drugAudits = $em->getRepository('UtilBundle:Drug')->listProductWithAudit($pharmacy->getId());
        foreach ($data['list'] as &$drug) {
            $drug["audits"] = isset($drugAudits[$drug['id']]) ? $drugAudits[$drug['id']] : array();
        }
        $audits = array();
        $drugAudits = $em->getRepository('UtilBundle:DrugAudit')->findBy(array(
            "drugGroup" => $groupDrug->getId(),
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

        $pending = $em->getRepository('UtilBundle:Drug')->totalPendingProducts($pharmacy->getId(), $groupDrug->getId());
        $settings = null;
        $list = $em->getRepository('UtilBundle:PlatformSettings')->findAll();
        foreach ($list as $item) {
            $settings = $item;
            break;
        }

        return $this->render('AdminBundle:pharmacy:product-list.html.twig', array(
            "pharmacy" => $pharmacy,
            "group"    => $groupDrug,
            "data" => $data,
            "pending" => $pending,
            "settings" => $settings,
            "params" => $params,
            "audits" => $audits,
            "menu" => "pharmacy",
            "sub_menu" => "list",
            "menu_item" => $pharmacy
        ));
    }

    /**
     * @Route("/admin/pharmacy/create", name="admin_pharmacy_create")
     */
    public function pharmacyCreateAction(Request $request) {

        $em = $this->getDoctrine()->getEntityManager();
        $country = $em->getRepository('UtilBundle:Country')->getByPreferredCountry(true);
        $pharmacy = new Pharmacy();
        $dependentData = array();
        $dependentData['country'] = $country;
        $dependentData['cityRepo'] = $em->getRepository('UtilBundle:City');
        $dependentData['pharmacy'] = $pharmacy;

        $form = $this->createForm(new PharmacyType(array('depend' => $dependentData)), array(), array());

        if ($request->getMethod() == 'POST') {
            $data = $request->request->get('admin_pharmacy');

            $em = $this->getDoctrine()->getEntityManager();
            $pharmacy->setName($data['pharmacyName']);
            $pharmacy->setIsGst($data['gst']);
            if($data['gst'] >0) {
                $pharmacy->setGstNo($data['gstRegisterNumber']);
                $pharmacy->setGstEffectiveDate(new \DateTime());
            }
            $pharmacy->setBusinessName($data['businessName']);
            $pharmacy->setEmailAddress($data['email']);
            $phone = new Phone();
            $country = $em->getRepository('UtilBundle:Country')->find($data['phoneLocation']);
            $phone->setCountry($country);
            $phone->setPhoneType($em->getRepository('UtilBundle:PhoneType')->getPhone());
            $phone->setAreaCode($data['phoneArea']);
            $phone->setNumber($data['phone']);
            $pharmacy->addPhone($phone);

            $add = new Address();
            $add->setCity($em->getRepository('UtilBundle:City')->find($data['city']));
            $add->setLine1($data['addressLine1']);
            $add->setLine2($data['addressLine2']);
            $add->setLine3($data['addressLine3']);
            $pharmacy->setRegisteredAddress($add);

            $bankCountry = $data['bankCountry'];
            if (empty($bankCountry)) {
                $bankCountry = $data['country'];
            }
            if (empty($bankCountry)) {
                $bankCountry = $country;
            }
            if ($data['bankCountry'] == Constant::ID_SINGAPORE || $data['bankCountry'] == Constant::ID_MALAYSIA) {
                $bank = $em->getRepository('UtilBundle:Bank')->find($data['bankName']);
            } else {
                $bank = new Bank();
                $bank->setName($data['bankName']);
                $bank->setCountry($em->getRepository('UtilBundle:Country')->find($bankCountry));
                $bank->setSwiftCode($data['bankSwiftCode']);
            }

            $bankAcc = new BankAccount();
            $bankAcc->setBank($bank);
            $bankAcc->setAccountName($data['accountName']);
            $bankAcc->setAccountNumber($data['accountNumber']);
            $pharmacy->setBankAccount($bankAcc);

            $pharmacy->setContactFirstname($data['contactFirstname']);
            $pharmacy->setContactLastname($data['contactLastname']);
            $pharmacy->setPharmacyCode($data['pharmacyCode']);
            $pharmacy->setShortName($data['shortName']);
            $pharmacy->setPharmacistName($data['pharmacistName']);
            $pharmacy->setPharmacistLicense($data['pharmacistLicense']);
            $pharmacy->setUen($data['uen']);
            $pharmacy->setPermitNumber($data['permitNumber']);
            $pharmacy->setPickupAddress($add);
            $pharmacy->setBillingAddress($add);
            $pharmacy->setPhysicalAddress($add);
            $pharmacy->setMailingAddress($add);

            $em->persist($pharmacy);
            $em->flush();
            return $this->redirectToRoute('admin_pharmacy_list');

        }
        $parameters = array(
            'form' => $form->createView(),
            'title' => 'Create a Pharmacy',
            'pharmacy' => $pharmacy,
            'ajaxDependent' => 'admin_doctor_create_getdependent',
        );

        return $this->render('AdminBundle:pharmacy:pharmacy-create.html.twig', $parameters);
    }

    /**
     * @Route("/admin/pharmacy/{id}/edit", name="admin_pharmacy_edit")
     */
    public function pharmacyEditAction(Request $request, $id) {
        $em = $this->getDoctrine()->getEntityManager();
        $country = $em->getRepository('UtilBundle:Country')->getListCountryForPhone();
        $pharmacy = $em->getRepository('UtilBundle:Pharmacy')->find($id);

        $dependentData = array();
        $dependentData['country'] = $country;
        $dependentData['cityRepo'] = $em->getRepository('UtilBundle:City');
        $dependentData['pharmacy'] = $pharmacy;


        $form = $this->createForm(new PharmacyType(array('depend' => $dependentData)), array(), array());

        if ($request->getMethod() == 'POST') {
           $data = $request->request->get('admin_pharmacy');

            $em = $this->getDoctrine()->getEntityManager();
            $pharmacy->setName($data['pharmacyName']);
            $pharmacy->setIsGst($data['gst']);
            if($data['gst'] >0) {
                $pharmacy->setNewGstNo($data['gstRegisterNumber']);
                $pharmacy->setGstEffectiveDate(new \DateTime());
            }
            $pharmacy->setBusinessName($data['businessName']);
            $pharmacy->setPharmacyCode($data['pharmacyCode']);
            $pharmacy->setShortName($data['shortName']);
            $pharmacy->setPharmacistName($data['pharmacistName']);
            $pharmacy->setPharmacistLicense($data['pharmacistLicense']);
            $pharmacy->setUen($data['uen']);
            $pharmacy->setPermitNumber($data['permitNumber']);
            $pharmacy->setEmailAddress($data['email']);
            $phone = $pharmacy->getPhones()->first();
            $country = $em->getRepository('UtilBundle:Country')->find($data['phoneLocation']);
            $phone->setCountry($country);
            $phone->setPhoneType($em->getRepository('UtilBundle:PhoneType')->getPhone());
            $phone->setAreaCode($data['phoneArea']);
            $phone->setNumber($data['phone']);

            $add = $pharmacy->getRegisteredAddress();
            $add->setCity($em->getRepository('UtilBundle:City')->find($data['city']));
            $add->setLine1($data['addressLine1']);
            $add->setLine2($data['addressLine2']);
            $add->setLine3($data['addressLine3']);

            $bankAcc = $pharmacy->getBankAccount();
            if (!$bankAcc) {
                $bankAcc = new BankAccount();
            }
            if ($data['bankCountry'] == Constant::ID_SINGAPORE || $data['bankCountry'] == Constant::ID_MALAYSIA) {
                $bank = $em->getRepository('UtilBundle:Bank')->find($data['bankName']);
            } else {
                $bank = $bankAcc->getBank();
                if (!$bank) {
                    $bank = new Bank();
                }
                $bank->setName($data['bankName']);
                $bank->setSwiftCode($data['bankSwiftCode']);
                $bankCountry = $data['bankCountry'];
                if (empty($bankCountry)) {
                    $bankCountry = $data['country'];
                }
                if (empty($bankCountry)) {
                    $bankCountry = $country;
                }
                $bank->setCountry($em->getRepository('UtilBundle:Country')->find($bankCountry));

            }

            $bankAcc->setAccountName($data['accountName']);
            $bankAcc->setAccountNumber($data['accountNumber']);
            $bankAcc->setBank($bank);
            $pharmacy->setBankAccount($bankAcc);

            $pharmacy->setContactFirstname($data['contactFirstname']);
            $pharmacy->setContactLastname($data['contactLastname']);

            $em->persist($pharmacy);
            $em->flush();
            return $this->redirectToRoute('admin_pharmacy_list');
        }

        $parameters = array(
            'form' => $form->createView(),
            'title' => 'Edit Pharmacy',
            'pharmacy' => $pharmacy,
            'ajaxDependent' => 'admin_doctor_create_getdependent',
        );

        return $this->render('AdminBundle:pharmacy:pharmacy-create.html.twig', $parameters);
    }
}