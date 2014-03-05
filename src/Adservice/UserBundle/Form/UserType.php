<?php

namespace Adservice\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class UserType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder = UserAdminType::getbasicUserType($builder);
        $builder->add('workshop', 'entity', array('class'       => 'WorkshopBundle:Workshop',
                                                  'property'    => 'name',
                                                  'read_only'   => true));
            
    }

    public function getName()
    {
        return 'adservice_userbundle_usertype';
    }
}