<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Entity\User;
use AppBundle\Form\UserPasswordType;

class UserController extends Controller
{
    //    $this->isGranted('ROLE_ADMIN');
    //    $this->getUser();

    public function usersAction(Request $request, $page=1/*, $filter=0, $value=0*/)
    {
        $em = $this->getDoctrine()->getManager();

        $user  = $this->get('security.token_storage')->getToken()->getUser();
        $roles = $this->get('utils')->getRolesForRole($user->getRoleId());

        $query = $this->get('utilsUser')->findUsersByRole($this, $roles);
        /* TODO: filtrar por Service del user_token*/

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
        $tokenRole = $this->get('security.token_storage')->getToken()->getUser()->getRoleId();
        $roles = $this->get('utils')->getRolesForRole($tokenRole);

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
        // Si entras en tu mismo usuario solo se te permite resetear password y mostrar tus datos
        if ($this->get('security.token_storage')->getToken()->getUser()->getId() == $user->getId())
        {
            return $this->redirectToRoute('userView', array('id' => $user->getId()));
        }
        $return = $this->get('utilsUser')->editUser($this, $request, $user);
        
        if($return)
            return $this->render('user/user.html.twig', $return);
        else
            return $this->redirectToRoute('users');
    }

    /*
     * @ParamConverter("user", class="AppBundle:User")
     */
    public function userViewAction(Request $request, User $user)
    {
        $form = $this->createForm(new UserPasswordType(), $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if($this->get('utilsUser')->checkOldPassword($this, $user, $request->request->get('oldPassword')))
            {
                $user = $this->get('utilsUser')->setNewPassword($this, $user);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                // Show confirmation of new password
                $this->get('session')->getFlashBag()->add('ok' , 'password_changed');
            }
            else
            {
                // Show error old password
                $this->get('session')->getFlashBag()->add('ko' , 'password_old_error');
            }
        }
        
        return $this->render('user/user_view.html.twig', array('user' => $user, 'form' => $form->createView()));
    }

    /*
     * @ParamConverter("user", class="AppBundle:User")
     * @throws AccessDeniedException
     */
    public function searchIdAction(User $user)
    {
        $service = $this->get('security.token_storage')->getToken()->getUser()->getService();

        if( $service == NULL or $service == $user->getService()) 
        {
            return $this->redirectToRoute('userEdit', array('id' => $user->getId() ));
        }
        else throw new AccessDeniedException();
    }
}