<?php

namespace Adservice\UtilBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class GenericController extends Controller{
    public function changeLanguageAction(){
        // Cambio el idioma
//        $request = $this->getRequest();
        //$url = $request->getBaseUrl().'/'.$request->getLocale().substr($request->get('path'), 3);   
        // Vuelvo a dónde estaba (fuera donde fuera)
//        $request->headers->set('_locale', $request->getLocale()); 
//        echo 'locale: '.$request->headers->get('_locale');
//        echo "<br>";
//        echo $request->get('_locale');
//        echo $referer = $request->headers->get('referer'); 
//        die;
//        return  $this->redirect($referer);
    }
}