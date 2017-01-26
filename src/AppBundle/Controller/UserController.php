<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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

    public function userAction(Request $request, $role_id=null, $id=null)
    {
        // Si no hay rol redirige a la funcion de selección de rol para el nuevo usuario
        if($role_id == null) return $this->redirect($this->generateUrl('selectRole'));

        $em = $this->getDoctrine()->getManager();

        if($id == null) // NewEntity
        {
            $user = new User();
            $workshop = New Workshop();
            $workshop->setUser($user);
        }
        else // EditEntity
        {
            $user = $em->getRepository('AppBundle:User')->find($id);
            $workshop = $em->getRepository('AppBundle:Workshop')->findByUser($user);
        }

        $form = $this->createForm(new UserType(), $user);
        if($role_id == 9) $formW = $this->createForm(new WorkshopType(), $workshop);
        
        $form->handleRequest($request);
        $formW->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $formW->isSubmitted() && $formW->isValid())
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

            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('login'));
        }

        # Devolvemos
        $return = array('_locale' => $this->get('locale'), 'role_id' => $role_id, 'form' => $form->createView());
        if($role_id == 9) $return['formW'] = $formW->createView();

        return $this->render('user/user.html.twig', $return);
    }
}