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
    	$session = $this->get('session');
		$em 	 = $this->getDoctrine()->getEntityManager('default');
		$em_old  = $this->getDoctrine()->getEntityManager('em_old');
		$sa 	 = $em->getRepository('UserBundle:User')->find('1');	// SUPER_ADMIN

//  ____   _    ____ _____ _   _ _____ ____
// |  _ \ / \  |  _ \_   _| \ | | ____|  _ \
// | |_) / _ \ | |_) || | |  \| |  _| | |_) |
// |  __/ ___ \|  _ < | | | |\  | |___|  _ <
// |_| /_/   \_\_| \_\|_| |_| \_|_____|_| \_\

    	if( $bbdd == 'partner' )
    	{
    		$old_Socios = $em_old->getRepository('ImportBundle:old_Socio')->findAll();	// PARTNERS //
    		// $old_Socios = $em_old->getRepository('ImportBundle:old_Socio')->findBy(array('asociado' => 0));	// PARTNERS //

			$locations     = $this->getLocations($em);												//MAPPING LOCATIONS

			foreach ($old_Socios as $old_Socio)
			{
				$newPartner = UtilController::newEntity(new Partner(), $sa);
				$newPartner->setName($old_Socio->getNombre());
				$newPartner->setCodePartner($old_Socio->getId());
				$newPartner->setActive('1');
				$newPartner = $this->setContactFields($em, $old_Socio, $newPartner, $locations);
				UtilController::saveEntity($em, $newPartner, $sa,false);
			}
			$em->flush();
			$session->set('msg' ,	'Socios importados correctamente! ('.date("H:i:s").')');
			$session->set('info',  	'Importando tiendas por defecto (entidad Shop)...');
			$session->set('next',  	'shop-default');

/***************************************************************************************************************/
		$session->set('time-'.$bbdd, array('time-'.$bbdd => date("H:i:d -- d/m/Y")));
		var_dump($session);
/***************************************************************************************************************/
			//return $this->render('ImportBundle:Import:import.html.twig');
        	return $this->render('ImportBundle:Import:import.html.twig', array('bbdd' => 'partner'));
    	}
//  ____  _   _  ___  ____       ____  _____ _____ _   _   _ _   _____
// / ___|| | | |/ _ \|  _ \     |  _ \| ____|  ___/ \ | | | | | |_   _|
// \___ \| |_| | | | | |_) |____| | | |  _| | |_ / _ \| | | | |   | |
//  ___) |  _  | |_| |  __/_____| |_| | |___|  _/ ___ \ |_| | |___| |
// |____/|_| |_|\___/|_|        |____/|_____|_|/_/   \_\___/|_____|_|

		elseif( $bbdd == 'shop-default' )
		{
			$old_Socios = $em_old->getRepository('ImportBundle:old_Socio')->findAll();	// PARTNERS //
			// $old_Socios = $em_old->getRepository('ImportBundle:old_Socio')->findBy(array('asociado' => 0));	// PARTNERS //

			$locations    = $this->getLocations($em);											//MAPPING LOCATIONS
			$all_partners = $em->getRepository('PartnerBundle:Partner')->findAll();				//MAPPING PARTNERS

			foreach ($all_partners as $partner) { $partners[$partner->getCodePartner()] = $partner;		}

			foreach ($old_Socios as $old_Socio)
			{
				$newShop = UtilController::newEntity(new Shop(), $sa);
				$newShop->setName('...');
				$newShop->setPartner($partners[$old_Socio->getId()]);
				$newShop->setActive('1');
				$newShop = $this->setContactFields($em, $old_Socio, $newShop, $locations);
				UtilController::saveEntity($em, $newShop, $sa,false);
			}
			$em->flush();
			$session->set('msg' ,	'Tiendas por defecto importadas correctamente! ('.date("H:i:s").')');
			$session->set('info',  	'Importando Tiendas asociadas (entidad Shop)...');
			$session->set('next',  	'shop');

/***************************************************************************************************************/
		$session->set('time-'.$bbdd, array('time-'.$bbdd => date("H:i:d -- d/m/Y")));
		var_dump($session);
/***************************************************************************************************************/
			//return $this->render('ImportBundle:Import:import.html.twig');
        	return $this->render('ImportBundle:Import:import.html.twig', array('bbdd' => 'shop-default'));
    	}
