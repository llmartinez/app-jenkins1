<?php
namespace Adservice\TicketBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class NewTicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($_SESSION['einatech'] == 1) {
            $importance =' =5'; 
        } elseif ($_SESSION['einatech'] == 2) {
            $importance =' != 5 and s.id != 6'; 
        } elseif ($_SESSION['einatech'] == 3) {
            $importance =' != 5'; 
        }
        else { 
            $importance = ' != 0';            
        }
        unset($_SESSION['einatech']);
        $builder
                ->add('description', TextareaType::class)
                ->add('importance', EntityType::class , array(
                  'required' => true,
                  'class' => 'Adservice\TicketBundle\Entity\Importance',
                  'choice_label' => 'importance',
                  'choice_translation_domain' => null,
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($importance){
                                                return $er->createQueryBuilder('s')
                  ->where('s.id'.$importance);}))
                ->add('subsystem',  EntityType::class , array(
                  'required' => true,
                  'class' => 'Adservice\TicketBundle\Entity\Subsystem',
                  'choice_label' => 'name',
                  'placeholder' => '',
                  'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                                                return $er->createQueryBuilder('s')
                                                          ->where('s.id = 0'); }))
                ;
    }
    public function getBlockPrefix()
    {
        return 'ticket_form';
    }
}
