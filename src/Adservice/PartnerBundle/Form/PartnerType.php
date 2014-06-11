<?php

namespace Adservice\PartnerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Validator\Constraints\NotNull;

class PartnerType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('code_partner')
            ->add('active', 'checkbox', array('required' => false))
             //CONTACT
            ->add('country')
            ->add('region', 'text')
            ->add('city', 'text')
            ->add('address')
            ->add('postal_code')
            ->add('phone_number_1', 'text')
            ->add('phone_number_2', 'text', array('required' => false))
            ->add('movile_number_1', 'text', array('required' => false))
            ->add('movile_number_2', 'text', array('required' => false))
            ->add('fax', 'text', array('required' => false))
            ->add('email_1','email')
            ->add('email_2','email', array('required' => false))
        ;
    }

    public function getName() {
        return 'adservice_partnerbundle_partnertype';
    }

}
