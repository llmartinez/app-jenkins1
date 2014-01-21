<?php

namespace Adservice\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;

use Adservice\UserBundle\Form\UserProfileType;
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
        
        return $this->render('UserBundle:login:login.html.twig', array('last_username'  => $session->get(SecurityContext::LAST_USERNAME),
                                                                       'error'          => $error));
    }

    /**
     * Obtener los datos del usuario logueado
     * Si la petición es GET, mostrar el formulario
     * Si la petición es POST, actualizar la información del usuario con los nuevos datos obtenidos del formulario
     */
    public function profileAction(){
        // Aquí tengo que recoger el locale del usuario
//        return $this->redirect($this->generateUrl('login_ok'));
        
        $em = $this->getDoctrine()->getEntityManager();

        $usuario = $this->get('security.context')->getToken()->getUser();
        $username = $usuario->getUsername();
        $user = $em->getRepository("UserBundle:User")->findByUsername($username);
        
        $request = $this->getRequest();
        $form_profile = $this->createForm(new UserProfileType(), $user);
        $form_profile->bindRequest($request);
        
        if ($form_profile->isValid()) {
            
            echo "envio OK del formulario del perfil";
            
        }
        
        //vamos a la edicion del usuario logeado
        return $this->render('UserBundle:User:profile.html.twig', array('edit_form_profile' => $form_profile->createView()));
        
        
    }
    
    public function homeAction(){
        return $this->render('UserBundle:Default:index.html.twig', array());
    }
}

?>
