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

        $roleId = $this->get('security.token_storage')->getToken()->getUser()->getRoleId();
        $getRolesFor = "getRolesFor".$this->get('utils')->getRoles($roleId);
        $roles = $this->get('utilsRole')->$getRolesFor();

        $query = $this->get('utilsUser')->findUsersByRole($em, $roles);
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
        
        $pagination = $this->get('knp_paginator')->paginate($query->getQuery(), $page, 10);

        return $this->render('user/users.html.twig', array('pagination' => $pagination));
    }

    /** Selecciona el rol del usuario a crear */
    public function selectRoleAction(Request $request)
    {
        $roleId = $this->get('security.token_storage')->getToken()->getUser()->getRoleId();
        $getRolesFor = "getRolesFor".$this->get('utils')->getRoles($roleId);
        $roles = $this->get('utilsRole')->$getRolesFor();

        return $this->render('user/selectRole.html.twig', array('roles' => $roles));
    }

    public function userNewAction(Request $request, $role_id=null)
    {
        // Si no hay rol redirige a la funcion de selección de rol para el nuevo usuario
        if($role_id == null) return $this->redirectToRoute('selectRole');
        
        $return = $this->get('utilsUser')->newUser($this, $request, $role_id);

        if($return)
            return $this->render('user/user.html.twig', $return);
        else
            return $this->redirectToRoute('users');
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
            return $this->redirectToRoute('users');
    }

    /*
     * @ParamConverter("user", class="AppBundle:User")
     * @throws AccessDeniedException
     */
    public function searchIdAction(User $user)
    {
        $service = $this->get('security.token_storage')->getToken()->getUser()->getCategoryService();

        if( $service == NULL or $service == $user->getCategoryService()) 
        {
            return $this->redirectToRoute('userEdit', array('id' => $user->getId() ));
        }
        else throw new AccessDeniedException();
    }

    /*
     * @ParamConverter("user", class="AppBundle:User")
     */
    public function userDeleteAction(User $user)
    {
        $this->get('utilsUser')->deleteUser($this, $user);

        return $this->redirect($this->generateUrl('users'));

    }
}