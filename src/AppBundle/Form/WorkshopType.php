<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Doctrine\ORM\EntityRepository;

use AppBundle\Utils\UtilsWorkshop as UtilsWorkshop;

class WorkshopType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['attr']['em'];
        $services = $options['attr']['tokenService'];
        if(isset($options['attr']['idPartner'])) $idPartner = $options['attr']['idPartner'];
        else $idPartner = '0';

        $builder
            ->add('name'         , 'text'   , array('required'=> 'required', 'attr' => array('class' => 'required')))
            ->add('Partner'      , 'entity' , array(
                                                    'class' => 'AppBundle:Partner',
                                                    'query_builder' => UtilsWorkshop::getFilteredPartner($em, $services, $idPartner),
                                                    'empty_value' => 'SelectValue',
                                                    'attr' => array('class' => 'required'))
                                                    )
            ->add('codeWorkshop' , 'integer', array('required'=> 'required', 'attr' => array('min' => 1)))
            ->add('Typology'     , 'entity' , array(
                                                    'class' => 'AppBundle:Typology',
                                                    'query_builder' => UtilsWorkshop::getFilteredEntity($em, 'Typology', $services),
                                                    'empty_value' => 'SelectValue',
                                                    'attr' => array('class' => 'required'))
                                                    )
            ->add('Shop'         , 'entity' , array(
                                                    'class' => 'AppBundle:Shop',
                                                    'query_builder' => UtilsWorkshop::getFilteredEntity($em, 'Shop', $services, $idPartner),
                                                    'empty_value' => 'SelectValue',
                                                    'attr' => array('class' => 'required'))
                                                    )
            ->add('DiagnosisMachine', 'entity' , array('required'=> false,
                                                       'multiple' => true,
                                                       'class' => 'AppBundle:DiagnosisMachine',
                                                       'query_builder' => UtilsWorkshop::getFilteredEntity($em, 'DiagnosisMachine', $services),
                                                       'empty_value' => 'SelectValue')
                                                    )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }

}