//  ____  _   _  ___  ____
// / ___|| | | |/ _ \|  _ \
// \___ \| |_| | | | | |_) |
//  ___) |  _  | |_| |  __/
// |____/|_| |_|\___/|_|

    	elseif( $bbdd == 'shop' )
    	{
			// $old_Socios = $em_old->getRepository('ImportBundle:old_Socio')->findAll();	// SHOPS //
			// // $old_Socios = $em_old->getRepository('ImportBundle:old_Socio')->getEntityNotEqual($em_old, 'ImportBundle', 'old_Socio', 'asociado', 0)	// SHOPS //

			// $locations    = $this->getLocations($em);											//MAPPING LOCATIONS
			// $all_partners = $em->getRepository('PartnerBundle:Partner')->findAll();				//MAPPING PARTNERS

			// foreach ($old_Socios as $old_Socio)
			// {
			// 	$newShop = UtilController::newEntity(new Shop(), $sa);
			// 	$newShop->setName($old_Socio->getNombre());
			// 	$newShop->setPartner($partners[$old_Socio->getAsociado()]);
			// 	$newShop->setActive('1');
			// 	$newShop = $this->setContactFields($em, $old_Socio, $newShop, $locations);
			// 	UtilController::saveEntity($em, $newShop, $sa,false);
			// }
			// $em->flush();
			$session->set('msg' ,	'Tiendas importadas correctamente! ('.date("H:i:s").')');
			$session->set('info',  	'Importando usuarios para socios (entidad User de rol AD)...');
			$session->set('next',  	'ad');

/***************************************************************************************************************/
		$session->set('time-'.$bbdd, array('time-'.$bbdd => date("H:i:d -- d/m/Y")));
		var_dump($session);
/***************************************************************************************************************/
			//return $this->render('ImportBundle:Import:import.html.twig');
        	return $this->render('ImportBundle:Import:import.html.twig', array('bbdd' => 'shop'));
    	}
//  _   _ ____  _____ ____       _    ____
// | | | / ___|| ____|  _ \     / \  |  _ \
// | | | \___ \|  _| | |_) |   / _ \ | | | |
// | |_| |___) | |___|  _ <   / ___ \| |_| |
//  \___/|____/|_____|_| \_\ /_/   \_\____/

    	elseif( $bbdd == 'ad' )
    	{
   			$old_Socios = $em_old->getRepository('ImportBundle:old_Socio')->findAll();	// SHOPS //
			// $old_Socios = $em_old->getRepository('ImportBundle:old_Socio')->findBy(array('asociado' => 0));	// PARTNERS //

			$locations     = $this->getLocations($em);												//MAPPING LOCATIONS
			$all_partners  = $em->getRepository('PartnerBundle:Partner')->findAll();				//MAPPING PARTNERS
			$role          = $em->getRepository('UserBundle:Role'      )->findOneByName('ROLE_AD');	//ROLE

			foreach ($all_partners as $partner) { $partners[$partner->getCodePartner()] = $partner;	}

			foreach ($old_Socios as $old_Socio)
			{
				$newAD = UtilController::newEntity(new User(), $sa);
				$newAD = $this->setUserFields   ($em, $newAD, $role, $old_Socio->getNombre());
				$newAD = $this->setContactFields($em, $old_Socio, $newAD, $locations);
				$newAD->setLanguage ($em->getRepository('UtilBundle:Language')->findOneByLanguage($newAD->getCountry()->getLang()));
				$newAD->setActive('1');
				$newAD->setPartner($partners[$old_Socio->getId()]);
				UtilController::saveEntity($em, $newAD, $sa,false);
			}
			$em->flush();
			$session->set('msg' ,	'Usuarios para socios importados correctamente! ('.date("H:i:s").')');
			$session->set('info',  	'Importando talleres (entidad Workshop)...');
			$session->set('next',  	'workshop');

/***************************************************************************************************************/
		$session->set('time-'.$bbdd, array('time-'.$bbdd => date("H:i:d -- d/m/Y")));
		var_dump($session);
/***************************************************************************************************************/
			//return $this->render('ImportBundle:Import:import.html.twig');
        	return $this->render('ImportBundle:Import:import.html.twig', array('bbdd' => 'ad'));
    	}
