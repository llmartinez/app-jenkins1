<?php

namespace Adservice\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class EditCommercialType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder = $this->getbasicUserType($builder);
    }

    public static function getbasicUserType($builder)
    {
        // Recojemos variables de sesion para fitlrar los resultados del formulario
        if (isset($_SESSION['role'      ])) { $role = $_SESSION['role'];unset($_SESSION['role']);} else { $role = '0';}
        if (isset($_SESSION['id_partner'])
        and $_SESSION['id_partner'] != ' IN (0)')
                                            { $id_partner = $_SESSION['id_partner'];unset($_SESSION['id_partner']);} else { $id_partner = ' != 0';}
        if (isset($_SESSION['id_country'])) { $id_country = $_SESSION['id_country'];unset($_SESSION['id_country']);} else { $id_country = ' != 0';}
        if (isset($_SESSION['id_catserv'])) { $id_catserv = $_SESSION['id_catserv'];unset($_SESSION['id_catserv']);} else { $id_catserv = ' != 0';}

        $builder
            ->add('username')
            ->add('name')
            ->add('surname')
            ->add('active' , 'checkbox', array('required' => false))
            ->add('partner', 'entity', array(
                  'required' => true,
                  'class' => 'Adservice\PartnerBundle\Entity\Partner',
                  'choice_label' => 'name',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($id_catserv, $id_partner) {
                                                return $er->createQueryBuilder('s')
                                                          ->orderBy('s.name', 'ASC')
                                                          ->where('s.active = 1')
                                                          ->andWhere('s.id'.$id_partner)
                                                          ->andWhere('s.category_service'.$id_catserv); }))
            ->add('language')

            //CONTACT
            ->add('country', 'entity', array(
                  'required' => true,
                  'class' => 'Adservice\UtilBundle\Entity\Country',
                  'choice_label' => 'country',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($id_country) {
                                                return $er->createQueryBuilder('c')
                                                          ->orderBy('c.country', 'ASC'); }))
            ->add('region')
            ->add('city')
            ->add('address')
            ->add('postal_code')
            ->add('phone_number_1' , 'text')
            ->add('phone_number_2' , 'text', array('required' => false))
            ->add('mobile_number_1', 'text', array('required' => false))
            ->add('mobile_number_2', 'text', array('required' => false))
            ->add('fax'            , 'text', array('required' => false))
            ->add('email_1','email')
            ->add('email_2','email', array('required' => false))
            ->add('language')
            ->add('allow_list','checkbox', array('required' => false))
            ->add('allow_order','checkbox', array('required' => false))
        ;

        if($role != '0' and $id_catserv != ' = 3')
        {
          $builder->add('shop', 'entity', array(
                    'required' => false,
                    'class' => 'Adservice\PartnerBundle\Entity\Shop',
                    'choice_label' => 'name',
                    'placeholder' => false,
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($id_catserv, $id_partner) {
                                                  return $er->createQueryBuilder('s')
                                                            ->orderBy('s.name', 'ASC')
                                                            ->where('s.active = 1 OR s.id = 1 ')
                                                            ->andWhere('s.partner'.$id_partner.' OR s.id = 1')
                                                            ->andWhere('s.category_service'.$id_catserv.' OR s.id = 1'); }));
        }
        return $builder;
    }

    public function getBlockPrefix() {
        return 'commercial_type';
    }

}