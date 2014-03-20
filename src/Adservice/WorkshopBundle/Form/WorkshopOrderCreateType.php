<?php

namespace Adservice\WorkshopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class WorkshopOrderCreateType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('address', 'text', array('required' => true))
            ->add('city', 'text', array('required' => true))
            ->add('phone_number_1', 'text')
            ->add('movile_phone_1', 'text', array('required' => false))
            ->add('contact', 'text', array('required' => true))
            ->add('email_1','email')
            ->add('province')
            ->add('region')
        ;
    }

    public function getName()
    {
        return 'workshopOrder_createNewOrder';
    }
}