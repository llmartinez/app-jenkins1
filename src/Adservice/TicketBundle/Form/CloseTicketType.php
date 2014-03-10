<?php
namespace Adservice\TicketBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class CloseTicketType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
                ->add('solution')
                ->add('importance')
                ->add('subsystem')
                ;
    }
    public function getName()
    {
        return 'close_ticket_form';
    }
}