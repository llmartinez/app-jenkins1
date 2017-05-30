<?php
namespace AppBundle\Utils;

class UtilsWorkshop
{
    /** Removes spaces, tabs and line breaks from a string
     *
     */
    static function getWorkshops($_this, $category_service=null, $id=null)
    {
        $em = $_this->getDoctrine();

        $query = $em->getRepository("AppBundle:Workshop")
            ->createQueryBuilder("w")
            ->select("w")
            ->where("w.id != 0")
            ->orderBy("w.name", "ASC");

        if($category_service != null) $query->andWhere("w.category_service = ".$category_service);

        if($id != null) $query->andWhere("w.id = ".$id);

        return $query->getQuery()->getResult();
    }
}