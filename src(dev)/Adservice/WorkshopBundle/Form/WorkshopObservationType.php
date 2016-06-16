<?php

namespace Adservice\WorkshopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class WorkshopObservationType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('observationAssessor', 'textarea', array('required' => true));
    }

    public function getName()
    {
        return 'adservice_workshopbundle_workshop_observation_type';
    }
}