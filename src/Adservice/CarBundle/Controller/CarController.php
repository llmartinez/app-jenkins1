<?php

namespace Adservice\CarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Adservice\UtilBundle\Controller\UtilController as UtilController;
use Adservice\CarBundle\Form\CarType;

class CarController extends Controller {

    /**
     * Edita el car asignado a partir de su id
     * @Route("/car/edit/{id}")
     * @ParamConverter("ticket", class="TicketBundle:Ticket")
     * @return url
     */
    public function editCarAction($id, $ticket) {
        $em        = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();

        $car = $ticket->getCar();
        $formC = $this->createForm(new CarType(), $car);

        if ($request->getMethod() == 'POST') {

            $user = $em->getRepository('UserBundle:User')->find($this->get('security.context')->getToken()->getUser()->getId());

            $formC->bindRequest($request);

            //Define CAR
            //La segunda comparacion ($form->getErrors()...) se hizo porque el request que reciber $form puede ser demasiado largo y hace que la funcion isValid() devuelva false
            $formC_errors = $formC->getErrors();
	    if(isset($formC_errors[0])) {
                $formC_errors = $formC_errors[0];
                $formC_errors = $formC_errors->getMessageTemplate();
            }else{
                $formC_errors = 'none';
            }
            if ($formC->isValid() or $formC_errors == 'The uploaded file was too large. Please try to upload a smaller file') {

                // if ($car->getVersion() != "") {

                    $id_brand = $request->request->get('new_car_form_brand');
                    $brand = $em->getRepository('CarBundle:Brand')->find($id_brand);
                    $id_model = $request->request->get('new_car_form_model');
                    $model = $em->getRepository('CarBundle:Model')->find($id_model);
                    $id_version = $request->request->get('new_car_form_version');
                    $version = $em->getRepository('CarBundle:Version')->find($id_version);

                    $car->setBrand($brand);
                    $car->setModel($model);
                    $car->setVersion($version);
                    UtilController::saveEntity($em, $car, $user);

                    return $this->redirect($this->generateUrl('showTicket', array('id' => $id)));

                // } else { $this->get('session')->setFlash('error', '¡Error! No has introducido un vehiculo correctamente'); }

            }else{ $this->get('session')->setFlash('error', $this->get('translator')->trans('error.bad_introduction')); }
        }

        $brands      = $em->getRepository('CarBundle:Brand'        )->findAll();
        $models      = $em->getRepository('CarBundle:Model'        )->findByBrand($car->getBrand()->getId());
        $versions    = $em->getRepository('CarBundle:Version'      )->findByModel($car->getModel()->getId());

        return $this->render('TicketBundle:Layout:edit_car_layout.html.twig', array(
                    'formC'       => $formC->createView(),
                    'ticket'      => $ticket,
                    'brands'      => $brands,
                    'models'      => $models,
                    'versions'    => $versions
                ));
    }
}
