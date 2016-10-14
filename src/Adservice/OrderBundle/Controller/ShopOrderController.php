<?php

namespace Adservice\OrderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Adservice\UtilBundle\Controller\UtilController as UtilController;
use Adservice\PartnerBundle\Entity\Shop;
use Adservice\OrderBundle\Entity\ShopOrder;
use Adservice\OrderBundle\Form\ShopOrderType;
use Adservice\OrderBundle\Form\ShopNewOrderType;
use Adservice\OrderBundle\Form\ShopEditOrderType;
use Adservice\OrderBundle\Form\ShopRejectOrderType;
use Adservice\UserBundle\Entity\User;
use Adservice\UtilBundle\Entity\Mailer;
use Adservice\UtilBundle\Entity\Pagination;

class ShopOrderController extends Controller {

//  _     ___ ____ _____
// | |   |_ _/ ___|_   _|
// | |    | |\___ \ | |
// | |___ | | ___) || |
// |_____|___|____/ |_|
//
    /**
     * Listado de todas las tiendas de la bbdd
     * @throws AccessDeniedException
     */
    public function listShopsAction($page=1, $partner='none') {

        $security = $this->get('security.context');
        if ($security->isGranted('ROLE_AD') === false) {
            throw new AccessDeniedException();
        }
        $em   = $this->getDoctrine()->getEntityManager();
        $user = $security->getToken()->getUser();

        $params[] = array("name", " != '...' "); //Evita listar las tiendas por defecto de los socios (Tiendas con nombre '...')

        if($security->isGranted('ROLE_SUPER_AD')) {
            $country = $security->getToken()->getUser()->getCountry();
            $params[] = array('country', ' = '.$country->getId());
            if ($partner != 'none') $params[] = array('partner', ' = '.$partner);
        }
        else $params[] = array('partner', ' = '.$security->getToken()->getUser()->getPartner()->getId());

        //$params[] = array('country', ' = '.$security->getToken()->getUser()->getCountry()->getId());

        if($user->getCategoryService() != null) {
            $params[] = array('category_service', ' = '.$user->getCategoryService()->getId());
        }

        $pagination = new Pagination($page);

        $shops  = $pagination->getRows($em, 'PartnerBundle', 'Shop', $params, $pagination);

        $length = $pagination->getRowsLength($em, 'PartnerBundle', 'Shop', $params);

        $pagination->setTotalPagByLength($length);

        if($security->isGranted('ROLE_SUPER_AD') and $user->getCategoryService() != null)
        {
            $consulta = $em->createQuery("SELECT p FROM PartnerBundle:Partner p JOIN p.users u
                                          WHERE p.country = '".$user->getCountry()->getId()."'
                                          AND u.category_service = ".$user->getCategoryService()->getId()."
                                          ORDER BY p.name ASC");

            $partners = $consulta->getResult();
        }
        elseif($security->isGranted('ROLE_AD')) {
            $country = $user->getCountry();
            $partners = $em->getRepository('PartnerBundle:Partner')->findBy(array('country'=>$country),array('name'=>'ASC'));
        }
        else {
            $partners = array();
        }

        return $this->render('OrderBundle:ShopOrders:list_shops.html.twig', array( 'shops'      => $shops,
                                                                                   'pagination' => $pagination,
                                                                                   'partners'   => $partners,
                                                                                   'partner'    => $partner,));
    }

//  _   _ _______        __
// | \ | | ____\ \      / /
// |  \| |  _|  \ \ /\ / /
// | |\  | |___  \ V  V /
// |_| \_|_____|  \_/\_/
//

    /**
     * Crea una solicitud (shopOrder) del tipo "create", por defecto la tienda que se creara estara inactiva...
     * @return type
     * @throws AccessDeniedException.
     */
    public function newAction(){
        $request = $this->getRequest();
        $security = $this->get('security.context');
        $user = $security->getToken()->getUser();
        if ($security->isGranted('ROLE_AD') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();

        $shopOrder = new ShopOrder();
        if ($security->isGranted('ROLE_SUPER_AD')) {
            $id_partner = '0';
            $params_partners['active'] = '1';

            if($user->getCategoryService() != null) $params_partners['category_service'] = $user->getCategoryService()->getId();
            else                                    $params_partners['country'] = $user->getCountry()->getId();

            $partners   = $em->getRepository("PartnerBundle:Partner")->findBy($params_partners);
        }
        else { $id_partner = $user->getPartner()->getId();
               $partners   = '0';
        }
        $partner = $em->getRepository("PartnerBundle:Partner")->find($id_partner);

        // Creamos variables de sesion para fitlrar los resultados del formulario
        if ($security->isGranted('ROLE_SUPER_AD')) {

            $partner_ids = '0';
            foreach ($partners as $p) { $partner_ids = $partner_ids.', '.$p->getId(); }
            $_SESSION['id_partner'] = ' IN ('.$partner_ids.')';
            $_SESSION['id_country'] = ' = '.$user->getCountry()->getId();
        }else {
            $_SESSION['id_partner'] = ' = '.$partner->getId();
            $_SESSION['id_country'] = ' = '.$partner->getCountry()->getId();
        }
        if($user->getCategoryService() != null)
        {
            $_SESSION['id_catserv'] = ' = '.$user->getCategoryService()->getId();
            unset($_SESSION['id_country']);
        }

        $form = $this->createForm(new ShopNewOrderType(), $shopOrder);

        if ($request->getMethod() == 'POST') {

            $form->bindRequest($request);

            if ($form->isValid()) {
                $partner_id = $request->request->get('shopOrder_newOrder')['partner'];
                $partner = $em->getRepository("PartnerBundle:Partner")->find($partner_id);

                $shopOrder = UtilController::newEntity($shopOrder, $user);
                if ($security->isGranted('ROLE_AD_COUNTRY') === false)

                $shopOrder->setPartner($partner);
                $shopOrder->setCategoryService($user->getCategoryService());
                $shopOrder->setActive(false);
                $shopOrder->setAction('create');
                $shopOrder->setWantedAction('create');
                UtilController::saveEntity($em, $shopOrder, $user);

                $mail = $shopOrder->getCreatedBy()->getEmail1();
                $pos = strpos($mail, '@');
                if ($pos != 0) {

                    // Cambiamos el locale para enviar el mail en el idioma del taller
                    $locale = $request->getLocale();
                    $lang_p = $shopOrder->getPartner()->getCountry()->getLang();
                    $lang   = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_p);
                    $request->setLocale($lang->getShortName());

                    /* MAILING */
                    $mailer = $this->get('cms.mailer');
                    $mailer->setTo($mail);
                    $mailer->setSubject($this->get('translator')->trans('mail.newOrder.subject').$shopOrder->getId());
                    $mailer->setFrom('noreply@adserviceticketing.com');
                    $mailer->setBody($this->renderView('UtilBundle:Mailing:order_new_shop_mail.html.twig', array('shopOrder' => $shopOrder, '__locale' => $locale)));
                    $mailer->sendMailToSpool();
                    //echo $this->renderView('UtilBundle:Mailing:order_new_shop_mail.html.twig', array('shopOrder' => $shopOrder));die;

                    // Copia del mail de confirmacion a modo de backup
                    //
                    $mail = $this->container->getParameter('mail_report');
                    $request->setLocale('es_ES');
                    $mailer->setTo($mail);
                    $mailer->sendMailToSpool();

                    // Dejamos el locale tal y como estaba
                    $request->setLocale($locale);

                }
                return $this->redirect($this->generateUrl('list_orders'));
            }
        }

        return $this->render('OrderBundle:ShopOrders:new_order.html.twig', array('shopOrder'    => $shopOrder,
                                                                                 'form_name'    => $form->getName(),
                                                                                 'form'         => $form->createView()));

    }

//  _____ ____ ___ _____
// | ____|  _ \_ _|_   _|
// |  _| | | | | |  | |
// | |___| |_| | |  | |
// |_____|____/___| |_|

    /**
     * Crea una solicitud (shopOrder) del tipo "modify"
     * @Route("/shop/edit/{id}")
     * @return type
     * @throws AccessDeniedException
     */
    public function editAction($id) {
        $security = $this->get('security.context');
        $user = $security->getToken()->getUser();
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();

        //miramos si es una "re-modificacion" (una modificacion ha sido rechazada y la volvemos a modificar para volver a enviar)
        $shopOrder = $em->getRepository("OrderBundle:ShopOrder")->findOneBy(array('id'     => $id,
                                                                                  'action' => 'rejected'));
        if ($shopOrder) $shop = $em->getRepository("PartnerBundle:Shop")->find($shopOrder->getIdShop());
        else {
            $shop = $em->getRepository("PartnerBundle:Shop")->find($id);
            if (!$shop)
                throw $this->createNotFoundException('Tienda no encontrada en la BBDD');

            //si no existe una shopOrder previa la creamos por primera vez a partir del shop original
             $shopOrder = $this->shop_to_shopOrder($shop);
        }

        if (($security->isGranted('ROLE_AD') and ($user->getPartner() != null and $user->getPartner()->getId() == $shopOrder->getPartner()->getId()) === false)
        and ($security->isGranted('ROLE_SUPER_AD') and ($user->getCountry()->getId() == $shopOrder->getCountry()->getId()) === false)) {
            return $this->render('TwigBundle:Exception:exception_access.html.twig');
        }

        if (!$shopOrder) $shopOrder = new ShopOrder();
        if ($security->isGranted('ROLE_SUPER_AD')) {
            $id_partner = '0';
            $partners   = $em->getRepository("PartnerBundle:Partner")->findBy(array('country' => $user->getCountry()->getId(),
                                                                                    'active' => '1'));
        }
        else { $id_partner = $user->getPartner()->getId();
               $partners   = '0';
        }

        $partner = $em->getRepository("PartnerBundle:Partner")->find($id_partner);

        // Creamos variables de sesion para fitlrar los resultados del formulario
        if ($security->isGranted('ROLE_SUPER_AD')) {

            $partner_ids = '0';
            foreach ($partners as $p) { $partner_ids = $partner_ids.', '.$p->getId(); }
            $_SESSION['id_country'] = ' = '.$user->getCountry()->getId();
        }else {
            $_SESSION['id_country'] = ' = '.$partner->getCountry()->getId();
        }
        if($user->getCategoryService() != null)
        {
            $_SESSION['id_catserv'] = ' = '.$user->getCategoryService()->getId();
        }

        $form = $this->createForm(new ShopEditOrderType(), $shopOrder);

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {

                $shopOrder = UtilController::newEntity($shopOrder, $user);

                if ($shopOrder->getAction() == 'rejected' && $shopOrder->getWantedAction() == 'modify') {
                    $shopOrder->setAction('re_modify');
                }
                elseif ($shopOrder->getAction() == 'rejected') {
                    $shopOrder->setAction('re_modify');
                }else{
                    $shopOrder->setAction('modify');
                    $shopOrder->setWantedAction('modify');
                }
                UtilController::saveEntity($em, $shopOrder, $user);

                $mail = $shopOrder->getCreatedBy()->getEmail1();
                $pos = strpos($mail, '@');
                if ($pos != 0) {

                    // Cambiamos el locale para enviar el mail en el idioma del taller
                    $locale = $request->getLocale();
                    $lang_p = $shopOrder->getPartner()->getCountry()->getLang();
                    $lang   = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_p);
                    $request->setLocale($lang->getShortName());

                    /* MAILING */
                    $mailer = $this->get('cms.mailer');
                    $mailer->setTo($mail);
                    $mailer->setSubject($this->get('translator')->trans('mail.editOrder.subject').$shopOrder->getId());
                    $mailer->setFrom('noreply@adserviceticketing.com');
                    $mailer->setBody($this->renderView('UtilBundle:Mailing:order_edit_shop_mail.html.twig', array('shopOrder' => $shopOrder,
                                                                                                                  'shop'  => $shop,
                                                                                                                  '__locale' => $locale )));
                    $mailer->sendMailToSpool();
                    // echo $this->renderView('UtilBundle:Mailing:order_edit_shop_mail.html.twig', array('shopOrder' => $shopOrder,
                    //                                                                                   'shop'      => $shop));die;

                    // Copia del mail de confirmacion a modo de backup
                    //
                    $mail = $this->container->getParameter('mail_report');
                    $request->setLocale('es_ES');
                    $mailer->setTo($mail);
                    $mailer->sendMailToSpool();

                    // Dejamos el locale tal y como estaba
                    $request->setLocale($locale);
                }

                return $this->redirect($this->generateUrl('list_orders'));

            }
        }

