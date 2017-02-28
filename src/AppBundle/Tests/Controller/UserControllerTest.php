<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use AppBundle\Utils\Slugger as Slugger;
use AppBundle\Utils\UtilsUser as UtilsUser;
class UserControllerTest extends WebTestCase
{
    public $client = null;
    public $id = null;
    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testNewGod()
    {
        SecurityControllerTest::LogInGod($this);

        $crawler = $this->client->request('GET', '/en/users/user-new/1');

        $form = $crawler->selectButton('Submit')->form();
        $form = self::getGenericForm($form);
        // sustituye algunos valores
        $form['user_new[username]'] = 'godtest';

        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('IndexUsers', $breadcrumbs);
    }

    public function testEditGod()
    {
        SecurityControllerTest::LogInGod($this);
        $em =  $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('AppBundle:User')->findOneByUsername('godtest');
        $this->assertEquals('godtest', $user->getUsername());

        $crawler = $this->client->request('GET', '/en/users');
        $link = $crawler->filter('a:contains(">>")')->eq(0)->link();
        $crawler = $this->client->click($link);
        $link = $crawler->filter('a:contains("Edit")')->last()->link();
        $crawler = $this->client->click($link);
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('IndexUsersRoleUserGod', $breadcrumbs);
        $form = $crawler->selectButton('Submit')->form();

        // sustituye algunos valores
        $form['user_edit[username]'] = 'godtest2';

        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('IndexUsers', $breadcrumbs);

        $user = $em->getRepository('AppBundle:User')->findOneByUsername('godtest2');
        $this->assertEquals('godtest2', $user->getUsername());
    }

    public function testDeleteGod()
    {
        SecurityControllerTest::LogInGod($this);
        $em =  $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('AppBundle:User')->findOneByUsername('godtest2');
        $this->assertEquals('godtest2', $user->getUsername());

        $crawler = $this->client->request('GET', '/en/users');
        $link = $crawler->filter('a:contains(">>")')->eq(0)->link();
        $crawler = $this->client->click($link);
        $link = $crawler->filter('a:contains("Delete")')->last()->link();
        $crawler = $this->client->click($link);
        $user = $em->getRepository('AppBundle:User')->findOneByUsername('godtest2');
        $this->assertEquals(null, $user);

    }

    public function testNewSuperAdmin()
    {
        SecurityControllerTest::LogInGod($this);

        $crawler = $this->client->request('GET', '/en/users/user-new/2');

        $form = $crawler->selectButton('Submit')->form();
        $form = self::getGenericForm($form);
        // sustituye algunos valores
        $form['user_new[username]'] = 'superadmintest';

        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('IndexUsers', $breadcrumbs);
    }

    public function testEditSuperAdmin()
    {
        SecurityControllerTest::LogInGod($this);
        $em =  $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('AppBundle:User')->findOneByUsername('superadmintest');
        $this->assertEquals('superadmintest', $user->getUsername());

        $crawler = $this->client->request('GET', '/en/users');
        $link = $crawler->filter('a:contains(">>")')->eq(0)->link();
        $crawler = $this->client->click($link);
        $link = $crawler->filter('a:contains("Edit")')->last()->link();
        $crawler = $this->client->click($link);
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('IndexUsersRoleUserSuperAdmin', $breadcrumbs);
        $form = $crawler->selectButton('Submit')->form();

        // sustituye algunos valores
        $form['user_edit[username]'] = 'superadmintest2';

        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('IndexUsers', $breadcrumbs);

        $user = $em->getRepository('AppBundle:User')->findOneByUsername('superadmintest2');
        $this->assertEquals('superadmintest2', $user->getUsername());
    }

    public function testDeleteSuperAdmin()
    {
        SecurityControllerTest::LogInGod($this);
        $em =  $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('AppBundle:User')->findOneByUsername('superadmintest2');
        $this->assertEquals('superadmintest2', $user->getUsername());

        $crawler = $this->client->request('GET', '/en/users');
        $link = $crawler->filter('a:contains(">>")')->eq(0)->link();
        $crawler = $this->client->click($link);
        $link = $crawler->filter('a:contains("Delete")')->last()->link();
        $crawler = $this->client->click($link);
        $user = $em->getRepository('AppBundle:User')->findOneByUsername('superadmintest2');
        $this->assertEquals(null, $user);

    }

