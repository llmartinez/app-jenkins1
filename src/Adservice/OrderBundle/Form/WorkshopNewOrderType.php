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
            ->add('cif','text', array('required' => true))
            // ->add('partner', 'choice' , array('required' => true, 'empty_value' => 'Selecciona un socio'))
            ->add('shop')
            ->add('code_workshop')
            ->add('typology')
            ->add('test')
            ->add('contact', 'text', array('required' => true))
            ->add('test', 'checkbox', array('required' => false))
            ->add('internal_code', 'text', array('required' => false))
            ->add('ad_service_plus', 'checkbox', array('required' => false))
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
        return 'workshopOrder_newOrder';
    }
}