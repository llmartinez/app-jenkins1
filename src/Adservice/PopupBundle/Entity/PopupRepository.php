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

        $query = "SELECT p FROM PopupBundle:Popup p WHERE p.startdate_at <= '".$date->format("Y-m-d H:i:s")."' AND p.enddate_at >= '".$date->format("Y-m-d H:i:s")."' AND p.role = ".$role->getId()." AND p.active = '1' ".$country;

        $consulta = $em->createQuery($query);
        $results = $consulta->getResult();

        $size = sizeOf($results);
        if($size > 1) {
            $popup = new Popup();
            $popup->setName('POPUP');
            $desc = '';
            foreach ($results as $result){
                $desc = $desc.$result->getDescription().'<br>';
            }
            $popup->setDescription($desc);
            return array('0' => $popup);
        }
        else{
            return $results;
        }
    }
}