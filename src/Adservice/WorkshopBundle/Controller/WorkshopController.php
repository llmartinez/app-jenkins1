<?php

namespace Adservice\WorkshopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Adservice\UtilBundle\Controller\UtilController as UtilController;
use Adservice\UtilBundle\Entity\Pagination;
use Adservice\UserBundle\Entity\User;
use Adservice\WorkshopBundle\Entity\Workshop;
use Adservice\WorkshopBundle\Form\WorkshopType;
use Adservice\WorkshopBundle\Form\WorkshopObservationType;
use Adservice\WorkshopBundle\Entity\TypologyRepository;
use Adservice\WorkshopBundle\Entity\DiagnosisMachineRepository;
use Adservice\WorkshopBundle\Entity\ADSPlus;
use Adservice\WorkshopBundle\Entity\WorkshopStatusHistory;

class WorkshopController extends Controller {

    /**
     * Devuelve todos los talleres segun el usuario logeado
     * @return type
     * @throws AccessDeniedException
     */
    public function listAction($page = 1, $w_idpartner = '0', $w_id = '0', $country = '0', $partner = '0', $status = '0', $term = '0', $field = '0') {

        $em = $this->getDoctrine()->getEntityManager();
        $security = $this->get('security.context');
        $joins = array();

        if ($security->isGranted('ROLE_ASSESSOR') === false and $security->isGranted('ROLE_AD') === false) {
            throw new AccessDeniedException();
        }

        if ($term != '0' and $field != '0') {

            if ($term == 'tel') {
                $params[] = array('phone_number_1', " != '0' AND (e.phone_number_1 LIKE '%" . $field . "%' OR e.phone_number_2 LIKE '%" . $field . "%' OR e.movile_number_1 LIKE '%" . $field . "%' OR e.movile_number_2 LIKE '%" . $field . "%') ");
            } elseif ($term == 'mail') {
                $params[] = array('email_1', " != '0' AND (e.email_1 LIKE '%" . $field . "%' OR e.email_2 LIKE '%" . $field . "%') ");
            } elseif ($term == 'name') {
                $params[] = array($term, " LIKE '%" . $field . "%'");
            } elseif ($term == 'cif') {
                $params[] = array($term, " LIKE '%" . $field . "%'");
            }
        }
        if ($security->isGranted('ROLE_SUPER_ADMIN')) {
            if ($country != '0')
                $params[] = array('country', ' = ' . $country);
        } else
            $params[] = array('country', ' = ' . $security->getToken()->getUser()->getCountry()->getId());

        if ($security->isGranted('ROLE_ADMIN')) {

            if ($partner != '0')
                $params[] = array('partner', ' = ' . $partner);

            if ($w_idpartner != '0' and $w_id != '0') {
                $params[] = array('code_workshop', ' = ' . $w_id);
                $workshop = $em->getRepository('WorkshopBundle:Workshop')->findOneBy(array('code_workshop' => $w_id));
                $joins[] = array('e.partner p ', 'p.id = e.partner AND p.code_partner = ' . $w_idpartner . ' ');
            }

            if ($status == "active") {
                $params[] = array('active', ' = 1');
            } elseif ($status == "deactive") {
                $params[] = array('active', ' != 1');
            }
        }

        if (!isset($params))
            $params[] = array();
        $pagination = new Pagination($page);

        $workshops = $pagination->getRows($em, 'WorkshopBundle', 'Workshop', $params, $pagination, null, $joins);

        $length = $pagination->getRowsLength($em, 'WorkshopBundle', 'Workshop', $params, null, $joins);

        $pagination->setTotalPagByLength($length);

        if ($security->isGranted('ROLE_SUPER_ADMIN')) {
            $countries = $em->getRepository('UtilBundle:Country')->findAll();
            $partners = $em->getRepository('PartnerBundle:Partner')->findAll();
        } else {
            $countries = array();
            $partners = $em->getRepository('PartnerBundle:Partner')->findByCountry($security->getToken()->getUser()->getCountry()->getId());
        }

        return $this->render('WorkshopBundle:Workshop:list.html.twig', array('workshops' => $workshops,
                    'pagination' => $pagination,
                    'countries' => $countries,
                    'partners' => $partners,
                    'w_idpartner' => $w_idpartner,
                    'w_id' => $w_id,
                    'country' => $country,
                    'partner' => $partner,
                    'status' => $status,
                    'term' => $term,
                    'field' => $field));
    }

