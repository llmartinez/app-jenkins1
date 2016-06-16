<?php
namespace Adservice\UtilBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class DocumentType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
//        $builder->add('file', 'file', array('required' => false));
        $builder
                ->add('file')
        ;

    }
    public function getName()
    {
        return 'new_file_form';
    }
}