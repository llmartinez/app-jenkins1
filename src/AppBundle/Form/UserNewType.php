<?php
namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use AppBundle\Utils\Utils as Utils;
use AppBundle\Utils\UtilsUser;

class UserNewType extends AbstractType
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
            // TODO: guardar Service como JSON: http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/types.html#array-types
            ->add('categoryService' , 'choice'  , array('choices' => $this->getServices($options['attr']),
                                                        'required'=> 'required',
                                                        'empty_value' => 'SelectValue'))

            ->add('country' , 'choice'  , array('choices' => Utils::getCountries(),
                                                'required'=> 'required',
                                                'empty_value' => 'SelectValue' ))

            ->add('language' , 'choice'  , array('choices' => Utils::getLanguages(),
                                                 'required'=> 'required',
                                                 'empty_value' => 'SelectValue'))

            ->add('status' , 'choice'  , array('choices' => UtilsUser::getStatus(),
                                               'required'=> 'required',
                                               'empty_value' => 'SelectValue' ))

            ->add('email1', 'email', array('required' => 'required' ))
            ->add('email2', 'email', array('required' => false ))

            ->add('phoneNumber1', 'integer', array('required' => 'required', 'attr' => array('min' => 0)))
            ->add('phoneNumber2', 'integer', array('required' => false, 'attr' => array('min' => 0)))
            ->add('mobileNumber1', 'integer', array('required' => false, 'attr' => array('min' => 0)))
            ->add('mobileNumber2', 'integer', array('required' => false, 'attr' => array('min' => 0)))
            ->add('fax', 'integer', array('required' => false, 'attr' => array('min' => 0)))
            ->add('region', 'text', array('required' => false ))
            ->add('city', 'text', array('required' => false ))
            ->add('address', 'text', array('required' => false ))
            ->add('postalCode', 'text', array('required' => false ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }

    /** Si el usuario tiene un Servicio asignado devolvemos solo ese servicio,
        sino devolvemos todos los que le permite su Rol
     */
    private function getServices($attr)
    {
        if($attr['tokenService'] != '0')
            // $services = array(idService => nameService)
            return array($attr['tokenService'] => Utils::getCategoryServices($attr['tokenService']));
        else
            return Utils::getCategoryServicesForRole($attr['role']);
    }
}