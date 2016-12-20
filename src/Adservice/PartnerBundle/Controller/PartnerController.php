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
    public function listAction($page=1, $country='0', $catserv=0, $term='0', $field='0') {

        $security = $this->get('security.context');
        if ($security->isGranted('ROLE_ADMIN') === false) {
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getEntityManager();
        $params = array();
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
        $cat_services = array();
        if($security->isGranted('ROLE_SUPER_ADMIN')) {

            $cat_services = $em->getRepository("UserBundle:CategoryService")->findAll();
            if ($country != '0') $params[] = array('country', ' = '.$country);
        }
        else{
             $cat_services[] =  $this->get('security.context')->getToken()->getUser()->getCategoryService();
            $params[] = array('country', ' = '.$security->getToken()->getUser()->getCountry()->getId());
        }
        $pagination = new Pagination($page);

        if($catserv != 0) {
            $params[] = array('category_service', ' = '.$catserv);
        }

        $partners = $pagination->getRows($em, 'PartnerBundle', 'Partner', $params, $pagination);
        $length   = $pagination->getRowsLength($em, 'PartnerBundle', 'Partner', $params);

        $pagination->setTotalPagByLength($length);

        if($security->isGranted('ROLE_SUPER_ADMIN')) $countries = $em->getRepository('UtilBundle:Country')->findAll();
        else $countries = array();

        return $this->render('PartnerBundle:Partner:list_partners.html.twig', array('all_partners' => $partners,
                                                                                    'pagination'   => $pagination,
                                                                                    'countries'    => $countries,
                                                                                    'country'      => $country,
                                                                                    'cat_services' => $cat_services,
                                                                                    'catserv'      => $catserv,
                                                                                    'term'         => $term,
                                                                                    'field'        => $field
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
        $cat_services = array();
        // Creamos variables de sesion para fitlrar los resultados del formulario
        if ($security->isGranted('ROLE_SUPER_ADMIN')) {
            $cat_services = $em->getRepository("UserBundle:CategoryService")->findAll();
            $_SESSION['id_country'] = ' != 0 ';

        }elseif ($security->isGranted('ROLE_SUPER_AD')) {
            $cat_services[] =  $this->get('security.context')->getToken()->getUser()->getCategoryService();
            $_SESSION['id_country'] = ' = '.$security->getToken()->getUser()->getCountry()->getId();

        }else {
            $cat_services[] =  $this->get('security.context')->getToken()->getUser()->getCategoryService();
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

                    $id_catserv = $request->request->get('id_catserv');
                    $catserv = $em->getRepository('UserBundle:CategoryService')->find($id_catserv);

                    $newUser = UtilController::newEntity(new User(), $security->getToken()->getUser());
                    $newUser->setUsername      ($username);
                    $newUser->setPassword      ($pass);
                    $newUser->setName          ($partner->getContact());
                    $newUser->setSurname       ($this->get('translator')->trans('partner'));
                    $newUser->setActive        ('1');
                    $newUser->setCreatedBy     ($partner->getCreatedBy());
                    $newUser->setCreatedAt     (new \DateTime());
                    $newUser->setModifiedBy    ($partner->getCreatedBy());
                    $newUser->setModifiedAt    (new \DateTime());
                    $newUser->setLanguage      ($lang);
                    $newUser->setPartner       ($partner);
                    $newUser->addRole          ($role);
                    $newUser->setAllowList     (1);
                    $newUser->setAllowCreate   (1);
                    $newUser->setAllowOrder    (1);

                    if($catserv != null){
                        $partner->setCategoryService($catserv);
                        $newUser->setCategoryService($catserv);
                    }


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

        $regions      = $em->getRepository("UtilBundle:Region")->findBy(array('country' => '1'));
        $cities       = $em->getRepository("UtilBundle:City"  )->findAll();
//        $cat_services = $em->getRepository("UserBundle:CategoryService")->findAll();
        return $this->render('PartnerBundle:Partner:new_partner.html.twig', array('partner'      => $partner,
                                                                                  'form_name'    => $form->getName(),
                                                                                  'form'         => $form->createView(),
                                                                                  'regions'      => $regions,
                                                                                  'cities'       => $cities,
                                                                                  'cat_services' => $cat_services,
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
                    $user_partner = $em->getRepository('UserBundle:User')->findOneByPartner($partner);
                    if($user_partner != null) {
                        $user_partner = UtilController::saveUserFromWorkshop($partner,$user_partner);
                        $user_partner->setName($partner->getContact());
                        $user_partner->setActive($partner->getActive());
                        UtilController::saveEntity($em, $user_partner, $this->get('security.context')->getToken()->getUser());
                    }
                    UtilController::saveEntity($em, $partner, $this->get('security.context')->getToken()->getUser());
                    return $this->redirect($this->generateUrl('partner_list'));
                }
            }
        }
        $catserv = $partner->getCategoryService()->getCategoryService();

        return $this->render('PartnerBundle:Partner:edit_partner.html.twig', array('partner'    => $partner,
                                                                                   'catserv'    => $catserv,
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

    /**
     * Listado de todos los usuarios socios de la bbdd
     * @throws AccessDeniedException
     */
    // public function userPartnerlistAction($page=1, $country='0', $term='0', $field='0') {

    //     $security = $this->get('security.context');
    //     if ($security->isGranted('ROLE_TOP_AD') === false) {
    //         throw new AccessDeniedException();
    //     }
    //     $em = $this->getDoctrine()->getEntityManager();
    //     $params = array();
    //     if ($term != '0' and $field != '0'){

    //         if ($term == 'tel') {
    //             $params[] = array('phone_number_1', " != '0' AND (e.phone_number_1 LIKE '%".$field."%' OR e.phone_number_2 LIKE '%".$field."%' OR e.mobile_number_1 LIKE '%".$field."%' OR e.mobile_number_2 LIKE '%".$field."%') ");
    //         }
    //         elseif($term == 'mail'){
    //             $params[] = array('email_1', " != '0' AND (e.email_1 LIKE '%".$field."%' OR e.email_2 LIKE '%".$field."%') ");
    //         }
    //         elseif($term == 'name'){
    //             $params[] = array($term, " LIKE '%".$field."%'");
    //         }
    //     }

    //     if($country != '0'){
    //         $params[] = array('country', ' = '.$country);
    //     }
    //     $params[] = array('partner', ' > 0 ');
    //     $params[] = array('category_service', ' = '.$security->getToken()->getUser()->getCategoryService()->getId());
    //     $pagination = new Pagination($page);



    //     $partners = $pagination->getRows($em, 'UserBundle', 'User', $params, $pagination);
    //     $length   = $pagination->getRowsLength($em, 'UserBundle', 'User', $params);

    //     $pagination->setTotalPagByLength($length);

    //     $countries = $em->getRepository('UtilBundle:Country')->findAll();

    //     return $this->render('PartnerBundle:Partner:list_user_partners.html.twig', array('all_partners' => $partners,
    //                                                                                 'pagination'   => $pagination,
    //                                                                                 'countries'    => $countries,
    //                                                                                 'country'      => $country,
    //                                                                                 'term'         => $term,
    //                                                                                 'field'        => $field,
    //                                                                                 'role'         => 'partner'
    //                                                                                 ));
    // }

     /**
     * Listado de todos los usuarios socios de la bbdd
     * @throws AccessDeniedException
     */
    // public function userSuperPartnerlistAction($page=1, $country='0', $term='0', $field='0') {

    //     $security = $this->get('security.context');
    //     if ($security->isGranted('ROLE_TOP_AD') === false) {
    //         throw new AccessDeniedException();
    //     }
    //     $em = $this->getDoctrine()->getEntityManager();
    //     $params = array();
    //     if ($term != '0' and $field != '0'){

    //         if ($term == 'tel') {
    //             $params[] = array('phone_number_1', " != '0' AND (e.phone_number_1 LIKE '%".$field."%' OR e.phone_number_2 LIKE '%".$field."%' OR e.mobile_number_1 LIKE '%".$field."%' OR e.mobile_number_2 LIKE '%".$field."%') ");
    //         }
    //         elseif($term == 'mail'){
    //             $params[] = array('email_1', " != '0' AND (e.email_1 LIKE '%".$field."%' OR e.email_2 LIKE '%".$field."%') ");
    //         }
    //         elseif($term == 'name'){
    //             $params[] = array($term, " LIKE '%".$field."%'");
    //         }
    //     }

    //     if($country != '0'){
    //         $params[] = array('country', ' = '.$country);
    //     }
    //     $params[] = array('category_service', ' = '.$security->getToken()->getUser()->getCategoryService()->getId());
    //     $pagination = new Pagination($page);

    //     $partners = $pagination->getRows($em, 'UserBundle', 'User', $params, $pagination);
    //     $users_role_super_ad=array();
    //     foreach ($partners as $user) {

    //         $role     = $user->getRoles();
    //         $role     = $role[0];
    //         $role     = $role->getName();
    //         if ($role == "ROLE_SUPER_AD")     $users_role_super_ad[]    = $user;

    //     }
    //     $length   = $pagination->getRowsLength($em, 'UserBundle', 'User', $params);

    //     $pagination->setTotalPagByLength($length);

    //     $countries = $em->getRepository('UtilBundle:Country')->findAll();

    //     return $this->render('PartnerBundle:Partner:list_user_super_partners.html.twig', array('all_partners' => $users_role_super_ad,
    //                                                                                 'pagination'   => $pagination,
    //                                                                                 'countries'    => $countries,
    //                                                                                 'country'      => $country,
    //                                                                                 'term'         => $term,
    //                                                                                 'field'        => $field,
    //                                                                                 'role'         => 'super_partner'
    //                                                                                 ));
    // }

    public function activeDeactiveListAction($user_id, $permission, $page, $country, $option, $term, $field){
        $em = $this->getDoctrine()->getEntityManager();
        $user = $em->getRepository('UserBundle:User')->findOneById($user_id);

        if($user){
            switch($permission){
                case 'list': $user->setAllowList(!$user->getAllowList()); break;
                case 'create': $user->setAllowCreate(!$user->getAllowCreate()); break;
                case 'order': $user->setAllowOrder(!$user->getAlloworder()); break;
            }

            $user->setModifiedAt(new \DateTime(\date("Y-m-d H:i:s")));
            $user->setModifiedBy($this->get('security.context')->getToken()->getUser());
        }
        $em->persist($user);
        $em->flush();

        return $this->redirect($this->generateUrl('user_partner_list', array('page'   => $page,
                                                                             'country'=> $country,
                                                                             'option' => $option,
                                                                             'term'   => $term,
                                                                             'field'  => $field,
                                                                        )));
    }
}
