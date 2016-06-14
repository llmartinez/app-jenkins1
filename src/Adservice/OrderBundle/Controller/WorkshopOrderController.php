<?php

namespace Adservice\OrderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Adservice\UtilBundle\Controller\UtilController as UtilController;
use Adservice\WorkshopBundle\Entity\ADSPlus;
use Adservice\WorkshopBundle\Entity\Workshop;
use Adservice\OrderBundle\Entity\WorkshopOrder;
use Adservice\OrderBundle\Form\WorkshopOrderType;
use Adservice\OrderBundle\Form\WorkshopNewOrderType;
use Adservice\OrderBundle\Form\WorkshopEditOrderType;
use Adservice\OrderBundle\Form\WorkshopRejectOrderType;
use Adservice\UserBundle\Entity\User;
use Adservice\UtilBundle\Entity\Mailer;
use Adservice\UtilBundle\Entity\Pagination;

class WorkshopOrderController extends Controller {

//  _     ___ ____ _____
// | |   |_ _/ ___|_   _|
// | |    | |\___ \ | |
// | |___ | | ___) || |
// |_____|___|____/ |_|
//

    /**
     * Devuelve todos los talleres segun el usuario logeado
     * @return type
     * @throws AccessDeniedException
     */
    public function listWorkshopsAction($page=1 , $w_idpartner='0', $w_id='0', $country='0', $partner='0', $status='0', $term='0', $field='0') {
        $security = $this->get('security.context');
        $user     = $security->getToken()->getUser();
        if ($security->isGranted('ROLE_AD') === false) {
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getEntityManager();
        $params   = array();
        if ($term != '0' and $field != '0'){

            if ($term == 'tel') {
                $params[] = array('phone_number_1', " != '0' AND (e.phone_number_1 LIKE '%".$field."%' OR e.phone_number_2 LIKE '%".$field."%' OR e.mobile_number_1 LIKE '%".$field."%' OR e.mobile_number_2 LIKE '%".$field."%') ");
            }
            elseif($term == 'mail'){
                $params[] = array('email_1', " != '0' AND (e.email_1 LIKE '%".$field."%' OR e.email_2 LIKE '%".$field."%') ");
            }
            elseif($term == 'name'){
                $params[] = array($term, " LIKE '%".$field."%'");
            }
            elseif($term == 'cif'){
                $params[] = array($term, " LIKE '%".$field."%'");
            }
        }

            if($security->isGranted('ROLE_SUPER_AD')) {
                if ($partner != '0') $params[] = array('partner', ' = '.$partner);
                $params[] = array('country', ' = '.$user->getCountry()->getId());
            }
            else { $params[] = array('partner', ' = '.$user->getPartner()->getId()); }

            if ($status != 'none') {
                if     ($status == 'active')   $params[] = array('active', ' = 1 AND e.test = 0');
                elseif ($status == 'deactive') $params[] = array('active', ' = 0');
                elseif ($status == 'test')     $params[] = array('active', ' = 1 AND e.test = 1');
            }
        //$params[] = array('country', ' = '.$security->getToken()->getUser()->getCountry()->getId());

        $pagination = new Pagination($page);

        $workshops = $pagination->getRows($em, 'WorkshopBundle', 'Workshop', $params, $pagination);

        $length = $pagination->getRowsLength($em, 'WorkshopBundle', 'Workshop', $params);

        $pagination->setTotalPagByLength($length);

        if($security->isGranted('ROLE_SUPER_AD')) $partners = $em->getRepository('PartnerBundle:Partner')->findBy(array('country' => $user->getCountry()->getId()),array('name'=>'ASC'));
        else $partners = array();

        $numTickets = 0;

        if($security->isGranted('ROLE_SUPER_AD')){
            if ($partner != '0')
                $numTickets = $em->getRepository("WorkshopBundle:Workshop")->getNumTicketsByPartnerCountry($partner);
            elseif ($security->isGranted('ROLE_SUPER_AD'))
                $numTickets = $em->getRepository("WorkshopBundle:Workshop")->getNumTicketsByPartnerCountry('', $user->getCountry()->getId());
        }
        elseif ($security->isGranted('ROLE_AD')){
            $numTickets = $em->getRepository("WorkshopBundle:Workshop")->getNumTicketsByPartnerId($user->getPartner()->getId());
        }

        return $this->render('OrderBundle:WorkshopOrders:list_workshops.html.twig', array( 'workshops'  => $workshops,
                                                                                           'numTickets' => $numTickets,
                                                                                           'pagination' => $pagination,
                                                                                           'partners'   => $partners,
                                                                                           'partner'    => $partner,
                                                                                           'term'       => $term,
                                                                                           'field'      => $field,
                                                                                           'status'     => $status,
                                                                                           'length'     => $length));
    }

//  _   _ _______        __
// | \ | | ____\ \      / /
// |  \| |  _|  \ \ /\ / /
// | |\  | |___  \ V  V /
// |_| \_|_____|  \_/\_/
//

    /**
     * Crea una solicitud (workshopOrder) del tipo "create", por defecto el taller que se creara estara inactivo...
     * @return type
     * @throws AccessDeniedException.
     */
    public function newAction(){
        $security = $this->get('security.context');
        if ($security->isGranted('ROLE_AD') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();

        $workshopOrder = new WorkshopOrder();
        if ($security->isGranted('ROLE_SUPER_AD')) {

            if($request->request->get('partner') != null)
                $id_partner = $request->request->get('partner');
            else
                $id_partner = '0';

            $partners   = $em->getRepository("PartnerBundle:Partner")->findBy(array('country' => $security->getToken()->getUser()->getCountry()->getId(),
                                                                                    'active' => '1'),array('name'=>'ASC'));
        }
        else { $id_partner = $security->getToken()->getUser()->getPartner()->getId();
               $partners   = '0';
        }

        $partner = $em->getRepository("PartnerBundle:Partner")->find($id_partner);
        if ($partner != null) $code = UtilController::getCodeWorkshopUnused($em, $partner); /*OBTIENE EL PRIMER CODIGO DISPONIBLE*/
        else                  $code = 0;

        $request = $this->getRequest();

        // Creamos variables de sesion para fitlrar los resultados del formulario
        if ($security->isGranted('ROLE_SUPER_AD')) {

            $partner_ids = '0';
            foreach ($partners as $p) { $partner_ids = $partner_ids.', '.$p->getId(); }

            $_SESSION['id_partner'] = ' IN ('.$partner_ids.')';
            $_SESSION['id_country'] = ' = '.$security->getToken()->getUser()->getCountry()->getId();

        }else {
            $_SESSION['id_partner'] = ' = '.$partner->getId();
            $_SESSION['id_country'] = ' = '.$partner->getCountry()->getId();
        }

        $form    = $this->createForm(new WorkshopNewOrderType(), $workshopOrder);

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            // $id_partner = $request->request->get('partner');
            // $partner    = $em->getRepository("PartnerBundle:Partner")->find($id_partner);

            $code = UtilController::getCodeWorkshopUnused($em, $partner);        /*OBTIENE EL PRIMER CODIGO DISPONIBLE*/

            if ($form->isValid()) {

                /*CHECK CODE WORKSHOP NO SE REPITA*/
                $find = $em->getRepository("WorkshopBundle:Workshop")->findOneBy(array('partner' => $partner->getId(),
                                                                                 'code_workshop' => $workshopOrder->getCodeWorkshop()));
                $findPhone = array(0,0,0,0);
                if($workshopOrder->getPhoneNumber1() !=null){
                    $findPhone[0] = $em->getRepository("WorkshopBundle:Workshop")->findPhone($workshopOrder->getPhoneNumber1());
                }
                if($workshopOrder->getPhoneNumber2() !=null){
                    $findPhone[1] = $em->getRepository("WorkshopBundle:Workshop")->findPhone($workshopOrder->getPhoneNumber2());
                }
                if($workshopOrder->getMobileNumber1() !=null){
                    $findPhone[2] = $em->getRepository("WorkshopBundle:Workshop")->findPhone($workshopOrder->getMobileNumber1());
                }
                if($workshopOrder->getMobileNumber2() !=null){
                    $findPhone[3] = $em->getRepository("WorkshopBundle:Workshop")->findPhone($workshopOrder->getMobileNumber2());
                }
                if($find == null and $findPhone[0]['1']<1 and $findPhone[1]['1']<1 and $findPhone[2]['1']<1 and $findPhone[3]['1']<1)
                {
                    $user = $security->getToken()->getUser();
                    $workshopOrder = UtilController::newEntity($workshopOrder, $user);
                    $workshopOrder->setPartner($partner);
                    $workshopOrder->setCodePartner($partner->getCodePartner());
                    $workshopOrder->setCountry($user->getCountry());
                    $roles=$user->getRoles();
		            $roles = $roles[0];
                    if($roles != 'ROLE_SUPER_AD') {
                        $workshopOrder->setPartner($user->getPartner());
                        $workshopOrder->setCountry($user->getPartner()->getCountry());
                        $workshopOrder->setRegion($user->getPartner()->getRegion());
                    }
                    $workshopOrder->setActive(false);
                    $workshopOrder->setAction('create');
                    $workshopOrder->setWantedAction('create');

                    // Set default shop to NULL
                    $shop = $form['shop']->getClientData();
                    if($shop == 0) { $workshopOrder->setShop(null); }

                    //CHECKS
                    // if($workshopOrder->getHasChecks() == false and $workshopOrder->getNumChecks() != null) $workshopOrder->setNumChecks(null);
                    // if($workshopOrder->getHasChecks() == true and $workshopOrder->getNumChecks() == '') $workshopOrder->setNumChecks(0);

                    UtilController::saveEntity($em, $workshopOrder, $user);

                    $mail = $workshopOrder->getCreatedBy()->getEmail1();
                    $pos = strpos($mail, '@');
                    if ($pos != 0) {

                        // Cambiamos el locale para enviar el mail en el idioma del taller
                        $locale = $request->getLocale();
                        $lang_w = $workshopOrder->getPartner()->getCountry()->getLang();
                        $lang   = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_w);
                        $request->setLocale($lang->getShortName());

                        /* MAILING */
                        $mailer = $this->get('cms.mailer');
                        $mailer->setTo($mail);
                        $mailer->setSubject($this->get('translator')->trans('mail.newOrder.subject').$workshopOrder->getId());
                        $mailer->setFrom('noreply@adserviceticketing.com');
                        $mailer->setBody($this->renderView('UtilBundle:Mailing:order_new_mail.html.twig', array('workshopOrder' => $workshopOrder, '__locale' => $locale)));
                        $mailer->sendMailToSpool();
                        //echo $this->renderView('UtilBundle:Mailing:order_new_mail.html.twig', array('workshopOrder' => $workshopOrder));die;

                        // Copia del mail de confirmacion a modo de backup
                        //
                        $mail = $this->container->getParameter('mail_report');
                        $request->setLocale('es_ES');
                        $mailer->setTo($mail);
                        $mailer->sendMailToSpool();

                        // Dejamos el locale tal y como estaba
                        $request->setLocale($locale);
                    }

                    return $this->redirect($this->generateUrl('list_orders'));

                }else{
                    if($findPhone[0]['1']>0){
                        $flash = $this->get('translator')->trans('error.code_phone.used').$workshopOrder->getPhoneNumber1();
                    }
                    else if($findPhone[1]['1']>0){
                        $flash = $this->get('translator')->trans('error.code_phone.used').$workshopOrder->getPhoneNumber2();
                    }
                    else if($findPhone[2]['1']>0){
                        $flash = $this->get('translator')->trans('error.code_phone.used').$workshopOrder->getMobileNumber1();
                    }
                    else if($findPhone[3]['1']>0){
                        $flash = $this->get('translator')->trans('error.code_phone.used').$workshopOrder->getMobileNumber2();
                    }
                    else {
                        $flash = $this->get('translator')->trans('error.code_workshop.used').$code;
                    }
                    $this->get('session')->setFlash('error', $flash);
                }
            }else{
                var_dump($form_errors);
            }
        }

        // $id_partner = $security->getToken()->getUser()->getPartner()->getId();

        // $shops[] = $em->getRepository("PartnerBundle:Shop")->find(1);

        // $array_shops = $em->getRepository("PartnerBundle:Shop")->findBy(array('partner' => $id_partner));

        // foreach ($array_shops as $shop) { $shops[] = $shop; }

        return $this->render('OrderBundle:WorkshopOrders:new_order.html.twig', array('workshopOrder'    => $workshopOrder,
                                                                                     'form_name'        => $form->getName(),
                                                                                     'form'             => $form->createView(),
                                                                                     'partners'         => $partners,
                                                                                     // 'shops'            => $shops,
                                                                                     'id_partner'       => $id_partner,
                                                                                     'code'             => $code));
    }

//  _____ ____ ___ _____
// | ____|  _ \_ _|_   _|
// |  _| | | | | |  | |
// | |___| |_| | |  | |
// |_____|____/___| |_|

