<?php
namespace AppBundle\Utils;

use AppBundle\Entity\User;
use AppBundle\Entity\Workshop;
use AppBundle\Form\UserNewType;
use AppBundle\Form\UserEditType;
use AppBundle\Form\WorkshopType;

class UtilsUser
{
    public static $rolesAdmin = array('3' => 'ROLE_ADMIN', '4' => 'ROLE_TOP', '5' => 'ROLE_SUPER_PARTNER',
                                      '6' => 'ROLE_PARTNER', '7' => 'ROLE_COMMERCIAL', '8' => 'ROLE_ADVISER', '9' => 'ROLE_WORKSHOP');

    public static $rolesPartner = array('7' => 'ROLE_COMMERCIAL');

    public static $rolesCommercial = array('9' => 'ROLE_WORKSHOP');

    public static $status = array('0' => 'inactive', '1' => 'active', '2' => 'test');

    public static function getRolesForAdmin() {
        return self::$rolesAdmin;
    }
    
    public static function getRolesForPartner() {
        return self::$rolesPartner;
    }
    
    public static function getRolesForCommercial() {
        return self::$rolesCommercial;
    }

    public static function getStatus() {
        return self::$status;
    }

    public static function newUser($_this, $request, $role_id)
    {
        $em = $_this->getDoctrine()->getManager();

        $user = new User();

        $form = $_this->createForm(new UserNewType(), $user);
        $return = array('_locale' => $_this->get('locale'), 'role_id' => $role_id, 'form' => $form->createView());

        $entityName = self::getEntityName($role_id);

        if($entityName != null)
        {
            $entityType = 'AppBundle\Form\\'.$entityName.'Type';
            $entityCheck = 'check'.$entityName;
            $entityName = 'AppBundle\Entity\\'.$entityName;

            $entity = New $entityName();
            $entity->setUser($user);

            $formE = $_this->createForm(new $entityType(), $entity);
            $return['formE'] = $formE->createView();
            $formE->handleRequest($request);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && (!isset($formE) || $formE->isSubmitted() && $formE->isValid()))
        {
            if($user->getRoleId() == null)
            {
                $role = $em->getRepository('AppBundle:Role')->find($role_id);

                $user->setRoleId($role_id);
                $user->addRole($role);
            }

            if($user->getPassword() == null)
            {
                $user->setSalt(md5(uniqid()));
                $password = $_this->get('security.password_encoder')
                    ->encodePassword($user, $user->getPlainPassword(), $user->getSalt());
                $user->setPassword($password);
            }

            if(self::checkUser($_this, $user))
            {
                $user->setToken(self::getRandomToken());

                if ($entityName != null)
                {
                    if (self::$entityCheck($em, $entity))
                    {
                        $em->persist($user);
                        $em->persist($entity);
                    }
                }
                else $em->persist($user);
            }
            $em->flush();

            return false;
        }

        return $return;
    }

    public static function editUser($_this, $request, $user)
    {
        $em = $_this->getDoctrine()->getManager();

        $form = $_this->createForm(new UserEditType(), $user);
        $form->handleRequest($request);
        $return = array('_locale' => $_this->get('locale'), 'user' => $user->getId(), 'role_id' => $user->getRoleId(), 'form' => $form->createView());
        $entityName = self::getEntityName($user->getRoleId());

        if($entityName != null)
        {
            $entityType = 'AppBundle\Form\\'.$entityName.'Type';
            $entity = $em->getRepository('AppBundle:'.$entityName)->findOneByUser($user);
            $formE = $_this->createForm(new $entityType(), $entity);
            $formE->handleRequest($request);

            $return['formE'] = $formE->createView();
        }
        if ($form->isSubmitted() && $form->isValid() && (!isset($formE) || $formE->isSubmitted() && $formE->isValid()))
        {
                $user->setToken(self::getRandomToken());
            $em->persist($user);
            if($entityName != null)
                $em->persist($entity);
            $em->flush();
            return false;
        }

        # Devolvemos
        return $return;
    }


    static public function getRandomToken()
    {
          $key = '';
          $keys = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));

          for ($i = 0; $i < 20; $i++) {
              $key .= $keys[array_rand($keys)];
          }
          return $key;
    }

    public static function getEntityName($role_id)
    {
        if($role_id == 6) $entityName = 'Partner';
        elseif($role_id == 9) $entityName = 'Workshop';
        else $entityName = null;
        return $entityName;
    }

    public static function getMaxIdWorkshop($em,$workshop)
    {
        $query = $em->createQuery(
            'SELECT MAX(w.id) FROM AppBundle:Workshop w
             WHERE w.partner = :idPartner'
        )->setParameter('idPartner', $workshop->getPartner()->getId());

        $max_id = $query->getSingleScalarResult();

        if($max_id == null) return 1;
        return $max_id+1;
    }

    public static function checkUser($_this, $user)
    {
        // Username tiene que estar en formato slug
        if( $user->getUsername() != $_this->get('slugger')->slugify($user->getUsername())) return false;

        return true;
    }

    public static function checkWorkshop($em,$workshop)
    {
        if($workshop->getId() == null){
            $workshop->setId(self::getMaxIdWorkshop($em,$workshop));
        }
        return true;
    }

    public static function checkPartner($em,$workshop)
    {
        return true;
    }
}