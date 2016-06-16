<?php
namespace Adservice\CarBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
// use Adservice\CarBundle\Form\BrandType;


class CarType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
                // ->add('brand', 'choice', array('expanded'=>false, 'multiple'=>true))
                // ->add('model', 'choice', array('expanded'=>false, 'multiple'=>true))
                // ->add('version', 'choice', array('expanded'=>false, 'multiple'=>true, 'required'=>'0'))
                ->add('year', 'text', array('required'=>false))
                ->add('motor', 'text', array('required'=>false))
                ->add('kW', 'text', array('required'=>'0'))
                ->add('displacement', 'text', array('required'=>'0'))
                ->add('vin', 'text', array('required'=>'true'))
                ->add('plateNumber', 'text', array('required'=>'true'))
                ;
    }
    public function getName()
    {
        return 'new_car_form';
    }
}