<?php
namespace Adservice\WorkshopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Adservice\UtilBundle\Controller\UtilController as UtilController;
use Adservice\UtilBundle\Entity\Pagination;
use Adservice\UserBundle\Entity\User;
use Adservice\WorkshopBundle\Entity\Workshop;
use Adservice\WorkshopBundle\Form\WorkshopType;
use Adservice\WorkshopBundle\Entity\TypologyRepository;
use Adservice\WorkshopBundle\Entity\DiagnosisMachineRepository;

class WorkshopController extends Controller {

    /**
     * Devuelve todos los talleres segun el usuario logeado
     * @return type
     * @throws AccessDeniedException
     */
    public function listAction($page=1 , $country='none') {
        $em = $this->getDoctrine()->getEntityManager();

        if ($this->get('security.context')->isGranted('ROLE_AD') === false) {
            throw new AccessDeniedException();
        }

        if($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            if ($country != 'none') $params[] = array('country', ' = '.$country);
            else                    $params[] = array();
        }
        else $params[] = array('country', ' = '.$this->get('security.context')->getToken()->getUser()->getCountry()->getId());

        $pagination = new Pagination($page);

        $workshops = $pagination->getRows($em, 'WorkshopBundle', 'Workshop', $params, $pagination);

        $length = $pagination->getRowsLength($em, 'WorkshopBundle', 'Workshop', $params);

        $pagination->setTotalPagByLength($length);

        if($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) $countries = $em->getRepository('UtilBundle:Country')->findAll();
        else $countries = array();

        return $this->render('WorkshopBundle:Workshop:list.html.twig', array('workshops'  => $workshops,
                                                                            'pagination' => $pagination,
                                                                            'countries'  => $countries,
                                                                            'country'    => $country,));
    }

    public function newWorkshopAction() {
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false)
            throw new AccessDeniedException();
        $em       = $this->getDoctrine()->getEntityManager();
        $request  = $this->getRequest();
        $workshop = new Workshop();

        $form     = $this->createForm(new WorkshopType(), $workshop);

        if ($request->getMethod() == 'POST') {

            $form->bindRequest($request);
            $partner = $workshop->getPartner();
            $code = UtilController::getCodeWorkshopUnused($em, $partner);        /*OBTIENE EL PRIMER CODIGO DISPONIBLE*/

            //La segunda comparacion ($form->getErrors()...) se hizo porque el request que reciber $form puede ser demasiado largo y hace que la funcion isValid() devuelva false
            $form_errors = $form->getErrors();
                if(isset($form_errors[0])) {
                    $form_errors = $form_errors[0];
                    $form_errors = $form_errors->getMessageTemplate();
                }else{
                    $form_errors = 'none';
                }
            if ($form->isValid() or $form_errors == 'The uploaded file was too large. Please try to upload a smaller file') {

                /*CHECK CODE WORKSHOP NO SE REPITA*/
                $find = $em->getRepository("WorkshopBundle:Workshop")->findOneBy(array('partner' => $partner->getId(),
                                                                                       'code_workshop' => $workshop->getCodeWorkshop()));
                if($find == null)
                {
                    $workshop = UtilController::newEntity($workshop, $this->get('security.context')->getToken()->getUser());
                    $workshop = UtilController::settersContact($workshop, $workshop);
                    $this->saveWorkshop($em, $workshop);

                    /*CREAR USERNAME Y EVITAR REPETICIONES*/
                    $username = UtilController::getUsernameUnused($em, $workshop->getName());

                    /*CREAR PASSWORD AUTOMATICAMENTE*/
                    $pass = substr( md5(microtime()), 1, 8);

                    $role = $em->getRepository('UserBundle:Role')->findOneByName('ROLE_USER');
                    $lang = $em->getRepository('UtilBundle:Language')->findOneByLanguage($workshop->getCountry()->getLang());

                    $newUser = UtilController::newEntity(new User(), $this->get('security.context')->getToken()->getUser());
                    $newUser->setUsername      ($username);
                    $newUser->setPassword      ($pass);
                    $newUser->setName          ($workshop->getContactName());
                    $newUser->setSurname       ($workshop->getContactSurname());
                    $newUser->setActive        ('1');
                    $newUser->setCreatedBy     ($workshop->getCreatedBy());
                    $newUser->setCreatedAt     (new \DateTime());
                    $newUser->setModifiedBy    ($workshop->getCreatedBy());
                    $newUser->setModifiedAt    (new \DateTime());
                    $newUser->setLanguage      ($lang);
                    $newUser->setWorkshop      ($workshop);
                    $newUser->addRole          ($role);
                    $newUser = UtilController::settersContact($newUser, $workshop);

                    //password nuevo, se codifica con el nuevo salt
                    $encoder = $this->container->get('security.encoder_factory')->getEncoder($newUser);
                    $salt = md5(time());
                    $password = $encoder->encodePassword($newUser->getPassword(), $salt);
                    $newUser->setPassword($password);
                    $newUser->setSalt($salt);
                    UtilController::saveEntity($em, $newUser, $this->get('security.context')->getToken()->getUser());

                    /* MAILING */
                    $mailerUser = $this->get('cms.mailer');
                    $mailerUser->setTo('dmaya@grupeina.com'); /* COLOCAR EN PROD -> *//* $mailerUser->setTo($newUser->getEmail1());*/
                    $mailerUser->setSubject($this->get('translator')->trans('mail.newUser.subject').$newUser->getWorkshop());
                    $mailerUser->setFrom('noreply@grupeina.com');
                    $mailerUser->setBody($this->renderView('UtilBundle:Mailing:user_new_mail.html.twig', array('user' => $newUser, 'password' => $pass)));
                    $mailerUser->sendMailToSpool();
                    // echo $this->renderView('UtilBundle:Mailing:user_new_mail.html.twig', array('user' => $newUser, 'password' => $pass));die;

                    return $this->redirect($this->generateUrl('workshop_list'));
                }
                else{
                    $flash = 'El codigo de Taller ya esta en uso, el primer numero disponible es: '.$code;
                    $this->get('session')->setFlash('error', $flash);
                }
            }
        }

