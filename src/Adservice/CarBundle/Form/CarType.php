<?php
namespace Adservice\CarBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Adservice\CarBundle\Form\BrandType;


class CarType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder 
                ->add('version')
                ->add('year', 'text')
                ->add('vin')
                ->add('plateNumber')
                ;
    }
    public function getName()
    {
        return 'new_car_form';
    }
}