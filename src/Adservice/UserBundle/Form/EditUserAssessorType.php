<?php

namespace Adservice\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Security\Core\SecurityContext;

class EditUserAssessorType extends AbstractType {


    public function buildForm(FormBuilder $builder, array $options)
    {
        // Recojemos variables de sesion para fitlrar los resultados del formulario
        if (isset($_SESSION['id_country'])) { $id_country = $_SESSION['id_country'];unset($_SESSION['id_country']);} else { $id_country = ' != 0';}

        $builder
            ->add('username')
            ->add('name')
            ->add('surname')
            ->add('active', 'checkbox', array('required' => false))
            // ->add('charge')
            //CONTACT
            ->add('country_service', 'entity', array(
                  'required' => true,
                  'class' => 'Adservice\UtilBundle\Entity\CountryService',
                  'property' => 'country',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($id_country) {
                                                return $er->createQueryBuilder('c')
                                                          ->orderBy('c.country', 'ASC'); }))
            ->add('country', 'entity', array(
                  'required' => true,
                  'class' => 'Adservice\UtilBundle\Entity\Country',
                  'property' => 'country',
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
        ;

        return $builder;
    }

    public function getName() {
//        return 'adservice_userbundle_usertype';
        return 'assessor_type';
    }

}