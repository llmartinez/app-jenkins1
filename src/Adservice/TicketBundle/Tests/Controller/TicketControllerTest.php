<?php

namespace Adservice\TicketBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Adservice\TicketBundle\Entity\Ticket;
use \Adservice\UtilBundle\Tests\Controller\UtilFunctionTest;

class TicketControllerTest extends WebTestCase {

    protected function setUp() {
        
    }

    /**
     * @dataProvider users
     */
    public function testNewTicket($users) {
        $client = static::createClient();
        $client-> followRedirects(true);
        UtilFunctionTest::setClient($client, $users['adservice_userbundle_usertype[username]'], $users['adservice_userbundle_usertype[password]']);
        $crawler = $client->getCrawler();
        
        //miramos que exista el link "mis tickets" y lo clickamos
        $myTicket_link = $crawler->filter('table[name=tbl_user]')->selectLink('Mis Tickets')->link();
        $crawler = $client->click($myTicket_link);
        
        //hay 1 boton de "New Ticket" y lo clickamos
        $newTicket_link = $crawler->filter('div.tblContainer')->selectLink('New Ticket')->link();
        $num_newTicketLinks = $crawler->filter('a[id=newTicket]')->count();
        $this->assertEquals(1, $num_newTicketLinks, 'Hay un botón "New Ticket" en "/es/ticket"');
        $crawler = $client->click($newTicket_link);
        
        //CAMPOS DE TICKET
        $num_field_ticket_title = $crawler->filter('form input[name="new_ticket_form[title]"]')->count();
        $this->assertEquals(1, $num_field_ticket_title, 'Existe el campo TITLE en el formulario de "New Ticket"');
        
        $num_field_ticket_importance = $crawler->filter('form input[name="new_ticket_form[importance]"]')->count();
        $this->assertEquals(1, $num_field_ticket_importance, 'Existe el campo IMPORTANCE en el formulario de "New Ticket"');
        
        //CAMPOS DE CAR
        $num_field_car_brand = $crawler->filter('form select[id="idBrand"]')->count();
        $this->assertEquals(1, $num_field_car_brand, 'Existe el campo BRAND en el formulario de "New Ticket"');
              
        
    }
    

    
    public function newTicket(){
        
    }

    protected function tearDown() {
        parent::tearDown();
    }

    
    
    /**
     * Método que provee de usuarios de prueba a los tests de esta clase
     */
    public function users() {
//        admin               admin
//        admin1-2-3-4        admin
//        assessor1-2-3-4     assessor
//        user1-2-3-4         user

        return array(
            array(
//                array('adservice_userbundle_usertype[username]' => 'admin1',
//                    'adservice_userbundle_usertype[password]' => 'admin'),
//                array('adservice_userbundle_usertype[username]' => 'assessor1',
//                    'adservice_userbundle_usertype[password]' => 'assessor'),
                array('adservice_userbundle_usertype[username]' => 'user1',
                    'adservice_userbundle_usertype[password]' => 'user'),
                array('adservice_userbundle_usertype[username]' => 'user2',
                    'adservice_userbundle_usertype[password]' => 'user')
            )
        );
    }

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