        return $this->render('OrderBundle:ShopOrders:edit_order.html.twig', array('shopOrder' => $shopOrder,
                                                                                  'shop'      => $shop,
                                                                                  'form_name' => $form->getName(),
                                                                                  'form'      => $form->createView()));

    }

//   ____ _   _    _    _   _  ____ _____   ____ _____  _  _____ _   _ ____
//  / ___| | | |  / \  | \ | |/ ___| ____| / ___|_   _|/ \|_   _| | | / ___|
// | |   | |_| | / _ \ |  \| | |  _|  _|   \___ \ | | / _ \ | | | | | \___ \
// | |___|  _  |/ ___ \| |\  | |_| | |___   ___) || |/ ___ \| | | |_| |___) |
//  \____|_| |_/_/   \_\_| \_|\____|_____| |____/ |_/_/   \_\_|  \___/|____/

    /**
     * Crea una solicitud (shopOrder) del tipo "activate" o "deactivate" segun el $status
     * @Route("/shop/change_status/{id}/{status}")
     * @ParamConverter("shop", class="PartnerBundle:Shop")
     * @param string $status (active | inactive)
     * @return type
     * @throws AccessDeniedException
     * @throws type
     */
    public function changeStatusAction($id, $status, $shop){
        $security = $this->get('security.context');
        $request = $this->getRequest();
        if ($security->isGranted('ROLE_AD') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();

        //si veneimos de un estado "rejected" y queremos volver a activar/desactivar tenemos que eliminar la shopOrder antigua
        //antes de crear la nueva (asi evitamos tener workshopsOrders duplicados)
        $shopOrder = $em->getRepository("OrderBundle:ShopOrder")->findOneBy(array('id_shop' => $id));
        if ($shopOrder && $shopOrder->getAction() == 'rejected'){
            $em->remove($shopOrder);
            $em->flush();
        }

        $user = $security->getToken()->getUser();
        $shopOrder = $this->shop_to_shopOrder($shop);
        $shopOrder = UtilController::newEntity($shopOrder, $user);

        //actualizamos el campo "action" de la orden segun queramos activar o desactivar
        if ($status == 'active'){
            $shopOrder->setAction('activate');
            $shopOrder->setWantedAction('activate');

        }elseif ($status == 'inactive'){
            $shopOrder->setAction('deactivate');
            $shopOrder->setWantedAction('deactivate');
        }

        UtilController::saveEntity($em, $shopOrder, $user);

        $mail = $shopOrder->getCreatedBy()->getEmail1();
        $pos = strpos($mail, '@');
        if ($pos != 0) {

            // Cambiamos el locale para enviar el mail en el idioma del taller
            $locale = $request->getLocale();
            $lang_p = $shopOrder->getPartner()->getCountry()->getLang();
            $lang   = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_p);
            $request->setLocale($lang->getShortName());

            /* MAILING */
            $mailer = $this->get('cms.mailer');
            $mailer->setTo($mail);
            $mailer->setSubject($this->get('translator')->trans('mail.changeOrder.subject').$shopOrder->getId());
            $mailer->setFrom('noreply@adserviceticketing.com');
            $mailer->setBody($this->renderView('UtilBundle:Mailing:order_change_shop_mail.html.twig', array('shopOrder' => $shopOrder, '__locale' => $locale)));
            $mailer->sendMailToSpool();
            //echo $this->renderView('UtilBundle:Mailing:order_change_shop_mail.html.twig', array('shopOrder' => $shopOrder));die;

            // Copia del mail de confirmacion a modo de backup
            //
            $mail = $this->container->getParameter('mail_report');
            $request->setLocale('es_ES');
            $mailer->setTo($mail);
            $mailer->sendMailToSpool();

            // Dejamos el locale tal y como estaba
            $request->setLocale($locale);
        }

        return $this->redirect($this->generateUrl('list_orders'));

    }