    /**
     * Crea una solicitud (workshopOrder) del tipo "modify"
     * @param integer $id del workshop que queremos modificar
     * @return type
     * @throws AccessDeniedException
     * @throws type
     */
    public function editAction($id) {
        $security = $this->get('security.context');
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();

        //miramos si es una "re-modificacion" (una modificacion ha sido rechazada y la volvemos a modificar para volver a enviar)
        $workshopOrder = $em->getRepository("OrderBundle:WorkshopOrder")->findOneBy(array('id_workshop' => $id,
                                                                                          'action' => 'rejected'));
        if ($workshopOrder) $workshop = $em->getRepository("WorkshopBundle:Workshop")->find($workshopOrder->getIdWorkshop());
        else {
            $workshop = $em->getRepository("WorkshopBundle:Workshop")->find($id);
            if (!$workshop)
                throw $this->createNotFoundException('Taller no encontrado en la BBDD');

            //si no existe una workshopOrder previa la creamos por primera vez a partir del workshop original
             $workshopOrder = $this->workshop_to_workshopOrder($workshop);
        }

        if (!$security->isGranted('ROLE_SUPERADMIN'))
        {
            // SUPER_AD
            if ($security->isGranted('ROLE_SUPER_AD'))
            {
                if($security->getToken()->getUser()->getCountry()->getId() != $workshopOrder->getCountry()->getId())
                throw new AccessDeniedException();
            }
            // AD
            else{
                if($security->getToken()->getUser()->getPartner()->getCodePartner() != $workshopOrder->getPartner()->getCodePartner())
                throw new AccessDeniedException();
            }
        }

        if ((($security->isGranted('ROLE_AD') and $security->getToken()->getUser()->getCountry()->getId() == $workshopOrder->getCountry()->getId()) === false)
        and (!$security->isGranted('ROLE_SUPER_AD'))) {
            return $this->render('TwigBundle:Exception:exception_access.html.twig');
        }

        if ($security->isGranted('ROLE_SUPER_AD')) {
            $id_partner = '0';
            $partners   = $em->getRepository("PartnerBundle:Partner")->findBy(array('country' => $security->getToken()->getUser()->getCountry()->getId(),
                                                                                    'active' => '1'));
        }
        else { $id_partner = $security->getToken()->getUser()->getPartner()->getId();
               $partners   = '0';
        }

        $partner = $workshop->getPartner();

        // Creamos variables de sesion para fitlrar los resultados del formulario
        if ($security->isGranted('ROLE_SUPER_AD')) {

            $partner_ids = '0';
            foreach ($partners as $p) { $partner_ids = $partner_ids.', '.$p->getId(); }

            $_SESSION['id_partner'] = ' IN ('.$partner_ids.')';
            $_SESSION['id_country'] = ' = '.$security->getToken()->getUser()->getCountry()->getId();

        }else {
            $_SESSION['id_partner'] = ' = '.$partner->getId();
            $_SESSION['id_country'] = ' = '.$partner->getCountry()->getId();
        }
        $form = $this->createForm(new WorkshopEditOrderType(), $workshopOrder);

        if ($request->getMethod() == 'POST') {

            $form->bindRequest($request);

            if ($form->isValid()) {
                $user = $security->getToken()->getUser();

                $workshopOrder = UtilController::newEntity($workshopOrder, $user);

                if ($workshopOrder->getAction() == 'rejected' && $workshopOrder->getWantedAction() == 'modify') {
                    $workshopOrder->setAction('re_modify');
                }
                elseif ($workshopOrder->getAction() == 'rejected') {
                    $workshopOrder->setAction('re_modify');
                }else{
                    $workshopOrder->setAction('modify');
                    $workshopOrder->setWantedAction('modify');
                }

                // Set default shop to NULL
                $shop = $form['shop']->getClientData();
                if($shop == 0) { $workshopOrder->setShop(null); }

                //CHECKS
                // if($workshopOrder->getHasChecks() == false and $workshopOrder->getNumChecks() != null) $workshopOrder->setNumChecks(null);
                // if($workshopOrder->getHasChecks() == true and $workshopOrder->getNumChecks() == '') $workshopOrder->setNumChecks(0);

                UtilController::saveEntity($em, $workshopOrder, $user);

                $mail = $workshopOrder->getCreatedBy()->getEmail1();
                $pos = strpos($mail, '@');
                if ($pos != 0) {

                    // Cambiamos el locale para enviar el mail en el idioma del taller
                    $locale = $request->getLocale();
                    $lang_w = $workshopOrder->getPartner()->getCountry()->getLang();
                    $lang   = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_w);
                    $request->setLocale($lang->getShortName());

                    /* MAILING */
                    $mailer = $this->get('cms.mailer');
                    $mailer->setTo($mail);
                    $mailer->setSubject($this->get('translator')->trans('mail.editOrder.subject').$workshopOrder->getId());
                    $mailer->setFrom('noreply@adserviceticketing.com');
                    $mailer->setBody($this->renderView('UtilBundle:Mailing:order_edit_mail.html.twig', array('workshopOrder' => $workshopOrder,
                                                                                                             'workshop'      => $workshop,
                                                                                                             '__locale' => $locale )));
                    $mailer->sendMailToSpool();
                    // echo $this->renderView('UtilBundle:Mailing:order_edit_mail.html.twig', array('workshopOrder' => $workshopOrder,
                    //                                                                              'workshop'      => $workshop));die;

                    // Copia del mail de confirmacion a modo de backup
                    //
                    $mail = $this->container->getParameter('mail_report');
                    $request->setLocale('es_ES');
                    $mailer->setTo($mail);
                    $mailer->sendMailToSpool();

                    // Dejamos el locale tal y como estaba
                    $request->setLocale($locale);
                }

                return $this->redirect($this->generateUrl('list_orders'));

            }
        }

