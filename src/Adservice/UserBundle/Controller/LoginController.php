<?php

namespace Adservice\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;
use Adservice\UserBundle\Form\UserType;
use Adservice\UserBundle\Entity\User;

class LoginController extends Controller {

    public function loginAction() {
        $request = $this->getRequest();
        $session = $request->getSession();
        // obtiene el error de inicio de sesión si lo hay
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render('UserBundle:login:login.html.twig', array('last_username' => $session->get(SecurityContext::LAST_USERNAME),
                                                                       'error' => $error));
    }

    /**
     * Obtener los datos del usuario logueado
     * Si la petición es GET, mostrar el formulario
     * Si la petición es POST, actualizar la información del usuario con los nuevos datos obtenidos del formulario
     */
    public function profileAction() {

        $em = $this->getDoctrine()->getEntityManager();
        $logged_user = $this->get('security.context')->getToken()->getUser();
        $id_user = $logged_user->getId();
        $user = $em->getRepository('UserBundle:User')->find($id_user);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createForm(new UserType(), $user);
//        $deleteForm = $this->createDeleteForm($id);

        return $this->render('UserBundle:User:profile.html.twig', array('user'          => $user,
                                                                        'edit_form'     => $editForm->createView(),
//                                                                        'delete_form'   => $deleteForm->createView()
                ));













        // Aquí tengo que recoger el locale del usuario
//        return $this->redirect($this->generateUrl('login_ok'));
//        
//        $em = $this->getDoctrine()->getEntityManager();
//
//        $usuario = $this->get('security.context')->getToken()->getUser();
//        $username = $usuario->getUsername();
//        $user = $em->getRepository("UserBundle:User")->findByUsername($username);
//        
//        $request = $this->getRequest();
//        $form_profile = $this->createForm(new UserProfileType(), $user);
//        $form_profile->bindRequest($request);
//        
//        if ($form_profile->isValid()) {
//            
//            echo "envio OK del formulario del perfil";
//            
//        }
//        
//        //vamos a la edicion del usuario logeado
//        return $this->render('UserBundle:User:profile.html.twig', array('edit_form_profile' => $form_profile->createView()));
    }

    public function homeAction() {
        return $this->render('UserBundle:Default:index.html.twig', array());
    }

}

?>
