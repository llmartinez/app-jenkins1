<?php
namespace Adservice\WorkshopBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * WorkshopRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class WorkshopRepository extends EntityRepository
{
    public function findWorkshopInfo($request)
    {
        $em = $this->getEntityManager();

        $w_id        = $request->get('w_id'       );
        $w_idpartner = $request->get('w_idpartner');
        $w_name      = $request->get('w_name'     );
        $w_cif       = $request->get('w_cif'      );
        $w_email     = $request->get('w_email'    );
        $w_tel       = $request->get('w_tel'      );
        $w_region    = $request->get('w_region'   );


        if((is_numeric ($w_id) and is_numeric ($w_idpartner)) or ($w_email != '' or $w_tel != ''))
        {
            $active = 1;
            if ($w_id != "" and $w_idpartner   != ""){
                $workshop = $em->getRepository('WorkshopBundle:Workshop')->findOneBy(array('code_workshop' => $w_id,
                                                                                           'code_partner'  => $w_idpartner));
                if (isset($workshop)) $active = $workshop->getActive();
                else                  $active = 0;
            }

            // if($active == 1) {
            $query = 'SELECT w ';
            $from  = 'FROM WorkshopBundle:Workshop w ';
            $where = 'WHERE w.id != 0 ';
            // $where = 'WHERE w.active = 1 ';

            if ($w_id          != "") {  $where .= "AND w.code_workshop = ".$w_id." "; }
            if ($w_idpartner   != "") {  $query .= ", p ";
                                         $from  .= "JOIN w.partner p ";
                                         $where .= "AND p.code_partner = ".$w_idpartner." "; }

            if ($w_id == "" and $w_idpartner == ""){
                if ($w_name        != "") {  $where .= "AND w.name like '%".$w_name."%' "; }
                if ($w_cif         != "") {  $where .= "AND w.cif like '%".$w_cif."%' "; }
                if ($w_email       != "") {  $where .= "AND w.email_1 like '%".$w_email."%' OR w.email_2 like '%".$w_email."%' "; }
                if ($w_tel         != "") {  $where .= "AND (w.phone_number_1 like '%".$w_tel."%' OR w.phone_number_2 like '%".$w_tel."%'
                                                        OR  w.movile_number_1 like '%".$w_tel."%' OR w.movile_number_2 like '%".$w_tel."%')"; }
                if ($w_region      != "") {  $where .= "AND w.region like '%".$w_region."%' "; }
            }
            //Crea la consulta
            $sql = $query.$from.$where.' ORDER BY w.id ';
            // echo $sql;
            $consulta = $em->createQuery($sql);
            $array = $consulta->getResult();

            //Si la consulta da resultado y hay algun campo de los filtros introducido se devuelve el resultado, sino se devuelve un array vacio
            if ((sizeof($array) > 0) and ($w_id != "" or $w_idpartner != "" or $w_email != "" or $w_tel != "" or $w_name != "" or $w_cif != "" or $w_region != "" ))
                {  return $array;  }
            else
                {  return array('0' => new Workshop()); }
        }
        // else
        //     {   return array('error' => 'error'); }
    }

    /**
     * [isADSPlus description]
     * @param  [type]  $id [description]
     * @return boolean     [description]
     */
    public function hasADSPlus($workshops)
    {
        $em = $this->getEntityManager();
        $in   = ' ( ';

        foreach ($workshops as $workshop) {
            $id  = $workshop->getId();
            $in .= $id.', ';
        }

        $in = substr($in, 0, -1).' )';

        $query = 'SELECT a FROM WorkshopBundle:ADSPlus a WHERE a.idTallerADS IN '.$in.' ';
        $consulta = $em->createQuery($query.' ORDER BY a.idTallerADS ');

        return $consulta->getResult();
    }

    public function findPhone($number) {
        $em = $this->getEntityManager();
        $query = 'SELECT COUNT(w) FROM WorkshopBundle:Workshop w '
                .'WHERE w.phone_number_1 = '.$number
                   .'OR w.phone_number_2 = '.$number
                   .'OR w.movile_number_1 = '.$number
                   .'OR w.movile_number_1 = '.$number;
        $consulta = $em-> createQuery($query);
        return $consulta->getResult()[0];

    }

    public function findPhoneGetCode($number) {

        $em = $this->getEntityManager();
        $query = 'SELECT w.code_partner, w.code_workshop, w.name FROM WorkshopBundle:Workshop w '
                .'WHERE w.phone_number_1 = '.$number
                   .'OR w.phone_number_2 = '.$number
                   .'OR w.movile_number_1 = '.$number
                   .'OR w.movile_number_1 = '.$number;
        $consulta = $em-> createQuery($query);

        $result = $consulta->getResult()[0];
        $res = $result['code_partner'].' - '.$result['code_workshop'].' '.$result['name'];

        return $res;
    }

    public function findPhoneNoId($number,$id) {
        $em = $this->getEntityManager();
        $query = 'SELECT COUNT(w) FROM WorkshopBundle:Workshop w '
                .'WHERE w.id != '.$id.' AND (w.phone_number_1 = '.$number
                   .' OR w.phone_number_2 = '.$number
                   .' OR w.movile_number_1 = '.$number
                   .' OR w.movile_number_1 = '.$number.')';
        $consulta = $em-> createQuery($query);
        return $consulta->getResult()[0];
    }

    public function findPhoneNoIdGetCode($number,$id) {
        $em = $this->getEntityManager();
        $query = 'SELECT w.code_partner, w.code_workshop, w.name FROM WorkshopBundle:Workshop w '
                .'WHERE w.id != '.$id.' AND (w.phone_number_1 = '.$number
                   .' OR w.phone_number_2 = '.$number
                   .' OR w.movile_number_1 = '.$number
                   .' OR w.movile_number_1 = '.$number.')';
        $consulta = $em-> createQuery($query);

        $result = $consulta->getResult()[0];
        $res = $result['code_partner'].' - '.$result['code_workshop'].' '.$result['name'];

        return $res;

    }

    public function getNumTickets($id) {
        $em = $this->getEntityManager();
        $query = 'SELECT COUNT(t) FROM TicketBundle:Ticket t '
                .'WHERE t.workshop = '.$id.'';
        $consulta = $em-> createQuery($query);
        return $consulta->getResult()[0][1];
    }

    public function getNumTicketsByPartnerCountry($partner='', $country=null) {
        $em = $this->getEntityManager();

        if($partner != '') $query = 'SELECT COUNT(t) FROM TicketBundle:Ticket t JOIN t.workshop  w WHERE w.partner = '.$partner.' ';
        else $query = 'SELECT COUNT(t) FROM TicketBundle:Ticket t JOIN t.workshop  w WHERE w.country = '.$country.' ';

        $consulta = $em-> createQuery($query);
        return $consulta->getResult()[0][1];
    }
    
    public function getNumTicketsByPartnerId($id) {
        $em = $this->getEntityManager();

        $query = 'SELECT COUNT(t) FROM TicketBundle:Ticket t JOIN t.workshop w JOIN w.partner p WHERE p.id = '.$id;
        
        $consulta = $em-> createQuery($query);
        return $consulta->getResult()[0][1];
    }
}
