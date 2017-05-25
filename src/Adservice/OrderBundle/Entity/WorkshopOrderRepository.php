<?php
namespace Adservice\OrderBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * WorkshopRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class WorkshopOrderRepository extends EntityRepository
{
    /**
     * Devuelve el ultimo ID almacenado en DB
     * @return integer
     */
    public function getMaxIdByCodePartner($code_partner)
    {
        $em = $this->getEntityManager();

        $query = 'SELECT MAX(w.code_workshop) FROM OrderBundle:WorkshopOrder w Where w.code_partner = '.$code_partner;
        $consulta = $em->createQuery($query);

        $id = $consulta->getSingleResult()[1];

        return (int)$id;
    }

}