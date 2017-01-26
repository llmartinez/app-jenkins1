<?php 

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
    public function loginAction()
    {
	    $authenticationUtils = $this->get('security.authentication_utils');

	    return $this->render('default/login.html.twig', array(
	        'last_username' => $authenticationUtils->getLastUsername(),
	        'error'         => $authenticationUtils->getLastAuthenticationError(),
	    ));
    }
}