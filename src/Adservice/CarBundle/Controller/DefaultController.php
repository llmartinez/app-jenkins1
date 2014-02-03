<?php

namespace Adservice\CarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('CarBundle:Default:index.html.twig', array('name' => $name));
    }
}
