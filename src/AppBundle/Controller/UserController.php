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
    public function usersAction(Request $request, $role_id, $page=1/*, $filter=0, $value=0*/)
    {
        // Si no hay rol redirige a la funcion de selección de rol para el nuevo usuario
        if($role_id == null) return $this->redirectToRoute('selectRole');

        // Si entras en un rol al que no tienes permisos para acceder te devuelve al listado de selección de rol
        if(!$this->get('utils')->hasAccessTo($this, $role_id))
        {
            $this->get('session')->getFlashBag()->add('ko' , $this->get('translator')->trans('access_denied'));
            return $this->redirectToRoute('selectRole');
        }
        $em = $this->getDoctrine()->getManager();

        $query = $this->get('utilsUser')->findUsersByRole($this, $role_id);

        // TODO: Si hay filtros filtramos los resultados
        //if($filter != 0) $query->where("u.".$filter." LIKE '%".$value."%'' ")
        // Deberiamos filtrar con un FORM para gestionar todos los campos simultaneamente


        /**********************************************************************************/
        /**********************************************************************************/
        /**********************************************************************************/

        // TODO: hacer un formulario por cada rol para los filtros de los listados








        /**********************************************************************************/
        /**********************************************************************************/
        /**********************************************************************************/

        $pagination = $this->get('knp_paginator')->paginate($query->getQuery(), $page, 10);

        return $this->render('user/users.html.twig', array('pagination' => $pagination, 'role_id' => $role_id));
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
            return $this->redirectToRoute('users', array('role_id' => $role_id));
    }
    /*
     * @ParamConverter("user", class="AppBundle:User")
     */
    public function userEditAction(Request $request, User $user, $role_id=null)
    {
        // Si entras en tu mismo usuario solo se te permite resetear password y mostrar tus datos
        if ($this->get('security.token_storage')->getToken()->getUser()->getId() == $user->getId())
            return $this->redirectToRoute('userView', array('id' => $user->getId(), 'role_id' => $role_id));

        // Si entras en un rol al que no tienes permisos para acceder te devuelve al listado de usuarios
        if(!$this->get('utils')->hasAccessTo($this, $user->getRoleId()))
        {
            $this->get('session')->getFlashBag()->add('ko' , 'access_denied');
            return $this->redirectToRoute('users', array('role_id' => $role_id));
        }
        // Editamos el usuario
        $return = $this->get('utilsUser')->editUser($this, $request, $user);
        
        if($return)
            return $this->render('user/user.html.twig', $return);
        else
            return $this->redirectToRoute('users', array('role_id' => $role_id));
    }

    /*
     * @ParamConverter("user", class="AppBundle:User")
     */
    public function userViewAction(Request $request, User $user, $role_id=null)
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
                $this->get('session')->getFlashBag()->add('ok' , $this->get('translator')->trans('password_changed'));
            }
            else
            {
                // Show error old password
                $this->get('session')->getFlashBag()->add('ko' , $this->get('translator')->trans('password_old_error'));
            }
        }
        
        return $this->render('user/user_view.html.twig', array('role_id' => $role_id, 'user' => $user, 'form' => $form->createView()));
    }

    /*
     * @ParamConverter("user", class="AppBundle:User")
     * @throws AccessDeniedException
     */
    public function searchIdAction(User $user, $role_id=null)
    {
        $service = $this->get('security.token_storage')->getToken()->getUser()->getService();

        if( $service == NULL or $service == $user->getService()) 
        {
            return $this->redirectToRoute('userEdit', array('id' => $user->getId(), 'role_id' => $role_id ));
        }
        else throw new AccessDeniedException();
    }

    /*
     * @ParamConverter("user", class="AppBundle:User")
     */
    public function userDeleteAction(User $user, $role_id=null)
    {
        $this->get('utilsUser')->deleteUser($this, $user);

        return $this->redirect($this->generateUrl('users', array('role_id' => $role_id)));

    }
}