<?php
namespace Adservice\CarBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class CarType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
                ->add('version')
                ->add('year', 'integer')
                ->add('vin', 'text')
                ->add('plateNumber')
                ;
    }
    public function getName()
    {
        return 'new_car_form';
    }
}