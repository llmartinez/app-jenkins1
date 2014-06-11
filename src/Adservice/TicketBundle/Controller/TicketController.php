<?php
namespace Adservice\TicketBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Adservice\UtilBundle\Controller\UtilController as UtilController;
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

use Adservice\UtilBundle\Entity\Pagination;
use Adservice\UtilBundle\Entity\Mailer;

class TicketController extends Controller {

    /**
     * Devuelve el listado de tickets segunla pagina y la opcion escogida
     * @return url
     */
    public function listTicketAction($page=1 , $option=null)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $request    = $this->getRequest();
        $security   = $this->get('security.context');

        $id_user    = $this->get('security.context')->getToken()->getUser()->getId();
        $open       = $em->getRepository('TicketBundle:Status')->findOneBy(array('name' => 'open'  ));
        $closed     = $em->getRepository('TicketBundle:Status')->findOneBy(array('name' => 'closed'));
        $workshops  = array('0' => new Workshop());

        /* TRATAMIENTO DE LAS OPCIONES DE slct_historyTickets */
        if($option == null){
            // Si se envia el codigo del taller se buscan los tickets en funcion de estos
            if ($request->getMethod() == 'POST') {
                $workshops = $em->getRepository('WorkshopBundle:Workshop')->findWorkshopInfo($request);

                if($workshops[0]->getId() != "") {
                    $params[] = array('workshop', '= '.$workshops[0]->getId());
                    $option = $workshops[0]->getId();
                }
                else{ $params[] = array(); }
            }
            elseif (!$security->isGranted('ROLE_ASSESSOR')) {
                $workshops = $em->getRepository('WorkshopBundle:Workshop')->findBy(array('id' => $security->getToken()->getUser()->getWorkshop()->getId()));

                if($workshops[0]->getId() != "") {
                    $params[] = array('workshop', '= '.$workshops[0]->getId());
                    $option = $workshops[0]->getId();
                }
                else{ $params[] = array(); }
            }
            else{ $params[] = array(); }
            $option = 'all';
        }

