<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OthersFeeType extends AbstractType
{
    private $gstCodes = array();
    public function __construct($params = array(), $gstType = 'doctor') {

        $this->params = $params;
        $this->gstCodes = $this->listGstCode($gstType);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('bufferRate', 'text', array(
                    'required' => true,
                    'trim'     => true,
                    'data'     => number_format($options['data']['bufferRate'], 2, '.', ',')
                ))
                ->add('operationsCountryId', 'hidden', array(
                    'required' => true,
                    'data'     => $options['data']['operationsCountryId']
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