<?php

namespace Adservice\PartnerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class PartnerType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Recojemos variables de sesion para fitlrar los resultados del formulario
        if (isset($_SESSION['id_country'])) { $id_country = $_SESSION['id_country'];unset($_SESSION['id_country']);} else { $id_country = ' != 0';}

        $builder
            ->add('name')
            ->add('code_partner')
            ->add('cif')
            ->add('active', CheckboxType::class, array('required' => false))
             //CONTACT
            ->add('country', EntityType::class, array(
                  'required' => true,
                  'class' => 'Adservice\UtilBundle\Entity\Country',
                  'choice_label' => 'country',
                  'choice_translation_domain' => null,
                  'placeholder' => '',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($id_country) {
                                                return $er->createQueryBuilder('c')
                                                          ->orderBy('c.country', 'ASC'); }))
            ->add('region', TextType::class, array('required' => false))
            ->add('city', TextType::class)
            ->add('address')
            ->add('postal_code')
            ->add('phone_number_1', TextType::class)
            ->add('phone_number_2', TextType::class, array('required' => false))
            ->add('mobile_number_1', TextType::class, array('required' => false))
            ->add('mobile_number_2', TextType::class, array('required' => false))
            ->add('fax', TextType::class, array('required' => false))
            ->add('email_1',EmailType::class)
            ->add('email_2',EmailType::class, array('required' => false))
            ->add('contact', TextType::class, array('required' => false))
            ->add('observations',  TextareaType::class, array('required' => false))
        ;
        
        if (isset($_SESSION['code_billing'])) $builder->add('code_billing', TextType::class, array('required' => false));
    }

    public function getBlockPrefix() {
        return 'adservice_partnerbundle_partnertype';
    }

}
