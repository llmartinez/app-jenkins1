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

class ImportController extends Controller
{
    public function importAction($importar=null)
    {
		$em = $this->getDoctrine()->getEntityManager('default'  );
		$em_old = $this->getDoctrine()->getEntityManager('emParams1');
		$sa = $em->getRepository('UserBundle:User')->find('1');	// SUPER_ADMIN

    	if( $importar == 'partner' )
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
			echo '- Registros "PARTNER" creados correctamente.<br>';

			foreach ($shops as $shop) {
				UtilController::saveEntity($em, $shop, $sa,false);
			}
			$em->flush();
			echo '- Registros "SHOP_DEFAULT" creados correctamente.<br>';

			foreach ($ads as $ad) {
				UtilController::saveEntity($em, $ad, $sa,false);
			}
			$em->flush();
			echo '- Usuarios de tipo "AD" creados correctamente.<br>';
		}
		elseif( $importar == 'workshop' )
		{
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
				$workshops[] = $newWorkshop;

				$newUser = UtilController::newEntity(new User(), $sa);
				$newUser = $this->setUserFields($em, $newUser, 'ROLE_USER', $old_Taller->getContacto());
				$newUser = $this->setContactFields($em, $old_Taller, $newUser);
				$newUser->setLanguage ($em->getRepository('UtilBundle:Language')->findOneByLanguage($newUser->getCountry()->getLang()));
				$newUser->setActive($old_Taller->getActive());
				$users[] = $newUser;
			}
			foreach ($workshops as $workshop) {
				UtilController::saveEntity($em, $workshop, $sa,false);
			}
			$em->flush();
			echo '- Registros "WORKSHOP" creados correctamente.<br>';

			foreach ($users as $user) {
				UtilController::saveEntity($em, $user, $sa,false);
			}
			echo '- Usuarios de tipo "User" creados correctamente.<br>';
		}
		elseif( $importar == 'assessor' )
		{
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
			echo '- Usuarios de tipo "ASSESSOR" creados correctamente.<br>';
		}
		elseif( $importar == 'system' )
		{
    		$old_Gropers     = $em_old->getRepository('ImportBundle:old_Groper'    	)->findAll();	// SYSTEM						//

			foreach ($old_Gropers as $old_Groper)
			{
				$newSystem = new System();
				$newSystem->setName($old_Groper->getNombre());
				$em->persist($newSystem);
				$em->flush();
			}
			echo '- Registros "SYSTEM" creados correctamente.<br>';
		}
		elseif( $importar == 'subsystem' )
		{
			$old_Operaciones = $em_old->getRepository('ImportBundle:old_Operacion' 	)->findAll();	// SUBSYSTEM 			 		//

			foreach ($old_Operaciones as $old_Operacion)
			{
				$newSubSystem = new Subsystem();
				$newSubSystem->setName($old_Operacion->getNombre());
				$newSubSystem->setSystem($em->getRepository('TicketBundle:System')->find($old_Operacion->getIdGrupo()));
				$em->persist($newSubSystem);
				$em->flush();
			}
			echo '- Registros "SUBSYSTEM" creados correctamente.<br>';
    	}
        return $this->render('ImportBundle:Import:import.html.twig', array('importar' => $importar ));
    }

 	private function setUserFields($em, $entity, $role, $nombre)
	{
			$entity->setUsername   (UtilController::getUsernameUnused($em, $nombre));	/*CREAR USERNAME Y EVITAR REPETICIONES*/
            $entity->setPassword   (substr( md5(microtime()), 1, 8));	/*CREAR PASSWORD AUTOMATICAMENTE*/

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
        $city = $em->getRepository('UtilBundle:City')->findOneByCity($old_entity->getPoblacion());

    /////////////////////////////////////////////////////////////////////////////////////////////////
    ////    ESTA SITUACIÓN NO DEBERIA PRODUCIRSE UNA VEZ ESTEN TODAS LAS POBLAICONES CARGADAS    ////
    /////////////////////////////////////////////////////////////////////////////////////////////////
        if($city == null) $city = $em->getRepository('UtilBundle:City')->find('1');
    /////////////////////////////////////////////////////////////////////////////////////////////////
        $entity->setCity          ($city);

        $entity->setRegion        ($em->getRepository('UtilBundle:Region')->findOneByRegion($entity->getCity()->getRegion()));
        $entity->setCountry       ($em->getRepository('UtilBundle:Country')->findOneByCountry($entity->getRegion()->getCountry()));

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
