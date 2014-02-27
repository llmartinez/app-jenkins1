<?php

namespace Adservice\UtilBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Adservice\UtilBundle\Entity\Region;
use Adservice\UtilBundle\Entity\Province;

class DefaultController extends Controller
{
    
     public function provincesFromRegionAction(){
        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();
        $id_region = $petition->request->get('id_region');

        $provinces = $em->getRepository("UtilBundle:Province")->findBy(array('region' => $id_region));
        foreach ($provinces as $province) {
            $json[] = $province->to_json();
        }
        return new Response(json_encode($json), $status=200);
    }
}
