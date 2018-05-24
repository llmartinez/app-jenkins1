<?php
namespace Adservice\CarBundle\Repository;

use Doctrine\ORM\EntityRepository;

class VersionRepository extends EntityRepository
{
    public function findVersionByIdAndMotorName($versionId, $motorName)
    {
        return $this->createQueryBuilder('v')
            ->join('v.motor', 'm')
            ->where('v.id = :versionId')
            ->andWhere('m.name = :motorName')
            ->setParameter('versionId', $versionId)
            ->setParameter('motorName', $motorName)
            ->getQuery()->getOneOrNullResult();
    }
}