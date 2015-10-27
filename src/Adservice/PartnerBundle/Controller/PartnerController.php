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
use Adservice\UserBundle\Entity\User;
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

            if ($form->isValid()) {

                /*CHECK CODE PARTNER NO SE REPITA*/
                $find = $em->getRepository("PartnerBundle:Partner")->findOneBy(array('code_partner' => $partner->getCodePartner()));
                if($find == null)
                {
                    $partner = UtilController::newEntity($partner, $security->getToken()->getUser());
                    $partner = UtilController::settersContact($partner, $partner);

                    /*CREAR USERNAME Y EVITAR REPETICIONES*/
                    $username = UtilController::getUsernameUnused($em, $partner->getName());

                    /*CREAR PASSWORD AUTOMATICAMENTE*/
                    $pass = substr( md5(microtime()), 1, 8);

                    $role = $em->getRepository('UserBundle:Role')->findOneByName('ROLE_AD');
                    $lang = $em->getRepository('UtilBundle:Language')->findOneByLanguage($partner->getCountry()->getLang());

                    $newUser = UtilController::newEntity(new User(), $security->getToken()->getUser());
                    $newUser->setUsername      ($username);
                    $newUser->setPassword      ($pass);
                    $newUser->setName          ($partner->getName());
                    $newUser->setSurname       ($this->get('translator')->trans('partner'));
                    $newUser->setActive        ('1');
                    $newUser->setCreatedBy     ($partner->getCreatedBy());
                    $newUser->setCreatedAt     (new \DateTime());
                    $newUser->setModifiedBy    ($partner->getCreatedBy());
                    $newUser->setModifiedAt    (new \DateTime());
                    $newUser->setLanguage      ($lang);
                    $newUser->setPartner       ($partner);
                    $newUser->addRole          ($role);

                    $newUser = UtilController::settersContact($newUser, $partner);

                    //password nuevo, se codifica con el nuevo salt
                    $encoder = $this->container->get('security.encoder_factory')->getEncoder($newUser);
                    $salt = md5(time());
                    $password = $encoder->encodePassword($newUser->getPassword(), $salt);
                    $newUser->setPassword($password);
                    $newUser->setSalt($salt);

                    UtilController::saveEntity($em, $partner, $security->getToken()->getUser());
                    UtilController::saveEntity($em, $newUser, $this->get('security.context')->getToken()->getUser());

                    $flash =  $this->get('translator')->trans('create').' '.$this->get('translator')->trans('partner').': '.$username.' '.$this->get('translator')->trans('with_password').': '.$pass;
                    $this->get('session')->setFlash('alert', $flash);

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
            return $this->render('TwigBundle:Exception:exception_access.html.twig');
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

            if ($form->isValid()) {

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
