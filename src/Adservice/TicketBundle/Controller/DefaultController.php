<?php
namespace Adservice\TicketBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Adservice\TicketBundle\Entity\Subsystem;
use Adservice\CarBundle\Entity\Model;

class DefaultController extends Controller
{
    /**
     * Asigna el usuario que ha creado la clase y la fecha de la creación.
     * @param Class $entity
     * @param Class $user
     * @return Class
     */
    public static function newEntity($entity, $user){
        $entity->setOwner($user);
        $entity->setCreatedAt(new \DateTime(\date("Y-m-d H:i:s")));
        return $entity;
    }

    /**
     * Asigna el usuario que ha modificado la clase y la fecha de la modificación.
     * @param EntityManager $em
     * @param Class $entity
     * @param Bool $auto_flush true: aplica cambios en BBDD
     * @return Bool
     */
    public static function saveEntity($em, $entity, $user, $auto_flush=true)
    {
        $entity->setModifiedBy($user);
        $entity->setModifiedAt(new \DateTime(\date("Y-m-d H:i:s")));
        $em->persist($entity);
        if($auto_flush) $em->flush();
        return true;
    }

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

    /**
     * Funcion Ajax que devuelve un listado de tickets filtrados a partir del subsistemas ($subsystem)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function tblSimilarAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();

        $id_model     = $petition->request->get('id_model');
        $id_subsystem = $petition->request->get('id_subsystem');
        $status       = $em->getRepository('TicketBundle:Status')->findByName('closed');

        if($id_model     != null) { $model     = $em->getRepository('CarBundle:Model'       )->find($id_model);     } else { $model     = null; }
        if($id_subsystem != null) { $subsystem = $em->getRepository('TicketBundle:Subsystem')->find($id_subsystem); } else { $subsystem = null; }

        $tickets = $em->getRepository('TicketBundle:Ticket')->findSimilar($status, $model, $subsystem);

        if(count($tickets) > 0) {
            foreach ($tickets as $ticket) {
                $json[] = $ticket->to_json_subsystem();
            }
        }else{
            $json = array( 'error' => 'No hay coincidencias');
        }
        return new Response(json_encode($json), $status = 200);
    }

}
