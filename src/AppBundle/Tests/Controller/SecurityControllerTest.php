<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

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
        $this->logIn($this);
        $crawler = $this->client->request('GET', '/en');
        $crawler = $this->client->followRedirect(); //go to '/en'

        $breadcrumbs = Slugger::noSpaces($crawler->filter('#breadcrumbs')->text());
        $this->assertEquals('Index', $breadcrumbs);
    
    }
    public static function logIn($_this)
    {
        $_this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'user2',
            'PHP_AUTH_PW'   => 'user',
        ));
    }
}
