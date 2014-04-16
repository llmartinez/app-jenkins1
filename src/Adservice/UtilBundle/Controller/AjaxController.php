<?php

namespace Adservice\UtilBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Adservice\UtilBundle\Entity\Region;
use Adservice\PartnerBundle\Entity\Shop;
use Adservice\CarBundle\Entity\Brand;
use Adservice\CarBundle\Entity\Model;

class AjaxController extends Controller
{
    /**
     * Funcion Ajax para obtener las regiones de un país
     * @return json
     */
    public function regionsFromCountryAction() {
       $em = $this->getDoctrine()->getEntityManager();
       $petition = $this->getRequest();
       $id_country = $petition->request->get('id_country');

       $regions = $em->getRepository("UtilBundle:Region")->findBy(array('country' => $id_country));
       if(count($regions) > 0) {
            foreach ($regions as $region) {
                $json[] = $region->to_json();
            }
        }else{
                $json = array( 'error' => 'No hay coincidencias');
        }
       return new Response(json_encode($json), $status = 200);
    }

    /**
     * Funcion Ajax para obtener las tiendas de un socio
     * @return json
     */
    public function shopsFromPartnerAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();
        $id_partner = $petition->request->get('id_partner');

        $shops = $em->getRepository("PartnerBundle:Shop")->findBy(array('partner' => $id_partner));
        if(count($shops) > 0) {
            foreach ($shops as $shop) {
                $json[] = $shop->to_json();
            }
        }else{
                $json = array( 'error' => 'No hay coincidencias');
        }
        return new Response(json_encode($json), $status = 200);
    }

}
