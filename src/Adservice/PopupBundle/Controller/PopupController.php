<?php

namespace Adservice\PopupBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Adservice\PopupBundle\Entity\Popup;
use Adservice\PopupBundle\Entity\PopupRepository;
use Adservice\PopupBundle\Form\PopupType;
use Adservice\UtilBundle\Entity\Pagination;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PopupController extends Controller {

    /**
     * Busca en la BBDD si en la fecha de la peticion hay algun popup activo para mostrar
     * es una llamada AJAX
     */
    public function getPopupAction(Request $request) {

        $em = $this->getDoctrine()->getManager();
        $date_today = new \DateTime(\date("Y-m-d H:i:s"));
        $user = $this->getUser();
        $popups = $em->getRepository('PopupBundle:Popup')->findPopupByDate($date_today, $user);

        $json = array();
        $popup = $popups[0];
        $popup_id = $popup->getId();

        if (!isset($_SESSION['popup_id']) or $_SESSION['popup_id'] != $popup_id)
        {
            $json[] = $popup->to_json();
            $_SESSION['popup_id'] = $popup_id;
        }

        return new Response(json_encode($json), $status = 200);
    }

    public function popupListAction(Request $request, $page=1 , $category_service='none' , $country='none') {

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getManager();

        if($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            if($country != 'none' OR $category_service != 'none')
            {
                if ($country          != 'none') $params[] = array('country', ' = '.$country);
                if ($category_service != 'none') $params[] = array('category_service', ' = '.$category_service);
            }
            else $params[] = array();
        }
        else {
            $id_superadmin = $em->getRepository('UserBundle:Role')->findOneByName('ROLE_SUPER_ADMIN')->getId();
            $params[] = array('category_service', ' = '.$this->getUser()->getCategoryService()->getId());
            $params[] = array('role', ' != '.$id_superadmin);
        }

        $pagination = new Pagination($page);

        $popups = $pagination->getRows($em, 'PopupBundle', 'Popup', $params, $pagination);

        $length = $pagination->getRowsLength($em, 'PopupBundle', 'Popup', $params);

        $pagination->setTotalPagByLength($length);

        if($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) $catservices = $em->getRepository('UserBundle:CategoryService')->findAll();
        else $catservices = array();
        $countries = $em->getRepository('UtilBundle:Country')->findAll();

        return $this->render('PopupBundle:Popup:list_popups.html.twig', array(  'all_popups'   => $popups,
                                                                                'pagination'   => $pagination,
                                                                                'countries'    => $countries,
                                                                                'country'      => $country,
                                                                                'catservices'  => $catservices,
                                                                                'category_service' => $category_service,
                                                                                ));
    }

    public function newPopupAction(Request $request){

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') === false){
            throw new AccessDeniedException();
        }
        $popup = new Popup();


        // Creamos variables de sesion para fitlrar los resultados del formulario
        $role = $this->getUser()->getRoles();
        $role = $role[0]->getName();
        $_SESSION['role'] = ' = '.$role;
        if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {

            //$_SESSION['id_country'] = ' != 0 ';
            $_SESSION['id_catserv'] = ' != 0 ';

        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_AD')) {

            //$_SESSION['id_country'] = ' = '.$this->getCountry()->getId();
            $_SESSION['id_catserv'] = ' = '.$this->getUser()->getCategoryService()->getId();

        }else {
            //$_SESSION['id_country'] = ' = '.$partner->getCountry()->getId();
            $_SESSION['id_catserv'] = ' = '.$this->getUser()->getCategoryService()->getId();
        }
        $form = $this->createForm(new PopupType(), $popup);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $popup->setCreatedAt(new \DateTime(\date("Y-m-d H:i:s")));
            $popup->setCreatedBy($this->getUser());
            $this->savePopup($em, $popup);

            return $this->redirect($this->generateUrl('popup_list'));
        }
        return $this->render('PopupBundle:Popup:new_popup.html.twig', array('popup'      => $popup,
                                                                            'form_name'  => $form->getName(),
                                                                            'form'       => $form->createView()));
    }

    /**
     * Obtener los datos del popup a partir de us ID para poder editarlo (solo lo puede hacer el ROLE_ADMIN)
     * Si la petición es GET  --> mostrar el formulario
     * Si la petición es POST --> save del formulario
     * @Route("/edit/{id}")
     * @ParamConverter("popup", class="PopupBundle:Popup")
     */
    public function editPopupAction(Request $request, $popup){

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') === false){
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        // Creamos variables de sesion para fitlrar los resultados del formulario
        $role = $this->getUser()->getRoles();
        $role = $role[0]->getName();
        $_SESSION['role'] = $role;
        if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {

            //$_SESSION['id_country'] = ' != 0 ';
            $_SESSION['id_catserv'] = ' != 0 ';

        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_AD')) {

            //$_SESSION['id_country'] = ' = '.$this->getUser()->getCountry()->getId();
            $_SESSION['id_catserv'] = ' = '.$this->getUser()->getCategoryService()->getId();

        }else {
            //$_SESSION['id_country'] = ' = '.$partner->getCountry()->getId();
            $_SESSION['id_catserv'] = ' = '.$this->getUser()->getCategoryService()->getId();
        }
        $form = $this->createForm(new PopupType(), $popup);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->savePopup($em, $popup); }
            return $this->redirect($this->generateUrl('popup_list'));
        }

        return $this->render('PopupBundle:Popup:edit_popup.html.twig', array('popup'     => $popup,
                                                                            'form_name'  => $form->getName(),
                                                                            'form'       => $form->createView()));
    }

    /**
     * Elimina el popup con $id de la bbdd
     * @Route("/delete/{id}")
     * @ParamConverter("popup", class="PopupBundle:Popup")
     * @throws AccessDeniedException
     * @throws CreateNotFoundException
     */
    public function deletePopupAction(Request $request, $popup){

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') === false){
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager();
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
        $popup->setModifiedBy($this->getUser());
        $em->persist($popup);
        $em->flush();
    }

}
