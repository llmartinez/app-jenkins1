<?php

namespace Adservice\PopupBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Adservice\PopupBundle\Entity\Popup;
use Adservice\PopupBundle\Entity\PopupRepository;
use Adservice\PopupBundle\Form\PopupType;
use Adservice\UtilBundle\Entity\Pagination;

class PopupController extends Controller {

    /**
     * Busca en la BBDD si en la fecha de la peticion hay algun popup activo para mostrar
     * es una llamada AJAX
     */
    public function getPopupAction() {

        $em = $this->getDoctrine()->getEntityManager();
        $date_today = new \DateTime(\date("Y-m-d H:i:s"));
        $user = $this->get('security.context')->getToken()->getUser();
        $popups = $em->getRepository('PopupBundle:Popup')->findPopupByDate($date_today, $user);

        $json = array();
        foreach ($popups as $popup) {
          $json[] = $popup->to_json();
        }
        return new Response(json_encode($json), $status = 200);
    }

    public function popupListAction($page=1 , $country='none') {
        $security = $this->get('security.context');
        if ($security->isGranted('ROLE_ADMIN') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();

        if($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            if ($country != 'none') $params[] = array('country', ' = '.$country);
            else                    $params[] = array();
        }
        else $params[] = array('country', ' = '.$this->get('security.context')->getToken()->getUser()->getCountry()->getId());

        $pagination = new Pagination($page);

        $popups = $pagination->getRows($em, 'PopupBundle', 'Popup', $params, $pagination);

        $length = $pagination->getRowsLength($em, 'PopupBundle', 'Popup', $params);

        $pagination->setTotalPagByLength($length);

        if($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) $countries = $em->getRepository('UtilBundle:Country')->findAll();
        else $countries = array();

        return $this->render('PopupBundle:Popup:list_popups.html.twig', array( 'all_popups' => $popups,
                                                                        'pagination' => $pagination,
                                                                        'countries'  => $countries,
                                                                        'country'    => $country,));
    }

    public function newPopupAction(){
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false){
            throw new AccessDeniedException();
        }
        $popup = new Popup();
        $request = $this->getRequest();
        $form = $this->createForm(new PopupType(), $popup);
        $form->bindRequest($request);

        //La segunda comparacion ($form->getErrors()...) se hizo porque el request que reciber $form puede ser demasiado largo y hace que la funcion isValid() devuelva false
        if ($form->isValid() or $form->getErrors()[0]->getMessageTemplate() == 'The uploaded file was too large. Please try to upload a smaller file') {

            $em = $this->getDoctrine()->getEntityManager();
            $popup->setCreatedAt(new \DateTime(\date("Y-m-d H:i:s")));
            $popup->setCreatedBy($this->get('security.context')->getToken()->getUser());
            $this->savePopup($em, $popup);

            return $this->redirect($this->generateUrl('popup_list'));
        }
        return $this->render('PopupBundle:Popup:new_popup.html.twig', array( 'popup'      => $popup,
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

            //La segunda comparacion ($form->getErrors()...) se hizo porque el request que reciber $form puede ser demasiado largo y hace que la funcion isValid() devuelva false
            if ($form->isValid() or $form->getErrors()[0]->getMessageTemplate() == 'The uploaded file was too large. Please try to upload a smaller file') {
                $this->savePopup($em, $popup); }
            return $this->redirect($this->generateUrl('popup_list'));
        }

        return $this->render('PopupBundle:Popup:edit_popup.html.twig', array('popup'      => $popup,
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
        $popup->setModifiedBy($this->get('security.context')->getToken()->getUser());
        $em->persist($popup);
        $em->flush();
    }

}
