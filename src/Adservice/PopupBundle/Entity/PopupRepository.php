<?php

namespace Adservice\PopupBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Adservice\PopupBundle\Entity\Popup;
use Adservice\PopupBundle\Entity\PopupRepository;

class PopupRepository extends EntityRepository{

    /**
     *
     * @param Datetime('Y-m-d H:i:s') $date
     * @return Popup
     */
    public function findPopupByDate($date, $user){

        $em = $this->getEntityManager();
        $role = $user->getRoles();
        $role = $role[0];

        $query = "SELECT p FROM PopupBundle:Popup p
                  WHERE p.startdate_at <= :start_date
                  AND   p.enddate_at >= :end_date
                  AND   p.role = :role
                 ";

        if($role == 'ROLE_SUPER_ADMIN') $country = "";
        else $country = "AND p.country = ".$user->getCountry()->getId();

        $consulta = $em->createQuery($query.$country);
        $consulta->setParameter('start_date', $date);
        $consulta->setParameter('end_date', $date);
        $consulta->setParameter('role', $role->getId());

        return $consulta->getResult();
    }
}