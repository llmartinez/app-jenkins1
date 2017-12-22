<?php

namespace Adservice\WorkshopBundle\Form;

use Adservice\WorkshopBundle\Form\DiagnosisMachineType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

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
            ->add('code_workshop', 'number')
            ->add('cif', 'text', array('required' => true))
            ->add('category_service', 'entity', array(
                  'required' => false,
                  'class' => 'Adservice\UserBundle\Entity\CategoryService',
                  'choice_label' => 'category_service',
                  'placeholder' => '',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use($id_catserv) {
                                                return $er->createQueryBuilder('cs')
                                                          ->orderBy('cs.category_service', 'ASC')
                                                            ->where('cs.id '.$id_catserv)
                                                          ; }))
            ->add('partner', 'entity', array(
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
            ->add('shop', 'entity', array(
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
            ->add('typology', 'entity', array(
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
            ->add('diagnosis_machines', 'entity', array(
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
            ->add('contact', 'text', array('required' => true))
            ->add('internal_code', 'text', array('required' => false))
            ->add('commercial_code', 'text', array('required' => false))
            ->add('ad_service_plus', 'checkbox', array('required' => false))
//            ->add('active', 'checkbox', array('required' => false))
            ->add('test', 'checkbox', array('required' => false))
            ->add('endtest_at', 'date', array('format' => 'dd-MM-yyyy'))
            ->add('haschecks', 'checkbox', array('required' => false))
            ->add('numchecks', 'integer', array('required' => false))
            ->add('infotech', 'checkbox', array('required' => false))
            ->add('observation_workshop', 'textarea', array('required' => false))
            ->add('observation_assessor', 'textarea', array('required' => false))
            ->add('observation_admin', 'textarea', array('required' => false))
            ->add('conflictive', 'checkbox', array('required' => false))
            //CONTACT
            ->add('country', 'entity', array(
                  'required' => true,
                  'class' => 'Adservice\UtilBundle\Entity\Country',
                  'choice_label' => 'country',
                  'placeholder' => '',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er)  {
                                                return $er->createQueryBuilder('c')
                                                          ->orderBy('c.country', 'ASC'); }))
            ->add('region', 'text', array('required' => false))
            ->add('city')
            ->add('address')
            ->add('postal_code')
            ->add('phone_number_1', 'text')
            ->add('phone_number_2', 'text', array('required' => false))
            ->add('mobile_number_1', 'text', array('required' => false))
            ->add('mobile_number_2', 'text', array('required' => false))
            ->add('fax', 'text', array('required' => false))
            ->add('email_1','email')
            ->add('email_2','email', array('required' => false))
        ;
                                                          
        if (isset($_SESSION['code_billing'])) $builder->add('code_billing', 'text', array('required' => false));
    }

    public function getBlockPrefix()
    {
        return 'adservice_workshopbundle_workshoptype';
    }
}