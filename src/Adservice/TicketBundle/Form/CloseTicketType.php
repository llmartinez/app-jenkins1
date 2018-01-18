<?php
namespace Adservice\TicketBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CloseTicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('solution', TextareaType::class)
                ->add('importance')
                ->add('subsystem')
                ;
    }
    public function getBlockPrefix()
    {
        return 'close_ticket_form';
    }
}