<?php
namespace Adservice\TicketBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
                ->add('title')
                ->add('status')
                ->add('importance')
                ;
    }
    public function getName()
    {
        return 'new_ticket_form';
    }
}