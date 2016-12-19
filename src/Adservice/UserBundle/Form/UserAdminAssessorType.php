<?php

namespace Adservice\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Security\Core\SecurityContext;

class UserAdminAssessorType extends AbstractType {


    public function buildForm(FormBuilder $builder, array $options)
    {
        // Recojemos variables de sesion para fitlrar los resultados del formulario
        if (isset($_SESSION['id_country'])) { $id_country = $_SESSION['id_country'];unset($_SESSION['id_country']);} else { $id_country = ' != 0';}

        $builder
            ->add('username')
            ->add('password', 'repeated', array('type'            => 'password',
                                                'invalid_message' => 'Las dos contraseñas deben coincidir',
                                                'first_name'      => 'password1',
                                                'second_name'     => 'password2',
                                                'required'        => 'required',
            ))
            ->add('name')
            ->add('surname')
            ->add('active', 'checkbox', array('required' => false))
            //CONTACT
            ->add('country', 'entity', array(
                  'required' => true,
                  'class' => 'Adservice\UtilBundle\Entity\Country',
                  'property' => 'country',
                  'empty_value' => '',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($id_country) {
                                                return $er->createQueryBuilder('c')
                                                          ->orderBy('c.country', 'ASC')
//                                                          ->where('c.id'.$id_country)
                                                          ; }))

            ->add('category_service', 'entity', array(
                  'required' => false,
                  'class' => 'Adservice\UserBundle\Entity\CategoryService',
                  'property' => 'category_service',
                  'empty_value' => '',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                                                return $er->createQueryBuilder('cs')
                                                          ->orderBy('cs.category_service', 'ASC')
                                                   ; }))
            ->add('region')
            ->add('city')
            ->add('address')
            ->add('postal_code')
            ->add('phone_number_1' , 'text')
            ->add('phone_number_2' , 'text', array('required' => false))
            ->add('mobile_number_1', 'text', array('required' => false))
            ->add('mobile_number_2', 'text', array('required' => false))
            ->add('fax'            , 'text', array('required' => false))
            ->add('email_1','email')
            ->add('email_2','email', array('required' => false))
            ->add('language','entity', array(
                  'class' => 'Adservice\UtilBundle\Entity\Language',
                  'property' => 'language',
                  'required' => true,
                  'empty_value' => ''))
        ;

        return $builder;
    }

    public function getName() {
//        return 'adservice_userbundle_usertype';
        return 'admin_assessor_type';
    }

}