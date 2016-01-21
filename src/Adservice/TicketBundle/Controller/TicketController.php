<?php
namespace Adservice\TicketBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Adservice\TicketBundle\Entity\Ticket;
use Adservice\TicketBundle\Entity\TicketRepository;
use Adservice\TicketBundle\Form\NewTicketType;
use Adservice\TicketBundle\Form\EditTicketType;
use Adservice\TicketBundle\Form\CloseTicketType;
use Adservice\TicketBundle\Form\CloseTicketWorkshopType;
use Adservice\TicketBundle\Form\EditDescriptionType;

use Adservice\TicketBundle\Entity\Status;
use Adservice\CarBundle\Entity\Car;
use Adservice\CarBundle\Entity\Version;
use Adservice\CarBundle\Entity\Motor;
use Adservice\CarBundle\Form\CarType;

use Adservice\TicketBundle\Entity\Post;
use Adservice\TicketBundle\Form\PostType;

use Adservice\UserBundle\Entity\User;

use Adservice\UtilBundle\Controller\UtilController as UtilController;
use Adservice\UtilBundle\Entity\Document;
use Adservice\UtilBundle\Entity\DocumentRepository;
use Adservice\UtilBundle\Form\DocumentType;

use Adservice\WorkshopBundle\Entity\Workshop;
use Adservice\WorkshopBundle\Entity\WorkshopRepository;
use Adservice\WorkshopBundle\Form\WorkshopType;

use Adservice\UtilBundle\Entity\Pagination;
use Adservice\UtilBundle\Entity\Mailer;

class TicketController extends Controller {

    /**
     * Acceso directo de centralita para abrir el listado de tickets desde una llamada de taller
     * @throws AccessDeniedException
     * @return url
     */
    public function callTicketAction($code_partner=0, $code_workshop=0)
    {
        $security   = $this->get('security.context');
        if (!$security->isGranted('ROLE_ASSESSOR')) throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();
        $array = array();

        if($code_partner != 0 and $code_workshop != 0)
        {
            $workshop = $em->getRepository('WorkshopBundle:Workshop')->findOneBy(array('code_partner'  => $code_partner,
                                                                                       'code_workshop' => $code_workshop));

            if(isset($workshop))
            $array = array('page' => 1, 'num_rows' => 10, 'country' => 0, 'option' => 'all', 'workshop_id' => $workshop->getId() );
            else
            $array = array('page' => 1, 'num_rows' => 10, 'country' => 0, 'option' => 'all');
        }
            return $this->redirect($this->generateUrl('listTicket', $array ));
    }

    /**
     * Devuelve el listado de tickets segunla pagina y la opcion escogida
     * @return url
     */
    public function listTicketAction($page=1, $num_rows=10, $country=0, $option=null, $workshop_id=null)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $request    = $this->getRequest();
        $security   = $this->get('security.context');

        $id_user    = $security->getToken()->getUser()->getId();
        $open       = $em->getRepository('TicketBundle:Status')->findOneBy(array('name' => 'open'  ));
        $closed     = $em->getRepository('TicketBundle:Status')->findOneBy(array('name' => 'closed'));
        $params     = array();
        $joins      = array();

        /* Entrada desde Centralita al listado con el taller seleccionado */
        if($security->isGranted('ROLE_ASSESSOR') and $workshop_id != null) {
            $workshop = $em->getRepository('WorkshopBundle:Workshop')->find($workshop_id);
            $workshops = array('0' => $workshop);
        }else
            $workshops  = array('0' => new Workshop());

        /* TRATAMIENTO DE LAS OPCIONES DE slct_historyTickets */
        if($option == null){
            $params[] = array();
            // Si se envia el codigo del taller se buscan los tickets en funcion de estos
            if ($request->getMethod() == 'POST') {
                $workshops = $em->getRepository('WorkshopBundle:Workshop')->findWorkshopInfo($request);
                if(!empty($workshops)){
                    if ($workshops[0]->getActive() == 0) {
                        $error = $this->get('translator')->trans('workshop_inactive');
                        $this->get('session')->setFlash('error', '¡Error! '.$error);
                    }

                    if(isset($workshops[0]) and $workshops[0]->getId() != "")
                    {
                        $joins[] = array('e.workshop w ', 'w.code_workshop = '.$workshops[0]->getCodeWorkshop()." AND w.partner = ".$workshops[0]->getPartner()->getid()." ");
                        $option  = $workshops[0]->getId();
                    }
                    elseif(isset($workshops['error']))
                    {
                        $error = $this->get('translator')->trans('workshop_inactive');
                        $this->get('session')->setFlash('error', '¡Error! '.$error);
                    }
                    else{ $joins[] = array(); }
                }else {
                    $error = $this->get('translator')->trans('workshop_inactive');
                    $this->get('session')->setFlash('error', '¡Error! '.$error);
                }
            }
            elseif (!$security->isGranted('ROLE_ASSESSOR'))
            {
                $workshops   = $em->getRepository('WorkshopBundle:Workshop')->findBy(array('id' => $security->getToken()->getUser()->getWorkshop()->getId()));

                if($workshops[0]->getId() != "") {
                    $joins[] = array('e.workshop w ', 'w.code_workshop = '.$workshops[0]->getCodeWorkshop()." AND w.partner = ".$workshops[0]->getPartner()->getid()." ");
                    $option  = $workshops[0]->getCodeWorkshop();
                }
            }
            //$option = 'all';
        }
        elseif ($option == 'all'      ) { $params[] = array();  }
        elseif ($option == 'all_mine' ) { $params[] = array('assigned_to', '= '.$id_user); }
        elseif ($option == 'opened'   ) { $params[] = array('status', ' = '.$open  ->getId()); }
        elseif ($option == 'closed'   ) { $params[] = array('status', ' = '.$closed->getId()); }
        elseif ($option == 'free'     )
        {
            $params[] = array('status'        , ' = '.$open  ->getId());
            $params[] = array('assigned_to '  , 'IS NULL');
        }
        elseif ($option == 'pending')
        {
            $params[] = array('status', ' = '.$open->getId());
            $params[] = array('assigned_to'   , 'IS NOT NULL');
            $params[] = array('pending'   , '= 1');
        }
        elseif ($option == 'answered')
        {
            $params[] = array('status', ' = '.$open->getId());
            $params[] = array('assigned_to'   , 'IS NOT NULL');
            $params[] = array('pending'   , '= 0');
        }
        elseif ($option == 'assessor_pending')
        {
            $params[] = array('status', ' = '.$open->getId());

            $country_service = $security->getToken()->getUser()->getCountryService()->getId();
            if($country_service == '7') $params[] = array('id'   , ' != 0 AND e.assigned_to = '.$id_user.' OR (e.assigned_to IS NULL AND w.country IN (5,6) AND e.status = 1)');
            else                        $params[] = array('id'   , ' != 0 AND e.assigned_to = '.$id_user.' OR (e.assigned_to IS NULL AND w.country = '.$country_service.' AND e.status = 1)');
        }

        elseif ($option == 'assessor_answered')
        {
            $params[] = array('status', ' = '.$open->getId());
            $params[] = array('assigned_to'   , '= '.$id_user);
            $params[] = array('pending'   , '= 0');
        }
        elseif ($option == 'other_pending')
        {
            $params[] = array('status', ' = '.$open->getId());
            $params[] = array('assigned_to'   , '!= '.$id_user);
            $params[] = array('pending'   , '= 1');
        }
        elseif ($option == 'other_answered')
        {
            $params[] = array('status', ' = '.$open->getId());
            $params[] = array('assigned_to'   , '!= '.$id_user);
            $params[] = array('pending'   , '= 0');
        }
        elseif ($option == 'assessor_closed')
        {
            $params[] = array('status', ' = '.$closed->getId());
            $params[] = array('assigned_to'   , '= ' .$id_user);
        }
        elseif ($option == 'other_closed')
        {
            $params[] = array('status', ' = '.$closed->getId());
            $params[] = array('assigned_to'   , '!= '.$id_user);
        }
        elseif ($option == 'inactive' )
        {
            // Recupera la fecha del ultimo post de cada ticket del asesor

            if ($security->isGranted('ROLE_ASSESSOR') and !$security->isGranted('ROLE_ADMIN')){

                $consulta = $em->createQuery('SELECT t.id as id, MAX(p.modified_at) as time FROM TicketBundle:Post p JOIN p.ticket t GROUP BY t.id');
                $ids = '0';
                $ids_not = '0';
                foreach ($consulta->getResult() as $row)
                {
                    // Solo añade las id de los ticket que sobrepasan el limite de tiempo (2h)
                    $now = new \DateTime(\date("Y-m-d H:i:s"));
                    $time = new \DateTime(\date($row['time']));

                    $diff = $now->diff($time);
                    $hours = $diff->h + ($diff->days*24);

                    if ($hours >= 2 and $diff->invert == 1) $ids = $ids.', '.$row['id'];
                    else             $ids_not = $ids_not.', '.$row['id'];
                }
                $params[] = array('status', ' = '.$open->getId());
                $params[] = array('id', ' NOT IN ('.$ids_not.') AND e.assigned_to = '.$security->getToken()->getUser()->getId());
            }
            // Recupera la fecha del ultimo post de cada ticket
            //  SQL: SELECT t.id, MAX(p.modified_at) FROM post p JOIN ticket t GROUP BY p.ticket_id
            else{
                $consulta = $em->createQuery('SELECT t.id as id, MAX(p.modified_at) as time FROM TicketBundle:Post p JOIN p.ticket t GROUP BY t');
                $ids = '0';
                $ids_not = '0';
                foreach ($consulta->getResult() as $row)
                {
                    // Solo añade las id de los ticket que sobrepasan el limite de tiempo (2h)
                    $now = new \DateTime(\date("Y-m-d H:i:s"));
                    $time = new \DateTime(\date($row['time']));

                    $diff = $now->diff($time);
                    $hours = $diff->h + ($diff->days*24);

                    if ($hours >= 2 and $diff->invert == 1) $ids = $ids.', '.$row['id'];
                    else             $ids_not = $ids_not.', '.$row['id'];
                }

                $params[] = array('status', ' = '.$open->getId());
                $params[] = array('id', ' IN ('.$ids.') OR (e.id NOT IN ('.$ids_not.') AND e.assigned_to IS NOT NULL)');
            }
        }
        else{
            $workshops = $em->getRepository('WorkshopBundle:Workshop')->findBy(array('id' => $option));
            $params[] = array('workshop', ' = '.$option);
        }

