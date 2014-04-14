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
            ->add('code_workshop', 'number')
            ->add('cif', 'text', array('required' => true))
            ->add('typology')
            ->add('partner')
            ->add('shop')
            ->add('contact_name', 'text', array('required' => true))
            ->add('contact_surname', 'text', array('required' => true))
            ->add('diagnosis_machines')
            ->add('active', 'checkbox', array('required' => false))
            ->add('test', 'checkbox', array('required' => false))
            ->add('endtest_at', 'datetime')
            ->add('country')
            ->add('region')
            ->add('province')
            ->add('address')
            ->add('city')
            ->add('phone_number_1', 'text')
            ->add('phone_number_2', 'text', array('required' => false))
            ->add('movile_number_1', 'text', array('required' => false))
            ->add('movile_number_2', 'text', array('required' => false))
            ->add('fax', 'text', array('required' => false))
            ->add('email_1','email')
            ->add('email_2','email', array('required' => false))
            ->add('observation_workshop', 'textarea', array('required' => false))
            ->add('observation_assessor', 'textarea', array('required' => false))
            ->add('observation_admin', 'textarea', array('required' => false))
            ->add('conflictive', 'checkbox', array('required' => false))
        ;
    }

    public function getName()
    {
        return 'adservice_workshopbundle_workshoptype';
    }
}