<?php
namespace Adservice\TicketBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * TicketRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TicketRepository extends EntityRepository
{
    public function findAllFree($em, $status, $ordered=null)
    {
        $query = 'SELECT t FROM TicketBundle:Ticket t
                  WHERE t.status = '.$status->getId().'
                  AND t.assigned_to IS NULL
                  ORDER BY t.modified_at '.$ordered;

        $consulta = $em->createQuery($query);

        return $consulta->getResult();
    }

    public function findAllAssigned($em, $status, $ordered=null)
    {
        $query = 'SELECT t FROM TicketBundle:Ticket t
                  WHERE t.status = '.$status->getId().'
                  AND t.assigned_to IS NOT NULL
                  ORDER BY t.modified_at '.$ordered;

        $consulta = $em->createQuery($query);

        return $consulta->getResult();
    }

    public function findAllPending($em, $status)
    {
        $tickets = $this->findAllAssigned($em, $status);
        $pending = $this->getPending($tickets);
        return $pending;
    }

    public function findAllAnswered($em, $status)
    {
        $tickets = $this->findAllAssigned($em, $status, 'DESC');
        $answered = $this->getPending($tickets, false);
        return $answered;
    }

    private function getLastPostRole($ticket) {
        $posts = $ticket->getPosts();
        //$last_post = end($posts);
        $num_posts = count($ticket->getPosts());

        if    ($num_posts > 0) { 
				$last_post_role = $posts[$num_posts-1]; 
				$last_post_role = $last_post_role->getRoles(); 
				$last_post_role = $last_post_role[0]; 
				$last_post_role = $last_post_role->getName(); 
	}
        // elseif($ticket->getAssignedTo() != null) { $last_post_role = $ticket->getAssignedTo()->getRoles()[0]->getName(); }
        else                   { 
				$last_post_role = $ticket->getCreatedBy(); 
				$last_post_role = $last_post_role->getRoles(); 
				$last_post_role = $last_post_role[0]; 
				$last_post_role = $last_post_role->getName(); 
	}

        return $last_post_role;
    }

    private function getPending($tickets, $pending=true)
    {
        $return_tickets = array();

        foreach ($tickets as $ticket) {

            if    ($pending == true and $this->getLastPostRole($ticket) == "ROLE_USER"){
                    $return_tickets[] = $ticket;
            }
            elseif($pending != true and $this->getLastPostRole($ticket) != "ROLE_USER"){
                    $return_tickets[] = $ticket;
            }
        }
        return $return_tickets;
    }

/**
 * Encuentra los tickets segun el estado ($status) y los filtros que quieran devolver ($return)
 * @param  Entity $user     El usuario logeado actualmente
 * @param  Entity $status   El estado en que esta el ticket que se quiere devolver ('open', 'closed')
 * @param  Entity $return   El filtro de tickets que se quieran devolver ( 'all', 'accesible', 'answered', 'assigned')
 * @return array            array de tickets filtrados segun $status y $return
 */
    public function findOption ($em, $user, $status, $return, $ordered=null)
    {
        $tickets = $this->findAllStatus($em, $status, $ordered);

        $assessor_pending  = array();  //array con los tickets pendientes  del assessor
        $assessor_answered = array();  //array con los tickets respondidos del assessor
        $assessor_closed   = array();  //array con los tickets cerrados    del assessor
        $other_pending     = array();  //array con los tickets pendientes  de otro assessor
        $other_answered    = array();  //array con los tickets respondidos de otro assessor
        $other_closed      = array();  //array con los tickets cerrados    de otro assessor

        foreach ($tickets as $ticket) {

            $last_post_role = $this->getLastPostRole($ticket);

            // Si el ultimo post es de un user
            if ($last_post_role == "ROLE_USER")
            {
                // tickets pendientes de respuesta
                if ( $ticket->getAssignedTo() != null )
                {
                    // pendientes del assessor
                    if($ticket->getAssignedTo() == $user) {
                        if($status->getname() != 'closed') $assessor_pending[] = $ticket;
                        else $assessor_closed[] = $ticket;
                    }
                    // pendientes de otro assessor
                    else {
                        if($status->getname() != 'closed') $other_pending[] = $ticket;
                        else $other_closed[] = $ticket;
                    }
                }

            // Si el ultimo post es de un assessor
            }else {
                // respondidos del assessor
                if($ticket->getAssignedTo() == $user) {
                    if($status->getname() != 'closed') $assessor_answered[] = $ticket;
                    else $assessor_closed[] = $ticket;
                }
                // respondidos de otro assessor
                else {
                    if($status->getname() != 'closed') $other_answered[] = $ticket;
                    else $other_closed[] = $ticket;
                }
            }
        }
        if     ($return == 'assessor_pending'  ) return $assessor_pending;
        elseif ($return == 'assessor_answered' ) return $assessor_answered;
        elseif ($return == 'assessor_closed'   ) return $assessor_closed;
        elseif ($return == 'other_pending'     ) return $other_pending;
        elseif ($return == 'other_answered'    ) return $other_answered;
        elseif ($return == 'other_closed'      ) return $other_closed;
    }
/************************************
*
*   INTENTO DE MEJORA DE FINDOPTION()
*
*************************************/
    public function findAllFromUser($em, $status, $assigned=null, $pending=true, $user=null, $withUser=false, $ordered=null)
    {
        $query = 'SELECT t FROM TicketBundle:Ticket t ';
        $where = 'WHERE t.status = '.$status->getId().' ';

        if     ($assigned == null ) { $condition = 'AND t.assigned_to IS NULL ';               }
        elseif ($user     == null ) { $condition = 'AND t.assigned_to IS NOT NULL ';           }
        elseif ($withUser == true ) { $condition = 'AND t.assigned_to =  '.$user->getId().' '; }
        else                        { $condition = 'AND t.assigned_to <> '.$user->getId().' '; }

        $order = 'ORDER BY t.modified_at '.$ordered;

        $consulta = $em->createQuery($query.$where.$condition.$order);

        $result_tickets = $consulta->getResult();

        if($pending == true) { $tickets = $this->getPending($result_tickets);        }
        else                 { $tickets = $this->getPending($result_tickets, false); }

        return $tickets;
    }

