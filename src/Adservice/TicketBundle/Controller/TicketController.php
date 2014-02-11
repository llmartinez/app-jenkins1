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
use Adservice\TicketBundle\Form\TicketType;
use Adservice\CarBundle\Entity\Car;
use Adservice\CarBundle\Form\CarType;
use Adservice\TicketBundle\Entity\Post;
use Adservice\TicketBundle\Form\PostType;
use Adservice\UtilBundle\Entity\Document;
use Adservice\UtilBundle\Entity\DocumentRepository;
use Adservice\UtilBundle\Form\DocumentType;
use Adservice\WorkshopBundle\Form\WorkshopType;
use Adservice\WorkshopBundle\Entity\Workshop;

/*
  use Symfony\Component\Serializer\Encoder\JsonEncoder;
  use Symfony\Component\HttpFoundation\Response;
 */

class TicketController extends Controller {

    /**
     * Crea un ticket abierto con sus respectivos post y car
     * @return url
     */
    public function newTicketAction() {

        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        $ticket = new Ticket();
        $car = new Car();
        $post = new Post();
        $document = new Document();

        //Define Forms
        $form = $this->createForm(new TicketType(), $ticket);
        $formC = $this->createForm(new CarType(), $car);
        $formP = $this->createForm(new PostType(), $post);
        $formD = $this->createForm(new DocumentType(), $document);

        if ($request->getMethod() == 'POST') {

            //campos comunes
            $user = $em->getRepository('UserBundle:User')->find($request->get('user'));
            $status = $em->getRepository('TicketBundle:Status')->find('0');
            $security = $this->get('security.context');

            $form->bindRequest($request);
            $formC->bindRequest($request);
            $formP->bindRequest($request);
            $formD->bindRequest($request);

            if ($car->getVersion() != "") {
                //Define CAR
                $car = DefaultC::newEntity($car, $user);
                DefaultC::saveEntity($em, $car, $user, false);

                //Define TICKET
                $ticket = DefaultC::newEntity($ticket, $user);
                if ($security->isGranted('ROLE_ASSESSOR')) {
                    //$ticket->setWorkshop($request->get('workshop'));
                } else {
                    $ticket->setWorkshop($user->getWorkshop());
                }
                $ticket->setStatus($status);
                $ticket->setCar($car);
                DefaultC::saveEntity($em, $ticket, $user, false);

                //Define POST
                $post = DefaultC::newEntity($post, $user);
                $post->setTicket($ticket);
                DefaultC::saveEntity($em, $post, $user, false);

                //Define Document
                $document->setPost($post);

                if ($document->getFile() != "") {
                    $em->persist($document);
                }

                $em->flush();

                $sesion = $request->getSession();

                return $this->redirect($this->generateUrl('showTicket', array('id_ticket' => $ticket->getId())));
            } else {
                $this->get('session')->setFlash('error', '¡Error! No has introducido un vehiculo correctamente');
            }
        }

        $brands = $em->getRepository('CarBundle:Brand')->findAll();


        return $this->render('TicketBundle:Ticket:newTicket.html.twig', array( 'ticket' =>  $ticket, 
                                                                                'form' => $form->createView(), 
                                                                                'formC' => $formC->createView(),
                                                                                'formP' => $formP->createView(), 
                                                                                'formD' => $formD->createView(), 
                                                                                'brands' => $brands, ));

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
        $car = $ticket->getCar();

        $form = $this->createForm(new TicketType(), $ticket);
        $formC = $this->createForm(new CarType(), $car);

        if ($request->getMethod() == 'POST') {

            $user = $em->getRepository('UserBundle:User')->find($request->get('user'));
            $asesor = $em->getRepository('UserBundle:User')->find($request->get('asesor'));
            //Define CAR
            $formC->bindRequest($request);

            if ($car->getVersion() != "") {
                $car = DefaultC::newEntity($car, $user);
                DefaultC::saveEntity($em, $car, $user, false);

                $form->bindRequest($request);
                DefaultC::saveEntity($em, $ticket, $asesor);

                $sesion = $request->getSession();

                return $this->redirect($this->generateUrl('showTicket', array('id_ticket' => $ticket->getId())));
            } else {

                $this->get('session')->setFlash('error', '¡Error! No has introducido un vehiculo correctamente');
            }
        }

        $workshops = $em->getRepository('WorkshopBundle:Workshop')->findAll();
        $brands = $em->getRepository('CarBundle:Brand')->findAll();
        $models = $em->getRepository('CarBundle:Model')->findByBrand($car->getVersion()->getModel()->getBrand()->getId());
        $versions = $em->getRepository('CarBundle:Version')->findByModel($car->getVersion()->getModel()->getId());

        return $this->render('TicketBundle:Ticket:editTicket.html.twig', array(
                                                                                'form' => $form->createView(),
                                                                                'formC' => $formC->createView(),
                                                                                'tickets' => $this->loadTicket(),
                                                                                'ticket' => $ticket,
                                                                                'workshops' => $workshops,
                                                                                'brands' => $brands,
                                                                                'models' => $models,
                                                                                'versions' => $versions
                                                                    ));
    }

