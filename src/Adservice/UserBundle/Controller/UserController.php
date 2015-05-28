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
use Adservice\UserBundle\Form\UserSuperPartnerType;
use Adservice\UserBundle\Form\UserPartnerType;
use Adservice\UserBundle\Form\UserWorkshopType;
use Adservice\UserBundle\Form\EditUserAdminAssessorType;
use Adservice\UserBundle\Form\EditUserSuperPartnerType;
use Adservice\UserBundle\Form\EditUserPartnerType;
use Adservice\UserBundle\Form\EditUserWorkshopType;

use Adservice\UserBundle\Entity\User;
use Adservice\UtilBundle\Entity\Pagination;
use Adservice\WorkshopBundle\Entity\Workshop;
use Adservice\StatisticBundle\Entity\StatisticRepository;
use Adservice\UtilBundle\Controller\UtilController as UtilController;

class UserController extends Controller {

    /**
     * Welcome function, redirige al html del menu de usuario
     */
    public function indexAction() {

//        $id_logged_user = $this->get('security.context')->getToken()->getUser()->getId();
//
//        $session = $this->getRequest()->getSession();
//        $session->set('id_logged_user', $id_logged_user);

        if ($this->get('security.context')->isGranted('ROLE_AD')) $length = $this->getPendingOrders();
        else $length = 0;

        return $this->render('UserBundle:User:index.html.twig', array('length' => $length));
    }

