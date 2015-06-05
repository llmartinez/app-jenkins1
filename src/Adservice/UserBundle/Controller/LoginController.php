<?php

namespace Adservice\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
//use Symfony\Component\HttpFoundation\Response;
//use Adservice\UserBundle\Form\UserType;
use Adservice\UserBundle\Entity\User;

class LoginController extends Controller {

    public function loginAction() {
        $request = $this->getRequest();
        $session = $request->getSession();

        $u_agent = $_SERVER['HTTP_USER_AGENT'];

        // Para controlar si es IE11 o superior...
        if((preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) || (preg_match("/(Trident\/(\d{2,}|7|8|9)(.*)rv:(\d{2,}))|(MSIE\ (\d{2,}|8|9)(.*)Tablet\ PC)|(Trident\/(\d{2,}|7|8|9))/", $u_agent)))
        {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        }

        if(!$this->get('isMSIE')->isMSIE($request)){
            // obtiene el error de inicio de sesión si lo hay
            if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
                $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
            } else {
                $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            }

            return $this->render('UserBundle:Login:login.html.twig', array('last_username'  => $session->get(SecurityContext::LAST_USERNAME),
                                                                           'error'          => $error));
        }else{
            return $this->render('UserBundle:Login:error_explorer.html.twig');
        }
    }

    public function goToLoginAction() {
        return $this->redirect($this->generateUrl('user_login'));
    }
}

?>