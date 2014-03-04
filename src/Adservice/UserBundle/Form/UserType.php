<?php

namespace Adservice\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class UserType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder = UserAdminType::getbasicUserType($builder);
        $builder->add('workshop');
    }

    public function getName()
    {
        return 'adservice_userbundle_usertype';
    }
}