        $user = $em->getRepository('UserBundle:User')->findOneByWorkshop($workshop->getId());
        $token = $user->getToken();
        return $this->render('OrderBundle:WorkshopOrders:edit_order.html.twig', array( 'workshopOrder' => $workshopOrder,
                                                                                       'workshop'      => $workshop,
                                                                                       'token'         => $token,
                                                                                       'id_partner'    => $id_partner,              //old values
                                                                                       'form_name'     => $form->getName(),         //new values
                                                                                       'form'          => $form->createView()));

    }

//   ____ _   _    _    _   _  ____ _____   ____ _____  _  _____ _   _ ____
//  / ___| | | |  / \  | \ | |/ ___| ____| / ___|_   _|/ \|_   _| | | / ___|
// | |   | |_| | / _ \ |  \| | |  _|  _|   \___ \ | | / _ \ | | | | | \___ \
// | |___|  _  |/ ___ \| |\  | |_| | |___   ___) || |/ ___ \| | | |_| |___) |
//  \____|_| |_/_/   \_\_| \_|\____|_____| |____/ |_/_/   \_\_|  \___/|____/

    /**
     * Crea una solicitud (workshopOrder) del tipo "activate" o "deactivate" segun el $status
     * @Route("/workshop/change_status/{id}/{status}")
     * @ParamConverter("workshop", class="WorkshopBundle:Workshop")
     * @param string $status (active | inactive)
     * @return type
     * @throws AccessDeniedException
     * @throws type
     */
    public function changeStatusAction($id, $status, $workshop){

        $security = $this->get('security.context');
        if ($security->isGranted('ROLE_AD') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();

        //si veneimos de un estado "rejected" y queremos volver a activar/desactivar tenemos que eliminar la workshopOrder antigua
        //antes de crear la nueva (asi evitamos tener workshopsOrders duplicados)
        $workshopOrder = $em->getRepository("OrderBundle:WorkshopOrder")->findOneBy(array('id_workshop' => $id));
        if ($workshopOrder && $workshopOrder->getAction() == 'rejected'){
            $em->remove($workshopOrder);
            $em->flush();
        }

        $user = $security->getToken()->getUser();
        $workshopOrder = $this->workshop_to_workshopOrder($workshop);
        $workshopOrder = UtilController::newEntity($workshopOrder, $user);

        //actualizamos el campo "action" de la orden segun queramos activar o desactivar
        if ($status == 'active'){
            $workshopOrder->setAction('activate');
            $workshopOrder->setWantedAction('activate');

        }elseif ($status == 'inactive'){
            $workshopOrder->setAction('deactivate');
            $workshopOrder->setWantedAction('deactivate');
        }
        $workshopOrder->setAction('deactivate');
        UtilController::saveEntity($em, $workshopOrder, $user);

        $mail = $workshopOrder->getCreatedBy()->getEmail1();
        $pos = strpos($mail, '@');
        if ($pos != 0) {

            // Cambiamos el locale para enviar el mail en el idioma del taller
            $locale = $request->getLocale();
            $lang_w = $workshopOrder->getPartner()->getCountry()->getLang();
            $lang   = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_w);
            $request->setLocale($lang->getShortName());

            /* MAILING */
            $mailer = $this->get('cms.mailer');
            $mailer->setTo($mail);
            $mailer->setSubject($this->get('translator')->trans('mail.changeOrder.subject').$workshopOrder->getId());
            $mailer->setFrom('noreply@adserviceticketing.com');
            $mailer->setBody($this->renderView('UtilBundle:Mailing:order_change_mail.html.twig', array('workshopOrder' => $workshopOrder, '__locale' => $locale)));
            $mailer->sendMailToSpool();
            //echo $this->renderView('UtilBundle:Mailing:order_change_mail.html.twig', array('workshopOrder' => $workshopOrder));die;

            // Copia del mail de confirmacion a modo de backup
            //
            $mail = $this->container->getParameter('mail_report');
            $request->setLocale('es_ES');
            $mailer->setTo($mail);
            $mailer->sendMailToSpool();

            // Dejamos el locale tal y como estaba
            $request->setLocale($locale);
        }

        return $this->redirect($this->generateUrl('list_orders'));

    }

