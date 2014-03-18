<?php
namespace Adservice\TicketBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class EditTicketType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
                ->add('importance')
                ->add('subsystem')
                ;
    }
    public function getName()
    {
        return 'edit_ticket_form';
    }
}