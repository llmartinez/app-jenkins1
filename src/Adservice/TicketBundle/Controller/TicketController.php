<?php
namespace Adservice\TicketBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Adservice\TicketBundle\Controller\DefaultController as DefaultC;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Adservice\UserBundle\Entity\User;

use Adservice\TicketBundle\Entity\Ticket;
use Adservice\TicketBundle\Entity\TicketRepository;
use Adservice\TicketBundle\Form\NewTicketType;
use Adservice\TicketBundle\Form\EditTicketType;
use Adservice\TicketBundle\Form\CloseTicketType;

use Adservice\TicketBundle\Entity\Status;
use Adservice\CarBundle\Entity\Car;
use Adservice\CarBundle\Form\CarType;

use Adservice\TicketBundle\Entity\Post;
use Adservice\TicketBundle\Form\PostType;

use Adservice\UtilBundle\Entity\Document;
use Adservice\UtilBundle\Entity\DocumentRepository;
use Adservice\UtilBundle\Form\DocumentType;

use Adservice\WorkshopBundle\Entity\Workshop;
use Adservice\WorkshopBundle\Entity\WorkshopRepository;
use Adservice\WorkshopBundle\Form\WorkshopType;

class TicketController extends Controller {

    /**
     * Crea un ticket abierto con sus respectivos post y car
     * @return url
     */
    public function newTicketAction($id_workshop=null) {

        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        $ticket = new Ticket();
        $car = new Car();

        if ($id_workshop != null)
            { $workshop = $em->getRepository('WorkshopBundle:Workshop')->find($id_workshop); }
        else{ $workshop =  new Workshop(); }

        $systems = $em->getRepository('TicketBundle:System')->findAll();

        //Define Forms
        $form  = $this->createForm(new NewTicketType(), $ticket);
        $formC = $this->createForm(new CarType(), $car);

        if ($request->getMethod() == 'POST') {

            //campos comunes
            $user     = $em->getRepository('UserBundle:User')->find($this->get('security.context')->getToken()->getUser()->getId());
            $status   = $em->getRepository('TicketBundle:Status')->findOneByName('open');
            $security = $this->get('security.context');

            $form ->bindRequest($request);
            $formC->bindRequest($request);

            /*Validacion Car*/
            // if (($car->getModel()[0] != "") && ($car->getBrand()[0] != "")) {

                /*Validacion Ticket*/
                if ($ticket->getSubsystem() != "" or $security->isGranted('ROLE_ASSESSOR') == 0) {

                    /*Validacion Formularios*/
                    if (($form->isValid()) && ($formC->isValid())) {

                        //Define CAR
                        $car = DefaultC::newEntity($car, $user);

                        $id_brand   = $request->request->get('new_car_form_brand'  );
                        $id_model   = $request->request->get('new_car_form_model'  );
                        $id_version = $request->request->get('new_car_form_version');

                        $brand   = $em->getRepository('CarBundle:Brand'  )->find($id_brand  );
                        $model   = $em->getRepository('CarBundle:Model'  )->find($id_model  );
                        $version = $em->getRepository('CarBundle:Version')->find($id_version);

                        $car->setBrand($brand);
                        $car->setModel($model);
                        $car->setVersion($version);
                        $car = DefaultC::newEntity($car, $user);
                        DefaultC::saveEntity($em, $car, $user, false);

                        //Define TICKET
                        $ticket = DefaultC::newEntity($ticket, $user);
                        if ($security->isGranted('ROLE_ASSESSOR'))
                        {
                            $ticket->setWorkshop($workshop);
                            $ticket->setAssignedTo($user);
                        }else{
                            $ticket->setWorkshop($user->getWorkshop());
                        }
                        $ticket->setStatus($status);
                        $ticket->setCar($car);
                        DefaultC::saveEntity($em, $ticket, $user);

                        if (isset($_POST['save&close'])){
                            return $this->redirect($this->generateUrl('closeTicket', array( 'id_ticket' => $ticket->getId() )));
                        }else{
                            return $this->redirect($this->generateUrl('showTicket', array('id_ticket' => $ticket->getId())));
                        }

                    } else { $this->get('session')->setFlash('error', '¡Error! No has introducido los valores correctamente'); }

                } else { $this->get('session')->setFlash('error_ticket', '¡Error! No has introducido los campos de ticket correctamente'); }

            // } else { $this->get('session')->setFlash('error_car', '¡Error! No has introducido el vehiculo correctamente'); }
        }

        $brands  = $em->getRepository('CarBundle:Brand'    )->findAll();
        return $this->render('TicketBundle:Ticket:new_ticket_layout.html.twig', array('ticket' => $ticket,
                    'form' => $form->createView(),
                    'formC' => $formC->createView(),
                    'brands' => $brands,
                    'systems' => $systems,
                    'workshop' => $workshop,
                    'form_name' => $form->getName(),));
    }

