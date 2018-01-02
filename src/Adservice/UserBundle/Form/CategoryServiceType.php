<?php

namespace Adservice\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CategoryServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category_service')
            ->add('slug')
            ->add('dis', 'text', array('required' => false))
            ->add('vts', 'text', array('required' => false))
            ->add('email', 'text', array('required' => false))
        ;
    }

    public function getBlockPrefix()
    {
        return 'adservice_userbundle_categoryservicetype';
    }
}