<?php

namespace Adservice\TicketBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Adservice\TicketBundle\Controller\DefaultController as DefaultC;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Adservice\TicketBundle\Entity\Ticket;
use Adservice\TicketBundle\Entity\TicketRepository;
use Adservice\TicketBundle\Form\TicketType;

use Adservice\TicketBundle\Entity\Post;
use Adservice\TicketBundle\Form\PostType;

use Adservice\UtilBundle\Entity\Document;
use Adservice\UtilBundle\Entity\DocumentRepository;
use Adservice\UtilBundle\Form\DocumentType;

use Adservice\CarBundle\Entity\Car;

/*
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpFoundation\Response;
*/
class TicketController extends Controller{
    
    /**
     * Crea un ticket abierto con sus respectivos post y car
     * @return url
     */
    public function newTicketAction() {
        
         $em = $this->getDoctrine()->getEntityManager();
         $request = $this->getRequest();
         
         $ticket = new Ticket();
        
         $form = $this->createForm(new TicketType(), $ticket);
        
         if ($request->getMethod() == 'POST') {
                
            $form->bindRequest($request);

            //campos comunes
            $user = $em->getRepository('UserBundle:User')->find($request->get('user'));
            $status = $em->getRepository('TicketBundle:Status')->find(0);
            $version = $em->getRepository('CarBundle:Version')->find(array($request->get('version')));
            
            //campos de CAR
            $car = DefaultC::newEntity(new Car(),$user); 
            $car->setVersion($version);
            $car->setVin($request->get('vin'));
            $car->setPlateNumber($request->get('plateNumber'));
            $car->setYear($request->get('year'));
            DefaultC::saveEntity($em, $car, $user, false);
            
            //campos de TICKET
            $ticket= DefaultC::newEntity($ticket,$user); 
            $ticket->setStatus($status);
            $ticket->setCar($car);
            DefaultC::saveEntity($em, $ticket, $user, false);
            
            //campos de POST
            $post = DefaultC::newEntity(new Post(),$user); 
            $post->setTicket($ticket);
            $post->setMessage($request->get('message'));
            DefaultC::saveEntity($em, $post, $user);

            $sesion = $request->getSession();

            return $this->redirect($this->generateUrl('showTicket', array('id_ticket' => $ticket->getId())));
            }
         
         $brands = $em->getRepository('CarBundle:Brand')->findAll();
         $models = $em->getRepository('CarBundle:Model')->findAll();
         $versions = $em->getRepository('CarBundle:Version')->findAll();
            
         return $this->render('TicketBundle:Ticket:newTicket.html.twig', array( 'tickets' =>  $this->loadTicket(), 
                                                                                'form' => $form->createView(), 
                                                                                'brands' => $brands, 
                                                                                'models' => $models,
                                                                                'versions' => $versions, ));
    }
    
    /**
     * Edita el ticket y el car asignado a partir de su id
     * @param integer $id_ticket
     * @return url
     */
    public function editTicketAction($id_ticket)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();

        $ticket = $em->getRepository('TicketBundle:Ticket')->find($id_ticket);

        $form = $this->createForm(new TicketType(), $ticket);
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            $user = $em->getRepository('UserBundle:User')->find($request->get('user'));
            $asesor = $em->getRepository('UserBundle:User')->find($request->get('asesor'));
            $version = $em->getRepository('CarBundle:Version')->find(array($request->get('version')));
             
            //campos de CAR
            $car = $em->getRepository('CarBundle:Car')->find($ticket->getCar()->getId());
            $car->setVersion($version);
            $car->setVin($request->get('vin'));
            $car->setPlateNumber($request->get('plateNumber'));
            $car->setYear($request->get('year'));
            DefaultC::saveEntity($em, $car, $user, false);
            
            DefaultC::saveEntity($em, $ticket, $asesor);

            $sesion = $request->getSession();
            
