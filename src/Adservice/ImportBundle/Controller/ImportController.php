<?php
namespace Adservice\ImportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Adservice\UtilBundle\Controller\UtilController;
use Adservice\UserBundle\Entity\User;
use Adservice\PartnerBundle\Entity\Partner;
use Adservice\PartnerBundle\Entity\Shop;
use Adservice\WorkshopBundle\Entity\Workshop;
use Adservice\WorkshopBundle\Entity\Typology;
use Adservice\TicketBundle\Entity\System;
use Adservice\TicketBundle\Entity\Subsystem;
use Adservice\ImportBundle\Entity\old_Asesor;
use Adservice\ImportBundle\Entity\old_Socio;
use Adservice\ImportBundle\Entity\old_Taller;
use Adservice\ImportBundle\Entity\old_Oper;
use Adservice\LockBundle\Entity\lock_car;
use Adservice\LockBundle\Entity\lock_incidence;

class ImportController extends Controller
{
    public function importAction($bbdd=null)
    {
		/**/echo date("H:i:s");
		$em = $this->getDoctrine()->getEntityManager('default');
		$em_old = $this->getDoctrine()->getEntityManager('em_old');
		$sa = $em->getRepository('UserBundle:User')->find('1');	// SUPER_ADMIN

    	if( $bbdd == 'adservice' )
    	{
    		$old_Socios      = $em_old->getRepository('ImportBundle:old_Socio' 		)->findAll();	// PARTNER  - AD 			 	//

			foreach ($old_Socios as $old_Socio)
			{
				$newPartner = UtilController::newEntity(new Partner(), $sa);
				$newPartner->setName($old_Socio->getNombre());
				$newPartner->setCodePartner($old_Socio->getId());
				$newPartner->setActive('1');
				$newPartner = $this->setContactFields($em, $old_Socio, $newPartner);
				$partners[] = $newPartner;

				/* SHOP 'SIN TIENDA' PARA CADA PARTNER*/
				$newShop = UtilController::newEntity(new Shop(), $sa);
				$newShop->setName('...');
				$newShop->setPartner($newPartner);
				$newShop->setActive('1');
				$newShop = $this->setContactFields($em, $old_Socio, $newShop);
				$shops[] = $newShop;

				$newAD = UtilController::newEntity(new User(), $sa);
				$newAD = $this->setUserFields($em, $newAD, 'ROLE_AD', $old_Socio->getNombre());
				$newAD = $this->setContactFields($em, $old_Socio, $newAD);
				$newAD->setLanguage ($em->getRepository('UtilBundle:Language')->findOneByLanguage($newAD->getCountry()->getLang()));
				$newAD->setActive('1');
				$ads[] = $newAD;
			}
			foreach ($partners as $partner) {
				UtilController::saveEntity($em, $partner, $sa,false);
			}
			$em->flush();

			foreach ($shops as $shop) {
				UtilController::saveEntity($em, $shop, $sa,false);
			}
			$em->flush();

			foreach ($ads as $ad) {
				UtilController::saveEntity($em, $ad, $sa,false);
			}
			$em->flush();

    		$old_Talleres    = $em_old->getRepository('ImportBundle:old_Taller'		)->findAll();	// TYPOLOGY - WORKSHOP - USER 	//

			foreach ($old_Talleres as $old_Taller)
			{
				$newWorkshop = UtilController::newEntity(new Workshop(), $sa);
				$newWorkshop->setName($old_Taller->getNombre());
				$newWorkshop->setCodeWorkshop($old_Taller->getIdGrupo());
				$newWorkshop->setPartner($em->getRepository('PartnerBundle:Partner')->findOneBy(array('code_partner' => $old_Taller->getIdSocio())));
				$newWorkshop->setTypology($em->getRepository('WorkshopBundle:Typology')->find('1')); //find($old_Taller->getTipologia());
				$newWorkshop->setAddress($old_Taller->getDireccion());
				$newWorkshop->setActive($old_Taller->getActive());
				$newWorkshop->setContactName($old_Taller->getContacto());
				$newWorkshop->setContactSurname('sin-especificar');
				$newWorkshop->setObservationAdmin($old_Taller->getObservaciones());
				$newWorkshop->setObservationAssessor($old_Taller->getObservaciones());
				$newWorkshop = $this->setContactFields($em, $old_Taller, $newWorkshop);

				$newUser = UtilController::newEntity(new User(), $sa);
				$newUser = $this->setUserFields($em, $newUser, 'ROLE_USER', $old_Taller->getContacto());
				$newUser = $this->setContactFields($em, $old_Taller, $newUser);
				$newUser->setLanguage ($em->getRepository('UtilBundle:Language')->findOneByLanguage($newUser->getCountry()->getLang()));
				$newUser->setActive($old_Taller->getActive());
				$workshops[] = array('workshop' => $newWorkshop, 'user' => $newUser);
			}
			foreach ($workshops as $workshop) {
				UtilController::saveEntity($em, $workshop['workshop'], $sa, false);
				$workshop['user']->setWorkshop($workshop['workshop']);
				UtilController::saveEntity($em, $workshop['user'], $sa, false);
			}
			$em->flush();

			$old_Asesores    = $em_old->getRepository('ImportBundle:old_Asesor'		)->findAll();	// ASSESSOR 					//

			foreach ($old_Asesores as $old_Asesor)
			{
				$newAssessor = UtilController::newEntity(new User(), $sa);
				$newAssessor = $this->setUserFields($em, $newAssessor, 'ROLE_ASSESSOR', $old_Asesor->getNombre());
				$newAssessor = $this->setContactFields($em, $old_Asesor, $newAssessor);
				$newAssessor->setLanguage ($em->getRepository('UtilBundle:Language')->findOneByLanguage($newAssessor->getCountry()->getLang()));
				$newAssessor->setActive($old_Asesor->getActive());
				$assessors[] = $newAssessor;
			}
			foreach ($assessors as $assessor) {
				UtilController::saveEntity($em, $assessor, $sa,false);
			}

    		$old_Gropers     = $em_old->getRepository('ImportBundle:old_Groper'    	)->findAll();	// SYSTEM						//

			foreach ($old_Gropers as $old_Groper)
			{
				$newSystem = new System();
				$newSystem->setName($old_Groper->getNombre());
				$em->persist($newSystem);
				$em->flush();
			}

			$old_Operaciones = $em_old->getRepository('ImportBundle:old_Operacion' 	)->findAll();	// SUBSYSTEM 			 		//

			foreach ($old_Operaciones as $old_Operacion)
			{
				$newSubSystem = new Subsystem();
				$newSubSystem->setName($old_Operacion->getNombre());
				$newSubSystem->setSystem($em->getRepository('TicketBundle:System')->find($old_Operacion->getIdGrupo()));
				$em->persist($newSubSystem);
				$em->flush();
			}
        	/**/echo '<br> IMPORTADA BBDD!! <br>';
/**/echo date("H:i:s");
//return $this->render('ImportBundle:Import:import.html.twig', array('bbdd' => 'old_cars' ));
        	return $this->render('ImportBundle:Import:import.html.twig');
    	}

        return $this->render('ImportBundle:Import:import.html.twig');
    }

