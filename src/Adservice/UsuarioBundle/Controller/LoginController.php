<?php

namespace Adservice\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
//use Symfony\Component\HttpFoundation\Response;
//use Adservice\UserBundle\Form\UserType;
use Adservice\UsuarioBundle\Entity\User;

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

        return $this->render('UsuarioBundle:Login:login.html.twig', array('last_username'  => $session->get(SecurityContext::LAST_USERNAME),
                                                                          'error'          => $error));
        
        
    }

}

?>