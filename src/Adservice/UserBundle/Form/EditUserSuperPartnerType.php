<?php

namespace Adservice\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class EditUserSuperPartnerType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder = $this->getbasicUserType($builder);
    }

    public static function getbasicUserType($builder)
    {
        $builder
            ->add('username')
            ->add('name')
            ->add('surname')
            ->add('active' , 'checkbox', array('required' => false))
            ->add('language')
            ->add('partner', 'entity'  , array('class'    => 'PartnerBundle:Partner',
                                               'property' => 'name'))

            //CONTACT
            ->add('country')
            ->add('region')
            ->add('city')
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
        return 'super_partner_type';
    }

}