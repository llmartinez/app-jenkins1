<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction(Request $request, $_locale=null)
    {
        if($_locale == null) {
            $lc = $this->get('locale')->getDefaultLocale($request);
            return $this->redirect($this->generateUrl('index', array('_locale' => $lc) ));
        }

        $this->get('locale')->changeLocale($this, $request, $_locale);

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ));
    }

    public function changeLangAction(Request $request, $locale)
    {
        $currentPath = $request->server->get('HTTP_REFERER');

        if($locale != null AND $locale != $request->getLocale())
        {
            $currentLocale = $request->getLocale();

            if($locale != $currentLocale)
            {
                // Cambio el idioma
                $request->setLocale($locale);
                $currentPath = str_replace('/'.$currentLocale.'/', '/'.$locale.'/', $currentPath);
            }
        }

        if($currentPath != "") return $this->redirect($currentPath);
        else                   return $this->redirect($this->generateUrl('index', array('_locale' => $locale)));
    }

    public function helpAction()
    {
        //return $this->get('export')->xls($this);

        //$html = $this->renderView('::default/menu/menu_ROLE_ADVISOR.html.twig');
        //return $this->get('export')->pdf($this, "TEST", $html);

        return $this->render('default/help.html.twig');
    }

}
