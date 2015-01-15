<?php

namespace Adservice\StatisticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Adservice\StatisticBundle\Entity\Statistic;
use Adservice\StatisticBundle\Form\DateType;
use Adservice\UtilBundle\Entity\Pagination;
use Adservice\UtilBundle\Controller\UtilController as UtilController;

class StatisticController extends Controller {

    public function listAction($type='0', $page=1, $from_y ='0', $from_m='0', $from_d ='0',
                                                   $to_y   ='0', $to_m  ='0', $to_d   ='0',
                                                   $partner='0', $shop='0', $workshop='0', $typology='0',
                                                   $status='0', $country='0') {
        $em = $this->getDoctrine()->getEntityManager();
        $security = $this->get('security.context');
        $statistic = new Statistic();
        $pagination = new Pagination($page);
        $params = array();
        $joins  = array();

        if($type != '0'){

            if ($from_y != '0' and $from_m != '0' and $from_d != '0') {
                $params[]  = array('created_at', " >= '".$from_y.'-'.$from_m.'-'.$from_d." 00:00:00'");
            }
            if ($to_y != '0' and $to_m != '0' and $to_d != '0') {
                $params[] = array('created_at', " <= '".$to_y.'-'.$to_m.'-'.$to_d." 23:59:59'");
            }

            if     ($type == 'ticket'  ){
                                        $bundle = 'TicketBundle';
                                        $entity = 'Ticket';
                                        if     ($status == "open"  ) {  $open = $em->getRepository('TicketBundle:Status')->findOneByName('open');
                                                                        $params[] = array('status', ' = '.$open->getId());
                                        }
                                        elseif ($status == "closed") {  $closed = $em->getRepository('TicketBundle:Status')->findOneByName('closed');
                                                                        $params[] = array('status', ' = '.$closed->getId());
                                        }
                                        if    ($partner != '0'     ) {  $joins[]  = array('e.workshop w', 'w.id != 0');
                                                                        $joins[]  = array('w.partner  p', 'p.id = '.$partner);
                                        }
                                        if    ($workshop != '0'    ) {  $params[] = array('workshop', ' = '.$workshop);
                                        }
                                        if($security->isGranted('ROLE_SUPER_ADMIN')){
                                            if    ($country != '0'     ) { $joins[] = array('e.workshop wks', ' wks.country = '.$country); }
                                        }else{
                                            $joins[] = array('e.workshop wks', ' wks.country = '.$security->getToken()->getUser()->getCountry()->getId());
                                        }
            }
            elseif ($type == 'workshop'){
                                        $bundle = 'WorkshopBundle';
                                        $entity = 'Workshop';
                                        if     ($partner!= '0'  ) { $params[] = array('partner', ' = '.$partner); }
                                        if     ($shop     != '0') { $params[] = array('shop', ' = '.$shop); }
                                        if     ($typology != '0') { $params[] = array('typology', ' = '.$typology); }
                                        if     ($status == "active"  ) { $params[] = array('active', ' = 1' ); }
                                        elseif ($status == "deactive") { $params[] = array('active', ' != 1'); }
                                        elseif ($status == "test"    ) { $params[] = array('test', ' = 1'); }
                                        elseif ($status == "adsplus" ) { $params[] = array('ad_service_plus', ' = 1'); }
                                        if($security->isGranted('ROLE_SUPER_ADMIN')){
                                            if    ($country != '0'     ) { $params[] = array('country', ' = '.$country); }
                                        }else{
                                            $params[] = array('country', ' = '.$security->getToken()->getUser()->getCountry()->getId());
                                        }
            }

            $result = $pagination->getRows($em, $bundle, $entity, $params, $pagination, null, $joins);
            $statistic->setResults($result);

            $length = $pagination->getRowsLength($em, $bundle, $entity, $params, null, $joins);

            $pagination->setTotalPagByLength($length);

        }else{
            $type = '0';

            $statistic->setNumUsers        ($statistic->getNumUsersInAdservice    ($em, $security));
            $statistic->setNumPartners     ($statistic->getNumPartnersInAdservice ($em, $security));
            $statistic->setNumShops        ($statistic->getNumShopsInAdservice    ($em, $security));
            $statistic->setNumWorkshops    ($statistic->getNumWorkshopsInAdservice($em, $security));
            $statistic->setNumTickets      ($statistic->getTicketsInAdservice     ($em, $security));
            $statistic->setNumOpenTickets  ($statistic->getNumTicketsByStatus($em, 'open' , $security));
            $statistic->setNumClosedTickets($statistic->getNumTicketsByStatus($em, 'close', $security));
        }

        if($security->isGranted('ROLE_SUPER_ADMIN')){
            $partners  = $em->getRepository('PartnerBundle:Partner')->findAll();
            $shops     = $em->getRepository('PartnerBundle:Shop'     )->findAll();
            $workshops = $em->getRepository('WorkshopBundle:Workshop')->findAll();
            $typologies= $em->getRepository('WorkshopBundle:Typology')->findAll();
        }else{
            $partners  = $em->getRepository('PartnerBundle:Partner'  )->findByCountry($security->getToken()->getUser()->getCountry()->getId());
            $shops     = $em->getRepository('PartnerBundle:Shop'     )->findByCountry($security->getToken()->getUser()->getCountry()->getId());
            $workshops = $em->getRepository('WorkshopBundle:Workshop')->findByCountry($security->getToken()->getUser()->getCountry()->getId());
            $typologies= $em->getRepository('WorkshopBundle:Typology')->findByCountry($security->getToken()->getUser()->getCountry()->getId());
        }
        $countries = $em->getRepository('UtilBundle:Country')->findAll();

        return $this->render('StatisticBundle:Statistic:list_statistics.html.twig', array('statistic' => $statistic,
                                                                                          'from_y'    => $from_y,
                                                                                          'from_m'    => $from_m,
                                                                                          'from_d'    => $from_d,
                                                                                          'to_y'      => $to_y  ,
                                                                                          'to_m'      => $to_m ,
                                                                                          'to_d'      => $to_d  ,
                                                                                          'partners'  => $partners,
                                                                                          'shops'     => $shops,
                                                                                          'workshops' => $workshops,
                                                                                          'typologies'=> $typologies,
                                                                                          'countries' => $countries,
                                                                                          'pagination'=> $pagination,
                                                                                          'type'      => $type,
                                                                                          'partner'   => $partner,
                                                                                          'shop'      => $shop,
                                                                                          'wks'       => $workshop,
                                                                                          'typology'  => $typology,
                                                                                          'status'    => $status,
                                                                                          'country'   => $country,
                                                                            ));
    }

