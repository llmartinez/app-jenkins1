<?php namespace Adservice\TicketBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Adservice\TicketBundle\Entity\Ticket;
use \Adservice\UtilBundle\Tests\Controller\UtilFunctionTest;

class TicketControllerTest extends WebTestCase {

    public function testIsTrue(){
        $this->assertTrue(true);
        $this->assertFalse(false);
    }

    /**
     * Test que comprueba que se cree un ticket
     * @dataProvider tickets
     */
    public function testNewTicket($ticket) {
//         $client = static::createClient();
//         $client->followRedirects(true);
//         //Lleva al usuario desde la pantalla de login hasta la de nuevo partner introducido por dataProvider
//         UtilFunctionTest::doLogin($client, 'admin', 'admin');
//         UtilFunctionTest::linkTo($client, $this, 'table tr td a#ticket_list');

//         $form['w_idpartner'] = '1';
//         $form['w_id'       ] = '1';

//         $newWorkshopForm = $client->getCrawler()->selectButton('btn_check')->form($form);
//         $newWorkshopForm['w_idpartner'] = '1';
//         $newWorkshopForm['w_id'       ] = '1';
//         $client->submit($newWorkshopForm);

//         UtilFunctionTest::linkTo($client, $this, 'div.info a#newTicket');

//         //carga el form con los datos del partner
//         $newTicketForm = $client->getCrawler()->selectButton('btn_create')->form($ticket);
//         //ejecuta el submit del form
//         $crawler = $client->submit($newTicketForm);

//         //comprueba que devuelva una pagina sin error
//         $this->assertTrue($client->getResponse()->isSuccessful());

// // $ar=fopen("zdatos.html","a") or die("Problemas en la creacion");
// // fputs($ar,$client->getResponse());
// // fclose($ar);
// // $ar=fopen("zurl.html","a") or die("Problemas en la creacion");
// // fputs($ar,$client->getRequest()->getUri());
// // fclose($ar);
//         //comprueba que vuelva a la pagina del listado de partners
//         $this->assertRegExp('/.*\/..\/partner\/list\/partner/', $client->getRequest()->getUri(), 'El usuario ve el listado de partners');
//         // $this->assertGreaterThan(0, $crawler->filter('table tr td:contains("testpartner")')->count(), 'El partners creado esta en la lista');
//         //volver al inicio
//         UtilFunctionTest::linkTo($client, $this, 'ol li a:contains("Índice")');


    }

//    public function newTicket(){
//
//    }
//
//    protected function tearDown() {
//        parent::tearDown();
//    }

