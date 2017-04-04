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
    public function callTicketAction($code_partner = 0, $code_workshop = 0) {
        $security = $this->get('security.context');
        if (!$security->isGranted('ROLE_ASSESSOR'))
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();
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
    public function listTicketAction($page = 1, $num_rows = 10, $country = 0, $lang = 0, $catserv = 0, $option = null, $workshop_id = null, $adviser_id = null) {
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        $security = $this->get('security.context');
        $user = $security->getToken()->getUser();
        $catservId = $catserv ;
        if($user == 'anon.') return $this->redirect($this->generateUrl('user_login'));

        $id_user = $user->getId();
        $open = $em->getRepository('TicketBundle:Status')->findOneBy(array('name' => 'open'));
        $closed = $em->getRepository('TicketBundle:Status')->findOneBy(array('name' => 'closed'));
        $inactive = $em->getRepository('TicketBundle:Status')->findOneBy(array('name' => 'inactive'));
        $expirated = $em->getRepository('TicketBundle:Status')->findOneBy(array('name' => 'expirated'));
        $params = array();
        $joins = array();

        /* Entrada desde Centralita al listado con el taller seleccionado */
        if ($security->isGranted('ROLE_ASSESSOR') and $workshop_id != null) {
            $workshop = $em->getRepository('WorkshopBundle:Workshop')->find($workshop_id);
            $workshops = array('0' => $workshop);
        } else
            $workshops = array('0' => new Workshop());

        /* TRATAMIENTO DE LAS OPCIONES DE slct_historyTickets */
        $this->get('session')->setFlash('error', null);
        if ($option == null) {
            // Si se envia el codigo del taller se buscan los tickets en funcion de estos
            if ($request->getMethod() == 'POST') {
                $workshops = $em->getRepository('WorkshopBundle:Workshop')->findWorkshopInfo($request, $security);

                if (!empty($workshops)) {
                    if ($workshops[0]->getActive() == 0) {
                        $error = $this->get('translator')->trans('workshop_inactive');
                        $this->get('session')->setFlash('error', '¡Error! ' . $error);
                    }

                    if (isset($workshops[0]) and $workshops[0]->getId() != "") {
                        $joins[] = array('e.workshop w ', 'w.code_workshop = ' . $workshops[0]->getCodeWorkshop() . " AND w.code_partner = " . $workshops[0]->getCodepartner() . " ");
                        $option = $workshops[0]->getId();
                    } elseif (isset($workshops['error'])) {
                        $error = $this->get('translator')->trans('workshop_inactive');
                        $this->get('session')->setFlash('error', '¡Error! ' . $error);
                    } else {
                        $joins[] = array();
                    }
                } else {
                    $error = $this->get('translator')->trans('workshop_inactive');
                    $this->get('session')->setFlash('error', '¡Error! ' . $error);
                }
            } elseif (!$security->isGranted('ROLE_ASSESSOR') and !$security->isGranted('ROLE_COMMERCIAL')) {
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

        if ($security->isGranted('ROLE_SUPER_ADMIN')) {
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
        elseif ($security->isGranted('ROLE_ADMIN') and ! $security->isGranted('ROLE_SUPER_ADMIN')) {
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
                    if ($security->isGranted('ROLE_ASSESSOR'))
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

        if($security->isGranted('ROLE_ASSESSOR') AND $user->getCategoryService() != NULL) {
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

        if (($security->isGranted('ROLE_SUPER_ADMIN')) or ( isset($workshops[0]) and ( $workshops[0]->getId() != null))) {
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
            } elseif ($security->isGranted('ROLE_USER') and ! $security->isGranted('ROLE_ASSESSOR')) {
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
            $this->get('session')->setFlash('error', $error);
        }

        if ($option == null)
            $option = 'all';

        $b_query   = $em->createQuery('SELECT b FROM CarBundle:Brand b, CarBundle:Model m WHERE b.id = m.brand ORDER BY b.name');
        $brands = $b_query->getResult();
        $systems = $em->getRepository('TicketBundle:System')->findBy(array(), array('name' => 'ASC'));
        if ($security->isGranted('ROLE_ASSESSOR') and ! $security->isGranted('ROLE_ADMIN'))
            $countries = $em->getRepository('UtilBundle:CountryService')->findAll();
        else
            $countries = $em->getRepository('UtilBundle:Country')->findAll();

        if($security->isGranted('ROLE_ASSESSOR')) {
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
            $pagination = new Pagination(0);
        
        $array = array('workshop' => $workshops[0], 'pagination' => $pagination, 'tickets' => $tickets,
            'country' => $country, 'lang' => $lang, 'catserv' => $catserv, 'num_rows' => $num_rows, 'option' => $option, 'brands' => $brands,
            'systems' => $systems, 'countries' => $countries, 'catservices' => $catservices, 'languages' => $languages,
            'adsplus' => $adsplus, 'inactive' => $inactive, 't_inactive' => $t_inactive, 'importances' => $importances,
            'advisers' => $advisers, 'adviser_id' => $adviser_id, 'workshop_id' => $workshop_id
        );
        if($security->getToken()->getUser()->getCategoryService() != null){
            $array['id_catserv'] = $security->getToken()->getUser()->getCategoryService()->getId();
        }else{
            $array['id_catserv'] = 0;
        }
            

        if ($security->isGranted('ROLE_ADMIN'))
            return $this->render('TicketBundle:Layout:list_ticket_layout.html.twig', $array);
        elseif ($security->isGranted('ROLE_ASSESSOR')) {
            return $this->render('TicketBundle:Layout:list_ticket_assessor_layout.html.twig', $array);
        } else
            return $this->render('TicketBundle:Layout:list_ticket_layout.html.twig', $array);
    }

    /**
     * Crea un ticket abierto con sus respectivos post y car
     * @return url
     */
    public function newTicketAction($id_workshop = null, $einatech = 0) {
        
        $em = $this->getDoctrine()->getEntityManager();
        $security = $this->get('security.context');
        $request = $this->getRequest();
        $trans = $this->get('translator');
        $ticket = new Ticket();
        $car = new Car();
        $document = new Document();
        $_SESSION['einatech'] = $einatech;
        
        $id_catserv = 0;
        if($security->getToken()->getUser()->getCategoryService() != null){
            $id_catserv = $security->getToken()->getUser()->getCategoryService()->getId();
        }
        if($id_catserv != 0 && $id_catserv != 2 && $id_catserv != 4){
            $_SESSION['einatech'] = 2;
        }
        if ($security->isGranted('ROLE_USER') and !$security->isGranted('ROLE_ADMIN') and !$security->isGranted('ROLE_ASSESSOR') and $einatech == 0 ){
            $_SESSION['einatech'] = 2;
        }
        if ($id_workshop != null) {
            $workshop = $em->getRepository('WorkshopBundle:Workshop')->find($id_workshop);
        } else {
            $workshop = new Workshop();
        }

        // TODO
        // Ha aparecido un error en el formulario que no recoge los datos de Marca, Modelo, Gama y Subsistema.
        // Al no encontrar solucion aparente cargaremos los datos desde $request
        //
        $id_brand = $request->request->get('new_car_form_brand');
        $id_model = $request->request->get('new_car_form_model');
        $id_version = $request->request->get('new_car_form_version');
        if(isset($request->request->get('ticket_form')['subsystem'])){
            $id_subsystem = $request->request->get('ticket_form')['subsystem'];
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

            $version = $em->getRepository('CarBundle:Version')->findById($id_version);
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
        if($security->isGranted('ROLE_SUPER_ADMIN') || $security->isGranted('ROLE_ADMIN')){
            $b_query   = $em->createQuery('SELECT b FROM CarBundle:Brand b, CarBundle:Model m WHERE b.id = m.brand ORDER BY b.name');
        }
        else{
            $b_query   = $em->createQuery('SELECT b FROM CarBundle:Brand b, CarBundle:Model m WHERE b.id = m.brand AND b.id <> 0 ORDER BY b.name');
        }
        $brands = $b_query->getResult();
        $adsplus = $em->getRepository('WorkshopBundle:ADSPlus')->findOneBy(array('idTallerADS' => $workshop->getId()));

        //Define Forms
        $form = $this->createForm(new NewTicketType(), $ticket);
        $formC = $this->createForm(new CarType(), $car);
        $formD = $this->createForm(new DocumentType(), $document);
        $textarea_content = "";
        if($einatech == 1){
             $textarea_content = $this->get('translator')->trans('einatech_textarea_default');
         }
        if (isset($open_newTicket) and $open_newTicket == '1' and $request->getMethod() == 'POST') {
            //campos comunes
            $user = $em->getRepository('UserBundle:User')->find($security->getToken()->getUser()->getId());
            $status = $em->getRepository('TicketBundle:Status')->findOneByName('open');
            
            $form->bindRequest($request);
            $formC->bindRequest($request);
            $formD->bindRequest($request);
           
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
                    'form_name' => $form->getName());

                if ($ticket->getSubsystem() != "" or $security->isGranted('ROLE_ASSESSOR') == 0) {
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
                                            AND c.model = " . $id_model . " ";

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
                                if ($security->isGranted('ROLE_ASSESSOR') or $size <= 10240000) {
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
                                                    // OR (
                                                    //     ($exist_vin->getVersion() != null AND $car->getVersion() == null)
                                                    //     OR
                                                    //     ($exist_vin->getVersion() == null AND $car->getVersion() != null)
                                                    //     )
                                                    OR (
                                                    $exist_vin->getVersion() != null
                                                    AND
                                                    $car->getVersion() != null
                                                    AND

                                                    // $exist_vin->getVersion()->getName() != null
                                                    // AND
                                                    // $car->getVersion()->getName() != null
                                                    // AND
                                                    // $exist_vin->getVersion()->getName() != $car->getVersion()->getName()

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

                                                UtilController::saveEntity($em, $car, $user);
                                                
                                                
                                                //Define TICKET
                                                $ticket = UtilController::newEntity($ticket, $user);
                                                if ($security->isGranted('ROLE_ASSESSOR')) {
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
                                                    if($security->isGranted('ROLE_ADMIN')) {
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
                                                $this->get('session')->setFlash('error', $exist_car);

                                                return $this->render('TicketBundle:Layout:new_ticket_layout.html.twig', $array);
                                            }
                                        } else {
                                            $this->get('session')->setFlash('error', $trans->trans('ticket_vin_error_o'));

                                            return $this->render('TicketBundle:Layout:new_ticket_layout.html.twig', $array);
                                        }
                                    } else {
                                        $this->get('session')->setFlash('error', $trans->trans('ticket_vin_error_length'));

                                        return $this->render('TicketBundle:Layout:new_ticket_layout.html.twig', $array);
                                    }
                                } else {
                                    // ERROR tamaño
                                    $this->get('session')->setFlash('error', $trans->trans('error.file_size'));

                                    return $this->render('TicketBundle:Layout:new_ticket_layout.html.twig', $array);
                                }
                            } else {
                                // ERROR tipo de fichero
                                $this->get('session')->setFlash('error', $trans->trans('error.file'));

                                return $this->render('TicketBundle:Layout:new_ticket_layout.html.twig', $array);
                            }
                        } else {
                            // ERROR tipo de fichero
                            $this->get('session')->setFlash('error', $trans->trans('error.same_ticket')
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

                            if (!$security->isGranted('ROLE_ASSESSOR') and $security->isGranted('ROLE_USER')) {
                                if($ticket->getImportance()->getId() == 5){
                                    $mail_centralita = $this->container->getParameter('mail_einatech');
                                    $mailer->setSubject('ticket: ' . $ticket->getId());
                                }
                                else {
                                    $mail_centralita = $this->container->getParameter('mail_centralita');
                                    $mailer->setSubject('ticket: ' . $ticket->getId());
                                }
                                //Hay un email diferente por cada pais en funcion del idioma que tenga asignado el taller.
                                $mailer->setTo($this->get('translator')->trans($mail_centralita));
                                
                                $date = date("Y-m-d H:i:s");
                                $mailer->setBody('ticket: ' . $ticket->getId() . ' - ' . $date);
                                $mailer->sendMailToSpool();
                            }

                            // Dejamos el locale tal y como estaba
                            $request->setLocale($locale);

                            $this->get('session')->setFlash('ticket_created', $this->get('translator')->trans('ticket_created'));
                        }

                        if (isset($_POST['save_close'])) {
                            return $this->redirect($this->generateUrl('closeTicket', array('id' => $ticket->getId())));
                        } else {
                            return $this->redirect($this->generateUrl('showTicket', array('id' => $ticket->getId())));
                        }
                    } else {
                        $this->get('session')->setFlash('error', $this->get('translator')->trans('error.bad_introduction'));
                    }
                } else {
                    $this->get('session')->setFlash('error_ticket', $this->get('translator')->trans('error.bad_introduction.ticket'));
                }
            } 
//            else {
//                if ($security->isGranted('ROLE_ASSESSOR')) {
//                    $max_len = 10000;
//                } else {
//                    $max_len = 500;
//                }
//                $this->get('session')->setFlash('error', $this->get('translator')->trans('error.txt_length_%numchars%', array('%numchars%' => $max_len)));
//            }
        
        // else {
        //     $flash = $this->get('translator')->trans('error.bad_introduction');
        //     $this->get('session')->setFlash('error', $flash);
        // }

        if ($id_subsystem != '' and $id_subsystem != '0') {
            $subsystem = $em->getRepository('TicketBundle:Subsystem')->find($id_subsystem);
            $id_system = $subsystem->getSystem()->getId();
            $id_subsystem = $subsystem->getId();
        } else {
            $id_system = '';
            $id_subsystem = '';
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
            'form_name' => $form->getName()
        );
        // if(isset($subsystem)) { $array[] = 'subsystem' => $subsystem; }

        if ($security->isGranted('ROLE_ASSESSOR'))
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
    public function editTicketAction($id, $ticket) {

        $security = $this->get('security.context');
        $id_catserv = 0;
        if($security->getToken()->getUser()->getCategoryService() != null){
            $id_catserv = $security->getToken()->getUser()->getCategoryService()->getId();
        }
        $_SESSION['einatech'] = 0;
        if($id_catserv != 0 && $id_catserv != 2 && $id_catserv != 4){
            $_SESSION['einatech'] = 2;
        }
        if($ticket->getImportance() != null && $ticket->getImportance()->getId() == 5){
            $_SESSION['einatech'] = 1;
        }
        if ($security->isGranted('ROLE_SUPER_ADMIN')
                or ( !$security->isGranted('ROLE_SUPER_ADMIN') and $ticket->getWorkshop()->getCountry()->getId() == $security->getToken()->getUser()->getCountry()->getId())
                or ( $security->isGranted('ROLE_ASSESSOR') and ! $security->isGranted('ROLE_ADMIN'))
        ) {

            $em = $this->getDoctrine()->getEntityManager();
            $request = $this->getRequest();

            $form = $this->createForm(new EditTicketType(), $ticket);

            if ($request->getMethod() == 'POST') {

                $user = $em->getRepository('UserBundle:User')->find($security->getToken()->getUser()->getId());

                $form->bindRequest($request);

                $car = $ticket->getCar();

                if ($ticket->getDescription() != null and $car->getBrand() != null and $car->getBrand()->getId() != null
                        and $car->getModel() != null and $car->getModel()->getId() != null and $car->getVin() != null and $car->getPlateNumber() != null) {
                    /* Validacion Ticket */
                    $str_len = strlen($ticket->getDescription());
                    if ($security->isGranted('ROLE_ASSESSOR')) {
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
                            $this->get('session')->setFlash('error', $this->get('translator')->trans('error.bad_introduction'));
                        }
                    } else {
                        $this->get('session')->setFlash('error', $this->get('translator')->trans('error.txt_length_%numchars%', array('%numchars%' => $max_len)));
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
                $this->get('session')->setFlash('error', $flash);
            }
            if ($security->isGranted('ROLE_ASSESSOR'))
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
    public function deleteTicketAction($id, $ticket) {
        $security = $this->get('security.context');
        $request = $this->getRequest();
        if ($security->isGranted('ROLE_SUPER_ADMIN')
                or ( !$security->isGranted('ROLE_SUPER_ADMIN') and $ticket->getWorkshop()->getCountry()->getId() == $security->getToken()->getUser()->getCountry()->getId())
                or ( $security->isGranted('ROLE_ASSESSOR') and ! $security->isGranted('ROLE_ADMIN'))
        ) {
            if ($security->isGranted('ROLE_USER') === false) {
                throw new AccessDeniedException();
            }
            $em = $this->getDoctrine()->getEntityManager();

            //se borrara solo si hay un post sin respuesta, si hay mas de uno se deniega
            $posts = $ticket->getPosts(); //echo count($posts);
            // if (count($posts)>1) throw $this->createNotFoundException('Este Ticket no puede borrarse, ya esta respondido');
            //puede borrarlo el assessor o el usuario si el ticket no esta assignado aun
            if ((!$security->isGranted('ROLE_ASSESSOR') and ( $ticket->getAssignedTo() != null))) {
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
    public function showTicketAction($ticket) {
        $security = $this->get('security.context');
        $user = $this->get('security.context')->getToken()->getUser();
        
        if($user == 'anon.') return $this->redirect($this->generateUrl('user_login'));

        $user = $security->getToken()->getUser();
        
         $id_catserv = 0;
        if($security->getToken()->getUser()->getCategoryService() != null){
            $id_catserv = $security->getToken()->getUser()->getCategoryService()->getId();
        }
        $_SESSION['einatech'] = 0;
        if($id_catserv != 0 && $id_catserv != 2 && $id_catserv != 4){
            $_SESSION['einatech'] = 2;
        }
        if($ticket->getImportance() != null && $ticket->getImportance()->getId() == 5){
            $_SESSION['einatech'] = 1;
        }
        
        if (
            ($security->isGranted('ROLE_ADMIN')
                or ( !$security->isGranted('ROLE_SUPER_ADMIN') and $security->isGranted('ROLE_ASSESSOR') and $ticket->getWorkshop()->getCountry()->getId() == $security->getToken()->getUser()->getCountry()->getId())
                or ( !$security->isGranted('ROLE_ASSESSOR') and $ticket->getWorkshop() == $user->getWorkshop())
                or ( $security->isGranted('ROLE_ASSESSOR') and ! $security->isGranted('ROLE_ADMIN'))
            ) and ($user->getCategoryService() == null OR $user->getCategoryService() == $ticket->getCategoryService())
        ) {
            $em = $this->getDoctrine()->getEntityManager();
            $request = $this->getRequest();
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
            $formP = $this->createForm(new PostType(), $post);
            $formD = $this->createForm(new DocumentType(), $document);

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

            if ($security->isGranted('ROLE_ASSESSOR')) {
                $form = $this->createForm(new EditTicketType(), $ticket);
                $array['form'] = $form->createView();
            }
            if ($request->getMethod() == 'POST') {

                //Define Ticket
                if ($security->isGranted('ROLE_ASSESSOR')) {

                    $form->bindRequest($request);
                }

                if (!$security->isGranted('ROLE_ASSESSOR') or ( $security->isGranted('ROLE_ASSESSOR') /* and $form->isValid() */)) {

                    $formP->bindRequest($request);
                    $formD->bindRequest($request);

                    if ($formP->isValid() and $formD->isValid()) {

                        if ($security->isGranted('ROLE_ASSESSOR')) {

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

                            if ($security->isGranted('ROLE_ASSESSOR') or $size <= 10240000) {
                                $str_len = strlen($post->getMessage());
                                if ($security->isGranted('ROLE_ASSESSOR')) {
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
                                        if ($security->isGranted('ROLE_ASSESSOR')) {
                                            $ticket->setAssignedTo($user);
                                            $ticket->setPending(0);
                                        }else{
                                            $ticket->setPending(1);
                                        }

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

                                            // if (!$security->isGranted('ROLE_ASSESSOR') and $ticket->getAssignedTo() != null) {
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
                                    $this->get('session')->setFlash('error', $this->get('translator')->trans('error.txt_length_%numchars%', array('%numchars%' => $max_len)));
                                }
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

                return $this->redirect($this->generateUrl('showTicket', array('id' => $ticket->getId(),
                                    'form_name' => $formP->getName(),
                                    'action' => 'showTicket',
                                    'ticket' => $ticket,
                                    'systems' => $systems,
                                    'brand' => $brand,
                                    'model' => $model,
                                    'version' => $version)));
            }

            if ($security->isGranted('ROLE_ASSESSOR')) {
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
    public function editPostAction($post) {
        if (!$this->get('security.context')->isGranted('ROLE_USER')) {
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
                return $this->redirect($this->generateUrl('showTicket', array('id' => $ticket->getId())));
                //return $this->showTicketAction($ticket);
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
   public function closeTicketAction($id, $ticket, $close='1')
   {
       $message = '';
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
               if($request->request->get('sol_other_txt') != ''){
                   $message = $request->request->get('sol_other_txt');
               }
               else{
                   $message= $request->getSession()->get('message');
               }

               /*Validacion Ticket*/
               $str_len = strlen($message);
               if($security->isGranted('ROLE_ASSESSOR')) { $max_len = 10000; }
               else { $max_len = 500; }

               if ($str_len <= $max_len ) {

                   if ($form->isValid()) {

                       if ($security->isGranted('ROLE_ASSESSOR') === false) {
                           if     ($ticket->getSolution() == "0") $ticket->setSolution($this->get('translator')->trans('ticket.close_as_instructions'));
                           elseif ($ticket->getSolution() == "1") $ticket->setSolution($this->get('translator')->trans('ticket.close_irreparable_car'));
                           elseif ($ticket->getSolution() == "2") $ticket->setSolution($this->get('translator')->trans('ticket.close_other').': '.$message);
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
                           $this->get('session')->setFlash('error', $this->get('translator')->trans('error.msg_solution'));
                       }
                   }else{
                       $this->get('session')->setFlash('error', $this->get('translator')->trans('error.bad_introduction'));
                   }
               }else{
                   $request->getSession()->set('message', $message);
                $this->get('session')->setFlash('error', $this->get('translator')->trans('error.txt_length_%numchars%', array('%numchars%' => $max_len)));

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
    public function editDescriptionAction($id, $ticket) {
        $security = $this->get('security.context');
        if ($security->isGranted('ROLE_ASSESSOR')) {
            $em = $this->getDoctrine()->getEntityManager();
            $request = $this->getRequest();

            $form = $this->createForm(new EditDescriptionType(), $ticket);

            if ($request->getMethod() == 'POST') {
                $form->bindRequest($request);

                /* Validacion Ticket */
                $str_len = strlen($ticket->getSolution());
                $max_len = 10000;

                if ($str_len <= $max_len) {

                    if ($form->isValid()) {

                        if ($ticket->getDescription() != "") {

                            $user = $security->getToken()->getUser();

                            UtilController::saveEntity($em, $ticket, $user);

                            return $this->redirect($this->generateUrl('showTicket', array('id' => $id)));
                        } else {
                            $this->get('session')->setFlash('error', $this->get('translator')->trans('error.msg_solution'));
                        }
                    } else {
                        $this->get('session')->setFlash('error', $this->get('translator')->trans('error.bad_introduction'));
                    }
                } else {
                    $this->get('session')->setFlash('error', $this->get('translator')->trans('error.txt_length_%numchars%', array('%numchars%' => $max_len)));
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
    public function listMotorsAction() {
        $em = $this->getDoctrine()->getEntityManager();
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
    public function reopenTicketAction($id, $ticket) {
        $em = $this->getDoctrine()->getEntityManager();
        $security = $this->get('security.context');
        $request = $this->getRequest();

        $user = $security->getToken()->getUser();
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
    public function workshopListAction($page = 1, $option = null) {
        $em = $this->getDoctrine()->getEntityManager();

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
    public function getTicketsFromWorkshopAction($id_workshop, $page = 1) {
        $em = $this->getDoctrine()->getEntityManager();

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
    public function assignUserToTicketAction($ticket, $id_user = null) {
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
    public function assignTicketSelectUserAction($ticket) {
        $em = $this->getDoctrine()->getEntityManager();
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
    public function blockTicketAction($ticket, $id_user = null) {

        $em = $this->getDoctrine()->getEntityManager();
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

        return $this->showTicketAction($ticket);
    }

    /**
     * Funcion que devuelve un listado de tickets filtrados a partir de una opcion de un combo ($option)
     * @return array
     */
    public function getTicketsByOption($option) {
        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();
        $security = $this->get('security.context');

        $tickets = array();
        $check_id = $petition->request->get('filter_id');
        $user = $security->getToken()->getUser();
        $repoTicket = $em->getRepository('TicketBundle:Ticket');
        $open = $em->getRepository('TicketBundle:Status')->findOneBy(array('name' => 'open'));
        $closed = $em->getRepository('TicketBundle:Status')->findOneBy(array('name' => 'closed'));

        if ($security->isGranted('ROLE_ADMIN')) {
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
        } elseif ($security->isGranted('ROLE_ASSESSOR')) {
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

                $check_status = $petition->request->get('status');

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
    public function findTicketByIdAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $security = $this->get('security.context');
        $request = $this->getRequest();
        $id = $request->get('flt_id');
        $catserv = $security->getToken()->getUser()->getCategoryService();
        $user = $security->getToken()->getUser();
        if($catserv != null)
            $ticket = $em->getRepository('TicketBundle:Ticket')->findOneBy(array('id' => $id, 'category_service' => $catserv->getId()));
        else
            $ticket = $em->getRepository('TicketBundle:Ticket')->find($id);

        if ($ticket)
            $tickets = array($ticket);
        else
            $tickets = array();
        $b_query   = $em->createQuery('SELECT b FROM CarBundle:Brand b, CarBundle:Model m WHERE b.id = m.brand ORDER BY b.name');
//        if($security->isGranted('ROLE_SUPER_ADMIN') || $security->isGranted('ROLE_ADMIN')){
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
        if($security->getToken()->getUser()->getCategoryService() != NULL) {
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

        if ($security->isGranted('ROLE_ASSESSOR') and ! $security->isGranted('ROLE_ADMIN'))
            return $this->render('TicketBundle:Layout:list_ticket_assessor_layout.html.twig', $array);
        else
            return $this->render('TicketBundle:Layout:list_ticket_layout.html.twig', $array);
    }

    /**
     * Devuelve un ticket segun la id enviada por parametro y el taller
     * @return url
     */
    public function findTicketByIdAndWorkshopAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $security = $this->get('security.context');
        $request = $this->getRequest();
        $id = $request->get('flt_id');
        $workshop = $security->getToken()->getUser()->getWorkshop();
        $ticket = $em->getRepository('TicketBundle:Ticket')->find($id);
        if ($workshop != null and $ticket) {
            return $this->redirect($this->generateUrl('showTicket', array('id' => $ticket->getId())));
        } else {
            if ($ticket and ( $ticket->getWorkshop()->getCountry()->getId() == $security->getToken()->getUser()->getCountry()->getId()))
                return $this->redirect($this->generateUrl('showTicket', array('id' => $ticket->getId())));
        }
        if ($security->isGranted('ROLE_ASSESSOR') and ! $security->isGranted('ROLE_ADMIN'))
            return $this->redirect($this->generateUrl('listTicket', array('option' => "assessor_pending")));
        else {
            return $this->redirect($this->generateUrl('listTicket'));
        }
    }

    /**
     * Devuelve un ticket segun la id enviada por parametro
     * @return url
     */
    public function findTicketByBMVAction($page = 1, $brand = 0, $model = 0, $version = 0, $system = 0, $subsystem = 0, $importance = 0, $year = 0, $motor = 0, $kw = 0, $num_rows = 10) {
        $em = $this->getDoctrine()->getEntityManager();
        $security = $this->get('security.context');
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
        if($user->getCategoryService() != NULL) {
            $advisers = $em->getRepository("UserBundle:User")->findByRole($em, 0, $user->getCategoryService()->getId(), 'ROLE_ASSESSOR');
        }else{
            $advisers = $em->getRepository("UserBundle:User")->findByRole($em, 0, 0, 'ROLE_ASSESSOR');
        }
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
                if ($tck and ( $tck->getWorkshop()->getCountry()->getId() == $security->getToken()->getUser()->getCountry()->getId() or $security->isGranted('ROLE_SUPER_ADMIN'))) {
                    $tickets[] = $tck;
                }
            }
        } else {
            if ($ticket and ( $ticket->getWorkshop()->getCountry()->getId() == $security->getToken()->getUser()->getCountry()->getId() or $security->isGranted('ROLE_SUPER_ADMIN'))) {
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
        if ($security->isGranted('ROLE_ASSESSOR') and ! $security->isGranted('ROLE_ADMIN'))
            return $this->render('TicketBundle:Layout:list_ticket_assessor_layout.html.twig', $array);
        else
            return $this->render('TicketBundle:Layout:list_ticket_layout.html.twig', $array);
    }

    /**
     * Devuelve un ticket segun la id enviada por parametro
     * @return url
     */
    public function findAssessorTicketByBMVAction($page = null) {
        /*         * *********************************************************************************************************************
         * TODO:
         * En una nueva version (2.8) habría que separar esta funcion en otras 2,
         *  en función de si se ha escogido un taller en la busqueda o no
         * (La comprobacion "$workshop->getId();" muestra menos registros que los de $pagination, y hace fallar la paginación)
         * -- QF:
         *     - De momento se mostrará un listado fijo ($max_rows = 100;) y se ocultará la paginación,
         *       a fin de evitar registros que no se mostraban al estar en una segunda página de $pagination.
         *
         * ********************************************************************************************************************* */
        $em = $this->getDoctrine()->getEntityManager();
        $security = $this->get('security.context');
        $request = $this->getRequest();

        $user = $security->getToken()->getUser();
        if($user == 'anon.') return $this->redirect($this->generateUrl('user_login'));

        $params = array();

        // PAGE
        if (!isset($page))
            $page = $request->request->get('ftbmv_page');
        if (!isset($page))
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

        if ($model != '0' and $version == '0')
            $version = '';

        if ($plateNumber == null)
            $plateNumber = $request->request->get('new_car_form_plate_number');

        $workshop = new Workshop();

        if(!$security->isGranted('ROLE_ASSESSOR'))
            $workshop = $user->getWorkshop();

        elseif (isset($codepartner) and isset($codeworkshop) and $codepartner != '' and $codeworkshop != '')
            $workshop = $em->getRepository('WorkshopBundle:Workshop')->findOneBy(array('code_workshop' => $codeworkshop, 'code_partner' => $codepartner));

        if($workshop->getId() == null) {

            $num_rows = $request->request->get('slct_numRows');
            if (!isset($num_rows))
                $num_rows = 10;
            if (isset($brand) and $brand != null and $brand != '')
                $params[] = array('brand', ' = ' . $brand);
            if (isset($model) and $model != null and $model != '')
                $params[] = array('model', ' = ' . $model);
            if (isset($version) and $version != null and $version != '')
                $params[] = array('version', ' = ' . $version);

            if (isset($plateNumber) and $plateNumber != '0' and $plateNumber != '')
                $params[] = array('plateNumber' . ' LIKE ', '\'' . $plateNumber . '\'');
            $pagination = new Pagination($page);

            // Seteamos el numero de resultados que se mostraran
            $max_rows = 100;
            $pagination->setMaxRows($max_rows);
            $ordered = array('e.modified_at', 'DESC');

            $cars = $pagination->getRows($em, 'CarBundle', 'Car', $params, $pagination, $ordered);
            $length = $pagination->getRowsLength($em, 'CarBundle', 'Car', $params, $ordered);

            $pagination->setTotalPagByLength($length);

            $tickets = array();
            $key = array_keys($cars);
            $size = sizeOf($key);

            if ($length > $max_rows)
                $more_results = $length - $max_rows;
            else
                $more_results = 0;

            if(!$security->isGranted('ROLE_ADMIN') AND $user->getCategoryService() != NULL) {
                $catserv = $user->getCategoryService()->getId();
            }
            else{
                $catserv = null;
            }

            if ($size > 0) {

                for ($i = 0; $i < $size; $i++) {

                    $id = $cars[$key[$i]]->getId();
                    if ($subsystem == 0 or $subsystem == ''){
                        if(isset($catserv) && $catserv != null) $ticket = $em->getRepository('TicketBundle:Ticket')->findBy(array('car' => $id, 'category_service' => $catserv));
                        else                $ticket = $em->getRepository('TicketBundle:Ticket')->findBy(array('car' => $id));
                    }
                    else{
                        if(isset($catserv) && $catserv != null) $ticket = $em->getRepository('TicketBundle:Ticket')->findBy(array('car' => $id, 'subsystem' => $subsystem, 'category_service' => $catserv));
                        else                $ticket = $em->getRepository('TicketBundle:Ticket')->findBy(array('car' => $id, 'subsystem' => $subsystem));
                    }

                    if (sizeof($ticket) >= 1) {
                        foreach ($ticket as $tck) {
                            if ($tck and ( $tck->getWorkshop()->getCountry()->getId() == $user->getCountry()->getId() or $security->isGranted('ROLE_ASSESSOR'))) {
                                if($workshop != null){
                                    $w_id = $workshop->getId();
                                }
                                // Si esta definido el taller añadimos al array solo las que coinciden con el taller
                                if (isset($w_id)) {

                                    if ($workshop->getId() == $tck->getWorkshop()->getId()) {

                                        $tickets[] = $tck;
                                    }
                                }
                                // Sino añadimos todos los tickets independientemente del taller que sean
                                else {
                                    $tickets[] = $tck;
                                }
                            }
                        }
                    }
                }
            }
        }else{

            if(!$security->isGranted('ROLE_ADMIN') AND $user->getCategoryService() != NULL) {
                $catserv = $user->getCategoryService()->getId();
            }
            else $catserv = null;

            if($workshop->getId() != null) $ticket = $em->getRepository('TicketBundle:Ticket')->findByWorkshop($workshop);
            else                           $ticket = $em->getRepository('TicketBundle:Ticket')->findByCategoryService($catserv);
            $pagination = new Pagination();
            $num_rows = $request->request->get('slct_numRows');
            if (!isset($num_rows))
                $num_rows = 10;
            $more_results = 0;

            $arr_open = array();
            $arr_closed = array();
            $arr_inactive = array();
            $arr_expirated = array();

            foreach ($ticket as $tck)
            {
                $car = $tck->getCar();
                $add_ticket = null;

                // Si se esta buscando algun campo y no lo encuentra, deja de buscar coincidencias entre ese ticket y el coche buscado
                $search = true;

                if($subsystem != null and $subsystem != '' and $subsystem != '0'){
                    if($tck->getSubsystem() == null or $subsystem != $tck->getSubsystem()->getId()) $search = false;
                }
                if($plateNumber != null and $plateNumber != '' and $plateNumber != '0'){
                    if($car->getPlateNumber() == null or $plateNumber != $car->getPlateNumber())    $search = false;
                }

                if($search){
                    if($version != null and $version != '' and $version != '0'){
                        if($car->getVersion() != null and $version == $car->getVersion()->getId()) {
                            $add_ticket = $tck;
                        }
                    }
                    elseif($model != null and $model != '' and $model != '0'){
                        if($car->getModel() != null and $model == $car->getModel()->getId()) {
                            $add_ticket = $tck;
                        }
                    }
                    elseif($brand != null and $brand != '' and $brand != '0'){
                        if($car->getBrand() != null and $brand == $car->getBrand()->getId()) {
                            $add_ticket = $tck;
                        }
                    }else{
                        if($subsystem != null and $subsystem != '' and $subsystem != '0' and $plateNumber != null and $plateNumber != '' and $plateNumber != '0')
                        {
                            if($car->getSubsystem() != null and $subsystem == $car->getSubsystem()->getId()
                              and $car->getPlateNumber() != null and $plateNumber == $car->getPlateNumber()->getId()) {
                                $add_ticket = $tck;
                            }
                        }elseif($subsystem != null and $subsystem != '' and $subsystem != '0')
                        {
                            if($car->getSubsystem() != null and $subsystem == $car->getSubsystem()->getId())
                                $add_ticket = $tck;

                        }elseif($plateNumber != null and $plateNumber != '' and $plateNumber != '0')
                        {
                            if ($car->getPlateNumber() != null and $plateNumber == $car->getPlateNumber())
                                $add_ticket = $tck;

                        }
                    }
                }

                if($add_ticket != null)
                {
                    $id_stat = $tck->getStatus()->getId();

                    switch ($id_stat) {
                        case '1': // OPEN
                            $arr_open[] = $add_ticket;
                            break;
                        case '2': // CLOSED
                            $arr_closed[] = $add_ticket;
                            break;
                        case '3': // INACTIVE
                            $arr_inactive[] = $add_ticket;
                            break;
                        case '4': // EXPIRATED
                            $arr_expirated[] = $add_ticket;
                            break;
                        default:
                            break;
                    }
                }
            }

            $tickets = array();
            foreach ($arr_inactive  as $t) { $tickets[] = $t; }
            foreach ($arr_expirated as $t) { $tickets[] = $t; }
            foreach ($arr_open      as $t) { $tickets[] = $t; }
            foreach ($arr_closed    as $t) { $tickets[] = $t; }
        }

        $b_query = $em->createQuery('SELECT b FROM CarBundle:Brand b, CarBundle:Model m WHERE b.id = m.brand ORDER BY b.name');
        $brands = $b_query->getResult();
        $countries = $em->getRepository('UtilBundle:Country')->findAll();
        $systems = $em->getRepository('TicketBundle:System')->findBy(array(), array('name' => 'ASC'));
        $importances = $em->getRepository('TicketBundle:Importance')->findAll();
        $advisers = array();
        
        if($security->isGranted('ROLE_ASSESSOR')) {
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

        if (isset($version) and $version != '' and $version != null)
            $version = $em->getRepository('CarBundle:Version')->findOneById($version);

        if (isset($subsystem) and $subsystem != '0' and $subsystem != '')
            $subsystem = $em->getRepository('TicketBundle:Subsystem')->find($subsystem);

        if (sizeof($tickets) == 0)
            $pagination = new Pagination(0);


        if ($plateNumber != '') {


            if (!isset($cars)) {
                $cars[0] = $em->getRepository('CarBundle:Car')->findOneByPlateNumber($plateNumber);
            }

            if (isset($cars) and $cars != null and $cars[0] != null) {
                $brand = $cars[0]->getBrand()->getId();
                $model = $cars[0]->getModel();
                $vin = $cars[0]->getVin();
                $year = $cars[0]->getYear();
                $motor = $cars[0]->getMotor();
                $kw = $cars[0]->getKw();
                $displacement = $cars[0]->getDisplacement();
                if($cars[0]->getVersion() != null){
                    if($cars[0]->getVersion()->getId() > 0){
                        $version = $em->getRepository('CarBundle:Version')->findOneById($cars[0]->getVersion());
                    }
                    else
                        $version = null;
                }
            }
        }
        if($brand == '') $brand = null;

        //Al llamar a esta funcion se pierde la version del coche, aqui la volvemos a asignar
        if(sizeOf($tickets) > 0)
        {
            if($tickets[0]->getCar()->getVersion() != null) {
            $ver = $em->getRepository('CarBundle:Version')->findOneById($tickets[0]->getCar()->getVersion()->getId());

                if($ver != null){
                    foreach ($tickets as $ticket) {

                        $ticket->getCar()->setVersion($ver);
                    }
                }
            }
        }

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
            'catserv' => $catserv,
            'catservices' => $catservices,
            'languages' => $languages,
            'lang' => $lang,
            'importances' => $importances,
            'option' => 'all',
            'page' => $page,
            'num_rows' => $num_rows,
            'more_results' => $more_results,
            'country' => 0,
            'inactive' => 0,
            'disablePag' => 0,
            'advisers' => $advisers);
        if ($security->isGranted('ROLE_ASSESSOR') and ! $security->isGranted('ROLE_ADMIN'))
            return $this->render('TicketBundle:Layout:list_ticket_assessor_layout.html.twig', $array);
        else
            return $this->render('TicketBundle:Layout:list_ticket_layout.html.twig', $array);
    }

    /**
     * Devuelve todos los usuarios que podran ser asignados a un ticket (admins i asesores has nuevo aviso)
     * @param type $id_ticket
     */
    private function getUsersToAssingFromTicket($ticket=null) {
        $em = $this->getDoctrine()->getEntityManager();

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