    public function doExcelAction($type='0', $page=1, $from_y ='0', $from_m='0', $from_d ='0',
                                                      $to_y   ='0', $to_m  ='0', $to_d   ='0',
                                                      $partner='0', $status='0', $country='0'){
        $em = $this->getDoctrine()->getEntityManager();
        $statistic = new Statistic();
        $security = $this->get('security.context');
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

        if($type != '0'){
            if ($from_y != '0' and $from_m != '0' and $from_d != '0') { $params[] = array('created_at', " >= '".$from_y.'-'.$from_m.'-'.$from_d." 00:00:00'"); }
            if ($to_y   != '0' and $to_m   != '0' and $to_d   != '0') { $params[] = array('created_at', " <= '".$to_y  .'-'.$to_m  .'-'.$to_d  ." 23:59:59'"); }

            if     ($type == 'ticket'  ){
                                        $bundle = 'TicketBundle';
                                        $entity = 'Ticket';
                                        if     ($status == "open"  ) {  $open = $em->getRepository('TicketBundle:Status')->findOneByName('open');
                                                                        $params[] = array('status', ' = '.$open->getId());
                                        }
                                        elseif ($status == "closed") {  $closed = $em->getRepository('TicketBundle:Status')->findOneByName('closed');
                                                                        $params[] = array('status', ' = '.$closed->getId());
                                        }
                                        if    ($partner != "0"     ) {  $joins[]  = array('e.workshop w', 'w.id != 0');
                                                                        $joins[]  = array('w.partner  p', 'p.id = '.$partner);
                                        }
                                        if(!$security->isGranted('ROLE_SUPER_ADMIN')){
                                            $joins[] = array('e.workshop wks', ' wks.country = '.$security->getToken()->getUser()->getCountry()->getId());
                                        }else{
                                            if    ($country != "0"  ) { $joins[] = array('e.workshop wks', ' wks.country = '.$country); }
                                        }

                $results = $pagination->getRows($em, $bundle, $entity, $params, $pagination, null, $joins);
                $response->headers->set('Content-Disposition', 'attachment;filename="informeTickets_'.date("dmY").'.csv"');
                $excel = $this->createExcelTicket($results);
            }
            elseif ($type == 'workshop'){
                                        $bundle = 'WorkshopBundle';
                                        $entity = 'Workshop';
                                        if     ($partner!= "0"       ) { $params[] = array('partner', ' = '.$partner); }
                                        if     ($status == "active"  ) { $params[] = array('active', ' = 1' ); }
                                        elseif ($status == "deactive") { $params[] = array('active', ' != 1'); }
                                        if(!$security->isGranted('ROLE_SUPER_ADMIN')){
                                            $params[] = array('country', ' = '.$security->getToken()->getUser()->getCountry()->getId());
                                        }else{
                                            if    ($country != "0"  ) { $params[] = array('country', ' = '.$country); }
                                        }

                $results = $pagination->getRows($em, $bundle, $entity, $params, null, null, $joins);
                $response->headers->set('Content-Disposition', 'attachment;filename="informeTickets_'.date("dmY").'.csv"');
                $excel = $this->createExcelWorkshop($results);
            }

            $excel = UtilController::sinAcentos($excel);
            $response->setContent($excel);
            return $response;

        }
    }

    public function createExcelTicket($results){
        //Creación de cabecera
        $excel ='ID;Date;Car;Assigned To;Description;Status;Solution;';
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
            $excel.=$row->getSolution().';';
            $excel.="\n";
        }
        $excel = str_replace(',', '.', $excel);
        return($excel);
    }
    public function createExcelWorkshop($results){
        //Creación de cabecera
        $excel ='Code Partner;Code Workshop;Name;Partner;Shop;Email1;Phone Number1;Active;';
        $excel.="\n";

        $em = $this->getDoctrine()->getEntityManager();

        foreach ($results as $row) {
            $excel.=$row->getPartner()->getCodePartner().';';
            $excel.=$row->getCodeWorkshop().';';
            $excel.=$row->getName().';';
            $excel.=$row->getPartner().';';
            $excel.=$row->getShop().';';
            $excel.=$row->getEmail1().';';
            $excel.=$row->getPhoneNumber1().';';
            $excel.=$row->getActive().';';
            $excel.="\n";
        }
        return($excel);
    }
}