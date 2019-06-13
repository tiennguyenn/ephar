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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AdminBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use UtilBundle\Entity\Agent3paFee;
use UtilBundle\Entity\FeeSetting;
use UtilBundle\Entity\PersonalInformation;
use UtilBundle\Entity\Phone;
use UtilBundle\Entity\Address;
use UtilBundle\Entity\Bank;
use UtilBundle\Entity\Agent;
use UtilBundle\Entity\BankAccount;
use UtilBundle\Entity\Identification;
use UtilBundle\Entity\AgentCompany;
use UtilBundle\Entity\AgentPrimaryCustomFee;
use UtilBundle\Entity\AgentCustomMedicineFee;
use UtilBundle\Utility\Common;
use UtilBundle\Utility\Constant;
use AdminBundle\Form\AgentAdminType;
use AdminBundle\Form\SubAgentAdminType;
use AdminBundle\Form\SelectBoxAgentType;
use UtilBundle\Utility\Utils;
class AgentsController extends BaseController
{

    /**
     * @Route("/agents", name="agents")
     */
    public function indexAction(Request $request)
    {

        return $this->render('AdminBundle:agents:index.html.twig');
    }

    /**
     * @Route("/register-agent", name="register_agent")
     */
    public function registerAction(Request $request)
    {
        $form = $this->createForm('AdminBundle\Form\AgentType');
        $form->handleRequest($request);

        return $this->render('AdminBundle:agents:register.html.twig', [
                    'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/admin/agent", name="admin_agent")
     */
    public function agentAction(Request $request)
    {
  
        $parameters=  array(
                'ajaxURL' => 'admin_agent_list_ajax',
                'updateStatusUrl' => 'admin_agent_update_status_ajax',
                'viewDoctorUrl' =>'admin_agent_view_doctor_ajax'           
            );
        
        return $this->render('AdminBundle:admin:agent.html.twig',$parameters);
    }
    /**
     * @Route("/admin/agent-auto-complete", name="admin_agent_autocomplete")
     */
    public function agentAutocompleteAction(Request $request)
    {
        $data = array(); 
        $data['text'] = $request->request->get('term');       
        $data['id'] = $request->request->get('id');
        $data['status'] = $request->request->get('status',2);
        $em = $this->getDoctrine()->getEntityManager();    
        $result = $em->getRepository('UtilBundle:Agent')->selectAgentAutoComplete($data); 
       
        return new JsonResponse($result);
    }
    /**
     * @Route("/admin/agent/{id}/sub-agent", name="admin_sub_agent")
     */
    public function subAgentAction(Request $request,$id)
    {
        $em = $this->getDoctrine()->getEntityManager();  
        $agent = $em->getRepository('UtilBundle:Agent')->find($id);
        $parameters=  array(
            'ajaxURL' => 'admin_agent_list_ajax',
            'updateStatusUrl' => 'admin_agent_update_status_ajax',
            'viewDoctorUrl' =>'admin_agent_view_doctor_ajax',
            'agentId' => $id,
            'agentName' => $agent->getPersonalInformation()->getFullName() 
            );
        
        return $this->render('AdminBundle:admin:sub-agent.html.twig',$parameters);
    }

    /**
     * @Route("/admin/agent/{id}/reactivate-agent", name="admin_reactivate_agent")
     */
    public function reactivateAgentAction(Request $request, $id)
    {
        try{
            $em = $this->getDoctrine()->getEntityManager();
            $agent = $em->getRepository('UtilBundle:Agent')->find($id);
            $deletedOn = null;
            $agent->setDeletedOn($deletedOn);
            $agent->setIsActive(true);

            $users = array();
            $userActors = $em->getRepository('UtilBundle:UserActors')->findBy(array(
                'entityId' => $agent->getId(),
                'role' => array(Constant::AGENT_ROLE, Constant::SUB_AGENT_ROLE)
            ));

            $em->beginTransaction();
            try {
                $em->persist($agent);
                $em->flush();

                if (!empty($userActors)) {
                    foreach ($userActors as $userActor) {
                        $users[] = $userActor->getUser();
                        $userActor->setDeletedOn($deletedOn);
                        $em->persist($userActor);
                    }
                    $em->flush();
                }

                if (!empty($users)) {
                    foreach ($users as $user) {
                        $user->setIsActive(1);
                        $em->persist($user);
                    }
                    $em->flush();
                }

                $em->commit();
            } catch (\Exception $ex) {
                $em->rollback();
            }

            return new JsonResponse(array('success' => true));
        } catch (\Exception $exception) {
            return new JsonResponse(array('success' => false));
        }
    }

     /**
     * @Route("/admin/agent-doctor-view", name="admin_agent_view_doctor_ajax")
     */
    public function agentViewDoctorAjaxAction(Request $request)
    {   
        $type = $request->request->get('type');
        $em = $this->getDoctrine()->getEntityManager();        
        if($type ==1 ) {
            $em = $this->getDoctrine()->getEntityManager();  

            $result = $em->getRepository('UtilBundle:Agent')->getDoctorsForViewModal($request->request->get('id'));   
            
            return new JsonResponse($result);
        }
        if($type ==2 ) {
            $result = array();
            $id = $request->request->get('id');
            
            $agent =  $em->getRepository('UtilBundle:Agent')->getAgentForSelectBoxDelete($id);
            $result['agentSelectBox'] = '';
            if(!empty($agent)) {
                $form = $this->createForm(new SelectBoxAgentType(array('agent' => $agent)), array(), array());       
                $parameters = array(
                    'form' => $form->createView(),

                );
                $html =  $this->render('AdminBundle:admin:deleteAgentPopup.html.twig',$parameters);
              
                $result['agentSelectBox'] = $html->getContent();
            }
            $agentData =  $em->getRepository('UtilBundle:Agent')->getAgentDataForDeletePopup($id);
            $result['data'] = $agentData;
            return new JsonResponse($result);
        }
        if($type == 3 ) {
            $result = array();
            $data = $request->request->get('data');
            $data = Common::removeSpaceOf($data);
            $result =  $this->deleteAgentAdmin($data);  
            return new JsonResponse($result);
        }
        if($type == 4 ) {
            $result = array();
            $data = $request->request->get('data');
            $data = Common::removeSpaceOf($data);
            $result =  $this->deleteSubAgentAdmin($data);  
            return new JsonResponse($result);
        }
    }  
    
    /*
     * delete a sub agent
     */
    private function deleteSubAgentAdmin($data){
        $id = $data['id'];
        $em = $this->getDoctrine()->getEntityManager();    
        $agent = $em->getRepository('UtilBundle:Agent')->find($id);        
        $agentDoctors = $agent->getAgentDoctors();
        
        $agentAvailable = $em->getRepository('UtilBundle:Agent')->getAgentForSelectBoxDelete($id);
       
        if(empty($agentAvailable)) {
            $pa = $agent->getParent();
            foreach ($agentDoctors as $ad) {
                if(empty($ad->getDeletedOn())) {
                    $doctor = $ad->getDoctor();
                    $doctor->addAgent($pa);
                    $em->persist($doctor);
                    $em->flush();
                }
            }
        } else {
            $doctor = $data['doctor'];
            foreach ($doctor as $key=>$val) {
                $doctor = $em->getRepository('UtilBundle:Doctor')->find($key); 
                $doctor->addAgent($em->getRepository('UtilBundle:Agent')->find($val));
                $em->persist($doctor);
                $em->flush();
            }
        }
        foreach ($agentDoctors as $ad) {
            $ad->setDeletedOn( new \DateTime('now'));
        }
        $agent->setDeletedOn( new \DateTime('now'));
        $agent->setBlockedNote($data['note']);
        
        $em->persist($agent);
        $em->flush();
                
        return array('success' => true);
        
    }

    /*
     * delete an agent
     */
    private function deleteAgentAdmin($data){
        $id = $data['id'];
        $em = $this->getDoctrine()->getEntityManager();    
        $agent = $em->getRepository('UtilBundle:Agent')->find($id);        
        $agentDoctors = $agent->getAgentDoctors();
        foreach ($agentDoctors as $ad) {
            $ad->setDeletedOn( new \DateTime('now'));
        }
        $doctor = $data['doctor'];
        foreach ($doctor as $key=>$val) {
            $doctor = $em->getRepository('UtilBundle:Doctor')->find($key); 
            $doctor->addAgent($em->getRepository('UtilBundle:Agent')->find($val));
            $em->persist($doctor);
            $em->flush();
        }
      
        $agent->setDeletedOn( new \DateTime('now'));
        $agent->setBlockedNote($data['note']);
        
        $em->persist($agent);
        $em->flush();
                
        return array('success' => true);
        
    }

    /**
     * @Route("/admin/agent/{id}/edit", name="admin_agent_edit")
    */
    public function agentEditAction(Request $request,$id)
    {
        $allowLogging = false;
        $em = $this->getDoctrine()->getEntityManager();
        $country = $em->getRepository('UtilBundle:Country')->getByPreferredCountry(true);
        $agent = $em->getRepository('UtilBundle:Agent')->find($id);
        $dependentData= array();
        $dependentData['country'] = $country;
        $dependentData['cityRepo'] = $em->getRepository('UtilBundle:City');
        $dependentData['agentCompany'] = $em->getRepository('UtilBundle:AgentCompany');


        $agentFeeMedicine = $em->getRepository('UtilBundle:Agent')->getAgentFeeMedicine($agent);
        foreach ($agentFeeMedicine as &$amf) {
            $amf['takeEffectOn'] = $amf['takeEffectOn']->format('d M y');
        }
        $dependentData['agentFeeMedicine'] = $agentFeeMedicine;

        $listAgentFee = [];
        $repoFee = $em->getRepository('UtilBundle:AgentPrimaryCustomFee');
        $repoDefaultFee = $em->getRepository('UtilBundle:PlatformShareFee');

        $listAgentFee['feeServiceLocalAgent'] = -1;
        $listAgentFee['feeServiceLocalPlatform'] = -1;
        $feeServiceLocal = $repoFee->findOneBy(array(
            'agent'           => $agent,
            'isActive'        => 1,
            'areaType'        => Constant::AREA_TYPE_LOCAL,
            'marginShareType' => Constant::MST_SERVICE,
        ));
        if(empty($feeServiceLocal)) {
            $feeServiceLocal = $repoDefaultFee->findOneBy([
                'areaType' => Constant::AREA_TYPE_LOCAL,
                'marginShareType' => Constant::MST_SERVICE
            ]);
            if(strtotime($feeServiceLocal->getTakeEffectOn()->format("Y-m-d")) <= strtotime(date("Y-m-d"))) {
                $feeServiceLocal->setTakeEffectOn(new \DateTime());
            }
        }
        if(!empty($feeServiceLocal->getTakeEffectOn())
            && strtotime($feeServiceLocal->getTakeEffectOn()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            if($feeServiceLocal->getAgentPercentage() != $feeServiceLocal->getNewAgentPercentage()) {
                $listAgentFee['feeServiceLocalAgent'] = $feeServiceLocal->getAgentPercentage();
            }
            if($feeServiceLocal->getPlatformPercentage() != $feeServiceLocal->getNewPlatformPercentage()) {
                $listAgentFee['feeServiceLocalPlatform'] = $feeServiceLocal->getPlatformPercentage();
            }
        }

        $listAgentFee['feeServiceOverseaAgent'] = -1;
        $listAgentFee['feeServiceOverseaPlatform'] = -1;
        $feeServiceOversea = $repoFee->findOneBy(array(
            'agent'           => $agent,
            'isActive'        => 1,
            'areaType'        => Constant::AREA_TYPE_OVERSEA,
            'marginShareType' => Constant::MST_SERVICE,
        ));
        if(empty($feeServiceOversea)) {
            $feeServiceOversea = $repoDefaultFee->findOneBy([
                'areaType' => Constant::AREA_TYPE_OVERSEA,
                'marginShareType' => Constant::MST_SERVICE
            ]);
            if(strtotime($feeServiceOversea->getTakeEffectOn()->format("Y-m-d")) <= strtotime(date("Y-m-d"))) {
                $feeServiceOversea->setTakeEffectOn(new \DateTime());
            }
        }
        if(!empty($feeServiceOversea->getTakeEffectOn())
            && strtotime($feeServiceOversea->getTakeEffectOn()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            if($feeServiceOversea->getAgentPercentage() != $feeServiceOversea->getNewAgentPercentage()) {
                $listAgentFee['feeServiceOverseaAgent'] = $feeServiceOversea->getAgentPercentage();
            }
            if($feeServiceOversea->getPlatformPercentage() != $feeServiceOversea->getNewPlatformPercentage()) {
                $listAgentFee['feeServiceOverseaPlatform'] = $feeServiceOversea->getPlatformPercentage();
            }
        }

        $listAgentFee['feeMedicineLocalAgent'] = -1;
        $listAgentFee['feeMedicineLocalPlatform'] = -1;
        $feeMedicineLocal = $repoFee->findOneBy(array(
            'agent'           => $agent,
            'isActive'        => 1,
            'areaType'        => Constant::AREA_TYPE_LOCAL,
            'marginShareType' => Constant::MST_MEDICINE,
        ));
        if(empty($feeMedicineLocal)) {
            $feeMedicineLocal = $repoDefaultFee->findOneBy([
                'areaType' => Constant::AREA_TYPE_LOCAL,
                'marginShareType' => Constant::MST_MEDICINE
            ]);
            if(strtotime($feeMedicineLocal->getTakeEffectOn()->format("Y-m-d")) <= strtotime(date("Y-m-d"))) {
                $feeMedicineLocal->setTakeEffectOn(new \DateTime());
            }
        }
        if(!empty($feeMedicineLocal->getTakeEffectOn())
            && strtotime($feeMedicineLocal->getTakeEffectOn()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            if($feeMedicineLocal->getAgentPercentage() != $feeMedicineLocal->getNewAgentPercentage()) {
                $listAgentFee['feeMedicineLocalAgent'] = $feeMedicineLocal->getAgentPercentage();
            }
            if($feeMedicineLocal->getPlatformPercentage() != $feeMedicineLocal->getNewPlatformPercentage()) {
                $listAgentFee['feeMedicineLocalPlatform'] = $feeMedicineLocal->getPlatformPercentage();
            }
        }

        $listAgentFee['feeMedicineOverseaAgent'] = -1;
        $listAgentFee['feeMedicineOverseaPlatform'] = -1;
        $feeMedicineOversea = $repoFee->findOneBy(array(
            'agent'           => $agent,
            'isActive'        => 1,
            'areaType'        => Constant::AREA_TYPE_OVERSEA,
            'marginShareType' => Constant::MST_MEDICINE,
        ));
        if(empty($feeMedicineOversea)) {
            $feeMedicineOversea = $repoDefaultFee->findOneBy([
                'areaType' => Constant::AREA_TYPE_OVERSEA,
                'marginShareType' => Constant::MST_MEDICINE
            ]);
            if(strtotime($feeMedicineOversea->getTakeEffectOn()->format("Y-m-d")) <= strtotime(date("Y-m-d"))) {
                $feeMedicineOversea->setTakeEffectOn(new \DateTime());
            }
        }
        if(!empty($feeMedicineOversea->getTakeEffectOn())
            && strtotime($feeMedicineOversea->getTakeEffectOn()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            if($feeMedicineOversea->getAgentPercentage() != $feeMedicineOversea->getNewAgentPercentage()) {
                $listAgentFee['feeMedicineOverseaAgent'] = $feeMedicineOversea->getAgentPercentage();
            }
            if($feeMedicineOversea->getPlatformPercentage() != $feeMedicineOversea->getNewPlatformPercentage()) {
                $listAgentFee['feeMedicineOverseaPlatform'] = $feeMedicineOversea->getPlatformPercentage();
            }
        }

        $listAgentFee['feeConsultLocalAgent'] = -1;
        $listAgentFee['feeConsultLocalPlatform'] = -1;
        $feeConsultLocal = $repoFee->findOneBy(array(
            'agent'           => $agent,
            'isActive'        => 1,
            'areaType'        => Constant::AREA_TYPE_LOCAL,
            'marginShareType' => Constant::MST_LIVE_CONSULT,
        ));
        if(empty($feeConsultLocal)) {
            $feeConsultLocal = $repoDefaultFee->findOneBy([
                'areaType' => Constant::AREA_TYPE_LOCAL,
                'marginShareType' => Constant::MST_LIVE_CONSULT
            ]);
            if(strtotime($feeConsultLocal->getTakeEffectOn()->format("Y-m-d")) <= strtotime(date("Y-m-d"))) {
                $feeConsultLocal->setTakeEffectOn(new \DateTime());
            }
        }
        if(!empty($feeConsultLocal->getTakeEffectOn())
            && strtotime($feeConsultLocal->getTakeEffectOn()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            if($feeConsultLocal->getAgentPercentage() != $feeConsultLocal->getNewAgentPercentage()) {
                $listAgentFee['feeConsultLocalAgent'] = $feeConsultLocal->getAgentPercentage();
            }
            if($feeConsultLocal->getPlatformPercentage() != $feeConsultLocal->getNewPlatformPercentage()) {
                $listAgentFee['feeConsultLocalPlatform'] = $feeConsultLocal->getPlatformPercentage();
            }
        }

        $listAgentFee['feeConsultOverseaAgent'] = -1;
        $listAgentFee['feeConsultOverseaPlatform'] = -1;
        $feeConsultOversea = $repoFee->findOneBy(array(
            'agent'           => $agent,
            'isActive'        => 1,
            'areaType'        => Constant::AREA_TYPE_OVERSEA,
            'marginShareType' => Constant::MST_LIVE_CONSULT,
        ));
        if(empty($feeConsultOversea)) {
            $feeConsultOversea = $repoDefaultFee->findOneBy([
                'areaType' => Constant::AREA_TYPE_OVERSEA,
                'marginShareType' => Constant::MST_LIVE_CONSULT
            ]);
            if(strtotime($feeConsultOversea->getTakeEffectOn()->format("Y-m-d")) <= strtotime(date("Y-m-d"))) {
                $feeConsultOversea->setTakeEffectOn(new \DateTime());
            }
        }
        if(!empty($feeConsultOversea->getTakeEffectOn())
            && strtotime($feeConsultOversea->getTakeEffectOn()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            if($feeConsultOversea->getAgentPercentage() != $feeConsultOversea->getNewAgentPercentage()) {
                $listAgentFee['feeConsultOverseaAgent'] = $feeConsultOversea->getAgentPercentage();
            }
            if($feeConsultOversea->getPlatformPercentage() != $feeConsultOversea->getNewPlatformPercentage()) {
                $listAgentFee['feeConsultOverseaPlatform'] = $feeConsultOversea->getPlatformPercentage();
            }
        }

        $defaultMinAgentFees = $em->getRepository('UtilBundle:AgentMininumFeeSetting')
            ->findAll();
        $minAgentFees = $em->getRepository('UtilBundle:AgentCustomMedicineFee')
            ->findBy(['agent' => $agent]);
        if(empty($minAgentFees)) {
            $minAgentFees = $defaultMinAgentFees;
        }
        $minAgentFeePrimary = array(
            'local'=> '',
            'feeIndo'=> '',
            'feeEastMalay'=> '',
            'feeWestMalay'=> '',
            'feeInternational'=> '',
        );
        $minAgentFee3pa = array(
            'local'=> '',
            'feeIndo'=> '',
            'feeEastMalay'=> '',
            'feeWestMalay'=> '',
            'feeInternational'=> '',
        );
        foreach ($minAgentFees as $fee) {
           if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_PRIMARY_lOCAL){
                $minAgentFeePrimary['local'] =  $fee->getFeeValue();
            }
            if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_PRIMARY_INDONESIA){
                $minAgentFeePrimary['feeIndo'] =  $fee->getFeeValue();
            }
            if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_PRIMARY_EAST_MALAY){
                $minAgentFeePrimary['feeEastMalay'] =  $fee->getFeeValue();
            } if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_PRIMARY_WEST_MALAY){
                $minAgentFeePrimary['feeWestMalay'] =  $fee->getFeeValue();
            }
            if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_PRIMARY_INTERNATIONAL){
                $minAgentFeePrimary['feeInternational'] =  $fee->getFeeValue();
            }
            if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_SECONDARY_lOCAL){
                $minAgentFee3pa['local'] =  $fee->getFeeValue();
            }
            if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_SECONDARY_INDONESIA){
                $minAgentFee3pa['feeIndo'] =  $fee->getFeeValue();
            }
            if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_SECONDARY_EAST_MALAY){
                $minAgentFee3pa['feeEastMalay'] =  $fee->getFeeValue();
            }
            if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_SECONDARY_WEST_MALAY){
                $minAgentFee3pa['feeWestMalay'] =  $fee->getFeeValue();
            }
            if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_SECONDARY_INTERNATIONAL){
                $minAgentFee3pa['feeInternational'] =  $fee->getFeeValue();
            }
        }
        if(empty($minAgentFee3pa['local'])) {
            foreach ($defaultMinAgentFees as $fee) {
                if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_SECONDARY_lOCAL){
                    $minAgentFee3pa['local'] =  $fee->getFeeValue();
                }
                if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_SECONDARY_INDONESIA){
                    $minAgentFee3pa['feeIndo'] =  $fee->getFeeValue();
                }
                if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_SECONDARY_EAST_MALAY){
                    $minAgentFee3pa['feeEastMalay'] =  $fee->getFeeValue();
                }
                if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_SECONDARY_WEST_MALAY){
                    $minAgentFee3pa['feeWestMalay'] =  $fee->getFeeValue();
                }
                if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_SECONDARY_INTERNATIONAL){
                    $minAgentFee3pa['feeInternational'] =  $fee->getFeeValue();
                }
            }
        }

        $repository = $this->getDoctrine()->getRepository(Agent3paFee::class);
        $is3rd = $agent->getIs3paAgent();
        
        if($is3rd){
            $fee3ardMedicineLocal =  $repository->findOneBy(['area' => Constant::GMS_TYPE_3RD_AGENT_LOCAL, 'feeType' => Constant::GMS_FEE_3RD_AGENT_MEDICINE,'agent' => $agent,'deletedOn' => null]);
            $fee3ardMedicineOversea = $repository->findOneBy(['area' => Constant::GMS_TYPE_3RD_AGENT_OVERSEAS, 'feeType' => Constant::GMS_FEE_3RD_AGENT_MEDICINE,'agent' => $agent,'deletedOn' => null]);
            $fee3ardDescriptionLocal = $repository->findOneBy(['area' => Constant::GMS_TYPE_3RD_AGENT_LOCAL, 'feeType' => Constant::GMS_FEE_3RD_AGENT_PRESCRIPTION,'agent' => $agent,'deletedOn' => null]);
            $fee3ardDescriptionOversea = $repository->findOneBy(['area' => Constant::GMS_TYPE_3RD_AGENT_OVERSEAS, 'feeType' => Constant::GMS_FEE_3RD_AGENT_PRESCRIPTION,'agent' => $agent,'deletedOn' => null]);
            $fee3ardConsultLocal = $repository->findOneBy(['area' => Constant::GMS_TYPE_3RD_AGENT_LOCAL, 'feeType' => Constant::GMS_FEE_3RD_AGENT_LIVECONSULT,'agent' => $agent,'deletedOn' => null]);
            $fee3ardConsultOversea = $repository->findOneBy(['area' => Constant::GMS_TYPE_3RD_AGENT_OVERSEAS, 'feeType' => Constant::GMS_FEE_3RD_AGENT_LIVECONSULT,'agent' => $agent,'deletedOn' => null]);

        } else {
            $fee3ardMedicineLocal =  $repository->findOneBy(['area' => Constant::GMS_TYPE_3RD_AGENT_LOCAL, 'feeType' => Constant::GMS_FEE_3RD_AGENT_MEDICINE,'agent' => null,'deletedOn' => null]);
            $fee3ardMedicineOversea = $repository->findOneBy(['area' => Constant::GMS_TYPE_3RD_AGENT_OVERSEAS, 'feeType' => Constant::GMS_FEE_3RD_AGENT_MEDICINE,'agent' => null,'deletedOn' => null]);
            $fee3ardDescriptionLocal = $repository->findOneBy(['area' => Constant::GMS_TYPE_3RD_AGENT_LOCAL, 'feeType' => Constant::GMS_FEE_3RD_AGENT_PRESCRIPTION,'agent' => null,'deletedOn' => null]);
            $fee3ardDescriptionOversea = $repository->findOneBy(['area' => Constant::GMS_TYPE_3RD_AGENT_OVERSEAS, 'feeType' => Constant::GMS_FEE_3RD_AGENT_PRESCRIPTION,'agent' => null,'deletedOn' => null]);
            $fee3ardConsultLocal = $repository->findOneBy(['area' => Constant::GMS_TYPE_3RD_AGENT_LOCAL, 'feeType' => Constant::GMS_FEE_3RD_AGENT_LIVECONSULT,'agent' => null,'deletedOn' => null]);
            $fee3ardConsultOversea = $repository->findOneBy(['area' => Constant::GMS_TYPE_3RD_AGENT_OVERSEAS, 'feeType' => Constant::GMS_FEE_3RD_AGENT_LIVECONSULT,'agent' => null,'deletedOn' => null]);

        }

        $listAgentFee['fee3ardMedicineLocalFee'] = -1;
        if(!empty($fee3ardMedicineLocal->getFeeSetting()->getEffectDate())&& strtotime($fee3ardMedicineLocal->getFeeSetting()->getEffectDate()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            $listAgentFee['fee3ardMedicineLocalFee'] = $fee3ardMedicineLocal->getFeeSetting()->getFee();
        }

        $listAgentFee['fee3ardMedicineOverseaFee'] = -1;
        if(!empty($fee3ardMedicineOversea->getFeeSetting()->getEffectDate())&& strtotime($fee3ardMedicineOversea->getFeeSetting()->getEffectDate()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            $listAgentFee['fee3ardMedicineOverseaFee'] = $fee3ardMedicineOversea->getFeeSetting()->getFee();
        }

        $listAgentFee['fee3ardDescriptionLocalFee'] = -1;
        if(!empty($fee3ardDescriptionLocal->getFeeSetting()->getEffectDate())&& strtotime($fee3ardDescriptionLocal->getFeeSetting()->getEffectDate()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            $listAgentFee['fee3ardDescriptionLocalFee'] = $fee3ardDescriptionLocal->getFeeSetting()->getFee();
        }

        $listAgentFee['fee3ardDescriptionOverseaFee'] = -1;
        if(!empty($fee3ardDescriptionOversea->getFeeSetting()->getEffectDate())&& strtotime($fee3ardDescriptionOversea->getFeeSetting()->getEffectDate()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            $listAgentFee['fee3ardDescriptionOverseaFee'] = $fee3ardDescriptionOversea->getFeeSetting()->getFee();
        }

        $listAgentFee['fee3ardConsultLocalFee'] = -1;
        if(!empty($fee3ardConsultLocal->getFeeSetting()->getEffectDate())&& strtotime($fee3ardConsultLocal->getFeeSetting()->getEffectDate()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            $listAgentFee['fee3ardConsultLocalFee'] = $fee3ardConsultLocal->getFeeSetting()->getFee();
        }

        $listAgentFee['fee3ardConsultOverseaFee'] = -1;
        if(!empty($fee3ardConsultOversea->getFeeSetting()->getEffectDate())&& strtotime($fee3ardConsultOversea->getFeeSetting()->getEffectDate()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            $listAgentFee['fee3ardConsultOverseaFee'] = $fee3ardConsultOversea->getFeeSetting()->getFee();
        }


        if(!empty($agent->getParent()))
        {
            return $this->redirectToRoute('admin_agent');
        }
        $form = $this->createForm(new AgentAdminType(
            array(
                'agent' => $agent,
                'fee' => array(
                    'medLc' => $fee3ardMedicineLocal,
                    'medOs' => $fee3ardMedicineOversea,
                    'desLc' => $fee3ardDescriptionLocal,
                    'desOs' => $fee3ardDescriptionOversea,
                    'conLc' => $fee3ardConsultLocal,
                    'conOs' => $fee3ardConsultOversea,
                    'priDesLc' => $feeServiceLocal,
                    'priDesOs' => $feeServiceOversea,
                    'priMedLc' => $feeMedicineLocal,
                    'priMedOs' => $feeMedicineOversea,
                    'priConLc' => $feeConsultLocal,
                    'priConOs' => $feeConsultOversea,
                    'minPri'   => $minAgentFeePrimary,
                    'min3pa'   => $minAgentFee3pa,
                ),
                'depend'=>$dependentData,
                'entity_manager' => $this->get('doctrine.orm.entity_manager')
            )
        ), array(), array());
        $eror = array();
        if($request->getMethod() == 'POST') {  
            // detect writing log or not
            $allowLogging = $request->request->get('detect-changed', 'false')  == 'true' ? true : false;
            $form->handleRequest($request);   
            $data = $request->request->get('admin_agent');
            $data = Common::removeSpaceOf($data);
            $agent->setIsGst($data['gstSetting']);
            if ($data['gstSetting'] == 1) {
                $agent->setGstNo($data['gstNo']);
                $agent->setGstEffectDate( new \DateTime(date('Y-m-d', strtotime($data['gstEffectDate']))) );
            }
            // if(isset($data['checkMinFee'])&& !empty($data['checkMinFee']) ){
                $agent->setIsMinimunFeeEnabled(true);
            // } else {
            //     $agent->setIsMinimunFeeEnabled(false);
            // }

            // Setup primary fee
            if (!($feeServiceLocal instanceof AgentPrimaryCustomFee)) {
                $feeServiceLocalAgent = $feeServiceLocal->getAgentPercentage();
                $feeServiceLocalPlatform = $feeServiceLocal->getPlatformPercentage();

                $feeServiceLocal = new AgentPrimaryCustomFee();
                $feeServiceLocal->setAreaType(Constant::AREA_TYPE_LOCAL);
                $feeServiceLocal->setMarginShareType(Constant::MST_SERVICE);
                $feeServiceLocal->setAgentPercentage($feeServiceLocalAgent);
                $feeServiceLocal->setPlatformPercentage($feeServiceLocalPlatform);
                $feeServiceLocal->setAgent($agent);
                $agent->addPrimaryFee($feeServiceLocal);
            }
            $feeServiceLocal->setNewAgentPercentage($data['feeServiceLocal']);
            $feeServiceLocal->setNewPlatformPercentage($data['feePlatformServiceLocal']);
            $feeServiceLocal->setTakeEffectOn(new \DateTime($data['feeServiceLocalDate']));
            if(strtotime($feeServiceLocal->getTakeEffectOn()->format("Y-m-d")) <= strtotime(date("Y-m-d"))) {
                $feeServiceLocal->setAgentPercentage($feeServiceLocal->getNewAgentPercentage());
                $feeServiceLocal->setPlatformPercentage($feeServiceLocal->getNewPlatformPercentage());
            }
            $em->persist($feeServiceLocal);

            if (!($feeServiceOversea instanceof AgentPrimaryCustomFee)) {
                $feeServiceOverseaAgent = $feeServiceOversea->getAgentPercentage();
                $feeServiceOverseaPlatform = $feeServiceOversea->getPlatformPercentage();

                $feeServiceOversea = new AgentPrimaryCustomFee();
                $feeServiceOversea->setAreaType(Constant::AREA_TYPE_OVERSEA);
                $feeServiceOversea->setMarginShareType(Constant::MST_SERVICE);
                $feeServiceOversea->setAgentPercentage($feeServiceOverseaAgent);
                $feeServiceOversea->setPlatformPercentage($feeServiceOverseaPlatform);
                $feeServiceOversea->setAgent($agent);
                $agent->addPrimaryFee($feeServiceOversea);
            }
            $feeServiceOversea->setNewAgentPercentage($data['feeServiceOversea']);
            $feeServiceOversea->setNewPlatformPercentage($data['feePlatformServiceOversea']);
            $feeServiceOversea->setTakeEffectOn(new \DateTime($data['feeServiceOverseaDate']));
            if(strtotime($feeServiceOversea->getTakeEffectOn()->format("Y-m-d")) <= strtotime(date("Y-m-d"))) {
                $feeServiceOversea->setAgentPercentage($feeServiceOversea->getNewAgentPercentage());
                $feeServiceOversea->setPlatformPercentage($feeServiceOversea->getNewPlatformPercentage());
            }
            $em->persist($feeServiceOversea);

            if (!($feeMedicineLocal instanceof AgentPrimaryCustomFee)) {
                $feeMedicineLocalAgent = $feeMedicineLocal->getAgentPercentage();

                $feeMedicineLocal = new AgentPrimaryCustomFee();
                $feeMedicineLocal->setAreaType(Constant::AREA_TYPE_LOCAL);
                $feeMedicineLocal->setMarginShareType(Constant::MST_MEDICINE);
                $feeMedicineLocal->setPlatformPercentage(0);
                $feeMedicineLocal->setNewPlatformPercentage(0);
                $feeMedicineLocal->setAgentPercentage($feeMedicineLocalAgent);
                $feeMedicineLocal->setAgent($agent);
                $agent->addPrimaryFee($feeMedicineLocal);
            }
            $feeMedicineLocal->setNewAgentPercentage($data['feeMedicineLocal']);
            $feeMedicineLocal->setTakeEffectOn(new \DateTime($data['feeMedicineLocalDate']));
            if(strtotime($feeMedicineLocal->getTakeEffectOn()->format("Y-m-d")) <= strtotime(date("Y-m-d"))) {
                $feeMedicineLocal->setAgentPercentage($feeMedicineLocal->getNewAgentPercentage());
            }
            $em->persist($feeMedicineLocal);

            if (!($feeMedicineOversea instanceof AgentPrimaryCustomFee)) {
                $feeMedicineOverseaAgent = $feeMedicineOversea->getAgentPercentage();

                $feeMedicineOversea = new AgentPrimaryCustomFee();
                $feeMedicineOversea->setAreaType(Constant::AREA_TYPE_OVERSEA);
                $feeMedicineOversea->setMarginShareType(Constant::MST_MEDICINE);
                $feeMedicineOversea->setPlatformPercentage(0);
                $feeMedicineOversea->setNewPlatformPercentage(0);
                $feeMedicineOversea->setAgentPercentage($feeMedicineOverseaAgent);
                $feeMedicineOversea->setAgent($agent);
                $agent->addPrimaryFee($feeMedicineOversea);
            }
            $feeMedicineOversea->setNewAgentPercentage($data['feeMedicineOversea']);
            $feeMedicineOversea->setTakeEffectOn(new \DateTime($data['feeMedicineOverseaDate']));
            if(strtotime($feeMedicineOversea->getTakeEffectOn()->format("Y-m-d")) <= strtotime(date("Y-m-d"))) {
                $feeMedicineOversea->setAgentPercentage($feeMedicineOversea->getNewAgentPercentage());
            }
            $em->persist($feeMedicineOversea);

            if (!($feeConsultLocal instanceof AgentPrimaryCustomFee)) {
                $feeConsultLocalAgent = $feeConsultLocal->getAgentPercentage();
                $feeConsultLocalPlatform = $feeConsultLocal->getPlatformPercentage();

                $feeConsultLocal = new AgentPrimaryCustomFee();
                $feeConsultLocal->setAreaType(Constant::AREA_TYPE_LOCAL);
                $feeConsultLocal->setMarginShareType(Constant::MST_LIVE_CONSULT);
                $feeConsultLocal->setAgentPercentage($feeConsultLocalAgent);
                $feeConsultLocal->setPlatformPercentage($feeConsultLocalPlatform);
                $feeConsultLocal->setAgent($agent);
                $agent->addPrimaryFee($feeConsultLocal);
            }
            $feeConsultLocal->setNewAgentPercentage($data['feeConsultLocal']);
            $feeConsultLocal->setNewPlatformPercentage($data['feePlatformConsultLocal']);
            $feeConsultLocal->setTakeEffectOn(new \DateTime($data['feeConsultLocalDate']));
            if(strtotime($feeConsultLocal->getTakeEffectOn()->format("Y-m-d")) <= strtotime(date("Y-m-d"))) {
                $feeConsultLocal->setAgentPercentage($feeConsultLocal->getNewAgentPercentage());
                $feeConsultLocal->setPlatformPercentage($feeConsultLocal->getNewPlatformPercentage());
            }
            $em->persist($feeConsultLocal);

            if (!($feeConsultOversea instanceof AgentPrimaryCustomFee)) {
                $feeConsultOverseaAgent = $feeConsultOversea->getAgentPercentage();
                $feeConsultOverseaPlatform = $feeConsultOversea->getPlatformPercentage();

                $feeConsultOversea = new AgentPrimaryCustomFee();
                $feeConsultOversea->setAreaType(Constant::AREA_TYPE_OVERSEA);
                $feeConsultOversea->setMarginShareType(Constant::MST_LIVE_CONSULT);
                $feeConsultOversea->setAgentPercentage($feeConsultOverseaAgent);
                $feeConsultOversea->setPlatformPercentage($feeConsultOverseaPlatform);
                $feeConsultOversea->setAgent($agent);
                $agent->addPrimaryFee($feeConsultOversea);
            }
            $feeConsultOversea->setNewAgentPercentage($data['feeConsultOversea']);
            $feeConsultOversea->setNewPlatformPercentage($data['feePlatformConsultOversea']);
            $feeConsultOversea->setTakeEffectOn(new \DateTime($data['feeConsultOverseaDate']));
            if(strtotime($feeConsultOversea->getTakeEffectOn()->format("Y-m-d")) <= strtotime(date("Y-m-d"))) {
                $feeConsultOversea->setAgentPercentage($feeConsultOversea->getNewAgentPercentage());
                $feeConsultOversea->setPlatformPercentage($feeConsultOversea->getNewPlatformPercentage());
            }
            $em->persist($feeConsultOversea);

            // Minimum primary fee
            $repoAgentMin = $em->getRepository('UtilBundle:AgentCustomMedicineFee');
            $minFeePrimaryLocal = $repoAgentMin->findOneOrCreate(array(
                'agent' => $agent,
                'feeName' => Constant::AGENT_MINUMUM_FEE_NAME_lOCAL,
                'feeCode' => Constant::AGENT_MINUMUM_FEE_PRIMARY_lOCAL,
            ));
            $minFeePrimaryLocal->setFeeValue($data['minAgentFeePrimaryLocal']);
            $em->persist($minFeePrimaryLocal);

            $minFeePrimaryIndo = $repoAgentMin->findOneOrCreate(array(
                'agent' => $agent,
                'feeName' => Constant::AGENT_MINUMUM_FEE_NAME_INDONESIA,
                'feeCode' => Constant::AGENT_MINUMUM_FEE_PRIMARY_INDONESIA,
            ));
            $minFeePrimaryIndo->setFeeValue($data['minAgentFeePrimaryIndo']);
            $em->persist($minFeePrimaryIndo);

            $minFeePrimaryEastMalay = $repoAgentMin->findOneOrCreate(array(
                'agent' => $agent,
                'feeName' => Constant::AGENT_MINUMUM_FEE_NAME_EAST_MALAY,
                'feeCode' => Constant::AGENT_MINUMUM_FEE_PRIMARY_EAST_MALAY,
            ));
            $minFeePrimaryEastMalay->setFeeValue($data['minAgentFeePrimaryEastMalay']);
            $em->persist($minFeePrimaryEastMalay);

            $minFeePrimaryWestMalay = $repoAgentMin->findOneOrCreate(array(
                'agent' => $agent,
                'feeName' => Constant::AGENT_MINUMUM_FEE_NAME_WEST_MALAY,
                'feeCode' => Constant::AGENT_MINUMUM_FEE_PRIMARY_WEST_MALAY,
            ));
            $minFeePrimaryWestMalay->setFeeValue($data['minAgentFeePrimaryWestMalay']);
            $em->persist($minFeePrimaryWestMalay);

            $minFeePrimaryInternational = $repoAgentMin->findOneOrCreate(array(
                'agent' => $agent,
                'feeName' => Constant::AGENT_MINUMUM_FEE_NAME_INTERNATIONAL,
                'feeCode' => Constant::AGENT_MINUMUM_FEE_PRIMARY_INTERNATIONAL,
            ));
            $minFeePrimaryInternational->setFeeValue($data['minAgentFeePrimaryInternational']);
            $em->persist($minFeePrimaryInternational);

            if(isset($data['check3rd'])&& !empty($data['check3rd'])  ){
                if($is3rd){

                    $feeSetting = $fee3ardMedicineLocal->getFeeSetting();
                    $feeSetting->setNewFee(floatval($data['fee3rdLcMedicine']));
                    $feeSetting->setEffectDate(new \DateTime($data['date3rdLcMedicine']));
                    if($feeSetting->getEffectDate()->format("Y-m-d") == date("Y-m-d")){
                        $feeSetting->setFee($feeSetting->getNewFee());
                    }
                    $em->persist($fee3ardMedicineLocal);

                    $feeSetting = $fee3ardMedicineOversea->getFeeSetting();
                    $feeSetting->setNewFee(floatval($data['fee3rdOsMedicine']));
                    $feeSetting->setEffectDate(new \DateTime($data['date3rdOsMedicine']));
                    if($feeSetting->getEffectDate()->format("Y-m-d") == date("Y-m-d")){
                        $feeSetting->setFee($feeSetting->getNewFee());
                    }
                    $em->persist($fee3ardMedicineOversea);

                    $feeSetting = $fee3ardDescriptionLocal->getFeeSetting();
                    $feeSetting->setNewFee(floatval($data['fee3rdLcPrescription']));
                    $feeSetting->setEffectDate(new \DateTime($data['date3rdLcPrescription']));
                    if($feeSetting->getEffectDate()->format("Y-m-d") == date("Y-m-d")){
                        $feeSetting->setFee($feeSetting->getNewFee());
                    }
                    $em->persist($fee3ardDescriptionLocal);


                    $feeSetting = $fee3ardDescriptionOversea->getFeeSetting();
                    $feeSetting->setNewFee(floatval($data['fee3rdOsPrescription']));
                    $feeSetting->setEffectDate(new \DateTime($data['date3rdOsPrescription']));
                    if($feeSetting->getEffectDate()->format("Y-m-d") == date("Y-m-d")){
                        $feeSetting->setFee($feeSetting->getNewFee());
                    }
                    $em->persist($fee3ardDescriptionOversea);

                    $feeSetting = $fee3ardConsultLocal->getFeeSetting();
                    $feeSetting->setNewFee(floatval($data['fee3rdLcConsult']));
                    $feeSetting->setEffectDate(new \DateTime($data['date3rdLcConsult']));
                    if($feeSetting->getEffectDate()->format("Y-m-d") == date("Y-m-d")){
                        $feeSetting->setFee($feeSetting->getNewFee());
                    }
                    $em->persist($fee3ardConsultLocal);

                    $feeSetting = $fee3ardConsultOversea->getFeeSetting();
                    $feeSetting->setNewFee(floatval($data['fee3rdOsConsult']));
                    $feeSetting->setEffectDate(new \DateTime($data['date3rdOsConsult']));
                    if($feeSetting->getEffectDate()->format("Y-m-d") == date("Y-m-d")){
                        $feeSetting->setFee($feeSetting->getNewFee());
                    }
                    $em->persist($fee3ardConsultOversea);
                }
                else {

                    $agent->setIs3paAgent($data['check3rd']);
                    $fee3ardMedicineLc = new Agent3paFee();
                    $fee3ardMedicineLc->setArea(Constant::GMS_TYPE_3RD_AGENT_LOCAL);
                    $fee3ardMedicineLc->setFeeType(Constant::GMS_FEE_3RD_AGENT_MEDICINE);
                    $feeSetting = new FeeSetting();
                    $feeSetting->setFee($fee3ardMedicineLocal->getFeeSetting()->getFee());
                    $feeSetting->setNewFee(floatval($data['fee3rdLcMedicine']));
                    $feeSetting->setEffectDate(new \DateTime($data['date3rdLcMedicine']));
                    if($feeSetting->getEffectDate()->format("Y-m-d") == date("Y-m-d")){
                        $feeSetting->setFee($feeSetting->getNewFee());
                    }

                    $fee3ardMedicineLc->setFeeSetting($feeSetting);
                    $agent->addThirdPartyFee($fee3ardMedicineLc);

                    $fee3ardMedicineOs = new Agent3paFee();
                    $fee3ardMedicineOs->setArea(Constant::GMS_TYPE_3RD_AGENT_OVERSEAS);
                    $fee3ardMedicineOs->setFeeType(Constant::GMS_FEE_3RD_AGENT_MEDICINE);
                    $feeSetting = new FeeSetting();
                    $feeSetting->setFee($fee3ardMedicineOversea->getFeeSetting()->getFee());
                    $feeSetting->setNewFee(floatval($data['fee3rdOsMedicine']));
                    $feeSetting->setEffectDate(new \DateTime($data['date3rdOsMedicine']));
                    if($feeSetting->getEffectDate()->format("Y-m-d") == date("Y-m-d")){
                        $feeSetting->setFee($feeSetting->getNewFee());
                    }
                    $fee3ardMedicineOs->setFeeSetting($feeSetting);
                    $agent->addThirdPartyFee($fee3ardMedicineOs);

                    $fee3ardDescriptionLc = new Agent3paFee();
                    $fee3ardDescriptionLc->setArea(Constant::GMS_TYPE_3RD_AGENT_LOCAL);
                    $fee3ardDescriptionLc->setFeeType(Constant::GMS_FEE_3RD_AGENT_PRESCRIPTION);
                    $feeSetting = new FeeSetting();
                    $feeSetting->setFee($fee3ardDescriptionLocal->getFeeSetting()->getFee());
                    $feeSetting->setNewFee(floatval($data['fee3rdLcPrescription']));
                    $feeSetting->setEffectDate(new \DateTime($data['date3rdLcPrescription']));
                    if($feeSetting->getEffectDate()->format("Y-m-d") == date("Y-m-d")){
                        $feeSetting->setFee($feeSetting->getNewFee());
                    }
                    $fee3ardDescriptionLc->setFeeSetting($feeSetting);
                    $agent->addThirdPartyFee($fee3ardDescriptionLc);

                    $fee3ardDescriptionOs = new Agent3paFee();
                    $fee3ardDescriptionOs->setArea(Constant::GMS_TYPE_3RD_AGENT_OVERSEAS);
                    $fee3ardDescriptionOs->setFeeType(Constant::GMS_FEE_3RD_AGENT_PRESCRIPTION);
                    $feeSetting = new FeeSetting();
                    $feeSetting->setFee($fee3ardDescriptionOversea->getFeeSetting()->getFee());
                    $feeSetting->setNewFee(floatval($data['fee3rdOsPrescription']));
                    $feeSetting->setEffectDate(new \DateTime($data['date3rdOsPrescription']));
                    if($feeSetting->getEffectDate()->format("Y-m-d") == date("Y-m-d")){
                        $feeSetting->setFee($feeSetting->getNewFee());
                    }
                    $fee3ardDescriptionOs->setFeeSetting($feeSetting);
                    $agent->addThirdPartyFee($fee3ardDescriptionOs);

                    $fee3ardConsultLc = new Agent3paFee();
                    $fee3ardConsultLc->setArea(Constant::GMS_TYPE_3RD_AGENT_LOCAL);
                    $fee3ardConsultLc->setFeeType(Constant::GMS_FEE_3RD_AGENT_LIVECONSULT);
                    $feeSetting = new FeeSetting();
                    $feeSetting->setFee($fee3ardConsultLocal->getFeeSetting()->getFee());
                    $feeSetting->setNewFee(floatval($data['fee3rdLcConsult']));
                    $feeSetting->setEffectDate(new \DateTime($data['date3rdLcConsult']));
                    if($feeSetting->getEffectDate()->format("Y-m-d") == date("Y-m-d")){
                        $feeSetting->setFee($feeSetting->getNewFee());
                    }
                    $fee3ardConsultLc->setFeeSetting($feeSetting);
                    $agent->addThirdPartyFee($fee3ardConsultLc);

                    $fee3ardConsultOs = new Agent3paFee();
                    $fee3ardConsultOs->setArea(Constant::GMS_TYPE_3RD_AGENT_OVERSEAS);
                    $fee3ardConsultOs->setFeeType(Constant::GMS_FEE_3RD_AGENT_LIVECONSULT);
                    $feeSetting = new FeeSetting();
                    $feeSetting->setFee($fee3ardConsultOversea->getFeeSetting()->getFee());
                    $feeSetting->setNewFee(floatval($data['fee3rdOsConsult']));
                    $feeSetting->setEffectDate(new \DateTime($data['date3rdOsConsult']));
                    if($feeSetting->getEffectDate()->format("Y-m-d") == date("Y-m-d")){
                        $feeSetting->setFee($feeSetting->getNewFee());
                    }
                    $fee3ardConsultOs->setFeeSetting($feeSetting);
                    $agent->addThirdPartyFee($fee3ardConsultOs);
                }

                // Secondary minimum fee
                $minFee3paLocal = $repoAgentMin->findOneOrCreate(array(
                    'agent' => $agent,
                    'feeName' => Constant::AGENT_MINUMUM_FEE_NAME_lOCAL,
                    'feeCode' => Constant::AGENT_MINUMUM_FEE_SECONDARY_lOCAL,
                ));
                $minFee3paLocal->setFeeValue($data['minAgentFeeSecondaryLocal']);
                $em->persist($minFee3paLocal);

                $minFee3paIndo = $repoAgentMin->findOneOrCreate(array(
                    'agent' => $agent,
                    'feeName' => Constant::AGENT_MINUMUM_FEE_NAME_INDONESIA,
                    'feeCode' => Constant::AGENT_MINUMUM_FEE_SECONDARY_INDONESIA,
                ));
                $minFee3paIndo->setFeeValue($data['minAgentFeeSecondaryIndo']);
                $em->persist($minFee3paIndo);

                $minFee3paEastMalay = $repoAgentMin->findOneOrCreate(array(
                    'agent' => $agent,
                    'feeName' => Constant::AGENT_MINUMUM_FEE_NAME_EAST_MALAY,
                    'feeCode' => Constant::AGENT_MINUMUM_FEE_SECONDARY_EAST_MALAY,
                ));
                $minFee3paEastMalay->setFeeValue($data['minAgentFeeSecondaryEastMalay']);
                $em->persist($minFee3paEastMalay);

                $minFee3paWestMalay = $repoAgentMin->findOneOrCreate(array(
                    'agent' => $agent,
                    'feeName' => Constant::AGENT_MINUMUM_FEE_NAME_WEST_MALAY,
                    'feeCode' => Constant::AGENT_MINUMUM_FEE_SECONDARY_WEST_MALAY,
                ));
                $minFee3paWestMalay->setFeeValue($data['minAgentFeeSecondaryWestMalay']);
                $em->persist($minFee3paWestMalay);

                $minFee3paInternational = $repoAgentMin->findOneOrCreate(array(
                    'agent' => $agent,
                    'feeName' => Constant::AGENT_MINUMUM_FEE_NAME_INTERNATIONAL,
                    'feeCode' => Constant::AGENT_MINUMUM_FEE_SECONDARY_INTERNATIONAL,
                ));
                $minFee3paInternational->setFeeValue($data['minAgentFeeSecondaryInternational']);
                $em->persist($minFee3paInternational);
            } else {
                $agent->setIs3paAgent(false);
                $listFees = $agent->getThirdPartyFees();
                foreach ($listFees as $fee){
                    $fee->setDeletedOn(new \DateTime('now'));
                }
            }


            $subAgents = $agent->getActiveChild();
            foreach ($subAgents as $sub){
                $sub->setIs3paAgent($agent->getIs3paAgent());
            }
            $personalInfo =  $agent->getPersonalInformation();
            $personalInfo->setFirstName($data['firstName']);
            $personalInfo->setLastName($data['lastName']);
            $personalInfo->setGender($data['gender']);
            $personalInfo->setPassportNo($data['localIdPassport']); 

            $iden = $agent->getIdentifications()->first();
            $iden->setIdentityNumber($data['localIdPassport']);
            $iden->setIssuingCountryId($data['localIdPassportCountry']);    
            
            $agentCompany = $em->getRepository('UtilBundle:AgentCompany')->findOneBy(array('agent' => $id));

            $agentCompany->setCompanyName($data['comName']);
            $agentCompany->setCompanyRegistrationNumber($data['registerNo']);
            $agentCompany->setUpdatedOn(new \DateTime());

            $companyPhone = $agentCompany->getPhone();
            $companyPhone->setNumber($data['comPhone']);

            $comCountry = $em->getRepository('UtilBundle:Country')->find($data['comPhoneLocation']);
            $companyPhone->setCountry($comCountry);

            $agentCompany->setPhone($companyPhone);

            $companyAddress = $agentCompany->getAddress();
            $companyAddress->setLine1($data['comAddressLine1']);
            $companyAddress->setLine2($data['comAddressLine2']);
            $companyAddress->setLine3($data['comAddressLine3']);
            $companyAddress->setPostalCode($data['comZipCode']);
            $companyAddress->setUpdatedOn(new \DateTime());

            $city = $em->getRepository('UtilBundle:City')->find($data['comCity']);
            $companyAddress->setCity($city);
            $agentCompany->setAddress($companyAddress);

            // $site = $em->getRepository('UtilBundle:Site')->find($data['site']);
            // $agent->setSite($site);
            
            if(isset($data['checkAddress'])){
                $add = $agent->getAdresses()->first();
                $add->setCity($em->getRepository('UtilBundle:City')->find($data['comCity']));
                $add->setPostalCode($data['comZipCode']);
                $add->setLine1($data['comAddressLine1']);
                $add->setLine2($data['comAddressLine2']);
                $add->setLine3($data['comAddressLine3']);
                $agent->setIsUseCompanyAddress(true);
               
            } else { 
                $add = $agent->getAdresses()->first();
                $add->setCity($em->getRepository('UtilBundle:City')->find($data['city']));
                $add->setPostalCode($data['zipCode']);
                $add->setLine1($data['addressLine1']);
                $add->setLine2($data['addressLine2']);
                $add->setLine3($data['addressLine3']);
                $agent->setIsUseCompanyAddress(false);

              
            }

            $phone = $agent->getPhones()->first();
            $country = $em->getRepository('UtilBundle:Country')->find($data['phoneLocation']);       
            $phone->setCountry($country);
            $phone->setPhoneType($em->getRepository('UtilBundle:PhoneType')->getPhone());
            $phone->setNumber($data['phone']);

            $bankAcc =  $agent->getBankAccount();
            // prepare old data for writting log
            if (!$bankAcc) {
                $bankAcc = new BankAccount();
            } else {
                $oldData = array(
                    'bankName'      => $bankAcc->getBank()->getName(),
                    'accountNumber' => $bankAcc->getAccountNumber()
                );
            }

            if ($data['bankCountryIssue'] == Constant::ID_SINGAPORE || $data['bankCountryIssue'] == Constant::ID_MALAYSIA) {
                $bank = $em->getRepository('UtilBundle:Bank')->find($data['bankName']);
            } else {
                $bank = $bankAcc->getBank();
                if (!$bank) {
                    $bank = new Bank();
                }
                $bank->setName($data['bankName']);
                $bank->setCountry($em->getRepository('UtilBundle:Country')->find($data['bankCountryIssue']));
                $bank->setSwiftCode($data['bankSwiftCode']);
            }

            $bankAcc->setAccountName($data['accountName']);
            $bankAcc->setAccountNumber($data['accountNumber']);
            $bankAcc->setBank($bank);
            $agent->setBankAccount($bankAcc);

            
            $em->persist($companyAddress);
            $em->persist($companyPhone);
            $em->persist($agentCompany);
            $em->persist($agent);
            $em->flush();
            $file = array();
            if(isset( $_FILES["admin_agent"]))
            {
                $file['name'] = $_FILES["admin_agent"]['name']['logo'];
                $file['type'] = $_FILES["admin_agent"]['type']['logo'];
                $file['tmp_name'] = $_FILES["admin_agent"]['tmp_name']['logo'];
                $file['error'] = $_FILES["admin_agent"]['error']['logo'];
                $file['size'] = $_FILES["admin_agent"]['size']['logo'];
                $profile = $this->uploadfile($file, 'agent/profile'.$agent->getId());

                if(!empty($profile)) {
                    $agent->setProfilePhotoUrl($profile);
                }
            }

            // Agent Fee Medicine
            $em->getRepository('UtilBundle:Agent')->updateAgentFeeMedicine($agent, $data['fees'], $this->getUser()->getLoggedUser()->getId());

            $em->persist($agent);
            $em->flush();


            if ($allowLogging == true) {
                $newData = array(
                    'bankName'      => $data['bankName'],
                    'accountNumber' => $data['accountNumber']
                );
                $author = $this->getUser()->getLoggedUser()->getFirstName() . ' ' .
                    $this->getUser()->getLoggedUser()->getLastName();
                $arr = array('module' => 'agents', 
                             'title'  =>'bank_information_updated',
                             'id'     => $agent->getId());
                Utils::saveLog($oldData, $newData, $author, $arr, $em);
            }



            return $this->redirectToRoute('admin_agent');    
        }
        
        $parameters = array(
            'form' => $form->createView(),
            'ajaxURL' => 'admin_doctor_create_ajax',
            'ajaxDependent' => 'admin_doctor_create_getdependent',          
            'successUrl' => 'admin_agent',
            'agentId'=> $id,
            'agent' => $agent,
            'is3pa' => boolval($agent->getIs3paAgent()),
            'agentCode' => $agent->getAgentCode(),
            'title' => 'Edit Agent',
            'listAgentFee' => $listAgentFee
        );
        
        return $this->render('AdminBundle:admin:agent-edit.html.twig',$parameters);
    }
    /**
     * @Route("/admin/agent-ajax", name="admin_agent_list_ajax")
     */
    public function agentAjaxAction(Request $request)
    {    
        $em = $this->getDoctrine()->getEntityManager();  
        $result = $em->getRepository('UtilBundle:Agent')->getAgentsAdmin($request->request);   
        
        return new JsonResponse($result);
    }  
    /**
     * @Route("/admin/agent/create", name="admin_agent_create")
    */
    public function agentCreateAction(Request $request)
    {
        
        $em = $this->getDoctrine()->getEntityManager();        
        $country = $em->getRepository('UtilBundle:Country')->getByPreferredCountry(true);
        $dependentData= array();
        $dependentData['country'] = $country;
        $repository = $this->getDoctrine()->getRepository(Agent3paFee::class);
        $repoFee = $em->getRepository('UtilBundle:PlatformShareFee');

        // medicine fee type =1

        $fee3ardMedicineLocal =  $repository->findOneBy(['area' => Constant::GMS_TYPE_3RD_AGENT_LOCAL, 'feeType' => Constant::GMS_FEE_3RD_AGENT_MEDICINE,'agent' => null]);
        $fee3ardMedicineOversea = $repository->findOneBy(['area' => Constant::GMS_TYPE_3RD_AGENT_OVERSEAS, 'feeType' => Constant::GMS_FEE_3RD_AGENT_MEDICINE,'agent' => null]);
        $fee3ardDescriptionLocal = $repository->findOneBy(['area' => Constant::GMS_TYPE_3RD_AGENT_LOCAL, 'feeType' => Constant::GMS_FEE_3RD_AGENT_PRESCRIPTION,'agent' => null]);
        $fee3ardDescriptionOversea = $repository->findOneBy(['area' => Constant::GMS_TYPE_3RD_AGENT_OVERSEAS, 'feeType' => Constant::GMS_FEE_3RD_AGENT_PRESCRIPTION,'agent' => null]);
        $fee3ardConsultLocal = $repository->findOneBy(['area' => Constant::GMS_TYPE_3RD_AGENT_LOCAL, 'feeType' => Constant::GMS_FEE_3RD_AGENT_LIVECONSULT,'agent' => null]);
        $fee3ardConsultOversea = $repository->findOneBy(['area' => Constant::GMS_TYPE_3RD_AGENT_OVERSEAS, 'feeType' => Constant::GMS_FEE_3RD_AGENT_LIVECONSULT,'agent' => null]);

        $listAgentFee = [];

        $listAgentFee['fee3ardMedicineLocalFee'] = -1;
        if(!empty($fee3ardMedicineLocal->getFeeSetting()->getEffectDate())&& strtotime($fee3ardMedicineLocal->getFeeSetting()->getEffectDate()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            $listAgentFee['fee3ardMedicineLocalFee'] = $fee3ardMedicineLocal->getFeeSetting()->getFee();
        } else {
            $fee3ardMedicineLocal->getFeeSetting()->setEffectDate(new \DateTime());
        }

        $listAgentFee['fee3ardMedicineOverseaFee'] = -1;
        if(!empty($fee3ardMedicineOversea->getFeeSetting()->getEffectDate())&& strtotime($fee3ardMedicineOversea->getFeeSetting()->getEffectDate()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            $listAgentFee['fee3ardMedicineOverseaFee'] = $fee3ardMedicineOversea->getFeeSetting()->getFee();
        } else {
            $fee3ardMedicineOversea->getFeeSetting()->setEffectDate(new \DateTime());
        }

        $listAgentFee['fee3ardDescriptionLocalFee'] = -1;
        if(!empty($fee3ardDescriptionLocal->getFeeSetting()->getEffectDate())&& strtotime($fee3ardDescriptionLocal->getFeeSetting()->getEffectDate()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            $listAgentFee['fee3ardDescriptionLocalFee'] = $fee3ardDescriptionLocal->getFeeSetting()->getFee();
        } else {
            $fee3ardDescriptionLocal->getFeeSetting()->setEffectDate(new \DateTime());
        }

        $listAgentFee['fee3ardDescriptionOverseaFee'] = -1;
        if(!empty($fee3ardDescriptionOversea->getFeeSetting()->getEffectDate())&& strtotime($fee3ardDescriptionOversea->getFeeSetting()->getEffectDate()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            $listAgentFee['fee3ardDescriptionOverseaFee'] = $fee3ardDescriptionOversea->getFeeSetting()->getFee();
        } else {
            $fee3ardDescriptionOversea->getFeeSetting()->setEffectDate(new \DateTime());
        }

        $listAgentFee['fee3ardConsultLocalFee'] = -1;
        if(!empty($fee3ardConsultLocal->getFeeSetting()->getEffectDate())&& strtotime($fee3ardConsultLocal->getFeeSetting()->getEffectDate()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            $listAgentFee['fee3ardConsultLocalFee'] = $fee3ardConsultLocal->getFeeSetting()->getFee();
        } else {
            $fee3ardConsultLocal->getFeeSetting()->setEffectDate(new \DateTime());
        }

        $listAgentFee['fee3ardConsultOverseaFee'] = -1;
        if(!empty($fee3ardConsultOversea->getFeeSetting()->getEffectDate())&& strtotime($fee3ardConsultOversea->getFeeSetting()->getEffectDate()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            $listAgentFee['fee3ardConsultOverseaFee'] = $fee3ardConsultOversea->getFeeSetting()->getFee();
        } else {
            $fee3ardConsultOversea->getFeeSetting()->setEffectDate(new \DateTime());
        }

        $listAgentFee['feeServiceLocalAgent'] = -1;
        $listAgentFee['feeServiceLocalPlatform'] = -1;
        $feeServiceLocal = $repoFee->findOneBy(['areaType' => Constant::AREA_TYPE_LOCAL, 'marginShareType' => Constant::MST_SERVICE]);
        if(!empty($feeServiceLocal->getTakeEffectOn())
            && strtotime($feeServiceLocal->getTakeEffectOn()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            if($feeServiceLocal->getAgentPercentage() != $feeServiceLocal->getNewAgentPercentage()) {
                $listAgentFee['feeServiceLocalAgent'] = $feeServiceLocal->getAgentPercentage();
            }
            if($feeServiceLocal->getPlatformPercentage() != $feeServiceLocal->getNewPlatformPercentage()) {
                $listAgentFee['feeServiceLocalPlatform'] = $feeServiceLocal->getPlatformPercentage();
            }
        } else {
            $feeServiceLocal->setTakeEffectOn(new \DateTime());
        }

        $listAgentFee['feeServiceOverseaAgent'] = -1;
        $listAgentFee['feeServiceOverseaPlatform'] = -1;
        $feeServiceOversea = $repoFee->findOneBy(['areaType' => Constant::AREA_TYPE_OVERSEA, 'marginShareType' => Constant::MST_SERVICE]);
        if(!empty($feeServiceOversea->getTakeEffectOn())
            && strtotime($feeServiceOversea->getTakeEffectOn()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            if($feeServiceOversea->getAgentPercentage() != $feeServiceOversea->getNewAgentPercentage()) {
                $listAgentFee['feeServiceOverseaAgent'] = $feeServiceOversea->getAgentPercentage();
            }
            if($feeServiceOversea->getPlatformPercentage() != $feeServiceOversea->getNewPlatformPercentage()) {
                $listAgentFee['feeServiceOverseaPlatform'] = $feeServiceOversea->getPlatformPercentage();
            }
        } else {
            $feeServiceOversea->setTakeEffectOn(new \DateTime());
        }

        $listAgentFee['feeMedicineLocalAgent'] = -1;
        $listAgentFee['feeMedicineLocalPlatform'] = -1;
        $feeMedicineLocal = $repoFee->findOneBy(['areaType' => Constant::AREA_TYPE_LOCAL, 'marginShareType' => Constant::MST_MEDICINE]);
        if(!empty($feeMedicineLocal->getTakeEffectOn())
            && strtotime($feeMedicineLocal->getTakeEffectOn()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            if($feeMedicineLocal->getAgentPercentage() != $feeMedicineLocal->getNewAgentPercentage()) {
                $listAgentFee['feeMedicineLocalAgent'] = $feeMedicineLocal->getAgentPercentage();
            }
            if($feeMedicineLocal->getPlatformPercentage() != $feeMedicineLocal->getNewPlatformPercentage()) {
                $listAgentFee['feeMedicineLocalPlatform'] = $feeMedicineLocal->getPlatformPercentage();
            }
        } else {
            $feeMedicineLocal->setTakeEffectOn(new \DateTime());
        }

        $listAgentFee['feeMedicineOverseaAgent'] = -1;
        $listAgentFee['feeMedicineOverseaPlatform'] = -1;
        $feeMedicineOversea = $repoFee->findOneBy(['areaType' => Constant::AREA_TYPE_OVERSEA, 'marginShareType' => Constant::MST_MEDICINE]);
        if(!empty($feeMedicineOversea->getTakeEffectOn())
            && strtotime($feeMedicineOversea->getTakeEffectOn()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            if($feeMedicineOversea->getAgentPercentage() != $feeMedicineOversea->getNewAgentPercentage()) {
                $listAgentFee['feeMedicineOverseaAgent'] = $feeMedicineOversea->getAgentPercentage();
            }
            if($feeMedicineOversea->getPlatformPercentage() != $feeMedicineOversea->getNewPlatformPercentage()) {
                $listAgentFee['feeMedicineOverseaPlatform'] = $feeMedicineOversea->getPlatformPercentage();
            }
        } else {
            $feeMedicineOversea->setTakeEffectOn(new \DateTime());
        }

        $listAgentFee['feeConsultLocalAgent'] = -1;
        $listAgentFee['feeConsultLocalPlatform'] = -1;
        $feeConsultLocal = $repoFee->findOneBy(['areaType' => Constant::AREA_TYPE_LOCAL, 'marginShareType' => Constant::MST_LIVE_CONSULT]);
        if(!empty($feeConsultLocal->getTakeEffectOn())
            && strtotime($feeConsultLocal->getTakeEffectOn()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            if($feeConsultLocal->getAgentPercentage() != $feeConsultLocal->getNewAgentPercentage()) {
                $listAgentFee['feeConsultLocalAgent'] = $feeConsultLocal->getAgentPercentage();
            }
            if($feeConsultLocal->getPlatformPercentage() != $feeConsultLocal->getNewPlatformPercentage()) {
                $listAgentFee['feeConsultLocalPlatform'] = $feeConsultLocal->getPlatformPercentage();
            }
        } else {
            $feeConsultLocal->setTakeEffectOn(new \DateTime());
        }

        $listAgentFee['feeConsultOverseaAgent'] = -1;
        $listAgentFee['feeConsultOverseaPlatform'] = -1;
        $feeConsultOversea = $repoFee->findOneBy(['areaType' => Constant::AREA_TYPE_OVERSEA, 'marginShareType' => Constant::MST_LIVE_CONSULT]);
        if(!empty($feeConsultOversea->getTakeEffectOn())
            && strtotime($feeConsultOversea->getTakeEffectOn()->format("Y-m-d")) > strtotime(date("Y-m-d"))) {
            if($feeConsultOversea->getAgentPercentage() != $feeConsultOversea->getNewAgentPercentage()) {
                $listAgentFee['feeConsultOverseaAgent'] = $feeConsultOversea->getAgentPercentage();
            }
            if($feeConsultOversea->getPlatformPercentage() != $feeConsultOversea->getNewPlatformPercentage()) {
                $listAgentFee['feeConsultOverseaPlatform'] = $feeConsultOversea->getPlatformPercentage();
            }
        } else {
            $feeConsultOversea->setTakeEffectOn(new \DateTime());
        }

        $minAgentFees = $em->getRepository('UtilBundle:AgentMininumFeeSetting')->findAll();
        $minAgentFeePrimary = array(
            'local'=> '',
            'feeIndo'=> '',
            'feeEastMalay'=> '',
            'feeWestMalay'=> '',
            'feeInternational'=> '',
        );
        $minAgentFee3pa = array(
            'local'=> '',
            'feeIndo'=> '',
            'feeEastMalay'=> '',
            'feeWestMalay'=> '',
            'feeInternational'=> '',
        );
        foreach ($minAgentFees as $fee) {
           if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_PRIMARY_lOCAL){
                $minAgentFeePrimary['local'] =  $fee->getFeeValue();
            }
            if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_PRIMARY_INDONESIA){
                $minAgentFeePrimary['feeIndo'] =  $fee->getFeeValue();
            }
            if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_PRIMARY_EAST_MALAY){
                $minAgentFeePrimary['feeEastMalay'] =  $fee->getFeeValue();
            } if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_PRIMARY_WEST_MALAY){
                $minAgentFeePrimary['feeWestMalay'] =  $fee->getFeeValue();
            }
            if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_PRIMARY_INTERNATIONAL){
                $minAgentFeePrimary['feeInternational'] =  $fee->getFeeValue();
            }
            if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_SECONDARY_lOCAL){
                $minAgentFee3pa['local'] =  $fee->getFeeValue();
            }
            if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_SECONDARY_INDONESIA){
                $minAgentFee3pa['feeIndo'] =  $fee->getFeeValue();
            }
            if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_SECONDARY_EAST_MALAY){
                $minAgentFee3pa['feeEastMalay'] =  $fee->getFeeValue();
            }
            if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_SECONDARY_WEST_MALAY){
                $minAgentFee3pa['feeWestMalay'] =  $fee->getFeeValue();
            }
            if ($fee->getFeeCode() == Constant::AGENT_MINUMUM_FEE_SECONDARY_INTERNATIONAL){
                $minAgentFee3pa['feeInternational'] =  $fee->getFeeValue();
            }
        }

        $agent = new Agent();
        $agentFeeMedicine = [];
        // $agentFeeMedicinef = $em->getRepository('UtilBundle:Agent')->getAgentFeeMedicine($agent);
        // foreach ($agentFeeMedicinef as &$amf) {
        //     if(strtotime($amf['takeEffectOn']->format('d M y'))>= strtotime(date("d M y"))) {
        //         $amf['takeEffectOn'] = $amf['takeEffectOn']->format('d M y');
        //     } else {
        //         $amf['takeEffectOn'] = '';
        //     }
        //     $agentFeeMedicine[] = $amf;
        // }
        $dependentData['agentFeeMedicine'] = $agentFeeMedicine;
        $form = $this->createForm(new AgentAdminType(
            array(
                'agent' => $agent,
                'fee' => array(
                    'medLc' => $fee3ardMedicineLocal,
                    'medOs' => $fee3ardMedicineOversea,
                    'desLc' => $fee3ardDescriptionLocal,
                    'desOs' => $fee3ardDescriptionOversea,
                    'conLc' => $fee3ardConsultLocal,
                    'conOs' => $fee3ardConsultOversea,
                    'priDesLc' => $feeServiceLocal,
                    'priDesOs' => $feeServiceOversea,
                    'priMedLc' => $feeMedicineLocal,
                    'priMedOs' => $feeMedicineOversea,
                    'priConLc' => $feeConsultLocal,
                    'priConOs' => $feeConsultOversea,
                    'minPri'   => $minAgentFeePrimary,
                    'min3pa'   => $minAgentFee3pa,
                ),
                'depend' => $dependentData,
                'entity_manager' => $this->get('doctrine.orm.entity_manager')
            )
        ), array(), array());
        $eror = array();
        if($request->getMethod() == 'POST') {

            $form->handleRequest($request);   
            $data = $request->request->get('admin_agent');
            $data = Common::removeSpaceOf($data);

            $agent->setIsGst($data['gstSetting']);
            if ($data['gstSetting'] == 1) {
                $agent->setGstNo($data['gstNo']);
                $agent->setGstEffectDate( new \DateTime(date('Y-m-d', strtotime($data['gstEffectDate']))) );
            }
            $site = $em->getRepository('UtilBundle:Site')->find($data['site']);
            $agent->setSite($site);
            // if(isset($data['checkMinFee'])&& !empty($data['checkMinFee']) ){
                $agent->setIsMinimunFeeEnabled(true);
            // } else {
            //     $agent->setIsMinimunFeeEnabled(false);
            // }
            // Setup primary fee
            $feeServiceLc = new AgentPrimaryCustomFee();
            $feeServiceLc->setAreaType(Constant::AREA_TYPE_LOCAL);
            $feeServiceLc->setMarginShareType(Constant::MST_SERVICE);
            $feeServiceLc->setAgentPercentage($feeServiceLocal->getAgentPercentage());
            $feeServiceLc->setNewAgentPercentage($data['feeServiceLocal']);
            $feeServiceLc->setPlatformPercentage($feeServiceLocal->getPlatformPercentage());
            $feeServiceLc->setNewPlatformPercentage($data['feePlatformServiceLocal']);
            $feeServiceLc->setTakeEffectOn(new \DateTime($data['feeServiceLocalDate']));
            if(strtotime($feeServiceLc->getTakeEffectOn()->format("Y-m-d")) <= strtotime(date("Y-m-d"))) {
                $feeServiceLc->setAgentPercentage($feeServiceLc->getAgentPercentage());
                $feeServiceLc->setPlatformPercentage($feeServiceLc->getPlatformPercentage());
            }
            $feeServiceLc->setAgent($agent);
            $agent->addPrimaryFee($feeServiceLc);

            $feeServiceOs = new AgentPrimaryCustomFee();
            $feeServiceOs->setAreaType(Constant::AREA_TYPE_OVERSEA);
            $feeServiceOs->setMarginShareType(Constant::MST_SERVICE);
            $feeServiceOs->setAgentPercentage($feeServiceOversea->getAgentPercentage());
            $feeServiceOs->setNewAgentPercentage($data['feeServiceOversea']);
            $feeServiceOs->setPlatformPercentage($feeServiceOversea->getPlatformPercentage());
            $feeServiceOs->setNewPlatformPercentage($data['feePlatformServiceOversea']);
            $feeServiceOs->setTakeEffectOn(new \DateTime($data['feeServiceOverseaDate']));
            if(strtotime($feeServiceOs->getTakeEffectOn()->format("Y-m-d")) <= strtotime(date("Y-m-d"))) {
                $feeServiceOs->setAgentPercentage($feeServiceOs->getAgentPercentage());
                $feeServiceOs->setPlatformPercentage($feeServiceOs->getPlatformPercentage());
            }
            $feeServiceOs->setAgent($agent);
            $agent->addPrimaryFee($feeServiceOs);

            $feeMedicineLc = new AgentPrimaryCustomFee();
            $feeMedicineLc->setAreaType(Constant::AREA_TYPE_LOCAL);
            $feeMedicineLc->setMarginShareType(Constant::MST_MEDICINE);
            $feeMedicineLc->setAgentPercentage($feeMedicineLocal->getAgentPercentage());
            $feeMedicineLc->setNewAgentPercentage($data['feeMedicineLocal']);
            $feeMedicineLc->setPlatformPercentage(0);
            $feeMedicineLc->setNewPlatformPercentage(0);
            $feeMedicineLc->setTakeEffectOn(new \DateTime($data['feeMedicineLocalDate']));
            if(strtotime($feeMedicineLc->getTakeEffectOn()->format("Y-m-d")) <= strtotime(date("Y-m-d"))) {
                $feeMedicineLc->setAgentPercentage($feeMedicineLc->getAgentPercentage());
            }
            $feeMedicineLc->setAgent($agent);
            $agent->addPrimaryFee($feeMedicineLc);

            $feeMedicineOs = new AgentPrimaryCustomFee();
            $feeMedicineOs->setAreaType(Constant::AREA_TYPE_OVERSEA);
            $feeMedicineOs->setMarginShareType(Constant::MST_MEDICINE);
            $feeMedicineOs->setAgentPercentage($feeMedicineOversea->getAgentPercentage());
            $feeMedicineOs->setNewAgentPercentage($data['feeMedicineOversea']);
            $feeMedicineOs->setPlatformPercentage(0);
            $feeMedicineOs->setNewPlatformPercentage(0);
            $feeMedicineOs->setTakeEffectOn(new \DateTime($data['feeMedicineOverseaDate']));
            if(strtotime($feeMedicineOs->getTakeEffectOn()->format("Y-m-d")) <= strtotime(date("Y-m-d"))) {
                $feeMedicineOs->setAgentPercentage($feeMedicineOs->getAgentPercentage());
            }
            $feeMedicineOs->setAgent($agent);
            $agent->addPrimaryFee($feeMedicineOs);

            $feeConsultLc = new AgentPrimaryCustomFee();
            $feeConsultLc->setAreaType(Constant::AREA_TYPE_LOCAL);
            $feeConsultLc->setMarginShareType(Constant::MST_LIVE_CONSULT);
            $feeConsultLc->setAgentPercentage($feeConsultLocal->getAgentPercentage());
            $feeConsultLc->setNewAgentPercentage($data['feeConsultLocal']);
            $feeConsultLc->setPlatformPercentage($feeConsultLocal->getPlatformPercentage());
            $feeConsultLc->setNewPlatformPercentage($data['feePlatformConsultLocal']);
            $feeConsultLc->setTakeEffectOn(new \DateTime($data['feeConsultLocalDate']));
            if(strtotime($feeConsultLc->getTakeEffectOn()->format("Y-m-d")) <= strtotime(date("Y-m-d"))) {
                $feeConsultLc->setAgentPercentage($feeConsultLc->getAgentPercentage());
                $feeConsultLc->setPlatformPercentage($feeConsultLc->getPlatformPercentage());
            }
            $feeConsultLc->setAgent($agent);
            $agent->addPrimaryFee($feeConsultLc);

            $feeConsultOs = new AgentPrimaryCustomFee();
            $feeConsultOs->setAreaType(Constant::AREA_TYPE_OVERSEA);
            $feeConsultOs->setMarginShareType(Constant::MST_LIVE_CONSULT);
            $feeConsultOs->setAgentPercentage($feeConsultOversea->getAgentPercentage());
            $feeConsultOs->setNewAgentPercentage($data['feeConsultOversea']);
            $feeConsultOs->setPlatformPercentage($feeConsultOversea->getPlatformPercentage());
            $feeConsultOs->setNewPlatformPercentage($data['feePlatformConsultOversea']);
            $feeConsultOs->setTakeEffectOn(new \DateTime($data['feeConsultOverseaDate']));
            if(strtotime($feeConsultOs->getTakeEffectOn()->format("Y-m-d")) <= strtotime(date("Y-m-d"))) {
                $feeConsultOs->setAgentPercentage($feeConsultOs->getAgentPercentage());
                $feeConsultOs->setPlatformPercentage($feeConsultOs->getPlatformPercentage());
            }
            $feeConsultOs->setAgent($agent);
            $agent->addPrimaryFee($feeConsultOs);

            // Minimum primary fee
            $minFeePrimaryLocal =  new AgentCustomMedicineFee();
            $minFeePrimaryLocal->setFeeValue($data['minAgentFeePrimaryLocal']);
            $minFeePrimaryLocal->setFeeName(Constant::AGENT_MINUMUM_FEE_NAME_lOCAL);
            $minFeePrimaryLocal->setFeeCode(Constant::AGENT_MINUMUM_FEE_PRIMARY_lOCAL);
            $minFeePrimaryLocal->setAgent($agent);
            $agent->addMinMedicineFee($minFeePrimaryLocal);

            $minFeePrimaryIndo =  new AgentCustomMedicineFee();
            $minFeePrimaryIndo->setFeeValue($data['minAgentFeePrimaryIndo']);
            $minFeePrimaryIndo->setFeeName(Constant::AGENT_MINUMUM_FEE_NAME_INDONESIA);
            $minFeePrimaryIndo->setFeeCode(Constant::AGENT_MINUMUM_FEE_PRIMARY_INDONESIA);
            $minFeePrimaryIndo->setAgent($agent);
            $agent->addMinMedicineFee($minFeePrimaryIndo);

            $minFeePrimaryEastMalay =  new AgentCustomMedicineFee();
            $minFeePrimaryEastMalay->setFeeValue($data['minAgentFeePrimaryEastMalay']);
            $minFeePrimaryEastMalay->setFeeName(Constant::AGENT_MINUMUM_FEE_NAME_EAST_MALAY);
            $minFeePrimaryEastMalay->setFeeCode(Constant::AGENT_MINUMUM_FEE_PRIMARY_EAST_MALAY);
            $minFeePrimaryEastMalay->setAgent($agent);
            $agent->addMinMedicineFee($minFeePrimaryEastMalay);

            $minFeePrimaryWestMalay =  new AgentCustomMedicineFee();
            $minFeePrimaryWestMalay->setFeeValue($data['minAgentFeePrimaryWestMalay']);
            $minFeePrimaryWestMalay->setFeeName(Constant::AGENT_MINUMUM_FEE_NAME_WEST_MALAY);
            $minFeePrimaryWestMalay->setFeeCode(Constant::AGENT_MINUMUM_FEE_PRIMARY_WEST_MALAY);
            $minFeePrimaryWestMalay->setAgent($agent);
            $agent->addMinMedicineFee($minFeePrimaryWestMalay);

            $minFeePrimaryInternational =  new AgentCustomMedicineFee();
            $minFeePrimaryInternational->setFeeValue($data['minAgentFeePrimaryInternational']);
            $minFeePrimaryInternational->setFeeName(Constant::AGENT_MINUMUM_FEE_NAME_INTERNATIONAL);
            $minFeePrimaryInternational->setFeeCode(Constant::AGENT_MINUMUM_FEE_PRIMARY_INTERNATIONAL);
            $minFeePrimaryInternational->setAgent($agent);
            $agent->addMinMedicineFee($minFeePrimaryInternational);

            if(isset($data['check3rd'])&& !empty($data['check3rd']) ){
                $agent->setIs3paAgent($data['check3rd']);
                $fee3ardMedicineLc = new Agent3paFee();
                $fee3ardMedicineLc->setArea(Constant::GMS_TYPE_3RD_AGENT_LOCAL);
                $fee3ardMedicineLc->setFeeType(Constant::GMS_FEE_3RD_AGENT_MEDICINE);
                $feeSetting = new FeeSetting();
                $feeSetting->setFee($fee3ardMedicineLocal->getFeeSetting()->getFee());
                $feeSetting->setNewFee(floatval($data['fee3rdLcMedicine']));
                $feeSetting->setEffectDate(new \DateTime($data['date3rdLcMedicine']));
                if($feeSetting->getEffectDate()->format("Y-m-d") == date("Y-m-d")){
                    $feeSetting->setFee($feeSetting->getNewFee());
                }
                $fee3ardMedicineLc->setFeeSetting($feeSetting);
                $agent->addThirdPartyFee($fee3ardMedicineLc);

                $fee3ardMedicineOs = new Agent3paFee();
                $fee3ardMedicineOs->setArea(Constant::GMS_TYPE_3RD_AGENT_OVERSEAS);
                $fee3ardMedicineOs->setFeeType(Constant::GMS_FEE_3RD_AGENT_MEDICINE);
                $feeSetting = new FeeSetting();
                $feeSetting->setFee($fee3ardMedicineOversea->getFeeSetting()->getFee());
                $feeSetting->setNewFee(floatval($data['fee3rdOsMedicine']));
                $feeSetting->setEffectDate(new \DateTime($data['date3rdOsMedicine']));
                if($feeSetting->getEffectDate()->format("Y-m-d") == date("Y-m-d")){
                    $feeSetting->setFee($feeSetting->getNewFee());
                }
                $fee3ardMedicineOs->setFeeSetting($feeSetting);
                $agent->addThirdPartyFee($fee3ardMedicineOs);

                $fee3ardDescriptionLc = new Agent3paFee();
                $fee3ardDescriptionLc->setArea(Constant::GMS_TYPE_3RD_AGENT_LOCAL);
                $fee3ardDescriptionLc->setFeeType(Constant::GMS_FEE_3RD_AGENT_PRESCRIPTION);
                $feeSetting = new FeeSetting();
                $feeSetting->setFee($fee3ardDescriptionLocal->getFeeSetting()->getFee());
                $feeSetting->setNewFee(floatval($data['fee3rdLcPrescription']));
                $feeSetting->setEffectDate(new \DateTime($data['date3rdLcPrescription']));
                if($feeSetting->getEffectDate()->format("Y-m-d") == date("Y-m-d")){
                    $feeSetting->setFee($feeSetting->getNewFee());
                }
                $fee3ardDescriptionLc->setFeeSetting($feeSetting);
                $agent->addThirdPartyFee($fee3ardDescriptionLc);

                $fee3ardDescriptionOs = new Agent3paFee();
                $fee3ardDescriptionOs->setArea(Constant::GMS_TYPE_3RD_AGENT_OVERSEAS);
                $fee3ardDescriptionOs->setFeeType(Constant::GMS_FEE_3RD_AGENT_PRESCRIPTION);
                $feeSetting = new FeeSetting();
                $feeSetting->setFee($fee3ardDescriptionOversea->getFeeSetting()->getFee());
                $feeSetting->setNewFee(floatval($data['fee3rdOsPrescription']));
                $feeSetting->setEffectDate(new \DateTime($data['date3rdOsPrescription']));
                if($feeSetting->getEffectDate()->format("Y-m-d") == date("Y-m-d")){
                    $feeSetting->setFee($feeSetting->getNewFee());
                }
                $fee3ardDescriptionOs->setFeeSetting($feeSetting);
                $agent->addThirdPartyFee($fee3ardDescriptionOs);

                $fee3ardConsultLc = new Agent3paFee();
                $fee3ardConsultLc->setArea(Constant::GMS_TYPE_3RD_AGENT_LOCAL);
                $fee3ardConsultLc->setFeeType(Constant::GMS_FEE_3RD_AGENT_LIVECONSULT);
                $feeSetting = new FeeSetting();
                $feeSetting->setFee($fee3ardConsultLocal->getFeeSetting()->getFee());
                $feeSetting->setNewFee(floatval($data['fee3rdLcConsult']));
                $feeSetting->setEffectDate(new \DateTime($data['date3rdLcConsult']));
                if($feeSetting->getEffectDate()->format("Y-m-d") == date("Y-m-d")){
                    $feeSetting->setFee($feeSetting->getNewFee());
                }
                $fee3ardConsultLc->setFeeSetting($feeSetting);
                $agent->addThirdPartyFee($fee3ardConsultLc);

                $fee3ardConsultOs = new Agent3paFee();
                $fee3ardConsultOs->setArea(Constant::GMS_TYPE_3RD_AGENT_OVERSEAS);
                $fee3ardConsultOs->setFeeType(Constant::GMS_FEE_3RD_AGENT_LIVECONSULT);
                $feeSetting = new FeeSetting();
                $feeSetting->setFee($fee3ardConsultOversea->getFeeSetting()->getFee());
                $feeSetting->setNewFee(floatval($data['fee3rdOsConsult']));
                $feeSetting->setEffectDate(new \DateTime($data['date3rdOsConsult']));
                if($feeSetting->getEffectDate()->format("Y-m-d") == date("Y-m-d")){
                    $feeSetting->setFee($feeSetting->getNewFee());
                }
                $fee3ardConsultOs->setFeeSetting($feeSetting);
                $agent->addThirdPartyFee($fee3ardConsultOs);

                // Minimum 3pa fee
                $minFee3paLocal =  new AgentCustomMedicineFee();
                $minFee3paLocal->setFeeValue($data['minAgentFeeSecondaryLocal']);
                $minFee3paLocal->setFeeName(Constant::AGENT_MINUMUM_FEE_NAME_lOCAL);
                $minFee3paLocal->setFeeCode(Constant::AGENT_MINUMUM_FEE_SECONDARY_lOCAL);
                $minFee3paLocal->setAgent($agent);
                $agent->addMinMedicineFee($minFee3paLocal);

                $minFee3paIndo =  new AgentCustomMedicineFee();
                $minFee3paIndo->setFeeValue($data['minAgentFeeSecondaryIndo']);
                $minFee3paIndo->setFeeName(Constant::AGENT_MINUMUM_FEE_NAME_INDONESIA);
                $minFee3paIndo->setFeeCode(Constant::AGENT_MINUMUM_FEE_SECONDARY_INDONESIA);
                $minFee3paIndo->setAgent($agent);
                $agent->addMinMedicineFee($minFee3paIndo);

                $minFee3paEastMalay =  new AgentCustomMedicineFee();
                $minFee3paEastMalay->setFeeValue($data['minAgentFeeSecondaryEastMalay']);
                $minFee3paEastMalay->setFeeName(Constant::AGENT_MINUMUM_FEE_NAME_EAST_MALAY);
                $minFee3paEastMalay->setFeeCode(Constant::AGENT_MINUMUM_FEE_SECONDARY_EAST_MALAY);
                $minFee3paEastMalay->setAgent($agent);
                $agent->addMinMedicineFee($minFee3paEastMalay);

                $minFee3paWestMalay =  new AgentCustomMedicineFee();
                $minFee3paWestMalay->setFeeValue($data['minAgentFeeSecondaryWestMalay']);
                $minFee3paWestMalay->setFeeName(Constant::AGENT_MINUMUM_FEE_NAME_WEST_MALAY);
                $minFee3paWestMalay->setFeeCode(Constant::AGENT_MINUMUM_FEE_SECONDARY_WEST_MALAY);
                $minFee3paWestMalay->setAgent($agent);
                $agent->addMinMedicineFee($minFee3paWestMalay);

                $minFee3paInternational =  new AgentCustomMedicineFee();
                $minFee3paInternational->setFeeValue($data['minAgentFeeSecondaryInternational']);
                $minFee3paInternational->setFeeName(Constant::AGENT_MINUMUM_FEE_NAME_INTERNATIONAL);
                $minFee3paInternational->setFeeCode(Constant::AGENT_MINUMUM_FEE_SECONDARY_INTERNATIONAL);
                $minFee3paInternational->setAgent($agent);
                $agent->addMinMedicineFee($minFee3paInternational);
            }

            $personalInfo = new PersonalInformation();
            $personalInfo->setFirstName($data['firstName']);
            $personalInfo->setLastName($data['lastName']);
            $personalInfo->setGender($data['gender']);
            $personalInfo->setEmailAddress($data['email']);
            $personalInfo->setPassportNo($data['localIdPassport']);
            $agent->setPersonalInformation($personalInfo);

            $iden = new Identification();
            $iden->setIdentityNumber($data['localIdPassport']);
            $iden->setIssuingCountryId($data['localIdPassportCountry']);        
            $agent->addIdentification($iden);
            // company info
            $agentCompany = new AgentCompany();
            $agentCompany->setCompanyName($data['comName']);
            $agentCompany->setCompanyRegistrationNumber($data['registerNo']);

            //company phone
            $companyPhone = new Phone();
            $companyPhone->setNumber($data['comPhone']);
            $comCountry = $em->getRepository('UtilBundle:Country')->find($data['comPhoneLocation']);
            $companyPhone->setCountry($comCountry);
            
            $agentCompany->setPhone($companyPhone);
            //COMPANY ADDRESS
            $companyAddress = new Address();
            $companyAddress->setLine1($data['comAddressLine1']);
            $companyAddress->setLine2($data['comAddressLine2']);
            $companyAddress->setLine3($data['comAddressLine3']);
            $companyAddress->setPostalCode($data['comZipCode']);

            $city = $em->getRepository('UtilBundle:City')->find($data['comCity']);
            $companyAddress->setCity($city);
            $agentCompany->setAddress($companyAddress);
            if(isset($data['checkAddress'])){
                $agentAddress = clone $companyAddress;
                $agentAddress->removeId();
                $agent->addAdress($agentAddress);
                $agent->setIsUseCompanyAddress(true);
            } else {
                $add = new Address();
                $add->setCity($em->getRepository('UtilBundle:City')->find($data['city']));
                $add->setPostalCode($data['zipCode']);
                $add->setLine1($data['addressLine1']);
                $add->setLine2($data['addressLine2']);
                $add->setLine3($data['addressLine3']);
                $agent->addAdress($add);
                $agent->setIsUseCompanyAddress(false);
                
            }        
            $phone = new Phone();
            $country = $em->getRepository('UtilBundle:Country')->find($data['phoneLocation']);       
            $phone->setCountry($country);
            $phone->setPhoneType($em->getRepository('UtilBundle:PhoneType')->getPhone());
            $phone->setNumber($data['phone']);
            $agent->addPhone($phone);

            if ($data['bankCountry'] == Constant::ID_SINGAPORE || $data['bankCountry'] == Constant::ID_MALAYSIA) {
                $bank = $em->getRepository('UtilBundle:Bank')->find($data['bankName']);
            } else {
                $bank = new Bank();
                $bank->setName($data['bankName']);
                $bank->setCountry($em->getRepository('UtilBundle:Country')->find($data['bankCountryIssue']));
                $bank->setSwiftCode($data['bankSwiftCode']);
            }

            $bankAcc = new BankAccount();
            $bankAcc->setBank($bank);
            $bankAcc->setAccountName($data['accountName']);
            $bankAcc->setAccountNumber($data['accountNumber']);

            $agent->setBankAccount($bankAcc);

            $agent->setGlobalId(1);
            $agent->setIsActive(true);
            $agentCode = '';
            if(isset($data['checkAddress'])){
                $c = $em->getRepository('UtilBundle:Country')->find($data['comCountry']);
            } else {
                $c = $em->getRepository('UtilBundle:Country')->find($data['country']);
            }
            if($c) {
                $code = $c->getCodeAthree();

                $agentCode = $em->getRepository('UtilBundle:Agent')->generateCode($code);
            }
            $agent->setAgentCode($agentCode);
            $agentCompany->setAgent($agent);
            $em->persist($companyAddress);
            $em->persist($companyPhone);
            $em->persist($agentCompany);
            $em->persist($agent);
            $em->flush();

            // logging
            $arr = array('module' => Constant::AGENT_MODULE_NAME, 
                         'title'  =>'new_agent_created', 
                         'action' => 'create',
                         'id'     => $agent->getId());
            $loggerUser = $this->getUser()->getLoggedUser();
            $author = $loggerUser->getFirstName() . ' ' . $loggerUser->getLastName();
            Utils::saveLog(array(), array(), $author, $arr, $em);

            $file = array();
            $file['name'] = $_FILES["admin_agent"]['name']['logo'];
            $file['type'] = $_FILES["admin_agent"]['type']['logo'];
            $file['tmp_name'] = $_FILES["admin_agent"]['tmp_name']['logo'];
            $file['error'] = $_FILES["admin_agent"]['error']['logo'];
            $file['size'] = $_FILES["admin_agent"]['size']['logo'];
            $profile = $this->uploadfile($file, 'agent/profile'.$agent->getId());

            if(!empty($profile)) {
                $agent->setProfilePhotoUrl($profile);
            }

            // Agent Fee Medicine
            // $em->getRepository('UtilBundle:Agent')->updateAgentFeeMedicine($agent, $data['fees']);

            $em->persist($agent);
            $em->flush();
            
            $this->sendAgentEmail($agent);
            return $this->redirectToRoute('admin_agent');
                
           
        
        }
        
         $parameters = array(
            'form' => $form->createView(),
            'ajaxURL' => 'admin_doctor_create_ajax',
            'ajaxDependent' => 'admin_doctor_create_getdependent',
            'successUrl' => 'admin_agent',
            'agent' => $agent,
            'agentCode' => '',
            'agentId' => '',
            'title' => 'Register Agent',
             'listAgentFee' => $listAgentFee
        );        
        return $this->render('AdminBundle:admin:agent-register.html.twig',$parameters);
    }
   
    /**
     * @Route("/admin/agent/{id}/sub-agent/create", name="admin_agent_create_sub")
    */
    public function subAgentCreateAction(Request $request,$id)
    {
        $em = $this->getDoctrine()->getEntityManager();        
        $country = $em->getRepository('UtilBundle:Country')->getByPreferredCountry(true);

        $dependentData= array();
        $dependentData['country'] = $country;

        $agent = new Agent();
        $form = $this->createForm(new SubAgentAdminType(array('agent' => $agent,'depend'=>$dependentData)), array(), array());       
        $eror = array();
        if($request->getMethod() == 'POST') {  
            $form->handleRequest($request); 
            $data = $request->request->get('admin_agent');
            $data = Common::removeSpaceOf($data);
            $personalInfo = new PersonalInformation();
            $personalInfo->setFirstName($data['firstName']);
            $personalInfo->setLastName($data['lastName']);
            $personalInfo->setGender($data['gender']);
            $personalInfo->setEmailAddress($data['email']);
            $personalInfo->setPassportNo($data['localIdPassport']);
            $agent->setPersonalInformation($personalInfo);

            $iden = new Identification();
            $iden->setIdentityNumber($data['localIdPassport']);
            $iden->setIssuingCountryId($data['localIdPassportCountry']);        
         //   $iden->setIssueDate(date('Y-m-d', strtotime($data['localIdPassportDate'])));
            $agent->addIdentification($iden);

            $add = new Address();
            $add->setCity($em->getRepository('UtilBundle:City')->find($data['city']));
            $add->setPostalCode($data['zipCode']);
            $add->setLine1($data['addressLine1']);
            $add->setLine2($data['addressLine2']);
            $add->setLine3($data['addressLine3']);
            $agent->addAdress($add);

            $phone = new Phone();
            $country = $em->getRepository('UtilBundle:Country')->find($data['phoneLocation']);       
            $phone->setCountry($country);
            $phone->setPhoneType($em->getRepository('UtilBundle:PhoneType')->getPhone());
           // $phone->setAreaCode($data['phoneArea']);
            $phone->setNumber($data['phone']);
            $agent->addPhone($phone);
            $agent->setGlobalId(1);
            $agent->setIsActive(true);
            $parent = $em->getRepository('UtilBundle:Agent')->find($id);
            $agent->setParent($parent);
            $agent->setIs3paAgent($parent->getIs3paAgent());

            $agentCode = '';

            $c = $em->getRepository('UtilBundle:Country')->find($data['country']);

            if($c) {
                $code = $c->getCodeAthree();

                $agentCode = $em->getRepository('UtilBundle:Agent')->generateCode($code);
            }
            $agent->setAgentCode($agentCode);
            $em->persist($agent);
            $em->flush();
            $file = array();
            $file['name'] = $_FILES["admin_agent"]['name']['logo'];
            $file['type'] = $_FILES["admin_agent"]['type']['logo'];
            $file['tmp_name'] = $_FILES["admin_agent"]['tmp_name']['logo'];
            $file['error'] = $_FILES["admin_agent"]['error']['logo'];
            $file['size'] = $_FILES["admin_agent"]['size']['logo'];
            $profile = $this->uploadfile($file, 'agent/profile'.$agent->getId());

            if(!empty($profile)) {
                $agent->setProfilePhotoUrl($profile);
            }
            $em->persist($agent);
            $em->flush();
            if(count($parent->getActiveChild()) == 1)
            {
                $agentDoctors = $parent->getAgentDoctors();
                foreach ($agentDoctors as $ad) {

                    if(empty($ad->getDeletedOn())) {
                        $doctor = $ad->getDoctor();
                        $doctor->addAgent($agent);
                        $em->persist($doctor);       
                        $em->flush();
                    }
                    $ad->setDeletedOn(new \DateTime('now'));
                    $em->persist($ad);
                    $em->flush();
                }
            }
            $this->sendAgentEmail($agent);
            return $this->redirectToRoute('admin_sub_agent',array('id' => $id));
           
        }

        $masterAgent =  $em->getRepository('UtilBundle:Agent')->find($id);
        $companyDetail = $this->getDoctrine()->getRepository('UtilBundle:AgentCompany')->findOneBy(['agent' => $masterAgent]);
      
        $parameters = array(
            'form' => $form->createView(),
            'ajaxURL' => 'admin_doctor_create_ajax',
            'ajaxDependent' => 'admin_doctor_create_getdependent',          
            'successUrl' => 'admin_agent',
            'agentId' => $id,
            'agent' => $agent,
            'title' => 'Register Agent',
            'companyDetail' => $companyDetail,
            'masteragent' => $masterAgent
        );
        
        return $this->render('AdminBundle:admin:sub-agent-register.html.twig',$parameters);
    }
    
    /**
     * @Route("/admin/agent/{id}/sub-agent/{child}/edit", name="admin_sub_agent_edit")
    */
    public function subAgentEditAction(Request $request,$id,$child)
    {
        
        $em = $this->getDoctrine()->getEntityManager();
        $country = $em->getRepository('UtilBundle:Country')->getByPreferredCountry(true);
        $dependentData= array();
        $dependentData['country'] = $country;
        $dependentData['cityRepo'] = $em->getRepository('UtilBundle:City');
        $agent = $em->getRepository('UtilBundle:Agent')->find($child);
        
        if(empty($agent->getParent()))
        {
            return $this->redirectToRoute('admin_sub_agent',array('id' => $id));
        }
        $form = $this->createForm(new SubAgentAdminType(array('agent' => $agent,'depend'=>$dependentData)), array(), array());       
        $eror = array();
        if($request->getMethod() == 'POST') {  
            $form->handleRequest($request);
            $data = $request->request->get('admin_agent');
            $data = Common::removeSpaceOf($data);

            $personalInfo =  $agent->getPersonalInformation();
            $personalInfo->setFirstName($data['firstName']);
            $personalInfo->setLastName($data['lastName']);
            $personalInfo->setGender($data['gender']);
            $personalInfo->setPassportNo($data['localIdPassport']);

            $iden = $agent->getIdentifications()->first();
            $iden->setIdentityNumber($data['localIdPassport']);
            $iden->setIssuingCountryId($data['localIdPassportCountry']);        
           


            $add = $agent->getAdresses()->first();
            $add->setCity($em->getRepository('UtilBundle:City')->find($data['city']));
            $add->setPostalCode($data['zipCode']);
            $add->setLine1($data['addressLine1']);
            $add->setLine2($data['addressLine2']);
            $add->setLine3($data['addressLine3']);

            $phone = $agent->getPhones()->first();
            $country = $em->getRepository('UtilBundle:Country')->find($data['phoneLocation']);       
            $phone->setCountry($country);
            $phone->setPhoneType($em->getRepository('UtilBundle:PhoneType')->getPhone());
            $phone->setAreaCode($data['phoneArea']);
            $phone->setNumber($data['phone']);    

           
            $em->persist($agent);
            $em->flush();
            $file = array();

            if(isset( $_FILES["admin_agent"]))
            {
                $file['name'] = $_FILES["admin_agent"]['name']['logo'];
                $file['type'] = $_FILES["admin_agent"]['type']['logo'];
                $file['tmp_name'] = $_FILES["admin_agent"]['tmp_name']['logo'];
                $file['error'] = $_FILES["admin_agent"]['error']['logo'];
                $file['size'] = $_FILES["admin_agent"]['size']['logo'];
                $profile = $this->uploadfile($file, 'agent/profile'.$agent->getId());                    
                if(!empty($profile)) {
                    $agent->setProfilePhotoUrl($profile);
                }
            }
            $em->persist($agent);
            $em->flush();


            return $this->redirectToRoute('admin_sub_agent',array('id' => $id));                
       
        }
        $masterAgent =  $em->getRepository('UtilBundle:Agent')->find($id);
        $companyDetail = $this->getDoctrine()->getRepository('UtilBundle:AgentCompany')->findOneBy(['agent' => $masterAgent]);
        $parameters = array(
            'form' => $form->createView(),
            'ajaxURL' => 'admin_doctor_create_ajax',
            'ajaxDependent' => 'admin_doctor_create_getdependent',          
            'successUrl' => 'admin_agent',
            'agentId'=>$id,
            'agent' => $agent,
            'title' => 'Edit Agent',
            'companyDetail' => $companyDetail,
            'masteragent' => $masterAgent
        );
        
        return $this->render('AdminBundle:admin:sub-agent-register.html.twig',$parameters);
    }
    
    
     /**
     * @Route("/admin/agent-update-status", name="admin_agent_update_status_ajax")
     */
    public function agentUpdateStatusAction(Request $request)
    {    
        $em = $this->getDoctrine()->getEntityManager();  
        $id = $request->request->get('id');
        $type = $request->request->get('type');
        if($type == 1) {
            $doctor = $em->getRepository('UtilBundle:Agent')->find($id);  
            $status = $doctor->getIsActive();
            $oldData = array('isActive' => $status);
            $newStatus = ($status+1)%2;
            $doctor->setIsActive($newStatus);

            $newData = array('isActive' => $newStatus);
			
			$users = array();
			$userActors = $em->getRepository('UtilBundle:UserActors')->findBy(array(
				'entityId' => $doctor->getId(),
				'role' => array(Constant::AGENT_ROLE, Constant::SUB_AGENT_ROLE)
			));
			if ($userActors) {
				foreach ($userActors as $userActor) {
					$users[] = $userActor->getUser();
				}
			}
			
			$em->beginTransaction();
			try {
				$em->persist($doctor);
				$em->flush();
				
				if (!empty($users)) {
					foreach ($users as $user) {
						$user->setIsActive($newStatus);
						$em->persist($user);
					}
					$em->flush();
				}
				
				$em->commit();
			} catch (\Exception $ex) {
				$em->rollback();
			}

            $arr = array('module' => 'agents', 'title' =>'status_changed', 'id' => $id);
            $loggerUser = $this->getUser()->getLoggedUser();
            $author = $loggerUser->getFirstName() . ' ' . $loggerUser->getLastName();
            Utils::saveLog($oldData, $newData, $author, $arr, $em);
        } elseif ($type == 2) {
            $doctor = $em->getRepository('UtilBundle:Agent')->find($id);              
            $doctor->setDeletedOn(new \DateTime("now"));
			
			$users = array();
			$userActors = $em->getRepository('UtilBundle:UserActors')->findBy(array(
				'entityId' => $doctor->getId(),
				'role' => array(Constant::AGENT_ROLE, Constant::SUB_AGENT_ROLE)
			));
			
			$em->beginTransaction();
			try {
				$em->persist($doctor);
				$em->flush();
				
				if (!empty($userActors)) {
					foreach ($userActors as $userActor) {
						$users[] = $userActor->getUser();
						$userActor->setDeletedOn(new \DateTime("now"));
						$em->persist($userActor);
					}
					$em->flush();
				}
				
				if (!empty($users)) {
					foreach ($users as $user) {
						$user->setIsActive(0);
						$em->persist($user);
					}
					$em->flush();
				}
				
				$em->commit();
			} catch (\Exception $ex) {
				$em->rollback();
			}
        }
        return new JsonResponse(array('success'=>true));
    }
    
    private function sendAgentEmail($agent = null) {
     
        $base = $this->container->get('request')->getSchemeAndHttpHost();
        $host = 'http://'; 
        if (strpos($base, 'https://') !== false) {
            $host = 'https://'; 
        }
        $url_agent = '';
        if ($agent) {
            if ($agent->getParent()) {
                $url_agent = $host.$agent->getParent()->getSite()->getUrl();
            } else {
                if (count($agent->getChild()) == 0) {
                    $url_agent = $host.$agent->getSite()->getUrl();
                }
            }
        }
        if ($url_agent != '') {
            $base = $url_agent;
        }
        $emailTo = $agent->getPersonalInformation()->getEmailAddress();
        if (empty($agent->getParent())) {
            $subject = "Setting Agent Infomation";
            $masterName = '';
        } else {
            $subject = "Setting G-MEDS sub-agent user account";
            $masterName = $agent->getParent()->getPersonalInformation()->getFullName();
        }
     
        $mailTemplate = 'AdminBundle:admin:email/register-agent.html.twig';
        $mailParams = array(
            'logoUrl' => $base.'/bundles/admin/assets/pages/img/logo.png',
            'name' =>  $agent->getPersonalInformation()->getFullName(),
            'id' => $agent->getId(),
            'base' => $base,
            'masterName' => $masterName,
        );
        $dataSendMail = array(
            'title'  => $subject,
            'body'   => $this->container->get('templating')->render($mailTemplate, $mailParams),
            'to'     => $emailTo,
       
        );
        $this->container->get('microservices.sendgrid.email')->sendEmail($dataSendMail);
    }



    /**
     * @Route("/admin/resend-agent-welcome-email", name="admin_resend_agent_welcome_email")
     */
    public function resendAgentWelcomeMail(Request $request){
        $em = $this->getDoctrine()->getEntityManager();
        $id = $request->get('id');
        $agent = $em->getRepository('UtilBundle:Agent')->find($id);
        if($agent->getIsConfirmed() < Constant::STATUS_CONFIRM){
            $this->sendAgentEmail($agent);
            return new JsonResponse(true);
        }else{
            return new JsonResponse(false);
        }
    }
}
