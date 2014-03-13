<?php

namespace Adservice\WorkshopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Adservice\WorkshopBundle\Form\WorkshopType;
use Adservice\WorkshopBundle\Entity\Workshop;
use Adservice\WorkshopBundle\Entity\Typology;
use Adservice\WorkshopBundle\Form\WorkshopOrderType;

class DefaultController extends Controller {

    /**
     * Devuelve todos los talleres segun el usuario logeado
     * @return type
     * @throws AccessDeniedException
     */
    public function listAction() {
        $em = $this->getDoctrine()->getEntityManager();
        
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false) {
            throw new AccessDeniedException();
        }
        
//        $logged_user = $this->get('security.context')->getToken()->getUser();
//        $workshops = $em->getRepository("WorkshopBundle:Workshop")->findByPartner($logged_user->getPartner()->getId());

//        $workshops = $em->getRepository("WorkshopBundle:Workshop")->findAll();
        $workshops = $em->getRepository("WorkshopBundle:Workshop")->findBy(array('register_pending' => null));
                
        return $this->render('WorkshopBundle:Default:list.html.twig', array('workshops' => $workshops));
    }

    public function newWorkshopAction() {
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false)
            throw new AccessDeniedException();
        $petition = $this->getRequest();
        $workshop  = new Workshop();
        $request = $this->getRequest();
        $form = $this->createForm(new WorkshopType(), $workshop);
        $form->bindRequest($request);
        
        if ($petition->getMethod() == 'POST') {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $workshop->setCreatedBy($this->get('security.context')->getToken()->getUser());
                $workshop->setCreatedAt(new \DateTime(\date("Y-m-d H:i:s")));
                $this->saveWorkshop($em, $workshop);

                return $this->redirect($this->generateUrl('workshop_list'));
            }
        }
        
        return $this->render('WorkshopBundle:Default:newWorkshop.html.twig', array('workshop'   => $workshop,
                                                                                   'form_name'  => $form->getName(),
                                                                                   'form'       => $form->createView()));
    }

    /**
     * Obtener los datos del workshop a partir de us ID para poder editarlo (solo lo puede hacer el ROLE_ADMIN)
     * Si la petición es GET  --> mostrar el formulario
     * Si la petición es POST --> save del formulario
     */
    public function editWorkshopAction($id) {
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false){
            throw new AccessDeniedException();
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        $workshop = $em->getRepository("WorkshopBundle:Workshop")->find($id);
        
        if (!$workshop) throw $this->createNotFoundException('Workshop no encontrado en la BBDD');

        $petition = $this->getRequest();
        $form = $this->createForm(new WorkshopType(), $workshop);
        
        if ($petition->getMethod() == 'POST') {
            $form->bindRequest($petition);
            if ($form->isValid()) $this->saveWorkshop($em, $workshop);
            return $this->redirect($this->generateUrl('workshop_list'));
        }

        return $this->render('WorkshopBundle:Default:editWorkshop.html.twig', array('workshop'   => $workshop,
                                                                                    'form_name'  => $form->getName(),
                                                                                    'form'       => $form->createView()));
    }

    public function deleteWorkshopAction($id) {
                
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false){
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getEntityManager();
        $workshop = $em->getRepository("WorkshopBundle:Workshop")->find($id);
        if (!$workshop) throw $this->createNotFoundException('Workshop no encontrado en la BBDD');
        
        $em->remove($workshop);
        $em->flush();
        
        return $this->redirect($this->generateUrl('workshop_list'));
    }
    
    
    /**
     * Crea una nueva solicitud de alta de taller
     * @return type
     * @throws AccessDeniedException
     */
    public function newWorkshopOrderAction(){
        
        //solo pasan ROLE_AD y ROLE_ADMIN
        if ($this->get('security.context')->isGranted('ROLE_AD') === false)
            throw new AccessDeniedException();
        
        $petition = $this->getRequest();
        $workshop  = new Workshop();

        $request = $this->getRequest();
        $form = $this->createForm(new WorkshopOrderType(), $workshop);
        
        if ($petition->getMethod() == 'POST') {
            $form->bindRequest($petition);
            
            if ($form->isValid())
                //creamos un taller pendiente de alta
                $em = $this->getDoctrine()->getEntityManager();
                $user = $this->get('security.context')->getToken()->getUser();
                $workshop->setActive(false);
                $workshop->setRegisterPending(true);
                $workshop->setPartner($user->getPartner());
                $workshop->setCreatedAt(new \DateTime(\date("Y-m-d H:i:s")));
                $workshop->setModifiedAt(new \DateTime(\date("Y-m-d H:i:s")));
                $workshop->setCreatedBy($user);
                $workshop->setModifyBy($user);
                
                $em->persist($workshop);
                $em->flush();
                
                $user_role = 'ad';
                $workshops_pending_orders = $em->getRepository("WorkshopBundle:Workshop")->findBy(array('register_pending'  => 1,
                                                                                                        'partner'           => $user->getPartner()->getId()));
                return $this->render('WorkshopBundle:Default:listWorkshopOrder.html.twig', array('user_role'                => $user_role,
                                                                                                 'workshops_pending_orders' => $workshops_pending_orders));
        }
        
        return $this->render('WorkshopBundle:Default:newWorkshopOrder.html.twig', array('workshop'   => $workshop,
                                                                                        'form_name'  => $form->getName(),
                                                                                        'form'       => $form->createView()));
        
        
    }
    
    /**
     * Funcion que lista las solicitudes de alta pendientes
     * @return type
     */
    public function listWorkshopOrderAction(){
        $user = $this->get('security.context')->getToken()->getUser();
        $role = $user->getRoles();
        $em = $this->getDoctrine()->getEntityManager();
        
        if ($role[0]->getRole() == "ROLE_ADMIN"){
            //vera todas las solicitudes de todos los socios
            $user_role = 'admin';
            $workshops_pending_orders = $em->getRepository("WorkshopBundle:Workshop")->findBy(array('register_pending' => 1));
            
        }elseif ($role[0]->getRole() == "ROLE_AD"){
            //solo sus solicitudes
            $user_role = 'ad';
            $workshops_pending_orders = $em->getRepository("WorkshopBundle:Workshop")->findBy(array('register_pending'  => 1,
                                                                                                    'partner'           => $user->getPartner()->getId()));
        }
        
        return $this->render('WorkshopBundle:Default:listWorkshopOrder.html.twig', array('user_role'                => $user_role,
                                                                                         'workshops_pending_orders' => $workshops_pending_orders));
    }
    
    /**
     * Funcion que lista las solicitudes de activación/desactivación
     * @return type
     */
    public function activeWorkshopOrderAction(){
        $em = $this->getDoctrine()->getEntityManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $role = $user->getRoles();
        
        if ($role[0]->getRole() == "ROLE_ADMIN"){
            $user_role = 'admin';
            //queremos todos los talleres que estan pendientes de activacion/desactivacion...
            $workshops = $em->getRepository("WorkshopBundle:Workshop")->findBy(array('deactivate_pending' => 1));
            $workshops = array_merge($workshops, $em->getRepository("WorkshopBundle:Workshop")->findBy(array('activate_pending' => 1)));

        }elseif ($role[0]->getRole() == "ROLE_AD"){
            $user_role = 'ad';
            //solo queremos los talleres que me pertenezcan...
//            $workshops = $user->getPartner()->getWorkshops();
            
            $workshops = $em->getRepository("WorkshopBundle:Workshop")->findBy(array('register_pending' => null,
                                                                                     'partner'          => $user->getPartner()->getId()));
            $workshops = array_merge($workshops, $em->getRepository("WorkshopBundle:Workshop")->findBy(array('register_pending' => 0,
                                                                                                             'partner'          => $user->getPartner()->getId())));
        }
        
        return $this->render('WorkshopBundle:Default:activationWorkshopOrder.html.twig', array('workshops' => $workshops,
                                                                                               'user_role' => $user_role));
        
    }
    
    /**
     * Si es llamada con un usuario con rol "ad" hara una peticion de activacion o desactivacion de un taller
     * Si es llamada con un usuario con rol "admin" activara o desactivara un taller
     * @param int $id id del taller
     * @param string $action (activate | deactivate) define la acción a realizar
     */
    public function changeStatusWorkshopOrderAction($id, $action){
        $em = $this->getDoctrine()->getEntityManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $role = $user->getRoles();
        $workshop = $em->getRepository("WorkshopBundle:Workshop")->find($id);
        if ($role[0]->getRole() == "ROLE_ADMIN"){
            $user_role = 'admin';
            if ($action == 'activate'){
                $workshop->setActive(true);
                $workshop->setDeactivatePending(false);
                $workshop->setActivatePending(false);
            }elseif ($action == 'deactivate'){
                $workshop->setActive(false);
                $workshop->setDeactivatePending(false);
                $workshop->setActivatePending(false);
                
            }
            $this->saveWorkshop($em, $workshop);
            
            //todos los workshops con estados pendientes
            $workshops = $em->getRepository("WorkshopBundle:Workshop")->findBy(array('deactivate_pending' => 1));
            $workshops = array_merge($workshops, $em->getRepository("WorkshopBundle:Workshop")->findBy(array('activate_pending' => 1)));
            
        }elseif ($role[0]->getRole() == "ROLE_AD"){
            $user_role = 'ad';
            if ($action == 'activate'){
                $workshop->setActivatePending(true);
                $workshop->setDeactivatePending(false);
            }elseif ($action == 'deactivate'){
                $workshop->setDeactivatePending(true);
                $workshop->setActivatePending(false);
            }
            $this->saveWorkshop($em, $workshop);
            
            //workshops del usuario logeado
            $workshops = $user->getPartner()->getWorkshops();
        }
        
        
        return $this->render('WorkshopBundle:Default:activationWorkshopOrder.html.twig', array('workshops' => $workshops,
                                                                                               'user_role' => $user_role));
    }
    /**
     * Hace el alta o baja de un taller
     * @param int $id id del taller
     * @param string $action OK -> aceptar la alta, 
     *                       KO -> denegarla
     */
    public function registerWorkshopOrderAction($id, $action){
        
        $em = $this->getDoctrine()->getEntityManager();
        $workshop = $em->getRepository("WorkshopBundle:Workshop")->find($id);
        
        if ($action == 'OK'){
            $workshop->setActive(true);
            $workshop->setRegisterPending(null);
            $this->saveWorkshop($em, $workshop);
            
        }elseif ($action == 'KO'){
            $workshop->setRegisterPending(0); //rechazado
            $em->remove($workshop);
            $em->flush();
        }
        
        $user = $this->get('security.context')->getToken()->getUser();
        $role = $user->getRoles();
        if ($role[0]->getRole() == "ROLE_ADMIN")    $user_role = 'admin';
        elseif ($role[0]->getRole() == "ROLE_AD")   $user_role = 'ad';
        
        $workshops_pending_orders = $em->getRepository("WorkshopBundle:Workshop")->findBy(array('register_pending'  => 1));
        return $this->render('WorkshopBundle:Default:listWorkshopOrder.html.twig', array('user_role'                => $user_role,
                                                                                         'workshops_pending_orders' => $workshops_pending_orders));
        
        
        
    }
    
    /**
     * Hace el save de un workshop
     * @param EntityManager $em
     * @param Workshop $workshop
     */
    private function saveWorkshop($em, $workshop){
        $workshop->setModifiedAt(new \DateTime(\date("Y-m-d H:i:s")));
        $workshop->setModifyBy($this->get('security.context')->getToken()->getUser());
        $em->persist($workshop);
        $em->flush();
    }

}
