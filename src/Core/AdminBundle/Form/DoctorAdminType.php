<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AdminBundle\Form\Type\AdminRadioType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use UtilBundle\Entity\Agent;
use UtilBundle\Entity\Doctor;
use UtilBundle\Entity\GstCode;
use UtilBundle\Repository\GstCodeRepository;
use UtilBundle\Entity\City;
use UtilBundle\Repository\CityRepository;
use UtilBundle\Entity\State;
use UtilBundle\Repository\StateRepository;
use UtilBundle\Entity\Country;
use UtilBundle\Repository\CountryRepository;
use UtilBundle\Utility\Constant;

class DoctorAdminType extends AbstractType {

    public $initdata;

    public function __construct($options) {
        $this->initdata = $options;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $agent = $this->initdata['agent'];
        $specality = $this->initdata['specality'];
        $doctor = $this->initdata['doctor'];
        $coutry = $this->initdata['depend']['country'];
        $em = $this->initdata['entity_manager'];
        $phoneCountry = array();
        $countries = array();
        foreach ($coutry as $c) {
            $phoneCountry[$c['id']] = $c['name'] . ' (+' . $c['phoneCode'] . ')';
            $countries[$c['id']] = $c['name'];
        }
        $currentspec = array();
        $title = '';
        $firstname = '';
        $lastname = '';
        $email = '';
        $phoneLocation = '';
        $phoneArea = '';
        $phoneNumber = '';
        $medicalNumber = '';
        $medicalIssueDate = '';
        $passportNum = '';
        $passportdate = '';
        $medicalCountry = '';
        $passportCountry = '';
        $accountName = '';
        $accountNum = '';
        $bankName = '';
        $branchName = '';
        $bankcode = '';
        $gender = '';
        $clinicName = '';
        $clinicEmail = '';
        $clinicSetting = 0;
        $mainZipCode = '';
        $mainCountry = '';
        $rxViewFee = '';
        $rxReviewFeeLocal = '';
        $rxReviewFeeInternational = '';
        $rxFeeLiveConsultLocal = '';
        $rxFeeLiveConsultInternational = '';

        $listRxReviewFeeLocal = Constant::LIST_RX_REVIEW_FEE_LOCAL;
        $listRxReviewFeeInternational = Constant::LIST_RX_REVIEW_FEE_INTERNATIONAL;
        $listRxFeeLiveConsultLocal = Constant::LIST_RX_FEE_LIVE_CONSULT_LOCAL;
        $listRxFeeLiveConsultInternational = Constant::LIST_RX_FEE_LIVE_CONSULT_INTERNATIONAL;

        $cityMainClinic = array();
        $cityMainClinicId = '';
        $agentId = '';
        $mainLine1 = '';
        $mainLine2 = '';
        $mainLine3 = '';
        $mainLocation = '';
        $mainArea = '';
        $mainNumber = '';
        $gstNo = '';
        $gstDate = '';
        $bankCountry = '';
        $bankState = '';
        $bankCity = '';
        $listState = array();
        $mainState = '';
        $gstCode = '';
        $paymentGate = Constant::PAYMENT_GATE_REDDOT; 
        $settingGstReviewLocal = '';
        $settingGstReviewInternational = '';
        $settingGstConsultLocal = '';
        $settingGstConsultInternational = '';
        $settingGstMedicineLocal = '';
        $settingGstMedicineInternational = '';
        $name = '';
        $areas = ['local', 'overseas'];

        $isApply3rdAgent = false;
        $secondaryAgent = $this->initdata['secondaryAgent'];
        $secondaryAgentId = '';
        $isCustomizeMedicineEnabled = true;

        foreach ($areas as $area) {
            foreach ($doctor->getGstSettings() as $item) {
                switch ($item->getFeeType()) {
                    case Constant::SETTING_GST_REVIEW:
                        if($area == $item->getArea()){
                            $settingGstReviewInternational = $item;
                        }else{
                            $settingGstReviewLocal = $item;
                        }
                        break;
                    case Constant::SETTING_GST_CONSULT:
                        if($area == $item->getArea()){
                            $settingGstConsultInternational = $item;
                        }else{
                            $settingGstConsultLocal = $item;
                        }
                        break;
                    case Constant::SETTING_GST_MEDICINE:
                        if($area == $item->getArea()){
                            $settingGstMedicineInternational = $item;
                        }else{
                            $settingGstMedicineLocal = $item;
                        }
                        break;
                }
            }
        }
        if (is_object($doctor) && $doctor->getId()) {
            $spec = $doctor->getMedicalSpecialty();
            $cityRepo = $this->initdata['depend']['cityRepo'];
            foreach ($spec as $item) {
                array_push($currentspec, $item->getId());
            }
            $gstServiceCode = $doctor->getGstServiceCode();
            $title = $doctor->getPersonalInformation()->getTitle();
            $firstname = $doctor->getPersonalInformation()->getFirstName();
            $lastname = $doctor->getPersonalInformation()->getLastName();
            $email = $doctor->getPersonalInformation()->getEmailAddress();
            $name = $doctor->getDisplayName();
            $rxViewFee = $doctor->getRxReviewFee();

            $rxReviewFeeLocal = $doctor->getRxReviewFeeLocal();
            if(!in_array($rxReviewFeeLocal, $listRxReviewFeeLocal) && $rxReviewFeeLocal != ''){
                $rxReviewFeeLocal = 'Other';
            }
            $rxReviewFeeInternational = $doctor->getRxReviewFeeInternational();
            if(!in_array($rxReviewFeeInternational, $listRxReviewFeeInternational) && $rxReviewFeeInternational != ''){
                $rxReviewFeeInternational = 'Other';
            }
            $rxFeeLiveConsultLocal = $doctor->getRxFeeLiveConsultLocal();
            if(!in_array($rxFeeLiveConsultLocal, $listRxFeeLiveConsultLocal) && $rxFeeLiveConsultLocal != ''){
                $rxFeeLiveConsultLocal = 'Other';
            }
            $rxFeeLiveConsultInternational = $doctor->getRxFeeLiveConsultInternational();
            if(!in_array($rxFeeLiveConsultInternational, $listRxFeeLiveConsultInternational) && $rxFeeLiveConsultInternational != ''){
                $rxFeeLiveConsultInternational = 'Other';
            }
            $paymentGate = $doctor->getCurrentPaymentGate();

            $listAgentMaps = $doctor->getAgentDoctors();
            $isApply3rdAgent = boolval($doctor->getIsApply3rdAgent());
            foreach ($listAgentMaps as $it){
                if(!$it->getIsActive () || !empty($it->getDeletedOn()) ){
                    continue;
                }
                if($it->getIsPrimary()){
                    $agentId = $it->getAgent()->getId();
                } else {
                    $secondaryAgentId = $it->getAgent()->getId();
                }
            }
            $isCustomizeMedicineEnabled = boolval($doctor->getIsCustomizeMedicineEnabled());


            $gender = $doctor->getPersonalInformation()->getGender();
            $phoneObj = $doctor->getDoctorPhones()->first()->getContact();
            $phoneLocation = $phoneObj->getCountry()->getId();
            $phoneArea = $phoneObj->getAreaCode();
            $phoneNumber = $phoneObj->getNumber();
            $medicalLicense = $doctor->getMedicalLicense();
            $medicalNumber = $medicalLicense->getRegistrationNumber();
            $medicalIssueDate = $medicalLicense->getIssuingDate()->format('Y');
            $medicalCountry = $medicalLicense->getIssuingCountryId();
            $iden = $doctor->getIdentification()->first();
            $passportNum = $iden->getIdentityNumber();
            if ($iden->getIssueDate()) {
                $passportdate = date('d M y', strtotime($iden->getIssueDate()));
            }
            if ($iden->getIssuingCountryId()) {
                $passportCountry = $iden->getIssuingCountryId();
            }
            $bankA = $doctor->getBankAccount();
            if ($bankA) {
                $accountName = $bankA->getAccountName();
                $accountNum = $bankA->getAccountNumber();
                $bank = $bankA->getBank();
                $bankName = $bank->getName();
                $bankCountry = $bank->getCountry();
                if ($bankCountry && ($bankCountry->getId() == Constant::ID_SINGAPORE || $bankCountry->getId() == Constant::ID_MALAYSIA)) {
                    $bankName = $bank->getId();
                }
                $bankcode = $bank->getSwiftCode();
            }
            $clinics = $doctor->getClinics();
            $clinic = '';
            foreach ($clinics as $cl) {
                if ($cl->getIsPrimary()) {
                    $clinic = $cl;
                    break;
                }
            }
            $gstNo = $doctor->getGstNo();
            if ($doctor->getGstEffectDate()) {
                $gstDate = $doctor->getGstEffectDate()->format('d M y');
            }
            $clinicSetting = $doctor->getIsGst();
            if ($clinic) {
                $clinicName = $clinic->getBusinessName();
                $clinicEmail = $clinic->getEmail();                
                $clinicAdress = $clinic->getBusinessAddress();

                //address
                $mainAddress = $clinicAdress->getAddress();
                $mainZipCode = $mainAddress->getPostalCode();
                $mainLine1 = $mainAddress->getLine1();
                $mainLine2 = $mainAddress->getLine2();
                $mainLine3 = $mainAddress->getLine3();
                $city = $mainAddress->getCity();
                $cityMainClinicId = $city->getId();

                $mainCountry = $city->getCountry()->getId();

                $listState = $cityRepo->getStateByCountry($mainCountry);

                $listCity = array();
                if (empty($city->getState())) {
                    $listCity = $city->getCountry()->getCities();
                    $mainState = '';
                } else {
                    $listCity = $city->getState()->getCities();
                    $mainState = $city->getState()->getId();
                }

                foreach ($listCity as $c) {
                    $cityMainClinic[$c->getId()] = $c->getName();
                }
                $mainPhone = $clinicAdress->getBusinessPhone();
                $mainLocation = $mainPhone->getCountry()->getId();
                $mainArea = $mainPhone->getAreaCode();
                $mainNumber = $mainPhone->getNumber();
            }


        }
        $titleList = array(
            'Dr' => 'Doctor',
            'Prof' => 'Professor',
            'A/Prof' => 'Associate Professor'
        );
        $builder
                ->add('title', ChoiceType::class, array(
                    'label' => 'Doctor\'s Title ',
                    'required' => true,
                    'choices' => $titleList,
                    'data' => $title,
                    'placeholder' => 'Select Title'
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
                ->add('paymentGate', AdminRadioType::class, array(
                        'expanded' => true,
                        'choices' => array(
//                            'MOL Pay' => Constant::PAYMENT_GATE_MOLPAY,
//                            'iPay88' => Constant::PAYMENT_GATE_IPAY,
                            'Reddot' => Constant::PAYMENT_GATE_REDDOT
                        ),
                        'data' => $paymentGate
                    )
                )
                ->add('email', EmailType::class, array('label' => 'Email', 'read_only' => $email != '' ? true : false, 'attr' => array('placeholder' => 'Enter Email Address', 'value' => $email)))
                ->add('displayName', TextType::class, array('label' => 'Doctor Name to appear on the Prescription Advice and Invoice (if none, please skip)','required' => false, 'attr' => array('placeholder' => 'Enter Name', 'value' => $name, 'required' => false)))
                ->add('phone', TextType::class, array('attr' => array('value' => $phoneNumber)))
                ->add('localMedicalLicence', TextType::class, array('label' => 'Medical Licence No', 'attr' => array('placeholder' => 'Medical Licence No', 'value' => $medicalNumber)))
                ->add('localIdPassport', TextType::class, array('label' => 'Local Identity Card', 'attr' => array('placeholder' => 'Enter Local Identity Card', 'value' => $passportNum), 'required' => false))
                // ->add('localIdPassportDate', TextType::class, array('label' => 'Local ID / Passport Date of Issue', 'attr' => array('placeholder' => '', 'value' => $passportdate), 'required' => false))
                ->add('localIdPassportCountry', ChoiceType::class, array(
                    'label' => 'Local Identity Card - Country of Issue ',
                    'placeholder' => 'Select Country',
                    'required' => false,
                    'choices' => $countries,
                    'data' => $passportCountry
                ))
                ->add('localMedicalDate', TextType::class, array('label' => 'Medical Licence No - Year of Issue', 'attr' => array('placeholder' => '', 'value' => $medicalIssueDate)))
                ->add('localMedicalCountry', ChoiceType::class, array(
                    'label' => 'Medical Licence No - Country of Issue',
                    'choices' => $countries,
                    'placeholder' => 'Select Country',
                    'data' => $medicalCountry
                ))
                ->add('bankName', TextType::class, array('label' => 'Bank Name', 'attr' => array('placeholder' => 'Enter Bank Name', 'value' => $bankName)))


                ->add('bankCountryIssue', EntityType::class, array(
                    'label' => 'Bank Country',
                    'class' => Country::class,
                    'choices' => $em->getRepository('UtilBundle:Country')
                        ->getByPreferredCountry(),
                    'choice_label' => 'name',
                    'data'  => $bankCountry,
                    'required'  => true,
                    'placeholder'   => 'Select Country'
                ))


                ->add('accountName', TextType::class, array('label' => 'Account Name', 'attr' => array('placeholder' => 'Enter Account Name', 'value' => $accountName)))
                ->add('accountNumber', TextType::class, array('label' => 'Account Number', 'attr' => array('placeholder' => 'Enter Account Number', 'value' => $accountNum)))
                ->add('bankSwiftCode', TextType::class, array('label' => 'Bank Swift Code', 'attr' => array('placeholder' => 'Enter Account Swift Code', 'value' => $bankcode)))
                ->add('agentId', ChoiceType::class, array(
                    'label' => 'Select Primary Agent',
                    'choices' => $agent,
                    'placeholder' => 'Select Agent Name',
                    'required'  => true,
                    'data' => $agentId
                ))
                ->add('secondaryAgentId', ChoiceType::class, array(
                    'label' => 'Select Secondary Agent',
                    'choices' => $secondaryAgent,
                    'placeholder' => 'Select Agent Name',
                    'data' => $secondaryAgentId,
                    'required'  => true,
                    'attr' => ['disabled' => !$isApply3rdAgent]
                ))
                ->add('check3rd', CheckboxType::class,[
                    'label'    => 'Secondary Agent applies to this doctor.',
                    'required' => false,
                    'attr' => ['checked' => $isApply3rdAgent]
                ])
                ->add('listRxReviewFeeLocal', ChoiceType::class, array(
                    'label' => 'Agent ID',
                    'choices' => $listRxReviewFeeLocal,
                    'data' => $rxReviewFeeLocal != '' ? $rxReviewFeeLocal : '50.00',
                    'required'  => true,
                ))
                ->add('listRxReviewFeeInternational', ChoiceType::class, array(
                    'label' => 'Agent ID',
                    'choices' => $listRxReviewFeeInternational,
                    'data' => $rxReviewFeeInternational != '' ? $rxReviewFeeInternational : '50.00',
                    'required'  => true,
                ))
                ->add('listRxFeeLiveConsultLocal', ChoiceType::class, array(
                    'label' => 'Agent ID',
                    'choices' => $listRxFeeLiveConsultLocal,
                    'data' => $rxFeeLiveConsultLocal != '' ? $rxFeeLiveConsultLocal : '100.00',
                    'required'  => true,
                ))
                ->add('listRxFeeLiveConsultInternational', ChoiceType::class, array(
                    'label' => 'Agent ID',
                    'choices' => $listRxFeeLiveConsultInternational,
                    'data' => $rxFeeLiveConsultInternational != '' ? $rxFeeLiveConsultInternational : '100.00',
                    'required'  => true,
                ))

                ->add('rxReviewFeeLocal', TextType::class, array('label' => 'Prescription Review Fee','required' => false, 'attr' => array('placeholder' => 'Enter Amount'), 'data' => $doctor->getRxReviewFeeLocal()))
                ->add('rxReviewFeeInternational', TextType::class, array('label' => 'Prescription Review Fee' ,'required' => false, 'attr' => array('placeholder' => 'Enter Amount'), 'data' => $doctor->getRxReviewFeeInternational()))
                ->add('rxFeeLiveConsultLocal', TextType::class, array('label' => 'Prescription Review Fee' ,'required' => false, 'attr' => array('placeholder' => 'Enter Amount'), 'data' => $doctor->getRxFeeLiveConsultLocal()))
                ->add('rxFeeLiveConsultInternational', TextType::class, array('label' => 'Prescription Review Fee' ,'required' => false, 'attr' => array('placeholder' => 'Enter Amount'), 'data' => $doctor->getRxFeeLiveConsultInternational()))

                ->add('profile', FileType::class, array('required' => false))
                ->add('signature', FileType::class)
                ->add('mainClinicName', TextType::class, array('label' => 'Clinic Name', 'attr' => array('placeholder' => 'Enter Clinic Name', 'value' => $clinicName)))
                ->add('mainClinicEmail', EmailType::class, array('label' => 'Clinic Email', 'attr' => array('placeholder' => 'Enter Email Address', ' value' => $clinicEmail)))
                ->add('mainClinicTelephoneLocation', ChoiceType::class, array(
                    'label' => 'Clinic Telephone Number',
                    'data' => $mainLocation,
                    'choices' => $phoneCountry,
                    'placeholder' => 'Select Country'
                ))
                ->add('mainClinicPhone', TextType::class, array('attr' => array('value' => $mainNumber)))
                ->add('mainClinicAddressLine1', TextType::class, array('label' => 'Address Line 1', 'attr' => array('placeholder' => 'Enter Address', 'value' => $mainLine1)))
                ->add('mainClinicAddressLine2', TextType::class, array('label' => 'Address Line 2', 'attr' => array('placeholder' => 'Enter Address', 'value' => $mainLine2), 'required' => false))
                ->add('mainClinicAddressLine3', TextType::class, array('label' => 'Address Line 3', 'attr' => array('placeholder' => 'Enter Address', 'value' => $mainLine3), 'required' => false))
                ->add('mainClinicCountry', ChoiceType::class, array(
                    'label' => 'Country',
                    'data' => $mainCountry,
                    'placeholder' => 'Select Country',
                    'choices' => $countries,
                ))
                ->add('mainClinicState', ChoiceType::class, array('label' => 'State / Province', 'placeholder' => 'Select State / Province', 'choices' => $listState, 'data' => $mainState, 'required' => false))
                ->add('mainClinicCity', ChoiceType::class, array('label' => 'City', 'placeholder' => 'Select City', 'data' => $cityMainClinicId, 'choices' => $cityMainClinic))
                ->add('mainClinicZipCode', TextType::class, array('label' => 'Zip / Postal Code', 'attr' => array('placeholder' => 'Enter Zip / Postal Code', 'value' => $mainZipCode)))

                ->add('gstSetting', AdminRadioType::class, array(
                    'expanded' => true,
                    'label' => 'GST Registered',
                    'choices' => array(
                        'Yes' => '1',
                        'No' => '0',
                    ),  
                    'data' => $clinicSetting
                        )
                )
                ->add('applyGstReviewLocal', CheckboxType::class, array(
                        'label' => 'GST Registered',
                        'data' => is_object($settingGstReviewLocal) ? $settingGstReviewLocal->getIsHasGst() : false,
                        'required'  => false
                    )
                )
                ->add('applyGstReviewInternational', CheckboxType::class, array(
                        'label' => 'GST Registered',
                        'data' => is_object($settingGstReviewInternational) ? $settingGstReviewInternational->getIsHasGst() : false,
                        'required'  => false
                    )
                )
                ->add('applyGstConsultLocal', CheckboxType::class, array(
                        'label' => 'GST Registered',
                        'data' => is_object($settingGstConsultLocal) ? $settingGstConsultLocal->getIsHasGst() : false,
                        'required'  => false
                    )
                )
                ->add('applyGstConsultInternational', CheckboxType::class, array(
                        'label' => 'GST Registered',
                        'data' => is_object($settingGstConsultInternational) ? $settingGstConsultInternational->getIsHasGst() : false,
                        'required'  => false
                    )
                )
                ->add('gstCodeReviewLocal',
                EntityType::class, array(
                    // query choices from this entity
                    'class' => GstCode::class,
                    'query_builder' => function (GstCodeRepository $er){
                        return $er->createQueryBuilder('u')
                            ->where('u.id IN (1,2,3,4)');
                    },
                    'choice_label' => function ($gstCode) {
                            return $gstCode->getDescription().' ('.$gstCode->getCode().')';
                    },
                    'data'  => $settingGstReviewLocal != '' ? $settingGstReviewLocal->getNewGst() != null ? $settingGstReviewLocal->getNewGst() : $settingGstReviewLocal->getGst() : '' ,
                    'empty_data'  => null,
                    'required'  => $settingGstReviewLocal != '' && $settingGstReviewLocal->getIsHasGst() ? true : false,
                    'attr'  => array(
                        'placeholder'   => 'Select GST code'
                    )
                ))
                ->add('reviewLocalDate', TextType::class, array('required'  => $settingGstReviewLocal != '' && $settingGstReviewLocal->getIsHasGst() ? true : false, 'label' => 'GST Effective Date', 'attr' => array('placeholder' => 'Enter GST Effective Date', 'value' =>  $settingGstReviewLocal != '' && $settingGstReviewLocal->getEffectiveDate() != null ? $settingGstReviewLocal->getEffectiveDate()->format('d M y') : '',)))
                ->add('gstCodeConsultLocal', 
                EntityType::class, array(
                    // query choices from this entity
                    'class' => GstCode::class,
                    'query_builder' => function (GstCodeRepository $er){
                        return $er->createQueryBuilder('u')
                            ->where('u.id IN (1,2,3,4)');
                    },
                    'choice_label' => function ($gstCode) {
                            return $gstCode->getDescription().' ('.$gstCode->getCode().')';
                    },
                    'data'  => $settingGstConsultLocal != '' ? $settingGstConsultLocal->getNewGst() == null ? $settingGstConsultLocal->getGst() : $settingGstConsultLocal->getNewGst() : '',
                    'empty_data'  => null,
                    'required'  => $settingGstConsultLocal != '' && $settingGstConsultLocal->getIsHasGst() ? true : false,
                    'attr'  => array(
                        'placeholder'   => 'Select GST code'
                    )
                ))
                ->add('consultLocalDate', TextType::class, array('required'  => $settingGstConsultLocal != '' && $settingGstConsultLocal->getIsHasGst() ? true : false, 'label' => 'GST Effective Date', 'attr' => array('placeholder' => 'Enter GST Effective Date', 'value' => $settingGstConsultLocal != '' && $settingGstConsultLocal->getEffectiveDate() != null ? $settingGstConsultLocal->getEffectiveDate()->format('d M y') : '',)))
                ->add('gstCodeMedicineInternational', 
                EntityType::class, array(
                    // query choices from this entity
                    'class' => GstCode::class,
                    'query_builder' => function (GstCodeRepository $er){
                        return $er->createQueryBuilder('u')
                            ->where('u.id IN (1,2)');
                    },
                    'choice_label' => function ($gstCode) {
                        if (Constant::GST_ZRS == $gstCode->getCode()) {
                            $label = 'Follow Export Ruling on GST';
                        } else {
                            $label = 'Based on Medicine GST Code';
                        }

                        return $label;
                    },
                    'data'  => $settingGstMedicineInternational != '' ? $settingGstMedicineInternational->getNewGst() == null ? $settingGstMedicineInternational->getGst() : $settingGstMedicineInternational->getNewGst() : '',
                    'empty_data'  => null,
                    'attr'  => array(
                        'placeholder'   => 'Select GST code'
                    )
                ))
                ->add('medicineInternationalDate', TextType::class, array('label' => 'GST Effective Date', 'attr' => array('placeholder' => 'Enter GST Effective Date', 'value' => $settingGstMedicineInternational != '' && $settingGstMedicineInternational->getEffectiveDate() != null ? $settingGstMedicineInternational->getEffectiveDate()->format('d M y') : '',)))
                ->add('gstCodeReviewInternational', 
                EntityType::class, array(
                    // query choices from this entity
                    'class' => GstCode::class,
                    'query_builder' => function (GstCodeRepository $er){
                        return $er->createQueryBuilder('u')
                            ->where('u.id IN (1,2,3,4)');
                    },
                    'choice_label' => function ($gstCode) {
                            return $gstCode->getDescription().' ('.$gstCode->getCode().')';
                    },
                    'data'  => $settingGstReviewInternational != '' ? $settingGstReviewInternational->getNewGst() == null ? $settingGstReviewInternational->getGst() : $settingGstReviewInternational->getNewGst() : '',
                    'empty_data'  => null,
                    'required'  => $settingGstReviewInternational != '' && $settingGstReviewInternational->getIsHasGst() ? true : false,
                    'attr'  => array(
                        'placeholder'   => 'Select GST code'
                    )
                ))
                ->add('reviewInternationalDate', TextType::class, array('required'  => $settingGstReviewInternational != '' && $settingGstReviewInternational->getIsHasGst() ? true : false, 'label' => 'GST Effective Date', 'attr' => array('placeholder' => 'Enter GST Effective Date', 'value' => $settingGstReviewInternational != '' && $settingGstReviewInternational->getEffectiveDate() != null ? $settingGstReviewInternational->getEffectiveDate()->format('d M y') : '',)))
                ->add('gstCodeConsultInternational', 
                EntityType::class, array(
                    // query choices from this entity
                    'class' => GstCode::class,
                    'query_builder' => function (GstCodeRepository $er){
                        return $er->createQueryBuilder('u')
                            ->where('u.id IN (1,2,3,4)');
                    },
                    'choice_label' => function ($gstCode) {
                            return $gstCode->getDescription().' ('.$gstCode->getCode().')';
                    },
                    'data'  => $settingGstConsultInternational != '' ? $settingGstConsultInternational->getNewGst() == null ? $settingGstConsultInternational->getGst() : $settingGstConsultInternational->getNewGst() : '',
                    'empty_data'  => null,
                    'required'  => $settingGstConsultInternational != '' && $settingGstConsultInternational->getIsHasGst() ? true : false,
                    'attr'  => array(
                        'placeholder'   => 'Select GST code'
                    )
                ))
                ->add('consultInternationalDate', TextType::class, array('required'  => $settingGstConsultInternational != '' && $settingGstConsultInternational->getIsHasGst() ? true : false, 'label' => 'GST Effective Date', 'attr' => array('placeholder' => 'Enter GST Effective Date', 'value' => $settingGstConsultInternational != '' && $settingGstConsultInternational->getEffectiveDate() != null ? $settingGstConsultInternational->getEffectiveDate()->format('d M y') : '')))

                ->add('mainClinicGstNo', TextType::class, array('label' => 'GST Registration No.', 'attr' => array('placeholder' => 'Enter Gst Registration No.', 'disabled' => false, 'value' => $gstNo)))
                ->add('mainClinicGstDate', TextType::class, array('label' => 'GST Effective Date', 'attr' => array('placeholder' => 'Enter GST Effective Date', 'value' => $gstDate)))
                ->add('mainClinicLogo', FileType::class,array('attr' => ['class'=>'logo-clinic']));
        $builder->add('phoneLocation', ChoiceType::class, array(
            'label' => 'Mobile Number',
            'choices' => $phoneCountry,
            'data' => $phoneLocation,
            'placeholder' => 'Select Country',
        ));
        $builder->add('specialization', ChoiceType::class, array(
            'choices' => $specality,
            'label' => "Doctor's Specialization",
            'multiple' => true,
            'data' => $currentspec
        ));
        $builder->add('isCustomizeMedicineEnabled', CheckboxType::class,[
            'label'    => 'Enable custom medicine selling prices for this doctor.',
            'required' => false,
            'attr' => ['checked' => $isCustomizeMedicineEnabled],
        ]);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'em' => null
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'admin_doctor';
    }

}
