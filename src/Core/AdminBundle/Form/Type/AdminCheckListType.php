<?php 
namespace AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class  AdminCheckListType  extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'multiple' => true,
            'expanded' => true
        ));
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
	
	public function getName()
	{
		return 'admin_check_list';
	}
}