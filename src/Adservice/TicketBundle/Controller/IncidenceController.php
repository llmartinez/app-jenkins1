<?php

namespace Adservice\TicketBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Adservice\TicketBundle\Entity\Incidence;
use Adservice\TicketBundle\Form\IncidenceType;
use Adservice\TicketBundle\Entity\Ticket;
use Adservice\TicketBundle\Entity\Post;

class IncidenceController extends Controller{

  
    public function newIncidenceAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        
        $users = $em->getRepository('UserBundle:User')->findAll();
        //$users = $em->getRepository('UserBundle:User')->findBy(array('user_role' => 'ROLE_ADMIN'));
        
        $incidence = new Incidence();
        
        $form = $this->createForm(new IncidenceType(), $incidence);
        
        if ($request->getMethod() == 'POST') {
              
            $form->bindRequest($request);
            
            $user = $em->getRepository('UserBundle:User')->find($request->get('user'));
            $asesor =  $em->getRepository('UserBundle:User')->find($request->get('asesor'));
            
            $ticket = new Ticket();
            $ticket->setTitle($request->get('title'));
            $ticket->setStatus($incidence->getStatus());
            $ticket->setImportance($incidence->getImportance());
            $ticket = saveEntity($ticket, $user, false);
            
            $post = new Post();
            $post->setTicket($ticket);
            $post->setMessage($incidence->getDescription());
            $post = saveEntity($post,$user, false);
            
            $post2 = new Post();
            $post2->setTicket($ticket);
            $post2->setMessage($incidence->getSolution());
            $post2 = saveEntity($post2,$asesor, false);
                        
            $incidence->setTicket($ticket);
            $incidence = newEntity($incidence,$asesor);

            $sesion = $request->getSession();
            
            $incidences = $em->getRepository('TicketBundle:Incidence')->findAll();
            
            return $this->render('TicketBundle:Incidence:listIncidence.html.twig', array('incidences' => $incidences, ));
        }        
        
        $incidences = $em->getRepository('TicketBundle:Incidence')->findAll();
        
        return $this->render('TicketBundle:Incidence:newIncidence.html.twig', array('incidences' => $incidences, 
                                                                                    'form' => $form->createView(),
                                                                                    'users' => $users,
                                                                                 ));
    }
    
    public function createIncidenceAction($id_ticket)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        
        $incidences = $em->getRepository('TicketBundle:Incidence')->findAll();

        $incidence = new Incidence();
        
        $ticket = $em->getRepository('TicketBundle:Ticket')->find($id_ticket);
        
        $inc = $em->getRepository('TicketBundle:Incidence')->findOneBy( array('ticket' => $ticket->getId()));

        if(isset($inc)){
           
            return $this->redirect($this->generateUrl('editIncidence', array('id_incidence' => $inc->getId())));
        }
        
        $incidence->setTicket($ticket);
        
        $form = $this->createForm(new IncidenceType(), $incidence);
        
        if ($request->getMethod() == 'POST') {
              
            $form->bindRequest($request);
            
            $asesor =  $em->getRepository('UserBundle:User')->find($request->get('asesor'));
            
            $ticket->setStatus($em->getRepository('TicketBundle:Status')->findOneBy( array('status' => 'Cerrado')));
            $ticket = saveEntity($ticket,$asesor, false);
             
            $incidence = saveEntity($incidence,$asesor);

            $sesion = $request->getSession();
            
            return $this->redirect($this->generateUrl('listIncidence', array('incidences' => $incidences, 
                                                                             'incidence' => $incidence)));
        }
        
        $posts = $em->getRepository('TicketBundle:Post')->findBy(array('ticket' => $id_ticket));
        
        return $this->render('TicketBundle:Incidence:createIncidence.html.twig', array('incidences' => $incidences, 
                                                                                       'ticket' => $ticket, 
                                                                                       'posts' => $posts,
                                                                                       'form' => $form->createView(),
                                                                              ));
    }
    
     public function editIncidenceAction($id_incidence)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
       
        $incidences = $em->getRepository('TicketBundle:Incidence')->findAll();

        $incidence = $em->getRepository('TicketBundle:Incidence')->find($id_incidence);

        $form = $this->createForm(new IncidenceType(), $incidence);
        
        if ($request->getMethod() == 'POST') {
              
            $form->bindRequest($request);
            
            $asesor =  $em->getRepository('UserBundle:User')->find($request->get('asesor'));
            
            $incidence = saveEntity($incidence,$asesor);
            
            $em->persist($incidence);

            $em->flush();

            $sesion = $request->getSession();
            
            return $this->render('TicketBundle:Incidence:listIncidence.html.twig', array('incidences' => $incidences, 
                                                                                         'incidence' => $incidence));
        }
        
        $ticket = $em->getRepository('TicketBundle:Ticket')->find($incidence->getTicket()->getId());
        
        $posts = $em->getRepository('TicketBundle:Post')->findBy(array('ticket' => $ticket->getId()));
        
        $users = $em->getRepository('UserBundle:User')->findAll();
        
        return $this->render('TicketBundle:Incidence:editIncidence.html.twig', array('incidences' => $incidences, 
                                                                                       'incidence' => $incidence, 
                                                                                       'ticket' => $ticket, 
                                                                                       'posts' => $posts,
                                                                                       'users' => $users,
                                                                                       'form' => $form->createView(),
                                                                              ));
    }
    
    public function showIncidenceAction($id_incidence)
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $incidences = $em->getRepository('TicketBundle:Incidence')->findAll();
            
        $incidence = $em->getRepository('TicketBundle:Incidence')->find($id_incidence);
        
        $posts = $em->getRepository('TicketBundle:Post')->findBy(array('ticket' => $incidence->getTicket()->getId()));
            
        return $this->render('TicketBundle:Incidence:showIncidence.html.twig', array('incidences' => $incidences, 
                                                                                     'incidence' => $incidence, 
                                                                                     'posts' => $posts, ));
    }    
    
    public function listIncidenceAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        
        $incidence = new Incidence();
        
        if ($request->getMethod() == 'POST') {
            
            $id_incidence=$request->get('id_incidence');
            
            if($id_incidence){
                $incidence = $em->getRepository('TicketBundle:Incidence')->find($id_incidence);
            }
        }   
        $incidences = $em->getRepository('TicketBundle:Incidence')->findAll();
            
        return $this->render('TicketBundle:Incidence:listIncidence.html.twig', array('incidence' => $incidence,
                                                                                     'incidences' => $incidences, ));
    }
    
    public function saveEntity($em, $entity, $auto_flush=true){
        $em->setModifiedBy($entity);
        $em->setModifiedAt(new \DateTime(\date("Y-m-d H:i:s")));
        $em->persist($em);
        if($auto_flush) $em->flush(); 
        return $em;
    }
}