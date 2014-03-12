<?php

namespace Adservice\WorkshopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class WorkshopOrderType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('address')
            ->add('city')
            ->add('phone_number_1', 'text')
            ->add('province')
            ->add('region')
        ;
    }

    public function getName()
    {
        return 'WorkshopOrderType';
    }
}