<?php

namespace Adservice\OrderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class WorkshopOrderType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('cif', 'text', array('required' => true))
            ->add('address')
            ->add('city')
            ->add('phone_number_1', 'text')
            ->add('phone_number_2', 'text', array('required' => false))
            ->add('movile_number_1', 'text', array('required' => false))
            ->add('movile_number_2', 'text', array('required' => false))
            ->add('fax', 'text', array('required' => false))
            ->add('contact_name', 'text', array('required' => true))
            ->add('contact_surname', 'text', array('required' => true))
            ->add('email_1','email')
            ->add('email_2','email', array('required' => false))
//            ->add('diagnosis_machines')
            ->add('active', 'checkbox', array('required' => false))
            ->add('test', 'checkbox', array('required' => false))
            ->add('typology')
//            ->add('update_at')
//            ->add('lowdate_at')
//            ->add('endtest_at')
            ->add('partner')
//            ->add('conflictive', 'checkbox', array('required' => false))
            ->add('province')
            ->add('region')
            ->add('country')
//            ->add('order')
        ;
    }

    public function getName(){
        return 'WorkshopOrderType';
    }
}