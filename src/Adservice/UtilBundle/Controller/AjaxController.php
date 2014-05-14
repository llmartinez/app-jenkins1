<?php

namespace Adservice\UtilBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Adservice\UtilBundle\Entity\Region;
use Adservice\PartnerBundle\Entity\Shop;
use Adservice\CarBundle\Entity\Brand;
use Adservice\CarBundle\Entity\Model;
use Adservice\CarBundle\Entity\Version;
use Adservice\TicketBundle\Entity\System;
use Adservice\TicketBundle\Entity\Subsystem;

class AjaxController extends Controller
{

    //  ____  _____ ____ ___ ___  _   _
    // |  _ \| ____/ ___|_ _/ _ \| \ | |
    // | |_) |  _|| |  _ | | | | |  \| |
    // |  _ <| |__| |_| || | |_| | |\  |
    // |_| \_\_____\____|___\___/|_| \_|

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

    //  ____ ___ _______   __
    //  / ___|_ _|_   _\ \ / /
    // | |    | |  | |  \ V /
    // | |___ | |  | |   | |
    //  \____|___| |_|   |_|

    /**
     * Funcion Ajax para obtener las ciudades de una region
     * @return json
     */
    public function citiesFromRegionAction() {
       $em = $this->getDoctrine()->getEntityManager();
       $petition = $this->getRequest();
       $id_region = $petition->request->get('id_region');

       $cities = $em->getRepository("UtilBundle:City")->findBy(array('region' => $id_region));
       if(count($cities) > 0) {
            foreach ($cities as $city) {
                $json[] = $city->to_json();
            }
        }else{
                $json = array( 'error' => 'No hay coincidencias');
        }
       return new Response(json_encode($json), $status = 200);
    }

    //  ____  _   _  ___  ____
    // / ___|| | | |/ _ \|  _ \
    // \___ \| |_| | | | | |_) |
    //  ___) |  _  | |_| |  __/
    // |____/|_| |_|\___/|_|

    /**
     * Funcion Ajax para obtener las tiendas de un socio
     * @return json
     */
    public function shopsFromPartnerAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();
        $id_partner = $petition->request->get('id_partner');
        $partner = $em->getRepository("PartnerBundle:Partner")->find($id_partner);

        $shops = $em->getRepository("PartnerBundle:Shop")->findBy(array('partner' => $partner));
        if(count($shops) > 0) {
            foreach ($shops as $shop) {
                $json[] = $shop->to_json();
            }
        }else{
                $json = array( 'error' => 'No hay coincidencias');
        }
        return new Response(json_encode($json), $status = 200);
    }

    //   ____    _    ____
    //  / ___|  / \  |  _ \
    // | |     / _ \ | |_) |
    // | |___ / ___ \|  _ <
    //  \____/_/   \_\_| \_\

    /**
     * Funcion Ajax que devuelve un listado de modelos filtrados a partir de la marca ($brand)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function carModelAction($id_brand) {
        $em = $this->getDoctrine()->getEntityManager();

        $brand = $em->getRepository('CarBundle:Brand')->find($id_brand);

        $models = $em->getRepository('CarBundle:Model')->findBy(array('brand' => $brand->getId()));
        foreach ($models as $model) {
            $json[] = $model->to_json();
        }
        return new Response(json_encode($json), $status = 200);
    }

    /**
     * Funcion Ajax que devuelve un listado de versiones filtrados a partir del modelo ($model)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function carVersionAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();

        $id_model = $petition->request->get('id_model');
        $model = $em->getRepository('CarBundle:Model')->find($id_model);

        $versions = $em->getRepository('CarBundle:Version')->findBy(array('model' => $model->getId()));
        foreach ($versions as $version) {
            $json[] = $version->to_json();
        }
        return new Response(json_encode($json), $status = 200);
    }

//  _____ ___ ____ _  _______ _____
// |_   _|_ _/ ___| |/ / ____|_   _|
//   | |  | | |   | ' /|  _|   | |
//   | |  | | |___| . \| |___  | |
//   |_| |___\____|_|\_\_____| |_|

    /**
     * Funcion Ajax que devuelve un listado de subsistemas filtrados a partir del sistema ($system)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ticketSystemAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();

        $id_system = $petition->request->get('id_system');

        $system = $em->getRepository('TicketBundle:System')->find($id_system);

        $subsystems = $em->getRepository('TicketBundle:Subsystem')->findBy(array('system' => $system->getId()));
        foreach ($subsystems as $subsystem) {
            $json[] = $subsystem->to_json();
        }
        return new Response(json_encode($json), $status = 200);
    }
}
