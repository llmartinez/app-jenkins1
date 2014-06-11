<?php

namespace Adservice\WorkshopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class TypologyType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('country')
            ->add('active', 'checkbox', array('required' => false))
        ;
    }

    public function getName()
    {
        return 'adservice_workshopbundle_typologytype';
    }
}