<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Role;
use AppBundle\Entity\User;

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
			$manager->flush();	
    }
}