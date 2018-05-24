<?php
namespace Adservice\CarBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('year', TextType::class, array(
                    'required' => false,
                    'read_only' => ($options['status'] == 'verified'),
                    'attr' => array('class' => 'form-control asr_form-control', 'placeholder' => 'year')
                ))
                ->add('motor', TextType::class, array(
                    'required' => false,
                    'read_only' => ($options['status'] == 'verified' || $options['origin'] != 'custom'),
                    'attr' => array('class' => 'form-control asr_form-control', 'placeholder' => 'motor')
                ))
                ->add('kW', TextType::class, array(
                    'required' => false,
                    'read_only' => ($options['status'] == 'verified' || $options['origin'] != 'custom'),
                    'attr' => array('class' => 'form-control asr_form-control', 'placeholder' => 'kw')
                ))
                ->add('displacement', TextType::class, array(
                    'required' => false,
                    'read_only' => ($options['status'] == 'verified' || $options['origin'] != 'custom'),
                    'attr' => array('class' => 'form-control asr_form-control', 'placeholder' => 'displacement')
                ))
                ->add('vin', TextType::class, array(
                    'required' => true,
                    'read_only' => ($options['status'] == 'verified' || $options['origin'] != 'custom'),
                    'attr' => array( 'maxlength' => '17', 'class' => 'form-control asr_form-control', 'placeholder' => 'vin')
                ))
                ->add('plateNumber', TextType::class, array(
                    'required' => true,
                    'read_only' => ($options['status'] == 'verified'),
                    'attr' => array('class' => 'form-control asr_form-control', 'placeholder' => 'plate_number')
                ))
                ->add('origin', HiddenType::class, array('required' => false, 'empty_data' => 'custom'))
                ->add('variants', HiddenType::class, array('required' => false, 'empty_data' => 1))
                ->add('status', HiddenType::class, array('required' => false, 'empty_data' => 'undefined'))
                ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'status' => 'undefined',
            'origin' => 'custom'
        ));
    }


    public function getBlockPrefix()
    {
        return 'new_car_form';
    }
}