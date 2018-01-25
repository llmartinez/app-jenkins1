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

class UserCommercialType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder = $this->getbasicUserType($builder);
    }

    public static function getbasicUserType($builder)
    {
        // Recojemos variables de sesion para fitlrar los resultados del formulario
        if (isset($_SESSION['role'      ])) { $role = $_SESSION['role'];unset($_SESSION['role']);} else { $role = '0';}
        if (isset($_SESSION['id_partner']) and $_SESSION['id_partner'] != ' IN (0)') {$id_partner = $_SESSION['id_partner'];unset($_SESSION['id_partner']);} else { $id_partner = ' != 0';}
        if (isset($_SESSION['id_country'])) { $id_country = $_SESSION['id_country'];unset($_SESSION['id_country']);} else { $id_country = ' != 0';}
        if (isset($_SESSION['id_catserv'])) { $id_catserv = $_SESSION['id_catserv'];unset($_SESSION['id_catserv']);$cserv_empty=null;} else { $id_catserv =  ' != 0';$cserv_empty=null;}

        if($role == 'ROLE_AD') $p_empty = false; else $p_empty = '';

        $builder
            ->add('username')
            ->add('password', RepeatedType::class, array('type' => PasswordType::class,
                'invalid_message' => 'Las dos contraseñas deben coincidir',
                'first_name' => 'password1',
                'first_options' => array('attr' => array('class' => 'form-control')),
                'second_name' => 'password2',
                'second_options' => array('attr' => array('class' => 'form-control')),
                'required' => true,
            ))
            ->add('name')
            ->add('surname')
            ->add('active' , CheckboxType::class, array('required' => false))
            ->add('partner', EntityType::class, array(
                  'required' => true,
                  'class' => 'Adservice\PartnerBundle\Entity\Partner',
                  'choice_label' => 'name',
                  'placeholder' => $p_empty,
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($id_catserv, $id_partner) {
                                                return $er->createQueryBuilder('s')
                                                          ->orderBy('s.name', 'ASC')
                                                          ->where('s.id '.$id_partner)
                                                         ; }))
            //CONTACT
            ->add('country', EntityType::class, array(
                  'required' => true,
                  'class' => 'Adservice\UtilBundle\Entity\Country',
                  'choice_label' => 'country',               
                  'choice_translation_domain' => null,
                  'placeholder' => '',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($id_country) {
                                                return $er->createQueryBuilder('c')
                                                          ->orderBy('c.country', 'ASC'); }))
            ->add('region')
            ->add('city')
            ->add('address')
            ->add('postal_code')
            ->add('phone_number_1' , TextType::class)
            ->add('phone_number_2' , TextType::class, array('required' => false))
            ->add('mobile_number_1', TextType::class, array('required' => false))
            ->add('mobile_number_2', TextType::class, array('required' => false))
            ->add('fax'            , TextType::class, array('required' => false))
            ->add('email_1',EmailType::class)
            ->add('email_2',EmailType::class, array('required' => false))
            ->add('language',EntityType::class, array(
                  'class' => 'Adservice\UtilBundle\Entity\Language',
                  'choice_label' => 'language',               
                  'choice_translation_domain' => null,
                  'required' => true,
                  'placeholder' => ''))
            ->add('allow_list',CheckboxType::class, array('required' => false))
            ->add('allow_order',CheckboxType::class, array('required' => false))
        ;

        if($role == 'ROLE_SUPER_ADMIN' OR $role == 'ROLE_ADMIN') {
        $builder->add('category_service', EntityType::class, array(
                  'required' => false,
                  'class' => 'Adservice\UserBundle\Entity\CategoryService',
                  'choice_label' => 'category_service',
                  'placeholder' => $cserv_empty,
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($id_catserv) {
                                                return $er->createQueryBuilder('cs')
                                                          ->orderBy('cs.category_service', 'ASC')
                                                          ->where('cs.id'.$id_catserv)
                                                          ; }));
        }

        if($id_catserv != ' = 3')
        {
          $builder->add('shop', EntityType::class, array(
                    'required' => false,
                    'class' => 'Adservice\PartnerBundle\Entity\Shop',
                    'choice_label' => 'name',
                    'placeholder' => '',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($id_catserv, $id_partner) {
                                                  return $er->createQueryBuilder('s')
                                                            ->orderBy('s.name', 'ASC')
                                                            ->where('s.active = 1')
                                                            ->andWhere('s.partner'.$id_partner)
                                                            ->andWhere('s.category_service'.$id_catserv); }));
        }

        return $builder;
    }

    public function getBlockPrefix() {
        return 'commercial_type';
    }

}