    /**
     * Obtener los datos del usuario logueado para verlos
     */
    public function profileAction() {
        $em             = $this->getDoctrine()->getEntityManager();
        $id_logged_user = $this->get('security.context')->getToken()->getUser()->getId();
        $user           = $em->getRepository('UserBundle:User')->find($id_logged_user);

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
    public function userListAction($page=1, $country=0, $option=null) {

        $security = $this->get('security.context');
        if ($security->isGranted('ROLE_ADMIN') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();

        $users_role_super_admin = array();
        $users_role_admin       = array();
        $users_role_assessor    = array();
        $users_role_user        = array();
        $users_role_super_ad    = array();
        $users_role_ad          = array();


        $pagination = new Pagination($page);

        if($security->isGranted('ROLE_SUPER_ADMIN')) {
            if($country != 0){
                $params[] = array('country', ' = '.$country);
            }else{
                $params[] = array();
            }
        }else $params[] = array('country', ' = '.$security->getToken()->getUser()->getCountry()->getId());

        if($option == null or $option == 'all' or $option == 'none'){
                $users    = $pagination->getRows      ($em, 'UserBundle', 'User', $params, $pagination);
                $length   = $pagination->getRowsLength($em, 'UserBundle', 'User', $params);
                $role_id  = 'none';
        }else{
                $role     = $em->getRepository("UserBundle:Role")->find($option);
                $role_id  = $role->getId();
                $role     = $role->getName();
                $users    = $em->getRepository("UserBundle:User")->findByOption($em, $security, $country, $role, $pagination);
                $length   = $em->getRepository("UserBundle:User")->findLengthOption($em, $security, $country, $role);
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
            elseif ($role == "ROLE_AD")           $users_role_ad[]          = $user;

            if($option == null or $option == 'all') unset($role);
        }

        $length = $pagination->setTotalPagByLength($length);

        $roles = $em->getRepository("UserBundle:Role")->findAll();
        $countries = $em->getRepository("UtilBundle:Country")->findAll();

        return $this->render('UserBundle:User:list.html.twig', array(   'users_role_super_admin' => $users_role_super_admin,
                                                                        'users_role_admin'       => $users_role_admin,
                                                                        'users_role_user'        => $users_role_user,
                                                                        'users_role_assessor'    => $users_role_assessor,
                                                                        'users_role_super_ad'    => $users_role_super_ad,
                                                                        'users_role_ad'          => $users_role_ad,
                                                                        'pagination'             => $pagination,
                                                                        'roles'                  => $roles,
                                                                        'countries'              => $countries,
                                                                        'country'                => $country,
                                                                        'option'                 => $role_id,
                                                                       ));
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
        if ((($security->isGranted('ROLE_ADMIN') and $security->getToken()->getUser()->getCountry()->getId() == $user->getCountry()->getId()) === false)
        and (!$security->isGranted('ROLE_SUPER_ADMIN'))) {
            return $this->render('TwigBundle:Exception:exception_access.html.twig');
        }

        $em = $this->getDoctrine()->getEntityManager();

        //guardamos el password por si no lo queremos modificar...
        $original_password = $user->getPassword();

        $petition = $this->getRequest();

        if ($security->isGranted('ROLE_SUPER_AD')) {

            $partners = $em->getRepository("PartnerBundle:Partner")->findBy(array('country' => $security->getToken()->getUser()->getCountry()->getId(),
                                                                                    'active' => '1'));
        }
        else $partners = '0';

        // Creamos variables de sesion para fitlrar los resultados del formulario
        if ($security->isGranted('ROLE_SUPER_ADMIN')) {

            $_SESSION['id_partner'] = ' != 0 ';
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

        //que tipo de usuario estamos editando (los formtype varian...)
    	$role = $user->getRoles();
    	$role = $role[0];
    	$role = $role->getRole();

        if     ($role == "ROLE_SUPER_ADMIN" or $role == "ROLE_ADMIN" or $role == "ROLE_ASSESSOR") $form = $this->createForm(new EditUserAdminAssessorType(), $user);
        elseif ($role == "ROLE_SUPER_AD")                                                         $form = $this->createForm(new EditUserSuperPartnerType() , $user);
        elseif ($role == "ROLE_AD")                                                               $form = $this->createForm(new EditUserPartnerType()      , $user);
        elseif ($role == "ROLE_USER")                                                             $form = $this->createForm(new EditUserWorkshopType()     , $user);

        $actual_username = $user->getUsername();
        $actual_city   = $user->getRegion();
        $actual_region = $user->getCity();

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

                // SLUGIFY USERNAME TO MAKE IT UNREPEATED
                $name = $user->getUsername();
                if ($name != $actual_username) {
                    $username = UtilController::getUsernameUnused($em, $name);
                    $user->setUsername($username);

                    $error_username = $this->get('translator')->trans('username_used').$username;

                    return $this->render('UserBundle:User:edit_user.html.twig', array('user'      => $user,
                                                                          'form_name' => $form->getName(),
                                                                          'form'      => $form->createView(),
                                                                          'error_username' => $error_username));
                }

                $user = UtilController::settersContact($user, $user, $actual_region, $actual_city);
                $this->saveUser($em, $user, $original_password);

                $flash =  $this->get('translator')->trans('edit').' '.$this->get('translator')->trans('user').': '.$user->getUsername();
                $this->get('session')->setFlash('alert', $flash);
            }
            return $this->redirect($this->generateUrl('user_list'));
        }

        return $this->render('UserBundle:User:edit_user.html.twig', array('user'      => $user,
                                                                          'form_name' => $form->getName(),
                                                                          'form'      => $form->createView()));
    }

    /**
     * Elimina el usuario con la $id de la bbdd
     * @Route("/user/delete/{id}")
     * @ParamConverter("user", class="UserBundle:User")
     * @throws AccessDeniedException
     * @throws CreateNotFoundException
     */
    public function deleteUserAction($id) {

        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false) {
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($user);
        $em->flush();

        return $this->redirect($this->generateUrl('user_list'));
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

        return $this->redirect($this->generateUrl('user_edit'   , array('id'   => $id )));
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
            $mailerUser->setFrom('noreply@grupeina.com');
            $mailerUser->setBody($this->renderView('UtilBundle:Mailing:user_change_password_mail.html.twig', array('user' => $user, 'password' => $password)));
            $mailerUser->sendMailToSpool();
            //echo $this->renderView('UtilBundle:Mailing:user_change_password_mail.html.twig', array('user' => $user, 'password' => $password));die;

            // Dejamos el locale tal y como estaba
            $request->setLocale($locale);
        }

        $flash =  $this->get('translator')->trans('change_password.correct');
        $this->get('session')->setFlash('password', $flash);
    }

