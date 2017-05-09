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
        $security   = $this->get('security.context');
        $car = $ticket->getCar();
        $formC = $this->createForm(new CarType(), $car);
        // MAGIC: por algun motivo sin esto no carga nombre de Version en edit_car
        // mas info: http://imgur.com/gallery/YsbKHg1
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
                        $vin = $car->getVin();
                        $trans = $this->get('translator');
                        if (strlen($vin) == 17) {
                            if (!strpos(strtolower($vin), 'o')) {
                                $exist_car = '0';
                                $exist_vin = $em->getRepository('CarBundle:Car')->findOneByVin($vin);
                                $exist_num = $em->getRepository('CarBundle:Car')->findOneByPlateNumber($car->getPlateNumber());
                           

                                if ($exist_vin == null AND $exist_num == null) {
                                    $exist_car = '0';
                                } elseif (
                                    ($exist_vin != null AND $exist_num == null)
                                    OR ( $exist_vin == null AND $exist_num != null)
                                    OR ( $exist_vin != null AND $exist_num != null AND $exist_vin->getId() != $exist_num->getId())
                                ) {

                                    
                                    $str = $trans->trans('error.vin_platenumber_not_match') . ': ';
                                    if ($exist_vin != null) {
                                        $str .=' **' . $trans->trans('vin') . '** ' . $exist_vin->getVin() . ' -> ' . $trans->trans('plate_number') . ' ' . $exist_vin->getPlateNumber() . ': ' . $exist_vin->getBrand() . ' ' . $exist_vin->getModel();
                                        if ($exist_vin->getVersion() != null) {
                                            $str .= ' ' . $exist_vin->getVersion()->getName() . ' ';
                                            if ($exist_vin->getMotor() != null) {
                                                $str .= ' [' . $exist_vin->getMotor() . '] ';
                                            }
                                        }
                                    }
                                    if ($exist_vin != null and $exist_num != null)
                                        $str .= ', ';

                                    if ($exist_num != null) {
                                        $str .=' **' . $trans->trans('plate_number') . '** ' . $exist_num->getPlateNumber() . ' -> ' . $trans->trans('vin') . ' ' . $exist_num->getVin() . ': ' . $exist_num->getBrand() . ' ' . $exist_num->getModel();
                                        if ($exist_num->getVersion() != null) {
                                            $str .= ' ' . $exist_num->getVersion()->getName();
                                            if ($exist_num->getMotor() != null) {
                                                $str .= ' [' . $exist_num->getMotor() . ']';
                                            }
                                        }
                                    }
                                    $exist_car = $str;
                                } elseif (
                                    $exist_vin->getBrand()->getId() != $car->getBrand()->getId()
                                    OR
                                    $exist_vin->getModel()->getId() != $car->getModel()->getId()
                                    OR (
                                    $exist_vin->getVersion() != null
                                    AND
                                    $car->getVersion() != null
                                    AND
                                    $exist_vin->getVersion()->getId() != $car->getVersion()->getId()
                                    )
                                ) {
                                    $str = $trans->trans('error.same_vin');
                                    if ($exist_vin != null) {
                                        $str .=' (' . $exist_vin->getVin() . ' -> ' . $exist_vin->getBrand() . ' ' . $exist_vin->getModel();
                                        if ($exist_vin->getVersion() != null) {
                                            $str .= ' ' . $exist_vin->getVersion()->getName();
                                            if ($exist_vin->getMotor() != null) {
                                                $str .= ' [' . $exist_vin->getMotor() . ']';
                                            }
                                        }
                                        $str .= ' )';
                                    }
                                    $exist_car = $str;
                                } elseif (
                                    $exist_num->getBrand()->getId() != $car->getBrand()->getId()
                                    OR
                                    $exist_num->getModel()->getId() != $car->getModel()->getId()
                                    OR (
                                    $exist_num->getVersion() != null
                                    AND
                                    $car->getVersion() != null
                                    AND
                                    $exist_num->getVersion()->getId() != $car->getVersion()->getId()
                                    )
                                ) {
                                    $str = $trans->trans('error.same_platenumber');
                                    if ($exist_num != null) {
                                        $str .=' (' . $exist_num->getPlateNumber() . ' -> ' . $exist_num->getBrand() . ' ' . $exist_num->getModel();
                                        if ($exist_num->getVersion() != null) {
                                            $str .= ' ' . $exist_num->getVersion()->getName();
                                            if ($exist_num->getMotor() != null) {
                                                $str .= ' [' . $exist_num->getMotor() . ']';
                                            }
                                        }
                                        $str .= ' )';
                                    }
                                    $exist_car = $str;
                                }
                                if ($exist_car == '0') {
                                    UtilController::saveEntity($em, $car, $user);
                                    return $this->redirect($this->generateUrl('showTicket', array('id' => $id)));

                                } else {
                                    $this->get('session')->setFlash('error', $exist_car);
                                }
                            } else {
                                $this->get('session')->setFlash('error', $trans->trans('ticket_vin_error_o'));
                            }
                        } else {
                            $this->get('session')->setFlash('error', $trans->trans('ticket_vin_error_length'));
                        }

                        
                    }
                // } else { $this->get('session')->setFlash('error', '¡Error! No has introducido un vehiculo correctamente'); }

            }else{ $this->get('session')->setFlash('error', $this->get('translator')->trans('error.bad_introduction')); }
        }
        if($security->isGranted('ROLE_SUPER_ADMIN') || $security->isGranted('ROLE_ADMIN')){
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
                    'brands'      => $brands,
                    'models'      => $models,
                    'versions'    => $versions
                ));
    }
}
