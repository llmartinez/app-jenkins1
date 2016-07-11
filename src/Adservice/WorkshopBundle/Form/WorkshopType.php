<?php

namespace Adservice\WorkshopBundle\Form;

use Adservice\WorkshopBundle\Form\DiagnosisMachineType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class WorkshopType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        // Recojemos variables de sesion para fitlrar los resultados del formulario
        if (isset($_SESSION['id_partner'])) { $id_partner = $_SESSION['id_partner'];unset($_SESSION['id_partner']);} else { $id_partner = ' != 0';}
        if (isset($_SESSION['id_country'])) { $id_country = $_SESSION['id_country'];unset($_SESSION['id_country']);} else { $id_country = ' != 0';}

        $builder
            ->add('name')
            ->add('code_workshop', 'number')
            ->add('cif', 'text', array('required' => true))
            ->add('partner', 'entity', array(
                  'required' => true,
                  'class' => 'Adservice\PartnerBundle\Entity\Partner',
                  'property' => 'name',
                  'empty_value' => '',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($id_country, $id_partner) {
                                                return $er->createQueryBuilder('s')
                                                          ->orderBy('s.name', 'ASC')
                                                          ->where('s.active = 1')
                                                          ->andWhere('s.country'.$id_country); }))
            ->add('shop', 'entity', array(
                  'required' => true,
                  'class' => 'Adservice\PartnerBundle\Entity\Shop',
                  'property' => 'name',
                  'empty_value' => false,
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($id_country, $id_partner) {
                                                return $er->createQueryBuilder('s')
                                                          ->orderBy('s.name', 'ASC')
                                                          ->where('s.active = 1')
                                                          ->andWhere('s.country'.$id_country)
                                                          ->andWhere('s.partner'.$id_partner.' OR s.id = 1'); }))
            ->add('typology', 'entity', array(
                  'required' => true,
                  'class' => 'Adservice\WorkshopBundle\Entity\Typology',
                  'property' => 'name',
                  'empty_value' => '',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($id_country) {
                                                return $er->createQueryBuilder('s')
                                                          ->orderBy('s.name', 'ASC')
                                                          ->where('s.active = 1')
                                                          ->andWhere('s.country'.$id_country); }))
            ->add('diagnosis_machines', 'entity', array(
                  'required' => false,
                  'multiple' => true,
                  'class' => 'Adservice\WorkshopBundle\Entity\DiagnosisMachine',
                  'property' => 'name',
                  'empty_value' => '',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($id_country) {
                                                return $er->createQueryBuilder('s')
                                                          ->orderBy('s.name', 'ASC')
                                                          ->where('s.active = 1')
                                                          ->andWhere('s.country'.$id_country); }))
            ->add('contact', 'text', array('required' => true))
            ->add('internal_code', 'text', array('required' => false))
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
                  'property' => 'country',
                  'empty_value' => '',
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
    }

    public function getName()
    {
        return 'adservice_workshopbundle_workshoptype';
    }
}