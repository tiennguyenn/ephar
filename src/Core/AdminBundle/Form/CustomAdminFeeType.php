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


class CustomAdminFeeType extends AbstractType {

    public $initdata;

    public function __construct($options) {
        $this->initdata = $options;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $data = $this->initdata['config'];
     
        $fee1 = $data['fee1'];
        $fee2 = $data['fee2'];
        $date1 = $data['date1'];
        $date2 = $data['date2'];

        
        $builder
                ->add('fee1', TextType::class, array('label' => 'Set Admin Fee', 'attr' => array('placeholder' => '', 'value' => number_format($fee1, 2, '.', ','))))
                ->add('fee2', TextType::class, array('label' => 'Set Admin Fee', 'attr' => array('placeholder' => '', 'value' => number_format($fee2, 2, '.', ','))))               
                ->add('date1', TextType::class, array('label' => 'Percentage change will take effect on:', 'attr' => array('placeholder' => '', 'value' => $date1)))
                ->add('date2', TextType::class, array('label' => 'Percentage change will take effect on:', 'attr' => array('placeholder' => '', 'value' => $date2)))
               
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
        return 'admin_fee';
    }

}