//  ____  _____    _ _____ ____ _____
// |  _ \| ____|  | | ____/ ___|_   _|
// | |_) |  _| _  | |  _|| |     | |
// |  _ <| |__| |_| | |__| |___  | |
// |_| \_\_____\___/|_____\____| |_|


    /**
     * Actualiza el campo "rejection_reason" de la workshopOrder y pone su estada en "rejected"
     * @Route("/workshop/reject/{id}")
     * @ParamConverter("workshopOrder", class="OrderBundle:WorkshopOrder")
     * @return type
     * @throws AccessDeniedException
     * @throws type
     */
    public function rejectAction($workshopOrder){
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();

        $form = $this->createForm(new WorkshopRejectOrderType(), $workshopOrder);

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {

                $workshopOrder->setAction('rejected');
                $workshopOrder->setRejectionReason($form->get('rejection_reason')->getData());     //recogemos del formulario el motivo de rechazo...
                $em->persist($workshopOrder);
                $em->flush();

                $mail = $workshopOrder->getCreatedBy()->getEmail1();
                $pos = strpos($mail, '@');
                if ($pos != 0) {

                    // Cambiamos el locale para enviar el mail en el idioma del taller
                    $locale = $request->getLocale();
                    $lang_w = $workshopOrder->getPartner()->getCountry()->getLang();
                    $lang   = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_w);
                    $request->setLocale($lang->getShortName());

                    /* MAILING */
                    $mailer = $this->get('cms.mailer');
                    $mailer->setTo($mail);
                    $mailer->setSubject($this->get('translator')->trans('mail.rejectOrder.subject').$workshopOrder->getId());
                    $mailer->setFrom('noreply@adserviceticketing.com');
                    $mailer->setBody($this->renderView('UtilBundle:Mailing:order_reject_mail.html.twig', array('workshopOrder' => $workshopOrder, '__locale' => $locale)));
                    $mailer->sendMailToSpool();
                    //echo $this->renderView('UtilBundle:Mailing:order_reject_mail.html.twig', array('workshopOrder' => $workshopOrder));die;

                    // Copia del mail de confirmacion a modo de backup
                    //
                    $mail = $this->container->getParameter('mail_report');
                    $request->setLocale('es_ES');
                    $mailer->setTo($mail);
                    $mailer->sendMailToSpool();

                    // Dejamos el locale tal y como estaba
                    $request->setLocale($locale);
                }

                return $this->redirect($this->generateUrl('list_orders'));
            }

        }

        return $this->render('OrderBundle:WorkshopOrders:reject_order.html.twig', array('workshopOrder' => $workshopOrder,
                                                                                        'form_name'     => $form->getName(),
                                                                                        'form'          => $form->createView()));
    }

