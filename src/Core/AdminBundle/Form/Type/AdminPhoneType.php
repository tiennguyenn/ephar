<?php

namespace Bris\AspBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Bris\AspBundle\Form\Transformer\TelTransformer;
use Bris\AspBundle\Libs\Config;

class AdminPhoneType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('first', 'text')
      ->add('middle', 'text');
  }


  public function getName()
  {
    return 'phone';
  }
}