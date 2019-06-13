<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use UtilBundle\Utility\Constant;

class FeeSettingType extends AbstractType {

    public $initdata;

    public function __construct($options) {
        $this->initdata = $options;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $data = $this->initdata['config'];     
        $fee = $data->getNewFee();
        $date = '';
        $obj = $data->getEffectDate();
        if(!empty($obj))
        {
            $date = $obj->format(Constant::GENERAL_DATE_FORMAT);
        }
   
        $id = $data->getId();
        $builder
                ->add('id', HiddenType::class,array('attr' =>array('value' => $id)))
                ->add('fee', TextType::class, array('label' => 'Set Admin Fee', 'attr' => array('placeholder' => '', 'value' => number_format($fee, 2, '.', ','))))
                ->add('date', TextType::class, array('label' => 'Percentage change will take effect on:', 'attr' => array('placeholder' => '', 'value' => $date)))
                ->add('module', 'hidden', array(
                    'data'     => Constant::PAYMENT_GATEWAY_FEE_MODULE_NAME
                ))
                ->add('title', 'hidden', array(
                    'data'     => isset($options['data']['title']) ? $options['data']['title'] : ''
                ))
               
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
