<?php
//
//namespace Adservice\UserBundle\Tests\Controller;
//
//use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
//use Adservice\UserBundle\Entity\User;
//
//class LoginControllerTest extends WebTestCase
//{    
// 
//    public function testLoadLogin() { 
//        $client = static::createClient();
//        $crawler = $client->request('GET', '/');
//        $this->assertEquals(200, $client->getResponse()->getStatusCode(),
//        'Se muestra la pantalla de login (status 200)'
//        );
//        
//        $crawler = $client->request('GET', '/es/login');
//        $this->assertEquals(200, $client->getResponse()->getStatusCode(),
//        'Se muestra la pantalla de login (status 200)'
//        );
//        
//        $this->assertRegExp(
//            '/.*\/..\/login_check/', $crawler->filter('form')->attr('action'), 
//            'El usuario anónimo ve el formulario de login'
//        );
//    }
//    
//    public function testLogin() {
//        $client = static::createClient();
//        $this->doLogin($client, $this);
//    }
//    
//    public static function doLogin($client, $_this) {
//        //sigue automaticamente las redirecciones del codigo
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
//        
//        //comprueba que devuelva una pagina sin error
//        $_this->assertTrue($client->getResponse()->isSuccessful());
//        //comprueba que se haya devuelto una cookie de session
//        $_this->assertRegExp(
//            '/(\d|[a-z])+/',
//            $client->getCookieJar()->get('PHPSESSID')->getValue(),
//            'La aplicación ha enviado una cookie de sesión'
//        );
//        //link a la pagina de perfil
//        $crawler = TestFunctions::linkTo($client, $crawler, $_this, 'table tr td a#profile');
//        //comprueba que coincida el nombre de usuario con el logeado
//        $_this->assertEquals('Admin', $crawler->filter('form input[name="adservice_userbundle_usertype[username]"]')->attr('value'), 
//                'El usuario se ha registrado correctamente y sus datos se han guardado en la base de datos'
//        );
//        //link de vuelta al inicio
//        $crawler = TestFunctions::linkTo($client, $crawler, $_this, 'ol li a#home');
//        
//        $_this->assertEquals(200, $client->getResponse()->getStatusCode(),
//        'Se muestra la pantalla de home (status 200)'
//        );
//        return $client;
//    }
//}
