<?php

namespace Adservice\StatisticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Adservice\StatisticBundle\Entity\Statistic;
use Adservice\StatisticBundle\Form\DateType;
use Adservice\UtilBundle\Entity\Pagination;
use Adservice\UtilBundle\Controller\UtilController as UtilController;

class StatisticController extends Controller {

    public function listAction($type='0', $page=1, $from_y ='0', $from_m='0', $from_d ='0', $to_y   ='0', $to_m  ='0', $to_d   ='0', $partner='0', $shop='0', $workshop='0', $typology='0', $status='0', $country='0', $assessor='0', $created_by='0', $raport='0') {

        $em = $this->getDoctrine()->getEntityManager();
        $security = $this->get('security.context');
        $request  = $this->getRequest();
        $statistic = new Statistic();
        $pagination = new Pagination($page);
        $params = array();
        $joins  = array();

        if($security->isGranted('ROLE_SUPER_ADMIN')){

            $qp = $em->createQuery("select partial p.{id,name, code_partner} from PartnerBundle:Partner p");
            $qs = $em->createQuery("select partial s.{id,name} from PartnerBundle:Shop s");
            $qw = $em->createQuery("select partial w.{id,name, code_workshop} from WorkshopBundle:Workshop w");
            $qa = $em->createQuery("select partial a.{id,username} from UserBundle:User a JOIN a.user_role r WHERE r = 3");
            $qt = $em->createQuery("select partial t.{id,name} from WorkshopBundle:Typology t");
            $partners   = $qp->getResult();
            $shops      = $qs->getResult();
            $workshops  = $qw->getResult();
            $assessors  = $qa->getResult();
            $typologies = $qt->getResult();
        }else{
            $country = $security->getToken()->getUser()->getCountry()->getId();
            $qp = $em->createQuery("select partial p.{id,name, code_partner} from PartnerBundle:Partner p WHERE p.country = ".$country." ");
            $qs = $em->createQuery("select partial s.{id,name} from PartnerBundle:Shop s WHERE s.country = ".$country." ");
            $qw = $em->createQuery("select partial w.{id,name, code_workshop} from WorkshopBundle:Workshop w WHERE w.country = ".$country." ");
            $qa = $em->createQuery("select partial a.{id,username} from UserBundle:User a JOIN a.user_role r WHERE r = 3 AND a.country = ".$country." ");
            $qt = $em->createQuery("select partial t.{id,name} from WorkshopBundle:Typology t WHERE t.country = ".$country." ");
            $partners   = $qp->getResult();
            $shops      = $qs->getResult();
            $workshops  = $qw->getResult();
            $assessors  = $qa->getResult();
            $typologies = $qt->getResult();

        }
        //Estadísticas generales de Ad-service
        $statistic->setNumUsers        ($statistic->getNumUsersInAdservice    ($em, $security));
        $statistic->setNumPartners     ($statistic->getNumPartnersInAdservice ($em, $security));
        $statistic->setNumShops        ($statistic->getNumShopsInAdservice    ($em, $security));
        $statistic->setNumWorkshops    ($statistic->getNumWorkshopsInAdservice($em, $security));
        $statistic->setNumTickets      ($statistic->getTicketsInAdservice     ($em, $security));
        $statistic->setNumTicketsTel   ($statistic->getNumTicketsByTel($em, $security));
        $statistic->setNumTicketsApp   ($statistic->getNumTicketsByApp($em, $security));
        $statistic->setNumOpenTickets  ($statistic->getNumTicketsByStatus($em, 'open' , $security));
        $statistic->setNumClosedTickets($statistic->getNumTicketsByStatus($em, 'close', $security));
        $countries = $em->getRepository('UtilBundle:Country')->findAll();

        return $this->render('StatisticBundle:Statistic:list_statistics.html.twig', array('page'      => $page,
                                                                                          'statistic' => $statistic,
                                                                                          'from_y'    => $from_y,
                                                                                          'from_m'    => $from_m,
                                                                                          'from_d'    => $from_d,
                                                                                          'to_y'      => $to_y  ,
                                                                                          'to_m'      => $to_m ,
                                                                                          'to_d'      => $to_d  ,
                                                                                          'partners'  => $partners,
                                                                                          'shops'     => $shops,
                                                                                          'workshops' => $workshops,
                                                                                          'assessors' => $assessors,
                                                                                          'typologies'=> $typologies,
                                                                                          'countries' => $countries,
                                                                                          'pagination'=> $pagination,
                                                                                          'type'      => $type,
                                                                                          'partner'   => $partner,
                                                                                          'shop'      => $shop,
                                                                                          'wks'       => $workshop,
                                                                                          'assessor'  => $assessor,
                                                                                          'created_by'  => $created_by,
                                                                                          'typology'  => $typology,
                                                                                          'status'    => $status,
                                                                                          'country'   => $country,
                                                                                          //'length'    => $length,
                                                                            ));
    }

