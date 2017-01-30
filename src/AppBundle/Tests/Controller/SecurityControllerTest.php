<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use AppBundle\Utils\Slugger as Slugger;

class SecurityControllerTest extends WebTestCase
{    
    public $client = null;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testLogin()
    {
        $roles = array('God','SuperAdmin','Admin','Top','SuperPartner','Partner','Commercial','Adviser','Workshop','User');
        foreach($roles as $role){
            $name = 'logIn'.$role;

            $this->$name($this);
            $crawler = $this->client->request('GET', '/en');
            $crawler = $this->client->followRedirect(); //go to '/en'

            $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
            $this->assertEquals('Index', $breadcrumbs);

            $crawler = $this->client->request('GET', '/en/login');

            //$this->logOut($this);
        }
    }

    public static function logOut($_this){
        $crawler = $_this->client->request('GET', '/logout');

        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $_this->assertEquals('LoginHelp', $breadcrumbs);
    }
    public static function logInGod($_this)
    {
        $_this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'god',
            'PHP_AUTH_PW'   => 'test',
        ));
    }
    public static function logInSuperAdmin($_this)
    {
        $_this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'superadmin',
            'PHP_AUTH_PW'   => 'test',
        ));
    }
    public static function logInAdmin($_this)
    {
        $_this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'test',
        ));
    }
    public static function logInTop($_this)
    {
        $_this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'top',
            'PHP_AUTH_PW'   => 'test',
        ));
    }
    public static function logInSuperPartner($_this)
    {
        $_this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'superpartner',
            'PHP_AUTH_PW'   => 'test',
        ));
    }
    public static function logInPartner($_this)
    {
        $_this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'partner',
            'PHP_AUTH_PW'   => 'test',
        ));
    }
    public static function logInCommercial($_this)
    {
        $_this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'commercial',
            'PHP_AUTH_PW'   => 'test',
        ));
    }
    public static function logInAdviser($_this)
    {
        $_this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'adviser',
            'PHP_AUTH_PW'   => 'test',
        ));
    }
    public static function logInWorkshop($_this)
    {
        $_this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'workshop',
            'PHP_AUTH_PW'   => 'test',
        ));
    }
    public static function logInUser($_this)
    {
        $_this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW'   => 'test',
        ));
    }
}
