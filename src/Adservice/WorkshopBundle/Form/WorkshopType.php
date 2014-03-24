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
            ->add('cif', 'text', array('required' => true))
            ->add('num_ad_client')
            ->add('address')
            ->add('city')
            ->add('phone_number_1', 'text')
            ->add('phone_number_2', 'text', array('required' => false))
            ->add('movile_phone_1', 'text', array('required' => false))
            ->add('movile_phone_2', 'text', array('required' => false))
            ->add('fax', 'text', array('required' => false))
            ->add('contact')
            ->add('email_1','email')
            ->add('email_2','email', array('required' => false))
            ->add('diagnosis_machines')
            ->add('observation_workshop', 'textarea', array('required' => false))
            ->add('observation_assessor', 'textarea', array('required' => false))
            ->add('observation_admin', 'textarea', array('required' => false))
            ->add('active', 'checkbox', array('required' => false))
            ->add('test', 'checkbox', array('required' => false))
            ->add('typology')
            ->add('update_at')
            ->add('lowdate_at')
            ->add('endtest_at')
            ->add('partner')
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