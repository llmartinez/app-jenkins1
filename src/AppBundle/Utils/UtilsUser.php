<?php
namespace AppBundle\Utils;

class UtilsUser
{
    /** Removes spaces, tabs and line breaks from a string
     * 
     */
    static function getUsers($_this, $category_service=null, $role=null, $id=null)
    {
        $em = $_this->getDoctrine();

        $query = $em->getRepository("AppBundle:User")
            ->createQueryBuilder("u")
            ->select("u")
            ->where("u.id != 0")
            ->orderBy("u.username", "ASC");

        if($category_service != null) $query->andWhere("u.category_service = ".$category_service);

        if($role != null) $query->innerJoin('u.user_role','r')->andWhere("r.id = ".$role);

        if($id != null) $query->andWhere("u.id = ".$id);

        return $query->getQuery()->getResult();
    }
}