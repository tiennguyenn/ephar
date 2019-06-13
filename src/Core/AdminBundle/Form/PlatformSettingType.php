<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use AdminBundle\Form\Type\AdminRadioType;
use Symfony\Component\OptionsResolver\OptionsResolver;
class PlatformSettingType extends AbstractType
{
    public function __construct($params = array()) {
        $this->params = $params;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = $options['data'];
        if($data['formType'] == 'product_margin') {
            $builder
                ->add('local', 'text', array(
                    'required' => true,
                    'trim'     => true,
                    'data'     => number_format(isset($data['local']) ? $data['local'] : 0, 2, '.', ','),
                ))
                ->add('overseas', 'text', array(
                    'required' => true,
                    'trim'     => true,
                    'data'     => number_format(isset($data['overseas']) ? $data['overseas'] : 0, 2, '.', ',')
                ));
        }
        if($data['formType'] == 'gst_rate') {
            $gstRateAffectDate = !empty($data['gstRateAffectDate'])? $data['gstRateAffectDate']->format('d M y'): null;
            $gstAffectDate = !empty($data['gstAffectDate'])? $data['gstAffectDate']->format('d M y'): null;
            $builder
                ->add('newGstRate', 'text', array(
                    'required' => true,
                    'trim'     => true,
                    'data'     => number_format(isset($data['newGstRate']) ? $data['newGstRate'] : 0, 2, '.', ','),
                ))
                ->add('gstRateAffectDate', 'text', array(
                    'required' => false,
                    'trim'     => true,
                    'data'     => $gstRateAffectDate
                ))
                ->add('gstAffectDate', TextType::class, array(
                    'required' => false,
                    'trim'     => true,
                    'data'     => $gstAffectDate
                ))
                ->add('gstNo', TextType::class, array(
                    'required' => false,
                    'trim'     => true,
                    'data'     => $data['gstNo']
                ))
                ->add('isGst', AdminRadioType::class, array(
                    'expanded' => true,
                    'label' => 'GST Rigistered',
                    'choices' => array(
                           'Yes' => '1', 
                           'No' => '0'
                        ),
                    )
                );
        }
        if($data['formType'] == 'schedule') {
            $builder
                ->add('agentStatementDate', 'text', array(
                    'required' => true,
                    'trim'     => true,
                    'max_length' => 2,
                    'data'     => isset($data['agentStatementDate']) ? $data['agentStatementDate'] : null,
                ))
                ->add('doctorStatementDate', 'text', array(
                    'required' => true,
                    'trim'     => true,
                    'max_length' => 2,
                    'data'     => isset($data['doctorStatementDate']) ? $data['doctorStatementDate'] : null
                ))
                ->add('pharmacyWeeklyPoDay', 'choice', array(
                    'required'    => true,
                    'choices'     => $this->params['dayOfWeekList'],
                    'data'        => isset($data['pharmacyWeeklyPoDay']) ? $data['pharmacyWeeklyPoDay'] : null
                ))
                ->add('pharmacyWeeklyPoTime', 'choice', array(
                    'required'    => true,
                    'choices'     => $this->params['timeSlotList'],
                    'data'        => isset($data['pharmacyWeeklyPoTime']) ? $data['pharmacyWeeklyPoTime'] : null
                ))
                ->add('deliveryFortnightlyPoDay', 'choice', array(
                    'required'    => true,
                    'choices'     => $this->params['dayOfWeekList'],
                    'data'        => isset($data['deliveryFortnightlyPoDay']) ? $data['deliveryFortnightlyPoDay'] : null
                ))
                ->add('deliveryFortnightlyPoTime', 'choice', array(
                    'required'    => true,
                    'choices'     => $this->params['timeSlotList'],
                    'data'        => isset($data['deliveryFortnightlyPoTime']) ? $data['deliveryFortnightlyPoTime'] : null
                ));
        }
    }

    public function getName()
    {
        return 'platform_setting';
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false
        ));
    }
}