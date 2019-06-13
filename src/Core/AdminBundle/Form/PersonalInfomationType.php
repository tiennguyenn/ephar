<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AdminBundle\Form\Type\AdminRadioType;
use UtilBundle\Entity\PersonalInformation;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class PersonalInfomationType extends AbstractType
{
    public $initdata;
    public function __construct($options = array()){
        $this->initdata = $options;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {  
        $builder  
             ->add('firstName',TextType::class,array('label' => 'First Name','attr' => array('placeholder' => 'Enter First Name')))
            ->add('lastName',TextType::class,array('label' => 'Last Name(Surname)','attr' => array('placeholder' => 'Enter Last Name')))
            ->add('gender', AdminRadioType::class, array(
                'expanded' => true,
                'label' => 'Gender',
                'choices' => array(
                       'Male' => '1', 
                       'Female' => '0'
                    ),
                )
            )           
            ->add('email',EmailType::class,array('label' => 'Email','attr' => array('placeholder' => 'Enter Email')))
            
            ; 
    }
   
     /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PersonalInformation::class
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'admin_persionalInfo';
    }
}