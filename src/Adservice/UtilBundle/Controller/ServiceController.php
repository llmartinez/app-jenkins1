<?php

namespace Adservice\UtilBundle\Controller;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\DependencyInjection\Container;

class ServiceController {
    protected $container;

    public function __construct(Container $container){
        $this->container = $container;
    }
    
    public function setDefaultLanguage(Event $event){
        $request = $event->getRequest();
        //obtenemos el defaultLocale que tenemos en Parameters.ini
        $defaultLocale = $this->container->getParameter('session.default_locale');
        //seteamos el idioma        
        //$request->setLocale($defaultLocale);
    }
}