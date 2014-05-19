<?php
namespace Adservice\LockBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Adservice\UtilBundle\Controller\UtilController;
use Adservice\UtilBundle\Entity\Pagination;

class LockController extends Controller
{
    public function listIncidencesAction($page=1, $id_taller=null)
    {
        $em_lock  = $this->getDoctrine()->getEntityManager('em_lock');
        $security = $this->get('security.context');
        $params = array();

        if( ! $security->isGranted('ROLE_ASSESSOR')) $id_taller = $security->getToken()->getUser()->getWorkshop()->getCodeWorkshop();

    	if( $id_taller != null ) $params[] = array('taller', ' = '.$id_taller);

        $pagination = new Pagination($page);

        $incidences = $pagination->getRows($em_lock, 'LockBundle', 'lock_incidence', $params, $pagination);

        $length = $pagination->getRowsLength($em_lock, 'LockBundle', 'lock_incidence', $params);

        $pagination->setTotalPagByLength($length);

        return $this->render('LockBundle:Lock:list_incidences.html.twig', array('incidences' => $incidences,
                                                                                'pagination' => $pagination,
                                                                                'id_taller'  => $id_taller, ));
    }

    public function showIncidenceAction($page=1, $id_incidence=null)
    {
    	$em_lock   = $this->getDoctrine()->getEntityManager('em_lock');
        $incidence = $em_lock->getRepository('LockBundle:lock_incidence')->find($id_incidence);

        return $this->render('LockBundle:Lock:show_incidence.html.twig', array('incidence' => $incidence));

    }
}
