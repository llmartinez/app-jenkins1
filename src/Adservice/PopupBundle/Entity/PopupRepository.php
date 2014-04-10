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
    public function findPopupByDate($date, $role){

        $em = $this->getEntityManager();
        $query = $em->createQuery("SELECT p
                                   FROM PopupBundle:Popup p
                                   WHERE p.startdate_at <= :start_date
                                   AND   p.enddate_at >= :end_date
                                   AND   p.role = :role
                                  ");
        $query->setParameter('start_date', $date);
        $query->setParameter('end_date', $date);
        $query->setParameter('role', $role);

        return $query->getResult();
    }
}