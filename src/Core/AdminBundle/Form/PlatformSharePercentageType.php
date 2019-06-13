<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints\DateTime;
use UtilBundle\Utility\Constant;

class PlatformSharePercentageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $data = isset($options['data'])? $options['data']: array();
        $flagPlatform = isset($options['data']['flag']) ? $options['data']['flag'] : 0;

        $builder
            ->add('platformPercentage', 'text', array(
                'required' => true,
                'trim'     => true,
                'data'     => number_format(isset($data['newPlatformPercentage']) ? $data['newPlatformPercentage'] : 0, 2, '.', ','),
            ))
            ->add('agentPercentage', 'text', array(
                'required' => true,
                'trim'     => true,
                'data'     => number_format(isset($data['newAgentPercentage']) ? $data['newAgentPercentage'] : 0, 2, '.', ',')
            ))
            ->add('doctorPercentage', 'text', array(
                'required' => true,
                'trim'     => true,
                'data'     => number_format(isset($data['newDoctorPercentage']) ? $data['newDoctorPercentage'] : 0, 2, '.', ','),
            ))
            ->add('totalPercentage', 'text', array(
                'read_only' => true,
                'data'     => number_format(100, 2, '.', ',')
            ))
            ->add('takeEffectOn', 'text', array(
                'required' => true,
                'trim'     => true,
                'data'     => isset($data['takeEffectOn'])? $data['takeEffectOn']->format('d M y'): null
            ))
            ->add('areaType', 'hidden', array(
                'data'     => isset($data['areaType']) ? $data['areaType'] : null
            ))
            ->add('marginShareType', 'hidden', array(
                'data'     => isset($data['marginShareType']) ? $data['marginShareType'] : null
            ))
            ->add('title', 'hidden', array(
                'data'     => isset($data['title']) ? $data['title'] : ''
            ))
            ->add('id', 'hidden', array(
                'data'     => isset($data['id']) ? $data['id'] : null
            ))
            ->add('module', 'hidden', array(
                    'data'     => Constant::GROSS_MARGIN_SHARE_MODULE_NAME
                     ));
    }

    public function getName()
    {
        return 'ps_percentage';
    }
}