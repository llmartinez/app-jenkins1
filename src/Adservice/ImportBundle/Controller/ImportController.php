<?php
namespace Adservice\ImportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
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
use Adservice\LockBundle\Entity\LockCar;
use Adservice\LockBundle\Entity\LockIncidence;
use Adservice\TicketBundle\Entity\Ticket;
use Adservice\CarBundle\Entity\Car;
use Adservice\OrderBundle\Entity\ShopOrder;
use Adservice\OrderBundle\Entity\WorkshopOrder;

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
    		$old_Socios = $em_old->createQuery('SELECT os FROM ImportBundle:old_Socio os WHERE os.id < 60 OR os.id > 78' )->getResult(); // PARTNERS //
			$locations  = $this->getLocations($em);																						 //MAPPING LOCATIONS

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
			$old_Socios   = $em_old->createQuery('SELECT os FROM ImportBundle:old_Socio os WHERE os.id < 60 OR os.id > 78' )->getResult(); 	// PARTNERS //
			$locations    = $this->getLocations($em);																						//MAPPING LOCATIONS
			$all_partners = $em->getRepository('PartnerBundle:Partner')->findAll();															//MAPPING PARTNERS

			foreach ($all_partners as $partner) { $partners[$partner->getCodePartner()] = $partner;		}

			foreach ($old_Socios as $old_Socio)
			{
				$newShop = UtilController::newEntity(new Shop(), $sa);
				$newShop->setName('...');
				//$newShop->setCodeShop($old_Socio->getId());
				$newShop->setPartner($partners[$old_Socio->getId()]);
				$newShop->setActive('1');
				$newShop = $this->setContactFields($em, $old_Socio, $newShop, $locations);
				UtilController::saveEntity($em, $newShop, $sa,false);
			}
			$em->flush();
			$session->set('msg' ,	'Tiendas por defecto importadas correctamente! ('.date("H:i:s").')');
			$session->set('info',  	'Importando Tiendas asociadas (entidad Shop)...');
			$session->set('next',  	'shop');

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
			$old_Tiendas = $em_old->createQuery('SELECT os FROM ImportBundle:old_Socio os WHERE os.id >= 60 AND os.id <= 78' )->getResult(); // PARTNERS //
			$locations   = $this->getLocations($em);																					 	 //MAPPING LOCATIONS
			$partner     = $em->getRepository('PartnerBundle:Partner')->find('28'); //Tiendas asociadas con VEMARE, S.L.

			foreach ($old_Tiendas as $old_Tienda)
			{
				$newShop = UtilController::newEntity(new Shop(), $sa);
				//$newShop->setCodeShop($old_Tienda->getId());
				$newShop->setName($old_Tienda->getNombre());
				$newShop->setPartner($partner);
				$newShop->setActive('1');
				$newShop = $this->setContactFields($em, $old_Tienda, $newShop, $locations);
				UtilController::saveEntity($em, $newShop, $sa,false);
			}
			$em->flush();
			$session->set('msg' ,	'Tiendas importadas correctamente! ('.date("H:i:s").')');
			$session->set('info',  	'Importando usuarios para socios (entidad User de rol AD)...');
			$session->set('next',  	'ad');

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
   			$old_Socios = $em_old->createQuery('SELECT os FROM ImportBundle:old_Socio os WHERE os.id < 60 OR os.id > 78' )->getResult(); // PARTNERS //
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
			$session->set('info',  	'Importando usuarios para asesores (entidad User de rol ASSESSOR)...');
			$session->set('next',  	'assessor');

			//return $this->render('ImportBundle:Import:import.html.twig');
        	return $this->render('ImportBundle:Import:import.html.twig', array('bbdd' => 'ad'));
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
				$newAssessor->setLanguage ($languages[$locations['countries'][$newAssessor->getCountry()->getCountry()]->getLang()]);
				$newAssessor->setActive($old_Asesor->getActive());

				UtilController::saveEntity($em, $newAssessor, $sa, false);
			}
			$em->flush();
			$session->set('msg' ,	'Usuarios para asesores importados correctamente! ('.date("H:i:s").')');
			$session->set('info',  	'Importando talleres (entidad Workshop)...');
			$session->set('next',  	'workshop');

        	//return $this->render('ImportBundle:Import:import.html.twig');
			return $this->render('ImportBundle:Import:import.html.twig', array('bbdd' => 'assessor'));
    	}
