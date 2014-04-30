<?php
namespace Adservice\LockBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Adservice\UtilBundle\Controller\UtilController;
use Adservice\CarBundle\Entity\Car;
use Adservice\TicketBundle\Entity\Ticket;
use Adservice\TicketBundle\Entity\Post;

class LockController extends Controller
{
    public function historyAction($history=null)
    {
    	if( $history != null ){

			// $em = $this->getDoctrine()->getEntityManager('default'  );
			// $em_old = $this->getDoctrine()->getEntityManager('em_old');

			// $sa = $em->getRepository('UserBundle:User')->find('1');	// SUPER_ADMIN

    		// $old_Coches      = $em_old->getRepository('ImportBundle:old_Coche'    	)->findAll();	// CAR							//			 		//
    		// $old_incidencia  = $em_old->getRepository('ImportBundle:old_Incidencia'	)->findAll();	// TICKET	 			 		//

			// foreach ($old_Coches as $old_Coche)
			// {
			// 	$newCar = UtilController::newEntity(new Car(), $sa);
			// 	/////////////////////////////////////////////////////////////////////////////////////////////////
			// 	////    ESTA SITUACIÓN NO DEBERIA PRODUCIRSE UNA VEZ ESTEN TODAS LAS POBLAICONES CARGADAS    ////
			// 	/////////////////////////////////////////////////////////////////////////////////////////////////
			// 	    if($city == null) $city = $em->getRepository('UtilBundle:City')->find('1');
			// 	/////////////////////////////////////////////////////////////////////////////////////////////////
			// 	$newCar->setVersion    ($em->getRepository('CarBundle:Version')->find($newCar->getIdMMG()));
			// 	$newCar->setModel      ($em->getRepository('CarBundle:Model'  )->findBy($newCar->getVersion()->getModel()));
			// 	$newCar->setBrand      ($em->getRepository('CarBundle:Model'  )->findBy($newCar->getModel()->getBrand()));
			// 	$newCar->setYear       ($newCar->getAno());
			// 	$newCar->setVin        ($newCar->getbastidor());
			// 	$newCar->setMotor	   ($newCar->getMotor());
			// 	$newCar->setModifiedBy($sa);
			// 	$newCar->setModifiedAt(new \DateTime(\date("Y-m-d H:i:s")));
			// 	$em->persist($newCar);
			// 	//UtilController::saveEntity($em, $newCar, $sa);
			// }
		 //    $em->flush();
    		echo 'Importado el historico de coches!!<br>';
    	}
        return $this->render('ImportBundle:Import:import.html.twig');
    }
}
