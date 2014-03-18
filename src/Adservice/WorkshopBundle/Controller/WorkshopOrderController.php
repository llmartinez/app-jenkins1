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
use Adservice\WorkshopBundle\Form\WorkshopRejectedReasonType;

class WorkshopOrderController extends Controller {

    public function newWorkshopOrderAction($id, $action) {

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
                
//                $form = $this->createForm(new WorkshopOrderType(), $workshopOrder);
//                $form->bindRequest($request);

                $this->saveWorkshopOrder($em, $workshopOrder);
                return $this->redirect($this->generateUrl('user_index'));
                
            }elseif ($action == 're_modify'){
                //antes de guardar la nueva peticion de modificacion, eliminamos la anterior
                $workshopOrder = $em->getRepository("WorkshopBundle:WorkshopOrder")->findOneBy(array('id_workshop' => $id));
                $em->remove($workshopOrder);
                $em->flush();
                
                $workshopOrder = $this->workshop_to_workshopOrder($workshop);
                $workshopOrder->setAction($action);
                
//                $form = $this->createForm(new WorkshopOrderType(), $workshopOrder);
//                $form->bindRequest($request);
                
                $this->saveWorkshopOrder($em, $workshopOrder);
                return $this->redirect($this->generateUrl('user_index'));
                
            }elseif ($action == 'rejected'){           
                $workshopOrder = $em->getRepository("WorkshopBundle:WorkshopOrder")->findOneBy(array('id_workshop' => $id));
                $form = $this->createForm(new WorkshopRejectedReasonType(), $workshopOrder);
                $form->bindRequest($request);
                $rejection_reason = $form->get('rejection_reason')->getData();
                $workshopOrder->setRejectionReason($rejection_reason);
                
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
//                $workshop = $em->getRepository("WorkshopBundle:Workshop")->find($id);
                $workshopOrder = $em->getRepository("WorkshopBundle:WorkshopOrder")->findOneBy(array('id_workshop' => $id));
                $form = $this->createForm(new WorkshopOrderType(), $workshopOrder);

                return $this->render('WorkshopBundle:WorkshopOrders:newWorkshopOrder.html.twig', array('workshop'       => $workshop,           //old values
                                                                                                       'form_name'      => $form->getName(),    //new values
                                                                                                       'form'           => $form->createView(),
                                                                                                       'action'         => $action));
            }elseif($action == 'activate' || $action == 'deactivate'){
                $workshopOrder = $this->workshop_to_workshopOrder($workshop);
                $workshopOrder->setAction($action);
                $this->saveWorkshopOrder($em, $workshopOrder);
                return $this->redirect($this->generateUrl('user_index'));
            
                
            }elseif($action == 'canceled'){
                $workshopOrder = $em->getRepository("WorkshopBundle:WorkshopOrder")->findOneBy(array('id_workshop' => $id));
                $em->remove($workshopOrder);
                $em->flush();
                return $this->redirect($this->generateUrl('user_index'));
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
        $request = $this->getRequest();
        $workshopOrder = $em->getRepository("WorkshopBundle:WorkshopOrder")->find($id);
        $workshop = $em->getRepository("WorkshopBundle:Workshop")->find($workshopOrder->getIdWorkshop());
        if (!$workshop)
            throw $this->createNotFoundException('Workshop no encontrado en la BBDD');
        
        $user = $this->get('security.context')->getToken()->getUser();
        $role = $user->getRoles();
        if ($role[0]->getRole() == "ROLE_ADMIN"){
            if ($status == 'accepted'){
                if($workshopOrder->getAction() == "activate"){                                                          //queremos activar y nos lo aceptan
                    $workshop->setActive(true);                             
                }elseif ($workshopOrder->getAction() == "deactivate"){                                                  //queremos deasctivarlo y nos lo aceptan
                    $workshop->setActive(false);                            
                }elseif (($workshopOrder->getAction() == "modify") || ($workshopOrder->getAction() == "re_modify")){    //se tarata de una modificación y nos la aceptan
                    
                    //pasamos del workshopOrder al workshop
                    $workshop = $this->workshopOrder_to_workshop($workshop, $workshopOrder);
                    
                    
                }
                
                //guardamos el nuevo estado del workshop
                $this->saveWorkshopOrder($em, $workshop);                                                               //seria saveWorkshop, pero para el caso nos da igual
                
                //eliminamos la workshopOrder
                $em->remove($workshopOrder);
                $em->flush();
                
                
                }elseif ($status == 'rejected'){    //tenemos que indicar el motivo de rechazo
                    
                    $form = $this->createForm(new WorkshopRejectedReasonType(), $workshopOrder);
                    $form->bindRequest($request);
                    return $this->render('WorkshopBundle:WorkshopOrders:rejectedWorkshopOrder.html.twig', array('workshopOrder' => $workshopOrder,
                                                                                                                'form_name'     => $form->getName(),
                                                                                                                'form'          => $form->createView()
                                                                                                                ));
                

                //la petición ha sido rechazada, no hacemos nada y eliminamos el workshopOrder
//                $em->remove($workshopOrder);
//                $em->flush();
                
            }            

            
            return $this->redirect($this->generateUrl('workshopOrder_list'));
//            $request = $this->getRequest();
//            return $this->redirect($request->headers->get('referer'));
            
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
    
    private function workshopOrder_to_workshop($workshop, $workshopOrder){
        
//        $workshop->setIdWorkshop($workshopOrder->getId());
        $workshop->setName($workshopOrder->getName());
        $workshop->setCif($workshopOrder->getCif());
        $workshop->setNumAdClient($workshopOrder->getNumAdClient());
        $workshop->setAddress($workshopOrder->getAddress());
        $workshop->setCity($workshopOrder->getCity());
        $workshop->setRegion($workshopOrder->getRegion());
        $workshop->setProvince($workshopOrder->getProvince());
        $workshop->setPhoneNumber1($workshopOrder->getPhoneNumber1());
        $workshop->setPhoneNumber2($workshopOrder->getPhoneNumber2());
        $workshop->setMovilePhone1($workshopOrder->getMovilePhone1());
        $workshop->setMovilePhone2($workshopOrder->getMovilePhone2());
        $workshop->setFax($workshopOrder->getFax());
        $workshop->setEmail1($workshopOrder->getEmail1());
        $workshop->setEmail2($workshopOrder->getEmail2());
        $workshop->setContact($workshopOrder->getContact());
        $workshop->setPartner($workshopOrder->getPartner());
        $workshop->setTypology($workshopOrder->getTypology());
        
        return $workshop;
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
