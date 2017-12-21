<?php
namespace Adservice\LockBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Adservice\UtilBundle\Controller\UtilController;
use Adservice\UtilBundle\Entity\Pagination;

class LockController extends Controller
{
    public function listIncidencesAction(Request $request, $page=1, $id_taller='none', $id_socio='none') #, $country='none') #$texto=null, $brand=null, $model=null, $version=null)
    {
        //$em         = $this->getDoctrine()->getManager('default');
        $em_lock    = $this->getDoctrine()->getManager('em_lock');

        $pagination = new Pagination($page);
        $bundle     = 'LockBundle';
        $entity     = 'LockIncidence';
        $params     = array();

        if( ! $this->get('security.authorization_checker')->isGranted('ROLE_ASSESSOR')){
                                                      $id_taller = $this->getUser()->getWorkshop()->getPartner()->getCodeWorkshop();
                                                      $id_socio  = $this->getUser()->getWorkshop()->getIdSocio();
        }
        if( $id_taller != null and $id_taller != 'none' ){
            $params[] = array('id_taller', ' = '.$id_taller);
            $params[] = array('id_socio' , ' = '.$id_socio );
        }
        // if($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
        //     if ($country != 'none') $params[] = array("country", " = '".$country."' ");
        //     else                    $params[] = array();
        // }
        // else $params[] = array('country', ' = '.$this->getUser()->getCountry()->getId());

        // if( $texto != null or $brand != null or $model != null or $version != null ){
        //     $condition = " c.id != 0 ";

        //     if( $texto   != null ){ $condition = $condition." AND c.brand LIKE '".  $brand  ."' "; }

        //     if( $brand   != null ){ $condition = $condition." AND c.brand LIKE '".  $brand  ."' "; }
        //     if( $model   != null ){ $condition = $condition." AND c.model LIKE '".  $model  ."' "; }
        //     if( $version != null ){ $condition = $condition." AND c.version LIKE '".$version."' "; }

        //     $joins[]  = array('e.coche  c', $condition);

        //     $result = $pagination->getRows($em, $bundle, $entity, $params, $pagination, null, $joins);

        //     $length = $pagination->getRowsLength($em, $bundle, $entity, $params, null, $joins);
        // }
        // else{
            $incidences = $pagination->getRows($em_lock, $bundle, $entity, $params, $pagination);

            $length = $pagination->getRowsLength($em_lock, $bundle, $entity, $params);
        // }

        $pagination->setTotalPagByLength($length);

        // if($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) $countries = $em->getRepository('UtilBundle:Country')->findAll();
        // else $countries = array();

        return $this->render('LockBundle:Lock:list_incidences.html.twig', array('incidences' => $incidences,
                                                                                'pagination' => $pagination,
                                                                                'id_taller'  => $id_taller,
                                                                                'id_socio'   => $id_socio,));
                                                                                // 'countries'  => $countries,
                                                                                // 'country'    => $country, ));
    }

    public function showIncidenceAction(Request $request, $page=1, $id_incidence=null)
    {
    	$em_lock   = $this->getDoctrine()->getManager('em_lock');
        $incidence = $em_lock->getRepository('LockBundle:LockIncidence')->find($id_incidence);

        return $this->render('LockBundle:Lock:show_incidence.html.twig', array('incidence' => $incidence));

    }
}
