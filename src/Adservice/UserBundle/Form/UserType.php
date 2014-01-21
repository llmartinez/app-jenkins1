<?php

namespace Adservice\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class UserType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('password')
            ->add('salt')
            ->add('active')
            ->add('sessionID')
            ->add('language')
            ->add('user_role')
        ;
    }

    public function getName()
    {
        return 'adservice_userbundle_usertype';
    }
}