<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class PartnerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('codePartner' , 'integer', array('required'=> 'required', 'attr' => array('min' => 1)))
            ->add('name' 		, 'text'   , array('required'=> 'required'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }
}