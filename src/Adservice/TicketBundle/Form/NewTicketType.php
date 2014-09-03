<?php
namespace Adservice\TicketBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class NewTicketType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
                ->add('description', 'textarea')
                ->add('importance')
                ->add('subsystem')
                ;
    }
    public function getName()
    {
        return 'ticket_form';
    }
}