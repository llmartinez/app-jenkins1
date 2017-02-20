<?php

namespace Adservice\OrderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Adservice\OrderBundle\Controller\WorkshopOrderController;
use Adservice\OrderBundle\Controller\ShopOrderController;
use Adservice\UtilBundle\Entity\Pagination;


class OrderController extends Controller
{
    /**
     * Lista todas las Orders
     * @return type
     * @throws AccessDeniedException
     */
    public function listOrdersAction($page=1, $option='workshop_pending', $country='0', $w_idpartner='0', $w_id='0', $partner='0', $status='0', $term='0', $field='0'){

        $security = $this->get('security.context');
        if ($security->isGranted('ROLE_COMMERCIAL') === false)
            return $this->redirect($this->generateUrl('user_login'));

        $em = $this->getDoctrine()->getEntityManager();
        $user = $security->getToken()->getUser();
        $role = $user->getRoles();
        $role = $role[0];
        $role = $role->getRole();

        $ntickets = array();
        $workshop_pending = array();
        $workshop_rejected = array();
        $shop_pending = array();
        $shop_rejected = array();

        $rejected     = array('action' , " = 'rejected'");
        $not_rejected = array('action' , " != 'rejected'");

        $preorder     = array('wanted_action' , " = 'preorder'");
        $not_preorder = array('wanted_action' , " != 'preorder'");

        $workshop_pending [] = $not_preorder;
        $workshop_pending [] = $not_rejected;
        $shop_pending     [] = $not_rejected;
        $workshop_rejected[] = $not_preorder;
        $workshop_rejected[] = $rejected;
        $shop_rejected    [] = $rejected;
        $preorder_pending [] = $preorder;
        $preorder_pending [] = $not_rejected;
        $preorder_rejected[] = $preorder;
        $preorder_rejected[] = $rejected;

        if    ($role == "ROLE_SUPER_AD" || $role == "ROLE_TOP_AD"){
                                        }
        elseif($role == "ROLE_AD")      {   $by_partner          = array('partner', ' = '.$user->getPartner()->getId());
                                            $workshop_pending[]  = $by_partner;
                                            $workshop_rejected[] = $by_partner;
                                            $shop_pending[]      = $by_partner;
                                            $shop_rejected[]     = $by_partner;
                                            $preorder_pending[]  = $by_partner;
                                            $preorder_rejected[] = $by_partner;
                                        }
        elseif($role=="ROLE_COMMERCIAL"){   $by_commercial       = array('created_by', ' = '.$user->getId());
                                            $preorder_pending[]  = $by_commercial;
                                            $preorder_rejected[] = $by_commercial;

                                            // if($user->getShop() != null) {
                                            //     $by_shop             = array('shop', ' = '.$user->getShop()->getId());
                                            //     $preorder_pending[]  = $by_shop;
                                            //     $preorder_rejected[] = $by_shop;
                                            // }
                                        }
        elseif($role == "ROLE_ADMIN" )  { 
                                        }
        elseif($role == "ROLE_SUPER_ADMIN"){
                                        }

        if ($country != '0' ) {
            $workshop_pending[]  = array('country' , " = ".$country);
            $workshop_rejected[] = array('country' , " = ".$country);
            $preorder_pending[]  = array('country' , " = ".$country);
            $preorder_rejected[] = array('country' , " = ".$country);
            $shop_pending[]      = array('country' , " = ".$country);
            $shop_rejected[]     = array('country' , " = ".$country);
        }

        if($user->getCategoryService() != null) {
            $workshop_pending[]  = array('category_service' , " = ".$user->getCategoryService()->getId());
            $workshop_rejected[] = array('category_service' , " = ".$user->getCategoryService()->getId());
            $preorder_pending[]  = array('category_service' , " = ".$user->getCategoryService()->getId());
            $preorder_rejected[] = array('category_service' , " = ".$user->getCategoryService()->getId());
            $shop_pending[]      = array('category_service' , " = ".$user->getCategoryService()->getId());
            $shop_rejected[]     = array('category_service' , " = ".$user->getCategoryService()->getId());
        }

        $pagination = new Pagination($page);
        $length_workshop_pending  = $pagination->getRowsLength($em, 'OrderBundle', 'WorkshopOrder' , $workshop_pending);
        $length_workshop_rejected = $pagination->getRowsLength($em, 'OrderBundle', 'WorkshopOrder' , $workshop_rejected);
        $length_preorder_pending  = $pagination->getRowsLength($em, 'OrderBundle', 'WorkshopOrder' , $preorder_pending);
        $length_preorder_rejected = $pagination->getRowsLength($em, 'OrderBundle', 'WorkshopOrder' , $preorder_rejected);
        $length_shop_pending      = $pagination->getRowsLength($em, 'OrderBundle', 'ShopOrder'     , $shop_pending);
        $length_shop_rejected     = $pagination->getRowsLength($em, 'OrderBundle', 'ShopOrder'     , $shop_rejected);

        if($option == 'workshop_pending') {
            $orders = $pagination->getRows($em, 'OrderBundle', 'WorkshopOrder', $workshop_pending, $pagination);
            $pagination->setTotalPagByLength($length_workshop_pending);
        }
        elseif($option == 'workshop_rejected') {
            $orders = $pagination->getRows($em, 'OrderBundle', 'WorkshopOrder', $workshop_rejected, $pagination);
            $pagination->setTotalPagByLength($length_workshop_rejected);
        }
        elseif($option == 'preorder_pending') {
            $orders = $pagination->getRows($em, 'OrderBundle', 'WorkshopOrder', $preorder_pending, $pagination);
            $pagination->setTotalPagByLength($length_preorder_pending);
        }
        elseif($option == 'preorder_rejected') {
            $orders = $pagination->getRows($em, 'OrderBundle', 'WorkshopOrder', $preorder_rejected, $pagination);
            $pagination->setTotalPagByLength($length_preorder_rejected);
        }
        elseif($option == 'shop_pending') {
            $orders = $pagination->getRows($em, 'OrderBundle', 'ShopOrder'     , $shop_pending     , $pagination);
            $pagination->setTotalPagByLength($length_shop_pending);
        }

        elseif($option == 'shop_rejected') {
            $orders = $pagination->getRows($em, 'OrderBundle', 'ShopOrder'     , $shop_rejected    , $pagination);
            $pagination->setTotalPagByLength($length_shop_rejected);
        }

        foreach ($orders as $order)
        {
            if($order->getWantedAction() == 'activate' || $order->getWantedAction() == 'deactivate')
            {
                $id = $order->getIdWorkshop();

                $ntickets[$id] = $em->getRepository("WorkshopBundle:Workshop")->getNumTickets($id);
            }
        }

        //valores anteriores a la modificacion/rechazo de la Solicitud
        if    (($option == 'workshop_pending') or ($option == 'workshop_rejected') or ($option == 'preorder_pending') or ($option == 'preorder_rejected'))
                                                                                    $ordersBefore = WorkshopOrderController::getWorkshopOrdersBefore($em, $orders);
        elseif(($option == 'shop_pending')     or ($option == 'shop_rejected'))     $ordersBefore = ShopOrderController::getShopOrdersBefore($em, $orders);

        $countries = $em->getRepository('UtilBundle:Country')->findAll();

        return $this->render('OrderBundle:Order:list_orders.html.twig', array(  'pagination'   => $pagination,
                                                                                'option'       => $option,
                                                                                'countries'    => $countries,
                                                                                'country'      => $country,
                                                                                'orders'       => $orders,
                                                                                'ordersBefore' => $ordersBefore,
                                                                                'ntickets'     => $ntickets,
                                                                                'length_workshop_pending'  => $length_workshop_pending,
                                                                                'length_workshop_rejected' => $length_workshop_rejected,
                                                                                'length_preorder_pending'   => $length_preorder_pending,
                                                                                'length_preorder_rejected'  => $length_preorder_rejected,
                                                                                'length_shop_pending'      => $length_shop_pending,
                                                                                'length_shop_rejected'     => $length_shop_rejected,
                                                                            ));
    }
}
