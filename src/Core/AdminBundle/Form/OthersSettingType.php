<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OthersSettingType extends AbstractType
{
    private $gstCodes = array();
    public function __construct($params = array(), $gstType = 'doctor') {

        $this->params = $params;
        $this->gstCodes = $this->listGstCode($gstType);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('scheduleDeclarationTime', 'number', array(
                    'required' => true,
                    'trim'     => true,
                    'attr'     => [
                        'min' => 0,
                        'max' => 999
                    ],
                    'data'     => $options['data']['scheduleDeclarationTime']
                ))
                ->add('operationsCountryId', 'hidden', array(
                    'required' => true,
                    'data'     => $options['data']['operationsCountryId']
                ))
                ->add('save', SubmitType::class, array('label' => 'Save'));
    }

    private function listGstCode($type)
    {
        $list = array();
        return $list;
    }

    public function getName()
    {
        return 'others_setting';
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