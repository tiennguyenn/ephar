<?php

namespace DoctorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;

class PersonalInformationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', ChoiceType::class, array(
                'choices' => array(
                    'Mr' => 'Mr.',
                    'Ms' => 'Ms.',
                    'Mrs' => 'Mrs.',
                    'Mdm' => 'Mdm.',
                    'Dr' => 'Dr.',
                    'Prof' => 'Prof.'
                ),
                'attr' => array('class' => 'form-control select2')
            ))
            ->add('firstName', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('lastName', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('passportNo', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('emailAddress', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('dateOfBirth', BirthdayType::class, array(
                'format' => 'ddMMyyyy',
                'placeholder' => array(
                    'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
                ),
                'years' => range(1918, date('Y')),
            ))
            ->add('gender', ChoiceType::class, array(
                'choices' => array('1' => 'Male', '0' => 'Female'),
                'expanded' => true
            ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UtilBundle\Entity\PersonalInformation',
            'attr' => array('class' => 'form-control')
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'utilbundle_personalinformation';
    }


}
