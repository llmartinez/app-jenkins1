<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\DiagnosisMachine;
use AppBundle\Form\DiagnosisMachineType;

class DiagnosisMachineController extends Controller
{
    public function diagnosisMachinesAction($page=1)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository("AppBundle:DiagnosisMachine")
                    ->createQueryBuilder("d")
                    ->select("d")
                    ->where("d.id != 0")
                    ->orderBy("d.name", "ASC");

        $pagination = $this->get('knp_paginator')->paginate($query->getQuery(), $page, 10);

        return $this->render('w_diagnosisMachine/diagnosisMachines.html.twig', array('pagination' => $pagination));
    }

    /*
     * @ParamConverter("diagnosisMachine", class="AppBundle:DiagnosisMachine")
     */
    public function diagnosisMachineAction(Request $request, DiagnosisMachine $diagnosisMachine=null)
    {
        if($diagnosisMachine == null) $diagnosisMachine = new DiagnosisMachine();
        $tokenUser = $this->get('security.token_storage')->getToken()->getUser();

        if($tokenUser->getService() != null)
             $tokenService = $tokenUser->getService();
        else $tokenService = '0';

        $form = $this->createForm(new DiagnosisMachineType(), $diagnosisMachine, array('attr' => array('ids' => $tokenUser->getService())));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($diagnosisMachine);
            $em->flush();

            // Show confirmation
            $this->get('session')->getFlashBag()->add('ok' , $this->get('translator')->trans('diagnosisMachine_update'));

            return $this->redirect($this->generateUrl('diagnosisMachines'));
        }

        return $this->render('w_diagnosisMachine/diagnosisMachine.html.twig', array('_locale' => $this->get('locale'), 'form' => $form->createView()));
    }

    /*
     * @ParamConverter("diagnosisMachine", class="AppBundle:DiagnosisMachine")
     */
    public function diagnosisMachineDeleteAction(DiagnosisMachine $diagnosisMachine)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($diagnosisMachine);
        $em->flush();

        // Show confirmation
        $this->get('session')->getFlashBag()->add('ok' , $this->get('translator')->trans('diagnosisMachine_deleted'));

        return $this->redirect($this->generateUrl('diagnosisMachines'));
    }
}