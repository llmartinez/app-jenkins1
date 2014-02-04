<?php

namespace Adservice\TicketBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

    public function saveEntity($em, $entity, $user, $auto_flush=true)
    {
        $entity->setModifiedBy($user);
        $entity->setModifiedAt(new \DateTime(\date("Y-m-d H:i:s")));
        $em->persist($entity); 
        if($auto_flush) $em->flush(); 
        return true;
    }
    public function newTicketAction() {
        
         $em = $this->getDoctrine()->getEntityManager();
         $request = $this->getRequest();
         
         $ticket = new Ticket();
        
         $form = $this->createForm(new TicketType(), $ticket);
        
         if ($request->getMethod() == 'POST') {
                
            $form->bindRequest($request);

            //campos comunes
            $user = $em->getRepository('UserBundle:User')->find($request->get('user'));
            $date = new \DateTime(\date("Y-m-d H:i:s"));

            $status = $em->getRepository('TicketBundle:Status')->find(0);
            $version = $em->getRepository('CarBundle:Version')->find(array($request->get('version')));
            
            //campos de CAR
            $car = new Car();
            $car->setVersion($version);
            $car->setVin($request->get('vin'));
            $car->setPlateNumber($request->get('plateNumber'));
            $car->setYear($request->get('year'));
            $car->setOwner($user);
            $car->setCreatedAt($date);
            $this->saveEntity($em, $car, $user, false);
            
            //campos de TICKET
            $ticket->setStatus($status);
            $ticket->setCar($car);
            $ticket->setOwner($user);
            $ticket->setCreatedAt($date);
            $this->saveEntity($em, $ticket, $user, false);
            
            //campos de POST
            $post = new Post();
            $post->setTicket($ticket);
            $post->setMessage($request->get('message'));
            $post->setOwner($user);
            $post->setCreatedAt($date);
            $this->saveEntity($em, $post, $user);

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
            $this->saveEntity($em, $car, $user, false);
            
            $this->saveEntity($em, $ticket, $asesor);

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
    
    public function showTicketAction($id_ticket)
    {
        $request = $this->getRequest();
        
        return $this->render('TicketBundle:Ticket:showTicket.html.twig', $this->createPost($request, $id_ticket));
    }      
     
    public function showPostAction($id_ticket)
    {
        $request = $this->getRequest();

        return $this->render('TicketBundle:Ticket:showPost.html.twig', $this->createPost($request, $id_ticket));
    }   
    
    
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
    
    private function loadTicket()
    {
    // Prepara un array con todos los tickets abiertos que puede ver el usuario
        $em = $this->getDoctrine()->getEntityManager();
        
        //Rellenando la lista de tickets abiertos
        $ticketsFiltered = $em->getRepository('TicketBundle:Ticket')->findTicketFiltered($this->get('security.context'));
        
        return $ticketsFiltered;
    }
    
    private function createPost($request, $id_ticket)
    {
    // Prepara un array con el ticket al que pertenecen los posts y su id, 
    //                     todos los tickets abiertos que puede ver el usuario
    //                     y los formularios de post y documento
        
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
            $post->setTicket($ticket);
            
            $post->setOwner($user); 
            $post->setCreatedAt($date);
            $this->saveEntity($em, $post, $user, false);
            
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
