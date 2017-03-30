<?php
namespace Adservice\TicketBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class EditTicketType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        if ($_SESSION['einatech'] == 1) {
            $importance =' =5'; 
        } elseif ($_SESSION['einatech'] == 2) {
            $importance =' != 5'; 
        }
        else { 
            $importance = ' != 0';            
        }
        unset($_SESSION['einatech']);
        $builder
//                ->add('importance','entity', array(
//                  'required' => true,
//                  'class' => 'Adservice\TicketBundle\Entity\Importance',
//                  'property' => 'importance',
//                  'empty_value' => '...'))
                ->add('importance', 'entity', array(
                  'required' => true,
                  'class' => 'Adservice\TicketBundle\Entity\Importance',
                  'property' => 'importance',
                  'empty_value' => '...',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($importance){
                                                return $er->createQueryBuilder('s')
                  ->where('s.id'.$importance);}))

                ->add('subsystem', 'entity', array(
                  'required' => true,
                  'class' => 'Adservice\TicketBundle\Entity\Subsystem',
                  'property' => 'name',
                  'empty_value' => '...',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                                                return $er->createQueryBuilder('s')
                                                          ->orderBy('s.name', 'ASC')
                                                          ->where('s.id = 0'); }))
                ;
    }
    public function getName()
    {
        return 'edit_ticket_form';
    }
}