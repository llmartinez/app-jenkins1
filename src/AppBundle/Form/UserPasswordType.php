<?php
namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plainPassword', 'repeated', array(
                                                    'type' => 'password',
                                                    'first_options'  => array('label' => 'password_insert'),
                                                    'second_options' => array('label' => 'password_repeat'),
                                                    'invalid_message' => 'error_passwords_not_match',
                                                    'required' => 'required'
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }
}