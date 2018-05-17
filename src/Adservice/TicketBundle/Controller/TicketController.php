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
    public function callTicketAction(Request $request, $code_partner = 0, $code_workshop = 0) {

        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR'))
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $array = array();

        if ($code_partner != 0 and $code_workshop != 0) {
            $workshop = $em->getRepository('WorkshopBundle:Workshop')->findOneBy(array('code_partner' => $code_partner,
                'code_workshop' => $code_workshop));

            if (isset($workshop))
                $array = array('page' => 1, 'num_rows' => 10, 'country' => 0, 'option' => 'all', 'workshop_id' => $workshop->getId());
            else
                $array = array('page' => 1, 'num_rows' => 10, 'country' => 0, 'option' => 'all');
        }
        return $this->redirect($this->generateUrl('listTicket', $array));
    }

    /**
     * Devuelve el listado de tickets segunla pagina y la opcion escogida
     * @return url
     */
    public function listTicketAction(Request $request, $page = 1, $num_rows = 10, $country = 0, $lang = 0, $catserv = 0, $option = null, $workshop_id = null, $adviser_id = null) {
        $em = $this->getDoctrine()->getManager();

        
        $user = $this->getUser();
        $catservId = $catserv ;
       
        
        if(!$user) return $this->redirect($this->generateUrl('user_login'));

        $id_user = $user->getId();
        $open = $em->getRepository('TicketBundle:Status')->findOneBy(array('name' => 'open'));
        $closed = $em->getRepository('TicketBundle:Status')->findOneBy(array('name' => 'closed'));
        $inactive = $em->getRepository('TicketBundle:Status')->findOneBy(array('name' => 'inactive'));
        $expirated = $em->getRepository('TicketBundle:Status')->findOneBy(array('name' => 'expirated'));
        $params = array();
        $joins = array();

        /* Entrada desde Centralita al listado con el taller seleccionado */
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR') and $workshop_id != null) {
            $workshop = $em->getRepository('WorkshopBundle:Workshop')->find($workshop_id);
            $workshops = array('0' => $workshop);
        } else
            $workshops = array('0' => new Workshop());

        /* TRATAMIENTO DE LAS OPCIONES DE slct_historyTickets */
        $this->get('session')->getFlashBag()->add('error', null);
        if ($option == null) {
            // Si se envia el codigo del taller se buscan los tickets en funcion de estos
            if ($request->getMethod() == 'POST') {
                $workshops = $em->getRepository('WorkshopBundle:Workshop')->findWorkshopInfo($request, $this->getUser()->getCategoryService());

                if (!empty($workshops)) {
                    if ($workshops[0]->getActive() == 0) {
                        $error = $this->get('translator')->trans('workshop_inactive');
                        $this->get('session')->getFlashBag()->add('error', '¡Error! ' . $error);
                    }

                    if (isset($workshops[0]) and $workshops[0]->getId() != "") {
                        $joins[] = array('e.workshop w ', 'w.code_workshop = ' . $workshops[0]->getCodeWorkshop() . " AND w.code_partner = " . $workshops[0]->getCodepartner() . " ");
                        $option = $workshops[0]->getId();
                    } elseif (isset($workshops['error'])) {
                        $error = $this->get('translator')->trans('workshop_inactive');
                        $this->get('session')->getFlashBag()->add('error', '¡Error! ' . $error);
                    } else {
                        $joins[] = array();
                    }
                } else {
                    $error = $this->get('translator')->trans('workshop_inactive');
                    $this->get('session')->getFlashBag()->add('error', '¡Error! ' . $error);
                }
            } elseif (!$this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR') and !$this->get('security.authorization_checker')->isGranted('ROLE_COMMERCIAL')) {
                $workshops = $em->getRepository('WorkshopBundle:Workshop')->findBy(array('id' => $user->getWorkshop()->getId()));

                if ($workshops[0]->getId() != "") {
                    $joins[] = array('e.workshop w ', 'w.code_workshop = ' . $workshops[0]->getCodeWorkshop() . " AND w.code_partner = " . $workshops[0]->getCodepartner() . " ");
                    $option = $workshops[0]->getCodeWorkshop();
                }
            }
            //$option = 'all';
        } elseif ($option == 'all') {
            $params[] = array();
        } elseif ($option == 'all_mine') {
            $params[] = array('assigned_to', '= ' . $id_user);
        } elseif ($option == 'opened') {
            $params[] = array('status', ' IN (' . $open->getId() .', '. $inactive->getId() .', '. $expirated->getId().' )');
        } elseif ($option == 'closed') {
            $params[] = array('status', ' = ' . $closed->getId());
        } elseif ($option == 'free') {
            $params[] = array('status', ' IN (' . $open->getId() .', '. $inactive->getId() .', '. $expirated->getId().' )');
            $params[] = array('assigned_to ', 'IS NULL');
        } elseif ($option == 'pending') {
            $params[] = array('status', ' IN (' . $open->getId() .', '. $inactive->getId() .', '. $expirated->getId().' )');
            $params[] = array('assigned_to', 'IS NOT NULL');
            $params[] = array('pending', '= 1');
        } elseif ($option == 'answered') {
            $params[] = array('status', ' IN (' . $open->getId() .', '. $inactive->getId() .', '. $expirated->getId().' )');
            $params[] = array('assigned_to', 'IS NOT NULL');
            $params[] = array('pending', '= 0');
        } elseif ($option == 'assessor_pending') {
            $params[] = array('status', ' IN (' . $open->getId() .', '. $inactive->getId() .', '. $expirated->getId().' )');

            if($user->getCountryService() != null) $country_service = $user->getCountryService()->getId();
            else $country_service = '0';
            // Esto hacía que se vieran los libres(rojo) además de los pendientes(naranja)
            if ($country_service == '7')
                $params[] = array('id', ' != 0 AND e.assigned_to = ' . $id_user . ' AND e.pending = 1 '); // OR (e.assigned_to IS NULL AND w.country IN (5,6) AND e.status = 1)');
            else
                $params[] = array('id', ' != 0 AND e.assigned_to = ' . $id_user . ' AND e.pending = 1 '); // OR (e.assigned_to IS NULL AND w.country = '.$country_service.' AND e.status = 1)');
        }
        elseif ($option == 'assessor_answered') {
            $params[] = array('status', ' IN (' . $open->getId() .', '. $inactive->getId() .', '. $expirated->getId().' )');
            $params[] = array('assigned_to', '= ' . $id_user);
            $params[] = array('pending', '= 0');
        } elseif ($option == 'other_pending') {
            $params[] = array('status', ' IN (' . $open->getId() .', '. $inactive->getId() .', '. $expirated->getId().' )');
            $params[] = array('assigned_to', '!= ' . $id_user);
            $params[] = array('pending', '= 1');
        } elseif ($option == 'other_answered') {
            $params[] = array('status', ' IN (' . $open->getId() .', '. $inactive->getId() .', '. $expirated->getId().' )');
            $params[] = array('assigned_to', '!= ' . $id_user);
            $params[] = array('pending', '= 0');
        } elseif ($option == 'assessor_closed') {
            $params[] = array('status', ' = ' . $closed->getId());
            $params[] = array('assigned_to', '= ' . $id_user);
        } elseif ($option == 'other_closed') {
            $params[] = array('status', ' = ' . $closed->getId());
            $params[] = array('assigned_to', '!= ' . $id_user);
        } elseif ($option == 'inactive') {
            $params[] = array('status', ' = ' . $inactive->getId());
        }
        elseif ($option == 'expirated')
        {
            $params[] = array('status', ' = ' . $expirated->getId());
            $params[] = array('expiration_date', ' IS NOT NULL ');
        }
        else {
            $workshops = $em->getRepository('WorkshopBundle:Workshop')->findBy(array('id' => $option));
            $params[] = array('workshop', ' = ' . $option);
        }

        if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            if (isset($joins[0][0]) and $joins[0][0] == 'e.workshop w ') {
                if ($country == '7')
                    $joins[0][1] = $joins[0][1] . ' AND w.country IN (5,6) ';
                elseif ($country != 0)
                    $joins[0][1] = $joins[0][1] . ' AND w.country = ' . $country;
                else
                    $joins[0][1] = $joins[0][1] . ' AND w.country != 0';
            }
            else {
                if ($country == '7')
                    $joins[] = array('e.workshop w ', 'w.country IN (5,6) ');
                elseif ($country != 0)
                    $joins[] = array('e.workshop w ', 'w.country = ' . $country);
                else
                    $joins[] = array('e.workshop w ', 'w.country != 0');
            }
        }
        elseif ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') and ! $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            if($country != 'none' && $country != '0') {

                if (isset($joins[0][0]) and $joins[0][0] == 'e.workshop w ') {
                    if ($country == '7')
                        $joins[0][1] = $joins[0][1] . ' AND w.country IN (5,6) ';
                    else
                        $joins[0][1] = $joins[0][1] . ' AND w.country = ' . $country;
                }
                else {
                    if ($country == '7')
                        $joins[] = array('e.workshop w ', 'w.country IN (5,6) ');
                    else
                        $joins[] = array('e.workshop w ', 'w.country = ' . $country);
                }
            }else {
                if (isset($joins[0][0]) and $joins[0][0] == 'e.workshop w ') {
                    $joins[0][1] = $joins[0][1] . ' AND w.country != 0';
                } else {
                    $joins[] = array('e.workshop w ', 'w.country != 0');
                }
            }
        }else {
            if ($country != 'none') {
                if ($country != 0) {
                    if (isset($joins[0][0]) and $joins[0][0] == 'e.workshop w ') {
                        if ($country == '7')
                            $joins[0][1] = $joins[0][1] . ' AND w.country IN (5,6) ';
                        else
                            $joins[0][1] = $joins[0][1] . ' AND w.country = ' . $country;
                    }
                    else {
                        if ($country == '7')
                            $joins[] = array('e.workshop w ', 'w.country IN (5,6) ');
                        else
                            $joins[] = array('e.workshop w ', 'w.country = ' . $country);
                    }
                }else {
                    if ($this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR'))
                        $country = '0'; //$user->getCountryService()->getId();
                    else
                        $country = $user->getCountry()->getId();
                    if ($country != '0') {
                        if (isset($joins[0][0]) and $joins[0][0] == 'e.workshop w ') {
                            if ($country == '7')
                                $joins[0][1] = $joins[0][1] . ' AND w.country IN (5,6) ';
                            else
                                $joins[0][1] = $joins[0][1] . ' AND w.country = ' . $country;
                        }
                        else {
                            if ($country == '7')
                                $joins[] = array('e.workshop w ', 'w.country IN (5,6) ');
                            else
                                $joins[] = array('e.workshop w ', 'w.country = ' . $country);
                        }
                    }
                }
            }else {
                if (isset($joins[0][0]) and $joins[0][0] == 'e.workshop w ') {
                    $joins[0][1] = $joins[0][1] . ' AND w.country != 0';
                } else {
                    $joins[] = array('e.workshop w ', 'w.country != 0');
                }
            }
        }
        $pagination = new Pagination($page);
        $ordered = array('e.modified_at', 'DESC');
        if ($pagination->getMaxRows() != $num_rows)
            $pagination = $pagination->changeMaxRows($page, $num_rows);

        if($this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR') AND $user->getCategoryService() != NULL) {
            $catserv = $user->getCategoryService();
            if($params == null or ($params != null and $params[0] == null)) $params = null;
            $params[] = array('category_service', " = " . $catserv->getId()." ");
        }
        elseif($catserv != 0) {
            $catserv = $em->getRepository('UserBundle:CategoryService')->find($catserv);
            if($params == null or ($params != null and $params[0] == null)) $params = null;
            $params[] = array('category_service', " = " . $catserv->getId()." ");
        }
        if($lang != 0) {
            if($params == null or ($params != null and $params[0] == null)) $params = null;
            $params[] = array('language', " = " . $lang." ");
        }
        if($adviser_id != 0) {
            if($params == null or ($params != null and $params[0] == null)) $params = null;
            $params[] = array('assigned_to', " = " . $adviser_id." ");
        }
        
        if (($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) or ( isset($workshops[0]) and ( $workshops[0]->getId() != null))) {
            $tickets = $pagination->getRows($em, 'TicketBundle', 'Ticket', $params, $pagination, $ordered, $joins);
            $length = $pagination->getRowsLength($em, 'TicketBundle', 'Ticket', $params, $ordered, $joins);
        } elseif (($option == 'assessor_pending') or ( $option == 'assessor_answered') or ( $option == 'other_pending') or ( $option == 'other_answered')) {

            $query = 'SELECT t FROM TicketBundle:Ticket t';
            if (($option == 'assessor_pending') or ( $option == 'assessor_answered'))
                $query = $query . ' WHERE t.assigned_to = ' . $id_user;
            elseif (($option == 'other_pending' ) or ( $option == 'other_answered' ))
                $query = $query . ' WHERE t.assigned_to != ' . $id_user;

            if (($option == 'assessor_pending') or ( $option == 'other_pending'))
                $query = $query . ' AND t.pending = 1';
            elseif (($option == 'assessor_answered') or ( $option == 'other_answered'))
                $query = $query . ' AND t.pending != 1';

            $tickets = $pagination->getRows($em, 'TicketBundle', 'Ticket', $params, $pagination, $ordered, $joins);
            $length = $pagination->getRowsLength($em, 'TicketBundle', 'Ticket', $params, $ordered, $joins);
        }
        else {
            if (isset($workshops[0]) and $workshops[0]->getId()) {
                $joins[] = array('e.workshop w ', 'w.code_workshop = ' . $workshops[0]->getCodeWorkshop() . " AND w.partner = " . $workshops[0]->getPartner()->getid() . " ");
                //$joins[] = array('e.workshop w', ' w.country = '.$user->getCountry()->getId());
                $tickets = $pagination->getRows($em, 'TicketBundle', 'Ticket', $params, $pagination, $ordered, $joins);
                $length = $pagination->getRowsLength($em, 'TicketBundle', 'Ticket', $params, $ordered, $joins);
            } elseif ($this->get('security.authorization_checker')->isGranted('ROLE_USER') and ! $this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR')) {
                $user = $em->getRepository('UserBundle:User')->findOneBy(array('id' => $id_user));
                $workshop = $user->getWorkshop();

                if (isset($joins[0][0]) and $joins[0][0] == 'e.workshop w ') {
                    $joins[0][1] = $joins[0][1] . ' AND w.code_workshop = ' . $workshop->getCodeWorkshop() . " AND w.partner = " . $workshop->getPartner()->getid() . " ";
                } else {
                    $joins[] = array('e.workshop w ', 'w.code_workshop = ' . $workshop->getCodeWorkshop() . " AND w.partner = " . $workshop->getPartner()->getid() . " ");
                }
                //$joins[] = array('e.workshop w ', 'w.country = '.$user->getCountry()->getId());
                $tickets = $pagination->getRows($em, 'TicketBundle', 'Ticket', $params, $pagination, $ordered, $joins);
                $length = $pagination->getRowsLength($em, 'TicketBundle', 'Ticket', $params, $ordered, $joins);
            } else {
                //$joins[] = array('e.workshop w ', 'w.country = '.$user->getCountry()->getId());
                $tickets = $pagination->getRows($em, 'TicketBundle', 'Ticket', $params, $pagination, $ordered, $joins);
                $length = $pagination->getRowsLength($em, 'TicketBundle', 'Ticket', $params, $ordered, $joins);
            }
        }
        $pagination->setTotalPagByLength($length);

        if (!isset($workshops[0])) {
            $workshops = array('0' => new Workshop());
        }

        $size = sizeof($workshops);
        if ($size > 1) {
            $error = $this->get('translator')->trans('workshop_confirm');
            $error .='(' . $size . ')';
            $this->get('session')->getFlashBag()->add('error', $error);
        }

        if ($option == null)
            $option = 'all';

        $b_query   = $em->createQuery('SELECT b FROM CarBundle:Brand b, CarBundle:Model m WHERE b.id = m.brand ORDER BY b.name');
        $brands = $b_query->getResult();
        $systems = $em->getRepository('TicketBundle:System')->findBy(array(), array('name' => 'ASC'));
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR') and ! $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
            $countries = $em->getRepository('UtilBundle:CountryService')->findAll();
        else
            $countries = $em->getRepository('UtilBundle:Country')->findAll();

        if($this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR')) {
            if($user->getCategoryService() != NULL) {
                $catservices[0] = $em->getRepository('UserBundle:CategoryService')->find($user->getCategoryService());
                $advisers = $em->getRepository("UserBundle:User")->findByRole($em, $country, $user->getCategoryService()->getId(), 'ROLE_ASSESSOR');
            }else{
                $catservices = $em->getRepository('UserBundle:CategoryService')->findAll();
                $advisers = $em->getRepository("UserBundle:User")->findByRole($em, $country, $catservId, 'ROLE_ASSESSOR');
            }
        }else{
            $advisers = array();
            $catservices = $em->getRepository('UserBundle:CategoryService')->findAll();
        }
        $languages = $em->getRepository('UtilBundle:Language')->findAll();

        $adsplus = $em->getRepository('WorkshopBundle:ADSPlus')->findOneBy(array('idTallerADS' => $workshops[0]->getId()));

        $importances = $em->getRepository('TicketBundle:Importance')->findAll();

        $t_inactive =  $em->getRepository('TicketBundle:Ticket')->findBy(array('status' => 3));
        $inactive =  sizeof($t_inactive);

        if (sizeof($tickets) == 0)
            $pagination = new Pagination(1);
        
        $array = array('workshop' => $workshops[0], 'pagination' => $pagination, 'tickets' => $tickets,
            'country' => $country, 'lang' => $lang, 'catserv' => $catserv, 'num_rows' => $num_rows, 'option' => $option, 'brands' => $brands,
            'systems' => $systems, 'countries' => $countries, 'catservices' => $catservices, 'languages' => $languages,
            'adsplus' => $adsplus, 'inactive' => $inactive, 't_inactive' => $t_inactive, 'importances' => $importances,
            'advisers' => $advisers, 'adviser_id' => $adviser_id, 'workshop_id' => $workshop_id, 'brand' => null
        );
        if($this->getUser()->getCategoryService() != null){
            $array['id_catserv'] = $this->getUser()->getCategoryService()->getId();
        }else{
            $array['id_catserv'] = 0;
        }
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
            return $this->render('TicketBundle:Layout:list_ticket_layout.html.twig', $array);
        elseif ($this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR')) {
            return $this->render('TicketBundle:Layout:list_ticket_assessor_layout.html.twig', $array);
        } else
            return $this->render('TicketBundle:Layout:list_ticket_layout.html.twig', $array);
    }

    /**
     * Crea un ticket abierto con sus respectivos post y car
     * @return url
     */
    public function newTicketAction(Request $request, $id_workshop = null, $einatech = 0, $reset = 0) {
        
        if($reset == 1) {
            $this->deleteSessionCar();
        }
       
        $em = $this->getDoctrine()->getManager();


        $trans = $this->get('translator');
        $ticket = new Ticket();
        $car = new Car();
        $document = new Document();
        $_SESSION['einatech'] = $einatech;
        
        $autologin_session = $this->get('session')->get('autologin');
        $user = $this->getUser();
        if(!$user)
        { 
            return $this->redirect($this->generateUrl('user_login'));
        }
        
        $id_catserv = 0;
        if($this->getUser()->getCategoryService() != null){
            $id_catserv = $this->getUser()->getCategoryService()->getId();
        }
        if($id_catserv != 0 && $id_catserv != 2 && $id_catserv != 4){
            $_SESSION['einatech'] = 2;
        }
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')  && !$this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR') && $einatech == 0 ){
            $_SESSION['einatech'] = 2;
        }
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') && $einatech == 0 ){
            $_SESSION['einatech'] = 2;
        }       
        if( $this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR') && $einatech == 0 && !$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') && $id_catserv == 2){
            $_SESSION['einatech'] = 3;
        }
        if ($id_workshop != null) {
            $workshop = $em->getRepository('WorkshopBundle:Workshop')->find($id_workshop);
        } else {
            $workshop = new Workshop();
        }
        
        // TODO
        // Ha aparecido un error en el formulario que no recoge los datos de Marca, Modelo, Gama, Subsistema y Importancia.
        // Al no encontrar solucion aparente cargaremos los datos desde $request
        //
        $id_brand = $request->request->get('new_car_form_brand');
        $id_model = $request->request->get('new_car_form_model');
        $id_version = $request->request->get('new_car_form_version');
        
        if(isset($request->request->get('ticket_form')['subsystem'])){
            $id_subsystem = $request->request->get('ticket_form')['subsystem'];
        }
        if(isset($request->request->get('ticket_form')['importance'])){
            $id_importance = $request->request->get('ticket_form')['importance'];
            $importance= $em->getRepository('TicketBundle:Ticket')->find($id_importance);
            $ticket->setImportance($importance);
        }
        if (isset($id_brand) and $id_brand != '') {
            $brand = $em->getRepository('CarBundle:Brand')->find($id_brand);
            $car->setBrand($brand);
        }
        if (isset($id_model) and $id_model != '') {
            $model = $em->getRepository('CarBundle:Model')->find($id_model);
            $car->setModel($model);
        }
        if (isset($id_version) and $id_version != '') {

            $version = $em->getRepository('CarBundle:Version')->findOneById($id_version);
            $car->setVersion($version);
        }
        if (isset($id_subsystem) and $id_subsystem != '' and $id_subsystem != '0') {
            $subsystem = $em->getRepository('TicketBundle:Subsystem')->find($id_subsystem);
            $ticket->setSubsystem($subsystem);
        }
        
        $open_newTicket = $request->request->get('open_newTicket');
        $id_brand = $request->request->get('n_id_brand');
        $id_model = $request->request->get('n_id_model');
        $id_version = $request->request->get('n_id_version');
        $id_year = $request->request->get('n_id_year');
        $id_motor = $request->request->get('n_id_motor');
        $id_kw = $request->request->get('n_id_kw');
        $id_subsystem = $request->request->get('n_id_subsystem');
        $id_importance = $request->request->get('n_id_importance');
        $id_displacement = $request->request->get('n_id_displacement');
        
        if($this->get('session')->get('version') == null) {
            $id_vin = $request->request->get('n_id_vin');
            $id_plateNumber = $request->request->get('n_id_plateNumber');
        }
        if (isset($id_brand) and $id_brand != '' and $id_brand != '0') {
            $brand = $em->getRepository('CarBundle:Brand')->find($id_brand);
            $car->setBrand($brand);
        }
        if (isset($id_model) and $id_model != '' and $id_model != '0') {
            $model = $em->getRepository('CarBundle:Model')->find($id_model);
            
            $car->setModel($model);
        }
        if (isset($id_version) and $id_version != '' and $id_version != '0') {
            $version = $em->getRepository('CarBundle:Version')->findOneById($id_version);
            if(sizeof($version)>1){ 
                if(isset($id_motor) && $id_motor != '' && $id_motor != '0'){
                    $v_query   = $em->createQuery("SELECT v FROM CarBundle:Version v, CarBundle:Motor m WHERE v.id= ".$id_version." AND m.name = '".$id_motor."' AND m.id = v.motor");
                   
                    $version = $v_query->getSingleResult();
                }
                else{
                    $version = $em->getRepository('CarBundle:Version')->findOneById($id_version);
                }
                
            }
            $car->setVersion($version);
        }
        
        if (isset($id_year) and $id_year != '' and $id_year != '0') {
            $car->setYear($id_year);
        }
        if (isset($id_motor) and $id_motor != '' and $id_motor != '0') {
            $car->setMotor($id_motor);
        }
        if (isset($id_kw) and $id_kw != '' and $id_kw != '0') {
            $car->setKw($id_kw);
        }
        if (isset($id_subsystem) and $id_subsystem != '' and $id_subsystem != '0') {
            $subsystem = $em->getRepository('TicketBundle:Subsystem')->find($id_subsystem);
            $ticket->setSubsystem($subsystem);
        }
        if (isset($id_importance) and $id_importance != '' and $id_importance != '0') {
            $importance = $em->getRepository('TicketBundle:Importance')->find($id_importance);
            $ticket->setImportance($importance);
        }
        if (isset($id_displacement) and $id_displacement != '' and $id_displacement != '0') {
            $car->setDisplacement($id_displacement);
        }
        if (isset($id_vin) and $id_vin != '' and $id_vin != '0') {
            $id_vin = strtoupper($id_vin);
            $car->setVin($id_vin);
        }
        if (isset($id_plateNumber) and $id_plateNumber != '' and $id_plateNumber != '0') {
            $id_plateNumber = strtoupper($id_plateNumber);
            $car->setPlateNumber($id_plateNumber);
        }

        $systems = $em->getRepository('TicketBundle:System')->findAll();
        if($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') || $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            $b_query   = $em->createQuery('SELECT b FROM CarBundle:Brand b, CarBundle:Model m WHERE b.id = m.brand ORDER BY b.name');
        }
        else{
            $b_query   = $em->createQuery('SELECT b FROM CarBundle:Brand b, CarBundle:Model m WHERE b.id = m.brand AND b.id <> 0 ORDER BY b.name');
        }
        $brands = $b_query->getResult();
        $adsplus = $em->getRepository('WorkshopBundle:ADSPlus')->findOneBy(array('idTallerADS' => $workshop->getId()));

        //Define Forms
        $form = $this->createForm(NewTicketType::class, $ticket);
        $formC = $this->createForm(CarType::class, $car);
        $formD = $this->createForm(DocumentType::class, $document);
        $textarea_content = "";
        if($einatech == 1){
             $textarea_content = $this->get('translator')->trans('einatech_textarea_default');
         }
        if (isset($open_newTicket) and $open_newTicket == '1' and $request->getMethod() == 'POST') {
            //campos comunes
            $user = $em->getRepository('UserBundle:User')->find($this->getUser()->getId());
            $status = $em->getRepository('TicketBundle:Status')->findOneByName('open');
                        
            $form->handleRequest($request);
            $formC->handleRequest($request);
            $formD->handleRequest($request);
           
        }
        
        if ($ticket->getDescription() != null and $car->getBrand() != null and ($car->getBrand()->getId() == 0 || $car->getBrand()->getId() != null)
                and $car->getModel() != null and ($car->getModel()->getId() == 0 || $car->getModel()->getId() != null) and $car->getVin() != null and $car->getPlateNumber() != null) {
            $array = array('ticket' => $ticket,
                'form' => $form->createView(),
                'formC' => $formC->createView(),
                'formD' => $formD->createView(),
                'brands' => $brands,
                'systems' => $systems,
                'einatech' => $einatech,
                'textarea_content' => $textarea_content,
                'id_catserv' => $id_catserv,
                'adsplus' => $adsplus,
                'workshop' => $workshop,
                'form_name' => $form->getName(),
                'autologin_session' => $autologin_session,
                'marca_session' => $car->getBrand()->getId(),
                'modelo_session' => $car->getModel()->getId(),
                'version_session' => null,
                'description_session' => $ticket->getDescription(),
                'plateNumber_session' => $car->getPlateNumber(),
                'vin_session' => $car->getVin(),
                'system_session' => null,
                'subsystem_session' => null,
                'importance_session' => $ticket->getImportance()->getId()
                );

            if ($ticket->getSubsystem() != "" or $this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR') == 0) {
                if($car->getVersion() != null){
                    $array['version_session'] = $car->getVersion()->getId();
                }
                if( $ticket->getSubsystem() != ""){
                    $array['system_session']= $ticket->getSubsystem()->getSystem()->getId();
                    $array['subsystem_session']= $ticket->getSubsystem()->getId();
                }
                if ($formC->isValid() && $formD->isValid()) {
                    $id_brand = $request->request->get('new_car_form_brand');
                    $id_model = $request->request->get('new_car_form_model');
                    $id_version = $request->request->get('new_car_form_version');
                    if($id_version == 0 && $id_brand != 0){
                        $id_version = null;
                    }
                    $brand = $em->getRepository('CarBundle:Brand')->find($id_brand);
                    $model = $em->getRepository('CarBundle:Model')->find($id_model);

                    //SI NO HA ESCOGIDO VERSION SE DEJA NULL
                    if (isset($id_version)) {
                        $version = $em->getRepository('CarBundle:Version')->findOneBy(array('id' => $id_version));
                    } else {
                        $id_version = null;
                    }

                    // Controla si ya existe el mismo ticket
                    $desc = $ticket->getDescription();
                    $desc = $this->fixWrongCharacters($desc);
                    $select = "SELECT t FROM TicketBundle:Ticket t
                                    WHERE t.workshop = " . $workshop->getId() . "
                                    AND t.description LIKE '" . $desc . "'
                                    AND t.car IN (
                                        SELECT c FROM CarBundle:Car c
                                        WHERE c.brand = " . $id_brand . "
                                        AND c.model = " . $id_model . " AND c.vin LIKE  '". $car->getVin() ."' AND c.plateNumber LIKE '". $car->getPlateNumber() . "' ";

                    if ($id_version != null) {
                        $select .= "AND c.version = " . $id_version;
                    }
                    $select .= ')';
                    $query = $em->createQuery($select);
                    $existTicket = $query->getResult();

                    if ($existTicket == null) {

                        // Controla si se ha subido un fichero erroneo
                        $file = $document->getFile();
                        if (isset($file))
                            $extension = $file->getMimeType();
                        else {
                            $extension = '0';
                        }
                        if (isset($file))
                            $size = $file->getSize();
                        else {
                            $size = '0';
                        }

                        if ($extension == "application/pdf" or $extension == "application/x-pdf" or $extension == "image/bmp" or $extension == "image/jpeg"
                                or $extension == "image/png" or $extension == "image/gif" or $extension == "application/mspowerpoint" or $extension == "0") {
                            if ($this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR') or $size <= 10240000) {
                                //Define CAR
                                $car = UtilController::newEntity($car, $user);

                                $car->setBrand($brand);
                                $car->setModel($model);
                                $vin = $car->getVin();
                                //SI VIN TIENE LONGITUD 17
                                if (strlen($vin) == 17) {
                                    //SI VIN NO CONTIENE 'O'
                                    if (!strpos(strtolower($vin), 'o')) {
                                        if (isset($version)) {
                                            $sizeV = sizeof($version);

                                            if ($sizeV > 0) {
                                                $car->setVersion($version);
                                            }
                                        } else {
                                            $car->setVersion(null);
                                        }
                                        $exist_car = '0';
                                        $exist_vin = $em->getRepository('CarBundle:Car')->findOneByVin($vin);
                                        $exist_num = $em->getRepository('CarBundle:Car')->findOneByPlateNumber($car->getPlateNumber());

                                        if ($exist_vin == null AND $exist_num == null) {
                                            $exist_car = '0';
                                        } elseif (
                                                ($exist_vin != null AND $exist_num == null)
                                                OR ( $exist_vin == null AND $exist_num != null)
                                                OR ( $exist_vin != null AND $exist_num != null AND $exist_vin->getId() != $exist_num->getId())
                                        ) {
                                            $str = $trans->trans('error.vin_platenumber_not_match') . ': ';
                                            if ($exist_vin != null) {
                                                $str .=' **' . $trans->trans('vin') . '** ' . $exist_vin->getVin() . ' -> ' . $trans->trans('plate_number') . ' ' . $exist_vin->getPlateNumber() . ': ' . $exist_vin->getBrand() . ' ' . $exist_vin->getModel();
                                                if ($exist_vin->getVersion() != null) {
                                                    $str .= ' ' . $exist_vin->getVersion()->getName() . ' ';
                                                    if ($exist_vin->getMotor() != null) {
                                                        $str .= ' [' . $exist_vin->getMotor() . '] ';
                                                    }
                                                }
                                            }
                                            if ($exist_vin != null and $exist_num != null)
                                                $str .= ', ';

                                            if ($exist_num != null) {
                                                $str .=' **' . $trans->trans('plate_number') . '** ' . $exist_num->getPlateNumber() . ' -> ' . $trans->trans('vin') . ' ' . $exist_num->getVin() . ': ' . $exist_num->getBrand() . ' ' . $exist_num->getModel();
                                                if ($exist_num->getVersion() != null) {
                                                    $str .= ' ' . $exist_num->getVersion()->getName();
                                                    if ($exist_num->getMotor() != null) {
                                                        $str .= ' [' . $exist_num->getMotor() . ']';
                                                    }
                                                }
                                            }
                                            $exist_car = $str;
                                        } elseif (
                                                $exist_vin->getBrand()->getId() != $car->getBrand()->getId()
                                                OR
                                                $exist_vin->getModel()->getId() != $car->getModel()->getId()
                                                OR (
                                                $exist_vin->getVersion() != null
                                                AND
                                                $car->getVersion() != null
                                                AND
                                                $exist_vin->getVersion()->getId() != $car->getVersion()->getId()
                                                )
                                        ) {
                                            $str = $trans->trans('error.same_vin');
                                            if ($exist_vin != null) {
                                                $str .=' (' . $exist_vin->getVin() . ' -> ' . $exist_vin->getBrand() . ' ' . $exist_vin->getModel();
                                                if ($exist_vin->getVersion() != null) {
                                                    $str .= ' ' . $exist_vin->getVersion()->getName();
                                                    if ($exist_vin->getMotor() != null) {
                                                        $str .= ' [' . $exist_vin->getMotor() . ']';
                                                    }
                                                }
                                                $str .= ' )';
                                            }
                                            $exist_car = $str;
                                        } elseif (
                                                $exist_num->getBrand()->getId() != $car->getBrand()->getId()
                                                OR
                                                $exist_num->getModel()->getId() != $car->getModel()->getId()
                                                // OR (
                                                //     ($exist_num->getVersion() != null AND $car->getVersion() == null)
                                                //     OR
                                                //     ($exist_num->getVersion() == null AND $car->getVersion() != null)
                                                //     )
                                                OR (
                                                $exist_num->getVersion() != null
                                                AND
                                                $car->getVersion() != null
                                                AND

                                                // $exist_num->getVersion()->getName() != null
                                                // AND
                                                // $car->getVersion()->getName() != null
                                                // AND
                                                // $exist_num->getVersion()->getName() != $car->getVersion()->getName()

                                                $exist_num->getVersion()->getId() != $car->getVersion()->getId()
                                                )
                                        ) {
                                            $str = $trans->trans('error.same_platenumber');
                                            if ($exist_num != null) {
                                                $str .=' (' . $exist_num->getPlateNumber() . ' -> ' . $exist_num->getBrand() . ' ' . $exist_num->getModel();
                                                if ($exist_num->getVersion() != null) {
                                                    $str .= ' ' . $exist_num->getVersion()->getName();
                                                    if ($exist_num->getMotor() != null) {
                                                        $str .= ' [' . $exist_num->getMotor() . ']';
                                                    }
                                                }
                                                $str .= ' )';
                                            }
                                            $exist_car = $str;
                                        }

                                        if ($exist_car == '0') {
                                            if ($workshop->getHasChecks() == true and $workshop->getNumChecks() != null) {
                                                $numchecks = $workshop->getNumChecks();
                                                $workshop->setNumChecks($numchecks - 1);
                                                UtilController::saveEntity($em, $workshop, $user);
                                            }

                                            // Si el coche ya existe sobreescribimos los datos nuevos (si los hay)
                                            if ($exist_vin != null AND $exist_num != null AND $exist_vin->getId() == $exist_num->getId()) {
                                                $old_car = $car;
                                                $car = $exist_vin;

                                                //VERSION
                                                if ($car->getVersion() == null and $old_car->getVersion() != null and $old_car->getVersion()->getName() != null)
                                                    $car->setVersion($old_car->getVersion());
                                                //YEAR
                                                if ($car->getYear() == null and $old_car->getYear() != null)
                                                    $car->setYear($old_car->getYear());
                                                //MOTOR
                                                if ($car->getMotor() == null and $old_car->getMotor() != null)
                                                    $car->setMotor($old_car->getMotor());
                                                //KW
                                                if ($car->getKw() == null and $old_car->getKw() != null)
                                                    $car->setKw($old_car->getKw());
                                                //DISPLACEMENT
                                                if ($car->getDisplacement() == null and $old_car->getDisplacement() != null)
                                                    $car->setDisplacement($old_car->getDisplacement());
                                            }
                                            $car->setVin(strtoupper($car->getVin()));
                                            UtilController::saveEntity($em, $car, $user);


                                            //Define TICKET
                                            $ticket = UtilController::newEntity($ticket, $user);
                                            if ($this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR')) {
                                                $ticket->setWorkshop($workshop);
                                                $ticket->setCategoryService($workshop->getCategoryService());
                                                $ticket->setCountry($workshop->getCountry());
                                                $language = $em->getRepository('UtilBundle:Language')->findOneByLanguage($workshop->getCountry()->getLang());
                                                $ticket->setLanguage($language);
                                                $ticket->setAssignedTo($user);
                                                $ticket->setPending(0);
                                                $ticket->setIsPhoneCall(1);
                                            } else {
                                                $ticket->setWorkshop($user->getWorkshop());
                                                $ticket->setCategoryService($user->getCategoryService());
                                                $ticket->setCountry($user->getCountry());
                                                $ticket->setLanguage($user->getLanguage());
                                                $ticket->setPending(1);
                                                $ticket->setIsPhoneCall(0);
                                                if($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                                                     $ticket->setIsPhoneCall(1);
                                                }     

                                            }
                                            if(isset($request->request->get('ticket_form')['importance'])){
                                                $imp = $em->getRepository('TicketBundle:Importance')->find($request->request->get('ticket_form')['importance']);
                                                $ticket->setImportance($imp);
                                            }
                                            $ticket->setStatus($status);
                                            $ticket->setCar($car);

                                            UtilController::saveEntity($em, $ticket, $user);
                                            $this->deleteSessionCar();
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
                                            $this->get('session')->getFlashBag()->add('error', $exist_car);

                                            return $this->render('TicketBundle:Layout:new_ticket_layout.html.twig', $array);
                                        }
                                    } else {
                                        $this->get('session')->getFlashBag()->add('error', $trans->trans('ticket_vin_error_o'));

                                        return $this->render('TicketBundle:Layout:new_ticket_layout.html.twig', $array);
                                    }
                                } else {
                                    $this->get('session')->getFlashBag()->add('error', $trans->trans('ticket_vin_error_length'));

                                    return $this->render('TicketBundle:Layout:new_ticket_layout.html.twig', $array);
                                }
                            } else {
                                // ERROR tamaño
                                $this->get('session')->getFlashBag()->add('error', $trans->trans('error.file_size'));

                                return $this->render('TicketBundle:Layout:new_ticket_layout.html.twig', $array);
                            }
                        } else {
                            // ERROR tipo de fichero
                            $this->get('session')->getFlashBag()->add('error', $trans->trans('error.file'));

                            return $this->render('TicketBundle:Layout:new_ticket_layout.html.twig', $array);
                        }
                    } else {
                        // ERROR tipo de fichero
                        $this->get('session')->getFlashBag()->add('error', $trans->trans('error.same_ticket')
                                . ' (' . $trans->trans('ticket') . ' #' . $existTicket[0]->getId() . ')');

                        return $this->render('TicketBundle:Layout:new_ticket_layout.html.twig', $array);
                    }

                    $mail = $ticket->getWorkshop()->getEmail1();
                    $pos = strpos($mail, '@');

                    if ($pos != 0 and $ticket->getWorkshop()->getActive()) {

                        // Cambiamos el locale para enviar el mail en el idioma del taller
                        $locale = $request->getLocale();
                        $lang_w = $ticket->getWorkshop()->getCountry()->getLang();
                        $lang = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_w);
                        $request->setLocale($lang->getShortName());

                        /* MAILING */
                        $mailer = $this->get('cms.mailer');
                        $mailer->setTo($mail);
                        $mailer->setSubject($this->get('translator')->trans('mail.newTicket.subject') . $ticket->getId());
                        $mailer->setFrom('noreply@adserviceticketing.com');
                        $mailer->setBody($this->renderView('UtilBundle:Mailing:ticket_new_mail.html.twig', array('ticket' => $ticket, '__locale' => $locale)));
                        $mailer->sendMailToSpool();
                        // echo $this->renderView('UtilBundle:Mailing:ticket_new_mail.html.twig', array('ticket' => $ticket, '__locale' => $locale));die;

                        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR') and $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
                            if($ticket->getImportance()->getId() == 5){
                                $mail_centralita = $this->container->getParameter('mail_einatech');
                                $mailer->setSubject('ticket: ' . $ticket->getId());
                            }
                            else
                            {
                                $mailer->setSubject('ticket: ' . $ticket->getId());
                                $mail_centralita = $this->container->getParameter('mail_centralita_default');
                                if($ticket->getCategoryService()->getEmail() != null || $ticket->getCategoryService()->getEmail() != ''){
                                    $mail_centralita = $ticket->getCategoryService()->getEmail();
                                }   
                            }
                            //Hay un email diferente por cada pais en funcion del idioma que tenga asignado el taller.
                            $mailer->setTo($this->get('translator')->trans($mail_centralita));

                            $date = date("Y-m-d H:i:s");
                            $mailer->setBody('ticket: ' . $ticket->getId() . ' - ' . $date);
                            $mailer->sendMailToSpool();
                        }

                        // Dejamos el locale tal y como estaba
                        $request->setLocale($locale);

                        $this->get('session')->getFlashBag()->add('ticket_created', $this->get('translator')->trans('ticket_created'));
                    }

                    if (isset($_POST['save_close'])) {
                        return $this->redirect($this->generateUrl('closeTicket', array('id' => $ticket->getId())));
                    } else {
                        return $this->redirect($this->generateUrl('showTicket', array('id' => $ticket->getId())));
                    }
                } else {
                    $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans('error.bad_introduction'));
                }
            } else {
                $this->get('session')->getFlashBag()->add('error_ticket', $this->get('translator')->trans('error.bad_introduction.ticket'));
            }
        } 

        if ($id_subsystem != '' and $id_subsystem != '0') {
            $subsystem = $em->getRepository('TicketBundle:Subsystem')->find($id_subsystem);
            $id_system = $subsystem->getSystem()->getId();
            $id_subsystem = $subsystem->getId();
        } else {
            $id_system = '';
            $id_subsystem = '';
        }
        $marca_session = $modelo_session = $version_session = $description_session = $plateNumber_session = $vin_session = $system_session = $subsystem_session = $importance_session = null; 
        
        if($this->get('session')->get('marca') != null) {
            $marca_session = $this->get('session')->get('marca');
            $modelo_session = $this->get('session')->get('modelo');
            $version_session = $this->get('session')->get('version');    
            if($einatech != 1)
                $description_session = $this->get('session')->get('description');           
            $plateNumber_session = $this->get('session')->get('plateNumber'); 
            if($this->get('session')->get('vin') != null) {
                $vin_session = $this->get('session')->get('vin');
            }
            $id_vin = null;
            $car->setVin($id_vin);       
            $id_plateNumber = null;
            $car->setPlateNumber($id_plateNumber);    
            
        }
        if($this->get('session')->get('subsystem') != null) {
            $system_session = $this->get('session')->get('system');
            $subsystem_session = $this->get('session')->get('subsystem');
        }
        if($this->get('session')->get('importance') != null) {
            $importance_session = $this->get('session')->get('importance');
        }
        $array = array('ticket' => $ticket,
            'action' => 'newTicket',
            'car' => $car,
            'form' => $form->createView(),
            'formC' => $formC->createView(),
            'formD' => $formD->createView(),
            'brands' => $brands,
            'einatech' => $einatech,
            'id_version' => $id_version,
            'id_system' => $id_system,
            'id_subsystem' => $id_subsystem,
            'systems' => $systems,
            'adsplus' => $adsplus,
            'workshop' => $workshop,
            'textarea_content' => $textarea_content,
            'id_catserv' => $id_catserv,
            'form_name' => $form->getName(),     
            'autologin_session' => $autologin_session,        
            'marca_session' => $marca_session,             
            'modelo_session' => $modelo_session,             
            'version_session' => $version_session,
            'description_session' => $description_session,             
            'plateNumber_session' => $plateNumber_session,
            'vin_session' => $vin_session,
            'system_session' => $system_session,
            'subsystem_session' => $subsystem_session,
            'importance_session' => $importance_session    
                
        );

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR'))
            return $this->render('TicketBundle:Layout:new_ticket_assessor_layout.html.twig', $array);
        else
            return $this->render('TicketBundle:Layout:new_ticket_layout.html.twig', $array);
    }

    /**
     * Edita el ticket y el car asignado a partir de su id
     * @Route("/ticket/edit/{id}")
     * @ParamConverter("ticket", class="TicketBundle:Ticket")
     * @return url
     */
    public function editTicketAction(Request $request, $id, $ticket) {


        $id_catserv = 0;
        if($this->getUser()->getCategoryService() != null){
            $id_catserv = $this->getUser()->getCategoryService()->getId();
        }
        $_SESSION['einatech'] = 0;
        if($id_catserv != 0 && $id_catserv != 2 && $id_catserv != 4){
            $_SESSION['einatech'] = 2;
        }
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_ASSERSSOR') and $ticket->getImportance() != null && $ticket->getImportance()->getId() == 5){
            $_SESSION['einatech'] = 1;
        }
        if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')
                or ( !$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') and $ticket->getWorkshop()->getCountry()->getId() == $this->getUser()->getCountry()->getId())
                or ( $this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR') and ! $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
        ) {

            $em = $this->getDoctrine()->getManager();


            $form = $this->createForm(EditTicketType::class, $ticket);

            if ($request->getMethod() == 'POST') {

                $user = $em->getRepository('UserBundle:User')->find($this->getUser()->getId());

                $form->handleRequest($request);

                $car = $ticket->getCar();

                if ($ticket->getDescription() != null and $car->getBrand() != null and $car->getBrand()->getId() != null
                        and $car->getModel() != null and $car->getModel()->getId() != null and $car->getVin() != null and $car->getPlateNumber() != null) {
                    /* Validacion Ticket */
                    $str_len = strlen($ticket->getDescription());
                    if ($this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR')) {
                        $max_len = 10000;
                    } else {
                        $max_len = 500;
                    }

                    if ($str_len <= $max_len) {
                        //Define CAR
                        if ($form->isValid()) {
                            
                            UtilController::saveEntity($em, $ticket, $user);

                            $mail = $ticket->getWorkshop()->getEmail1();
                            $pos = strpos($mail, '@');
                            if ($pos != 0  and $ticket->getWorkshop()->getActive()) {

                                // Cambiamos el locale para enviar el mail en el idioma del taller
                                $locale = $request->getLocale();
                                $lang_w = $ticket->getWorkshop()->getCountry()->getLang();
                                $lang = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_w);
                                $request->setLocale($lang->getShortName());

                                /* MAILING */
                                $mailer = $this->get('cms.mailer');
                                $mailer->setTo($mail);
                                $mailer->setSubject($this->get('translator')->trans('mail.editTicket.subject') . $id);
                                $mailer->setFrom('noreply@adserviceticketing.com');
                                $mailer->setBody($this->renderView('UtilBundle:Mailing:ticket_edit_mail.html.twig', array('ticket' => $ticket, '__locale' => $locale)));
                                $mailer->sendMailToSpool();
                                //echo $this->renderView('UtilBundle:Mailing:ticket_new_mail.html.twig', array('ticket' => $ticket));die;
                                // Dejamos el locale tal y como estaba
                                $request->setLocale($locale);
                            }

                            return $this->redirect($this->generateUrl('showTicket', array('id' => $id)));
                        } else {
                            $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans('error.bad_introduction'));
                        }
                    } else {
                        $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans('error.txt_length_%numchars%', array('%numchars%' => $max_len)));
                    }
                }

                $systems = $em->getRepository('TicketBundle:System')->findAll();

                $array = array(
                    'action' => 'showTicket',
                    'form' => $form->createView(),
                    'form_name' => $form->getName(),
                    'ticket' => $ticket,
                    'systems' => $systems,
                    'form_name' => $form->getName(),
                );
            } else {
                $flash = $this->get('translator')->trans('error.bad_introduction');
                $this->get('session')->getFlashBag()->add('error', $flash);
            }
            if ($this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR'))
                return $this->render('TicketBundle:Layout:show_ticket_assessor_layout.html.twig', $array);
            else
                return $this->render('TicketBundle:Layout:show_ticket_layout.html.twig', $array);
        }else {
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
    public function deleteTicketAction(Request $request, $id, $ticket) {


        if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')
                or ( !$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') and $ticket->getWorkshop()->getCountry()->getId() == $this->getUser()->getCountry()->getId())
                or ( $this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR') and ! $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
        ) {
            if ($this->get('security.authorization_checker')->isGranted('ROLE_USER') === false) {
                throw new AccessDeniedException();
            }
            $em = $this->getDoctrine()->getManager();

            //se borrara solo si hay un post sin respuesta, si hay mas de uno se deniega
            $posts = $ticket->getPosts(); //echo count($posts);
            // if (count($posts)>1) throw $this->createNotFoundException('Este Ticket no puede borrarse, ya esta respondido');
            //puede borrarlo el assessor o el usuario si el ticket no esta assignado aun
            if ((!$this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR') and ( $ticket->getAssignedTo() != null))) {
                throw $this->createNotFoundException('Este ticket solo puede ser borrado por un asesor');
            }

            //Si este ticket es el unico que apunta a un coche, al boorarlo borramos tambien el coche
            $car_id = $ticket->getCar()->getId();
            $query = $em->createQuery(' SELECT t FROM TicketBundle:Ticket t
                                        WHERE t.car = '.$car_id);

            if(sizeof($query->getResult()) == 1)
            {
                $car = $ticket->getCar();
                $em->remove($car);
            }

            //si el ticket esta cerrado no se puede borrar
            // if($ticket->getStatus()->getName() == 'closed'){
            //    throw $this->createNotFoundException('Este ticket ya esta cerrado');
            // }
            //borra todos los post del ticket
            foreach ($posts as $post) {

                if ($post->getDocument() != null) {
                    $document = $post->getDocument();
                    $document->setPath(null);
                    $em->remove($document);
                }
                $em->remove($post);
            }
            //borra el ticket
            $em->remove($ticket);

            // $mail = $ticket->getWorkshop()->getEmail1();
            // $pos = strpos($mail, '@');
            // if ($pos != 0) {
            //     // Cambiamos el locale para enviar el mail en el idioma del taller
            //     $locale = $request->getLocale();
            //     $lang_w = $ticket->getWorkshop()->getCountry()->getLang();
            //     $lang   = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_w);
            //     $request->setLocale($lang->getShortName());
            //     /* MAILING */
            //     $mailer = $this->get('cms.mailer');
            //     $mailer->setTo($mail);
            //     $mailer->setSubject($this->get('translator')->trans('mail.deleteTicket.subject').$id);
            //     $mailer->setFrom('noreply@adserviceticketing.com');
            //     $mailer->setBody($this->renderView('UtilBundle:Mailing:ticket_delete_mail.html.twig', array('ticket' => $ticket, '__locale' => $locale)));
            //     $mailer->sendMailToSpool();
            //     //echo $this->renderView('UtilBundle:Mailing:ticket_delete_mail.html.twig', array('ticket' => $ticket));die;
            //     // Dejamos el locale tal y como estaba
            //     $request->setLocale($locale);
            // }

            $em->flush();
            return $this->redirect($this->generateUrl('listTicket'));
        } else {
            return $this->render('TwigBundle:Exception:exception_access.html.twig');
        }
    }

    /**
     * Muestra los posts que pertenecen a un ticket
     * @Route("/ticket/show/{id}")
     * @ParamConverter("ticket", class="TicketBundle:Ticket")
     * @return url
     */
    public function showTicketAction(Request $request, $ticket) {

        $user = $this->getUser();
        
        if(!$user) return $this->redirect($this->generateUrl('user_login'));

        $user = $this->getUser();
        
         $id_catserv = 0;
        if($this->getUser()->getCategoryService() != null){
            $id_catserv = $this->getUser()->getCategoryService()->getId();
        }
        $_SESSION['einatech'] = 0;
        if($id_catserv != 0 && $id_catserv != 2 && $id_catserv != 4){
            $_SESSION['einatech'] = 2;
        }
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR') and $ticket->getImportance() != null && $ticket->getImportance()->getId() == 5){
            $_SESSION['einatech'] = 1;
        }
        
        if (
            ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')
                or ( !$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') and $this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR') and $ticket->getWorkshop()->getCountry()->getId() == $this->getUser()->getCountry()->getId())
                or ( !$this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR') and $ticket->getWorkshop() == $user->getWorkshop())
                or ( $this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR') and ! $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
            ) and ($user->getCategoryService() == null OR $user->getCategoryService() == $ticket->getCategoryService())
        ) {
            $em = $this->getDoctrine()->getManager();

            $car = $ticket->getCar();
            $version = $car->getVersion();
            $model = $car->getModel();
            $brand = $car->getBrand();

            if (isset($version))
                $id = $car->getVersion()->getId();
            else
                $id = "";

            $locale      = $request->getLocale();
            $loc_country = $em->getRepository('UtilBundle:Country')->findBy(array('short_name' => $locale))[0];

            $sentences = $em->getRepository('TicketBundle:Sentence')->findBy(array('active' => 1, 'country' => $loc_country->getId()));

            $post = new Post();
            $message = $request->getSession()->get('message');
            $post->setMessage($message);
            $document = new Document();
            $systems = $em->getRepository('TicketBundle:System')->findAll();
            $block = null;

            if ($ticket->getBlockedBy() != null and $ticket->getBlockedBy() != $user) {

                //Si ha pasado mas de una hora desde la ultima modificación y esta bloqueado.. lo desbloqueamos
                $now = new \DateTime(\date("Y-m-d H:i:s"));
                $last_modified = $ticket->getModifiedAt();

                $interval = $last_modified->diff($now);

                $block = $interval->h . 'h ' . $interval->m . 'm ';
                //echo $block;die;
                if ($interval->h > 1) {
                    $ticket->setBlockedBy(null);
                }
            }

            //Define Forms
            $formP = $this->createForm(PostType::class, $post);
            $formD = $this->createForm(DocumentType::class, $document);

            $new_subsystem = $request->request->get('edit_ticket_form')['subsystem'];
            if ($new_subsystem != null) {
                $subsystem = $em->getRepository('TicketBundle:Subsystem')->find($new_subsystem);
                $id_system = $subsystem->getSystem()->getId();
                $id_subsystem = $new_subsystem;
            } else {

                if ($ticket->getSubsystem() != null) {
                    $id_subsystem = $ticket->getSubsystem()->getId();
                    $id_system = $ticket->getSubsystem()->getSystem()->getId();
                } else {
                    $id_system = '';
                    $id_subsystem = '';
                }
            }

            $array = array('formP' => $formP->createView(),
                'formD' => $formD->createView(),
                'action' => 'showTicket',
                'ticket' => $ticket,
                'systems' => $systems,
                'id_system' => $id_system,
                'id_subsystem' => $id_subsystem,
                'sentences' => $sentences,
                'form_name' => $formP->getName(),
                'brand' => $brand,
                'model' => $model,
                'version' => $version,
                'id' => $id);

            if ($this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR')) {
                $form = $this->createForm(EditTicketType::class, $ticket);
                $array['form'] = $form->createView();
            }
            if ($request->getMethod() == 'POST') {

                //Define Ticket
                if ($this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR')) {

                    $form->handleRequest($request);
                }

                if (!$this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR') or ( $this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR') /* and $form->isValid() */)) {

                    $formP->handleRequest($request);
                    $formD->handleRequest($request);

                    if ($formP->isValid() and $formD->isValid()) {

                        if($post->getMessage() == null) $post->setMessage(' ');

                        if ($this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR')) {

                            if ($id_subsystem != '0' and ( $ticket->getSubsystem() == null or $ticket->getSubsystem()->getId() != $id_subsystem)) {
                                $subsystem = $em->getRepository('TicketBundle:Subsystem')->find($id_subsystem);

                                if (isset($subsystem))
                                    $ticket->setSubsystem($subsystem);
                            }
                        }

                        // Controla si se ha subido un fichero erroneo
                        $file = $document->getFile();
                        if (isset($file))
                            $extension = $file->getMimeType();
                        else {
                            $extension = '0';
                        }
                        if (isset($file))
                            $size = $file->getSize();
                        else {
                            $size = '0';
                        }

                        if ($extension == "application/pdf" or $extension == "application/x-pdf" or $extension == "image/bmp" or $extension == "image/jpeg"
                                or $extension == "image/png" or $extension == "image/gif" or $extension == "application/mspowerpoint" or $extension == "0") {

                            if ($this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR') or $size <= 10240000) {
                                $str_len = strlen($post->getMessage());
                                if ($this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR')) {
                                    $max_len = 10000;
                                } else {
                                    $max_len = 500;
                                }
                                if ($str_len <= $max_len) {
                                    if($request->request->has("sendTicket")){
                                        //Define Post
                                        if($request->request->has("is_phone_call")){
                                            $post->setIsPhoneCall(1);
                                        }
                                        $post = UtilController::newEntity($post, $user);
                                        $post->setTicket($ticket);
                                        UtilController::saveEntity($em, $post, $user, false);


                                        // Define Document
                                        $document->setPost($post);

                                        if ($file != "") {
                                                $em->persist($document);
                                        }

                                        // Quitamos el estado inactivo/caducado si alguien responde al ticket
                                        $ticket->setStatus($em->getRepository('TicketBundle:Status')->find(1));
                                        $ticket->setExpirationDate(null);

                                        //Se desbloquea el ticket una vez respondido
                                        if ($ticket->getBlockedBy() != null) {
                                            $ticket->setBlockedBy(null);
                                        }

                                        // Si assessor responde se le asigna y se marca como respondido, si es el taller se marca como pendiente
                                        if ($this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR')) {
                                            $ticket->setAssignedTo($user);
                                            $ticket->setPending(0);
                                        }else{
                                            $ticket->setPending(1);
                                        }
                                        
                                        $ticket->setExpirationDate(null);
                                        UtilController::saveEntity($em, $ticket, $user);

                                        $mail = $ticket->getWorkshop()->getEmail1();
                                        $pos = strpos($mail, '@');

                                        if ($pos != 0 and $ticket->getWorkshop()->getActive()) {
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
                                            $mailer->setBody($this->renderView('UtilBundle:Mailing:ticket_answer_mail.html.twig', array('ticket' => $ticket, '__locale' => $locale)));
                                            $mailer->sendMailToSpool();

                                            // if (!$this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR') and $ticket->getAssignedTo() != null) {
                                            //     $mailer->setTo($ticket->getAssignedTo()->getEmail1());
                                            //     $mailer->sendMailToSpool();
                                            // }
                                            //echo $this->renderView('UtilBundle:Mailing:ticket_answer_mail.html.twig', array('ticket' => $ticket));die;

                                            // Dejamos el locale tal y como estaba
                                            $request->setLocale($locale);
                                            $request->getSession()->set('message', '');
                                        }
                                    } elseif ($request->request->has("closeTicket")){
                                        //Define Post
                                        $post = UtilController::newEntity($post, $user);
                                        $post->setTicket($ticket);
                                        $closed = $em->getRepository('TicketBundle:Status')->findOneByName('closed');
                                        $ticket->setStatus($closed);
                                        $ticket->setSolution($post->getMessage());
                                        $ticket->setPending(0);
                                        $ticket->setBlockedBy(null);
                                        $ticket->setExpirationDate(null);
                                        UtilController::saveEntity($em, $ticket, $user);

                                        $mail = $ticket->getWorkshop()->getEmail1();
                                        $pos = strpos($mail, '@');
                                        if ($pos != 0 and $ticket->getWorkshop()->getActive()) {
                                            // Cambiamos el locale para enviar el mail en el idioma del taller
                                            $locale = $request->getLocale();
                                            $lang_w = $ticket->getWorkshop()->getCountry()->getLang();
                                            $lang   = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_w);
                                            $request->setLocale($lang->getShortName());

                                            /* MAILING */
                                            $mailer = $this->get('cms.mailer');
                                            $mailer->setTo($mail);
                                            $mailer->setSubject($this->get('translator')->trans('mail.closeTicket.subject').$ticket->getId());
                                            $mailer->setFrom('noreply@adserviceticketing.com');
                                            $mailer->setBody($this->renderView('UtilBundle:Mailing:ticket_close_mail.html.twig', array('ticket' => $ticket, '__locale' => $locale)));
                                            $mailer->sendMailToSpool();
                                            //echo $this->renderView('UtilBundle:Mailing:ticket_close_mail.html.twig', array('ticket' => $ticket));die;

                                            // Dejamos el locale tal y como estaba
                                            $request->setLocale($locale);
                                        }
                                        // //Si es el taller el que cierra, se le envia un mail al asesor asignado
                                        // if ($ticket->getAssignedTo() != null) {
                                        //    $mail = $ticket->getAssignedTo()->getEmail1();
                                        //    $pos = strpos($mail, '@');
                                        //    if ($pos != 0) {
                                        //        // Cambiamos el locale para enviar el mail en el idioma del taller
                                        //        $locale = $request->getLocale();
                                        //        $lang_w = $ticket->getWorkshop()->getCountry()->getLang();
                                        //        $lang   = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_w);
                                        //        $request->setLocale($lang->getShortName());

                                        //        /* MAILING */
                                        //        $mailer = $this->get('cms.mailer');
                                        //        $mailer->setTo($mail);
                                        //        $mailer->setSubject($this->get('translator')->trans('mail.closeTicket.subject').$ticket->getId());
                                        //        $mailer->setFrom('noreply@adserviceticketing.com');
                                        //        $mailer->setBody($this->renderView('UtilBundle:Mailing:ticket_close_mail.html.twig', array('ticket' => $ticket, '__locale' => $locale)));
                                        //        $mailer->sendMailToSpool();
                                        //        //echo $this->renderView('UtilBundle:Mailing:ticket_close_mail.html.twig', array('ticket' => $ticket));die;
                                        //        // Dejamos el locale tal y como estaba
                                        //        $request->setLocale($locale);
                                        //    }
                                        // }
                                    }
                                } else {
                                    $request->getSession()->set('message', $post->getMessage());
                                    $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans('error.txt_length_%numchars%', array('%numchars%' => $max_len)));
                                }
                            } else {
                                // ERROR tamaño
                                $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans('error.file_size'));

                                return $this->render('TicketBundle:Layout:show_ticket_layout.html.twig', $array);
                            }
                        } else {
                            // ERROR tipo de fichero
                            $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans('error.file'));

                            return $this->render('TicketBundle:Layout:show_ticket_layout.html.twig', $array);
                        }
                    }
                }

                return $this->redirect($this->generateUrl('showTicket', array('id' => $ticket->getId(),
                                    'form_name' => $formP->getName(),
                                    'action' => 'showTicket',
                                    'ticket' => $ticket,
                                    'systems' => $systems,
                                    'brand' => $brand,
                                    'model' => $model,
                                    'version' => $version)));
            }

            if ($this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR')) {
                $array['form'] = ($form->createView());
                return $this->render('TicketBundle:Layout:show_ticket_assessor_layout.html.twig', $array);
            } else
                return $this->render('TicketBundle:Layout:show_ticket_layout.html.twig', $array);
        }
        else {
            return $this->render('TwigBundle:Exception:exception_access.html.twig');
        }
    }

    /**
     * @Route("/post_edit/{id}")
     * @ParamConverter("post", class="TicketBundle:Post")
     */
    public function editPostAction(Request $request, $post) {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $ticket = $post->getTicket();

        $form = $this->createForm(PostType::class, $post);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $em->persist($post);
                $em->flush();
                return $this->redirect($this->generateUrl('showTicket', array('id' => $ticket->getId())));
                //return $this->showTicketAction($request, $ticket);
            }
        }

        return $this->render('TicketBundle:Post:edit_post.html.twig', array('post' => $post,
                    'ticket' => $ticket,
                    'form_name' => $form->getName(),
                    'form' => $form->createView()));
    }

    /**
     * Cierra el ticket
     * @Route("/ticket/close/{id}")
     * @ParamConverter("ticket", class="TicketBundle:Ticket")
     * @return url
     */
   public function closeTicketAction(Request $request, $id, $ticket, $close='1')
   {
       $message = '';

       if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')
       or (!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') and $ticket->getWorkshop()->getCountry()->getId() == $this->getUser()->getCountry()->getId())
       or ($this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR') and !$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
       ){
           $em = $this->getDoctrine()->getManager();

           if ($this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR') === false)   $form = $this->createForm(CloseTicketWorkshopType::class, $ticket);
           else                                                   $form = $this->createForm(new CloseTicketType()        , $ticket);

           if ($request->getMethod() == 'POST') {
               $form->handleRequest($request);
               if($request->request->get('sol_other_txt') != ''){
                   $message = $request->request->get('sol_other_txt');
               }
               else{
                   $message= $request->getSession()->get('message');
               }

               /*Validacion Ticket*/
               $str_len = strlen($message);
               if($this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR')) { $max_len = 10000; }
               else { $max_len = 500; }

               if ($str_len <= $max_len ) {

                   if ($form->isValid()) {

                       if ($this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR') === false) {
                           if     ($ticket->getSolution() == "0") $ticket->setSolution($this->get('translator')->trans('ticket.close_as_instructions'));
                           elseif ($ticket->getSolution() == "1") $ticket->setSolution($this->get('translator')->trans('ticket.close_irreparable_car'));
                           elseif ($ticket->getSolution() == "2") $ticket->setSolution($this->get('translator')->trans('ticket.close_other').': '.$message);
                       }

                       if($ticket->getSolution() != ""){

                            $closed = $em->getRepository('TicketBundle:Status')->findOneByName('closed');
                            $user   = $this->getUser();
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
                               $mailer->setBody($this->renderView('UtilBundle:Mailing:ticket_close_mail.html.twig', array('ticket' => $ticket, '__locale' => $locale)));
                               $mailer->sendMailToSpool();
                               //echo $this->renderView('UtilBundle:Mailing:ticket_close_mail.html.twig', array('ticket' => $ticket));die;

                               // Dejamos el locale tal y como estaba
                               $request->setLocale($locale);
                            }
                            //Si es el taller el que cierra, se le envia un mail al asesor asignado
                           //  if ($ticket->getAssignedTo() != null) {
                           //     $mail = $ticket->getAssignedTo()->getEmail1();
                           //     $pos = strpos($mail, '@');
                           //     if ($pos != 0) {

                           //         // Cambiamos el locale para enviar el mail en el idioma del taller
                           //         $locale = $request->getLocale();
                           //         $lang_w = $ticket->getWorkshop()->getCountry()->getLang();
                           //         $lang   = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_w);
                           //         $request->setLocale($lang->getShortName());

                           //         /* MAILING */
                           //         $mailer = $this->get('cms.mailer');
                           //         $mailer->setTo($mail);
                           //         $mailer->setSubject($this->get('translator')->trans('mail.closeTicket.subject').$id);
                           //         $mailer->setFrom('noreply@adserviceticketing.com');
                           //         $mailer->setBody($this->renderView('UtilBundle:Mailing:ticket_close_mail.html.twig', array('ticket' => $ticket, '__locale' => $locale)));
                           //         $mailer->sendMailToSpool();
                           //         //echo $this->renderView('UtilBundle:Mailing:ticket_close_mail.html.twig', array('ticket' => $ticket));die;

                           //         // Dejamos el locale tal y como estaba
                           //         $request->setLocale($locale);
                           //     }
                           // }

                           return $this->redirect($this->generateUrl('showTicket', array('id' => $id) ));
                       }
                       else{
                           $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans('error.msg_solution'));
                       }
                   }else{
                       $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans('error.bad_introduction'));
                   }
               }else{
                   $request->getSession()->set('message', $message);
                $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans('error.txt_length_%numchars%', array('%numchars%' => $max_len)));

               }
           }

           $systems = $em->getRepository('TicketBundle:System')->findAll();

           return $this->render('TicketBundle:Layout:close_ticket_layout.html.twig', array('ticket'    => $ticket,
                                                                                           'message'   => $message,
                                                                                           'systems'   => $systems,
                                                                                           'form'      => $form->createView(),
                                                                                           'form_name' => $form->getName(),
                                                                                           'close'     => $close ));
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
    public function editDescriptionAction(Request $request, $id, $ticket) {

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR')) {
            $em = $this->getDoctrine()->getManager();


            $form = $this->createForm(EditDescriptionType::class, $ticket);

            if ($request->getMethod() == 'POST') {
                $form->handleRequest($request);

                /* Validacion Ticket */
                $str_len = strlen($ticket->getSolution());
                $max_len = 10000;

                if ($str_len <= $max_len) {

                    if ($form->isValid()) {

                        if ($ticket->getDescription() != "") {

                            $user = $this->getUser();

                            UtilController::saveEntity($em, $ticket, $user);

                            return $this->redirect($this->generateUrl('showTicket', array('id' => $id)));
                        } else {
                            $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans('error.msg_solution'));
                        }
                    } else {
                        $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans('error.bad_introduction'));
                    }
                } else {
                    $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans('error.txt_length_%numchars%', array('%numchars%' => $max_len)));
                }
            }

            return $this->render('TicketBundle:Layout:edit_description_layout.html.twig', array('ticket' => $ticket,
                        'form' => $form->createView(),
                        'form_name' => $form->getName(),));
        } else {
            return $this->render('TwigBundle:Exception:exception_access.html.twig');
        }
    }

    /**
     * Muestra una lista de motores
     * @return url
     */
    public function listMotorsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $motors = $em->getRepository('CarBundle:Motor')->findBy(array(), array('name' => 'ASC'));

        $query = $em->createQuery('SELECT m FROM CarBundle:Version v, CarBundle:Motor m
                                        WHERE m.id = v.motor
                                        AND v.motor IN (
                                            SELECT mt FROM CarBundle:Motor mt)
                                        GROUP BY m.name
                                        ORDER BY m.name');
        $motors = $query->getResult();

        return $this->render('TicketBundle:Layout:show_motors_layout.html.twig', array('motors' => $motors));
    }

    /**
     * Reabre el ticket
     * @Route("/ticket/reopen/{id}/")
     * @ParamConverter("ticket", class="TicketBundle:Ticket")
     * @return url
     */
    public function reopenTicketAction(Request $request, $id, $ticket) {
        $em = $this->getDoctrine()->getManager();



        $user = $this->getUser();
        $status = $em->getRepository('TicketBundle:Status')->findOneByName('open');

        $ticket->setStatus($status);
        $ticket->setPending(1);
        UtilController::saveEntity($em, $ticket, $user);

        $mail = $ticket->getWorkshop()->getEmail1();
        $pos = strpos($mail, '@');
        if ($pos != 0) {

            // Cambiamos el locale para enviar el mail en el idioma del taller
            $locale = $request->getLocale();
            $lang_w = $ticket->getWorkshop()->getCountry()->getLang();
            $lang = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_w);
            $request->setLocale($lang->getShortName());

            /* MAILING */
            $mailer = $this->get('cms.mailer');
            $mailer->setTo($mail);
            $mailer->setSubject($this->get('translator')->trans('mail.reopenTicket.subject') . $id);
            $mailer->setFrom('noreply@adserviceticketing.com');
            $mailer->setBody($this->renderView('UtilBundle:Mailing:ticket_reopen_mail.html.twig', array('ticket' => $ticket, '__locale' => $locale)));
            $mailer->sendMailToSpool();
            //echo $this->renderView('UtilBundle:Mailing:ticket_reopen_mailecho 'pasa';.html.twig', array('ticket' => $ticket));die;
            // Dejamos el locale tal y como estaba
            $request->setLocale($locale);
        }

        return $this->redirect($this->generateUrl('showTicket', array('id' => $id)));
    }

    /**
     * Obtiene todos los talleres del usuario logeado
     */
    public function workshopListAction(Request $request, $page = 1, $option = null) {
        $em = $this->getDoctrine()->getManager();

        $params[] = array();

        $pagination = new Pagination($page);

        $workshops = $pagination->getRows($em, 'WorkshopBundle', 'Workshop', $params, $pagination);

        $length = $pagination->getRowsLength($em, 'WorkshopBundle', 'Workshop', $params);

        $pagination->setTotalPagByLength($length);

        return $this->render('TicketBundle:Workshop:list_workshop.html.twig', array('workshops' => $workshops,
                    'pagination' => $pagination));
    }

    /**
     * A partir de un $id_taller, la vista listará todos sus tickets i se le podrá asignar un usuario
     * @param Int $id_workshop
     * @return type
     */
    public function getTicketsFromWorkshopAction(Request $request, $id_workshop, $page = 1) {
        $em = $this->getDoctrine()->getManager();

        $params = array();
        $params[] = array('workshop', ' = ' . $id_workshop);

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
    public function assignUserToTicketAction(Request $request, $ticket, $id_user = null) {
        $em = $this->getDoctrine()->getManager();

        //id_user puede venir por parametro o por post
        if ($id_user == null) {
            $id_user = $request->get('id_user');
        }

        //si $id_user != null ---> viene de parametro de la funcion o de POST y queremos asignar
        //si $id_user == null ---> queremos desasignar
        if ($id_user != null) {
            $user = $em->getRepository('UserBundle:User')->find($id_user);
            $this->assignTicket($ticket, $user);
        } else {
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
    public function assignTicketSelectUserAction(Request $request, $ticket) {
        $em = $this->getDoctrine()->getManager();
        $users = $this->getUsersToAssingFromTicket($ticket);

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
    public function blockTicketAction(Request $request, $ticket, $id_user = null) {

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UserBundle:User')->find($id_user);

        if ($user != null and $id_user != 0) {

            $ticket->setBlockedBy($user);
            $this->assignTicket($ticket, $user);

            UtilController::saveEntity($em, $ticket, $user);

            if ($ticket->getAssignedTo() == null) {
                $this->assignTicket($ticket, $user);
            } else {
                $em->persist($ticket);
                $em->flush();
            }
        } else {
            $ticket->setBlockedBy(null);
            $em->persist($ticket);
            $em->flush();
        }

        return $this->showTicketAction($request, $ticket);
    }

    /**
     * Funcion que devuelve un listado de tickets filtrados a partir de una opcion de un combo ($option)
     * @return array
     */
    public function getTicketsByOption(Request $request, $option) {

        $em = $this->getDoctrine()->getManager();

        $tickets = array();
        $check_id = $request->request->get('filter_id');
        $user = $this->getUser();
        $repoTicket = $em->getRepository('TicketBundle:Ticket');
        $open = $em->getRepository('TicketBundle:Status')->findOneBy(array('name' => 'open'));
        $closed = $em->getRepository('TicketBundle:Status')->findOneBy(array('name' => 'closed'));

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            //Admin
            if ($option == 'all') {
                $tickets = $repoTicket->findAll();
            } elseif ($option == 'opened') {
                $tickets = $repoTicket->findAllStatus($em, $open);
            } elseif ($option == 'closed') {
                $tickets = $repoTicket->findAllStatus($em, $closed);
            } elseif ($option == 'free') {
                $tickets = $repoTicket->findAllFree($em, $open);
            } elseif ($option == 'pending') {
                $tickets = $repoTicket->findAllPending($em, $open);
            } elseif ($option == 'answered') {
                $tickets = $repoTicket->findAllAnswered($em, $open);
            }

            if ($check_id != 'all') {
                $tickets = $this->filterTickets($tickets, $check_id);
            }
        } elseif ($this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR')) {
            //Assessor
            if ($option == 'free') {
                $tickets = $repoTicket->findAllFree($em, $open);
            } elseif ($option == 'assessor_pending') {
                $tickets = $repoTicket->findAllFromUser($em, $open, true, true, $user, true);
            } elseif ($option == 'assessor_answered') {
                $tickets = $repoTicket->findOption($em, $user, $open, 'assessor_answered', 'DESC');
            } elseif ($option == 'assessor_closed') {
                $tickets = $repoTicket->findOption($em, $user, $closed, 'assessor_closed');
            } elseif ($option == 'other_pending') {
                $tickets = $repoTicket->findOption($em, $user, $open, 'other_pending');
            } elseif ($option == 'other_answered') {
                $tickets = $repoTicket->findOption($em, $user, $open, 'other_answered', 'DESC');
            } elseif ($option == 'other_closed') {
                $tickets = $repoTicket->findOption($em, $user, $closed, 'other_closed');
            } elseif ($option == 'all') {
                $tickets = $repoTicket->findAll();
            }

            if ($check_id != 'all') {
                $tickets = $this->filterTickets($tickets, $check_id);
            }
        } else {

            if ($check_id == 'all') {

                $check_status = $request->request->get('status');

                if ($check_status == 'all') {
                    $status = 'all';
                } elseif ($check_status == 'open') {
                    $status = $open;
                } elseif ($check_status == 'closed') {
                    $status = $closed;
                }

                //User
                if ($option == 'created_by')
                    $tickets = $repoTicket->findAllByOwner($user, $status);
                elseif ($option == 'workshop')
                    $tickets = $repoTicket->findAllByWorkshop($user, $status);
            }else {
                $array = array('id' => $check_id);
                $tickets = $repoTicket->findBy($array);
            }
        }
        return $tickets;
    }

    /**
     * Devuelve un ticket segun la id enviada por parametro
     * @return url
     */
    public function findTicketByIdAction(Request $request) {
        $em = $this->getDoctrine()->getManager();


        $id = $request->get('flt_id');
        $catserv = $this->getUser()->getCategoryService();
        $user = $this->getUser();
        if($catserv != null)
            $ticket = $em->getRepository('TicketBundle:Ticket')->findOneBy(array('id' => $id, 'category_service' => $catserv->getId()));
        else
            $ticket = $em->getRepository('TicketBundle:Ticket')->find($id);

        if ($ticket)
            $tickets = array($ticket);
        else
            $tickets = array();
        $b_query   = $em->createQuery('SELECT b FROM CarBundle:Brand b, CarBundle:Model m WHERE b.id = m.brand ORDER BY b.name');
//        if($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') || $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
//           $b_query   = $em->createQuery('SELECT b FROM CarBundle:Brand b, CarBundle:Model m WHERE b.id = m.brand ORDER BY b.name');
//        }
//        else{
//            $b_query   = $em->createQuery('SELECT b FROM CarBundle:Brand b, CarBundle:Model m WHERE b.id = m.brand AND b.id <> 0 ORDER BY b.name');
//        }
        $brands = $b_query->getResult();
        $systems = $em->getRepository('TicketBundle:System')->findBy(array(), array('name' => 'ASC'));
        $countries = $em->getRepository('UtilBundle:Country')->findAll();
        $importances = $em->getRepository('TicketBundle:Importance')->findAll();
        $catservices = $em->getRepository('UserBundle:CategoryService')->findAll();
        $languages = $em->getRepository('UtilBundle:Language')->findAll();
        if($this->getUser()->getCategoryService() != NULL) {
            $advisers = $em->getRepository("UserBundle:User")->findByRole($em, 0, $user->getCategoryService()->getId(), 'ROLE_ASSESSOR');
        }else{
            $advisers = $em->getRepository("UserBundle:User")->findByRole($em, 0, 0, 'ROLE_ASSESSOR');
        }
        $array = array('workshop' => new Workshop(),
            'pagination' => new Pagination(0),
            'tickets' => $tickets,
            'brands' => $brands,
            'systems' => $systems,
            'countries' => $countries,
            'importances' => $importances,
            'catservices' => $catservices,
            'languages' => $languages,
            'lang' => '0',
            'option' => 'all',
            'page' => 0,
            'num_rows' => 10,
            'country' => 0,
            'inactive' => 0,
            'disablePag' => 0,
            'advisers' => $advisers);

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR') and ! $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
            return $this->render('TicketBundle:Layout:list_ticket_assessor_layout.html.twig', $array);
        else
            return $this->render('TicketBundle:Layout:list_ticket_layout.html.twig', $array);
    }

    /**
     * Devuelve un ticket segun la id enviada por parametro y el taller
     * @return url
     */
    public function findTicketByIdAndWorkshopAction(Request $request) {
        $em = $this->getDoctrine()->getManager();


        $id = $request->get('flt_id');
        $workshop = $this->getUser()->getWorkshop();
        $ticket = $em->getRepository('TicketBundle:Ticket')->find($id);
        if ($workshop != null and $ticket) {
            return $this->redirect($this->generateUrl('showTicket', array('id' => $ticket->getId())));
        } else {
            if ($ticket and ( $ticket->getWorkshop()->getCountry()->getId() == $this->getUser()->getCountry()->getId()))
                return $this->redirect($this->generateUrl('showTicket', array('id' => $ticket->getId())));
        }
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR') and ! $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
            return $this->redirect($this->generateUrl('listTicket', array('option' => "assessor_pending")));
        else {
            return $this->redirect($this->generateUrl('listTicket'));
        }
    }

    /**
     * Devuelve un ticket segun la id enviada por parametro
     * @return url
     */
    public function findTicketByBMVAction(Request $request, $page = 1, $brand = 0, $model = 0, $version = 0, $system = 0, $subsystem = 0, $importance = 0, $year = 0, $motor = 0, $kw = 0, $num_rows = 10) {
        $em = $this->getDoctrine()->getManager();

        $params = array();
        if ($brand != '0' and $brand != '')
            $params[] = array('brand', ' = ' . $brand);
        if ($model != '0' and $model != '')
            $params[] = array('model', ' = ' . $model);
        if ($version != '0' and $version != '')
            $params[] = array('version', ' = ' . $version);

        if ($year != '0' and $year != '')
            $params[] = array('year', " LIKE '%" . $year . "%' ");
        if ($motor != '0' and $motor != '')
            $params[] = array('motor', " LIKE '%" . $motor . "%' ");
        if ($kw != '0' and $kw != '')
            $params[] = array('kw', ' = ' . $kw);
        if($this->getUser()->getCategoryService() != NULL) {
            $advisers = $em->getRepository("UserBundle:User")->findByRole($em, 0, $user->getCategoryService()->getId(), 'ROLE_ASSESSOR');
        }else{
            $advisers = $em->getRepository("UserBundle:User")->findByRole($em, 0, 0, 'ROLE_ASSESSOR');
        }
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
        if ($size > 0) {

            for ($i = 0; $i < $size; $i++) {

                $id = $cars[$key[$i]]->getId();
                if ($subsystem == 0) {
                    $ticket = $em->getRepository('TicketBundle:Ticket')->findBy(array('car' => $id));
                } else {
                    $ticket = $em->getRepository('TicketBundle:Ticket')->findBy(array('car' => $id, 'subsystem' => $subsystem));
                }
            }
        } else {
            $ticket = $em->getRepository('TicketBundle:Ticket')->findOneBy(array('subsystem' => $subsystem));
        }
        if (sizeof($ticket) >= 1) {
            foreach ($ticket as $tck) {
                if ($tck and ( $tck->getWorkshop()->getCountry()->getId() == $this->getUser()->getCountry()->getId() or $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN'))) {
                    $tickets[] = $tck;
                }
            }
        } else {
            if ($ticket and ( $ticket->getWorkshop()->getCountry()->getId() == $this->getUser()->getCountry()->getId() or $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN'))) {
                $tickets[] = $ticket;
            }
        }
        $b_query = $em->createQuery('SELECT b FROM CarBundle:Brand b, CarBundle:Model m WHERE b.id = m.brand ORDER BY b.name');
        $brands = $b_query->getResult();
        $systems = $em->getRepository('TicketBundle:System')->findBy(array(), array('name' => 'ASC'));
        $countries = $em->getRepository('UtilBundle:Country')->findAll();
        $importances = $em->getRepository('TicketBundle:Importance')->findAll();

        $array = array('workshop' => new Workshop(),
            'pagination' => new Pagination(0),
            'brand' => $brand,
            'model' => $model,
            'version' => $version,
            'system' => $system,
            'subsystem' => $subsystem,
            'importance' => $importance,
            'year' => $year,
            'motor' => $motor,
            'kw' => $kw,
            'num_rows' => $num_rows,
            'tickets' => $tickets,
            'brands' => $brands,
            'systems' => $systems,
            'countries' => $countries,
            'importances' => $importances,
            'option' => 'all',
            'page' => $page,
            'country' => 0,
            'inactive' => 0,
            'disablePag' => 0,
            'advisers' => $advisers);
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR') and ! $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
            return $this->render('TicketBundle:Layout:list_ticket_assessor_layout.html.twig', $array);
        else
            return $this->render('TicketBundle:Layout:list_ticket_layout.html.twig', $array);
    }

    /**
     * Devuelve un ticket segun la id enviada por parametro
     * @return url
     */
    public function findAssessorTicketByBMVAction(Request $request, $page = null) {
        
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if(!$user){ return $this->redirect($this->generateUrl('user_login'));}
        $page = $request->request->get('ftbmv_page');
        if (!isset($page) or $page == 0)
            $page = 1;
        // WORKSHOP
        $codepartner = $request->get('ftbmv_codepartner');
        $codeworkshop = $request->get('ftbmv_codeworkshop');
        $email = $request->get('ftbmv_email');
        $phone = $request->get('ftbmv_phone');

        // CAR
        $brand = $request->request->get('new_car_form_brand');
        $model = $request->request->get('new_car_form_model');
        $version = $request->request->get('new_car_form_version');
        $year = $request->request->get('new_car_form_year');
        $motor = $request->request->get('new_car_form_motor');
        $kw = $request->request->get('new_car_form_kW');
        $importance = $request->request->get('new_car_form_importance');
        $system = $request->request->get('id_system');
        $subsystem = $request->request->get('new_car_form_subsystem');
        $displacement = $request->request->get('new_car_form_displacement');
        $vin = $request->request->get('new_car_form_vin');
        $plateNumber = $request->request->get('new_car_form_plateNumber');
        
        if ($model != '0' and $version == '0'){
            $version = '';
        }
        if ($plateNumber == null){
            $plateNumber = $request->request->get('new_car_form_plate_number');
        }
        $workshop = new Workshop();

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR')){
            $workshop = $user->getWorkshop();
        }
        elseif (isset($codepartner) and isset($codeworkshop) and $codepartner != '' and $codeworkshop != ''){
            $workshop = $em->getRepository('WorkshopBundle:Workshop')->findOneBy(array('code_workshop' => $codeworkshop, 'code_partner' => $codepartner));
        }

        $pagination = new Pagination($page);
        $num_rows = ($request->request->get('slct_numRows') > 0 ) ? $request->request->get('slct_numRows') : 10;
        $pagination->setMaxRows($num_rows);

        $ordered = array('e.modified_at', 'DESC');
        
        $conditions[] =  "ca.id = e.car ";
        $params = array();
        if($plateNumber != null){
            $conditions[] = "ca.plateNumber = '". $plateNumber."'";
        }
        if($version != null){
            $conditions[] = "ca.version = " . $version;
        }
        elseif($model != null){
            $conditions[] = 'ca.model = ' . $model;
        }
        elseif($brand != null){
            $conditions[] = 'ca.brand = ' . $brand;
        }
        
        $joins[] = array('e.car ca ', implode(" AND ",$conditions)); 
         
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') AND $user->getCategoryService() != NULL) {
            $catserv = $user->getCategoryService()->getId();
        }
        else{
            $catserv = null;
        }
        if (isset($subsystem) and $subsystem != null){
            $params[] = array('subsystem', ' = ' . $subsystem);
        } 
        if(isset($catserv) && $catserv != null){
            $params[] = array('category_service', ' = ' . $catserv);
        }
        if($workshop->getId() != null) {
            $params[] = array('workshop', ' = ' . $workshop->getId());
        }
        $tickets = $pagination->getRows($em, 'TicketBundle', 'Ticket', $params, $pagination, $ordered, $joins);
        $length = $pagination->getRowsLength($em, 'TicketBundle', 'Ticket', $params, $ordered, $joins);
        $pagination->setTotalPagByLength($length);
        
        $b_query = $em->createQuery('SELECT b FROM CarBundle:Brand b, CarBundle:Model m WHERE b.id = m.brand ORDER BY b.name');
        $brands = $b_query->getResult();
        $countries = $em->getRepository('UtilBundle:Country')->findAll();
        $systems = $em->getRepository('TicketBundle:System')->findBy(array(), array('name' => 'ASC'));
        $importances = $em->getRepository('TicketBundle:Importance')->findAll();
        $advisers = array();
        
        if($this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR')) {
            if($user->getCategoryService() != NULL) {
                $catservices[0] = $em->getRepository('UserBundle:CategoryService')->find($user->getCategoryService());
                $advisers = $em->getRepository("UserBundle:User")->findByRole($em, 0, $user->getCategoryService()->getId(), 'ROLE_ASSESSOR');
            }else{
                $catservices = $em->getRepository('UserBundle:CategoryService')->findAll();
                $advisers = $em->getRepository("UserBundle:User")->findByRole($em, 0, 0, 'ROLE_ASSESSOR');
            }            
        }else{
            $catservices = $em->getRepository('UserBundle:CategoryService')->findAll();
        }
        $languages = $em->getRepository('UtilBundle:Language')->findAll();

        $lang = $request->get('lang');
        if (isset($model) and $model != null and $model !='') {
            $model = $em->getRepository('CarBundle:Model')->find($model);
        }

        if (isset($version) and $version != '' and $version != null){
            $version = $em->getRepository('CarBundle:Version')->findOneById($version);
        }
        if (isset($subsystem) and $subsystem != '0' and $subsystem != ''){
            $subsystem = $em->getRepository('TicketBundle:Subsystem')->find($subsystem);
        }
        if (sizeof($tickets) == 0)
            $pagination = new Pagination(0);

        if($brand == '') {
            $brand = null;
        }
        //Al llamar a esta funcion se pierde la version del coche, aqui la volvemos a asignar
        $array = array('workshop' => $workshop,
            'pagination' => $pagination,
            'codepartner' => $codepartner,
            'codeworkshop' => $codeworkshop,
            'email' => $email,
            'phone' => $phone,
            'brand' => $brand,
            'model' => $model,
            'version' => $version,
            'year' => $year,
            'motor' => $motor,
            'kw' => $kw,
            'importance' => $importance,
            'system' => $system,
            'subsystem' => $subsystem,
            'displacement' => $displacement,
            'vin' => $vin,
            'plateNumber' => $plateNumber,
            'tickets' => $tickets,
            'brands' => $brands,
            'systems' => $systems,
            'countries' => $countries,
            'id_catserv' => $catserv,
            'catserv' => $catserv,
            'catservices' => $catservices,
            'languages' => $languages,
            'lang' => $lang,
            'importances' => $importances,
            'option' => 'all',
            'page' => $page,
            'num_rows' => $num_rows,
            'country' => 0,
            'inactive' => 0,
            'disablePag' => 0,
            'advisers' => $advisers);
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR') and ! $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
            return $this->render('TicketBundle:Layout:list_ticket_assessor_layout.html.twig', $array);
        else
            return $this->render('TicketBundle:Layout:list_ticket_layout.html.twig', $array);
    }

    /**
     * Devuelve todos los usuarios que podran ser asignados a un ticket (admins i asesores has nuevo aviso)
     * @param type $id_ticket
     */
    private function getUsersToAssingFromTicket($ticket=null) {
        $em = $this->getDoctrine()->getManager();

        $query = "SELECT u FROM UserBundle:User u INNER JOIN u.user_role r WHERE r.name = 'ROLE_ASSESSOR' AND u.active = 1";

        if($ticket != null AND $ticket->getCategoryService() != NULL) {
            $query .=" AND (u.category_service IS NULL OR u.category_service = ".$ticket->getCategoryService()->getId().") ";
        }
        $query .= " ORDER BY u.name ASC";

        $consulta = $em->createQuery($query);
        return $consulta->getResult();
    }

    /**
     * Asigna un $ticket a un $user
     * Si $user == NULL, se desasigna
     * @param Ticket $ticket
     * @param User $user
     */
    private function assignTicket($ticket, $user = null) {
        $em = $this->getDoctrine()->getManager();

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
    private function filterTickets($tickets, $check_id) {
        $tickets_filtered = array();

        foreach ($tickets as $ticket) {

            if ($ticket->getId() == $check_id)
                $tickets_filtered[] = $ticket;
        }
        return $tickets_filtered;
    }

    /**
     * Elimina los caracteres extraños de una consulta (afectan a la ejecucion del SQL)
     * @param  String $str
     * @return string
     */
    private function fixWrongCharacters($str) {

        $str = str_replace("'", '"', $str);

        return $str;
    }

    private function deleteSessionCar(){
        $this->get('session')->set('marca', null);
        $this->get('session')->set('modelo', null);
        $this->get('session')->set('description', null);
        $this->get('session')->set('plateNumber', null);
        $this->get('session')->set('vin', null);
        $this->get('session')->set('displacement', null);
        $this->get('session')->set('kW', null);
        $this->get('session')->set('importance', null);
        $this->get('session')->set('subsystem', null);
        $this->get('session')->set('system', null);
        $this->get('session')->set('autologin', null);
                
        
        
    }
    
// /**
    //  * Devuelve todos los tickets realizados
    //  * @return url
    //  */
    // public function listTicketFilteredAction(Request $request, $page=1, $id_workshop='none', $id_ticket='none', $status='all', $option='all')
    // {
    //     $em = $this->getDoctrine()->getManager();
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
