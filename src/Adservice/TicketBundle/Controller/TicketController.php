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

            //Define CAR
            $formC->bindRequest($request);

            if ($car->getVersion() != "") {
                $car = DefaultC::newEntity($car, $user);
                DefaultC::saveEntity($em, $car, $user, false);

                //Define TICKET
                $form->bindRequest($request);
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
                $formP->bindRequest($request);
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
        $lastPosts = array();

        foreach ($tickets as $t) {
            $posts = $em->getRepository('TicketBundle:Post')->findBy(array('ticket' => $t->getId()));

            $lastPosts[$t->getId()] = $posts[count($posts) - 1];
        }

        return $this->render('TicketBundle:Ticket:listTicket.html.twig', array('ticket' => $ticket,
                    'lastPosts' => $lastPosts,
                    'tickets' => $tickets,));
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

        $posts = $em->getRepository('TicketBundle:Post')->findBy(array('ticket' => $id_ticket));

        $documents = $em->getRepository('UtilBundle:Document')->findDocumentFiltered($posts);

        $array = array('form' => $form->createView(),
            'formD' => $formD->createView(),
            'tickets' => $this->loadTicket(),
            'ticket' => $ticket,
            'id_ticket' => $id_ticket,
            'posts' => $posts,
            'documents' => $documents,
        );
        return $array;
    }

    /**
     * Asigna/desasigna un asesor a un ticket
     * @param integer $id_ticket
     * @param integer $user
     * @return url
     */
//    public function assignTicketAction($id_ticket) {
//        $em = $this->getDoctrine()->getEntityManager();
//        $request = $this->getRequest();
//        $ticket = $em->getRepository('TicketBundle:Ticket')->find($id_ticket);
//        $user = new User();
//        
//        if ($ticket->getAssignedTo()==null){
//            $user = $em->getRepository('UserBundle:User')->find($this->get('security.context')->getToken()->getUser()->getId());
//            $ticket->setAssignedTo($user);
//            
//        }else{
//            $ticket->setAssignedTo(null);
//        }
//        
//        $em->persist($ticket);
//        $em->flush();
//
//        return $this->render('TicketBundle:Ticket:showTicket.html.twig', $this->createPost($request, $id_ticket));
//    }

    public function workshopListAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $workshops = $em->getRepository('WorkshopBundle:Workshop')->findAll();

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

        $petition = $this->getRequest();
        if ($petition->getMethod() == 'POST') {
            $id_ticket = $petition->get('id_ticket');
            $id_user = $petition->get('id_user');
            $this->assignUserToTicket($id_ticket, $id_user);
        }

        return $this->render('TicketBundle:Ticket:ticketsFromWorkshop.html.twig', array('workshop' => $workshop));

    }
    
    /**
     * Asigna un ticket a un usuario si se le pasa un $id_usuario, sino se pone a null
     * @param Int $id_ticket 
     * @param Int $id_user
     */
    private function assignUserToTicket($id_ticket, $id_user=null){
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        $ticket = $em->getRepository('TicketBundle:Ticket')->find($id_ticket);
        
        if($id_user != null){
            $user = $em->getRepository('UserBundle:User')->find($id_user);
            $ticket->setAssignedTo($user);
        }else{
            $ticket->setAssignedTo(null);
        }
        
        $em->persist($ticket);
        $em->flush();
        
    }

    /**/

    public function fill_ticketsAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();

        $option = $petition->request->get('option');
        
        if($option == 'all') $tickets = $em->getRepository('TicketBundle:Ticket')->findAll();
        if($option == 'assign') $tickets = $em->getRepository('TicketBundle:Ticket')->findBy(array('assigned_to' => $this->get('security.context')->getToken()->getUser()->getId()));
        if($option == 'ignore') $tickets = $em->getRepository('TicketBundle:Ticket')->findBy(array('assigned_to' => null));
        
        return new Response(json_encode($tickets), $status = 200);
    }

}
