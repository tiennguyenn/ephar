<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AdminBundle\Libs\Config;
use UtilBundle\Utility\MsgUtils;
class DoctorPhoneType extends AbstractType
{
    public $initdata;
    public function __construct($options = array()){
        $this->initdata = $options;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      
        $builder  
            ->add('phoneArea',TextType::class,array('attr'=>array('placeholder'=>'Area Code')))
            ->add('phone')
            ->add('phoneLocation', ChoiceType::class, array(                
                'label' => 'Mobile Number',               
                'data'=> 65
            ))
            ; 
    }
   
     /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => \UtilBundle\Entity\DoctorPhone::class
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'admin_doctor_phone';
    }
}