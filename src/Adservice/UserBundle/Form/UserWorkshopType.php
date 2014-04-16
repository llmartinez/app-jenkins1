<?php

namespace Adservice\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Security\Core\SecurityContext;

class UserWorkshopType extends AbstractType {


    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('password', 'repeated', array('type'            => 'password',
                                                'invalid_message' => 'Las dos contraseñas deben coincidir',
                                                'first_name'      => 'Contraseña',
                                                'second_name'     => 'Repite Contraseña',
                                                'required'        => 'required'
            ))
            ->add('name')
            ->add('surname')
            ->add('active', 'checkbox', array('required' => false))
            ->add('workshop', 'entity', array('class'    => 'WorkshopBundle:Workshop',
                                              'property' => 'name'))
            ->add('language')

            //CONTACT
            ->add('country')
            ->add('region')
            ->add('address')
            ->add('postal_code')
            ->add('phone_number_1' , 'text')
            ->add('phone_number_2' , 'text', array('required' => false))
            ->add('movile_number_1', 'text', array('required' => false))
            ->add('movile_number_2', 'text', array('required' => false))
            ->add('fax'            , 'text', array('required' => false))
            ->add('email_1','email')
            ->add('email_2','email', array('required' => false))
            ->add('language')
        ;

        return $builder;
    }

    public function getName() {
//        return 'adservice_userbundle_usertype';
        return 'workshop_type';
    }
}