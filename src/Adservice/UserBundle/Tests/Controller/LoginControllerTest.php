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
        //$crawler = $client->request('GET', '/');
        //$this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Se muestra la pantalla de login "/" (status 200)');


        $url = $client->getContainer()->get('router')->generate('user_login', array('_locale' => 'es'));
        $crawler = $client->request('GET', $url);
        //$this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Se muestra la pantalla de login "/es/login" (status 200)');

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

        $url = $client->getContainer()->get('router')->generate('_login');
        $crawler = $client->request('GET', $url);

        //carga el form con los datos de login
        $loginForm = $crawler->selectButton('btn_login')->form(array('_username' => $users[0]['value'],
                                                                     '_password' => $users[1]['value'],
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

        $this->assertRegExp('/.*\/es\/user\/index/', $client->getRequest()->getUri(), 'El usuario ve el indice');

/****************************************************************************************************/
// $ar=fopen("zdatos.html","a") or die("Problemas en la creacion");
// fputs($ar,$client->getResponse());
// fclose($ar);
// $ar=fopen("zurl.html","a") or die("Problemas en la creacion");
// fputs($ar,$client->getRequest()->getUri());
// fclose($ar);
/****************************************************************************************************/

        // UtilFunctions::linkTo($client, $this, 'div tr td a:contains("Mi Perfil")');
        // //comprobación de que el formulario de mi perfil corresponde a la persona que ha hecho login
        // $this->assertTrue( $crawler->filter('table:contains('.$users[0]['value'].')')->count() > 0,
        //     'En el Perfil se muestra el mismo nombre que el usado en el login'
        // );

    }

    /**
     * Provando un login incorrecto...
     */
    public function testWrongLogin(){

        $client = static::createClient();
        $client->followRedirects(true);

        $url = $client->getContainer()->get('router')->generate('_login');
        $crawler = $client->request('GET', $url);

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
                array(array('field' => 'login_form[username]', 'value' => 'admin'),
                      array('field' => 'login_form[password]', 'value' => 'admin'))//,
                // array(array('field' => 'admin_assessor_type[username]', 'value' => 'assessor1'),
                //       array('field' => 'admin_assessor_type[password]', 'value' => 'assessor')),
                // array(array('field' => 'adservice_userbundle_usertype[username]', 'value' => 'user1'),
                //       array('field' => 'adservice_userbundle_usertype[password]', 'value' => 'user')),
                // array(array('field' => 'adservice_userbundle_usertype[username]', 'value' => 'user2'),
                //       array('field' => 'adservice_userbundle_usertype[password]', 'value' => 'user'))
            )
        );
    }
}