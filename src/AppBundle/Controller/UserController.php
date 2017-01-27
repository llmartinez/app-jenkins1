<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Entity\User;


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
        if( $this->isGranted('ROLE_ADMIN') ) $roles = $this->get('utilsUser')->getRolesForAdmin();
        elseif( $this->isGranted('ROLE_PARTNER') ) $roles = $this->get('utilsUser')->getRolesForPartner();
        else $roles = null;

        return $this->render('user/selectRole.html.twig', array('roles' => $roles));
    }

    public function userNewAction(Request $request, $role_id=null)
    {
        // Si no hay rol redirige a la funcion de selección de rol para el nuevo usuario
        if($role_id == null) return $this->redirect($this->generateUrl('selectRole'));
        $return = $this->get('utilsUser')->newUser($this, $request, $role_id);

        if($return)
            return $this->render('user/user.html.twig', $return);
        else
            return $this->redirect($this->generateUrl('users'));
    }
    /*
     * @ParamConverter("user", class="AppBundle:User")
     */
    public function userEditAction(Request $request, User $user)
    {
        $return = $this->get('utilsUser')->editUser($this, $request, $user);
        if($return)
            return $this->render('user/user.html.twig', $return);
        else
            return $this->redirect($this->generateUrl('users'));
    }
}