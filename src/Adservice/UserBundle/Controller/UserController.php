<?php

namespace Adservice\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Adservice\UserBundle\Form\UserAdminAssessorType;
use Adservice\UserBundle\Form\UserAssessorType;
use Adservice\UserBundle\Form\UserSuperPartnerType;
use Adservice\UserBundle\Form\UserPartnerType;
use Adservice\UserBundle\Form\UserCommercialType;
use Adservice\UserBundle\Form\UserWorkshopType;
use Adservice\UserBundle\Form\EditUserAdminAssessorType;
use Adservice\UserBundle\Form\EditUserAssessorType;
use Adservice\UserBundle\Form\EditUserSuperPartnerType;
use Adservice\UserBundle\Form\EditUserPartnerType;
use Adservice\UserBundle\Form\EditCommercialType;
use Adservice\UserBundle\Form\EditUserWorkshopType;

use Adservice\UserBundle\Entity\User;
use Adservice\UtilBundle\Entity\Pagination;
use Adservice\WorkshopBundle\Entity\Workshop;
use Adservice\StatisticBundle\Entity\StatisticRepository;
use Adservice\UtilBundle\Controller\UtilController as UtilController;
use Adservice\UtilBundle\Controller\LanguageController as LanguageController;

class UserController extends Controller {

    /**
     * Welcome function, redirige al html del menu de usuario
     */
    public function indexAction() {
        //  $id_logged_user = $this->get('security.context')->getToken()->getUser()->getId();
        //  $session = $this->getRequest()->getSession();
        //  $session->set('id_logged_user', $id_logged_user);
        $request  = $this->getRequest();
        $locale = $request->getLocale();
        $currentLocale = $request->getLocale();
        $user = $this->get('security.context')->getToken()->getUser();

        if($user == 'anon.') return $this->redirect($this->generateUrl('user_login'));

        if ($this->get('security.context')->isGranted('ROLE_COMMERCIAL')) $length = $this->getPendingOrders();
        else $length = 0;
        // Se pondrá por defecto el idioma del usuario en el primer login

        if(!isset($_SESSION['lang'])) {
            if(isset($user)) {
                $lang   = $user->getLanguage()->getShortName();
                $lang   = substr($lang, 0, strrpos($lang, '_'));
            }
            else{ $lang   = 'es'; }
            $currentLocale = $request->getLocale();
            $request->setLocale($lang);

            if(isset($user)) {
                if($user->getPrivacy() == 0 || $user->getPrivacy() == null ){
                     $currentPath = $this->generateUrl('accept_privacy');
                     $currentPath = str_replace('/'.$currentLocale.'/', '/'.$lang.'/', $currentPath);
                     $_SESSION['lang'] = $lang;
                     return $this->redirect($currentPath);
                }
                // POPUP: Mostramos por flash los popups para el usuario logeado
                    $em   = $this->getDoctrine()->getEntityManager();
                    $now  = new \DateTime('now');
                    $now  = $now->format("Y-m-d H:i:s");

                    $query ="SELECT p.name, p.description FROM PopupBundle:Popup p ".
                            "WHERE p.country = ".$user->getCountry()->getId()." ".
                            "AND p.role = ".$user->getRoles()[0]->getId()." ".
                            "AND p.startdate_at < '".$now."' ".
                            "AND p.enddate_at > '".$now."' ".
                            "AND p.active = 1 ";

                    if($user->getCategoryService() != null) 
                        $query .= "AND p.category_service = ".$user->getCategoryService()->getId()." ";

                    $popups = $em->createQuery($query)->getResult();

                    if(isset($popups)){
                        if(sizeof($popups) > 0) $flash = '**'.$this->get('translator')->trans('info').'**';
                        else $flash = '';

                        foreach ($popups as $popup) {
                            $flash .= '
                            '.$popup['name'].': '.$popup['description'].' ';
                        }
                        if($flash != '') $this->get('session')->setFlash('popup', $flash);
                    }
                //END POPUP
            }


            if (isset($length) and $length != 0) $currentPath = $this->generateUrl('user_index', array('length' => $length));
            elseif (!$this->get('security.context')->isGranted('ROLE_ADMIN') AND !$this->get('security.context')->isGranted('ROLE_COMMERCIAL')){

                if ($this->get('security.context')->isGranted('ROLE_ASSESSOR')) {

                    if($user->getCountryService() != null)
                        $country = $user->getCountryService()->getId();
                    else $country = '0';

                    $currentPath = $this->generateUrl('listTicket', array('page'     => 1,
                                                                          'num_rows' => 10,
                                                                          'country'  => $country,
                                                                          'option'   => 'assessor_pending'));
                }
                elseif($this->get('security.context')->isGranted('ROLE_USER')){

                    $country = $user->getCountry()->getId();

                    if($user->getWorkshop() != null){
                        if((($user->getWorkshop()->getCIF() == null ) || $user->getWorkshop()->getCIF() == "0" ) && $country == 1 ){
                            $currentPath = $this->generateUrl('insert_cif', array('workshop_id'=> $user->getWorkshop()->getId(),
                                                                                  'country'  => $country));
                        }
                        else{
                            $currentPath = $this->generateUrl('listTicket', array('page'     => 1,
                                                                                  'num_rows' => 10,
                                                                                  'country'  => $country));
                        }
                    }
                }else{
                    $country = $user->getCountry()->getId();
                    $currentPath = $this->generateUrl('listTicket', array('page'     => 1,
                                                                          'num_rows' => 10,
                                                                          'country'  => $country));
                }
            }

            else    $currentPath = $this->generateUrl('user_index');


            $currentPath = str_replace('/'.$currentLocale.'/', '/'.$lang.'/', $currentPath);
            $_SESSION['lang'] = $lang;

            return $this->redirect($currentPath);
        }elseif($this->get('security.context')->isGranted('ROLE_USER')){

            $country= $user->getCountry()->getId();

            $lang   = $user->getLanguage()->getShortName();
            $lang   = substr($lang, 0, strrpos($lang, '_'));

            if(isset($user)) {
                if($user->getPrivacy() == 0 || $user->getPrivacy() == null ){
                     $currentPath = $this->generateUrl('accept_privacy');
                     $currentPath = str_replace('/'.$currentLocale.'/', '/'.$lang.'/', $currentPath);
                     $_SESSION['lang'] = $lang;
                     return $this->redirect($currentPath);
                }
            }
            if($user->getWorkshop() != null){

                if((($user->getWorkshop()->getCIF() == null ) || $user->getWorkshop()->getCIF() == "0"  ) && $country == 1 ){
                    $currentPath = $this->generateUrl('insert_cif', array('workshop_id'=> $user->getWorkshop()->getId(),
                                                                          'country'  => $country));

                }
                else{
                    $currentPath = $this->generateUrl('listTicket', array('page'     => 1,
                                                                          'num_rows' => 10,
                                                                          'country'  => $country));
                }
                $currentPath = str_replace('/'.$currentLocale.'/', '/'.$lang.'/', $currentPath);
                $_SESSION['lang'] = $lang;

                return $this->redirect($currentPath);
            }
        }

        return $this->render('UserBundle:User:index.html.twig', array('length' => $length));
    }