        if($security->isGranted('ROLE_SUPER_ADMIN')) {
            if(isset($joins[0][0]) and $joins[0][0] == 'e.workshop w ')
            {
                if     ($country == '7') $joins[0][1] = $joins[0][1].' AND w.country IN (5,6) ';
                elseif ($country != 0)   $joins[0][1] = $joins[0][1].' AND w.country = '.$country;
                else                     $joins[0][1] = $joins[0][1].' AND w.country != 0';
            }
            else{
                if     ($country == '7') $joins[] = array('e.workshop w ', 'w.country IN (5,6) ');
                elseif ($country != 0)   $joins[] = array('e.workshop w ', 'w.country = '.$country);
                else                     $joins[] = array('e.workshop w ', 'w.country != 0');
            }
        }elseif($security->isGranted('ROLE_ADMIN') and !$security->isGranted('ROLE_SUPER_ADMIN')) {
            $country = $security->getToken()->getUser()->getCountry()->getId();
            if(isset($joins[0][0]) and $joins[0][0] == 'e.workshop w ')
            {
                if($country == '7') $joins[0][1] = $joins[0][1].' AND w.country IN (5,6) ';
                else                $joins[0][1] = $joins[0][1].' AND w.country = '.$country;
            }
            else{
                if($country == '7') $joins[] = array('e.workshop w ', 'w.country IN (5,6) ');
                else                $joins[] = array('e.workshop w ', 'w.country = '.$country);
            }
        }else{
            if($country != 'none'){
                if($country != 0) {
                    if(isset($joins[0][0]) and $joins[0][0] == 'e.workshop w ')
                    {
                        if($country == '7') $joins[0][1] = $joins[0][1].' AND w.country IN (5,6) ';
                        else                $joins[0][1] = $joins[0][1].' AND w.country = '.$country;
                    }
                    else{
                        if($country == '7') $joins[] = array('e.workshop w ', 'w.country IN (5,6) ');
                        else                $joins[] = array('e.workshop w ', 'w.country = '.$country);
                    }
                }else{
                    if($security->isGranted('ROLE_ASSESSOR')) $country = $security->getToken()->getUser()->getCountryService()->getId();
                    else $country = $security->getToken()->getUser()->getCountry()->getId();
                     if(isset($joins[0][0]) and $joins[0][0] == 'e.workshop w ')
                    {
                        if($country == '7') $joins[0][1] = $joins[0][1].' AND w.country IN (5,6) ';
                        else                $joins[0][1] = $joins[0][1].' AND w.country = '.$country;
                    }
                    else{
                        if($country == '7') $joins[] = array('e.workshop w ', 'w.country IN (5,6) ');
                        else                $joins[] = array('e.workshop w ', 'w.country = '.$country);
                    }
                }
            }else{
                 if(isset($joins[0][0]) and $joins[0][0] == 'e.workshop w ')
                {
                    $joins[0][1] = $joins[0][1].' AND w.country != 0';
                }
                else{
                    $joins[] = array('e.workshop w ', 'w.country != 0');
                }
            }
        }

        $pagination = new Pagination($page);
        $ordered = array('e.modified_at', 'DESC');

        if($pagination->getMaxRows() != $num_rows) $pagination = $pagination->changeMaxRows($page, $num_rows);

        if(($security->isGranted('ROLE_SUPER_ADMIN')) or ( isset($workshops[0]) and ($workshops[0]->getId() != null))){
            $tickets = $pagination->getRows($em, 'TicketBundle', 'Ticket', $params, $pagination, $ordered, $joins);
            $length  = $pagination->getRowsLength($em, 'TicketBundle', 'Ticket', $params, $ordered, $joins);
        }
        elseif(($option == 'assessor_pending') or ($option == 'assessor_answered') or ($option == 'other_pending') or ($option == 'other_answered')) {

            $query = 'SELECT t FROM TicketBundle:Ticket t';
            if     (($option == 'assessor_pending') or ($option == 'assessor_answered')) $query = $query.' WHERE t.assigned_to = '.$id_user;
            elseif (($option == 'other_pending'   ) or ($option == 'other_answered'   )) $query = $query.' WHERE t.assigned_to != '.$id_user;

            if     (($option == 'assessor_pending') or ($option == 'other_pending')) $query = $query.' AND t.pending = 1';
            elseif (($option == 'assessor_answered') or ($option == 'other_answered')) $query = $query.' AND t.pending != 1';

            $consulta = $em->createQuery($query);
            $query_posts = '';
            foreach ($consulta->getResult() as $ticket)
            {
                $query2 = 'SELECT p FROM TicketBundle:Post p WHERE p.ticket = '.$ticket->getId();
                $consulta2 = $em->createQuery($query2);
                $result = $consulta2->getResult();
                $last_post = end($result);

                if($last_post != null){
                   $last_post_role = $last_post->getCreatedBy();
                   $last_post_role = $last_post_role->getRoles();
                   $last_post_role = $last_post_role[0];
                }

                if(count($result) != 0 and $last_post != null
                and ($last_post_role == 'ROLE_ASSESSOR'
                  or $last_post_role == 'ROLE_ADMIN'
                  or $last_post_role == 'ROLE_SUPER_ADMIN')
                and ($option == 'assessor_answered' or $option == 'other_answered'))
                {
                    if($query_posts == '') $query_posts = ' e.id = '.$ticket->getId();
                    else                   $query_posts = $query_posts.' OR e.id = '.$ticket->getId();
                }
                elseif((($option == 'assessor_pending' or $option == 'other_pending') and count($result) == 0)
                    or (($option == 'assessor_pending' or $option == 'other_pending') and $last_post->getCreatedBy()->getId() != $id_user
                                                                                      and $last_post->getCreatedBy()->getId() != $ticket->getAssignedTo()->getId() ))
                {
                    if($query_posts == '') $query_posts = ' e.id = '.$ticket->getId();
                    else                   $query_posts = $query_posts.' OR e.id = '.$ticket->getId();
                }
            }
            if($query_posts != '') $joins[] = array('e.status s', $query_posts);

            $tickets = $pagination->getRows      ($em, 'TicketBundle', 'Ticket', $params, $pagination, $ordered, $joins);
            $length  = $pagination->getRowsLength($em, 'TicketBundle', 'Ticket', $params, $ordered, $joins);
        }
        else{
            if(isset($workshops[0]) and $workshops[0]->getId()){
                $joins[] = array('e.workshop w ', 'w.code_workshop = '.$workshops[0]->getCodeWorkshop()." AND w.partner = ".$workshops[0]->getPartner()->getid()." ");
                //$joins[] = array('e.workshop w', ' w.country = '.$security->getToken()->getUser()->getCountry()->getId());
                $tickets = $pagination->getRows      ($em, 'TicketBundle', 'Ticket', $params, $pagination, $ordered, $joins);
                $length  = $pagination->getRowsLength($em, 'TicketBundle', 'Ticket', $params, $ordered, $joins);
            }
            elseif($security->isGranted('ROLE_USER') and !$security->isGranted('ROLE_ASSESSOR')) {
                $user = $em->getRepository('UserBundle:User')->findOneBy(array('id' => $id_user));
                $workshop = $user->getWorkshop();

                if(isset($joins[0][0]) and $joins[0][0] == 'e.workshop w ')
                {
                    $joins[0][1] = $joins[0][1].' AND w.code_workshop = '.$workshop->getCodeWorkshop()." AND w.partner = ".$workshop->getPartner()->getid()." ";
                }
                else{
                    $joins[] = array('e.workshop w ', 'w.code_workshop = '.$workshop->getCodeWorkshop()." AND w.partner = ".$workshop->getPartner()->getid()." ");
                }
                //$joins[] = array('e.workshop w ', 'w.country = '.$security->getToken()->getUser()->getCountry()->getId());
                $tickets = $pagination->getRows      ($em, 'TicketBundle', 'Ticket', $params, $pagination, $ordered, $joins);
                $length  = $pagination->getRowsLength($em, 'TicketBundle', 'Ticket', $params, $ordered, $joins);
            }else{
                //$joins[] = array('e.workshop w ', 'w.country = '.$security->getToken()->getUser()->getCountry()->getId());
                $tickets = $pagination->getRows      ($em, 'TicketBundle', 'Ticket', $params, $pagination, $ordered, $joins);
                $length  = $pagination->getRowsLength($em, 'TicketBundle', 'Ticket', $params, $ordered, $joins);
            }
        }

        $pagination->setTotalPagByLength($length);

        if (!isset($workshops[0])) {
            $workshops = array('0' => new Workshop());
        }

        $size = sizeof($workshops);
        if( $size > 1 ) {
            $error = $this->get('translator')->trans('workshop_confirm');
            $error .='('.$size.')';
            $this->get('session')->setFlash('error', $error);
        }

        if ($option == null) $option = 'all';

        $b_query   = $em->createQuery('SELECT b FROM CarBundle:Brand b, CarBundle:Model m WHERE b.id = m.brand ORDER BY b.name');
        $brands    = $b_query->getResult();
        $systems   = $em->getRepository('TicketBundle:System')->findBy(array(), array('name' => 'ASC'));
        if ($security->isGranted('ROLE_ASSESSOR') and !$security->isGranted('ROLE_ADMIN'))
             $countries = $em->getRepository('UtilBundle:CountryService')->findAll();
        else $countries = $em->getRepository('UtilBundle:Country')->findAll();
        $t_inactive = array();

        $adsplus  = $em->getRepository('WorkshopBundle:ADSPlus'  )->findOneBy(array('idTallerADS'  => $workshops[0]->getId() ));

        if ($security->isGranted('ROLE_ASSESSOR') and !$security->isGranted('ROLE_ADMIN')){

            $consulta = $em->createQuery('SELECT t.id as id, MAX(p.modified_at) as time FROM TicketBundle:Post p JOIN p.ticket t WHERE t.assigned_to = '.$security->getToken()->getUser()->getId().' GROUP BY t');
            $ids = '0';
            $ids_not = '0';
            foreach ($consulta->getResult() as $row)
            {
                // Solo añade las id de los ticket que sobrepasan el limite de tiempo (2h)
                $now = new \DateTime(\date("Y-m-d H:i:s"));
                $time = new \DateTime(\date($row['time']));

                $diff = $now->diff($time);
                $hours = $diff->h + ($diff->days*24);

                if ($hours >= 2 and $diff->invert == 1) $ids = $ids.', '.$row['id'];
                else $ids_not = $ids_not.', '.$row['id'];
            }

            $params_inactive[] = array('assigned_to', ' = '.$security->getToken()->getUser()->getId());
            $params_inactive[] = array('status', ' = '.$open->getId());
            $params_inactive[] = array('id', ' NOT IN ('.$ids_not.')');

            $pagination_inactive = new Pagination(1);

            if(isset($country) and $country != 0) {
                // Buscar tickets inactivos según el país de servicio
                $joins[] = array('e.workshop wo ', 'wo.country = '.$country." ");
                $inactive  = $pagination_inactive->getRowsLength($em, 'TicketBundle', 'Ticket', $params_inactive, null, $joins);
                $tickets_inactive = $pagination_inactive->getRows($em, 'TicketBundle', 'Ticket', $params_inactive, $pagination_inactive, null, $joins);

            }else{
                $inactive = $pagination_inactive->getRowsLength($em, 'TicketBundle', 'Ticket', $params_inactive);
                $tickets_inactive = $pagination_inactive->getRows($em, 'TicketBundle', 'Ticket', $params_inactive);
            }


            foreach ($tickets_inactive as $t) {
                $t_inactive[$t->getId()] = $t->getId();
            }

        }else{
            $inactive = 0;
        }

