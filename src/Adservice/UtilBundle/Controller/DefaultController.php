<?php

namespace Adservice\UtilBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Adservice\UtilBundle\Entity\Region;
use Adservice\UtilBundle\Entity\Province;

class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('UtilBundle:Default:index.html.twig', array('name' => $name));
    }
    
    /**
     * Get all provinces of one region
     * En españá obtiene todas las provincias a partir de una comunidad autonoma
     * @param type $region
     * @return string
     */
//    public function _selectProvinceAction($region){
//        $foo =  "_selectProvinceAction";
//        return $this->render('UtilBundle:Default:_select_province.html.twig', array('foo' => $foo));
//
//    }
    
     public function provincesFromRegionAction(){
        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();
        $id_region = $petition->request->get('id_region');

        $em = $this->getDoctrine()->getEntityManager();
//        echo $id_region;die;
//        $region = $em->getRepository("UtilBundle:Region")->findOneBy(array('id' => $id_region));
//        $region = $em->getRepository("UtilBundle:Region")->find($id_region);
        $provinces = $em->getRepository("UtilBundle:Province")->findBy(array('region' => $id_region));
//        $provinces = $em->getRepository("UtilBundle:Province")->findAll();
//        var_dump($provinces);die;
//        $json = array();
//        foreach($provinces as $key => $value) {
//            $json[$key] = $value;
//        }
//        return $json; // or json_encode($json)
//        return json_encode($json);

        return new Response(json_encode($provinces), $status=200);
//        return new Response(json);
//         return new Response(json_encode($provinces), 200); //200 = response OK

                
    }
}
