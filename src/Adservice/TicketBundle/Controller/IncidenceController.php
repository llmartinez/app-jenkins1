<?php

namespace Adservice\TicketBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Adservice\TicketBundle\Entity\Incidence;
use Adservice\TicketBundle\Form\IncidenceType;

class IncidenceController extends Controller{

    public function newIncidenceAction($id_ticket)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        
        $incidence = new Incidence();
        
        $ticket = $em->getRepository('TicketBundle:Ticket')->find($id_ticket);

        $incidence->setTicket($ticket);
        
        $form = $this->createForm(new IncidenceType(), $incidence);
        
        if ($request->getMethod() == 'POST') {
              
            $form->bindRequest($request);
            
            $em->persist($incidence);

            $em->flush();

            $sesion = $request->getSession();
            
            $incidences = $em->getRepository('TicketBundle:Incidence')->findAll();
            
            return $this->render('TicketBundle:Incidence:listIncidence.html.twig', array('incidences' => $incidences, 
                                                                                         'incidence' => $incidence));
        }        
        
        $posts = $em->getRepository('TicketBundle:Post')->findBy(array('ticket' => $id_ticket));
        
        return $this->render('TicketBundle:Incidence:newIncidence.html.twig', array('ticket' => $ticket, 
                                                                                 'posts' => $posts,
                                                                                 'form' => $form->createView(),
                                                                              ));
    }
     
    public function listIncidenceAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $incidences = $em->getRepository('TicketBundle:Incidence')->findAll();
            
        return $this->render('TicketBundle:Incidence:listIncidence.html.twig', array('incidences' => $incidences, 
                                                                                         ));
    }
}