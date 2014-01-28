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
//            ->add('password', 'repeated', array(
//                  'type' => 'password',
//                  'invalid_message' => 'Las dos contraseñas deben coincidir',
//                  'options' => array('always_empty' => false)
//            ))    
            ->add('name')
            ->add('surname')
            ->add('dni')
            ->add('city')
            ->add('province')
            ->add('phone_number_1')
            ->add('phone_number_2')
            ->add('movile_number_1')
            ->add('movile_number_2')
            ->add('fax')
            ->add('email_1')
            ->add('email_2')
            ->add('active')
            ->add('language')
            ->add('region')
            ->add('province')
            ->add('user_role')    
        ;
    }

    public function getName()
    {
        return 'adservice_userbundle_usertype';
    }
}