        elseif ($option == 'all'      ) { $params[] = array();  }
        elseif ($option == 'opened'   ) { $params[] = array('status'        , ' = '.$open  ->getId()); }
        elseif ($option == 'closed'   ) { $params[] = array('status'        , ' = '.$closed->getId()); }
        elseif ($option == 'free'     )
        {
            $params[] = array('status'        , ' = '.$open  ->getId());
            $params[] = array('assigned_to '  , 'IS NULL');
        }
        elseif ($option == 'pending'          or  $option == 'answered')
        {
            $params[] = array('status', ' = '.$open->getId());
            $params[] = array('assigned_to'   , 'IS NOT NULL');
        }
        elseif ($option == 'assessor_pending' or  $option == 'assessor_answered')
        {
            $params[] = array('status', ' = '.$open->getId());
            $params[] = array('assigned_to'   , '= '.$id_user);
        }
        elseif ($option == 'other_pending'    or  $option == 'other_answered')
        {
            $params[] = array('status', ' = '.$open->getId());
            $params[] = array('assigned_to'   , '!= '.$id_user);
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
        else{
            $workshops = $em->getRepository('WorkshopBundle:Workshop')->findBy(array('id' => $option));
            $params[] = array('workshop', ' = '.$option);
        }
        $pagination = new Pagination($page);

        if(($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) or ($workshops[0]->getId() != null)){
            $tickets = $pagination->getRows($em, 'TicketBundle', 'Ticket', $params, $pagination);
            $length = $pagination->getRowsLength($em, 'TicketBundle', 'Ticket', $params);
        }
        elseif(($option == 'assessor_pending') or ($option == 'assessor_answered') or ($option == 'other_pending') or ($option == 'other_answered')) {

            $query = 'SELECT t FROM TicketBundle:Ticket t';
            if     (($option == 'assessor_pending') or ($option == 'assessor_answered')) $query = $query.' WHERE t.assigned_to = '.$id_user;
            elseif (($option == 'other_pending'   ) or ($option == 'other_answered'   )) $query = $query.' WHERE t.assigned_to != '.$id_user;

            $consulta = $em->createQuery($query);
            $query_posts = '';
            foreach ($consulta->getResult() as $ticket)
            {
                $query2 = 'SELECT p FROM TicketBundle:Post p WHERE p.ticket = '.$ticket->getId();
                $consulta2 = $em->createQuery($query2);
                $result = $consulta2->getResult();
                $last_post = end($result);

                if(count($result) != 0 and $last_post != null
                and ($last_post->getCreatedBy()->getRoles()[0] == 'ROLE_ASSESSOR'
                  or $last_post->getCreatedBy()->getRoles()[0] == 'ROLE_ADMIN'
                  or $last_post->getCreatedBy()->getRoles()[0] == 'ROLE_SUPER_ADMIN')
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
            else $joins = array();

            $tickets = $pagination->getRows      ($em, 'TicketBundle', 'Ticket', $params, $pagination, null, $joins);
            $length  = $pagination->getRowsLength($em, 'TicketBundle', 'Ticket', $params, null, $joins);
        }
        else{
            $joins[] = array('e.workshop w', ' w.country = '.$this->get('security.context')->getToken()->getUser()->getCountry()->getId());
            $tickets = $pagination->getRows      ($em, 'TicketBundle', 'Ticket', $params, $pagination, null, $joins);
            $length  = $pagination->getRowsLength($em, 'TicketBundle', 'Ticket', $params, null, $joins);
        }

        $pagination->setTotalPagByLength($length);

        return $this->render('TicketBundle:Layout:list_ticket_layout.html.twig', array('workshop'   => $workshops[0],
                                                                                       'pagination' => $pagination,
                                                                                       'tickets'    => $tickets,
                                                                                       'option'     => $option,
                                                                              ));
    }

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
                        //La segunda comparacion ($form->getErrors()...) se hizo porque el request que reciber $form puede ser demasiado largo y hace que la funcion isValid() devuelva false
                        if (($form ->isValid() or $form ->getErrors()[0]->getMessageTemplate() == 'The uploaded file was too large. Please try to upload a smaller file')
                         && ($formC->isValid() or $formC->getErrors()[0]->getMessageTemplate() == 'The uploaded file was too large. Please try to upload a smaller file')) {

                        //Define CAR
                        $car = UtilController::newEntity($car, $user);

                        $id_brand   = $request->request->get('new_car_form_brand'  );
                        $id_model   = $request->request->get('new_car_form_model'  );
                        $id_version = $request->request->get('new_car_form_version');

                        $brand   = $em->getRepository('CarBundle:Brand'  )->find($id_brand  );
                        $model   = $em->getRepository('CarBundle:Model'  )->find($id_model  );
                        $version = $em->getRepository('CarBundle:Version')->find($id_version);

                        $car->setBrand($brand);
                        $car->setModel($model);
                        $car->setVersion($version);
                        $car = UtilController::newEntity($car, $user);
                        UtilController::saveEntity($em, $car, $user, false);

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
                        $ticket->setCar($car);
                        UtilController::saveEntity($em, $ticket, $user);

                        /* MAILING */
                        $mailer = $this->get('cms.mailer');
                        $mailer->setTo('dmaya@grupeina.com');  /* COLOCAR EN PROD -> *//* $mailer->setTo($ticket->getWorkshop()->getUsers()[0]->getEmail1());*/
                        $mailer->setSubject($this->get('translator')->trans('mail.newTicket.subject').$ticket->getId());
                        $mailer->setFrom('noreply@grupeina.com');
                        $mailer->setBody($this->renderView('UtilBundle:Mailing:ticket_new_mail.html.twig', array('ticket' => $ticket)));
                        $mailer->sendMailToSpool();
                        //echo $this->renderView('UtilBundle:Mailing:ticket_new_mail.html.twig', array('ticket' => $ticket));die;

                        if (isset($_POST['save&close'])){
                            return $this->redirect($this->generateUrl('closeTicket', array( 'id_ticket' => $ticket->getId())));
                        }else{
                            return $this->redirect($this->generateUrl('showTicket', array('id_ticket' => $ticket->getId())));
                        }

                    } else { $this->get('session')->setFlash('error', '¡Error! No has introducido los valores correctamente'); }

                } else { $this->get('session')->setFlash('error_ticket', '¡Error! No has introducido los campos de ticket correctamente'); }

            // } else { $this->get('session')->setFlash('error_car', '¡Error! No has introducido el vehiculo correctamente'); }
        }

        $brands  = $em->getRepository('CarBundle:Brand'    )->findAll();
        return $this->render('TicketBundle:Layout:new_ticket_layout.html.twig', array('ticket' => $ticket,
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

                    UtilController::saveEntity($em, $ticket, $user);

                    /* MAILING */
                    $mailer = $this->get('cms.mailer');
                    $mailer->setTo('dmaya@grupeina.com');  /* COLOCAR EN PROD -> *//* $mailer->setTo($ticket->getWorkshop()->getUsers()[0]->getEmail1());*/
                    $mailer->setSubject($this->get('translator')->trans('mail.editTicket.subject').$ticket->getId());
                    $mailer->setFrom('noreply@grupeina.com');
                    $mailer->setBody($this->renderView('UtilBundle:Mailing:ticket_edit_mail.html.twig', array('ticket' => $ticket)));
                    $mailer->sendMailToSpool();
                    //echo $this->renderView('UtilBundle:Mailing:ticket_new_mail.html.twig', array('ticket' => $ticket));die;

                    return $this->redirect($this->generateUrl('showTicket', array('id_ticket' => $ticket->getId())));

            }else{ $this->get('session')->setFlash('error', '¡Error! No has introducido los valores correctamente'); }
        }

