<?php

namespace Adservice\OrderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ShopEditOrderType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
                ->add('name')
                ->add('partner')
                ->add('phone_number_1', 'text')
                ->add('phone_number_2', 'text', array('required' => false))
                ->add('email_1', 'email')
                ->add('email_2', 'email', array('required' => false))
                ->add('fax', 'text', array('required' => false))
                ->add('address')
                ->add('postal_code')
                ->add('province')
                ->add('region')
        ;
    }

    public function getName()
    {
        return 'shopOrder_editOrder';
    }
}