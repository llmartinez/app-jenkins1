<?php

namespace Adservice\StatisticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Adservice\StatisticBundle\Entity\Statistic;

class DefaultController extends Controller {

    /**
     * Listado de estadisticas...muy chorras...
     */
    public function listAction() {
        $em = $this->getDoctrine()->getEntityManager();


        $statistic = new Statistic();
        
        $statistic->setNumUsers($statistic->getNumUsersInAdservice($em));
        $statistic->setNumTickets($statistic->getTicketsInAdservice($em));
        $statistic->setNumOpenTickets($statistic->getNumTicketsByStatus($em, 'open'));
        $statistic->setNumClosedTickets($statistic->getNumTicketsByStatus($em, 'close'));
        $statistic->setUserWithMaxPost($statistic->getUserWithMaxNumPost($em));
        
        return $this->render('StatisticBundle:Default:list.html.twig', array('statistic' => $statistic));
    }
}