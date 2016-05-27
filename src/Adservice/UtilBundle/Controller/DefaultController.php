<?php

namespace Adservice\UtilBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

use Adservice\UtilBundle\Controller\UtilController;

class DefaultController extends Controller{

    public function helpAction(){

        return $this->render('UtilBundle:Default:help.html.twig');
    }

    public function privacyAction(){

        return $this->render('UtilBundle:Default:privacy.html.twig');
    }
}