    public function findSimilar($status, $model=null, $subsystem=null)
    {
        $em = $this->getEntityManager();

        $query    = ' SELECT t FROM TicketBundle:Ticket t ';
        $joins    = ' ';
        $where    = ' WHERE t.status = '.$status->getId().' ';

        if ($model != null)
        {
            $joins  .= ' JOIN t.car c ';
            $where  .= ' AND c.model = '.$model->getId().' ';
        }

        if ($subsystem != null)
        {
            $where .=  ' AND t.subsystem = '.$subsystem->getId().' ';
        }
        //Crea la consulta
        $consulta = $em->createQuery($query.$joins.$where.' ORDER BY t.id ');

        return $consulta->getResult();
    }


    public function findTicketInfo($request)
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
                $partner  = $em->getRepository('PartnerBundle:Partner'  )->findOneBy(array('code_partner'  => $w_idpartner ));

                if (isset($partner)) $workshop = $em->getRepository('WorkshopBundle:Workshop')->findOneBy(array('code_workshop' => $w_id,
                                                                                                                'partner'       => $partner->getId()));
                else $active = 0;

                if (isset($workshop)) $active = $workshop->getActive();
            }

            if($active == 1) {
                $query = 'SELECT w ';
                $from  = 'FROM WorkshopBundle:Workshop w ';
                $where = 'WHERE w.active = 1 ';

                if ($w_id          != "") {  $where .= "AND w.code_workshop = ".$w_id." "; }
                if ($w_idpartner   != "") {  $query .= ", p ";
                                             $from  .= "JOIN w.partner p ";
                                             $where .= "AND p.code_partner = ".$w_idpartner." "; }
                if ($w_name        != "") {  $where .= "AND w.name like '%".$w_name."%' "; }
                if ($w_cif         != "") {  $where .= "AND w.cif like '%".$w_cif."%' "; }
                if ($w_email       != "") {  $where .= "AND w.email_1 like '%".$w_email."%' OR w.email_2 like '%".$w_email."%' "; }
                if ($w_tel         != "") {  $where .= "AND (w.phone_number_1 like '%".$w_tel."%' OR w.phone_number_2 like '%".$w_tel."%'
                                                        OR  w.mobile_number_1 like '%".$w_tel."%' OR w.mobile_number_2 like '%".$w_tel."%')"; }
                if ($w_region      != "") {  $where .= "AND w.region like '%".$w_region."%' "; }

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
            }else
                {   return array('error' => 'error'); }

        }else
            {   return array('error' => 'error'); }
    }
}