        $systems     = $em->getRepository('TicketBundle:System'    )->findAll();

        return $this->render('TicketBundle:Layout:show_ticket_layout.html.twig', array(
                                                                                        'form'      => $form->createView(),
                                                                                        'form_name' => $form->getName(),
                                                                                        'ticket'    => $ticket,
                                                                                        'systems'   => $systems,
                                                                                        'form_name' => $form->getName(),
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

        /* MAILING */
        $mailer = $this->get('cms.mailer');
        $mailer->setTo('dmaya@grupeina.com');  /* COLOCAR EN PROD -> *//* $mailer->setTo($ticket->getWorkshop()->getUsers()[0]->getEmail1());*/
        $mailer->setSubject($this->get('translator')->trans('mail.deleteTicket.subject').$ticket->getId());
        $mailer->setFrom('noreply@grupeina.com');
        $mailer->setBody($this->renderView('UtilBundle:Mailing:ticket_delete_mail.html.twig', array('ticket' => $ticket)));
        $mailer->sendMailToSpool();
        //echo $this->renderView('UtilBundle:Mailing:ticket_delete_mail.html.twig', array('ticket' => $ticket));die;

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
        $request  = $this->getRequest();
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

            if(($security->isGranted('ROLE_ASSESSOR') and ($form->isValid() or $form->getErrors()[0]->getMessageTemplate() == 'The uploaded file was too large. Please try to upload a smaller file')) or (!$security->isGranted('ROLE_ASSESSOR'))){

                $formP->bindRequest($request);
                $formD->bindRequest($request);

                if (($formP->isValid() or $formP ->getErrors()[0]->getMessageTemplate() == 'The uploaded file was too large. Please try to upload a smaller file')
                and ($formD->isValid() or $formD ->getErrors()[0]->getMessageTemplate() == 'The uploaded file was too large. Please try to upload a smaller file')) {

                    //Define Post
                    $post = UtilController::newEntity($post, $user);
                    $post->setTicket($ticket);
                    UtilController::saveEntity($em, $post, $user, false);

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

                    UtilController::saveEntity($em, $ticket, $user);

                    /* MAILING */
                    $mailer = $this->get('cms.mailer');
                    $mailer->setTo('dmaya@grupeina.com');  /* COLOCAR EN PROD -> *//* $mailer->setTo($ticket->getWorkshop()->getUsers()[0]->getEmail1());*/
                    $mailer->setSubject($this->get('translator')->trans('mail.answerTicket.subject').$ticket->getId());
                    $mailer->setFrom('noreply@grupeina.com');
                    $mailer->setBody($this->renderView('UtilBundle:Mailing:ticket_answer_mail.html.twig', array('ticket' => $ticket)));
                    $mailer->sendMailToSpool();
                    //echo $this->renderView('UtilBundle:Mailing:ticket_answer_mail.html.twig', array('ticket' => $ticket));die;
                }
            }
            return $this->redirect($this->generateUrl('showTicket', array(  'id_ticket' => $ticket->getId(),
                                                                            'form_name' => $formP->getName(),
                                                                            'ticket'    => $ticket,
                                                                            'systems'   => $systems,
                                                                            'form_name' => $formP->getName(), )));
        }

        $array = array( 'formP'     => $formP->createView(),
                        'formD'     => $formD->createView(),
                        'ticket'    => $ticket,
                        'systems'   => $systems,
                        'form_name' => $formP->getName(), );

        if ($security->isGranted('ROLE_ASSESSOR')) {  $array['form'] = ($form ->createView()); }

        return $this->render('TicketBundle:Layout:show_ticket_layout.html.twig', $array);
    }

    /**
     * Devuelve todos los tickets realizados
     * @return url
     */
    public function listTicketFilteredAction($page=1, $id_workshop='none', $id_ticket='none', $status='all', $option='all')
    {
        $em = $this->getDoctrine()->getEntityManager();
        $request  = $this->getRequest();
        $workshop = new Workshop();
        $tickets  = array();
        $params   = array();

        if($id_ticket   != 'none') $params[] = array('id'    , ' = '.$id_ticket  );
        if($id_workshop != 'none')  {
                                        $workshop = $em->getRepository('WorkshopBundle:Workshop')->find($id_workshop);
                                        $params[] = array('workshop'    , ' = '.$id_workshop );
                                    }
        if($status      != 'all' )  {
                                        $id_status = $em->getRepository('TicketBundle:Status')->findOneBy(array('name' => $status))->getId();
                                        $params[] = array('status', " = '".$id_status."'");
                                    }

        $pagination = new Pagination($page);

        $tickets = $pagination->getRows($em, 'TicketBundle', 'Ticket', $params, $pagination);

        $length  = $pagination->getRowsLength($em, 'TicketBundle', 'Ticket', $params);

        $pagination->setTotalPagByLength($length);

        return $this->render('TicketBundle:Layout:list_ticket_layout.html.twig', array('workshop'   => $workshop,
                                                                                       'pagination' => $pagination,
                                                                                       'tickets'    => $tickets,
                                                                                       'option'     => $option,
                                                                              ));
    }