// __        _____  ____  _  ______  _   _  ___  ____
// \ \      / / _ \|  _ \| |/ / ___|| | | |/ _ \|  _ \
//  \ \ /\ / / | | | |_) | ' /\___ \| |_| | | | | |_) |
//   \ V  V /| |_| |  _ <| . \ ___) |  _  | |_| |  __/
//    \_/\_/  \___/|_| \_\_|\_\____/|_| |_|\___/|_|

    	elseif( $bbdd == 'workshop' )
    	{
    		$old_Talleres    = $em_old->getRepository('ImportBundle:old_Taller'		)->findAll();	// WORKSHOP	//

			$locations    = $this->getLocations($em);											//MAPPING LOCATIONS
			$all_partners = $em->getRepository('PartnerBundle:Partner'  )->findAll();			//MAPPING PARTNERS
			$typology     = $em->getRepository('WorkshopBundle:Typology')->find('1');			//MAPPING TYPOLOGIES
			//find($old_Taller->getTipologia());

			foreach ($all_partners as $partner) { $partners[$partner->getCodePartner()] = $partner;	}

			foreach ($old_Talleres as $old_Taller)
			{
				$newWorkshop = UtilController::newEntity(new Workshop(), $sa);
				$newWorkshop->setName 					($old_Taller->getNombre());
				$newWorkshop->setCodeWorkshop 			($old_Taller->getId());
				$newWorkshop->setAddress 				($old_Taller->getDireccion());
				$newWorkshop->setObservationAdmin 		($old_Taller->getObservaciones());
				$newWorkshop->setObservationAssessor 	($old_Taller->getObservaciones());
				$newWorkshop->setActive	 				($old_Taller->getActive());
				$newWorkshop->setContactName 			($old_Taller->getContacto());
				$newWorkshop->setContactSurname 		('sin-especificar');
				$newWorkshop->setTypology 				($typology); //$partners[$old_Socio->getId()]);
				$newWorkshop = $this->setContactFields	($em, $old_Taller, $newWorkshop, $locations);
				//COMPROVACION SI EXISTE EL SOCIO
				if(isset($partners[$old_Taller->getIdGrupo()])) $newWorkshop->setPartner ($partners[$old_Taller->getIdGrupo()]);
				else 											$newWorkshop->setPartner ($partners[9999]); //SIN SOCIO

				UtilController::saveEntity($em, $newWorkshop, $sa, false);
				$em->flush();
			}
			$session->set('msg' ,	'Talleres importados correctamente! ('.date("H:i:s").')');
			$session->set('info',  	'Importando usuarios para talleres (entidad User de rol USER)...');
			$session->set('next',  	'user');

/***************************************************************************************************************/
		$session->set('time-'.$bbdd, array('time-'.$bbdd => date("H:i:d -- d/m/Y")));
		var_dump($session);
/***************************************************************************************************************/
			//return $this->render('ImportBundle:Import:import.html.twig');
        	return $this->render('ImportBundle:Import:import.html.twig', array('bbdd' => 'workshop'));
    	}
