<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlatformSettingGstCodeType extends AbstractType
{
    private $gstCodes = array();
    public function __construct($params = array(), $gstType = 'doctor') {

        $this->params = $params;
        $this->gstCodes = $this->listGstCode($gstType);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach($options['data'] as $psgc) {
            $builder->add($psgc['feeCode'], 'choice', array(
                'required' => true,
                'choices' => $this->gstCodes,
                'data'     => $psgc['gstCode'],
            ));
        }
    }
    /**
     * get list gst code
     * @param  string $type
     * @author  thu.tranq
     * @return array
     */
    private function listGstCode($type)
    {
        $list = array();
        foreach($this->params as $item) {
            if ($type == 'gmeds') {
                if(substr($item['code'], -2) == 'GM') {
                    $list[$item['id']] = $item['code'];
                }
            } else {
                if(substr($item['code'], -2) != 'GM') {
                    $list[$item['id']] = $item['code'];
                }
                
            }
            
        }
        return $list;
    }

    public function getName()
    {
        return 'platform_seting_gst_code';
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
