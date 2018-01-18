<?php
namespace Adservice\CarBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                // ->add('brand', 'choice', array('expanded'=>false, 'multiple'=>true))
                // ->add('model', 'choice', array('expanded'=>false, 'multiple'=>true))
                // ->add('version', 'choice', array('expanded'=>false, 'multiple'=>true, 'required'=>'0'))
                ->add('year', TextType::class, array('required'=>false))
                ->add('motor', TextType::class, array('required'=>false))
                ->add('kW', TextType::class, array('required'=>'0'))
                ->add('displacement', TextType::class, array('required'=>'0'))
                ->add('vin', TextType::class, array('required'=>'true', 'attr'=>array( 'maxlength'=>'17')))
                ->add('plateNumber', TextType::class, array('required'=>'true'))                
                ;
    }
    public function getBlockPrefix()
    {
        return 'new_car_form';
    }
}