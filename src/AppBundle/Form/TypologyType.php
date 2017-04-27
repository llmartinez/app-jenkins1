<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use AppBundle\Utils\Utils as Utils;

class TypologyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('service', 'choice'  , array('choices' => Utils::getServices($options['attr']['ids']),
                                               'expanded'=> 'true',
                                               'multiple'=> 'true',
                                               'required'=>'required',
                                               'translation_domain' => 'messages'
                                              ))

            ->add('name'   , 'text'    , array('required'=> 'required', 'translation_domain' => 'messages' ))
            ->add('active' , 'checkbox', array('required'=> false, 'attr' => array('checked'   => 'checked'), 'translation_domain' => 'messages' ))

            ->add('submit', 'submit')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }
}