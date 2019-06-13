<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AdminBundle\Form\Type\AdminRadioType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use UtilBundle\Entity\Courier;
use UtilBundle\Utility\Constant;

class DeliveryAdminType extends AbstractType {

    public $initdata;

    public function __construct($options) {
        $this->initdata = $options;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $delivery = $this->initdata['delivery'];
        $country = $this->initdata['depend']['country'];

        $phoneCountry = array();
        $countries = array(); // array('empty' => 'Select Country');
        foreach ($country as $c) {
            $phoneCountry[$c['id']] = $c['name'] . ' (+' . $c['phoneCode'] . ')';
            $countries[$c['id']] = $c['name'];
        }

        $deliveryName = '';
        $businessName = '';
        $email = '';
        $gstNum = '';
        $gst = FALSE;
        $phoneLocation = '';
        $phoneArea = '';
        $phoneNumber = '';
       
        $line1 = '';
        $line2 = '';
        $line3 = '';
        $countryDelivery = '';
        $listState = array();
        $stateDelivery = '';
        $cities = array();
        $city = '';
        $accountName = '';
        $accountNum = '';
        $bankName = '';
        $branchName = '';
        $bankcode = '';
        $bankCountry = '';
        if (is_object($delivery) && $delivery->getId()) {
            $cityRepo = $this->initdata['depend']['cityRepo'];
            $phoneObj = $delivery->getPhone();
            $gstNum = $delivery->getGstNo();
            $gst = $delivery->getIsGst();
            $deliveryName = $delivery->getName();
            $businessName = $delivery->getBusinessRegistrationNumber();
            $email = $delivery->getPersonalInformation()->getEmailAddress();
            $phoneLocation = $phoneObj->getCountry()->getId();
            $phoneArea = $phoneObj->getAreaCode();
            $phoneNumber = $phoneObj->getNumber();
            $address = $delivery->getAddresses()->first();
            $line1 = $address->getLine1();
            $line2 = $address->getLine2();
            $line3 = $address->getLine3();
            $cityObj = $address->getCity();
            $city = $cityObj->getId();
            $countryDelivery = $cityObj->getCountry()->getId();
            $listState = $cityRepo->getStateByCountry($countryDelivery);
           
            $listcityObj = array();
            if (empty($cityObj->getState())) {
                $listcityObj = $cityObj->getCountry()->getCities();
                $stateDelivery = '';
            } else {
                $listcityObj = $cityObj->getState()->getCities();
                $stateDelivery = $cityObj->getState()->getId();
            }
            foreach ($listcityObj as $c) {
                $cities[$c->getId()] = $c->getName();
            }
            $bankAcc = $delivery->getBankAccount();
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
        }

        $builder
                ->add('deliveryName', TextType::class, array('label' => 'Delivery Partner Name', 'attr' => array('placeholder' => 'Enter Delivery Partner Name', 'value' => $deliveryName)))
                ->add('businessName', TextType::class, array('label' => 'Business Registration Number', 'attr' => array('placeholder' => 'Enter Business Registration Number', 'value' => $businessName)))
                ->add('gstSetting', AdminRadioType::class, array(
                    'expanded' => true,
                    'label' => 'GST',
                    'choices' => array(
                        'Yes' => '1',
                        'No' => '0',
                        ),
                    'data' => $gst
                    )
                )
                ->add('gstNum', TextType::class, array('label' => 'GST Registration Number', 'attr' => array('placeholder' => 'Enter GST Registration Number', 'value' => $gstNum)))
                ->add('email', EmailType::class, array('label' => 'Email', 'attr' => array('placeholder' => 'Enter Email', 'value' => $email)))
                ->add('phoneLocation', ChoiceType::class, array(
                    'label' => 'Mobile Number',
                    'placeholder' => 'Select Country',
                    'choices' => $phoneCountry,
                    'data' => $phoneLocation
                ))
                ->add('phone', TextType::class, array('attr' => array('value' => $phoneNumber)))
                ->add('addressLine1', TextType::class, array('label' => 'Address Line 1', 'attr' => array('placeholder' => 'Enter Address', 'value' => $line1)))
                ->add('addressLine2', TextType::class, array('label' => 'Address Line 2', 'attr' => array('placeholder' => 'Enter Address', 'value' => $line2), 'required' => false))
                ->add('addressLine3', TextType::class, array('label' => 'Address Line 3', 'attr' => array('placeholder' => 'Enter Address', 'value' => $line3), 'required' => false))
                ->add('country', ChoiceType::class, array('label' => 'Country',
                    'placeholder' => 'Select Country',
                    'attr' => array(),
                    'data' => $countryDelivery,
                    'choices' => $countries,
                ))
                ->add('state', ChoiceType::class, array('label' => 'State / Province', 'placeholder' => 'Select State / Province', 'choices' => $listState, 'data' => $stateDelivery, 'required' => false))
                ->add('city', ChoiceType::class, array(
                    'label' => 'City',
                    'data' => $city,
                    'choices' => $cities,
                    'placeholder' => 'Select City',
                    'attr' => array()))
                 ->add('bankName', TextType::class, array('label' => 'Bank Name', 'attr' => array('placeholder' => 'Enter Bank Name', 'value' => $bankName)))
                 ->add('bankCountry', ChoiceType::class, array(
                    'label' => 'Bank Country',
                    'data'  => $bankCountry,
                    'choices' => $countries,
                    'placeholder'   => 'Select Country'
                 ))
                ->add('accountName', TextType::class, array('label' => 'Account Name', 'attr' => array('placeholder' => 'Enter Account Name', 'value' => $accountName)))
                ->add('accountNumber', TextType::class, array('label' => 'Account Number', 'attr' => array('placeholder' => 'Enter Account Number', 'value' => $accountNum)))
                ->add('bankSwiftCode', TextType::class, array('label' => 'Bank Swift Code', 'attr' => array('placeholder' => 'Enter Account Swift Code', 'value' => $bankcode)))


        ;
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
