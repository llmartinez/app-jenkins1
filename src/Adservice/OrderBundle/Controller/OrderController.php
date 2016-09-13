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
        if ($security->isGranted('ROLE_AD') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();
        $user = $security->getToken()->getUser();
        $role = $user->getRoles();
        $role = $role[0];
        $role = $role->getRole();

        $workshop_pending = array();
        $workshop_rejected = array();
        $shop_pending = array();
        $shop_rejected = array();

        $rejected     = array('action' , " = 'rejected'");
        $not_rejected = array('action' , " != 'rejected'");

        if    ($role == "ROLE_SUPER_AD" || $role == "ROLE_TOP_AD"){   $by_country          = array('country', ' = '.$user->getCountry()->getId());
                                            $workshop_pending[]  = $by_country;
                                            $workshop_pending[]  = $not_rejected;
                                            $workshop_rejected[] = $by_country;
                                            $workshop_rejected[] = $rejected;
                                            $shop_pending[]      = $by_country;
                                            $shop_pending[]      = $not_rejected;
                                            $shop_rejected[]     = $by_country;
                                            $shop_rejected[]     = $rejected;
                                        }
        elseif($role == "ROLE_AD")      {   $by_partner          = array('partner', ' = '.$user->getPartner()->getId());
                                            $workshop_pending[]  = $by_partner;
                                            $workshop_pending[]  = $not_rejected;
                                            $workshop_rejected[] = $by_partner;
                                            $workshop_rejected[] = $rejected;
                                            $shop_pending[]      = $by_partner;
                                            $shop_pending[]      = $not_rejected;
                                            $shop_rejected[]     = $by_partner;
                                            $shop_rejected[]     = $rejected;
                                        }
        elseif($role == "ROLE_ADMIN" )  {   $by_country          = array('country', ' = '.$user->getCountry()->getId());
                                            $workshop_pending[]  = $by_country;
                                            $workshop_pending[]  = $not_rejected;
                                            $workshop_rejected[] = $by_country;
                                            $workshop_rejected[] = $rejected;
                                            $shop_pending[]      = $by_country;
                                            $shop_pending[]      = $not_rejected;
                                            $shop_rejected[]     = $by_country;
                                            $shop_rejected[]     = $rejected;
                                        }
        elseif($role == "ROLE_SUPER_ADMIN"){$workshop_pending[]  = $not_rejected;
                                            $workshop_rejected[] = $rejected;
                                            $shop_pending[]      = $not_rejected;
                                            $shop_rejected[]     = $rejected;
                                        }

        if ($country != '0' ) {
            $workshop_pending[] = array('country' , " = ".$country);
            $workshop_rejected[] = array('country' , " = ".$country);
            $shop_pending[] = array('country' , " = ".$country);
            $shop_rejected[] = array('country' , " = ".$country);
        }

        $pagination = new Pagination($page);
        $length_workshop_pending  = $pagination->getRowsLength($em, 'OrderBundle', 'WorkshopOrder' , $workshop_pending);
        $length_workshop_rejected = $pagination->getRowsLength($em, 'OrderBundle', 'WorkshopOrder' , $workshop_rejected);
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
        elseif($option == 'shop_pending') {
            $orders = $pagination->getRows($em, 'OrderBundle', 'ShopOrder'    , $shop_pending     , $pagination);
            $pagination->setTotalPagByLength($length_shop_pending);
        }

        elseif($option == 'shop_rejected') {
            $orders = $pagination->getRows($em, 'OrderBundle', 'ShopOrder'    , $shop_rejected    , $pagination);
            $pagination->setTotalPagByLength($length_shop_rejected);
        }

        //valores anteriores a la modificacion/rechazo de la Solicitud
        if    (($option == 'workshop_pending') or ($option == 'workshop_rejected')) $ordersBefore = WorkshopOrderController::getWorkshopOrdersBefore($em, $orders);
        elseif(($option == 'shop_pending')     or ($option == 'shop_rejected'))     $ordersBefore = ShopOrderController::getShopOrdersBefore($em, $orders);

        $countries = $em->getRepository('UtilBundle:Country')->findAll();

        return $this->render('OrderBundle:Order:list_orders.html.twig', array(  'pagination'   => $pagination,
                                                                                'option'       => $option,
                                                                                'countries'    => $countries,
                                                                                'country'      => $country,
                                                                                'orders'       => $orders,
	                                                                            'ordersBefore' => $ordersBefore,
                                                                                'length_workshop_pending'  => $length_workshop_pending,
                                                                                'length_workshop_rejected' => $length_workshop_rejected,
                                                                                'length_shop_pending'      => $length_shop_pending,
                                                                                'length_shop_rejected'     => $length_shop_rejected,
                                                                            ));
    }
}
