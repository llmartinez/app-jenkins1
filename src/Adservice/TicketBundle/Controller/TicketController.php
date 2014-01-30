<?php

namespace Adservice\TicketBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Adservice\TicketBundle\Entity\Ticket;
use Adservice\TicketBundle\Entity\TicketRepository;
use Adservice\TicketBundle\Form\TicketType;
use Adservice\TicketBundle\Entity\Post;
use Adservice\TicketBundle\Form\PostType;
/*
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpFoundation\Response;
*/
class TicketController extends Controller{

    public function newTicketAction() {
        
         $em = $this->getDoctrine()->getEntityManager();
         $request = $this->getRequest();
         
         $ticket = new Ticket();
        
         $form = $this->createForm(new TicketType(), $ticket);
        
         if ($request->getMethod() == 'POST') {
                
            $form->bindRequest($request);

            //campos de TICKET
            $user = $em->getRepository('UserBundle:User')->find($request->get('user'));

            $status = $em->getRepository('TicketBundle:Status')->findOneBy(array('status' => 'Abierto'));

            //campos de TICKET
            $ticket->setCreatedBy($user);
            $ticket->setCreatedAt(new \DateTime(\date("Y-m-d H:i:s")));
            $ticket->setModifiedBy($user);
            $ticket->setModifiedAt(new \DateTime(\date("Y-m-d H:i:s")));
            $ticket->setStatus($status);
            $em->persist($ticket);

            //campos de POST
            $post= new Post();
            $post->setTicket($ticket);
            $post->setCreatedBy($user);
            $post->setCreatedAt(new \DateTime(\date("Y-m-d H:i:s")));
            $post->setModifiedBy($user);
            $post->setModifiedAt(new \DateTime(\date("Y-m-d H:i:s")));
            $post->setMessage($request->get('message'));
            $em->persist($post);

            $em->flush();

            $sesion = $request->getSession();

            return $this->redirect($this->generateUrl('showTicket', array('id_ticket' => $ticket->getId())));
            }
         
         return $this->render('TicketBundle:Ticket:newTicket.html.twig', array( 'tickets' =>  $this->loadTicket(), 
                                                                                'form' => $form->createView(), ));
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
            
            $ticket->setCreatedBy($user);
            $ticket->setModifiedBy($asesor);
            $ticket->setModifiedAt(new \DateTime(\date("Y-m-d H:i:s")));
            
            $em->persist($ticket);

            $em->flush();

            $sesion = $request->getSession();
            
            return $this->render('TicketBundle:Ticket:listTicket.html.twig', array( 'tickets' => $this->loadTicket(), 
                                                                                    'ticket' => $ticket));
        }
        
        $posts = $em->getRepository('TicketBundle:Post')->findBy(array('ticket' => $ticket->getId()));
        
        $users = $em->getRepository('UserBundle:User')->findAll();
        
        return $this->render('TicketBundle:Ticket:editTicket.html.twig', array( 'tickets' => $this->loadTicket(),
                                                                                'ticket' => $ticket, 
                                                                                'posts' => $posts,
                                                                                'users' => $users,
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
        $em = $this->getDoctrine()->getEntityManager();
        
        //Rellenando la lista de tickets abiertos
        $ticketsFiltered = $em->getRepository('TicketBundle:Ticket')->findTicketFiltered($this->get('security.context'));
        
        return $ticketsFiltered;
    }
    
    private function createPost($request, $id_ticket){
        
        $em = $this->getDoctrine()->getEntityManager();
                
        $post = new Post();
        
        $ticket = $em->getRepository('TicketBundle:Ticket')->find($id_ticket);
        
        $form = $this->createForm(new PostType(), $post);
        
        if ($request->getMethod() == 'POST') {
                
            $form->bindRequest($request);

            //Define User
            $user = $em->getRepository('UserBundle:User')->find($request->get('user'));
            
            //Define Post
            $post->setTicket($ticket);
            
            $post->setCreatedBy($user);
            $post->setCreatedAt(new \DateTime(\date("Y-m-d H:i:s")));
            $post->setModifiedBy($user);
            $post->setModifiedAt(new \DateTime(\date("Y-m-d H:i:s")));
            
            $em->persist($post);

            $em->flush();

            $sesion = $request->getSession();

        }
        
        $posts = $em->getRepository('TicketBundle:Post')->findBy(array('ticket' => $id_ticket));
        
        $array = array('form' => $form->createView(),
                       'tickets'    =>  $this->loadTicket(),
                       'ticket' => $ticket,
                       'id_ticket' => $id_ticket,
                       'posts' => $posts, 
                      );
        return $array;
    }
}