    /**
     * DataProvider de tickets: Contiene un array de tickets
     * @return array tickets
     */
   public function tickets() {
       return array(
           array( 'ticket' => array(
                        'new_car_form_brand'           => '1',
                        'new_car_form_model'           => '1',
                        'new_car_form_version'         => '1',
                        'new_car_form[year]'           => '1',
                        'new_car_form[motor]'          => '1',
                        'new_car_form[kW]'             => '1',
                        'new_car_form[displacement]'   => '1',
                        'new_car_form[vin]'            => '1',
                        'new_car_form[plateNumber]'    => '1',
                        'new_car_form[importance]'     => '1',
                        'new_car_form[plateNumber]'    => '1',
                        'new_car_form[plateNumber]'    => '1',
                        'id_system'                    => '1',
                        'new_ticket_form[subsystem]'   => '1',
                        'new_ticket_form[description]' => '1',
                )
           )
       );
   }

//    private function checkFieldExist($crawler){
//         //CAMPOS DE TICKET
//        $num_field_ticket_title = $crawler->filter('form input[name="new_ticket_form[title]"]')->count();
//        $this->assertEquals(1, $num_field_ticket_title, 'Existe el campo TITLE en el formulario de "New Ticket"');
//
//        $num_field_ticket_importance = $crawler->filter('form input[name="new_ticket_form[importance]"]')->count();
//        $this->assertEquals(1, $num_field_ticket_importance, 'Existe el campo IMPORTANCE en el formulario de "New Ticket"');
//
//        //CAMPOS DE CAR
//        $field_car_brand = $crawler->filter('form select[id="idBrand"]')->count();
//        $this->assertEquals(1, $field_car_brand, 'Existe el campo BRAND en el formulario de "New Ticket"');
//
//        $field_car_model = $crawler->filter('form select[id="idModel"]')->count();
//        $this->assertEquals(1, $field_car_model, 'Existe el campo MODEL en el formulario de "New Ticket"');
//
//        $field_car_version = $crawler->filter('form select[name="new_car_form[version]"]')->count();
//        $this->assertEquals(1, $field_car_version, 'Existe el campo VERSION en el formulario de "New Ticket"');
//
//        $field_car_year = $crawler->filter('form input[name="new_car_form[year]"]')->count();
//        $this->assertEquals(1, $field_car_year, 'Existe el campo YEAR en el formulario de "New Ticket"');
//
//        $field_car_vin = $crawler->filter('form input[name="new_car_form[vin]"]')->count();
//        $this->assertEquals(1, $field_car_vin, 'Existe el campo VIN en el formulario de "New Ticket"');
//
//        $field_car_plateNumber = $crawler->filter('form input[name="new_car_form[plateNumber]"]')->count();
//        $this->assertEquals(1, $field_car_plateNumber, 'Existe el campo PLATE NUMBER en el formulario de "New Ticket"');
//
//    }

//    private function setTicketTitle(){
//        return $this->random_lipsum(rand(0,5), 'words', rand(0,20));
//    }

    /**
     * Se va a la paguina de lorem ipsum y devuelve texto
     * @param type $amount  how much of $what you want.
     * @param type $what is either paras, words, bytes or lists.
     * @param type $start whether or not to start the result with ‘Lorem ipsum dolor sit amet…‘
     * @return type
     */
//    private function random_lipsum($amount, $what, $start) {
//        return simplexml_load_file("http://www.lipsum.com/feed/xml?amount=$amount&what=$what&start=$start")->lipsum;
//    }
//
//    private function getRandomNumber($min,$max){
//        return rand($min, $max);
//    }
}

//    public function testLoadNewTicket() {
//        $client = static::createClient();
//        $crawler = $client->request('GET', '/es/ticket/');
//        $this->assertEquals(200, $client->getResponse()->getStatusCode(),
//        'Se muestra la pantalla de listado de tickets (status 200)'
//        );

//        $this->assertRegExp(
//            '/.*\/..\/login_check/', $crawler->filter('form')->attr('action'),
//            'El usuario anónimo ve el formulario de login'
//        );
//    }
//
//    public function testLogin() {
//        $client = static::createClient();
//        $client->followRedirects(true);
//
//        $crawler = $client->request('GET', '/');
//        //carga el form con los datos de login
//        $loginForm = $crawler->selectButton('btn_login')->form(array(
//            '_username' => 'admin',
//            '_password' => 'admin',
//        ));
//        //ejecuta el submit del form
//        $crawler = $client->submit($loginForm);
//        //comprueba que devuelva una pagina sin error
//        $this->assertTrue($client->getResponse()->isSuccessful());
//        //comprueba que se haya devuelto una cookie de session
//        $this->assertRegExp(
//            '/(\d|[a-z])+/',
//            $client->getCookieJar()->get('PHPSESSID')->getValue(),
//            'La aplicación ha enviado una cookie de sesión'
//        );
//        //link a la pagina de perfil
//        $perfil = $crawler->filter('table tr td a#profile')->link();
//        $crawler = $client->click($perfil);
//        //comprueba que coincida el nombre de usuario con el logeado
//        $this->assertEquals('Admin', $crawler->filter('form input[name="adservice_userbundle_usertype[username]"]')->attr('value'), 
//                'El usuario se ha registrado correctamente y sus datos se han guardado en la base de datos'
//        );
//    }
//}