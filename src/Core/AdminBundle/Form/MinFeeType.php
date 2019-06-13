<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MinFeeType extends AbstractType
{
    private $gstCodes = array();
    public function __construct($params = array()) {

        $this->params = $params;

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('feeLocal', 'text', array(
                    'required' => true,
                    'trim'     => true,
                    'data'     => number_format($options['data']['local'], 2, '.', ',')
                ))
                ->add('feeIndo', 'text', array(
                    'required' => true,
                    'trim'     => true,
                    'data'     => number_format($options['data']['feeIndo'], 2, '.', ',')
                ))

                ->add('feeEastMalay', 'text', array(
                    'required' => true,
                    'trim'     => true,
                    'data'     => number_format($options['data']['feeEastMalay'], 2, '.', ',')
                ))
                ->add('feeWestMalay', 'text', array(
                    'required' => true,
                    'trim'     => true,
                    'data'     => number_format($options['data']['feeWestMalay'], 2, '.', ',')
                ))
                ->add('feeInternational', 'text', array(
                    'required' => true,
                    'trim'     => true,
                    'data'     => number_format($options['data']['feeInternational'], 2, '.', ',')
                ));

    }

    private function listGstCode($type)
    {
        $list = array();
        return $list;
    }

    public function getName()
    {
        return 'others_fee';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false
        ));
    }
}