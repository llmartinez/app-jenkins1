<?php

namespace Adservice\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Adservice\UserBundle\Entity\User;

class LoginControllerTest extends WebTestCase {

    protected function setUp() {
        
    }

    /**
     * La pantalla de login se muestra correctamente
     */
    public function testShowLogin() {
        $client = static::createClient();
        $client->followRedirects(true);
        
        $crawler = $client->request('GET', '/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Se muestra la pantalla de login "/" (status 200)');

        $crawler = $client->request('GET', '/es/login');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Se muestra la pantalla de login "/es/login" (status 200)');
    }

    /**
     * Hace login en la aplicacion y va a su perfil
     */
    public function testLogin() {
        
        $client = static::createClient();
        $client->followRedirects(true);
        
        $crawler = $client->request('GET', '/');
        
//        admin               admin
//        admin1-2-3-4        admin
//        assessor1-2-3-4     assessor
//        user1-2-3-4         user
        
        //carga el form con los datos de login
        $loginForm = $crawler->selectButton('btn_login')->form(array('_username' => 'admin',
                                                                     '_password' => 'admin',
                                                                    ));
        //ejecuta el submit del form
        $crawler = $client->submit($loginForm);
        
        //comprueba que devuelva una pagina sin error
        $this->assertTrue($client->getResponse()->isSuccessful());
        
        //comprueba que se haya devuelto una cookie de session
        $this->assertRegExp('/(\d|[a-z])+/', $client->getCookieJar()->get('PHPSESSID')->getValue(),
                'La aplicación ha enviado una cookie de sesión');
//        $this->assertTrue($client->getCookieJar()->get('PHPSESSID'), 'La aplicación ha enviado una cookie de sesión'); <---- no va....deberia ir....
        
        //seleccionamos idioma español (para facilitar tema de url)
//        $links_spain_flag = $crawler->selectLink('body > div.selectLang');
        
//        $select_lang_links = $crawler->filter('div.selectLang');
        $select_spanish_link = $crawler->filter('#selectLang a')->eq(1)->link();
        $crawler = $client->click($select_spanish_link);
        var_dump($crawler);
        
//        var_dump($crawler->selectLink('body > div.selectLang'));
//        var_dump($links_spain_flag);
//        $linksPerfil = $crawler->selectLink('Mi Perfil');
//        $linkPerfil = $linksPerfil->link();
//        $crawler = $client->click($linkPerfil);
        
        //comprobación de que el formulario de mi perfil corresponde a la persona que ha hecho login
//        $this->assertEquals( "admin", $crawler->filter('form input[name="adservice_userbundle_usertype[username]"]')->attr('value'),
//            'En el formulario de Mi Perfil sale el mismo nombre que el usado en el login'
//        );
        
    }

    protected function tearDown() {
        parent::tearDown();
    }

    /*******************************/
    
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
//        $_this->assertEquals('admin', $crawler->filter('form input[name="adservice_userbundle_usertype[username]"]')->attr('value'), 
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
}
