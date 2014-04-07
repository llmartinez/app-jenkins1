<?php

namespace Adservice\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class UserPartnerType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder = $this->getbasicUserType($builder);
    }

    public static function getbasicUserType($builder) {
        $builder
                ->add('username')
                ->add('password', 'repeated', array('type' => 'password',
                                                    'invalid_message' => 'Las dos contraseñas deben coincidir',
                                                    'first_name' => 'Contraseña',
                                                    'second_name' => 'Repite Contraseña',
                                                    'required' => false ))
                ->add('name')
                ->add('surname')
                ->add('city')
                ->add('phone_number_1')
                ->add('phone_number_2', 'text', array('required' => false))
                ->add('movile_number_1', 'text', array('required' => false))
                ->add('movile_number_2', 'text', array('required' => false))
                ->add('fax', 'text', array('required' => false))
                ->add('email_1', 'email')
                ->add('email_2', 'email', array('required' => false))
                ->add('active', 'checkbox', array('required' => false))
                ->add('partner', 'entity', array('class'       => 'PartnerBundle:Partner',
                                                 'property'    => 'name'))
                ->add('region')
                ->add('province')
                ->add('country')
                ->add('language')
        ;
        return $builder;
    }

    public function getName() {
//        return 'adservice_userbundle_usertype';
        return 'partner_type';
    }

}