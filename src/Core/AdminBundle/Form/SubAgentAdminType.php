<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AdminBundle\Form\Type\AdminRadioType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AdminBundle\Libs\Config;
use UtilBundle\Utility\MsgUtils;
use UtilBundle\Utility\Constant;

class SubAgentAdminType extends AbstractType {

    public $initdata;

    public function __construct($options) {
        $this->initdata = $options;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $agent = $this->initdata['agent'];
        $country = $this->initdata['depend']['country'];

        $phoneCountry = array();
        $countries = array();
        foreach ($country as $c) {
            $phoneCountry[$c['id']] = $c['name'] . ' (+' . $c['phoneCode'] . ')';
            $countries[$c['id']] = $c['name'];
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

        if (is_object($agent) && $agent->getId()) {
            $cityRepo = $this->initdata['depend']['cityRepo'];
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
            $passport = $iden->getIdentityNumber();
            $passportCountry = $iden->getIssuingCountryId();
            $passportDate = date('d M y', strtotime($iden->getIssueDate()));
        }
        $builder
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
                ->add('email', EmailType::class, array('label' => 'Email', 'attr' => array('placeholder' => 'Enter Email', 'value' => $email,'readonly' => $email != '' ? true : false)))
                ->add('phone', TextType::class, array('attr' => array('value' => $phoneNumber)))
                ->add('localIdPassport', TextType::class, array('label' => 'Local Identity Card', 'attr' => array('placeholder' => 'Enter Local Identity Card', 'value' => $passport)))
              
                ->add('localIdPassportCountry', ChoiceType::class, array('label' => 'Local Identity Card - Country of Issue', 'placeholder' => 'Select Country', 'choices' => $countries, 'data' => $passportCountry))
                ->add('addressLine1', TextType::class, array('label' => 'Address Line 1', 'attr' => array('placeholder' => 'Enter Address', 'value' => $line1)))
                ->add('addressLine2', TextType::class, array('label' => 'Address Line 2', 'attr' => array('placeholder' => 'Enter Address', 'value' => $line2), 'required' => false))
                ->add('addressLine3', TextType::class, array('label' => 'Address Line 3', 'attr' => array('placeholder' => 'Enter Address', 'value' => $line3), 'required' => false))
                ->add('country', ChoiceType::class, array('label' => 'Country',
                    'placeholder' => 'Select Country',
                    'attr' => array(),
                    'data' => $countryAgent,
                    'choices' => $countries,
                ))
                ->add('state', ChoiceType::class, array('label' => 'State / Province', 'placeholder' => 'Select State / Province', 'choices' => $listState, 'data' => $stateAgent, 'required' => false))
                ->add('city', ChoiceType::class, array(
                    'label' => 'City',
                    'data' => $city,
                    'choices' => $cities,
                    'placeholder' => 'Select City',
                    'attr' => array()))
                ->add('zipCode', TextType::class, array('label' => 'Zip / Postal Code', 'attr' => array('placeholder' => 'Enter Zip / Postal Code', 'value' => $zipCode)))
                ->add('phoneLocation', ChoiceType::class, array(
                    'label' => 'Mobile Number',
                    'placeholder' => 'Select Country',
                    'choices' => $phoneCountry,
                    'data' => $phoneLocation,
                ))
                ->add('logo', FileType::class)
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
