<?php

namespace Adservice\WorkshopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class WorkshopObservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('observationAssessor', 'textarea', array('required' => true));
    }

    public function getBlockPrefix()
    {
        return 'adservice_workshopbundle_workshop_observation_type';
    }
}