    public function testNewAdmin()
    {
        SecurityControllerTest::LogInGod($this);

        $crawler = $this->client->request('GET', '/en/users/user-new/3');

        $form = $crawler->selectButton('Submit')->form();
        $form = self::getGenericForm($form);
        // sustituye algunos valores
        $form['user_new[username]'] = 'admintest';

        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('IndexUsers', $breadcrumbs);
    }

    public function testEditAdmin()
    {
        SecurityControllerTest::LogInGod($this);
        $em =  $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('AppBundle:User')->findOneByUsername('admintest');
        $this->assertEquals('admintest', $user->getUsername());

        $crawler = $this->client->request('GET', '/en/users');
        $link = $crawler->filter('a:contains(">>")')->eq(0)->link();
        $crawler = $this->client->click($link);
        $link = $crawler->filter('a:contains("Edit")')->last()->link();
        $crawler = $this->client->click($link);
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('IndexUsersRoleUserAdmin', $breadcrumbs);
        $form = $crawler->selectButton('Submit')->form();

        // sustituye algunos valores
        $form['user_edit[username]'] = 'admintest2';

        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('IndexUsers', $breadcrumbs);

        $user = $em->getRepository('AppBundle:User')->findOneByUsername('admintest2');
        $this->assertEquals('admintest2', $user->getUsername());
    }

    public function testDeleteAdmin()
    {
        SecurityControllerTest::LogInGod($this);
        $em =  $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('AppBundle:User')->findOneByUsername('admintest2');
        $this->assertEquals('admintest2', $user->getUsername());

        $crawler = $this->client->request('GET', '/en/users');
        $link = $crawler->filter('a:contains(">>")')->eq(0)->link();
        $crawler = $this->client->click($link);
        $link = $crawler->filter('a:contains("Delete")')->last()->link();
        $crawler = $this->client->click($link);
        $user = $em->getRepository('AppBundle:User')->findOneByUsername('admintest2');
        $this->assertEquals(null, $user);

    }

    public function testNewTop()
    {
        SecurityControllerTest::LogInGod($this);

        $crawler = $this->client->request('GET', '/en/users/user-new/4');

        $form = $crawler->selectButton('Submit')->form();
        $form = self::getGenericForm($form);
        // sustituye algunos valores
        $form['user_new[username]'] = 'toptest';

        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('IndexUsers', $breadcrumbs);
    }

    public function testEditTop()
    {
        SecurityControllerTest::LogInGod($this);
        $em =  $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('AppBundle:User')->findOneByUsername('toptest');
        $this->assertEquals('toptest', $user->getUsername());

        $crawler = $this->client->request('GET', '/en/users');
        $link = $crawler->filter('a:contains(">>")')->eq(0)->link();
        $crawler = $this->client->click($link);
        $link = $crawler->filter('a:contains("Edit")')->last()->link();
        $crawler = $this->client->click($link);
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('IndexUsersRoleUserTop', $breadcrumbs);
        $form = $crawler->selectButton('Submit')->form();

        // sustituye algunos valores
        $form['user_edit[username]'] = 'toptest2';

        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('IndexUsers', $breadcrumbs);

        $user = $em->getRepository('AppBundle:User')->findOneByUsername('toptest2');
        $this->assertEquals('toptest2', $user->getUsername());
    }

    public function testDeleteTop()
    {
        SecurityControllerTest::LogInGod($this);
        $em =  $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('AppBundle:User')->findOneByUsername('toptest2');
        $this->assertEquals('toptest2', $user->getUsername());

        $crawler = $this->client->request('GET', '/en/users');
        $link = $crawler->filter('a:contains(">>")')->eq(0)->link();
        $crawler = $this->client->click($link);
        $link = $crawler->filter('a:contains("Delete")')->last()->link();
        $crawler = $this->client->click($link);
        $user = $em->getRepository('AppBundle:User')->findOneByUsername('toptest2');
        $this->assertEquals(null, $user);

    }

    public function testNewSuperPartner()
    {
        SecurityControllerTest::LogInGod($this);

        $crawler = $this->client->request('GET', '/en/users/user-new/5');

        $form = $crawler->selectButton('Submit')->form();
        $form = self::getGenericForm($form);
        // sustituye algunos valores
        $form['user_new[username]'] = 'superpartnertest';

        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('IndexUsers', $breadcrumbs);
    }

