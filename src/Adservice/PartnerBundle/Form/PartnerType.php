<?php

namespace Adservice\PartnerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class PartnerType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder
                ->add('name')
                ->add('phone_number_1', 'text')
                ->add('phone_number_2', 'text')
                ->add('email_1', 'email')
                ->add('email_2', 'email')
                ->add('fax', 'text')
                ->add('address')
                ->add('postal_code', 'text')
                ->add('active', 'checkbox', array('required' => false))
                ->add('province')
                ->add('region')
        ;
    }

    public function getName() {
        return 'adservice_partnerbundle_partnertype';
    }

}
