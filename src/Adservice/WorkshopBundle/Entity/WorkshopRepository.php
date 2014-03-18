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

        $query = 'SELECT w FROM WorkshopBundle:Workshop w
                  WHERE w.active = 1 ';
        $where = "";

        $w_id        = $request->get('w_id'       );
        $w_idpartner = $request->get('w_idpartner');
        $w_email     = $request->get('w_email'    );
        $w_tel       = $request->get('w_tel'      );

        if ($w_id        != "") {  $where .= "AND w.id = ".$w_id." ";                                                            }
        if ($w_idpartner != "") {  $where .= "AND w.partner = ".$w_idpartner." ";                                                }
        if ($w_email     != "") {  $where .= "AND w.email_1 like '%".$w_email."%' OR w.email_2 like '%".$w_email."%' ";          }
        if ($w_tel       != "") {  $where .= "AND w.phone_number_1 like '%".$w_tel."%' OR w.phone_number_2 like '%".$w_tel."%'
                                              OR  w.movile_phone_1 like '%".$w_tel."%' OR w.movile_phone_2 like '%".$w_tel."%'"; }

        //Crea la consulta
        // echo $query.$where.' ORDER BY w.id ';die;
        $consulta = $em->createQuery($query.$where.' ORDER BY w.id ');

        //Si la consulta da resultado y hay algun campo de los filtros introducido se devuelve el resultado, sino se devuelve un array vacio
        if ((count($consulta->getResult()) > 0) && ($w_id != "" || $w_idpartner != "" || $w_email != "" || $w_tel != "" ))
            {  return $consulta->getResult();  }
        else
            {  return array('0' => new Workshop()); }
    }
}
