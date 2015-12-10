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
    public function codePartnerFromPartnerAction() {
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

    public function codeWorkshopFromPartnerAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();

        $id_partner = $petition->request->get('id_partner');
        $partner = $em->getRepository("PartnerBundle:Partner")->find($id_partner);

        $workshop = UtilController::getCodeWorkshopUnused($em,$partner);
        $json = array('code' => $workshop);

        return new Response(json_encode($json), $status = 200);
    }

     public function getIdFromCodePartnerAction($code) {
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

        if($filter != '') {
            if($filter == 'motor'){
                $query = "SELECT m FROM CarBundle:Brand b, CarBundle:Model m, CarBundle:Version v, CarBundle:Motor mt
                          WHERE b.id = m.brand AND m.id = v.model AND mt.id = v.motor AND b.id = ".$id_brand." AND mt.name like '%".$filter_value."%'
                          ORDER BY m.name";}
            elseif($filter == 'year'){
                $query = "SELECT m FROM CarBundle:Brand b, CarBundle:Model m
                          WHERE b.id = m.brand AND b.id = ".$id_brand." AND m.brand IS NOT NULL AND m.inicio <= '".$filter_value."00' AND m.fin >= '".$filter_value."99'
                          GROUP BY m.id ORDER BY m.name";}

            $consulta = $em->createQuery($query);
            $models   = $consulta->getResult();
        }
        else{
            $models = $em->getRepository('CarBundle:Model')->findBy(array('brand' => $id_brand), array('name' => 'ASC'));
        }

        $size = sizeOf($models);
        if($size > 0) {
            foreach ($models as $model) {
                $json[] = $model->to_json();
            }
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

        if($filter != '') {
            if($filter == 'motor')
                $query = "SELECT v FROM CarBundle:Brand b, CarBundle:Model m, CarBundle:Version v, CarBundle:Motor mt
                          WHERE b.id = m.brand AND m.id = v.model AND mt.id = v.motor AND m.id = ".$id_model." AND mt.name like '%".$filter_value."%'
                          ORDER BY m.name";
            elseif($filter == 'year')
                $query = "SELECT v FROM CarBundle:Version v
                          WHERE v.model = ".$id_model." AND v.model IS NOT NULL AND v.inicio <= '".$filter_value."00' AND v.fin >= '".$filter_value."99'
                          GROUP BY v.id ORDER BY v.name";

            $consulta = $em->createQuery($query);
            $versions   = $consulta->getResult();
        }
        else{
            $model = $em->getRepository('CarBundle:Model')->find($id_model);
            $versions = $em->getRepository('CarBundle:Version')->findBy(array('model' => $model->getId()));
        }

        $size = sizeOf($versions);
        if($size > 0) {
            foreach ($versions as $version) {
                $json[] = $version->to_json();
            }
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

        $id_version = $petition->request->get('id_version');

        $version = $em->getRepository('CarBundle:Version')->find($id_version);

        if (isset($version)) {
            $id_motor = $version->getMotor();
            $motor = $em->getRepository('CarBundle:Motor')->find($id_motor);
            $version->setMotor($motor->getName());
        }

        if(isset($version)) {
            $json[] = $version->to_json();
        }
        else                {$json   = array( 'error' => 'No hay coincidencias');}

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
            $query = "SELECT b FROM CarBundle:Brand b, CarBundle:Model m WHERE b.id = m.brand AND m.brand IS NOT NULL AND m.inicio <= '".$year."00' AND m.fin >= '".$year."99' GROUP BY b.id ORDER BY b.name ASC";

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

        $query = "SELECT b FROM CarBundle:Brand b, CarBundle:Model m, CarBundle:Version v, CarBundle:Motor mt WHERE b.id = m.brand AND m.id = v.model AND mt.id = v.motor AND mt.name like '%".$motor."%' ORDER BY b.name ASC";
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

        return new Response(json_encode($json), $status = 200);
    }

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
        //var_dump($json);
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
        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();

        $id_model     = $petition->request->get('id_model');
        $id_subsystem = $petition->request->get('id_subsystem');

        $status       = $em->getRepository('TicketBundle:Status')->findOneByName('open');

        if (sizeOf($id_model) == 1 and $id_model != "" and sizeOf($id_subsystem) == 1 and $id_subsystem != "") {
            if($id_model     != null) { $model     = $em->getRepository('CarBundle:Model'       )->find($id_model[0]);     } else { $model     = null; }
            if($id_subsystem != null) { $subsystem = $em->getRepository('TicketBundle:Subsystem')->find($id_subsystem[0]); } else { $subsystem = null; }

            $tickets = $em->getRepository('TicketBundle:Ticket')->findSimilar($status, $model, $subsystem);

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
