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
use Adservice\UserBundle\Entity\User;
use Adservice\UtilBundle\Entity\Mailer;
use Adservice\UtilBundle\Controller\DefaultController as UtilController;

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

                /* MAILING */
                $mailer = $this->get('cms.mailer');
                $mailer->setTo($workshopOrder->getCreatedBy()->getEmail1());
                $mailer->setSubject($this->get('translator')->trans('mail.newOrder.subject').$workshopOrder->getId());
                $mailer->setFrom('noreply@grupeina.com');
                $mailer->setBody($this->renderView('UtilBundle:Mailing:order_new_mail.html.twig', array('workshopOrder' => $workshopOrder)));
                $mailer->sendMailToSpool();
                //echo $this->renderView('UtilBundle:Mailing:order_new_mail.html.twig', array('workshopOrder' => $workshopOrder));die;

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

                /* MAILING */
                $mailer = $this->get('cms.mailer');
                $mailer->setTo($workshopOrder->getCreatedBy()->getEmail1());
                $mailer->setSubject($this->get('translator')->trans('mail.editOrder.subject').$workshopOrder->getId());
                $mailer->setFrom('noreply@grupeina.com');
                $mailer->setBody($this->renderView('UtilBundle:Mailing:order_edit_mail.html.twig', array('workshopOrder' => $workshopOrder,
                                                                                                         'workshop'      => $workshop )));
                $mailer->sendMailToSpool();
                // echo $this->renderView('UtilBundle:Mailing:order_edit_mail.html.twig', array('workshopOrder' => $workshopOrder,
                //                                                                              'workshop'      => $workshop));die;

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

        //si veneimos de un estado "rejected" y queremos volver a activar/desactivar tenemos que eliminar la workshopOrder antigua
        //antes de crear la nueva (asi evitamos tener workshopsOrders duplicados)
        $workshopOrder = $em->getRepository("WorkshopBundle:WorkshopOrder")->findOneBy(array('id_workshop' => $id));
        if ($workshopOrder && $workshopOrder->getAction() == 'rejected'){
            $em->remove($workshopOrder);
            $em->flush();
        }

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

        /* MAILING */
        $mailer = $this->get('cms.mailer');
        $mailer->setTo($workshopOrder->getCreatedBy()->getEmail1());
        $mailer->setSubject($this->get('translator')->trans('mail.changeOrder.subject').$workshopOrder->getId());
        $mailer->setFrom('noreply@grupeina.com');
        $mailer->setBody($this->renderView('UtilBundle:Mailing:order_change_mail.html.twig', array('workshopOrder' => $workshopOrder)));
        $mailer->sendMailToSpool();
        //echo $this->renderView('UtilBundle:Mailing:order_change_mail.html.twig', array('workshopOrder' => $workshopOrder));die;

        return $this->redirect($this->generateUrl('user_index'));

    }


    public function resendOrderAction($id){

        if ($this->get('security.context')->isGranted('ROLE_AD') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();

        //si veneimos de un estado "rejected" y queremos volver a solicitar tenemos que eliminar la workshopOrder antigua
        //antes de crear la nueva (asi evitamos tener workshopsOrders duplicados)
        $workshopOrder = $em->getRepository("WorkshopBundle:WorkshopOrder")->find($id);
        if ($workshopOrder && $workshopOrder->getAction() == 'rejected'){
            $workshopOrder->setAction($workshopOrder->getWantedAction());
            $em->persist($workshopOrder);

            $action = $workshopOrder->getWantedAction();

            /* MAILING */
            $mailer = $this->get('cms.mailer');
            $mailer->setTo($workshopOrder->getCreatedBy()->getEmail1());
            $mailer->setSubject($this->get('translator')->trans('mail.resendOrder.subject').$workshopOrder->getId());
            $mailer->setFrom('noreply@grupeina.com');
            $mailer->setBody($this->renderView('UtilBundle:Mailing:order_resend_mail.html.twig', array('workshopOrder' => $workshopOrder,
                                                                                                       'action'        => $action)));
            $mailer->sendMailToSpool();
            // echo $this->renderView('UtilBundle:Mailing:order_resend_mail.html.twig', array('workshopOrder' => $workshopOrder,
            //                                                                                'action'   => $action));die;

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

        $workshopOrder = $em->getRepository("WorkshopBundle:WorkshopOrder")->find($id);
        if (!$workshopOrder)
            throw $this->createNotFoundException('Orden de Taller no encontrado en la BBDD: (id:'.$id.')');

        $action = $workshopOrder->getWantedAction();

        /* MAILING */
        $mailer = $this->get('cms.mailer');
        $mailer->setTo($workshopOrder->getCreatedBy()->getEmail1());
        $mailer->setSubject($this->get('translator')->trans('mail.removeOrder.subject').$workshopOrder->getId());
        $mailer->setFrom('noreply@grupeina.com');
        $mailer->setBody($this->renderView('UtilBundle:Mailing:order_remove_mail.html.twig', array('workshopOrder' => $workshopOrder,
                                                                                                   'action'        => $action)));
        $mailer->sendMailToSpool();
        // echo $this->renderView('UtilBundle:Mailing:order_remove_mail.html.twig', array('workshopOrder' => $workshopOrder,
        //                                                                                'action'   => $action));die;

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
     * @param integer $id del workshopOrder
     * @param string $status (accepted)
     * @return type
     * @throws AccessDeniedException
     */
    public function doActionWorkshopAction($id, $status){

        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();

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
            $action = $workshopOrder->getWantedAction();
            $em->remove($workshopOrder);
            DefaultC::newEntity($workshop, $user);
            DefaultC::saveEntity($em, $workshop, $user);

        }elseif (( $workshopOrder->getWantedAction() == 'deactivate') && $status == 'accepted'){
            $workshop = $em->getRepository('WorkshopBundle:Workshop')->findOneBy(array('id' => $workshopOrder->getIdWorkshop()));
            $workshop = $this->workshopOrder_to_workshop($workshop, $workshopOrder);
            $workshop->setActive(false);
            $action = $workshopOrder->getWantedAction();
            $em->remove($workshopOrder);
            DefaultC::saveEntity($em, $workshop, $user);

        }elseif (($workshopOrder->getWantedAction() == 'modify')  && $status == 'accepted'){
            $workshop = $em->getRepository('WorkshopBundle:Workshop')->findOneBy(array('id' => $workshopOrder->getIdWorkshop()));
            $workshop = $this->workshopOrder_to_workshop($workshop, $workshopOrder);
            $action = $workshopOrder->getWantedAction();
            $em->remove($workshopOrder);
            DefaultC::saveEntity($em, $workshop, $user);

        }elseif (($workshopOrder->getWantedAction() == 'create')  && $status == 'accepted'){
            $workshop = $this->workshopOrder_to_workshop(new Workshop(), $workshopOrder);

            if ($workshopOrder->getTest() != null) {
                $workshop->setEndTestAt(new \DateTime(\date('Y-m-d H:i:s',strtotime("+1 month"))));
            }
            $action = $workshopOrder->getWantedAction();
            $em->remove($workshopOrder);
            DefaultC::newEntity($workshop, $user);
            DefaultC::saveEntity($em, $workshop, $user);

            /*CREAR USERNAME Y EVITAR REPETICIONES*/
            $username = UtilController::getUsernameUnused($em, $workshop->getName());

            /*CREAR PASSWORD AUTOMATICAMENTE*/
            $pass = substr( md5(microtime()), 1, 8);

            $role = $em->getRepository('UserBundle:Role')->findOneByName('ROLE_USER');
            $lang = $em->getRepository('UtilBundle:Language')->findOneByLanguage($workshop->getCountry()->getLang());

            $newUser = DefaultC::newEntity(new User(), $user);
            $newUser->setUsername      ($username);
            $newUser->setPassword      ($pass);
            $newUser->setName          ($workshop->getContactName());
            $newUser->setSurname       ($workshop->getContactSurname());
            $newUser->setPhoneNumber1  ($workshop->getPhoneNumber1());
            $newUser->setPhoneNumber2  ($workshop->getPhoneNumber2());
            $newUser->setMovileNumber1 ($workshop->getMovileNumber1());
            $newUser->setMovileNumber2 ($workshop->getMovileNumber2());
            $newUser->setFax           ($workshop->getFax());
            $newUser->setEmail1        ($workshop->getEmail1());
            $newUser->setEmail2        ($workshop->getEmail2());
            $newUser->setActive        ('1');
            $newUser->setCountry       ($workshop->getCountry());
            $newUser->setRegion        ($workshop->getRegion());
            $newUser->setProvince      ($workshop->getProvince());
            $newUser->setCreatedBy     ($workshop->getCreatedBy());
            $newUser->setCreatedAt     (new \DateTime());
            $newUser->setModifiedBy    ($workshop->getCreatedBy());
            $newUser->setModifiedAt    (new \DateTime());
            $newUser->setLanguage      ($lang);
            $newUser->setWorkshop      ($workshop);
            $newUser->addRole          ($role);

            //password nuevo, se codifica con el nuevo salt
            $encoder = $this->container->get('security.encoder_factory')->getEncoder($newUser);
            $salt = md5(time());
            $password = $encoder->encodePassword($newUser->getPassword(), $salt);
            $newUser->setPassword($password);
            $newUser->setSalt($salt);
            DefaultC::saveEntity($em, $newUser, $user);

            /* MAILING */
            $mailerUser = $this->get('cms.mailer');
            $mailerUser->setTo($newUser->getEmail1());
            $mailerUser->setSubject($this->get('translator')->trans('mail.newUser.subject').$newUser->getWorkshop());
            $mailerUser->setFrom('noreply@grupeina.com');
            $mailerUser->setBody($this->renderView('UtilBundle:Mailing:user_new_mail.html.twig', array('user' => $newUser, 'password' => $pass)));
            $mailerUser->sendMailToSpool();
            // echo $this->renderView('UtilBundle:Mailing:user_new_mail.html.twig', array('user' => $newUser, 'password' => $pass));die;

        }

        /* MAILING */
        $mailer = $this->get('cms.mailer');
        $mailer->setTo($workshop->getCreatedBy()->getEmail1());
        $mailer->setSubject($this->get('translator')->trans('mail.acceptOrder.subject').$workshop->getId());
        $mailer->setFrom('noreply@grupeina.com');
        $mailer->setBody($this->renderView('UtilBundle:Mailing:order_accept_mail.html.twig', array('workshop' => $workshop,
                                                                                                   'action'   => $action)));
        $mailer->sendMailToSpool();
        // echo $this->renderView('UtilBundle:Mailing:order_accept_mail.html.twig', array('workshop' => $workshop,
        //                                                                                'action'   => $action));die;

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

                /* MAILING */
                $mailer = $this->get('cms.mailer');
                $mailer->setTo($workshopOrder->getCreatedBy()->getEmail1());
                $mailer->setSubject($this->get('translator')->trans('mail.rejectOrder.subject').$workshopOrder->getId());
                $mailer->setFrom('noreply@grupeina.com');
                $mailer->setBody($this->renderView('UtilBundle:Mailing:order_reject_mail.html.twig', array('workshopOrder' => $workshopOrder)));
                $mailer->sendMailToSpool();
                //echo $this->renderView('UtilBundle:Mailing:order_reject_mail.html.twig', array('workshopOrder' => $workshopOrder));die;

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

        $workshopOrder->setIdWorkshop    ($workshop->getId());
        $workshopOrder->setName          ($workshop->getName());
        $workshopOrder->setCif           ($workshop->getCif());
        $workshopOrder->setNumAdClient   ($workshop->getNumAdClient());
        $workshopOrder->setAddress       ($workshop->getAddress());
        $workshopOrder->setCity          ($workshop->getCity());
        $workshopOrder->setRegion        ($workshop->getRegion());
        $workshopOrder->setProvince      ($workshop->getProvince());
        $workshopOrder->setCountry       ($workshop->getCountry());
        $workshopOrder->setPhoneNumber1  ($workshop->getPhoneNumber1());
        $workshopOrder->setPhoneNumber2  ($workshop->getPhoneNumber2());
        $workshopOrder->setMovileNumber1  ($workshop->getMovileNumber1());
        $workshopOrder->setMovileNumber2  ($workshop->getMovileNumber2());
        $workshopOrder->setFax           ($workshop->getFax());
        $workshopOrder->setEmail1        ($workshop->getEmail1());
        $workshopOrder->setEmail2        ($workshop->getEmail2());
        $workshopOrder->setContactName   ($workshop->getContactName());
        $workshopOrder->setContactSurname($workshop->getContactSurname());
        $workshopOrder->setPartner       ($workshop->getPartner());
        $workshopOrder->setTypology      ($workshop->getTypology());
        $workshopOrder->setTest          ($workshopOrder->getTest());
        $workshopOrder->setCreatedBy     ($workshop->getCreatedBy());

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

        $workshop->setName               ($workshopOrder->getName());
        $workshop->setCif                ($workshopOrder->getCif());
        $workshop->setNumAdClient        ($workshopOrder->getNumAdClient());
        $workshop->setAddress            ($workshopOrder->getAddress());
        $workshop->setCity               ($workshopOrder->getCity());
        $workshop->setRegion             ($workshopOrder->getRegion());
        $workshop->setProvince           ($workshopOrder->getProvince());
        $workshop->setCountry            ($workshopOrder->getCountry());
        $workshop->setPhoneNumber1       ($workshopOrder->getPhoneNumber1());
        $workshop->setPhoneNumber2       ($workshopOrder->getPhoneNumber2());
        $workshop->setMovileNumber1       ($workshopOrder->getMovileNumber1());
        $workshop->setMovileNumber2       ($workshopOrder->getMovileNumber2());
        $workshop->setFax                ($workshopOrder->getFax());
        $workshop->setEmail1             ($workshopOrder->getEmail1());
        $workshop->setEmail2             ($workshopOrder->getEmail2());
        $workshop->setContactName        ($workshopOrder->getContactName());
        $workshop->setContactSurname     ($workshopOrder->getContactSurname());
        $workshop->setPartner            ($workshopOrder->getPartner());
        $workshop->setTypology           ($workshopOrder->getTypology());
        $workshop->setTest               ($workshopOrder->getTest());
        $workshop->setActive             (true);
        $workshop->setCreatedBy          ($workshopOrder->getCreatedBy());

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

        foreach ($workshopsOrders as $workshopOrder) {

            if ($workshopOrder->getAction() == 'modify' or $workshopOrder->getAction() == 're_modify') {

                $workshopBefore = $em->getRepository("WorkshopBundle:Workshop")->findOneBy(array('id' => $workshopOrder->getIdWorkshop()));
                $ordersBefore[$workshopOrder->getId()] = $workshopBefore;
            }

            if ($workshopOrder->getAction() == 'rejected' or $workshopOrder->getAction() == 'resend') {

                $workshopBefore = $em->getRepository("WorkshopBundle:WorkshopOrder")->find($workshopOrder->getId());
                $ordersBefore[$workshopOrder->getId()] = $workshopBefore;
            }
        }
        return $ordersBefore;
    }
}
