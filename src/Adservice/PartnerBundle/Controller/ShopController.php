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
    public function listAction($page=1, $country='none', $partner='none') {

        $security = $this->get('security.context');
        if ($security->isGranted('ROLE_AD') === false) {
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getEntityManager();

        $dql = "SELECT e FROM PartnerBundle:Shop e WHERE e.id > 0 AND e.name != '...' ";

        if($security->isGranted('ROLE_SUPER_ADMIN')) {
            if ($country != 'none') $dql .=' AND e.country = '.$country.' ';
        }
        else $dql .=' AND e.country = '.$security->getToken()->getUser()->getCountry()->getId().' ';

        if ($partner != 'none') $dql .=' AND e.partner = '.$partner.' ';

        $pagination = new Pagination($page, $em, $dql);

        $shops = $pagination->getResult();

        if($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) $countries = $em->getRepository('UtilBundle:Country')->findAll();
        else $countries = array();

        if($country != 'none') $country_name = $em->getRepository('UtilBundle:Country')->find($country)->getCountry();
        else                   $country_name = 'none';

        if($partner != 'none') $partner_name = $em->getRepository('UtilBundle:Partner')->find($partner)->getCountry();
        else                   $partner_name = 'none';

        return $this->render('PartnerBundle:Shop:list_shops.html.twig', array(  'shops'        => $shops,
                                                                                'pagination'   => $pagination,
                                                                                'countries'    => $countries,
                                                                                'country'      => $country,
                                                                                'country_name' => $country_name,
                                                                                'partner_name' => $partner_name,
                                                                                ));
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
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false){
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();
        $form = $this->createForm(new ShopType(), $shop);

        $actual_city   = $shop->getRegion();
        $actual_region = $shop->getCity();

        if ($petition->getMethod() == 'POST') {
            $form->bindRequest($petition);

        //La segunda comparacion ($form->getErrors()...) se hizo porque el request que reciber $form puede ser demasiado largo y hace que la funcion isValid() devuelva false
            $form_errors = $form->getErrors();
	    if(isset($form_errors[0])) {
                $form_errors = $form_errors[0];
                $form_errors = $form_errors->getMessageTemplate();
            }else{
                $form_errors = 'none';
            }
            if ($form->isValid() or $form_errors == 'The uploaded file was too large. Please try to upload a smaller file') {

                $shop = UtilController::settersContact($shop, $shop, $actual_region, $actual_city);
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
