<?php

namespace Adservice\OrderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class WorkshopRejectOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rejection_reason', TextareaType::class, array('max_length' => 255))
        ;
    }

    public function getBlockPrefix()
    {
        return 'workshop_rejected_reason';
    }
}