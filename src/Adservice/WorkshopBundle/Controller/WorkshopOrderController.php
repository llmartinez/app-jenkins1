<?php

namespace Adservice\WorkshopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Adservice\TicketBundle\Controller\DefaultController as DefaultC;
use Adservice\WorkshopBundle\Entity\Workshop;
use Adservice\WorkshopBundle\Entity\WorkshopOrder;
use Adservice\WorkshopBundle\Form\WorkshopOrderType;
use Adservice\WorkshopBundle\Form\WorkshopOrderCreateType;
use Adservice\WorkshopBundle\Form\WorkshopOrderModifyType;
use Adservice\WorkshopBundle\Form\WorkshopRejectedReasonType;

class WorkshopOrderController extends Controller {

    /**
     * Lista todos los talleres, si somos un usuario con rol "ad" solo se mostraran los talleres que tenga relacionado...
     * @return type
     * @throws AccessDeniedException
     */
    public function listWorkshopsAction(){
        if ($this->get('security.context')->isGranted('ROLE_AD') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();
        $user = $this->get('security.context')->getToken()->getUser();

        $workshops = $em->getRepository('WorkshopBundle:Workshop')->findBy(array('partner' => $user->getPartner()->getId()));

        return $this->render('WorkshopBundle:WorkshopOrders:listWorkshops.html.twig', array('workshops' => $workshops));

    }

    /**
     * Crea una solicitud (workshopOrder) del tipo "create", por defecto el taller que se creara estara inactivo...
     * @return type
     * @throws AccessDeniedException.
     */
    public function newCreateWorkshopOrderAction(){

        if ($this->get('security.context')->isGranted('ROLE_AD') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();

        $workshopOrder = new WorkshopOrder();
        $request = $this->getRequest();
        $form = $this->createForm(new WorkshopOrderCreateType(), $workshopOrder);

        if ($request->getMethod() == 'POST') {

            $form->bindRequest($request);

            if ($form->isValid()) {

                $user = $this->get('security.context')->getToken()->getUser();

                $workshopOrder = DefaultC::newEntity($workshopOrder, $user);
                $workshopOrder->setPartner($user->getPartner());
                $workshopOrder->setActive(false);
                $workshopOrder->setAction('create');
                $workshopOrder->setWantedAction('create');
                DefaultC::saveEntity($em, $workshopOrder, $user);

                return $this->redirect($this->generateUrl('user_index'));
            }
        }

        return $this->render('WorkshopBundle:WorkshopOrders:createNewWorkshopOrder.html.twig', array('workshopOrder'    => $workshopOrder,
                                                                                                     'form_name'        => $form->getName(),
                                                                                                     'form'             => $form->createView()));

    }

    /**
     * Crea una solicitud (workshopOrder) del tipo "modify"
     * @param integer $id del workshop que queremos modificar
     * @return type
     * @throws AccessDeniedException
     * @throws type
     */
    public function newModifyWorkshopOrderAction($id) {
        if ($this->get('security.context')->isGranted('ROLE_AD') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();


        //miramos si es una "re-modificacion" (una modificacion ha sido rechazada y la volvemos a modificar para volver a enviar)
        $workshopOrder = $em->getRepository("WorkshopBundle:WorkshopOrder")->findOneBy(array('id'     => $id,
                                                                                             'action' => 'rejected'));
        if ($workshopOrder) $workshop = $em->getRepository("WorkshopBundle:Workshop")->find($workshopOrder->getIdWorkshop());
        else {
            $workshop = $em->getRepository("WorkshopBundle:Workshop")->find($id);
            if (!$workshop)
                throw $this->createNotFoundException('Taller no encontrado en la BBDD');

            //si no existe una workshopOrder previa la creamos por primera vez a partir del workshop original
             $workshopOrder = $this->workshop_to_workshopOrder($workshop);
        }

        $form = $this->createForm(new WorkshopOrderModifyType(), $workshopOrder);

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {

                $user = $this->get('security.context')->getToken()->getUser();

                $workshopOrder = DefaultC::newEntity($workshopOrder, $user);

                if ($workshopOrder->getAction() == 'rejected' && $workshopOrder->getWantedAction() == 'modify') {
                    $workshopOrder->setAction('re_modify');
                }
                elseif ($workshopOrder->getAction() == 'rejected') {
                    $workshopOrder->setAction('re_modify');
                }else{
                    $workshopOrder->setAction('modify');
                    $workshopOrder->setWantedAction('modify');
                }
                DefaultC::saveEntity($em, $workshopOrder, $user);

                return $this->redirect($this->generateUrl('workshopOrder_listWorkshopOrders'));

            }
        }
        return $this->render('WorkshopBundle:WorkshopOrders:createModifyWorkshop.html.twig', array('workshopOrder' => $workshopOrder,
                                                                                                   'workshop'      => $workshop,                //old values
                                                                                                   'form_name'     => $form->getName(),         //new values
                                                                                                   'form'          => $form->createView()));

    }
    
    
    /**
     * Crea una solicitud (workshopOrder) del tipo "activate" o "deactivate" segun el $status
     * @param integer $id del workshop que queremos modificar
     * @param string $status (active | inactive)
     * @return type
     * @throws AccessDeniedException
     * @throws type
     */
    public function newChangeStatusOrderAction($id, $status){

        if ($this->get('security.context')->isGranted('ROLE_AD') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();
        $workshop = $em->getRepository("WorkshopBundle:Workshop")->find($id);
        if (!$workshop)
            throw $this->createNotFoundException('Taller no encontrado en la BBDD');


        /* REPASAR ESTO */

                //si veneimos de un estado "rejected" y queremos volver a activar/desactivar tenemos que eliminar la workshopOrder antigua
                //antes de crear la nueva (asi evitamos tener workshopsOrders duplicados)
                $workshopOrder = $em->getRepository("WorkshopBundle:WorkshopOrder")->findOneBy(array('id_workshop' => $id));
                if ($workshopOrder && $workshopOrder->getAction() == 'rejected'){
                    $em->remove($workshopOrder);
                    $em->flush();
                }
        /**/
        $user = $this->get('security.context')->getToken()->getUser();
        $workshopOrder = $this->workshop_to_workshopOrder($workshop);
        $workshopOrder = DefaultC::newEntity($workshopOrder, $user);

        //actualizamos el campo "action" de la orden segun queramos activar o desactivar
        if ($status == 'active'){
            $workshopOrder->setAction('activate');
            $workshopOrder->setWantedAction('activate');

        }elseif ($status == 'inactive'){
            $workshopOrder->setAction('deactivate');
            $workshopOrder->setWantedAction('deactivate');
        }

        DefaultC::saveEntity($em, $workshopOrder, $user);

        return $this->redirect($this->generateUrl('user_index'));

    }


    public function resendOrderAction($id, $status){

        if ($this->get('security.context')->isGranted('ROLE_AD') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();

        //si veneimos de un estado "rejected" y queremos volver a solicitar tenemos que eliminar la workshopOrder antigua
        //antes de crear la nueva (asi evitamos tener workshopsOrders duplicados)
        $workshopOrder = $em->getRepository("WorkshopBundle:WorkshopOrder")->find($id);
        if ($workshopOrder && $workshopOrder->getAction() == 'rejected'){
            $workshopOrder->setAction($workshopOrder->getWantedAction());
            $em->persist($workshopOrder);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('user_index'));
    }

    /**
     * Lista todas las workshopsOrders
     * @return type
     * @throws AccessDeniedException
     */
    public function listWorkshopOrdersAction(){

        if ($this->get('security.context')->isGranted('ROLE_AD') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $role = $user->getRoles();

        //segun el rol puede ver sus talleres o todos los que haya
        if ($role[0]->getRole() == "ROLE_AD"){
            $workshopsOrders = $em->getRepository("WorkshopBundle:WorkshopOrder")->findBy(array('partner' => $user->getPartner()->getId()));
            $workshopsRejectedOrders = $em->getRepository("WorkshopBundle:WorkshopOrder")->findBy(array('partner'   => $user->getPartner()->getId(),
                                                                                                        'action'    => 'rejected'));
        }elseif ($role[0]->getRole() == "ROLE_ADMIN"){
            $workshopsOrders = $em->getRepository("WorkshopBundle:WorkshopOrder")->findAll();
            $workshopsRejectedOrders = $em->getRepository("WorkshopBundle:WorkshopOrder")->findByAction('rejected');
        }

        //eliminamos de la lista de "todos" los que ya tenemos en la lista de rechazados

        foreach ($workshopsOrders as $key => $workshopOrder) {

            if (in_array($workshopOrder, $workshopsRejectedOrders)){
                unset($workshopsOrders[$key]);
            }
        }

        //casos que todas esten en rechazadas... (el "unset" de todos los elementos elimina el array....)
        if (count($workshopsOrders) <= 0 )  $workshopsOrders = array();

        //creamos arrays de los valores anteriores a la modificacion/rechazo de la solicitud
        $ordersBefore = $this->getOrdersBefore($workshopsOrders);

        return $this->render('WorkshopBundle:WorkshopOrders:listWorkshopOrder.html.twig', array('workshopsOrders'           => $workshopsOrders,
                                                                                                'workshopsRejectedOrders'   => $workshopsRejectedOrders,
                                                                                                'ordersBefore'              => $ordersBefore,
                                                                                                'user'                      => $user));
    }

    /**
     * Elimina una workshopOrder segun el $id
     * @param integer $id del workshopOrder que queremos eliminar
     * @return type
     * @throws AccessDeniedException
     * @throws type
     */
    public function removeWorkshopOrderAction($id){

        if ($this->get('security.context')->isGranted('ROLE_AD') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();

        $workshopOrder = $em->getRepository("WorkshopBundle:WorkshopOrder")->find($id);
        if (!$workshopOrder)
            throw $this->createNotFoundException('Orden de Taller no encontrado en la BBDD: (id:'.$id.')');

        $em->remove($workshopOrder);
        $em->flush();

        $user = $this->get('security.context')->getToken()->getUser();
        $workshopsOrders = $em->getRepository("WorkshopBundle:WorkshopOrder")->findBy(array('partner' => $user->getPartner()->getId()));

        $ordersBefore = $this->getOrdersBefore($workshopsOrders);

        return $this->render('WorkshopBundle:WorkshopOrders:listWorkshopOrder.html.twig', array('workshopsOrders' => $workshopsOrders,
                                                                                                'ordersBefore'    => $ordersBefore,
                                                                                                'user'            => $user));

    }
    
    /**
     * 
     * @param integer $id del workshopOrder
     * @param string $status (accepted)
     * @return type
     * @throws AccessDeniedException
     */
    public function doActionWorkshopAction($id, $status){

        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();

        $workshopOrder = $em->getRepository('WorkshopBundle:WorkshopOrder')->find($id);

        // activate   + accepted = setActive a TRUE  and delete workshopOrder
        // deactivate + accepted = setActive a FALSE and delete workshopOrder
        // modify     + accepted = se hacen los cambios en workshop and delete del workshopOrder
        // create     + accepted = new workshop and delete workshopOrder

        $user = $this->get('security.context')->getToken()->getUser();

        if (( $workshopOrder->getWantedAction() == 'activate') && $status == 'accepted'){
            $workshop = $em->getRepository('WorkshopBundle:Workshop')->findOneBy(array('id' => $workshopOrder->getIdWorkshop()));
            $workshop = $this->workshopOrder_to_workshop($workshop, $workshopOrder);
            $workshop->setActive(true);
            $em->remove($workshopOrder);
            DefaultC::newEntity($workshop, $user);
            DefaultC::saveEntity($em, $workshop, $user);

        }elseif (( $workshopOrder->getWantedAction() == 'deactivate') && $status == 'accepted'){
            $workshop = $em->getRepository('WorkshopBundle:Workshop')->findOneBy(array('id' => $workshopOrder->getIdWorkshop()));
            $workshop = $this->workshopOrder_to_workshop($workshop, $workshopOrder);
            $workshop->setActive(false);
            $em->remove($workshopOrder);
            DefaultC::saveEntity($em, $workshop, $user);

        }elseif (($workshopOrder->getWantedAction() == 'modify')  && $status == 'accepted'){
            $workshop = $em->getRepository('WorkshopBundle:Workshop')->findOneBy(array('id' => $workshopOrder->getIdWorkshop()));
            $workshop = $this->workshopOrder_to_workshop($workshop, $workshopOrder);
            $em->remove($workshopOrder);
            DefaultC::saveEntity($em, $workshop, $user);

        }elseif (($workshopOrder->getWantedAction() == 'create')  && $status == 'accepted'){
            $workshop = $this->workshopOrder_to_workshop(new Workshop(), $workshopOrder);

            if ($workshopOrder->getTest() != null) {
                $workshop->setEndTestAt(new \DateTime(\date('Y-m-d H:i:s',strtotime("+1 month"))));
            }
            $em->remove($workshopOrder);
            DefaultC::newEntity($workshop, $user);
            DefaultC::saveEntity($em, $workshop, $user);
        }

        $user = $this->get('security.context')->getToken()->getUser();
        $workshopsOrders = $em->getRepository("WorkshopBundle:WorkshopOrder")->findAll();
        $ordersBefore = $this->getOrdersBefore($workshopsOrders);

        return $this->render('WorkshopBundle:WorkshopOrders:listWorkshopOrder.html.twig', array('workshopsOrders'   => $workshopsOrders,
                                                                                                'ordersBefore'      => $ordersBefore,
                                                                                                'user'              => $user));

    }
    
    /**
     * Actualiza el campo "rejection_reason" de la workshopOrder i pone su estada en "rejected"
     * @param type $id
     * @return type
     * @throws AccessDeniedException
     * @throws type
     */
    public function setReasonRejectionOrderAction($id){
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();

        $workshopOrder = $em->getRepository("WorkshopBundle:WorkshopOrder")->find($id);
        if (!$workshopOrder)
            throw $this->createNotFoundException('Orden de Taller no encontrado en la BBDD: (id:'.$id.')');

        $form = $this->createForm(new WorkshopRejectedReasonType(), $workshopOrder);

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $workshopOrder->setAction('rejected');
                $workshopOrder->setRejectionReason($form->get('rejection_reason')->getData());     //recogemos del formulario el motivo de rechazo...
                $em->persist($workshopOrder);
                $em->flush();

                $user = $this->get('security.context')->getToken()->getUser();
                $workshopsOrders = $em->getRepository("WorkshopBundle:WorkshopOrder")->findAll();
                $workshopsRejectedOrders = $em->getRepository("WorkshopBundle:WorkshopOrder")->findByAction('rejected');

                //eliminamos de la lista de "todos" los que ya tenemos en la lista de rechazados
                foreach ($workshopsOrders as $key => $workshopOrder) {
                    if (in_array($workshopOrder, $workshopsRejectedOrders)){
                        unset($workshopsOrders[$key]);
                    }
                }

                //casos que todas esten en rechazadas... (el "unset" de todos los elementos elimina el array....)
                if (count($workshopsOrders) <= 0 )  $workshopsOrders = array();

                $ordersBefore = $this->getOrdersBefore($workshopsOrders);

                return $this->render('WorkshopBundle:WorkshopOrders:listWorkshopOrder.html.twig', array('workshopsOrders'           => $workshopsOrders,
                                                                                                        'workshopsRejectedOrders'   => $workshopsRejectedOrders,
                                                                                                        'ordersBefore'              => $ordersBefore,
                                                                                                        'user'                      => $user));
            }

        }

        return $this->render('WorkshopBundle:WorkshopOrders:rejectedWorkshopOrder.html.twig', array('workshopOrder' => $workshopOrder,
                                                                                                    'form_name'     => $form->getName(),
                                                                                                    'form'          => $form->createView()));
    }
            
    /**
     * Hace el mapeo entre workshop y workshopOrder
     * @param Workshop $workshop
     * @return \Adservice\WorkshopBundle\Entity\WorkshopOrder
     */
    private function workshop_to_workshopOrder($workshop) {

        $workshopOrder = new WorkshopOrder();

        $workshopOrder->setIdWorkshop   ($workshop->getId());
        $workshopOrder->setName         ($workshop->getName());
        $workshopOrder->setCif          ($workshop->getCif());
        $workshopOrder->setNumAdClient  ($workshop->getNumAdClient());
        $workshopOrder->setAddress      ($workshop->getAddress());
        $workshopOrder->setCity         ($workshop->getCity());
        $workshopOrder->setRegion       ($workshop->getRegion());
        $workshopOrder->setProvince     ($workshop->getProvince());
        $workshopOrder->setPhoneNumber1 ($workshop->getPhoneNumber1());
        $workshopOrder->setPhoneNumber2 ($workshop->getPhoneNumber2());
        $workshopOrder->setMovilePhone1 ($workshop->getMovilePhone1());
        $workshopOrder->setMovilePhone2 ($workshop->getMovilePhone2());
        $workshopOrder->setFax          ($workshop->getFax());
        $workshopOrder->setEmail1       ($workshop->getEmail1());
        $workshopOrder->setEmail2       ($workshop->getEmail2());
        $workshopOrder->setContact      ($workshop->getContact());
        $workshopOrder->setPartner      ($workshop->getPartner());
        $workshopOrder->setTypology     ($workshop->getTypology());
        $workshopOrder->setTest         ($workshopOrder->getTest());

        $workshopOrder->setCreatedAt(new \DateTime(\date("Y-m-d H:i:s")));
        $workshopOrder->setActive(false);

        return $workshopOrder;
    }
    
    /**
     * Hace el mapeo entre workshopOrder y workshop
     * @param Workshop $workshop
     * @param type $workshopOrder
     * @return \Adservice\WorkshopBundle\Entity\WorkshopOrder
     */
    private function workshopOrder_to_workshop($workshop, $workshopOrder){

        $workshop->setName         ($workshopOrder->getName());
        $workshop->setCif          ($workshopOrder->getCif());
        $workshop->setNumAdClient  ($workshopOrder->getNumAdClient());
        $workshop->setAddress      ($workshopOrder->getAddress());
        $workshop->setCity         ($workshopOrder->getCity());
        $workshop->setRegion       ($workshopOrder->getRegion());
        $workshop->setProvince     ($workshopOrder->getProvince());
        $workshop->setPhoneNumber1 ($workshopOrder->getPhoneNumber1());
        $workshop->setPhoneNumber2 ($workshopOrder->getPhoneNumber2());
        $workshop->setMovilePhone1 ($workshopOrder->getMovilePhone1());
        $workshop->setMovilePhone2 ($workshopOrder->getMovilePhone2());
        $workshop->setFax          ($workshopOrder->getFax());
        $workshop->setEmail1       ($workshopOrder->getEmail1());
        $workshop->setEmail2       ($workshopOrder->getEmail2());
        $workshop->setContact      ($workshopOrder->getContact());
        $workshop->setPartner      ($workshopOrder->getPartner());
        $workshop->setTypology     ($workshopOrder->getTypology());
        $workshop->setTest         ($workshopOrder->getTest());
        $workshop->setActive       (true);

        return $workshop;
    }

        //
    /**
     * crea un array con los valores anteriores a la modificacion/rechazo de la solicitud
     * @param  Array $workshopsOrders
     * @return Array
     */
    private function getOrdersBefore($workshopsOrders) {

        $em = $this->getDoctrine()->getEntityManager();
        $ordersBefore = array();

        foreach ($workshopsOrders as $key => $workshopOrder) {

            if ($workshopOrder->getAction() == 'modify' or $workshopOrder->getAction() == 're_modify') {

                $workshopBefore = $em->getRepository("WorkshopBundle:Workshop")->findOneBy(array('id' => $workshopOrder->getIdWorkshop()));
                $ordersBefore[$workshopOrder->getId()] = $workshopBefore;
            }

            if ($workshopOrder->getAction() == 'rejected' or $workshopOrder->getAction() == 'resend') {

                $workshopBefore = $em->getRepository("WorkshopBundle:WorkshopOrder")->find($workshopOrder->getId());
                var_dump($workshopBefore);
                $ordersBefore[$workshopOrder->getId()] = $workshopBefore;
            }
        }
        return $ordersBefore;
    }
}
