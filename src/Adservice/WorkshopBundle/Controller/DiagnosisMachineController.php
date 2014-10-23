<?php
namespace Adservice\WorkshopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Adservice\UtilBundle\Controller\UtilController as UtilController;
use Adservice\UtilBundle\Entity\Pagination;
use Adservice\UserBundle\Entity\User;
use Adservice\WorkshopBundle\Entity\DiagnosisMachine;
use Adservice\WorkshopBundle\Form\DiagnosisMachineType;

class DiagnosisMachineController extends Controller {

    /**
     * Devuelve todos los talleres segun el usuario logeado
     * @return type
     * @throws AccessDeniedException
     */
    public function listDiagnosisMachineAction($page=1, $country='none') {

        $security = $this->get('security.context');
        if ($security->isGranted('ROLE_ADMIN') === false) {
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getEntityManager();

        $dql = 'SELECT e FROM WorkshopBundle:DiagnosisMachine e WHERE e.id > 0 ';

        if($security->isGranted('ROLE_SUPER_ADMIN')) {
            if ($country != 'none') $dql .=' AND e.country = '.$country.' ';
        }
        else $dql .=' AND e.country = '.$security->getToken()->getUser()->getCountry()->getId().' ';

        $pagination = new Pagination($page, $em, $dql);

        $diagnosis_machines = $pagination->getResult();

        if($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) $countries = $em->getRepository('UtilBundle:Country')->findAll();
        else $countries = array();

        return $this->render('WorkshopBundle:DiagnosisMachine:list_diagnosis_machine.html.twig', array('diagnosis_machines' => $diagnosis_machines,
                                                                                                       'pagination'         => $pagination,
                                                                                                       'countries'          => $countries,
                                                                                                       'country'            => $country,));
    }

    /**
     * Obtener los datos de la maquina de diagnosis a partir de su ID para poder editarlo (solo lo puede hacer el ROLE_ADMIN)
     * Si la petición es GET  --> mostrar el formulario
     * Si la petición es POST --> save del formulario
     */
    public function editDiagnosisMachineAction($id) {
        if (! $this->get('security.context')->isGranted('ROLE_ADMIN')){
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $diagnosis_machine = new DiagnosisMachine();

        $petition = $this->getRequest();
        if ($id != null) {
                          $diagnosis_machine = $em->getRepository("WorkshopBundle:DiagnosisMachine")->find($id);
                          if (!$diagnosis_machine) throw $this->createNotFoundException('Maquina no encontrado en la BBDD');
        }
        $form = $this->createForm(new DiagnosisMachineType(), $diagnosis_machine);

        if ($petition->getMethod() == 'POST') {
            $form->bindRequest($petition);
            if ($diagnosis_machine->getName() != '...'){
                //La segunda comparacion ($form->getErrors()...) se hizo porque el request que reciber $form puede ser demasiado largo y hace que la funcion isValid() devuelva false
                $form_errors = $form->getErrors();
                    if(isset($form_errors[0])) {
                        $form_errors = $form_errors[0];
                        $form_errors = $form_errors->getMessageTemplate();
                    }else{
                        $form_errors = 'none';
                    }
                if ($form->isValid() or $form_errors == 'The uploaded file was too large. Please try to upload a smaller file') {

                    $this->saveDiagnosisMachine($em, $diagnosis_machine);
                    return $this->redirect($this->generateUrl('diagnosis_machine_list'));
                }
            }else
            {
                $flash = 'No puedes insertar el nombre "..."';
                $this->get('session')->setFlash('error', $flash);
            }
        }

        return $this->render('WorkshopBundle:DiagnosisMachine:edit_diagnosis_machine.html.twig', array('diagnosis_machine' => $diagnosis_machine,
                                                                                                      'form_name'          => $form->getName(),
                                                                                                      'form'               => $form->createView()));
    }

    /**
     * Hace el save de un diagnosis_machine
     * @param EntityManager $em
     * @param DiagnosisMachine $diagnosis_machine
     */
    private function saveDiagnosisMachine($em, $diagnosis_machine){
        $em->persist($diagnosis_machine);
        $em->flush();
    }

}
