<?php

namespace Adservice\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Adservice\UserBundle\Form\UserType;
use Adservice\UserBundle\Entity\User;
use Adservice\UtilBundle\Entity\Region;
use Adservice\UtilBundle\Entity\Province;

class DefaultController extends Controller {

    public function indexAction() {
        return $this->render('UserBundle:Default:index.html.twig');
    }

    /**
     * Obtener los datos del usuario logueado para verlos/modificarlos
     * Si la petición es GET  --> mostrar el formulario
     * Si la petición es POST --> save del formulario
     */
    public function profileAction() {

        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();
        $id_logged_user = $this->get('security.context')->getToken()->getUser()->getId();
        $user = $em->getRepository('UserBundle:User')->find($id_logged_user);
        $form = $this->createForm(new UserType(), $user);

        if (!$user)
            throw $this->createNotFoundException('Usuario no encontrado en la BBDD');

        $original_password = $form->getData()->getPassword();

        if ($petition->getMethod() == 'POST') {
            $form->bindRequest($petition);
            if ($form->isValid()) $this->saveUser($em, $user);
            return $this->redirect($this->generateUrl('user_index'));
        }


        return $this->render('UserBundle:Default:profile.html.twig', array('user'       => $user,
                                                                           'form_name'  => $form->getName(),
                                                                           'form'       => $form->createView()
        ));
    }

    /**
     * Recupera todos los usuarios segun su rol
     * @return type
     */
    public function userListAction() {
        
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false){
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getEntityManager();
        $all_users = $em->getRepository("UserBundle:User")->findAll();

        $users_role_admin = array();
        $users_role_user = array();

        foreach ($all_users as $user) {
            $role = $user->getRoles();
            if ($role[0]->getRole() == "ROLE_ADMIN") {
                $users_role_admin[] = $user;
            } elseif ($role[0]->getRole() == "ROLE_USER") {
                $users_role_user[] = $user;
            }
        }

        return $this->render('UserBundle:Default:list.html.twig', array('users_role_admin' => $users_role_admin,
                                                                        'users_role_user'  => $users_role_user));
    }

    /**
     * Obtener los datos del usuario a partir de us ID para poder editarlo (solo lo puede hacer el ROLE_ADMIN
     * El ROLE_USER debe usar el profileAction (solo se puede editar a si mismo)
     * Si la petición es GET  --> mostrar el formulario
     * Si la petición es POST --> save del formulario
     */
    public function editUserAction($id) {
        
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false){
            throw new AccessDeniedException();
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        $user = $em->getRepository("UserBundle:User")->find($id);
        
        if (!$user) throw $this->createNotFoundException('Usuario no encontrado en la BBDD');

        $petition = $this->getRequest();
        $form = $this->createForm(new UserType(), $user);
        
        if ($petition->getMethod() == 'POST') {
            $form->bindRequest($petition);
            if ($form->isValid()) $this->saveUser($em, $user);
            return $this->redirect($this->generateUrl('user_list'));
        }

        return $this->render('UserBundle:Default:editUser.html.twig', array('user'       => $user,
                                                                            'form_name'  => $form->getName(),
                                                                            'form'       => $form->createView()));
    }
    
    public function deleteUserAction($id){
        
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false){
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getEntityManager();
        $user = $em->getRepository("UserBundle:User")->find($id);
        if (!$user) throw $this->createNotFoundException('Usuario no encontrado en la BBDD');
        
        $em->remove($user);
        $em->flush();
        
        return $this->redirect($this->generateUrl('user_list'));
    }
    
    public function newUserAction(){
        
        $user  = new User();
        $request = $this->getRequest();
        $form = $this->createForm(new UserType(), $user);
        $form->bindRequest($request);
        
         if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $user->setCreatedAt(new \DateTime(\date("Y-m-d H:i:s")));
            $this->saveUser($em, $user);
            
            return $this->redirect($this->generateUrl('user_list'));
         }
        
        return $this->render('UserBundle:Default:newUser.html.twig', array('user'       => $user,
                                                                           'form_name'  => $form->getName(),
                                                                           'form'       => $form->createView()));
    }
    
    /**
     * Hace el save de un usuario
     * @param EntityManager $em
     * @param User $user
     */
    private function saveUser($em, $user){
        
        //codificamos el password
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        $salt = md5(time());
        $password = $encoder->encodePassword($user->getPassword(), $salt);
        $user->setPassword($password);
        $user->setSalt($salt);
        $user->setModifiedAt(new \DateTime(\date("Y-m-d H:i:s")));
        $user->setModifyBy($this->get('security.context')->getToken()->getUser());

        $em->persist($user);
        $em->flush();
    }
}
