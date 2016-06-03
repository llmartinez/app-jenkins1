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
        $_SESSION['lang'] = null;

        // Para controlar si es IE11 o superior...
        if(preg_match("/(Trident\/(\d{2,}|7|8|9)(.*)rv:(\d{2,}))|(MSIE\ (\d{2,}|8|9)(.*)Tablet\ PC)|(Trident\/(\d{2,}|7|8|9))/", $u_agent))
        {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        }

var_dump($u_agent);
var_dump(!$this->get('isMSIE')->isMSIE($request));
var_dump('<br>Trident/7.0; rv:11 => ');
var_dump(strpos('Trident/7.0; rv:11', $u_agent) != false);
var_dump('<br>Trident/6.0; => ');
var_dump(strpos('Trident/6.0;', $u_agent) != false);
var_dump('<br>Trident/5.0; => ');
var_dump(strpos('Trident/5.0;', $u_agent) != false);
var_dump('<br>MSIE 10.0 => ');
var_dump(strpos('MSIE 10.0', $u_agent) != false);
var_dump('<br>MSIE 9.0 => ');
var_dump(strpos('MSIE 9.0;', $u_agent) != false);

        if(!$this->get('isMSIE')->isMSIE($request) or (strpos('Trident/7.0; rv:11') != false) or (strpos('Trident/6.0;') != false)){
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
        return $this->redirect($this->generateUrl('user_index'));
    }
}

?>