    /**
     * Edita el ticket y el car asignado a partir de su id
     * @param integer $id_ticket
     * @return url
     */
    public function editTicketAction($id_ticket) {
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();

        $ticket = $em->getRepository('TicketBundle:Ticket')->find($id_ticket);

        $form = $this->createForm(new EditTicketType(), $ticket);

        if ($request->getMethod() == 'POST') {

            $user = $em->getRepository('UserBundle:User')->find($this->get('security.context')->getToken()->getUser()->getId());

            $form->bindRequest($request);

            //Define CAR
            if ($form->isValid()) {

                    DefaultC::saveEntity($em, $ticket, $user);

                    return $this->redirect($this->generateUrl('showTicket', array('id_ticket' => $ticket->getId())));

            }else{ $this->get('session')->setFlash('error', '¡Error! No has introducido los valores correctamente'); }
        }

        $systems     = $em->getRepository('TicketBundle:System'    )->findAll();

        return $this->render('TicketBundle:Ticket:show_ticket_layout.html.twig', array(
                                                                                        'form'        => $form->createView(),
                                                                                        'form_name'   => $form->getName(),
                                                                                        'ticket'      => $ticket,
                                                                                        'systems'     => $systems,
                                                                                    ));
    }

    /**
     * Elimina el ticket de la bbdd si no tiene respuesta (posts == 1)
     * @param Int $id
     * @throws AccessDeniedException
     * @throws CreateNotFoundException
     */
    public function deleteTicketAction($id_ticket){

        if ($this->get('security.context')->isGranted('ROLE_USER') === false){
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getEntityManager();
        $ticket = $em->getRepository("TicketBundle:Ticket")->find($id_ticket);

        if (!$ticket) throw $this->createNotFoundException('Ticket no encontrado en la BBDD.. '.$id_ticket);

        //se borrara solo si hay un post sin respuesta, si hay mas de uno se deniega
        $posts = $ticket->getPosts(); //echo count($posts);
        // if (count($posts)>1) throw $this->createNotFoundException('Este Ticket no puede borrarse, ya esta respondido');

        //puede borrarlo el assessor o el usuario si el ticket no esta assignado aun
        if ((!$this->get('security.context')->isGranted('ROLE_ASSESSOR') and ($ticket->getAssignedTo() != null))){
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
        $em->flush();

        return $this->redirect($this->generateUrl('listTicket'));
    }

    /**
     * Muestra los posts que pertenecen a un ticket
     * @param integer $id_ticket
     * @return url
     */
    public function showTicketAction($id_ticket) {
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        $security = $this->get('security.context');

        $post = new Post();
        $document = new Document();
        $systems  = $em->getRepository('TicketBundle:System')->findAll();

        //Define ticket al que pertenecen los posts
        $ticket = $em->getRepository('TicketBundle:Ticket')->find($id_ticket);

        //Define Forms
        if ($security->isGranted('ROLE_ASSESSOR')) { $form = $this->createForm(new EditTicketType(), $ticket); }
        $formP = $this->createForm(new PostType(), $post);
        $formD = $this->createForm(new DocumentType(), $document);

        if ($request->getMethod() == 'POST') {

            //Define User
            $user = $security->getToken()->getUser();
            //Define Ticket
            if ($security->isGranted('ROLE_ASSESSOR')) { $form->bindRequest($request); }

            if(($security->isGranted('ROLE_ASSESSOR') and ($form->isValid())) or (!$security->isGranted('ROLE_ASSESSOR'))){

                $formP->bindRequest($request);
                $formD->bindRequest($request);

                if (($formP->isValid()) and ($formD->isValid())) {

                    //Define Post
                    $post = DefaultC::newEntity($post, $user);
                    $post->setTicket($ticket);
                    DefaultC::saveEntity($em, $post, $user, false);

                    //Define Document
                    $document->setPost($post);

                    if ($document->getFile() != "") {
                        $em->persist($document);
                    }

                    //Se desbloquea el ticket una vez respondido
                    if ($ticket->getBlockedBy() != null) {
                        $ticket->setBlockedBy(null);

                        /*si es el primer assessor que responde se le asigna*/
                        $posts = $ticket->getPosts();
                        $primer_assessor = 0;
                        foreach ($posts as $post) {
                            if ($post->getCreatedBy()->getRoles()[0]->getName() == 'ROLE_ASSESSOR') {
                                $primer_assessor = 1;
                            }
                        }
                        if($primer_assessor == 0) $ticket->setAssignedTo($user);
                    }

                    DefaultC::saveEntity($em, $ticket, $user);
                }
            }
            return $this->redirect($this->generateUrl('showTicket', array(  'id_ticket' => $ticket->getId(),
                                                                            'form_name' => $form->getName(),
                                                                            'ticket'    => $ticket,
                                                                            'systems'   => $systems, )));
        }

        $array = array( 'formP'     => $formP->createView(),
                        'form_name' => $form->getName(),
                        'formD'     => $formD->createView(),
                        'ticket'    => $ticket,
                        'systems'   => $systems, );

        if ($security->isGranted('ROLE_ASSESSOR')) {  $array['form'] = ($form ->createView()); }

        return $this->render('TicketBundle:Ticket:show_ticket_layout.html.twig', $array);
    }

    /**
     * Devuelve todos los tickets realizados
     * @return url
     */
    public function listTicketAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $request  = $this->getRequest();
        $workshops = array('0' => new Workshop());
        $tickets = array();

        if ($request->getMethod() == 'POST') {

            $workshops = $em->getRepository('WorkshopBundle:Workshop')->findWorkshopInfo($request);

            if($workshops[0]->getId() != "") {
                $tickets = $em->getRepository('TicketBundle:Ticket')->findByWorkshop($workshops[0]->getId());
            }
        }

        return $this->render('TicketBundle:Ticket:list_ticket_layout.html.twig', array('workshop'   => $workshops[0],
                                                                                       'tickets'   => $tickets,
                                                                              ));
    }

    /**
     * Devuelve todos los tickets realizados
     * @return url
     */
    public function listTicketFilteredAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $security = $this->get('security.context');
        $request  = $this->getRequest();
        $tickets  = array();
        $status   = new Status();

        $id_workshop = $request->request->get('id_workshop');
        $id_ticket   = $request->request->get('flt_id'     );
        $st_open     = $request->request->get('flt_open'   );
        $st_closed   = $request->request->get('flt_closed' );

        if($st_open != "") { $status = $em->getRepository('TicketBundle:Status')->findOneByName('open'  ); }
        else {
            if($st_closed != "") { $status = $em->getRepository('TicketBundle:Status')->findOneByName('closed'); }
        }
        $tickets  = $em->getRepository('TicketBundle:Ticket'    )->findTicketsFiltered($security, $id_workshop, $id_ticket, $status);
        $workshop = $em->getRepository('WorkshopBundle:Workshop')->find($id_workshop);

        return $this->render('TicketBundle:Ticket:list_ticket_layout.html.twig', array('workshop' => $workshop,
                                                                                       'tickets'  => $tickets,
                                                                              ));
    }

