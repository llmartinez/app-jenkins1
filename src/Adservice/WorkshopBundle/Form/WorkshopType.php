<?php

namespace Adservice\WorkshopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class WorkshopType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name'         )
            ->add('typology'     )
            ->add('contact'      )
            ->add('num_ad_client')
            ->add('cif'           , 'text', array('required' => true))

            ->add('phone_number_1', 'text')
            ->add('phone_number_2', 'text', array('required' => false))
            ->add('movile_phone_1', 'text', array('required' => false))
            ->add('movile_phone_2', 'text', array('required' => false))
            ->add('fax'           , 'text', array('required' => false))

            // ->add('phone_number_1', 'integer')
            // ->add('phone_number_2', 'integer')
            // ->add('movile_phone_1', 'integer')
            // ->add('movile_phone_2', 'integer')
            // ->add('fax'           , 'integer')

            ->add('email_1','email')
            ->add('email_2','email', array('required' => false))
            ->add('region'  )
            ->add('province')
            ->add('city'    )
            ->add('address' )
//            ->add('diagnosis_machines', 'text', array('required' => false))
            ->add('observation_workshop', 'textarea', array('required' => false))
            ->add('observation_assessor', 'textarea', array('required' => false))
            ->add('observation_admin'   , 'textarea', array('required' => false))
            ->add('active'     , 'checkbox', array('required' => false))
            ->add('test'       , 'checkbox', array('required' => false))
            ->add('conflictive', 'checkbox', array('required' => false))
            ->add('update_at' )
            ->add('lowdate_at')
            ->add('endtest_at')
            ->add('partner'   )
        ;
    }

    public function getName()
    {
        return 'adservice_workshopbundle_workshoptype';
    }
}