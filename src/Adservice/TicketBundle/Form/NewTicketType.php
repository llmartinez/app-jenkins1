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
                ->add('subsystem', 'entity', array(
                  'required' => true,
                  'class' => 'Adservice\TicketBundle\Entity\Subsystem',
                  'property' => 'name',
                  'empty_value' => '',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                                                return $er->createQueryBuilder('s')
                                                          ->where('s.id = 0'); }))
                ;
    }
    public function getName()
    {
        return 'ticket_form';
    }
}