<?php

namespace Adservice\PartnerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Adservice\PartnerBundle\Form\PartnerType;
use Adservice\PartnerBundle\Entity\Partner;
use Adservice\PartnerBundle\Entity\Shop;
use Adservice\UtilBundle\Entity\Region;
use Adservice\UtilBundle\Entity\Pagination;
use Adservice\UtilBundle\Controller\UtilController as UtilController;

class PartnerController extends Controller {

    /**
     * Listado de todos los socios de la bbdd
     * @throws AccessDeniedException
     */
    public function listAction($page=1, $country='none') {

        $security = $this->get('security.context');
        if ($security->isGranted('ROLE_ADMIN') === false) {
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getEntityManager();

        if($security->isGranted('ROLE_SUPER_ADMIN')) {
            if ($country != 'none') $params[] = array('country', ' = '.$country);
            else                    $params[] = array();
        }
        else $params[] = array('country', ' = '.$security->getToken()->getUser()->getCountry()->getId());

        $pagination = new Pagination($page);

        $partners = $pagination->getRows($em, 'PartnerBundle', 'Partner', $params, $pagination);

        $length = $pagination->getRowsLength($em, 'PartnerBundle', 'Partner', $params);

        $pagination->setTotalPagByLength($length);

        if($security->isGranted('ROLE_SUPER_ADMIN')) $countries = $em->getRepository('UtilBundle:Country')->findAll();
        else $countries = array();

        return $this->render('PartnerBundle:Partner:list_partners.html.twig', array('all_partners' => $partners,
                                                                                    'pagination'   => $pagination,
                                                                                    'countries'    => $countries,
                                                                                    'country'      => $country,
                                                                                    ));
    }
    /**
     * Crea un socio en la bbdd
     * @throws AccessDeniedException
     */
    public function newPartnerAction() {
        $security = $this->get('security.context');
        if ($security->isGranted('ROLE_ADMIN') === false){
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getEntityManager();
        $partner = new Partner();
        $request = $this->getRequest();
       
        // Creamos variables de sesion para fitlrar los resultados del formulario
        if ($security->isGranted('ROLE_SUPER_ADMIN')) {

            $_SESSION['id_country'] = ' != 0 ';

        }elseif ($security->isGranted('ROLE_SUPER_AD')) {
            $_SESSION['id_country'] = ' = '.$security->getToken()->getUser()->getCountry()->getId();

        }else {
            $_SESSION['id_country'] = ' = '.$partner->getCountry()->getId();
        }
        
        $form = $this->createForm(new PartnerType(), $partner);

        if ($request->getMethod() == 'POST') {

            $form->bindRequest($request);
            $code = UtilController::getCodePartnerUnused($em);

            //La segunda comparacion ($form->getErrors()...) se hizo porque el request que reciber $form puede ser demasiado largo y hace que la funcion isValid() devuelva false
            $form_errors = $form->getErrors();
	    if(isset($form_errors[0])) {
                $form_errors = $form_errors[0];
                $form_errors = $form_errors->getMessageTemplate();
            }else{
                $form_errors = 'none';
            }
            if ($form->isValid() or $form_errors == 'The uploaded file was too large. Please try to upload a smaller file') {

                /*CHECK CODE PARTNER NO SE REPITA*/
                $find = $em->getRepository("PartnerBundle:Partner")->findOneBy(array('code_partner' => $partner->getCodePartner()));
                if($find == null)
                {
                    $partner = UtilController::newEntity($partner, $security->getToken()->getUser());
                    $partner = UtilController::settersContact($partner, $partner);
                    UtilController::saveEntity($em, $partner, $security->getToken()->getUser());

                    /* SHOP 'SIN TIENDA' PARA EL PARTNER*/
                    $newShop = UtilController::newEntity(new Shop(), $security->getToken()->getUser());
                    $newShop->setName('...');
                    $newShop->setPartner($partner);
                    $newShop->setActive('1');
                    $newShop = UtilController::settersContact($newShop, $partner);

                    UtilController::saveEntity($em, $newShop, $security->getToken()->getUser());

                    return $this->redirect($this->generateUrl('partner_list'));
                }
                else{
                    $flash = $this->get('translator')->trans('error.code_partner.used').$code;
                    $this->get('session')->setFlash('error', $flash);
                }
            }
        }
        else{
            $partner->setCodePartner(UtilController::getCodePartnerUnused($em));
            $flash = $this->get('translator')->trans('error.first_number').$partner->getCodePartner();
            $this->get('session')->setFlash('info', $flash);
        }

        $regions = $em->getRepository("UtilBundle:Region")->findBy(array('country' => '1'));
        $cities  = $em->getRepository("UtilBundle:City"  )->findAll();

        return $this->render('PartnerBundle:Partner:new_partner.html.twig', array('partner'   => $partner,
                                                                                  'form_name' => $form->getName(),
                                                                                  'form'      => $form->createView(),
                                                                                  'regions'   => $regions,
                                                                                  'cities'    => $cities,
                                                                                 ));
    }

    /**
     * Obtener los datos del partner a partir de us ID para poder editarlo (solo lo puede hacer el ROLE_ADMIN)
     * Si la petición es GET  --> mostrar el formulario
     * Si la petición es POST --> save del formulario
     * @Route("/edit/partner/{id}")
     * @ParamConverter("partner", class="PartnerBundle:Partner")
     * @return type
     */
    public function editPartnerAction($partner){
        $security = $this->get('security.context');
        if ((($security->isGranted('ROLE_ADMIN') and $security->getToken()->getUser()->getCountry()->getId() == $partner->getCountry()->getId()) === false)
        and (!$security->isGranted('ROLE_SUPER_ADMIN'))) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();

        $petition = $this->getRequest();
        // Creamos variables de sesion para fitlrar los resultados del formulario
        if ($security->isGranted('ROLE_SUPER_ADMIN')) {

            $_SESSION['id_country'] = ' != 0 ';

        }elseif ($security->isGranted('ROLE_SUPER_AD')) {
            $_SESSION['id_country'] = ' = '.$security->getToken()->getUser()->getCountry()->getId();

        }else {
            $_SESSION['id_country'] = ' = '.$partner->getCountry()->getId();
        }
        $form = $this->createForm(new PartnerType(), $partner);

        $actual_city   = $partner->getRegion();
        $actual_region = $partner->getCity();

        if ($petition->getMethod() == 'POST') {

            $last_code = $partner->getCodePartner();
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

                /*CHECK CODE PARTNER NO SE REPITA*/
                $code = UtilController::getCodePartnerUnused($em, $partner->getCodePartner());
                if($code != $partner->getCodePartner() and $last_code != $partner->getCodePartner())
                {
                    $flash = $this->get('translator')->trans('error.code_partner.used').$code.' ('.$last_code.').';
                    $this->get('session')->setFlash('error', $flash);
                }
                else{
                    $partner = UtilController::settersContact($partner, $partner, $actual_region, $actual_city);
                    UtilController::saveEntity($em, $partner, $this->get('security.context')->getToken()->getUser());
                    return $this->redirect($this->generateUrl('partner_list'));
                }
            }
        }

        return $this->render('PartnerBundle:Partner:edit_partner.html.twig', array('partner'    => $partner,
                                                                                   'form_name'  => $form->getName(),
                                                                                   'form'       => $form->createView()));
    }

    /**
     * Elimina el socio con $id de la bbdd
     * @Route("/delete/partner/{id}")
     * @ParamConverter("partner", class="PartnerBundle:Partner")
     * @throws AccessDeniedException
     * @throws CreateNotFoundException
     */
    public function deletePartnerAction($partner){

        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false){
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($partner);
        $em->flush();

        return $this->redirect($this->generateUrl('partner_list'));
    }
}
