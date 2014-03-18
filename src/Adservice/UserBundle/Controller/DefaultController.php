<?php

namespace Adservice\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;

use Adservice\UserBundle\Form\UserAdminAssessorType;
use Adservice\UserBundle\Form\UserPartnerType;
use Adservice\UserBundle\Form\UserWorkshopType;

use Adservice\UserBundle\Entity\User;
use Adservice\WorkshopBundle\Entity\Workshop;
use Adservice\StatisticBundle\Entity\StatisticRepository;

class DefaultController extends Controller {

    /**
     * Welcome function, redirige al html del menu de usuario
     */
    public function indexAction() {
        return $this->render('UserBundle:Default:index.html.twig');
    }

    /**
     * Obtener los datos del usuario logueado para verlos
     */
    public function profileAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();
        $id_logged_user = $this->get('security.context')->getToken()->getUser()->getId();
        $user = $em->getRepository('UserBundle:User')->find($id_logged_user);
        
        if (!$user)
            throw $this->createNotFoundException('Usuario no encontrado en la BBDD');
        
        return $this->render('UserBundle:Default:profile.html.twig', array('user' => $user));
    }

    public function selectNewUserAction() {
        return $this->render('UserBundle:Default:selectUserType.html.twig');
    }

    /**
     * Recupera los usuarios del socio segun el usuario logeado y tambien recupera todos los usuarios de los talleres del socio
     */
    public function userListAction() {

        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();

        $users = $em->getRepository("UserBundle:User")->findAll();

        $users_role_admin = array();
        $users_role_assessor = array();
        $users_role_user = array();
        $users_role_admin  = array();
        //separamos los tipos de usuario...
        foreach ($users as $user) {
            $role = $user->getRoles();
            if     ($role[0]->getRole() == "ROLE_ADMIN")    $users_role_admin[] = $user;
            elseif ($role[0]->getRole() == "ROLE_USER")     $users_role_user[] = $user;
            elseif ($role[0]->getRole() == "ROLE_ASSESSOR") $users_role_assessor[] = $user;
            elseif ($role[0]->getRole() == "ROLE_AD")       $users_role_ad[] = $user;
        }


        return $this->render('UserBundle:Default:list.html.twig', array('users_role_admin'      => $users_role_admin,
                                                                        'users_role_user'       => $users_role_user,
                                                                        'users_role_assessor'   => $users_role_assessor,
                                                                        'users_role_ad'         => $users_role_ad
                                                                       ));
    }

    /**
     * Obtener los datos del usuario a partir de us ID para poder editarlo (solo lo puede hacer el ROLE_ADMIN)
     * El ROLE_USER debe usar el profileAction (solo se puede editar a si mismo)
     * Si la petición es GET  --> mostrar el formulario
     * Si la petición es POST --> save del formulario
     */
    public function editUserAction($id) {

        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();

        $user = $em->getRepository("UserBundle:User")->find($id);
        if (!$user)throw $this->createNotFoundException('Usuario no encontrado en la BBDD');

        //guardamos el password por si no lo queremos modificar...
        $original_password = $user->getPassword();

        $petition = $this->getRequest();
        
        //que tipo de usuario estamos editando (los formtype varian...)
        $role = $user->getRoles();
        if ($role[0]->getRole() == "ROLE_ADMIN")        $form = $this->createForm(new UserAdminAssessorType(), $user);
        elseif ($role[0]->getRole() == "ROLE_USER")     $form = $this->createForm(new UserWorkshopType(), $user);
        elseif ($role[0]->getRole() == "ROLE_ASSESSOR") $form = $this->createForm(new UserAdminAssessorType(), $user);
        elseif ($role[0]->getRole() == "ROLE_AD")       $form = $this->createForm(new UserPartnerType(), $user);
        
        

        if ($petition->getMethod() == 'POST') {
            $form->bindRequest($petition);
            if ($form->isValid())
                $this->saveUser($em, $user, $original_password);
            return $this->redirect($this->generateUrl('user_list'));
        }

        return $this->render('UserBundle:Default:editUser.html.twig', array('user'      => $user,
                                                                            'form_name' => $form->getName(),
                                                                            'form'      => $form->createView()));
    }

    /**
     * Elimina el usuario con la $id de la bbdd
     * @param Int $id
     * @throws AccessDeniedException
     * @throws CreateNotFoundException
     */
    public function deleteUserAction($id) {

        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false) {
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getEntityManager();
        $user = $em->getRepository("UserBundle:User")->find($id);
        if (!$user)
            throw $this->createNotFoundException('Usuario no encontrado en la BBDD');

        $em->remove($user);
        $em->flush();

        return $this->redirect($this->generateUrl('user_list'));
    }

    /**
     * Crea un nuevo usuario en la bbdd
     * @return type
     * @throws AccessDeniedException
     */
    public function newUserAction($type) {

        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $user = new User();

        //dependiendo del tipo de usuario llamamos a un formType o a otro y le seteamos el rol que toque
        if ($type == 'admin') {
            $rol = $em->getRepository('UserBundle:Role')->findByName('ROLE_ADMIN');
            $user->setUserRoles($rol);
            $form = $this->createForm(new UserAdminAssessorType(), $user);
        } elseif ($type == 'assessor') {
            $rol = $em->getRepository('UserBundle:Role')->findByName('ROLE_ASSESSOR');
            $user->setUserRoles($rol);
            $form = $this->createForm(new UserAdminAssessorType(), $user);
        } elseif ($type == 'user') {
            $rol = $em->getRepository('UserBundle:Role')->findByName('ROLE_USER');
            $user->setUserRoles($rol);
            $form = $this->createForm(new UserWorkshopType(), $user);
        }elseif ($type == 'ad') {
            $rol = $em->getRepository('UserBundle:Role')->findByName('ROLE_AD');
            $user->setUserRoles($rol);
            $form = $this->createForm(new UserPartnerType(), $user);
        }
        
        $request = $this->getRequest();
        
        $form->bindRequest($request);

        if ($form->isValid()) {
            $user->setCreatedAt(new \DateTime(\date("Y-m-d H:i:s")));
//            $partner = $form->getData('partner');
            $this->saveUser($em, $user);

            return $this->redirect($this->generateUrl('user_list'));
        }

        return $this->render('UserBundle:Default:newUser.html.twig', array('user'       => $user,
                                                                           'user_type'  => $type,
                                                                           'form_name'  => $form->getName(),
                                                                           'form'       => $form->createView()));
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

        if ($user->getPassword() != null) {
            //password nuevo, se codifica con el nuevo salt
            $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
            $salt = md5(time());
            $password = $encoder->encodePassword($user->getPassword(), $salt);
            $user->setPassword($password);
            $user->setSalt($salt);
        } else {
            //el password no se modifica
            $user->setPassword($original_password);
        }
        $user->setModifiedAt(new \DateTime(\date("Y-m-d H:i:s")));
        $user->setModifyBy($this->get('security.context')->getToken()->getUser());

        $em->persist($user);
        $em->flush();
    }
}
