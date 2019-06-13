<?php

namespace DoctorBundle\Form;

use AdminBundle\Utilities\Constant;
use UtilBundle\Repository\CountryRepository;
use UtilBundle\Entity\Country;
use UtilBundle\Repository\StateRepository;
use UtilBundle\Entity\State;
use UtilBundle\Repository\CityRepository;
use UtilBundle\Entity\City;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->agentPhone = $options['agentPhone'];
        $this->agentPi = $options['agentPi'];
        $this->agentAddress = $options['agentAddress'];
        $this->agentIdentification = $options['agentIdentification'];
        if($this->agentIdentification->getIssueDate() != null){
            $issueDate = new \DateTime($this->agentIdentification->getIssueDate()); 
            $issueDate = $issueDate->format('d M y');
        }else{
            $issueDate = date('d M y');
        }

        $builder
            ->add(
            'first_name',
            TextType::class,
            array(
                'attr' => array(
                    'placeholder' => 'Enter First Name',
                ),
                'data' => $this->agentPi['firstName'] ? $this->agentPi['firstName'] : '',
                'required'  => true,
            ))
            ->add(
            'last_name',
            TextType::class,
            array(
                'attr' => array(
                    'placeholder' => 'Enter Last Name',
                ),
                'data' => $this->agentPi['lastName'] ? $this->agentPi['lastName'] : '',
                'required'  => true,
            ))
            ->add(
            'gender',
            ChoiceType::class,
            array(
                'choices' => array(
                    array(
                        'Male' => 1,
                        'Female' => 0,
                    ),
                ),
                'choices_as_values' => true,'multiple'=>false,'expanded'=>true,
                'data' => $this->agentPi['gender'] ? $this->agentPi['gender'] : 1,
                'required'  => true,
            ))
            ->add(
            'email',
            TextType::class,
            array(
                'attr' => array(
                    'placeholder' => 'Enter Email Address',
                ),
                'data' => $this->agentPi['emailAddress'] ? $this->agentPi['emailAddress'] : '',
                'required'  => true,
            ))
            ->add(
            'country_code',
            EntityType::class, array(
                // query choices from this entity
                'class' => Country::class,
                'query_builder' => function (CountryRepository $er){
                    return $er->createQueryBuilder('u');
                },
                'choice_label' => function ($country) {
                    return $country->getName() . ' (+' . $country->getPhoneCode() . ')';
                },
                'data' => $this->agentPhone->getCountry() ? $this->agentPhone->getCountry() : '',
                'required'  => true,
            ))
            ->add(
            'area_code',
            TextType::class,
            array(
                'attr' => array(
                    'placeholder' => 'Area Code',
                ),
                'data' => $this->agentPhone->getAreaCode() ? $this->agentPhone->getAreaCode() : '',
                'required'  => true,
            ))
            ->add(
            'phone',
            TextType::class,
            array(
                'attr' => array(
                    'placeholder' => 'Enter Phone Number',
                ),
                'data' => $this->agentPhone->getNumber() ? $this->agentPhone->getNumber() : '',
                'required'  => true,
            ))
            ->add(
            'address_line1',
            TextType::class,
            array(
                'attr' => array(
                    'placeholder' => 'Enter Address Line 1',
                ),
                'data' => $this->agentAddress->getLine1() ? $this->agentAddress->getLine1() : '' ,
                'required'  => true,
            ))
            ->add(
            'address_line2',
            TextType::class,
            array(
                'attr' => array(
                    'placeholder' => 'Enter Address Line 2',
                ),
                'data' => $this->agentAddress->getLine2() ? $this->agentAddress->getLine2() : '' ,
                'required'  => false,
            ))
            ->add(
            'address_line3',
            TextType::class,
            array(
                'attr' => array(
                    'placeholder' => 'Enter Address Line 3',
                ),
                'data' => $this->agentAddress->getLine3() ? $this->agentAddress->getLine3() : '' ,
                'required'  => false,
            ))
            ->add(
            'country',
            EntityType::class, array(
                // query choices from this entity
                'class' => Country::class,
                'query_builder' => function (CountryRepository $er){
                    return $er->createQueryBuilder('u');
                },
                'choice_label' => 'name',
                'data' => $this->agentPhone->getCountry() ? $this->agentPhone->getCountry() : 1,
                'required'  => true,
            ))
            ->add(
            'state',
            EntityType::class, array(
                // query choices from this entity
                'class' => State::class,
                'query_builder' => function (StateRepository $er){
                    return $er->createQueryBuilder('u');
                },
                'choice_label' => 'name',
                'required'  => false,
                'data'          => $this->agentAddress->getCity()->getState() ? $this->agentAddress->getCity()->getState() : 1,
            ))
            ->add(
            'city',
            EntityType::class, array(
                // query choices from this entity
                'class' => City::class,
                'query_builder' => function (CityRepository $er){
                    return $er->createQueryBuilder('u');
                },
                'choice_label'  => 'name',
                'data'          => $this->agentAddress->getCity() ? $this->agentAddress->getCity() : 1,
                'required'      => true,
                'choice_attr' => function (City $city, $key, $index) {
                    $attr = [
                        'data-country'  => $city->getCountry() != null ? $city->getCountry()->getId() : null,
                        'data-state'    => $city->getState() != null ? $city->getState()->getId() : null,
                    ];

                    if( ($city->getCountry() != null && $city->getCountry()->getId() != $this->agentPhone->getCountry()->getId()) || ($city->getState() != null && $city->getState()->getId() != $this->agentAddress->getCity()->getState()->getId()) ){
                        $attr['disabled'] = 'disabled'; 
                    }
                    return $attr;
                }
            ))
            ->add(
            'postal_code',
            TextType::class,
            array(
                'attr' => array(
                    'placeholder' => 'Enter Zip / Postal Code',
                ),
                'data' => $this->agentAddress->getPostalCode() ? $this->agentAddress->getPostalCode() : '' ,
                'required'  => true,
            ))
            ->add(
            'passport',
            TextType::class,
            array(
                'attr' => array(
                    'placeholder' => 'Enter Local ID / Passport',
                ),
                'data' => $this->agentPi['passportNo'] ? $this->agentPi['passportNo'] : '',
                'required'  => true,
            ))
            ->add(
            'country_of_issue',
            EntityType::class, array(
                // query choices from this entity
                'class' => Country::class,
                'query_builder' => function (CountryRepository $er){
                    return $er->createQueryBuilder('u');
                },
                'choice_label' => 'name',
                'required'  => true,
            ))
            ->add(
            'date_of_issue',
            TextType::class,
            array(
                'attr' => array(
                    'placeholder' => '',
                ),
                'data' => $issueDate,
                'required'  => true,
            ))
            ->add(
            'image',
            FileType::class,
            array(
                'attr' => array(
                    'placeholder' => '',
                ),
                'data' => $this->agentPi['profilePhotoUrl'] ? $this->agentPi['profilePhotoUrl'] : '',
                'required'  => true,
                'data_class' => null
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'agentPhone' => null,
            'agentPi' => null,
            'agentAddress' => null,
            'agentIdentification' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ProfileBundle_doctor';
    }
}