<?php

namespace Adservice\PartnerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Adservice\PartnerBundle\Form\PartnerType;
use Adservice\PartnerBundle\Entity\Partner;
use Adservice\UtilBundle\Entity\Region;
use Adservice\UtilBundle\Entity\Province;

class DefaultController extends Controller {

    public function listAction() {

        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false) {
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getEntityManager();
        $all_partners = $em->getRepository("PartnerBundle:Partner")->findAll();


        return $this->render('PartnerBundle:Default:list.html.twig', array('all_partners' => $all_partners));
    }

    public function newPartnerAction() {
        
        $partner = new Partner();
        $request = $this->getRequest();
        $form = $this->createForm(new PartnerType(), $partner);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $partner->setCreatedAt(new \DateTime(\date("Y-m-d H:i:s")));
            $this->savePartner($em, $partner);

            return $this->redirect($this->generateUrl('partner_list'));
        }
        return $this->render('PartnerBundle:Default:newPartner.html.twig', array('partner'    => $partner,
                                                                                 'form_name'  => $form->getName(),
                                                                                 'form'       => $form->createView()));
    }
    
     /**
     * Obtener los datos del partner a partir de us ID para poder editarlo (solo lo puede hacer el ROLE_ADMIN)
     * Si la petición es GET  --> mostrar el formulario
     * Si la petición es POST --> save del formulario
     */
    public function editPartnerAction($id){
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false){
            throw new AccessDeniedException();
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        $partner = $em->getRepository("PartnerBundle:Partner")->find($id);
        
        if (!$partner) throw $this->createNotFoundException('Partner no encontrado en la BBDD');

        $petition = $this->getRequest();
        $form = $this->createForm(new PartnerType(), $partner);
        
        if ($petition->getMethod() == 'POST') {
            $form->bindRequest($petition);
            if ($form->isValid()) $this->savePartner($em, $partner);
            return $this->redirect($this->generateUrl('partner_list'));
        }

        return $this->render('PartnerBundle:Default:editPartner.html.twig', array('partner'    => $partner,
                                                                                  'form_name'  => $form->getName(),
                                                                                  'form'       => $form->createView()));
    }
    
    public function deletePartnerAction($id){
        
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false){
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getEntityManager();
        $partner = $em->getRepository("PartnerBundle:Partner")->find($id);
        if (!$partner) throw $this->createNotFoundException('Partner no encontrado en la BBDD');
        
        $em->remove($partner);
        $em->flush();
        
        return $this->redirect($this->generateUrl('partner_list'));
    }
    
     /**
     * Hace el save de un partner
     * @param EntityManager $em
     * @param Partner $partner
     */
    private function savePartner($em, $partner){
        $partner->setModifiedAt(new \DateTime(\date("Y-m-d H:i:s")));
        $partner->setModifyBy($this->get('security.context')->getToken()->getUser());
        $em->persist($partner);
        $em->flush();
    }

}