    public function newWorkshopAction() {
        $security = $this->get('security.context');
        if ($security->isGranted('ROLE_ADMIN') === false)
            throw new AccessDeniedException();
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        $workshop = new Workshop();

        if ($security->isGranted('ROLE_SUPER_AD')) {

            $partners = $em->getRepository("PartnerBundle:Partner")->findBy(array('country' => $security->getToken()->getUser()->getCountry()->getId(),
                'active' => '1'));
        } else
            $partners = '0';

        // Creamos variables de sesion para fitlrar los resultados del formulario
        if ($security->isGranted('ROLE_SUPER_ADMIN')) {

            $_SESSION['id_partner'] = ' != 0 ';
            $_SESSION['id_country'] = ' != 0 ';
        } elseif ($security->isGranted('ROLE_SUPER_AD')) {

            $partner_ids = '0';
            foreach ($partners as $p) {
                $partner_ids = $partner_ids . ', ' . $p->getId();
            }

            $_SESSION['id_partner'] = ' IN (' . $partner_ids . ')';
            $_SESSION['id_country'] = ' = ' . $security->getToken()->getUser()->getCountry()->getId();
        } else {
            $_SESSION['id_partner'] = ' = ' . $partner->getId();
            $_SESSION['id_country'] = ' = ' . $partner->getCountry()->getId();
        }

        $form = $this->createForm(new WorkshopType(), $workshop);

        if ($request->getMethod() == 'POST') {

            $form->bindRequest($request);
            $partner = $workshop->getPartner();
            $code = UtilController::getCodeWorkshopUnused($em, $partner);        /* OBTIENE EL PRIMER CODIGO DISPONIBLE */

            if ($form->isValid()) {

                /* CHECK CODE WORKSHOP NO SE REPITA */
                $find = $em->getRepository("WorkshopBundle:Workshop")->findOneBy(array('partner' => $partner->getId(),
                    'code_workshop' => $workshop->getCodeWorkshop()));
                $findPhone = array(0, 0, 0, 0);
                if ($workshop->getPhoneNumber1() != null) {
                    $findPhone[0] = $em->getRepository("WorkshopBundle:Workshop")->findPhone($workshop->getPhoneNumber1());
                }
                if ($workshop->getPhoneNumber2() != null) {
                    $findPhone[1] = $em->getRepository("WorkshopBundle:Workshop")->findPhone($workshop->getPhoneNumber2());
                }
                if ($workshop->getMovileNumber1() != null) {
                    $findPhone[2] = $em->getRepository("WorkshopBundle:Workshop")->findPhone($workshop->getMovileNumber1());
                }
                if ($workshop->getMovileNumber2() != null) {
                    $findPhone[3] = $em->getRepository("WorkshopBundle:Workshop")->findPhone($workshop->getMovileNumber2());
                }

                if ($find == null and $findPhone[0]['1'] < 1 and $findPhone[1]['1'] < 1 and $findPhone[2]['1'] < 1 and $findPhone[3]['1'] < 1) {
                    $workshop = UtilController::newEntity($workshop, $security->getToken()->getUser());
                    $workshop = UtilController::settersContact($workshop, $workshop);
                    $workshop->setCodePartner($partner->getCodePartner());
                    $workshop->setCodeWorkshop($code);
                    $this->saveWorkshop($em, $workshop);

                    //Si ha seleccionado AD-Service + lo añadimos a la BBDD correspondiente
                    if ($workshop->getAdServicePlus()) {
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

                    /* CREAR USERNAME Y EVITAR REPETICIONES */
                    $username = UtilController::getUsernameUnused($em, $workshop->getName());

                    /* CREAR PASSWORD AUTOMATICAMENTE */
                    $pass = substr(md5(microtime()), 1, 8);

                    $role = $em->getRepository('UserBundle:Role')->findOneByName('ROLE_USER');
                    $lang = $em->getRepository('UtilBundle:Language')->findOneByLanguage($workshop->getCountry()->getLang());

                    $newUser = UtilController::newEntity(new User(), $security->getToken()->getUser());
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

                    $newUser = UtilController::settersContact($newUser, $workshop);

                    //ad-service +
                    //password nuevo, se codifica con el nuevo salt
                    $encoder = $this->container->get('security.encoder_factory')->getEncoder($newUser);
                    $salt = md5(time());
                    $password = $encoder->encodePassword($newUser->getPassword(), $salt);
                    $newUser->setPassword($password);
                    $newUser->setSalt($salt);
                    UtilController::saveEntity($em, $newUser, $this->get('security.context')->getToken()->getUser());

                    $this->createHistoric($em, $workshop); /* Genera un historial de cambios del taller */


                    // $mail = $newUser->getEmail1();
                    $mail = $this->container->getParameter('mail_db');
                    $pos = strpos($mail, '@');
                    if ($pos != 0) {

                        /* Cambiamos el locale para enviar el mail en el idioma del taller */
                        $locale = $request->getLocale();
                        $lang_u = $newUser->getCountry()->getLang();
                        $lang = $em->getRepository('UtilBundle:Language')->findOneByLanguage($lang_u);
                        $request->setLocale($lang->getShortName());

                        /* MAILING */
                        $mailerUser = $this->get('cms.mailer');
                        $mailerUser->setTo($mail);
                        $mailerUser->setSubject($this->get('translator')->trans('mail.newUser.subject') . $newUser->getWorkshop());
                        $mailerUser->setFrom('noreply@adserviceticketing.com');
                        $mailerUser->setBody($this->renderView('UtilBundle:Mailing:user_new_mail.html.twig', array('user' => $newUser, 'password' => $pass)));
                        $mailerUser->sendMailToSpool();
                        //echo $this->renderView('UtilBundle:Mailing:user_new_mail.html.twig', array('user' => $newUser, 'password' => $pass));die;

                        /* Dejamos el locale tal y como estaba */
                        $request->setLocale($locale);
                    }


                    $flash = $this->get('translator')->trans('create') . ' ' . $this->get('translator')->trans('workshop') . ': ' . $username . ' ' . $this->get('translator')->trans('with_password') . ': ' . $pass;
                    $this->get('session')->setFlash('alert', $flash);

                    return $this->redirect($this->generateUrl('workshop_list'));
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
                        $flash = $this->get('translator')->trans('error.code_phone.used') . $workshop->getMovileNumber1()
                                . ' - ' . $this->get('translator')->trans('workshop')
                                . ' ' . $em->getRepository("WorkshopBundle:Workshop")->findPhoneGetCode($workshop->getMovileNumber1());
                    } else if ($findPhone[3]['1'] > 0) {
                        $flash = $this->get('translator')->trans('error.code_phone.used') . $workshop->getMovileNumber2()
                                . ' - ' . $this->get('translator')->trans('workshop')
                                . ' ' . $em->getRepository("WorkshopBundle:Workshop")->findPhoneGetCode($workshop->getMovileNumber2());
                    } else {
                        $flash = $this->get('translator')->trans('error.code_workshop.used') . $code;
                    }
                    $this->get('session')->setFlash('error', $flash);
                }
            }
        }

        if (!$security->isGranted('ROLE_SUPER_ADMIN'))
            $country = $security->getToken()->getUser()->getCountry()->getId();
        else
            $country = null;
        $typologies = TypologyRepository::findTypologiesList($em, $country);
        $diagnosis_machines = DiagnosisMachineRepository::findDiagnosisMachinesList($em, $country);

        return $this->render('WorkshopBundle:Workshop:new_workshop.html.twig', array('workshop' => $workshop,
                    'typologies' => $typologies,
                    'diagnosis_machines' => $diagnosis_machines,
                    'form_name' => $form->getName(),
                    'form' => $form->createView(),
                        // 'locations'          => UtilController::getLocations($em),
        ));
    }

