<?php

namespace Adservice\StatisticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Adservice\StatisticBundle\Entity\Statistic;
use Adservice\StatisticBundle\Form\DateType;
use Adservice\UtilBundle\Entity\Pagination;
use Adservice\UtilBundle\Controller\UtilController as UtilController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class StatisticController extends Controller {

    public function listAction($type='0', $page=1, $from_y ='0', $from_m='0', $from_d ='0', $to_y   ='0', $to_m  ='0', $to_d   ='0', $partner='0', $shop='0', $workshop='0', $typology='0', $status='0', $country='0', $assessor='0', $created_by='0', $raport='0') {

        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false){
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getEntityManager();
        $security = $this->get('security.context');
        $request  = $this->getRequest();
        $statistic = new Statistic();
        $pagination = new Pagination($page);
        $params = array();
        $joins  = array();

        if($security->isGranted('ROLE_SUPER_ADMIN')){
            $qp = $em->createQuery("select partial p.{id,name, code_partner} from PartnerBundle:Partner p WHERE p.active = 1 ");
            $qs = $em->createQuery("select partial s.{id,name} from PartnerBundle:Shop s WHERE s.active = 1 ");
            $qw = $em->createQuery("select partial w.{id,name, code_partner, code_workshop} from WorkshopBundle:Workshop w WHERE w.active = 1 ");
            $qa = $em->createQuery("select partial a.{id,username} from UserBundle:User a JOIN a.user_role r WHERE r = 3 and a.active = 1 ");
            $qt = $em->createQuery("select partial t.{id,name} from WorkshopBundle:Typology t WHERE t.active = 1 ");
            $partners   = $qp->getResult();
            $shops      = $qs->getResult();
            $workshops  = $qw->getResult();
            $assessors  = $qa->getResult();
            $typologies = $qt->getResult();
        }else{
            $catserv = $security->getToken()->getUser()->getCategoryService()->getId();
            $country = $security->getToken()->getUser()->getCountry()->getId();
            $qp = $em->createQuery("select partial p.{id,name, code_partner} from PartnerBundle:Partner p WHERE p.category_service = ".$catserv." AND p.active = 1 ");
            $qs = $em->createQuery("select partial s.{id,name} from PartnerBundle:Shop s WHERE s.category_service = ".$catserv." AND s.active = 1 ");
            $qw = $em->createQuery("select partial w.{id,name, code_partner, code_workshop} from WorkshopBundle:Workshop w WHERE w.category_service = ".$catserv." AND w.active = 1 ");
            $qa = $em->createQuery("select partial a.{id,username} from UserBundle:User a JOIN a.user_role r WHERE r = 3 AND a.category_service = ".$catserv." AND a.active = 1 ");
            $qt = $em->createQuery("select partial t.{id,name} from WorkshopBundle:Typology t WHERE t.category_service = ".$catserv." AND t.active = 1 ");
            $partners   = $qp->getResult();
            $shops      = $qs->getResult();
            $workshops  = $qw->getResult();
            $assessors  = $qa->getResult();
            $typologies = $qt->getResult();

        }
        //Estadísticas generales de Ad-service
        $statistic->setNumUsers        ($statistic->getNumUsersInAdservice    ($em, $security));
        $statistic->setNumPartners     ($statistic->getNumPartnersInAdservice ($em, $security));
        $statistic->setNumShops        ($statistic->getNumShopsInAdservice    ($em, $security)-1); //Tienda por defecto '...' no cuenta.
        $statistic->setNumWorkshops    ($statistic->getNumWorkshopsInAdservice($em, $security));
        $statistic->setNumTickets      ($statistic->getTicketsInAdservice     ($em, $security));
        $statistic->setNumTicketsTel   ($statistic->getNumTicketsByTel        ($em, $security));
        $statistic->setNumTicketsApp   ($statistic->getNumTicketsByApp        ($em, $security));
        $statistic->setNumOpenTickets  ($statistic->getNumTicketsByStatus($em, 'open' , $security));
        $statistic->setNumClosedTickets($statistic->getNumTicketsByStatus($em, 'close', $security));
        $countries = $em->getRepository('UtilBundle:Country')->findAll();
        $catservices = $em->getRepository('UserBundle:CategoryService')->findAll();

        return $this->render('StatisticBundle:Statistic:list_statistics.html.twig', array('page'        => $page,
                                                                                          'statistic'   => $statistic,
                                                                                          'from_y'      => $from_y,
                                                                                          'from_m'      => $from_m,
                                                                                          'from_d'      => $from_d,
                                                                                          'to_y'        => $to_y  ,
                                                                                          'to_m'        => $to_m ,
                                                                                          'to_d'        => $to_d  ,
                                                                                          'partners'    => $partners,
                                                                                          'shops'       => $shops,
                                                                                          'workshops'   => $workshops,
                                                                                          'assessors'   => $assessors,
                                                                                          'typologies'  => $typologies,
                                                                                          'countries'   => $countries,
                                                                                          'catservices' => $catservices,
                                                                                          'pagination'  => $pagination,
                                                                                          'type'        => $type,
                                                                                          'partner'     => $partner,
                                                                                          'shop'        => $shop,
                                                                                          'wks'         => $workshop,
                                                                                          'assessor'    => $assessor,
                                                                                          'created_by'  => $created_by,
                                                                                          'typology'    => $typology,
                                                                                          'status'      => $status,
                                                                                          'country'     => $country,
                                                                                          //'length'    => $length,
                                                                            ));
    }

    public function listTopAction($type='0', $from_y ='0', $from_m='0', $from_d ='0', $to_y   ='0', $to_m  ='0', $to_d   ='0', $partner='0', $shop='0', $workshop='0', $typology='0', $status='0', $country='0', $assessor='0', $created_by='0', $raport='0') {

        if ($this->get('security.context')->isGranted('ROLE_TOP_AD') === false){
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $security = $this->get('security.context');
        $request  = $this->getRequest();
        $statistic = new Statistic();
        $params = array();
        $joins  = array();

        if($security->isGranted('ROLE_SUPER_ADMIN')){
            $qp = $em->createQuery("select partial p.{id,name, code_partner} from PartnerBundle:Partner p WHERE p.active = 1 ");
            $qs = $em->createQuery("select partial s.{id,name} from PartnerBundle:Shop s WHERE s.active = 1 ");
            $qw = $em->createQuery("select partial w.{id,name, code_workshop} from WorkshopBundle:Workshop w WHERE w.active = 1 ");
            $qa = $em->createQuery("select partial a.{id,username} from UserBundle:User a JOIN a.user_role r WHERE r = 3 and a.active = 1 ");
            $qt = $em->createQuery("select partial t.{id,name} from WorkshopBundle:Typology t WHERE t.active = 1 ");
            $partners   = $qp->getResult();
            $shops      = $qs->getResult();
            $workshops  = $qw->getResult();
            $assessors  = $qa->getResult();
            $typologies = $qt->getResult();
        }else{
            $catserv = $security->getToken()->getUser()->getCategoryService()->getId();
            $qp = $em->createQuery("select partial p.{id,name, code_partner} from PartnerBundle:Partner p WHERE p.category_service = ".$catserv." AND p.active = 1 ");
            $qs = $em->createQuery("select partial s.{id,name} from PartnerBundle:Shop s WHERE s.category_service = ".$catserv." AND s.active = 1 ");
            $qw = $em->createQuery("select partial w.{id,name, code_workshop} from WorkshopBundle:Workshop w WHERE w.category_service = ".$catserv." AND w.active = 1 ");
            $qa = $em->createQuery("select partial a.{id,username} from UserBundle:User a JOIN a.user_role r WHERE r = 3 AND a.category_service = ".$catserv." AND a.active = 1 ");
            $qt = $em->createQuery("select partial t.{id,name} from WorkshopBundle:Typology t WHERE t.category_service = ".$catserv." AND t.active = 1 ");
            $partners   = $qp->getResult();
            $shops      = $qs->getResult();
            $workshops  = $qw->getResult();
            $assessors  = $qa->getResult();
            $typologies = $qt->getResult();

        }
        $countries = $em->getRepository('UtilBundle:Country')->findAll();

        return $this->render('StatisticBundle:Statistic:list_statistics_top.html.twig', array('from_y'    => $from_y,
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

    public function doExcelAction($type='0', $page=1, $from_y ='0', $from_m='0', $from_d ='0', $to_y   ='0', $to_m  ='0', $to_d   ='0', $partner='0', $shop='0', $workshop='0', $typology='0', $status='0', $country='0', $catserv='0', $assessor='0', $created_by='0', $raport='0'){

        $em = $this->getDoctrine()->getEntityManager();
        $statistic = new Statistic();
        $security = $this->get('security.context');
        $pagination = new Pagination($page);
        $where = 'e.id != 0 ';
        $join  = '';

        $response = new Response();
        // $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Type', 'application/msexcel');

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

            if ($from_y != '0' and $from_m != '0' and $from_d != '0') {

                $from_date = $from_y.'-'.$from_m.'-'.$from_d.' 00:00:00';
                $where .= "AND e.update_at >= '".$from_y.'-'.$from_m.'-'.$from_d." 00:00:00'";
            }
            if ($to_y   != '0' and $to_m   != '0' and $to_d   != '0') {

                $to_date = $to_y.'-'.$to_m.'-'.$to_d.' 23:59:59';
                $where .= "AND e.update_at <= '".$to_y  .'-'.$to_m  .'-'.$to_d  ." 23:59:59'";
            }

            if ($type == 'ticket'  )
            {
                //Realizamos una query deshydratada con los datos ya montados
                $qb = $em->getRepository('TicketBundle:Ticket')
                    ->createQueryBuilder('e')
                    ->select(
                        'e.id',
                        'e.created_at',
                        'e.description',
                        'e.solution',
                        's.name as status',
                        'w.name as nameWorkshop',
                        'w.region as regionWorkshop',
                        'p.name as namePartner',
                        'cs.category_service as categoryService',
                        'w.code_partner as codePartner',
                        'w.code_workshop as codeWorkshop',
                        'w.internal_code as internalCodeWorkshop',
                        'sh.code_shop as codeShop',
                        'sh.name as nameShop',
                        'sy.name as system',
                        'ss.name as subsystem',
                        'ty.name as typologyWorkshop',
                        'br.name as brandCar',
                        'mo.name as modelCar',
                        've.name as versionCar',
                        'c.year as yearCar',
                        'c.vin as vinCar',
                        'c.motor as motorCar',
                        'im.importance'
                    )
                    ->addSelect("concat(concat(ass.name,' '), ass.surname) as assignedTo")//Concatenamos nombre y apellido
                    ->leftJoin('e.workshop', 'w')
                    ->leftJoin('w.category_service', 'cs')
                    ->leftJoin('e.status', 's')
                    ->leftJoin('e.importance', 'im')
                    ->leftJoin('e.car', 'c')
                    ->leftJoin('e.subsystem', 'ss')
                    ->leftJoin('e.assigned_to', 'ass')
                    ->leftJoin('c.brand', 'br')
                    ->leftJoin('c.model', 'mo')
                    ->leftJoin('c.version', 've')
                    ->leftJoin('w.partner', 'p')
                    ->leftJoin('w.shop', 'sh')
                    ->leftJoin('w.typology', 'ty')
                    ->leftJoin('ss.system', 'sy')
                    ->groupBy('e.id')
                    ->orderBy('e.id');

                if (isset($from_date)) {

                    $qb = $qb->andWhere('e.created_at >= :created_at_from')
                        ->setParameter('created_at_from', $from_date);
                }

                if (isset($to_date)) {

                    $qb = $qb->andWhere('e.created_at <= :created_at_to')
                        ->setParameter('created_at_to', $to_date);
                }

                if ($status == "open" )
                {
                    $qb = $qb->andWhere('s.name = :status')
                        ->setParameter('status', 'open');
                }
                elseif ($status == "closed")
                {
                    $qb = $qb->andWhere('s.name = :status')
                        ->setParameter('status', 'closed');
                }

                if ($partner != "0")
                {
                    $qb = $qb->andWhere('w.id != 0')
                        ->andWhere('p.id = :partner')
                        ->setParameter('partner', $partner);
                }

                if ($workshop != "0")
                {
                    $qb = $qb->andWhere('w.id = :workshop')
                        ->setParameter('workshop', $workshop);
                }

                if ($assessor != '0')
                {
                    $qb = $qb->andWhere('e.assigned_to = :assessor')
                        ->setParameter('assessor', $assessor);
                }

                if ($created_by != '0'  ) {

                    if ($created_by == 'tel')
                    {
                        $qb = $qb
                            ->leftJoin('e.created_by', 'u')
                            ->leftJoin('u.user_role', 'ur')
                            ->andWhere('ur.id != :role')
                            ->setParameter('role', 4);
                    }
                    elseif($created_by == 'app')
                    {
                        $qb = $qb
                            ->leftJoin('e.created_by', 'u')
                            ->leftJoin('u.user_role', 'ur')
                            ->andWhere('ur.id = :role')
                            ->setParameter('role', 4);
                    }
                }

                if(!$security->isGranted('ROLE_SUPER_ADMIN'))
                {
                    $qb = $qb->andWhere('w.country = :country')
                        ->setParameter('country', $security->getToken()->getUser()->getCountry()->getId());
                }
                else
                {
                    if ($country != "0")
                    {
                        $qb = $qb->andWhere('w.country = :country')
                            ->setParameter('country', $country);
                    }
                }

                if ($catserv != "0")
                {
                    $qb = $qb->andWhere('w.category_service = :catserv')
                        ->setParameter('catserv', $catserv);
                }

                /********************************** Revisar tiempos de ejecución **************************************/
                //$start = microtime(true);
                //echo (microtime(true)-$start). ' s';
                /******************************************************************************************************/

                /******************************** Memoria actual usada ************************************************/
                //$mem_usage = memory_get_usage(true);
                //if ($mem_usage < 1024)
                //    echo $mem_usage." bytes";
                //elseif ($mem_usage < 1048576)
                //    echo round($mem_usage/1024,2)." kilobytes";
                //else
                //    echo round($mem_usage/1048576,2)." megabytes";
                //echo "<br/>";
                /******************************************************************************************************/
                $resultsDehydrated = $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

                $response->headers->set('Content-Disposition', 'attachment;filename="informeTickets_'.date("dmY").'.xls"');
                $excel = $this->createExcelTicket($resultsDehydrated);
            }
            elseif ($type == 'workshop')
            {
                //Realizamos una query deshydratada con los datos ya montados
                $qb = $em->getRepository('WorkshopBundle:Workshop')
                    ->createQueryBuilder('w')
                    ->select( 'w.id', 'w.code_workshop', 'w.internal_code', 'w.commercial_code', 'w.name', 'w.email_1', 'w.phone_number_1',
                        'w.active', 'w.created_at', 'w.modified_at', 'w.update_at', 'w.lowdate_at', 'w.ad_service_plus', 'w.test', 'w.haschecks',
                        'w.numchecks', 'w.infotech','sh.code_shop', 'sh.name as shop', 'p.name as partner', 'cs.category_service', 'w.code_partner', 't.name as tipology')
                    ->leftJoin('w.shop', 'sh')
                    ->leftJoin('w.partner', 'p')
                    ->leftJoin('w.category_service', 'cs')
                    ->leftJoin('w.typology', 't')
                    ->groupBy('w.id')
                    ->orderBy('w.id');

                if ($partner!= "0") {

                    $qb = $qb->andWhere('p.id = :partner')
                            ->setParameter('partner', $partner);
                }

                if ($shop != "0") {

                    $qb = $qb->andWhere('sh.id = :shop')
                            ->setParameter('shop', $shop);
                }

                if (isset($to_date)) $to_date = $to_y.'-'.$to_m.'-'.$to_d.' 00:00:00';

                switch ($status) {

                    case "active":

                        // $qb = $qb->andWhere("w.created_at <= '2016-08-15 23:59:59'")
                        //          ->andWhere("(w.active = 1 AND (w.lowdate_at IS NULL OR w.modified_at > w.lowdate_at)) OR ( w.active = 0 AND w.lowdate_at  >= '2016-08-15 23:59:59' )");

                        if(!isset($from_date) and !isset($to_date))
                        {
                            $qb = $qb->andWhere('e.active = 1')
                                     ->andWhere('e.test != 1');
                        }
                        elseif (isset($from_date) and isset($to_date)){

                            $qb = $qb->andWhere('e.update_at <= :update_at_to')
                                     ->andWhere('(e.endtest_at IS NULL OR e.endtest_at >= :endtest_at_from OR e.endtest_at >= :endtest_at_to)')
                                     ->andWhere('(e.lowdate_at IS NULL OR e.lowdate_at <= e.update_at OR e.lowdate_at >= :lowdate_at_from)')
                                     ->setParameter('update_at_to', $to_date)
                                     ->setParameter('endtest_at_from', $from_date)
                                     ->setParameter('endtest_at_to', $to_date)
                                     ->setParameter('lowdate_at_from', $from_date);
                        }
                        else{
                            if (isset($from_date))
                            {
                                $qb = $qb->andWhere('e.update_at >= :update_at_from')
                                         ->andWhere('(e.endtest_at IS NULL OR e.endtest_at >= :endtest_at_from)')
                                         ->andWhere('(e.lowdate_at IS NULL OR e.lowdate_at <= e.update_at)')
                                         ->setParameter('update_at_from', $from_date)
                                         ->setParameter('endtest_at_from', $from_date);
                            }

                            if (isset($to_date))
                            {
                                $qb = $qb->andWhere('e.update_at <= :update_at_to')
                                         ->andWhere('(e.endtest_at IS NULL OR e.endtest_at >= :endtest_at_to)')
                                         ->andWhere('(e.lowdate_at IS NULL OR e.lowdate_at >= :lowdate_at_to)')
                                         ->setParameter('update_at_to', $to_date)
                                         ->setParameter('endtest_at_to', $to_date)
                                         ->setParameter('lowdate_at_to', $to_date);
                            }
                        }
                        break;

                    case "deactive":

                        if(!isset($from_date) and !isset($to_date))
                        {
                            $qb = $qb->andWhere('w.active != 1');
                        }
                        else
                        {
                            if (isset($from_date))
                            {
                                $qb = $qb->andWhere('w.lowdate_at >= :lowdate_at_from')
                                         ->setParameter('lowdate_at_from', $from_date);
                            }
                            if (isset($to_date))
                            {
                                $qb = $qb->andWhere('w.update_at >= :update_at_to')
                                         ->setParameter('update_at_to', $to_date);
                            }
                        }
                        break;

                    case "test":

                        if(!isset($from_date) and !isset($to_date))
                        {
                            $qb = $qb->andWhere('w.test = 1');
                        }
                        else
                        {
                            if (isset($from_date))
                            {
                                $qb = $qb->andWhere('w.endtest_at >= :endtest_at_from')
                                         ->setParameter('endtest_at_from', $from_date);
                            }

                            if (isset($to_date))
                            {
                                $qb = $qb->andWhere('w.endtest_at >= :endtest_at_to')
                                         ->setParameter('endtest_at_to', $to_date);
                            }
                        }
                        break;

                    case "adsplus":
                        $qb = $qb->andWhere('w.ad_service_plus = 1');
                        break;

                    case "check":
                        $qb = $qb->andWhere('w.haschecks = 1');
                        break;

                    case "infotech":
                        $qb = $qb->andWhere('w.infotech = 1');
                        break;

                    default:
                        if (isset($from_date))
                        {
                            if (!isset($to_date))
                            {
                                $qb = $qb->andWhere('w.created_at >= :created_at_from')
                                         ->setParameter('created_at_from', $from_date);
                            }
                            $qb = $qb->andWhere('w.lowdate_at > :from_date OR w.lowdate_at IS NULL')
                                     ->setParameter('from_date', $from_date);
                        }

                        if (isset($to_date))
                        {
                            if (!isset($from_date))
                            {
                                $qb = $qb->andWhere('w.lowdate_at > :from_date')
                                     ->setParameter('from_date', $to_date);
                            }
                            $qb = $qb->andWhere('w.created_at <= :created_at_to')
                                     ->setParameter('created_at_to', $to_date);
                        }
                        break;
                }

                if(!$security->isGranted('ROLE_SUPER_ADMIN')){

                    $qb = $qb->andWhere('w.country = :country')
                        ->setParameter('country', $security->getToken()->getUser()->getCountry()->getId());
                }else{

                    if ($country != "0") {

                        $qb = $qb->andWhere('w.country = :country')
                            ->setParameter('country', $country);
                    }
                }
                if ($catserv != "0")
                {
                    $qb = $qb->andWhere('cs.id = :catserv')
                        ->setParameter('catserv', $catserv);
                }
                if ($typology != "0") {

                    $qb = $qb->andWhere('t.id = :typology')
                            ->setParameter('typology', $typology);
                }

                $resultsDehydrated = $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

                $response->headers->set('Content-Disposition', 'attachment;filename="informeTalleres_'.date("dmY").'.xls"');
                $excel = $this->createExcelWorkshop($resultsDehydrated);
            }
            elseif ($type == 'no-ticket'){

                //listado de talleres sin tickets
                $consulta = $em ->createQuery('SELECT w.id FROM TicketBundle:Ticket t JOIN t.workshop w GROUP BY t.workshop');

                $workshop_query = '0';
                foreach ( $consulta->getResult() as $row) { $workshop_query = $workshop_query.', '.$row['id']; }

                //Realizamos una query deshydratada con los datos ya montados
                $qb = $em->getRepository('WorkshopBundle:Workshop')
                    ->createQueryBuilder('w');

                $qb->select( 'w.id', 'w.code_workshop', 'w.internal_code', 'w.commercial_code', 'w.name', 'w.email_1', 'w.phone_number_1',
                        'w.active', 'w.created_at', 'w.modified_at', 'w.update_at', 'w.lowdate_at', 'w.ad_service_plus', 'w.test', 'w.haschecks',
                        'w.numchecks', 'w.infotech','sh.code_shop', 'sh.name as shop', 'p.name as partner', 'w.code_partner', 'cs.category_service', 't.name as tipology')
                    ->leftJoin('w.shop', 'sh')
                    ->leftJoin('w.partner', 'p')
                    ->leftJoin('w.category_service', 'cs')
                    ->leftJoin('w.typology', 't')
                    ->groupBy('w.id')
                    ->orderBy('w.id')
                    ->where($qb->expr()->notIn('w.id', $workshop_query));

                if (isset($from_date)) {

                    $qb = $qb->andWhere('w.update_at >= :update_at_from')
                        ->setParameter('update_at_from', $from_date);
                }

                if (isset($to_date)) {

                    $qb = $qb->andWhere('w.lowdate_at <= :lowdate_at_to')
                        ->setParameter('lowdate_at_to', $to_date);
                }

                if ($partner!= "0") {

                    $qb = $qb->andWhere('p.id = :partner')
                        ->setParameter('partner', $partner);
                }

                if ($shop != "0") {

                    $qb = $qb->andWhere('sh.id = :shop')
                        ->setParameter('shop', $shop);
                }

                switch ($status) {

                    case "active":
                        $qb = $qb->andWhere('w.active = 1')
                            ->andWhere('w.test != 1');
                        break;
                    case "deactive":
                        $qb = $qb->andWhere('w.active != 1');
                        breaK;
                    case "test":
                        $qb = $qb->andWhere('w.test = 1');
                        break;
                    case "adsplus":
                        $qb = $qb->andWhere('w.ad_service_plus = 1');
                        break;
                    case "check":
                        $qb = $qb->andWhere('w.haschecks = 1');
                        break;
                    case "infotech":
                        $qb = $qb->andWhere('w.infotech = 1');
                        break;
                }

                if(!$security->isGranted('ROLE_SUPER_ADMIN')){

                    $qb = $qb->andWhere('w.country = :country')
                        ->setParameter('country', $security->getToken()->getUser()->getCountry()->getId());
                }else{

                    if ($country != "0") {

                        $qb = $qb->andWhere('w.country = :country')
                            ->setParameter('country', $country);
                    }
                }
                if ($catserv != "0")
                {
                    $qb = $qb->andWhere('w.category_service = :catserv')
                        ->setParameter('catserv', $catserv);
                }
                if ($typology != "0") {

                    $qb = $qb->andWhere('t.id = :typology')
                        ->setParameter('typology', $typology);
                }

                $resultsDehydrated = $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

                $trans     = $this->get('translator');
                $informe   = UtilController::sinAcentos($trans->trans('statistic.no_ticket'));
                $response->headers->set('Content-Disposition', 'attachment;filename="'.$informe.'_'.date("dmY").'.xls"');
                $excel = $this->createExcelWorkshop($resultsDehydrated);
            }
            elseif ($type == 'numworkshopbypartner'){

                $trans     = $this->get('translator');
                $nTalleres = UtilController::sinAcentos($trans->trans('workshops'));
                $nSocio    = UtilController::sinAcentos($trans->trans('partner'));
                $informe   = UtilController::sinAcentos($trans->trans('numworkshopbypartner'));

                $select = "SELECT p.name as ".$nSocio.", count(e.id) as ".$nTalleres." FROM WorkshopBundle:Workshop e ";
                $where .= 'AND p.id = e.partner ';
                $join  = ' JOIN e.partner p ';

                if ($status != '0') {
                    switch ($status) {
                        case "active":
                            $where .= 'AND e.active = 1 ';
                            $where .= 'AND e.test != 1 ';
                            break;
                        case "deactive":
                            $where .= 'AND e.active = 0 ';
                            breaK;
                        case "test":
                            $where .= 'AND e.test = 1 ';
                            break;
                        case "adsplus":
                            $where .= 'AND e.ad_service_plus = 1 ';
                            break;
                        case "check":
                            $where .= 'AND e.haschecks = 1 ';
                            break;
                        case "infotech":
                            $where .= 'AND e.infotech = 1 ';
                            break;
                    }
                }
                if    ($partner != "0"     ) {  $where .= 'AND e.id != 0 ';
                                                $where .= 'AND p.id = '.$partner.' ';
                }
                if    ($shop != "0"        ) {  $join  = ' JOIN e.shop s ';
                                                $where .= 'AND s.id = e.shop ';
                                                $where .= 'AND s.id = '.$shop.' ';
                }
                if    ($typology != "0"    ) {  $join  .= ' JOIN e.typology tp ';
                                                $where .= 'AND tp.id = e.typology ';
                                                $where .= 'AND tp.id = '.$typology.' ';
                }
                if(!$security->isGranted('ROLE_SUPER_ADMIN')){
                    $where .= 'AND e.country = '.$security->getToken()->getUser()->getCountry()->getId().' ';
                }else{
                    if    ($country != "0"  ) { $where .= 'AND e.country = '.$country.' '; }
                }

                $sql = $select.$join." WHERE ".$where.' ';

                if(!$security->isGranted('ROLE_ADMIN')) $sql .= ' AND e.category_service = '.$security->getToken()->getUser()->getCategoryService()->getId().' ';

                elseif ($catserv != "0") $sql .= ' AND e.category_service = '.$catserv.' ';

                $sql .= ' GROUP BY p.id ORDER BY '.$nTalleres.' DESC ';
                $qt = $em->createQuery($sql);

                $results   = $qt->getResult();

                $response->headers->set('Content-Disposition', 'attachment;filename="'.$informe.'_'.date("dmY").'.xls"');
                $excel = $this->createExcelStatistics($results);
            }
            elseif ($type == 'ticketbyworkshopforpartner'){

                $trans     = $this->get('translator');
                $nTickets  = UtilController::sinAcentos($trans->trans('tickets'));
                $nTaller   = UtilController::sinAcentos($trans->trans('workshop'));
                $nSocio    = UtilController::sinAcentos($trans->trans('partner'));
                $code      = UtilController::sinAcentos($trans->trans('_code'));
                $informe   = UtilController::sinAcentos($trans->trans('ticketbyworkshopforpartner'));

                $select = "SELECT count(w.id) as ".$nTickets.", w.name as ".$nTaller.", p.name as ".$nSocio.", p.code_partner as ".$code.$nSocio.", w.code_workshop as ".$code.$nTaller." FROM TicketBundle:Ticket e JOIN e.workshop w ";
                $where .= 'AND p.id = w.partner ';
                $join  = ' JOIN w.partner p ';

                if ($status != '0') {
                    switch ($status) {
                        case "active":
                            $where .= 'AND w.active = 1 ';
                            $where .= 'AND w.test != 1 ';
                            break;
                        case "deactive":
                            $where .= 'AND w.active = 0 ';
                            breaK;
                        case "test":
                            $where .= 'AND w.test = 1 ';
                            break;
                        case "adsplus":
                            $where .= 'AND w.ad_service_plus = 1 ';
                            break;
                        case "check":
                            $where .= 'AND w.haschecks = 1 ';
                            break;
                        case "infotech":
                            $where .= 'AND w.infotech = 1 ';
                            break;
                    }
                }
                if    ($partner != "0"     ) {  $where .= 'AND w.id != 0 ';
                                                $where .= 'AND p.id = '.$partner.' ';
                }
                if    ($shop != "0"        ) {  $join  = ' JOIN w.shop s ';
                                                $where .= 'AND s.id = w.shop ';
                                                $where .= 'AND s.id = '.$shop.' ';
                }
                if    ($typology != "0"    ) {  $join  .= ' JOIN w.typology tp ';
                                                $where .= 'AND tp.id = w.typology ';
                                                $where .= 'AND tp.id = '.$typology.' ';
                }
                if(!$security->isGranted('ROLE_SUPER_ADMIN')){
                    $where .= 'AND w.country = '.$security->getToken()->getUser()->getCountry()->getId().' ';
                }else{
                    if    ($country != "0"  ) { $where .= 'AND w.country = '.$country.' '; }
                }
                $sql = $select.$join." WHERE ".$where.' ';
                if ($catserv != "0") $sql .= ' AND e.category_service = '.$catserv.' ';
                $sql .= ' GROUP BY w.id ORDER BY '.$nTickets.' DESC ';
                $qt = $em->createQuery($sql);
                $results   = $qt->getResult();

                $response->headers->set('Content-Disposition', 'attachment;filename="'.$informe.'_'.date("dmY").'.xls"');
                $excel = $this->createExcelStatistics($results);
            }
            elseif ($type == 'numticketsbypartner'){

                $trans     = $this->get('translator');
                $cSocio    = UtilController::sinAcentos($trans->trans('code'));
                $nTickets  = UtilController::sinAcentos($trans->trans('tickets'));
                $nSocio    = UtilController::sinAcentos($trans->trans('partner'));
                $informe   = UtilController::sinAcentos($trans->trans('numticketsbypartner'));

                $select = 'SELECT p.code_partner as '.$cSocio.', p.name as '.$nSocio.', count(w.id) as '.$nTickets.' FROM TicketBundle:Ticket e JOIN e.workshop w ';
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
                if    ($typology != "0"    ) {  $join  .= ' JOIN w.typology tp ';
                                                $where .= 'AND tp.id = w.typology ';
                                                $where .= 'AND tp.id = '.$typology.' ';
                }

                $sql = $select.$join." WHERE ".$where.' ';
                if ($catserv != "0") $sql .= ' AND e.category_service = '.$catserv.' ';
                $sql .= ' GROUP BY p.id ORDER BY '.$nTickets.' DESC ';
                $qt = $em->createQuery($sql);
                $results   = $qt->getResult();

                $response->headers->set('Content-Disposition', 'attachment;filename="'.$informe.'_'.date("dmY").'.xls"');
                $excel = $this->createExcelStatistics($results);
            }
            elseif ($type == 'numticketsbysystem'){

                $trans       = $this->get('translator');
                $nTickets    = UtilController::sinAcentos($trans->trans('tickets'));
                $nSistema    = UtilController::sinAcentos($trans->trans('system'));
                $nSubsistema = UtilController::sinAcentos($trans->trans('subsystem'));
                $informe     = UtilController::sinAcentos($trans->trans('numticketsbysystem'));

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
                if    ($typology != "0"    ) {  $join  .= ' JOIN w.typology tp ';
                                                $where .= 'AND tp.id = w.typology ';
                                                $where .= 'AND tp.id = '.$typology.' ';
                }
                if(!$security->isGranted('ROLE_SUPER_ADMIN')){
                    $where .= 'AND w.country = '.$security->getToken()->getUser()->getCountry()->getId().' ';
                }else{
                    if    ($country != "0"  ) { $where .= 'AND w.country = '.$country.' '; }
                }
                $select .= "FROM TicketBundle:Ticket e ";
                $sql = $select.$join." WHERE ".$where.' ';
                if ($catserv != "0") $sql .= ' AND e.category_service = '.$catserv.' ';
                $sql .= ' GROUP BY ss.id ORDER BY '.$nTickets.' DESC, s.name, ss.name ';
                $qt = $em->createQuery($sql);
                $results   = $qt->getResult();

                // Traducción al idioma del administrador

                $key = array_keys($results);
                $size = sizeof($key);
                for ($i=0; $i<$size; $i++){

                    $sistema    = $results[$key[$i]][$nSistema];
                    $subsistema = $results[$key[$i]][$nSubsistema];

                    $results[$key[$i]][$nSistema]    = UtilController::sinAcentos($trans->trans($sistema));
                    $results[$key[$i]][$nSubsistema] = UtilController::sinAcentos($trans->trans($subsistema));
                }

                $response->headers->set('Content-Disposition', 'attachment;filename="'.$informe.'_'.date("dmY").'.xls"');
                $excel = $this->createExcelStatistics($results);
            }
            elseif ($type == 'numticketsbybrand'){

                $trans     = $this->get('translator');
                $nTickets  = UtilController::sinAcentos($trans->trans('tickets'));
                $nMarca    = UtilController::sinAcentos($trans->trans('brand'));
                $informe   = UtilController::sinAcentos($trans->trans('numticketsbybrand'));

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
                if    ($typology != "0"    ) {  $join  .= ' JOIN w.typology tp ';
                                                $where .= 'AND tp.id = w.typology ';
                                                $where .= 'AND tp.id = '.$typology.' ';
                }
                if(!$security->isGranted('ROLE_SUPER_ADMIN')){
                    $where .= 'AND w.country = '.$security->getToken()->getUser()->getCountry()->getId().' ';
                }else{
                    if    ($country != "0"  ) { $where .= 'AND w.country = '.$country.' '; }
                }

                $select .= "FROM TicketBundle:Ticket e ";
                $sql = $select.$join." WHERE ".$where.' ';
                if ($catserv != "0") $sql .= ' AND e.category_service = '.$catserv.' ';
                $sql .= ' GROUP BY b.id ORDER BY '.$nTickets.' DESC, b.name ';
                $qt = $em->createQuery($sql);
                $results   = $qt->getResult();

                $response->headers->set('Content-Disposition', 'attachment;filename="'.$informe.'_'.date("dmY").'.xls"');
                $excel = $this->createExcelStatistics($results);
            }
            elseif ($type == 'numticketsbymodel'){

                $trans     = $this->get('translator');
                $nTickets  = UtilController::sinAcentos($trans->trans('tickets'));
                $nMarca    = UtilController::sinAcentos($trans->trans('brand'));
                $nModelo   = UtilController::sinAcentos($trans->trans('model'));
                $informe   = UtilController::sinAcentos($trans->trans('numticketsbymodel'));

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
                if    ($typology != "0"    ) {  $join  .= ' JOIN w.typology tp ';
                                                $where .= 'AND tp.id = w.typology ';
                                                $where .= 'AND tp.id = '.$typology.' ';
                }
                if(!$security->isGranted('ROLE_SUPER_ADMIN')){
                    $where .= 'AND w.country = '.$security->getToken()->getUser()->getCountry()->getId().' ';
                }else{
                    if    ($country != "0"  ) { $where .= 'AND w.country = '.$country.' '; }
                }

                $select .= "FROM TicketBundle:Ticket e ";
                $sql = $select.$join." WHERE ".$where.' ';
                if ($catserv != "0") $sql .= ' AND e.category_service = '.$catserv.' ';
                $sql .= ' GROUP BY m.id ORDER BY '.$nTickets.' DESC, m.name ';
                $qt = $em->createQuery($sql);
                $results   = $qt->getResult();

                $response->headers->set('Content-Disposition', 'attachment;filename="'.$informe.'_'.date("dmY").'.xls"');
                $excel = $this->createExcelStatistics($results);
            }
            elseif ($type == 'numticketsbyfabyear'){

                $locale = $this->getRequest()->getLocale();

                $trans     = $this->get('translator');

                if ($locale == 'es') $year = 'Year';
                else $year =  UtilController::sinAcentos($trans->trans('year'));
                $locale = $this->getRequest()->getLocale();

                $informe   = UtilController::sinAcentos($trans->trans('numticketsbyfabyear'));
                $nTickets  = UtilController::sinAcentos($trans->trans('tickets'));

                $select = "SELECT v.inicio as ".$year.", count(e.id) as ".$nTickets." ";
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
                if    ($typology != "0"    ) {  $join  .= ' JOIN w.typology tp ';
                                                $where .= 'AND tp.id = w.typology ';
                                                $where .= 'AND tp.id = '.$typology.' ';
                }
                if(!$security->isGranted('ROLE_SUPER_ADMIN')){
                    $where .= 'AND w.country = '.$security->getToken()->getUser()->getCountry()->getId().' ';
                }else{
                    if    ($country != "0"  ) { $where .= 'AND w.country = '.$country.' '; }
                }

                $select .= "FROM TicketBundle:Ticket e ";
                $sql = $select.$join." WHERE ".$where.' ';
                if ($catserv != "0") $sql .= ' AND e.category_service = '.$catserv.' ';
                $sql .= ' GROUP BY v.inicio ORDER BY v.inicio DESC, '.$nTickets.' DESC ';
                $qt = $em->createQuery($sql);
                $results   = $qt->getResult();

                $years = array();
                foreach ($results as $res) {

                    $inicio = substr($res[$year], 0, 4);
                    if(!isset($years[$inicio])) {
                        $years[$inicio][$year] = $inicio;
                        $years[$inicio][$nTickets] = $res[$nTickets];
                    }
                    else $years[$inicio][$nTickets] = $years[$inicio][$nTickets] + $res[$nTickets];
                }

                $response->headers->set('Content-Disposition', 'attachment;filename="'.$informe.'_'.date("dmY").'.xls"');
                $excel = $this->createExcelFabYear($years);
            }
            elseif ($type == 'numticketsbymonth'){

                $trans     = $this->get('translator');
                $nTickets  = UtilController::sinAcentos($trans->trans('tickets'));
                $nTaller   = UtilController::sinAcentos($trans->trans('workshop'));
                $nSocio    = UtilController::sinAcentos($trans->trans('partner'));
                $code      = UtilController::sinAcentos($trans->trans('_code'));
                $date      = UtilController::sinAcentos($trans->trans('date'));
                $informe   = UtilController::sinAcentos($trans->trans('numticketsbymonth'));

                $select = "SELECT p.code_partner as ".$code.$nSocio.", w.code_workshop as ".$code.$nTaller.", p.name as ".$nSocio.", w.name as ".$nTaller.", e.created_at as ".$date." FROM TicketBundle:Ticket e JOIN e.workshop w ";
                $where .= 'AND p.id = w.partner ';
                $join  = ' JOIN w.partner p ';

                if ($status != '0') {
                    switch ($status) {
                        case "active":
                            $where .= 'AND w.active = 1 ';
                            $where .= 'AND w.test != 1 ';
                            break;
                        case "deactive":
                            $where .= 'AND w.active = 0 ';
                            breaK;
                        case "test":
                            $where .= 'AND w.test = 1 ';
                            break;
                        case "adsplus":
                            $where .= 'AND w.ad_service_plus = 1 ';
                            break;
                        case "check":
                            $where .= 'AND w.haschecks = 1 ';
                            break;
                        case "infotech":
                            $where .= 'AND w.infotech = 1 ';
                            break;
                    }
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
                if    ($partner != "0"     ) {  $where .= 'AND w.id != 0 ';
                                                $where .= 'AND p.id = '.$partner.' ';
                }
                if    ($shop != "0"        ) {  $join  = ' JOIN w.shop s ';
                                                $where .= 'AND s.id = w.shop ';
                                                $where .= 'AND s.id = '.$shop.' ';
                }
                if    ($typology != "0"    ) {  $join  .= ' JOIN w.typology tp ';
                                                $where .= 'AND tp.id = w.typology ';
                                                $where .= 'AND tp.id = '.$typology.' ';
                }
                if(!$security->isGranted('ROLE_SUPER_ADMIN')){
                    $where .= 'AND w.country = '.$security->getToken()->getUser()->getCountry()->getId().' ';
                }else{
                    if    ($country != "0"  ) { $where .= 'AND w.country = '.$country.' '; }
                }

                $select .= "FROM TicketBundle:Ticket e ";
                $sql = $select.$join." WHERE ".$where.' ';
                if ($catserv != "0") $sql .= ' AND e.category_service = '.$catserv.' ';
                $sql .= ' ORDER BY w.id, e.created_at ';
                $qt = $em->createQuery($sql);
                $results   = $qt->getResult();

                $queryF = "SELECT e.created_at as ".$date." FROM TicketBundle:Ticket e ";
                if ($catserv != "0") $sql .= ' WHERE e.category_service = '.$catserv.' ';
                $queryF .= " ORDER BY e.created_at";
                $qF = $em->createQuery($queryF);
                $resultsF = $qF->getResult();

                $response->headers->set('Content-Disposition', 'attachment;filename="'.$informe.'_'.date("dmY").'.xls"');
                $excel = $this->createExcelByMonth($results, $resultsF);
            }
            elseif ($type == 'undefined' AND !$security->isGranted('ROLE_ADMIN'))
            {
                $trans  = $this->get('translator');
                $catserv = $security->getToken()->getUser()->getCategoryService();

                if($catserv->getId() != null) $catserv = $catserv->getId();
                else $catserv = 0;

                $code            = UtilController::sinAcentos(str_ireplace(" ", "", $trans->trans('_code')));
                $nTickets        = UtilController::sinAcentos(str_ireplace(" ", "", $trans->trans('tickets')));
                $nTaller         = UtilController::sinAcentos(str_ireplace(" ", "", $trans->trans('workshop')));
                $nSocio          = UtilController::sinAcentos(str_ireplace(" ", "", $trans->trans('partner')));
                $nShop           = UtilController::sinAcentos(str_ireplace(" ", "", $trans->trans('shop')));
                $nTypology       = UtilController::sinAcentos(str_ireplace(array(" ", "'"), array("", ""), $trans->trans('typology')));
                $nCountry        = UtilController::sinAcentos(str_ireplace(" ", "", $trans->trans('country')));
                $contact         = UtilController::sinAcentos(str_ireplace(" ", "", $trans->trans('contact')));
                $internal_code   = UtilController::sinAcentos(str_ireplace(" ", "", $trans->trans('internal_code')));
                $commercial_code = UtilController::sinAcentos(str_ireplace(" ", "", $trans->trans('commercial_code')));
                $update_at       = UtilController::sinAcentos(str_ireplace(" ", "", $trans->trans('subscribed')));
                $lowdate_at      = UtilController::sinAcentos(str_ireplace(" ", "", $trans->trans('unsubscribed')));
                $region          = UtilController::sinAcentos(str_ireplace(" ", "", $trans->trans('region')));
                $city            = UtilController::sinAcentos(str_ireplace(" ", "", $trans->trans('city')));
                $address         = UtilController::sinAcentos(str_ireplace(" ", "", $trans->trans('address')));
                $postal_code     = UtilController::sinAcentos(str_ireplace(" ", "", $trans->trans('postal_code')));
                $phone_number_1  = UtilController::sinAcentos(str_ireplace(array(" ", "."), array("", ""), $trans->trans('phone_number_1')));
                $fax             = UtilController::sinAcentos(str_ireplace(" ", "", $trans->trans('fax')));
                $email_1         = UtilController::sinAcentos(str_ireplace(array(" ", "-"), array("", ""), $trans->trans('email_1')));
                $informe         = UtilController::sinAcentos(str_ireplace(" ", "", $trans->trans('ticketbyworkshop')));

                if($shop    == 'undefined') $shop = '0';
                if($country == 'undefined') $country = '0';
                if($raport  == 'undefined') $raport = '0';

                //Realizamos una query deshydratada con los datos ya montados
                $qb = $em->getRepository('TicketBundle:Ticket')
                    ->createQueryBuilder('t')
                    ->select('e.name as '.$nTaller.'', 'p.name as '.$nSocio.'', 'p.code_partner as '.$code.$nSocio.'', 'e.code_workshop as '.$code.$nTaller.'',
                                    'tp.name as '.$nTypology.'', 'c.country as '.$nCountry.'', 'e.contact as '.$contact.'', 'e.internal_code as '.$internal_code.'',
                                    'e.commercial_code as '.$commercial_code.'', 'e.update_at as '.$update_at.'', 'e.lowdate_at as '.$lowdate_at.'', 'e.region as '.$region.'',
                                    'e.city as '.$city.'', 'e.address as '.$address.'', 'e.postal_code as '.$postal_code.'', 'e.phone_number_1 as '.$phone_number_1.'',
                                    'e.fax as '.$fax.'', 'e.email_1 as '.$email_1.'', 'count(t.id) as '.$nTickets.'')

                    ->leftJoin('t.workshop', 'e')
                    ->leftJoin('e.partner', 'p')
                    ->leftJoin('e.typology', 'tp')
                    ->leftJoin('e.country', 'c')

                    ->andWhere('p.id = e.partner')
                    ->andWhere('tp.id = e.typology')
                    ->andWhere('c.id = e.country')

                    ->groupBy('e.id')
                    ->orderBy('e.id');

                if ($shop != "0" and $shop != "undefined") {

                  $qb = $qb->addSelect('s.name as '.$nShop.'')
                           ->leftJoin('e.shop', 's')
                           ->andWhere('s.id = :shop')
                           ->setParameter('shop', $shop);
                }
                if($partner != "0") $qb = $qb->andWhere('p.id = :partner')
                                             ->setParameter('partner', $partner);

                if($typology != "0") $qb = $qb->andWhere('tp.id = :typology')
                                             ->setParameter('typology', $typology);

                if($catserv != "0") $qb = $qb->andWhere('e.category_service = :catserv')
                                             ->setParameter('catserv', $catserv);

                if($country != "0") $qb = $qb->andWhere('e.country = :country')->setParameter('country', $country);

                if (isset($to_date)) $to_date = $to_y.'-'.$to_m.'-'.$to_d.' 00:00:00';

                if ($status != '0') {
                    switch ($status) {
                        case "active":

                            // $qb = $qb->andWhere("e.created_at <= '2016-08-15 23:59:59'")
                            //          ->andWhere("(e.active = 1 AND (e.lowdate_at IS NULL OR e.modified_at > e.lowdate_at)) OR ( e.active = 0 AND e.lowdate_at  >= '2016-08-15 23:59:59' )");

                            if(!isset($from_date) and !isset($to_date))
                            {
                                $qb = $qb->andWhere('e.active = 1')
                                         ->andWhere('e.test != 1');
                            }
                            elseif (isset($from_date) and isset($to_date)){

                                $qb = $qb->andWhere('e.update_at <= :update_at_to')
                                         ->andWhere('(e.endtest_at IS NULL OR e.endtest_at >= :endtest_at_from OR e.endtest_at >= :endtest_at_to)')
                                         ->andWhere('(e.lowdate_at IS NULL OR e.lowdate_at <= e.update_at OR e.lowdate_at >= :lowdate_at_from)')
                                         ->setParameter('update_at_to', $to_date)
                                         ->setParameter('endtest_at_from', $from_date)
                                         ->setParameter('endtest_at_to', $to_date)
                                         ->setParameter('lowdate_at_from', $from_date);
                            }
                            else{
                                if (isset($from_date))
                                {
                                    $qb = $qb->andWhere('e.update_at >= :update_at_from')
                                             ->andWhere('(e.endtest_at IS NULL OR e.endtest_at >= :endtest_at_from)')
                                             ->andWhere('(e.lowdate_at IS NULL OR e.lowdate_at <= e.update_at)')
                                             ->setParameter('update_at_from', $from_date)
                                             ->setParameter('endtest_at_from', $from_date);
                                }

                                if (isset($to_date))
                                {
                                    $qb = $qb->andWhere('e.update_at <= :update_at_to')
                                             ->andWhere('(e.endtest_at IS NULL OR e.endtest_at >= :endtest_at_to)')
                                             ->andWhere('(e.lowdate_at IS NULL OR e.lowdate_at >= :lowdate_at_to)')
                                             ->setParameter('update_at_to', $to_date)
                                             ->setParameter('endtest_at_to', $to_date)
                                             ->setParameter('lowdate_at_to', $to_date);
                                }
                            }
                            break;

                        case "deactive":
                            if(!isset($from_date) and !isset($to_date))
                            {
                                $qb = $qb->andWhere('e.active != 1');
                            }
                            else
                            {
                                if (isset($from_date))
                                {
                                    $qb = $qb->andWhere('e.lowdate_at >= :lowdate_at_from')
                                             ->setParameter('lowdate_at_from', $from_date);
                                }
                                if (isset($to_date))
                                {
                                    $qb = $qb->andWhere('e.lowdate_at <= :lowdate_at')
                                             ->setParameter('lowdate_at', $to_date);
                                }
                            }
                            break;

                        case "test":

                            if(!isset($from_date) and !isset($to_date))
                            {
                                $qb = $qb->andWhere('e.test = 1');
                            }
                            else
                            {
                                if (isset($from_date))
                                {
                                    $qb = $qb->andWhere('e.endtest_at >= :endtest_at_from')
                                             ->setParameter('endtest_at_from', $from_date);
                                }

                                if (isset($to_date))
                                {
                                    $qb = $qb->andWhere('e.endtest_at >= :endtest_at_to')
                                             ->setParameter('endtest_at_to', $to_date);
                                }
                            }
                            break;

                        case "adsplus":
                            $qb = $qb->andWhere('e.ad_service_plus = 1');
                            break;

                        case "check":
                            $qb = $qb->andWhere('e.haschecks = 1');
                            break;

                        case "infotech":
                            $qb = $qb->andWhere('e.infotech = 1');
                            break;

                        default:

                            if (isset($from_date))
                            {
                                $qb = $qb->andWhere('e.created_at >= :created_at_from')
                                         ->setParameter('created_at_from', $from_date);
                            }

                            if (isset($to_date))
                            {
                                $qb = $qb->andWhere('e.created_at <= :created_at_to')
                                         ->setParameter('created_at_to', $to_date);
                            }
                            break;
                    }
                }

                // echo($qb->getQuery()->getSql());
                $resultsDehydrated = $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

                $response->headers->set('Content-Disposition', 'attachment;filename="'.$informe.'_'.date("dmY").'.xls"');
                $excel = $this->createExcelStatistics($resultsDehydrated);
            }
        }
        else{
            /*
                SELECT w.id, t.id FROM ticket t JOIN  workshop w ON t.workshop_id = w.id GROUP BY w.id ORDER BY t.id DESC
            */
            $where .= 'AND e.workshop = w.id ';

            if    ($typology != "0"    ) {  $join  = ' JOIN w.typology tp ';
                                            $where .= 'AND tp.id = w.typology ';
                                            $where .= 'AND tp.id = '.$typology.' ';
            }
            if($security->isGranted('ROLE_SUPER_ADMIN')){
                if    ($country != '0'     ) { $where .= 'AND e.country = '.$country.' '; }
            }else{
                $where .= 'AND w.country = '.$security->getToken()->getUser()->getCountry()->getId().' ';
            }


            $sql = "SELECT MAX(e.id) as t_id, w.id as w_id FROM TicketBundle:Ticket e JOIN e.workshop w ".$join." WHERE ".$where;
            if ($catserv != "0") $sql .= ' AND e.category_service = '.$catserv.' ';
            $sql .= 'GROUP BY w.id ORDER BY e.id DESC ';
            $qid = $em->createQuery($sql);
            $resultsid   = $qid->getResult();

            $ids = '0';
            foreach ($resultsid as $rid) {
                $ids .= ', '.$rid['t_id'];
            }

            $sql = "SELECT partial e.{ id, description, solution, created_at }, partial w.{ id, code_partner, code_workshop, name } FROM TicketBundle:Ticket e JOIN e.workshop w ".$join." WHERE e.id IN (".$ids.") ";
            if ($catserv != "0") $sql .= ' AND e.category_service = '.$catserv.' ';
            $qt = $em->createQuery($sql);

            $results   = $qt->getResult();

            $trans     = $this->get('translator');
            $informe   = UtilController::sinAcentos($trans->trans('statistic.last_tickets' ));
            $response->headers->set('Content-Disposition', 'attachment;filename="'.$informe.'_'.date("dmY").'.xls"');
            $excel = $this->createExcelLastTickets($results);

        }

        $excel = UtilController::sinAcentos($excel);
        $response->setContent($excel);
        return $response;
    }

    public function createExcelTicket($results){

        $locale = $this->getRequest()->getLocale();
        $trans = $this->get('translator');
        //Creación de cabecera
        $excel =
            $trans->trans('ticket').';'.
            $trans->trans('code_partner').';'.
            $trans->trans('code_shop').';'.
            $trans->trans('code_workshop').';'.
            $trans->trans('internal_code').';'.
            $trans->trans('name').';'.
            $trans->trans('category_service').';'.
            $trans->trans('partner').';'.
            $trans->trans('shop').';'.
            $trans->trans('region').';'.
            $trans->trans('typology').';'.
            $trans->trans('brand').';'.
            $trans->trans('model').';'.
            $trans->trans('version').';';

            if ($locale == 'es') $excel .= 'Year'.';';
            else $excel .= $trans->trans('year').';';

        $excel .=
            $trans->trans('vin').';'.
            $trans->trans('motor').';'.
            $trans->trans('system').';'.
            $trans->trans('subsystem').';'.
            $trans->trans('description').';'.
            $trans->trans('solution').';'.
            $trans->trans('status').';'.
            $trans->trans('date').';'.
            $trans->trans('assessor').';'.
            $trans->trans('importance').';';
        $excel.="\n";

        foreach ($results as $row) {

            //Recorremos el array deshidratado, revisando casos que esten vacios
            $excel.=$row['id'].';';

            if(isset($row['codePartner'])) $excel.=$row['codePartner'].';';
            else $excel.=' ;';
            if(isset($row['codeShop'])) $excel.=$row['codeShop'].';';
            else $excel.=' ;';
            if(isset($row['codeWorkshop'])) $excel.=$row['codeWorkshop'].';';
            else $excel.=' ;';
            if(isset($row['internalCodeWorkshop'])) $excel.=$row['internalCodeWorkshop'].';';
            else $excel.=' ;';
            if(isset($row['nameWorkshop'])) $excel.=$row['nameWorkshop'].';';
            else $excel.=' ;';
            if(isset($row['categoryService'])) $excel.=$row['categoryService'].';';
            else $excel.=' ;';
            if(isset($row['namePartner'])) $excel.=$row['namePartner'].';';
            if(isset($row['nameShop'])) $excel.=$row['nameShop'].';';
            else $excel.=' ;';

            if(isset($row['regionWorkshop'])) $excel.=$row['regionWorkshop'].';';
            else $excel.='-;';

            if(isset($row['typologyWorkshop'])) $typology = $row['typologyWorkshop'];
            else $typology = '';
            $excel.=$this->get('translator')->trans($typology).';';


            if(isset($row['brandCar'])) $excel.=$row['brandCar'].';';
            else $excel.=' ;';

            if(isset($row['modelCar'])) $excel.=$row['modelCar'].';';
            else $excel.=' ;';

            if(isset($row['versionCar'])) $excel.=$row['versionCar'].';';
            else $excel.=' ;';

            if(isset($row['yearCar'])) $excel.=$row['yearCar'].';';
            else $excel.=' ;';

            if(isset($row['vinCar'])) $excel.=$row['vinCar'].';';
            else $excel.=' ;';

            if(isset($row['motorCar'])) $excel.=$row['motorCar'].';';
            else $excel.=' ;';

            if(isset($row['system'])) $excel.=$row['system'].';';
            else $excel.=' ;';
            if(isset($row['subsystem'])) $excel.=$row['subsystem'].';';
            else $excel.=' ;';

            if(isset($row['description'])) $description = $row['description'];
            else $description = '';
            $buscar = array('"', chr(13).chr(10), "\r\n", "\n", "\r");
            $reemplazar = array("", "", "", "");
            $description = str_ireplace($buscar,$reemplazar,$description);
            $excel.=$description.';';

            if(isset($row['solution'])) $solution = $row['solution'];
            else $solution='';
            $buscar = array('"', chr(13).chr(10), "\r\n", "\n", "\r");
            $reemplazar = array("", "", "", "");
            $solution = str_ireplace($buscar,$reemplazar,$solution);
            $excel.= $solution.';';

            if(isset($row['status'])) $status = $row['status'];
            else $status = ' ';
            $excel.=$this->get('translator')->trans($status).';';
            if(isset($status)) unset($status);

            if(isset($row['created_at'])) $created = $row['created_at']->format("d/m/Y");
            else $created = '';
            $excel.=$created.';';

            if(isset($row['assignedTo'])) $excel.=$row['assignedTo'].';';
            else $excel.=' ;';

            if(isset($row['importance'])) $importance = $row['importance'];
            else $importance = ' ';
            $excel.=$this->get('translator')->trans($importance).';';
            if(isset($importance)) unset($importance);

            $excel.="\n";
        }
        $excel = nl2br($excel);
        $excel = str_replace('<br />', '.', $excel);
        $excel = str_replace(',', '.', $excel);

        return($excel);
    }

    public function createExcelWorkshop($results){
        $trans = $this->get('translator');
        //Creación de cabecera
        $excel =$trans->trans('code_partner').';'.
                $trans->trans('code_shop').';'.
                $trans->trans('code_workshop').';'.
                $trans->trans('internal_code').';'.
                $trans->trans('commercial_code').';'.
                $trans->trans('name').';'.
                $trans->trans('category_service').';'.
                $trans->trans('partner').';'.
                $trans->trans('shop').';'.
                $trans->trans('typology').';'.
                $trans->trans('email').';'.
                $trans->trans('tel').';'.
                $trans->trans('active').';'.
                $trans->trans('created_at').';'.
                $trans->trans('modified_at').';'.
                $trans->trans('subscribed').';'.
                $trans->trans('unsubscribed').';'.
                $trans->trans('testing').';'.
                $trans->trans('adsplus').';'.
                $trans->trans('haschecks').';'.
                $trans->trans('numchecks').';'.
                $trans->trans('infotech').';';

        $excel.="\n";

        foreach ($results as $row) {

            if(isset($row['code_partner'])) $excel.=$row['code_partner'].';';
            else $excel.=' ;';

            if(isset($row['code_shop'])) $excel.=$row['code_shop'].';';
            else $excel.=' ;';

            if(isset($row['code_workshop'])) $excel.=$row['code_workshop'].';';
            else $excel.=' ;';

            if(isset($row['internal_code'])) $excel.=$row['internal_code'].';';
            else $excel.=' ;';

            if(isset($row['commercial_code'])) $excel.=$row['commercial_code'].';';
            else $excel.=' ;';

            if(isset($row['name'])) $name = $row['name'];
            else $name = '';
            $buscar = array('"',';', chr(13).chr(10), "\r\n", "\n", "\r");
            $reemplazar = array("", "", "", "");
            $name = str_ireplace($buscar,$reemplazar,$name);

            // Problema con caracteres especiales
            $buscar=array("ª", "´", "·");
            $reemplazar=array("a", "'", ".");
            $name=str_ireplace($buscar,$reemplazar,$name);

            $excel.=$name.';';

            if(isset($row['category_service'])) $excel.=$row['category_service'].';';
            else $excel.=' ;';

            if(isset($row['partner'])) $excel.=$row['partner'].';';
            else $excel.=' ;';

            if(isset($row['shop'])) $shop = $row['shop'];

            if(isset($shop)) {
                $buscar = array('"',';', chr(13).chr(10), "\r\n", "\n", "\r");
                $reemplazar = array("", "", "", "");
                $shop = str_ireplace($buscar, $reemplazar, $shop);
            }
            else $shop = '-';
            $excel.= $shop.';';

            if(isset($row['tipology'])) $excel.=$row['tipology'].';';
            else $excel.=' ;';

            if(isset($row['email_1'])) $excel.=$row['email_1'].';';
            else $excel.=' ;';

            if(isset($row['phone_number_1'])) $excel.=$row['phone_number_1'].';';
            else $excel.=' ;';

            if(isset($row['active']) && $row['active'] == 0) $active = $this->get('translator')->trans('no');
            else $active = $this->get('translator')->trans('yes');
            $excel.=strtoupper($active).';';

            if(isset($row['created_at']) && $row['created_at'] != NULL) $created = $row['created_at']->format("d-m-Y");
            else $created = '--';
            $excel.=strtoupper($created).';';

            if(isset($row['modified_at']) && $row['modified_at'] != NULL) $modified = $row['modified_at']->format("d-m-Y");
            else $modified = '--';
            $excel.=strtoupper($modified).';';

            if(isset($row['update_at']) && $row['update_at'] != NULL) $update = $row['update_at']->format("d-m-Y");
            else $update = '--';
            $excel.=strtoupper($update).';';

            if(isset($row['lowdate_at']) && $row['lowdate_at'] != NULL) $lowdated= $row['lowdate_at']->format("d-m-Y");
            else $lowdated = '--';
            $excel.=strtoupper($lowdated).';';

            if(isset($row['test']) && $row['test'] == 1) $test = $this->get('translator')->trans('yes');
            else $test = '';
            $excel.=strtoupper($test).';';

            if(isset($row['ad_service_plus']) && $row['ad_service_plus'] == 1) $adsplus = $this->get('translator')->trans('yes');
            else $adsplus = '';
            $excel.=strtoupper($adsplus).';';

            if(isset($row['has_checks']) && $row['has_checks'] == 1) $hasChecks = $this->get('translator')->trans('yes');
            else $hasChecks = '';
            $excel.=strtoupper($hasChecks).';';

            if(isset($row['num_checks'])) $excel.= $row['num_checks'].';';
            else $excel.= '0;';

            if(isset($row['infotech']) && $row['infotech'] == 1) $infotech = $this->get('translator')->trans('yes');
            else $infotech = '';
            $excel.=strtoupper($infotech).';';

            $excel.="\n";
        }

        $excel = nl2br($excel);
        $excel = str_replace('<br />', '.', $excel);
        $excel = str_replace(',', '.', $excel);
        return($excel);
    }

    public function createExcelLastTickets($results){
        $trans = $this->get('translator');
        //Creación de cabecera
        $excel =$trans->trans('code_partner').';'.
                $trans->trans('code_shop').';'.
                $trans->trans('code_workshop').';'.
                $trans->trans('internal_code').';'.
                $trans->trans('commercial_code').';'.
                $trans->trans('name').';'.
                $trans->trans('category_service').';'.
                $trans->trans('partner').';'.
                $trans->trans('shop').';'.
                $trans->trans('ticket').';'.
                $trans->trans('description').';'.
                $trans->trans('status').';'.
                $trans->trans('solution').';'.
                $trans->trans('date').';';
        $excel.="\n";

        $em = $this->getDoctrine()->getEntityManager();

        foreach ($results as $row) {

            $excel.=$row->getWorkshop()->getCodePartner().';';

            $shop = $row->getWorkshop()->getShop();
            if(isset($shop)) $code_shop = $shop->getCodeShop();
            else $code_shop = '-';
            $excel.=$code_shop.';';

            $excel.=$row->getWorkshop()->getCodeWorkshop().';';
            $excel.=$row->getWorkshop()->getInternalCode().';';
            $excel.=$row->getWorkshop()->getCommercialCode().';';

            $buscar=array('"',';', chr(13).chr(10), "\r\n", "\n", "\r");
            $reemplazar=array("", "", "", "");
            $name=str_ireplace($buscar,$reemplazar,$row->getWorkshop()->getName());
            $excel.=$name.';';

            $excel.=$row->getWorkshop()->getCategoryService().';';
            $excel.=$row->getWorkshop()->getPartner().';';

            if(isset($shop)) {
                $name_shop = $shop->getName();
                $buscar=array('"',';', chr(13).chr(10), "\r\n", "\n", "\r");
                $reemplazar=array("", "", "", "");
                $name_shop=str_ireplace($buscar,$reemplazar,$name_shop);
            }
            else $name_shop = '-';
            $excel.=$name_shop.';';

            $excel.=$row->getId().';';

            $buscar=array('"', chr(13).chr(10), "\r\n", "\n", "\r");
            $reemplazar=array("", "", "", "");
            $description=str_ireplace($buscar,$reemplazar,$row->getDescription());
            $excel.=$description.';';

            $excel.=$row->getStatus().';';

            $buscar=array('"', chr(13).chr(10), "\r\n", "\n", "\r");
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

                    if ($value instanceof \DateTime) { $value = $value->format('Y-m-d H:i:s'); }

                    $buscar=array('"', ',', ';', chr(13).chr(10), "\r\n", "\n", "\r");
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

        $locale = $this->getRequest()->getLocale();
        $trans  = $this->get('translator');

        $nTickets = UtilController::sinAcentos($trans->trans('tickets'));

        if ($locale == 'es') $year = 'Year';
        else $year =  UtilController::sinAcentos($trans->trans('year'));

        $excel.=$year.';'.$nTickets.';';

        foreach ($results as $res)
        {
            foreach ($res as $key => $value)
            {
                if($key == $year) $excel.="\n";
                $buscar=array('"', ',', ';', chr(13).chr(10), "\r\n", "\n", "\r");
                $reemplazar=array("", "", "", "");
                $text=str_ireplace($buscar,$reemplazar,$value);
                $excel.=$text.';';
            }
        }
        return($excel);
    }

    public function createExcelByMonth($results, $resultsF){
        $excel = '';

        $locale = $this->getRequest()->getLocale();
        $trans  = $this->get('translator');

        $nTickets  = UtilController::sinAcentos($trans->trans('tickets'));
        $nTaller   = UtilController::sinAcentos($trans->trans('workshop'));
        $nSocio    = UtilController::sinAcentos($trans->trans('partner'));
        $code      = UtilController::sinAcentos($trans->trans('_code'));
        $date      = UtilController::sinAcentos($trans->trans('date'));

        $excel.= $code." ".$nSocio.";".$code." ".$nTaller.";".$nSocio.";".$nTaller.";";

        $arrayF = array();
        $fechas = array();
        // array para el rango de fechas de los datos
        foreach ($resultsF as $resF)
        {
            $fecha = $resF[$date]->format('m-y');
            $arrayF[$fecha] = $fecha;
        }
        foreach ($arrayF as $fecha) {
            $excel.= $fecha.';';
            $fechas[$fecha] = 0;
        }
        $excel.="\n";

        $cSocio = 0;
        $cTaller = 0;
        $month = 0;
        $year = 0;
        $num = 0;
        $str = "";
        $array = array();

        // array de datos del taller con numero de tickets por mes
        foreach ($results as $res)
        {
            if($cSocio == $res[$code.$nSocio] and $cTaller == $res[$code.$nTaller]){

                $fecha = $res[$date];
                if($month == $fecha->format('m') and $year == $fecha->format('y')){
                    $num++;
                    $array[$cSocio.'-'.$cTaller]['fecha'][$month.'-'.$year] = $num;
                }else{
                    $array[$cSocio.'-'.$cTaller]['fecha'][$month.'-'.$year] = $num;
                    $num = 1;
                    $month  = $fecha->format('m');
                    $year  = $fecha->format('y');
                    $array[$cSocio.'-'.$cTaller]['fecha'][$month.'-'.$year] = $num;
                }
            }else{

                if($num != 0) {
                    // Añadimos el último resultado del taller
                    $array[$cSocio.'-'.$cTaller]['fecha'][$month.'-'.$year] = $num;

                    // Añadimos el nuevo resultado del taller
                    $cSocio  = $res[$code.$nSocio];
                    $cTaller = $res[$code.$nTaller];
                    $Socio = $res[$nSocio];
                    $Taller = $res[$nTaller];

                    $buscar=array('"', ',', ';', chr(13).chr(10), "\r\n", "\n", "\r");
                    $reemplazar=array("", "", "", "");
                    $Socio=str_ireplace($buscar,$reemplazar,$Socio);
                    $Taller=str_ireplace($buscar,$reemplazar,$Taller);

                    $fecha = $res[$date];
                    $month  = $fecha->format('m');
                    $year  = $fecha->format('y');
                    $num = 1;
                    $str = $cSocio.';'.$cTaller.';'.$Socio.';'.$Taller.';';
                    $array[$cSocio.'-'.$cTaller] = array('fecha' => $fechas, 'str' => $str);
                    $array[$cSocio.'-'.$cTaller]['fecha'][$month.'-'.$year] = $num;
                }
                else {
                    $cSocio  = $res[$code.$nSocio];
                    $cTaller = $res[$code.$nTaller];
                    $Socio = $res[$nSocio];
                    $Taller = $res[$nTaller];

                    $buscar=array('"', ',', ';', chr(13).chr(10), "\r\n", "\n", "\r");
                    $reemplazar=array("", "", "", "");
                    $Socio=str_ireplace($buscar,$reemplazar,$Socio);
                    $Taller=str_ireplace($buscar,$reemplazar,$Taller);

                    $fecha = $res[$date];
                    $month  = $fecha->format('m');
                    $year  = $fecha->format('y');

                    $num = 1;
                    $str = $cSocio.';'.$cTaller.';'.$Socio.';'.$Taller.';';
                    $array[$cSocio.'-'.$cTaller] = array('fecha' => $fechas, 'str' => $str);
                    $array[$cSocio.'-'.$cTaller]['fecha'][$month.'-'.$year] = $num;
                }
            }
        }

        // Recorro el array para generar el contenido del excel
        foreach ($array as $value) {
            $excel .= $value['str'];

            foreach ($value['fecha'] as $valueF) {
                $excel .= $valueF.';';
            }
            $excel.="\n";
        }
        return($excel);
    }
}