<?php
namespace Adservice\TicketBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class IncidenceType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
                ->add('status')
                ->add('importance', 'text')
                ->add('description', 'textarea')
                ->add('solution', 'textarea')
                ;
    }
    public function getName()
    {
        return 'new_incedence_form';
    }
}