<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use AppBundle\Utils\Utils as Utils;
use AppBundle\Utils\UtilsWorkshop as UtilsWorkshop;

class ShopType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['attr']['em'];
        $ids = $options['attr']['ids'];
        $services = $options['attr']['tokenService'];

        $builder
            ->add('service', 'choice'  , array('choices' => Utils::getServices($ids),
                                               'expanded'=> 'true',
                                               'multiple'=> 'true',
                                               'required'=>'required',
                                               'translation_domain' => 'messages'
                                              ))
            
            ->add('Partner'      , 'entity' , array(
                                                    'class' => 'AppBundle:Partner',
                                                    'query_builder' => UtilsWorkshop::getFilteredPartner($em, $services),
                                                    'empty_value' => 'SelectValue',
                                                    'attr' => array('class' => 'required')
                                                   ))

            ->add('name'   , 'text'    , array('required'=> 'required', 'translation_domain' => 'messages' ))
            ->add('active' , 'checkbox', array('required'=> false, 'attr' => array('checked'   => 'checked'), 'translation_domain' => 'messages' ))

            ->add('submit', 'submit')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }
}