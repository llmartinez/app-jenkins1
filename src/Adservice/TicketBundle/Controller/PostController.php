<?php

namespace Adservice\TicketBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Adservice\TicketBundle\Entity\Post;
use Adservice\TicketBundle\Form\PostType;

class PostController extends Controller{

    public function newPostAction($id_ticket) {
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
        //Rellenando la lista de mensajes
        $tickets = $em->getRepository('TicketBundle:Ticket')->findAll();

        $messages = $em->getRepository('TicketBundle:Post')->findBy(array('ticket' => $id_ticket));

        $title = $messages[0]->getTicket()->getTitle();

        return $this->render('TicketBundle:Post:newPost.html.twig', array('tickets'    => $tickets, 
                                                                          'id_ticket' => $id_ticket,
                                                                          'title'      => $title, 
                                                                          'messages'   => $messages, 
                                                                          'form' => $form->createView(),
                                                                        ));
     }
}