<?php

namespace Adservice\UtilBundle\Controller;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\DependencyInjection\Container;

class ServiceController {
    protected $container;

    public function __construct(Container $container=null){
        $this->container = $container;
    }

    public function setDefaultLanguage(Event $event){
        $request = $event->getRequest();
        //obtenemos el defaultLocale que tenemos en Parameters.ini
        $defaultLocale = $this->container->getParameter('locale');
        //seteamos el idioma
        //$request->setLocale($defaultLocale);

        $request = $event->getRequest();
//        //obtenemos el defaultLocale que tenemos en Parameters.ini
//        $defaultLocale = $this->container->getParameter('session.default_locale');
//        $session_locale = $request->getLocale();
////        $user_locale = $this->get('security.context')->getToken()->getUser()->getLanguage()->getShortName();
//        $token = $event->
//        $user_locale = $token->getUser();
//
//        //seteamos el idioma
//        //$request->setLocale($defaultLocale);
//        var_dump($defaultLocale);
//        var_dump($session_locale);
//        var_dump($user_locale);
//        die;
    }

    public function isMSIE($request){

        $u_agent = $request->headers->get('user-agent');
        if((preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) || (preg_match("/(Trident\/(\d{2,}|7|8|9)(.*)rv:(\d{2,}))|(MSIE\ (\d{2,}|8|9)(.*)Tablet\ PC)|(Trident\/(\d{2,}|7|8|9))/", $u_agent))) {
        //if(preg_match('/(?i)msie [1-9]/',$request->headers->get('user-agent'))){
            return true;
        }else{
            return false;
        }
    }
}