//  ____  _____    _ _____ ____ _____
// |  _ \| ____|  | | ____/ ___|_   _|
// | |_) |  _| _  | |  _|| |     | |
// |  _ <| |__| |_| | |__| |___  | |
// |_| \_\_____\___/|_____\____| |_|

    /**
     * Actualiza el campo "rejection_reason" de la shopOrder i pone su estado en "rejected"
     * @Route("/shop/reject/{id}")
     * @ParamConverter("shopOrder", class="OrderBundle:ShopOrder")
     * @return type
     * @throws AccessDeniedException
     * @throws type
     */
    public function rejectAction($shopOrder){
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();

        $form = $this->createForm(new ShopRejectOrderType(), $shopOrder);

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {

                $shopOrder->setAction('rejected');
                $shopOrder->setRejectionReason($form->get('rejection_reason')->getData());     //recogemos del formulario el motivo de rechazo...
                $em->persist($shopOrder);
                $em->flush();

                $mail = $shopOrder->getCreatedBy()->getEmail1();
                $pos = strpos($mail, '@');
                if ($pos != 0) {

                    // Cambiamos el locale para enviar el mail en el idioma del taller
                    $locale = $request->getLocale();
                    $lang_p = $shopOrder->getPartner()->getCountry()->getLang();
                    $lang   = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_p);
                    $request->setLocale($lang->getShortName());

                    /* MAILING */
                    $mailer = $this->get('cms.mailer');
                    $mailer->setTo($mail);
                    $mailer->setSubject($this->get('translator')->trans('mail.rejectOrder.subject').$shopOrder->getId());
                    $mailer->setFrom('noreply@adserviceticketing.com');
                    $mailer->setBody($this->renderView('UtilBundle:Mailing:order_reject_shop_mail.html.twig', array('shopOrder' => $shopOrder, '__locale' => $locale)));
                    $mailer->sendMailToSpool();
                    //echo $this->renderView('UtilBundle:Mailing:order_reject_shop_mail.html.twig', array('shopOrder' => $shopOrder));die;

                    // Copia del mail de confirmacion a modo de backup
                    //
                    $mail = $this->container->getParameter('mail_report');
                    $request->setLocale('es_ES');
                    $mailer->setTo($mail);
                    $mailer->sendMailToSpool();

                    // Dejamos el locale tal y como estaba
                    $request->setLocale($locale);
                }

                return $this->redirect($this->generateUrl('list_orders'));
            }

        }

        return $this->render('OrderBundle:ShopOrders:reject_order.html.twig', array('shopOrder' => $shopOrder,
                                                                                    'form_name' => $form->getName(),
                                                                                    'form'      => $form->createView()));
    }

