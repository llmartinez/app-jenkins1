<?php

namespace Adservice\PartnerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
                                                                                    'country'      => $country,));
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
        $form = $this->createForm(new PartnerType(), $partner);

        if ($request->getMethod() == 'POST') {

            $form->bindRequest($request);
            $code = UtilController::getCodePartnerUnused($em);

            //La segunda comparacion ($form->getErrors()...) se hizo porque el request que reciber $form puede ser demasiado largo y hace que la funcion isValid() devuelva false
            if ($form->isValid() or $form->getErrors()[0]->getMessageTemplate() == 'The uploaded file was too large. Please try to upload a smaller file') {

                /*CHECK CODE PARTNER NO SE REPITA*/
                $find = $em->getRepository("PartnerBundle:Partner")->findOneBy(array('code_partner' => $partner->getCodePartner()));
                if($find == null)
                {
                    $partner = UtilController::newEntity($partner, $security->getToken()->getUser());
                    UtilController::saveEntity($em, $partner, $security->getToken()->getUser());

                    /* SHOP 'SIN TIENDA' PARA EL PARTNER*/
                    $newShop = UtilController::newEntity(new Shop(), $security->getToken()->getUser());
                    $newShop->setName('...');
                    $newShop->setPartner($partner);
                    $newShop->setActive('1');
                    $newShop->setCountry       ($partner->getCountry());
                    $newShop->setRegion        ($partner->getRegion());
                    $newShop->setCity          ($partner->getCity());
                    $newShop->setPhoneNumber1  ($partner->getPhoneNumber1());
                    $newShop->setPhoneNumber2  ($partner->getPhoneNumber2());
                    $newShop->setMovileNumber1 ($partner->getMovileNumber1());
                    $newShop->setMovileNumber2 ($partner->getMovileNumber2());
                    $newShop->setFax           ($partner->getFax());
                    $newShop->setEmail1        ($partner->getEmail1());
                    $newShop->setEmail2        ($partner->getEmail2());

                    UtilController::saveEntity($em, $newShop, $security->getToken()->getUser());

                    return $this->redirect($this->generateUrl('partner_list'));
                }
                else{
                    $flash = 'El codigo de Socio ya esta en uso, el primer numero disponible es: '.$code;
                    $this->get('session')->setFlash('error', $flash);
                }
            }
        }
        else{
            $partner->setCodePartner(UtilController::getCodePartnerUnused($em));
            $flash = 'El primer numero disponible es: '.$partner->getCodePartner();
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
     */
    public function editPartnerAction($id){
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false){
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $partner = $em->getRepository("PartnerBundle:Partner")->find($id);

        if (!$partner) throw $this->createNotFoundException('Partner no encontrado en la BBDD');

        $petition = $this->getRequest();
        $form = $this->createForm(new PartnerType(), $partner);


        if ($petition->getMethod() == 'POST') {

            $last_code = $partner->getCodePartner();
            $form->bindRequest($petition);

        //La segunda comparacion ($form->getErrors()...) se hizo porque el request que reciber $form puede ser demasiado largo y hace que la funcion isValid() devuelva false
        if ($form->isValid() or $form->getErrors()[0]->getMessageTemplate() == 'The uploaded file was too large. Please try to upload a smaller file') {

                /*CHECK CODE PARTNER NO SE REPITA*/
                $code = UtilController::getCodePartnerUnused($em, $partner->getCodePartner());
                if($code != $partner->getCodePartner() and $last_code != $partner->getCodePartner())
                {
                    $flash = 'El codigo de Socio ya esta en uso, el primer numero disponible es: '.$code.' (valor actual '.$last_code.').';
                    $this->get('session')->setFlash('error', $flash);
                }
                else{
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
     * @param Int $id
     * @throws AccessDeniedException
     * @throws CreateNotFoundException
     */
    public function deletePartnerAction($id){

        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false){
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getEntityManager();
        $partner = $em->getRepository("PartnerBundle:Partner")->find($id);
        if (!$partner) throw $this->createNotFoundException('Partner no encontrado en la BBDD');

        $em->remove($partner);
        $em->flush();

        return $this->redirect($this->generateUrl('partner_list'));
    }
}