        $importances = $em->getRepository('TicketBundle:Importance')->findAll();

        if (sizeof($tickets) == 0) $pagination = new Pagination(0);

        $array = array('workshop'   => $workshops[0], 'pagination'  => $pagination,  'tickets'    => $tickets,
                       'country'    => $country,      'num_rows'    => $num_rows,    'option'     => $option,    'brands'     => $brands,
                       'systems'    => $systems,      'countries'   => $countries,   'adsplus'    => $adsplus,   'inactive'   => $inactive,
                       't_inactive' => $t_inactive,   'importances' => $importances,
              );

        if      ($security->isGranted('ROLE_ADMIN'))    return $this->render('TicketBundle:Layout:list_ticket_layout.html.twig', $array);
        elseif  ($security->isGranted('ROLE_ASSESSOR')) {
            return $this->render('TicketBundle:Layout:list_ticket_assessor_layout.html.twig', $array);
        }
        else                                            return $this->render('TicketBundle:Layout:list_ticket_layout.html.twig', $array);
    }

    /**
     * Crea un ticket abierto con sus respectivos post y car
     * @return url
     */
    public function newTicketAction($id_workshop=null)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $security = $this->get('security.context');
        $request  = $this->getRequest();
        $ticket   = new Ticket();
        $car      = new Car();
        $document = new Document();

        if ($id_workshop != null)
            { $workshop = $em->getRepository('WorkshopBundle:Workshop')->find($id_workshop); }
        else{ $workshop =  new Workshop(); }

        $open_newTicket = $request->request->get('open_newTicket');
        $id_brand = $request->request->get('n_id_brand');
        $id_model = $request->request->get('n_id_model');
        $id_version = $request->request->get('n_id_version');
        $id_subsystem = $request->request->get('n_id_subsystem');
        $id_importance = $request->request->get('n_id_importance');
        $id_vin = $request->request->get('n_id_vin');
        $id_plateNumber = $request->request->get('n_id_plateNumber');

        if (isset($id_brand) and $id_brand != '' and $id_brand != '0') {
            $brand = $em->getRepository('CarBundle:Brand')->find($id_brand);
            $car->setBrand($brand);
        }
        if (isset($id_model) and $id_model != '' and $id_model != '0') {
            $model = $em->getRepository('CarBundle:Model')->find($id_model);
            $car->setModel($model);
        }
        if (isset($id_version) and $id_version != '' and $id_version != '0') {
            $version = $em->getRepository('CarBundle:Version')->findById($id_version);
            $car->setVersion($version);
        }
        if (isset($id_subsystem) and $id_subsystem != '' and $id_subsystem != '0') {
            $subsystem = $em->getRepository('TicketBundle:Subsystem')->find($id_subsystem);
            $ticket->setSubsystem($subsystem);
        }
        if (isset($id_importance) and $id_importance != '' and $id_importance != '0') {
            $importance = $em->getRepository('TicketBundle:Importance')->find($id_importance);
            $ticket->setImportance($importance);
        }
        if (isset($id_vin) and $id_vin != '' and $id_vin != '0') {
            $car->setVin($id_vin);
        }
        if (isset($id_plateNumber) and $id_plateNumber != '' and $id_plateNumber != '0') {
            $car->setPlateNumber($id_plateNumber);
        }

        $systems = $em->getRepository('TicketBundle:System')->findAll();
        $b_query = $em->createQuery('SELECT b FROM CarBundle:Brand b, CarBundle:Model m WHERE b.id = m.brand ORDER BY b.name');
        $brands  = $b_query->getResult();
        $adsplus = $em->getRepository('WorkshopBundle:ADSPlus'  )->findOneBy(array('idTallerADS'  => $workshop->getId() ));

        //Define Forms
        $form  = $this->createForm(new NewTicketType(), $ticket);
        $formC = $this->createForm(new CarType(), $car);
        $formD = $this->createForm(new DocumentType(), $document);

        if (isset($open_newTicket) and $open_newTicket == '1' and $request->getMethod() == 'POST') {
            //campos comunes
            $user     = $em->getRepository('UserBundle:User')->find($security->getToken()->getUser()->getId());
            $status   = $em->getRepository('TicketBundle:Status')->findOneByName('open');

            $form ->bindRequest($request);
            $formC->bindRequest($request);
            $formD->bindRequest($request);

            /*Validacion Ticket*/
            $str_len = strlen($ticket->getDescription());
            if($security->isGranted('ROLE_ASSESSOR')) { $max_len = 10000; }
            else { $max_len = 500; }

            if ($str_len <= $max_len ) {

                $ticket_form = $request->request->get('ticket_form');
                if (isset($ticket_form['subsystem'])) {
                    $id_subsystem = $ticket_form['subsystem'];
                }else{
                    $id_subsystem = '0';
                }
                if($id_subsystem != null and $id_subsystem != '' and $id_subsystem != '0') {
                    $subsystem = $em->getRepository('TicketBundle:Subsystem')->find($id_subsystem);
                    $ticket->setSubsystem($subsystem);
                }

                if ($ticket->getSubsystem() != "" or $security->isGranted('ROLE_ASSESSOR') == 0) {

                    if ($formC->isValid() && $formD->isValid()) {

                         // Controla si se ha subido un fichero erroneo
                        $file = $document->getFile();
                        if (isset($file)) $extension = $file->getMimeType(); else { $extension = '0'; }
                        if (isset($file)) $size      = $file->getSize();     else { $size      = '0'; }

                        if ($extension  == "application/pdf" or $extension  == "application/x-pdf" or $extension  == "image/bmp" or $extension  == "image/jpeg"
                         or $extension  == "image/png" or $extension  == "image/gif" or $extension  == "application/mspowerpoint" or $extension  == "0") {

                            if ($security->isGranted('ROLE_ASSESSOR') or $size <= 4096000 ){
                                //Define CAR
                                $car = UtilController::newEntity($car, $user);

                                $id_brand   = $request->request->get('new_car_form_brand');
                                $id_model   = $request->request->get('new_car_form_model');

                                $brand   = $em->getRepository('CarBundle:Brand'  )->find($id_brand  );
                                $model   = $em->getRepository('CarBundle:Model'  )->find($id_model  );

                                $car->setBrand($brand);
                                $car->setModel($model);
                                $vin = $car->getVin();
                                //SI VIN TIENE LONGITUD 17
                                if(strlen($vin) == 17){
                                    //SI VIN NO CONTIENE 'O'
                                    if(!strpos(strtolower($vin),'o')){
                                        //SI NO HA ESCOGIDO VERSION DE DEJA NULL
                                        $id_version = $request->request->get('new_car_form_version');
                                        if (isset($id_version)){
                                            $version = $em->getRepository('CarBundle:Version')->findById($id_version);
                                        }
                                        else{
                                            $id_version = null;
                                        }

                                        if (isset($version))
                                        {
                                            $sizeV = sizeof($version);

                                            if ($size > 0) {
                                                $car->setVersion($version);
                                            }
                                        }
                                        else{
                                            $car->setVersion(null);
                                        }

                                        $car = UtilController::newEntity($car, $user);
                                        UtilController::saveEntity($em, $car, $user);

                                        //Define TICKET
                                        $ticket = UtilController::newEntity($ticket, $user);
                                        if ($security->isGranted('ROLE_ASSESSOR'))
                                        {
                                            $ticket->setWorkshop($workshop);
                                            $ticket->setAssignedTo($user);
                                        }else{
                                            $ticket->setWorkshop($user->getWorkshop());
                                        }
                                        $ticket->setStatus($status);
                                        $ticket->setPending(1);
                                        $ticket->setCar($car);
                                        UtilController::saveEntity($em, $ticket, $user);

                                        //Define Document
                                        if ($file != "") {

                                            //Define Post
                                            $post = new Post();
                                            $post = UtilController::newEntity($post, $user);
                                            $post->setTicket($ticket);
                                            $post->setMessage(" ");
                                            UtilController::saveEntity($em, $post, $user, false);

                                            $document->setPost($post);
                                            $dir = $document->getUploadRootDir();
                                            if (!file_exists($dir) && !is_dir($dir)) {
                                                mkdir($dir, 0775);
                                            }

                                            $em->persist($document);
                                            $em->flush();
                                        }
                                    } else {
                                        $this->get('session')->setFlash('error', $this->get('translator')->trans('ticket_vin_error_o'));

                                        return $this->render('TicketBundle:Layout:new_ticket_layout.html.twig', array('ticket' => $ticket,
                                                    'form' => $form->createView(),
                                                    'formC' => $formC->createView(),
                                                    'formD' => $formD->createView(),
                                                    'brands' => $brands,
                                                    'systems' => $systems,
                                                    'adsplus' => $adsplus,
                                                    'workshop' => $workshop,
                                                    'form_name' => $form->getName(),));
                                    }

                                }else {
                                    $this->get('session')->setFlash('error', $this->get('translator')->trans('ticket_vin_error_length'));

                                        return $this->render('TicketBundle:Layout:new_ticket_layout.html.twig', array('ticket' => $ticket,
                                                    'form' => $form->createView(),
                                                    'formC' => $formC->createView(),
                                                    'formD' => $formD->createView(),
                                                    'brands' => $brands,
                                                    'systems' => $systems,
                                                    'adsplus' => $adsplus,
                                                    'workshop' => $workshop,
                                                    'form_name' => $form->getName(),));
                                }
                            } else {
                                // ERROR tamaño
                                $this->get('session')->setFlash('error', $this->get('translator')->trans('error.file_size'));

                                return $this->render('TicketBundle:Layout:new_ticket_layout.html.twig', array('ticket' => $ticket,
                                            'action' => 'newTicket',
                                            'form' => $form->createView(),
                                            'formC' => $formC->createView(),
                                            'formD' => $formD->createView(),
                                            'brands' => $brands,
                                            'systems' => $systems,
                                            'adsplus' => $adsplus,
                                            'workshop' => $workshop,
                                            'form_name' => $form->getName(),));
                            }
                        } else {
                            // ERROR tipo de fichero
                            $this->get('session')->setFlash('error', $this->get('translator')->trans('error.file'));

                            return $this->render('TicketBundle:Layout:new_ticket_layout.html.twig', array('ticket' => $ticket,
                                        'action' => 'newTicket',
                                        'form' => $form->createView(),
                                        'formC' => $formC->createView(),
                                        'formD' => $formD->createView(),
                                        'brands' => $brands,
                                        'systems' => $systems,
                                        'adsplus' => $adsplus,
                                        'workshop' => $workshop,
                                        'form_name' => $form->getName(),));
                         }

                        $mail = $ticket->getWorkshop()->getEmail1();
                        $pos = strpos($mail, '@');
                        if ($pos != 0) {

                            // Cambiamos el locale para enviar el mail en el idioma del taller
                            $locale = $request->getLocale();
                            $lang_w = $ticket->getWorkshop()->getCountry()->getLang();
                            $lang   = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_w);
                            $request->setLocale($lang->getShortName());

                            /* MAILING */
                            $mailer = $this->get('cms.mailer');
                            $mailer->setTo($mail);
                            $mailer->setSubject($this->get('translator')->trans('mail.newTicket.subject').$ticket->getId());
                            $mailer->setFrom('noreply@adserviceticketing.com');
                            $mailer->setBody($this->renderView('UtilBundle:Mailing:ticket_new_mail.html.twig', array('ticket' => $ticket)));
                            $mailer->sendMailToSpool();
                            //echo $this->renderView('UtilBundle:Mailing:ticket_new_mail.html.twig', array('ticket' => $ticket));die;

                            // Dejamos el locale tal y como estaba
                            $request->setLocale($locale);

                            $this->get('session')->setFlash('ticket_created', $this->get('translator')->trans('ticket_created'));
                        }

                        if (isset($_POST['save_close'])){
                            return $this->redirect($this->generateUrl('closeTicket', array( 'id' => $ticket->getId())));
                        }else{
                            return $this->redirect($this->generateUrl('showTicket', array('id' => $ticket->getId())));
                        }

                    } else { $this->get('session')->setFlash('error', $this->get('translator')->trans('error.bad_introduction')); }

                } else { $this->get('session')->setFlash('error_ticket', $this->get('translator')->trans('error.bad_introduction.ticket')); }

            }else{ $this->get('session')->setFlash('error', $this->get('translator')->trans('error.txt_length').' '.$max_len.' '.$this->get('translator')->trans('error.txt_chars').'.'); }
        }

        if(isset($id_subsystem)) {
            $subsystem = $em->getRepository('TicketBundle:Subsystem')->find($id_subsystem);
            $id_system = $subsystem->getSystem()->getId();
            $id_subsystem = $subsystem->getId();
        }else {
            $id_system = '0';
            $id_subsystem = '0';
        }


        $array = array( 'ticket' => $ticket,
                        'action' => 'newTicket',
                        'car' => $car,
                        'form' => $form->createView(),
                        'formC' => $formC->createView(),
                        'formD' => $formD->createView(),
                        'brands' => $brands,
                        'id_version' => $id_version,
                        'id_system' => $id_system,
                        'id_subsystem' => $id_subsystem,
                        'systems' => $systems,
                        'adsplus' => $adsplus,
                        'workshop' => $workshop,
                        'form_name' => $form->getName(),
                    );
        // if(isset($subsystem)) { $array[] = 'subsystem' => $subsystem; }

        if ($security->isGranted('ROLE_ASSESSOR'))  return $this->render('TicketBundle:Layout:new_ticket_assessor_layout.html.twig', $array);
        else                                        return $this->render('TicketBundle:Layout:new_ticket_layout.html.twig', $array);
    }

    /**
     * Edita el ticket y el car asignado a partir de su id
     * @Route("/ticket/edit/{id}")
     * @ParamConverter("ticket", class="TicketBundle:Ticket")
     * @return url
     */
    public function editTicketAction($id, $ticket)
    {

        $security = $this->get('security.context');
        if ($security->isGranted('ROLE_SUPER_ADMIN')
        or (!$security->isGranted('ROLE_SUPER_ADMIN') and $ticket->getWorkshop()->getCountry()->getId() == $security->getToken()->getUser()->getCountry()->getId())
        or ($security->isGranted('ROLE_ASSESSOR') and !$security->isGranted('ROLE_ADMIN'))
        ){

            $em = $this->getDoctrine()->getEntityManager();
            $request = $this->getRequest();

            $form = $this->createForm(new EditTicketType(), $ticket);

            if ($request->getMethod() == 'POST') {

                $user = $em->getRepository('UserBundle:User')->find($security->getToken()->getUser()->getId());

                $form->bindRequest($request);

                /*Validacion Ticket*/
                $str_len = strlen($ticket->getDescription());
                if($security->isGranted('ROLE_ASSESSOR')) { $max_len = 10000; }
                else { $max_len = 500; }

                if ($str_len <= $max_len ) {
                    //Define CAR
                    if ($form->isValid()) {

                        UtilController::saveEntity($em, $ticket, $user);

                        $mail = $ticket->getWorkshop()->getEmail1();
                        $pos = strpos($mail, '@');
                        if ($pos != 0) {

                        // Cambiamos el locale para enviar el mail en el idioma del taller
                            $locale = $request->getLocale();
                            $lang_w = $ticket->getWorkshop()->getCountry()->getLang();
                            $lang   = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_w);
                            $request->setLocale($lang->getShortName());

                            /* MAILING */
                            $mailer = $this->get('cms.mailer');
                            $mailer->setTo($mail);
                            $mailer->setSubject($this->get('translator')->trans('mail.editTicket.subject').$id);
                            $mailer->setFrom('noreply@adserviceticketing.com');
                            $mailer->setBody($this->renderView('UtilBundle:Mailing:ticket_edit_mail.html.twig', array('ticket' => $ticket)));
                            $mailer->sendMailToSpool();
                            //echo $this->renderView('UtilBundle:Mailing:ticket_new_mail.html.twig', array('ticket' => $ticket));die;

                            // Dejamos el locale tal y como estaba
                            $request->setLocale($locale);
                        }

                        return $this->redirect($this->generateUrl('showTicket', array('id' => $id)));

                    }else{ $this->get('session')->setFlash('error', $this->get('translator')->trans('error.bad_introduction')); }

            }else{ $this->get('session')->setFlash('error', $this->get('translator')->trans('error.txt_length').' '.$max_len.' '.$this->get('translator')->trans('error.txt_chars').'.'); }
            }
            $systems     = $em->getRepository('TicketBundle:System'    )->findAll();

            $array = array(
                            'action'    => 'showTicket',
                            'form'      => $form->createView(),
                            'form_name' => $form->getName(),
                            'ticket'    => $ticket,
                            'systems'   => $systems,
                            'form_name' => $form->getName(),
                        );
            if ($security->isGranted('ROLE_ASSESSOR'))  return $this->render('TicketBundle:Layout:show_ticket_assessor_layout.html.twig', $array);
            else                                        return $this->render('TicketBundle:Layout:show_ticket_layout.html.twig', $array);
        }else{
            return $this->render('TwigBundle:Exception:exception_access.html.twig');
        }
    }

    /**
     * Elimina el ticket de la bbdd si no tiene respuesta (posts == 1)
     * @Route("/ticket/delete/{id}")
     * @ParamConverter("ticket", class="TicketBundle:Ticket")
     * @throws AccessDeniedException
     * @throws CreateNotFoundException
     */
    public function deleteTicketAction($id, $ticket)
    {
        $security = $this->get('security.context');
        $request  = $this->getRequest();
        if ($security->isGranted('ROLE_SUPER_ADMIN')
        or (!$security->isGranted('ROLE_SUPER_ADMIN') and $ticket->getWorkshop()->getCountry()->getId() == $security->getToken()->getUser()->getCountry()->getId())
        or ($security->isGranted('ROLE_ASSESSOR') and !$security->isGranted('ROLE_ADMIN'))
        ){
            if ($security->isGranted('ROLE_USER') === false){
                throw new AccessDeniedException();
            }
            $em = $this->getDoctrine()->getEntityManager();

            //se borrara solo si hay un post sin respuesta, si hay mas de uno se deniega
            $posts = $ticket->getPosts(); //echo count($posts);
            // if (count($posts)>1) throw $this->createNotFoundException('Este Ticket no puede borrarse, ya esta respondido');

            //puede borrarlo el assessor o el usuario si el ticket no esta assignado aun
            if ((!$security->isGranted('ROLE_ASSESSOR') and ($ticket->getAssignedTo() != null))){
                throw $this->createNotFoundException('Este ticket solo puede ser borrado por un asesor');
            }

            //si el ticket esta cerrado no se puede borrar
            if($ticket->getStatus()->getName() == 'closed'){
               throw $this->createNotFoundException('Este ticket ya esta cerrado');
            }
            //borra todos los post del ticket
            foreach ($posts as $post) {
                 $em->remove($post);
            }
            //borra el ticket
            $em->remove($ticket);

            $mail = $ticket->getWorkshop()->getEmail1();
            $pos = strpos($mail, '@');
            if ($pos != 0) {

                // Cambiamos el locale para enviar el mail en el idioma del taller
                $locale = $request->getLocale();
                $lang_w = $ticket->getWorkshop()->getCountry()->getLang();
                $lang   = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_w);
                $request->setLocale($lang->getShortName());

                /* MAILING */
                $mailer = $this->get('cms.mailer');
                $mailer->setTo($mail);
                $mailer->setSubject($this->get('translator')->trans('mail.deleteTicket.subject').$id);
                $mailer->setFrom('noreply@adserviceticketing.com');
                $mailer->setBody($this->renderView('UtilBundle:Mailing:ticket_delete_mail.html.twig', array('ticket' => $ticket)));
                $mailer->sendMailToSpool();
                //echo $this->renderView('UtilBundle:Mailing:ticket_delete_mail.html.twig', array('ticket' => $ticket));die;

                // Dejamos el locale tal y como estaba
                $request->setLocale($locale);
            }

            $em->flush();
            return $this->redirect($this->generateUrl('listTicket'));
        }
        else{
            return $this->render('TwigBundle:Exception:exception_access.html.twig');
        }
    }

    /**
     * Muestra los posts que pertenecen a un ticket
     * @Route("/ticket/show/{id}")
     * @ParamConverter("ticket", class="TicketBundle:Ticket")
     * @return url
     */
    public function showTicketAction($ticket)
    {
        $security = $this->get('security.context');
        if ($security->isGranted('ROLE_SUPER_ADMIN')
        or (!$security->isGranted('ROLE_SUPER_ADMIN') and $security->isGranted('ROLE_ASSESSOR') and $ticket->getWorkshop()->getCountry()->getId() == $security->getToken()->getUser()->getCountry()->getId())
        or (!$security->isGranted('ROLE_ASSESSOR') and $ticket->getWorkshop() == $security->getToken()->getUser()->getWorkshop())
        or ($security->isGranted('ROLE_ASSESSOR') and !$security->isGranted('ROLE_ADMIN'))
        ){
            $em       = $this->getDoctrine()->getEntityManager();
            $request  = $this->getRequest();
            $user     = $security->getToken()->getUser();
            $car      = $ticket->getCar();
            $version  = $car->getVersion();
            $model    = $car->getModel();
            $brand    = $car->getBrand();

            if (isset($version)) $id = $car->getVersion()->getId();
            else $id = "";

            if ($security->isGranted('ROLE_SUPER_ADMIN')) $sentences = $em->getRepository('TicketBundle:Sentence')->findBy(array('active' => 1));
            else $sentences = $em->getRepository('TicketBundle:Sentence')->findBy(array('active' => 1, 'country' => $security->getToken()->getUser()->getCountry()->getId()));

            $post     = new Post();
            $document = new Document();
            $systems  = $em->getRepository('TicketBundle:System')->findAll();
            $block    = null;

            if ($ticket->getBlockedBy() != null and $ticket->getBlockedBy() != $user) {

                //Si ha pasado mas de una hora desde la ultima modificación y esta bloqueado.. lo desbloqueamos
                $now = new \DateTime(\date("Y-m-d H:i:s"));
                $last_modified = $ticket->getModifiedAt();

                $interval = $last_modified->diff($now);

                $block = $interval->h.'h '.$interval->m.'m ';
                //echo $block;die;
                if($interval->h > 1) {
                    $ticket->setBlockedBy(null);
                }
            }

            //Define Forms
            $formP = $this->createForm(new PostType(), $post);
            $formD = $this->createForm(new DocumentType(), $document);

            $array = array( 'formP'     => $formP->createView(),
                            'formD'     => $formD->createView(),
                            'action'    => 'showTicket',
                            'ticket'    => $ticket,
                            'systems'   => $systems,
                            'sentences' => $sentences,
                            'form_name' => $formP->getName(),
                            'brand'     => $brand,
                            'model'     => $model,
                            'version'   => $version,
                            'id'        => $id );

            if ($security->isGranted('ROLE_ASSESSOR')) {
                $form = $this->createForm(new EditTicketType(), $ticket);
                $array['form'] = $form->createView();
            }
            if ($request->getMethod() == 'POST') {

                //Define Ticket
                if ($security->isGranted('ROLE_ASSESSOR')) {

                    $form->bindRequest($request);
                }

                if(!$security->isGranted('ROLE_ASSESSOR') or ($security->isGranted('ROLE_ASSESSOR') /*and $form->isValid()*/)){

                    $formP->bindRequest($request);
                    $formD->bindRequest($request);

                    if ($formP->isValid() and $formD->isValid()) {

                        if($security->isGranted('ROLE_ASSESSOR')){

                            $id_subsystem = $request->request->get('edit_ticket_form')['subsystem'];
                            if($id_subsystem != '0' and ($ticket->getSubsystem() == null or $ticket->getSubsystem()->getId() != $id_subsystem)) {
                                $subsystem = $em->getRepository('TicketBundle:Subsystem')->find($id_subsystem);

                                if(isset($subsystem )) $ticket->setSubsystem($subsystem);
                            }
                        }
                        // Controla si se ha subido un fichero erroneo
                        $file = $document->getFile();
                        if (isset($file)) $extension = $file->getMimeType(); else { $extension = '0'; }
                        if (isset($file)) $size      = $file->getSize();     else { $size      = '0'; }

                        if ($extension  == "application/pdf" or $extension  == "application/x-pdf" or $extension  == "image/bmp" or $extension  == "image/jpeg"
                         or $extension  == "image/png" or $extension  == "image/gif" or $extension  == "application/mspowerpoint" or $extension  == "0") {

                            if ($security->isGranted('ROLE_ASSESSOR') or $size <= 4096000 ){
                                $str_len = strlen($post->getMessage());
                                if($security->isGranted('ROLE_ASSESSOR')) { $max_len = 10000; }
                                else { $max_len = 500; }
                                if ($str_len <= $max_len ) {
                                    //Define Post
                                    $post = UtilController::newEntity($post, $user);
                                    $post->setTicket($ticket);
                                    UtilController::saveEntity($em, $post, $user, false);

                                    //Define Document
                                    $document->setPost($post);

                                    if ($file != "") {
                                            $em->persist($document);
                                    }

                                    //Se desbloquea el ticket una vez respondido
                                    // if ($ticket->getBlockedBy() != null) {
                                    //     $ticket->setBlockedBy(null);

                                        /*si assessor responde se le asigna y se amrca como respondido, si es el taller se marca como pendiente */
                                        if ($security->isGranted('ROLE_ASSESSOR')) {
                                            $ticket->setAssignedTo($user);
                                            $ticket->setPending(0);
                                        }else{
                                            $ticket->setPending(1);
                                        }
                                    // }

                                    UtilController::saveEntity($em, $ticket, $user);

                                    $mail = $ticket->getWorkshop()->getEmail1();
                                    $pos = strpos($mail, '@');

                                    if ($pos != 0) {

                                        // Cambiamos el locale para enviar el mail en el idioma del taller
                                        $locale = $request->getLocale();
                                        $lang_w = $ticket->getWorkshop()->getCountry()->getLang();
                                        $lang   = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_w);
                                        $request->setLocale($lang->getShortName());

                                        /* MAILING */
                                        $mailer = $this->get('cms.mailer');
                                        $mailer->setTo($mail);
                                        $mailer->setSubject($this->get('translator')->trans('mail.answerTicket.subject').$ticket->getId());
                                        $mailer->setFrom('noreply@adserviceticketing.com');
                                        $mailer->setBody($this->renderView('UtilBundle:Mailing:ticket_answer_mail.html.twig', array('ticket' => $ticket)));
                                        $mailer->sendMailToSpool();

                                        if (!$security->isGranted('ROLE_ASSESSOR') and $ticket->getAssignedTo() != null) {
                                            $mailer->setTo($ticket->getAssignedTo()->getEmail1());
                                            $mailer->sendMailToSpool();
                                        }
                                        //echo $this->renderView('UtilBundle:Mailing:ticket_answer_mail.html.twig', array('ticket' => $ticket));die;

                                        // Dejamos el locale tal y como estaba
                                        $request->setLocale($locale);
                                    }
                                } else{ $this->get('session')->setFlash('error', $this->get('translator')->trans('error.txt_length').' '.$max_len.' '.$this->get('translator')->trans('error.txt_chars').'.'); }
                            } else {
                                // ERROR tamaño
                                $this->get('session')->setFlash('error', $this->get('translator')->trans('error.file_size'));

                                return $this->render('TicketBundle:Layout:show_ticket_layout.html.twig', $array);
                            }
                        } else {
                            // ERROR tipo de fichero
                            $this->get('session')->setFlash('error', $this->get('translator')->trans('error.file'));

                            return $this->render('TicketBundle:Layout:show_ticket_layout.html.twig', $array);
                        }
                    }
                }

                return $this->redirect($this->generateUrl('showTicket', array(  'id'        => $ticket->getId(),
                                                                                'form_name' => $formP->getName(),
                                                                                'action'    => 'showTicket',
                                                                                'ticket'    => $ticket,
                                                                                'systems'   => $systems,
                                                                                'form_name' => $formP->getName(),
                                                                                'brand'     => $brand,
                                                                                'model'     => $model,
                                                                                'version'   => $version )));
            }

            if ($security->isGranted('ROLE_ASSESSOR'))
            {
                $array['form'] = ($form ->createView());
                return $this->render('TicketBundle:Layout:show_ticket_assessor_layout.html.twig', $array);
            }
            else return $this->render('TicketBundle:Layout:show_ticket_layout.html.twig', $array);
        }
        else{
            return $this->render('TwigBundle:Exception:exception_access.html.twig');
        }
    }

    /**
     * @Route("/post_edit/{id}")
     * @ParamConverter("post", class="TicketBundle:Post")
     */
    public function editPostAction($post)
    {
        if (! $this->get('security.context')->isGranted('ROLE_USER')){
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $ticket = $post->getTicket();

        $petition = $this->getRequest();
        $form = $this->createForm(new PostType(), $post);

        if ($petition->getMethod() == 'POST') {
            $form->bindRequest($petition);

            if ($form->isValid()) {

                $em->persist($post);
                $em->flush();
                return $this->redirect($this->generateUrl('showTicket', array('id'=> $ticket->getId())));
                //return $this->showTicketAction($ticket);
            }
        }

        return $this->render('TicketBundle:Post:edit_post.html.twig', array('post'      => $post,
                                                                            'ticket'    => $ticket,
                                                                            'form_name' => $form->getName(),
                                                                            'form'      => $form->createView()));
    }

    /**
     * Cierra el ticket
     * @Route("/ticket/close/{id}")
     * @ParamConverter("ticket", class="TicketBundle:Ticket")
     * @return url
     */
    public function closeTicketAction($id, $ticket)
    {
        $security = $this->get('security.context');
        if ($security->isGranted('ROLE_SUPER_ADMIN')
        or (!$security->isGranted('ROLE_SUPER_ADMIN') and $ticket->getWorkshop()->getCountry()->getId() == $security->getToken()->getUser()->getCountry()->getId())
        or ($security->isGranted('ROLE_ASSESSOR') and !$security->isGranted('ROLE_ADMIN'))
        ){
            $em = $this->getDoctrine()->getEntityManager();
            $request  = $this->getRequest();

            if ($security->isGranted('ROLE_ASSESSOR') === false)   $form = $this->createForm(new CloseTicketWorkshopType(), $ticket);
            else                                                   $form = $this->createForm(new CloseTicketType()        , $ticket);

            if ($request->getMethod() == 'POST') {
                $form->bindRequest($request);

                /*Validacion Ticket*/
                $str_len = strlen($ticket->getSolution());
                if($security->isGranted('ROLE_ASSESSOR')) { $max_len = 10000; }
                else { $max_len = 500; }

                if ($str_len <= $max_len ) {

                    if ($form->isValid()) {

                        if ($security->isGranted('ROLE_ASSESSOR') === false) {
                            if     ($ticket->getSolution() == "0") $ticket->setSolution($this->get('translator')->trans('ticket.close_as_instructions'));
                            elseif ($ticket->getSolution() == "1") $ticket->setSolution($this->get('translator')->trans('ticket.close_irreparable_car'));
                            elseif ($ticket->getSolution() == "2") $ticket->setSolution($this->get('translator')->trans('ticket.close_other').': '.$request->get('sol_other_txt'));
                        }

                        if($ticket->getSolution() != ""){

                            $closed = $em->getRepository('TicketBundle:Status')->findOneByName('closed');
                            $user   = $security->getToken()->getUser();
                            $ticket->setStatus($closed);
                            $ticket->setPending(0);
                            $ticket->setBlockedBy(null);

                            UtilController::saveEntity($em, $ticket, $user);

                            $mail = $ticket->getWorkshop()->getEmail1();
                            $pos = strpos($mail, '@');
                            if ($pos != 0) {

                                // Cambiamos el locale para enviar el mail en el idioma del taller
                                $locale = $request->getLocale();
                                $lang_w = $ticket->getWorkshop()->getCountry()->getLang();
                                $lang   = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_w);
                                $request->setLocale($lang->getShortName());

                                /* MAILING */
                                $mailer = $this->get('cms.mailer');
                                $mailer->setTo($mail);
                                $mailer->setSubject($this->get('translator')->trans('mail.closeTicket.subject').$id);
                                $mailer->setFrom('noreply@adserviceticketing.com');
                                $mailer->setBody($this->renderView('UtilBundle:Mailing:ticket_close_mail.html.twig', array('ticket' => $ticket)));
                                $mailer->sendMailToSpool();
                                //echo $this->renderView('UtilBundle:Mailing:ticket_close_mail.html.twig', array('ticket' => $ticket));die;

                                // Dejamos el locale tal y como estaba
                                $request->setLocale($locale);
                            }
                            //Si es el taller el que cierra, se le envia un mail al asesor asignado
                            if ($ticket->getAssignedTo() != null) {
                                $mail = $ticket->getAssignedTo()->getEmail1();
                                $pos = strpos($mail, '@');
                                if ($pos != 0) {

                                    // Cambiamos el locale para enviar el mail en el idioma del taller
                                    $locale = $request->getLocale();
                                    $lang_w = $ticket->getWorkshop()->getCountry()->getLang();
                                    $lang   = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_w);
                                    $request->setLocale($lang->getShortName());

                                    /* MAILING */
                                    $mailer = $this->get('cms.mailer');
                                    $mailer->setTo($mail);
                                    $mailer->setSubject($this->get('translator')->trans('mail.closeTicket.subject').$id);
                                    $mailer->setFrom('noreply@adserviceticketing.com');
                                    $mailer->setBody($this->renderView('UtilBundle:Mailing:ticket_close_mail.html.twig', array('ticket' => $ticket)));
                                    $mailer->sendMailToSpool();
                                    //echo $this->renderView('UtilBundle:Mailing:ticket_close_mail.html.twig', array('ticket' => $ticket));die;

                                    // Dejamos el locale tal y como estaba
                                    $request->setLocale($locale);
                                }
                            }

                            return $this->redirect($this->generateUrl('showTicket', array('id' => $id) ));
                        }
                        else{
                            $this->get('session')->setFlash('error', $this->get('translator')->trans('error.msg_solution'));
                        }
                    }else{
                        $this->get('session')->setFlash('error', $this->get('translator')->trans('error.bad_introduction'));
                    }
                }else{ $this->get('session')->setFlash('error', $this->get('translator')->trans('error.txt_length').' '.$max_len.' '.$this->get('translator')->trans('error.txt_chars').'.'); }
            }

            $systems = $em->getRepository('TicketBundle:System')->findAll();

            return $this->render('TicketBundle:Layout:close_ticket_layout.html.twig', array('ticket'    => $ticket,
                                                                                            'systems'   => $systems,
                                                                                            'form'      => $form->createView(),
                                                                                            'form_name' => $form->getName(), ));
        }
        else{
            return $this->render('TwigBundle:Exception:exception_access.html.twig');
        }
    }

    /**
     * Edita la descripcion del ticket
     * @Route("/ticket/editDescription/{id}")
     * @ParamConverter("ticket", class="TicketBundle:Ticket")
     * @return url
     */
    public function editDescriptionAction($id, $ticket)
    {
        $security = $this->get('security.context');
        if ($security->isGranted('ROLE_ASSESSOR'))
        {
            $em = $this->getDoctrine()->getEntityManager();
            $request  = $this->getRequest();

            $form = $this->createForm(new EditDescriptionType(), $ticket);

            if ($request->getMethod() == 'POST') {
                $form->bindRequest($request);

                /*Validacion Ticket*/
                $str_len = strlen($ticket->getSolution());
                $max_len = 10000;

                if ($str_len <= $max_len ) {

                    if ($form->isValid()) {

                        if($ticket->getDescription() != ""){

                            $user   = $security->getToken()->getUser();

                            UtilController::saveEntity($em, $ticket, $user);

                            return $this->redirect($this->generateUrl('showTicket', array('id' => $id) ));
                        }
                        else{
                            $this->get('session')->setFlash('error', $this->get('translator')->trans('error.msg_solution'));
                        }
                    }else{
                        $this->get('session')->setFlash('error', $this->get('translator')->trans('error.bad_introduction'));
                    }
                }else{ $this->get('session')->setFlash('error', $this->get('translator')->trans('error.txt_length').' '.$max_len.' '.$this->get('translator')->trans('error.txt_chars').'.'); }
            }

            return $this->render('TicketBundle:Layout:edit_description_layout.html.twig', array('ticket'    => $ticket,
                                                                                                'form'      => $form->createView(),
                                                                                                'form_name' => $form->getName(), ));
        }
        else{
            return $this->render('TwigBundle:Exception:exception_access.html.twig');
        }
    }

    /**
     * Muestra una lista de motores
     * @return url
     */
    public function listMotorsAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $motors = $em->getRepository('CarBundle:Motor')->findBy(array(), array('name' => 'ASC'));

        $query   = $em->createQuery('SELECT m FROM CarBundle:Version v, CarBundle:Motor m
                                        WHERE m.id = v.motor
                                        AND v.motor IN (
                                            SELECT mt FROM CarBundle:Motor mt)
                                        GROUP BY m.name
                                        ORDER BY m.name');
        $motors    = $query->getResult();

        return $this->render('TicketBundle:Layout:show_motors_layout.html.twig', array('motors' => $motors));
    }

    /**
     * Reabre el ticket
     * @Route("/ticket/reopen/{id}/")
     * @ParamConverter("ticket", class="TicketBundle:Ticket")
     * @return url
     */
    public function reopenTicketAction($id, $ticket)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $security = $this->get('security.context');
        $request  = $this->getRequest();

        $user = $security->getToken()->getUser();
        $status = $em->getRepository('TicketBundle:Status')->findOneByName('open');

        $ticket->setStatus($status);
        $ticket->setPending($status);
        UtilController::saveEntity($em, $ticket, $user);

        $mail = $ticket->getWorkshop()->getEmail1();
        $pos = strpos($mail, '@');
        if ($pos != 0) {

            // Cambiamos el locale para enviar el mail en el idioma del taller
            $locale = $request->getLocale();
            $lang_w = $ticket->getWorkshop()->getCountry()->getLang();
            $lang   = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_w);
            $request->setLocale($lang->getShortName());

             /* MAILING */
            $mailer = $this->get('cms.mailer');
            $mailer->setTo($mail);
            $mailer->setSubject($this->get('translator')->trans('mail.reopenTicket.subject').$id);
            $mailer->setFrom('noreply@adserviceticketing.com');
            $mailer->setBody($this->renderView('UtilBundle:Mailing:ticket_reopen_mail.html.twig', array('ticket' => $ticket)));
            $mailer->sendMailToSpool();
            //echo $this->renderView('UtilBundle:Mailing:ticket_reopen_mailecho 'pasa';.html.twig', array('ticket' => $ticket));die;

            // Dejamos el locale tal y como estaba
            $request->setLocale($locale);
        }

        return $this->redirect($this->generateUrl('showTicket', array('id' => $id) ));
    }

    /**
     * Obtiene todos los talleres del usuario logeado
     */
    public function workshopListAction($page=1 , $option=null)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $params[] = array();

        $pagination = new Pagination($page);

        $workshops = $pagination->getRows($em, 'WorkshopBundle', 'Workshop', $params, $pagination);

        $length = $pagination->getRowsLength($em, 'WorkshopBundle', 'Workshop', $params);

        $pagination->setTotalPagByLength($length);

        return $this->render('TicketBundle:Workshop:list_workshop.html.twig', array( 'workshops'  => $workshops,
                                                                                     'pagination' => $pagination));
    }

    /**
     * A partir de un $id_taller, la vista listará todos sus tickets i se le podrá asignar un usuario
     * @param Int $id_workshop
     * @return type
     */
    public function getTicketsFromWorkshopAction($id_workshop, $page=1)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $params = array();
        $params[] = array('workshop',' = '.$id_workshop);

        $pagination = new Pagination($page);

        $tickets = $pagination->getRows($em, 'TicketBundle', 'Ticket', $params, $pagination);

        $length = $pagination->getRowsLength($em, 'TicketBundle', 'Ticket', $params);

        $pagination->setTotalPagByLength($length);

        return $this->render('TicketBundle:Workshop:ticketsFromWorkshop.html.twig', array('tickets' => $tickets));
    }

    /**
     * Asigna un ticket a un usuario si se le pasa un $id_usuario, sino se pone a null
     * @Route("/ticket/assign/{id}/")
     * @ParamConverter("ticket", class="TicketBundle:Ticket")
     * @param Int $id puede venir por POST o por parametro de la funcion
     * @param Int $id_user
     */
    public function assignUserToTicketAction($ticket, $id_user = null)
    {
        $em = $this->getDoctrine()->getEntityManager();

        //id_user puede venir por parametro o por post
        if ($id_user == null) {
            $petition = $this->getRequest();
            $id_user = $petition->get('id_user');
        }

        //si $id_user != null ---> viene de parametro de la funcion o de POST y queremos asignar
        //si $id_user == null ---> queremos desasignar
        if ($id_user != null) {
            $user = $em->getRepository('UserBundle:User')->find($id_user);
            $this->assignTicket($ticket, $user);
        }else{
            $this->assignTicket($ticket, null);
        }

        $workshop = $em->getRepository('WorkshopBundle:Workshop')->find($ticket->getWorkshop()->getId());
        return $this->render('TicketBundle:Workshop:ticketsFromWorkshop.html.twig', array('tickets' => $workshop->getTickets()));
    }

    /**
     * Busca los posibles usuarios al cual podemos asingar un ticket
     * @Route("/ticket/assingTicket/{id}/")
     * @ParamConverter("ticket", class="TicketBundle:Ticket")
     * @return type
     */
    public function assignTicketSelectUserAction($ticket)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $users = $this->getUsersToAssingFromTicket();

        return $this->render('TicketBundle:Ticket:assign_ticket.html.twig', array('ticket' => $ticket,
                                                                                  'users' => $users
                                                                                  ));
    }

    /**
     * Bloquea un ticket al asesor para que conteste
     * @Route("/ticket/assignAssesor/{id}/{id_user}/")
     * @ParamConverter("ticket", class="TicketBundle:Ticket")
     * @param Int $id puede venir por POST o por parametro de la funcion
     * @param Int $id_user
     */
    public function blockTicketAction($ticket, $id_user = null)
    {

        $em = $this->getDoctrine()->getEntityManager();
        $user = $em->getRepository('UserBundle:User')->find($id_user);

        if ($user != null and $id_user != 0) {

            $ticket->setBlockedBy($user);
            $this->assignTicket($ticket, $user);

            UtilController::saveEntity($em, $ticket, $user);

            if ($ticket->getAssignedTo() == null) {
                $this->assignTicket($ticket, $user);
            }else {
                $em->persist($ticket);
                $em->flush();
            }
        }else{
            $ticket->setBlockedBy(null);
            $em->persist($ticket);
            $em->flush();
        }

        return $this->showTicketAction($ticket);
    }

    /**
     * Funcion que devuelve un listado de tickets filtrados a partir de una opcion de un combo ($option)
     * @return array
     */
    public function getTicketsByOption($option)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();
        $security = $this->get('security.context');

        $tickets    = array();
        $check_id   = $petition->request->get('filter_id');
        $user       = $security->getToken()->getUser();
        $repoTicket = $em->getRepository('TicketBundle:Ticket');
        $open       = $em->getRepository('TicketBundle:Status')->findOneBy(array('name' => 'open'  ));
        $closed     = $em->getRepository('TicketBundle:Status')->findOneBy(array('name' => 'closed'));

        if($security->isGranted('ROLE_ADMIN')){
            //Admin
            if     ($option == 'all'     ) { $tickets = $repoTicket->findAll();                      }
            elseif ($option == 'opened'  ) { $tickets = $repoTicket->findAllStatus   ($em, $open);   }
            elseif ($option == 'closed'  ) { $tickets = $repoTicket->findAllStatus   ($em, $closed); }
            elseif ($option == 'free'    ) { $tickets = $repoTicket->findAllFree     ($em, $open);   }
            elseif ($option == 'pending' ) { $tickets = $repoTicket->findAllPending  ($em, $open);   }
            elseif ($option == 'answered') { $tickets = $repoTicket->findAllAnswered ($em, $open);   }

            if($check_id != 'all') { $tickets = $this->filterTickets($tickets,$check_id); }

        }elseif($security->isGranted('ROLE_ASSESSOR')){
                //Assessor
                if     ($option == 'free'              ) { $tickets = $repoTicket->findAllFree     ($em, $open);   }
                elseif ($option == 'assessor_pending'  ) { $tickets = $repoTicket->findAllFromUser ($em, $open, true, true, $user, true);         }
                elseif ($option == 'assessor_answered' ) { $tickets = $repoTicket->findOption($em, $user, $open  , 'assessor_answered', 'DESC' ); }
                elseif ($option == 'assessor_closed'   ) { $tickets = $repoTicket->findOption($em, $user, $closed, 'assessor_closed'           ); }
                elseif ($option == 'other_pending'     ) { $tickets = $repoTicket->findOption($em, $user, $open  , 'other_pending'             ); }
                elseif ($option == 'other_answered'    ) { $tickets = $repoTicket->findOption($em, $user, $open  , 'other_answered', 'DESC'    ); }
                elseif ($option == 'other_closed'      ) { $tickets = $repoTicket->findOption($em, $user, $closed, 'other_closed'              ); }
                elseif ($option == 'all'               ) { $tickets = $repoTicket->findAll();                                                         }

                if($check_id != 'all') { $tickets = $this->filterTickets($tickets,$check_id); }

        }else{

            if($check_id == 'all'){

                $check_status = $petition->request->get('status');

                if     ($check_status == 'all'   ) { $status = 'all';   }
                elseif ($check_status == 'open'  ) { $status = $open;   }
                elseif ($check_status == 'closed') { $status = $closed; }

                //User
                if ($option == 'created_by'    ) $tickets = $repoTicket->findAllByOwner($user, $status);
                elseif ($option == 'workshop'  ) $tickets = $repoTicket->findAllByWorkshop($user, $status);
            }else{
                $array  = array('id' => $check_id);
                $tickets = $repoTicket->findBy($array);
            }
        }
        return $tickets;
    }

    /**
     * Devuelve un ticket segun la id enviada por parametro
     * @return url
     */
    public function findTicketByIdAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $security   = $this->get('security.context');
        $request  = $this->getRequest();
        $id       = $request->get('flt_id');

        $ticket   = $em->getRepository('TicketBundle:Ticket')->find($id);

        if($ticket and ($ticket->getWorkshop()->getCountry()->getId() == $security->getToken()->getUser()->getCountry()->getId()))
             $tickets = array($ticket);
        else $tickets = array();

        $b_query   = $em->createQuery('SELECT b FROM CarBundle:Brand b, CarBundle:Model m WHERE b.id = m.brand ORDER BY b.name');
        $brands    = $b_query->getResult();
        $systems    = $em->getRepository('TicketBundle:System')->findBy(array(), array('name' => 'ASC'));
        $countries  = $em->getRepository('UtilBundle:Country')->findAll();
        $importances = $em->getRepository('TicketBundle:Importance')->findAll();

        $array = array('workshop'   => new Workshop(),
                       'pagination' => new Pagination(),
                       'tickets'    => $tickets,
                       'brands'     => $brands,
                       'systems'    => $systems,
                       'countries'  => $countries,
                       'importances' => $importances,
                       'option'     => 'all',
                       'page'       => 0,
                       'num_rows'   => 10,
                       'country'    => 0,
                       'inactive'   => 0,
                       'disablePag' => 0);

        if($security->isGranted('ROLE_ASSESSOR') and !$security->isGranted('ROLE_ADMIN'))
                return $this->render('TicketBundle:Layout:list_ticket_assessor_layout.html.twig', $array);
        else    return $this->render('TicketBundle:Layout:list_ticket_layout.html.twig', $array);
    }

    /**
     * Devuelve un ticket segun la id enviada por parametro
     * @return url
     */
    public function findTicketByBMVAction($page=1, $brand=0, $model=0, $version=0,
                                                   $system=0, $subsystem=0, $importance=0,
                                                   $year=0, $motor=0, $kw=0, $num_rows=10)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $security   = $this->get('security.context');
        $params = array();

        if($brand   != '0' and $brand   != '') $params[] = array('brand',' = '.$brand);
        if($model   != '0' and $model   != '') $params[] = array('model',' = '.$model);
        if($version != '0' and $version != '') $params[] = array('version',' = '.$version);

        if($year    != '0' and $year    != '') $params[] = array('year'," LIKE '%".$year."%' ");
        if($motor   != '0' and $motor   != '') $params[] = array('motor'," LIKE '%".$motor."%' ");
        if($kw      != '0' and $kw      != '') $params[] = array('kw',' = '.$kw);

        $pagination = new Pagination($page);

        // if($num_rows != 10) { $pagination->setMaxRows($num_rows); }
        // Seteamos el numero de resultados que se mostraran
        $pagination->setMaxRows(50);

        $cars = $pagination->getRows($em, 'CarBundle', 'Car', $params, $pagination);

        $length = $pagination->getRowsLength($em, 'CarBundle', 'Car', $params);

        $pagination->setTotalPagByLength($length);

        $tickets = array();

        $key = array_keys($cars);
        $size = sizeOf($key);
        if($size > 0){

            for ($i=0; $i<$size; $i++){

                $id     = $cars[$key[$i]]->getId();
               if( $subsystem == 0) {
                    $ticket = $em->getRepository('TicketBundle:Ticket')->findOneBy(array('car' => $id));
                }
                else {
                    $ticket = $em->getRepository('TicketBundle:Ticket')->findOneBy(array('car' => $id,'subsystem' => $subsystem));
                }

            }
        }
        else {
            $ticket = $em->getRepository('TicketBundle:Ticket')->findOneBy(array('subsystem' => $subsystem));
        }
        if($ticket and ($ticket->getWorkshop()->getCountry()->getId() == $security->getToken()->getUser()->getCountry()->getId() or $security->isGranted('ROLE_SUPER_ADMIN'))){
            $tickets[] = $ticket;
        }
        $b_query     = $em->createQuery('SELECT b FROM CarBundle:Brand b, CarBundle:Model m WHERE b.id = m.brand ORDER BY b.name');
        $brands      = $b_query->getResult();
        $systems     = $em->getRepository('TicketBundle:System')->findBy(array(), array('name' => 'ASC'));
        $countries   = $em->getRepository('UtilBundle:Country')->findAll();
        $importances = $em->getRepository('TicketBundle:Importance')->findAll();

        if (isset($ticket)) $adsplus = $em->getRepository('WorkshopBundle:ADSPlus'  )->findOneBy(array('idTallerADS'  => $ticket->getWorkshop()->getId() ));
        else $adsplus = null;

        $array = array('workshop'    => new Workshop(),
                       'pagination'  => new Pagination(0),
                       'brand'       => $brand,
                       'model'       => $model,
                       'version'     => $version,
                       'system'      => $system,
                       'subsystem'   => $subsystem,
                       'importance'  => $importance,
                       'year'        => $year,
                       'motor'       => $motor,
                       'kw'          => $kw,
                       'num_rows'    => $num_rows,
                       'tickets'     => $tickets,
                       'brands'      => $brands,
                       'systems'     => $systems,
                       'countries'   => $countries,
                       'adsplus'     => $adsplus,
                       'importances' => $importances,
                       'option'      => 'all',
                       'page'        => $page,
                       'country'     => 0,
                       'inactive'    => 0,
                       'disablePag'  => 0);

        if($security->isGranted('ROLE_ASSESSOR') and !$security->isGranted('ROLE_ADMIN'))
                return $this->render('TicketBundle:Layout:list_ticket_assessor_layout.html.twig', $array);
        else    return $this->render('TicketBundle:Layout:list_ticket_layout.html.twig', $array);
    }

    /**
     * Devuelve un ticket segun la id enviada por parametro
     * @return url
     */
    public function findAssessorTicketByBMVAction($page=null)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $security   = $this->get('security.context');
        $request    = $this->getRequest();
        $params = array();

        // PAGE
        if(!isset($page)) $page = $request->request->get('ftbmv_page');
        if(!isset($page)) $page = 1;

        // WORKSHOP
        $codepartner  = $request->get('ftbmv_codepartner');
        $codeworkshop = $request->get('ftbmv_codeworkshop');
        $email = $request->get('ftbmv_email');
        $phone = $request->get('ftbmv_phone');

        $workshop = new Workshop();

        if (isset($codepartner) and isset($codeworkshop) and $codepartner != '' and $codeworkshop != ''){

            $partner = $em->getRepository('PartnerBundle:Partner')->findOneBy(array('code_partner' => $codepartner));
            $workshop = $em->getRepository('WorkshopBundle:Workshop')->findOneBy(array('code_workshop' => $codeworkshop, 'partner' => $partner->getId()));
        }

        // CAR
        $brand = $request->request->get('new_car_form_brand');
        $model = $request->request->get('new_car_form_model');
        $version = $request->request->get('new_car_form_version');
        $year  = $request->get('new_car_form_year');
        $motor = $request->get('new_car_form_motor');
        $kw = $request->request->get('new_car_form_kW');
        $importance = $request->request->get('new_car_form_importance');
        $system = $request->request->get('id_system');
        $subsystem = $request->request->get('new_car_form_subsystem');
        $displacement = $request->request->get('new_car_form_displacement');
        $vin = $request->request->get('new_car_form_vin');
        $plateNumber = $request->request->get('new_car_form_plateNumber');
        $num_rows = $request->request->get('slct_numRows');
        if(!isset($num_rows)) $num_rows = 10;

        if(isset($brand)   and $brand   != '0' and $brand   != '') $params[] = array('brand',' = '.$brand);
        if(isset($model)   and $model   != '0' and $model   != '') $params[] = array('model',' = '.$model);
        if(isset($version) and $version != '0' and $version != '') $params[] = array('version',' = '.$version);

        $pagination = new Pagination($page);

        // if($num_rows != 10) { $pagination->setMaxRows($num_rows); }
        // Seteamos el numero de resultados que se mostraran
        $pagination->setMaxRows(10);

        $cars = $pagination->getRows($em, 'CarBundle', 'Car', $params, $pagination);

        $length = $pagination->getRowsLength($em, 'CarBundle', 'Car', $params);

        $pagination->setTotalPagByLength($length);

        $tickets = array();

        $key = array_keys($cars);
        $size = sizeOf($key);

        if($size > 0){

            for ($i=0; $i<$size; $i++){

                $id     = $cars[$key[$i]]->getId();
                if( $subsystem == 0 or $subsystem == '') $ticket = $em->getRepository('TicketBundle:Ticket')->findOneBy(array('car' => $id));
                else                 $ticket = $em->getRepository('TicketBundle:Ticket')->findOneBy(array('car' => $id,'subsystem' => $subsystem));

                if($ticket and ($ticket->getWorkshop()->getCountry()->getId() == $security->getToken()->getUser()->getCountry()->getId() or $security->isGranted('ROLE_ASSESSOR'))){
                    $w_id = $workshop->getId();

                    if(isset($w_id)) { if($workshop->getId() == $ticket->getWorkshop()->getId()) $tickets[] = $ticket; }
                    else $tickets[] = $ticket;
                }
            }
        }

        // Se crea una segunda paginacion que servirá para calcular el numero real de tickets de la paginacion, ya que despues de la consulta se filtra por taller
        // Busca la ultima pagina del listado, y calcula la longitud total despues de restar los registros que no coinciden con el taller
        $pagination2 = new Pagination($pagination->getTotalPag());
        $pagination2->setMaxRows(10);

        if (sizeof($cars) > 0){
            $cars2 = $pagination2->getRows($em, 'CarBundle', 'Car', $params, $pagination2);
            $length2 = $pagination2->getRowsLength($em, 'CarBundle', 'Car', $params);
        }else{
            $cars2 = array();
            $length2 = array();
        }

        $key2 = array_keys($cars2);
        $size2 = sizeOf($key2);
        if($size2 > 0){

            for ($i=0; $i<$size2; $i++){

                $id2     = $cars2[$key2[$i]]->getId();
                if( $subsystem == 0 or $subsystem == '') $ticket2 = $em->getRepository('TicketBundle:Ticket')->findOneBy(array('car' => $id2));
                else $ticket2 = $em->getRepository('TicketBundle:Ticket')->findOneBy(array('car' => $id2,'subsystem' => $subsystem));

                if($ticket2 and ($ticket2->getWorkshop()->getCountry()->getId() == $security->getToken()->getUser()->getCountry()->getId() or $security->isGranted('ROLE_SUPER_ADMIN'))){
                    $w_id2 = $workshop->getId();
                    if(isset($w_id2)) { if($workshop->getId() != $ticket2->getWorkshop()->getId()) $length2--; }
                }
            }
        }
        else {
            $ticket2 = $em->getRepository('TicketBundle:Ticket')->findOneBy(array('subsystem' => $subsystem));
            if($ticket2 and ($ticket2->getWorkshop()->getCountry()->getId() == $security->getToken()->getUser()->getCountry()->getId() or $security->isGranted('ROLE_SUPER_ADMIN'))){
                $w_id2 = $workshop->getId();
                if(isset($w_id2)) { if($workshop->getId() != $ticket2->getWorkshop()->getId()) $length2--; }
            }
        }

        if ($length2 <= $pagination2->getFirstRow()){
            $pagination->setTotalPagByLength($length2);
        }

        $b_query   = $em->createQuery('SELECT b FROM CarBundle:Brand b, CarBundle:Model m WHERE b.id = m.brand ORDER BY b.name');
        $brands    = $b_query->getResult();
        $systems    = $em->getRepository('TicketBundle:System')->findBy(array(), array('name' => 'ASC'));
        $countries  = $em->getRepository('UtilBundle:Country')->findAll();
        $importances = $em->getRepository('TicketBundle:Importance')->findAll();

        if (isset($ticket)) $adsplus = $em->getRepository('WorkshopBundle:ADSPlus'  )->findOneBy(array('idTallerADS'  => $ticket->getWorkshop()->getId() ));
        else $adsplus = null;

        if(isset($model) and $model != '0') $model = $em->getRepository('CarBundle:Model'  )->find($model);
        if(isset($version) and $version != '0') $version = $em->getRepository('CarBundle:Version'  )->findById($version);

        if(isset($subsystem) and $subsystem != '0' and $subsystem != '')
            $subsystem = $em->getRepository('TicketBundle:Subsystem'  )->find($subsystem);

        if (sizeof($tickets) == 0) $pagination = new Pagination(0);

        $array = array('workshop'    => $workshop,
                       'pagination'  => $pagination,
                       'codepartner' => $codepartner,
                       'codeworkshop'=> $codeworkshop,
                       'email'       => $email,
                       'phone'       => $phone,
                       'brand'       => $brand,
                       'model'       => $model,
                       'version'     => $version,
                       'year'        => $year,
                       'motor'       => $motor,
                       'kw'          => $kw,
                       'importance'  => $importance,
                       'system'      => $system,
                       'subsystem'   => $subsystem,
                       'displacement'=> $displacement,
                       'vin'         => $vin,
                       'plateNumber' => $plateNumber,
                       'tickets'     => $tickets,
                       'brands'      => $brands,
                       'systems'     => $systems,
                       'countries'   => $countries,
                       'adsplus'     => $adsplus,
                       'importances' => $importances,
                       'option'      => 'all',
                       'page'        => $page,
                       'num_rows'    => $num_rows,
                       'country'     => 0,
                       'inactive'    => 0,
                       'disablePag'  => 0);

        if($security->isGranted('ROLE_ASSESSOR') and !$security->isGranted('ROLE_ADMIN'))
                return $this->render('TicketBundle:Layout:list_ticket_assessor_layout.html.twig', $array);
        else    return $this->render('TicketBundle:Layout:list_ticket_layout.html.twig', $array);
    }

    /**
     * Devuelve todos los usuarios que podran ser asignados a un ticket (admins i asesores has nuevo aviso)
     * @param type $id_ticket
     */
    private function getUsersToAssingFromTicket()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $query    = "SELECT u FROM UserBundle:User u INNER JOIN u.user_role r WHERE r.name = 'ROLE_ASSESSOR' AND u.active = 1 ORDER BY u.name ASC";
        $consulta = $em->createQuery($query);
        return $consulta->getResult();
    }

    /**
     * Asigna un $ticket a un $user
     * Si $user == NULL, se desasigna
     * @param Ticket $ticket
     * @param User $user
     */
    private function assignTicket($ticket, $user=null)
    {
        $em = $this->getDoctrine()->getEntityManager();

        ($user != null) ? $ticket->setAssignedTo($user) : $ticket->setAssignedTo(null);

        $em->persist($ticket);
        $em->flush();
    }

    /**
     * Filtra un array de tickets en funcion del id
     * @param  Array   $tickets
     * @param  Integer $check_id
     * @return Array
     */
    private function filterTickets($tickets,$check_id)
    {
        $tickets_filtered = array();

        foreach ($tickets as $ticket) {

            if($ticket->getId() == $check_id)
                $tickets_filtered[] = $ticket;
        }
        return $tickets_filtered;
    }

