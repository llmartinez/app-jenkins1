<?php
namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use AppBundle\Utils\Utils as Utils;
use AppBundle\Utils\Ticket as Ticket;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('service'     , 'choice'  , array('choices' => Utils::getFormServices($options['attr']),
                                                    'expanded'=> 'true',
                                                    'multiple'=> 'true'
                                                    'required'=>'required',
                                                    'translation_domain' => 'messages' ))
            ->add('country'     , 'choice'  , array('choices' => Utils::getCountries(),
                                                    'required'=> 'required',
                                                    'translation_domain' => 'messages' ))
            ->add('language'    , 'choice'  , array('choices' => Utils::getLanguages(),
                                                    'required'=> 'required',
                                                    'translation_domain' => 'messages' ))
            ->add('status'      , 'choice'  , array('choices' => Utils::getStates(),
                                                    'required'=> 'required',
                                                    'translation_domain' => 'messages' ))
            ->add('importance'  , 'choice'  , array('choices' => Ticket::getImportances(),
                                                    'required'=> 'required',
                                                    'translation_domain' => 'messages' ))
            ->add('subsystem'   , 'choice'  , array('choices' => Ticket::getSubsystems(),
                                                    'required'=> false,
                                                    'translation_domain' => 'messages' ))
            ->add('description' , 'text'    , array('required'=> 'required',
                                                    'translation_domain' => 'messages' ))
            //
            // COMPROBAR SI FUNCIONA LINKANDO CON CARFORM
            /*
            ->add('car_fields', 'collection', array(
                    'type' => new CarType(), 
                    'allow_add' => true,))
            */
            ->add('submit', 'submit')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }
}