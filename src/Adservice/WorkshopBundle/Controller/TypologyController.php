<?php
namespace Adservice\WorkshopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Adservice\UtilBundle\Controller\UtilController as UtilController;
use Adservice\UtilBundle\Entity\Pagination;
use Adservice\UserBundle\Entity\User;
use Adservice\WorkshopBundle\Entity\Typology;
use Adservice\WorkshopBundle\Form\TypologyType;

class TypologyController extends Controller {

    /**
     * Devuelve todos los talleres segun el usuario logeado
     * @return type
     * @throws AccessDeniedException
     */
    public function listTypologyAction($page=1, $country='none', $catserv=0) {
        $em = $this->getDoctrine()->getManager();
        $security = $this->get('security.context');

        if (! $security->isGranted('ROLE_ADMIN')) {
             throw new AccessDeniedException();
        }

//        if($security->isGranted('ROLE_SUPER_ADMIN')) {
//            if ($country != 'none') $params[] = array('country', ' = '.$country);
//        }
//        else $params[] = array('country', ' = '.$security->getToken()->getUser()->getCountry()->getId());
        
        if(!$security->isGranted('ROLE_SUPER_ADMIN'))
            $params[] = array('category_service', ' = '.$security->getToken()->getUser()->getCategoryService()->getId());
        if ($catserv != 0) $params[] = array('category_service', ' = '.$catserv);

        if(!isset($params)) $params = array();

        $pagination = new Pagination($page);

        $typologies = $pagination->getRows($em, 'WorkshopBundle', 'Typology', $params, $pagination);

        $length = $pagination->getRowsLength($em, 'WorkshopBundle', 'Typology', $params);

        $pagination->setTotalPagByLength($length);

//        if($security->isGranted('ROLE_SUPER_ADMIN')) $countries = $em->getRepository('UtilBundle:Country')->findAll();
//        else $countries = array();
        if($security->isGranted('ROLE_SUPER_ADMIN'))
            $catservices = $em->getRepository('UserBundle:CategoryService')->findAll();
        else
            $catservices[] = $security->getToken()->getUser()->getCategoryService();
        return $this->render('WorkshopBundle:Typology:list_typology.html.twig', array('typologies'  => $typologies,
                                                                                      'pagination'  => $pagination,
//                                                                                      'countries'   => $countries,
                                                                                      'country'     => $country,
                                                                                      'catservices' => $catservices,
                                                                                      'catserv'     => $catserv,));
    }

    /**
     * Obtener los datos de la tipologia a partir de su ID para poder editarlo (solo lo puede hacer el ROLE_ADMIN)
     * Si la petición es GET  --> mostrar el formulario
     * Si la petición es POST --> save del formulario
     */
    public function editTypologyAction($id) {
        $security = $this->get('security.context');
        if (! $security->isGranted('ROLE_ADMIN')){
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $typology = new Typology();
        $catserv = $security->getToken()->getUser()->getCategoryService();
        $petition = $this->getRequest();
        if ($id != null) {
                          $typology = $em->getRepository("WorkshopBundle:Typology")->find($id);
                          if (!$typology) throw $this->createNotFoundException('Tipologia no encontrado en la BBDD');
        }

        // Creamos variables de sesion para fitlrar los resultados del formulario
//        if ($security->isGranted('ROLE_SUPER_ADMIN')) {
//
//            $_SESSION['id_country'] = ' != 0 ';
//
//        }elseif ($security->isGranted('ROLE_SUPER_AD')) {
//            $_SESSION['id_country'] = ' = '.$security->getToken()->getUser()->getCountry()->getId();
//
//        }else {
//            $_SESSION['id_country'] = ' = '.$partner->getCountry()->getId();
//        }

        $form = $this->createForm(new TypologyType(), $typology);

        if ($petition->getMethod() == 'POST') {
            $form->bind($petition);

            if ($form->isValid()) {
                if($typology->getCategoryService() == null){
                    $typology->setCategoryService($catserv);
                }
                $this->saveTypology($em, $typology);
                return $this->redirect($this->generateUrl('typology_list'));
            }
        }
        
        return $this->render('WorkshopBundle:Typology:edit_typology.html.twig', array('typology'   => $typology,
                                                                                      'form_name'  => $form->getName(),
                                                                                      'catserv'    => $catserv,
                                                                                      'form'       => $form->createView()));
    }

    /**
     * Hace el save de un typology
     * @param EntityManager $em
     * @param Typology $typology
     */
    private function saveTypology($em, $typology){
        $em->persist($typology);
        $em->flush();
    }

}
