<?php

namespace Adservice\StatisticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    /**
     * Estadisticas chorras en modo de prueba
     */
    public function generalStatisticsAction(){
        $em = $this->getDoctrine()->getEntityManager();
        $num_users_adservice = $em->getRepository("StatisticBundle:StatisticRepository")->getNumUsers();
        
    }
}