    public function importLockCarsAction($bbdd=null, $num=0)
    {
		/**/echo date("H:i:s");
		$em_old  = $this->getDoctrine()->getEntityManager('em_old' );
		$em_lock = $this->getDoctrine()->getEntityManager('em_lock');
		$max_rows = 1000;
 		$consulta = $em_old ->createQuery('SELECT oc FROM ImportBundle:old_Coche oc')
                        	->setFirstResult($num)
                        	->setMaxResults($max_rows);

		$old_Coches = $consulta->getResult();

		$count = $em_old->createQuery('SELECT count(oc) FROM ImportBundle:old_Coche oc')->getResult()[0][1];

		if($count > $num){

			foreach ($old_Coches as $old_Coche)
			{
				$newCar  = new lock_car();
				$gama   = $em_old->getRepository('ImportBundle:old_Gama'  )->find($old_Coche->getIdMMG());
				$modelo = $em_old->getRepository('ImportBundle:old_Modelo')->find($gama     ->getModelo());
				$marca  = $em_old->getRepository('ImportBundle:old_Marca' )->find($modelo   ->getMarca());

				$newCar->setOldId     ($old_Coche->getId());
				$newCar->setVersion   ($gama);
				$newCar->setModel     ($modelo);
				$newCar->setBrand     ($marca);
				$newCar->setYear      ($old_Coche->getAno());
				$newCar->setVin       ($old_Coche->getbastidor());
				$newCar->setMotor	  ($old_Coche->getMotor());
				$cars[] = $newCar;
			}
			foreach ($cars as $car) {
				$em_lock->persist($car);
			}
			$em_lock->flush();

			return $this->render('ImportBundle:Import:import.html.twig', array('bbdd' => 'old_cars', 'num' => $num + $max_rows ));
        	//return $this->render('ImportBundle:Import:import.html.twig');
		}else{
			//return $this->render('ImportBundle:Import:import.html.twig', array('bbdd' => 'old_incidences'));
        	/**/echo '<br> IMPORTADA BBDD CARS!! <br>';
			/**/echo date("H:i:s");
        	return $this->render('ImportBundle:Import:import.html.twig');
		}
    }

