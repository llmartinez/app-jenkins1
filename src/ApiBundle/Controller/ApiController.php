<?php
namespace ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\Annotations\Get;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

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
     * Disable the cheks option to a workshop.
     *
     * @Security("has_role('ROLE_TOP_AD')")
     *
     * @ApiDoc(
     *      resource=true,
     *      section="WORKSHOPS",
     *      description="Disable the cheks option to a workshop.",
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
    public function putWorkshopCheksDisableAction($id)
    {
        $trans = $this->get('translator');

        $em = $this->getDoctrine()->getManager();

        $category_service = $this->get('security.token_storage')->getToken()->getUser()->getCategoryService()->getId();

        $workshop = $em->getRepository('AppBundle:Workshop')->findOneBy(array('id' => $id, 'category_service' => $category_service));

        if (isset($workshop))
        {
            $workshop->setHasChecks(false);
            $workshop->setNumChecks("0");
            $em->persist($workshop);
            $em->flush();

            $data = $this->throwConfirmation($trans->trans('Workshop_cheks_disabled'), 200);
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
     * Add cheks to a workshop.
     *
     * @Security("has_role('ROLE_TOP_AD')")
     *
     * @ApiDoc(
     *      resource=true,
     *      section="WORKSHOPS",
     *      description="Add cheks to a workshop",
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
    public function putWorkshopCheksAction($id, $numchecks)
    {
        $trans = $this->get('translator');

        $em = $this->getDoctrine()->getManager();

        $category_service = $this->get('security.token_storage')->getToken()->getUser()->getCategoryService()->getId();

        $workshop = $em->getRepository('AppBundle:Workshop')->findOneBy(array('id' => $id, 'category_service' => $category_service));

        if (isset($workshop))
        {
            $workshop->setHasChecks(true);
            $cheks = $workshop->getNumChecks();
            $cheks = $cheks + $numchecks;

            $workshop->setNumChecks($cheks);
            $em->persist($workshop);
            $em->flush();

            $data = $this->throwConfirmation(array('Total' => $cheks), 200);
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