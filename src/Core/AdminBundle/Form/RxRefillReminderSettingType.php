<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RxRefillReminderSettingType extends AbstractType {

    public function __construct($params = array(), $form = null) {
        $this->params = $params;
		$this->form = $form;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        if ($this->form == 'formreminderthirty') {
            $builder
                    ->add('reminderthirtydays', TextType::class, array(
                        'data' => $this->params['reminderRxRefill30']
                        , 'trim' => true
                        , 'required' => false))
                    ->add('savethirtydays', SubmitType::class, array('label' => 'Save'));
        } elseif ($this->form == 'formremindersixty') {
            $builder
                    ->add('remindersixtydays', TextType::class, array(
                        'data' => $this->params['reminderRxRefill60']
                        , 'trim' => true
                        , 'required' => false))
                    ->add('savesixtydays', SubmitType::class, array('label' => 'Save'));
        } elseif ($this->form == 'formreminderemails') {
            $builder
                    ->add('first_patient_days', 'number', array(
                        'data' => $this->params['first_patient']['days']
                        , 'trim' => true
                        , 'required' => false))
                    ->add('first_patient_subject', TextType::class, array(
                        'data' => $this->params['first_patient']['subject']
                        , 'trim' => true
                        , 'required' => false))
                    ->add('first_patient_email', TextareaType::class, array(
                        'data' => $this->params['first_patient']['email']
                        , 'trim' => true
                        , 'required' => false))
                    ->add('first_patient_sms', TextType::class, array(
                        'data' => $this->params['first_patient']['sms']
                        , 'trim' => true
                        , 'required' => false))
                    ->add('first_doctor_days', 'number', array(
                        'data' => $this->params['first_doctor']['days']
                        , 'trim' => true
                        , 'required' => false))
                    ->add('first_doctor_subject', TextType::class, array(
                        'data' => $this->params['first_doctor']['subject']
                        , 'trim' => true
                        , 'required' => false))
                    ->add('first_doctor_email', TextareaType::class, array(
                        'data' => $this->params['first_doctor']['email']
                        , 'trim' => true
                        , 'required' => false))
                    ->add('second_patient_days', 'number', array(
                        'data' => $this->params['second_patient']['days']
                        , 'trim' => true
                        , 'required' => false))
                    ->add('second_patient_subject', TextType::class, array(
                        'data' => $this->params['second_patient']['subject']
                        , 'trim' => true
                        , 'required' => false))
                    ->add('second_patient_email', TextareaType::class, array(
                        'data' => $this->params['second_patient']['email']
                        , 'trim' => true
                        , 'required' => false))
                    ->add('second_patient_sms', TextType::class, array(
                        'data' => $this->params['second_patient']['sms']
                        , 'trim' => true
                        , 'required' => false))
                    ->add('second_doctor_days', 'number', array(
                        'data' => $this->params['second_doctor']['days']
                        , 'trim' => true
                        , 'required' => false))
                    ->add('second_doctor_subject', TextType::class, array(
                        'data' => $this->params['second_doctor']['subject']
                        , 'trim' => true
                        , 'required' => false))
                    ->add('second_doctor_email', TextareaType::class, array(
                        'data' => $this->params['second_doctor']['email']
                        , 'trim' => true
                        , 'required' => false))
					->add('reminderemails', SubmitType::class, array('label' => 'Save'));
		}
    }

}
