<?php

namespace Adservice\UtilBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LanguageController extends Controller{
    
//    public function changeLanguageAction($lang){
//        var_dump($lang);
//        echo "hola";
//        die;
//        
////        // Cambio el idioma
////        $request = $this->getRequest();
////        $request->setLocale($lang);
////        
////        // Vuelvo a dónde estaba (fuera donde fuera)
////        $referer = $request->headers->get('referer');
////        return new RedirectResponse($referer);
//        
//        $session_locale = $this->getRequest()->getLocale();
//        $user_locale = $this->get('security.context')->getToken()->getUser()->getLanguage()->getShortName();
//        
//        ($session_locale != null) ? $this->getRequest()->setLocale($session_locale) : $this->getRequest()->setLocale($user_locale);
//        // Vuelvo a dónde estaba (fuera donde fuera)
//        $referer = $request->headers->get('referer');
//        return new RedirectResponse($referer);
//        
////        if($session_locale != null){
////            $this->getRequest()->setLocale($session_locale);
////        }else{
////            $this->getRequest()->setLocale($user_locale);
////        }
//        
//    }
    
//    public function changeLanguageAction(){
//        echo "changeLanguage";
//    }
    
//    public function changeLanguageAction($locale){
////        echo $locale;die;
////        $this->get('session')->setLocale($locale);
//        $this->getRequest()->setLocale($locale);
//        $request = $this->getRequest();
//        return $this->redirect($request->headers->get('referer'));
//        
//    }
    
}
