<?php
namespace Adservice\UtilBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        $builder->add('file', 'file', array('required' => false));
        $builder
                ->add('file')
        ;

    }
    public function getBlockPrefix()
    {
        return 'new_file_form';
    }
}