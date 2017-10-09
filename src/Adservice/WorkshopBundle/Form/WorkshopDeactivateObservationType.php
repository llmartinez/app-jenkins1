<?php

namespace Adservice\WorkshopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class WorkshopDeactivateObservationType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('observationAdmin', 'textarea', array('required' => true));
    }

    public function getName()
    {
        return 'adservice_workshopbundle_workshop_deactivate_observation_type';
    }
}