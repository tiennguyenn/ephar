<?php

namespace DoctorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class CareGiverType extends AbstractType
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
            ->add('personalInformation', new PersonalInformationType())
            ->add('relationshipType', EntityType::class, array('class' => 'UtilBundle:RelationshipType', 'choice_label' => 'name', 'attr' => array('class' => 'form-control select2')
            ))
            ->add('phones', CollectionType::class, array(
                'entry_type' => new PhoneType($this->em),
                'entry_options' => array('label' => false),
                'by_reference' => false,
                'label' => false
            ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UtilBundle\Entity\CareGiver',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'utilbundle_caregiver';
    }


}
