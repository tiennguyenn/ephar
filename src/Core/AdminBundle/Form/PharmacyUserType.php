<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AdminBundle\Form\Type\AdminRadioType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use AdminBundle\Libs\Config;
class PharmacyUserType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder->add('firstName',TextType::class,array('label' => 'First Name','attr' => array('placeholder' => 'Enter First Name')))
            ->add('lastName',TextType::class,array('label' => 'Last Name','attr' => array('placeholder' =>'Enter Last Name')))
            ->add('userRole',TextType::class,array('label' => 'User Role','attr' => array('placeholder' =>'Enter User Role')))
            
            
        ;       
       

    }
}