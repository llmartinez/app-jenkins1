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
        // Esto es Magia: por algun motivo sin esto no carga nombre de Version en edit_car
        if($car->getVersion() != null) $version_name = $ticket->getCar()->getVersion()->getName();
        if ($request->getMethod() == 'POST') {

            $user = $em->getRepository('UserBundle:User')->find($this->get('security.context')->getToken()->getUser()->getId());

            $formC->bindRequest($request);

            //Define CAR
            if ($formC->isValid()) {

                // if ($car->getVersion() != "") {

                    $id_brand = $request->request->get('new_car_form_brand');
                    $brand = $em->getRepository('CarBundle:Brand')->find($id_brand);
                    $id_model = $request->request->get('new_car_form_model');
                    $model = $em->getRepository('CarBundle:Model')->find($id_model);
                    if(empty($model)){
                        $this->get('session')->setFlash('error', $this->get('translator')->trans('error.bad_introduction'));
                    }
                    else {
                        $car->setBrand($brand);
                        $car->setModel($model);

                        //SI NO HA ESCOGIDO VERSION DE DEJA NULL
                        $id_version = $request->request->get('new_car_form_version');
                        if (isset($id_version)){
                            $version = $em->getRepository('CarBundle:Version')->findById($id_version);
                        }
                        else{
                            $id_version = null;
                        }
                        if (isset($version)){
                            $car->setVersion($version[0]);
                        }
                        else{
                            $car->setVersion(null);
                        }
                        UtilController::saveEntity($em, $car, $user);

                        return $this->redirect($this->generateUrl('showTicket', array('id' => $id)));
                    }
                // } else { $this->get('session')->setFlash('error', '¡Error! No has introducido un vehiculo correctamente'); }

            }else{ $this->get('session')->setFlash('error', $this->get('translator')->trans('error.bad_introduction')); }
        }

        $brands      = $em->getRepository('CarBundle:Brand'        )->findBy(array(), array('name' => 'ASC'));
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
