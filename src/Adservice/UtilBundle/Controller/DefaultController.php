<?php

namespace Adservice\UtilBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller{

    public function helpAction(){

        return $this->render('UtilBundle:Default:help.html.twig');
    }
}