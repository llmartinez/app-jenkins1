<?php

namespace ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ApiControllerTest extends WebTestCase
{
    private $client = null;

// SECTION LOGINS

    public function login($token)
    {
        $auth_token = static::createClient()->getKernel()->getContainer()->get('autologin')->encrypt($token);

        // 'HTTP_' tiene que ir delante del nombre de los parametros fuera del standard de HTTP
        $this->client = static::createClient(array(), array('HTTP_X_AUTH_TOKEN' => $auth_token));
    }

    public function loginSA()
    {
        $this->login("TYDsi98rjz0D4mo19bwf"); // ROLE_SUPER_ADMIN - superadmin
    }
    public function loginTop()
    {
        $this->login("jOgQL8qnzwma1U3JzvIw"); // ROLE_TOP_AD - topad24
    }
    public function loginWorkshop()
    {
        $this->login("lzdsi98rjz0Dmo4r2bwf"); // ROLE_USER - (29-1) Ganix Talleres
    }

// SECTION CHECKS

    /* check user access */
    public function testAccess()
    {
        $method = 'GET';
        $uri = '/es/api/accesses/check';

        $crawler404 = static::createClient(); // NO Token
        $crawler404->request($method, $uri);
        $this->assertEquals(403, $crawler404->getResponse()->getStatusCode()); // 403 - Username could not be found

        $crawler403 =  static::createClient(array(), array('HTTP_X_AUTH_TOKEN' => '00000000000000000000000000000000000000000000000000=')); // FAKE Token
        $crawler403->request($method, $uri);
        $this->assertEquals(403, $crawler403->getResponse()->getStatusCode()); // 403 - Username could not be found

        $this->loginWorkshop();
        $this->client->request($method, $uri); // WITH Token
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode()); // 200 - OK
    }

// SECTION BASIC GETTERS

    /* Check the basic getters functions */
    public function checkBasicGetter($uri)
    {
        $this->loginTop();

        $this->client->request('GET', $uri);

        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $content = json_decode ( $response->getContent() );

        $this->assertArrayHasKey('0', $content);
    }

    /* Get partners with the CategoryService from the logged user */
    public function testGetPartners()
    {
        $this->checkBasicGetter('/es/api/partners');
    }
    /* Get shops with the CategoryService from the logged user */
    public function testGetShops()
    {
        $this->checkBasicGetter('/es/api/shops');
    }
    /* Get typologies with the CategoryService from the logged user */
    public function testGetTypologies()
    {
        $this->checkBasicGetter('/es/api/typologies');
    }
    /* Get workshops with the CategoryService from the logged user */
    public function testGetWorkshops()
    {
        $this->checkBasicGetter('/es/api/workshops');
    }

// SECTION TICKETS

    /* Get workshop's number of tickets */
    public function testGetWorkshopNumberTickets()
    {
        $this->loginWorkshop();

        $this->client->request('GET', '/es/api/workshop/number/tickets');

        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $content = json_decode ( $response->getContent() );

        $this->assertTrue(isset($content->confirm));

        $this->assertTrue(isset($content->confirm->message));

        $this->assertTrue(isset($content->confirm->message->Tickets));

        $this->assertGreaterThanOrEqual(0, $content->confirm->message->Tickets);
    }

// SECTION WORKSHOPS

    /* Get workshop by $id with the CategoryService from the logged user */
    public function testGetWorkshop()
    {
        $this->loginTop();

        $this->client->request('GET', '/es/api/workshops/6275'); // 6275 - CLASSIC AUTO

        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $content = json_decode ( $response->getContent() );

        $this->assertGreaterThanOrEqual(0, $content);
    }

    /* test Activate workshop */
    public function testActivateWorkshop()
    {
        $this->loginTop();

        $this->client->request('PUT', '/es/api/workshops/6275/activate'); // 6275 - CLASSIC AUTO

        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $content = json_decode ( $response->getContent() );

        $this->assertTrue(isset($content->confirm));

        $this->assertTrue(isset($content->confirm->message));

        $this->assertEquals($content->confirm->message, "Taller activado correctamente");
    }

    /* test Deactivate workshop */
    public function testDeactivateWorkshop()
    {
        $this->loginTop();

        $this->client->request('PUT', '/es/api/workshops/6275/deactivate'); // 6275 - CLASSIC AUTO

        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $content = json_decode ( $response->getContent() );

        $this->assertTrue(isset($content->confirm));

        $this->assertTrue(isset($content->confirm->message));

        $this->assertEquals($content->confirm->message, "Taller desactivado correctamente");
    }

//    /* Add cheks to a workshop */
//   public function testWorkshopCheks()
//    {
//        $this->loginTop();
//
//        $this->client->request('PUT', '/es/api/workshops/6275/cheks/1'); // 6275 - CLASSIC AUTO
//
//        $response = $this->client->getResponse();
//
//        $this->assertEquals(200, $response->getStatusCode());
//
//        $content = json_decode ( $response->getContent() );
//
//        $this->assertTrue(isset($content->confirm));
//
//        $this->assertTrue(isset($content->confirm->message));
//
//        $this->assertTrue(isset($content->confirm->message->Total));
//
//        $this->assertGreaterThan("0", $content->confirm->message->Total);
//    }
//
//    /* Disable the cheks option to a workshop */
//    public function testWorkshopCheksDisable()
//    {
//        $this->loginTop();
//
//        $this->client->request('PUT', '/es/api/workshops/6275/cheks/disable'); // 6275 - CLASSIC AUTO
//
//        $response = $this->client->getResponse();
//
//        $this->assertEquals(200, $response->getStatusCode());
//
//        $content = json_decode ( $response->getContent() );
//
//        $this->assertTrue(isset($content->confirm));
//
//        $this->assertTrue(isset($content->confirm->message));
//
//        $this->assertEquals($content->confirm->message, "Cheques del taller deshabilitados correctamente");
//    }


}