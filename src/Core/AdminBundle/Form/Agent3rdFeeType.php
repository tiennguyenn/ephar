<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class Agent3rdFeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = isset($options['data'])? $options['data']: array();

        $builder
            ->add('newAgentFee', 'text', array(
                'required' => true,
                'trim' => true
            ))
            ->add('takeEffectOn', 'text', array(
                'required' => true,
                'trim' => true,
                'data' => isset($data['takeEffectOn'])? $data['takeEffectOn']->format('d M y'): null
            ))
            ->add('areaType', 'hidden');
    }

    public function getName()
    {
        return 'agent_fee_medicine';
    }
}