<?php

namespace Adservice\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;
use Adservice\UserBundle\Form\UserType;
use Adservice\UserBundle\Entity\User;
use Adservice\UtilBundle\Entity\Region;
use Adservice\UtilBundle\Entity\Province;


class DefaultController extends Controller
{
    
    public function indexAction()
    {
        return $this->render('UserBundle:Default:index.html.twig');
    }
    
    /**
     * Obtener los datos del usuario logueado
     * Si la petición es GET  --> mostrar el formulario
     * Si la petición es POST --> save del formulario
     */
    public function profileAction() {

        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();
        $id_logged_user = $this->get('security.context')->getToken()->getUser()->getId();
        $user = $em->getRepository('UserBundle:User')->find($id_logged_user);
        $form = $this->createForm(new UserType(), $user);
        
        if (!$user) throw $this->createNotFoundException('Usuario no encontrado en la BBDD');

        $original_password = $form->getData()->getPassword();
        
        if ($petition->getMethod() == 'POST') {
            $form->bindRequest($petition);
            if ($form->isValid()) {
                
                if ($user->getPassword() == null ) {
                    $user->setPassword($original_password);
                }else{
                    //codificamos el password
                    $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
                    $salt = md5(time());
                    $password = $encoder->encodePassword($user->getPassword(), $salt);
                    $user->setPassword($password);
                    $user->setSalt($salt);
                }

                $em->persist($user);
                $em->flush();
                
            }
            return $this->redirect($this->generateUrl('user_index'));
        }
        
        
        return $this->render('UserBundle:Default:profile.html.twig', array('user'       => $user,
                                                                           'form_name'  => $form->getName(),
                                                                           'form'       => $form->createView()
                                                                          ));
    }
    
    public function userListAction(){
        $em = $this->getDoctrine()->getEntityManager();
        $all_users = $em->getRepository("UserBundle:User")->findAll();
        $users_role_admin = array();
        $users_role_user = array();
        foreach ($all_users as $user) {
            $role = $user->getRoles();
            if ($role[0]->getRole() == "ROLE_ADMIN"){
                $users_role_admin[] = $user;
            }elseif ($role[0]->getRole() == "ROLE_USER"){
                $users_role_user[] = $user;
            }
 
        }
        
        return $this->render('UserBundle:Default:list.html.twig', array('users_role_admin' => $users_role_admin,
                                                                        'users_role_user'  => $users_role_user));
    }
    
}
