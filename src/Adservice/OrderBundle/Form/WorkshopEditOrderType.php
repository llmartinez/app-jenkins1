<?php

namespace Adservice\OrderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class WorkshopEditOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Recojemos variables de sesion para fitlrar los resultados del formulario
        if (isset($_SESSION['id_partner'])) { $id_partner = $_SESSION['id_partner'];unset($_SESSION['id_partner']);} else { $id_partner = ' != 0';}
        if (isset($_SESSION['id_country'])) { $id_country = $_SESSION['id_country'];unset($_SESSION['id_country']);} else { $id_country = ' != 0';}
        if (isset($_SESSION['id_catserv'])) { $id_catserv = $_SESSION['id_catserv'];unset($_SESSION['id_catserv']);} else { $id_catserv = ' != 0';}

        $builder
            ->add('name','text', array('required' => false))
            ->add('cif','text', array('required' => false))
            ->add('partner', 'entity', array(
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
            ->add('typology', 'entity', array(
                              'required' => true,
                              'class' => 'Adservice\WorkshopBundle\Entity\Typology',
                              'choice_label' => 'name',
                              'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($id_catserv) {
                                                return $er->createQueryBuilder('t')
                                                          ->orderBy('t.name', 'ASC')
                                                          ->where('t.active = 1')
                                                          ->andWhere('t.category_service'.$id_catserv); }))
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
            ->add('test', 'checkbox', array('required' => false))
            ->add('haschecks', 'checkbox', array('required' => false))
            ->add('numchecks', 'integer', array('required' => false))
            ->add('internal_code', 'text', array('required' => false))
            ->add('commercial_code', 'text', array('required' => false))
             //CONTACT
            ->add('country', 'entity', array(
                  'required' => true,
                  'class' => 'Adservice\UtilBundle\Entity\Country',
                  'choice_label' => 'country',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($id_country) {
                                                return $er->createQueryBuilder('c')
                                                          ->orderBy('c.country', 'ASC')
                                                          ->where('c.id'.$id_country); }))
            ->add('region')
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
        
        if($id_catserv != ' = 3'){
            $builder
            ->add('shop', 'entity', array(
                  'required' => false,
                  'class' => 'Adservice\PartnerBundle\Entity\Shop',
                  'choice_label' => 'name',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($id_catserv, $id_partner) {
                                                return $er->createQueryBuilder('s')
                                                          ->orderBy('s.name', 'ASC')
                                                          ->where('s.active = 1')
                                                          ->andWhere('s.category_service'.$id_catserv.' OR s.id = 1')
                                                          ->andWhere('s.partner'.$id_partner.' OR s.id = 1'); }))

            ->add('infotech', 'checkbox', array('required' => false))

            ->add('ad_service_plus', 'checkbox', array('required' => false))
            ;
        }   
    }

    public function getName()
    {
        return 'workshopOrder_editOrder';
    }
}