 	public function importLockIncidencesAction($bbdd=null, $num=0)
    {
		/**/echo date("d/m/Y H:i:s");

		$em_old   = $this->getDoctrine()->getEntityManager('em_old' );
		$em_lock  = $this->getDoctrine()->getEntityManager('em_lock');
		$max_rows = 1000;

 		$consulta = $em_old ->createQuery('SELECT oi FROM ImportBundle:old_Incidencia oi')
                        	->setFirstResult($num)
                        	->setMaxResults($max_rows);

		$old_Incidences = $consulta->getResult();

		$count = $em_old->createQuery('SELECT count(oi) FROM ImportBundle:old_Incidencia oi')->getResult()[0][1];

		if($count > $num){

			foreach ($old_Incidences as $old_Incidence)
			{
				$newIncidence  = new lock_incidence();

				$newIncidence->setAsesor     ($em_old->getRepository('ImportBundle:old_Asesor'   )->find($old_Incidence->getAsesor())->getNombre());
				$newIncidence->setSocio      ($em_old->getRepository('ImportBundle:old_Socio'    )->find($old_Incidence->getSocio() )->getNombre());
				$newIncidence->setTaller     ($em_old->getRepository('ImportBundle:old_Taller'   )->find($old_Incidence->getTaller())->getNombre());
				$newIncidence->setOper       ($em_old->getRepository('ImportBundle:old_Operacion')->find($old_Incidence->getOper()  )->getNombre());
				$newIncidence->setCoche      ($em_lock->getRepository('LockBundle:lock_car')->findOneBy(array('oldId' => $old_Incidence->getCoche())));

				$newIncidence->setOldId      ($old_Incidence->getId());
				$newIncidence->setDescription($old_Incidence->getDescripcion());
				$newIncidence->setTracing	 ($old_Incidence->getSeguimiento());
				$newIncidence->setSolution   ($old_Incidence->getSolucion   ());
				$newIncidence->setImportance ($old_Incidence->getImportancia());
				$newIncidence->setDate   	 ($old_Incidence->getFecha());
				$newIncidence->setActive	 ($old_Incidence->getActive());
				$incidences[] = $newIncidence;
			}
			foreach ($incidences as $incidence) {
				$em_lock->persist($incidence);
			}
			$em_lock->flush();

			return $this->render('ImportBundle:Import:import.html.twig', array('bbdd' => 'old_incidences', 'num' => $num + $max_rows ));
        	//return $this->render('ImportBundle:Import:import.html.twig');
		}else{
        	/**/echo 'IMPORTADA INCIDENCES!! <br>';
			/**/echo date("d/m/Y H:i:s");
			return $this->render('ImportBundle:Import:import.html.twig', array('bbdd' => 'complete'));
		}
	}

