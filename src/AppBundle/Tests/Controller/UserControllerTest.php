<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use AppBundle\Utils\Slugger as Slugger;

class UserControllerTest extends WebTestCase
{
    public $client = null;
    public $ids = array();
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