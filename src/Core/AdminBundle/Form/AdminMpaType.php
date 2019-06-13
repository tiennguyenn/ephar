<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use AdminBundle\Form\Type\AdminRadioType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use UtilBundle\Entity\City;
use UtilBundle\Entity\MasterProxyAccount;
use UtilBundle\Entity\Site;
use UtilBundle\Repository\CityRepository;
use UtilBundle\Entity\State;
use UtilBundle\Repository\StateRepository;
use UtilBundle\Entity\Country;
use UtilBundle\Repository\CountryRepository;
use UtilBundle\Utility\Constant;

class AdminMpaType extends AbstractType {

    public $initdata;

    public function __construct($options) {
        $this->initdata = $options;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $em = $this->initdata['entity_manager'];
        $obj = $this->initdata['data'];
        $clinicName = '';
        $familyName = "";
        $giveName = "";
        $email = "";
        $phoneLocation = '';
        $phoneNumber = '';

        $country = $em->getRepository('UtilBundle:Country')->getByPreferredCountry(true);
        $countries = array();
        foreach ($country as $c) {
            $phoneCountry[$c['id']] = $c['name'] . ' (+' . $c['phoneCode'] . ')';
            $countries[$c['id']] = $c['name'];

        }

        $checkAddress = false;
        if (is_object($obj) && !empty($obj->getId())) {
            $clinicName = $obj->getClinicName();
            $familyName = $obj->getFamilyName();
            $giveName = $obj->getGivenName();
            $email =  $obj->getEmailAddress();
            $phoneLocation = $obj->getPhone()->getCountry()->getId();
            $phoneNumber = $obj->getPhone()->getNumber();

        }
        $builder
            ->add('clinicName', TextType::class, array('label' => 'Clinic Name', 'attr' => array('placeholder' => 'Enter Clinic Name', 'value' => $clinicName)))
            ->add('familyName', TextType::class, array('label' => 'Family Name', 'attr' => array('placeholder' => 'Enter Family Name', 'value' => $familyName)))
            ->add('email', EmailType::class, array('label' => 'Email', 'attr' => array('placeholder' => 'Enter Email', 'value' => $email, 'disabled' => !empty($email))))
            ->add('givenName', TextType::class, array('label' => 'Given Name','attr' => array('placeholder' => 'Enter Given Name','value' => $giveName)))
            ->add('profile', FileType::class)
            ->add('phoneLocation', ChoiceType::class, array(
                'label' => 'Mobile Number',
                'placeholder' => 'Select Country',
                'choices' => $countries,
                'data' => $phoneLocation
            ))
            ->add('phone', TextType::class, array('attr' => array('value' => $phoneNumber)))
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
        return 'admin_mpa';
    }

}
