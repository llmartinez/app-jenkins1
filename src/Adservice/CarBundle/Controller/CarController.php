<?php

namespace Adservice\CarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Adservice\UtilBundle\Controller\UtilController as UtilController;
use Symfony\Component\HttpFoundation\Response;
use Adservice\CarBundle\Form\CarType;

class CarController extends Controller {

    /**
     * Edita el car asignado a partir de su id
     * @param integer $id_ticket
     * @return url
     */
    public function editCarAction($id_ticket) {
        $em        = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();

        $ticket = $em->getRepository('TicketBundle:Ticket')->find($id_ticket);
        $car = $ticket->getCar();

        $formC = $this->createForm(new CarType(), $car);

        if ($request->getMethod() == 'POST') {

            $user = $em->getRepository('UserBundle:User')->find($this->get('security.context')->getToken()->getUser()->getId());

            $formC->bindRequest($request);

            //Define CAR
            //La segunda comparacion ($form->getErrors()...) se hizo porque el request que reciber $form puede ser demasiado largo y hace que la funcion isValid() devuelva false
            if ($formC->isValid() or $formC->getErrors()[0]->getMessageTemplate() == 'The uploaded file was too large. Please try to upload a smaller file') {

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

                    return $this->redirect($this->generateUrl('showTicket', array('id_ticket' => $id_ticket)));

                // } else { $this->get('session')->setFlash('error', '¡Error! No has introducido un vehiculo correctamente'); }

            }else{ $this->get('session')->setFlash('error', '¡Error! No has introducido los valores correctamente'); }
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
