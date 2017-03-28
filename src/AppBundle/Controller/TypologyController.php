<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Typology;
use AppBundle\Form\TypologyType;

class TypologyController extends Controller
{
    public function typologiesAction($page=1)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository("AppBundle:Typology")
                    ->createQueryBuilder("t")
                    ->select("t")
                    ->where("t.id != 0")
                    ->orderBy("t.name", "ASC");

        $pagination = $this->get('knp_paginator')->paginate($query->getQuery(), $page, 10);

        return $this->render('w_typology/typologies.html.twig', array('pagination' => $pagination));
    }

    /*
     * @ParamConverter("typology", class="AppBundle:Typology")
     */
    public function typologyAction(Request $request, Typology $typology=null)
    {
        if($typology == null) $typology = new Typology();
        $tokenUser = $this->get('security.token_storage')->getToken()->getUser();

        if($tokenUser->getService() != null)
             $tokenService = $tokenUser->getService();
        else $tokenService = '0';

        $form = $this->createForm(new TypologyType(), $typology, array('attr' => array('ids' => $tokenUser->getService())));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($typology);
            $em->flush();

            // Show confirmation
            $this->get('session')->getFlashBag()->add('ok' , $this->get('translator')->trans('typology_update'));

            return $this->redirect($this->generateUrl('typologies'));
        }

        return $this->render('w_typology/typology.html.twig', array('_locale' => $this->get('locale'), 'form' => $form->createView()));
    }

    /*
     * @ParamConverter("typology", class="AppBundle:Typology")
     */
    public function typologyDeleteAction(Typology $typology)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($typology);
        $em->flush();

        // Show confirmation
        $this->get('session')->getFlashBag()->add('ok' , $this->get('translator')->trans('typology_deleted'));

        return $this->redirect($this->generateUrl('typologies'));
    }
}