<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Workshop;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Role;
use AppBundle\Entity\User;

use AppBundle\Entity\Partner;

class LoadBasicData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
    	$role = new Role();
    	$role->setName('');

		// ROLES
			$role_god = new Role();
			$role_god->setName('ROLE_GOD');
			$manager->persist($role_god);

			$role_super_admin = new Role();
			$role_super_admin->setName('ROLE_SUPER_ADMIN');
			$manager->persist($role_super_admin);

			$role_admin = new Role();
			$role_admin->setName('ROLE_ADMIN');
			$manager->persist($role_admin);

			$role_top = new Role();
			$role_top->setName('ROLE_TOP');
			$manager->persist($role_top);

			$role_super_partner = new Role();
			$role_super_partner->setName('ROLE_SUPER_PARTNER');
			$manager->persist($role_super_partner);

			$role_partner = new Role();
			$role_partner->setName('ROLE_PARTNER');
			$manager->persist($role_partner);

			$role_commercial = new Role();
			$role_commercial->setName('ROLE_COMMERCIAL');
			$manager->persist($role_commercial);

			$role_adviser = new Role();
			$role_adviser->setName('ROLE_ADVISER');
			$manager->persist($role_adviser);

			$role_workshop = new Role();
			$role_workshop->setName('ROLE_WORKSHOP');
			$manager->persist($role_workshop);

			$role_user = new Role();
			$role_user->setName('ROLE_USER');
			$manager->persist($role_user);

		// PERMISSIONS

			$allow_list = new Role();
			$allow_list->setName('ALLOW_LIST');
			$manager->persist($allow_list);

			$allow_create = new Role();
			$allow_create->setName('ALLOW_CREATE');
			$manager->persist($allow_create);

		// SERVICES

			$serv_ged = new Role();
			$serv_ged->setName('SERV_GED');
			$manager->persist($serv_ged);

			$serv_adservice_es = new Role();
			$serv_adservice_es->setName('SERV_ADSERVICE_ES');
			$manager->persist($serv_adservice_es);

			$serv_adservice_pt = new Role();
			$serv_adservice_pt->setName('SERV_ADSERVICE_PT');
			$manager->persist($serv_adservice_pt);

			$serv_assistance_diag_24 = new Role();
			$serv_assistance_diag_24->setName('SERV_ASSISTANCE_DIAG_24');
			$manager->persist($serv_assistance_diag_24);

			$serv_phone_eina_ecp = new Role();
			$serv_phone_eina_ecp->setName('SERV_PHONE_EINA_ECP');
			$manager->persist($serv_phone_eina_ecp);

			$serv_phone_eina_js = new Role();
			$serv_phone_eina_js->setName('SERV_PHONE_EINA_JS');
			$manager->persist($serv_phone_eina_js);

			$serv_phone_eina_technodiag = new Role();
			$serv_phone_eina_technodiag->setName('SERV_PHONE_EINA_TECHNODIAG');
			$manager->persist($serv_phone_eina_technodiag);

			$serv_actia = new Role();
			$serv_actia->setName('SERV_ACTIA');
			$manager->persist($serv_actia);

			$serv_nexus = new Role();
			$serv_nexus->setName('SERV_NEXUS');
			$manager->persist($serv_nexus);

			$manager->flush();

		// USER (god:test)

			$userGod = new User();
			$userGod->addRole($role_god);
			$userGod->setUsername('god');
			$userGod->setPassword('$2y$13$3aa59e12fee9debecc349uGkMVuLFLmFXgT35rUYd33amUSH/vFlG');
			$userGod->setSalt('3aa59e12fee9debecc349384c0719245');
			$userGod->setService(NULL);
			$userGod->setRoleId(1);
			$userGod->setLanguage(1);
			$userGod->setCountry(1);
			$userGod->setStatus(1);
			$userGod->setEmail1('mail@mail.com');
			$userGod->setPhoneNumber1('0');
			$userGod->setCreatedAt(new \DateTime());

			$manager->persist($userGod);

		// USER (superadmin:test)

		$userSA = new User();
		$userSA->addRole($role_super_admin);
		$userSA->setUsername('superadmin');
		$userSA->setPassword('$2y$13$3aa59e12fee9debecc349uGkMVuLFLmFXgT35rUYd33amUSH/vFlG');
		$userSA->setSalt('3aa59e12fee9debecc349384c0719245');
		$userSA->setService(NULL);
		$userSA->setRoleId(2);
		$userSA->setLanguage(1);
		$userSA->setCountry(1);
		$userSA->setStatus(1);
		$userSA->setEmail1('mail@mail.com');
		$userSA->setPhoneNumber1('0');
		$userSA->setCreatedAt(new \DateTime());

		$manager->persist($userSA);

		// USER (admin:test)

		$userA = new User();
		$userA->addRole($role_admin);
		$userA->setUsername('admin');
		$userA->setPassword('$2y$13$3aa59e12fee9debecc349uGkMVuLFLmFXgT35rUYd33amUSH/vFlG');
		$userA->setSalt('3aa59e12fee9debecc349384c0719245');
		$userA->setService(NULL);
		$userA->setRoleId(3);
		$userA->setLanguage(1);
		$userA->setCountry(1);
		$userA->setStatus(1);
		$userA->setEmail1('mail@mail.com');
		$userA->setPhoneNumber1('0');
		$userA->setCreatedAt(new \DateTime());

		$manager->persist($userA);

		// USER (top:test)

		$userT = new User();
		$userT->addRole($role_top);
		$userT->setUsername('top');
		$userT->setPassword('$2y$13$3aa59e12fee9debecc349uGkMVuLFLmFXgT35rUYd33amUSH/vFlG');
		$userT->setSalt('3aa59e12fee9debecc349384c0719245');
		$userT->setService(NULL);
		$userT->setRoleId(4);
		$userT->setLanguage(1);
		$userT->setCountry(1);
		$userT->setStatus(1);
		$userT->setEmail1('mail@mail.com');
		$userT->setPhoneNumber1('0');
		$userT->setCreatedAt(new \DateTime());

		$manager->persist($userT);

		// USER (superpartner:test)

		$userSP = new User();
		$userSP->addRole($role_super_partner);
		$userSP->setUsername('superpartner');
		$userSP->setPassword('$2y$13$3aa59e12fee9debecc349uGkMVuLFLmFXgT35rUYd33amUSH/vFlG');
		$userSP->setSalt('3aa59e12fee9debecc349384c0719245');
		$userSP->setService(NULL);
		$userSP->setRoleId(5);
		$userSP->setLanguage(1);
		$userSP->setCountry(1);
		$userSP->setStatus(1);
		$userSP->setEmail1('mail@mail.com');
		$userSP->setPhoneNumber1('0');
		$userSP->setCreatedAt(new \DateTime());

		$manager->persist($userSP);

		// USER (partner:test)


		$userP = new User();
		$userP->addRole($role_partner);
		$userP->setUsername('partner');
		$userP->setPassword('$2y$13$3aa59e12fee9debecc349uGkMVuLFLmFXgT35rUYd33amUSH/vFlG');
		$userP->setSalt('3aa59e12fee9debecc349384c0719245');
		$userP->setService(NULL);
		$userP->setRoleId(6);
		$userP->setLanguage(1);
		$userP->setCountry(1);
		$userP->setStatus(1);
		$userP->setEmail1('mail@mail.com');
		$userP->setPhoneNumber1('0');
		$userP->setCreatedAt(new \DateTime());

		$manager->persist($userP);

		$new_partner = new Partner();
		$new_partner->setCodePartner(1);
		$new_partner->setName('partner');
		$new_partner->setUser($userP);
		$manager->persist($new_partner);

		// USER (commercial:test)

		$userC = new User();
		$userC->addRole($role_commercial);
		$userC->setUsername('commercial');
		$userC->setPassword('$2y$13$3aa59e12fee9debecc349uGkMVuLFLmFXgT35rUYd33amUSH/vFlG');
		$userC->setSalt('3aa59e12fee9debecc349384c0719245');
		$userC->setService(NULL);
		$userC->setRoleId(7);
		$userC->setLanguage(1);
		$userC->setCountry(1);
		$userC->setStatus(1);
		$userC->setEmail1('mail@mail.com');
		$userC->setPhoneNumber1('0');
		$userC->setCreatedAt(new \DateTime());

		$manager->persist($userC);

		// USER (adviser:test)

		$userAV = new User();
		$userAV->addRole($role_adviser);
		$userAV->setUsername('adviser');
		$userAV->setPassword('$2y$13$3aa59e12fee9debecc349uGkMVuLFLmFXgT35rUYd33amUSH/vFlG');
		$userAV->setSalt('3aa59e12fee9debecc349384c0719245');
		$userAV->setService(NULL);
		$userAV->setRoleId(8);
		$userAV->setLanguage(1);
		$userAV->setCountry(1);
		$userAV->setStatus(1);
		$userAV->setEmail1('mail@mail.com');
		$userAV->setPhoneNumber1('0');
		$userAV->setCreatedAt(new \DateTime());

		$manager->persist($userAV);

		// USER (workshop:test)
