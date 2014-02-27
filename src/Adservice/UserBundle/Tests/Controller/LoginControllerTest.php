<?php

namespace Adservice\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Adservice\UserBundle\Entity\User;
use Adservice\UtilBundle\Tests\Controller\UtilFunctionTest as UtilFunctions;

class LoginControllerTest extends WebTestCase {

    protected function setUp() {
        
    }

    /**
     * La pantalla de login se muestra correctamente
     */
    public function testShowLogin() {
        $client = static::createClient();
        $client->followRedirects(true);
        
        //se muestra la web de login...
        $crawler = $client->request('GET', '/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Se muestra la pantalla de login "/" (status 200)');

        
        $crawler = $client->request('GET', '/es/login');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Se muestra la pantalla de login "/es/login" (status 200)');
        
        //aparece el boton login...
        $num_login_button = $crawler->filter('form input[id="btn_login"]')->count();
        $this->assertEquals(1, $num_login_button,'Aparece el boton de "login" en la pantalla de login');

    }

    /**
     * @dataProvider users
     * Hace login en la aplicacion y va a su perfil
     */
    public function testLogin($users) {
        
        $client = static::createClient();
        $client->followRedirects(true);
        
        $crawler = $client->request('GET', '/');
        
        //carga el form con los datos de login
        $loginForm = $crawler->selectButton('btn_login')->form(array('_username' => $users['adservice_userbundle_usertype[username]'],
                                                                     '_password' => $users['adservice_userbundle_usertype[password]'],
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
        UtilFunctions::setLang($crawler, $client, 'es');
        $crawler = $client->getCrawler();
        $linksPerfil = $crawler->selectLink('Mi Perfil');
        $linkPerfil = $linksPerfil->link();
        $crawler = $client->click($linkPerfil);
        
        //comprobación de que el formulario de mi perfil corresponde a la persona que ha hecho login
        $this->assertEquals( $users['adservice_userbundle_usertype[username]'], $crawler->filter('form input[name="adservice_userbundle_usertype[username]"]')->attr('value'),
            'En el formulario de Mi Perfil sale el mismo nombre que el usado en el login'
        );
        
    }
        
    /**
     * Provando un login incorrecto...
     */
    public function testWrongLogin(){
        
        $client = static::createClient();
        $client->followRedirects(true);
        
        $crawler = $client->request('GET', '/');
        
        //carga el form con los datos de login
        $loginForm = $crawler->selectButton('btn_login')->form(array('_username' => 'foo',
                                                                     '_password' => 'foo',
                                                                    ));
        //ejecuta el submit del form
        $crawler = $client->submit($loginForm);
        $this->assertFalse($client->getResponse()->isRedirect(),'Cuando introducimos credenciales erroneas nos quedamos en la misma web');
        
    }

    protected function tearDown() {
        parent::tearDown();
    }
    
    /**
     * Método que provee de usuarios de prueba a los tests de esta clase
     */
    public function users()
    {
//        admin               admin
//        admin1-2-3-4        admin
//        assessor1-2-3-4     assessor
//        user1-2-3-4         user
        
        return array(
            array(
                array('adservice_userbundle_usertype[username]' => 'admin1',
                      'adservice_userbundle_usertype[password]' => 'admin'),
                array('adservice_userbundle_usertype[username]' => 'assessor1',
                      'adservice_userbundle_usertype[password]' => 'assessor'),
                array('adservice_userbundle_usertype[username]' => 'user1',
                      'adservice_userbundle_usertype[password]' => 'user'),
                array('adservice_userbundle_usertype[username]' => 'user2',
                      'adservice_userbundle_usertype[password]' => 'user')
            )
        );
    }
}