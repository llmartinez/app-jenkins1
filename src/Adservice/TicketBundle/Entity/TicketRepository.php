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
    public function findTicketFiltered($security)
    {
        $em = $this->getEntityManager();
        
        if ($security->isGranted('ROLE_ADMIN'))
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
 * ($this->get('security.context')->isGranted('ROLE_ADMIN') === false) {
            throw new AccessDeniedException();

 */