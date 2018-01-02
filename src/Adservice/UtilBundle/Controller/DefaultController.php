<?php

namespace Adservice\UtilBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller{

    public function helpAction(Request $request){

        return $this->render('UtilBundle:Default:help.html.twig');
    }
    
    public function privacyAction(Request $request){

        
        if ($request->getMethod() == 'POST') {
            if ($request->request->has('Return')) {                
                return $this->redirect($this->generateUrl('user_index'));
            }
        }
        return $this->render('UtilBundle:Default:privacy.html.twig');
    }
    
    
}