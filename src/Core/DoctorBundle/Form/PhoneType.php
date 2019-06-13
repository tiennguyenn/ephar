<?php

namespace DoctorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use UtilBundle\Utility\Constant;
use UtilBundle\Entity\Country;

class PhoneType extends AbstractType
{
    private $em;

    public function __construct($entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('country', EntityType::class, array(
                'class' => 'UtilBundle:Country',
                'choices' => $this->em->getRepository('UtilBundle:Country')->getByPreferredCountry(),
                'choice_label' => function ($country) {
                    return $country->getName() . ' ' . '(+' . $country->getPhoneCode() . ')';
                },
                'attr' => array('class' => 'form-control select2')
            ))
            ->add('areaCode', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Area Code'
                ),
                'required' => false
            ))
            ->add('number', TextType::class, array(
                'attr' => array('class' => 'form-control')
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UtilBundle\Entity\Phone'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'utilbundle_phone';
    }
}
