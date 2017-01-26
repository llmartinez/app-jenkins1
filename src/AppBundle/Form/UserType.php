<?php
namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use AppBundle\Utils\Utils as Utils;
use AppBundle\Utils\User as UtilsUser;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text', array('required' => 'required'))
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'first_options'  => array('label' => 'password_insert'),
                'second_options' => array('label' => 'password_repeat'),
                'invalid_message' => 'error_passwords_not_match',
                'required' => 'required'
            ))
            ->add('categoryService' , 'choice'  , array('choices' => Utils::getCategoryServices(),
                                                        'required'=> 'required',
                                                        'empty_value' => 'All'))

            ->add('country' , 'choice'  , array('choices' => Utils::getCountries(),
                                                'required'=> 'required',
                                                'empty_value' => 'All' ))

            ->add('language' , 'choice'  , array('choices' => Utils::getLanguages(),
                                                 'required'=> 'required',
                                                 'empty_value' => 'All'))

            ->add('status' , 'choice'  , array('choices' => UtilsUser::getStatus(),
                                               'required'=> 'required',
                                               'empty_value' => 'All' ))

            ->add('email1', 'email', array('required' => 'required' ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }
}