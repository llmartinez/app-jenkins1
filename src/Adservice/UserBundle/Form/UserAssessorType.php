<?php

namespace Adservice\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserAssessorType extends AbstractType {


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Recojemos variables de sesion para fitlrar los resultados del formulario
        if (isset($_SESSION['id_country'])) { $id_country = $_SESSION['id_country'];unset($_SESSION['id_country']);} else { $id_country = ' != 0';}
        if (isset($_SESSION['id_catserv'])) { $id_catserv = $_SESSION['id_catserv'];unset($_SESSION['id_catserv']);$cserv_empty=null;} else { $id_catserv = ' != 0';$cserv_empty='';}

        if (isset($_SESSION['all'])) { $all = $_SESSION['all'];unset($_SESSION['all']);} else { $all = 'All';}

        $builder
            ->add('username')
            ->add('password', RepeatedType::class, array('type' => PasswordType::class,
                'invalid_message' => 'Las dos contraseñas deben coincidir',
                'first_name' => 'password1',
                'first_options' => array('label' => 'password'),
                'second_name' => 'password2',
                'second_options' => array('label' => 'repeat_password'),
                'required' => true,
            ))
            ->add('name')
            ->add('surname')
            ->add('active', CheckboxType::class, array('required' => false))
            // ->add('charge', 'integer', array('empty_data' => '1'))
            //CONTACT

            ->add('country_service', EntityType::class, array(
                  'required' => false,
                  'class' => 'Adservice\UtilBundle\Entity\CountryService',
                  'choice_label' => 'country',                
                  'choice_translation_domain' => null,
                  'placeholder' => $all,
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
                                                return $er->createQueryBuilder('c')
                                                          ->orderBy('c.country', 'ASC')
                                                          ; }))
            ->add('category_service', EntityType::class, array(
                  'required' => false,
                  'class' => 'Adservice\UserBundle\Entity\CategoryService',
                  'choice_label' => 'category_service',
                  'placeholder' => $all,
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($id_catserv) {
                                                return $er->createQueryBuilder('cs')
                                                          ->orderBy('cs.category_service', 'ASC')
                                                          ->where('cs.id'.$id_catserv)
                                                          ; }))

            ->add('country', EntityType::class, array(
                  'required' => true,
                  'class' => 'Adservice\UtilBundle\Entity\Country',
                  'choice_label' => 'country',
                  'choice_translation_domain' => null,
                  'placeholder' => '',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($id_country) {
                                                return $er->createQueryBuilder('c')
                                                          ->orderBy('c.country', 'ASC'); }))

            ->add('region', TextType::class, array('required' => false))
            ->add('city', TextType::class, array('required' => false))
            ->add('address', TextType::class, array('required' => false))
            ->add('postal_code', TextType::class, array('required' => false))
            ->add('phone_number_1' ,TextType::class)
            ->add('phone_number_2' ,TextType::class, array('required' => false))
            ->add('mobile_number_1',TextType::class, array('required' => false))
            ->add('mobile_number_2',TextType::class, array('required' => false))
            ->add('fax'            ,TextType::class, array('required' => false))
            ->add('email_1',EmailType::class)
            ->add('email_2',EmailType::class, array('required' => false))
            ->add('language',EntityType::class, array(
                  'class' => 'Adservice\UtilBundle\Entity\Language',
                  'choice_label' => 'language',                
                  'choice_translation_domain' => null,
                  'required' => true,
                  'placeholder' => ''))
        ;

        return $builder;
    }

    public function getBlockPrefix() {
//        return 'adservice_userbundle_usertype';
        return 'admin_assessor_type';
    }

}