    public function doExcelAction($type='0', $page=1, $from_y ='0', $from_m='0', $from_d ='0', $to_y   ='0', $to_m  ='0', $to_d   ='0', $partner='0', $shop='0', $workshop='0', $typology='0', $status='0', $country='0', $assessor='0', $created_by='0', $raport='0'){

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

        if ($raport != '0'){
            $type = $raport;
            if ($raport == 'last-tickets') { $type = '0'; }
        }

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
                                                $join  = ' JOIN w.partner p ';
                }
                if    ($assessor != '0'    ) {  $where .= 'AND e.assigned_to = '.$assessor;
                }
                if    ($created_by != '0'  ) {
                                                if ($created_by == 'tel'){
                                                    $join .= 'JOIN e.created_by u JOIN u.user_role ur';
                                                    $where .= 'AND ur.id != 4';
                                                }
                                                elseif($created_by == 'app'){
                                                    $join .= 'JOIN e.created_by u JOIN u.user_role ur';
                                                    $where .= 'AND ur.id = 4';
                                                }
                }
                if(!$security->isGranted('ROLE_SUPER_ADMIN')){
                    $where .= 'AND w.country = '.$security->getToken()->getUser()->getCountry()->getId().' ';
                }else{
                    if    ($country != "0"  ) { $where .= 'AND w.country = '.$country.' '; }
                }

                $qt = $em->createQuery("SELECT partial e.{id, created_at, description, solution } FROM TicketBundle:Ticket e JOIN e.workshop w ".$join." WHERE ".$where);
                $results   = $qt->getResult();

