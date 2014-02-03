<?php
namespace Adservice\TicketBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class PostType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('ticket')
                ->add('owner')
                ->add('message', 'textarea')
                ;
    }
    public function getName()
    {
        return 'new_post_form';
    }
}