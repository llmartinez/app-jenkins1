<?php

namespace Adservice\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\SecurityContext;

class UserAssessorType extends AbstractType {


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Recojemos variables de sesion para fitlrar los resultados del formulario
        if (isset($_SESSION['id_country'])) { $id_country = $_SESSION['id_country'];unset($_SESSION['id_country']);} else { $id_country = ' != 0';}
        if (isset($_SESSION['id_catserv'])) { $id_catserv = $_SESSION['id_catserv'];unset($_SESSION['id_catserv']);$cserv_empty=null;} else { $id_catserv = ' != 0';$cserv_empty='';}

        if (isset($_SESSION['all'])) { $all = $_SESSION['all'];unset($_SESSION['all']);} else { $all = 'All';}

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
            // ->add('charge', 'integer', array('empty_data' => '1'))
            //CONTACT

            ->add('country_service', 'entity', array(
                  'required' => false,
                  'class' => 'Adservice\UtilBundle\Entity\CountryService',
                  'choice_label' => 'country',
                  'placeholder' => $all,
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
                                                return $er->createQueryBuilder('c')
                                                          ->orderBy('c.country', 'ASC')
                                                          ; }))
            ->add('category_service', 'entity', array(
                  'required' => false,
                  'class' => 'Adservice\UserBundle\Entity\CategoryService',
                  'choice_label' => 'category_service',
                  'placeholder' => $all,
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($id_catserv) {
                                                return $er->createQueryBuilder('cs')
                                                          ->orderBy('cs.category_service', 'ASC')
                                                          ->where('cs.id'.$id_catserv)
                                                          ; }))

            ->add('country', 'entity', array(
                  'required' => true,
                  'class' => 'Adservice\UtilBundle\Entity\Country',
                  'choice_label' => 'country',
                  'placeholder' => '',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($id_country) {
                                                return $er->createQueryBuilder('c')
                                                          ->orderBy('c.country', 'ASC'); }))

            ->add('region', 'text', array('required' => false))
            ->add('city', 'text', array('required' => false))
            ->add('address', 'text', array('required' => false))
            ->add('postal_code', 'text', array('required' => false))
            ->add('phone_number_1' , 'text')
            ->add('phone_number_2' , 'text', array('required' => false))
            ->add('mobile_number_1', 'text', array('required' => false))
            ->add('mobile_number_2', 'text', array('required' => false))
            ->add('fax'            , 'text', array('required' => false))
            ->add('email_1','email')
            ->add('email_2','email', array('required' => false))
            ->add('language','entity', array(
                  'class' => 'Adservice\UtilBundle\Entity\Language',
                  'choice_label' => 'language',
                  'required' => true,
                  'placeholder' => ''))
        ;

        return $builder;
    }

    public function getName() {
//        return 'adservice_userbundle_usertype';
        return 'admin_assessor_type';
    }

}