//  _   _ ____  _____ ____
// | | | / ___|| ____|  _ \
// | | | \___ \|  _| | |_) |
// | |_| |___) | |___|  _ <
//  \___/|____/|_____|_| \_\

    	elseif( $bbdd == 'user' )
    	{
    		$old_Talleres    = $em_old->getRepository('ImportBundle:old_Taller'		)->findAll();		// USER 	//

			$locations     = $this->getLocations($em);													//MAPPING LOCATIONS
			$all_workshops = $em->getRepository('WorkshopBundle:Workshop')->findAll();					//MAPPING WORKSHOPS
			$all_languages = $em->getRepository('UtilBundle:Language'    )->findAll();					//MAPPING LANG
			$role          = $em->getRepository('UserBundle:Role'        )->findOneByName('ROLE_USER');	//ROLE

			foreach ($all_workshops as $workshop) { $workshops[$workshop->getCodeWorkshop()] = $workshop;	}
			foreach ($all_languages as $language) { $languages[$language->getLanguage()] = $language;		}

			foreach ($old_Talleres as $old_Taller)
			{
				$newUser = UtilController::newEntity(new User(), $sa);
				$newUser = $this->setUserFields   ($em, $newUser, $role, $old_Taller->getContacto());
				$newUser = $this->setContactFields($em, $old_Taller, $newUser, $locations);
				$newUser->setLanguage ($languages[$locations['countries'][$newUser->getCountry()->getCountry()]->getLang()]);
				$newUser->setActive   ($old_Taller->getActive());
				$newUser->setWorkshop ($workshops[$old_Taller->getId()]);

				UtilController::saveEntity($em, $newUser, $sa, false);
 			}
			$em->flush();
			$session->set('msg' ,	'Usuarios para talleres importados correctamente! ('.date("H:i:s").')');
			$session->set('info',  	'Importando usuarios para asesores (entidad User de rol ASSESSOR)...');
			$session->set('next',  	'assessor');

/***************************************************************************************************************/
		$session->set('time-'.$bbdd, array('time-'.$bbdd => date("H:i:d -- d/m/Y")));
		var_dump($session);
/***************************************************************************************************************/
			//return $this->render('ImportBundle:Import:import.html.twig');
        	return $this->render('ImportBundle:Import:import.html.twig', array('bbdd' => 'workshop'));
    	}
//     _    ____ ____  _____ ____ ____   ___  ____
//    / \  / ___/ ___|| ____/ ___/ ___| / _ \|  _ \
//   / _ \ \___ \___ \|  _| \___ \___ \| | | | |_) |
//  / ___ \ ___) |__) | |___ ___) |__) | |_| |  _ <
// /_/   \_\____/____/|_____|____/____/ \___/|_| \_\

    	elseif( $bbdd == 'assessor' )
    	{
			$old_Asesores  = $em_old->getRepository('ImportBundle:old_Asesor' )->findAll();				// ASSESSOR

			$locations     = $this->getLocations($em);													//MAPPING LOCATIONS
			$all_languages = $em->getRepository('UtilBundle:Language')->findAll();						//MAPPING LANG
			$role          = $em->getRepository('UserBundle:Role' )->findOneByName('ROLE_ASSESSOR');	//ROLE

			foreach ($all_languages as $language) { $languages[$language->getLanguage()] = $language;		}

			foreach ($old_Asesores as $old_Asesor)
			{
				$newAssessor = UtilController::newEntity(new User(), $sa);
				$newAssessor = $this->setUserFields   ($em, $newAssessor, $role, $old_Asesor->getNombre());
				$newAssessor = $this->setContactFields($em, $old_Asesor, $newAssessor, $locations);
				$newUser->setLanguage ($languages[$locations['countries'][$newUser->getCountry()->getCountry()]->getLang()]);
				$newAssessor->setActive($old_Asesor->getActive());

				UtilController::saveEntity($em, $newAssessor, $sa, false);
			}
			$em->flush();
			$session->set('msg' ,	'Usuarios para asesores importados correctamente! ('.date("H:i:s").')');
			$session->set('info',  	'Importando historico de coches e incidencias(entidad lock_car y lock_incidence)...');

/***************************************************************************************************************/
		$session->set('time-'.$bbdd, array('time-'.$bbdd => date("H:i:d -- d/m/Y")));
		var_dump($session);
/***************************************************************************************************************/
/***************************************************************************************************************/
			//return $this->render('ImportBundle:Import:import.html.twig');
        	return $this->render('ImportBundle:Import:import.html.twig', array('bbdd' => 'old_cars'));

/***************************************************************************************************************/
    	}
    	else{

			$session->set('info', '<h3>Deseas importar la BBDD antigua de AD-service??</h3>
			<p>Se importaran Socios, Talleres, Usuarios y Asesores.</p>
			<p>Se creará el historico de coches e incidencias de los datos antiguos.</p>');

        	return $this->render('ImportBundle:Import:import.html.twig');
        }
    }
