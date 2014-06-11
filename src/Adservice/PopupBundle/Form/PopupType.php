<?php

namespace Adservice\PopupBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class PopupType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder
                ->add('name')
                ->add('description', 'textarea')
                ->add('role')
                ->add('country')
                ->add('startdate_at')
                ->add('enddate_at')
                ->add('active', 'checkbox', array('required' => false))
        ;
    }

    public function getName() {
        return 'adservice_popupbundle_popuptype';
    }

}
