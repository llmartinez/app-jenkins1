<?php

namespace Adservice\PartnerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
    public function listAction($page=1, $country='0', $catserv=0, $partner='0', $term='0', $field='0') {

        $security = $this->get('security.context');
        if ($security->isGranted('ROLE_AD') === false) {
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getEntityManager();
        $params[] = array("name", " != '...' "); //Evita listar las tiendas por defecto de los socios (Tiendas con nombre '...')

        if ($term != '0' and $field != '0'){

            if ($term == 'tel') {
                $params[] = array('phone_number_1', " != '0' AND (e.phone_number_1 LIKE '%".$field."%' OR e.phone_number_2 LIKE '%".$field."%' OR e.mobile_number_1 LIKE '%".$field."%' OR e.mobile_number_2 LIKE '%".$field."%') ");
            }
            elseif($term == 'mail'){
                $params[] = array('email_1', " != '0' AND (e.email_1 LIKE '%".$field."%' OR e.email_2 LIKE '%".$field."%') ");
            }
            elseif($term == 'name'){
                $params[] = array($term, " LIKE '%".$field."%'");
            }
            elseif($term == 'cif'){
                $params[] = array($term, " LIKE '%".$field."%'");
            }
        }
        if($security->isGranted('ROLE_SUPER_ADMIN')) {
            if ($country != '0' && $country != 'none') $params[] = array('country', ' = '.$country);
            if ($partner != 'none' && $partner != '0' ) $params[] = array('partner', ' = '.$partner);
        }
        else {
            $params[] = array('country', ' = '.$security->getToken()->getUser()->getCountry()->getId());
        }
        if($catserv != 0){
            $params[] = array('category_service', ' = '.$catserv);
        }
        $pagination = new Pagination($page);

        $shops  = $pagination->getRows($em, 'PartnerBundle', 'Shop', $params, $pagination);

        $length = $pagination->getRowsLength($em, 'PartnerBundle', 'Shop', $params);

        $pagination->setTotalPagByLength($length);

        if($security->isGranted('ROLE_SUPER_ADMIN')) $countries = $em->getRepository('UtilBundle:Country')->findAll();
        else $countries = array();

        if($security->isGranted('ROLE_SUPER_ADMIN')) {
            if($catserv != 0) $partners = $em->getRepository('PartnerBundle:Partner')->findBy(array('category_service' => $catserv));
            else              $partners = $em->getRepository('PartnerBundle:Partner')->findAll();
        }
        else $partners = array();

        $cat_services = $em->getRepository("UserBundle:CategoryService")->findAll();

        return $this->render('PartnerBundle:Shop:list_shops.html.twig', array(  'shops'        => $shops,
                                                                                'pagination'   => $pagination,
                                                                                'countries'    => $countries,
                                                                                'country'      => $country,
                                                                                'partners'     => $partners,
                                                                                'partner'      => $partner,
                                                                                'cat_services' => $cat_services,
                                                                                'catserv'      => $catserv,
                                                                                'term'         => $term,
                                                                                'field'        => $field
                                                                                ));
    }

    /**
     * Crea una tienda en la bbdd
     * @throws AccessDeniedException
     */
    public function newShopAction() {
        $security = $this->get('security.context');
        if ($security->isGranted('ROLE_ADMIN') === false){
            throw new AccessDeniedException();
        }
        $em      = $this->getDoctrine()->getEntityManager();
        $shop    = new Shop();
        $request = $this->getRequest();

        if ($security->isGranted('ROLE_SUPER_AD')) {

            $partners = $em->getRepository("PartnerBundle:Partner")->findBy(array('country' => $security->getToken()->getUser()->getCountry()->getId(),
                                                                                    'active' => '1'));
        }
        else $partners = '0';

        // Creamos variables de sesion para fitlrar los resultados del formulario
        if ($security->isGranted('ROLE_SUPER_ADMIN')) {

            $_SESSION['id_country'] = ' != 0 ';

        }elseif ($security->isGranted('ROLE_SUPER_AD')) {

            $partner_ids = '0';
            foreach ($partners as $p) { $partner_ids = $partner_ids.', '.$p->getId(); }

            $_SESSION['id_partner'] = ' IN ('.$partner_ids.')';
            $_SESSION['id_country'] = ' = '.$security->getToken()->getUser()->getCountry()->getId();

        }else {
            $_SESSION['id_partner'] = ' = '.$partner->getId();
            $_SESSION['id_country'] = ' = '.$partner->getCountry()->getId();
        }
        $form    = $this->createForm(new ShopType(), $shop);

        if ($request->getMethod() == 'POST') {

            $form->bindRequest($request);

            if ($form->isValid()) {

                $user = $security->getToken()->getUser();
                $shop = UtilController::newEntity($shop, $user );
                $shop = UtilController::settersContact($shop, $shop);
                UtilController::saveEntity($em, $shop, $user);

                return $this->redirect($this->generateUrl('shop_list'));
            }
        }
        return $this->render('PartnerBundle:Shop:new_shop.html.twig', array('shop'      => $shop,
                                                                            'form_name' => $form->getName(),
                                                                            'form'      => $form->createView()));
    }

    /**
     * Edita una tienda
     * @Route("/edit/shop/{id}")
     * @ParamConverter("shop", class="PartnerBundle:Shop")
     * Si la petición es GET  --> mostrar el formulario
     * Si la petición es POST --> save del formulario
     */
    public function editShopAction($shop){
        $security = $this->get('security.context');
        if ((($security->isGranted('ROLE_ADMIN') and $security->getToken()->getUser()->getCountry()->getId() == $shop->getCountry()->getId()) === false)
        and (!$security->isGranted('ROLE_SUPER_ADMIN'))) {
            return $this->render('TwigBundle:Exception:exception_access.html.twig');
        }

        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();
        if ($security->isGranted('ROLE_SUPER_AD')) {

            $partners = $em->getRepository("PartnerBundle:Partner")->findBy(array('country' => $security->getToken()->getUser()->getCountry()->getId(),
                                                                                    'active' => '1'));
        }
        else $partners = '0';

        // Creamos variables de sesion para fitlrar los resultados del formulario
        if ($security->isGranted('ROLE_SUPER_ADMIN')) {

            $_SESSION['id_country'] = ' != 0 ';

        }elseif ($security->isGranted('ROLE_SUPER_AD')) {

            $partner_ids = '0';
            foreach ($partners as $p) { $partner_ids = $partner_ids.', '.$p->getId(); }

            $_SESSION['id_partner'] = ' IN ('.$partner_ids.')';
            $_SESSION['id_country'] = ' = '.$security->getToken()->getUser()->getCountry()->getId();

        }else {
            $_SESSION['id_partner'] = ' = '.$partner->getId();
            $_SESSION['id_country'] = ' = '.$partner->getCountry()->getId();
        }
        $form = $this->createForm(new ShopType(), $shop);

        $actual_city   = $shop->getRegion();
        $actual_region = $shop->getCity();

        if ($petition->getMethod() == 'POST') {
            $form->bindRequest($petition);

            if ($form->isValid()) {

                $shop = UtilController::settersContact($shop, $shop, $actual_region, $actual_city);
                UtilController::saveEntity($em, $shop, $security->getToken()->getUser());
                return $this->redirect($this->generateUrl('shop_list'));
            }
        }
        $catserv = $shop->getCategoryService();
        return $this->render('PartnerBundle:Shop:edit_shop.html.twig', array('shop'       => $shop,
                                                                             'catserv'    => $catserv,
                                                                             'form_name'  => $form->getName(),
                                                                             'form'       => $form->createView()));
    }

    /**
     * Elimina la tienda con $id de la bbdd
     * @Route("/delete/shop/{id}")
     * @ParamConverter("shop", class="PartnerBundle:Shop")
     * @throws AccessDeniedException
     * @throws CreateNotFoundException
     */
    public function deleteShopAction($shop){

        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false){
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($shop);
        $em->flush();

        return $this->redirect($this->generateUrl('shop_list'));
    }
}
