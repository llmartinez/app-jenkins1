<?php

namespace Adservice\WorkshopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class WorkshopDeactivateObservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('observationAdmin',  TextareaType::class, array('required' => true));
    }

    public function getBlockPrefix()
    {
        return 'adservice_workshopbundle_workshop_deactivate_observation_type';
    }
}