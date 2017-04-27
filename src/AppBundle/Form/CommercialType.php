<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use AppBundle\Utils\Utils as Utils;
use AppBundle\Utils\UtilsWorkshop as UtilsWorkshop;

class CommercialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['attr']['em'];
        $services = $options['attr']['tokenService'];
        if(isset($options['attr']['idPartner'])) $idPartner = $options['attr']['idPartner'];
        else $idPartner = '0';

        $builder
            ->add('Partner'      , 'entity' , array(
                                                    'class' => 'AppBundle:Partner',
                                                    'query_builder' => UtilsWorkshop::getFilteredPartner($em, $services, $idPartner),
                                                    'empty_value' => 'SelectValue',
                                                    'attr' => array('class' => 'required')
                                                    ))

            ->add('Shop'         , 'entity' , array(
                                                    'class' => 'AppBundle:Shop',
                                                    'query_builder' => UtilsWorkshop::getFilteredEntity($em, 'Shop', $services, $idPartner),
                                                    'empty_value' => 'SelectValue',
                                                    'required'=> false
                                                    ))

            ->add('name'   , 'text'    , array('required'=> 'required', 'translation_domain' => 'messages' ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }
}