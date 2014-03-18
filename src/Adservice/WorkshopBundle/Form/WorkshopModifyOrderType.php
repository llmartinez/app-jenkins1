<?php

namespace Adservice\WorkshopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class WorkshopModifyOrderType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name')
//            ->add('cif')
//            ->add('address')
//            ->add('city')
//            ->add('phone_number_1', 'text')
//            ->add('province')
//            ->add('region')
//            ->add('email1')
        ;
    }

    public function getName()
    {
        return 'WorkshopModifyOrderType';
    }
}