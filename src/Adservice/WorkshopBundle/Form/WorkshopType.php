<?php

namespace Adservice\WorkshopBundle\Form;

use Adservice\WorkshopBundle\Form\DiagnosisMachineType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;


class WorkshopType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Recojemos variables de sesion para fitlrar los resultados del formulario
        if (isset($_SESSION['id_partner'])) { $id_partner = $_SESSION['id_partner'];unset($_SESSION['id_partner']);} else { $id_partner = ' != 0';}
        if (isset($_SESSION['id_country'])) { $id_country = $_SESSION['id_country'];unset($_SESSION['id_country']);} else { $id_country = ' != 0';}
        if (isset($_SESSION['id_catserv'])) { $id_catserv = $_SESSION['id_catserv'];unset($_SESSION['id_catserv']);} else { $id_catserv = ' != 0';}
        $builder
            ->add('name')
            ->add('code_workshop', NumberType::class)
            ->add('cif', TextType::class, array('required' => true))
            ->add('category_service', EntityType::class, array(
                  'required' => false,
                  'class' => 'Adservice\UserBundle\Entity\CategoryService',
                  'choice_label' => 'category_service',
                  'placeholder' => '',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use($id_catserv) {
                                                return $er->createQueryBuilder('cs')
                                                          ->orderBy('cs.category_service', 'ASC')
                                                            ->where('cs.id '.$id_catserv)
                                                          ; }))
            ->add('partner', EntityType::class, array(
                  'required' => true,
                  'class' => 'Adservice\PartnerBundle\Entity\Partner',
                  'choice_label' => 'name',
                  'placeholder' => '',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use (/*$id_country,*/ $id_catserv) {
                                                return $er->createQueryBuilder('s')
                                                          ->orderBy('s.name', 'ASC')
                                                          ->where('s.active = 1')
                                                          //->andWhere('s.country'.$id_country); }))
                                                          ->andWhere('s.category_service'.$id_catserv); }))
            ->add('shop', EntityType::class, array(
                  'required' => true,
                  'class' => 'Adservice\PartnerBundle\Entity\Shop',
                  'choice_label' => 'name',
                  'placeholder' => false,
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use (/*$id_country,*/ $id_catserv, $id_partner) {
                                                return $er->createQueryBuilder('s')
                                                          ->orderBy('s.name', 'ASC')
                                                          ->where('s.active = 1')
                                                          //->andWhere('s.country'.$id_country.' OR s.id = 1')
                                                          ->andWhere('s.category_service'.$id_catserv.' OR s.id = 1')
                                                          ->andWhere('s.partner'.$id_partner.' OR s.id = 1'); }))
            ->add('typology', EntityType::class, array(
                  'required' => true,
                  'class' => 'Adservice\WorkshopBundle\Entity\Typology',
                  'choice_label' => 'name',
                  'placeholder' => '',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use (/*$id_country,*/ $id_catserv) {
                                                return $er->createQueryBuilder('s')
                                                          ->orderBy('s.name', 'ASC')
                                                          ->where('s.active = 1')
                                                          ->andWhere('s.category_service'.$id_catserv)
                                                          // ->andWhere('s.country'.$id_country)
                                                          ; }))
            ->add('diagnosis_machines', EntityType::class, array(
                  'required' => false,
                  'multiple' => true,
                  'class' => 'Adservice\WorkshopBundle\Entity\DiagnosisMachine',
                  'choice_label' => 'name',
                  'placeholder' => '',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use (/*$id_country,*/ $id_catserv) {
                                                return $er->createQueryBuilder('s')
                                                          ->orderBy('s.name', 'ASC')
                                                          ->where('s.active = 1')
                                                          ->andWhere('s.category_service'.$id_catserv)
                                                          // ->andWhere('s.country'.$id_country)
                                                          ; }))
            ->add('contact', TextType::class, array('required' => true))
            ->add('internal_code', TextType::class, array('required' => false))
            ->add('commercial_code', TextType::class, array('required' => false))
            ->add('ad_service_plus', CheckboxType::class, array('required' => false))
//            ->add('active', 'checkbox', array('required' => false))
            ->add('test', CheckboxType::class, array('required' => false))
            ->add('endtest_at', DateType::class, array('format' => 'dd-MM-yyyy'))
            ->add('haschecks', CheckboxType::class, array('required' => false))
            ->add('numchecks', IntegerType::class, array('required' => false))
            ->add('infotech', CheckboxType::class, array('required' => false))
            ->add('observation_workshop', TextareaType::class, array('required' => false))
            ->add('observation_assessor', TextareaType::class, array('required' => false))
            ->add('observation_admin', TextareaType::class, array('required' => false))
            ->add('conflictive', CheckboxType::class, array('required' => false))
            //CONTACT
            ->add('country', EntityType::class, array(
                  'required' => true,
                  'class' => 'Adservice\UtilBundle\Entity\Country',
                  'choice_label' => 'country',
                  'choice_translation_domain' => null,
                  'placeholder' => '',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er)  {
                                                return $er->createQueryBuilder('c')
                                                          ->orderBy('c.country', 'ASC'); }))
            ->add('region', TextType::class, array('required' => false))
            ->add('city')
            ->add('address')
            ->add('postal_code')
            ->add('phone_number_1', TextType::class)
            ->add('phone_number_2', TextType::class, array('required' => false))
            ->add('mobile_number_1', TextType::class, array('required' => false))
            ->add('mobile_number_2', TextType::class, array('required' => false))
            ->add('fax', TextType::class, array('required' => false))
            ->add('email_1',EmailType::class)
            ->add('email_2',EmailType::class, array('required' => false))
        ;
                                                          
        if (isset($_SESSION['code_billing'])) $builder->add('code_billing', TextType::class, array('required' => false));
    }

    public function getBlockPrefix()
    {
        return 'adservice_workshopbundle_workshoptype';
    }
}