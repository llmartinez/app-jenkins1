<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Entity\User;
use AppBundle\Entity\Workshop;
use AppBundle\Form\UserType;
use AppBundle\Form\WorkshopType;

class UserController extends Controller
{
    //    $this->isGranted('ROLE_ADMIN');
    //    $this->getUser();

    public function usersAction(Request $request, $page=1/*, $filter=0, $value=0*/)
    {
        $em = $this->getDoctrine()->getManager();
        /*
            if($filter==0 && $value==0) $query = $em->getRepository('AppBundle:User')->findAll();
            else{
                $query = $em->getRepository("AppBundle:User")
                        ->createQueryBuilder("u")
                        ->select("u")
                        ->where("u.".$filter." LIKE '%".$value."%'' ")
                        ->getQuery();
            }
        */
        $query = $em->getRepository('AppBundle:User')->findAll();
        
        $pagination = $this->get('knp_paginator')->paginate($query, $page, 10);

        return $this->render('user/users.html.twig', array('pagination' => $pagination));
    }

    /** Selecciona el rol del usuario a crear */
    public function selectRoleAction(Request $request)
    {
        if( $this->isGranted('ROLE_ADMIN') ) $roles = $this->get('user')->getRolesForAdmin();
        elseif( $this->isGranted('ROLE_PARTNER') ) $roles = $this->get('user')->getRolesForPartner();
        else $roles = null;

        return $this->render('user/selectRole.html.twig', array('roles' => $roles));
    }

    public function userNewAction(Request $request, $role_id=null)
    {
        // Si no hay rol redirige a la funcion de selección de rol para el nuevo usuario
        if($role_id == null) return $this->redirect($this->generateUrl('selectRole'));

        $em = $this->getDoctrine()->getManager();

        $user = new User();

        $form = $this->createForm(new UserType(), $user);
        $return = array('_locale' => $this->get('locale'), 'role_id' => $role_id, 'form' => $form->createView());

        $entityName = $this->getEntityName($role_id);

        if($entityName != null)
        {
            $entityType = 'AppBundle\Form\\'.$entityName.'Type';
            $entityCheck = 'check'.$entityName;
            $entityName = 'AppBundle\Entity\\'.$entityName;

            $entity = New $entityName();
            $entity->setUser($user);

            $formE = $this->createForm(new $entityType(), $entity);
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
                $password = $this->get('security.password_encoder')
                    ->encodePassword($user, $user->getPlainPassword(), $user->getSalt());
                $user->setPassword($password);
            }
            if($this->checkUser($user)) {
                if ($entityName != null) {
                    if ($this->$entityCheck($entity)) {
                        $em->persist($user);
                        $em->persist($entity);
                    }
                } else
                    $em->persist($user);
            }
            $em->flush();

            return $this->redirect($this->generateUrl('users'));
        }


        return $this->render('user/user.html.twig', $return);
    }
    public function checkWorkshop($workshop){
        if($workshop->getId() == null){
            $workshop->setId($this->getMaxIdWorkshop($workshop));
        }
        //Faltan comprobaciones de que Workshop sea correcto
        return true;
    }

    public function checkUser($user){
        //Faltan comprobaciones de que User sea correcto
        return true;
    }

    public function getMaxIdWorkshop($workshop){
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT MAX(w.id)
             FROM AppBundle:Workshop w
             WHERE w.partner = :idPartner'
        )->setParameter('idPartner', $workshop->getPartner()->getId());

        $max_id = $query->getSingleScalarResult();
        if($max_id == null)
            return 1;
        return $max_id+1;
    }


    public function getEntityName($role_id)
    {
        if($role_id == 6) $entityName = 'Partner';
        elseif($role_id == 9) $entityName = 'Workshop';
        else $entityName = null;
        return $entityName;
    }
    /*
     * @paramconverter("user", class="AppBundle:User")
     */
    public function userEditAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new UserType(), $user);
        $form->handleRequest($request);
        $return = array('_locale' => $this->get('locale'), 'user' => $user->getId(), 'form' => $form->createView());
        $entityName = $this->getEntityName($user->getRoleId());

        if($entityName != null)
        {
            $entityType = 'AppBundle\Form\\'.$entityName.'Type';
            $entity = $em->getRepository('AppBundle:'.$entityName)->findByUser($user);

            $formE = $this->createForm(new $entityType(), $entity);
            $formE->handleRequest($request);

            $return['formE'] = $formE->createView();
        }
        if ($form->isSubmitted() && $form->isValid() && (!isset($formE) || $formE->isSubmitted() && $formE->isValid()))
        {
            $em->persist($user);
            $em->flush();
            if($entityName != null)
                $em->persist($entity);
            return $this->redirect($this->generateUrl('user'));
        }

        # Devolvemos
        return $this->render('user/user.html.twig', $return);
    }
}