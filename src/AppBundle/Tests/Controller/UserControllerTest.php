<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use AppBundle\Utils\Slugger as Slugger;

class UserControllerTest extends WebTestCase
{
    public $client = null;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testNewAdmin()
    {
        SecurityControllerTest::LogInGod($this);

        $crawler = $this->client->request('GET', '/en/users/user-new/3');

        $form = $crawler->selectButton('submit')->form();
        // sustituye algunos valores
        $form['user_new[username]'] = 'admintest';
        $form['user_new[plainPassword][first]'] = 'test';
        $form['user_new[plainPassword][second]'] = 'test';
        $form['user_new[service]'] = 1;
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
        //$form['form_name[subject]'] = 'Hey there!';


        // envía el formulario
        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('IndexUsers', $breadcrumbs);
    }

    public function testNewWorkshop()
    {
        SecurityControllerTest::LogInGod($this);

        $crawler = $this->client->request('GET', '/en/users/user-new/9');

        $form = $crawler->selectButton('submit')->form();
        // sustituye algunos valores
        $form['user_new[username]'] = 'workshoptest';
        $form['user_new[plainPassword][first]'] = 'test';
        $form['user_new[plainPassword][second]'] = 'test';
        $form['user_new[service]'] = 1;
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
        $form['workshop[name]'] = 'workshoptest';
        $form['workshop[Partner]'] = '1';
        //$form['form_name[subject]'] = 'Hey there!';


        // envía el formulario
        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('IndexUsers', $breadcrumbs);
    }
}