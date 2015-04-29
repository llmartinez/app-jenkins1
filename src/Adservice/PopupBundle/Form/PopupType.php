<?php

namespace Adservice\PopupBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class PopupType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        // Recojemos variables de sesion para fitlrar los resultados del formulario
        if (isset($_SESSION['id_country'])) { $id_country = $_SESSION['id_country'];unset($_SESSION['id_country']);} else { $id_country = ' != 0';}
        if (isset($_SESSION['role'])) 
        { 
            $role = " = '".$_SESSION['role']."' ";
            unset($_SESSION['role']);
            
            if ($role != 'ROLE_SUPER_ADMIN') { 
                $role = " != 'ROLE_SUPER_ADMIN'"; 
            }
        } else { $role = ' != 0';}
        
        $builder
                ->add('name')
                ->add('description', 'textarea')
                ->add('role', 'entity', array(
                  'required' => true,
                  'class' => 'Adservice\UserBundle\Entity\Role',
                  'property' => 'name',
                  'empty_value' => '',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($role) {
                                                return $er->createQueryBuilder('c')
                                                          ->where('c.name'.$role); }))
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
