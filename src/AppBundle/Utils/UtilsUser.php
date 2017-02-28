<?php
namespace AppBundle\Utils;

use AppBundle\Entity\User;
use AppBundle\Form\UserNewType;
use AppBundle\Form\UserEditType;

class UtilsUser
{
    public static $status = array('0' => 'inactive', '1' => 'active', '2' => 'test');

    public static function getStatus() {
        return self::$status;
    }

    public static function findUsersByRole($_this, $roles)
    {
        $em = $_this->getDoctrine()->getManager();

        $query = $em->getRepository("AppBundle:User")
                    ->createQueryBuilder("u")
                    ->select("u")
                    ->join('u.user_role', 'r')
                    ->where("u.id != 0");

        if($_this->get('security.token_storage')->getToken()->getUser()->getService() != null)
             $tokenService = $_this->get('security.token_storage')->getToken()->getUser()->getService();
        else $tokenService = '0';

        if($roles != '')
        {
            $roles_in = '(0';
            foreach ($roles as $key => $role)
            {
                $roles_in .= ', '.$key.' ';
            }
            $roles_in .= ')';

            $query->andWhere("u.roleId IN ".$roles_in." ");
        }

        if($tokenService != '0')
        {
            $services_in = '(0';
            foreach ($tokenService as $key => $token)
            {
                $services_in .= ', '.$token.' ';
            }
            $services_in .= ')';

            $query->andWhere("r.id IN ".$services_in." ");
        }

        // TODO: Get Users by filters
        // if(isset($filter)) $query->where("u.".$filter." LIKE '%".$value."%'' ")

        return $query;
    }

    public static function newUser($_this, $request, $role_id)
    {
        if($_this->get('security.token_storage')->getToken()->getUser()->getService() != null)
             $tokenService = $_this->get('security.token_storage')->getToken()->getUser()->getService();
        else $tokenService = '0';
        
        $em = $_this->getDoctrine()->getManager();

        $user = new User();

        $form = $_this->createForm(new UserNewType(), $user, array('attr' => array('tokenService' => $tokenService,
                                                                                   'role' => $role_id )));
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

        if ($form->isSubmitted() && (!isset($formE) || $formE->isSubmitted()) )
        {
            if( $form->isValid() && (!isset($formE) || $formE->isValid()) )
            {
                if($user->getRoleId() == null)
                {
                    $role = $em->getRepository('AppBundle:Role')->find($role_id);

                    $user->setRoleId($role_id);
                    $user->addRole($role);
                }

                if($user->getService() != null)
                {
                    foreach ($user->getService() as $service)
                    {
                        if($service != 0)
                        {
                            $role = $em->getRepository('AppBundle:Role')->find($service);
                            $user->addRole($role);
                        }
                    }
                }

                if($user->getPassword() == null) $user = self::setNewPassword($_this, $user);

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
            else #Si $form->isValid() == false devolvemos el error
            {
                $return['form'] = $form->createView();

                if(isset($formE))
                    $return['formE'] = $formE->createView();
            }
        }
        return $return;
    }

    public static function editUser($_this, $request, $user)
    {
        $em = $_this->getDoctrine()->getManager();

        $role_id = $user->getRoleId();

        $form = $_this->createForm(new UserEditType(), $user, array('attr' => array('tokenService' => '0',
                                                                                    'role' => $role_id )));
        $form->handleRequest($request);
        $return = array('_locale' => $_this->get('locale'), 'user' => $user->getId(), 'role_id' => $role_id, 'form' => $form->createView());
        $entityName = self::getEntityName($role_id);

        if($entityName != null)
        {
            $entityType = 'AppBundle\Form\\'.$entityName.'Type';
            $entity = $em->getRepository('AppBundle:'.$entityName)->findOneByUser($user);
            $formE = $_this->createForm(new $entityType(), $entity);
            $formE->handleRequest($request);

            $return['formE'] = $formE->createView();
        }

        if ($form->isSubmitted() && (!isset($formE) || $formE->isSubmitted()) )
        {
            if( $form->isValid() && (!isset($formE) || $formE->isValid()) )
            {
                $user->setToken(self::getRandomToken());
                $em->persist($user);

                if($entityName != null)
                    $em->persist($entity);

                $em->flush();
                return false;
            }
            else #Si $form->isValid() == fasle devolvemos el error
            {
                $return['form'] = $form->createView();

                if(isset($formE))
                    $return['formE'] = $formE->createView();
            }
        }
        return $return;
    }

    public static function deleteUser($_this, $user)
    {
        $em = $_this->getDoctrine()->getManager();

        $return = array('_locale' => $_this->get('locale'), 'user' => $user->getId(), 'role_id' => $user->getRoleId());
        $entityName = self::getEntityName($user->getRoleId());
        if($entityName != null){
            $entity = $em->getRepository('AppBundle:'.$entityName)->findOneByUser($user->getId());
            $em->remove($entity);
        }
        $em->remove($user);
        $em->flush();
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

    public static function setNewPassword($_this, $user)
    {
        $user->setSalt(md5(uniqid()));
        $password = $_this->get('security.password_encoder')
                          ->encodePassword($user, $user->getPlainPassword(), $user->getSalt());
        $user->setPassword($password);

        return $user;
    }
    public static function checkOldPassword($_this, $user, $oldPass)
    {
        $oldPassword = $_this->get('security.password_encoder')
                          ->encodePassword($user, $oldPass, $user->getSalt());
                          
        return ($oldPassword == $user->getPassword());
    }

    public static function getMaxCodeWorkshop($em,$codePartner)
    {
        $query = $em->createQuery(
            'SELECT MAX(w.id) FROM AppBundle:Workshop w
             JOIN w.partner p
             WHERE p.codePartner = :codePartner'
        )->setParameter('codePartner', $codePartner);

        $max_id = $query->getSingleScalarResult();

        if($max_id == null) return 1;
        return $max_id+1;
    }

    public static function checkUser($_this, $user)
    {
        return true;
    }

    public static function checkWorkshop($em,$workshop)
    {
        if($workshop->getCodeWorkshop() == null){
            $workshop->setCodeWorkshop(self::getMaxCodeWorkshop($em,$workshop->getPartner()->getCodePartner()));
        }
        return true;
    }

    public static function checkPartner($em,$workshop)
    {
        return true;
    }
}