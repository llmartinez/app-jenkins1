<?php
namespace Adservice\TicketBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CloseTicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('solution', 'textarea')
                ->add('importance')
                ->add('subsystem')
                ;
    }
    public function getName()
    {
        return 'close_ticket_form';
    }
}