//  ____  _____ ____  _____ _   _ ____
// |  _ \| ____/ ___|| ____| \ | |  _ \
// | |_) |  _| \___ \|  _| |  \| | | | |
// |  _ <| |___ ___) | |___| |\  | |_| |
// |_| \_\_____|____/|_____|_| \_|____/

    /**
     * Reenvia la solicitud
     * @Route("/shop/resend/{id}")
     * @ParamConverter("shopOrder", class="OrderBundle:ShopOrder")
     * @return type
     */
    public function resendOrderAction($shopOrder){

        if ($this->get('security.context')->isGranted('ROLE_AD') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();

        //si veneimos de un estado "rejected" y queremos volver a solicitar tenemos que eliminar la workshopOrder antigua
        //antes de crear la nueva (asi evitamos tener workshopsOrders duplicados)
        if ($shopOrder->getAction() == 'rejected'){
            $shopOrder->setAction($shopOrder->getWantedAction());
            $action = $shopOrder->getWantedAction();
            $em->persist($shopOrder);
            $em->flush();

            $mail = $shopOrder->getCreatedBy()->getEmail1();
            $pos = strpos($mail, '@');
            if ($pos != 0) {

                // Cambiamos el locale para enviar el mail en el idioma del taller
                $locale = $request->getLocale();
                $lang_p = $shopOrder->getPartner()->getCountry()->getLang();
                $lang   = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_p);
                $request->setLocale($lang->getShortName());

                /* MAILING */
                $mailer = $this->get('cms.mailer');
                $mailer->setTo($mail);
                $mailer->setSubject($this->get('translator')->trans('mail.resendOrder.subject').$shopOrder->getId());
                $mailer->setFrom('noreply@adserviceticketing.com');
                $mailer->setBody($this->renderView('UtilBundle:Mailing:order_shop_resend_mail.html.twig', array('shopOrder' => $shopOrder,
                                                                                                                'action'    => $action,
                                                                                                                '__locale' => $locale)));
                $mailer->sendMailToSpool();
                // echo $this->renderView('UtilBundle:Mailing:order_resend_mail.html.twig', array('shopOrder' => $shopOrder,
                //                                                                                'action'   => $action));die;

                // Copia del mail de confirmacion a modo de backup
                //
                $mail = $this->container->getParameter('mail_report');
                $request->setLocale('es_ES');
                $mailer->setTo($mail);
                $mailer->sendMailToSpool();

                // Dejamos el locale tal y como estaba
                $request->setLocale($locale);
            }


        }
        return $this->redirect($this->generateUrl('list_orders'));
    }

