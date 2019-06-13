<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AdminBundle\Form\Type\AdminRadioType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use UtilBundle\Entity\Pharmacy;
use UtilBundle\Utility\Constant;
class PharmacyType extends AbstractType
{
    public $initdata;
    public function __construct($options){
        $this->initdata = $options;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $country = $this->initdata['depend']['country'];
        $pharmacy = $this->initdata['depend']['pharmacy'];
        $phoneCountry =array();
        $countries = array();
        $phonelocation = '';
        foreach($country as $c) {
            $phoneCountry[$c['id']] = $c['name'].'(+'.$c['phoneCode'].')';
            $countries[$c['id']] = $c['name'];
        }    
        $cities = array();
        $states = array();
       
        $name = $pharmacy->getName();
        $code = $pharmacy->getPharmacyCode();
        $shortName = $pharmacy->getShortName();
        $pharmacistName = $pharmacy->getPharmacistName();
        $pharmacistLicense = $pharmacy->getPharmacistLicense();
        $uen = $pharmacy->getUen();
        $permitNumber = $pharmacy->getPermitNumber();
        $bussinessName= $pharmacy->getBusinessName();
        $gst = intval($pharmacy->getIsGst());
        $gstNo = $pharmacy->getGstNo();
        $newGstNo = $pharmacy->getNewGstNo();
        if ($newGstNo) {
            $gstNo = $newGstNo;
        }
        $email = $pharmacy->getEmailAddress();
        
        $country = '';
        $state = '';
        $city = '';
        $phoneLocation = '';
        $phoneArea= '';
        $phone = '';
        $bankCountry = '';
        $bankName = '';
        $branchName = '';
        $accountName = '';
        $accountNum = '';
        $bankcode = '';
        $line1 = '';
        $line2 = '';
        $line3 = '';
        $contactFirstname = '';
        $contactLastname = '';
        if($pharmacy->getId()) {
            $cityRepo = $this->initdata['depend']['cityRepo'];
            $address = $pharmacy->getRegisteredAddress();
            $line1 = $address->getLine1();
            $line2 = $address->getLine2();
            $line3 = $address->getLine3();
            $cityObj = $address->getCity();
            $city = $cityObj->getId();
            $country = $cityObj->getCountry()->getId();
            $states = $cityRepo->getStateByCountry($country);          
            $listcityObj = array();
            
            if (empty($cityObj->getState())) {
                $listcityObj = $cityObj->getCountry()->getCities();
                $state = '';
            } else {
                $listcityObj = $cityObj->getState()->getCities();
                $state = $cityObj->getState()->getId();
            }
            
            foreach ($listcityObj as $c) {
                $cities[$c->getId()] = $c->getName();
            }
            $phoneObj = $pharmacy->getPhones()->first();
            $phonelocation = $phoneObj->getCountry()->getId();
            $phoneArea = $phoneObj->getAreaCode();
            $phone = $phoneObj->getNumber();
            
            $bankAcc = $pharmacy->getBankAccount();
            if(!empty($bankAcc)) {
                $accountName = $bankAcc->getAccountName();
                $accountNum = $bankAcc->getAccountNumber();
                $bank = $bankAcc->getBank();
                if(!empty($bank)) {
                    $bankName = $bank->getName();
                    $bankCountry = $bank->getCountry()->getId();
                    if ($bankCountry && ($bankCountry == Constant::ID_SINGAPORE || $bankCountry == Constant::ID_MALAYSIA)) {
                        $bankName = $bank->getId();
                    }
                    $branchName = $bank->getBranchName();
                    $bankcode = $bank->getSwiftCode();
                }
            }
           $contactFirstname = $pharmacy->getContactFirstname();
           $contactLastname = $pharmacy->getContactLastname();
        }
        $builder
            ->add('pharmacyCode',TextType::class,array('label' => 'Pharmacy Code','attr' => array('placeholder' => 'Pharmacy Code','value' => $code)))
            ->add('pharmacyName',TextType::class,array('label' => 'Pharmacy Name','attr' => array('placeholder' => 'Enter Pharmacy Name','value' => $name)))
            ->add('shortName',TextType::class,array('label' => 'Short Name','attr' => array('placeholder' => 'Enter Short Name','value' => $shortName)))
            ->add('pharmacistName',TextType::class,array('label' => 'Pharmacist Name','attr' => array('placeholder' => 'Enter Pharmacist Name','value' => $pharmacistName)))
            ->add('pharmacistLicense',TextType::class,array('label' => 'Pharmacist License','attr' => array('placeholder' => 'Enter Pharmacist License','value' => $pharmacistLicense)))
            ->add('uen',TextType::class,array('label' => 'UEN','attr' => array('placeholder' => 'UEN','value' => $uen)))
            ->add('permitNumber',TextType::class,array('label' => 'Permit Number','attr' => array('placeholder' => 'Permit Number','value' => $permitNumber), 'required' => false))
            ->add('businessName',TextType::class,array('label' => 'Business Name','attr' => array('placeholder' => 'Enter Business Name','value' => $bussinessName)))
            ->add('gst', AdminRadioType::class, array(
                    'expanded' => true,
                    'label' => 'GST',
                    'choices' => array(
                            'Yes' => '1',
                            'No' => '0',
                    ),
                    'data' => $gst 
                )
            )
            ->add('gstRegisterNumber',TextType::class,array('label' => 'GST Registration Number','attr' => array('placeholder' => 'Enter GST Registration Number', 'value' => $gstNo)))
            ->add('email',EmailType::class,array('label' => 'Email','attr' => array('placeholder' => 'Enter Email', 'value' => $email),'required' => false))
            ->add('phone',TextType::class,array('attr'=>array('value' => $phone)))
            ->add('addressLine1',TextType::class,array('label' => 'Address Line 1','attr' => array('placeholder' => 'Enter Address Line 1','value' => $line1)))
            ->add('addressLine2',TextType::class,array('label' => 'Address Line 2','attr' => array('placeholder' =>'Enter Address Line 2','value' => $line2),'required' => false))
            ->add('addressLine3',TextType::class,array('label' => 'Address Line 3','attr' => array('placeholder' =>'Enter Address Line 3','value' => $line3),'required' => false))
            ->add('city', ChoiceType::class, array(
                    'label' => 'City',
                    'data' => $city,
                    'choices' => $cities,
                    'placeholder' => 'Select City',
                    'attr' => array()))
            ->add('state', ChoiceType::class, array('label' => 'State / Province', 'placeholder' => 'Select State / Province', 'choices' => $states, 'data' => $state, 'required' => false))
                
            ->add('country', ChoiceType::class, array('label' => 'Country',
                    'placeholder' => 'Select Country',
                    'attr' => array(),
                    'data' => $country,
                    'choices' => $countries,
                ))
            ->add('bankName',TextType::class,array('label' => 'Bank Name','attr' => array('placeholder' => 'Enter Bank Name', 'value' => $bankName),'required' => false))
            ->add('bankCountry', ChoiceType::class, array('label' => 'Bank Country',
                    'placeholder' => 'Select Country',
                    'attr' => array(),
                    'required' => FALSE,
                    'data' => $bankCountry,
                    'choices' => $countries,
                ))
            ->add('accountName',TextType::class,array('label' => 'Account Name','attr' => array('placeholder' => 'Enter Account Name', 'value' => $accountName),'required' => false))
            ->add('accountNumber',TextType::class,array('label' => 'Account Number','attr' => array('placeholder' => 'Enter Account Number', 'value' => $accountNum),'required' => false))
            ->add('bankSwiftCode',TextType::class,array('label' => 'Bank Swift Code','attr' => array('placeholder' => 'Enter Bank Swift Code', 'value' => $bankcode),'required' => false))
            ->add('apiToken',TextType::class,array('label' => 'API Token','attr' => array('placeholder' => 'Enter inputAPIToken')))
            ->add('secret',TextType::class,array('label' => 'Secret','attr' => array('placeholder' => 'Enter Secret')))
            ->add('contactFirstname',TextType::class,array('label' => 'Contact First Name','attr' => array('placeholder' => 'Enter contact first name', 'value' => $contactFirstname)))
            ->add('contactLastname',TextType::class,array('label' => 'Contact Last Name','attr' => array('placeholder' => 'Enter contact last name', 'value' => $contactLastname)))
        ;       
       

        $builder->add('phoneLocation', ChoiceType::class, array(
            'choices' => $phoneCountry,
            'label' => 'Mobile Number',
            'placeholder' => 'Select Country',
            'data'=> $phonelocation,
        ));
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
        return 'admin_pharmacy';
    }
}