// __        _____  ____  _  ______  _   _  ___  ____
// \ \      / / _ \|  _ \| |/ / ___|| | | |/ _ \|  _ \
//  \ \ /\ / / | | | |_) | ' /\___ \| |_| | | | | |_) |
//   \ V  V /| |_| |  _ <| . \ ___) |  _  | |_| |  __/
//    \_/\_/  \___/|_| \_\_|\_\____/|_| |_|\___/|_|

    	elseif( $bbdd == 'workshop' )
    	{
    		$old_Talleres    = $em_old->getRepository('ImportBundle:old_Taller'		)->findAll();	// WORKSHOP	//

			$locations    	 = $this->getLocations($em);											//MAPPING LOCATIONS
			$all_partners 	 = $em->getRepository('PartnerBundle:Partner'  )->findAll();			//MAPPING PARTNERS
			$all_shops    	 = $em->getRepository('PartnerBundle:Shop'     )->findAll();			//MAPPING SHOPS
			$typology    	 = $em->getRepository('WorkshopBundle:Typology')->find('1');			//MAPPING TYPOLOGIES
			$all_adsplus     = $em->getRepository('WorkshopBundle:ADSPlus' )->findAll();			//MAPPING AD-SERVICE PLUS
			//find($old_Taller->getTipologia());

			foreach ($all_partners as $partner) { $partners[$partner->getCodePartner()] = $partner;	}
			foreach ($all_shops    as $shop   ) { $shops   [$shop   ->getId()]    = $shop;	}
			foreach ($all_adsplus  as $adsp   ) { $adsplus [$adsp   ->getIdTallerADS()] = $adsp;	}
			//var_dump($all_shops);die;
			foreach ($old_Talleres as $old_Taller)
			{
				$newWorkshop = UtilController::newEntity(new Workshop(), $sa);
				$newWorkshop->setName 					($old_Taller->getNombre());
				$newWorkshop->setCodeWorkshop 			($old_Taller->getId());
				$newWorkshop->setAddress 				($old_Taller->getDireccion());
				$newWorkshop->setConflictive     		($old_Taller->getConflictivo());
				$newWorkshop->setObservationAdmin 		($old_Taller->getObservaciones());
				$newWorkshop->setObservationAssessor 	($old_Taller->getObservaciones());
				$newWorkshop->setActive	 				($old_Taller->getActive());
				$newWorkshop->setContact 				($old_Taller->getContacto());
				$newWorkshop->setTypology 				($typology);
				$newWorkshop = $this->setContactFields	($em, $old_Taller, $newWorkshop, $locations);

				//COMPROVACION SI EXISTE EL SOCIO
				$idSocio    = $old_Taller->getIdSocio();

				if(isset($partners[$idSocio]))
				{
					$newWorkshop->setPartner ($partners[$idSocio]);
				}
				elseif($idSocio >= 60 AND $idSocio <= 78){
						 $newWorkshop->setShop    ($shops[$idSocio]);
						 $newWorkshop->setPartner ($partners['28']);	//Tiendas asociadas con VEMARE, S.L.
					}
				else 	 $newWorkshop->setPartner ($partners[9999]); //SIN SOCIO

				//setAdServicePlus
				if(isset($partners[$old_Taller->getId()])) $newWorkshop->setAdServicePlus(1);
				else $newWorkshop->setAdServicePlus(0);

				UtilController::saveEntity($em, $newWorkshop, $sa, false);
			}
			$em->flush();
			$session->set('msg' ,	'Talleres importados correctamente! ('.date("H:i:s").')');
			$session->set('info',  	'Importando usuarios para talleres (entidad User de rol USER)...');
			$session->set('next',  	'user');

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
			foreach ($all_languages as $language) { $languages[$language->getLanguage()    ] = $language;	}

			foreach ($old_Talleres  as $old_Taller)
			{
				$newUser = UtilController::newEntity(new User(), $sa);
				$newUser = $this->setUserFields   ($em, $newUser, $role, $old_Taller->getContacto());
				$newUser = $this->setContactFields($em, $old_Taller, $newUser, $locations);
				$newUser->setLanguage ($languages[$locations['countries'][$newUser->getCountry()->getCountry()]->getLang()]);
				$newUser->setActive   ($old_Taller->getActive());
				$newUser->setWorkshop ($workshops[$old_Taller->getId()]);

				if( $newUser->getName() == 'sin-especificar' and $newUser->getSurname() == 'sin-especificar') {
					$newUser->setUsername($workshops[$old_Taller->getId()]->getName());
					$newUser->setName($workshops[$old_Taller->getId()]->getName());
				}
				if($newUser->getEmail1() == '' or $newUser->getEmail1() == '0'){
					$users_email_log[] = $newUser;
				}
				UtilController::saveEntity($em, $newUser, $sa, false);
 			}
 			if(isset($users_email_log)) {
				$session->set('msg' ,	'Usuarios para talleres importados correctamente! ('.date("H:i:s").')');
				$session->set('info',  	'Generarando excel con los ususarios erroneos...
										 Haz click en Importar Lock para importar el historico de coches e incidencias(entidad LockCar y LockIncidence)...');
				$session->set('next',  	'user_log');

				$response = $this->doExcelAction($users_email_log);
				$session->set('response' ,	$response);
 			}
			return $this->render('ImportBundle:Import:import.html.twig', array('bbdd' => 'imported'));
    	}
    	elseif( $bbdd == 'user_log' )
    	{
    		$response = $session->get('response');
 			if(isset($response)) {
 				return $response;
 			}else{
 				return $this->render('ImportBundle:Import:import.html.twig', array('bbdd' => 'imported'));
 			}

    	}
    	else{

			$session->set('info', '<h3>Deseas importar la BBDD antigua de AD-service??</h3>
			<p>Se importaran Socios, Asesores, Talleres y Usuarios.</p>
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
    	$session = $this->get('session');
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
				$newCar  = new LockCar();
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
			$em_lock->flush();

			return $this->render('ImportBundle:Import:import.html.twig', array('bbdd' => 'old_cars', 'num' => $num + $max_rows ));
//return $this->render('ImportBundle:Import:import.html.twig');
		}else{
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
    	$session = $this->get('session');
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
            $all_opers    = $em_old->createQuery('SELECT oo.id, oo.nombre FROM ImportBundle:old_Operacion oo' )->getResult();
            $all_socios   = $em_old->createQuery('SELECT os.id, os.nombre FROM ImportBundle:old_Socio os'     )->getResult();
            $all_talleres = $em_old->createQuery('SELECT ot.id, ot.nombre, ot.idGrupo FROM ImportBundle:old_Taller ot'    )->getResult();

			$em_old->clear(); $em_old->close();

			foreach ($all_asesores as $asesor ) { $asesores[$asesor['id']] = $asesor['nombre']; } 	//MAPPING OLD_ASESOR
			foreach ($all_opers    as $oper   ) { $opers   [$oper  ['id']] = $oper  ['nombre']; } 	//MAPPING OLD_OPERACIONES
			foreach ($all_socios   as $socio  ) { $socios  [$socio ['id']] = $socio ['nombre']; } 	//MAPPING OLD_SOCIO
			foreach ($all_talleres as $taller ) { $talleres[$taller['id']] = array($taller['nombre'],$taller['idGrupo']); } 	//MAPPING OLD_TALLER
			// foreach ($all_coches   as $coche  ) { $coches  [$coche ->getOldId()] = $coche; }

            unset($all_asesores); unset($all_socios); unset($all_talleres); unset($all_opers);

			foreach ($old_Incidences as $old_Incidence)
			{
				$newIncidence  = new LockIncidence();
				$newIncidence->setCoche      ($em_lock->getRepository('LockBundle:LockCar')->findOneBy(array('oldId' => $old_Incidence->getCoche())));

				$newIncidence->setAsesor     ($asesores [$old_Incidence->getAsesor()]);
				$newIncidence->setOper       ($opers    [$old_Incidence->getOper()  ]);
				$newIncidence->setSocio      ($socios   [$old_Incidence->getSocio() ]);
				$newIncidence->setTaller     ($talleres [$old_Incidence->getTaller()][0]);
				// $newIncidence->setCoche      ($coches   [$old_Incidence->getCoche() ]);

				$newIncidence->setIdSocio    ($old_Incidence->getSocio());
				$newIncidence->setIdTaller   ($talleres [$old_Incidence->getTaller()][1]);
				$newIncidence->setDescription($old_Incidence->getDescripcion());
				$newIncidence->setTracing	 ($old_Incidence->getSeguimiento());
				$newIncidence->setSolution   ($old_Incidence->getSolucion   ());
				$newIncidence->setImportance ($old_Incidence->getImportancia());
				$newIncidence->setDate   	 ($old_Incidence->getFecha());
				$newIncidence->setActive	 ($old_Incidence->getActive());
				$em_lock->persist($newIncidence);
				unset($newIncidence);
				//$em_lock->flush();die;
			}
			// foreach ($incidences as $incidence) {
			// 	$em_lock->persist($incidence);
			// }

			unset($old_Incidences);	unset($incidences);	unset($incidence);
			unset($asesores 	);	unset($asesor 	 );
			unset($socios 		);	unset($socio 	 );
			unset($talleres 	);	unset($taller 	 );
			unset($opers 		);	unset($oper 	 );
			$em_lock->flush();
			$em_lock->clear();
			$em_lock->close();

			return $this->render('ImportBundle:Import:import.html.twig', array('bbdd' => 'incidences', 'num' => $num + $max_rows ));
//return $this->render('ImportBundle:Import:import.html.twig');
		}else{
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
        else $entity->setCountry($locations['countries']['spain']);

        /* MAILING */
        // $mailerUser = $this->get('cms.mailer');
        // $mailerUser->setTo('test@ad-service.es');  /* COLOCAR EN PROD -> *//* $mailerUser->setTo($newUser->getEmail1()); */
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

    private function doExcelAction($users_email_log){
        $em = $this->getDoctrine()->getEntityManager();
        $security = $this->get('security.context');

        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->addCacheControlDirective('no-cache', true);
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->setMaxAge(600);
        $response->setSharedMaxAge(600);
        $response->headers->set('Pragma', 'public');
        $date    = new \DateTime();
        $response->setLastModified($date);

        $response->headers->set('Content-Disposition', 'attachment;filename="usuarios_sin_mail_('.date("d-m-Y").').csv"');
        $excel   = $this->createExcelTicket($users_email_log);

        $response->setContent($excel);
        return $response;
    }

    public function createExcelTicket($users_email_log){
        //Creación de cabecera
        $excel ='id;Código Socio;Código Taller;Taller;Contacto;Fijo 1;Fijo 2;Movil 1;Movil 2;Población;Provincia;Dirección;error;';
        $excel.="\n";

        $em = $this->getDoctrine()->getEntityManager();

        foreach ($users_email_log as $row) {
            $excel.=$row->getId().';';
            $excel.=$row->getWorkshop()->getPartner()->getCodePartner().';';
            $excel.=$row->getWorkshop()->getCodeWorkshop().';';
            $excel.=$row->getWorkshop()->getName().';';
            $excel.=$row->getName().';';
            $excel.=$row->getPhoneNumber1().';';
            $excel.=$row->getPhoneNumber2().';';
            $excel.=$row->getMovileNumber1().';';
            $excel.=$row->getMovileNumber2().';';
            $excel.=$row->getRegion().';';
            $excel.=$row->getCity().';';
            $excel.=$row->getAddress().';';
            $error = 'Este taller no tiene email. Contacta con el taller para solucionarlo.';
            $excel.=$error.';';
            $excel.="\n";
        }
        $excel = str_replace(',', '.', $excel);
        return($excel);
    }
// 

    public function testMailingAction()
    {
        $em      = $this->getDoctrine()->getEntityManager();
        $admin   = $em->getRepository('UserBundle:User')->find(1);
        $role    = $em->getRepository('UserBundle:Role'  )->find(1);
        $partner = $em->getRepository('PartnerBundle:Partner')->find(1);
        $workshop= $em->getRepository('WorkshopBundle:Workshop')->find(1);
        $country = $em->getRepository('UtilBundle:Country')->find(1);
        $region  = $em->getRepository('UtilBundle:Region')->find(1);
		$name = 'name TM';
		$city = 'city';
		$address = 'address';
		$postal_code = '08080';
		$phoneNumber1 = '931112233';
		$phoneNumber2 = '931112233';
		$movileNumber1 = '655112233';
		$movileNumber2 = '655112233';
		$fax = '931112233';
        $email1 = 'test@test.es';
        $email2 = 'test@test.es';

		$user = new User();
       	$user->setUsername('userTest');
       	$user->setPassword('test');
       	$user->setActive('0');
       	$user->setActive('1');
       	$user->setName($name);
       	$user->setSurname($name);
       	$user->setCity($city);
       	$user->setPhoneNumber1($phoneNumber1);
        $user->setPhoneNumber2($phoneNumber2);
        $user->setMovileNumber1($movileNumber1);
        $user->setMovileNumber2($movileNumber2);
        $user->setFax($fax);
        $user->setEmail1($email1);
        $user->setEmail2($email1);
        $user->addRole 		($role);
        $user->setRegion 	($region);
        $user->setWorkshop 	($workshop);
        $user->setCountry 	($country);
        $user->setCreatedAt(new \DateTime('today'));
        $user->setModifiedAt(new \DateTime('today'));
        $user->setModifiedBy($admin);

        $shopOrder = new ShopOrder();
        $shopOrder->setAction('action');
        $shopOrder->setRejectionReason('rejection_reason');
        $shopOrder->setWantedAction('action');
        $shopOrder->setPartner($partner);
        $shopOrder->setName($name);
        $shopOrder->setActive('1');
        $shopOrder->setCountry($country);
        $shopOrder->setRegion($region);
        $shopOrder->setCity($city);
        $shopOrder->setAddress($address);
        $shopOrder->setPostalCode($postal_code);
        $shopOrder->setPhoneNumber1($phoneNumber1);
        $shopOrder->setPhoneNumber2($phoneNumber2);
        $shopOrder->setMovileNumber1($movileNumber1);
        $shopOrder->setMovileNumber2($movileNumber2);
        $shopOrder->setFax($fax);
        $shopOrder->setEmail1($email1);
        $shopOrder->setEmail2($email1);
        $shopOrder->setCreatedAt(new \DateTime('today'));
        $shopOrder->setCreatedBy($admin);
        $shopOrder->setModifiedAt(new \DateTime('today'));
        $shopOrder->setModifiedBy($admin);

        $shop           = $em->getRepository('PartnerBundle:Shop'           )->find(1);

        $workshopOrder = new WorkshopOrder();
		$workshopOrder->setName($name);
		$workshopOrder->setCodeWorkshop(0);
		$workshopOrder->setCif(0);
		$workshopOrder->setContactName('contact_name');
		$workshopOrder->setContactSurname('contact_surname');
		$workshopOrder->setPartner($partner);
		$workshopOrder->setShop($shop);
		$workshopOrder->setActive('1');
		$workshopOrder->setTest('0');
		$workshopOrder->setTypology($em->getRepository('WorkshopBundle:Typology')->find(1));
		$workshopOrder->setObservationWorkshop('observation_workshop');
		$workshopOrder->setUpdateAt(new \DateTime('today'));
		$workshopOrder->setLowdateAt(new \DateTime('today'));
		$workshopOrder->setEndtestAt(new \DateTime('today'));
		$workshopOrder->setConflictive('1');
		$workshopOrder->setDiagnosisMachine($em->getRepository('WorkshopBundle:DiagnosisMachine')->find(1));
		$workshopOrder->setObservationWorkshop('observation_workshop');
		$workshopOrder->setObservationAssessor('observation_assessor');
		$workshopOrder->setObservationAdmin('observation_admin');
		$workshopOrder->setIdWorkshop('1');
		$workshopOrder->setAction('action');
		$workshopOrder->setRejectionReason('rejection_reason');
		$workshopOrder->setWantedAction('wanted_action');
		$workshopOrder->setCountry($country);
		$workshopOrder->setRegion($region);
		$workshopOrder->setCity($city);
		$workshopOrder->setAddress($address);
		$workshopOrder->setPostalCode($postal_code);
		$workshopOrder->setPhoneNumber1($phoneNumber1);
		$workshopOrder->setPhoneNumber2($phoneNumber2);
		$workshopOrder->setMovileNumber1($movileNumber1);
		$workshopOrder->setMovileNumber2($movileNumber2);
		$workshopOrder->setFax($fax);
		$workshopOrder->setEmail1($email1);
		$workshopOrder->setEmail2($email2);
		$workshopOrder->setCreatedAt(new \DateTime('today'));
		$workshopOrder->setCreatedBy($admin);
		$workshopOrder->setModifiedAt(new \DateTime('today'));
		$workshopOrder->setModifiedBy($admin);

        $workshop       = $em->getRepository('WorkshopBundle:Workshop' )->find(1);
        $subsystem      = $em->getRepository('TicketBundle:Subsystem'  )->find(1);

        $car = new Car();
		$car->setBrand($em->getRepository('CarBundle:Brand'  )->find(1));
		$car->setModel($em->getRepository('CarBundle:Model'  )->find(1));
		$car->setVersion($em->getRepository('CarBundle:Version')->find(1));
		$car->setCreatedAt(new \DateTime('today'));
		$car->setCreatedBy($admin);
		$car->setModifiedAt(new \DateTime('today'));
		$car->setModifiedBy($admin);

        $ticket = new Ticket();
		$ticket->setCreatedBy($admin);
		$ticket->setAssignedTo(null);
		$ticket->setBlockedBy(null);
		$ticket->setWorkshop($workshop);
		$ticket->setStatus('1');
		$ticket->setImportance('1');
		$ticket->setSubsystem($subsystem);
		$ticket->setCar($car);
		$ticket->setDescription('description');
		$ticket->setSolution('solution');
		$ticket->setCreatedAt(new \DateTime('today'));
		$ticket->setModifiedAt(new \DateTime('today'));
		$ticket->setModifiedBy($admin);

    	/* MAILING */
        $mailer = $this->get('cms.mailer');
        $mailer->setTo('test@ad-service.es');
        $mailer->setFrom('noreply@grupeina.com');
         
        $mailer->setSubject($this->get('translator')->trans('mail.newUser.subject').' TallerTM');
        $mailer->setBody($this->renderView('UtilBundle:Mailing:user_new_mail.html.twig', array('user' => $user, 'password' => 'grupeina')));
        $mailer->sendMailToSpool();
         
        $mailer->setSubject($this->get('translator')->trans('mail.newOrder.subject').' OrderTM');
        $mailer->setBody($this->renderView('UtilBundle:Mailing:order_new_shop_mail.html.twig', array('shopOrder' => $shopOrder)));
        $mailer->sendMailToSpool();
        
        $mailer->setSubject($this->get('translator')->trans('mail.editOrder.subject').$shopOrder->getId());
        $mailer->setBody($this->renderView('UtilBundle:Mailing:order_edit_shop_mail.html.twig', array('shopOrder' => $shopOrder, 'shop'  => $shop )));
        $mailer->sendMailToSpool();
        
        $mailer->setSubject($this->get('translator')->trans('mail.changeOrder.subject').$shopOrder->getId());
        $mailer->setBody($this->renderView('UtilBundle:Mailing:order_change_shop_mail.html.twig', array('shopOrder' => $shopOrder)));
        $mailer->sendMailToSpool();
        
        $mailer->setSubject($this->get('translator')->trans('mail.rejectOrder.subject').$shopOrder->getId());
        $mailer->setBody($this->renderView('UtilBundle:Mailing:order_reject_shop_mail.html.twig', array('shopOrder' => $shopOrder)));
        $mailer->sendMailToSpool();
        
        $mailer->setSubject($this->get('translator')->trans('mail.resendOrder.subject').$shopOrder->getId());
        $mailer->setBody($this->renderView('UtilBundle:Mailing:order_shop_resend_mail.html.twig', array('shopOrder' => $shopOrder, 'action' => 'action')));
        $mailer->sendMailToSpool();
        
        
        $mailer->setSubject($this->get('translator')->trans('mail.removeOrder.subject').$shopOrder->getId());
        $mailer->setBody($this->renderView('UtilBundle:Mailing:order_remove_shop_mail.html.twig', array('shopOrder' => $shopOrder, 'action' => 'action')));
        $mailer->sendMailToSpool();
        
        
        $mailer->setSubject($this->get('translator')->trans('mail.removeOrder.subject').$shopOrder->getId());
        $mailer->setBody($this->renderView('UtilBundle:Mailing:order_remove_shop_mail.html.twig', array('shopOrder' => $shopOrder, 'action' => 'action')));
        $mailer->sendMailToSpool();
        
        $mailer->setSubject($this->get('translator')->trans('mail.acceptOrder.shop.subject').$shop->getId());
        $mailer->setBody($this->renderView('UtilBundle:Mailing:order_accept_shop_mail.html.twig', array('shop'   => $shop, 'action' => 'action')));
        $mailer->sendMailToSpool();
        
        
        $mailer->setSubject($this->get('translator')->trans('mail.newOrder.subject').$workshopOrder->getId());
        $mailer->setBody($this->renderView('UtilBundle:Mailing:order_new_mail.html.twig', array('workshopOrder' => $workshopOrder)));
        $mailer->sendMailToSpool();

        $mailer->setSubject($this->get('translator')->trans('mail.editOrder.subject').$workshopOrder->getId());
        $mailer->setBody($this->renderView('UtilBundle:Mailing:order_edit_mail.html.twig', array('workshopOrder' => $workshopOrder, 'workshop' => $workshop )));
        $mailer->sendMailToSpool();
        
        $mailer->setSubject($this->get('translator')->trans('mail.changeOrder.subject').$workshopOrder->getId());
        $mailer->setBody($this->renderView('UtilBundle:Mailing:order_change_mail.html.twig', array('workshopOrder' => $workshopOrder)));
        $mailer->sendMailToSpool();
        
        $mailer->setSubject($this->get('translator')->trans('mail.rejectOrder.subject').$workshopOrder->getId());
        $mailer->setBody($this->renderView('UtilBundle:Mailing:order_reject_mail.html.twig', array('workshopOrder' => $workshopOrder)));
        $mailer->sendMailToSpool();
        
        $mailer->setSubject($this->get('translator')->trans('mail.resendOrder.subject').$workshopOrder->getId());
        $mailer->setBody($this->renderView('UtilBundle:Mailing:order_resend_mail.html.twig', array('workshopOrder' => $workshopOrder, 'action'=> 'action')));
        $mailer->sendMailToSpool();
        
        $mailer->setSubject($this->get('translator')->trans('mail.removeOrder.subject').$workshopOrder->getId());
        $mailer->setBody($this->renderView('UtilBundle:Mailing:order_remove_mail.html.twig', array('workshopOrder' => $workshopOrder,'action'=> 'action')));
        $mailer->sendMailToSpool();
        
        $mailer->setSubject($this->get('translator')->trans('mail.newUser.subject').$user->getWorkshop());
        $mailer->setBody($this->renderView('UtilBundle:Mailing:user_new_mail.html.twig', array('user' => $user, 'password' => 'grupeina')));
        $mailer->sendMailToSpool();
        
        $mailer->setSubject($this->get('translator')->trans('mail.acceptOrder.subject').$workshop->getId());
        $mailer->setBody($this->renderView('UtilBundle:Mailing:order_accept_mail.html.twig', array('workshop' => $workshop,'action'=> 'action')));
        $mailer->sendMailToSpool();
        
        $mailer->setSubject($this->get('translator')->trans('mail.newTicket.subject').'0');
        $mailer->setBody($this->renderView('UtilBundle:Mailing:ticket_new_mail.html.twig', array('ticket' => $ticket)));
        $mailer->sendMailToSpool();
        
        $mailer->setSubject($this->get('translator')->trans('mail.editTicket.subject').'0');
        $mailer->setBody($this->renderView('UtilBundle:Mailing:ticket_edit_mail.html.twig', array('ticket' => $ticket)));
        $mailer->sendMailToSpool();
        
        $mailer->setSubject($this->get('translator')->trans('mail.deleteTicket.subject').'0');
        $mailer->setBody($this->renderView('UtilBundle:Mailing:ticket_delete_mail.html.twig', array('ticket' => $ticket)));
        $mailer->sendMailToSpool();
        
        $mailer->setSubject($this->get('translator')->trans('mail.answerTicket.subject').'0');
        $mailer->setBody($this->renderView('UtilBundle:Mailing:ticket_answer_mail.html.twig', array('ticket' => $ticket)));
        $mailer->sendMailToSpool();
        
        $mailer->setSubject($this->get('translator')->trans('mail.closeTicket.subject').'0');
        $mailer->setBody($this->renderView('UtilBundle:Mailing:ticket_close_mail.html.twig', array('ticket' => $ticket)));
        $mailer->sendMailToSpool();

        $mailer->setSubject($this->get('translator')->trans('mail.reopenTicket.subject').'0');
        $mailer->setBody($this->renderView('UtilBundle:Mailing:ticket_reopen_mail.html.twig', array('ticket' => $ticket)));
        $mailer->sendMailToSpool();
        
        $mailer->setSubject($this->get('translator')->trans('mail.changePassword.subject').$user->getUsername());
        $mailer->setBody($this->renderView('UtilBundle:Mailing:user_change_password_mail.html.twig', array('user' => $user, 'password' => 'grupeina')));
        $mailer->sendMailToSpool();
        
        $mailer->setSubject($this->get('translator')->trans('mail.newUser.subject').$user->getWorkshop());
        $mailer->setBody($this->renderView('UtilBundle:Mailing:user_new_mail.html.twig', array('user' => $user, 'password' => 'grupeina')));
        $mailer->sendMailToSpool();
        
        return $this->render('ImportBundle:Import:import.html.twig');
    }

}