//  ____  _____ __  __  _____     _______
// |  _ \| ____|  \/  |/ _ \ \   / / ____|
// | |_) |  _| | |\/| | | | \ \ / /|  _|
// |  _ <| |___| |  | | |_| |\ V / | |___
// |_| \_\_____|_|  |_|\___/  \_/  |_____|

    /**
     * Elimina una shop segun el $id
     * @Route("/shop/remove/{id}")
     * @ParamConverter("shopOrder", class="OrderBundle:ShopOrder")
     * @return type
     * @throws AccessDeniedException
     * @throws type
     */
    public function removeAction($shopOrder){

        if ($this->get('security.context')->isGranted('ROLE_AD') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        $action = $shopOrder->getWantedAction();

        $mail = $shopOrder->getCreatedBy()->getEmail1();
        $pos = strpos($mail, '@');
        if ($pos != 0) {

            // Cambiamos el locale para enviar el mail en el idioma del taller
            $locale = $request->getLocale();
            $lang_p = $shopOrder->getPartner()->getCountry()->getLang();
            $lang   = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_p);
            $request->setLocale($lang->getShortName());

            /* MAILING */
            $mailer = $this->get('cms.mailer');
            $mailer->setTo($mail);
            $mailer->setSubject($this->get('translator')->trans('mail.removeOrder.subject').$shopOrder->getId());
            $mailer->setFrom('noreply@adserviceticketing.com');
            $mailer->setBody($this->renderView('UtilBundle:Mailing:order_remove_shop_mail.html.twig', array('shopOrder' => $shopOrder,
                                                                                                            'action'    => $action,
                                                                                                            '__locale' => $locale)));
            $mailer->sendMailToSpool();
            // echo $this->renderView('UtilBundle:Mailing:order_remove_shop_mail.html.twig', array('shopOrder' => $shopOrder,
            //                                                                                'action'   => $action));die;

            // Copia del mail de confirmacion a modo de backup
            //
            $mail = $this->container->getParameter('mail_report');
            $request->setLocale('es_ES');
            $mailer->setTo($mail);
            $mailer->sendMailToSpool();

            // Dejamos el locale tal y como estaba
            $request->setLocale($locale);
        }

        $em->remove($shopOrder);
        $em->flush();

        return $this->redirect($this->generateUrl('list_orders'));

    }

