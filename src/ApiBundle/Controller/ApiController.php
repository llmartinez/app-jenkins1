<?php
namespace ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\Annotations\Get;

class ApiController extends FOSRestController
{
    /**
     * Get user by id
     *
     * @Security("has_role('ROLE_TOP_AD')")
     *
     * @ApiDoc(
     *      resource=true,
     *      section="GET TECH NOTES",
     *      description="Get user by id",
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
     * @param Request $request the request object
     * @param Integer $user_id the user id
     *
     * @throws createNotFoundException when make id not exist
     *
     * @Annotations\View()
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getUserAction(Request $request, $user_id)
    {
        $em = $this->getDoctrine();
        $tickets = $em->getRepository('AppBundle:Tickets')->findOneBy(array('id' => '1'));

        if (!$user) {
            $data = $this->throwError("User with id " . $user_id . " not found", 404);
            $view = $this->view($data, 404);
        } else {
            $view = $this->view($user, 200);
        }
        $view->setFormat('json');
        return $this->handleView($view);
    }


    /**
     * Get number of tickets by user id
     *
     * @Security("has_role('ROLE_TOP_AD')")
     *
     * @ApiDoc(
     *      resource=true,
     *      section="TICKETS",
     *      description="Get number of tickets by user id",
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
     * @param Request $request the request object
     * @param Integer $user_id the user id
     *
     * @throws createNotFoundException when make id not exist
     *
     * @Annotations\View()
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getNumberTicketsByUserId(Request $request, $user_id)
    {
        $em = $this->getDoctrine();
        $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => '1'));

        if (!$user) {
            $data = $this->throwError("User with id " . $user_id . " not found", 404);
            $view = $this->view($data, 404);
        } else {
            $view = $this->view($user, 200);
        }
        $view->setFormat('json');
        return $this->handleView($view);
    }



}