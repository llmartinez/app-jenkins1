<?php

namespace Adservice\CarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
    public function editCarAction(Request $request, $id, $ticket) {
        $em        = $this->getDoctrine()->getManager();

        $car = $ticket->getCar();
        $formC = $this->createForm(CarType::class, $car, array('status' => $car->getStatus(), 'origin' => $car->getOrigin()));

        if ($request->getMethod() == 'POST') {

            $formC->handleRequest($request);

            //Define CAR
            if ($formC->isValid()) {

                // if ($car->getVersion() != "") {

                $id_brand = $request->request->get('new_car_form_brand');
                $brand = $em->getRepository('CarBundle:Brand')->find($id_brand);
                $id_model = $request->request->get('new_car_form_model');
                $model = $em->getRepository('CarBundle:Model')->find($id_model);
                if(empty($model)){
                    $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans('error.bad_introduction'));
                }
                else {
                    $car->setBrand($brand);
                    $car->setModel($model);

                    //SI NO HA ESCOGIDO VERSION DE DEJA NULL
                    $id_version = $request->request->get('new_car_form_version');
                    if($id_version == 0 && $id_brand != 0){
                        $id_version = null;
                    }
                    if (isset($id_version)){
                        $version = $em->getRepository('CarBundle:Version')->findById($id_version);
                    }
                    else{
                        $id_version = null;
                    }
                    if (isset($version) and isset($version[0])){
                        $car->setVersion($version[0]);
                    }
                    else{
                        $car->setVersion(null);
                    }
                    $car->setVin(strtoupper($car->getVin()));
                    $car->setPlateNumber(strtoupper($car->getPlateNumber()));
                    UtilController::saveEntity($em, $car, $this->getUser());

                    return $this->redirect($this->generateUrl('showTicket', array('id' => $id)));
                }


            }else{ $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans('error.bad_introduction')); }
        }
        if($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') ||
            $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
           $b_query   = $em->createQuery('SELECT b FROM CarBundle:Brand b, CarBundle:Model m WHERE b.id = m.brand ORDER BY b.name');
        }
        else{
           $b_query   = $em->createQuery('SELECT b FROM CarBundle:Brand b, CarBundle:Model m WHERE b.id = m.brand AND b.id <> 0 ORDER BY b.name');
        }
        $brands    = $b_query->getResult();
        //$brands      = $em->getRepository('CarBundle:Brand'        )->findBy(array(), array('name' => 'ASC'));
        $models      = $em->getRepository('CarBundle:Model'        )->findByBrand($car->getBrand()->getId());
        $versions    = $em->getRepository('CarBundle:Version'      )->findByModel($car->getModel()->getId());

        return $this->render('TicketBundle:Layout:edit_car_layout.html.twig', array(
                    'formC'       => $formC->createView(),
                    'ticket'      => $ticket,
                    'car'         => $car,
                    'brands'      => $brands,
                    'models'      => $models,
                    'versions'    => $versions
                ));
    }
    
    /**
     * Cambia el estado de un vehiculo por el valor pasado por parametro
     * @Route("/car/edit/{id}")
     * @ParamConverter("ticket", class="TicketBundle:Ticket")
     * @return url
     */
    public function changeStatusCarAction($id, $status, $ticket_id){
        $em = $this->getDoctrine()->getManager();
        $car = $em->getRepository('CarBundle:Car')->find($id);
        $car->setStatus($status);
        if ($status == 'invented'){
            $car->setOrigin('custom');
        }
        UtilController::saveEntity($em, $car, $this->getUser());
        return $this->redirect($this->generateUrl('editCar', array('id' => $ticket_id)));
    }
}
