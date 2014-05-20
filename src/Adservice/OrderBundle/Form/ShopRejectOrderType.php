<?php

namespace Adservice\OrderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ShopRejectOrderType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('rejection_reason', 'textarea', array('max_length' => 255))
        ;
    }

    public function getName()
    {
        return 'shop_rejected_reason';
    }
}