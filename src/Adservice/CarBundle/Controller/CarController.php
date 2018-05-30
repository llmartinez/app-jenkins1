<?php

namespace Adservice\CarBundle\Controller;

use Adservice\CarBundle\Entity\Brand;
use Adservice\CarBundle\Entity\Car;
use Adservice\CarBundle\Entity\Model;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Adservice\UtilBundle\Controller\UtilController as UtilController;
use Adservice\CarBundle\Form\CarType;
use Adservice\UtilBundle\Entity\Pagination;

class CarController extends Controller {

    /**
     * Edita el car
     *
     * @return Response
     */
    public function editCarAction(Request $request, $id, $ticketId = null)
    {
        $em = $this->getDoctrine()->getManager();

        $car = $em->getRepository('CarBundle:Car')->find($id);
        $originalCar = clone $car;

        $ticket = $ticketId ? $em->getRepository('TicketBundle:Ticket')->find($ticketId) : null;

        //Comprobamos si el coche ha cambiado de matrícula
        if ($request->get('new_car_form')['plateNumber'] && $request->get('new_car_form')['plateNumber'] != $car->getPlateNumber()) {

            $carPlateNumber = $em->getRepository('CarBundle:Car')->findOneBy(array('plateNumber' => $request->get('new_car_form')['plateNumber']));

            if ($carPlateNumber instanceof Car) {

                $car = $carPlateNumber;
            }
        }

        if ($car instanceof Car) {

            $formC = $this->createForm(CarType::class, $car, array(
                'status' => $car->getStatus(),
                'origin' => $car->getOrigin()
            ));

            if ($request->getMethod() == 'POST') {

                $formC->handleRequest($request);

                if ($formC->isValid()) {

                    $brand = $em->getRepository('CarBundle:Brand')->find($request->get('new_car_form_brand'));
                    $model = $em->getRepository('CarBundle:Model')->find($request->get('new_car_form_model'));


                    if($brand instanceof Brand && $model instanceof Model) {

                        $versions = $em->getRepository('CarBundle:Version')->findBy(
                            array('id' => $request->get('new_car_form_version'))
                        );

                        if (count($versions)>0) {
                            $car->setMotorId($versions[0]->getMotor());

                            foreach($versions as $version) {
                                if( $version->getMotor()->getName() == $car->getMotor()) {
                                    $car->setMotorId($version->getMotor());
                                }
                            }
                            $car->setVersion($versions[0]);
                        }

                        $car->setBrand($brand);
                        $car->setModel($model);

                        if ($car->getVersion()) {
                            $car->setMotorId($car->getVersion()->getMotor());
                        }

                        //Comprobamos si existe un vehículo con el mismo número de bastidor
                        $vinCar = $em->getRepository('CarBundle:Car')->findOneBy(array('vin' => $car->getVin()));
                        if ($vinCar instanceof Car && $vinCar->getId() != $car->getId()) {

                            $error = $this->get('translator')->trans('error.vin_platenumber_not_match') . ': '.
                                ' **' . $this->get('translator')->trans('vin') . '** ' . $vinCar->getVin() .
                                ' -> ' . $this->get('translator')->trans('plate_number') . ' ' .
                                $vinCar->getPlateNumber() . ': ' . $vinCar->__toString();
                            if ($vinCar->getMotor() != null) {
                                $error .= ' [' . $vinCar->getMotor() . '] ';
                            }

                            $this->get('session')->getFlashBag()->add('error', $error);

                        } else {
                            $this->get('car.helper')->updateCar($originalCar, $car, $ticketId);

                            if ($ticketId) {
                                return $this->redirect($this->generateUrl('showTicket', array('id' => $ticketId)));
                            } else {
                                return $this->redirect($this->generateUrl('car_list'));
                            }
                        }

                    } else {

                        $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans('error.bad_introduction'));
                    }
                }
            }

            if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                $brands = $em->getRepository('CarBundle:Brand')->findAll();
            } else {
                $brands = $em->getRepository('CarBundle:Brand')->findAllBrandsWithoutOther();
            }

            $models = $em->getRepository('CarBundle:Model')->findByBrand($car->getBrand()->getId());
            $versions = $em->getRepository('CarBundle:Version')->findByModel($car->getModel()->getId());

            return $this->render('TicketBundle:Layout:edit_car_layout.html.twig', array(
                'formC' => $formC->createView(),
                'car' => $car,
                'brands' => $brands,
                'models' => $models,
                'versions' => $versions,
                'ticket' => $ticket
            ));
        }

        throw $this->createNotFoundException();
    }
    
    /**
     * Cambia el estado de un vehiculo por el valor pasado por parametro
     *
     * @return RedirectResponse
     */
    public function changeStatusCarAction($id, $status, $ticketId = null)
    {
        $em = $this->getDoctrine()->getManager();
        $car = $em->getRepository('CarBundle:Car')->find($id);
        $car->setStatus($status);

        if ($status == 'invented') {
            $car->setOrigin('custom');
        }

        UtilController::saveEntity($em, $car, $this->getUser());
        return $this->redirectToRoute('editCar', array('id' => $id, 'ticketId' => $ticketId));
    }

    /**
     * Listado de vehiculos
     */
    public function listAction(Request $request, $page=1, $matricula=null)
    {
        $em = $this->getDoctrine()->getManager();

        $params = array();
        if($matricula !== null){
            $params[] = array('plateNumber', " = '".$matricula."'");
        }

        $pagination = new Pagination($page);
        $cars = $pagination->getRows($em, 'CarBundle', 'Car', $params, $pagination);
        $length   = $pagination->getRowsLength($em, 'CarBundle', 'Car', $params);

        $pagination->setTotalPagByLength($length);

        return $this->render('CarBundle:Car:list.html.twig', array(
            'cars' => $cars,
            'pagination'   => $pagination,
            'matricula' => $matricula,
            'length' => $length
        ));
    }
}
