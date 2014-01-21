<?php

/*
 * Formulario para editar los datos personales de la persona logueada
 */

namespace Adservice\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Adservice\UserBundle\Form\UserProfileType;

class UserProfileType extends AbstractType{
    
    public function buildForm(FormBuilder $builder, array $options){
        $builder
            ->add('username')
//            ->add('password', 'repeated', array('type'            => 'password',
//                                                'invalid_message' => 'Las dos contraseñas deben coincidir',
//                                                'first_options'   => array('label' => 'Contraseña'),
//                                                'second_options'  => array('label' => 'Repite Contraseña'),
//                                                'required'        => false))
            ->add('active')
            ->add('language')
        ;
    }


    public function getName(){
        return 'form_profile_user';
    }

    
}