<?php

namespace Adservice\WorkshopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Adservice\UtilBundle\Controller\UtilController as UtilController;
use Adservice\UtilBundle\Entity\Pagination;
use Adservice\UserBundle\Entity\User;
use Adservice\WorkshopBundle\Entity\Workshop;
use Adservice\WorkshopBundle\Form\WorkshopType;
use Adservice\WorkshopBundle\Form\WorkshopObservationType;
use Adservice\WorkshopBundle\Form\WorkshopDeactivateObservationType;
use Adservice\WorkshopBundle\Entity\TypologyRepository;
use Adservice\WorkshopBundle\Entity\DiagnosisMachineRepository;
use Adservice\WorkshopBundle\Entity\ADSPlus;
use Adservice\WorkshopBundle\Entity\Historical;
use Adservice\WorkshopBundle\Entity\WorkshopStatusHistory;

class WorkshopController extends Controller {

    /**
     * Devuelve todos los talleres segun el usuario logeado
     * @return type
     * @throws AccessDeniedException
     */
    public function listAction(Request $request, $page = 1, $w_idpartner = '0', $w_id = '0', $country = '0', $catserv = '0', $partner = '0', $status = '0', $term = '0', $field = '0') {

        $em = $this->getDoctrine()->getManager();

        $joins = array();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR') === false and $this->get('security.authorization_checker')->isGranted('ROLE_AD') === false) {
            throw new AccessDeniedException();
        }

