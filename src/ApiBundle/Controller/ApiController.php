<?php
namespace ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\Annotations\Get;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Entity\User;
use AppBundle\Entity\Workshop;
class ApiController extends FOSRestController
{

// SECTION CHECKS

    /**
     * Check the user access
     *
     * @ApiDoc(
     *      resource=true,
     *      section="CHECKS",
     *      description="Check the user access",
     *      statusCodes={
     *          200="Returned when successful",
     *          403="Returned when the user is not authorized",
     *          404="Resource not found"
     *      },
     *      headers={
     *          {
     *              "name"="X-AUTH-TOKEN",
     *              "description"="Encrypted API Key.",
     *              "required" = "true"
     *          }
     *      }
     * )
     *
     * @throws createNotFoundException when make id not exist
     *
     * @Annotations\View()
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function checkAccessAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $data = array('username' => $user->getUsername(), 'role' => $user->getRoles()[0]->getName());

        $view = $this->view($data, 200);
        return $this->handleView($view);
    }

// SECTION PARTNERS

    /**
     * Get partners with the CategoryService from the logged user
     *
     * @Security("has_role('ROLE_TOP_AD')")
     *
     * @ApiDoc(
     *      resource=true,
     *      section="PARTNERS",
     *      description="Get partners with the CategoryService from the logged user",
     *      statusCodes={
     *          200="Returned when successful",
     *          403="Returned when the user is not authorized",
     *          404="Resource not found"
     *      },
     *      headers={
     *          {
     *              "name"="X-AUTH-TOKEN",
     *              "description"="Encrypted API Key.",
     *              "required" = "true"
     *          }
     *      }
     * )
     *
     * @throws createNotFoundException when make id not exist
     *
     * @Annotations\View()
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getPartnersAction()
    {
        $category_service = $this->get('security.token_storage')->getToken()->getUser()->getCategoryService()->getId();
        $partners = $this->get('getters')->getPartners($this, $category_service);

        $view = $this->getGetterView($partners,'Partners_not_found');
        return $this->handleView($view);
    }

// SECTION SHOPS

    /**
     * Get shops with the CategoryService from the logged user
     *
     * @Security("has_role('ROLE_TOP_AD')")
     *
     * @ApiDoc(
     *      resource=true,
     *      section="SHOPS",
     *      description="Get shops with the CategoryService from the logged user",
     *      statusCodes={
     *          200="Returned when successful",
     *          403="Returned when the user is not authorized",
     *          404="Resource not found"
     *      },
     *      headers={
     *          {
     *              "name"="X-AUTH-TOKEN",
     *              "description"="Encrypted API Key.",
     *              "required" = "true"
     *          }
     *      }
     * )
     *
     * @throws createNotFoundException when make id not exist
     *
     * @Annotations\View()
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getShopsAction()
    {
        $category_service = $this->get('security.token_storage')->getToken()->getUser()->getCategoryService()->getId();
        $shops = $this->get('getters')->getShops($this, $category_service);

        $view = $this->getGetterView($shops,'Shops_not_found');
        return $this->handleView($view);
    }

// SECTION TICKETS

    /**
     * Get workshop's number of tickets
     *
     * @Security("has_role('ROLE_USER')")
     *
     * @ApiDoc(
     *      resource=true,
     *      section="TICKETS",
     *      description="Get workshop's number of tickets",
     *      statusCodes={
     *          200="Returned when successful",
     *          403="Returned when the user is not authorized",
     *          404="Resource not found"
     *      },
     *      headers={
     *          {
     *              "name"="X-AUTH-TOKEN",
     *              "description"="$trans->trans('Workshop_deactivated')",
     *              "required" = "true"
     *          }
     *      }
     * )
     *
     * @param Request $request the request object
     * @param Integer $workshop_id the workshop id
     *
     * @throws createNotFoundException when make id not exist
     *
     * @Annotations\View()
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getWorkshopNumberTicketsAction()
    {
        $em = $this->getDoctrine();
        $trans = $this->get('translator');

        $workshop_id = $this->get('security.token_storage')->getToken()->getUser()->getWorkshop()->getId();

        $query = $em->getRepository("AppBundle:Ticket")
            ->createQueryBuilder("t")
            ->select("count(t) as ".$trans->trans('tickets'))
            ->where("t.workshop = ".$workshop_id. " and t.pending = 1");
        $tickets =  $query->getQuery()->getResult();

        if (!$tickets) {
            $data = $this->throwError($trans->trans('Workshop_not_found%id%', array('%id%' => $workshop_id)), 404);
            $view = $this->view($data, 404);
        } else {
            $data = $this->throwConfirmation($tickets[0], 200);
            $view = $this->view($data, 200);
        }

        $view->setFormat('json');
        return $this->handleView($view);
    }

// SECTION TYPOLOGIES

    /**
     * Get typologies with the CategoryService from the logged user
     *
     * @Security("has_role('ROLE_TOP_AD')")
     *
     * @ApiDoc(
     *      resource=true,
     *      section="TYPOLOGIES",
     *      description="Get typologies with the CategoryService from the logged user",
     *      statusCodes={
     *          200="Returned when successful",
     *          403="Returned when the user is not authorized",
     *          404="Resource not found"
     *      },
     *      headers={
     *          {
     *              "name"="X-AUTH-TOKEN",
     *              "description"="Encrypted API Key.",
     *              "required" = "true"
     *          }
     *      }
     * )
     *
     * @throws createNotFoundException when make id not exist
     *
     * @Annotations\View()
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getTypologiesAction()
    {
        $category_service = $this->get('security.token_storage')->getToken()->getUser()->getCategoryService()->getId();
        $typologies = $this->get('getters')->getTypologies($this, $category_service);

        $view = $this->getGetterView($typologies,'Typologies_not_found');
        return $this->handleView($view);
    }

// SECTION WORKSHOPS

    /**
     * Get workshops with the CategoryService from the logged user
     *
     * @Security("has_role('ROLE_TOP_AD')")
     *
     * @ApiDoc(
     *      resource=true,
     *      section="WORKSHOPS",
     *      description="Get workshops with the CategoryService from the logged user",
     *      statusCodes={
     *          200="Returned when successful",
     *          403="Returned when the user is not authorized",
     *          404="Resource not found"
     *      },
     *      headers={
     *          {
     *              "name"="X-AUTH-TOKEN",
     *              "description"="Encrypted API Key.",
     *              "required" = "true"
     *          }
     *      }
     * )
     *
     * @throws createNotFoundException when make id not exist
     *
     * @Annotations\View()
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getWorkshopsAction()
    {
        $category_service = $this->get('security.token_storage')->getToken()->getUser()->getCategoryService()->getId();
        $workshops = $this->get('getters')->getWorkshops($this, $category_service);

        $view = $this->getGetterView($workshops,'Workshops_not_found');
        return $this->handleView($view);
    }

    /**
     * Get workshop by $id with the CategoryService from the logged user
     *
     * @Security("has_role('ROLE_TOP_AD')")
     *
     * @ApiDoc(
     *      resource=true,
     *      section="WORKSHOPS",
     *      description="GGet workshop by $id with the CategoryService from the logged user",
     *      statusCodes={
     *          200="Returned when successful",
     *          403="Returned when the user is not authorized",
     *          404="Resource not found"
     *      },
     *      headers={
     *          {
     *              "name"="X-AUTH-TOKEN",
     *              "description"="Encrypted API Key.",
     *              "required" = "true"
     *          }
     *      }
     * )
     *
     * @param Integer $id the user id
     *
     * @throws createNotFoundException when make id not exist
     *
     * @Annotations\View()
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getWorkshopAction($id)
    {
        $category_service = $this->get('security.token_storage')->getToken()->getUser()->getCategoryService()->getId();

        $workshop = $this->get('getters')->getWorkshops($this, $category_service, $id);

        if (!$workshop) {
            $data = $this->throwError($this->get('translator')->trans('Workshop_not_found%id%', array('%id%' => $id)), 404);
            $view = $this->view($data, 404);
        } else {
            $view = $this->view($workshop, 200);
        }
        return $this->handleView($view);
    }

    /**
     * Activate workshop
     *
     * @Security("has_role('ROLE_TOP_AD')")
     *
     * @ApiDoc(
     *      resource=true,
     *      section="WORKSHOPS",
     *      description="Activate workshop",
     *      statusCodes={
     *          200="Returned when successful",
     *          403="Returned when the user is not authorized",
     *          404="Resource not found"
     *      },
     *      headers={
     *          {
     *              "name"="X-AUTH-TOKEN",
     *              "description"="Encrypted API Key.",
     *              "required" = "true"
     *          }
     *      }
     * )
     *
     * @param Integer $id the user id
     *
     * @throws createNotFoundException when make id not exist
     *
     * @Annotations\View()
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function putWorkshopActivateAction($id)
    {
        $trans = $this->get('translator');

        $em = $this->getDoctrine()->getManager();

        $category_service = $this->get('security.token_storage')->getToken()->getUser()->getCategoryService()->getId();

        $workshop = $em->getRepository('AppBundle:Workshop')->findOneBy(array('id' => $id, 'category_service' => $category_service));

        if (isset($workshop))
        {
            $workshop->setActive(true);
            $em->persist($workshop);
            $em->flush();

            //Send Mail
            $message = new \Swift_Message('Auto Diagnostic Service | ' . $trans->trans('mail_workshop_activated%name%', array("name" => $workshop->getName())));
            $message->setFrom($this->container->getParameter('mail_noreply'))->setTo($workshop->getEmail1())
                ->setBody($this->renderView('Emails/workshop_activate.html.twig', array('workshop' => $workshop)), 'text/html');
            // echo $this->renderView('Emails/workshop_activate.html.twig', array('workshop' => $workshop));die;
            $this->get('mailer')->send($message);

            // $data = $this->throwConfirmation("Workshop with id " . $id . " activated", 200);
            $data = $this->throwConfirmation($trans->trans('Workshop_activated'), 200);
            $view = $this->view($data, 200);
        }
        else
        {
            $data = $this->throwError($trans->trans('Workshop_not_found%id%', array('%id%' => $id)), 404);
            $view = $this->view($data, 404);
        }
        return $this->handleView($view);
    }

    /**
     * Deactivate workshop
     *
     * @Security("has_role('ROLE_TOP_AD')")
     *
     * @ApiDoc(
     *      resource=true,
     *      section="WORKSHOPS",
     *      description="Deactivate workshop",
     *      statusCodes={
     *          200="Returned when successful",
     *          403="Returned when the user is not authorized",
     *          404="Resource not found"
     *      },
     *      headers={
     *          {
     *              "name"="X-AUTH-TOKEN",
     *              "description"="Encrypted API Key.",
     *              "required" = "true"
     *          }
     *      }
     * )
     *
     * @param Integer $id the user id
     *
     * @throws createNotFoundException when make id not exist
     *
     * @Annotations\View()
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function putWorkshopDeactivateAction($id)
    {
        $trans = $this->get('translator');

        $em = $this->getDoctrine()->getManager();

        $category_service = $this->get('security.token_storage')->getToken()->getUser()->getCategoryService()->getId();

        $workshop = $em->getRepository('AppBundle:Workshop')->findOneBy(array('id' => $id, 'category_service' => $category_service));

        if (isset($workshop))
        {
            $workshop->setActive(false);
            $em->persist($workshop);
            $em->flush();

            //Send Mail
            $message = new \Swift_Message('Auto Diagnostic Service | '.$trans->trans('mail_workshop_deactivated%name%', array("name" => $workshop->getName())));
            $message->setFrom($this->container->getParameter('mail_noreply'))->setTo($workshop->getEmail1())
                ->setBody($this->renderView('Emails/workshop_deactivate.html.twig',array('workshop' => $workshop)), 'text/html');
            // echo $this->renderView('Emails/workshop_deactivate.html.twig', array('workshop' => $workshop));die;
            $this->get('mailer')->send($message);

            $data = $this->throwConfirmation($trans->trans('Workshop_deactivated'), 200);
            $view = $this->view($data, 200);
        }
        else
        {
            $data = $this->throwError($trans->trans('Workshop_not_found%id%', array('%id%' => $id)), 404);
            $view = $this->view($data, 404);
        }

        return $this->handleView($view);
    }


    /**
     * Create workshop
     *
     * @Security("has_role('ROLE_TOP_AD')")
     *
     * @Annotations\Post("workshops/create")
     * @ApiDoc(
     *      resource=true,
     *      section="WORKSHOPS",
     *      description="Get workshop's number of tickets",
     *      statusCodes={
     *          200="Returned when successful",
     *          403="Returned when the user is not authorized",
     *          404="Resource not found"
     *      },
     *      headers={
     *          {
     *              "name"="X-AUTH-TOKEN",
     *              "description"="$trans->trans('Workshop_deactivated')",
     *              "required" = "true"
     *          }
     *      }
     * )
     *
     * @param Request $request the request object
     * @param Integer $workshop_id the workshop id
     *
     * @throws createNotFoundException when make id not exist
     *
     * @Annotations\View()
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postWorkshopCreateAction(){
        $em = $this->getDoctrine()->getEntityManager();
        $headers = getallheaders();
        $parsedHeaders = $this->parseHeaders($headers);
        $data = $this->checkParsedHeaders($parsedHeaders);
        $workshop = new Workshop();
        $security = $this->get('security.context');
        if($data == null) {
            $workshop->setId($parsedHeaders['id']);
            $workshop->setName($parsedHeaders['name']);
            $tmpCatServ = $em->getRepository("AppBundle:CategoryService")->find($parsedHeaders['category_service']);
            $workshop->setCategoryService($tmpCatServ);
            $tmpPartner = $em->getRepository("AppBundle:Partner")->find($parsedHeaders['partner']);
            $workshop->setPartner($tmpPartner);
            $workshop->setInternalCode($parsedHeaders['internal_code']);
            $workshop->setCodePartner($parsedHeaders['code_partner']);
            $workshop->setCodeWorkshop($parsedHeaders['code_workshop']);
            $workshop->setCif($parsedHeaders['cif']);
            $workshop->setTest($parsedHeaders['test']);
            $workshop->setEndTestAt($parsedHeaders['test_at']);
            $workshop->setHasChecks($parsedHeaders['checks']);
            $workshop->setNumChecks($parsedHeaders['number_checks']);
            $tmpTypology = $em->getRepository("AppBundle:Typology")->find($parsedHeaders['typology']);
            $workshop->setTypology($tmpTypology);
            $tmpDiagnosisMachine = $em->getRepository("AppBundle:DiagnosisMachine")->find($parsedHeaders['diag_machine']);
            $workshop->addDiagnosisMachine($tmpDiagnosisMachine);
            $workshop->setContact($parsedHeaders['contact']);
            $workshop->setPhoneNumber1($parsedHeaders['phone1']);
            $workshop->setPhoneNumber2($parsedHeaders['phone2']);
            $workshop->setMobileNumber1($parsedHeaders['mobile1']);
            $workshop->setMobileNumber2($parsedHeaders['mobile2']);
            $workshop->setFax($parsedHeaders['fax']);
            $workshop->setEmail1($parsedHeaders['email1']);
            $workshop->setEmail2($parsedHeaders['email2']);
            $tmpCountry = $em->getRepository("AppBundle:Country")->find($parsedHeaders['country']);
            $workshop->setCountry($tmpCountry);
            $workshop->setRegion("-");
            $workshop->setCity($parsedHeaders['city']);
            $workshop->setAddress($parsedHeaders['address']);
            $workshop->setPostalCode($parsedHeaders['postal_code']);
            $workshop->setConflictive($parsedHeaders['conflictive']);
            $workshop->setObservationWorkshop($parsedHeaders['observation_workshop']);
            $workshop->setObservationAssessor($parsedHeaders['observation_assessor']);
            $workshop->setObservationAdmin($parsedHeaders['observation_admin']);
            $workshop->setAdServicePlus($parsedHeaders['ad_service_plus']);

            //DEFAULT SETS
            $workshop->setActive(1);
            $workshop->setUpdateAt(new \DateTime(\date("Y-m-d H:i:s")));
            $workshop->setModifiedAt(new \DateTime(\date("Y-m-d H:i:s")));
            $workshop->setCreatedAt(new \DateTime(\date("Y-m-d H:i:s")));
            $workshop->setCreatedBy($security->getToken()->getUser());

            $em->persist($workshop);
            $em->flush();



            $username = $this->getUsernameUnused($em, $workshop->getName());

            /* CREAR PASSWORD AUTOMATICAMENTE */
            $pass = substr(md5(microtime()), 1, 8);
            $role = $em->getRepository('AppBundle:Role')->findOneByName('ROLE_USER');
            $lang = $em->getRepository('AppBundle:Language')->findOneByLanguage($workshop->getCountry()->getLang());
            $newUser = $this->newEntity(new User(), $security->getToken()->getUser());
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
            $newUser->setCategoryService($security->getToken()->getUser()->getCategoryService());
            $newUser = $this->settersContact($newUser, $workshop);

