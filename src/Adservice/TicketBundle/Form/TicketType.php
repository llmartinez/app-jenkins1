<?php
namespace Adservice\TicketBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
                ->add('description')
                ->add('importance')
                ->add('workshop')
                ->add('subsystem')
                ;
    }
    public function getName()
    {
        return 'new_ticket_form';
    }
}