    /**
     * Obtener los datos del workshop a partir de us ID para poder editarlo (solo lo puede hacer el ROLE_ADMIN)
     * @Route("/edit/{id}")
     * @ParamConverter("workshop", class="WorkshopBundle:Workshop")
     * Si la petición es GET  --> mostrar el formulario
     * Si la petición es POST --> save del formulario
     */
    public function editWorkshopAction($workshop) {
        $security = $this->get('security.context');
        $request = $this->getRequest();

        if ((!$security->isGranted('ROLE_SUPER_ADMIN')) and ( $security->isGranted('ROLE_AD') and ( $security->getToken()->getUser()->getPartner() != null and $security->getToken()->getUser()->getPartner()->getId() == $workshop->getPartner()->getId()) === false)
                and ( $security->isGranted('ROLE_SUPER_AD') and ( $security->getToken()->getUser()->getCountry()->getId() == $workshop->getCountry()->getId()) === false)) {
            return $this->render('TwigBundle:Exception:exception_access.html.twig');
        }

        $em = $this->getDoctrine()->getEntityManager();
        $partner = $workshop->getPartner();

        $petition = $this->getRequest();
        if ($security->isGranted('ROLE_SUPER_AD')) {

            $partners = $em->getRepository("PartnerBundle:Partner")->findBy(array('country' => $security->getToken()->getUser()->getCountry()->getId(),
                'active' => '1'));
        } else
            $partners = '0';

        // Creamos variables de sesion para fitlrar los resultados del formulario
        if ($security->isGranted('ROLE_SUPER_ADMIN')) {

            $_SESSION['id_partner'] = ' != 0 ';
            $_SESSION['id_country'] = ' = ' . $workshop->getCountry()->getId();
        } elseif ($security->isGranted('ROLE_SUPER_AD')) {

            $partner_ids = '0';
            foreach ($partners as $p) {
                $partner_ids = $partner_ids . ', ' . $p->getId();
            }

            $_SESSION['id_partner'] = ' IN (' . $partner_ids . ')';
            $_SESSION['id_country'] = ' = ' . $security->getToken()->getUser()->getCountry()->getId();
        } else {
            $_SESSION['id_partner'] = ' = ' . $partner->getId();
            $_SESSION['id_country'] = ' = ' . $partner->getCountry()->getId();
        }
        $form = $this->createForm(new WorkshopType(), $workshop);

        $actual_city = $workshop->getRegion();
        $actual_region = $workshop->getCity();

        if ($petition->getMethod() == 'POST') {
            $last_code = $workshop->getCodeWorkshop();
            $form->bindRequest($petition);

            if ($form->isValid()) {

                /* CHECK CODE WORKSHOP NO SE REPITA */
                $find = $em->getRepository("WorkshopBundle:Workshop")->findOneBy(array('partner' => $partner->getId(), 'code_workshop' => $workshop->getCodeWorkshop()));
//Comprobar telefono

                $findPhone = array(0, 0, 0, 0);

                if ($workshop->getPhoneNumber1() != '0' and $workshop->getPhoneNumber1() != null) {
                    $findPhone[0] = $em->getRepository("WorkshopBundle:Workshop")->findPhoneNoId($workshop->getPhoneNumber1(), $workshop->getId());
                }
                if ($workshop->getPhoneNumber2() != '0' and $workshop->getPhoneNumber2() != null) {
                    $findPhone[1] = $em->getRepository("WorkshopBundle:Workshop")->findPhoneNoId($workshop->getPhoneNumber2(), $workshop->getId());
                }
                if ($workshop->getMovileNumber1() != '0' and $workshop->getMovileNumber1() != null) {
                    $findPhone[2] = $em->getRepository("WorkshopBundle:Workshop")->findPhoneNoId($workshop->getMovileNumber1(), $workshop->getId());
                }
                if ($workshop->getMovileNumber2() != '0' and $workshop->getMovileNumber2() != null) {
                    $findPhone[3] = $em->getRepository("WorkshopBundle:Workshop")->findPhoneNoId($workshop->getMovileNumber2(), $workshop->getId());
                }
                if (($find == null or $workshop->getCodeWorkshop() == $last_code ) and $findPhone[0]['1'] < 1 and $findPhone[1]['1'] < 1 and $findPhone[2]['1'] < 1 and $findPhone[3]['1'] < 1) {
                    $workshop = UtilController::settersContact($workshop, $workshop, $actual_region, $actual_city);

                    $code_partner = $workshop->getPartner()->getCodePartner();
                    $workshop->setCodePartner($code_partner);

                    // Set default shop to NULL
                    $shop = $form['shop']->getClientData();
                    if ($shop == 0) {
                        $workshop->setShop(null);
                    }

                    $this->createHistoric($em, $workshop); /* Genera un historial de cambios del taller */

                    $this->saveWorkshop($em, $workshop);

                    if ($security->isGranted('ROLE_ADMIN'))
                        return $this->redirect($this->generateUrl('workshop_list'));
                    elseif ($security->isGranted('ROLE_ASSESSOR'))
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
                        $flash = $this->get('translator')->trans('error.code_phone.used') . $workshop->getMovileNumber1()
                                . ' - ' . $this->get('translator')->trans('workshop')
                                . ' ' . $em->getRepository("WorkshopBundle:Workshop")->findPhoneGetCode($workshop->getMovileNumber1(), $workshop->getId());
                    } else if ($findPhone[3]['1'] > 0) {
                        $flash = $this->get('translator')->trans('error.code_phone.used') . $workshop->getMovileNumber2()
                                . ' - ' . $this->get('translator')->trans('workshop')
                                . ' ' . $em->getRepository("WorkshopBundle:Workshop")->findPhoneGetCode($workshop->getMovileNumber2(), $workshop->getId());
                    } else {
                        $code = UtilController::getCodeWorkshopUnused($em, $partner);        /* OBTIENE EL PRIMER CODIGO DISPONIBLE */
                        $flash = $this->get('translator')->trans('error.code_workshop.used') . $code . ' (valor actual ' . $last_code . ').';
                    }
                    $this->get('session')->setFlash('error', $flash);
                }
            }
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

    public function deleteWorkshopAction($id) {

        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false) {
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getEntityManager();
        $workshop = $em->getRepository("WorkshopBundle:Workshop")->find($id);
        if (!$workshop)
            throw $this->createNotFoundException('Workshop no encontrado en la BBDD');

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
    public function workshopObservationAction($workshop, $id_ticket) {

        if ($this->get('security.context')->isGranted('ROLE_ASSESSOR') === false) {
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        $form = $this->createForm(new WorkshopObservationType(), $workshop);

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

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

    public function insertCifAction($workshop_id, $country) {
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $em = $this->getDoctrine()->getEntityManager();
            $workshop = $em->getRepository("WorkshopBundle:Workshop")->findOneById($workshop_id);
            $locale = $workshop->getCountry()->getId();
            $CIF = $request->request->get('new_car_form_CIF');
            if ($CIF == '0' || empty($CIF)) {
                $flash = $this->get('translator')->trans('workshop.cif_error2');
                $this->get('session')->setFlash('error', $flash);
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
                        $this->get('session')->setFlash('error', $flash);
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
                    $this->get('session')->setFlash('error', $flash);
                }
            }
        }
        return $this->render('TicketBundle:Workshop:insert_cif.html.twig', array('workshop_id' => $workshop_id, 'country' => $country));
    }

    public function findCifAction($cif) {
        $em = $this->getDoctrine()->getEntityManager();
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
    public function createHistoric($em, $workshop) {

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
    }

    /**
     * Hace el save de un workshop
     * @param EntityManager $em
     * @param Workshop $workshop
     */
    private function saveWorkshop($em, $workshop) {
        $workshop->setModifiedAt(new \DateTime(\date("Y-m-d H:i:s")));
        $workshop->setModifiedBy($this->get('security.context')->getToken()->getUser());
        $em->persist($workshop);
        $em->flush();
    }

}
