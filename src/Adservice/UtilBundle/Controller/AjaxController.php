<?php

namespace Adservice\UtilBundle\Controller;

use Adservice\CarBundle\Entity\Car;
use Adservice\TicketBundle\Controller\DGTWebservice;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Adservice\PartnerBundle\Entity\Shop;
use Adservice\CarBundle\Entity\Brand;
use Adservice\CarBundle\Entity\Model;
use Adservice\CarBundle\Entity\Version;
use Adservice\TicketBundle\Entity\System;
use Adservice\TicketBundle\Entity\Subsystem;

class AjaxController extends Controller
{

    /**
     * Funcion Ajax para obtener los socios de una Cateogria de Servicio
     * @return json
     */
    public function partnersFromCatServAction(Request $request) {

        $em = $this->getDoctrine()->getManager();
        $id_catserv = $request->request->get('id_catserv');

        $query = "SELECT p FROM PartnerBundle:partner p WHERE p.id != 0 ";
        if($id_catserv != '') $query .= "AND p.category_service = ".$id_catserv." ORDER by p.name";

        $consulta = $em->createQuery($query);
        $partners   = $consulta->getResult();

        $size = sizeOf($partners);
        if($size > 0) {
            foreach ($partners as $partner) {
                $json[] = $partner->to_json();
            }
        }else{
                $json = array( 'error' => 'No hay coincidencias');
        }
        return new Response(json_encode($json), $status = 200);
    }
    /**
     * Funcion Ajax para obtener las tipologias de una Cateogria de Servicio
     * @return json
     */
    public function typologiesFromCatServAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $id_catserv = $request->request->get('id_catserv');

        $query = "SELECT t FROM WorkshopBundle:Typology t WHERE t.id != 0 ";
        if($id_catserv != '') $query .= "AND t.category_service = ".$id_catserv." AND t.active = 1";

        $consulta = $em->createQuery($query);
        $result   = $consulta->getResult();