    /**
     * Obtener los datos del usuario logueado para verlos
     */
    public function profileAction() {
        $em             = $this->getDoctrine()->getEntityManager();
        $logged_user = $this->get('security.context')->getToken()->getUser();
        if($logged_user == 'anon.') return $this->redirect($this->generateUrl('user_login'));
        $user           = $em->getRepository('UserBundle:User')->find($logged_user->getId());

        if (!$user)
            throw $this->createNotFoundException('Usuario no encontrado en la BBDD');
        return $this->render('UserBundle:User:profile.html.twig', array('user' => $user));
    }

    /**
     * Muestra una pantalla para elegir el tipo de user
     */
    public function selectNewUserAction() {
        return $this->render('UserBundle:User:selectUserType.html.twig');
    }

    /**
     * Recupera los usuarios del socio segun el usuario logeado y tambien recupera todos los usuarios de los talleres del socio
     */
    public function userListAction($page=1, $country=0, $catserv=0, $option='0', $term='0', $field='0') {

        $security = $this->get('security.context');
        if ($security->isGranted('ROLE_ADMIN') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();

        $users_role_super_admin = array();
        $users_role_admin       = array();
        $users_role_assessor    = array();
        $users_role_user        = array();
        $users_role_super_ad    = array();
        $users_role_top_ad      = array();
        $users_role_ad          = array();
        $users_role_commercial  = array();

        $pagination = new Pagination($page);

        if($security->isGranted('ROLE_SUPER_ADMIN')) {
            if($catserv != 0){
                $params[] = array('category_service', ' = '.$catserv);
            }
        }else $params[] = array('category_service', ' = '.$security->getToken()->getUser()->getCategoryService()->getId());

         if($country != 0){
                $params[] = array('country', ' = '.$country);
            }
        if ($term != '0' and $field != '0') {

            if ($term == 'tel') {
                $params[] = array('phone_number_1', " != '0' AND (e.phone_number_1 LIKE '%" . $field . "%' OR e.phone_number_2 LIKE '%" . $field . "%' OR e.mobile_number_1 LIKE '%" . $field . "%' OR e.mobile_number_2 LIKE '%" . $field . "%') ");
            } elseif ($term == 'mail') {
                $params[] = array('email_1', " != '0' AND (e.email_1 LIKE '%" . $field . "%' OR e.email_2 LIKE '%" . $field . "%') ");
            } elseif ($term == 'user') {
                $params[] = array('username', " LIKE '%" . $field . "%'");
            }
        }

        if(!isset($params)) $params[] = array();
        if($option == null or $option == 'all' or $option == 'none' or $option == '0'){
                $users    = $pagination->getRows      ($em, 'UserBundle', 'User', $params, $pagination);
                $length   = $pagination->getRowsLength($em, 'UserBundle', 'User', $params);
                $role_id  = 'none';
        }else{
                $role     = $em->getRepository("UserBundle:Role")->find($option);
                $role_id  = $role->getId();
                $role     = $role->getName();
                $users    = $em->getRepository("UserBundle:User")->findByOption($em, $security, $country, $catserv, $role, $term, $field, $pagination);
                $length   = $em->getRepository("UserBundle:User")->findLengthOption($em, $security, $country, $catserv, $role);
        }

        //separamos los tipos de usuario...
        foreach ($users as $user) {
            // $role = $user->getRoles();
            if ($option == null or $option == 'all' or $option == 'none') {
                $role     = $user->getRoles();
                $role     = $role[0];
                $role     = $role->getName();
            }

            if     ($role == "ROLE_SUPER_ADMIN")  $users_role_super_admin[] = $user;
            elseif ($role == "ROLE_ADMIN")        $users_role_admin[]       = $user;
            elseif ($role == "ROLE_USER")         $users_role_user[]        = $user;
            elseif ($role == "ROLE_ASSESSOR")     $users_role_assessor[]    = $user;
            elseif ($role == "ROLE_SUPER_AD")     $users_role_super_ad[]    = $user;
            elseif ($role == "ROLE_TOP_AD")       $users_role_top_ad[]      = $user;
            elseif ($role == "ROLE_AD")           $users_role_ad[]          = $user;
            elseif ($role == "ROLE_COMMERCIAL")   $users_role_commercial[]  = $user;

            if($option == null or $option == 'all') unset($role);
        }

        $length = $pagination->setTotalPagByLength($length);

        $roles = $em->getRepository("UserBundle:Role")->findAll();
        $countries = $em->getRepository("UtilBundle:Country")->findAll();
        $cat_services = $em->getRepository("UserBundle:CategoryService")->findAll();

        return $this->render('UserBundle:User:list.html.twig', array(   'users_role_super_admin' => $users_role_super_admin,
                                                                        'users_role_admin'       => $users_role_admin,
                                                                        'users_role_user'        => $users_role_user,
                                                                        'users_role_assessor'    => $users_role_assessor,
                                                                        'users_role_super_ad'    => $users_role_super_ad,
                                                                        'users_role_top_ad'      => $users_role_top_ad,
                                                                        'users_role_ad'          => $users_role_ad,
                                                                        'users_role_commercial'  => $users_role_commercial,
                                                                        'pagination'             => $pagination,
                                                                        'roles'                  => $roles,
                                                                        'cat_services'           => $cat_services,
                                                                        'catserv'                => $catserv,
                                                                        'countries'              => $countries,
                                                                        'country'                => $country,
                                                                        'option'                 => $role_id,
                                                                        'term'                   => $term,
                                                                        'field'                  => $field,
                                                                    )
        );
    }

    /**
     * Recupera los usuarios del socio segun el usuario logeado y tambien recupera todos los usuarios de los talleres del socio
     */
    public function userPartnerListAction($page=1, $country=0, $catserv=0, $option='0', $term='0', $field='0') {

        $security = $this->get('security.context');
        if ($security->isGranted('ROLE_AD') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();

        $users_role_super_ad    = array();
        $users_role_top_ad      = array();
        $users_role_ad          = array();
        $users_role_commercial  = array();

        $pagination = new Pagination($page);

        $params[] = array('category_service', ' = '.$security->getToken()->getUser()->getCategoryService()->getId());

        if($country != 0){
            $params[] = array('country', ' = '.$country);
        }

        if ($term != '0' and $field != '0') {

            if ($term == 'tel') {
                $params[] = array('phone_number_1', " != '0' AND (e.phone_number_1 LIKE '%" . $field . "%' OR e.phone_number_2 LIKE '%" . $field . "%' OR e.mobile_number_1 LIKE '%" . $field . "%' OR e.mobile_number_2 LIKE '%" . $field . "%') ");
            } elseif ($term == 'mail') {
                $params[] = array('email_1', " != '0' AND (e.email_1 LIKE '%" . $field . "%' OR e.email_2 LIKE '%" . $field . "%') ");
            } elseif ($term == 'user') {
                $params[] = array('username', " LIKE '%" . $field . "%'");
            }
        }

        if(!isset($params)) $params[] = array();

        $roles_allowed = "'0'";
        if($security->isGranted('ROLE_TOP_AD')) $roles_allowed .= ", 'ROLE_SUPER_AD' ";
        if($security->isGranted('ROLE_SUPER_AD')) $roles_allowed .= ", 'ROLE_AD' ";
        if($security->isGranted('ROLE_AD')) $roles_allowed .= ", 'ROLE_COMMERCIAL' ";

        if($option == null or $option == 'all' or $option == 'none' or $option == '0'){
                $role_id  = 'none';
                $joins[] = array('e.user_role r ', "r.name IN (".$roles_allowed.")");
        }else{
                $role     = $em->getRepository("UserBundle:Role")->find($option);
                $role_id  = $role->getId();
                $role     = $role->getName();
                $joins[] = array('e.user_role r ', "r.name LIKE '".$role."'");
        }

        $users    = $pagination->getRows      ($em, 'UserBundle', 'User', $params, $pagination, null, $joins);
        $length   = $pagination->getRowsLength($em, 'UserBundle', 'User', $params, null, $joins);
        //separamos los tipos de usuario...
        foreach ($users as $user) {
            // $role = $user->getRoles();
            if ($option == null or $option == 'all' or $option == 'none') {
                $role     = $user->getRoles();
                $role     = $role[0];
                $role     = $role->getName();
            }
            if     ($role == "ROLE_SUPER_AD")     $users_role_super_ad[]    = $user;
            elseif ($role == "ROLE_TOP_AD")       $users_role_top_ad[]      = $user;
            elseif ($role == "ROLE_AD")           $users_role_ad[]          = $user;
            elseif ($role == "ROLE_COMMERCIAL")   $users_role_commercial[]  = $user;

            if($option == null or $option == 'all') unset($role);
        }

        $length = $pagination->setTotalPagByLength($length);

        $roles = $em->getRepository("UserBundle:Role")->findAll();

        $roles_allowed = "'0'";
        if($security->isGranted('ROLE_TOP_AD')) $roles_allowed .= ", 'ROLE_SUPER_AD' ";
        if($security->isGranted('ROLE_SUPER_AD')) $roles_allowed .= ", 'ROLE_AD' ";
        if($security->isGranted('ROLE_AD')) $roles_allowed .= ", 'ROLE_COMMERCIAL' ";

        $roles = $em->createQuery("SELECT r FROM UserBundle:Role r WHERE r.name IN (".$roles_allowed.")")->getResult();

        $countries = $em->getRepository("UtilBundle:Country")->findAll();
        $cat_services = $em->getRepository("UserBundle:CategoryService")->findAll();

        return $this->render('UserBundle:User:partner_list.html.twig', array(
                                                                        'users_role_super_ad'    => $users_role_super_ad,
                                                                        'users_role_top_ad'      => $users_role_top_ad,
                                                                        'users_role_ad'          => $users_role_ad,
                                                                        'users_role_commercial'  => $users_role_commercial,
                                                                        'pagination'             => $pagination,
                                                                        'roles'                  => $roles,
                                                                        'cat_services'           => $cat_services,
                                                                        'catserv'                => $catserv,
                                                                        'countries'              => $countries,
                                                                        'country'                => $country,
                                                                        'option'                 => $role_id,
                                                                        'term'                   => $term,
                                                                        'field'                  => $field,
                                                                    )
        );
    }

    /**
     * Crea un nuevo usuario en la bbdd
     * @return type
     * @throws AccessDeniedException
     */
    public function newUserAction($type) {

        $security = $this->get('security.context');
        $user_logged = $this->get('security.context')->getToken()->getUser();
        if($user_logged == 'anon.') return $this->redirect($this->generateUrl('user_login'));
        if($security->getToken()->getUser()) {

        }
        $catserv = $security->getToken()->getUser()->getCategoryService();
        if ($security->isGranted('ROLE_AD') === false OR $user_logged->getAllowCreate() == false) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $user = new User();

        if ($security->isGranted('ROLE_AD') and !$security->isGranted('ROLE_SUPER_AD')) {
            $partners = $em->getRepository("PartnerBundle:Partner")->find($security->getToken()->getUser()->getPartner()->getId());
        }
        elseif (!$security->isGranted('ROLE_SUPER_ADMIN'))
        {
            $partners = $em->getRepository("PartnerBundle:Partner")->findBy(array('category_service' => $security->getToken()->getUser()->getCategoryService()->getId(),
                                                                                  'active'  => '1'));
        }
        else $partners = '0';

        // Creamos variables de sesion para fitlrar los resultados del formulario
        if ($security->isGranted('ROLE_SUPER_ADMIN')) {

            $_SESSION['id_partner'] = ' != 0 ';
            $_SESSION['id_country'] = ' != 0 ';
            $_SESSION['role'] = $security->getToken()->getUser()->getRoles()[0]->getName();

        }elseif ($security->isGranted('ROLE_ADMIN')) {

            $partner_ids = '0';
            foreach ($partners as $p) { $partner_ids = $partner_ids.', '.$p->getId(); }

            $_SESSION['id_partner'] = ' IN ('.$partner_ids.')';
            $_SESSION['id_catserv'] = ' = '.$security->getToken()->getUser()->getCategoryService()->getId();
            $_SESSION['role'] = $security->getToken()->getUser()->getRoles()[0]->getName();

        }elseif ($security->isGranted('ROLE_SUPER_AD') or $security->isGranted('ROLE_TOP_AD')) {

            $partner_ids = '0';
            foreach ($partners as $p) { $partner_ids = $partner_ids.', '.$p->getId(); }
            
            $_SESSION['id_partner'] = ' IN ('.$partner_ids.')';
            $_SESSION['id_catserv'] = ' = '.$security->getToken()->getUser()->getCategoryService()->getId();
            $_SESSION['role'] = $security->getToken()->getUser()->getRoles()[0]->getName();

        }else {
            $_SESSION['id_partner'] = ' = '.$partners->getId();
            $_SESSION['id_catserv'] = ' = '.$security->getToken()->getUser()->getCategoryService()->getId();
            $_SESSION['role'] = $security->getToken()->getUser()->getRoles()[0]->getName();
        }


        //dependiendo del tipo de usuario llamamos a un formType o a otro y le seteamos el rol que toque
        if ($type == 'admin') {
            $rol = $em->getRepository('UserBundle:Role')->findByName('ROLE_ADMIN');
            $user->setUserRoles($rol);
            $form = $this->createForm(new UserAdminAssessorType(), $user);
        }elseif ($type == 'super_ad') {
            $rol = $em->getRepository('UserBundle:Role')->findByName('ROLE_SUPER_AD');
            $user->setUserRoles($rol);
            $form = $this->createForm(new UserSuperPartnerType(), $user);
        }elseif ($type == 'ad') {
            $rol = $em->getRepository('UserBundle:Role')->findByName('ROLE_AD');
            $user->setUserRoles($rol);
            $form = $this->createForm(new UserPartnerType(), $user);
        }elseif ($type == 'commercial') {
            $rol = $em->getRepository('UserBundle:Role')->findByName('ROLE_COMMERCIAL');
            $user->setUserRoles($rol);
            $form = $this->createForm(new UserCommercialType(), $user);
        } elseif ($type == 'assessor') {
            $_SESSION['all'] = $this->get('translator')->trans('all');
            $rol = $em->getRepository('UserBundle:Role')->findByName('ROLE_ASSESSOR');
            $user->setUserRoles($rol);
            $form = $this->createForm(new UserAssessorType(), $user);
        }

        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($request->getMethod() == 'POST') {
            if($user->getRegion() == null){
                $user->setRegion('-');
            }
            // SLUGIFY USERNAME TO MAKE IT UNREPEATED
            $name = $user->getUsername();

            $username = UtilController::getUsernameUnused($em, $name);
            $user->setUsername($username);

            // Si el username ya exite, mostramos un error
            if ($username != $name) {

                $error_username = $this->get('translator')->trans('username_used').$username;

                $array = array('user'       => $user,
                               'user_type'  => $type,
                               'form_name'  => $form->getName(),
                               'form'       => $form->createView(),
                               'catserv'    => $catserv,
                               'error_username' => $error_username);

                return $this->render('UserBundle:User:new_user.html.twig', $array);
            }

            $user->setCreatedAt(new \DateTime(\date("Y-m-d H:i:s")));
            $user->setCreatedBy($security->getToken()->getUser());
            if($user->getCategoryService() == null && !$security->isGranted('ROLE_SUPER_ADMIN')){
                $user->setCategoryService($catserv);
            }

            if($user->getPartner() == null and isset($request->request->get('commercial_type')['partner'])) {
                $u_partner = $em->getRepository('PartnerBundle:Partner')->find($request->request->get('commercial_type')['partner']);
                $user->setPartner($u_partner);
            }
            if($user->getShop() == null and isset($request->request->get('commercial_type')['shop'])) {
                $u_shop = $em->getRepository('PartnerBundle:Shop')->find($request->request->get('commercial_type')['shop']);
                $user->getShop($u_shop);
            }

            if($user->getAllowList() == null) $user->setAllowList(1);
            if($user->getAllowOrder() == null) $user->setAllowOrder(1);
            if($user->getAllowCreate() == null) $user->setAllowCreate(1);

            // $partner = $form->getData('partner');
            $user = UtilController::settersContact($user, $user);
            $this->saveUser($em, $user);

            $flash =  $this->get('translator')->trans('create').' '.$this->get('translator')->trans('user').': '.$username;
            $this->get('session')->setFlash('alert', $flash);

            if ($security->isGranted('ROLE_ADMIN')) return $this->redirect($this->generateUrl('user_list'));
            else                                    return $this->redirect($this->generateUrl('user_partner_list'));
        }

        $array = array('user'       => $user,
                       'user_type'  => $type,
                       'form_name'  => $form->getName(),
                       'catserv'    => $catserv,
                       'form'       => $form->createView());

        return $this->render('UserBundle:User:new_user.html.twig', $array);
    }

    /**
     * Obtener los datos del usuario a partir de us ID para poder editarlo (solo lo puede hacer el ROLE_ADMIN)
     * El ROLE_USER debe usar el profileAction (solo se puede editar a si mismo)
     * Si la petición es GET  --> mostrar el formulario
     * Si la petición es POST --> save del formulario
     * @Route("/user/edit/{id}")
     * @ParamConverter("user", class="UserBundle:User")
     */
    public function editUserAction($user) {

        $security = $this->get('security.context');
        if (($security->isGranted('ROLE_AD') === false)
        and (!$security->isGranted('ROLE_SUPER_ADMIN'))) {
            return $this->render('TwigBundle:Exception:exception_access.html.twig');
        }
        $user_logged = $this->get('security.context')->getToken()->getUser();
        if ($user_logged->getAllowCreate() == false) {
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getEntityManager();
        $trans = $this->get('translator');

        $petition = $this->getRequest();
        if($petition->request->has('assign_all')){
            $sql = 'UPDATE UserBundle:User u SET u.category_service = null WHERE u.id = '.$user->getId().' ';
            $result= $em->createQuery($sql)->getResult();
            $flash =  $trans->trans('btn.edit').' '.$trans->trans('user').': '.$user->getUsername();
            $this->get('session')->setFlash('alert', $flash);

            return $this->redirect($this->generateUrl('user_list'));
        }
        //guardamos el password por si no lo queremos modificar...
        $original_password = $user->getPassword();


        if ($security->isGranted('ROLE_AD') and !$security->isGranted('ROLE_SUPER_AD')) {
            $partners = $em->getRepository("PartnerBundle:Partner")->find($security->getToken()->getUser()->getPartner()->getId());
        }
        elseif (!$security->isGranted('ROLE_SUPER_ADMIN')) {

            $partners = $em->getRepository("PartnerBundle:Partner")->findBy(array('category_service' => $security->getToken()->getUser()->getCategoryService()->getId(),
                                                                                  'active'  => '1'));
        }
        else $partners = '0';

        //que tipo de usuario estamos editando (los formtype varian...)
    	$role = $user->getRoles();
    	$role = $role[0];
    	$role = $role->getRole();
        $partner_id = null;
        if ($security->isGranted('ROLE_ADMIN')) {
            if ($role == "ROLE_USER")
                $partner_id = $user->getWorkshop()->getPartner()->getId();
            elseif($role == "ROLE_AD")
                $partner_id = $user->getPartner()->getId();
        }
        if ($role == "ROLE_COMMERCIAL" and $user->getShop() != null)
             $shop_name = $user->getShop()->getName();
        else $shop_name = null;

        $user_role_id = 0;
        // Creamos variables de sesion para fitlrar los resultados del formulario
        if ($security->isGranted('ROLE_SUPER_ADMIN')) {

            if ($role == "ROLE_USER") {

                $_SESSION['id_partner'] = ' = '.$partner_id ;
            }
            else {
                $_SESSION['id_partner'] = ' != 0 ';
            }
            $_SESSION['id_country'] = ' != 0 ';
            $user_role_id = 1;
            if($user->getRoles()[0]->getId() != 1) {
                if($user->getCategoryService() != null) {
                    $_SESSION['id_catserv'] = ' = '.$user->getCategoryService()->getId();
                    $user_role_id = 0;
                }
            }
            if($user->getRoles()[0]->getId() == 3) {
                $user_role_id = 2;
            }

        }elseif ($security->isGranted('ROLE_SUPER_AD')) {

            $partner_ids = '0';
            foreach ($partners as $p) { $partner_ids = $partner_ids.', '.$p->getId(); }

            $_SESSION['id_partner'] = ' IN ('.$partner_ids.')';

        }else {
            $_SESSION['id_partner'] = ' = '.$partners->getId();
        }

        $_SESSION['role'] = $security->getToken()->getUser()->getRoles()[0]->getName();

        if     ($role == "ROLE_SUPER_ADMIN" or $role == "ROLE_ADMIN") $form = $this->createForm(new EditUserAdminAssessorType(), $user);
        elseif ($role == "ROLE_ASSESSOR"){
            $form = $this->createForm(new EditUserAssessorType() , $user);
            if ($user->getCategoryService() == null){
                $user_role_id = 2;
            }
        }
        elseif ($role == "ROLE_TOP_AD")     $form = $this->createForm(new EditUserSuperPartnerType() , $user);
        elseif ($role == "ROLE_SUPER_AD")   $form = $this->createForm(new EditUserSuperPartnerType() , $user);
        elseif ($role == "ROLE_AD")         $form = $this->createForm(new EditUserPartnerType()      , $user);
        elseif ($role == "ROLE_COMMERCIAL") $form = $this->createForm(new EditCommercialType()       , $user);
        elseif ($role == "ROLE_USER")       $form = $this->createForm(new EditUserWorkshopType()     , $user);

        $actual_username = $user->getUsername();
        $actual_city   = $user->getRegion();
        $actual_region = $user->getCity();

        if ($petition->getMethod() == 'POST') {

            if($user->getCategoryService() != null and $petition->request->get('assessor_type')['category_service'] == null and $role == "ROLE_ASSESSOR") {
                $flash =  $trans->trans('error.bad_introduction').' ('.$trans->trans('category_service').')';
                $this->get('session')->setFlash('error', $flash);
            }
            else {

                if($user->getShop() != null) $old_shop = $user->getShop()->getId();

                $form->bindRequest($petition);

                // SLUGIFY USERNAME TO MAKE IT UNREPEATED
                $name = $user->getUsername();
                if ($name != $actual_username) {
                    $username = UtilController::getUsernameUnused($em, $name);
                    $user->setUsername($username);

                    if ($username != $name) {
                        $error_username = $trans->trans('username_used').$username;

                        return $this->render('UserBundle:User:edit_user.html.twig', array('user'           => $user,
                                                                                          'form_name'      => $form->getName(),
                                                                                          'form'           => $form->createView(),
                                                                                          'error_username' => $error_username));
                    }
                }

                $user = UtilController::settersContact($user, $user, $actual_region, $actual_city);
                if($user->getWorkshop() !== null){
                    $workshop_user= $em->getRepository('WorkshopBundle:Workshop')->findOneById($user->getWorkshop()->getId());
                    $workshop_user = UtilController::saveUserFromWorkshop($user, $workshop_user );
       
                    
                    $workshop_user->setPartner($user->getWorkshop()->getPartner());
                    $workshop_user->setContact($user->getName());
                    $workshop_user->setActive($user->getActive());
                    $em->persist($workshop_user);
                    $status = 0;
                    if($workshop_user->getActive()){
                        $status = 1;
                    }
                    UtilController::createHistorical($em, $workshop_user, $status);
                    $em->flush();
                 }
                 elseif($user->getPartner() !== null){

                    if($user->getRoles()[0]->getName() == 'ROLE_COMMERCIAL') {

                        $em->persist($user);
                    }else{
                        $partner_user= $em->getRepository('PartnerBundle:Partner')->findOneById($user->getPartner()->getId());
                        $partner_user = UtilController::saveUserFromWorkshop($user, $partner_user );
                        $partner_user->setContact($user->getName());
                        $partner_user->setActive($user->getActive());
                        $em->persist($partner_user);
                    }

                    $em->flush();
                }

                if($user->getShop() != null) $shop = $user->getShop()->getId();

                if(isset($old_shop) and $old_shop != $shop) {
                    $orders = $em->getRepository('OrderBundle:WorkshopOrder')->findBy(array('created_by' => $user->getId()));
                    foreach ($orders as $order) {
                        $order->setAction('rejected');
                        $msg_reject = $trans->trans('msg.shop.changed');
                        $order->setRejectionReason($msg_reject);
                        $em->persist($order);
                    }
                    $em->flush();
                }

                $this->saveUser($em, $user, $original_password);
                $flash =  $trans->trans('btn.edit').' '.$trans->trans('user').': '.$user->getUsername();
                $this->get('session')->setFlash('alert', $flash);

                if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
                    return $this->redirect($this->generateUrl('user_list'));
                }else{
                    return $this->redirect($this->generateUrl('user_partner_list'));
                }
            }

        }

        return $this->render('UserBundle:User:edit_user.html.twig', array('user'         => $user,
                                                                          'role'         => $role,
                                                                          'form_name'    => $form->getName(),
                                                                          'partner_id'   => $partner_id,
                                                                          'shop_name'    => $shop_name,
                                                                          'user_role_id' => $user_role_id,
                                                                          'form'         => $form->createView()));
    }

    /**
     * Activa/Desactiva el usuario con la $id de la bbdd
     * @throws AccessDeniedException
     * @throws CreateNotFoundException
     */
    public function disableUserAction($id) {
        $security = $this->get('security.context');
        $user_logged = $security->getToken()->getUser();
        if ($security->isGranted('ROLE_AD') === false OR $user_logged->getAllowCreate() == false) {
            throw new AccessDeniedException();
        }
        $em   = $this->getDoctrine()->getEntityManager();
        $user = $em->getRepository("UserBundle:User")->find($id);
        $user->setActive(!$user->getActive());
        $em->persist($user);
        $em->flush();

        if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('user_list'));
        }else{
            return $this->redirect($this->generateUrl('user_partner_list'));
        }
    }

