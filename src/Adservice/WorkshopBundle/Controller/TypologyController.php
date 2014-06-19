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
    public function listTypologyAction($page=1, $country='none') {
        $em = $this->getDoctrine()->getEntityManager();

        if (! $this->get('security.context')->isGranted('ROLE_ADMIN')) {
             throw new AccessDeniedException();
        }

        if($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            if ($country != 'none') $params[] = array('country', ' = '.$country);
            else                    $params[] = array();
        }
        else $params[] = array('country', ' = '.$this->get('security.context')->getToken()->getUser()->getCountry()->getId());

        $pagination = new Pagination($page);

        $typologies = $pagination->getRows($em, 'WorkshopBundle', 'Typology', $params, $pagination);

        $length = $pagination->getRowsLength($em, 'WorkshopBundle', 'Typology', $params);

        $pagination->setTotalPagByLength($length);

        if($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) $countries = $em->getRepository('UtilBundle:Country')->findAll();
        else $countries = array();

        return $this->render('WorkshopBundle:Typology:list_typology.html.twig', array('typologies' => $typologies,
                                                                                      'pagination' => $pagination,
                                                                                      'countries'  => $countries,
                                                                                      'country'    => $country,));
    }

    /**
     * Obtener los datos de la tipologia a partir de su ID para poder editarlo (solo lo puede hacer el ROLE_ADMIN)
     * Si la petición es GET  --> mostrar el formulario
     * Si la petición es POST --> save del formulario
     */
    public function editTypologyAction($id) {
        if (! $this->get('security.context')->isGranted('ROLE_ADMIN')){
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $typology = new Typology();

        $petition = $this->getRequest();
        if ($id != null) {
                          $typology = $em->getRepository("WorkshopBundle:Typology")->find($id);
                          if (!$typology) throw $this->createNotFoundException('Tipologia no encontrado en la BBDD');
        }
        $form = $this->createForm(new TypologyType(), $typology);

        if ($petition->getMethod() == 'POST') {
            $form->bindRequest($petition);

            //La segunda comparacion ($form->getErrors()...) se hizo porque el request que reciber $form puede ser demasiado largo y hace que la funcion isValid() devuelva false
            $form_errors = $form->getErrors();
	    $form_errors = $form->getErrors();
                if(isset($form_errors[0])) {
                    $form_errors = $form_errors[0];
                    $form_errors = $form_errors->getMessageTemplate();
                }else{ 
                    $form_errors = 'none';
                }
            if ($form->isValid() or $form_errors == 'The uploaded file was too large. Please try to upload a smaller file') {

                $this->saveTypology($em, $typology);
                return $this->redirect($this->generateUrl('typology_list'));
            }
        }

        return $this->render('WorkshopBundle:Typology:edit_typology.html.twig', array('typology'   => $typology,
                                                                                      'form_name'  => $form->getName(),
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
