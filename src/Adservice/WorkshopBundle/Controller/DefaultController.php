<?php

namespace Adservice\WorkshopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Adservice\WorkshopBundle\Form\WorkshopType;
use Adservice\WorkshopBundle\Entity\Workshop;
use Adservice\WorkshopBundle\Entity\Typology;

class DefaultController extends Controller {

    /**
     * Devuelve todos los talleres segun el usuario logeado
     * @return type
     * @throws AccessDeniedException
     */
    public function listAction() {
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false) {
            throw new AccessDeniedException();
        }
        
        $logged_user = $this->get('security.context')->getToken()->getUser();
        
        $em = $this->getDoctrine()->getEntityManager();
        $workshops = $em->getRepository("WorkshopBundle:Workshop")->findByPartner($logged_user->getPartner()->getId());

        return $this->render('WorkshopBundle:Default:list.html.twig', array('workshops' => $workshops));
    }

    public function newWorkshopAction() {
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false)
            throw new AccessDeniedException();
        
        $logged_user = $this->get('security.context')->getToken()->getUser();
        $workshop  = new Workshop();
        $request = $this->getRequest();
        $form = $this->createForm(new WorkshopType(), $workshop);
        $form->bindRequest($request);
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $workshop->setPartner($logged_user->getPartner());
            $workshop->setCreatedAt(new \DateTime(\date("Y-m-d H:i:s")));
            $this->saveWorkshop($em, $workshop);
            
            return $this->redirect($this->generateUrl('workshop_list'));
        }
        
        return $this->render('WorkshopBundle:Default:newWorkshop.html.twig', array('workshop'   => $workshop,
                                                                                   'form_name'  => $form->getName(),
                                                                                   'form'       => $form->createView()));
    }

    /**
     * Obtener los datos del workshop a partir de us ID para poder editarlo (solo lo puede hacer el ROLE_ADMIN)
     * Si la petición es GET  --> mostrar el formulario
     * Si la petición es POST --> save del formulario
     */
    public function editWorkshopAction($id) {
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false){
            throw new AccessDeniedException();
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        $workshop = $em->getRepository("WorkshopBundle:Workshop")->find($id);
        
        if (!$workshop) throw $this->createNotFoundException('Workshop no encontrado en la BBDD');

        $petition = $this->getRequest();
        $form = $this->createForm(new WorkshopType(), $workshop);
        
        if ($petition->getMethod() == 'POST') {
            $form->bindRequest($petition);
            if ($form->isValid()) $this->saveWorkshop($em, $workshop);
            return $this->redirect($this->generateUrl('workshop_list'));
        }

        return $this->render('WorkshopBundle:Default:editWorkshop.html.twig', array('workshop'   => $workshop,
                                                                                    'form_name'  => $form->getName(),
                                                                                    'form'       => $form->createView()));
    }

    public function deleteWorkshopAction($id) {
                
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false){
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getEntityManager();
        $workshop = $em->getRepository("WorkshopBundle:Workshop")->find($id);
        if (!$workshop) throw $this->createNotFoundException('Workshop no encontrado en la BBDD');
        
        $em->remove($workshop);
        $em->flush();
        
        return $this->redirect($this->generateUrl('workshop_list'));
    }
    
    /**
     * Hace el save de un workshop
     * @param EntityManager $em
     * @param Workshop $workshop
     */
    private function saveWorkshop($em, $workshop){
        $workshop->setModifiedAt(new \DateTime(\date("Y-m-d H:i:s")));
        $workshop->setModifyBy($this->get('security.context')->getToken()->getUser());
        $em->persist($workshop);
        $em->flush();
    }

}