/*
		$userW = new User();
		$userW->addRole($role_workshop);
		$userW->setUsername('workshop');
		$userW->setPassword('$2y$13$3aa59e12fee9debecc349uGkMVuLFLmFXgT35rUYd33amUSH/vFlG');
		$userW->setSalt('3aa59e12fee9debecc349384c0719245');
		$userW->setService(NULL);
		$userW->setRoleId(9);
		$userW->setLanguage(1);
		$userW->setCountry(1);
		$userW->setStatus(1);
		$userW->setEmail1('mail@mail.com');
		$userW->setPhoneNumber1('0');
		$userW->setCreatedAt(new \DateTime());

		$manager->persist($userW);

		$new_workshop = new Workshop();
		$new_workshop->setName('workshop');
		$new_workshop->setCodePartner(1);
		$new_workshop->setUser($userW);
		$manager->persist($new_workshop);
*/

		// USER (user:test)

		$userU = new User();
		$userU->addRole($role_user);
		$userU->setUsername('user');
		$userU->setPassword('$2y$13$3aa59e12fee9debecc349uGkMVuLFLmFXgT35rUYd33amUSH/vFlG');
		$userU->setSalt('3aa59e12fee9debecc349384c0719245');
		$userU->setService(NULL);
		$userU->setRoleId(10);
		$userU->setLanguage(1);
		$userU->setCountry(1);
		$userU->setStatus(1);
		$userU->setEmail1('mail@mail.com');
		$userU->setPhoneNumber1('0');
		$userU->setCreatedAt(new \DateTime());

		$manager->persist($userU);
		$manager->flush();
    }
}