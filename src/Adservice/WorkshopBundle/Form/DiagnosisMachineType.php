<?php

namespace Adservice\WorkshopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class DiagnosisMachineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Recojemos variables de sesion para fitlrar los resultados del formulario
//        if (isset($_SESSION['id_country'])) { $id_country = $_SESSION['id_country'];unset($_SESSION['id_country']);} else { $id_country = ' != 0';}
//        
        $builder
            ->add('name')
//            ->add('country', 'entity', array(
//                  'required' => true,
//                  'class' => 'Adservice\UtilBundle\Entity\Country',
//                  'choice_label' => 'country',
//                  'empty_value' => '',
//                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($id_country) {
//                                                return $er->createQueryBuilder('c')
//                                                          ->orderBy('c.country', 'ASC')
//                                                          ->where('c.id'.$id_country); }))
            ->add('category_service', 'entity', array(
                  'required' => false,
                  'class' => 'Adservice\UserBundle\Entity\CategoryService',
                  'choice_label' => 'category_service',
                  'placeholder' => '',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                                                return $er->createQueryBuilder('cs')
                                                          ->orderBy('cs.category_service', 'ASC')
                                                          ; }))
            ->add('active', 'checkbox', array(
                  'required' => false,
                  'attr'     => array('checked'   => 'checked')
                ))
        ;
    }

    public function getName()
    {
        return 'adservice_workshopbundle_diagnosis_machinetype';
    }
}