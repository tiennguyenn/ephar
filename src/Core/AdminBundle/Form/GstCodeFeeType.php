<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GstCodeFeeType extends AbstractType
{
    private $gstCodes = array();
    public function __construct($params = array(), $gstType = 'doctor') {

        $this->params = $params;
        $this->gstCodes = $this->listGstCode($gstType);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach($options['data'] as $gcf) {
            $builder->add($gcf['feeCode'], 'choice', array(
                'required' => true,
                'choices' => $this->gstCodes,
                'data'     => $gcf['gstCode'],
            ));
        }
    }

    private function listGstCode($type)
    {
        $list = array();
        foreach($this->params as $item) {
            if ($type == 'gmeds') {
                // if(substr($item['code'], -2) == 'GM') {
                    $list[$item['code'] . '|' . $item['id']] = $item['code'];
                // }
            } else {
                if(substr($item['code'], -2) != 'GM') {
                    $list[$item['code'] . '|' . $item['id']] = $item['code'];
                }
                
            }
            
        }
        return $list;
    }

    public function getName()
    {
        return 'gst_code_fee';
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