//  ____  _____ ____  _____ _   _ ____
// |  _ \| ____/ ___|| ____| \ | |  _ \
// | |_) |  _| \___ \|  _| |  \| | | | |
// |  _ <| |___ ___) | |___| |\  | |_| |
// |_| \_\_____|____/|_____|_| \_|____/

    /**
     * Reenvia la solicitud
     * @Route("/workshop/resend/{id}")
     * @ParamConverter("workshopOrder", class="OrderBundle:WorkshopOrder")
     * @return type
     */
    public function resendAction($workshopOrder){

        if ($this->get('security.context')->isGranted('ROLE_AD') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();

        //si veneimos de un estado "rejected" y queremos volver a solicitar tenemos que eliminar la workshopOrder antigua
        //antes de crear la nueva (asi evitamos tener workshopsOrders duplicados)
        if ($workshopOrder && $workshopOrder->getAction() == 'rejected'){
            $workshopOrder->setAction($workshopOrder->getWantedAction());
            $em->persist($workshopOrder);

            $action = $workshopOrder->getWantedAction();

            $mail = $workshopOrder->getCreatedBy()->getEmail1();
            $pos = strpos($mail, '@');
            if ($pos != 0) {

                // Cambiamos el locale para enviar el mail en el idioma del taller
                $locale = $request->getLocale();
                $lang_w = $workshopOrder->getPartner()->getCountry()->getLang();
                $lang   = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_w);
                $request->setLocale($lang->getShortName());

                /* MAILING */
                $mailer = $this->get('cms.mailer');
                $mailer->setTo($mail);
                $mailer->setSubject($this->get('translator')->trans('mail.resendOrder.subject').$workshopOrder->getId());
                $mailer->setFrom('noreply@adserviceticketing.com');
                $mailer->setBody($this->renderView('UtilBundle:Mailing:order_resend_mail.html.twig', array('workshopOrder' => $workshopOrder,
                                                                                                           'action'        => $action,
                                                                                                           '__locale' => $locale)));
                $mailer->sendMailToSpool();
                // echo $this->renderView('UtilBundle:Mailing:order_resend_mail.html.twig', array('workshopOrder' => $workshopOrder,
                //                                                                                'action'   => $action));die;

                // Copia del mail de confirmacion a modo de backup
                //
                $mail = $this->container->getParameter('mail_report');
                $request->setLocale('es_ES');
                $mailer->setTo($mail);
                $mailer->sendMailToSpool();

                // Dejamos el locale tal y como estaba
                $request->setLocale($locale);
            }

            $em->flush();
        }
        return $this->redirect($this->generateUrl('list_orders'));
    }

//  ____  _____ __  __  _____     _______
// |  _ \| ____|  \/  |/ _ \ \   / / ____|
// | |_) |  _| | |\/| | | | \ \ / /|  _|
// |  _ <| |___| |  | | |_| |\ V / | |___
// |_| \_\_____|_|  |_|\___/  \_/  |_____|

    /**
     * Elimina una workshopOrder segun el $id
     * @Route("/workshop/remove/{id}")
     * @ParamConverter("workshopOrder", class="OrderBundle:WorkshopOrder")
     * @return type
     * @throws AccessDeniedException
     * @throws type
     */
    public function removeAction($workshopOrder){

        if ($this->get('security.context')->isGranted('ROLE_AD') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();

        $action = $workshopOrder->getWantedAction();

        $mail = $workshopOrder->getCreatedBy()->getEmail1();
        $pos = strpos($mail, '@');
        if ($pos != 0) {

            // Cambiamos el locale para enviar el mail en el idioma del taller
            $locale = $request->getLocale();
            $lang_w = $workshopOrder->getPartner()->getCountry()->getLang();
            $lang   = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_w);
            $request->setLocale($lang->getShortName());

            /* MAILING */
            $mailer = $this->get('cms.mailer');
            $mailer->setTo($mail);
            $mailer->setSubject($this->get('translator')->trans('mail.removeOrder.subject').$workshopOrder->getId());
            $mailer->setFrom('noreply@adserviceticketing.com');
            $mailer->setBody($this->renderView('UtilBundle:Mailing:order_remove_mail.html.twig', array('workshopOrder' => $workshopOrder,
                                                                                                       'action'        => $action,
                                                                                                       '__locale' => $locale)));
            $mailer->sendMailToSpool();
            // echo $this->renderView('UtilBundle:Mailing:order_remove_mail.html.twig', array('workshopOrder' => $workshopOrder,
            //                                                                                'action'   => $action));die;

            // Copia del mail de confirmacion a modo de backup
            //
            $mail = $this->container->getParameter('mail_report');
            $request->setLocale('es_ES');
            $mailer->setTo($mail);
            $mailer->sendMailToSpool();

            // Dejamos el locale tal y como estaba
            $request->setLocale($locale);
        }

        $em->remove($workshopOrder);
        $em->flush();

        return $this->redirect($this->generateUrl('list_orders'));

    }