    /**
     * Muestra los posts que pertenecen a un ticket 
     * @param integer $id_ticket
     * @return url
     */
    public function showTicketAction($id_ticket) {
        $request = $this->getRequest();

        return $this->render('TicketBundle:Ticket:showTicket.html.twig', $this->createPost($request, $id_ticket));
    }

    /**
     * Muestra los posts que pertenecen a un ticket 
     * @param integer $id_ticket
     * @return url
     */
    public function showPostAction($id_ticket) {
        $request = $this->getRequest();

        return $this->render('TicketBundle:Ticket:showPost.html.twig', $this->createPost($request, $id_ticket));
    }

    /**
     * Devuelve todos los tickets realizados
     * @return url
     */
    public function listTicketAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();

        $ticket = new Ticket();

        if ($request->getMethod() == 'POST') {

            $id_ticket = $request->get('id_ticket');

            if ($id_ticket) {
                $ticket = $em->getRepository('TicketBundle:Ticket')->find($id_ticket);
            }
        }

        $tickets = $this->loadTicket();
        
       return $this->render('TicketBundle:Ticket:listTicket.html.twig', array('ticket' => $ticket, 
                                                                              'tickets' => $tickets, ));
    }

    /**
     * Crea un array con todos los tickets abiertos en funcion del usuario que este logeado en ese momento. 
     * Utiliza findTicketFiltered() de TicketRepository.
     * @return array
     */
    private function loadTicket() {
        // Prepara un array con todos los tickets abiertos que puede ver el usuario
        $em = $this->getDoctrine()->getEntityManager();

        //Rellenando la lista de tickets abiertos
        $ticketsFiltered = $em->getRepository('TicketBundle:Ticket')->findTicketFiltered($this->get('security.context'));

        return $ticketsFiltered;
    }

    /**
     * Prepara un array con el ticket al que pertenecen los posts y su id, 
     *                     todos los tickets abiertos que puede ver el usuario
     *                     y los formularios de post y documento
     * @param Request $request
     * @param integer $id_ticket
     * @return array
     */
    private function createPost($request, $id_ticket) {
        $em = $this->getDoctrine()->getEntityManager();

        $post = new Post();
        $document = new Document();

        //Define ticket al que pertenecen los posts
        $ticket = $em->getRepository('TicketBundle:Ticket')->find($id_ticket);

        //Define Form de posts
        $form = $this->createForm(new PostType(), $post);

        //Define Form de documents        
        $formD = $this->createForm(new DocumentType(), $document);

        if ($request->getMethod() == 'POST') {
            //$ticket->getAssignedTo()
            if (true) {

                $form->bindRequest($request);

                //Define User
                $user = $em->getRepository('UserBundle:User')->find($request->get('user'));

                //Define Post
                $post = DefaultC::newEntity($post, $user);
                $post->setTicket($ticket);
                DefaultC::saveEntity($em, $post, $user, false);

                //Define Document
                $formD->bindRequest($request);
                $document->setPost($post);

                if ($document->getFile() != "") {
                    $em->persist($document);
                }

                $em->flush();

                $sesion = $request->getSession();
            } else {
                
            }
        }
        
        $array = array('form' => $form->createView(),
                       'formD' => $formD->createView(),
                       'tickets'    =>  $this->loadTicket(),
                       'ticket' => $ticket,
                       'id_ticket' => $id_ticket,
                      );
        return $array;
    }

    /**
     * Obtiene todos los talleres del usuario logeado
     */
    public function workshopListAction() {
        $em = $this->getDoctrine()->getEntityManager();

        $logged_user = $this->get('security.context')->getToken()->getUser();
        $workshops = $em->getRepository("WorkshopBundle:Workshop")->findByPartner($logged_user->getPartner()->getId());

        return $this->render('TicketBundle:Ticket:workshopsList.html.twig', array('workshops' => $workshops));
    }
    
    /**
     * A partir de un $id_taller, la vista listará todos sus tickets i se le podrá asignar un usuario
     * @param Int $id_workshop
     * @return type
     */
    public function getTicketsFromWorkshopAction($id_workshop) {
        $em = $this->getDoctrine()->getEntityManager();
        $workshop = $em->getRepository('WorkshopBundle:Workshop')->find($id_workshop);

        return $this->render('TicketBundle:Ticket:ticketsFromWorkshop.html.twig', array('workshop' => $workshop));

    }
    
    /**
     * Asigna un ticket a un usuario si se le pasa un $id_usuario, sino se pone a null
     * @param Int $id_ticket puede venir por POST o por parametro de la funcion
     * @param Int $id_user
     */
    public function assignUserToTicketAction($id_ticket, $id_user=null){
        $em = $this->getDoctrine()->getEntityManager();
        $ticket = $em->getRepository('TicketBundle:Ticket')->find($id_ticket);

        //id_user puede venir por parametro o por post
        if ($id_user == null) {
            $petition = $this->getRequest();
            $id_user = $petition->get('id_user'); //<<<<-------------------------------------------------------------- refactoring per a la de YA
        }

        //si no viene ni por parametro ni por post, es que lo queremos desasignar
        if ($id_user != null){
            $user = $em->getRepository('UserBundle:User')->find($id_user);
            $ticket->setAssignedTo($user);
        }else{
            $ticket->setAssignedTo(null);
        }
        
        $em->persist($ticket);
        $em->flush();
        
        $workshop = $em->getRepository('WorkshopBundle:Workshop')->find($ticket->getWorkshop()->getId());
        return $this->render('TicketBundle:Ticket:ticketsFromWorkshop.html.twig', array("workshop" => $workshop));
        
    }
    
    /**
     * Busca los posibles usuarios al cual podemos asingar un ticket
     * @param type $id_ticket
     * @return type
     */
    public function assignTicketSelectUserAction($id_ticket){
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        $ticket = $em->getRepository('TicketBundle:Ticket')->find($id_ticket);
        $users = $this->getUsersToAssingFromTicket($ticket);
        
        return $this->render('TicketBundle:Ticket:assignTicket.html.twig', array('ticket' => $ticket,
                                                                                 'users'  => $users
                                                                               )); 
        
    }
    

    /**
     * Funcion Ajax que devuelve un listado de tickets filtrados a partir de una opcion de un combo ($option)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function fill_ticketsAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();

        $option = $petition->request->get('option');
        
        if($option == 'all'     ) $tickets = $em->getRepository('TicketBundle:Ticket')->findAll();
        if($option == 'ignore'  ) $tickets = $em->getRepository('TicketBundle:Ticket')->findBy(array('assigned_to' => null));
        if($option == 'assign'  ) $tickets = $em->getRepository('TicketBundle:Ticket')->findBy(array('assigned_to' => $this->get('security.context')->getToken()->getUser()->getId()));
        if($option == 'owner'   ) $tickets = $em->getRepository('TicketBundle:Ticket')->findBy(array('owner'       => $this->get('security.context')->getToken()->getUser()->getId()));
        if($option == 'workshop') $tickets = $em->getRepository('TicketBundle:Ticket')->findBy(array('workshop'    => $this->get('security.context')->getToken()->getUser()->getWorkshop()->getId()));
        
        return new Response(json_encode($tickets), $status = 200);
    }
    
    /**
     * Devuelve todos los usuarios que podran ser asignados a un ticket (admins i asesores has nuevo aviso)
     * @param type $id_ticket
     */
    private function getUsersToAssingFromTicket($ticket){
        $em = $this->getDoctrine()->getEntityManager();
        $workshop = $em->getRepository('WorkshopBundle:Workshop')->find($ticket->getWorkshop()->getId());
        $partner = $em->getRepository('PartnerBundle:Partner')->find($workshop->getPartner()->getId());

        $users_for_assign = array();
        foreach ($partner->getUsers() as $user) {
            $role = $user->getRoles();
            if (($role[0]->getRole() == "ROLE_ADMIN") || ($role[0]->getRole() == "ROLE_ASSESSOR") ){
                $users_for_assign[] = $user;
            }
        }
        return $users_for_assign;
    }

}
