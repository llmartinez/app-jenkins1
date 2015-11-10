<?php

namespace Adservice\PartnerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Validator\Constraints\NotNull;

class PartnerType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options)
    {
        // Recojemos variables de sesion para fitlrar los resultados del formulario
        if (isset($_SESSION['id_country'])) { $id_country = $_SESSION['id_country'];unset($_SESSION['id_country']);} else { $id_country = ' != 0';}

        $builder
            ->add('name')
            ->add('code_partner')
            ->add('active', 'checkbox', array('required' => false))
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
            ->add('region', 'text')
            ->add('city', 'text')
            ->add('address')
            ->add('postal_code')
            ->add('phone_number_1', 'text')
            ->add('phone_number_2', 'text', array('required' => false))
            ->add('movile_number_1', 'text', array('required' => false))
            ->add('movile_number_2', 'text', array('required' => false))
            ->add('fax', 'text', array('required' => false))
            ->add('email_1','email')
            ->add('email_2','email', array('required' => false))
            ->add('contact', 'text', array('required' => false))
            ->add('observations', 'textarea', array('required' => false))
        ;
    }

    public function getName() {
        return 'adservice_partnerbundle_partnertype';
    }

}