        if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) $country = $this->get('security.context')->getToken()->getUser()->getCountry()->getId(); 
        else $country = null;
        $typologies = TypologyRepository::findTypologiesList($em, $country);
        $diagnosis_machines = DiagnosisMachineRepository::findDiagnosisMachinesList($em, $country);

        return $this->render('WorkshopBundle:Workshop:new_workshop.html.twig', array('workshop'           => $workshop,
                                                                                     'typologies'         => $typologies,
                                                                                     'diagnosis_machines' => $diagnosis_machines,
                                                                                     'form_name'          => $form->getName(),
                                                                                     'form'               => $form->createView(),
                                                                                     // 'locations'          => UtilController::getLocations($em),
                                                                                    ));
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

        $partner = $workshop->getPartner();

        $petition   = $this->getRequest();
        $form       = $this->createForm(new WorkshopType(), $workshop);

        $actual_city   = $workshop->getRegion();
        $actual_region = $workshop->getCity();

        if ($petition->getMethod() == 'POST') {
            $last_code = $workshop->getCodeWorkshop();
            $form->bindRequest($petition);

            //La segunda comparacion ($form->getErrors()...) se hizo porque el request que reciber $form puede ser demasiado largo y hace que la funcion isValid() devuelva false
            $form_errors = $form->getErrors();
                if(isset($form_errors[0])) {
                    $form_errors = $form_errors[0];
                    $form_errors = $form_errors->getMessageTemplate();
                }else{
                    $form_errors = 'none';
                }
            if ($form->isValid() or $form_errors == 'The uploaded file was too large. Please try to upload a smaller file') {

                /*CHECK CODE WORKSHOP NO SE REPITA*/
                $find = $em->getRepository("WorkshopBundle:Workshop")->findOneBy(array('partner' => $partner->getId(),
                                                                                       'code_workshop' => $workshop->getCodeWorkshop()));
                if($find == null or $workshop->getCodeWorkshop() == $last_code)
                {
                    $workshop   = UtilController::settersContact($workshop, $workshop, $actual_region, $actual_city);
                    $this->saveWorkshop($em, $workshop);
                    return $this->redirect($this->generateUrl('workshop_list'));
                }
                else{
                    $code  = UtilController::getCodeWorkshopUnused($em, $partner);        /*OBTIENE EL PRIMER CODIGO DISPONIBLE*/
                    $flash = 'El codigo de Taller ya esta en uso, el primer numero disponible es: '.$code.' (valor actual '.$last_code.').';
                    $this->get('session')->setFlash('error', $flash);
                }
            }
        }

        if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) $country = $this->get('security.context')->getToken()->getUser()->getCountry()->getId(); 
        else $country = null;
        $typologies = TypologyRepository::findTypologiesList($em, $country);
        $diagnosis_machines = DiagnosisMachineRepository::findDiagnosisMachinesList($em, $country);
        $workshop_machines  = $workshop->getDiagnosisMachines();
        // if($workshop_machines[0] and !isset($id_machine)){
        //     $id_machine = $workshop_machines[0];
        //     $id_machine = $id_machine->getId();
        // }

        return $this->render('WorkshopBundle:Workshop:edit_workshop.html.twig', array(  'workshop'           => $workshop,
                                                                                        'typologies'         => $typologies,
                                                                                        // 'id_machine'         => $id_machine,
                                                                                        'diagnosis_machines' => $diagnosis_machines,
                                                                                        'form_name'          => $form->getName(),
                                                                                        'form'               => $form->createView()));
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
     * Hace el save de un workshop
     * @param EntityManager $em
     * @param Workshop $workshop
     */
    private function saveWorkshop($em, $workshop){
        $workshop->setModifiedAt(new \DateTime(\date("Y-m-d H:i:s")));
        $workshop->setModifiedBy($this->get('security.context')->getToken()->getUser());
        $em->persist($workshop);
        $em->flush();
    }

}
