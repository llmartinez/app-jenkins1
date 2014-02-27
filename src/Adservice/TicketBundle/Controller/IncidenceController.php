<?php

namespace Adservice\TicketBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Adservice\TicketBundle\Entity\Incidence;
use Adservice\TicketBundle\Entity\IncidenceRepository;
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
        $incidence = new Incidence();
        $ticket = $em->getRepository('TicketBundle:Ticket')->find($id_ticket);

        $inc_setted = $em->getRepository('TicketBundle:Incidence')->findOneBy( array('ticket' => $ticket->getId()));

        if(isset($inc_setted)) return $this->redirect($this->generateUrl('editIncidence', array('id_incidence' => $inc_setted->getId())));

        $incidence->setTicket($ticket);
        $form = $this->createForm(new IncidenceType(), $incidence);

        if ($request->getMethod() == 'POST') {

            $form->bindRequest($request);

            echo $incidence->getStatus();
            die;

            //campos comunes
            $security = $this->get('security.context');
            $user = $security->getToken()->getUser();

            $ticket->setStatus($incidence->getStatus());
            DefaultC::saveEntity($em, $ticket, $user, false);

            $incidence = DefaultC::newEntity($incidence, $user);
            DefaultC::saveEntity($em, $incidence, $user);

            $sesion = $request->getSession();

            return $this->redirect($this->generateUrl('listIncidence', array('incidence' => $incidence)));
        }

        return $this->render('TicketBundle:Incidence:createIncidence.html.twig', array('ticket' => $ticket,
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

        $incidence = $em->getRepository('TicketBundle:Incidence')->find($id_incidence);

        return $this->render('TicketBundle:Incidence:showIncidence.html.twig', array('incidence' => $incidence, ));
    }

    /**
     * Devuelve todas las incidencias realizadas
     * @return url
     */
    public function listIncidenceAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $security = $this->get('security.context');
        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {

            $incidences = $em->getRepository('TicketBundle:Incidence')->findIncidencesFiltered($security, $request);
        }else{
            $incidences = $em->getRepository('TicketBundle:Incidence')->findAll();
        }

        if ($security->isGranted("ROLE_ASSESSOR")){
            $partners  = $em->getRepository('PartnerBundle:Partner')->findAll();
        }else{
            $partners  = $em->getRepository('PartnerBundle:Partner')->findByWorkshop($security->getToken()->getUser()->getWorkshop()->getId());
        }
        $regions   = $em->getRepository('UtilBundle:Region')->findAll();

        return $this->render('TicketBundle:Incidence:listIncidence.html.twig', array('request'    => $request,
                                                                                     'incidences' => $incidences,
                                                                                     'partners'   => $partners,
                                                                                     'regions'    => $regions,
                                                                                    ));
    }

    /**
     * Funcion Ajax que devuelve un listado de incidencias filtradas a partir de una opcion de un combo ($option)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function fill_incidencesAction() {

        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();

        $option = $petition->request->get('option');


        if($option == 'all'     ) $incidences = $em->getRepository('TicketBundle:Incidence')->findAll();
        if($option == 'owner'   ) $incidences = $em->getRepository('TicketBundle:Incidence')->findBy(array('owner'       => $this->get('security.context')->getToken()->getUser()->getId()));
        if($option == 'workshop') $incidences = $em->getRepository('TicketBundle:Incidence')->findBy(array('workshop'    => $this->get('security.context')->getToken()->getUser()->getWorkshop()->getId()));
        
        foreach ($incidences as $incidence) {
            $json[] = $incidence->to_json();
        }
        return new Response(json_encode($json), $status = 200);
    }

    /**
     * Obtiene el listado de todas las incidencias de un workshop
     * @param type $id_workshop
     */
    public function getIncidencesFromWorkshopAction($id_workshop){
        $em = $this->getDoctrine()->getEntityManager();
        $workshop = $em->getRepository('WorkshopBundle:Workshop')->find($id_workshop);

        return $this->render('TicketBundle:Incidence:incidencesFromWorkshop.html.twig', array('workshop' => $workshop));
    }
}
