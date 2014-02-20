<?php

//namespace Adservice\TicketBundle\Tests\Controller;
//
//use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
//use Adservice\TicketBundle\Entity\Ticket;
//
//class TicketControllerTest extends WebTestCase
//{    
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