    private function setUserFields($em, $entity, $role, $nombre)
    {
		$entity->setUsername   (UtilController::getUsernameUnused($em, $nombre));	/*CREAR USERNAME Y EVITAR REPETICIONES*/
        $entity->setPassword   ('grupeina'); //(substr( md5(microtime()), 1, 8));	/*CREAR PASSWORD AUTOMATICAMENTE*/

        //password nuevo, se codifica con el nuevo salt
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($entity);
        $salt = md5(time());
        $password = $encoder->encodePassword($entity->getPassword(), $salt);
        $entity->setPassword($password);
        $entity->setSalt($salt);

        //Se separa de la BBDD antigua el nombre y los apellidos
        $nombre_completo = explode (' ', $nombre);
        $name 	 = $nombre_completo[0];
        $surname = "";
        for($i=1; $i< count($nombre_completo);$i++){ $surname = $surname.' '.$nombre_completo[$i]; }
        	if($name == '')    $name 	= 'sin-especificar';
        	if($surname == '') $surname = 'sin-especificar';

        $entity->setName          ($name);
        $entity->setSurname       ($surname);
        $entity->addRole          ($em->getRepository('UserBundle:Role')->findOneByName($role));

		return $entity;
    }

    private function setContactFields($em, $old_entity, $entity)
    {
        $entity->setPhoneNumber1  ($old_entity->getTfno());
        $entity->setPhoneNumber2  ($old_entity->getTfno2());
        $entity->setMovileNumber1 ($old_entity->getMovil());
        $entity->setMovileNumber2 ($old_entity->getMovil2());
        $entity->setFax           ($old_entity->getFax());
        $entity->setEmail1        ($old_entity->getEmail());
        $entity->setEmail2        ($old_entity->getEmail2());

        $cities    = $em->getRepository('UtilBundle:City'  )->findAll();
        $regions   = $em->getRepository('UtilBundle:Region')->findAll();

        $slug_city   = UtilController::getSlug($old_entity->getPoblacion());
        $slug_region = UtilController::getSlug($old_entity->getProvincia());

        $entity->setCity  (UtilController::normalizeString($slug_city  , $cities ));
        $entity->setRegion(UtilController::normalizeString($slug_region, $regions));

        $region = $em->getRepository('UtilBundle:Region')->findOneByRegion($entity->getRegion());

        if( $region != null ) $entity->setCountry($em->getRepository('UtilBundle:Country')->findOneByCountry($region->getCountry()));
        else $entity->setCountry($em->getRepository('UtilBundle:Country')->findOneByCountry('Spain'));

        /* MAILING */
        // $mailerUser = $this->get('cms.mailer');
        // $mailerUser->setTo('dmaya@grupeina.com');  /* COLOCAR EN PROD -> *//* $mailerUser->setTo($newUser->getEmail1()); */
        // $mailerUser->setSubject($this->get('translator')->trans('mail.newUser.subject').$newUser->getWorkshop());
        // $mailerUser->setFrom('noreply@grupeina.com');
        // $mailerUser->setBody($this->renderView('UtilBundle:Mailing:user_new_mail.html.twig', array('user' => $newUser, 'password' => $pass)));
        // $mailerUser->sendMailToSpool();
        // echo $this->renderView('UtilBundle:Mailing:user_new_mail.html.twig', array('user' => $newUser, 'password' => $pass));die;

		return $entity;
	}
}