        $size = sizeOf($result);
        if($size > 0) {
            foreach ($result as $row) {
                $json[] = $row->to_json();
            }
        }else{
                $json = array( 'error' => 'No hay coincidencias');
        }
        return new Response(json_encode($json), $status = 200);
    }
    /**
     * Funcion Ajax para obtener las maquinas de diagnosis de una Cateogria de Servicio
     * @return json
     */
    public function diagMachinesFromCatServAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $id_catserv = $request->request->get('id_catserv');

        $query = "SELECT d FROM WorkshopBundle:DiagnosisMachine d WHERE d.id != 0 and d.active = 1 ";
        if($id_catserv != '') $query .= "AND d.category_service = ".$id_catserv." OR d.id = 1 ";

        $consulta = $em->createQuery($query);
        $result   = $consulta->getResult();

        $size = sizeOf($result);
        if($size > 0) {
            foreach ($result as $row) {
                $json[] = $row->to_json();
            }
        }else{
                $json = array( 'error' => 'No hay coincidencias');
        }
        return new Response(json_encode($json), $status = 200);
    }

    //  ____   _    ____ _____ _   _ _____ ____
    // |  _ \ / \  |  _ \_   _| \ | | ____|  _ \
    // | |_) / _ \ | |_) || | |  \| |  _| | |_) |
    // |  __/ ___ \|  _ < | | | |\  | |___|  _ <
    // |_| /_/   \_\_| \_\|_| |_| \_|_____|_| \_\

    /**
     * Funcion Ajax para obtener las tiendas de un socio
     * @return json
     */
    public function codePartnerFromPartnerAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id_partner = $request->request->get('id_partner');

        $partner = $em->getRepository("PartnerBundle:Partner")->find($id_partner);
        $size = sizeOf($partner);
        if($size > 0) {
            $json[] = $partner->to_json();
        }else{
                $json = array( 'error' => 'No hay coincidencias');
        }
        return new Response(json_encode($json), $status = 200);
    }

    public function codeWorkshopFromPartnerAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id_partner = $request->request->get('id_partner');
        $partner = $em->getRepository("PartnerBundle:Partner")->find($id_partner);
        $workshop = UtilController::getCodeWorkshopUnused($em,$partner->getCodePartner());

        $json = array('code' => $workshop);

        return new Response(json_encode($json), $status = 200);
    }

    public function getIdFromCodePartnerAction(Request $request, $code)
    {
        $em = $this->getDoctrine()->getManager();
        $code = $request->request->get('code');

        $partner = $em->getRepository("PartnerBundle:Partner")->findOneBy(array('code_partner' => $code));

        if (isset($partner) and $partner->getId() != null) {

            $workshop = UtilController::getCodeWorkshopUnused($em,$partner);

            $json = array('id' => $partner->getId(), 'code' => $workshop);
        }
        else $json = array('id' => '0', 'code' => '0');

        return new Response(json_encode($json), $status = 200);
    }

    public function getCountryPartnerAction(Request $request, $id_partner)
    {
        $em = $this->getDoctrine()->getManager();

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
    public function shopsFromPartnerAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $id_partner = $request->request->get('id_partner');

        $query = "SELECT s FROM PartnerBundle:Shop s WHERE s.id = 1 and s.active = 1 ";
        if($id_partner != '') $query .= "OR s.partner = ".$id_partner." and s.active = 1 ";

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

    // __        _____  ____  _  ______  _   _  ___  ____
    // \ \      / / _ \|  _ \| |/ / ___|| | | |/ _ \|  _ \
    //  \ \ /\ / / | | | |_) | ' /\___ \| |_| | | | | |_) |
    //   \ V  V /| |_| |  _ <| . \ ___) |  _  | |_| |  __/
    //    \_/\_/  \___/|_| \_\_|\_\____/|_| |_|\___/|_|    T

    /**
     * Funcion Ajax para obtener los talleres de un socio
     * @return json
     */
    public function workshopsFromPartnerAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $id_partner = $request->request->get('id_partner');

        $query = "SELECT w FROM WorkshopBundle:Workshop w WHERE w.id = 0 ";
        if($id_partner != '') $query .= "OR w.partner = ".$id_partner." ";

        $consulta = $em->createQuery($query);
        $workshops   = $consulta->getResult();

        $size = sizeOf($workshops);
        if($size > 0) {
            foreach ($workshops as $workshop) {
                $json[] = $workshop->to_json();
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
    public function carModelAction(Request $request, $id_brand, $filter='') {
        $em = $this->getDoctrine()->getManager();
        $filter_value = $request->request->get('filter_value');
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

            //     $id_mts = $request->request->get('id_mts');

            //     if($id_mts == '')
            //     {
            //         $query = "SELECT partial mt.{id, name} FROM CarBundle:Motor mt";
            //         $consulta = $em->createQuery($query);
            //         $motors   = $consulta->getResult();

            //         $motor = $request->request->get('motor');
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
    public function carVersionAction(Request $request, $id_model, $filter='') {
        $em = $this->getDoctrine()->getManager();
        $filter_value = $request->request->get('filter_value');
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

            //     $id_mts = $request->request->get('id_mts');

            //     if($id_mts == '')
            //     {
            //         $query = "SELECT partial mt.{id, name} FROM CarBundle:Motor mt";
            //         $consulta = $em->createQuery($query);
            //         $motors   = $consulta->getResult();

            //         $motor = $request->request->get('motor');
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
    public function carDataAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $id_version    = $request->request->get('id_version');
        $version_motor = $request->request->get('version_motor');
        $motor = $request->request->get('motor');
        $year = $request->request->get('year');

        if(isset($motor)){
            $motor = $request->request->get('motor');
            $query = $em->createQuery("SELECT v FROM CarBundle:Version v, CarBundle:Motor mt
                                        WHERE mt.id = v.motor AND v.id = ".$id_version." AND mt.name LIKE '%".$motor."%'");
            $version = $query->getResult();
            $version = $version[0];
        }
        elseif($year != null){
            $year = $request->request->get('year');
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
    public function carByYearAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $year = $request->request->get('year');

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
    public function carByMotorAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $motor = $request->request->get('motor');
        
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
    
     /**
     * Funcion Ajax que devuelve los datos del coche introducido a partir de la version ($version)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function carMotorsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('CarBundle:Motor')
                ->createQueryBuilder('m')
                ->select('m.name')
                ->orderBy('m.name');
        $motors = $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        //A partir de la version de PHP 5.5 se puede usar la siguiente funcion
        //$motors = array_column($motors, "name");
        //Pero para la 5.4 no es valida
        $motors = $this->get('util_controller')->array_column($motors, "name");
        return new Response(json_encode($motors), $status = 200);
    }
    
    public function getCarFromPlateNumberAction(Request $request, $idPlateNumber){
        $em = $this->getDoctrine();

        $car = $em->getRepository('CarBundle:Car')->findOneBy(array('plateNumber' => $idPlateNumber));

        if ($car instanceof Car AND $car->getStatus() == "validado"){

            $json = $car->to_json();
            return new JsonResponse($json);
        }

        $results = $this->get('dgt_webservice')->getData($idPlateNumber);
        $json = $this->get('dgt_webservice')->transformData($results);
        var_dump($json);
        die;

        return new JsonResponse($json);
    }
    
    public function getCarFromVinAction(Request $request, $vin){
        $em = $this->getDoctrine();

        $car = $em->getRepository('CarBundle:Car')->findOneBy(array('vin' => $vin));
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
    public function showTicketReadonlyAction(Request $request, $ticket) {

        $em = $this->getDoctrine()->getManager();

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
    public function ticketSystemAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $id_system = $request->request->get('id_system');

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
    public function tblSimilarAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $id_model     = $request->request->get('id_model');
        $id_subsystem = $request->request->get('id_subsystem');
        $id_country  = $request->request->get('id_country');

        $status       = $em->getRepository('TicketBundle:Status')->findOneByName('closed');
        
        if (sizeOf($id_model) == 1 and $id_model != ""
            and sizeOf($id_subsystem) == 1 and $id_subsystem != ""
            and sizeOf($id_country) == 1 and $id_country != "") {
            if($id_model     != null) { $model     = $em->getRepository('CarBundle:Model'       )->find($id_model);     } else { $model     = null; }
            if($id_subsystem != null) { $subsystem = $em->getRepository('TicketBundle:Subsystem')->find($id_subsystem); } else { $subsystem = null; }

            $catserv = $this->getUser()->getCategoryService();
            if($catserv != null) $catserv_id = $catserv->getId().' '; else $catserv_id = 0;

            $tickets = $em->getRepository('TicketBundle:Ticket')->findSimilar($status, $model, $subsystem, $id_country, $catserv_id);

            if(count($tickets) > 0) {
                foreach ($tickets as $ticket) {                    
                    if($ticket->getStatus()->getId() == 2 && $ticket->getExpirationDate() == null)
                    {
                        $json[] = $ticket->to_json_subsystem();
                    }
                }
            }else{
                $json = array( 'error' => 'No hay coincidencias');
            }
        }else{
            $json = array( 'error' => 'No hay coincidencias');
        }
        if(!isset($json)){
            $json = array( 'error' => 'No hay coincidencias');
        }
        return new Response(json_encode($json), $status = 200);
    }

    /**
     * Funcion Ajax que devuelve un listado de tickets filtrados a partir del subsistemas ($subsystem)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function tblRepeatedAction(Request $request) {
    //     $em = $this->getDoctrine()->getManager();

    //     $id_model     = $request->request->get('id_model');
    //     $id_subsystem = $request->request->get('id_subsystem');

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
    public function fill_ticketsFromWorkshopAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $id_workshop = $request->request->get('id_workshop');
        $check_id = $request->request->get('filter_id');
        $repoTicket  = $em->getRepository('TicketBundle:Ticket');

        if($check_id == 'all'){

            $check_status = $request->request->get('status');

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