    public function testEditSuperPartner()
    {
        SecurityControllerTest::LogInGod($this);
        $em =  $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('AppBundle:User')->findOneByUsername('superpartnertest');
        $this->assertEquals('superpartnertest', $user->getUsername());

        $crawler = $this->client->request('GET', '/en/users');
        $link = $crawler->filter('a:contains(">>")')->eq(0)->link();
        $crawler = $this->client->click($link);
        $link = $crawler->filter('a:contains("Edit")')->last()->link();
        $crawler = $this->client->click($link);
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('IndexUsersRoleUserSuperPartner', $breadcrumbs);
        $form = $crawler->selectButton('Submit')->form();

        // sustituye algunos valores
        $form['user_edit[username]'] = 'superpartnertest2';

        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('IndexUsers', $breadcrumbs);

        $user = $em->getRepository('AppBundle:User')->findOneByUsername('superpartnertest2');
        $this->assertEquals('superpartnertest2', $user->getUsername());
    }

    public function testDeleteSuperPartner()
    {
        SecurityControllerTest::LogInGod($this);
        $em =  $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('AppBundle:User')->findOneByUsername('superpartnertest2');
        $this->assertEquals('superpartnertest2', $user->getUsername());

        $crawler = $this->client->request('GET', '/en/users');
        $link = $crawler->filter('a:contains(">>")')->eq(0)->link();
        $crawler = $this->client->click($link);
        $link = $crawler->filter('a:contains("Delete")')->last()->link();
        $crawler = $this->client->click($link);
        $user = $em->getRepository('AppBundle:User')->findOneByUsername('superpartnertest2');
        $this->assertEquals(null, $user);

    }

    public function testNewPartner()
    {
        SecurityControllerTest::LogInGod($this);

        $crawler = $this->client->request('GET', '/en/users/user-new/6');

        $form = $crawler->selectButton('Submit')->form();
        $form = self::getGenericForm($form);
        // sustituye algunos valores
        $form['user_new[username]'] = 'partnertest';
        $form['partner[name]'] = 'partnertest';
        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('IndexUsers', $breadcrumbs);
    }


    public function testEditPartner()
    {
        SecurityControllerTest::LogInGod($this);
        $em =  $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('AppBundle:User')->findOneByUsername('partnertest');
        $this->assertEquals('partnertest', $user->getUsername());

        $crawler = $this->client->request('GET', '/en/users');
        $link = $crawler->filter('a:contains(">>")')->eq(0)->link();
        $crawler = $this->client->click($link);
        $link = $crawler->filter('a:contains("Edit")')->last()->link();
        $crawler = $this->client->click($link);
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('IndexUsersRoleUserPartner', $breadcrumbs);
        $form = $crawler->selectButton('Submit')->form();

        // sustituye algunos valores
        $form['user_edit[username]'] = 'partnertest2';

        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('IndexUsers', $breadcrumbs);

        $user = $em->getRepository('AppBundle:User')->findOneByUsername('partnertest2');
        $this->assertEquals('partnertest2', $user->getUsername());
    }

    public function testDeletePartner()
    {
        SecurityControllerTest::LogInGod($this);
        $em =  $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('AppBundle:User')->findOneByUsername('partnertest2');
        $this->assertEquals('partnertest2', $user->getUsername());

        $crawler = $this->client->request('GET', '/en/users');
        $link = $crawler->filter('a:contains(">>")')->eq(0)->link();
        $crawler = $this->client->click($link);
        $link = $crawler->filter('a:contains("Delete")')->last()->link();
        $crawler = $this->client->click($link);
        $user = $em->getRepository('AppBundle:User')->findOneByUsername('partnertest2');
        $this->assertEquals(null, $user);

    }

    public function testNewCommercial()
    {
        SecurityControllerTest::LogInGod($this);

        $crawler = $this->client->request('GET', '/en/users/user-new/7');

        $form = $crawler->selectButton('Submit')->form();
        $form = self::getGenericForm($form);
        // sustituye algunos valores
        $form['user_new[username]'] = 'commercialtest';

        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('IndexUsers', $breadcrumbs);
    }


