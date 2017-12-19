<?php
namespace Adservice\TicketBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CloseTicketWorkshopType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('solution', 'choice', array('choices'  => $this->getSolutions() , 'required' => false, 'empty_value' => false));
                ;
    }

    public function getName()
    {
        return 'close_ticket_form';
    }

    /**
     * Devuelve una lista de dias de validación de usuario
     * @param  EntityManager $em
     * @return array
     */
    public static function getSolutions()
    {
        $validity = array( '0' => 'ticket.close_as_instructions', '1' => 'ticket.close_irreparable_car', '2' => 'ticket.close_other');
        return $validity;
    }
}