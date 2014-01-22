<?php

namespace Adservice\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Adservice\UsuarioBundle\Form\UsuarioType;

class DefaultController extends Controller
{
    
    public function indexAction()
    {
        return $this->render('UsuarioBundle:Default:index.html.twig');
    }
    
    /**
     * Obtener los datos del usuario logueado
     * Si la petición es GET  --> mostrar el formulario
     * Si la petición es POST --> save del formulario
     */
    public function perfilAction() {

        $em = $this->getDoctrine()->getEntityManager();
        $peticion = $this->getRequest();
        $logged_user = $this->get('security.context')->getToken()->getUser();
        $id_user = $logged_user->getId();
        $user = $em->getRepository('UsuarioBundle:Usuario')->find($id_user);
        $form = $this->createForm(new UsuarioType(), $user);

        if (!$user)
            throw $this->createNotFoundException('Usuario no encontrado en la BBDD');

        if ($peticion->getMethod() == 'POST') {
            $form->bindRequest($peticion);
            if ($form->isValid()) {
                $em->persist($user);
                $em->flush();
            }
            return $this->redirect($this->generateUrl('usuario_perfil'));
        }


        return $this->render('UsuarioBundle:Default:perfil.html.twig', array('user' => $user,
                                                                        'edit_form' => $form->createView(),
        ));
    }
}