//  _     ___   ____ _  __   ____    _    ____  ____
// | |   / _ \ / ___| |/ /  / ___|  / \  |  _ \/ ___|
// | |  | | | | |   | ' /  | |     / _ \ | |_) \___ \
// | |__| |_| | |___| . \  | |___ / ___ \|  _ < ___) |
// |_____\___/ \____|_|\_\  \____/_/   \_\_| \_\____/

    public function importLockCarsAction($bbdd=null, $num=0)
    {
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
				unset($newCar);
			}
			foreach ($cars as $car) {
				$em_lock->persist($car);
			}
/***************************************************************************************************************/
		$session->set('time-'.$bbdd, array('time-'.$bbdd => date("H:i:d -- d/m/Y")));
		var_dump($session);
/***************************************************************************************************************/
			$em_lock->flush();

			return $this->render('ImportBundle:Import:import.html.twig', array('bbdd' => 'old_cars', 'num' => $num + $max_rows ));
//return $this->render('ImportBundle:Import:import.html.twig');
		}else{
/***************************************************************************************************************/
		$session->set('bbdd', array($bbdd => date("H:i:d -- d/m/Y")));
		var_dump($session);
/***************************************************************************************************************/
        	return $this->render('ImportBundle:Import:import.html.twig', array('bbdd' => 'incidences', 'num' => 0));
		}
    }
