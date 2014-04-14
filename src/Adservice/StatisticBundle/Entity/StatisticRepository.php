<?php

namespace Adservice\StatisticBundle\Entity;

/**
 * Adservice\StatisticBundle\Entity\StatisticRepository
 */
class StatisticRepository {

    private $num_users;
    private $num_tickets;
    private $num_open_tickets;
    private $num_closed_tickets;


    public function getNumUsers() {
        return $this->num_users;
    }

    public function getNumTickets() {
        return $this->num_tickets;
    }

    public function getNumOpenTickets() {
        return $this->num_open_tickets;
    }

    public function getNumClosedTickets() {
        return $this->num_closed_tickets;
    }

    public function setNumUsers($num_users) {
        $this->num_users = $num_users;
    }

    public function setNumTickets($num_tickets) {
        $this->num_tickets = $num_tickets;
    }

    public function setNumOpenTickets($num_open_tickets) {
        $this->num_open_tickets = $num_open_tickets;
    }

    public function setNumClosedTickets($num_closed_tickets) {
        $this->num_closed_tickets = $num_closed_tickets;
    }

    /********************************************************************************************************
     ********************************************************************************************************
     ****************************************  ZONA SQL *****************************************************
     ********************************************************************************************************
     ********************************************************************************************************/

    /**
     * Devuelve el número de usuarios dentro de ADService
     * @param EntityManager $em
     * @return Integer
     */
    public function getNumUsersInAdservice($em) {
        $query = $em->createQuery("SELECT COUNT(u) FROM UserBundle:User u");
        return $query->getSingleScalarResult();
    }

    /**
     * Devuelve el número de tickets dentro de ADService
     * @param EntityManager $em
     * @return Integer
     */
    public function getTicketsInAdservice($em) {
        $query = $em->createQuery("SELECT COUNT(t) FROM TicketBundle:Ticket t");
        return $query->getSingleScalarResult();
    }

    /**
     * Devuelve el número de tickets segun su $status
     * @param EntityManager $em
     * @param String $status 'closed' o 'open'
     * @return Integer
     */
    public function getNumTicketsByStatus($em, $status){
        $query = $em->createQuery("SELECT COUNT(t.id) FROM TicketBundle:Ticket t
                                   WHERE t.status = :status
                                  ");
        if ($status == 'open') $query->setParameter('status', 1);
        if ($status == 'close') $query->setParameter('status', 2);
        return $query->getSingleScalarResult();
    }

    /**
     * Devuelve el usuario con mas post, y el numero de post que ha hecho
     * @param EntityManager $em
     */
    public function getUserWithMaxNumPost($em){

        // $query = $em->createQuery("SELECT p, u, COUNT(p) num_post
        //                            FROM TicketBundle:Post p JOIN p.owner u
        //                            GROUP BY p.owner
        //                            ORDER BY num_post DESC
        //                           ");
        // $query->setMaxResults(1);
        // return $query->getSingleResult();
    }
}
