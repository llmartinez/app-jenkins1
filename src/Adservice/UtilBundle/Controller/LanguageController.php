<?php

namespace Adservice\UtilBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LanguageController extends Controller{
        
    public function changeLanguageAction($lang){
        // Cambio el idioma
        $request = $this->getRequest();
        $request->setLocale($lang);
        $path = substr($request->get('path'), 3);
        $url = $request->getBaseUrl().'/'.$request->getLocale().$path; 
        // Vuelvo a dónde estaba (fuera donde fuera)
        return new RedirectResponse($url);
    }
}
            // Cambio el idioma
//        $request = $this->getRequest();
//        $request->setLocale($lang);
//        
//        $session_locale = $this->getRequest()->getLocale();
//        $user_locale = $this->get('security.context')->getToken()->getUser()->getLanguage()->getShortName();
//        
//        ($session_locale != null) ? $this->getRequest()->setLocale($session_locale) : $this->getRequest()->setLocale($user_locale);
//        
//        $router = $this->container->get('router');
//        $route = $router->getRouteCollection()->get('newTicket');
//        $controllerAction = $route->getPattern();
//        echo "<br>";
//        var_dump($route);
//        echo "<br>";
//        echo 'pattern: '.$controllerAction;
//        echo "<br>";
//        echo $this->generateUrl('newTicket');
//        
//        echo "<br>";
//        $router = $this->get("router");
//        $route = $router->match($this->getRequest()->getPathInfo());
//        var_dump($route['_route']);
////        
//        die;
//        $this->get('session')->setLocale($lang);
//        echo "_locale: ".$request->getLocale();
//        echo "<br>";
//        echo "user:    ".$user_locale;
//        echo "<br>";
//        echo $referer = $request->headers->get('referer'); 
//        
//        return $this->redirect($request->headers->get('referer'));
//        
//        
//        
//    public function changeLanguageAction(){
//        echo "changeLanguage";
//    
    //        var_dump($lang);
//        echo "hola";
//        die;
////        
//        // Vuelvo a dónde estaba (fuera donde fuera)
//        $referer = $request->headers->get('referer');
//        return new RedirectResponse($referer);
//  //        // Vuelvo a dónde estaba (fuera donde fuera)
//        $referer = $request->headers->get('referer');
//        return new RedirectResponse($referer);
//        
////        if($session_locale != null){
////            $this->getRequest()->setLocale($session_locale);
////        }else{
////            $this->getRequest()->setLocale($user_locale);
////        }
//      }
//}