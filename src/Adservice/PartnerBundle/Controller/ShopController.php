<?php

namespace Adservice\PartnerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Adservice\PartnerBundle\Entity\Partner;
use Adservice\PartnerBundle\Form\PartnerType;
use Adservice\PartnerBundle\Entity\Shop;
use Adservice\PartnerBundle\Form\ShopType;
use Adservice\UtilBundle\Entity\Region;
use Adservice\UtilBundle\Entity\Pagination;
use Adservice\UtilBundle\Controller\UtilController as UtilController;

class ShopController extends Controller {

    /**
     * Listado de todas las tiendas de la bbdd
     * @throws AccessDeniedException
     */
    public function listAction($page=1, $country='none', $partner='none') {

        if ($this->get('security.context')->isGranted('ROLE_AD') === false) {
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getEntityManager();

        if($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            if ($country != 'none') $params[] = array('country', ' = '.$country);
            else                    $params[] = array();
        }
        else $params[] = array('country', ' = '.$this->get('security.context')->getToken()->getUser()->getCountry()->getId());

        $pagination = new Pagination($page);

        $shops  = $pagination->getRows($em, 'PartnerBundle', 'Shop', $params, $pagination);

        $length = $pagination->getRowsLength($em, 'PartnerBundle', 'Shop', $params);

        $pagination->setTotalPagByLength($length);

        if($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) $countries = $em->getRepository('UtilBundle:Country')->findAll();
        else $countries = array();

        return $this->render('PartnerBundle:Shop:list_shops.html.twig', array(  'shops'      => $shops,
                                                                                'pagination' => $pagination,
                                                                                'countries'  => $countries,
                                                                                'country'    => $country,));
    }

    /**
     * Crea una tienda en la bbdd
     * @throws AccessDeniedException
     */
    public function newShopAction() {
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false){
            throw new AccessDeniedException();
        }
        $em      = $this->getDoctrine()->getEntityManager();
        $shop    = new Shop();
        $request = $this->getRequest();
        $form    = $this->createForm(new ShopType(), $shop);

        if ($request->getMethod() == 'POST') {

            $form->bindRequest($request);

            if ($form->isValid()) {
                $user = $this->get('security.context')->getToken()->getUser();
                $shop = UtilController::newEntity($shop, $user );
                UtilController::saveEntity($em, $shop, $user);

                return $this->redirect($this->generateUrl('shop_list'));
            }
        }
        return $this->render('PartnerBundle:Shop:new_shop.html.twig', array('shop'      => $shop,
                                                                            'form_name' => $form->getName(),
                                                                            'form'      => $form->createView()));
    }

     /**
     * Obtener los datos de la tienda a partir de su ID para poder editarlo
     * Si la petición es GET  --> mostrar el formulario
     * Si la petición es POST --> save del formulario
     */
    public function editShopAction($id){
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false){
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $shop = $em->getRepository("PartnerBundle:Shop")->find($id);

        if (!$shop) throw $this->createNotFoundException('Shop no encontrado en la BBDD');

        $petition = $this->getRequest();
        $form = $this->createForm(new ShopType(), $shop);

        if ($petition->getMethod() == 'POST') {
            $form->bindRequest($petition);

            if ($form->isValid())
            {
                UtilController::saveEntity($em, $shop, $this->get('security.context')->getToken()->getUser());
                return $this->redirect($this->generateUrl('shop_list'));
            }
        }

        return $this->render('PartnerBundle:Shop:edit_shop.html.twig', array('shop'       => $shop,
                                                                             'form_name'  => $form->getName(),
                                                                             'form'       => $form->createView()));
    }

    /**
     * Elimina la tienda con $id de la bbdd
     * @param Int $id
     * @throws AccessDeniedException
     * @throws CreateNotFoundException
     */
    public function deleteShopAction($id){

        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false){
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getEntityManager();
        $shop = $em->getRepository("PartnerBundle:Shop")->find($id);
        if (!$shop) throw $this->createNotFoundException('Tienda no encontrada en la BBDD');

        $em->remove($shop);
        $em->flush();

        return $this->redirect($this->generateUrl('shop_list'));
    }
}
