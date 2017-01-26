<?php

namespace AppBundle\Tests\Utils;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Utils\Slugger as Slugger;
use AppBundle\Tests\Controller\SecurityControllerTest as SecurityControllerTest;

class NavigationTest extends WebTestCase
{    
    public $client = null;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testNavigation()
    {
        // LOGIN (redirect for anonymous users)
        $crawler = $this->client->request('GET', '/');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect(); //go to '/en'
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect(); //go to '/en/login'

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('LoginHelp', $breadcrumbs);

        // Login as USER
        SecurityControllerTest::LogIn($this);

        // INDEX
        $crawler = $this->client->request('GET', '/en');
        $crawler = $this->client->followRedirect(); //go to '/en'
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('Index', $breadcrumbs);

        // INDEX LOCALE
        $crawler = $this->client->request('GET', '/es');
        $crawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('Índice', $breadcrumbs);

        // CHANGE LANG
        $crawler = $this->client->request('GET', '/en/changeLang/es');
        $crawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('Índice', $breadcrumbs);


/////// FALTA HACER LOGOUT 


        // HELP
        $crawler = $this->client->request('GET', '/en');
        $crawler = $this->client->followRedirect();
        $link = $crawler->filter('a:contains("Help")')->eq(0)->link();
        $crawler = $this->client->click($link);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('IndexHelp', $breadcrumbs);
    }
}