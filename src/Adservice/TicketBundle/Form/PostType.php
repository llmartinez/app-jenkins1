<?php
namespace Adservice\TicketBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class PostType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('message', 'textarea')
                ;
    }
    public function getName()
    {
        return 'ticket_form';
    }
}