<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class AjaxController extends Controller
{
// WORKSHOP 
    /*
     * Get Max Workshop ID from a Partner
     * @return json
     */
    public function getMaxIdWorkshopByPartnerAction($partner)
    {
        $em = $this->getDoctrine()->getManager();

        $codePartner = $em->getRepository('AppBundle:Partner')->find($partner)->getCodePartner();

        $id = $this->get('utilsUser')->getMaxIdWorkshop($em, $codePartner);

        $json = array('id' => $id);
        return new Response(json_encode($json), $status = 200);
    }
}
