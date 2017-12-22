<?php

namespace Adservice\OrderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class WorkshopRejectOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rejection_reason', 'textarea', array('max_length' => 255))
        ;
    }

    public function getBlockPrefix()
    {
        return 'workshop_rejected_reason';
    }
}