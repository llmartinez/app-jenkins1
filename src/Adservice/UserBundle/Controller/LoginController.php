<?php

namespace Adservice\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

//use Symfony\Component\HttpFoundation\Response;
//use Adservice\UserBundle\Form\UserType;
use Adservice\UserBundle\Entity\User;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends Controller
{

    public function loginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $_SESSION['lang'] = null;

        // Para controlar si es IE11 o superior...
        // if(preg_match("/(Trident\/(\d{2,}|7|8|9)(.*)rv:(\d{2,}))|(MSIE\ (\d{2,}|8|9)(.*)Tablet\ PC)|(Trident\/(\d{2,}|7|8|9))/", $u_agent))
        // {
        //     $bname = 'Internet Explorer';
        //     $ub = "MSIE";
        // }

        if (!$this->get('isMSIE')->isMSIE($request)
            or (strpos($u_agent, 'Trident/7.0') != false and strpos($u_agent, ' rv:11') != false)
        ) {
            $error = $authenticationUtils->getLastAuthenticationError();

            $lastUsername = $authenticationUtils->getLastUsername();

            return $this->render(
                'UserBundle:Login:login.html.twig',
                array(
                    'last_username' => $lastUsername,
                    'error' => $error,
                )
            );

        } else {
            return $this->render('UserBundle:Login:error_explorer.html.twig');
        }
    }

    public function goToLoginAction(Request $request)
    {
        return $this->redirect($this->generateUrl('user_index'));
    }
}

?>