    /**
     * Crea un nuevo usuario en la bbdd
     * @return type
     * @throws AccessDeniedException
     */
    public function newUserAction($type) {

        $security = $this->get('security.context');
        if ($security->isGranted('ROLE_ADMIN') === false) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $user = new User();

        if ($security->isGranted('ROLE_SUPER_AD')) {

            $partners = $em->getRepository("PartnerBundle:Partner")->findBy(array('country' => $security->getToken()->getUser()->getCountry()->getId(),
                                                                                    'active' => '1'));
        }
        else $partners = '0';

        // Creamos variables de sesion para fitlrar los resultados del formulario
        if ($security->isGranted('ROLE_SUPER_ADMIN')) {

            $_SESSION['id_partner'] = ' != 0 ';
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
        } elseif ($type == 'assessor') {
            $rol = $em->getRepository('UserBundle:Role')->findByName('ROLE_ASSESSOR');
            $user->setUserRoles($rol);
            $form = $this->createForm(new UserAdminAssessorType(), $user);
        }

        $request = $this->getRequest();
        $actual_username = $user->getUsername();

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

            // SLUGIFY USERNAME TO MAKE IT UNREPEATED
            $name = $user->getUsername();
            if ($name != $actual_username) {
                $username = UtilController::getUsernameUnused($em, $name);
                $user->setUsername($username);

                $error_username = $this->get('translator')->trans('username_used').$username;

                return $this->render('UserBundle:User:new_user.html.twig', array(  'user'       => $user,
                                                                                   'user_type'  => $type,
                                                                                   'form_name'  => $form->getName(),
                                                                                   'form'       => $form->createView(),
                                                                                    'error_username' => $error_username));
            }

            $user->setCreatedAt(new \DateTime(\date("Y-m-d H:i:s")));
            $user->setCreatedBy($security->getToken()->getUser());
//            $partner = $form->getData('partner');
            $user = UtilController::settersContact($user, $user);
            $this->saveUser($em, $user);

            $flash =  $this->get('translator')->trans('create').' '.$this->get('translator')->trans('user').': '.$username;
            $this->get('session')->setFlash('alert', $flash);

            return $this->redirect($this->generateUrl('user_list'));
        }

        return $this->render('UserBundle:User:new_user.html.twig', array(  'user'       => $user,
                                                                           'user_type'  => $type,
                                                                           'form_name'  => $form->getName(),
                                                                           'form'       => $form->createView()));
    }



    /**
     * Devuelve el numero de solicitudes pendientes
     * @return integer
     * @throws AccessDeniedException
     */
    public function getPendingOrders(){

        $security = $this->get('security.context');
        if ($security->isGranted('ROLE_AD') === false)
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


        if    ($role == "ROLE_SUPER_AD"){   $by_country          = array('country', ' = '.$user->getCountry()->getId());
                                            $workshop_rejected[] = $by_country;
                                            $workshop_rejected[] = $rejected;
                                            $shop_rejected[]     = $by_country;
                                            $shop_rejected[]     = $rejected;

                                            $length_workshop_rejected = $pagination->getRowsLength($em, 'OrderBundle', 'WorkshopOrder' , $workshop_rejected);
                                            $length_shop_rejected     = $pagination->getRowsLength($em, 'OrderBundle', 'ShopOrder'     , $shop_rejected);

                                            $length = $length_workshop_rejected + $length_shop_rejected;
                                        }
        elseif($role == "ROLE_AD")      {   $by_partner          = array('partner', ' = '.$user->getPartner()->getId());
                                            $workshop_rejected[] = $by_partner;
                                            $workshop_rejected[] = $rejected;
                                            $shop_rejected[]     = $by_partner;
                                            $shop_rejected[]     = $rejected;

                                            $length_workshop_rejected = $pagination->getRowsLength($em, 'OrderBundle', 'WorkshopOrder' , $workshop_rejected);
                                            $length_shop_rejected     = $pagination->getRowsLength($em, 'OrderBundle', 'ShopOrder'     , $shop_rejected);

                                            $length = $length_workshop_rejected + $length_shop_rejected;
                                        }
        elseif($role == "ROLE_ADMIN")   {
                                            $by_country          = array('country', ' = '.$user->getCountry()->getId());
                                            $workshop_pending[]  = $by_country;
                                            $workshop_pending[]  = $not_rejected;
                                            $shop_pending[]      = $by_country;
                                            $shop_pending[]      = $not_rejected;

                                            $length_workshop_pending  = $pagination->getRowsLength($em, 'OrderBundle', 'WorkshopOrder' , $workshop_pending);
                                            $length_shop_pending      = $pagination->getRowsLength($em, 'OrderBundle', 'ShopOrder'     , $shop_pending);

                                            $length = $length_workshop_pending + $length_shop_pending;
                                        }
        elseif($role == "ROLE_SUPER_ADMIN"){
                                            $workshop_pending[]  = $not_rejected;
                                            $shop_pending[]      = $not_rejected;

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
}
