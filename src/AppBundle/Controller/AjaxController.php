<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class AjaxController extends Controller
{
// PARTNER 
    /*
     * Get Max Code Partner
     * @return json
     */
    public function getMaxCodePartnerAction()
    {
        $em = $this->getDoctrine()->getManager();

        $codePartner = $this->get('utilsUser')->getMaxCodePartner($em);

        $json = array('codePartner' => $codePartner);
        return new Response(json_encode($json), $status = 200);
    }

// WORKSHOP 
    /*
     * Get Max Code Workshop from a Partner
     * @return json
     */
    public function getMaxCodeWorkshopByPartnerAction($partner)
    {
        $em = $this->getDoctrine()->getManager();

        $codePartner = $em->getRepository('AppBundle:Partner')->find($partner)->getCodePartner();

        $codeWorkshop = $this->get('utilsUser')->getMaxCodeWorkshop($em, $codePartner);

        $json = array('codeWorkshop' => $codeWorkshop);
        return new Response(json_encode($json), $status = 200);
    }
}