//  ____  _____ _     _____ _____ _____
// |  _ \| ____| |   | ____|_   _| ____|
// | | | |  _| | |   |  _|   | | |  _|
// | |_| | |___| |___| |___  | | | |___
// |____/|_____|_____|_____| |_| |_____|


    /**
     * Elimina una shop segun el $id
     * @Route("/shop/delete/{id}")
     * @ParamConverter("shop", class="PartnerBundle:Shop")
     * @return type
     * @throws AccessDeniedException
     * @throws type
     */
    public function deleteAction($shop){
        $security = $this->get('security.context');
        $request = $this->getRequest();
        if ($security->isGranted('ROLE_AD') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();
        $shopOrder = $this->shop_to_shopOrder($shop);
        $action = $shopOrder->setWantedAction('delete');
        $action = $shopOrder->setAction('delete');

        UtilController::saveEntity($em, $shopOrder, $security->getToken()->getUser());

        $mail = $shopOrder->getCreatedBy()->getEmail1();
        $pos = strpos($mail, '@');
        if ($pos != 0) {

            // Cambiamos el locale para enviar el mail en el idioma del taller
            $locale = $request->getLocale();
            $lang_p = $shopOrder->getPartner()->getCountry()->getLang();
            $lang   = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_p);
            $request->setLocale($lang->getShortName());

            /* MAILING */
            $mailer = $this->get('cms.mailer');
            $mailer->setTo($mail);
            $mailer->setSubject($this->get('translator')->trans('mail.removeOrder.subject').$shopOrder->getId());
            $mailer->setFrom('noreply@adserviceticketing.com');
            $mailer->setBody($this->renderView('UtilBundle:Mailing:order_remove_shop_mail.html.twig', array('shopOrder' => $shopOrder,
                                                                                                            'action'    => $action,
                                                                                                            '__locale' => $locale)));
            $mailer->sendMailToSpool();
            // echo $this->renderView('UtilBundle:Mailing:order_remove_shop_mail.html.twig', array('shopOrder' => $shopOrder,
            //                                                                                     'action'   => $action));die;

            // Copia del mail de confirmacion a modo de backup
            //
            $mail = $this->container->getParameter('mail_report');
            $request->setLocale('es_ES');
            $mailer->setTo($mail);
            $mailer->sendMailToSpool();

            // Dejamos el locale tal y como estaba
            $request->setLocale($locale);
        }

        return $this->redirect($this->generateUrl('list_orders'));

    }

