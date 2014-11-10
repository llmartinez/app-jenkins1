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

        if($role == 'ROLE_SUPER_ADMIN' OR $role == 'ROLE_SUPER_AD') $country = "";
        else $country = " AND p.country = '".$user->getCountry()->getId()."' ";

        $query = "SELECT p FROM PopupBundle:Popup p WHERE p.startdate_at <= '".$date->format("Y-m-h")."' AND p.enddate_at >= '".$date->format("Y-m-h")."e' AND p.role = ".$role->getId()." ".$country;

        $consulta = $em->createQuery($query);
        // $consulta->setParameter('start_date', $date);
        // $consulta->setParameter('end_date', $date);
        // $consulta->setParameter('role', $role->getId());
        // if($role != 'ROLE_SUPER_ADMIN' AND $role != 'ROLE_SUPER_AD') $consulta->setParameter('country', $user->getCountry()->getId());

        return $consulta->getResult();
    }
}