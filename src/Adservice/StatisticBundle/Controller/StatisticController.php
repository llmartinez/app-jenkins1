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
                                        //Estadísticas de tickets de Ad-service
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
                                        //Estadísticas de talleres de Ad-service
                                        $bundle = 'WorkshopBundle';
                                        $entity = 'Workshop';
                                        if     ($partner  != '0') { $params[] = array('partner', ' = '.$partner); }
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
            elseif ($type == 'no-ticket'){

                //Estadísticas generales de Ad-service
                $statistic->setNumUsers        ($statistic->getNumUsersInAdservice    ($em, $security));
                $statistic->setNumPartners     ($statistic->getNumPartnersInAdservice ($em, $security));
                $statistic->setNumShops        ($statistic->getNumShopsInAdservice    ($em, $security));
                $statistic->setNumWorkshops    ($statistic->getNumWorkshopsInAdservice($em, $security));
                $statistic->setNumTickets      ($statistic->getTicketsInAdservice     ($em, $security));
                $statistic->setNumOpenTickets  ($statistic->getNumTicketsByStatus($em, 'open' , $security));
                $statistic->setNumClosedTickets($statistic->getNumTicketsByStatus($em, 'close', $security));

                //listado de talleres sin tickets
                // SELECT w.id FROM workshop w WHERE w.id NOT IN (SELECT t.workshop_id FROM ticket t GROUP BY t.workshop_id)

                $consulta = $em ->createQuery('SELECT w.id FROM TicketBundle:Ticket t JOIN t.workshop w GROUP BY t.workshop');
                $workshop_query = '0';

                foreach ( $consulta->getResult() as $row) {
                    $workshop_query = $workshop_query.', '.$row['id'];
                }

                $bundle = 'WorkshopBundle';
                $entity = 'Workshop';
                $params[] = array('id', ' NOT IN ('.$workshop_query.')');
                if     ($partner  != '0') { $params[] = array('partner', ' = '.$partner); }
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

            //Extraemos los resultados segun el tipo de bsqueda y los filtros aplicados
            $result = $pagination->getRows($em, $bundle, $entity, $params, $pagination, null, $joins);
            $statistic->setResults($result);

            $length = $pagination->getRowsLength($em, $bundle, $entity, $params, null, $joins);

            $pagination->setTotalPagByLength($length);

        }else{
            $type = '0';

            //Estadísticas generales de Ad-service
            $statistic->setNumUsers        ($statistic->getNumUsersInAdservice    ($em, $security));
            $statistic->setNumPartners     ($statistic->getNumPartnersInAdservice ($em, $security));
            $statistic->setNumShops        ($statistic->getNumShopsInAdservice    ($em, $security));
            $statistic->setNumWorkshops    ($statistic->getNumWorkshopsInAdservice($em, $security));
            $statistic->setNumTickets      ($statistic->getTicketsInAdservice     ($em, $security));
            $statistic->setNumOpenTickets  ($statistic->getNumTicketsByStatus($em, 'open' , $security));
            $statistic->setNumClosedTickets($statistic->getNumTicketsByStatus($em, 'close', $security));

            /*
                SELECT w.id, t.id FROM ticket t JOIN  workshop w ON t.workshop_id = w.id GROUP BY w.id ORDER BY t.id DESC
            */
            $bundle = 'TicketBundle';
            $entity = 'Ticket';
            $joins[] = array('e.workshop w', ' e.workshop = w.id ');
            $group_by = 'w.id';
            $order = array('e.id', 'DESC');

            if($security->isGranted('ROLE_SUPER_ADMIN')){
                if    ($country != '0'     ) { $params[] = array('country', ' = '.$country); }
            }else{
                $params[] = array('id != 0 AND w.country', ' = '.$security->getToken()->getUser()->getCountry()->getId());
            }

            //Extraemos los resultados segun el tipo de bsqueda y los filtros aplicados
            $result = $pagination->getRows($em, $bundle, $entity, $params, $pagination, $order, $joins,'',$group_by);
            $statistic->setResults($result);

            $length = $pagination->getRowsLength($em, $bundle, $entity, $params, $order, $joins,'',$group_by);
            $pagination->setTotalPagByLength($length);

            $statistic->setResults($result);
        }
        //select partial u.{id,name} from MyApp\Domain\User u

        if($security->isGranted('ROLE_SUPER_ADMIN')){

            $qp = $em->createQuery("select partial p.{id,name, code_partner} from PartnerBundle:Partner p");
            $qs = $em->createQuery("select partial s.{id,name} from PartnerBundle:Shop s");
            $qw = $em->createQuery("select partial w.{id,name, code_workshop} from WorkshopBundle:Workshop w");
            $qt = $em->createQuery("select partial t.{id,name} from WorkshopBundle:Typology t");
            $partners   = $qp->getResult();
            $shops      = $qs->getResult();
            $workshops  = $qw->getResult();
            $typologies = $qt->getResult();
        }else{
            $country = $security->getToken()->getUser()->getCountry()->getId();
            $qp = $em->createQuery("select partial p.{id,name, code_partner} from PartnerBundle:Partner p where p.country = ".$country." ");
            $qs = $em->createQuery("select partial s.{id,name} from PartnerBundle:Shop s where s.country = ".$country." ");
            $qw = $em->createQuery("select partial w.{id,name, code_workshop} from WorkshopBundle:Workshop w where w.country = ".$country." ");
            $qt = $em->createQuery("select partial t.{id,name} from WorkshopBundle:Typology t where t.country = ".$country." ");
            $partners   = $qp->getResult();
            $shops      = $qs->getResult();
            $workshops  = $qw->getResult();
            $typologies = $qt->getResult();

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
        $where = 'e.id != 0 ';
        $join  = '';

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
            if ($from_y != '0' and $from_m != '0' and $from_d != '0') { $where .= "AND e.created_at >= '".$from_y.'-'.$from_m.'-'.$from_d." 00:00:00'"; }
            if ($to_y   != '0' and $to_m   != '0' and $to_d   != '0') { $where .= "AND e.created_at <= '".$to_y  .'-'.$to_m  .'-'.$to_d  ." 23:59:59'"; }

            if     ($type == 'ticket'  ){

                if     ($status == "open"  ) {  $open = $em->getRepository('TicketBundle:Status')->findOneByName('open');
                                                $where .= 'AND e.status = '.$open->getId().' ';
                }
                elseif ($status == "closed") {  $closed = $em->getRepository('TicketBundle:Status')->findOneByName('closed');
                                                $where .= 'AND e.status = '.$closed->getId().' ';
                }
                if    ($partner != "0"     ) {  $where .= 'AND w.id != 0 ';
                                                $where .= 'AND p.id = '.$partner.' ';
                                                $join  = ', w.partner p ';
                }
                if(!$security->isGranted('ROLE_SUPER_ADMIN')){
                    $where .= 'AND w.country = '.$security->getToken()->getUser()->getCountry()->getId().' ';
                }else{
                    if    ($country != "0"  ) { $where .= 'AND w.country = '.$country.' '; }
                }

                $qt = $em->createQuery("select partial e.{id, created_at, description, solution } from TicketBundle:Ticket e JOIN e.workshop w ".$join." WHERE ".$where);
                $results   = $qt->getResult();

                $response->headers->set('Content-Disposition', 'attachment;filename="informeTickets_'.date("dmY").'.csv"');
                $excel = $this->createExcelTicket($results);
            }
            elseif ($type == 'workshop'){

                if     ($partner!= "0"       ) { $where .= 'AND e.partner = '.$partner.' '; }
                if     ($status == "active"  ) { $where .= 'AND e.active = 1 ' ; }
                elseif ($status == "deactive") { $where .= 'AND e.active != 1 '; }
                if(!$security->isGranted('ROLE_SUPER_ADMIN')){
                    $where .= 'AND e.country = '.$security->getToken()->getUser()->getCountry()->getId().' ';
                }else{
                    if    ($country != "0"  ) { $where .= 'AND e.country = '.$country.' '; }
                }

                $qw = $em->createQuery("select partial e.{id, code_partner, code_workshop, name, email_1, phone_number_1, active, ad_service_plus, test } from WorkshopBundle:Workshop e WHERE ".$where);
                $results   = $qw->getResult();

                $qp = $em->createQuery("select partial p.{id, code_partner, name } from PartnerBundle:Partner p");
                $res_partners   = $qp->getResult();

                foreach ($res_partners as $partner) {
                     $partners[$partner->getCodePartner()] = $partner->getName();
                 }
                 unset($res_partners);
                 unset($partner);

                $response->headers->set('Content-Disposition', 'attachment;filename="informeTickets_'.date("dmY").'.csv"');
                $excel = $this->createExcelWorkshop($results, $partners);
            }
            elseif ($type == 'no-ticket'){

                //listado de talleres sin tickets
                $consulta = $em ->createQuery('SELECT w.id FROM TicketBundle:Ticket t JOIN t.workshop w GROUP BY t.workshop');

                $workshop_query = '0';
                foreach ( $consulta->getResult() as $row) { $workshop_query = $workshop_query.', '.$row['id']; }

                $where .= 'e.id NOT IN ('.$workshop_query.') ';
                if     ($partner  != '0') { $where .= 'AND e.partner = '.$partner.' '; }
                if     ($status == "active"  ) { $where .= 'AND e.active = 1 '; }
                elseif ($status == "deactive") { $where .= 'AND e.active != 1 '; }
                elseif ($status == "test"    ) { $where .= 'AND e.test = 1 '; }
                elseif ($status == "adsplus" ) { $where .= 'AND e.ad_service_plus = 1 '; }
                if($security->isGranted('ROLE_SUPER_ADMIN')){
                    if    ($country != '0'     ) { $where .= 'AND e.country = '.$country.' '; }
                }else{
                    $where .= 'AND e.country = '.$security->getToken()->getUser()->getCountry()->getId().' ';
                }

                $qw = $em->createQuery("select partial e.{id, code_partner, code_workshop, name, email_1, phone_number_1, active, ad_service_plus, test } from WorkshopBundle:Workshop e WHERE ".$where);
                $results   = $qw->getResult();

                $qp = $em->createQuery("select partial p.{id, code_partner, name } from PartnerBundle:Partner p");
                $res_partners   = $qp->getResult();

                foreach ($res_partners as $partner) {
                     $partners[$partner->getCodePartner()] = $partner->getName();
                 }
                 unset($res_partners);
                 unset($partner);

                $response->headers->set('Content-Disposition', 'attachment;filename="informeTalleresSinTickets_'.date("dmY").'.csv"');
                $excel = $this->createExcelWorkshop($results, $partners);
            }
        }
        else{
            /*
                SELECT w.id, t.id FROM ticket t JOIN  workshop w ON t.workshop_id = w.id GROUP BY w.id ORDER BY t.id DESC
            */
            $where .= 'AND e.workshop = w.id ';

            if($security->isGranted('ROLE_SUPER_ADMIN')){
                if    ($country != '0'     ) { $$where .= 'AND e.country = '.$country.' '; }
            }else{
                $where .= 'AND e.country = '.$security->getToken()->getUser()->getCountry()->getId().' ';
            }

            $where .= 'GROUP BY w.id ';
            $where .= 'ORDER BY e.id DESC ';
            $qt = $em->createQuery("select partial e.{ id, description, solution, modified_at }, partial w.{ id, code_partner, code_workshop, name } from TicketBundle:Ticket e JOIN e.workshop w WHERE ".$where);
            $results   = $qt->getResult();

            $response->headers->set('Content-Disposition', 'attachment;filename="informeUltimosTickets_'.date("dmY").'.csv"');
            $excel = $this->createExcelLastTickets($results);
        }

        $excel = UtilController::sinAcentos($excel);
        $response->setContent($excel);
        return $response;
    }

    public function createExcelTicket($results){
        //Creación de cabecera
        //'ID;Date;Car;Assigned To;Description;Status;Solution;';
        $excel ='ID;'.
                $this->get('translator')->trans('date').';'.
                $this->get('translator')->trans('car').';'.
                $this->get('translator')->trans('assigned_to').';'. //ID ticket
                $this->get('translator')->trans('description').';'.
                $this->get('translator')->trans('status').';'.
                $this->get('translator')->trans('solution').';';
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
        $excel = nl2br($excel);
        $excel = str_replace('<br />', '.', $excel);
        $excel = str_replace(',', '.', $excel);
        return($excel);
    }
    public function createExcelWorkshop($results, $partners){
        //Creación de cabecera
        //'Code Partner;Code Workshop;Name;Partner;Shop;Email1;Phone Number1;Active;';
        $excel =$this->get('translator')->trans('code_partner').';'.
                $this->get('translator')->trans('code_workshop').';'.
                $this->get('translator')->trans('name').';'.
                $this->get('translator')->trans('partner').';'. //ID ticket
                $this->get('translator')->trans('shop').';'.
                $this->get('translator')->trans('email').';'.
                $this->get('translator')->trans('tel').';'.
                $this->get('translator')->trans('active').';';
        $excel.="\n";

        $em = $this->getDoctrine()->getEntityManager();

        foreach ($results as $row) {
            $excel.=$row->getCodePartner().';';
            $excel.=$row->getCodeWorkshop().';';
            $excel.=$row->getName().';';
            $excel.=$partners[$row->getCodePartner()].';';
            $excel.=$row->getShop().';';
            $excel.=$row->getEmail1().';';
            $excel.=$row->getPhoneNumber1().';';
            if ($row->getActive() == 1) $active = $this->get('translator')->trans('yes');
            else $active = $this->get('translator')->trans('no');
            $excel.=$active.';';
            $excel.="\n";
        }

        $excel = nl2br($excel);
        $excel = str_replace('<br />', '.', $excel);
        $excel = str_replace(',', '.', $excel);
        return($excel);
    }
    public function createExcelLastTickets($results){
        //Creación de cabecera
        //'Code Partner;Code Workshop;Name;Partner;ID Ticket;Ticket;Status;Date;';
        $excel =$this->get('translator')->trans('code_partner').';'.
                $this->get('translator')->trans('code_workshop').';'.
                $this->get('translator')->trans('name').';'.
                $this->get('translator')->trans('partner').';ID '. //ID ticket
                $this->get('translator')->trans('ticket').';'.
                $this->get('translator')->trans('ticket').';'.
                $this->get('translator')->trans('status').';'.
                $this->get('translator')->trans('solution').';'.
                $this->get('translator')->trans('date').';';
        $excel.="\n";

        $em = $this->getDoctrine()->getEntityManager();

        foreach ($results as $row) {

            $excel.=$row->getWorkshop()->getPartner()->getCodePartner().';';
            $excel.=$row->getWorkshop()->getCodeWorkshop().';';
            $excel.=$row->getWorkshop()->getName().';';
            $excel.=$row->getWorkshop()->getPartner().';';
            $excel.=$row->getId().';';
            $excel.=$row->getDescription().';';
            $excel.=$row->getStatus().';';
            $excel.=$row->getSolution().';';
            $excel.=$row->getModifiedAt()->format('Y-m-d').';';
            $excel.="\n";
        }

        $excel = nl2br($excel);
        $excel = str_replace('<br />', '.', $excel);
        $excel = str_replace(',', '.', $excel);
        return($excel);
    }
    public function createExcelNoTickets($results){
        //Creación de cabecera
        //'Code Partner;Code Workshop;Name;Partner;ID Ticket;Ticket;Status;Date;';
        // $excel ='Code Partner;Code Workshop;Name;Partner;ID Ticket;Ticket;Status;Date;';
        // $excel.="\n";

        // $em = $this->getDoctrine()->getEntityManager();

        // foreach ($results as $row) {

        //     $excel.=$row->getWorkshop()->getPartner()->getCodePartner().';';
        //     $excel.=$row->getWorkshop()->getCodeWorkshop().';';
        //     $excel.=$row->getWorkshop()->getName().';';
        //     $excel.=$row->getWorkshop()->getPartner().';';
        //     $excel.=$row->getId().';';
        //     $excel.=$row->getDescription().';';
        //     $excel.=$row->getStatus().';';
        //     $excel.=$row->getModifiedAt()->format('Y-m-d').';';
        //     $excel.="\n";
        // }
        // return($excel);
    }
}