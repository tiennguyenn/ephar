<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AdminBundle\Form\Type\AdminRadioType;
use UtilBundle\Entity\Clinic;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AdminBundle\Libs\Config;
use UtilBundle\Utility\MsgUtils;
class ClinicType extends AbstractType
{
    public $initdata;
    public function __construct($options = array()){
        $this->initdata = $options;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        
        
        $builder  
            ->add('name', TextType::class, array('label' => 'Clinic Name','attr' => array('placeholder' => 'Enter Clinic Name')))
            ->add('gstSetting', AdminRadioType::class, array(
                'expanded' => true,
                'label' => 'GST Rigistered',
                'choices' => array(
                       'Yes' => '1', 
                       'No' => '0',
                       'De-Register' => '2'
                    ),
                )
            )      
            ->add('logo', FileType::class) 
            ->add('email',TextType::class,array('label' => 'Clinic Email','attr' => array('placeholder' => 'Enter Email Address')))
            ->add('telephoneLocation', ChoiceType::class, array(
               
                'label' => 'Clinic Telephone Number',
               
            ))
            ->add('areaCode',TextType::class,array('attr' => array('placeholder' => 'Area Code')))
            ->add('phone',TextType::class,array('attr' => array()))
            ->add('addressLine1',TextType::class,array('label' => 'Address Line 1','attr' => array('placeholder' => 'Enter Address')))
            ->add('addressLine2',TextType::class,array('label' => 'Address Line 2','attr' => array('placeholder' => 'Enter Address'),'required' => false))
            ->add('addressLine3',TextType::class,array('label' => 'Address Line 3','attr' => array('placeholder' => 'Enter Address'),'required' => false))    
            ->add('country',ChoiceType::class,array('label' => 'Country','attr' => array( 'data'=> 'empty')))    
            ->add('state',ChoiceType::class,array('label' => 'State / Province','choices' => array('empty' =>"Select State / Province"),'attr' => array( 'data'=> 'empty'))) 
            ->add('city',ChoiceType::class,array('label' => 'City','choices' => array('empty' =>"Select City"),'attr' => array( 'data'=> 'empty')))    
            ->add('zipCode',TextType::class,array('label' => 'Zip / Postal Code','attr' => array('placeholder' => 'Enter Zip / Postal Code')))            
            ; 
    }
   
     /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Clinic::class
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'admin_clinic';
    }
}