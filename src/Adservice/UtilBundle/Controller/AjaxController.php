<?php

namespace Adservice\UtilBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
        $size = sizeOf($regions);
        if($size > 0) {
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
        $size = sizeOf($cities);
        if($size > 0) {
            foreach ($cities as $city) {
                $json[] = $city->to_json();
            }
        }else{
                $json = array( 'error' => 'No hay coincidencias');
        }
        return new Response(json_encode($json), $status = 200);
    }

    //  ____
    // |  _ \
    // | |_) |
    // |  __/
    // |_|artner

    /**
     * Funcion Ajax para obtener las tiendas de un socio
     * @return json
     */
    public function codePartnerFromPartnerAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();
        $id_partner = $petition->request->get('id_partner');

        $partner = $em->getRepository("PartnerBundle:Partner")->find($id_partner);
        $size = sizeOf($partner);
        if($size > 0) {
            $json[] = $partner->to_json();
        }else{
                $json = array( 'error' => 'No hay coincidencias');
        }
        return new Response(json_encode($json), $status = 200);
    }

    public function codeWorkshopFromPartnerAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();

        $id_partner = $petition->request->get('id_partner');
        $partner = $em->getRepository("PartnerBundle:Partner")->find($id_partner);

        $workshop = UtilController::getCodeWorkshopUnused($em,$partner);
        $json = array('code' => $workshop);

        return new Response(json_encode($json), $status = 200);
    }

    public function getIdFromCodePartnerAction($code)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();
        $code = $petition->request->get('code');

        $partner = $em->getRepository("PartnerBundle:Partner")->findOneBy(array('code_partner' => $code));

        if (isset($partner) and $partner->getId() != null) {

            $workshop = UtilController::getCodeWorkshopUnused($em,$partner);

            $json = array('id' => $partner->getId(), 'code' => $workshop);
        }
        else $json = array('id' => '0', 'code' => '0');

        return new Response(json_encode($json), $status = 200);
    }

    public function getCountryPartnerAction($id_partner)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $partner = $em->getRepository("PartnerBundle:Partner")->find($id_partner);

        if (isset($partner) and $partner->getId() != null) {

            $country = $partner->getCountry();

            $name = $this->get('translator')->trans($country->getCountry());

            $json = array('id' => $country->getId(), 'name' => $name);
        }
        else $json = array('id' => '0', 'name' => '0');

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

        $query = "SELECT s FROM PartnerBundle:Shop s WHERE s.id = 1 ";
        if($id_partner != '') $query .= "OR s.partner = ".$id_partner." ";

        $consulta = $em->createQuery($query);
        $shops   = $consulta->getResult();

        $size = sizeOf($shops);
        if($size > 0) {
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
    public function carModelAction($id_brand, $filter='', $filter_value='') {
        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();

        // $id_mts = '';
        if($filter != '') {
            if($filter == 'motor')
                $query = "SELECT m FROM CarBundle:Brand b, CarBundle:Model m, CarBundle:Version v, CarBundle:Motor mt
                          WHERE b.id = m.brand AND m.id = v.model AND mt.id = v.motor AND b.id = ".$id_brand."
                          AND mt.name like '%".$filter_value."%'
                          ORDER BY m.name";
            elseif($filter == 'year')
                $query = "SELECT m FROM CarBundle:Brand b, CarBundle:Model m
                          WHERE b.id = m.brand AND b.id = ".$id_brand." AND m.brand IS NOT NULL
                          AND (m.inicio <= '".$filter_value."99' AND m.inicio != '') AND (m.fin >= '".$filter_value."00' OR m.fin = '')
                          GROUP BY m.id ORDER BY m.name";

            $consulta = $em->createQuery($query);
            $models   = $consulta->getResult();

            // $size = sizeOf($models);
            // if($size == 0) {

            //     $id_mts = $petition->request->get('id_mts');

            //     if($id_mts == '')
            //     {
            //         $query = "SELECT partial mt.{id, name} FROM CarBundle:Motor mt";
            //         $consulta = $em->createQuery($query);
            //         $motors   = $consulta->getResult();

            //         $motor = $petition->request->get('motor');
            //         $motor = UtilController::getSlug($motor, '');

            //         $id_mts = $this->getMotorsId($motors, $motor);
            //     }

            //     $query = "SELECT m FROM CarBundle:Model m, CarBundle:Version v
            //               WHERE m.id = v.model AND v.motor IN ".$id_mts."
            //               ORDER BY m.name ASC";
            //     $consulta = $em->createQuery($query);
            //     $models   = $consulta->getResult();
            // }
        }
        else{
            //$models = $em->getRepository('CarBundle:Model')->findBy(array('brand' => $id_brand), array('name' => 'ASC'));
            $query = "SELECT m FROM CarBundle:Model m
                          WHERE m.brand = ".$id_brand."
                          ORDER BY m.name";

            $consulta = $em->createQuery($query);
            $models   = $consulta->getResult();
        }

        $size = sizeOf($models);
        if($size > 0) {
            foreach ($models as $model) {
                $json[] = $model->to_json();
            }
            // $json['id_mts'] = $id_mts;
        }else{
            $json = array( 'error' => 'No hay coincidencias');
        }
        return new Response(json_encode($json), $status = 200);
    }

    /**
     * Funcion Ajax que devuelve un listado de versiones filtrados a partir del modelo ($model)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function carVersionAction($id_model, $filter='', $filter_value='') {
        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();

        // $id_mts = '';
        if($filter != '') {
            if($filter == 'motor')
                $query = "SELECT v FROM CarBundle:Brand b, CarBundle:Model m, CarBundle:Version v, CarBundle:Motor mt
                          WHERE b.id = m.brand AND m.id = v.model AND mt.id = v.motor
                          AND m.id = ".$id_model." AND mt.name like '%".$filter_value."%'
                          ORDER BY v.name";
            elseif($filter == 'year')
                $query = "SELECT v FROM CarBundle:Version v
                          WHERE v.model = ".$id_model." AND v.model IS NOT NULL
                          AND (v.inicio <= '".$filter_value."99' AND v.inicio != '') AND (v.fin >= '".$filter_value."00' OR v.fin = '')
                          ORDER BY v.name";

            $consulta = $em->createQuery($query);
            $versions = $consulta->getResult();
            // $size = sizeOf($versions);
            // if($size == 0) {

            //     $id_mts = $petition->request->get('id_mts');

            //     if($id_mts == '')
            //     {
            //         $query = "SELECT partial mt.{id, name} FROM CarBundle:Motor mt";
            //         $consulta = $em->createQuery($query);
            //         $motors   = $consulta->getResult();

            //         $motor = $petition->request->get('motor');
            //         $motor = UtilController::getSlug($motor, '');

            //         $id_mts = $this->getMotorsId($motors, $motor);
            //     }

            //     $query = "SELECT v FROM CarBundle:Version v
            //               WHERE v.motor IN ".$id_mts."
            //               ORDER BY v.name ASC";
            //     $consulta = $em->createQuery($query);
            //     $versions   = $consulta->getResult();
            // }
        }
        else{
            $q_version = $em->createQuery("SELECT v FROM CarBundle:Motor m, CarBundle:Version v
                                           WHERE v.model = ".$id_model." AND v.motor = m.id ORDER BY v.name ASC");
            $versions = $q_version->getResult();
        }

        // if($id_mts == '') {
            $q_motor = $em->createQuery("SELECT partial m.{id,name} from CarBundle:Motor m, CarBundle:Version v
                                        WHERE v.motor = m.id AND v.model = ".$id_model." GROUP BY m.name");
        // }else{
        //     $q_motor = $em->createQuery("SELECT partial m.{id,name} from CarBundle:Motor m, CarBundle:Version v
        //                                 WHERE v.motor = m.id AND m.id IN ".$id_mts." AND v.model = ".$id_model." ");
        // }
        $res_motors = $q_motor->getResult();

        $motors = array();
        foreach ( $res_motors as $motor) { $motors[$motor->getId()] = $motor->getName(); }

        $size = sizeOf($versions);
        if($size > 0) {
            foreach ($versions as $version) {

                $id_motor = $version->getMotor();

                if(isset($motors[$id_motor])) {
                    $m_name = $motors[$id_motor];
                    $v_name = $version->getName().' ['.$m_name.']';
                    $version->setName($v_name);

                    $json[] = $version->to_json();
                }

            }
            // $json['id_mts'] = $id_mts;
        }else{
                $json = array( 'error' => 'No hay coincidencias');
        }
        return new Response(json_encode($json), $status = 200);
    }

    /**
     * Funcion Ajax que devuelve los datos del coche introducido a partir de la version ($version)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function carDataAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();

        $id_version    = $petition->request->get('id_version');
        $version_motor = $petition->request->get('version_motor');
        $motor = $petition->request->get('motor');
        $year = $petition->request->get('year');

        if(isset($motor)){
            $motor = $petition->request->get('motor');
            $query = $em->createQuery("SELECT v FROM CarBundle:Version v, CarBundle:Motor mt
                                        WHERE mt.id = v.motor AND v.id = ".$id_version." AND mt.name LIKE '%".$motor."%'");
            $version = $query->getResult();
            $version = $version[0];
        }
        elseif($year != null){
            $year = $petition->request->get('year');
            $query = $em->createQuery("SELECT v FROM CarBundle:Version v
                                        WHERE v.id = ".$id_version."
                                        AND (v.inicio <= '".$year."99' AND v.inicio != '')
                                        AND (v.fin >= '".$year."00' OR v.fin = '')");
            $version = $query->getResult();
            $version = $version[0];
        }else{
            $query = $em->createQuery("SELECT v FROM CarBundle:Version v, CarBundle:Motor mt
                                        WHERE mt.id = v.motor AND v.id = ".$id_version." AND mt.name LIKE '%".$version_motor."%'");
            $version = $query->getResult();
            $version = $version[0];
        }

        if (isset($version)) {
            $id_motor = $version->getMotor();
            $motor = $em->getRepository('CarBundle:Motor')->find($id_motor);
            $version->setMotor($motor->getName());
        }

        if(isset($version)) {
            $json[] = $version->to_json();
        }
        else $json   = array( 'error' => 'No hay coincidencias');

        return new Response(json_encode($json), $status = 200);
    }

    /**
     * Funcion Ajax que devuelve los datos del coche introducido a partir de la version ($version)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function carByYearAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();

        $year = $petition->request->get('year');

        if(strlen($year) == 4)
        {
            $query = "SELECT b FROM CarBundle:Brand b, CarBundle:Model m WHERE b.id = m.brand AND m.brand IS NOT NULL
            AND (m.inicio <= '".$year."99' AND m.inicio != '') AND (m.fin >= '".$year."00' OR m.fin = '')
            GROUP BY b.id ORDER BY b.name ASC";

            $consulta = $em->createQuery($query);
            $brands   = $consulta->getResult();

            $size = sizeOf($brands);
            if($size > 0) {
                foreach ($brands as $brand) {
                    $json[] = $brand->to_json();
                }
            }else{
                    $json = array( 'error' => 'No hay coincidencias');
            }
        }else{
                $json = array( 'error' => 'msg_bad_filter');
        }

        return new Response(json_encode($json), $status = 200);
    }

    /**
     * Funcion Ajax que devuelve los datos del coche introducido a partir de la version ($version)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function carByMotorAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();

        $motor = $petition->request->get('motor');

        $query = "SELECT b FROM CarBundle:Brand b, CarBundle:Model m, CarBundle:Version v, CarBundle:Motor mt
                    WHERE b.id = m.brand AND m.id = v.model AND mt.id = v.motor
                    AND mt.name LIKE '%".$motor."%' ORDER BY b.name ASC";

        $consulta = $em->createQuery($query);
        $brands   = $consulta->getResult();

        $size = sizeOf($brands);
        if($size > 0) {
            foreach ($brands as $brand) {
                $json[] = $brand->to_json();
            }
            // $json['id_mts'] = '';
        }else{
            // // Slug del campo introducido
            // $motor = UtilController::getSlug($motor, '');

            // $query = "SELECT partial mt.{id, name} FROM CarBundle:Motor mt";
            // $consulta = $em->createQuery($query);
            // $motors   = $consulta->getResult();

            // $id_mts = $this->getMotorsId($motors, $motor);

            // $query = "SELECT b FROM CarBundle:Brand b, CarBundle:Version v
            //             WHERE b.id = v.brand AND v.motor IN ".$id_mts." ORDER BY b.name ASC";
            // $consulta = $em->createQuery($query);
            // $brands   = $consulta->getResult();

            // $size = sizeOf($brands);

            // if($size > 0) {
            //     foreach ($brands as $brand) {
            //         $json[] = $brand->to_json();
            //     }
            //     $json['id_mts'] = $id_mts;
            // }else{
                $json = array( 'error' => 'No hay coincidencias');
            // }
        }

        return new Response(json_encode($json), $status = 200);
    }

    public function getCarFromPlateNumberAction($idPlateNumber){
        $em = $this->getDoctrine();

        $car = $em->getRepository('CarBundle:Car')->findOneBy(array('plateNumber' => $idPlateNumber));
        if($car == null){
            $json = array( 'error' => 'No hay coincidencias');
        }
        else{
            $json = $car->to_json();

            $version = null;
            if($car->getVersion() != null){
                $version = $car->getVersion()->getId();
            }
        }
        return new Response(json_encode($json), $status = 200);
    }

    /**
     * Devuelve un array de ids de motores que coinciden con una cadena con un slug
     * @return $string
     */
    // public function getMotorsId($motors, $motor) {

    //     $id_mts = '(0';
    //     foreach ($motors as $mt) {
    //         $mt_name = $mt->getName();
    //         $mt_name = UtilController::getSlug($mt_name, '');

    //         $pos = strpos($mt_name, $motor);
    //         if ($pos !== false) {
    //             $id_mts .= ', '.$mt->getId();
    //         }
    //     }
    //     $id_mts .= ')';

    //     return $id_mts;
    // }

//  _____ ___ ____ _  _______ _____
// |_   _|_ _/ ___| |/ / ____|_   _|
//   | |  | | |   | ' /|  _|   | |
//   | |  | | |___| . \| |___  | |
//   |_| |___\____|_|\_\_____| |_|

    /**
     * Muestra los posts que pertenecen a un ticket
     * @Route("/ticket/show_read/{id}")
     * @ParamConverter("ticket", class="TicketBundle:Ticket")
     * @return url
     */
    public function showTicketReadonlyAction($ticket) {

        $em = $this->getDoctrine()->getEntityManager();

        $systems  = $em->getRepository('TicketBundle:System')->findAll();
        $adsplus  = $em->getRepository('WorkshopBundle:ADSPlus'  )->findOneBy(array('idTallerADS'  => $ticket->getWorkshop()->getId() ));

        return $this->render('TicketBundle:Layout:show_ticket_readonly_layout.html.twig', array( 'ticket'  => $ticket,
                                                                                                 'systems' => $systems,
                                                                                                 'adsplus' => $adsplus, ));
    }

    /**
     * Funcion Ajax que devuelve un listado de subsistemas filtrados a partir del sistema ($system)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ticketSystemAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();

        $id_system = $petition->request->get('id_system');

        if (sizeOf($id_system) == 1 and $id_system != "" and $id_system != "0") {

            $system = $em->getRepository('TicketBundle:System')->find($id_system);
            $subsystems = $em->getRepository('TicketBundle:Subsystem')->findBy(array('system' => $system->getId()));

            $size = sizeOf($subsystems);
            if($size > 0) {
                $j = 0;
                foreach ($subsystems as $subsystem) {
                    $json[] = $subsystem->to_json();
                    $json[$j]['name'] = $this->get('translator')->trans($json[$j]['name']);
                    $j++;
                }
            }else{
                    $json = array( 'error' => 'No hay coincidencias');
            }
        }else{
            $json = array( 'error' => 'No hay coincidencias');
        }
        return new Response(json_encode($json), $status = 200);
    }

    /**
     * Funcion Ajax que devuelve un listado de tickets filtrados a partir del subsistemas ($subsystem)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function tblSimilarAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();

        $id_model     = $petition->request->get('id_model');
        $id_subsystem = $petition->request->get('id_subsystem');
        $id_country  = $petition->request->get('id_country');

        $status       = $em->getRepository('TicketBundle:Status')->findOneByName('closed');

        if (sizeOf($id_model) == 1 and $id_model != ""
            and sizeOf($id_subsystem) == 1 and $id_subsystem != ""
            and sizeOf($id_country) == 1 and $id_country != "") {
            if($id_model     != null) { $model     = $em->getRepository('CarBundle:Model'       )->find($id_model);     } else { $model     = null; }
            if($id_subsystem != null) { $subsystem = $em->getRepository('TicketBundle:Subsystem')->find($id_subsystem); } else { $subsystem = null; }

            $tickets = $em->getRepository('TicketBundle:Ticket')->findSimilar($status, $model, $subsystem, $id_country);

            if(count($tickets) > 0) {
                foreach ($tickets as $ticket) {
                    $json[] = $ticket->to_json_subsystem();
                }
            }else{
                $json = array( 'error' => 'No hay coincidencias');
            }
        }else{
            $json = array( 'error' => 'No hay coincidencias');
        }

        return new Response(json_encode($json), $status = 200);
    }

    /**
     * Funcion Ajax que devuelve un listado de tickets filtrados a partir del subsistemas ($subsystem)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function tblRepeatedAction() {
    //     $em = $this->getDoctrine()->getEntityManager();
    //     $petition = $this->getRequest();

    //     $id_model     = $petition->request->get('id_model');
    //     $id_subsystem = $petition->request->get('id_subsystem');

    //     $status       = $em->getRepository('TicketBundle:Status')->findOneByName('open');

    //     if (sizeOf($id_model) == 1 and $id_model != "" and sizeOf($id_subsystem) == 1 and $id_subsystem != "") {
    //         if($id_model     != null) { $model     = $em->getRepository('CarBundle:Model'       )->find($id_model[0]);     } else { $model     = null; }
    //         if($id_subsystem != null) { $subsystem = $em->getRepository('TicketBundle:Subsystem')->find($id_subsystem[0]); } else { $subsystem = null; }

    //         $tickets = $em->getRepository('TicketBundle:Ticket')->findSimilar($status, $model, $subsystem);

    //         if(count($tickets) > 0) {
    //             foreach ($tickets as $ticket) {
    //                 $json[] = $ticket->to_json_subsystem();
    //             }
    //         }else{
    //             $json = array( 'error' => 'No hay coincidencias');
    //         }
    //     }else{
    //         $json = array( 'error' => 'No hay coincidencias');
    //     }

    //     return new Response(json_encode($json), $status = 200);
    }

    /**
     * Funcion Ajax que devuelve un listado de tickets filtrados a partir de una opcion de un combo ($option)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function fill_ticketsFromWorkshopAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();

        $id_workshop = $petition->request->get('id_workshop');
        $check_id = $petition->request->get('filter_id');
        $repoTicket  = $em->getRepository('TicketBundle:Ticket');


            if($check_id == 'all'){

                $check_status = $petition->request->get('status');

                if     ($check_status == 'all'   ) {
                                                    $array  = array('workshop' => $id_workshop);
                                                   }
                elseif ($check_status == 'open'  ) {
                                                    $open   = $em->getRepository('TicketBundle:Status')->findOneBy(array('name' => 'open'  ));
                                                    $array  = array('workshop' => $id_workshop,
                                                                    'status'   => $open->getId());
                                                   }
                elseif ($check_status == 'closed') {
                                                    $closed = $em->getRepository('TicketBundle:Status')->findOneBy(array('name' => 'closed'  ));
                                                    $array  = array('workshop' => $id_workshop,
                                                                    'status'   => $closed->getId());
                                                   }
            }else{
                if($id_workshop == 'all'){ $array  = array('id' => $check_id);
                }else{
                    $array  = array('id'       => $check_id,
                                    'workshop' => $id_workshop,);
                }
            }

        $tickets = $repoTicket->findBy($array);

        if(count($tickets) != 0){

            foreach ($tickets as $ticket) {
                $json[] = $ticket->to_json();
            }
        }else{
            $json[] = array('error' => "You don't have any ticket..");
        }
        return new Response(json_encode($json), $status = 200);
    }
}
