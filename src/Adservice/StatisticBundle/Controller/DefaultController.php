<?php

namespace Adservice\StatisticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Adservice\StatisticBundle\Entity\Statistic;
use Adservice\UtilBundle\Entity\Pagination;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller {

    /**
     * Listado de estadisticas...muy chorras...
     */
    public function listAction($type=null, $page=1, $date_from='none', $date_to='none', $partner='none', $status='none') {
        $em = $this->getDoctrine()->getEntityManager();
        $statistic = new Statistic();

        $pagination = new Pagination($page);
        $params = array();
        $joins  = array();

        if($type != null){

            if ($date_from != "none") { $params[] = array('created_at', " >= '".$date_from." 00:00:00'"); }
            if ($date_to   != "none") { $params[] = array('created_at', " <= '".$date_to  ." 23:59:59'"  ); }

            if     ($type == 'ticket'  ){
                                        $bundle = 'TicketBundle';
                                        $entity = 'Ticket';
                                        if     ($status == "open"  ) {  $open = $em->getRepository('TicketBundle:Status')->findOneByName('open');
                                                                        $params[] = array('status', ' = '.$open->getId());
                                        }
                                        elseif ($status == "closed") {  $closed = $em->getRepository('TicketBundle:Status')->findOneByName('closed');
                                                                        $params[] = array('status', ' = '.$closed->getId());
                                        }
                                        if    ($partner != "none"  ) {  $joins[]  = array('e.workshop w', 'w.id != 0');
                                                                        $joins[]  = array('w.partner  p', 'p.id = '.$partner);
                                        }
            }
            elseif ($type == 'workshop'){
                                        $bundle = 'WorkshopBundle';
                                        $entity = 'Workshop';
                                        if     ($partner!= "none"    ) { $params[] = array('partner', ' = '.$partner); }
                                        if     ($status == "active"  ) { $params[] = array('active', ' = 1' ); }
                                        elseif ($status == "deactive") { $params[] = array('active', ' != 1'); }
                                        // elseif ($status == "noticket") {

                                        //                                 $joins[]  = array('e.tickets t', ' t >= 0');
                                        //     $workshops = $em->getRepository('WorkshopBundle:Workshop')->findAll();

                                        //     $ids = ' != 0 AND ( e.id = 0 ';
                                        //     foreach ($workshops as $workshop) {
                                        //         if(count($workshop->getTickets()) == 0) { $ids = $ids.'OR e.id = '.$workshop->getId().' '; }
                                        //     }
                                        //     $ids = $ids.') ';
                                        //     $params[] = array('id', $ids);
                                        // }

            }

            $result = $pagination->getRows($em, $bundle, $entity, $params, $pagination, null, $joins);
            $statistic->setResults($result);

            $length = $pagination->getRowsLength($em, $bundle, $entity, $params, null, $joins);

            $pagination->setTotalPagByLength($length);

        }else{
            $type = 'all';

            $statistic->setNumUsers        ($statistic->getNumUsersInAdservice    ($em));
            $statistic->setNumPartners     ($statistic->getNumPartnersInAdservice ($em));
            $statistic->setNumShops        ($statistic->getNumShopsInAdservice    ($em));
            $statistic->setNumWorkshops    ($statistic->getNumWorkshopsInAdservice($em));
            $statistic->setNumTickets      ($statistic->getTicketsInAdservice     ($em));
            $statistic->setNumOpenTickets  ($statistic->getNumTicketsByStatus($em, 'open'));
            $statistic->setNumClosedTickets($statistic->getNumTicketsByStatus($em, 'close'));
        }

        $partners = $em->getRepository('PartnerBundle:Partner')->findAll();

        return $this->render('StatisticBundle:Default:list.html.twig', array('statistic' => $statistic,
                                                                             'partners'  => $partners,
                                                                             'pagination'=> $pagination,
                                                                             'type'      => $type,
                                                                             'date_from' => $date_from,
                                                                             'date_to'   => $date_to,
                                                                             'partner'   => $partner,
                                                                             'status'    => $status,
                                                                            ));
    }

    public function doExcelAction($type=null, $page=1, $date_from='none', $date_to='none', $partner='none', $status='none'){
        $em = $this->getDoctrine()->getEntityManager();
        $statistic = new Statistic();

        $pagination = new Pagination($page);
        $params = array();
        $joins  = array();

        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->addCacheControlDirective('no-cache', true);
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->setMaxAge(600);
        $response->setSharedMaxAge(600);
        $response->headers->set('Pragma', 'public');
        $date = new \DateTime();
        $response->setLastModified($date);

        if($type != null){

            if ($date_from != "none") { $params[] = array('created_at', " >= '".$date_from." 00:00:00'"); }
            if ($date_to   != "none") { $params[] = array('created_at', " <= '".$date_to  ." 23:59:59'"  ); }

            if     ($type == 'ticket'  ){
                                        $bundle = 'TicketBundle';
                                        $entity = 'Ticket';
                                        if     ($status == "open"  ) {  $open = $em->getRepository('TicketBundle:Status')->findOneByName('open');
                                                                        $params[] = array('status', ' = '.$open->getId());
                                        }
                                        elseif ($status == "closed") {  $closed = $em->getRepository('TicketBundle:Status')->findOneByName('closed');
                                                                        $params[] = array('status', ' = '.$closed->getId());
                                        }
                                        if    ($partner != "none"  ) {  $joins[]  = array('e.workshop w', 'w.id != 0');
                                                                        $joins[]  = array('w.partner  p', 'p.id = '.$partner);
                                        }

                $results = $pagination->getRows($em, $bundle, $entity, $params, null, null, $joins);
                $response->headers->set('Content-Disposition', 'attachment;filename="informeTickets_'.date("dmY").'.csv"');
                $excel = $this->createExcelTicket($results);
            }
            elseif ($type == 'workshop'){
                                        $bundle = 'WorkshopBundle';
                                        $entity = 'Workshop';
                                        if     ($partner!= "none"    ) { $params[] = array('partner', ' = '.$partner); }
                                        if     ($status == "active"  ) { $params[] = array('active', ' = 1' ); }
                                        elseif ($status == "deactive") { $params[] = array('active', ' != 1'); }

                $results = $pagination->getRows($em, $bundle, $entity, $params, null, null, $joins);
                $response->headers->set('Content-Disposition', 'attachment;filename="informeTickets_'.date("dmY").'.csv"');
                $excel = $this->createExcelWorkshop($results);
            }

            $response->setContent($excel);
            return $response;

        }
    }

    public function createExcelTicket($results){
        //Creación de cabecera
        $excel ='ID;Date;Car;Assigned To;Description;Status;';
        $excel.="\n";

        $em = $this->getDoctrine()->getEntityManager();

        foreach ($results as $row) {
            $excel.=$row->getId().';';

            $created = $row->getCreatedAt();
            $excel.=$created->format("d/m/Y").';';
            $excel.=$row->getCar().';';
            $excel.=$row->getAssignedTo().';';
            $excel.=$row->getDescription().';';
            $excel.=$row->getStatus().';';
            $excel.="\n";
        }
        return($excel);
    }
    public function createExcelWorkshop($results){
        //Creación de cabecera
        $excel ='Code Partner;Code Workshop;Name;Partner;Email1;Phone Number1;Active;';
        $excel.="\n";

        $em = $this->getDoctrine()->getEntityManager();

        foreach ($results as $row) {
            $excel.=$row->getPartner()->getCodePartner().';';
            $excel.=$row->getCodeWorkshop().';';
            $excel.=$row->getName().';';
            $excel.=$row->getPartner().';';
            $excel.=$row->getEmail1().';';
            $excel.=$row->getPhoneNumber1().';';
            $excel.=$row->getActive().';';
            $excel.="\n";
        }
        return($excel);
    }
}