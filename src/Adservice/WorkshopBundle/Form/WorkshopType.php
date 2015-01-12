<?php

namespace Adservice\WorkshopBundle\Form;

use Adservice\WorkshopBundle\Form\DiagnosisMachineType;
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
            ->add('diagnosis_machines')
            ->add('contact', 'text', array('required' => true))
            ->add('internal_code', 'text', array('required' => false))
            ->add('ad_service_plus', 'checkbox', array('required' => false))
            ->add('active', 'checkbox', array('required' => false))
            ->add('test', 'checkbox', array('required' => false))
            ->add('endtest_at', 'date', array('format' => 'dd-MM-yyyy'))
            ->add('observation_workshop', 'textarea', array('required' => false))
            ->add('observation_assessor', 'textarea', array('required' => false))
            ->add('observation_admin', 'textarea', array('required' => false))
            ->add('conflictive', 'checkbox', array('required' => false))
            //CONTACT
            ->add('country')
            ->add('region')
            ->add('city')
            ->add('address')
            ->add('postal_code')
            ->add('phone_number_1', 'text')
            ->add('phone_number_2', 'text', array('required' => false))
            ->add('movile_number_1', 'text', array('required' => false))
            ->add('movile_number_2', 'text', array('required' => false))
            ->add('fax', 'text', array('required' => false))
            ->add('email_1','email')
            ->add('email_2','email', array('required' => false))
        ;
    }

    public function getName()
    {
        return 'adservice_workshopbundle_workshoptype';
    }
}