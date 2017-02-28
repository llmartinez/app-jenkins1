<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class WorkshopType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name'         , 'text'   , array('required'=> 'required', 'attr' => array('class' => 'required')))
            ->add('Partner'      , 'entity' , array('class' => 'AppBundle:Partner', 'empty_value' => 'SelectValue', 'attr' => array('class' => 'required')))
            ->add('codeWorkshop' , 'integer', array('required'=> 'required', 'attr' => array('min' => 1)))
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }
}