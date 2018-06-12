<?php

namespace Adservice\UserBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CategoryServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category_service')
            ->add('slug')
            ->add('dis', TextType::class, array('required' => false))
            ->add('vts', TextType::class, array('required' => false))
            ->add('email', TextType::class, array('required' => false))
            ->add('searchServices', EntityType::class, array(
                'class' => 'Adservice\UserBundle\Entity\SearchService',
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'label_attr' => array(
                    'class' => 'checkbox-inline'
                ),
                'choice_attr' => function($value) {
                    return ['style' => 'margin-top: 2px;'];
                },
            ))
        ;
    }

    public function getBlockPrefix()
    {
        return 'adservice_userbundle_categoryservicetype';
    }
}