        if (in_array($term, array("tel","name","city","mail","internal_code","postal_code","cif","typology","code_workshop")) and $field != '0') {
            switch($term){
                case 'tel':
                    $params[] = array('phone_number_1', " != '0' AND (e.phone_number_1 LIKE '%" . $field . "%' OR e.phone_number_2 LIKE '%" . $field . "%' OR e.mobile_number_1 LIKE '%" . $field . "%' OR e.mobile_number_2 LIKE '%" . $field . "%') ");
                    break;
                case 'mail':
                    $params[] = array('email_1', " != '0' AND (e.email_1 LIKE '%" . $field . "%' OR e.email_2 LIKE '%" . $field . "%') ");
                    break;
                case 'typology':
                    $joins[] = array('e.typology ty ', "ty.id = e.typology AND ty.name LIKE '%". $field."%'");
                    break;
                case 'name':
                    $field = str_replace("'","''",$field);
                case 'code_workshop':
                    $params[] = array($term, " = '" . $field . "'");
                default:
                    $params[] = array($term, " LIKE '%" . $field . "%'");
                    break;
            }                   
        }
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            if ($country != '0')
                $params[] = array('country', ' = ' . $country);
        } else
            $params[] = array('country', ' = ' . $this->getUser()->getCountry()->getId());

        if($catserv != 0)
        {
            $params[] = array('category_service', ' = '.$catserv.' ');
        }

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') ) {
            
            $catser = $this->getUser()->getCategoryService();
             
             
            if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN'))
                $params[] = array('category_service', ' = ' . $catser->getId());

            if ($partner != '0')
                $params[] = array('partner', ' = ' . $partner);

            if ($w_idpartner != '0' and $w_id != '0') {
                $params[] = array('code_workshop', ' = ' . $w_id);
                $workshop = $em->getRepository('WorkshopBundle:Workshop')->findOneBy(array('code_workshop' => $w_id));
                $joins[] = array('e.partner p ', 'p.id = e.partner AND p.code_partner = ' . $w_idpartner . ' ');
            }

            if ($status == "active") {
                $params[] = array('active', ' = 1');
                $params[] = array('test', ' = 0');
            } elseif ($status == "deactive") {
                $params[] = array('active', ' != 1');
            } elseif ($status == "test") {
                $params[] = array('active', ' = 1');
                $params[] = array('test', ' = 1');
            } elseif ($status == "check") {
                $params[] = array('haschecks', ' = 1');
            } elseif ($status == "infotech"){
                $params[] = array('infotech', ' = 1');
            }
        }
        elseif ($this->get('security.authorization_checker')->isGranted('ROLE_TOP_AD') ) {
            $catser = $this->getUser()->getCategoryService();
            if($catser != null)
                $catserv = $catser->getId();
            else
                $catserv = 0;
            
            
            $params[] = array('category_service', ' = ' . $catserv);

            if ($partner != '0')
                $params[] = array('partner', ' = ' . $partner);

            if ($w_idpartner != '0' and $w_id != '0') {
                $params[] = array('code_workshop', ' = ' . $w_id);
                $workshop = $em->getRepository('WorkshopBundle:Workshop')->findOneBy(array('code_workshop' => $w_id));
                $joins[] = array('e.partner p ', 'p.id = e.partner AND p.code_partner = ' . $w_idpartner . ' ');
            }
             
            if ($status == "active")  {
                $params[] = array('active', ' = 1');
                $params[] = array('test', ' = 0');
            } elseif ($status == "deactive") {
                $params[] = array('active', ' != 1');
            } elseif ($status == "test") {
                $params[] = array('active', ' = 1');
                $params[] = array('test', ' = 1');
            } elseif ($status == "check") {
                $params[] = array('haschecks', ' = 1');
            } elseif ($status == "infotech"){
                $params[] = array('infotech', ' = 1');
            }
            
        }
        $ordered = array('e.code_partner, e.code_workshop', 'ASC');
        if (!isset($params))
            $params[] = array();
        $pagination = new Pagination($page);
        
        $workshops = $pagination->getRows($em, 'WorkshopBundle', 'Workshop', $params, $pagination, $ordered, $joins);

        $length = $pagination->getRowsLength($em, 'WorkshopBundle', 'Workshop', $params, $ordered, $joins);

        $pagination->setTotalPagByLength($length);

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $countries = $em->getRepository('UtilBundle:Country')->findAll();
        }
        else
            $countries = array();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {

            if($catserv != 0) $partners = $em->getRepository('PartnerBundle:Partner')->findBy(array('category_service' => $catserv),array('name' => 'ASC'));
            else              $partners = $em->getRepository('PartnerBundle:Partner')->findAll(array('name' => 'ASC'));
        } else {
            $country_id = $this->getUser()->getCountry()->getId();
            if($catserv != 0) $partners = $em->getRepository('PartnerBundle:Partner')->findBy(array('category_service' => $catserv, 'country' => $country_id),array('name' => 'ASC'));
            else              $partners = $em->getRepository('PartnerBundle:Partner')->findBy(array('country' => $country_id), array('name' => 'ASC'));
        }

        $cat_services = $em->getRepository("UserBundle:CategoryService")->findAll();
        $field = str_replace("''","'",$field);
        return $this->render('WorkshopBundle:Workshop:list.html.twig', array('workshops' => $workshops,
                    'pagination'    => $pagination,
                    'w_idpartner'   => $w_idpartner,
                    'w_id'          => $w_id,
                    'countries'     => $countries,
                    'country'       => $country,
                    'cat_services'  => $cat_services,
                    'catserv'       => $catserv,
                    'catser'        => $catser,
                    'partner'       => $partner,
                    'partners'      => $partners,
                    'status'        => $status,
                    'term'          => $term,
                    'field'         => $field,
                    'length'        => $length));
    }

    public function newWorkshopAction(Request $request) {

        if ($this->get('security.authorization_checker')->isGranted('ROLE_TOP_AD') === false)
            throw new AccessDeniedException();
        $em = $this->getDoctrine()->getManager();

        $workshop = new Workshop();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_AD')) {

            $partners = $em->getRepository("PartnerBundle:Partner")->findBy(array('country' => $this->getUser()->getCountry()->getId(),
                'active' => '1'));
        } else
            $partners = '0';

        // Creamos variables de sesion para fitlrar los resultados del formulario
        if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {

            $_SESSION['id_partner'] = ' != 0 ';
            //$_SESSION['id_country'] = ' != 0 ';
            $_SESSION['id_catserv'] = ' != 0 ';
        }
        elseif ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            $_SESSION['id_catserv'] = ' = ' . $this->getUser()->getCategoryService()->getId();
        }
        elseif ($this->get('security.authorization_checker')->isGranted('ROLE_TOP_AD')) {
            $_SESSION['id_catserv'] = ' = 3';
        }
        elseif ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_AD')) {

            $partner_ids = '0';
            foreach ($partners as $p) {
                $partner_ids = $partner_ids . ', ' . $p->getId();
            }

            $_SESSION['id_partner'] = ' IN (' . $partner_ids . ')';
            $_SESSION['id_country'] = ' = ' . $this->getUser()->getCountry()->getId();
        }
       
        else {
            $_SESSION['id_partner'] = ' = ' . $partner->getId();
            $_SESSION['id_country'] = ' = ' . $partner->getCountry()->getId();
        }
        
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) $_SESSION['code_billing'] = 'code_billing';
        else unset($_SESSION['code_billing']);

        $form = $this->createForm(WorkshopType::class, $workshop);

        if ($request->getMethod() == 'POST') {
            
            $form->handleRequest($request);
            if($workshop->getRegion() == null){
                $workshop->setRegion('-');
            }
            $partner_id = $request->request->get('adservice_workshopbundle_workshoptype')['partner'];
            $partner = $em->getRepository("PartnerBundle:Partner")->findOneById($partner_id);
            
            $code_partner = $request->request->get('code_partner');
            $workshop->setCodePartner($code_partner);
            $workshop->setPartner($partner);
            
            $code = UtilController::getCodeWorkshopUnused($em, $workshop->getCodePartner(), $workshop->getCodeWorkshop());  /* OBTIENE EL PRIMER CODIGO DISPONIBLE */

            $workshop->setActive(1);
            /* COMPRUEBA CODE WORKSHOP NO SE REPITA */
           
            $find = $em->getRepository("WorkshopBundle:Workshop")->findOneBy(array('code_partner'  => $workshop->getCodePartner(),
                                                                                   'code_workshop' => $workshop->getCodeWorkshop()));
            $findPhone = array(0, 0, 0, 0);
            if ($workshop->getPhoneNumber1() != null) {
                $findPhone[0] = $em->getRepository("WorkshopBundle:Workshop")->findPhone($workshop->getPhoneNumber1());
            }
            if ($workshop->getPhoneNumber2() != null) {
                $findPhone[1] = $em->getRepository("WorkshopBundle:Workshop")->findPhone($workshop->getPhoneNumber2());
            }
            if ($workshop->getMobileNumber1() != null) {
                $findPhone[2] = $em->getRepository("WorkshopBundle:Workshop")->findPhone($workshop->getMobileNumber1());
            }
            if ($workshop->getMobileNumber2() != null) {
                $findPhone[3] = $em->getRepository("WorkshopBundle:Workshop")->findPhone($workshop->getMobileNumber2());
            }

            if ($find == null and $findPhone[0]['1'] < 1 and $findPhone[1]['1'] < 1 and $findPhone[2]['1'] < 1 and $findPhone[3]['1'] < 1) {
                $workshop = UtilController::newEntity($workshop, $this->getUser());
                $workshop = UtilController::settersContact($workshop, $workshop);
                $workshop->setCodePartner($partner->getCodePartner());
                $workshop->setCodeWorkshop($code);
                $workshop->setCategoryService($partner->getCategoryService());

                if($workshop->getHasChecks() == false and $workshop->getNumChecks() != null) $workshop->setNumChecks(null);
                if($workshop->getHasChecks() == true and $workshop->getNumChecks() == '') $workshop->setNumChecks(0);

                if($workshop->getActive() == true) {
                    $workshop->setUpdateAt(new \DateTime(\date("Y-m-d H:i:s")));
                }else{
                    $workshop->setLowdateAt(new \DateTime(\date("Y-m-d H:i:s")));
                }
                $this->saveWorkshop($em, $workshop);
                $status = 3; // 3 para primera alta
                if($workshop->getTest()){
                    $status = 2;
                }
                if($workshop->getNumChecks() > 5){
                    $workshop->setNumChecks(5);
                }
                UtilController::createHistorical($em, $workshop, $status);
                //Si ha seleccionado AD-Service + lo añadimos a la BBDD correspondiente
                if ($workshop->getAdServicePlus()) {
                    $adsplus = new ADSPlus();
                    $adsplus->setIdTallerADS($workshop->getId());
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

                /* CREAR USERNAME Y EVITAR REPETICIONES */
                $username = UtilController::getUsernameUnused($em, $workshop->getName());

                /* CREAR PASSWORD AUTOMATICAMENTE */
                $pass = substr(md5(microtime()), 1, 8);

                $role = $em->getRepository('UserBundle:Role')->findOneByName('ROLE_USER');
                $lang = $em->getRepository('UtilBundle:Language')->findOneByLanguage($workshop->getCountry()->getLang());

                $newUser = UtilController::newEntity(new User(), $this->getUser());
                $newUser->setUsername($username);
                $newUser->setPassword($pass);
                $newUser->setName($workshop->getContact());
                $newUser->setSurname($workshop->getName());
                $newUser->setActive('1');
                $newUser->setCreatedBy($workshop->getCreatedBy());
                $newUser->setCreatedAt(new \DateTime());
                $newUser->setModifiedBy($workshop->getCreatedBy());
                $newUser->setModifiedAt(new \DateTime());
                $newUser->setLanguage($lang);
                $newUser->setWorkshop($workshop);
                $newUser->addRole($role);
                $newUser->setCategoryService($partner->getCategoryService());

                $newUser = UtilController::settersContact($newUser, $workshop);

                //ad-service +
                //password nuevo, se codifica con el nuevo salt
                $encoder = $this->container->get('security.encoder_factory')->getEncoder($newUser);
                $salt = md5(time());
                $password = $encoder->encodePassword($newUser->getPassword(), $salt);
                $newUser->setPassword($password);
                $newUser->setSalt($salt);

                //Asignamos un Token para AD360
                $token = UtilController::getRandomToken();
                $newUser->setToken($token);

                UtilController::saveEntity($em, $newUser, $this->getUser());

                //$this->createHistoric($em, $workshop); /* Genera un historial de cambios del taller */

                $mail = $newUser->getEmail1();
                $pos = strpos($mail, '@');
                if ($pos != 0) {

                    /* Cambiamos el locale para enviar el mail en el idioma del taller */
                    $locale = $this->get('translator')->getLocale();
                    $lang_w = $em->getRepository('UtilBundle:Language')->findOneByLanguage($workshop->getCountry()->getLang());
                    $this->get('translator')->setLocale($lang_w->getShortName());

                    /* MAILING */
                    $mailerUser = $this->get('cms.mailer');
                    $mailerUser->setTo($mail);
                    $mailerUser->setSubject($this->get('translator')->trans('mail.newUser.subject') . $newUser->getWorkshop());
                    $mailerUser->setFrom('noreply@adserviceticketing.com');
                    $mailerUser->setBody($this->renderView('UtilBundle:Mailing:user_new_mail.html.twig', array('user' => $newUser, 'password' => $pass, '__locale' => $lang_w->getShortName())));
                    $mailerUser->sendMailToSpool();
                    
                    if($workshop->getCategoryService()->getId() == 3) {
                        $mail = $this->container->getParameter('mail_report_ad');
                        $mailCC1 = $this->container->getParameter('mail_admin_1');
                        $mailCC2 = $this->container->getParameter('mail_admin_2');   
                        $mailerUser->setTo($mail);
                        $mailerUser->setCc(array($mailCC1,$mailCC2)); 
                        $mailerUser->sendMailToSpool();

                        $mailTopFr = $this->container->getParameter('mail_top_fr');
                        
                        $mailerUser->setTo($mailTopFr);                        
                        $mailerUser->setCc(null); 
                        $mailerUser->setBody($this->renderView('UtilBundle:Mailing:user_new_mail_top_fr.html.twig', array('user' => $newUser, '__locale' => $lang_w->getShortName())));
                        $mailerUser->sendMailToSpool();
                        
                    } else {
                        $mailReportAd = $this->container->getParameter('mail_report');
                        
                        $mailerUser->setTo($mailReportAd);
                        $mailerUser->sendMailToSpool();
                        
                    }
                    
                    /* Dejamos el locale tal y como estaba */
                    $this->get('translator')->setLocale($locale);
                }

                $flash = $this->get('translator')->trans('create') . ' ' . $this->get('translator')->trans('workshop') . ': ' . $username;
                if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
                {
                    $flash = $flash . ' '. $this->get('translator')->trans('with_password') . ': ' . $pass;
                }
                $this->get('session')->getFlashBag()->add('alert', $flash);

                return $this->redirect($this->generateUrl('workshop_new'));
            } else {

                if ($findPhone[0]['1'] > 0) {
                    $flash = $this->get('translator')->trans('error.code_phone.used') . $workshop->getPhoneNumber1()
                            . ' -> ' . $this->get('translator')->trans('workshop')
                            . ' ' . $em->getRepository("WorkshopBundle:Workshop")->findPhoneGetCode($workshop->getPhoneNumber1());
                } else if ($findPhone[1]['1'] > 0) {
                    $flash = $this->get('translator')->trans('error.code_phone.used') . $workshop->getPhoneNumber2()
                            . ' - ' . $this->get('translator')->trans('workshop')
                            . ' ' . $em->getRepository("WorkshopBundle:Workshop")->findPhoneGetCode($workshop->getPhoneNumber2());
                } else if ($findPhone[2]['1'] > 0) {
                    $flash = $this->get('translator')->trans('error.code_phone.used') . $workshop->getMobileNumber1()
                            . ' - ' . $this->get('translator')->trans('workshop')
                            . ' ' . $em->getRepository("WorkshopBundle:Workshop")->findPhoneGetCode($workshop->getMobileNumber1());
                } else if ($findPhone[3]['1'] > 0) {
                    $flash = $this->get('translator')->trans('error.code_phone.used') . $workshop->getMobileNumber2()
                            . ' - ' . $this->get('translator')->trans('workshop')
                            . ' ' . $em->getRepository("WorkshopBundle:Workshop")->findPhoneGetCode($workshop->getMobileNumber2());
                } else {
                    $flash = $this->get('translator')->trans('error.code_workshop.used') . $code;
                }
                $this->get('session')->getFlashBag()->add('error', $flash);
            }
            
        }

        if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')){
            //$country = $this->getUser()->getCountry()->getId();
            $catserv = null;
        }
        else{
            $catserv = $this->getUser()->getCategoryService();
        }
        //$typologies = TypologyRepository::findTypologiesList($em, $country);
        //$diagnosis_machines = DiagnosisMachineRepository::findDiagnosisMachinesList($em, $country);

        return $this->render('WorkshopBundle:Workshop:new_workshop.html.twig', array('workshop' => $workshop,
                    //'typologies' => $typologies,
                    //'diagnosis_machines' => $diagnosis_machines,
                    'form_name' => $form->getName(),
                    'form' => $form->createView(),
                    'catserv' => $catserv,
        ));
    }

    /**
     * Obtener los datos del workshop a partir de us ID para poder editarlo (solo lo puede hacer el ROLE_ADMIN)
     * @Route("/edit/{id}")
     * @ParamConverter("workshop", class="WorkshopBundle:Workshop")
     * Si la petición es GET  --> mostrar el formulario
     * Si la petición es POST --> save del formulario
     */
    public function editWorkshopAction(Request $request, $workshop)
    {

        if ((!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) and ( $this->get('security.authorization_checker')->isGranted('ROLE_AD') and ( $this->getUser()->getPartner() != null and $this->getUser()->getPartner()->getId() == $workshop->getPartner()->getId()) === false)
                and ( $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_AD') and ( $this->getUser()->getCountry()->getId() == $workshop->getCountry()->getId()) === false)) {
            return $this->render('TwigBundle:Exception:exception_access.html.twig');
        }

        $em = $this->getDoctrine()->getManager();
        $partner = $workshop->getPartner();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_AD')) {

            $partners = $em->getRepository("PartnerBundle:Partner")->findBy(array('country' => $this->getUser()->getCountry()->getId(),
                'active' => '1'));
        } else
            $partners = '0';

        // Creamos variables de sesion para fitlrar los resultados del formulario
        if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {

            $_SESSION['id_partner'] = ' != 0 ';
            $_SESSION['id_country'] = ' = ' . $workshop->getCountry()->getId();
        } 
        elseif ($this->get('security.authorization_checker')->isGranted('ROLE_TOP_AD')){
            $_SESSION['id_catserv'] = ' = ' . $this->getUser()->getCategoryService()->getId();
        }
        elseif ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_AD')) {

            $partner_ids = '0';
            foreach ($partners as $p) {
                $partner_ids = $partner_ids . ', ' . $p->getId();
            }

            $_SESSION['id_partner'] = ' IN (' . $partner_ids . ')';
            $_SESSION['id_country'] = ' = ' . $this->getUser()->getCountry()->getId();
        } else {
            $_SESSION['id_partner'] = ' = ' . $partner->getId();
            $_SESSION['id_country'] = ' = ' . $partner->getCountry()->getId();
        }
        
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) $_SESSION['code_billing'] = 'code_billing';
        else unset($_SESSION['code_billing']);
        
        $form = $this->createForm(WorkshopType::class, $workshop);

        $actual_city = $workshop->getRegion();
        $actual_region = $workshop->getCity();
        $actual_test = $workshop->getTest();
        if ($request->getMethod() == 'POST') {
            $last_code = $workshop->getCodeWorkshop();
            $form->handleRequest($request);
            //if ($form->isValid()) {
            if($request->request->has("btn_reset_token")) {
                
                $token = UtilController::getRandomToken();
                $user = $em->getRepository("UserBundle:User")->findOneBy(array('workshop' => $workshop->getId()));
                $user->setToken($token);
                $em->persist($user);
                $em->flush();
            }
            else {
                /* CHECK CODE WORKSHOP NO SE REPITA */
                $find = $em->getRepository("WorkshopBundle:Workshop")->findOneBy(array('code_partner'  => $partner->getCodePartner(),
                                                                                       'code_workshop' => $workshop->getCodeWorkshop()));

                //Comprobar telefono
                $findPhone = array(0, 0, 0, 0);

                if ($workshop->getPhoneNumber1() != '0' and $workshop->getPhoneNumber1() != null) {
                    $findPhone[0] = $em->getRepository("WorkshopBundle:Workshop")->findPhoneNoId($workshop->getPhoneNumber1(), $workshop->getId());
                }
                if ($workshop->getPhoneNumber2() != '0' and $workshop->getPhoneNumber2() != null) {
                    $findPhone[1] = $em->getRepository("WorkshopBundle:Workshop")->findPhoneNoId($workshop->getPhoneNumber2(), $workshop->getId());
                }
                if ($workshop->getMobileNumber1() != '0' and $workshop->getMobileNumber1() != null) {
                    $findPhone[2] = $em->getRepository("WorkshopBundle:Workshop")->findPhoneNoId($workshop->getMobileNumber1(), $workshop->getId());
                }
                if ($workshop->getMobileNumber2() != '0' and $workshop->getMobileNumber2() != null) {
                    $findPhone[3] = $em->getRepository("WorkshopBundle:Workshop")->findPhoneNoId($workshop->getMobileNumber2(), $workshop->getId());
                }
                
                if (($find == null or $workshop->getCodeWorkshop() == $last_code ) and $findPhone[0]['1'] < 1 and $findPhone[1]['1'] < 1 and $findPhone[2]['1'] < 1 and $findPhone[3]['1'] < 1) {
                    $workshop = UtilController::settersContact($workshop, $workshop, $actual_region, $actual_city);

                    $code_partner = $workshop->getPartner()->getCodePartner();
                    $workshop->setCodePartner($code_partner);

                    // Set default shop to NULL
                    $shop = $workshop->getShop();
                    if (isset($shop) && $shop->getId() == 0) {
                        $workshop->setShop(null);
                    }

                    //$this->createHistoric($em, $workshop); /* Genera un historial de cambios del taller */

                    if($workshop->getHasChecks() == false and $workshop->getNumChecks() != null) $workshop->setNumChecks(null);
                    if($workshop->getHasChecks() == true and $workshop->getNumChecks() == '') $workshop->setNumChecks(0);
                    if($workshop->getNumChecks() > 5){
                        $workshop->setNumChecks(5);
                    }
                    $this->saveWorkshop($em, $workshop);
                    $status = 1;
                    if($workshop->getTest()){
                        $status = 2;
                    }
                    if($actual_test != $workshop->getTest()){
                        UtilController::createHistorical($em, $workshop, $status);
                    }
                    if ($this->get('security.authorization_checker')->isGranted('ROLE_TOP_AD'))
                        return $this->redirect($this->generateUrl('workshop_list'));
                    elseif ($this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR'))
                        return $this->redirect($this->generateUrl('listTicket'));
                }
                else {
                    if ($findPhone[0]['1'] > 0) {
                        $flash = $this->get('translator')->trans('error.code_phone.used') . $workshop->getPhoneNumber1()
                                . ' -> ' . $this->get('translator')->trans('workshop')
                                . ' ' . $em->getRepository("WorkshopBundle:Workshop")->findPhoneNoIdGetCode($workshop->getPhoneNumber1(), $workshop->getId());
                    } else if ($findPhone[1]['1'] > 0) {
                        $flash = $this->get('translator')->trans('error.code_phone.used') . $workshop->getPhoneNumber2()
                                . ' - ' . $this->get('translator')->trans('workshop')
                                . ' ' . $em->getRepository("WorkshopBundle:Workshop")->findPhoneGetCode($workshop->getPhoneNumber2(), $workshop->getId());
                    } else if ($findPhone[2]['1'] > 0) {
                        $flash = $this->get('translator')->trans('error.code_phone.used') . $workshop->getMobileNumber1()
                                . ' - ' . $this->get('translator')->trans('workshop')
                                . ' ' . $em->getRepository("WorkshopBundle:Workshop")->findPhoneGetCode($workshop->getMobileNumber1(), $workshop->getId());
                    } else if ($findPhone[3]['1'] > 0) {
                        $flash = $this->get('translator')->trans('error.code_phone.used') . $workshop->getMobileNumber2()
                                . ' - ' . $this->get('translator')->trans('workshop')
                                . ' ' . $em->getRepository("WorkshopBundle:Workshop")->findPhoneGetCode($workshop->getMobileNumber2(), $workshop->getId());
                    } else {
                        $code = UtilController::getCodeWorkshopUnused($em, $partner->getCodePartner());        /* OBTIENE EL PRIMER CODIGO DISPONIBLE */
                        $flash = $this->get('translator')->trans('error.code_workshop.used') . $code . ' (valor actual ' . $last_code . ').';
                    }
                    $this->get('session')->getFlashBag()->add('error', $flash);
                }
            }
            //}
        }

        $country = $workshop->getCountry()->getId();

        $typologies = TypologyRepository::findTypologiesList($em, $country);
        $diagnosis_machines = DiagnosisMachineRepository::findDiagnosisMachinesList($em, $country);
        $workshop_machines = $workshop->getDiagnosisMachines();

        // if($workshop_machines[0] and !isset($id_machine)){
        //     $id_machine = $workshop_machines[0];
        //     $id_machine = $id_machine->getId();
        // }

        return $this->render('WorkshopBundle:Workshop:edit_workshop.html.twig', array('workshop' => $workshop,
                    'typologies' => $typologies,
                    // 'id_machine'         => $id_machine,
                    'diagnosis_machines' => $diagnosis_machines,
                    'form_name' => $form->getName(),
                    'form' => $form->createView()));
    }

    public function deleteWorkshopAction(Request $request, $id) {

        if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') === false) {
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager();
        $workshop = $em->getRepository("WorkshopBundle:Workshop")->find($id);
        if (!$workshop)
            throw $this->createNotFoundException('Workshop no encontrado en la BBDD');
        $user= $workshop->getUsers()[0];
        $tickets = $workshop->getTickets();
        if(isset($tickets)){
            foreach ($tickets as $ticket) {
                $posts = $ticket->getPosts();
                if(isset($posts)){
                    foreach ($posts as $post) {
                        $document = $em->getRepository("UtilBundle:Document")->findOneById($post->getDocument());
                        if(isset($document)){
                                $em->remove($document);
                                $em->flush();
                        }
                        $em->remove($post);
                        $em->flush();
                    }
                }
                $em->remove($ticket);
                $em->flush();
            }
        }
        $usertmp = $em->getRepository("UserBundle:User")->findOneById(1);
        $workshops = $em->getRepository("WorkshopBundle:Workshop")->findBy(array('modified_by' => $user->getId()));
        if(isset($workshops)){
            foreach($workshops as $workshop){
                $workshop->setModifiedBy($usertmp);
                $workshop->setCreatedBy($usertmp);
                $em->persist($workshop);
                $em->flush();
            }
        }
        if(isset($user)){
           $cars = $em->getRepository("CarBundle:Car")->findBy(array('modified_by' => $user->getId()));
           if(isset($cars)){
               foreach($cars as $car){
                   $car->setModifiedBy($usertmp);
                   $em->persist($car);
                    $em->flush();
                }
            }
            $cars = $em->getRepository("CarBundle:Car")->findBy(array('created_by' => $user->getId()));
            if(isset($cars)){
                foreach($cars as $car){
                    $car->setCreatedBy($usertmp);
                    $em->persist($car);
                    $em->flush();
                }
                $em->remove($user);
                $em->flush();
            }
        }
        $em->remove($workshop);
        $em->flush();
        return $this->redirect($this->generateUrl('workshop_list'));
    }

    /**
     * Edita las observaciones para asesor del taller
     * @Route("/workshop/observation/{id}/{id_ticket}")
     * @ParamConverter("workshop", class="WorkshopBundle:Workshop")
     * @return url
     */
    public function workshopObservationAction(Request $request, $workshop, $id_ticket) {

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR') === false) {
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(WorkshopObservationType::class, $workshop);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $em->persist($workshop);
                $em->flush();
                return $this->redirect($this->generateUrl('showTicket', array('id' => $id_ticket)));
            }
        }
        return $this->render('WorkshopBundle:Workshop:workshop_observation.html.twig', array('workshop' => $workshop,
                    'id_ticket' => $id_ticket,
                    'form_name' => $form->getName(),
                    'form' => $form->createView()));
    }

    public function workshopDeactivateObservationAction(Request $request, $workshop_id)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_TOP_AD') === false) {
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager();
        
        $workshop = $em->getRepository('WorkshopBundle:Workshop')->findOneById($workshop_id);
        if($workshop)
        {

            $form = $this->createForm(WorkshopDeactivateObservationType::class, $workshop);

            if ($request->getMethod() == 'POST') {
                $form->handleRequest($request);

                if ($form->isValid()) {

                    $em->persist($workshop);
                    $em->flush();

                    $this->deactivateActivateWorkshopAction($request, $workshop->getId());

                    return $this->redirect($this->generateUrl('workshop_list'));
                }
            }
            return $this->render('WorkshopBundle:Workshop:workshop_deactivate_observation.html.twig', array('workshop' => $workshop,
                        'form_name' => $form->getName(),
                        'form' => $form->createView()));
        }
        else
        {
            return $this->redirect($this->generateUrl('workshop_list'));
        }
    }

    public function deactivateActivateWorkshopAction(Request $request,$workshop_id)
    {
        $em = $this->getDoctrine()->getManager();
        $workshop = $em->getRepository('WorkshopBundle:Workshop')->findOneById($workshop_id);
        if($workshop)
        {
            $workshop->setActive(!$workshop->getActive());
            $workshop->getUsers()[0]->setActive($workshop->getActive());
            $new_date = new \DateTime(\date("Y-m-d H:i:s"));
            if($workshop->getActive() == true)
            {
                $workshop->setUpdateAt($new_date);
                $status = 1;
            }
            else
            {
                $workshop->setLowdateAt($new_date);
                
                if($workshop->getEndTestAt()!=null && strtotime($new_date->format("Y-m-d H:i:s"))< strtotime($workshop->getEndTestAt()->format("Y-m-d H:i:s")) )
                    {
                        $workshop->setEndTestAt($new_date);
                        $workshop->setTest(0);
                    }
                
                $status = 0;
                // Cerramos todos los tickets del taller deshabilitado
                    //$tickets = $em->getRepository('TicketBundle:Ticket')->findBy(array('workshop' => $workshop->getId()));
                    //$unsubscribed = $this->get('translator')->trans('closed_by_unsubscription');

                    //$ids = '0';
                    //foreach ($tickets as $ticket) { $ids .= ', '.$ticket->getId(); }

                    //$consulta = $em->createQuery("UPDATE TicketBundle:Ticket t SET t.status = 2, t.solution = '".$unsubscribed."' WHERE t.id IN (".$ids.")");
                    //$consulta->getResult();
            }
            $workshop->setModifiedAt(new \DateTime(\date("Y-m-d H:i:s")));
            $workshop->setModifiedBy($this->getUser());
        }
       
        if($workshop->getTest() && $status == 1){
            $status = 2;
        }
        UtilController::createHistorical($em, $workshop, $status);

         /* MAILING */
        //Mail to workshop
        $mail = $workshop->getEmail1();
        $action = 'deactivate';
        $locale = $this->get('translator')->getLocale();
        $lang_w = $em->getRepository('UtilBundle:Language')->findOneByLanguage($workshop->getCountry()->getLang());
        $this->get('translator')->setLocale($lang_w->getShortName());
        $mailerUser = $this->get('cms.mailer');
        $mailerUser->setSubject($this->get('translator')->trans('mail.deactivateWorkshop.subject').$workshop->getName());
        if($workshop->getActive()== true)
        {
            $action = 'activate';
            $mailerUser->setSubject($this->get('translator')->trans('mail.activateWorkshop.subject').$workshop->getName());
        }
        $mailerUser->setTo($mail);
        $mailerUser->setFrom('noreply@adserviceticketing.com');
        $mailerUser->setBody($this->renderView('UtilBundle:Mailing:order_accept_mail.html.twig', array('workshop' => $workshop, 'action'=> $action, '__locale' => $lang_w->getShortName())));
        $mailerUser->sendMailToSpool();

        if($workshop->getCategoryService()->getId() == 3) {
            $mail = $this->container->getParameter('mail_report_ad');
            $mailCC1 = $this->container->getParameter('mail_admin_1');
            $mailCC2 = $this->container->getParameter('mail_admin_2'); 
            
            $mailerUser->setTo($mail);
            $mailerUser->setCc(array($mailCC1,$mailCC2));   
            $mailerUser->sendMailToSpool();
            
            $mailTopFr = $this->container->getParameter('mail_top_fr');
            
            $mailerUser->setTo($mailTopFr);
            $mailerUser->setCc(null); 
            $mailerUser->setBody($this->renderView('UtilBundle:Mailing:user_status_mail_top_fr.html.twig', array('workshop' => $workshop, 'action'=> $action, '__locale' => $lang_w->getShortName())));
            $mailerUser->sendMailToSpool();
            
        } else {
            $mailReportAd = $this->container->getParameter('mail_report');

            $mailerUser->setTo($mailReportAd);
            $mailerUser->sendMailToSpool();
            
        }
        
        $this->get('translator')->setLocale($locale);
        return $this->redirect($this->generateUrl('workshop_list'));
    }

    public function insertCifAction(Request $request, $workshop_id, $country) {

        if ($request->getMethod() == 'POST') {
            $em = $this->getDoctrine()->getManager();
            $workshop = $em->getRepository("WorkshopBundle:Workshop")->findOneById($workshop_id);
            $locale = $workshop->getCountry()->getId();
            $CIF = $request->request->get('new_car_form_CIF');
            if ($CIF == '0' || empty($CIF)) {
                $flash = $this->get('translator')->trans('workshop.cif_error2');
                $this->get('session')->getFlashBag()->add('error', $flash);
            }
            else {
                $result = 1;
                if($locale == 1){
                    $validador = $this->get('validadorCIF');
                    $result = $validador->check_cif($CIF);
                }
                if ($result > 0) {
                    $tmp_workshop = $em->getRepository("WorkshopBundle:Workshop")->findByCif($CIF);

                    if (count($tmp_workshop) > 0) {
                        //$flash =  $this->get('translator')->trans('create').' '.$this->get('translator')->trans('user').': '.$username;
                        $flash = $this->get('translator')->trans('workshop.cif_error');
                        $this->get('session')->getFlashBag()->add('error', $flash);
                    } else {
                        $workshop->setCIF($CIF);
                        $this->saveWorkshop($em, $workshop);

                        $currentPath = $this->generateUrl('listTicket', array('page' => 1,
                            'num_rows' => 10,
                            'country' => $country));
                        return $this->redirect($currentPath);
                    }
                } else {
                    $flash = $this->get('translator')->trans('workshop.cif_no_valido');
                    $this->get('session')->getFlashBag()->add('error', $flash);
                }
            }
        }
        return $this->render('TicketBundle:Workshop:insert_cif.html.twig', array('workshop_id' => $workshop_id, 'country' => $country));
    }

    public function findCifAction(Request $request, $cif) {
        $em = $this->getDoctrine()->getManager();
        $workshop = $em->getRepository("WorkshopBundle:Workshop")->findOneByCif($cif);
        $find = false;
        if ($workshop) {
            $find = true;
        }
        $json = json_encode($find);
        return new Response(json_encode($json), $status = 200);
    }

    /**
     * Genera un historial de cambios del taller
     * @return WorkshopHistory
     */
    /*public function createHistoric($em, $workshop) {

        $history = new WorkshopStatusHistory();

        $history->setCodeWorkshop($workshop->getCodeWorkshop());
        $history->setName($workshop->getName());
        $history->setCif($workshop->getCif());
        $history->setContact($workshop->getContact());
        $history->setPartner($workshop->getPartner());
        $history->setShop($workshop->getShop());
        $history->setInternalCode($workshop->getInternalCode());
        $history->setActive($workshop->getActive());
        $history->setAdServicePlus($workshop->getAdServicePlus());
        $history->setTest($workshop->getTest());
        $history->setUpdateAt($workshop->getUpdateAt());
        $history->setLowdateAt($workshop->getLowdateAt());
        $history->setEndtestAt($workshop->getEndtestAt());
        $history->setConflictive($workshop->getConflictive());
        $history->setObservationWorkshop($workshop->getObservationWorkshop());
        $history->setObservationAssessor($workshop->getObservationAssessor());
        $history->setObservationAdmin($workshop->getObservationAdmin());
        $history->setModifiedAt($workshop->getModifiedAt());
        $history->setModifiedBy($workshop->getModifiedBy());
        $history->setCreatedAt($workshop->getCreatedAt());
        $history->setCreatedBy($workshop->getCreatedBy());

        $em->persist($history);
        $em->flush();
        }*/
   
    /**
     * Hace el save de un workshop
     * @param EntityManager $em
     * @param Workshop $workshop
     */
    private function saveWorkshop($em, $workshop) {

        if($workshop->getId() != null) {
            $user = $em->getRepository('UserBundle:User')->findOneByWorkshop($workshop->getId());

            $user = UtilController::saveUserFromWorkshop($workshop,$user);
            $user->setName($workshop->getContact());
            $user->setActive($workshop->getActive());

            $em->persist($user);
        }

        $workshop->setModifiedAt(new \DateTime(\date("Y-m-d H:i:s")));
        $workshop->setModifiedBy($this->getUser());

        $em->persist($workshop);
        $em->flush();
    }



}
