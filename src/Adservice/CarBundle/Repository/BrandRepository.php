<?php
namespace Adservice\CarBundle\Repository;

use Doctrine\ORM\EntityRepository;

class BrandRepository extends EntityRepository
{
    public function findAllBrandsWithoutOther()
    {
        return $this->createQueryBuilder('b')
            ->where('b.id != :otherId')
            ->setParameter('otherId', 0)
            ->getQuery()->getResult();
    }
}