            //ad-service +
            //password nuevo, se codifica con el nuevo salt
            $encoder = $this->container->get('security.encoder_factory')->getEncoder($newUser);
            $salt = md5(time());
            $password = $encoder->encodePassword($newUser->getPassword(), $salt);
            $newUser->setPassword($password);
            $newUser->setSalt($salt);
            //Asignamos un Token para AD360
            $token = $this->getRandomToken();
            $newUser->setToken($token);
           /* $this->saveEntity($em, $newUser, $this->get('security.context')->getToken()->getUser());*/

            $newUser->setModifiedBy($security->getToken()->getUser());
            $newUser->setModifiedAt(new \DateTime(\date("Y-m-d H:i:s")));
            $em->persist($newUser);


            $em->flush();
            $trans = $this->get('translator');
            $data = $this->throwConfirmation($trans->trans('Workshop_created'), 200);
            $view = $this->view($data, 200);
        }
        else {
            $view = $this->view($data, 404);
        }

        $view->setFormat('json');
        return $this->handleView($view);
    }

    /**
     * Asigna el usuario que ha modificado la clase y la fecha de la modificación.
     * @param EntityManager $em
     * @param Class $entity
     * @param Bool $auto_flush true: aplica cambios en BBDD
     * @return Bool
     */
    public static function saveEntity($em, $entity, $user, $auto_flush=true)
    {

        $entity->setModifiedBy($user);
        $entity->setModifiedAt(new \DateTime(\date("Y-m-d H:i:s")));
        $em->persist($entity);

        if($auto_flush) $em->flush();
        return true;
    }

    /**
     * Genera un Token aleatorio
     * @return string
     */
    static public function getRandomToken()
    {
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));

        for ($i = 0; $i < 20; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return $key;
    }


    /**
     * Obtiene el Slug de un username sin usar. Si la cadena pasada por parametro ya existe, se le añade un '-' y un numero.
     * @param  entityManager $em
     * @param  string        $name
     * @return string
     */
    public function getUsernameUnused($em, $name)
    {
        $separador = '';
        $slug = $this->getSlug($name, $separador);
        $unused = 1;
        while($unused != 'unused') {
            $find = $em->getRepository('AppBundle:User')->findOneByUsername($slug);
            if( $find == null) { $unused = 'unused'; }
            else{
                $slug = $this->getSlug($name, $separador).$unused;
                $unused = $unused+1;
            }
        }
        return $slug;
    }

    /**
     * Obtiene el Slug de una cadena
     * @param  string $cadena
     * @param  string $separador
     * @return string
     */
    static public function getSlug($cadena, $separador = '-')
    {
        // Remove all non url friendly characters with the unaccent function
        $valor = self::sinAcentos($cadena);

        if (function_exists('mb_strtolower')) {
            $valor = mb_strtolower($valor);
        } else {
            $valor = strtolower($valor);
        }

        // Remove all none word characters
        $valor = preg_replace('/\W/', ' ', $valor);

        // More stripping. Replace spaces with dashes
        $valor = strtolower(preg_replace('/[^A-Z^a-z^0-9^\/]+/', $separador,
            preg_replace('/([a-z\d])([A-Z])/', '\1_\2',
                preg_replace('/([A-Z]+)([A-Z][a-z])/', '\1_\2',
                    preg_replace('/::/', '/', $valor)))));
        //return trim($valor, $separador);
        return $valor;
    }

    /**
     * Define los campos de contacto de una entidad a partir de los campos de entidad de otra.
     * También elimina espacios en blanco para evitar errores en numeros
     * @param  [type] $entity [description]
     * @param  [type] $data   [description]
     * @return [type]         [description]
     */
    public function settersContact($entity, $data, $actual_region = '', $actual_city = '')
    {
        $entity->setPhoneNumber1  ($this->getSlug($data->getPhoneNumber1() , ''));
        $entity->setPhoneNumber2  ($this->getSlug($data->getPhoneNumber2() , ''));
        $entity->setMobileNumber1 ($this->getSlug($data->getMobileNumber1(), ''));
        $entity->setMobileNumber2 ($this->getSlug($data->getMobileNumber2(), ''));
        $entity->setFax           ($this->getSlug($data->getFax()          , ''));
        $entity->setCountry       ($data->getCountry());
        $entity->setAddress       ($data->getAddress());
        $entity->setPostalCode    ($data->getPostalCode());
        $entity->setEmail1        ($data->getEmail1());
        $entity->setEmail2        ($data->getEmail2());

        if($data->getRegion() == '[object Object]') $entity->setRegion($actual_region    );
        else                                        $entity->setRegion($data->getRegion());
        if($data->getCity()   == '[object Object]') $entity->setCity  ($actual_city      );
        else                                        $entity->setCity  ($data->getCity()  );

        return $entity;
    }

    /**
     * Asigna el usuario que ha creado la clase y la fecha de la creación.
     * @param Class $entity
     * @param Class $user
     * @return Class
     */
    public static function newEntity($entity, $user){
        $entity->setCreatedBy($user);
        $entity->setCreatedAt(new \DateTime(\date("Y-m-d H:i:s")));
        return $entity;
    }

    public static function sinAcentos($string)
    {
        if ( ! preg_match('/[\x80-\xff]/', $string) ) {
            return $string;
        }

        if (self::seemsUtf8($string)) {
            $chars = array(
                // Decompositions for Latin-1 Supplement
                chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
                chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
                chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
                chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
                chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
                chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
                chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
                chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
                chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
                chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
                chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
                chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
                chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
                chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
                chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
                chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
                chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
                chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
                chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
                chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
                chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
                chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
                chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
                chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
                chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
                chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
                chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
                chr(195).chr(191) => 'y',
                // Decompositions for Latin Extended-A
                chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
                chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
                chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
                chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
                chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
                chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
                chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
                chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
                chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
                chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
                chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
                chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
                chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
                chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
                chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
                chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
                chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
                chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
                chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
                chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
                chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
                chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
                chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
                chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
                chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
                chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
                chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
                chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
                chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
                chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
                chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
                chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
                chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
                chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
                chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
                chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
                chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
                chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
                chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
                chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
                chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
                chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
                chr(197).chr(148) => 'R', chr(197).chr(149) => 'r',
                chr(197).chr(150) => 'R', chr(197).chr(151) => 'r',
                chr(197).chr(152) => 'R', chr(197).chr(153) => 'r',
                chr(197).chr(154) => 'S', chr(197).chr(155) => 's',
                chr(197).chr(156) => 'S', chr(197).chr(157) => 's',
                chr(197).chr(158) => 'S', chr(197).chr(159) => 's',
                chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
                chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
                chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
                chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
                chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
                chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
                chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
                chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
                chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
                chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
                chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
                chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
                chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
                chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
                chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
                chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
                // Euro Sign
                chr(226).chr(130).chr(172) => 'E',
                // GBP (Pound) Sign
                chr(194).chr(163) => '',
                'Ä' => 'Ae', 'ä' => 'ae', 'Ü' => 'Ue', 'ü' => 'ue',
                'Ö' => 'Oe', 'ö' => 'oe', 'ß' => 'ss',
                // Norwegian characters
                'Å'=>'Aa','Æ'=>'Ae','Ø'=>'O','æ'=>'a','ø'=>'o'
            );

            $string = strtr($string, $chars);
        } else {
            // Assume ISO-8859-1 if not UTF-8
            $chars['in'] = chr(128).chr(131).chr(138).chr(142).chr(154).chr(158)
                .chr(159).chr(162).chr(165).chr(181).chr(192).chr(193).chr(194)
                .chr(195).chr(196).chr(197).chr(199).chr(200).chr(201).chr(202)
                .chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210)
                .chr(211).chr(212).chr(213).chr(214).chr(216).chr(217).chr(218)
                .chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227)
                .chr(228).chr(229).chr(231).chr(232).chr(233).chr(234).chr(235)
                .chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243)
                .chr(244).chr(245).chr(246).chr(248).chr(249).chr(250).chr(251)
                .chr(252).chr(253).chr(255);

            $chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

            $string = strtr($string, $chars['in'], $chars['out']);
            $doubleChars['in'] = array(chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254));
            $doubleChars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
            $string = str_replace($doubleChars['in'], $doubleChars['out'], $string);
        }

        return $string;
    }


    private function parseHeaders($headers) {
        $parsedHeaders['id'] = $parsedHeaders['name'] = $parsedHeaders['category_service'] = $parsedHeaders['partner'] = $parsedHeaders['internal_code'] = $parsedHeaders['partner_code'] = $parsedHeaders['workshop_code'] = $parsedHeaders['cif'] = $parsedHeaders['test'] = $parsedHeaders['test_at'] = $parsedHeaders['checks'] = $parsedHeaders['number_checks'] = $parsedHeaders['contact'] = $parsedHeaders['phone1'] = $parsedHeaders['phone2'] = $parsedHeaders['mobile1'] = $parsedHeaders['mobile2'] = $parsedHeaders['fax'] = $parsedHeaders['email1'] = $parsedHeaders['email2'] = $parsedHeaders['country'] = $parsedHeaders['city'] = $parsedHeaders['postal_code'] = $parsedHeaders['address'] = $parsedHeaders['conflictive'] = $parsedHeaders['observation_workshop'] = $parsedHeaders['observation_assessor'] = $parsedHeaders['observation_admin'] = $parsedHeaders['typology'] = $parsedHeaders['diag_machine']  = null;
        foreach ($headers as $key =>$item) {
            if ($key == 'id') {                     $parsedHeaders['id'] = $item;                        }
            elseif ($key == 'name') {                   $parsedHeaders['name'] = $item;                      }
            elseif ($key == 'category_service') {       $parsedHeaders['category_service'] = $item;          }
            elseif ($key == 'partner') {                $parsedHeaders['partner'] = $item;                   }
            elseif ($key == 'internal_code') {          $parsedHeaders['internal_code'] = $item;             }
            elseif ($key == 'code_partner') {           $parsedHeaders['code_partner'] = $item;              }
            elseif ($key == 'code_workshop') {          $parsedHeaders['code_workshop'] = $item;             }
            elseif ($key == 'cif') {                    $parsedHeaders['cif'] = $item;                       }
            elseif ($key == 'test') {                   $parsedHeaders['test'] = $item;                      }
            elseif ($key == 'test_at') {                $parsedHeaders['test_at'] = $item;                   }
            elseif ($key == 'checks') {                 $parsedHeaders['checks'] = $item;                    }
            elseif ($key == 'number_checks') {          $parsedHeaders['number_checks'] = $item;             }
            elseif ($key == 'contact') {                $parsedHeaders['contact'] = $item;                   }
            elseif ($key == 'phone1') {                 $parsedHeaders['phone1'] = $item;                    }
            elseif ($key == 'phone2') {                 $parsedHeaders['phone2'] = $item;                    }
            elseif ($key == 'mobile1') {                $parsedHeaders['mobile1'] = $item;                   }
            elseif ($key == 'mobile2') {                $parsedHeaders['mobile2'] = $item;                   }
            elseif ($key == 'fax') {                    $parsedHeaders['fax'] = $item;                       }
            elseif ($key == 'email1') {                 $parsedHeaders['email1'] = $item;                    }
            elseif ($key == 'email2') {                 $parsedHeaders['email2'] = $item;                    }
            elseif ($key == 'country') {                $parsedHeaders['country'] = $item;                   }
            elseif ($key == 'city') {                   $parsedHeaders['city'] = $item;                      }
            elseif ($key == 'postal_code') {            $parsedHeaders['postal_code'] = $item;               }
            elseif ($key == 'address') {                $parsedHeaders['address'] = $item;                   }
            elseif ($key == 'conflictive') {            $parsedHeaders['conflictive'] = $item;               }
            elseif ($key == 'observation_workshop') {   $parsedHeaders['observation_workshop'] = $item;      }
            elseif ($key == 'observation_assessor') {   $parsedHeaders['observation_assessor'] = $item;      }
            elseif ($key == 'observation_admin') {      $parsedHeaders['observation_admin'] = $item;         }
            elseif ($key == 'diag_machine') {           $parsedHeaders['diag_machine'] = $item;              }
            elseif ($key == 'typology') {               $parsedHeaders['typology'] = $item;                  }
            elseif ($key == 'ad_service_plus') {        $parsedHeaders['ad_service_plus'] = $item;           }
        }
        return $parsedHeaders;
    }

    private function checkParsedHeaders($parsedHeaders) {
        $em = $this->getDoctrine()->getEntityManager();
        $trans = $this->get('translator');
        $tmp_workshop = $em->getRepository('AppBundle:Workshop')->find($parsedHeaders['id']);
        $security = $this->get('security.context');
        $data = null;

        if ($tmp_workshop != null){
            $data[] = $this->throwError($trans->trans('Workshop_yet_exist%id%', array('%id%' => $parsedHeaders['id'])), 404);
        }

        //Comprobación name no es nulo
        if ($parsedHeaders['name'] == null){
            $data[] = $this->throwError($trans->trans('Name_not_null%name%', array('%name%' => $parsedHeaders['name'])), 404);
        }
        //Comprobación categoria servicio es la misma que el usuario que usa la API
        if ($parsedHeaders['category_service'] != $security->getToken()->getUser()->getCategoryService()->getId()){
            $data[] = $this->throwError($trans->trans('Category_service_not_valid%category_service%', array('%category_service%' => $parsedHeaders['category_service'])), 404);
        }
        //Comprobación Partner es valido
        $tmp_partner = $em->getRepository('AppBundle:Partner')->find($parsedHeaders['partner']);
        if ($tmp_partner->getCategoryService()->getId() != $security->getToken()->getUser()->getCategoryService()->getId()){
            $data[] = $this->throwError($trans->trans('Partner_not_valid%partner%', array('%partner%' => $parsedHeaders['partner'])), 404);
        }
        //Comprobacion Code Partner
        if ($parsedHeaders['code_partner'] != $tmp_partner->getCodePartner()){
            $data[] = $this->throwError($trans->trans('Code_partner_not_valid%code_partner%', array('%code_partner%' => $parsedHeaders['code_partner'])), 404);
        }
        //Comprobacion Code Workshop
        $tmp_workshop = null;
        $tmp_workshop = $em->getRepository('AppBundle:Workshop')->findOneBy(array('code_partner' =>$parsedHeaders['code_partner'], 'code_workshop' => $parsedHeaders['code_workshop']));
        if ($tmp_workshop != null){
            $data[] = $this->throwError($trans->trans('Workshop_yet_exist%code_workshop%', array('%code_workshop%' => $parsedHeaders['code_workshop'])), 404);
        }
        //Comprobacion Cif
        if ($parsedHeaders['cif'] == null){
            $data[] = $this->throwError($trans->trans('CIF_not_valid%cif%', array('%cif%' => $parsedHeaders['cif'])), 404);
        }

        //Comprobacion Test
        if ($parsedHeaders['test'] == null || $parsedHeaders['test'] < 0 || $parsedHeaders['test'] > 1){
            $data[] = $this->throwError($trans->trans('Test_not_valid%test%', array('%test%' => $parsedHeaders['test'])), 404);
        }
        //Comprobacion Test_At
        if ($parsedHeaders['test'] == 1 && ['test_at'] == null){
            $data[] = $this->throwError($trans->trans('Test_At_not_valid%test_at%', array('%test_at%' => $parsedHeaders['test_at'])), 404);
        }
        //Comprobacion Checks
        if ($parsedHeaders['checks'] == null){
            $data[] = $this->throwError($trans->trans('Test_At_not_valid%checks%', array('%checks%' => $parsedHeaders['checks'])), 404);
        }
        //Comprobacion Number Checks
        if ($parsedHeaders['number_checks'] > 1 || $parsedHeaders['number_checks'] < 0){
            $data[] = $this->throwError($trans->trans('Number_checks_not_valid%number_checks%', array('%number_checks%' => $parsedHeaders['number_checks'])), 404);
        }
        //Comprobacion Contact
        if ($parsedHeaders['contact'] == null){
            $data[] = $this->throwError($trans->trans('Contact_not_valid%contact%', array('%contact%' => $parsedHeaders['contact'])), 404);
        }

        //Comprobacion Phone Number 1
        if($parsedHeaders['phone1'] == null){
            $data[] = $this->throwError($trans->trans('Phone1_not_null%phone1%', array('%phone1%' => $parsedHeaders['phone1'])), 404);
        }
        else {
            $findPhone[0] = $em->getRepository("AppBundle:Workshop")->findPhone($parsedHeaders['phone1']);

            if ($findPhone[0]['1'] > 0) {
                $data[] = $this->throwError($trans->trans('Phone1_yet_exist%phone1%', array('%phone1%' => $parsedHeaders['phone1'])), 404);
            }
        }
        //Comprobacion Phone Number 2
        if($parsedHeaders['phone2'] != null) {
            $findPhone[0] = $em->getRepository("AppBundle:Workshop")->findPhone($parsedHeaders['phone2']);
            if ($findPhone[0]['1'] > 0) {
                $data[] = $this->throwError($trans->trans('Phone2_yet_exist%phone2%', array('%phone2%' => $parsedHeaders['phone2'])), 404);
            }
        }
        //Comprobacion Mobile Number 1

        if($parsedHeaders['mobile1'] != null) {
            $findPhone[0] = $em->getRepository("AppBundle:Workshop")->findPhone($parsedHeaders['mobile1']);
            if ($findPhone[0]['1'] > 0) {
                $data[] = $this->throwError($trans->trans('Mobile1_yet_exist%mobile1%', array('%mobile1%' => $parsedHeaders['mobile1'])), 404);
            }
        }
        //Comprobacion Mobile Number 2
        if($parsedHeaders['mobile2'] != null) {
            $findPhone[0] = $em->getRepository("AppBundle:Workshop")->findPhone($parsedHeaders['mobile2']);
            if ($findPhone[0]['1'] > 0) {
                $data[] = $this->throwError($trans->trans('Mobile2_yet_exist%mobile2%', array('%mobile2%' => $parsedHeaders['mobile2'])), 404);
            }
        }
        //Comprobacion Fax
        if($parsedHeaders['fax'] != null) {
            $findPhone[0] = $em->getRepository("AppBundle:Workshop")->findPhone($parsedHeaders['fax']);
            if ($findPhone[0]['1'] > 0) {
                $data[] = $this->throwError($trans->trans('Fax_yet_exist%fax%', array('%fax%' => $parsedHeaders['fax'])), 404);
            }
        }
        //Comprobacion Email1
        if($parsedHeaders['email1'] == null ) {
            $data[] = $this->throwError($trans->trans('Email1_not_null%email1%', array('%email1%' => $parsedHeaders['email1'])), 404);
        }

        elseif (!strrpos($parsedHeaders['email1'], "@")){
            $data[] = $this->throwError($trans->trans('Email1_not_valid%email1%', array('%email1%' => $parsedHeaders['email1'])), 404);
        }
        //Comprobacion Email2
        if($parsedHeaders['email2'] != null ) {
            if (!strrpos($parsedHeaders['email2'], "@")) {
                $data[] = $this->throwError($trans->trans('Email2_not_valid%email2%', array('%email2%' => $parsedHeaders['email2'])), 404);
            }
        }

        //Comprobacion Country
        if($parsedHeaders['country'] == null) {
            $data[] = $this->throwError($trans->trans('Country_not_null%country%', array('%country%' => $parsedHeaders['country'])), 404);
        }
        else{
            $tmp_country = $em->getRepository("AppBundle:Country")->find($parsedHeaders['country']);
            if ($tmp_country == null) {
                $data[] = $this->throwError($trans->trans('Country_not_valid%country%', array('%country%' => $parsedHeaders['country'])), 404);
            }
        }

        //Comprobacion City
        if ($parsedHeaders['city'] == null){
            $data[] = $this->throwError($trans->trans('City_not_valid%city%', array('%city%' => $parsedHeaders['city'])), 404);
        }
        //Comprobacion Address
        if ($parsedHeaders['address'] == null){
            $data[] = $this->throwError($trans->trans('Address_not_valid%address%', array('%address%' => $parsedHeaders['address'])), 404);
        }
        //Comprobacion Postal code
        if ($parsedHeaders['postal_code'] == null || $parsedHeaders['postal_code'] <0){
            $data[] = $this->throwError($trans->trans('Postal_code_not_valid%postal_code%', array('%postal_code%' => $parsedHeaders['postal_code'])), 404);
        }

        //Comprobacion Conflictive
        if ($parsedHeaders['conflictive'] > 1 || $parsedHeaders['conflictive'] < 0){
            $data[] = $this->throwError($trans->trans('Conflictive_not_valid%conflictive%', array('%conflictive%' => $parsedHeaders['conflictive'])), 404);
        }
        //Comprobacion Typology
        if($parsedHeaders['typology'] == null) {
            $data[] = $this->throwError($trans->trans('Typology_not_null%typology%', array('%typology%' => $parsedHeaders['typology'])), 404);
        }
        else {
            $tmp_typology = $em->getRepository("AppBundle:Typology")->find($parsedHeaders['typology']);
            if ($tmp_typology == null) {
                $data[] = $this->throwError($trans->trans('typology_not_valid%typology%', array('%typology%' => $parsedHeaders['typology'])), 404);
            }
        }
        //Comprobacion DiagMachine
        if($parsedHeaders['diag_machine'] == null) {
            $data[] = $this->throwError($trans->trans('diag_machine_not_null%diag_machine%', array('%diag_machine%' => $parsedHeaders['diag_machine'])), 404);
        }
        else {

            $tmp_diag = $em->getRepository("AppBundle:DiagnosisMachine")->find($parsedHeaders['diag_machine']);

            if ($tmp_diag == null) {
                $data[] = $this->throwError($trans->trans('diag_machine_not_valid%diag_machine%', array('%diag_machine%' => $parsedHeaders['diag_machine'])), 404);
            }
        }
        if ($parsedHeaders['ad_service_plus'] > 1 || $parsedHeaders['ad_service_plus'] < 0){
            $data[] = $this->throwError($trans->trans('Ad_service_plus_not_valid%ad_service_plus%', array('%ad_service_plus%' => $parsedHeaders['ad_service_plus'])), 404);
        }
        return $data;
    }

    /**
     * Check if $entities return values and generate an Error/Confirmation View
     * @param $entities         Array of elements
     * @param $message_error    code error 404
     * @return view
     */
    private function getGetterView($entities,$message_error){
        if (!$entities) {
            $data = $this->throwError($this->get('translator')->trans($message_error), 404);
            $view = $this->view($data, 404);
        } else {
            $view = $this->view($entities, 200);
        }
        return $view;
    }

    /**
     * Generate a confirmation array
     * @param $message    Text to confirm the action
     * @param $code       code confirmation 200
     * @return mixed
     */
    private function throwConfirmation($message,$code){
        $data['confirm']['code'] = $code;
        $data['confirm']['message'] = $message;
        return $data;
    }

    /**
     * Generate an error array
     * @param $message_error    Text explaint the error
     * @param $code_error       code error 404, 303, 200,...
     * @return mixed
     */
    private function throwError($message_error,$code_error){
        $data['error']['code'] = $code_error;
        $data['error']['message'] = $message_error;
        return $data;
    }

}