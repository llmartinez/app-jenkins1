<?php
namespace Adservice\LockBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Adservice\UtilBundle\Controller\UtilController;
use Adservice\UtilBundle\Entity\Pagination;

class LockController extends Controller
{
    public function listIncidencesAction($page=1, $id_taller='none', $id_socio='none') #, $country='none') #$texto=null, $brand=null, $model=null, $version=null)
    {
        //$em         = $this->getDoctrine()->getEntityManager('default');
        $em_lock    = $this->getDoctrine()->getEntityManager('em_lock');
        $security   = $this->get('security.context');

        $pagination = new Pagination($page);
        $bundle     = 'LockBundle';
        $entity     = 'LockIncidence';
        $params     = array();

        if( ! $security->isGranted('ROLE_ADMIN')){
                                                      $id_taller = $security->getToken()->getUser()->getWorkshop()->getPartner()->getCodeWorkshop();
                                                      $id_socio  = $security->getToken()->getUser()->getWorkshop()->getIdSocio();
        }
        if( $id_taller != null and $id_taller != 'none' ){
            $params[] = array('id_taller', ' = '.$id_taller);
            $params[] = array('id_socio' , ' = '.$id_socio );
        }
        // if($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
        //     if ($country != 'none') $params[] = array("country", " = '".$country."' ");
        //     else                    $params[] = array();
        // }
        // else $params[] = array('country', ' = '.$this->get('security.context')->getToken()->getUser()->getCountry()->getId());

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

        // if($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) $countries = $em->getRepository('UtilBundle:Country')->findAll();
        // else $countries = array();

        return $this->render('LockBundle:Lock:list_incidences.html.twig', array('incidences' => $incidences,
                                                                                'pagination' => $pagination,
                                                                                'id_taller'  => $id_taller,
                                                                                'id_socio'   => $id_socio,));
                                                                                // 'countries'  => $countries,
                                                                                // 'country'    => $country, ));
    }

    public function showIncidenceAction($page=1, $id_incidence=null)
    {
    	$em_lock   = $this->getDoctrine()->getEntityManager('em_lock');
        $incidence = $em_lock->getRepository('LockBundle:LockIncidence')->find($id_incidence);

        return $this->render('LockBundle:Lock:show_incidence.html.twig', array('incidence' => $incidence));

    }
}
