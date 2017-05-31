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
        $workshops = $this->get('utilsWorkshop')->getWorkshops($this, $category_service);

        if (!$workshops) {
            $data = $this->throwError($this->get('translator')->trans('Workshops_not_found'), 404);
            $view = $this->view($data, 404);
        } else {
            $view = $this->view($workshops, 200);
        }
        return $this->handleView($view);
    }

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

        $workshop = $this->get('utilsWorkshop')->getWorkshops($this, $category_service, $id);

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
        $category_service = $this->get('security.token_storage')->getToken()->getUser()->getCategoryService()->getId();
        $workshops = $this->get('utilsWorkshop')->getWorkshops($this, $category_service, $id);
        $trans = $this->get('translator');

        if (!$workshops) {
            $data = $this->throwError($trans->trans('Workshop_not_found%id%', array('%id%' => $id)), 404);
            $view = $this->view($data, 404);
        } else {
            $workshop = $workshops[0];

            $em = $this->getDoctrine()->getManager();
            $workshop->setActive(true);
            $em->persist($workshop);
            $em->flush();

            //Send Mail
            $message = new \Swift_Message('Auto Diagnostic Service | '.$trans->trans('mail_workshop_activated%name%', array("name" => $workshop->getName())));
            $message->setFrom($this->container->getParameter('mail_noreply'))
                ->setTo($workshop->getEmail1())
                ->setBody($this->renderView('Emails/workshop_activate.html.twig',array('workshop' => $workshop)), 'text/html');
            // echo $this->renderView('Emails/workshop_activate.html.twig', array('workshop' => $workshop));die;
            $this->get('mailer')->send($message);

            // $data = $this->throwConfirmation("Workshop with id " . $id . " activated", 200);
            $data = $this->throwConfirmation($trans->trans('Workshop_activated'), 200);
            $view = $this->view($data, 200);
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
        $category_service = $this->get('security.token_storage')->getToken()->getUser()->getCategoryService()->getId();
        $workshops = $this->get('utilsWorkshop')->getWorkshops($this, $category_service, $id);
        $trans = $this->get('translator');

        if (!$workshops) {
            $data = $this->throwError($trans->trans('Workshop_not_found%id%', array('%id%' => $id)), 404);
            $view = $this->view($data, 404);
        } else {
            $workshop = $workshops[0];

            $em = $this->getDoctrine()->getManager();
            $workshop->setActive(false);
            $em->persist($workshop);
            $em->flush();

            //Send Mail
            $message = new \Swift_Message('Auto Diagnostic Service | '.$trans->trans('mail_workshop_deactivated%name%', array("name" => $workshop->getName())));
            $message->setFrom($this->container->getParameter('mail_noreply'))
                ->setTo($workshop->getEmail1())
                ->setBody($this->renderView('Emails/workshop_deactivate.html.twig',array('workshop' => $workshop)), 'text/html');
            // echo $this->renderView('Emails/workshop_deactivate.html.twig', array('workshop' => $workshop));die;
            $this->get('mailer')->send($message);

            $data = $this->throwConfirmation($trans->trans('Workshop_deactivated'), 200);
            $view = $this->view($data, 200);
        }

        return $this->handleView($view);
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