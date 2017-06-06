<?php
namespace AppBundle\Utils;

class Getters
{
    /**
     * Generate a query for basic GET functions
     * @param $em
     * @param $entity
     * @param $category_service
     * @param $id
     *
     * @return QueryBuilder
     */
    static function getBasicQuery($em, $entity, $category_service, $id)
    {
        $query = $em->getRepository($entity)
            ->createQueryBuilder("e")
            ->select("e")
            ->where("e.id != 0");

        if($category_service != null) $query->andWhere("e.category_service = ".$category_service);

        if($id != null) $query->andWhere("e.id = ".$id);

        return $query;
    }

    static function getPartners($_this, $category_service=null, $id=null)
    {
        $em = $_this->getDoctrine();

        $query = self::getBasicQuery($em, "AppBundle:Partner", $category_service, $id)->orderBy("e.name", "ASC");

        return $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    static function getShops($_this, $category_service=null, $id=null)
    {
        $em = $_this->getDoctrine();

        $query = self::getBasicQuery($em, "AppBundle:Shop", $category_service, $id)->orderBy("e.name", "ASC");

        return $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    static function getTypologies($_this, $category_service=null, $id=null)
    {
        $em = $_this->getDoctrine();

        $query = self::getBasicQuery($em, "AppBundle:Typology", $category_service, $id)->orderBy("e.name", "ASC");

        return $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    static function getUsers($_this, $category_service=null, $role=null, $id=null)
    {
        $em = $_this->getDoctrine();

        $query = self::getBasicQuery($em, "AppBundle:User", $category_service, $id)->orderBy("e.username", "ASC");

        if($role != null) $query->innerJoin('u.user_role','r')->andWhere("r.id = ".$role);

        return $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    static function getWorkshops($_this, $category_service=null, $id=null)
    {
        $em = $_this->getDoctrine();

        $query = self::getBasicQuery($em, "AppBundle:Workshop", $category_service, $id)->orderBy("e.name", "ASC");

        return $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }
}