                $response->headers->set('Content-Disposition', 'attachment;filename="informeTickets_'.date("dmY").'.csv"');
                $excel = $this->createExcelTicket($results);
            }
            elseif ($type == 'workshop'){

                if     ($partner!= "0"       ) { $where .= 'AND e.partner = '.$partner.' '; }
                if     ($status == "active"  ) { $where .= 'AND e.active = 1 ' ; }
                elseif ($status == "deactive") { $where .= 'AND e.active != 1 '; }
                elseif ($status == "test"    ) { $where .= 'AND e.test = 1 '; }
                elseif ($status == "adsplus" ) { $where .= 'AND e.ad_service_plus = 1 '; }
                if(!$security->isGranted('ROLE_SUPER_ADMIN')){
                    $where .= 'AND e.country = '.$security->getToken()->getUser()->getCountry()->getId().' ';
                }else{
                    if    ($country != "0"  ) { $where .= 'AND e.country = '.$country.' '; }
                }

                $qw = $em->createQuery("SELECT partial e.{id, code_partner, code_workshop, name, email_1, phone_number_1, active, ad_service_plus, test } FROM WorkshopBundle:Workshop e WHERE ".$where);
                $results   = $qw->getResult();

                $qp = $em->createQuery("SELECT partial p.{id, code_partner, name } FROM PartnerBundle:Partner p");
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

                $where = 'e.id NOT IN ('.$workshop_query.') ';
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

                $qw = $em->createQuery("SELECT partial e.{id, code_partner, code_workshop, name, email_1, phone_number_1, active, ad_service_plus, test } FROM WorkshopBundle:Workshop e WHERE ".$where);
                $results   = $qw->getResult();

                $qp = $em->createQuery("SELECT partial p.{id, code_partner, name } FROM PartnerBundle:Partner p");
                $res_partners   = $qp->getResult();

                foreach ($res_partners as $partner) {
                     $partners[$partner->getCodePartner()] = $partner->getName();
                 }
                 unset($res_partners);
                 unset($partner);

                $trans     = $this->get('translator');
                $informe   = $trans->trans('statistic.no_ticket');
                $response->headers->set('Content-Disposition', 'attachment;filename="'.$informe.'_'.date("dmY").'.csv"');
                $excel = $this->createExcelWorkshop($results, $partners);
            }
            elseif ($type == 'numworkshopbypartner'){

                $trans     = $this->get('translator');
                $nTalleres = $trans->trans('workshops');
                $nSocio    = $trans->trans('partner');
                $informe   = $trans->trans('numworkshopbypartner');

                $select = "SELECT p.name as ".$nSocio.", count(e.id) as ".$nTalleres." FROM WorkshopBundle:Workshop e ";
                $where .= 'AND p.id = e.partner ';
                $join  = ' JOIN e.partner p ';

                if ($status != '0') {
                    if     ($status == "active"  ) {  $where .= 'AND e.active = 1 '; }
                    elseif ($status == "deactive") {  $where .= 'AND e.active = 0 '; }
                    elseif ($status == "test"    ) {  $where .= 'AND e.test = 1 '; }
                    elseif ($status == "adsplus" ) {  $where .= 'AND e.ad_service_plus = 0 '; }
                }
                if    ($partner != "0"     ) {  $where .= 'AND e.id != 0 ';
                                                $where .= 'AND p.id = '.$partner.' ';
                }
                if    ($shop != "0"        ) {  $join  = ' JOIN e.shop s ';
                                                $where .= 'AND s.id = e.shop ';
                                                $where .= 'AND s.id = '.$shop.' ';
                }
                if    ($typology != "0"    ) {  $join  = ' JOIN e.typology tp ';
                                                $where .= 'AND tp.id = e.typology ';
                                                $where .= 'AND tp.id = '.$typology.' ';
                }
                if(!$security->isGranted('ROLE_SUPER_ADMIN')){
                    $where .= 'AND e.country = '.$security->getToken()->getUser()->getCountry()->getId().' ';
                }else{
                    if    ($country != "0"  ) { $where .= 'AND e.country = '.$country.' '; }
                }

                $qt = $em->createQuery($select.$join." WHERE ".$where.' GROUP BY p.id ORDER BY '.$nTalleres.' DESC');
                $results   = $qt->getResult();

                $response->headers->set('Content-Disposition', 'attachment;filename="'.$informe.'_'.date("dmY").'.csv"');
                $excel = $this->createExcelStatistics($results);
            }
            elseif ($type == 'ticketbyworkshopforpartner'){

                $trans     = $this->get('translator');
                $nTickets  = $trans->trans('tickets');
                $nTaller   = $trans->trans('workshop');
                $nSocio    = $trans->trans('partner');
                $code      = $trans->trans('_code');
                $informe   = $trans->trans('ticketbyworkshopforpartner');

                $select = "SELECT count(w.id) as ".$nTickets.", w.name as ".$nTaller.", p.name as ".$nSocio.", p.code_partner as ".$code.$nSocio.", w.code_workshop as ".$code.$nTaller." FROM TicketBundle:Ticket e JOIN e.workshop w ";
                $where .= 'AND p.id = w.partner ';
                $join  = ' JOIN w.partner p ';

                if ($status != '0') {
                    if     ($status == "active"  ) {  $where .= 'AND w.active = 1 '; }
                    elseif ($status == "deactive") {  $where .= 'AND w.active = 0 '; }
                    elseif ($status == "test"    ) {  $where .= 'AND w.test = 1 '; }
                    elseif ($status == "adsplus" ) {  $where .= 'AND w.ad_service_plus = 0 '; }
                }
                if    ($partner != "0"     ) {  $where .= 'AND w.id != 0 ';
                                                $where .= 'AND p.id = '.$partner.' ';
                }
                if    ($shop != "0"        ) {  $join  = ' JOIN w.shop s ';
                                                $where .= 'AND s.id = w.shop ';
                                                $where .= 'AND s.id = '.$shop.' ';
                }
                if    ($typology != "0"    ) {  $join  = ' JOIN w.typology tp ';
                                                $where .= 'AND tp.id = w.typology ';
                                                $where .= 'AND tp.id = '.$typology.' ';
                }
                if(!$security->isGranted('ROLE_SUPER_ADMIN')){
                    $where .= 'AND w.country = '.$security->getToken()->getUser()->getCountry()->getId().' ';
                }else{
                    if    ($country != "0"  ) { $where .= 'AND w.country = '.$country.' '; }
                }

                $qt = $em->createQuery($select.$join." WHERE ".$where.' GROUP BY w.id ORDER BY '.$nTickets.' DESC');
                $results   = $qt->getResult();

                $response->headers->set('Content-Disposition', 'attachment;filename="'.$informe.'_'.date("dmY").'.csv"');
                $excel = $this->createExcelStatistics($results);
            }
            elseif ($type == 'numticketsbypartner'){

                $trans     = $this->get('translator');
                $nTickets  = $trans->trans('tickets');
                $nSocio    = $trans->trans('partner');
                $informe   = $trans->trans('numticketsbypartner');

                $select = "SELECT p.name as ".$nSocio.", count(w.id) as ".$nTickets." FROM TicketBundle:Ticket e JOIN e.workshop w ";
                $where .= 'AND w.id = e.workshop AND p.id = w.partner ';
                $join  = ' JOIN w.partner p ';

                if     ($status == "open"  ) {  $open = $em->getRepository('TicketBundle:Status')->findOneByName('open');
                                                $where .= 'AND e.status = '.$open->getId().' ';
                }
                elseif ($status == "closed") {  $closed = $em->getRepository('TicketBundle:Status')->findOneByName('closed');
                                                $where .= 'AND e.status = '.$closed->getId().' ';
                }
                if    ($partner != "0"     ) {  $where .= 'AND w.id != 0 ';
                                                $where .= 'AND p.id = '.$partner.' ';
                }
                if    ($assessor != '0'    ) {  $where .= 'AND e.assigned_to = '.$assessor;
                }
                if    ($created_by != '0'  ) {
                                                if ($created_by == 'tel'){
                                                    $join .= 'JOIN e.created_by u JOIN u.user_role ur';
                                                    $where .= 'AND ur.id != 4';
                                                }
                                                elseif($created_by == 'app'){
                                                    $join .= 'JOIN e.created_by u JOIN u.user_role ur';
                                                    $where .= 'AND ur.id = 4';
                                                }
                }
                if(!$security->isGranted('ROLE_SUPER_ADMIN')){
                    $where .= 'AND w.country = '.$security->getToken()->getUser()->getCountry()->getId().' ';
                }else{
                    if    ($country != "0"  ) { $where .= 'AND w.country = '.$country.' '; }
                }

                $qt = $em->createQuery($select.$join." WHERE ".$where.' GROUP BY p.id ORDER BY '.$nTickets.' DESC');
                $results   = $qt->getResult();

                $response->headers->set('Content-Disposition', 'attachment;filename="'.$informe.'_'.date("dmY").'.csv"');
                $excel = $this->createExcelStatistics($results);
            }
            elseif ($type == 'numticketsbysystem'){

                $trans       = $this->get('translator');
                $nTickets    = $trans->trans('tickets');
                $nSistema    = $trans->trans('system');
                $nSubsistema = $trans->trans('subsystem');
                $informe     = $trans->trans('numticketsbysystem');

                $select = "SELECT s.name as ".$nSistema.", ss.name as ".$nSubsistema.", count(w.id) as ".$nTickets." ";
                $join = ' JOIN e.workshop w ';
                $join .= ' JOIN e.subsystem ss ';
                $join .= ' JOIN ss.system s ';
                $where .= ' AND w.id = e.workshop ';
                $where .= ' AND e.subsystem = ss.id ';
                $where .= ' AND ss.system = s.id ';

                if     ($status == "open"  ) {  $open = $em->getRepository('TicketBundle:Status')->findOneByName('open');
                                                $where .= 'AND e.status = '.$open->getId().' ';
                }
                elseif ($status == "closed") {  $closed = $em->getRepository('TicketBundle:Status')->findOneByName('closed');
                                                $where .= 'AND e.status = '.$closed->getId().' ';
                }
                if    ($partner != "0"     ) {  $select .= ", p.name as Socio ";
                                                $join  .= ' JOIN w.partner p ';
                                                $where .= ' AND w.id != 0 ';
                                                $where .= ' AND p.id = '.$partner.' ';
                                                $where .= ' AND p.id = w.partner ';
                }
                if    ($workshop != "0"    ) {  $select .= ", w.name as Taller ";
                                                $where .= ' AND w.id = '.$workshop.' ';
                }
                if    ($assessor != '0'    ) {  $select .= ", a.name as Asesor ";
                                                $where .= 'AND e.assigned_to = '.$assessor;
                }
                if    ($created_by != '0'  ) {
                                                if ($created_by == 'tel'){
                                                    $join .= 'JOIN e.created_by u JOIN u.user_role ur';
                                                    $where .= 'AND ur.id != 4';
                                                }
                                                elseif($created_by == 'app'){
                                                    $join .= 'JOIN e.created_by u JOIN u.user_role ur';
                                                    $where .= 'AND ur.id = 4';
                                                }
                }
                if(!$security->isGranted('ROLE_SUPER_ADMIN')){
                    $where .= 'AND w.country = '.$security->getToken()->getUser()->getCountry()->getId().' ';
                }else{
                    if    ($country != "0"  ) { $where .= 'AND w.country = '.$country.' '; }
                }
                $select .= "FROM TicketBundle:Ticket e ";
                $qt = $em->createQuery($select.$join." WHERE ".$where.' GROUP BY ss.id ORDER BY '.$nTickets.' DESC, s.name, ss.name');
                $results   = $qt->getResult();

                // Traducción al idioma del administrador

                $key = array_keys($results);
                $size = sizeof($key);
                for ($i=0; $i<$size; $i++){

                    $sistema    = $results[$key[$i]][$nSistema];
                    $subsistema = $results[$key[$i]][$nSubsistema];

                    $results[$key[$i]][$nSistema]    = $trans->trans($sistema);
                    $results[$key[$i]][$nSubsistema] = $trans->trans($subsistema);
                }

                $response->headers->set('Content-Disposition', 'attachment;filename="'.$informe.'_'.date("dmY").'.csv"');
                $excel = $this->createExcelStatistics($results);
            }
            elseif ($type == 'numticketsbybrand'){

                $trans     = $this->get('translator');
                $nTickets  = $trans->trans('tickets');
                $nMarca    = $trans->trans('brand');
                $informe   = $trans->trans('numticketsbybrand');

                $select = "SELECT b.name as ".$nMarca.", count(e.id) as ".$nTickets." ";
                $join = ' JOIN e.workshop w ';
                $join .= ' JOIN e.car c ';
                $join .= ' JOIN c.brand b ';
                $where .= ' AND w.id = e.workshop ';
                $where .= ' AND e.car = c.id ';
                $where .= ' AND c.brand = b.id ';

                if     ($status == "open"  ) {  $open = $em->getRepository('TicketBundle:Status')->findOneByName('open');
                                                $where .= 'AND e.status = '.$open->getId().' ';
                }
                elseif ($status == "closed") {  $closed = $em->getRepository('TicketBundle:Status')->findOneByName('closed');
                                                $where .= 'AND e.status = '.$closed->getId().' ';
                }
                if    ($partner != "0"     ) {  $select .= ", p.name as Socio ";
                                                $join  .= ' JOIN w.partner p ';
                                                $where .= ' AND w.id != 0 ';
                                                $where .= ' AND p.id = '.$partner.' ';
                                                $where .= ' AND p.id = w.partner ';
                }
                if    ($workshop != "0"    ) {  $select .= ", w.name as Taller ";
                                                $where .= ' AND w.id = '.$workshop.' ';
                }
                if    ($assessor != '0'    ) {  $select .= ", a.name as Asesor ";
                                                $where .= 'AND e.assigned_to = '.$assessor;
                }
                if    ($created_by != '0'  ) {
                                                if ($created_by == 'tel'){
                                                    $join .= 'JOIN e.created_by u JOIN u.user_role ur';
                                                    $where .= 'AND ur.id != 4';
                                                }
                                                elseif($created_by == 'app'){
                                                    $join .= 'JOIN e.created_by u JOIN u.user_role ur';
                                                    $where .= 'AND ur.id = 4';
                                                }
                }
                if(!$security->isGranted('ROLE_SUPER_ADMIN')){
                    $where .= 'AND w.country = '.$security->getToken()->getUser()->getCountry()->getId().' ';
                }else{
                    if    ($country != "0"  ) { $where .= 'AND w.country = '.$country.' '; }
                }
                $select .= "FROM TicketBundle:Ticket e ";

                $qt = $em->createQuery($select.$join." WHERE ".$where.' GROUP BY b.id ORDER BY '.$nTickets.' DESC, b.name');
                $results   = $qt->getResult();

                $response->headers->set('Content-Disposition', 'attachment;filename="'.$informe.'_'.date("dmY").'.csv"');
                $excel = $this->createExcelStatistics($results);
            }
            elseif ($type == 'numticketsbymodel'){

                $trans     = $this->get('translator');
                $nTickets  = $trans->trans('tickets');
                $nMarca    = $trans->trans('brand');
                $nModelo   = $trans->trans('model');
                $informe   = $trans->trans('numticketsbymodel');

                $select = "SELECT b.name as ".$nMarca.", m.name as ".$nModelo.", count(e.id) as ".$nTickets." ";
                $join = ' JOIN e.workshop w ';
                $join .= ' JOIN e.car c ';
                $join .= ' JOIN c.model m ';
                $join .= ' JOIN c.brand b ';
                $where .= ' AND w.id = e.workshop ';
                $where .= ' AND e.car = c.id ';
                $where .= ' AND c.model = m.id ';
                $where .= ' AND c.brand = b.id ';

                if     ($status == "open"  ) {  $open = $em->getRepository('TicketBundle:Status')->findOneByName('open');
                                                $where .= 'AND e.status = '.$open->getId().' ';
                }
                elseif ($status == "closed") {  $closed = $em->getRepository('TicketBundle:Status')->findOneByName('closed');
                                                $where .= 'AND e.status = '.$closed->getId().' ';
                }
                if    ($partner != "0"     ) {  $select .= ", p.name as Socio ";
                                                $join  .= ' JOIN w.partner p ';
                                                $where .= ' AND w.id != 0 ';
                                                $where .= ' AND p.id = '.$partner.' ';
                                                $where .= ' AND p.id = w.partner ';
                }
                if    ($workshop != "0"    ) {  $select .= ", w.name as Taller ";
                                                $where .= ' AND w.id = '.$workshop.' ';
                }
                if    ($assessor != '0'    ) {  $select .= ", a.name as Asesor ";
                                                $where .= 'AND e.assigned_to = '.$assessor;
                }
                if    ($created_by != '0'  ) {
                                                if ($created_by == 'tel'){
                                                    $join .= 'JOIN e.created_by u JOIN u.user_role ur';
                                                    $where .= 'AND ur.id != 4';
                                                }
                                                elseif($created_by == 'app'){
                                                    $join .= 'JOIN e.created_by u JOIN u.user_role ur';
                                                    $where .= 'AND ur.id = 4';
                                                }
                }
                if(!$security->isGranted('ROLE_SUPER_ADMIN')){
                    $where .= 'AND w.country = '.$security->getToken()->getUser()->getCountry()->getId().' ';
                }else{
                    if    ($country != "0"  ) { $where .= 'AND w.country = '.$country.' '; }
                }
                $select .= "FROM TicketBundle:Ticket e ";

                $qt = $em->createQuery($select.$join." WHERE ".$where.' GROUP BY m.id ORDER BY '.$nTickets.' DESC, m.name');
                $results   = $qt->getResult();

                $response->headers->set('Content-Disposition', 'attachment;filename="'.$informe.'_'.date("dmY").'.csv"');
                $excel = $this->createExcelStatistics($results);
            }
            elseif ($type == 'numticketsbyfabyear'){

                $trans     = $this->get('translator');
                $nTickets  = $trans->trans('tickets');
                $date      = $trans->trans('date');
                $informe   = $trans->trans('numticketsbyfabyear');

                $select = "SELECT v.inicio as ".$date.", count(e.id) as ".$nTickets." ";
                $join  = ' JOIN e.workshop w ';
                $join .= ' JOIN e.car c ';
                $join .= ' JOIN c.version v ';
                $where  = 'e.id != 0 '; // Reiniciamos where para no cojer los filtros e.created_at
                $where .= ' AND w.id = e.workshop ';
                $where .= ' AND e.car = c.id ';
                $where .= ' AND c.version = v.id ';

                if ($from_y != '0' ) { $where .= "AND v.inicio >= ".$from_y."00 "; }
                if ($to_y   != '0' ) { $where .= "AND v.inicio <= ".$to_y."99 "; }

                if     ($status == "open"  ) {  $open = $em->getRepository('TicketBundle:Status')->findOneByName('open');
                                                $where .= 'AND e.status = '.$open->getId().' ';
                }
                elseif ($status == "closed") {  $closed = $em->getRepository('TicketBundle:Status')->findOneByName('closed');
                                                $where .= 'AND e.status = '.$closed->getId().' ';
                }
                if    ($partner != "0"     ) {  $select .= ", p.name as Socio ";
                                                $join  .= ' JOIN w.partner p ';
                                                $where .= ' AND w.id != 0 ';
                                                $where .= ' AND p.id = '.$partner.' ';
                                                $where .= ' AND p.id = w.partner ';
                }
                if    ($workshop != "0"    ) {  $select .= ", w.name as Taller ";
                                                $where .= ' AND w.id = '.$workshop.' ';
                }
                if    ($assessor != '0'    ) {  $select .= ", a.name as Asesor ";
                                                $where .= 'AND e.assigned_to = '.$assessor;
                }
                if    ($created_by != '0'  ) {
                                                if ($created_by == 'tel'){
                                                    $join .= 'JOIN e.created_by u JOIN u.user_role ur';
                                                    $where .= 'AND ur.id != 4';
                                                }
                                                elseif($created_by == 'app'){
                                                    $join .= 'JOIN e.created_by u JOIN u.user_role ur';
                                                    $where .= 'AND ur.id = 4';
                                                }
                }
                if(!$security->isGranted('ROLE_SUPER_ADMIN')){
                    $where .= 'AND w.country = '.$security->getToken()->getUser()->getCountry()->getId().' ';
                }else{
                    if    ($country != "0"  ) { $where .= 'AND w.country = '.$country.' '; }
                }
                $select .= "FROM TicketBundle:Ticket e ";
                $qt = $em->createQuery($select.$join." WHERE ".$where.' GROUP BY v.inicio ORDER BY v.inicio DESC, '.$nTickets.' DESC ');
                $results   = $qt->getResult();
                $years = array();
                foreach ($results as $res) {

                    $inicio = substr($res[$date], 0, 4);
                    if(!isset($years[$inicio])) {
                        $years[$inicio][$date] = $inicio;
                        $years[$inicio][$nTickets] = $res[$nTickets];
                    }
                    else $years[$inicio][$nTickets] = $years[$inicio][$nTickets] + $res[$nTickets];
                }

                $response->headers->set('Content-Disposition', 'attachment;filename="'.$informe.'_'.date("dmY").'.csv"');
                $excel = $this->createExcelFabYear($years);
            }
        }
        else{
            /*
                SELECT w.id, t.id FROM ticket t JOIN  workshop w ON t.workshop_id = w.id GROUP BY w.id ORDER BY t.id DESC
            */
            $where .= 'AND e.workshop = w.id ';

            if($security->isGranted('ROLE_SUPER_ADMIN')){
                if    ($country != '0'     ) { $where .= 'AND e.country = '.$country.' '; }
            }else{
                $where .= 'AND w.country = '.$security->getToken()->getUser()->getCountry()->getId().' ';
            }

            $where .= 'GROUP BY w.id ';
            $where .= 'ORDER BY e.id DESC ';

            $qid = $em->createQuery("select MAX(e.id) as t_id, w.id as w_id from TicketBundle:Ticket e JOIN e.workshop w WHERE ".$where);
            $resultsid = $qid->getResult();

            $ids = '0';
            foreach ($resultsid as $rid) {
                $ids .= ', '.$rid['t_id'];
            }

            $qt = $em->createQuery("select partial e.{ id, description, solution, created_at }, partial w.{ id, code_partner, code_workshop, name } from TicketBundle:Ticket e JOIN e.workshop w WHERE e.id IN (".$ids.")");
            $results   = $qt->getResult();

            $trans     = $this->get('translator');
            $informe   = $trans->trans('statistic.last_tickets' );
            $response->headers->set('Content-Disposition', 'attachment;filename="'.$informe.'_'.date("dmY").'.csv"');
            $excel = $this->createExcelLastTickets($results);
        }

        $excel = UtilController::sinAcentos($excel);
        $response->setContent($excel);
        return $response;
    }

    public function createExcelTicket($results){
        //Creación de cabecera
        //'ID;Date;Car;Assigned To;Description;Status;Solution;';
        $excel =
            $this->get('translator')->trans('ticket').';'.
            $this->get('translator')->trans('workshop').';'.
            $this->get('translator')->trans('region').';'.
            $this->get('translator')->trans('typology').';'.
            $this->get('translator')->trans('brand').';'.
            $this->get('translator')->trans('model').';'.
            $this->get('translator')->trans('version').';'.
            $this->get('translator')->trans('year').';'.
            $this->get('translator')->trans('vin').';'.
            $this->get('translator')->trans('motor').';'.
            $this->get('translator')->trans('system').';'.
            $this->get('translator')->trans('subsystem').';'.
            $this->get('translator')->trans('description').';'.
            $this->get('translator')->trans('solution').';'.
            $this->get('translator')->trans('status').';'.
            $this->get('translator')->trans('date').';'.
            $this->get('translator')->trans('assessor').';'.
            $this->get('translator')->trans('importance').';';
        $excel.="\n";

        $em = $this->getDoctrine()->getEntityManager();

        foreach ($results as $row) {
            $excel.=$row->getId().';';

            $workshop = $row->getWorkshop();
            $excel.=$workshop->getPartner()->getCodePartner().' - '.$workshop->getCodeWorkshop().';';

            $shop = $workshop->getShop();
            if(isset($shop)) $code_shop = $shop->getCodeShop();
            else $code_shop = '-';
            $excel.=$code_shop.';';

            $excel.=$workshop->getRegion().';';

            $typology = $workshop->getTypology();
            $excel.=$this->get('translator')->trans($typology).';';

            $car = $row->getCar();
            $excel.=$car->getBrand().';';
            $excel.=$car->getModel().';';
            $excel.=$car->getVersion().';';
            $excel.=$car->getYear().';';
            $excel.=$car->getVin().';';
            $excel.=$car->getMotor().';';

            // Ticket
            $system = $row->getSubsystem();
            if(isset($system)) {
                $excel.=$system->getSystem().';';
                $excel.=$system.';';
            }else{
                $excel.=$system.'- ; - ;';
            }

            $buscar=array(chr(13).chr(10), "\r\n", "\n", "\r");
            $reemplazar=array("", "", "", "");
            $description=str_ireplace($buscar,$reemplazar,$row->getDescription());
            $excel.=$description.';';

            $buscar=array(chr(13).chr(10), "\r\n", "\n", "\r");
            $reemplazar=array("", "", "", "");
            $solution=str_ireplace($buscar,$reemplazar,$row->getSolution());
            $excel.=$solution.';';

            $status = $row->getStatus();

            $excel.=$this->get('translator')->trans($status).';';

            $created = $row->getCreatedAt();
            $excel.=$created->format("d/m/Y").';';
            $excel.=$row->getAssignedTo().';';

            $importance = $row->getImportance();
            $excel.=$this->get('translator')->trans($importance).';';

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
                $this->get('translator')->trans('code_shop').';'.
                $this->get('translator')->trans('code_workshop').';'.
                $this->get('translator')->trans('name').';'.
                $this->get('translator')->trans('partner').';'. //ID ticket
                $this->get('translator')->trans('shop').';'.
                $this->get('translator')->trans('email').';'.
                $this->get('translator')->trans('tel').';'.
                $this->get('translator')->trans('active').';'.
                $this->get('translator')->trans('testing').';'.
                $this->get('translator')->trans('adsplus').';';
        $excel.="\n";

        $em = $this->getDoctrine()->getEntityManager();

        foreach ($results as $row) {
            $excel.=$row->getCodePartner().';';

            $shop = $row->getShop();
            if(isset($shop)) $code_shop = $shop->getCodeShop();
            else $code_shop = '-';
            $excel.=$code_shop.';';

            $excel.=$row->getCodeWorkshop().';';

            $buscar=array(';', chr(13).chr(10), "\r\n", "\n", "\r");
            $reemplazar=array("", "", "", "");
            $name=str_ireplace($buscar,$reemplazar,$row->getName());
            $excel.=$name.';';

            $excel.=$partners[$row->getCodePartner()].';';
            $excel.=$row->getShop().';';
            $excel.=$row->getEmail1().';';
            $excel.=$row->getPhoneNumber1().';';

            if ($row->getActive() == 1) $active = $this->get('translator')->trans('yes');
            else $active = $this->get('translator')->trans('no');
            $excel.=$active.';';

            if ($row->getTest() == 1) $test = $this->get('translator')->trans('yes');
            else $test = $this->get('translator')->trans('no');
            $excel.=$test.';';

            if ($row->getAdServicePlus() == 1) $adsplus = $this->get('translator')->trans('yes');
            else $adsplus = $this->get('translator')->trans('no');
            $excel.=$adsplus.';';

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
                $this->get('translator')->trans('code_shop').';'.
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

            $excel.=$row->getWorkshop()->getCodePartner().';';
            $excel.=$row->getWorkshop()->getCodeWorkshop().';';

            $buscar=array(';', chr(13).chr(10), "\r\n", "\n", "\r");
            $reemplazar=array("", "", "", "");
            $name=str_ireplace($buscar,$reemplazar,$row->getWorkshop()->getName());
            $excel.=$name.';';

            $excel.=$row->getWorkshop()->getPartner().';';
            $excel.=$row->getId().';';

            $buscar=array(chr(13).chr(10), "\r\n", "\n", "\r");
            $reemplazar=array("", "", "", "");
            $description=str_ireplace($buscar,$reemplazar,$row->getDescription());
            $excel.=$description.';';

            $excel.=$row->getStatus().';';

            $buscar=array(chr(13).chr(10), "\r\n", "\n", "\r");
            $reemplazar=array("", "", "", "");
            $solution=str_ireplace($buscar,$reemplazar,$row->getSolution());
            $excel.=$solution.';';
            $excel.=$row->getCreatedAt()->format('Y-m-d').';';
            $excel.="\n";
        }

        $excel = nl2br($excel);
        $excel = str_replace('<br />', '.', $excel);
        $excel = str_replace(',', '.', $excel);
        return($excel);
    }

    public function createExcelStatistics($results){
        $excel = '';
        $firstKey = ''; // guardaremos la primera key para introducir el salto de linea

        if (isset($results[0])) {
            //Bucle para las cabeceras
            foreach ($results[0] as $key => $value) {
                if($firstKey == '') { $firstKey = $key; }
                $excel.=$key.';';
            }

            foreach ($results as $res)
            {
                foreach ($res as $key => $value)
                {
                    if($firstKey == $key) $excel.="\n";

                    $buscar=array(',', ';', chr(13).chr(10), "\r\n", "\n", "\r");
                    $reemplazar=array("", "", "", "");
                    $text=str_ireplace($buscar,$reemplazar,$value);
                    $excel.=$text.';';
                }
            }
        }
        return($excel);
    }

    public function createExcelFabYear($results){
        $excel = '';
        $trans = $this->get('translator');
        $nTickets = $trans->trans('tickets');
        $date  = $trans->trans('date');

        $excel.=$date.';'.$nTickets.';';

        foreach ($results as $res)
        {
            foreach ($res as $key => $value)
            {
                if($key == $date) $excel.="\n";
                $buscar=array(',', ';', chr(13).chr(10), "\r\n", "\n", "\r");
                $reemplazar=array("", "", "", "");
                $text=str_ireplace($buscar,$reemplazar,$value);
                $excel.=$text.';';
            }
        }
        return($excel);
    }
}