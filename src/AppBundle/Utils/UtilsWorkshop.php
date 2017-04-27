<?php
namespace AppBundle\Utils;

use AppBundle\Entity\Workshop;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UtilsWorkshop extends Controller
{
    /** Filtramos $entity segun si esta dentro de los servicios del usuario ($services)
     */
    public static function getFilteredEntity($em, $entity, $services, $idPartner=null)
    {
        $query = $em->getRepository("AppBundle:".$entity)->createQueryBuilder('e')->orderBy('e.name', 'ASC');

        if($idPartner != null && $idPartner != '0')
        {
            $query->join('e.partner', 'p');
            $query->andWhere("p.id = ".$idPartner);
        }
        elseif($services != NULL && $services != '0')
        {
            foreach ($services as $s) {
                $query->orWhere("e.service LIKE '%".$s."%'");
            } 
        }
        //if($idPartner != null && $services != '0') $query->orWhere("e.partner = ".$idPartner);

        return $query;
    }

    /** Filtramos Partner segun si esta dentro de los servicios del usuario ($services)
     */
    public static function getFilteredPartner($em, $services, $idPartner=null)
    {
        $query = $em->getRepository("AppBundle:Partner")->createQueryBuilder('p')->orderBy('p.codePartner, p.name', 'ASC');

        if($idPartner != null && $idPartner != '0') $query->andWhere("p.id = ".$idPartner);
        elseif($services != NULL && $services != '0')
        {
            $query->join('p.user', 'u');

            foreach ($services as $s) {
                $query->orWhere("u.service LIKE '%".$s."%'");
            } 
        }

        return $query;
    }

}