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
            ->add('cif','text', array('required' => false))
            ->add('partner')
            ->add('shop')
            ->add('code_workshop')
            ->add('typology')
            ->add('test')
            ->add('contact_name', 'text', array('required' => true))
            ->add('contact_surname', 'text', array('required' => true))
             //CONTACT
            ->add('country')
            ->add('region')
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