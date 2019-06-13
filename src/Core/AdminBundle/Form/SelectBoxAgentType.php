<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SelectBoxAgentType extends AbstractType
{
    public $initdata;
    public function __construct($options){
        $this->initdata = $options;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    
        $agent = $this->initdata['agent'];           
       
        $builder           
            ->add('agentId', ChoiceType::class,array('label' => 'Agent ID','placeholder' => 'Select Agent Name','choices' => $agent))    
           
            ; 
 
       
      
    }
      /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
          //  'data_class' => Doctor::class
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'admin_select_agent';
    }
    
}