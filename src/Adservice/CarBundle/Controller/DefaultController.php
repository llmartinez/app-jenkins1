<?php

namespace Adservice\CarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller {

    public function carModelAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();
        
        $id_brand = $petition->request->get('id_brand');
        $brand = $em->getRepository('CarBundle:Brand')->find($id_brand);
        
        $models = $em->getRepository('CarBundle:Model')->findBy(array('brand' => $brand->getId()));
        return new Response(json_encode($models), $status = 200);
    }
    
    public function carVersionAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();
        
        $id_model = $petition->request->get('id_model');
        $model = $em->getRepository('CarBundle:Model')->find($id_model);
        
        $version = $em->getRepository('CarBundle:Version')->findBy(array('model' => $model->getId()));
        return new Response(json_encode($version), $status = 200);
    }

}
