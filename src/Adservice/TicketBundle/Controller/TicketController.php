<?php

namespace Adservice\TicketBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Adservice\TicketBundle\Entity\Ticket;
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
            $ticket->setUser($user);
            $ticket->setDateCreated(new \DateTime(\date("Y-m-d")));
            $ticket->setUserModified($user);
            $ticket->setDateModified(new \DateTime(\date("Y-m-d")));
            $ticket->setStatus($status);
            $em->persist($ticket);

            //campos de POST
            $message= new Post();
            $message->setTicket($ticket);
            $message->setUser($user);
            $message->setMessage($request->get('message'));
            $em->persist($message);

            $em->flush();

            $sesion = $request->getSession();

            return $this->redirect($this->generateUrl('newPost', array('id_ticket' => $ticket->getId())));
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
            
            $em->persist($ticket);

            $em->flush();

            $sesion = $request->getSession();
            
            return $this->render('TicketBundle:Ticket:listTicket.html.twig', array( loadTicket(), 
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
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        
        $post = new Post();
        
        $form = $this->createForm(new PostType(), $post);
        
        if ($request->getMethod() == 'POST') {
                
            $form->bindRequest($request);

            //campos de POST
            $post->setTicket($em->getRepository('TicketBundle:Ticket')->find($id_ticket));
            
            $user = $em->getRepository('UserBundle:User')->find($request->get('user'));
            
            $post->setUser($user);
            
            $em->persist($post);

            $em->flush();

            $sesion = $request->getSession();

        }
        
        $ticket = $em->getRepository('TicketBundle:Ticket')->find($id_ticket);

        $messages = $em->getRepository('TicketBundle:Post')->findBy(array('ticket' => $id_ticket));

        $title = $messages[0]->getTicket()->getTitle();

        return $this->render('TicketBundle:Ticket:showTicket.html.twig', array('tickets'    =>  $this->loadTicket(), 
                                                                          'ticket'    => $ticket, 
                                                                          'id_ticket' => $id_ticket,
                                                                          'title'      => $title, 
                                                                          'messages'   => $messages, 
                                                                          'form' => $form->createView(),
                                                                        ));
     }    
     
    public function listTicketAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        
        $ticket = new Ticket();
        
        if ($request->getMethod() == 'POST') {
            if($id_ticket == $request->get('id_ticket')){
                $ticket = $em->getRepository('TicketBundle:Ticket')->find($id_ticket);
            }
        }   
            
        return $this->render('TicketBundle:Ticket:listTicket.html.twig', array('ticket' => $ticket, 
                                                                               'tickets' => $this->loadTicket(), ));
    }
    
    public function loadTicket()
    {
        $em = $this->getDoctrine()->getEntityManager();
        //Rellenando la lista de tickets abiertos
        $open = $em->getRepository('TicketBundle:Status')->find(0);
       
        $tickets = $em->getRepository('TicketBundle:Ticket')->findBy(array('status' => $open));
        
        return $tickets;
    }
}
