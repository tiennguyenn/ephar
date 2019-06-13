<?php 
namespace AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class  AdminRadioType  extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => array(
               'Yes' => '1', 
               'No' => '0'
            ),
            'choices_as_values' => true,
            'label_attr' => array('class'=>'mt-radio mt-radio-outline')
           
        ));
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}