<?php

namespace Adservice\TicketBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Adservice\TicketBundle\Entity\Incidence;
use Adservice\TicketBundle\Form\IncidenceType;
use Adservice\TicketBundle\Entity\Ticket;
use Adservice\TicketBundle\Entity\TicketRepository;
use Adservice\TicketBundle\Form\TicketType;
use Adservice\TicketBundle\Entity\Post;
use Adservice\CarBundle\Entity\Car;
use Adservice\CarBundle\Form\CarType;

use Adservice\TicketBundle\Controller\DefaultController as DefaultC;

class IncidenceController extends Controller{

  /**
   * Crea una incidencia introducida por el asesor con sus respectivos ticket y posts 
   * @return url
   */
    public function newIncidenceAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        
        $incidence = new Incidence();
        $ticket = new Ticket();
        $car = new Car();
        
        //Define Forms
        $form = $this->createForm(new IncidenceType(), $incidence);
        $formT = $this->createForm(new TicketType(), $ticket);
        $formC = $this->createForm(new CarType(), $car);
        
        if ($request->getMethod() == 'POST') {
            //campos comunes
            $security = $this->get('security.context');
            $user = $security->getToken()->getUser();
           
            $form->bindRequest($request);
            $formC->bindRequest($request);
            $formT->bindRequest($request);

            if (($car->getVersion() != "") && ($request->get('workshop') != 0)) {
                
                $workshop = $em->getRepository('WorkshopBundle:Workshop')->find($request->get('workshop'));
                
                //Define CAR
                $car = DefaultC::newEntity($car, $user);
                DefaultC::saveEntity($em, $car, $user, false);
                
                //Define INCIDENCE
                $incidence = DefaultC::newEntity($incidence,$user);
                $incidence->setTicket($ticket);
                $incidence->setWorkshop($workshop);
                DefaultC::saveEntity($em, $incidence, $user, false);
                
                //Define TICKET
                $ticket = DefaultC::newEntity($ticket, $user);
                $ticket->setCar($car);
                $ticket->setWorkshop($incidence->getWorkshop());
                $ticket->setStatus($incidence->getStatus());
                $ticket->setImportance($incidence->getImportance());
                DefaultC::saveEntity($em, $ticket, $user, false);
                        
                $post = DefaultC::newEntity(new Post(),$user);
                $post->setTicket($ticket);
                $post->setMessage($incidence->getDescription());
                DefaultC::saveEntity($em, $post, $user, false);

                $post2 = DefaultC::newEntity(new Post(),$user);
                $post2->setTicket($ticket);
                $post2->setMessage($incidence->getSolution());
                DefaultC::saveEntity($em, $post2, $user, false);
                
                $em->flush();  

                $sesion = $request->getSession();
            
                return $this->redirect($this->generateUrl('showIncidence', array('id_incidence' => $incidence->getId())));
            } else {
                $this->get('session')->setFlash('error', '¡Error! Hay campos sin introducir');
            }
        }
        
        $workshops = $em->getRepository('WorkshopBundle:Workshop')->findAll();
        $brands = $em->getRepository('CarBundle:Brand')->findAll();
        
        return $this->render('TicketBundle:Incidence:newIncidence.html.twig', array('form'      => $form->createView(),
                                                                                    'formT'     => $formT->createView(),
                                                                                    'formC'     => $formC->createView(),
                                                                                    'workshops' => $workshops,
                                                                                    'brands' => $brands,
                                                                                 ));
    }
    
    /**
     * Crea una incidencia a partir de un ticket 
     * @param integer $id_ticket
     * @return url
     */
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
            $status = $em->getRepository('TicketBundle:Status')->find($request->get('status'));
            
            $ticket->setStatus($status);
            DefaultC::saveEntity($em, $ticket, $asesor, false);
            
            $incidence = DefaultC::newEntity($incidence, $asesor);
            DefaultC::saveEntity($em, $incidence, $asesor);

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
    
    /**
     * Edita la incidencia a partir de su id
     * @param integer $id_incidence
     * @return url
     */
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
            
            DefaultC::saveEntity($em, $incidence, $asesor);

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
    
    /**
     * Devuelve la incidencia a partir de su id
     * @param integer $id_incidence
     * @return url
     */
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
    
    /**
     * Devuelve todas las incidencias realizadas
     * @return url
     */
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

    /**/
    public function fill_incidencesAction() {
        
        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();
        
        $option = $petition->request->get('option');
        
        
        if($option == 'all'     ) $incidences = $em->getRepository('TicketBundle:Incidence')->findAll();
        if($option == 'owner'   ) $incidences = $em->getRepository('TicketBundle:Incidence')->findBy(array('owner'       => $this->get('security.context')->getToken()->getUser()->getId()));
        if($option == 'workshop') $incidences = $em->getRepository('TicketBundle:Incidence')->findBy(array('workshop'    => $this->get('security.context')->getToken()->getUser()->getWorkshop()->getId()));
        

        return new Response(json_encode($incidences), $status = 200);
    }
}