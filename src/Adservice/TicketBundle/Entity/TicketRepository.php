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
   
    public function findAllOpen($user, $status)
    {
        $workshops = $user->getPartner()->getWorkshops();
        foreach ($workshops as $workshop) {
            $tickets = $this->findBy(array('status' => $status->getId(),
                                           'workshop' => $workshop->getId()));
            if ($tickets != null) return $tickets;
        }
    }
    
    public function findAllAssigned ($user, $status, $bool)
    {
        ($bool == 0) ? $assigned = $user->getId() : $assigned = null;
        
        $workshops = $user->getPartner()->getWorkshops();
        foreach ($workshops as $workshop) {

            $tickets = $this->findBy(array('status' => $status->getId(),
                                           'assigned_to' => $assigned,
                                           'workshop' => $workshop->getId()));
            if ($tickets != null) return $tickets;
        }
    }
    
    public function findAllByOwner ($user, $status)
    {
        $tickets = $this->findBy(array('owner' => $user->getId(), 
                                       'status' => $status->getId()));
        return $tickets;
    }
     
    public function findAllByWorkshop ($user, $status)
    {
        $tickets = $this->findBy(array('workshop' => $user->getWorkshop()->getId(), 
                                       'status' => $status->getId()));
        return $tickets;
    }
    
    public function findTicketFiltered($security)
    {
        $em = $this->getEntityManager();
        
        if ($security->isGranted('ROLE_ASSESSOR'))
        {
            $consulta = $em->createQuery('
                SELECT t FROM TicketBundle:Ticket t
                WHERE t.status = :status
            ');
           
        }else{
            
            $consulta = $em->createQuery('
                SELECT t FROM TicketBundle:Ticket t
                WHERE t.status = :status
                AND t.workshop = :workshop
            ');
            
            $consulta->setParameter('workshop', $security->getToken()->getUser()->getWorkshop());
        }
           
        $consulta->setParameter('status', 0);
        
	return $consulta->getResult();
    }
}
/* $partner->setModifyBy($this->get('security.context')->getToken()->getUser());
 * 
 * ($this->get('security.context')->isGranted('ROLE_ASSESSOR') === false) {
            throw new AccessDeniedException();

 */