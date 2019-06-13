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

class AgentLoginAdminType extends AbstractType {

	public $initdata;
	
    public function __construct($options) {
		$this->initdata = $options;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
		$agentLogin = $this->initdata['agent_login'];
		$user = $agentLogin ? $agentLogin->getUser() : null;
		$firstName = $user ? $user->getFirstName() : null;
		$lastName = $user ? $user->getLastName() : null;
		$email = $user ? $user->getEmailAddress() : null;
		$gender = $user ? $user->getGender() : null;
		$privilege = $agentLogin ? $agentLogin->getPrivilege() : array();

        $builder
                ->add('firstName', TextType::class, array(
						'label' => 'First Name', 
						'required' => true,
						'attr' => array('placeholder' => 'Enter First Name'),
						'data' => $firstName,
						'constraints' => new NotBlank(array('message' => 'This field is required.'))
					)
				)
                ->add('lastName', TextType::class, array(
						'label' => 'Last Name (Surname)', 
						'required' => true,
						'attr' => array('placeholder' => 'Enter Last Name'),
						'data' => $lastName,
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
						'attr' => array('placeholder' => 'Enter Email', 'readonly' => $email != '' ? true : false),
						'data' => $email,
						'constraints' => array(new NotBlank(array('message' => 'This field is required.')), new Email(array('message' => 'Please enter a valid email address.')))
					)
				)
                ->add('privilege', AdminCheckListType::class, array(
						'expanded' => true,
						'label' => 'Privilege',
						'required' => true,
						'choices' => array(
							'My Doctors' => array(
								'doctors' => 'List Doctors'
							),
							'Reports' => array(
								'sales_report' => 'Sales Report',
								'monthly_statement' => 'Monthly Statement'
							)
						),
						'data' => $privilege,
						'constraints' => array(new NotBlank(array('message' => 'This field is required.')))
					)
				)
                ->add('photo', FileType::class)
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
        return 'admin_agent_login';
    }

}
