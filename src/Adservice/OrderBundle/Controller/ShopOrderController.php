<?php

namespace Adservice\OrderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
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
        $em = $this->getDoctrine()->getEntityManager();

        $params[] = array("name", " != '...' "); //Evita listar las tiendas por defecto de los socios (Tiendas con nombre '...')

        if($security->isGranted('ROLE_SUPER_AD')) {
            if ($partner != 'none') $params[] = array('partner', ' = '.$partner);
            else                    $params[] = array();
        }
        else $params[] = array('partner', ' = '.$security->getToken()->getUser()->getPartner()->getId());

        //$params[] = array('country', ' = '.$security->getToken()->getUser()->getCountry()->getId());

        $pagination = new Pagination($page);

        $shops  = $pagination->getRows($em, 'PartnerBundle', 'Shop', $params, $pagination);

        $length = $pagination->getRowsLength($em, 'PartnerBundle', 'Shop', $params);

        $pagination->setTotalPagByLength($length);

        if($security->isGranted('ROLE_SUPER_AD')) $partners = $em->getRepository('PartnerBundle:Partner')->findAll();
        else $partners = array();

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

        if ($this->get('security.context')->isGranted('ROLE_AD') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();

        $shopOrder = new ShopOrder();
        $form = $this->createForm(new ShopNewOrderType(), $shopOrder);

        if ($request->getMethod() == 'POST') {

            $form->bindRequest($request);

            //La segunda comparacion ($form->getErrors()...) se hizo porque el request que reciber $form puede ser demasiado largo y hace que la funcion isValid() devuelva false
	    $form_errors = $form->getErrors();
	    if(isset($form_errors[0])) {
                $form_errors = $form_errors[0];
                $form_errors = $form_errors->getMessageTemplate();
            }else{ 
                $form_errors = 'none';
            }
            if ($form->isValid() or $form_errors == 'The uploaded file was too large. Please try to upload a smaller file') {

                $user = $this->get('security.context')->getToken()->getUser();

                $shopOrder = UtilController::newEntity($shopOrder, $user);
                if ($this->get('security.context')->isGranted('ROLE_AD_COUNTRY') === false)
                $shopOrder->setPartner($user->getPartner());
                $shopOrder->setActive(false);
                $shopOrder->setAction('create');
                $shopOrder->setWantedAction('create');
                UtilController::saveEntity($em, $shopOrder, $user);

                /* MAILING */
                $mailer = $this->get('cms.mailer');
                $mailer->setTo('dmaya@grupeina.com');  /* COLOCAR EN PROD -> *//* $mailer->setTo($shopOrder->getCreatedBy()->getEmail1()); */
                $mailer->setSubject($this->get('translator')->trans('mail.newOrder.subject').$shopOrder->getId());
                $mailer->setFrom('noreply@grupeina.com');
                $mailer->setBody($this->renderView('UtilBundle:Mailing:order_new_shop_mail.html.twig', array('shopOrder' => $shopOrder)));
                $mailer->sendMailToSpool();
                //echo $this->renderView('UtilBundle:Mailing:order_new_shop_mail.html.twig', array('shopOrder' => $shopOrder));die;

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
     * @param integer $id del workshop que queremos modificar
     * @return type
     * @throws AccessDeniedException
     * @throws type
     */
    public function editAction($id) {
        if ($this->get('security.context')->isGranted('ROLE_AD') === false)
            throw new AccessDeniedException();

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

        $form = $this->createForm(new ShopEditOrderType(), $shopOrder);

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

             //La segunda comparacion ($form->getErrors()...) se hizo porque el request que reciber $form puede ser demasiado largo y hace que la funcion isValid() devuelva false
            $form_errors = $form->getErrors();
	    if(isset($form_errors[0])) {
                $form_errors = $form_errors[0];
                $form_errors = $form_errors->getMessageTemplate();
            }else{ 
                $form_errors = 'none';
            }
            if ($form->isValid() or $form_errors == 'The uploaded file was too large. Please try to upload a smaller file') {

                $user = $this->get('security.context')->getToken()->getUser();

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

                /* MAILING */
                $mailer = $this->get('cms.mailer');
                $mailer->setTo('dmaya@grupeina.com');  /* COLOCAR EN PROD -> *//* $mailer->setTo($shopOrder->getCreatedBy()->getEmail1()); */
                $mailer->setSubject($this->get('translator')->trans('mail.editOrder.subject').$shopOrder->getId());
                $mailer->setFrom('noreply@grupeina.com');
                $mailer->setBody($this->renderView('UtilBundle:Mailing:order_edit_shop_mail.html.twig', array('shopOrder' => $shopOrder,
                                                                                                              'shop'  => $shop )));
                $mailer->sendMailToSpool();
                // echo $this->renderView('UtilBundle:Mailing:order_edit_shop_mail.html.twig', array('shopOrder' => $shopOrder,
                //                                                                                   'shop'      => $shop));die;

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
     * @param integer $id del shop que queremos modificar
     * @param string $status (active | inactive)
     * @return type
     * @throws AccessDeniedException
     * @throws type
     */
    public function changeStatusAction($id, $status){

        if ($this->get('security.context')->isGranted('ROLE_AD') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();
        $shop = $em->getRepository("PartnerBundle:Shop")->find($id);
        if (!$shop)
            throw $this->createNotFoundException('Taller no encontrado en la BBDD');

        //si veneimos de un estado "rejected" y queremos volver a activar/desactivar tenemos que eliminar la shopOrder antigua
        //antes de crear la nueva (asi evitamos tener workshopsOrders duplicados)
        $shopOrder = $em->getRepository("OrderBundle:ShopOrder")->findOneBy(array('id_shop' => $id));
        if ($shopOrder && $shopOrder->getAction() == 'rejected'){
            $em->remove($shopOrder);
            $em->flush();
        }

        $user = $this->get('security.context')->getToken()->getUser();
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

        /* MAILING */
        $mailer = $this->get('cms.mailer');
        $mailer->setTo('dmaya@grupeina.com');  /* COLOCAR EN PROD -> *//* mailer->setTo($shopOrder->getCreatedBy()->getEmail1()); */
        $mailer->setSubject($this->get('translator')->trans('mail.changeOrder.subject').$shopOrder->getId());
        $mailer->setFrom('noreply@grupeina.com');
        $mailer->setBody($this->renderView('UtilBundle:Mailing:order_change_shop_mail.html.twig', array('shopOrder' => $shopOrder)));
        $mailer->sendMailToSpool();
        //echo $this->renderView('UtilBundle:Mailing:order_change_shop_mail.html.twig', array('shopOrder' => $shopOrder));die;

        return $this->redirect($this->generateUrl('list_orders'));

    }

//  ____  _____    _ _____ ____ _____
// |  _ \| ____|  | | ____/ ___|_   _|
// | |_) |  _| _  | |  _|| |     | |
// |  _ <| |__| |_| | |__| |___  | |
// |_| \_\_____\___/|_____\____| |_|

    /**
     * Actualiza el campo "rejection_reason" de la shopOrder i pone su estado en "rejected"
     * @param type $id
     * @return type
     * @throws AccessDeniedException
     * @throws type
     */
    public function rejectAction($id){
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();

        $shopOrder = $em->getRepository("OrderBundle:ShopOrder")->find($id);
        if (!$shopOrder)
            throw $this->createNotFoundException('Solicitud de Tienda no encontrada en la BBDD: (id:'.$id.')');

        $form = $this->createForm(new ShopRejectOrderType(), $shopOrder);

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

             //La segunda comparacion ($form->getErrors()...) se hizo porque el request que reciber $form puede ser demasiado largo y hace que la funcion isValid() devuelva false
            $form_errors = $form->getErrors();
	    if(isset($form_errors[0])) {
                $form_errors = $form_errors[0];
                $form_errors = $form_errors->getMessageTemplate();
            }else{ 
                $form_errors = 'none';
            }
            if ($form->isValid() or $form_errors == 'The uploaded file was too large. Please try to upload a smaller file') {

                $shopOrder->setAction('rejected');
                $shopOrder->setRejectionReason($form->get('rejection_reason')->getData());     //recogemos del formulario el motivo de rechazo...
                $em->persist($shopOrder);
                $em->flush();

                /* MAILING */
                $mailer = $this->get('cms.mailer');
                $mailer->setTo('dmaya@grupeina.com');  /* COLOCAR EN PROD -> *//* $mailer->setTo($shopOrder->getCreatedBy()->getEmail1());*/
                $mailer->setSubject($this->get('translator')->trans('mail.rejectOrder.subject').$shopOrder->getId());
                $mailer->setFrom('noreply@grupeina.com');
                $mailer->setBody($this->renderView('UtilBundle:Mailing:order_reject_shop_mail.html.twig', array('shopOrder' => $shopOrder)));
                $mailer->sendMailToSpool();
                //echo $this->renderView('UtilBundle:Mailing:order_reject_shop_mail.html.twig', array('shopOrder' => $shopOrder));die;

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


    public function resendOrderAction($id){

        if ($this->get('security.context')->isGranted('ROLE_AD') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();

        //si veneimos de un estado "rejected" y queremos volver a solicitar tenemos que eliminar la workshopOrder antigua
        //antes de crear la nueva (asi evitamos tener workshopsOrders duplicados)
        $shopOrder = $em->getRepository("OrderBundle:ShopOrder")->find($id);
        if ($shopOrder->getAction() == 'rejected'){
            $shopOrder->setAction($shopOrder->getWantedAction());
            $em->persist($shopOrder);

            $action = $shopOrder->getWantedAction();

            /* MAILING */
            $mailer = $this->get('cms.mailer');
            $mailer->setTo('dmaya@grupeina.com');  /* COLOCAR EN PROD -> *//* $mailer->setTo($shopOrder->getCreatedBy()->getEmail1()); */
            $mailer->setSubject($this->get('translator')->trans('mail.resendOrder.subject').$shopOrder->getId());
            $mailer->setFrom('noreply@grupeina.com');
            $mailer->setBody($this->renderView('UtilBundle:Mailing:order_shop_resend_mail.html.twig', array('shopOrder' => $shopOrder,
                                                                                                            'action'    => $action)));
            $mailer->sendMailToSpool();
            // echo $this->renderView('UtilBundle:Mailing:order_resend_mail.html.twig', array('shopOrder' => $shopOrder,
            //                                                                                'action'   => $action));die;

            $em->flush();
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
     * @param integer $id del shop que queremos eliminar
     * @return type
     * @throws AccessDeniedException
     * @throws type
     */
    public function removeAction($id){

        if ($this->get('security.context')->isGranted('ROLE_AD') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();

        $shopOrder = $em->getRepository("OrderBundle:ShopOrder")->find($id);
        if (!$shopOrder)
            throw $this->createNotFoundException('Orden de Tienda no encontrado en la BBDD: (id:'.$id.')');

        $action = $shopOrder->getWantedAction();

        /* MAILING */
        $mailer = $this->get('cms.mailer');
        $mailer->setTo('dmaya@grupeina.com');  /* COLOCAR EN PROD -> *//* $mailer->setTo($shopOrder->getCreatedBy()->getEmail1());*/
        $mailer->setSubject($this->get('translator')->trans('mail.removeOrder.subject').$shopOrder->getId());
        $mailer->setFrom('noreply@grupeina.com');
        $mailer->setBody($this->renderView('UtilBundle:Mailing:order_remove_shop_mail.html.twig', array('shopOrder' => $shopOrder,
                                                                                                        'action'    => $action)));
        $mailer->sendMailToSpool();
        // echo $this->renderView('UtilBundle:Mailing:order_remove_shop_mail.html.twig', array('shopOrder' => $shopOrder,
        //                                                                                'action'   => $action));die;

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
     * @param integer $id del shop que queremos eliminar
     * @return type
     * @throws AccessDeniedException
     * @throws type
     */
    public function deleteAction($id){

        if ($this->get('security.context')->isGranted('ROLE_AD') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();

        $shop = $em->getRepository("PartnerBundle:Shop")->find($id);
        if (!$shop)
            throw $this->createNotFoundException('Orden de Tienda no encontrada en la BBDD: (id:'.$id.')');

        $shopOrder = $this->shop_to_shopOrder($shop);
        $action = $shopOrder->setWantedAction('delete');
        $action = $shopOrder->setAction('delete');

        UtilController::saveEntity($em, $shopOrder, $this->get('security.context')->getToken()->getUser());

        /* MAILING */
        $mailer = $this->get('cms.mailer');
        $mailer->setTo('dmaya@grupeina.com');  /* COLOCAR EN PROD -> *//* $mailer->setTo($shopOrder->getCreatedBy()->getEmail1()); */
        $mailer->setSubject($this->get('translator')->trans('mail.removeOrder.subject').$shopOrder->getId());
        $mailer->setFrom('noreply@grupeina.com');
        $mailer->setBody($this->renderView('UtilBundle:Mailing:order_remove_shop_mail.html.twig', array('shopOrder' => $shopOrder,
                                                                                                        'action'    => $action)));
        $mailer->sendMailToSpool();
        // echo $this->renderView('UtilBundle:Mailing:order_remove_shop_mail.html.twig', array('shopOrder' => $shopOrder,
        //                                                                                     'action'   => $action));die;

        return $this->redirect($this->generateUrl('list_orders'));

    }

//     _    ____ ____ _____ ____ _____
//    / \  / ___/ ___| ____|  _ \_   _|
//   / _ \| |  | |   |  _| | |_) || |
//  / ___ \ |__| |___| |___|  __/ | |
// /_/   \_\____\____|_____|_|    |_|

    /**
     * @param integer $id del shopOrder
     * @param string $status (accepted)
     * @return type
     * @throws AccessDeniedException
     */
    public function acceptAction($id, $status){

        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();

        $shopOrder = $em->getRepository('OrderBundle:ShopOrder')->find($id);

        // activate   + accepted = setActive a TRUE  and delete shopOrder
        // deactivate + accepted = setActive a FALSE and delete shopOrder
        // modify     + accepted = se hacen los cambios en shop and delete del shopOrder
        // create     + accepted = new workshop and delete shopOrder

        $user = $this->get('security.context')->getToken()->getUser();

        if (( $shopOrder->getWantedAction() == 'activate') && $status == 'accepted'){
            $shop = $em->getRepository('PartnerBundle:Shop')->findOneBy(array('id' => $shopOrder->getIdShop()));
            $shop = $this->shopOrder_to_shop($shop, $shopOrder);
            $shop->setActive(true);
            $action = $shopOrder->getWantedAction();
            $em->remove($shopOrder);
            UtilController::newEntity($shop, $user);
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

        /* MAILING */
        $mailer = $this->get('cms.mailer');
        $mailer->setTo('dmaya@grupeina.com');  /* COLOCAR EN PROD -> *//* $mailer->setTo($shop->getCreatedBy()->getEmail1());*/
        $mailer->setSubject($this->get('translator')->trans('mail.acceptOrder.shop.subject').$shop->getId());
        $mailer->setFrom('noreply@grupeina.com');
        $mailer->setBody($this->renderView('UtilBundle:Mailing:order_accept_shop_mail.html.twig', array('shop'   => $shop,
                                                                                                        'action' => $action)));
        $mailer->sendMailToSpool();
        // echo $this->renderView('UtilBundle:Mailing:order_accept_shop_mail.html.twig', array('shop' => $shop,
        //                                                                                     'action'   => $action));die;

        $user = $this->get('security.context')->getToken()->getUser();
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

        $shopOrder->setIdShop        ($shop->getId());
        $shopOrder->setName          ($shop->getName());
        $shopOrder->setPartner       ($shop->getPartner());
        $shopOrder->setCountry       ($shop->getCountry());
        $shopOrder->setRegion        ($shop->getRegion());
        $shopOrder->setCity          ($shop->getCity());
        $shopOrder->setAddress       ($shop->getAddress());
        $shopOrder->setPostalCode    ($shop->getPostalCode());
        $shopOrder->setPhoneNumber1  ($shop->getPhoneNumber1());
        $shopOrder->setPhoneNumber2  ($shop->getPhoneNumber2());
        $shopOrder->setFax           ($shop->getFax());
        $shopOrder->setEmail1        ($shop->getEmail1());
        $shopOrder->setEmail2        ($shop->getEmail2());

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

        $shop->setName          ($shopOrder->getName());
        $shop->setPartner       ($shopOrder->getPartner());
        $shop->setCountry       ($shopOrder->getCountry());
        $shop->setRegion        ($shopOrder->getRegion());
        $shop->setCity          ($shopOrder->getCity());
        $shop->setAddress       ($shopOrder->getAddress());
        $shop->setPostalCode    ($shopOrder->getPostalCode());
        $shop->setPhoneNumber1  ($shopOrder->getPhoneNumber1());
        $shop->setPhoneNumber2  ($shopOrder->getPhoneNumber2());
        $shop->setFax           ($shopOrder->getFax());
        $shop->setEmail1        ($shopOrder->getEmail1());
        $shop->setEmail2        ($shopOrder->getEmail2());

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

        //
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
