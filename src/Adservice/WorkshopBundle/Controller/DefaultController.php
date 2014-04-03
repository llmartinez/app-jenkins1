<?php

namespace Adservice\WorkshopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Adservice\WorkshopBundle\Form\WorkshopType;
use Adservice\WorkshopBundle\Entity\Workshop;
use Adservice\WorkshopBundle\Entity\Typology;
use Adservice\WorkshopBundle\Form\WorkshopOrderType;
use Adservice\UtilBundle\Entity\Pagination;
use Adservice\UtilBundle\Controller\DefaultController as UtilController;
use Adservice\TicketBundle\Controller\DefaultController as DefaultC;
use Adservice\UserBundle\Entity\User;

class DefaultController extends Controller {

    /**
     * Devuelve todos los talleres segun el usuario logeado
     * @return type
     * @throws AccessDeniedException
     */
    public function listAction($page=1 , $option=null) {
        $em = $this->getDoctrine()->getEntityManager();

        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false) {
            throw new AccessDeniedException();
        }

        $params[] = array();

        $pagination = new Pagination($page);

        $workshops = $pagination->getRows($em, 'WorkshopBundle', 'Workshop', $params, $pagination);

        $length = $pagination->getRowsLength($em, 'WorkshopBundle', 'Workshop', $params);

        $pagination->setTotalPagByLength($length);

        return $this->render('WorkshopBundle:Default:list.html.twig', array('workshops' => $workshops,
                                                                            'pagination' => $pagination,));
    }

    public function newWorkshopAction() {
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false)
            throw new AccessDeniedException();
        $request = $this->getRequest();
        $workshop  = new Workshop();
        $form = $this->createForm(new WorkshopType(), $workshop);
        $form->bindRequest($request);

        if ($request->getMethod() == 'POST') {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                /*TODO newEntity????*/
                $workshop->setCreatedBy($this->get('security.context')->getToken()->getUser());
                $workshop->setCreatedAt(new \DateTime(\date("Y-m-d H:i:s")));
                $this->saveWorkshop($em, $workshop);

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

                echo $newUser->getPassword();die;
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
