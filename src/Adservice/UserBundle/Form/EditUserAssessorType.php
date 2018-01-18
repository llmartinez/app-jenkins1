<?php

namespace Adservice\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class EditUserAssessorType extends AbstractType {


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Recojemos variables de sesion para fitlrar los resultados del formulario
        if (isset($_SESSION['id_country'])) { $id_country = $_SESSION['id_country'];unset($_SESSION['id_country']);} else { $id_country = ' != 0';}

        $builder
            ->add('username')
            ->add('name')
            ->add('surname')
            ->add('active', CheckboxType::class, array('required' => false))
            // ->add('charge')
            //CONTACT
            ->add('category_service', EntityType::class, array(
                  'required' => false,
                  'class' => 'Adservice\UserBundle\Entity\CategoryService',
                  'choice_label' => 'category_service',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er)  {
                                                return $er->createQueryBuilder('cs')
                                                          ->orderBy('cs.category_service', 'ASC')
                                                          ; }))
            ->add('country_service', EntityType::class, array(
                  'required' => true,
                  'class' => 'Adservice\UtilBundle\Entity\CountryService',
                  'choice_label' => 'country',               
                  'choice_translation_domain' => null,
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($id_country) {
                                                return $er->createQueryBuilder('c')
                                                          ->orderBy('c.country', 'ASC'); }))
            ->add('country', EntityType::class, array(
                  'required' => true,
                  'class' => 'Adservice\UtilBundle\Entity\Country',
                  'choice_label' => 'country',               
                  'choice_translation_domain' => null,
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
        ;

        return $builder;
    }

    public function getBlockPrefix() {
//        return 'adservice_userbundle_usertype';
        return 'assessor_type';
    }

}