<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class RxReminderSettingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('durationTime')
            ->add('expiredTime')
            ->add('timeUnit', ChoiceType::class, array(
                'choices' => array('hour' => 'Hours', 'day' => 'Days', 'month' => 'Months')
            ))
            ->add('timeUnitExpire', ChoiceType::class, array(
                'choices' => array('hour' => 'Hours', 'day' => 'Days', 'month' => 'Months')
            ))
            ->add('templateSubjectEmail')
            ->add('templateBodyEmail', TextareaType::class)
            ->add('templateSms', TextareaType::class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UtilBundle\Entity\RxReminderSetting',
            'label' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'utilbundle_rxremindersetting';
    }


}
