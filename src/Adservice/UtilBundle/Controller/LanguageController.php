<?php

namespace Adservice\UtilBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LanguageController extends Controller{

    public function changeLanguageAction($lang){
        // Cambio el idioma
        $request = $this->getRequest();
        $currentLocale = $request->getLocale();
        $request->setLocale($lang);
        $currentPath = $_SERVER['HTTP_REFERER'];
        $currentPath = str_replace('/'.$currentLocale.'/', '/'.$lang.'/', $currentPath);

        // Vuelvo a dónde estaba (fuera donde fuera)
        return new RedirectResponse($currentPath);

        // Cambio el idioma
        // $request = $this->getRequest();
        // $request->setLocale($lang);
        // $path = substr($request->get('path'), 3);
        // $url = $request->getBaseUrl().'/'.$request->getLocale().$path;
        // // Vuelvo a dónde estaba (fuera donde fuera)
        // return new RedirectResponse($url);
    }
}