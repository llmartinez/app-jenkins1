<?php

namespace Adservice\WorkshopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Adservice\WorkshopBundle\Entity\Workshop;
use Adservice\WorkshopBundle\Entity\WorkshopOrder;
use Adservice\WorkshopBundle\Entity\Typology;
use Adservice\WorkshopBundle\Form\WorkshopType;
use Adservice\WorkshopBundle\Form\WorkshopNewOrderType;
use Adservice\WorkshopBundle\Form\WorkshopOrderType;

class WorkshopOrderController extends Controller {

    public function newWorkshopOrderAction($id = null, $action) {

        if ($this->get('security.context')->isGranted('ROLE_AD') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        
        $workshop = $em->getRepository("WorkshopBundle:Workshop")->find($id);
        if (!$workshop)
            throw $this->createNotFoundException('Workshop no encontrado en la BBDD');

        if ($request->getMethod() == 'POST') {
            
            if ($action == 'modify'){

                $workshopOrder = $this->workshop_to_workshopOrder($workshop);
                $workshopOrder->setAction($action);
                
                $form = $this->createForm(new WorkshopOrderType(), $workshopOrder);
                $form->bindRequest($request);

                $this->saveWorkshopOrder($em, $workshopOrder);
                return $this->redirect($this->generateUrl('user_index'));
                
            }elseif ($action == 're_modify'){
                //antes de guardar la nueva peticion de modificacion, eliminamos la anterior
                $workshopOrder = $em->getRepository("WorkshopBundle:WorkshopOrder")->findOneBy(array('id_workshop' => $id));
                $em->remove($workshopOrder);
                $em->flush();
                
                $workshopOrder = $this->workshop_to_workshopOrder($workshop);
                $workshopOrder->setAction($action);
                
                $form = $this->createForm(new WorkshopOrderType(), $workshopOrder);
                $form->bindRequest($request);
                
                $this->saveWorkshopOrder($em, $workshopOrder);
                return $this->redirect($this->generateUrl('user_index'));
            }
            
            
        }else{
            
            if ($action == 'modify') {
                $form = $this->createForm(new WorkshopOrderType(), $workshop);
                
                return $this->render('WorkshopBundle:WorkshopOrders:newWorkshopOrder.html.twig', array('workshop'   => $workshop,               //old values
                                                                                                       'form_name'  => $form->getName(),        //new values
                                                                                                       'form'       => $form->createView(),
                                                                                                       'action'     => $action));
             }elseif ($action == 're_modify'){
                $workshop = $em->getRepository("WorkshopBundle:Workshop")->find($id);
                $workshopOrder = $em->getRepository("WorkshopBundle:WorkshopOrder")->findOneBy(array('id_workshop' => $id));
                $form = $this->createForm(new WorkshopOrderType(), $workshopOrder);

                return $this->render('WorkshopBundle:WorkshopOrders:newWorkshopOrder.html.twig', array('workshop'       => $workshop,           //old values
                                                                                                       'form_name'      => $form->getName(),    //new values
                                                                                                       'form'           => $form->createView(),
                                                                                                       'action'         => $action));
            }
        }
    }

    public function listWorkshopsAction() {
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getEntityManager();
        $workshops = $em->getRepository("WorkshopBundle:Workshop")->findBy(array('partner' => $user->getPartner()->getId()));


        return $this->render('WorkshopBundle:WorkshopOrders:listWorkshops.html.twig', array('workshops' => $workshops,
                    'user' => $user));
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
//            //vera todas las solicitudes de todos los socios
            $user_role = 'admin';
            $workshopsOrders = $em->getRepository("WorkshopBundle:WorkshopOrder")->findAll();
            
        }elseif ($role[0]->getRole() == "ROLE_AD"){
            //solo sus solicitudes
            $user_role = 'ad';
            $workshopsOrders = $em->getRepository("WorkshopBundle:WorkshopOrder")->findBy(array('partner' => $user->getPartner()->getId()));
        }
        
        return $this->render('WorkshopBundle:WorkshopOrders:listWorkshopOrder.html.twig', array('user_role'         => $user_role,
                                                                                                'workshopsOrders'   => $workshopsOrders,
                                                                                                'user'              => $user));
    }
    
    public function changeWorkshopStatusOrderAction($id, $status){
        
        $em = $this->getDoctrine()->getEntityManager();
        $workshop = $em->getRepository("WorkshopBundle:Workshop")->find($id);
        if (!$workshop)
            throw $this->createNotFoundException('Workshop no encontrado en la BBDD');
        
        $user = $this->get('security.context')->getToken()->getUser();
        $role = $user->getRoles();
        
        if ($role[0]->getRole() == "ROLE_ADMIN"){
            if ($status == 'activate'){
                $workshop->setActive(true);
            }elseif ($status == 'deactivate'){
                $workshop->setActive(falses);
            }
            $this->saveWorkshopOrder($em, $workshop);   //seria saveWorkshop, pero para el caso nos da igual
            return $this->redirect($this->generateUrl('workshopOrder_workshoplist'));
            
        }elseif ($role[0]->getRole() == "ROLE_AD"){
            $workshopOrder = $this->workshop_to_workshopOrder($workshop);
            $workshopOrder->setAction($status);
            $this->saveWorkshopOrder($em, $workshopOrder);
            return $this->redirect($this->generateUrl('user_index'));
        }
        
        
    }
    
    
    private function workshop_to_workshopOrder($workshop) {
        
        $workshopOrder = new WorkshopOrder();
        
        $workshopOrder->setIdWorkshop($workshop->getId());
        $workshopOrder->setName($workshop->getName());
        $workshopOrder->setCif($workshop->getCif());
        $workshopOrder->setNumAdClient($workshop->getNumAdClient());
        $workshopOrder->setAddress($workshop->getAddress());
        $workshopOrder->setCity($workshop->getCity());
        $workshopOrder->setRegion($workshop->getRegion());
        $workshopOrder->setProvince($workshop->getProvince());
        $workshopOrder->setPhoneNumber1($workshop->getPhoneNumber1());
        $workshopOrder->setPhoneNumber2($workshop->getPhoneNumber2());
        $workshopOrder->setMovilePhone1($workshop->getMovilePhone1());
        $workshopOrder->setMovilePhone2($workshop->getMovilePhone2());
        $workshopOrder->setFax($workshop->getFax());
        $workshopOrder->setEmail1($workshop->getEmail1());
        $workshopOrder->setEmail2($workshop->getEmail2());
        $workshopOrder->setContact($workshop->getContact());
        $workshopOrder->setPartner($workshop->getPartner());
        $workshopOrder->setTypology($workshop->getTypology());
        
        $workshopOrder->setCreatedAt(new \DateTime(\date("Y-m-d H:i:s")));
        $workshopOrder->setActive(false);
        
        return $workshopOrder;
    }

    /**
     * Hace el save de un workshopOrder
     * @param EntityManager $em
     * @param WorkshopOrder $workshopOrder
     */
    private function saveWorkshopOrder($em, $workshopOrder){
        $workshopOrder->setModifiedAt(new \DateTime(\date("Y-m-d H:i:s")));
        $workshopOrder->setModifyBy($this->get('security.context')->getToken()->getUser());
        $em->persist($workshopOrder);
        $em->flush();
    }
}