    /**
     * Cierra el ticket
     * @param  Entity $id_ticket
     * @return url
     */
    public function closeTicketAction($id_ticket=null)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $security = $this->get('security.context');
        $request  = $this->getRequest();
        $ticket = $em->getRepository('TicketBundle:Ticket')->find($id_ticket);

        $form = $this->createForm(new CloseTicketType(), $ticket);

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid() or $form ->getErrors()[0]->getMessageTemplate() == 'The uploaded file was too large. Please try to upload a smaller file') {

                if($ticket->getSolution() != ""){
                    $closed = $em->getRepository('TicketBundle:Status')->findOneByName('closed');
                    $user   = $security->getToken()->getUser();
                    $ticket->setStatus($closed);
                    $ticket->setBlockedBy(null);

                    UtilController::saveEntity($em, $ticket, $user);

                    /* MAILING */
                        $mailer = $this->get('cms.mailer');
                        $mailer->setTo('dmaya@grupeina.com');  /* COLOCAR EN PROD -> *//* $mailer->setTo($ticket->getWorkshop()->getUsers()[0]->getEmail1());*/
                        $mailer->setSubject($this->get('translator')->trans('mail.closeTicket.subject').$ticket->getId());
                        $mailer->setFrom('noreply@grupeina.com');
                        $mailer->setBody($this->renderView('UtilBundle:Mailing:ticket_close_mail.html.twig', array('ticket' => $ticket)));
                        $mailer->sendMailToSpool();
                        //echo $this->renderView('UtilBundle:Mailing:ticket_close_mail.html.twig', array('ticket' => $ticket));die;

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

        return $this->render('TicketBundle:Layout:close_ticket_layout.html.twig', array('ticket'    => $ticket,
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
        UtilController::saveEntity($em, $ticket, $user);
         /* MAILING */
            $mailer = $this->get('cms.mailer');
            $mailer->setTo('dmaya@grupeina.com');  /* COLOCAR EN PROD -> *//* $mailer->setTo($ticket->getWorkshop()->getUsers()[0]->getEmail1());*/
            $mailer->setSubject($this->get('translator')->trans('mail.reopenTicket.subject').$ticket->getId());
            $mailer->setFrom('noreply@grupeina.com');
            $mailer->setBody($this->renderView('UtilBundle:Mailing:ticket_reopen_mail.html.twig', array('ticket' => $ticket)));
            $mailer->sendMailToSpool();
            //echo $this->renderView('UtilBundle:Mailing:ticket_reopen_mailecho 'pasa';.html.twig', array('ticket' => $ticket));die;

        return $this->redirect($this->generateUrl('showTicket', array('id_ticket' => $ticket->getId()) ));
    }

    /**
     * Obtiene todos los talleres del usuario logeado
     */
    public function workshopListAction($page=1 , $option=null) {
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
    public function getTicketsFromWorkshopAction($id_workshop, $page=1) {
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

        $workshop = $em->getRepository('WorkshopBundle:Workshop')->find($ticket->getWorkshop()->getId());
        return $this->render('TicketBundle:Workshop:ticketsFromWorkshop.html.twig', array('tickets' => $workshop->getTickets()));
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

        return $this->render('TicketBundle:Ticket:assign_ticket.html.twig', array('ticket' => $ticket,
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

        ($user != null and $id_user != 0) ? $ticket->setBlockedBy($user) : $ticket->setBlockedBy(null);

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

        return $this->render('TicketBundle:Layout:show_ticket_readonly_layout.html.twig', array( 'ticket'    => $ticket,
                                                                                                 'systems'   => $systems, ));
    }

    /**
     * Funcion que devuelve un listado de tickets filtrados a partir de una opcion de un combo ($option)
     * @return array
     */
    public function getTicketsByOption($option) {
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

    /**
     * Filtra un array de tickets en funcion del id
     * @param  Array   $tickets
     * @param  Integer $check_id
     * @return Array
     */
    private function filterTickets($tickets,$check_id){
        $tickets_filtered = array();

        foreach ($tickets as $ticket) {

            if($ticket->getId() == $check_id)
                $tickets_filtered[] = $ticket;
        }
        return $tickets_filtered;
    }

}
