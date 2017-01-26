<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Ticket;
use AppBundle\Form\TicketType;

class TicketController extends Controller
{
    public function ticketsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $tickets = $em->getRepository('AppBundle:Ticket')->findAll();
        $subsystems = $this->get('ticket')->getSubsystems();

        return $this->render('ticket/tickets.html.twig', array( 'tickets' => $tickets,
                                                                'subsystems' => $subsystems));
    }

    public function ticketAction(Request $request)
    {
        $ticket = new Ticket();
        $form = $this->createForm(new TicketType(), $ticket);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            /* config ticket */
            /* ... */
            /*
            $em = $this->getDoctrine()->getManager();
            $em->persist($ticket);
            $em->flush();
            */

            return $this->redirect($this->generateUrl('login'));
        }

        return $this->render(
            'ticket/ticket.html.twig',
            array('_locale' => $this->get('locale'), 'form' => $form->createView()));
    }
}