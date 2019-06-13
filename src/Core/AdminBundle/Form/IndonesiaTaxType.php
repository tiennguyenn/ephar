<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use AdminBundle\Form\Type\AdminRadioType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IndonesiaTaxType extends AbstractType
{
    public function __construct($params = array()) {
        $this->params = $params;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = isset($options['data'])? $options['data']: array();
        foreach($data as $v) {
            $taxName = $v->getTaxName();

            $taxValueNew = $v->getTaxValueNew()? $v->getTaxValueNew() : 0;
            if($taxName == 'insuranceVariable') {
                $taxValueNew = number_format($taxValueNew, 3, '.', ',');
            } else {
                $taxValueNew = number_format($taxValueNew, 2, '.', ',');
            }
            $builder
                ->add($taxName, 'text', array(
                    'required' => true,
                    'trim' => true,
                    'data' => $taxValueNew
                ))
                ->add($taxName.'Date', 'text', array(
                    'required' => true,
                    'trim' => true,
                    'data' =>  $v->getEffectDate()? $v->getEffectDate()->format('d M Y') : null
                ));
        }

    }

    public function getName()
    {
        return 'it';
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