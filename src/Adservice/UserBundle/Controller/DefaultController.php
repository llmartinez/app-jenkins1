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
        
//        $region = $em->getRepository("UtilBundle:Region")->find(1);
//        $provinces = $em->getRepository("UtilBundle:Province")->findBy(array('region' => $region->getId()));
////        echo "<pre>";
//        var_dump($provinces);
////        echo "</pre>";
//die;        
        if (!$user)
            throw $this->createNotFoundException('Usuario no encontrado en la BBDD');

        if ($petition->getMethod() == 'POST') {
            $form->bindRequest($petition);
            if ($form->isValid()) {
                $em->persist($user);
                $em->flush();
            }
            return $this->redirect($this->generateUrl('user_index'));
        }
        
        
        return $this->render('UserBundle:Default:profile.html.twig', array('user'       => $user,
                                                                           'form_name'  => $form->getName(),
                                                                           'form'       => $form->createView(),
        ));
    }
    
}
