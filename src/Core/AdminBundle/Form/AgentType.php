<?php

namespace AdminBundle\Form;

use AdminBundle\Utilities\Constant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class AgentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
            'first_name',
            TextType::class,
            array(
                'attr' => array(
                    'placeholder' => 'Enter First Name',
                )
            ))
            ->add(
            'last_name',
            TextType::class,
            array(
                'attr' => array(
                    'placeholder' => 'Enter Last Name',
                )
            ))
            ->add(
            'gender',
            Choice::class,
            array(
                'choices' => array(
                    'Main Statuses' => array(
                        'Yes' => 'stock_yes',
                        'No' => 'stock_no',
                    ),
                ),
            ))
            ->add(
            'last_name',
            TextType::class,
            array(
                'attr' => array(
                    'placeholder' => 'Enter Last Name',
                )
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'AdminBundle_agent';
    }
}