    /**
     * Cierra el ticket
     * @param  Entity $id_ticket
     * @return url
     */
    public function closeTicketAction($id_ticket)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $security = $this->get('security.context');
        $request  = $this->getRequest();
        $ticket = $em->getRepository('TicketBundle:Ticket')->find($id_ticket);

        $form = $this->createForm(new CloseTicketType(), $ticket);

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {

                if($ticket->getSolution() != ""){
                    $closed = $em->getRepository('TicketBundle:Status')->findOneByName('closed');
                    $user   = $security->getToken()->getUser();
                    $ticket->setStatus($closed);
                    $ticket->setBlockedBy(null);

                    DefaultC::saveEntity($em, $ticket, $user);
                    return $this->redirect($this->generateUrl('showTicket', array('id_ticket' => $ticket->getId()) ));
                }
                else{
                    $this->get('session')->setFlash('error', '¡Error! Debes introducir una solucion');
                }
            }else{
                $this->get('session')->setFlash('error', '¡Error! No has introducido los valores correctamente');
            }
        }

        $systems = $em->getRepository('TicketBundle:System')->findAll();

        return $this->render('TicketBundle:Ticket:close_ticket_layout.html.twig', array('ticket'    => $ticket,
                                                                                        'systems'   => $systems,
                                                                                        'form'      => $form->createView(),
                                                                                        'form_name' => $form->getName(), ));
    }

    /**
     * Reabre el ticket
     * @param  Entity $id_ticket
     * @return url
     */
    public function reopenTicketAction($id_ticket)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $security = $this->get('security.context');

        $user = $security->getToken()->getUser();
        $status = $em->getRepository('TicketBundle:Status')->findOneByName('open');
        $ticket = $em->getRepository('TicketBundle:Ticket')->find($id_ticket);

        $ticket->setStatus($status);
        DefaultC::saveEntity($em, $ticket, $user);

        return $this->redirect($this->generateUrl('showTicket', array('id_ticket' => $ticket->getId()) ));
    }

    /**
     * Obtiene todos los talleres del usuario logeado
     */
    public function workshopListAction() {
        $em = $this->getDoctrine()->getEntityManager();

       // $logged_user = $this->get('security.context')->getToken()->getUser();
       // $workshops = $em->getRepository("WorkshopBundle:Workshop")->findByPartner($logged_user->getPartner()->getId());

        $workshops = $em->getRepository("WorkshopBundle:Workshop")->findAll();

        return $this->render('TicketBundle:Ticket:workshop/list_workshop.html.twig', array('workshops' => $workshops));
    }

    /**
     * A partir de un $id_taller, la vista listará todos sus tickets i se le podrá asignar un usuario
     * @param Int $id_workshop
     * @return type
     */
    public function getTicketsFromWorkshopAction($id_workshop) {
        $em = $this->getDoctrine()->getEntityManager();
        $workshop = $em->getRepository('WorkshopBundle:Workshop')->find($id_workshop);

        return $this->render('TicketBundle:Ticket:workshop/ticketsFromWorkshop.html.twig', array('workshop' => $workshop));
    }

    /**
     * Asigna un ticket a un usuario si se le pasa un $id_usuario, sino se pone a null
     * @param Int $id_ticket puede venir por POST o por parametro de la funcion
     * @param Int $id_user
     */
    public function assignUserToTicketAction($id_ticket, $id_user = null) {
        $em = $this->getDoctrine()->getEntityManager();
        $ticket = $em->getRepository('TicketBundle:Ticket')->find($id_ticket);

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

        //<--------------------------------------------------------------------------------------------------TODO hacer refactoring, no mola los 2 if(user=null)...

        $workshop = $em->getRepository('WorkshopBundle:Workshop')->find($ticket->getWorkshop()->getId());
        return $this->render('TicketBundle:Ticket:ticketsFromWorkshop.html.twig', array("workshop" => $workshop));
    }

    /**
     * Busca los posibles usuarios al cual podemos asingar un ticket
     * @param type $id_ticket
     * @return type
     */
    public function assignTicketSelectUserAction($id_ticket) {
        $em = $this->getDoctrine()->getEntityManager();
        $ticket = $em->getRepository('TicketBundle:Ticket')->find($id_ticket);
        $users = $this->getUsersToAssingFromTicket();

        return $this->render('TicketBundle:Ticket:ticket/assign_ticket.html.twig', array('ticket' => $ticket,
                                                                                         'users' => $users
                                                                                       ));
    }

    /**
     * Bloquea un ticket al asesor para que conteste
     * @param Int $id_ticket puede venir por POST o por parametro de la funcion
     * @param Int $id_user
     */
    public function blockTicketAction($id_ticket, $id_user = null) {
        $em = $this->getDoctrine()->getEntityManager();
        $ticket = $em->getRepository('TicketBundle:Ticket')->find($id_ticket);
        $user = $em->getRepository('UserBundle:User')->find($id_user);

        ($user != null) ? $ticket->setBlockedBy($user) : $ticket->setBlockedBy(null);

        $em->persist($ticket);
        $em->flush();

        return $this->showTicketAction($id_ticket);
    }

    /**
     * Muestra los posts que pertenecen a un ticket
     * @param integer $id_ticket
     * @return url
     */
    public function showTicketReadonlyAction($id_ticket) {
        $em = $this->getDoctrine()->getEntityManager();

        $systems  = $em->getRepository('TicketBundle:System')->findAll();
        $ticket = $em->getRepository('TicketBundle:Ticket')->find($id_ticket);

        return $this->render('TicketBundle:Ticket:show_ticket_readonly_layout.html.twig', array( 'ticket'    => $ticket,
                                                                                                 'systems'   => $systems, ));
    }

    /**
     * Funcion Ajax que devuelve un listado de tickets filtrados a partir de una opcion de un combo ($option)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function fill_ticketsAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();
        $security = $this->get('security.context');

        $option     = $petition->request->get('option');
        $user       = $security->getToken()->getUser();
        $repoTicket = $em->getRepository('TicketBundle:Ticket');
        $open       = $em->getRepository('TicketBundle:Status')->findOneBy(array('name' => 'open'  ));
        $closed     = $em->getRepository('TicketBundle:Status')->findOneBy(array('name' => 'closed'));

        if($security->isGranted('ROLE_ADMIN')){
            $allTickets = $repoTicket->findAll();
            //SuperAdmin
            if ($option == 'all'        )     $tickets = $allTickets;
            //Admin
            if ($option == 'all_opened' ){    $tickets = $repoTicket->findAllOpen($user, $open  , $allTickets); }
            else{
                if ($option == 'all_closed' ) $tickets = $repoTicket->findAllOpen($user, $closed, $allTickets); }

        }else {
            if($security->isGranted('ROLE_ASSESSOR')){
                //Assessor
                if  ($option == 'free' )                        { $tickets = $repoTicket->findAllTickets($em, $user, $open, 'free'            ); }
                else{ if ($option == 'assigned' )               { $tickets = $repoTicket->findAllTickets($em, $user, $open, 'assigned'        ); }
                    else{ if ($option == 'answered' )           { $tickets = $repoTicket->findAllTickets($em, $user, $open, 'answered', 'DESC'); }
                        else{ if ($option == 'other_assessor' ) { $tickets = $repoTicket->findAllTickets($em, $user, $open, 'other_assessor'  ); }
                        }
                    }
                }
            }else{

                $check_id = $petition->request->get('filter_id');

                if($check_id == 'all'){

                    $check_status = $petition->request->get('status');

                    if     ($check_status == 'all'   ) { $status = 'all';   }
                    elseif ($check_status == 'open'  ) { $status = $open;   }
                    elseif ($check_status == 'closed') { $status = $closed; }

                    //User
                    if ($option == 'created_by'      ) { $tickets = $repoTicket->findAllByOwner($user, $status);    }
                    else{ if ($option == 'workshop'  )   $tickets = $repoTicket->findAllByWorkshop($user, $status); }
                }else{
                    $array  = array('id' => $check_id);
                    $tickets = $repoTicket->findBy($array);
                }
            }
        }
        if(count($tickets) != 0){

            foreach ($tickets as $ticket) {
                $json[] = $ticket->to_json();
            }
        }else{
            $json[] = array('error' => "You don't have any ticket..");
        }
        return new Response(json_encode($json), $status = 200);
    }


    /**
     * Devuelve todos los usuarios que podran ser asignados a un ticket (admins i asesores has nuevo aviso)
     * @param type $id_ticket
     */
    private function getUsersToAssingFromTicket() {
        $em = $this->getDoctrine()->getEntityManager();
        $users = $em->getRepository('UserBundle:user')->findAll();

        $users_for_assign = array();
        foreach ($users as $user) {
            $role = $user->getRoles();
            if (($role[0]->getRole() == "ROLE_ADMIN") || ($role[0]->getRole() == "ROLE_ASSESSOR")) {
                $users_for_assign[] = $user;
            }
        }
        return $users_for_assign;
    }

    /**
     * Asigna un $ticket a un $user
     * Si $user == NULL, se desasigna
     * @param Ticket $ticket
     * @param User $user
     */
    private function assignTicket($ticket, $user=null) {
        $em = $this->getDoctrine()->getEntityManager();

        ($user != null) ? $ticket->setAssignedTo($user) : $ticket->setAssignedTo(null);

        $em->persist($ticket);
        $em->flush();
    }

}