//     _    ____ ____ _____ ____ _____
//    / \  / ___/ ___| ____|  _ \_   _|
//   / _ \| |  | |   |  _| | |_) || |
//  / ___ \ |__| |___| |___|  __/ | |
// /_/   \_\____\____|_____|_|    |_|

    /**
     * Acepta la solicitud
     * @Route("/shop/accept/{id}/{status}")
     * @ParamConverter("shopOrder", class="OrderBundle:ShopOrder")
     * @param string $status (accepted)
     * @return type
     * @throws AccessDeniedException
     */
    public function acceptAction($shopOrder, $status){

        $security = $this->get('security.context');
        $request = $this->getRequest();
        if ($security->isGranted('ROLE_ADMIN') === false)
            throw new AccessDeniedException();
        $em = $this->getDoctrine()->getEntityManager();

        // activate   + accepted = setActive a TRUE  and delete shopOrder
        // deactivate + accepted = setActive a FALSE and delete shopOrder
        // modify     + accepted = se hacen los cambios en shop and delete del shopOrder
        // create     + accepted = new workshop and delete shopOrder

        $user = $security->getToken()->getUser();

        if (( $shopOrder->getWantedAction() == 'activate') && $status == 'accepted'){
            $shop = $em->getRepository('PartnerBundle:Shop')->findOneBy(array('id' => $shopOrder->getIdShop()));
            $shop = $this->shopOrder_to_shop($shop, $shopOrder);
            $shop->setActive(true);
            $action = $shopOrder->getWantedAction();
            $em->remove($shopOrder);
            UtilController::saveEntity($em, $shop, $user);

        }elseif (( $shopOrder->getWantedAction() == 'deactivate') && $status == 'accepted'){
            $shop = $em->getRepository('PartnerBundle:Shop')->findOneBy(array('id' => $shopOrder->getIdShop()));
            $shop = $this->shopOrder_to_shop($shop, $shopOrder);
            $shop->setActive(false);
            $action = $shopOrder->getWantedAction();
            $em->remove($shopOrder);
            UtilController::saveEntity($em, $shop, $user);

        }elseif (($shopOrder->getWantedAction() == 'modify')  && $status == 'accepted'){
            $shop = $em->getRepository('PartnerBundle:Shop')->findOneBy(array('id' => $shopOrder->getIdShop()));
            $shop = $this->shopOrder_to_shop($shop, $shopOrder);
            $action = $shopOrder->getWantedAction();
            $em->remove($shopOrder);
            UtilController::saveEntity($em, $shop, $user);

        }elseif (($shopOrder->getWantedAction() == 'delete')  && $status == 'accepted'){
            $shop = $em->getRepository('PartnerBundle:Shop')->findOneBy(array('id' => $shopOrder->getIdShop()));
            $action = $shopOrder->getWantedAction();
            $em->remove($shopOrder);
            $em->remove($shop);
            $em->flush();

        }elseif (($shopOrder->getWantedAction() == 'create')  && $status == 'accepted'){
            $shop = $this->shopOrder_to_shop(new Shop(), $shopOrder);
            $action = $shopOrder->getWantedAction();
            $em->remove($shopOrder);
            UtilController::newEntity($shop, $user);
            UtilController::saveEntity($em, $shop, $user);

        }

        // MAIL DE CONFIRMACION

        $mail = $shopOrder->getCreatedBy()->getEmail1();
        $pos = strpos($mail, '@');
        if ($pos != 0) {

            // Cambiamos el locale para enviar el mail en el idioma del taller
            $locale = $request->getLocale();
            $lang_p = $shopOrder->getPartner()->getCountry()->getLang();
            $lang   = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_p);
            $request->setLocale($lang->getShortName());

            /* MAILING */
            $mailer = $this->get('cms.mailer');
            $mailer->setTo($mail);
            $mailer->setSubject($this->get('translator')->trans('mail.acceptOrder.shop.subject').$shop->getId());
            $mailer->setFrom('noreply@adserviceticketing.com');
            $mailer->setBody($this->renderView('UtilBundle:Mailing:order_accept_shop_mail.html.twig', array('shop'   => $shop,
                                                                                                            'action' => $action,
                                                                                                            '__locale' => $locale)));
            $mailer->sendMailToSpool();
            // echo $this->renderView('UtilBundle:Mailing:order_accept_shop_mail.html.twig', array('shop' => $shop,
            //                                                                                     'action'   => $action));die;

            // Copia del mail de confirmacion a modo de backup
            //
            $mail = $this->container->getParameter('mail_report');
            $request->setLocale('es_ES');
            $mailer->setTo($mail);
            $mailer->sendMailToSpool();

            // Dejamos el locale tal y como estaba
            $request->setLocale($locale);
        }

        $user = $security->getToken()->getUser();
        $shopOrders = $em->getRepository("OrderBundle:ShopOrder")->findAll();
        $ordersBefore = $this->getShopOrdersBefore($em, $shopOrders);

        return $this->redirect($this->generateUrl('list_orders'));

    }


    /**
     * Hace el mapeo entre shop y shopOrder
     * @param Shop $shop
     * @return \Adservice\OrderBundle\Entity\shopOrder
     */
    private function shop_to_shopOrder($shop) {

        $shopOrder = new ShopOrder();

        $shopOrder->setIdShop         ($shop->getId());
        $shopOrder->setName           ($shop->getName());
        $shopOrder->setPartner        ($shop->getPartner());
        $shopOrder->setCategoryService($shop->getCategoryService());
        $shopOrder->setCountry        ($shop->getCountry());
        $shopOrder->setRegion         ($shop->getRegion());
        $shopOrder->setCity           ($shop->getCity());
        $shopOrder->setAddress        ($shop->getAddress());
        $shopOrder->setPostalCode     ($shop->getPostalCode());
        $shopOrder->setPhoneNumber1   ($shop->getPhoneNumber1());
        $shopOrder->setPhoneNumber2   ($shop->getPhoneNumber2());
        $shopOrder->setFax            ($shop->getFax());
        $shopOrder->setEmail1         ($shop->getEmail1());
        $shopOrder->setEmail2         ($shop->getEmail2());
        $shopOrder->setCodeShop       ($shop->getCodeShop());
        $shopOrder->setCif            ($shop->getCif());
        $shopOrder->setContact        ($shop->getContact());

        if ($shop->getCreatedBy() != null ) {
            $shopOrder->setCreatedBy($shop->getCreatedBy());
        }
        if ($shop->getCreatedAt() != null ) {
            $shopOrder->setCreatedAt($shop->getCreatedAt());
        }
        if ($shop->getModifiedBy() != null ) {
            $shopOrder->setModifiedBy($shop->getModifiedBy());
        }
        if ($shop->getModifiedAt() != null ) {
            $shopOrder->setModifiedAt($shop->getModifiedAt());
        }
        $shopOrder->setActive(false);

        return $shopOrder;
    }

    /**
     * Hace el mapeo entre shopOrder y shop
     * @param Shop $shop
     * @param type $shopOrder
     * @return \Adservice\OrderBundle\Entity\shopOrder
     */
    private function shopOrder_to_shop($shop, $shopOrder){

        $shop->setName           ($shopOrder->getName());
        $shop->setPartner        ($shopOrder->getPartner());
        $shop->setCategoryService($shopOrder->getCategoryService());
        $shop->setCountry        ($shopOrder->getCountry());
        $shop->setRegion         ($shopOrder->getRegion());
        $shop->setCity           ($shopOrder->getCity());
        $shop->setAddress        ($shopOrder->getAddress());
        $shop->setPostalCode     ($shopOrder->getPostalCode());
        $shop->setPhoneNumber1   ($shopOrder->getPhoneNumber1());
        $shop->setPhoneNumber2   ($shopOrder->getPhoneNumber2());
        $shop->setFax            ($shopOrder->getFax());
        $shop->setEmail1         ($shopOrder->getEmail1());
        $shop->setEmail2         ($shopOrder->getEmail2());
        $shop->setCodeShop       ($shopOrder->getCodeShop());
        $shop->setCif            ($shopOrder->getCif());
        $shop->setContact        ($shopOrder->getContact());

        if ($shopOrder->getCreatedBy() != null ) {
            $shop->setCreatedBy($shopOrder->getCreatedBy());
        }
        if ($shopOrder->getCreatedAt() != null ) {
            $shop->setCreatedAt($shopOrder->getCreatedAt());
        }
        if ($shopOrder->getModifiedBy() != null ) {
            $shop->setModifiedBy($shopOrder->getModifiedBy());
        }
        if ($shopOrder->getModifiedAt() != null ) {
            $shop->setModifiedAt($shopOrder->getModifiedAt());
        }
        $shop->setActive(true);

        return $shop;
    }

    /**
     * crea un array con los valores anteriores a la modificacion/rechazo de la solicitud
     * @param  Array $shopsOrders
     * @return Array
     */
    public static function getShopOrdersBefore($em, $shopOrders) {
        $ordersBefore = array();

        foreach ($shopOrders as $shopOrder) {

            if ($shopOrder->getAction() == 'modify' or $shopOrder->getAction() == 're_modify') {

                $shopBefore = $em->getRepository("PartnerBundle:Shop")->findOneBy(array('id' => $shopOrder->getIdShop()));
                $ordersBefore[$shopOrder->getId()] = $shopBefore;
            }

            if ($shopOrder->getAction() == 'rejected' or $shopOrder->getAction() == 'resend') {

                $shopBefore = $em->getRepository("OrderBundle:shopOrder")->find($shopOrder->getId());
                $ordersBefore[$shopOrder->getId()] = $shopBefore;
            }
        }
        return $ordersBefore;
    }
}
