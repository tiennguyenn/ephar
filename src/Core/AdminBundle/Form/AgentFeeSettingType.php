<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use UtilBundle\Utility\Constant;

class AgentFeeSettingType extends AbstractType {

    public $initdata;

    public function __construct() {

    }

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $data = $options['data']['fee'];


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
                ->add('date', TextType::class, array('label' => 'Fee will take effect on:', 'attr' => array('placeholder' => '', 'value' => $date)))
                ->add('value', TextType::class, array('label' => 'Agemt', 'attr' => array('placeholder' => '', 'value' => number_format($fee, 2, '.', ','))))

               
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
        return 'admin_agent_fee';
    }

}
