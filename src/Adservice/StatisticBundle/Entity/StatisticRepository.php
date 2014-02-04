<?php

namespace Adservice\StatisticBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Adservice\StatisticBundle\Entity\Statistic;
use Adservice\StatisticBundle\Entity\StatisticRepository;

class StatisticRepository extends EntityRepository{
    
//    /**
//     * 
//     * @param Datetime('Y-m-d H:i:s') $date
//     * @param Boolean $only_one con valor TRUE indicamos que solo queremos 1 resultado, con FALSE devuelve todos
//     * @return Popup
//     */
//    public function findPopupByDate($date, $only_one=null){
//
//        $em = $this->getEntityManager();
//        $query = $em->createQuery("SELECT p
//                                   FROM PopupBundle:Popup p
//                                   WHERE p.startdate_at <= :start_date AND
//                                         p.enddate_at >= :end_date"
//                                  );
//        $query->setParameter('start_date', $date);
//        $query->setParameter('end_date', $date);
//        if ($only_one==true) $query->setMaxResults(1);
//        
//        return $query->getResult();
//    }
    
   public function getNumUsers(){
        $em = $this->getEntityManager();
        $query = $em->createQuery("SELECT COUNT(u.id) as num_users
                                   FROM UserBundle:User u"
                                 );
        
        return $query->getResult();
   }
}