<?php

namespace Adservice\PopupBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Adservice\PopupBundle\Entity\Popup;
use Adservice\PopupBundle\Entity\PopupRepository;
use Adservice\PopupBundle\Form\PopupType;

class DefaultController extends Controller {

    /**
     * Busca en la BBDD si en la fecha de la peticion hay algun popup activo para mostrar
     * es una llamada AJAX
     */
    public function getPopupAction() {
        $date_today = new \DateTime(\date("Y-m-d"));
        $em = $this->getDoctrine()->getEntityManager();
        $popups = $em->getRepository('PopupBundle:Popup')->findPopupByDate($date_today, true);
//        $json = $popup->to_json($popup[0]);
        foreach ($popups as $popup) {
            $json[] = $popup->to_json();
        }
        return new Response(json_encode($json), $status = 200);
    }

    public function popupListAction() {
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();
        $all_popups = $em->getRepository("PopupBundle:Popup")->findAll();

        return $this->render('PopupBundle:Default:list.html.twig', array('all_popups' => $all_popups));
    }
    
    public function newPopupAction(){
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false){
            throw new AccessDeniedException();
        }
        $popup = new Popup();
        $request = $this->getRequest();
        $form = $this->createForm(new PopupType(), $popup);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $popup->setCreatedAt(new \DateTime(\date("Y-m-d H:i:s")));
            $popup->setCreatedBy($this->get('security.context')->getToken()->getUser());
            $this->savePopup($em, $popup);

            return $this->redirect($this->generateUrl('popup_list'));
        }
        return $this->render('PopupBundle:Default:newPopup.html.twig', array('popup'      => $popup,
                                                                             'form_name'  => $form->getName(),
                                                                             'form'       => $form->createView()));
    }
    
    /**
     * Obtener los datos del popup a partir de us ID para poder editarlo (solo lo puede hacer el ROLE_ADMIN)
     * Si la petición es GET  --> mostrar el formulario
     * Si la petición es POST --> save del formulario
     */
    public function editPopupAction($id){
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false){
            throw new AccessDeniedException();
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        $popup = $em->getRepository("PopupBundle:Popup")->find($id);
        
        if (!$popup) throw $this->createNotFoundException('Popup no encontrado en la BBDD');

        $petition = $this->getRequest();
        $form = $this->createForm(new PopupType(), $popup);
        
        if ($petition->getMethod() == 'POST') {
            $form->bindRequest($petition);
            if ($form->isValid()) $this->savePopup($em, $popup);
            return $this->redirect($this->generateUrl('popup_list'));
        }

        return $this->render('PopupBundle:Default:editPopup.html.twig', array('popup'      => $popup,
                                                                              'form_name'  => $form->getName(),
                                                                              'form'       => $form->createView()));
    }
    
    /**
     * Elimina el popup con $id de la bbdd
     * @param Int $id
     * @throws AccessDeniedException
     * @throws CreateNotFoundException
     */
    public function deletePopupAction($id){
        
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false){
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getEntityManager();
        $popup = $em->getRepository("PopupBundle:Popup")->find($id);
        if (!$popup) throw $this->createNotFoundException('Popup no encontrado en la BBDD');
        
        $em->remove($popup);
        $em->flush();
        
        return $this->redirect($this->generateUrl('popup_list'));
    }
    /**
     * Hace el save de un popup
     * @param EntityManager $em
     * @param Popup $popup
     */
    private function savePopup($em, $popup){
        $popup->setModifiedAt(new \DateTime(\date("Y-m-d H:i:s")));
        $popup->setModifyBy($this->get('security.context')->getToken()->getUser());
        $em->persist($popup);
        $em->flush();
    }

}
