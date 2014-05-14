<?php
namespace Adservice\WorkshopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Adservice\UtilBundle\Controller\UtilController as UtilController;
use Adservice\UtilBundle\Entity\Pagination;
use Adservice\UserBundle\Entity\User;
use Adservice\WorkshopBundle\Entity\Workshop;
use Adservice\WorkshopBundle\Entity\Typology;
use Adservice\WorkshopBundle\Form\WorkshopType;
use Adservice\WorkshopBundle\Form\WorkshopOrderType;

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
        $form = $this->createForm(new WorkshopType(), $workshop);

        if ($request->getMethod() == 'POST') {

            $form->bindRequest($request);
            $partner = $workshop->getPartner();
            $code = UtilController::getCodeWorkshopUnused($em, $partner);        /*OBTIENE EL PRIMER CODIGO DISPONIBLE*/

            if ($form->isValid()) {
                /*CHECK CODE WORKSHOP NO SE REPITA*/
                $find = $em->getRepository("WorkshopBundle:Workshop")->findOneBy(array('partner' => $partner->getId(),
                                                                                       'code_workshop' => $workshop->getCodeWorkshop()));
                if($find == null)
                {
                    $workshop = UtilController::newEntity($workshop, $this->get('security.context')->getToken()->getUser());
                    if($workshop->getShop()->getName() == '...') { $workshop->setShop(null); }
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
                    $newUser->setCity          ($workshop->getCity());
                    $newUser->setAddress       ($workshop->getAddress());
                    $newUser->setPostalCode    ($workshop->getPostalCode());
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
                    UtilController::saveEntity($em, $newUser, $this->get('security.context')->getToken()->getUser());

                    /* MAILING */
                    $mailerUser = $this->get('cms.mailer');
                    $mailerUser->setTo($newUser->getEmail1());
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

        return $this->render('WorkshopBundle:Workshop:new_workshop.html.twig', array('workshop' => $workshop,
                                                                                     'form_name'  => $form->getName(),
                                                                                     'form'       => $form->createView(),
                                                                                     'locations'  => UtilController::getLocations($em),
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
        $code = UtilController::getCodeWorkshopUnused($em, $partner);        /*OBTIENE EL PRIMER CODIGO DISPONIBLE*/

        $petition = $this->getRequest();
        $form = $this->createForm(new WorkshopType(), $workshop);

        if ($petition->getMethod() == 'POST') {
            $last_code = $workshop->getCodeWorkshop();
            $form->bindRequest($petition);

            if ($form->isValid())
            {
                /*CHECK CODE WORKSHOP NO SE REPITA*/
                $find = $em->getRepository("WorkshopBundle:Workshop")->findOneBy(array('partner' => $partner->getId(),
                                                                                       'code_workshop' => $workshop->getCodeWorkshop()));
                if($find == null or $workshop->getCodeWorkshop() == $last_code)
                {
                    $this->saveWorkshop($em, $workshop);
                    return $this->redirect($this->generateUrl('workshop_list'));
                }
                else{
                    $flash = 'El codigo de Taller ya esta en uso, el primer numero disponible es: '.$code.' (valor actual '.$last_code.').';
                    $this->get('session')->setFlash('error', $flash);
                }
            }
        }
        else{
            $flash = 'El primer numero disponible es: '.$code;
            $this->get('session')->setFlash('info', $flash);
        }

        return $this->render('WorkshopBundle:Workshop:edit_workshop.html.twig', array('workshop'   => $workshop,
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
