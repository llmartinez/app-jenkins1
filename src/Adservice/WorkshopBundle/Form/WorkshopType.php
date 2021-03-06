<?php

namespace Adservice\WorkshopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class WorkshopType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('address')
            ->add('city')
            ->add('phone_number_1')
            ->add('phone_number_2')
            ->add('movile_phone_1')
            ->add('movile_phone_2')
            ->add('fax')
            ->add('contact')
            ->add('email_1','email')
            ->add('email_2','email')
            ->add('observations')
            ->add('active', 'checkbox', array('required' => false))
            ->add('adservice_plus', 'checkbox', array('required' => false))
            ->add('test', 'checkbox', array('required' => false))
            ->add('typology')
            ->add('update_at')
            ->add('lowdate_at')
            ->add('endtest_at')
            ->add('conflictive', 'checkbox', array('required' => false))
            ->add('province')
            ->add('region')
        ;
    }

    public function getName()
    {
        return 'adservice_workshopbundle_workshoptype';
    }
}