// /**
    //  * Devuelve todos los tickets realizados
    //  * @return url
    //  */
    // public function listTicketFilteredAction($page=1, $id_workshop='none', $id_ticket='none', $status='all', $option='all')
    // {
    //     $em = $this->getDoctrine()->getEntityManager();
    //     $request  = $this->getRequest();
    //     $workshop = new Workshop();
    //     $tickets  = array();
    //     $params   = array();

    //     if($id_ticket   != 'none') $params[] = array('id'    , ' = '.$id_ticket  );
    //     if($id_workshop != 'none')  {
    //                                     $workshop = $em->getRepository('WorkshopBundle:Workshop')->find($id_workshop);
    //                                     $params[] = array('workshop'    , ' = '.$id_workshop );
    //                                 }
    //     if($status      != 'all' )  {
    //                                     $id_status = $em->getRepository('TicketBundle:Status')->findOneBy(array('name' => $status))->getId();
    //                                     $params[] = array('status', " = '".$id_status."'");
    //                                 }

    //     $pagination = new Pagination($page);

    //     $tickets = $pagination->getRows($em, 'TicketBundle', 'Ticket', $params, $pagination);

    //     $length  = $pagination->getRowsLength($em, 'TicketBundle', 'Ticket', $params);

    //     $pagination->setTotalPagByLength($length);

    //     return $this->render('TicketBundle:Layout:list_ticket_layout.html.twig', array('workshop'   => $workshop,
    //                                                                                    'pagination' => $pagination,
    //                                                                                    'tickets'    => $tickets,
    //                                                                                    'option'     => $option,
    //                                                                           ));
    // }
}
