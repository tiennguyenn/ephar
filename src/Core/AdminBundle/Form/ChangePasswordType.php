<?php

namespace AdminBundle\Form;

use AdminBundle\Utilities\Constant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ChangePasswordType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
            'new_password',
            PasswordType::class,
            array(
                'attr' => array(
                    'placeholder' => 'Enter New Password',
                )
            ))
            ->add(
            'confirm_password',
            PasswordType::class,
            array(
                'attr' => array(
                    'placeholder' => 'Re-type New Password',
                )
            ));
    }

    /**ll
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ChangePasswordBundle_public';
    }
}