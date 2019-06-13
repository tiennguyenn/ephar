<?php

namespace DoctorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use UtilBundle\Utility\Constant;
use UtilBundle\Entity\Country;

class PatientType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['entity_manager'];
        $builder
            ->add('issueCountry', EntityType::class, array(
                'class' => 'UtilBundle:Country',
                'choice_label' => 'name',
                'choices' => $em->getRepository('UtilBundle:Country')->getByPreferredCountry(),
                'attr' => array('class' => 'form-control select2')
            ))
            ->add('useCaregiver', ChoiceType::class, array(
                'choices' => array('1' => 'Yes', '0' => 'No'),
                'expanded' => true
            ))
            ->add('isAssessed', CheckboxType::class, array(
                'label' => 'I confirm this patient has been clinically assessed and diagnosed before, prior to the use of the G-MEDS service.',
                'label_attr' => array('class' => 'bold-medium mt-5 mb-5'),
                'attr' => array('class' => 'icheck')
            ))
            ->add('isEnrolled', CheckboxType::class, array(
                'label' => 'I confirm the clinic has informed and sought consent from the patient (or caregiver) to be enrolled for the G-MEDS service, prior to sending the first electronic prescription. The patient (or caregiver) is aware that he/she will receive emails/SMS from the G-MEDS service.',
                'label_attr' => array('class' => 'bold-medium mt-5 mb-5'),
                'attr' => array('class' => 'icheck')
            ))
            ->add('primaryResidenceCountry', EntityType::class, array(
                'class' => 'UtilBundle:Country',
                'choice_label' => 'name',
                'choices' => $em->getRepository('UtilBundle:Country')->getByPreferredCountry(),
                'attr' => array('class' => 'form-control select2')
            ))
            ->add('nationality', EntityType::class, array(
                'class' => 'UtilBundle:Country',
                'choice_label' => 'name',
                'choices' => $em->getRepository('UtilBundle:Country')->getByPreferredCountry(),
                'attr' => array('class' => 'form-control select2')
            ))
            ->add('taxId', TextType::class, array('attr' => array('class' => 'form-control'),'required' => false))
            ->add('personalInformation', new PersonalInformationType())
            ->add('diagnosis', EntityType::class, array(
                'class' => 'UtilBundle:Diagnosis',
                'choice_label' => 'diagnosis',
                'attr' => array('class' => 'form-control'),
                'label' => false,
                'multiple' => true
            ))
            ->add('allergies', CollectionType::class, array(
                'entry_type' => new PatientMedicationAllergyType(),
                'entry_options' => array('label' => false),
                'allow_add' => true,
                'by_reference' => false,
                'label' => false
            ))
            ->add('caregivers', CollectionType::class, array(
                'entry_type' => new CareGiverType($em),
                'entry_options' => array('label' => false),
                'by_reference' => false,
                'label' => false
            ))
            ->add('phones', CollectionType::class, array(
                'entry_type' => new PhoneType($em),
                'entry_options' => array('label' => false),
                'by_reference' => false,
                'label' => false
            ))
            ->add('isSendMailToCaregiver', CheckboxType::class, array(
                'attr' => array('class' => 'icheck'),
                'required' => false
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UtilBundle\Entity\Patient',
            'attr' => array('id' => 'patientForm', 'class' => 'form-horizontal'),
            'csrf_protection' => false,
            'entity_manager' => null
        ));

        $resolver->setRequired('entity_manager');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'utilbundle_patient';
    }

}
