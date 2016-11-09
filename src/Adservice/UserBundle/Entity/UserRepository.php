<?php

namespace Adservice\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    /**
     * Busca todos los usuarios que pertenecen a un partner
     * Añade los usuarios de los talleres del partner
     * @param Int $partner
     */
    public function findByPartner($partner)
    {

        //users que dependen directamente del partner
        $em = $this->getEntityManager();
        $query = $em->createQuery("SELECT u
                                   FROM UserBundle:User u
                                   WHERE u.partner = :partner
                                  ");
        $query->setParameter('partner', $partner);
        $users = $query->getResult();

        //users que dependen del taller
        $workshops = $partner->getWorkshops();
        if (count($workshops)>0){
            foreach ($workshops as $workshop) {
                $users_workshop = $workshop->getUsers();
                foreach ($users_workshop as $user_workshop) {
                    array_push($users, $user_workshop);
                }
            }
        }

        return $users;
    }

     /**
     * Get 10 rows of a role
     *
     * @return string
     */
    public function findByOption($em, $security, $country, $catserv, $option, $term, $field, $pagination)
    {

        $query = 'SELECT u FROM UserBundle:user u JOIN u.user_role r WHERE r.name = :role';

        if(!$security->isGranted('ROLE_SUPER_ADMIN')) {
            $query = $query.' AND u.country = '.$security->getToken()->getUser()->getCountry()->getId();
        }
        else{
            if ($country != 0 ) {
                $query = $query.' AND u.country = '.$country;
            }
        }
        if ($catserv != 0 ) {
            $query = $query.' AND u.category_service = '.$catserv;
        }

        if (!$field == 0 ) {
            if ($term == 'tel') {
               $query = $query." AND u.phone_number_1 != '0' AND (u.phone_number_1 LIKE '%" . $field . "%' OR u.phone_number_2 LIKE '%" . $field . "%' OR u.mobile_number_1 LIKE '%" . $field . "%' OR u.mobile_number_2 LIKE '%" . $field . "%')";
            } elseif ($term == 'mail') {
               $query = $query." AND u.email_1 != '0' AND (u.email_1 LIKE '%" . $field . "%' OR u.email_2 LIKE '%" . $field . "%')";
            } elseif ($term == 'user') {
               $query = $query." AND u.username  LIKE '%" . $field . "%'";
            }
        }
        $consulta = $em ->createQuery($query)
                        ->setParameter('role', $option)
                        ->setMaxResults($pagination->getMaxRows())
                        ->setFirstResult($pagination->getFirstRow());

        return $consulta->getResult();
    }
    /**
     * Get 10 rows of a role
     *
     * @return string
     */
    public function findLengthOption($em, $security, $country, $catserv, $option)
    {
        $query = 'SELECT count(u) FROM UserBundle:user u JOIN u.user_role r WHERE r.name = :role';

        if(!$security->isGranted('ROLE_SUPER_ADMIN')) {
            $query = $query.' AND u.country = '.$security->getToken()->getUser()->getCountry()->getId();
        }
        else{
            if ($country != 0 ) {
                $query = $query.' AND u.country = '.$country;
            }
        }
        if ($catserv != 0 ) {
            $query = $query.' AND u.category_service = '.$catserv;
        }
        $consulta = $em ->createQuery($query)
                        ->setParameter('role', $option);

    	$result = $consulta->getResult();
    	$result = $result[0];
    	$result = $result[1];
        return $result;
    }
}
