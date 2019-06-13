<?php

namespace AdminBundle\Form;

use AdminBundle\Utilities\Constant;
use UtilBundle\Repository\CountryRepository;
use UtilBundle\Entity\Country;
use UtilBundle\Repository\StateRepository;
use UtilBundle\Entity\State;
use UtilBundle\Repository\CityRepository;
use UtilBundle\Entity\City;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->data = $options['data'];
        $builder
            ->add(
            'first_name',
            TextType::class,
            array(
                'attr' => array(
                    'placeholder' => 'Enter First Name',
                ),
                'data' => $this->data['firstName'] != null ? $this->data['firstName'] : '',
                'required'  => true,
            ))
            ->add(
            'last_name',
            TextType::class,
            array(
                'attr' => array(
                    'placeholder' => 'Enter Last Name',
                ),
                'data' => $this->data['lastName'] != null ? $this->data['lastName'] : '',
                'required'  => true,
            ))
            ->add(
            'gender',
            ChoiceType::class,
            array(
                'choices' => array(
                    array(
                        'Male' => 1,
                        'Female' => 0,
                    ),
                ),
                'choices_as_values' => true,'multiple'=>false,'expanded'=>true,
                'data' => $this->data['gender'] != null ? $this->data['gender'] : 1,
                'required'  => true,
            ))
            ->add(
            'email',
            TextType::class,
            array(
                'attr' => array(
                    'placeholder' => 'Enter Email Address',
                ),
                'data' => $this->data['emailAddress'] != null ? $this->data['emailAddress'] : '',
                'required'  => true,
            ))
            ->add(
            'image',
            FileType::class,
            array(
                'attr' => array(
                    'placeholder' => '',
                ),
                'data' => $this->data['profilePhotoUrl'] != null ? $this->data['profilePhotoUrl'] : '',
                'required'  => true,
                'data_class' => null
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ProfileBundle_admin';
    }
}