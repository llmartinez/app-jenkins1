<?php

namespace Adservice\OrderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;


class WorkshopEditOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Recojemos variables de sesion para fitlrar los resultados del formulario
        if (isset($_SESSION['id_partner'])) { $id_partner = $_SESSION['id_partner'];unset($_SESSION['id_partner']);} else { $id_partner = ' != 0';}
        if (isset($_SESSION['id_country'])) { $id_country = $_SESSION['id_country'];unset($_SESSION['id_country']);} else { $id_country = ' != 0';}
        if (isset($_SESSION['id_catserv'])) { $id_catserv = $_SESSION['id_catserv'];unset($_SESSION['id_catserv']);} else { $id_catserv = ' != 0';}

        $builder
            ->add('name',TextType::class, array('required' => false))
            ->add('cif',TextType::class, array('required' => false))
            ->add('partner', EntityType::class, array(
                  'required' => false,
                  'class' => 'Adservice\PartnerBundle\Entity\Partner',
                  'choice_label' => 'name',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($id_country, $id_catserv) {
                                                return $er->createQueryBuilder('s')
                                                          ->orderBy('s.name', 'ASC')
                                                          ->where('s.active = 1')
                                                          ->andWhere('s.category_service'.$id_catserv)
                                                          ->andWhere('s.country'.$id_country); }))
            ->add('code_workshop')
            ->add('typology', EntityType::class, array(
                              'required' => true,
                              'class' => 'Adservice\WorkshopBundle\Entity\Typology',
                              'choice_label' => 'name',
                              'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($id_catserv) {
                                                return $er->createQueryBuilder('t')
                                                          ->orderBy('t.name', 'ASC')
                                                          ->where('t.active = 1')
                                                          ->andWhere('t.category_service'.$id_catserv); }))
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
            ->add('test', CheckboxType::class, array('required' => false))
            ->add('haschecks', CheckboxType::class, array('required' => false))
            ->add('numchecks', IntegerType::class, array('required' => false))
            ->add('internal_code', TextType::class, array('required' => false))
            ->add('commercial_code', TextType::class, array('required' => false))
             //CONTACT
            ->add('country', EntityType::class, array(
                  'required' => true,
                  'class' => 'Adservice\UtilBundle\Entity\Country',
                  'choice_label' => 'country',
                  'choice_translation_domain' => null,
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($id_country) {
                                                return $er->createQueryBuilder('c')
                                                          ->orderBy('c.country', 'ASC')
                                                          ->where('c.id'.$id_country); }))
            ->add('region')
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
        
        if($id_catserv != ' = 3'){
            $builder
            ->add('shop', EntityType::class, array(
                  'required' => false,
                  'class' => 'Adservice\PartnerBundle\Entity\Shop',
                  'choice_label' => 'name',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($id_catserv, $id_partner) {
                                                return $er->createQueryBuilder('s')
                                                          ->orderBy('s.name', 'ASC')
                                                          ->where('s.active = 1')
                                                          ->andWhere('s.category_service'.$id_catserv.' OR s.id = 1')
                                                          ->andWhere('s.partner'.$id_partner.' OR s.id = 1'); }))

            ->add('infotech', CheckboxType::class, array('required' => false))

            ->add('ad_service_plus', CheckboxType::class, array('required' => false))
            ;
        }   
    }

    public function getBlockPrefix()
    {
        return 'workshopOrder_editOrder';
    }
}