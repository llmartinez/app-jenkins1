<?php

namespace Adservice\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    /**
     * Busca todos los usuarios que pertenecen a un partner
     * Añade los usuarios de los talleres del partner
     * @param Int $partner
     */
    public function findByPartner($partner)
    {
        //users que dependen directamente del partner
        $em = $this->getEntityManager();
        $query = $em->createQuery("SELECT u 
                                   FROM UserBundle:User u
                                   WHERE u.partner = :partner
                                  ");
        $query->setParameter('partner', $partner);
        $users = $query->getResult();
        
        //users que dependen del taller
        $workshops = $partner->getWorkshops();
        foreach ($workshops as $workshop) {
            $users_workshop = $workshop->getUsers();
            foreach ($users_workshop as $user_workshop) {
                array_push($users, $user_workshop);
            }
        }

        return $users;
    }
}