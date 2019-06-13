<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AdminBundle\Form\Type\AdminRadioType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use UtilBundle\Entity\Agent;
use UtilBundle\Entity\Agent3paFee;
use UtilBundle\Entity\City;
use UtilBundle\Entity\Site;
use UtilBundle\Repository\CityRepository;
use UtilBundle\Entity\State;
use UtilBundle\Repository\StateRepository;
use UtilBundle\Entity\Country;
use UtilBundle\Repository\CountryRepository;
use UtilBundle\Utility\Constant;

class AgentAdminType extends AbstractType {

    public $initdata;

    public function __construct($options) {
        $this->initdata = $options;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $em = $this->initdata['entity_manager'];
        $agent = $this->initdata['agent'];
        $country = $this->initdata['depend']['country'];
        $agentFeeMedicine = $this->initdata['depend']['agentFeeMedicine'];

        $phoneCountry = array();
        $countries = array(); // array('empty' => 'Select Country');
        foreach ($country as $c) {
            $phoneCountry[$c['id']] = $c['name'] . ' (+' . $c['phoneCode'] . ')';
            $countries[$c['id']] = $c['name'];
        }
        
        $isGst = $agent->getIsGst();
        $isGst = isset($isGst) ? $isGst : 0;

        $gstNo = $agent->getGstNo();
        $gstDate = '';
        if ($agent->getGstEffectDate()) {
            $gstDate = $agent->getGstEffectDate()->format('d M y');
        }

        $firstname = '';
        $lastname = '';
        $email = '';
        $phoneLocation = '';
        $phoneArea = '';
        $phoneNumber = '';
        $gender = '';
        $passport = '';
        $passportCountry = '';
        $passportDate = '';
        $line1 = '';
        $line2 = '';
        $line3 = '';
        $countryAgent = '';
        $listState = array();
        $stateAgent = '';
        $cities = array();
        $city = '';
        $zipCode = '';
        $bankCountry = '';
        $agentSite = '';
        $bankState = '';
        $bankCity = '';
        $bankName = '';
        $accountName = '';
        $accountNum = '';
        $bankcode = '';
        $edited = false;
        $enable3rdFee = true;
        $enableMin = true;
        $company = [
            'name' => '',
            'registrationNo' => '',
            'phone' => [
                'location' => '',
                'area' => '',
                'number' => ''
            ],
            'line1' => '',
            'line2' => '',
            'line3' => '',
            'country' => '',
            'state' => '',
            'city' => '',
            'zipCode' => '',
            'cities' => [],
            'states' => [],
        ];
        $checkAddress = false;

        $is3rd  = true;
        $fee3rdAgent =  $this->initdata['fee'];

        $fee3rdLcMedicine = $fee3rdAgent['medLc']->getFeeSetting()->getNewFee();
        $date3rdLcMedicine = $fee3rdAgent['medLc']->getFeeSetting()
            ->getEffectDate()->format(Constant::GENERAL_DATE_FORMAT);

        $fee3rdOsMedicine = $fee3rdAgent['medOs']->getFeeSetting()->getNewFee();
        $date3rdOsMedicine = $fee3rdAgent['medLc']->getFeeSetting()
            ->getEffectDate()->format(Constant::GENERAL_DATE_FORMAT);

        $fee3rdLcPrescription = $fee3rdAgent['desLc']->getFeeSetting()->getNewFee();
        $date3rdLcPrescription = $fee3rdAgent['medLc']->getFeeSetting()
            ->getEffectDate()->format(Constant::GENERAL_DATE_FORMAT);

        $fee3rdOsPrescription = $fee3rdAgent['desOs']->getFeeSetting()->getNewFee();
        $date3rdOsPrescription = $fee3rdAgent['medLc']->getFeeSetting()
            ->getEffectDate()->format(Constant::GENERAL_DATE_FORMAT);

        $fee3rdLcConsult = $fee3rdAgent['conLc']->getFeeSetting()->getNewFee();
        $date3rdLcConsult = $fee3rdAgent['medLc']->getFeeSetting()
            ->getEffectDate()->format(Constant::GENERAL_DATE_FORMAT);

        $fee3rdOsConsult = $fee3rdAgent['conOs']->getFeeSetting()->getNewFee();
        $date3rdOsConsult = $fee3rdAgent['medLc']->getFeeSetting()
            ->getEffectDate()->format(Constant::GENERAL_DATE_FORMAT);

        $feeServiceLocal = $fee3rdAgent['priDesLc']->getNewAgentPercentage();
        $feePlatformServiceLocal = $fee3rdAgent['priDesLc']->getNewPlatformPercentage();
        $feeServiceLocalDate = $fee3rdAgent['priDesLc']->getTakeEffectOn()->format(Constant::GENERAL_DATE_FORMAT);

        $feeServiceOversea = $fee3rdAgent['priDesOs']->getNewAgentPercentage();
        $feePlatformServiceOversea = $fee3rdAgent['priDesOs']->getNewPlatformPercentage();
        $feeServiceOverseaDate = $fee3rdAgent['priDesOs']->getTakeEffectOn()->format(Constant::GENERAL_DATE_FORMAT);

        $feeMedicineLocal = $fee3rdAgent['priMedLc']->getNewAgentPercentage();
        $feeMedicineLocalDate = $fee3rdAgent['priMedLc']->getTakeEffectOn()->format(Constant::GENERAL_DATE_FORMAT);

        $feeMedicineOversea = $fee3rdAgent['priMedOs']->getNewAgentPercentage();
        $feeMedicineOverseaDate = $fee3rdAgent['priMedOs']->getTakeEffectOn()->format(Constant::GENERAL_DATE_FORMAT);

        $feeConsultLocal = $fee3rdAgent['priConLc']->getNewAgentPercentage();
        $feePlatformConsultLocal = $fee3rdAgent['priConLc']->getNewPlatformPercentage();
        $feeConsultLocalDate = $fee3rdAgent['priConLc']->getTakeEffectOn()->format(Constant::GENERAL_DATE_FORMAT);

        $feeConsultOversea = $fee3rdAgent['priConOs']->getNewAgentPercentage();
        $feePlatformConsultOversea = $fee3rdAgent['priConOs']->getNewPlatformPercentage();
        $feeConsultOverseaDate = $fee3rdAgent['priConOs']->getTakeEffectOn()->format(Constant::GENERAL_DATE_FORMAT);

        if (is_object($agent) && $agent->getId()) {
            $cityRepo = $this->initdata['depend']['cityRepo'];
            $agentSite = $agent->getSite();
            $firstname = $agent->getPersonalInformation()->getFirstName();
            $lastname = $agent->getPersonalInformation()->getLastName();
            $email = $agent->getPersonalInformation()->getEmailAddress();
            $gender = $agent->getPersonalInformation()->getGender();
            $phoneObj = $agent->getPhones()->first();
            $phoneLocation = $phoneObj->getCountry()->getId();
            $phoneArea = $phoneObj->getAreaCode();
            $phoneNumber = $phoneObj->getNumber();
            $address = $agent->getAdresses()->first();
            $line1 = $address->getLine1();
            $line2 = $address->getLine2();
            $line3 = $address->getLine3();
            $cityObj = $address->getCity();
            $city = $cityObj->getId();
            $countryAgent = $cityObj->getCountry()->getId();
            $states = $cityRepo->getStateByCountry($countryAgent);
            $listState = $states;
            $listcityObj = array();
            $checkAddress = ( is_null($agent->getIsUseCompanyAddress()))? false : $agent->getIsUseCompanyAddress() ;
            if (empty($cityObj->getState())) {
                $listcityObj = $cityObj->getCountry()->getCities();
                $stateAgent = '';
            } else {
                $listcityObj = $cityObj->getState()->getCities();
                $stateAgent = $cityObj->getState()->getId();
            }

            $listcity = array();
            foreach ($listcityObj as $c) {
                $listcity[$c->getId()] = $c->getName();
            }
            $cities = $listcity;
            $zipCode = $address->getPostalCode();
            $iden = $agent->getIdentifications()->first();
            if(!empty($iden)) {
                $passport = $iden->getIdentityNumber();
                $passportCountry = $iden->getIssuingCountryId();
                $passportDate = date('d M y', strtotime($iden->getIssueDate()));
            }
            $bankAcc = $agent->getBankAccount();
            if(!empty($bankAcc)) {
                $accountName = $bankAcc->getAccountName();
                $accountNum = $bankAcc->getAccountNumber();
                $bank = $bankAcc->getBank();
                $bankName = $bank->getName();
                $bankCountry = $bank->getCountry();
                if ($bankCountry && ($bankCountry->getId() == Constant::ID_SINGAPORE || $bankCountry->getId() == Constant::ID_MALAYSIA)) {
                    $bankName = $bank->getId();
                }
                $bankCity = $bank->getCity();
                if(!empty($bankCity)){
                    $bankState = $bankCity->getState();      
                }
                $bankcode = $bank->getSwiftCode();
            }

            $is3rd = boolval( $agent->getIs3paAgent() );
            $enableMin  = boolval( $agent->getIsMinimunFeeEnabled() );
            if(!$is3rd) {
                $enable3rdFee = false;
            }
            $edited = true;
            if(isset($this->initdata['depend']['agentCompany'])){
                $agentCompanyRes = $this->initdata['depend']['agentCompany'];
                $agentCompany = $agentCompanyRes->findOneBy(array('agent' => $agent));
                $company['name'] = $agentCompany->getCompanyName();
                $company['registrationNo'] = $agentCompany->getCompanyRegistrationNumber();
                $company['phone']['location'] = $agentCompany->getPhone()->getCountry();
                $company['phone']['area'] = $agentCompany->getPhone()->getAreaCode();
                $company['phone']['number'] = $agentCompany->getPhone()->getNumber();
                $company['line1'] = $agentCompany->getAddress()->getLine1();
                $company['line2'] = $agentCompany->getAddress()->getLine2();
                $company['line3'] = $agentCompany->getAddress()->getLine3();
              
                $cCity = $agentCompany->getAddress()->getCity();
                $company['country'] = $cCity->getCountry()->getId();
                
                $cStates = $cityRepo->getStateByCountry($company['country']);
                $company['states'] = $cStates;
                $cState = '';
                $listcityObjC = array();
                if (empty($cCity->getState())) {
                    $listcityObjC = $cCity->getCountry()->getCities();
                    $cState = '';
                } else {
                    $listcityObjC = $cCity->getState()->getCities();
                    $cState = $cCity->getState()->getId();
                }
                $listcityC = array();
                foreach ($listcityObjC as $c) {
                    $listcityC[$c->getId()] = $c->getName();
                }             
                $company['state'] = $cState;
                $company['city'] = $cCity->getId();
                $company['zipCode'] = $agentCompany->getAddress()->getPostalCode();
                $company['cities'] = $listcityC;

            }




            $date3rdLcMedicine = $fee3rdAgent['medLc']->getFeeSetting()->getEffectDate()->format(Constant::GENERAL_DATE_FORMAT);

            $date3rdOsMedicine = $fee3rdAgent['medOs']->getFeeSetting()->getEffectDate()->format(Constant::GENERAL_DATE_FORMAT);

            $date3rdLcPrescription = $fee3rdAgent['desLc']->getFeeSetting()->getEffectDate()->format(Constant::GENERAL_DATE_FORMAT);


            $date3rdOsPrescription = $fee3rdAgent['desOs']->getFeeSetting()->getEffectDate()->format(Constant::GENERAL_DATE_FORMAT);

            $date3rdLcConsult = $fee3rdAgent['conLc']->getFeeSetting()->getEffectDate()->format(Constant::GENERAL_DATE_FORMAT);

            $date3rdOsConsult = $fee3rdAgent['conOs']->getFeeSetting()->getEffectDate()->format(Constant::GENERAL_DATE_FORMAT);

        }


        $builder
                ->add('site', EntityType::class, array(
                    'label' => 'G-MEDS Site',
                    'class' => Site::class,
                    'choice_label' => function ($site) {
                        return $site->getDisplaySite();
                    },
                    'data'  => $agentSite,
                    'placeholder'   => 'Select Site',
                    'attr' => array('disabled' => $edited)
                ))


                ->add('fee3rdLcMedicine', TextType::class, array('label' => 'Set Admin Fee', 'attr' => array('placeholder' => '', 'disabled' => !$enable3rdFee, 'value' => number_format($fee3rdLcMedicine, 2, '.', ','))))
                ->add('date3rdLcMedicine', TextType::class, array('label' => 'Percentage change will take effect on:', 'attr' => array('placeholder' => '', 'disabled' => !$enable3rdFee, 'value' => $date3rdLcMedicine)))

                ->add('fee3rdOsMedicine', TextType::class, array('label' => 'Set Admin Fee', 'attr' => array('placeholder' => '', 'disabled' => !$enable3rdFee, 'value' => number_format($fee3rdOsMedicine, 2, '.', ','))))
                ->add('date3rdOsMedicine', TextType::class, array('label' => 'Percentage change will take effect on:', 'attr' => array('placeholder' => '', 'disabled' => !$enable3rdFee, 'value' => $date3rdOsMedicine)))

                ->add('fee3rdLcPrescription', TextType::class, array('label' => 'Set Admin Fee', 'attr' => array('placeholder' => '', 'disabled' => !$enable3rdFee, 'value' => number_format($fee3rdLcPrescription, 2, '.', ','))))
                ->add('date3rdLcPrescription', TextType::class, array('label' => 'Percentage change will take effect on:', 'attr' => array('placeholder' => '', 'disabled' => !$enable3rdFee, 'value' => $date3rdLcPrescription)))

                ->add('fee3rdOsPrescription', TextType::class, array('label' => 'Set Admin Fee', 'attr' => array('placeholder' => '', 'disabled' => !$enable3rdFee, 'value' => number_format($fee3rdOsPrescription, 2, '.', ','))))
                ->add('date3rdOsPrescription', TextType::class, array('label' => 'Percentage change will take effect on:', 'attr' => array('placeholder' => '', 'disabled' => !$enable3rdFee, 'value' => $date3rdOsPrescription)))

                ->add('fee3rdLcConsult', TextType::class, array('label' => 'Set Admin Fee', 'attr' => array('placeholder' => '', 'disabled' => !$enable3rdFee, 'value' => number_format($fee3rdLcConsult, 2, '.', ','))))
                ->add('date3rdLcConsult', TextType::class, array('label' => 'Percentage change will take effect on:', 'attr' => array('placeholder' => '', 'disabled' => !$enable3rdFee, 'value' => $date3rdLcConsult)))

                ->add('fee3rdOsConsult', TextType::class, array('label' => 'Set Admin Fee', 'attr' => array('placeholder' => '', 'disabled' => !$enable3rdFee, 'value' => number_format($fee3rdOsConsult, 2, '.', ','))))
                ->add('date3rdOsConsult', TextType::class, array('label' => 'Percentage change will take effect on:', 'attr' => array('placeholder' => '', 'value' => $date3rdOsConsult, 'disabled' => !$enable3rdFee)))

                ->add('feeServiceLocal', TextType::class, array('label' => 'Primary Agent Fee', 'attr' => array('placeholder' => '', 'value' => number_format($feeServiceLocal, 2, '.', ','))))
                ->add('feePlatformServiceLocal', TextType::class, array('label' => 'GMedes Fee', 'attr' => array('placeholder' => '', 'value' => number_format($feePlatformServiceLocal, 2, '.', ','))))
                ->add('feeServiceLocalDate', TextType::class, array('label' => 'Effective date', 'attr' => array('placeholder' => '', 'value' => $feeServiceLocalDate)))

                ->add('feeServiceOversea', TextType::class, array('label' => 'Primary Agent Fee', 'attr' => array('placeholder' => '', 'value' => number_format($feeServiceOversea, 2, '.', ','))))
                ->add('feePlatformServiceOversea', TextType::class, array('label' => 'GMedes Fee', 'attr' => array('placeholder' => '', 'value' => number_format($feePlatformServiceOversea, 2, '.', ','))))
                ->add('feeServiceOverseaDate', TextType::class, array('label' => 'Effective date', 'attr' => array('placeholder' => '', 'value' => $feeServiceOverseaDate)))

                ->add('feeMedicineLocal', TextType::class, array('label' => 'Primary Agent Fee', 'attr' => array('placeholder' => '', 'value' => number_format($feeMedicineLocal, 2, '.', ','))))
                ->add('feeMedicineLocalDate', TextType::class, array('label' => 'Effective date', 'attr' => array('placeholder' => '', 'value' => $feeMedicineLocalDate)))

                ->add('feeMedicineOversea', TextType::class, array('label' => 'Primary Agent Fee', 'attr' => array('placeholder' => '', 'value' => number_format($feeMedicineOversea, 2, '.', ','))))
                ->add('feeMedicineOverseaDate', TextType::class, array('label' => 'Effective date', 'attr' => array('placeholder' => '', 'value' => $feeMedicineOverseaDate)))

                ->add('feeConsultLocal', TextType::class, array('label' => 'Primary Agent Fee', 'attr' => array('placeholder' => '', 'value' => number_format($feeConsultLocal, 2, '.', ','))))
                ->add('feePlatformConsultLocal', TextType::class, array('label' => 'GMedes Fee', 'attr' => array('placeholder' => '', 'value' => number_format($feePlatformConsultLocal, 2, '.', ','))))
                ->add('feeConsultLocalDate', TextType::class, array('label' => 'Effective date', 'attr' => array('placeholder' => '', 'value' => $feeConsultLocalDate)))

                ->add('feeConsultOversea', TextType::class, array('label' => 'Primary Agent Fee', 'attr' => array('placeholder' => '', 'value' => number_format($feeConsultOversea, 2, '.', ','))))
                ->add('feePlatformConsultOversea', TextType::class, array('label' => 'GMedes Fee', 'attr' => array('placeholder' => '', 'value' => number_format($feePlatformConsultOversea, 2, '.', ','))))
                ->add('feeConsultOverseaDate', TextType::class, array('label' => 'Effective date', 'attr' => array('placeholder' => '', 'value' => $feeConsultOverseaDate)))

                ->add('minAgentFeePrimaryLocal', 'text', array(
                    'required' => true,
                    'trim'     => true,
                    'data'     => number_format($fee3rdAgent['minPri']['local'], 2, '.', ',')
                ))
                ->add('minAgentFeePrimaryIndo', 'text', array(
                    'required' => true,
                    'trim'     => true,
                    'data'     => number_format($fee3rdAgent['minPri']['feeIndo'], 2, '.', ',')
                ))
                ->add('minAgentFeePrimaryEastMalay', 'text', array(
                    'required' => true,
                    'trim'     => true,
                    'data'     => number_format($fee3rdAgent['minPri']['feeEastMalay'], 2, '.', ',')
                ))
                ->add('minAgentFeePrimaryWestMalay', 'text', array(
                    'required' => true,
                    'trim'     => true,
                    'data'     => number_format($fee3rdAgent['minPri']['feeWestMalay'], 2, '.', ',')
                ))
                ->add('minAgentFeePrimaryInternational', 'text', array(
                    'required' => true,
                    'trim'     => true,
                    'data'     => number_format($fee3rdAgent['minPri']['feeInternational'], 2, '.', ',')
                ))

                ->add('minAgentFeeSecondaryLocal', 'text', array(
                    'required' => true,
                    'trim'     => true,
                    'data'     => number_format($fee3rdAgent['min3pa']['local'], 2, '.', ',')
                ))
                ->add('minAgentFeeSecondaryIndo', 'text', array(
                    'required' => true,
                    'trim'     => true,
                    'data'     => number_format($fee3rdAgent['min3pa']['feeIndo'], 2, '.', ',')
                ))
                ->add('minAgentFeeSecondaryEastMalay', 'text', array(
                    'required' => true,
                    'trim'     => true,
                    'data'     => number_format($fee3rdAgent['min3pa']['feeEastMalay'], 2, '.', ',')
                ))
                ->add('minAgentFeeSecondaryWestMalay', 'text', array(
                    'required' => true,
                    'trim'     => true,
                    'data'     => number_format($fee3rdAgent['min3pa']['feeWestMalay'], 2, '.', ',')
                ))
                ->add('minAgentFeeSecondaryInternational', 'text', array(
                    'required' => true,
                    'trim'     => true,
                    'data'     => number_format($fee3rdAgent['min3pa']['feeInternational'], 2, '.', ',')
                ))


            ->add('firstName', TextType::class, array('label' => 'First Name', 'attr' => array('placeholder' => 'Enter First Name', 'value' => $firstname)))
                ->add('lastName', TextType::class, array('label' => 'Last Name (Surname)', 'attr' => array('placeholder' => 'Enter Last Name', 'value' => $lastname)))
                ->add('gender', AdminRadioType::class, array(
                        'expanded' => true,
                        'label' => 'Gender',
                        'choices' => array(
                            'Male' => '1',
                            'Female' => '0'
                        ),
                        'data' => $gender
                    )
                )
                ->add('email', EmailType::class, array('label' => 'Email', 'attr' => array('placeholder' => 'Enter Email', 'value' => $email, 'disabled' => $edited, 'read_only' => $email != '' ? true : false)))
                ->add('phone', TextType::class, array('attr' => array('placeholder' => 'Enter Mobile Number','value' => $phoneNumber)))
                ->add('checkAddress', CheckboxType::class,[
                        'label'    => 'Use Company Address',
                        'required' => false,    
                        'attr' => ['checked' => $checkAddress]
                    ])
                ->add('check3rd', CheckboxType::class,[
                    'label'    => 'This entity can be a Secondary Agent. As a secondary agent, this entity can sign up Parkway Pantai doctors under the parkway accredited service, and earn fees from Parkway Doctors. The settings below indicate the fees earned by secondary agents for each type of service.',
                    'required' => false,
                    'attr' => ['checked' => $is3rd]
                ])
                ->add('checkMinFee', CheckboxType::class,[
                    'label'    => 'Yes, minimum fees apply to this agent',
                    'required' => false,
                    'attr' => ['checked' => $enableMin]
                ])
                ->add('addressLine1', TextType::class, array('label' => 'Address Line 1', 'attr' => array('disabled' =>$checkAddress,'placeholder' => 'Enter Address', 'value' => $line1)))
                ->add('addressLine2', TextType::class, array('label' => 'Address Line 2', 'attr' => array('disabled' =>$checkAddress,'placeholder' => 'Enter Address', 'value' => $line2), 'required' => false))
                ->add('addressLine3', TextType::class, array('label' => 'Address Line 3', 'attr' => array('disabled' =>$checkAddress,'placeholder' => 'Enter Address', 'value' => $line3), 'required' => false))
                ->add('country', ChoiceType::class, array('label' => 'Country',
                    'placeholder' => 'Select Country',
                    'attr' => array('disabled' =>$checkAddress),
                    'data' => $countryAgent,
                    'choices' => $countries,
                ))
                ->add('state', ChoiceType::class, array(
                    'label' => 'State / Province',
                    'placeholder' => 'Select State / Province', 
                    'choices' => $listState, 
                    'data' => $stateAgent, 
                    'required' => false,
                    'attr' => ['disabled' =>$checkAddress]
                    ))
                ->add('city', ChoiceType::class, array(
                    'label' => 'City',
                    'data' => $city,
                    'choices' => $cities,
                    'placeholder' => 'Select City',
                    'attr' => array('disabled' =>$checkAddress)))
                ->add('zipCode', TextType::class, array('label' => 'Zip / Postal Code', 'attr' => array('placeholder' => 'Enter Zip / Postal Code', 'value' => $zipCode,'disabled' =>$checkAddress )))
                
                ->add('phoneLocation', ChoiceType::class, array(
                    'label' => 'Mobile Number',
                    'placeholder' => 'Select Country',
                    'choices' => $phoneCountry,
                    'data' => $phoneLocation,
                ))
                
                ->add('comName', TextType::class, array('label' => 'Company Name', 'attr' => array('placeholder' => 'Enter Company Name', 'value' => $company['name'])))
                ->add('registerNo', TextType::class, array('label' => 'Company Registration No', 'attr' => array('placeholder' => 'Enter Company Registration No', 'value' => $company['registrationNo'])))
                ->add('comPhone', TextType::class, array('attr' => array('placeholder' => 'Enter Mobile Number','value' => $company['phone']['number'])))
                ->add('comAddressLine1', TextType::class, array('label' => 'Address Line 1', 'attr' => array('placeholder' => 'Enter Address', 'value' => $company['line1'])))
                ->add('comAddressLine2', TextType::class, array('label' => 'Address Line 2', 'attr' => array('placeholder' => 'Enter Address', 'value' => $company['line2']), 'required' => false))
                ->add('comAddressLine3', TextType::class, array('label' => 'Address Line 3', 'attr' => array('placeholder' => 'Enter Address', 'value' => $company['line3']), 'required' => false))
                
                ->add('comCountry', ChoiceType::class, [
                        'label' => 'Country',
                        'placeholder' => 'Select Country',
                        'data' => $company['country'],
                        'choices' => $countries,
                    ])
                ->add('comState', ChoiceType::class,[
                        'label' => 'State / Province', 
                        'placeholder' => 'Select State / Province', 
                        'choices' => $company['states'], 
                        'data' => $company['state'], 
                        'required' => false
                    ])
                ->add('comCity', ChoiceType::class, [
                        'label' => 'City',
                        'data' => $company['city'],
                        'choices' => $company['cities'],
                        'placeholder' => 'Select City',
                        'attr' => []
                    ])
                ->add('comZipCode', TextType::class, [
                        'label' => 'Zip / Postal Code', 
                        'attr' => [
                            'placeholder' => 'Enter Zip / Postal Code', 
                            'value' => $company['zipCode']
                        ]
                    ])
                ->add('gstSetting', AdminRadioType::class, 
                    array(
                        'expanded' => true,
                        'label'    => 'GST Registered',
                        'choices' => array(
                            'Yes' => '1',
                            'No'  => '0',
                            ),  
                        'data' => $isGst
                        )
                )
                ->add('gstNo', TextType::class, 
                    array('label' => 'GST Registration No.', 
                        'attr' => array('placeholder' => 'Enter Gst Registration No.', 'disabled' => false, 'value' => $gstNo)
                        )
                  )
                ->add('gstEffectDate', TextType::class, 
                    array('label' => 'GST Effective Date', 'attr' => array('placeholder' => 'Enter GST Effective Date', 'value' => $gstDate)
                        )
                )
                ->add('comPhoneLocation', ChoiceType::class, array(
                    'label' => 'Mobile Number',
                    'placeholder' => 'Country',
                    'choices' => $phoneCountry,
                    'data' => $phoneLocation,
                ))
               
                
                ->add('localIdPassport', TextType::class, array('label' => 'Local Identification Card', 'attr' => array('placeholder' => 'Enter Local ID / Passport', 'value' => $passport)))
                ->add('localIdPassportCountry', ChoiceType::class, array('label' => 'Local Identification Card - Country Issue', 'placeholder' => 'Select Country', 'choices' => $countries, 'data' => $passportCountry))
                ->add('bankName', TextType::class, array('label' => 'Bank Name', 'attr' => array('placeholder' => 'Enter Bank Name', 'value' => $bankName)))

                ->add('bankCountryIssue', EntityType::class, array(
                    'label' => 'Bank Country',
                    'class' => Country::class,
                    'choices' => $em->getRepository('UtilBundle:Country')
                        ->getByPreferredCountry(),
                    'choice_label' => 'name',
                    'data'  => $bankCountry,
                    'placeholder'   => 'Select Country'
                ))
                ->add('accountName', TextType::class, array('label' => 'Account Name', 'attr' => array('placeholder' => 'Enter Account Name', 'value' => $accountName)))
                ->add('accountNumber', TextType::class, array('label' => 'Account Number', 'attr' => array('placeholder' => 'Enter Account Number', 'value' => $accountNum)))
                ->add('bankSwiftCode', TextType::class, array('label' => 'Bank Swift Code', 'attr' => array('placeholder' => 'Enter Account Swift Code', 'value' => $bankcode)))
                ->add('logo', FileType::class)
                ->add('fees', CollectionType::class, [
                    'entry_type' => AgentFeeMedicineType::class,
                    'entry_options' => ['label' => false],
                    'data' => $agentFeeMedicine,
                    'label' => false
                ]);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'admin_agent';
    }

}