//  ___ _   _  ____ ___ ____  _____ _   _  ____ _____ ____
// |_ _| \ | |/ ___|_ _|  _ \| ____| \ | |/ ___| ____/ ___|
//  | ||  \| | |    | || | | |  _| |  \| | |   |  _| \___ \
//  | || |\  | |___ | || |_| | |___| |\  | |___| |___ ___) |
// |___|_| \_|\____|___|____/|_____|_| \_|\____|_____|____/

 	public function importLockIncidencesAction($bbdd=null, $num=0)
    {
		$em_old   = $this->getDoctrine()->getEntityManager('em_old' );
		$em_lock  = $this->getDoctrine()->getEntityManager('em_lock');
		$max_rows = 1000;

 		$consulta = $em_old ->createQuery('SELECT oi FROM ImportBundle:old_Incidencia oi')
                        	->setFirstResult($num)
                        	->setMaxResults($max_rows);

		$old_Incidences = $consulta->getResult();

		$count = $em_old->createQuery('SELECT count(oi) FROM ImportBundle:old_Incidencia oi')->getResult()[0][1];

		if($count > $num){

            $all_asesores = $em_old->createQuery('SELECT oa.id, oa.nombre FROM ImportBundle:old_Asesor oa'    )->getResult();
            $all_socios   = $em_old->createQuery('SELECT os.id, os.nombre FROM ImportBundle:old_Socio os'     )->getResult();
            $all_talleres = $em_old->createQuery('SELECT ot.id, ot.nombre FROM ImportBundle:old_Taller ot'    )->getResult();
            $all_opers    = $em_old->createQuery('SELECT oo.id, oo.nombre FROM ImportBundle:old_Operacion oo' )->getResult();

			$em_old->clear(); $em_old->close();

			foreach ($all_asesores as $asesor ) { $asesores[$asesor['id']] = $asesor['nombre']; } 	//MAPPING OLD_ASESOR
			foreach ($all_socios   as $socio  ) { $socios  [$socio ['id']] = $socio ['nombre']; } 	//MAPPING OLD_SOCIO
			foreach ($all_talleres as $taller ) { $talleres[$taller['id']] = $taller['nombre']; } 	//MAPPING OLD_TALLER
			foreach ($all_opers    as $oper   ) { $opers   [$oper  ['id']] = $oper  ['nombre']; } 	//MAPPING OLD_OPERACIONES
			// foreach ($all_coches   as $coche  ) { $coches  [$coche ->getOldId()] = $coche; }

            unset($all_asesores); unset($all_socios); unset($all_talleres); unset($all_opers);

			foreach ($old_Incidences as $old_Incidence)
			{
				$newIncidence  = new lock_incidence();
				$newIncidence->setCoche      ($em_lock->getRepository('LockBundle:lock_car')->findOneBy(array('oldId' => $old_Incidence->getCoche())));

				$newIncidence->setAsesor     ($asesores [$old_Incidence->getAsesor()]);
				$newIncidence->setSocio      ($socios   [$old_Incidence->getSocio() ]);
				$newIncidence->setTaller     ($talleres [$old_Incidence->getTaller()]);
				$newIncidence->setOper       ($opers    [$old_Incidence->getOper()  ]);
				// $newIncidence->setCoche      ($coches   [$old_Incidence->getCoche() ]);

				$newIncidence->setOldId      ($old_Incidence->getId());
				$newIncidence->setDescription($old_Incidence->getDescripcion());
				$newIncidence->setTracing	 ($old_Incidence->getSeguimiento());
				$newIncidence->setSolution   ($old_Incidence->getSolucion   ());
				$newIncidence->setImportance ($old_Incidence->getImportancia());
				$newIncidence->setDate   	 ($old_Incidence->getFecha());
				$newIncidence->setActive	 ($old_Incidence->getActive());
				$em_lock->persist($newIncidence);
				unset($newIncidence);
			}
			// foreach ($incidences as $incidence) {
			// 	$em_lock->persist($incidence);
			// }

			unset($old_Incidences);	unset($incidences   );	unset($incidence);
			unset($asesores 	);	unset($asesor 	);
			unset($socios 		);	unset($socio 	);
			unset($talleres 	);	unset($taller 	);
			unset($opers 		);	unset($oper 	);
/***************************************************************************************************************/
		$session->set('time-'.$bbdd, array('time-'.$bbdd => date("H:i:d -- d/m/Y")));
		var_dump($session);
/***************************************************************************************************************/
			$em_lock->flush();
			$em_lock->clear();
			$em_lock->close();

			return $this->render('ImportBundle:Import:import.html.twig', array('bbdd' => 'incidences', 'num' => $num + $max_rows ));
//return $this->render('ImportBundle:Import:import.html.twig');
		}else{
/***************************************************************************************************************/
		$session->set('bbdd', array($bbdd => date("H:i:d -- d/m/Y")));
		var_dump($session);
/***************************************************************************************************************/
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
        $entity->addRole          ($role);

		return $entity;
    }

    private function setContactFields($em, $old_entity, $entity, $locations)
    {
        $entity->setPhoneNumber1  ($old_entity->getTfno());
        $entity->setPhoneNumber2  ($old_entity->getTfno2());
        $entity->setMovileNumber1 ($old_entity->getMovil());
        $entity->setMovileNumber2 ($old_entity->getMovil2());
        $entity->setFax           ($old_entity->getFax());
        $entity->setEmail1        ($old_entity->getEmail());
        $entity->setEmail2        ($old_entity->getEmail2());

        $slug_city   = UtilController::getSlug($old_entity->getPoblacion());
        $slug_region = UtilController::getSlug($old_entity->getProvincia());

        $entity->setCity  (UtilController::normalizeString($slug_city  , $locations['cities' ]));
        $entity->setRegion(UtilController::normalizeString($slug_region, $locations['regions']));

        $region  = $em->getRepository('UtilBundle:Region')->findOneByRegion($entity->getRegion());
        if( $region != null ) {
        	$country = $region->getCountry()->getCountry();
        	$entity->setCountry($locations['countries'][$country]);
        }
        else $entity->setCountry($locations['countries']['Spain']);

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

    private function getEntityNotEqual($em, $bundle, $entity, $field, $value)
    {
    	$sql = "SELECT e FROM ".$bundle.":".$entity." e WHERE e.".$field." != ".$value;

        $query = $em->createQuery($sql);
        $result = $query->getResult();

		return $result;
	}
    private function getLocations($em)
    {
		//MAPPING LOCATIONS
		$all_cities    = $em->getRepository('UtilBundle:City'   )->findAll();
		$all_regions   = $em->getRepository('UtilBundle:Region' )->findAll();
		$all_countries = $em->getRepository('UtilBundle:Country')->findAll();

		foreach ($all_cities as $city      ) { $cities[]    = $city->getCity();       }
		foreach ($all_regions as $region   ) { $regions[]   = $region->getRegion();   }
		foreach ($all_countries as $country) { $countries[$country->getCountry()] = $country; }

		$locations = array(	'countries' => $countries,
							'regions'   => $regions,
							'cities'    => $cities
						   );
		return $locations;
	}
}