<?php

namespace Adservice\OrderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class WorkshopNewOrderType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        // Recojemos variables de sesion para fitlrar los resultados del formulario
        if (isset($_SESSION['id_partner'])) { $id_partner = $_SESSION['id_partner'];unset($_SESSION['id_partner']);} else { $id_partner = ' != 0';}
        if (isset($_SESSION['id_country'])) { $id_country = $_SESSION['id_country'];unset($_SESSION['id_country']);} else { $id_country = ' != 0';}
        if (isset($_SESSION['id_catserv'])) { $id_catserv = $_SESSION['id_catserv'];unset($_SESSION['id_catserv']);} else { $id_catserv = ' != 0';}
        if (isset($_SESSION['id_shop'   ])
        and $_SESSION['id_shop'] != ' = 1') { $id_shop    = $_SESSION['id_shop'   ];unset($_SESSION['id_shop'   ]);$s_empty=false;} else { $id_shop=' != 0';$s_empty='';}

        $builder
            ->add('name')
            ->add('cif','text', array('required' => true))
            // ->add('partner', 'choice' , array('required' => true, 'empty_value' => 'Selecciona un socio'))
            ->add('code_workshop')
            ->add('typology', 'entity', array(
                  'required' => true,
                  'class' => 'Adservice\WorkshopBundle\Entity\Typology',
                  'property' => 'name',
                  'empty_value' => '',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($id_catserv) {
                                                return $er->createQueryBuilder('t')
                                                          ->orderBy('t.name', 'ASC')
                                                          ->where('t.active = 1')
                                                          ->andWhere('t.category_service'.$id_catserv); }))
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

        
          $builder
              ->add('infotech', 'checkbox', array('required' => false))
              ->add('ad_service_plus', 'checkbox', array('required' => false))
              ->add('shop', 'entity', array(
                    'required' => false,
                    'class' => 'Adservice\PartnerBundle\Entity\Shop',
                    'property' => 'name',
                    'empty_value' => $s_empty,
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($id_catserv, $id_country, $id_partner, $id_shop) {
                                                  return $er->createQueryBuilder('s')
                                                            ->orderBy('s.name', 'ASC')
                                                            ->where('s.active = 1')
                                                            ->andWhere('s.category_service'.$id_catserv.' OR s.id = 1')
                                                            ->andWhere('s.country'.$id_country.' OR s.id = 1')
                                                            ->andWhere('s.partner'.$id_partner.' OR s.id = 1')
                                                            ->andWhere('s.id'.$id_shop.''); }))
              ;
        
    }

    public function getName()
    {
        return 'workshopOrder_newOrder';
    }
}