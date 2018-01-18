<?php
namespace Adservice\WorkshopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;

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
    public function listDiagnosisMachineAction(Request $request, $page=1, $country='none', $catserv=0) {
        $em = $this->getDoctrine()->getManager();

        if (! $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
             throw new AccessDeniedException();
        }

//        if($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
//            if ($country != 'none') $params[] = array('country', ' = '.$country);
//            else                    $params[] = array();
//        }
//        else $params[] = array('country', ' = '.$this->getUser()->getCountry()->getId());
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN'))
            $params[] = array('category_service', ' = '.$this->getUser()->getCategoryService()->getId());
        if ($catserv != 0) $params[] = array('category_service', ' = '.$catserv);

        if(!isset($params)) $params = array();
         
        $pagination = new Pagination($page);

        $diagnosis_machines = $pagination->getRows($em, 'WorkshopBundle', 'DiagnosisMachine', $params, $pagination);

        $length = $pagination->getRowsLength($em, 'WorkshopBundle', 'DiagnosisMachine', $params);

        $pagination->setTotalPagByLength($length);

//        if($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) $countries = $em->getRepository('UtilBundle:Country')->findAll();
//        else $countries = array();

       if($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN'))
            $catservices = $em->getRepository('UserBundle:CategoryService')->findAll();
        else
            $catservices[] = $this->getUser()->getCategoryService();

        return $this->render('WorkshopBundle:DiagnosisMachine:list_diagnosis_machine.html.twig', array('diagnosis_machines' => $diagnosis_machines,
                                                                                                        'pagination'  => $pagination,
                  //                                                                                      'countries'   => $countries,
                                                                                                        'country'     => $country,
                                                                                                        'catservices' => $catservices,
                                                                                                        'catserv'     => $catserv,));
    }

    /**
     * Obtener los datos de la maquina de diagnosis a partir de su ID para poder editarlo (solo lo puede hacer el ROLE_ADMIN)
     * Si la petición es GET  --> mostrar el formulario
     * Si la petición es POST --> save del formulario
     */
    public function editDiagnosisMachineAction(Request $request, $id) {

        if (! $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            return $this->render('TwigBundle:Exception:exception_access.html.twig');
        }

        $em = $this->getDoctrine()->getManager();
        $diagnosis_machine = new DiagnosisMachine();
        $catserv = $this->getUser()->getCategoryService();
        if ($id != null) {
                          $diagnosis_machine = $em->getRepository("WorkshopBundle:DiagnosisMachine")->find($id);
                          if (!$diagnosis_machine) throw $this->createNotFoundException('Maquina no encontrado en la BBDD');
        }
        
        // Creamos variables de sesion para fitlrar los resultados del formulario
//        if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
//
//            $_SESSION['id_country'] = ' != 0 ';
//
//        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_AD')) {
//            $_SESSION['id_country'] = ' = '.$this->getUser()->getCountry()->getId();
//
//        }else {
//            $_SESSION['id_country'] = ' = '.$partner->getCountry()->getId();
//        }

        $form = $this->createForm(DiagnosisMachineType::class, $diagnosis_machine);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($diagnosis_machine->getName() != '...'){

                if ($form->isValid()) {
                    if($diagnosis_machine->getCategoryService() == null){
                        $diagnosis_machine->setCategoryService($catserv);
                    }
                    //$diagnosis_machine->setName($diagnosis_machine->getName().' ('.$diagnosis_machine->getCountry()->getShortName().')');
                    $this->saveDiagnosisMachine($em, $diagnosis_machine);
                    return $this->redirect($this->generateUrl('diagnosis_machine_list'));
                }
            }else
            {
                $flash = $this->get('translator')->trans('error.bad_introduction.name');
                $this->get('session')->getFlashBag()->add('error', $flash);
            }
        }

        return $this->render('WorkshopBundle:DiagnosisMachine:edit_diagnosis_machine.html.twig', array('diagnosis_machine' => $diagnosis_machine,
                                                                                                      'form_name'          => $form->getName(),
                                                                                                      'catserv'    => $catserv,
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
