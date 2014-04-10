<?php

namespace Adservice\OrderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class WorkshopEditOrderType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name','text', array('required' => false))
            ->add('cif','text', array('required' => false))
            ->add('address','text', array('required' => false))
            ->add('city','text', array('required' => false))
            ->add('phone_number_1','text', array('required' => false))
            ->add('phone_number_2','text', array('required' => false))
            ->add('movile_number_1', 'text', array('required' => false))
            ->add('movile_number_2', 'text', array('required' => false))
            ->add('fax', 'text', array('required' => false))
            ->add('contact_name', 'text', array('required' => true))
            ->add('contact_surname', 'text', array('required' => true))
            ->add('email_1','email', array('required' => false))
            ->add('email_2','email', array('required' => false))
            ->add('typology')
            ->add('province')
            ->add('region')
            ->add('country')
        ;
    }

    public function getName()
    {
        return 'workshopOrder_editOrder';
    }
}