<?php

namespace Adservice\PopupBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PopupType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        // Recojemos variables de sesion para fitlrar los resultados del formulario
        if (isset($_SESSION['id_catserv'])) { $id_catserv = $_SESSION['id_catserv'];unset($_SESSION['id_catserv']);} else { $id_catserv = ' != 0';}
        if (isset($_SESSION['id_country'])) { $id_country = $_SESSION['id_country'];unset($_SESSION['id_country']);} else { $id_country = ' != 0';}
        $role = " != 'ROLE_SUPER_ADMIN'";
        if (isset($_SESSION['role']) and ($_SESSION['role'] == 'ROLE_SUPER_ADMIN')){ $role = " != '0'"; }
        unset($_SESSION['role']);
        
        $builder
                ->add('name')
                ->add('description', 'textarea')
                ->add('role', 'entity', array(
                  'required' => true,
                  'class' => 'Adservice\UserBundle\Entity\Role',
                  'property' => 'name',
                  'empty_value' => '',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($role) {
                                                return $er->createQueryBuilder('r')
                                                          ->where('r.name'.$role); }))
                ->add('category_service', 'entity', array(
                  'required' => true,
                  'class' => 'Adservice\UserBundle\Entity\CategoryService',
                  'property' => 'category_service',
                  'empty_value' => '',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($id_catserv) {
                                                return $er->createQueryBuilder('c')
                                                          ->orderBy('c.category_service', 'ASC')
                                                          ->where('c.id'.$id_catserv); }))
                ->add('country', 'entity', array(
                  'required' => true,
                  'class' => 'Adservice\UtilBundle\Entity\Country',
                  'property' => 'country',
                  'empty_value' => '',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($id_country) {
                                                return $er->createQueryBuilder('c')
                                                          ->orderBy('c.country', 'ASC')
                                                          ->where('c.id'.$id_country); }))
                ->add('startdate_at')
                ->add('enddate_at')
                ->add('active', 'checkbox', array('required' => false))
        ;
    }

    public function getName() {
        return 'adservice_popupbundle_popuptype';
    }

}