            return $this->redirect($this->generateUrl('showTicket', array('id_ticket' => $ticket->getId())));
        }
        
        $posts = $em->getRepository('TicketBundle:Post')->findBy(array('ticket' => $ticket->getId()));
        $users = $em->getRepository('UserBundle:User')->findAll();
        $brands = $em->getRepository('CarBundle:Brand')->findAll();
        $models = $em->getRepository('CarBundle:Model')->findAll();
        $versions = $em->getRepository('CarBundle:Version')->findAll();
            
        return $this->render('TicketBundle:Ticket:editTicket.html.twig', array( 'tickets' => $this->loadTicket(),
                                                                                'ticket' => $ticket, 
                                                                                'posts' => $posts,
                                                                                'users' => $users, 
                                                                                'brands' => $brands, 
                                                                                'models' => $models,
                                                                                'versions' => $versions,
                                                                                'form' => $form->createView(),
                                                                              ));
    }
    
    /**
     * Muestra los posts que pertenecen a un ticket 
     * @param integer $id_ticket
     * @return url
     */
    public function showTicketAction($id_ticket)
    {
        $request = $this->getRequest();
        
        return $this->render('TicketBundle:Ticket:showTicket.html.twig', $this->createPost($request, $id_ticket));
    }    
    
    /**
     * Muestra los posts que pertenecen a un ticket 
     * @param integer $id_ticket
     * @return url
     */
    public function showPostAction($id_ticket)
    {
        $request = $this->getRequest();

        return $this->render('TicketBundle:Ticket:showPost.html.twig', $this->createPost($request, $id_ticket));
    }   
    
    /**
     * Devuelve todos los tickets realizados
     * @return url
     */
    public function listTicketAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        
        $ticket = new Ticket();
        
        if ($request->getMethod() == 'POST') {
            
            $id_ticket = $request->get('id_ticket');
            
            if($id_ticket){
                $ticket = $em->getRepository('TicketBundle:Ticket')->find($id_ticket);
            }
        }  
        
        $tickets = $this->loadTicket();
        $lastPosts = array();
        
        foreach ($tickets as $t)
        {
             $posts = $em->getRepository('TicketBundle:Post')->findBy(array('ticket' => $t->getId()));
             
             $lastPosts[$t->getId()] = $posts[count($posts)-1];
        }
        
       return $this->render('TicketBundle:Ticket:listTicket.html.twig', array('ticket' => $ticket, 
                                                                              'lastPosts' => $lastPosts, 
                                                                              'tickets' => $tickets, ));
    }
    
    /**
     * Crea un array con todos los tickets abiertos en funcion del usuario que este logeado en ese momento. 
     * Utiliza findTicketFiltered() de TicketRepository.
     * @return array
     */
    private function loadTicket()
    {
    // Prepara un array con todos los tickets abiertos que puede ver el usuario
        $em = $this->getDoctrine()->getEntityManager();
        
        //Rellenando la lista de tickets abiertos
        $ticketsFiltered = $em->getRepository('TicketBundle:Ticket')->findTicketFiltered($this->get('security.context'));
        
        return $ticketsFiltered;
    }
    
    /**
     * Prepara un array con el ticket al que pertenecen los posts y su id, 
    //                     todos los tickets abiertos que puede ver el usuario
    //                     y los formularios de post y documento
     * @param Request $request
     * @param integer $id_ticket
     * @return array
     */
    private function createPost($request, $id_ticket)
    {        
        $em = $this->getDoctrine()->getEntityManager();
                
        $post = new Post();
        $document = new Document();
        
        //Define ticket al que pertenecen los posts
        $ticket = $em->getRepository('TicketBundle:Ticket')->find($id_ticket);
        
        //Define Form de posts
        $form = $this->createForm(new PostType(), $post);
        
        //Define Form de documents        
        $formF = $this->createForm(new DocumentType(), $document);
        
        if ($request->getMethod() == 'POST') {
                
            $form->bindRequest($request);
            
            //Define User
            $user = $em->getRepository('UserBundle:User')->find($request->get('user'));
            $date = new \DateTime(\date("Y-m-d H:i:s"));
      
            //Define Post
            $post = DefaultC::newEntity($ticket,$user);
            $post->setTicket($ticket);
            DefaultC::saveEntity($em, $post, $user, false);
            
            //Define Document
            $formF->bindRequest($request);
            $document->setPost($post);
            
            if ($document->getFile() != "")
            {
                $em->persist($document);
            }
            
            $em->flush();

            $sesion = $request->getSession();

        }
        
        $posts = $em->getRepository('TicketBundle:Post')->findBy(array('ticket' => $id_ticket));
        
        $documents = $em->getRepository('UtilBundle:Document')->findDocumentFiltered($posts);
        
        $array = array('form' => $form->createView(),
                       'formF' => $formF->createView(),
                       'tickets'    =>  $this->loadTicket(),
                       'ticket' => $ticket,
                       'id_ticket' => $id_ticket,
                       'posts' => $posts, 
                       'documents' => $documents,
                      );
        return $array;
    }

}
