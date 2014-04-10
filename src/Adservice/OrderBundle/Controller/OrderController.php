<?php

namespace Adservice\OrderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Adservice\OrderBundle\Controller\WorkshopOrderController;
use Adservice\OrderBundle\Controller\ShopOrderController;


class OrderController extends Controller
{
    /**
     * Lista todas las Orders
     * @return type
     * @throws AccessDeniedException
     */
    public function listOrdersAction(){

        if ($this->get('security.context')->isGranted('ROLE_AD') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $role = $user->getRoles();

        //SHOP ORDERS :segun el rol puede ver sus tiendas o todos los que haya
        if ($role[0]->getRole() == "ROLE_AD"){
            $shopOrders         = $em->getRepository("OrderBundle:ShopOrder")->findBy(array('partner' => $user->getPartner()->getId()));
            $shopRejectedOrders = $em->getRepository("OrderBundle:ShopOrder")->findBy(array('partner' => $user->getPartner()->getId(),
                                                                                            'action'  => 'rejected'));
        }elseif ($role[0]->getRole() == "ROLE_ADMIN"){
            $shopOrders         = $em->getRepository("OrderBundle:ShopOrder")->findAll();
            $shopRejectedOrders = $em->getRepository("OrderBundle:ShopOrder")->findByAction('rejected');
        }

        //WORKSHOP ORDERS :segun el rol puede ver sus talleres o todos los que haya
        if ($role[0]->getRole() == "ROLE_AD"){
            $workshopOrders         = $em->getRepository("OrderBundle:WorkshopOrder")->findBy(array('partner' => $user->getPartner()->getId()));
            $workshopRejectedOrders = $em->getRepository("OrderBundle:WorkshopOrder")->findBy(array('partner' => $user->getPartner()->getId(),
                                                                                                    'action'  => 'rejected'));
        }elseif ($role[0]->getRole() == "ROLE_ADMIN"){
            $workshopOrders         = $em->getRepository("OrderBundle:WorkshopOrder")->findAll();
            $workshopRejectedOrders = $em->getRepository("OrderBundle:WorkshopOrder")->findByAction('rejected');
        }

        //eliminamos de la lista los que ya tenemos en la lista de rechazados
        foreach ($shopOrders as $key => $shopOrder) {

            if (in_array($shopOrder, $shopRejectedOrders)){
                unset($shopOrders[$key]);
            }
        }
        foreach ($workshopOrders as $key => $workshopOrder) {

            if (in_array($workshopOrder, $workshopRejectedOrders)){
                unset($workshopOrders[$key]);
            }
        }

        //casos que todas esten en rechazadas... (el "unset" de todos los elementos elimina el array....)
        if (count($workshopOrders) <= 0 )  $workshopOrders = array();
        if (count($shopOrders    ) <= 0 )  $shopOrders     = array();

        //creamos arrays de los valores anteriores a la modificacion/rechazo de la solicitud
        $ordersBefore = ShopOrderController::getShopOrdersBefore($em, $shopOrders);
        $ordersBefore = WorkshopOrderController::getWorkshopOrdersBefore($em, $workshopOrders);

        return $this->render('OrderBundle:Order:list_orders.html.twig', array(  'shopOrders'             => $shopOrders,
	                                                                            'shopRejectedOrders'     => $shopRejectedOrders,
	                                                                            'workshopOrders'         => $workshopOrders,
	                                                                            'workshopRejectedOrders' => $workshopRejectedOrders,
	                                                                            'ordersBefore'           => $ordersBefore,
	                                                                            'user'                   => $user));
    }
}
