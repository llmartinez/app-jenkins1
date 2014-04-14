<?php

namespace Adservice\OrderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class WorkshopNewOrderType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('address', 'text', array('required' => true))
            ->add('city', 'text', array('required' => true))
            ->add('phone_number_1', 'text')
            ->add('movile_number_1', 'text', array('required' => false))
            ->add('contact_name', 'text', array('required' => true))
            ->add('contact_surname', 'text', array('required' => true))
            ->add('email_1','email')
            ->add('province')
            ->add('region')
            ->add('country')
            ->add('typology')
            ->add('test')
        ;
    }

    public function getName()
    {
        return 'workshopOrder_newOrder';
    }
}