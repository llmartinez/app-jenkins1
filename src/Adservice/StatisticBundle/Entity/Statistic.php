<?php

namespace Adservice\StatisticBundle\Entity;

use SensioLabs\Security\SecurityChecker;

/**
 * Adservice\StatisticBundle\Entity\Statistic
 */
class Statistic {

    private $num_users;
    private $num_partners;
    private $num_shops;
    private $num_workshops;
    private $num_tickets;
    private $num_tickets_tel;
    private $num_tickets_app;
    private $num_open_tickets;
    private $num_closed_tickets;
    private $user_with_max_post;

    private $results;


    public function getNumUsers() {
        return $this->num_users;
    }
    public function getNumPartners() {
        return $this->num_partners;
    }
    public function getNumShops() {
        return $this->num_shops;
    }
    public function getNumWorkshops() {
        return $this->num_workshops;
    }

    public function getNumTickets() {
        return $this->num_tickets;
    }

    public function getNumTicketsTel() {
        return $this->num_tickets_tel;
    }

    public function getNumTicketsApp() {
        return $this->num_tickets_app;
    }

    public function getNumOpenTickets() {
        return $this->num_open_tickets;
    }

    public function getNumClosedTickets() {
        return $this->num_closed_tickets;
    }

    public function getResults() {
        return $this->results;
    }

    public function setNumUsers($num_users) {
        $this->num_users = $num_users;
    }
    public function setNumPartners($num_partners) {
        $this->num_partners = $num_partners;
    }
    public function setNumShops($num_shops) {
        $this->num_shops = $num_shops;
    }
    public function setNumWorkshops($num_workshops) {
        $this->num_workshops = $num_workshops;
    }

    public function setNumTickets($num_tickets) {
        $this->num_tickets = $num_tickets;
    }

    public function setNumTicketsTel($num_tickets_tel) {
        $this->num_tickets_tel = $num_tickets_tel;
    }

    public function setNumTicketsApp($num_tickets_app) {
        $this->num_tickets_app = $num_tickets_app;
    }

    public function setNumOpenTickets($num_open_tickets) {
        $this->num_open_tickets = $num_open_tickets;
    }

    public function setNumClosedTickets($num_closed_tickets) {
        $this->num_closed_tickets = $num_closed_tickets;
    }

    public function setResults($results) {
        $this->results = $results;
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
    public function getNumUsersInAdservice($em, $security) {
        if($security->isGranted('ROLE_SUPER_ADMIN')){
            $filter_country = '';
        }else{
            $filter_country = 'AND u.country = '.$this->getUser()->getCountry()->getId();
        }
        $query = $em->createQuery("SELECT COUNT(u) FROM UserBundle:User u WHERE u.active = 1 ".$filter_country);
        return $query->getSingleScalarResult();
    }

    /**
     * Devuelve el número de socios dentro de ADService
     * @param EntityManager $em
     * @return Integer
     */
    public function getNumPartnersInAdservice($em, $security) {
        if($security->isGranted('ROLE_SUPER_ADMIN')){
            $filter_country = '';
        }else{
            $filter_country = 'AND p.country = '.$this->getUser()->getCountry()->getId();
        }
        $query = $em->createQuery("SELECT COUNT(p) FROM PartnerBundle:Partner p WHERE p.active = 1 ".$filter_country);
        return $query->getSingleScalarResult();
    }
    /**
     * Devuelve el número de tiendas dentro de ADService
     * @param EntityManager $em
     * @return Integer
     */
    public function getNumShopsInAdservice($em, $security) {
        if($security->isGranted('ROLE_SUPER_ADMIN')){
            $filter_country = '';
        }else{
            $filter_country = 'AND s.country = '.$this->getUser()->getCountry()->getId();
        }
        $query = $em->createQuery("SELECT COUNT(s) FROM PartnerBundle:Shop s WHERE s.active = 1 ".$filter_country);
        return $query->getSingleScalarResult();
    }

    /**
     * Devuelve el número de talleres dentro de ADService
     * @param EntityManager $em
     * @return Integer
     */
    public function getNumWorkshopsInAdservice($em, $security) {
        if($security->isGranted('ROLE_SUPER_ADMIN')){
            $filter_country = '';
        }else{
            $filter_country = 'AND w.country = '.$this->getUser()->getCountry()->getId();
        }
        $query = $em->createQuery("SELECT COUNT(w) FROM WorkshopBundle:Workshop w WHERE w.active = 1 ".$filter_country);
        return $query->getSingleScalarResult();
    }

    /**
     * Devuelve el número de tickets dentro de ADService
     * @param EntityManager $em
     * @return Integer
     */
    public function getTicketsInAdservice($em, $security) {
        if($security->isGranted('ROLE_SUPER_ADMIN')){
            $filter_country = '';
        }else{
            $filter_country = 'JOIN t.workshop w WHERE w.country = '.$this->getUser()->getCountry()->getId();
        }
        $query = $em->createQuery("SELECT COUNT(t) FROM TicketBundle:Ticket t ".$filter_country);
        return $query->getSingleScalarResult();
    }
    /**
     * Devuelve el número de tickets creados por telefono (Asesor) dentro de ADService
     * @param EntityManager $em
     * @return Integer
     */
    public function getNumTicketsByTel($em, $security) {
        $join = 'JOIN t.created_by u JOIN u.user_role ur ';
        $where = ' WHERE t.id != 0 ';
        $and   = ' AND ur.id != 4 '; //ROL 4 = ROLE_USER
        if($security->isGranted('ROLE_SUPER_ADMIN')){
            $filter_country = '';
        }else{
            $filter_country = 'JOIN t.workshop w ';
            $and = 'AND w.country = '.$this->getUser()->getCountry()->getId();
        }
        $query = $em->createQuery("SELECT COUNT(t) FROM TicketBundle:Ticket t ".$join.$filter_country.$where.$and);
        return $query->getSingleScalarResult();
    }

    /**
     * Devuelve el número de tickets creados por la aplicacion (taller) dentro de ADService
     * @param EntityManager $em
     * @return Integer
     */
    public function getNumTicketsByApp($em, $security) {
        $join = 'JOIN t.created_by u JOIN u.user_role ur ';
        $where = ' WHERE t.id != 0 ';
        $and   = ' AND ur.id = 4 '; //ROL 4 = ROLE_USER
        if($security->isGranted('ROLE_SUPER_ADMIN')){
            $filter_country = '';
        }else{
            $filter_country = 'JOIN t.workshop w ';
            $and = 'AND w.country = '.$this->getUser()->getCountry()->getId();
        }

        $query = $em->createQuery("SELECT COUNT(t) FROM TicketBundle:Ticket t ".$join.$filter_country.$where.$and);
        return $query->getSingleScalarResult();
    }

    /**
     * Devuelve el número de tickets segun su $status
     * @param EntityManager $em
     * @param String $status 'closed' o 'open'
     * @return Integer
     */
    public function getNumTicketsByStatus($em, $status, $security){
        if($security->isGranted('ROLE_SUPER_ADMIN')){
            $join_country   = '';
            $filter_country = '';
        }else{
            $join_country   = ' JOIN t.workshop w ';
            $filter_country = ' AND w.country = '.$this->getUser()->getCountry()->getId();
        }
        $query = $em->createQuery("SELECT COUNT(t.id) FROM TicketBundle:Ticket t
                                  ".$join_country."
                                   WHERE t.status = :status
                                  ".$filter_country);
        if ($status == 'open') $query->setParameter('status', 1);
        if ($status == 'close') $query->setParameter('status', 2);
        return $query->getSingleScalarResult();
    }
}
