<?php

namespace Adservice\UtilBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class LanguageController extends Controller{

    public function changeLanguageAction(Request $request, $lang){
        // Cambio el idioma

        $currentLocale = $request->getLocale();
        $request->setLocale($lang);
        $currentPath = $_SERVER['HTTP_REFERER'];
        $currentPath = str_replace('/'.$currentLocale.'/', '/'.$lang.'/', $currentPath);

        // Vuelvo a dónde estaba (fuera donde fuera)
        return new RedirectResponse($currentPath);

        // Cambio el idioma
        //
        // $request->setLocale($lang);
        // $path = substr($request->get('path'), 3);
        // $url = $request->getBaseUrl().'/'.$request->getLocale().$path;
        // // Vuelvo a dónde estaba (fuera donde fuera)
        // return new RedirectResponse($url);
    }
}