//     _    ____ ____ _____ ____ _____
//    / \  / ___/ ___| ____|  _ \_   _|
//   / _ \| |  | |   |  _| | |_) || |
//  / ___ \ |__| |___| |___|  __/ | |
// /_/   \_\____\____|_____|_|    |_|


    /**
     * Acepta la solicitud
     * @Route("/workshop/accept/{id}")
     * @ParamConverter("workshopOrder", class="OrderBundle:WorkshopOrder")
     * @param string $status (accepted)
     * @return type
     * @throws AccessDeniedException
     */
    public function acceptAction($workshopOrder, $status){

        $security = $this->get('security.context');
        if ($security->isGranted('ROLE_ADMIN') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();

        // activate   + accepted = setActive a TRUE  and delete workshopOrder
        // deactivate + accepted = setActive a FALSE and delete workshopOrder
        // modify     + accepted = se hacen los cambios en workshop and delete del workshopOrder
        // create     + accepted = new workshop and delete workshopOrder

        $user = $security->getToken()->getUser();

        /*COMPROBAR CODE WORKSHOP NO SE REPITA*/
        $find = $em->getRepository("WorkshopBundle:Workshop")->findOneBy(array('partner'       => $workshopOrder->getPartner()->getId(),
                                                                               'code_workshop' => $workshopOrder->getCodeWorkshop()));
        $code= 0;
        $flash = "";
        $findPhone = array(0,0,0,0);
        if($workshopOrder->getPhoneNumber1() !=null){
            $findPhone[0] = $em->getRepository("WorkshopBundle:Workshop")->findPhone($workshopOrder->getPhoneNumber1());
        }
        if($workshopOrder->getPhoneNumber2() !=null){
            $findPhone[1] = $em->getRepository("WorkshopBundle:Workshop")->findPhone($workshopOrder->getPhoneNumber2());
        }
        if($workshopOrder->getMobileNumber1() !=null){
            $findPhone[2] = $em->getRepository("WorkshopBundle:Workshop")->findPhone($workshopOrder->getMobileNumber1());
        }
        if($workshopOrder->getMobileNumber2() !=null){
            $findPhone[3] = $em->getRepository("WorkshopBundle:Workshop")->findPhone($workshopOrder->getMobileNumber2());
        }
        if(isset($find))$codeWorkshop = $find->getCodeWorkshop();
        else            $codeWorkshop = " ";
        if($find){
            $code  = UtilController::getCodeWorkshopUnused($em, $workshopOrder->getPartner());        /*OBTIENE EL PRIMER CODIGO DISPONIBLE*/
            $flash .= $this->get('translator')->trans('error.code_partner.used').$code.' ('.$codeWorkshop.').';
        }
        if($findPhone[0]['1']>0){
            $flash .=" ". $this->get('translator')->trans('error.code_phone.used').$workshopOrder->getPhoneNumber1();
        }
        else if($findPhone[1]['1']>0){
            $flash .=" ". $this->get('translator')->trans('error.code_phone.used').$workshopOrder->getPhoneNumber2();
        }
        else if($findPhone[2]['1']>0){
            $flash .=" ". $this->get('translator')->trans('error.code_phone.used').$workshopOrder->getMobileNumber1();
        }
        else if($findPhone[3]['1']>0){
            $flash .=" ". $this->get('translator')->trans('error.code_phone.used').$workshopOrder->getMobileNumber2();
        }

//        else $this->get('session')->setFlash('error', $flash);

            if (( $workshopOrder->getWantedAction() == 'activate') && $status == 'accepted'){
                $workshop = $em->getRepository('WorkshopBundle:Workshop')->findOneBy(array('id' => $workshopOrder->getIdWorkshop()));
                $workshop = $this->workshopOrder_to_workshop($workshop, $workshopOrder);
                $workshop->setActive(true);

                $action = $workshopOrder->getWantedAction();
                $em->remove($workshopOrder);
                $user_workshop = $em->getRepository('UserBundle:User')->findOneBy(array('workshop' => $workshop->getId()));
                $user_workshop->setActive($workshop->getActive());
                $em->persist($user_workshop);
                $em->flush();
                UtilController::newEntity($workshop, $user);
                UtilController::saveEntity($em, $workshop, $user);

                // Cambiamos el locale para enviar el mail en el idioma del taller
                $locale = $request->getLocale();
                $lang_w = $workshop->getCountry()->getLang();
                $lang   = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_w);
                $request->setLocale($lang->getShortName());

                // Enviamos un mail con la solicitud al taller
                $mail = $workshop->getEmail1();
                $pos = strpos($mail, '@');
                if ($pos != 0) {

                    /* MAILING */
                    $mailerUser = $this->get('cms.mailer');
                    $mailerUser->setTo($mail);
                    $mailerUser->setSubject($this->get('translator')->trans('mail.acceptOrder.subject').$workshop);
                    $mailerUser->setFrom('noreply@adserviceticketing.com');
                    $mailerUser->setBody($this->renderView('UtilBundle:Mailing:order_accept_mail.html.twig', array('workshop' => $workshop, 'action'=> 'activate', '__locale' => $locale)));
                    $mailerUser->sendMailToSpool();
                    // echo $this->renderView('UtilBundle:Mailing:user_new_mail.html.twig', array('user' => $newUser, 'password' => $pass));die;

                }
                // Enviamos un mail con la solicitud a modo de backup
                $mail = $this->container->getParameter('mail_report');
                $pos = strpos($mail, '@');
                if ($pos != 0) {

                    $mailerUser->setTo($mail);
                    $mailerUser->sendMailToSpool();
                }
                // Dejamos el locale tal y como estaba
                $request->setLocale($locale);

            }elseif (( $workshopOrder->getWantedAction() == 'deactivate') && $status == 'accepted'){
                $workshop = $em->getRepository('WorkshopBundle:Workshop')->findOneBy(array('id' => $workshopOrder->getIdWorkshop()));
                $workshop = $this->workshopOrder_to_workshop($workshop, $workshopOrder);
                $workshop->setActive(false);
                $workshop->setLowdateAt(new \DateTime(\date("Y-m-d H:i:s")));

                $action = $workshopOrder->getWantedAction();
                $user_workshop = $em->getRepository('UserBundle:User')->findOneBy(array('workshop' => $workshop->getId()));
                $user_workshop->setActive($workshop->getActive());
                $em->persist($user_workshop);
                $em->flush();
                $em->remove($workshopOrder);
                UtilController::saveEntity($em, $workshop, $user);

                // Cambiamos el locale para enviar el mail en el idioma del taller
                $locale = $request->getLocale();
                $lang_w = $workshop->getCountry()->getLang();
                $lang   = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_w);
                $request->setLocale($lang->getShortName());

                // Enviamos un mail con la solicitud al taller
                $mail = $workshop->getEmail1();
                $pos = strpos($mail, '@');
                if ($pos != 0) {

                    /* MAILING */
                    $mailerUser = $this->get('cms.mailer');
                    $mailerUser->setTo($mail);
                    $mailerUser->setSubject($this->get('translator')->trans('mail.acceptOrder.subject').$workshop);
                    $mailerUser->setFrom('noreply@adserviceticketing.com');
                    $mailerUser->setBody($this->renderView('UtilBundle:Mailing:order_accept_mail.html.twig', array('workshop' => $workshop, 'action'=> 'deactivate', '__locale' => $locale)));
                    $mailerUser->sendMailToSpool();
                    // echo $this->renderView('UtilBundle:Mailing:user_new_mail.html.twig', array('user' => $newUser, 'password' => $pass));die;

                }
                // Enviamos un mail con la solicitud a modo de backup
                $mail = $this->container->getParameter('mail_report');
                $pos = strpos($mail, '@');
                if ($pos != 0) {

                    $mailerUser->setTo($mail);
                    $mailerUser->sendMailToSpool();
                }
                // Dejamos el locale tal y como estaba
                $request->setLocale($locale);


            }elseif (($workshopOrder->getWantedAction() == 'modify')  && $status == 'accepted'){
                $workshop = $em->getRepository('WorkshopBundle:Workshop')->findOneBy(array('id' => $workshopOrder->getIdWorkshop()));
                $workshop = $this->workshopOrder_to_workshop($workshop, $workshopOrder);
                $action = $workshopOrder->getWantedAction();
                $em->remove($workshopOrder);
                $user_workshop = $em->getRepository('UserBundle:User')->findOneBy(array('workshop' => $workshop->getId()));
                $user_workshop = UtilController::saveUserFromWorkshop($workshop,$user_workshop);

                $user_workshop->setName($workshop->getContact());
                $user_workshop->setActive($workshop->getActive());
                $em->persist($user_workshop);
                $em->flush();
                UtilController::saveEntity($em, $workshop, $user);

                // Cambiamos el locale para enviar el mail en el idioma del taller
                $locale = $request->getLocale();
                $lang_w = $workshop->getCountry()->getLang();
                $lang   = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_w);
                $request->setLocale($lang->getShortName());

                // Enviamos un mail con la solicitud al taller
                $mail = $workshop->getEmail1();
                $pos = strpos($mail, '@');
                if ($pos != 0) {

                    /* MAILING */
                    $mailerUser = $this->get('cms.mailer');
                    $mailerUser->setTo($mail);
                    $mailerUser->setSubject($this->get('translator')->trans('mail.acceptOrder.subject').$workshop);
                    $mailerUser->setFrom('noreply@adserviceticketing.com');
                    $mailerUser->setBody($this->renderView('UtilBundle:Mailing:order_accept_mail.html.twig', array('workshop' => $workshop, 'action'=> 'modify', '__locale' => $locale)));
                    $mailerUser->sendMailToSpool();
                    // echo $this->renderView('UtilBundle:Mailing:user_new_mail.html.twig', array('user' => $newUser, 'password' => $pass));die;

                }
                // Enviamos un mail con la solicitud a modo de backup
                $mail = $this->container->getParameter('mail_report');
                $pos = strpos($mail, '@');
                if ($pos != 0) {

                    $mailerUser->setTo($mail);
                    $mailerUser->sendMailToSpool();
                }
                // Dejamos el locale tal y como estaba
                $request->setLocale($locale);

            }elseif (($workshopOrder->getWantedAction() == 'create')  && $status == 'accepted'){

                if($find == null or $workshopOrder->getCodeWorkshop() != $find->getCodeWorkshop())
                {
                    $workshop = $this->workshopOrder_to_workshop(new Workshop(), $workshopOrder);
                    if ($workshopOrder->getTest() != null) {
                        $workshop->setEndTestAt(new \DateTime(\date('Y-m-d H:i:s',strtotime("+1 month"))));
                    }
                    $action = $workshopOrder->getWantedAction();
                    $em->remove($workshopOrder);

                    UtilController::newEntity($workshop, $user);
                    UtilController::saveEntity($em, $workshop, $user);

                    //Si ha seleccionado AD-Service + lo añadimos a la BBDD correspondiente
                    if ($workshop->getAdServicePlus()){
                        $adsplus = new ADSPlus();
                        $adsplus->setIdTallerADS($workshop->getID());
                        $dateI = new \DateTime('now');
                        $dateF = new \DateTime('+2 year');
                        $adsplus->setAltaInicial($dateI->format('Y-m-d'));
                        $adsplus->setUltAlta($dateI->format('Y-m-d'));
                        $adsplus->setBaja($dateF->format('Y-m-d'));
                        $adsplus->setContador(0);
                        $adsplus->setActive(1);
                        $em->persist($adsplus);
                        $em->flush();
                    }

                    /*CREAR USERNAME Y EVITAR REPETICIONES*/
                    $username = UtilController::getUsernameUnused($em, $workshop->getName());

                    /*CREAR PASSWORD AUTOMATICAMENTE*/
                    $pass = substr( md5(microtime()), 1, 8);

                    $role = $em->getRepository('UserBundle:Role')->findOneByName('ROLE_USER');
                    $lang = $em->getRepository('UtilBundle:Language')->findOneByLanguage($workshop->getCountry()->getLang());

                    $newUser = UtilController::newEntity(new User(), $user);
                    $newUser->setUsername      ($username);
                    $newUser->setPassword      ($pass);
                    $newUser->setName          ($workshop->getContactName());
                    $newUser->setSurname       ($workshop->getName());
                    $newUser->setPhoneNumber1  ($workshop->getPhoneNumber1());
                    $newUser->setPhoneNumber2  ($workshop->getPhoneNumber2());
                    $newUser->setMobileNumber1 ($workshop->getMobileNumber1());
                    $newUser->setMobileNumber2 ($workshop->getMobileNumber2());
                    $newUser->setFax           ($workshop->getFax());
                    $newUser->setEmail1        ($workshop->getEmail1());
                    $newUser->setEmail2        ($workshop->getEmail2());
                    $newUser->setActive        ('1');
                    $newUser->setCountry       ($workshop->getCountry());
                    $newUser->setRegion        ($workshop->getRegion());
                    $newUser->setCity          ($workshop->getCity());
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
                    UtilController::saveEntity($em, $newUser, $user);

                    // Enviamos un mail con credenciales de usuario a modo de backup
                    $mail = $this->container->getParameter('mail_report');
                    $pos = strpos($mail, '@');
                    if ($pos != 0) {

                        // Cambiamos el locale para enviar el mail en el idioma del taller
                        $locale = $request->getLocale();

                        /* MAILING */
                        $mailerUser = $this->get('cms.mailer');
                        $mailerUser->setTo($mail);
                        $mailerUser->setSubject($this->get('translator')->trans('mail.newUser.subject').$newUser->getWorkshop());
                        $mailerUser->setFrom('noreply@adserviceticketing.com');
                        $mailerUser->setBody($this->renderView('UtilBundle:Mailing:user_new_mail.html.twig', array('user' => $newUser, 'password' => $pass, '__locale' => $locale)));
                        $mailerUser->sendMailToSpool();
                        // echo $this->renderView('UtilBundle:Mailing:user_new_mail.html.twig', array('user' => $newUser, 'password' => $pass));die;

                        // Dejamos el locale tal y como estaba
                        $request->setLocale($locale);
                    }

                    $flash =  $this->get('translator')->trans('create').' '.$this->get('translator')->trans('workshop').': '.$username.'  -  '.$this->get('translator')->trans('with_password').': '.$pass;
                    $this->get('session')->setFlash('alert', $flash);
                }
                else $this->get('session')->setFlash('error', $flash);
            }

            if($find == null or $workshopOrder->getCodeWorkshop() != $find->getCodeWorkshop())
            {
                $mail = $workshop->getCreatedBy()->getEmail1();
                $pos = strpos($mail, '@');
                if ($pos != 0) {

                    // Cambiamos el locale para enviar el mail en el idioma del taller
                    $locale = $request->getLocale();
                    $lang_w = $newUser->getCountry()->getLang();
                    $lang   = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_w);
                    $request->setLocale($lang->getShortName());

                    /* MAILING */
                    $mailer = $this->get('cms.mailer');
                    $mailer->setTo($mail);
                    $mailer->setSubject($this->get('translator')->trans('mail.acceptOrder.subject').$workshop->getId());
                    $mailer->setFrom('noreply@adserviceticketing.com');
                    $mailer->setBody($this->renderView('UtilBundle:Mailing:order_accept_mail.html.twig', array('workshop' => $workshop,
                                                                                                               'action'   => $action,
                                                                                                               '__locale' => $locale)));
                    $mailer->sendMailToSpool();
                    // echo $this->renderView('UtilBundle:Mailing:order_accept_mail.html.twig', array('workshop' => $workshop,
                    //                                                                                'action'   => $action));die;

                    // Copia del mail de confirmacion a modo de backup
                    //
                    $mail = $this->container->getParameter('mail_report');
                    $request->setLocale('es_ES');
                    $mailer->setTo($mail);
                    $mailer->sendMailToSpool();

                    // Dejamos el locale tal y como estaba
                    $request->setLocale($locale);
                }
            }

        return $this->redirect($this->generateUrl('list_orders'));
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
        $workshopOrder->setCodePartner   ($workshop->getCodePartner());
        $workshopOrder->setCodeWorkshop  ($workshop->getCodeWorkshop());
        $workshopOrder->setCif           ($workshop->getCif());
        $workshopOrder->setInternalCode  ($workshop->getInternalCode());
        $workshopOrder->setPartner       ($workshop->getPartner());
        $shop = $workshop->getShop();
        if(isset($shop)) { $workshopOrder->setShop($shop); }
        $tipology = $workshop->getTypology();
        if(isset($tipology)) { $workshopOrder->setTypology($tipology); }
        $workshopOrder->setTest          ($workshop->getTest());
        $workshopOrder->setContactName   ($workshop->getContactName());
        $workshopOrder->setPhoneNumber1  ($workshop->getPhoneNumber1());
        $workshopOrder->setPhoneNumber2  ($workshop->getPhoneNumber2());
        $workshopOrder->setMobileNumber1 ($workshop->getMobileNumber1());
        $workshopOrder->setMobileNumber2 ($workshop->getMobileNumber2());
        $workshopOrder->setFax           ($workshop->getFax());
        $workshopOrder->setEmail1        ($workshop->getEmail1());
        $workshopOrder->setEmail2        ($workshop->getEmail2());
        $workshopOrder->setCountry       ($workshop->getCountry());
        $workshopOrder->setRegion        ($workshop->getRegion());
        $workshopOrder->setCity          ($workshop->getCity());
        $workshopOrder->setAddress       ($workshop->getAddress());
        $workshopOrder->setPostalCode    ($workshop->getPostalCode());
        $workshopOrder->setAdServicePlus ($workshop->getAdServicePlus());
        //CHECKS
        // $workshopOrder->setHasChecks     ($workshop->getHasChecks());
        // $workshopOrder->setNumChecks     ($workshop->getNumChecks());

        if ($workshopOrder->getCreatedBy() != null ) {
            $workshopOrder->setCreatedBy($workshopOrder->getCreatedBy());
        }
        if ($workshopOrder->getCreatedAt() != null ) {
            $workshopOrder->setCreatedAt($workshopOrder->getCreatedAt());
        }
        if ($workshopOrder->getModifiedBy() != null ) {
            $workshopOrder->setModifiedBy($workshopOrder->getModifiedBy());
        }
        if ($workshopOrder->getModifiedAt() != null ) {
            $workshopOrder->setModifiedAt($workshopOrder->getModifiedAt());
        }
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

        $workshop->setName          ($workshopOrder->getName());
        $workshop->setCodePartner   ($workshopOrder->getCodePartner());
        $workshop->setCodeWorkshop  ($workshopOrder->getCodeWorkshop());
        $workshop->setCif           ($workshopOrder->getCif());
        $workshop->setInternalCode  ($workshopOrder->getInternalCode());
        $workshop->setCodePartner   ($workshopOrder->getPartner()->getCodePartner());
        $workshop->setPartner       ($workshopOrder->getPartner());
        $shop = $workshopOrder->getShop();
        if(isset($shop)) { $workshop->setShop($shop); }
        $workshop->setTypology      ($workshopOrder->getTypology());
        $workshop->setTest          ($workshopOrder->getTest());
        $workshop->setContactName   ($workshopOrder->getContact());
        $workshop->setPhoneNumber1  ($workshopOrder->getPhoneNumber1());
        $workshop->setPhoneNumber2  ($workshopOrder->getPhoneNumber2());
        $workshop->setMobileNumber1 ($workshopOrder->getMobileNumber1());
        $workshop->setMobileNumber2 ($workshopOrder->getMobileNumber2());
        $workshop->setFax           ($workshopOrder->getFax());
        $workshop->setEmail1        ($workshopOrder->getEmail1());
        $workshop->setEmail2        ($workshopOrder->getEmail2());
        $workshop->setCountry       ($workshopOrder->getCountry());
        $workshop->setRegion        ($workshopOrder->getRegion());
        $workshop->setCity          ($workshopOrder->getCity());
        $workshop->setAddress       ($workshopOrder->getAddress());
        $workshop->setPostalCode    ($workshopOrder->getPostalCode());
        $workshop->setAdServicePlus ($workshopOrder->getAdServicePlus());
        //CHECKS
        // $workshop->setHasChecks     ($workshopOrder->getHasChecks());
        // $workshop->setNumChecks     ($workshopOrder->getNumChecks());

        if ($workshopOrder->getCreatedBy() != null ) {
            $workshop->setCreatedBy($workshopOrder->getCreatedBy());
        }
        if ($workshopOrder->getCreatedAt() != null ) {
            $workshop->setCreatedAt($workshopOrder->getCreatedAt());
        }
        if ($workshopOrder->getModifiedBy() != null ) {
            $workshop->setModifiedBy($workshopOrder->getModifiedBy());
        }
        if ($workshopOrder->getModifiedAt() != null ) {
            $workshop->setModifiedAt($workshopOrder->getModifiedAt());
        }
        $workshop->setActive             (true);

        return $workshop;
    }

        //
    /**
     * crea un array con los valores anteriores a la modificacion/rechazo de la solicitud
     * @param  Array $workshopsOrders
     * @return Array
     */
    public static function getWorkshopOrdersBefore($em, $workshopsOrders) {

        $ordersBefore = array();

        foreach ($workshopsOrders as $workshopOrder) {

            if ($workshopOrder->getAction() == 'modify' or $workshopOrder->getAction() == 're_modify') {

                $workshopBefore = $em->getRepository("WorkshopBundle:Workshop")->findOneBy(array('id' => $workshopOrder->getIdWorkshop()));
                $ordersBefore[$workshopOrder->getId()] = $workshopBefore;
            }

            if ($workshopOrder->getAction() == 'rejected' or $workshopOrder->getAction() == 'resend') {

                $workshopBefore = $em->getRepository("OrderBundle:WorkshopOrder")->find($workshopOrder->getId());
                $ordersBefore[$workshopOrder->getId()] = $workshopBefore;
            }
        }
        return $ordersBefore;
    }

    public function findCifAction($cif) {
        $em = $this->getDoctrine()->getEntityManager();
        $workshop = $em->getRepository("WorkshopBundle:Workshop")->findOneByCif($cif);
        $find = false;
        if($workshop){
            $find=true;
        }
        $json = json_encode($find);
        return new Response(json_encode($json), $status = 200);
    }


    /**
     * Genera un pdf con los datos del Taller
     * @Route("/workshop/doPdfWorkshop/{id}")
     * @ParamConverter("workshop", class="WorkshopBundle:Workshop")
     */
    public function doPdfWorkshopAction($workshop) {

        $trans = $this->get('translator');
        $filename = $trans->trans('workshop').'_'.$workshop->getCodePartner().'-'.$workshop->getCodeWorkshop();
        // $filename = $trans->trans('workshop').'_'.$workshop->getCodePartner().'-'.$workshop->getCodeWorkshop().'_'.date("d-m-Y");

        $html = $this->renderView('OrderBundle:WorkshopOrders:order_workshop_pdf.html.twig', array(
            'workshop'  => $workshop
        ));

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="'.$filename.'.pdf"'
            )
        );
    }

}
