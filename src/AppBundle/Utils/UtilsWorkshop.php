<?php
namespace AppBundle\Utils;

use AppBundle\Entity\Workshop;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UtilsWorkshop extends Controller
{
    public static function getFilteredEntity($em, $entity, $services)
    {
        $query = $em->getRepository("AppBundle:".$entity)->createQueryBuilder('e');

        if($entity == 'Partner') $query->orderBy('e.codePartner, e.name', 'ASC');
        else                     $query->orderBy('e.name', 'ASC');

        if($services != NULL && $services != '0')
        {
            foreach ($services as $s) {
                $query->orWhere("e.service LIKE '%".$s."%'");
            } 
        }
        return $query;
    }

}