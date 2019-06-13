<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AdminBundle\Form\Type\AdminRadioType;
use AdminBundle\Form\Type\AdminCheckListType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class DoctorLoginAdminType extends AbstractType {

	public $initdata;
	
    public function __construct($options) {
		$this->initdata = $options;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
		$doctorLogin = $this->initdata['doctor_login'];
		$country = $this->initdata['phone_country'];
        $phoneCountry = array();
        foreach ($country as $c) {
            $phoneCountry[$c['id']] = $c['name'] . ' (+' . $c['phoneCode'] . ')';
        }

        $phoneLocation = null;
        $phoneNumber = null;
        $phoneArea = null;
        $contact = $doctorLogin ? $doctorLogin->getContact() : null;
        if ($contact) {
            $phoneLocation = $contact->getCountry()->getId();
            $phoneNumber = $contact->getNumber();
            $phoneArea = $contact->getAreaCode();
        }
        $user = $doctorLogin ? $doctorLogin->getUser() : null;
		$firstName = $user ? $user->getFirstName() : null;
		$lastName = $user ? $user->getLastName() : null;
		$email = $user ? $user->getEmailAddress() : null;
		$gender = $user ? $user->getGender() : null;
		$privilege = $doctorLogin ? $doctorLogin->getPrivilege() : array();
        $builder->add('firstName', TextType::class, array(
						'label' => 'First Name',
						'required' => true,
						'attr' => array('placeholder' => 'Enter First Name', 'value' => $firstName),
						'constraints' => new NotBlank(array('message' => 'This field is required.'))
					)
				)
                ->add('lastName', TextType::class, array(
						'label' => 'Last Name (Surname)',
						'required' => true,
						'attr' => array('placeholder' => 'Enter Last Name', 'value' => $lastName),
						'constraints' => new NotBlank(array('message' => 'This field is required.'))
					)
				)
                ->add('gender', AdminRadioType::class, array(
						'expanded' => true,
						'label' => 'Gender',
						'required' => true,
						'choices' => array(
							'Male' => '1',
							'Female' => '0'
						),
						'constraints' => new NotBlank(array('message' => 'This field is required.')),
						'data' => $gender
                    )
                )
                ->add('email', EmailType::class, array(
						'label' => 'Email',
						'required' => true,
						'attr' => array('placeholder' => 'Enter Email', 'value' => $email, 'readonly' => $email != '' ? true : false),
						'constraints' => array(new NotBlank(array('message' => 'This field is required.')), new Email(array('message' => 'Please enter a valid email address.')))
					)
				)
                ->add('privilege', AdminCheckListType::class, array(
						'expanded' => true,
						'label' => 'Privilege',
						'required' => true,
						'choices' => array(
							'My Patients' => array(
								'patient_index' => 'List Patients',
								'patient_new' => 'Register New Patient',
								//'patient_edit' => 'Edit Patient',
								//'patient_delete' => 'Delete Patient'
							),
							'My Rx' => array(
								'index_rx,create_rx' => 'Create New Rx',
								'list_rx' => 'List Rx',
								'list_draft_rx' => 'Draft Rx Orders',
								'list_pending_rx' => 'Pending Rx Orders',
								'list_confirmed_rx' => 'Confirmed Rx Orders',
								'list_recalled_rx' => 'Recalled Rx Orders',
								'list_failed_rx' => 'Failed Rx Orders',
								'list_reported_rx' => 'Reported Issues',
								//'review_rx' => 'View Rx'
							),
							'Reports' => array(
								'doctor_report_transaction_history' => 'Rx Transaction History',
								'doctor_report_monthly_statement' => 'Monthly Statements'
							)
						),
						'data' => $privilege,
						'constraints' => array(new NotBlank(array('message' => 'This field is required.')))
					)
				)
                ->add('photo', FileType::class)
                ->add('phoneLocation', ChoiceType::class, array(
                    'label' => 'Mobile Number',
                    'choices' => $phoneCountry,
                    'data' => $phoneLocation,
                    'placeholder' => 'Select Country',
                ))
                ->add('phone', TextType::class, array('attr' => array('value' => $phoneNumber)))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'admin_doctor_login';
    }

}
