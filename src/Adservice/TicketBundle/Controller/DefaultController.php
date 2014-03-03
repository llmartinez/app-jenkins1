<?php

namespace Adservice\TicketBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    /**
     * Asigna el usuario que ha creado la clase y la fecha de la creación.
     * @param Class $entity
     * @param Class $user
     * @return Class
     */
    public static function newEntity($entity, $user){
        $entity->setOwner($user);
        $entity->setCreatedAt(new \DateTime(\date("Y-m-d H:i:s")));
        return $entity;
    }

    /**
     * Asigna el usuario que ha modificado la clase y la fecha de la modificación.
     * @param EntityManager $em
     * @param Class $entity
     * @param Bool $auto_flush true: aplica cambios en BBDD
     * @return Bool
     */
    public static function saveEntity($em, $entity, $user, $auto_flush=true)
    {
        $entity->setModifiedBy($user);
        $entity->setModifiedAt(new \DateTime(\date("Y-m-d H:i:s")));
        $em->persist($entity);
        if($auto_flush) $em->flush();
        return true;
    }
}