    public function testEditCommercial()
    {
        SecurityControllerTest::LogInGod($this);
        $em =  $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('AppBundle:User')->findOneByUsername('commercialtest');
        $this->assertEquals('commercialtest', $user->getUsername());

        $crawler = $this->client->request('GET', '/en/users');
        $link = $crawler->filter('a:contains(">>")')->eq(0)->link();
        $crawler = $this->client->click($link);
        $link = $crawler->filter('a:contains("Edit")')->last()->link();
        $crawler = $this->client->click($link);
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('IndexUsersRoleUserCommercial', $breadcrumbs);
        $form = $crawler->selectButton('Submit')->form();

        // sustituye algunos valores
        $form['user_edit[username]'] = 'commercialtest2';

        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('IndexUsers', $breadcrumbs);

        $user = $em->getRepository('AppBundle:User')->findOneByUsername('commercialtest2');
        $this->assertEquals('commercialtest2', $user->getUsername());
    }

    public function testDeleteCommercial()
    {
        SecurityControllerTest::LogInGod($this);
        $em =  $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('AppBundle:User')->findOneByUsername('commercialtest2');
        $this->assertEquals('commercialtest2', $user->getUsername());

        $crawler = $this->client->request('GET', '/en/users');
        $link = $crawler->filter('a:contains(">>")')->eq(0)->link();
        $crawler = $this->client->click($link);
        $link = $crawler->filter('a:contains("Delete")')->last()->link();
        $crawler = $this->client->click($link);
        $user = $em->getRepository('AppBundle:User')->findOneByUsername('commercialtest2');
        $this->assertEquals(null, $user);

    }

    public function testNewAdviser()
    {
        SecurityControllerTest::LogInGod($this);

        $crawler = $this->client->request('GET', '/en/users/user-new/8');

        $form = $crawler->selectButton('Submit')->form();
        $form = self::getGenericForm($form);
        // sustituye algunos valores
        $form['user_new[username]'] = 'advisertest';

        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('IndexUsers', $breadcrumbs);

    }

    public function testEditAdviser()
    {
        SecurityControllerTest::LogInGod($this);
        $em =  $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('AppBundle:User')->findOneByUsername('advisertest');
        $this->assertEquals('advisertest', $user->getUsername());

        $crawler = $this->client->request('GET', '/en/users');
        $link = $crawler->filter('a:contains(">>")')->eq(0)->link();
        $crawler = $this->client->click($link);
        $link = $crawler->filter('a:contains("Edit")')->last()->link();
        $crawler = $this->client->click($link);
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('IndexUsersRoleUserAdviser', $breadcrumbs);
        $form = $crawler->selectButton('Submit')->form();

        // sustituye algunos valores
        $form['user_edit[username]'] = 'advisertest2';

        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('IndexUsers', $breadcrumbs);

        $user = $em->getRepository('AppBundle:User')->findOneByUsername('advisertest2');
        $this->assertEquals('advisertest2', $user->getUsername());
    }

    public function testDeleteAdviser()
    {
        SecurityControllerTest::LogInGod($this);
        $em =  $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('AppBundle:User')->findOneByUsername('advisertest2');
        $this->assertEquals('advisertest2', $user->getUsername());

        $crawler = $this->client->request('GET', '/en/users');
        $link = $crawler->filter('a:contains(">>")')->eq(0)->link();
        $crawler = $this->client->click($link);
        $link = $crawler->filter('a:contains("Delete")')->last()->link();
        $crawler = $this->client->click($link);
        $user = $em->getRepository('AppBundle:User')->findOneByUsername('advisertest2');
        $this->assertEquals(null, $user);

    }

    public function testNewWorkshop()
    {
        SecurityControllerTest::LogInGod($this);

        $crawler = $this->client->request('GET', '/en/users/user-new/9');

        $form = $crawler->selectButton('Submit')->form();
        $form = self::getGenericForm($form);
        // sustituye algunos valores
        $form['user_new[username]'] = 'workshoptest';
        $form['workshop[name]'] = 'workshoptest';
        $form['workshop[Partner]'] = 1;
        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('IndexUsers', $breadcrumbs);

    }