    /**
     * Elimina el usuario con la $id de la bbdd
     * @throws AccessDeniedException
     * @throws CreateNotFoundException
     */
    public function deleteUserAction($id) {
        $security = $this->get('security.context');
        $user_logged = $security->getToken()->getUser();
        if ($security->isGranted('ROLE_AD') === false OR $user_logged->getAllowCreate() == false) {
            throw new AccessDeniedException();
        }
        $em   = $this->getDoctrine()->getEntityManager();
        $user = $em->getRepository("UserBundle:User")->find($id);
        $em->remove($user);
        $em->flush();

        if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('user_list'));
        }else{
            return $this->redirect($this->generateUrl('user_partner_list'));
        }
    }

    /**
     * Genera el password para un usuario
     * @Route("/user/generatePassword/{id}")
     * @ParamConverter("user", class="UserBundle:User")
     * @return type
     */
    public function generatePasswordAction($id, $user)
    {
        $em   = $this->getDoctrine()->getEntityManager();
        $user = $em->getRepository("UserBundle:User")->find($id);
        if (!$user)
            throw $this->createNotFoundException('Usuario no encontrado en la BBDD');

        /*CREAR PASSWORD AUTOMATICAMENTE*/
        $password = substr( md5(microtime()), 1, 8);
        //$password = 'grupeina';
        $this->savePassword($em, $user, $password);

        return $this->redirect($this->generateUrl('user_edit' , array('id' => $id )));
    }

    /**
     * Cambia el password de un ususario
     * @Route("/user/changePassword/{id}/{new_pass}/{rep_pass}/{old_pass}")
     * @ParamConverter("user", class="UserBundle:User")
     * @param  string $new_pass
     * @param  string $rep_pass
     * @param  string $old_pass
     * @return type
     */
    public function changePasswordAction($user, $new_pass='none', $rep_pass='none', $old_pass='none')
    {
        $em   = $this->getDoctrine()->getEntityManager();

        /*CREAR PASSWORD MANUALMENTE*/
        $encoder  = $this->container->get('security.encoder_factory')->getEncoder($user);
        $pass     = $encoder->encodePassword( $old_pass, $user->getSalt());

        if ($pass == $user->getPassword())
        {
            if($new_pass == $rep_pass)
            {
                if(strlen($new_pass) >= 8)
                {
                    $this->savePassword($em, $user, $new_pass);
                }else{
                    $flash =  $this->get('translator')->trans('error.length_password');
                    $this->get('session')->setFlash('password', $flash);
                }
            }else{
                $flash =  $this->get('translator')->trans('error.same_password');
                $this->get('session')->setFlash('password', $flash);
            }
        }
        else{
            $flash =  $this->get('translator')->trans('change_password.error');
            $this->get('session')->setFlash('password', $flash);
        }
        return $this->render('UserBundle:User:profile.html.twig', array('user' => $user));
    }

    /**
     * Guarda el password del usuario y envia mail con credenciales
     * @param  EntityManager $em
     * @param  Entity $user
     * @param  string $password
     * @return type
     */
    private function savePassword($em, $user, $password){

        $user->setPassword($password);
        $this->saveUser($em, $user);
        $request = $this->getRequest();

        $mail = $user->getEmail1();
        $pos = strpos($mail, '@');
        if ($pos != 0) {

            // Cambiamos el locale para enviar el mail en el idioma del taller
            $locale = $request->getLocale();
            $lang_u = $user->getCountry()->getLang();
            $lang   = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_u);
            $request->setLocale($lang->getShortName());

            /* MAILING */
            $mailerUser = $this->get('cms.mailer');
            $mailerUser->setTo($mail);
            $mailerUser->setSubject($this->get('translator')->trans('mail.changePassword.subject').$user->getUsername());
            $mailerUser->setFrom('noreply@adserviceticketing.com');
            $mailerUser->setBody($this->renderView('UtilBundle:Mailing:user_change_password_mail.html.twig', array('user' => $user, 'password' => $password, '__locale' => $locale)));
            $mailerUser->sendMailToSpool();
            //echo $this->renderView('UtilBundle:Mailing:user_change_password_mail.html.twig', array('user' => $user, 'password' => $password));die;

            /* MAILING */
            $mail = $this->container->getParameter('mail_db');
            $mailerUser->setTo($mail);
            $mailerUser->sendMailToSpool();
            //echo $this->renderView('UtilBundle:Mailing:user_change_password_mail.html.twig', array('user' => $user, 'password' => $password));die;

            // Dejamos el locale tal y como estaba
            $request->setLocale($locale);
        }

        $flash =  $this->get('translator')->trans('change_password.correct');
        $this->get('session')->setFlash('password', $flash);
    }

    /**
     * Devuelve el numero de solicitudes pendientes
     * @return integer
     * @throws AccessDeniedException
     */
    public function getPendingOrders(){

        $security = $this->get('security.context');
        if ($security->isGranted('ROLE_COMMERCIAL') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();
        $user = $security->getToken()->getUser();
        $role = $user->getRoles();
        $role = $role[0];
        $role = $role->getRole();

        $pagination = new Pagination(1);
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


        if($user->getCategoryService() != null) {
            $id_catserv = $user->getCategoryService()->getId();
            $workshop_pending[]  = array('category_service' , " = ".$id_catserv);
            $workshop_rejected[] = array('category_service' , " = ".$id_catserv);
            $preorder_pending[]  = array('category_service' , " = ".$id_catserv);
            $preorder_rejected[] = array('category_service' , " = ".$id_catserv);
            $shop_pending[]      = array('category_service' , " = ".$id_catserv);
            $shop_rejected[]     = array('category_service' , " = ".$id_catserv);
        }

        if    ($role == "ROLE_SUPER_AD"
            OR $role == "ROLE_TOP_AD"  ){   $length_workshop_rejected = $pagination->getRowsLength($em, 'OrderBundle', 'WorkshopOrder' , $workshop_rejected);
                                            $length_shop_rejected     = $pagination->getRowsLength($em, 'OrderBundle', 'ShopOrder'     , $shop_rejected);
                                            $length_preorder_pending  = $pagination->getRowsLength($em, 'OrderBundle', 'WorkshopOrder' , $preorder_pending);

                                            $length = $length_workshop_rejected + $length_shop_rejected + $length_preorder_pending;
                                        }
        elseif($role == "ROLE_AD")      {   $by_partner          = array('partner', ' = '.$user->getPartner()->getId());
                                            $workshop_rejected[] = $by_partner;
                                            $shop_rejected[]     = $by_partner;
                                            $preorder_pending[]  = $by_partner;

                                            $length_workshop_rejected = $pagination->getRowsLength($em, 'OrderBundle', 'WorkshopOrder' , $workshop_rejected);
                                            $length_shop_rejected     = $pagination->getRowsLength($em, 'OrderBundle', 'ShopOrder'     , $shop_rejected);
                                            $length_preorder_pending  = $pagination->getRowsLength($em, 'OrderBundle', 'WorkshopOrder' , $preorder_pending);

                                            $length = $length_workshop_rejected + $length_shop_rejected + $length_preorder_pending;
                                        }
        elseif($role == "ROLE_COMMERCIAL"){ $by_commercial       = array('created_by', ' = '.$user->getId());
                                            $preorder_pending[]  = $by_commercial;
                                            $preorder_rejected[] = $by_commercial;

                                            $length = $pagination->getRowsLength($em, 'OrderBundle', 'WorkshopOrder' , $preorder_rejected);
                                        }
        elseif($role == "ROLE_ADMIN")   {
                                            $length_workshop_pending  = $pagination->getRowsLength($em, 'OrderBundle', 'WorkshopOrder' , $workshop_pending);
                                            $length_shop_pending      = $pagination->getRowsLength($em, 'OrderBundle', 'ShopOrder'     , $shop_pending);

                                            $length = $length_workshop_pending + $length_shop_pending;
                                        }
        elseif($role == "ROLE_SUPER_ADMIN"){
                                            $length_workshop_pending  = $pagination->getRowsLength($em, 'OrderBundle', 'WorkshopOrder' , $workshop_pending);
                                            $length_shop_pending      = $pagination->getRowsLength($em, 'OrderBundle', 'ShopOrder'     , $shop_pending);

                                            $length = $length_workshop_pending + $length_shop_pending;
                                        }
        return $length;
    }

    /**
     * Hace el save de un usuario
     * Si $original_password == NULL indica que no se quiere modificar y se mantienen el que habia (viene del formulario y esta en blanco)
     * Si $original_password != NULL indica que si que lo queremos cambiar y se tiene que codificar con el nuevo salt (viene del formulario y NO esta en blanco)
     * @param EntityManager $em
     * @param User $user
     * @param String $original_password password existente en la bbdd
     */
    private function saveUser($em, $user, $original_password = null) {

        if ($user->getPassword() != null and $original_password == null) {
            //password nuevo, se codifica con el nuevo salt
            $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
            $salt = md5(time());
            $password = $encoder->encodePassword($user->getPassword(), $salt);
            $user->setPassword($password);
            $user->setSalt($salt);
        }
        $user->setModifiedAt(new \DateTime(\date("Y-m-d H:i:s")));
        $user->setModifiedBy($this->get('security.context')->getToken()->getUser());

        $em->persist($user);
        $em->flush();
    }

    public function acceptPrivacyAction() {
        $user = $this->get('security.context')->getToken()->getUser();
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            if ($request->request->has('Accept')) {
                $em = $this->getDoctrine()->getEntityManager();
                $user->setPrivacy(1);
                $em->persist($user);
                $em->flush();
            return $this->redirect($this->generateUrl('user_index'));
            }
            else  if ($request->request->has('Cancel')) {
                return $this->redirect($this->generateUrl('user_logout'));
            }
        }
        return $this->render('UserBundle:User:accept_privacy.html.twig');

    }
}