    public function testEditWorkshop()
    {
        SecurityControllerTest::LogInGod($this);
        $em =  $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('AppBundle:User')->findOneByUsername('workshoptest');
        $this->assertEquals('workshoptest', $user->getUsername());

        $crawler = $this->client->request('GET', '/en/users');
        $link = $crawler->filter('a:contains(">>")')->eq(0)->link();
        $crawler = $this->client->click($link);
        $link = $crawler->filter('a:contains("Edit")')->last()->link();
        $crawler = $this->client->click($link);
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('IndexUsersRoleUserWorkshop', $breadcrumbs);
        $form = $crawler->selectButton('Submit')->form();

        // sustituye algunos valores
        $form['user_edit[username]'] = 'workshoptest2';

        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('IndexUsers', $breadcrumbs);

        $user = $em->getRepository('AppBundle:User')->findOneByUsername('workshoptest2');
        $this->assertEquals('workshoptest2', $user->getUsername());
    }

    public function testDeleteWorkshop()
    {
        SecurityControllerTest::LogInGod($this);
        $em =  $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('AppBundle:User')->findOneByUsername('workshoptest2');
        $this->assertEquals('workshoptest2', $user->getUsername());

        $crawler = $this->client->request('GET', '/en/users');
        $link = $crawler->filter('a:contains(">>")')->eq(0)->link();
        $crawler = $this->client->click($link);
        $link = $crawler->filter('a:contains("Delete")')->last()->link();
        $crawler = $this->client->click($link);
        $user = $em->getRepository('AppBundle:User')->findOneByUsername('workshoptest2');
        $this->assertEquals(null, $user);

    }

    public function testNewUser()
    {
        SecurityControllerTest::LogInGod($this);

        $crawler = $this->client->request('GET', '/en/users/user-new/10');

        $form = $crawler->selectButton('Submit')->form();
        $form = self::getGenericForm($form);
        // sustituye algunos valores
        $form['user_new[username]'] = 'usertest';
        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('IndexUsers', $breadcrumbs);

    }


    public function testEditUser()
    {
        SecurityControllerTest::LogInGod($this);
        $em =  $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('AppBundle:User')->findOneByUsername('usertest');
        $this->assertEquals('usertest', $user->getUsername());

        $crawler = $this->client->request('GET', '/en/users');
        $link = $crawler->filter('a:contains(">>")')->eq(0)->link();
        $crawler = $this->client->click($link);
        $link = $crawler->filter('a:contains("Edit")')->last()->link();
        $crawler = $this->client->click($link);
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('IndexUsersRoleUserUser', $breadcrumbs);
        $form = $crawler->selectButton('Submit')->form();

        // sustituye algunos valores
        $form['user_edit[username]'] = 'usertest2';

        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('IndexUsers', $breadcrumbs);

        $user = $em->getRepository('AppBundle:User')->findOneByUsername('usertest2');
        $this->assertEquals('usertest2', $user->getUsername());
    }

    public function testDeleteUser()
    {
        SecurityControllerTest::LogInGod($this);
        $em =  $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('AppBundle:User')->findOneByUsername('usertest2');
        $this->assertEquals('usertest2', $user->getUsername());

        $crawler = $this->client->request('GET', '/en/users');
        $link = $crawler->filter('a:contains(">>")')->eq(0)->link();
        $crawler = $this->client->click($link);
        $link = $crawler->filter('a:contains("Delete")')->last()->link();
        $crawler = $this->client->click($link);
        $user = $em->getRepository('AppBundle:User')->findOneByUsername('usertest2');
        $this->assertEquals(null, $user);

    }

    public function getGenericForm($form){
        $form['user_new[plainPassword][first]'] = 'test';
        $form['user_new[plainPassword][second]'] = 'test';
        $form['user_new[categoryService]'] = 1;
        $form['user_new[country]'] = 1;
        $form['user_new[language]'] = 1;
        $form['user_new[status]'] = 1;
        $form['user_new[email1]'] = 'test@mail.com';
        $form['user_new[email2]'] = '';
        $form['user_new[phoneNumber1]'] = '123456879';
        $form['user_new[phoneNumber2]'] = '';
        $form['user_new[mobileNumber1]'] = '';
        $form['user_new[mobileNumber2]'] = '';
        $form['user_new[fax]'] = '';
        $form['user_new[region]'] = '';
        $form['user_new[city]'] = '';
        $form['user_new[address]'] = '';
        $form['user_new[postalCode]'] = '';
        return $form;
    }
}