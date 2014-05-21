<?php

namespace Adservice\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use \Adservice\UtilBundle\Tests\Controller\UtilFunctionTest;

class UserControllerTest extends WebTestCase
{
    protected function setUp() {
    }

    /**
     * Test que comprueba que se cree un usuario de cada tipo
     * @dataProvider users
     */
    public function testNewUser($type, $user)
    {
        $client = static::createClient();
        $client-> followRedirects(true);
        //Lleva al usuario desde la pantalla de login hasta la de nuevo usuario del $type que se introduzca por dataProvider
            UtilFunctionTest::doLogin($client, 'admin', 'admin');
 // $ar=fopen("datos.html","a") or die("Problemas en la creacion");
 // fputs($ar,$client->getResponse());
 // fclose($ar);
            UtilFunctionTest::linkTo($client, $this, 'table tr td a#user_list');
            UtilFunctionTest::linkTo($client, $this, 'div legend a#user_new');
            UtilFunctionTest::linkTo($client, $this, 'table tr td a#type_'.$type);

        //carga el form con los datos del usuario
        $newUserForm = $client->getCrawler()->selectButton('btn_create')->form($user);
        //ejecuta el submit del form
        $crawler = $client->submit($newUserForm);

        //comprueba que devuelva una pagina sin error
        $this->assertTrue($client->getResponse()->isSuccessful());

        //comprueba que vuelva a la pagina del listado de usuarios
        $this->assertRegExp('/.*\/..\/user\/list/', $client->getRequest()->getUri(),
            'El usuario ve el listado de usuarios'
        );
        // $this->assertGreaterThan(0, $crawler->filter('table tr td a#list_username:contains("test'.$type.'")')->count(),
        //     'El admin creado esta en la lista'
        // );
// $ar=fopen("datos.html","a") or die("Problemas en la creacion");
// fputs($ar,$client->getResponse());
// fclose($ar);
        //volver al inicio
        UtilFunctionTest::linkTo($client, $this, 'ol li a:contains("Índice")');

        //comprueba que vuelva a la pagina del listado de usuarios
        $this->assertRegExp('/.*\/..\/user\/index/', $client->getRequest()->getUri(),
            'El usuario ve la pagina principal'
        );
    }

    /**
     * Test que comprueba que se edite un usuario de cada tipo
     * @dataProvider userEditFields
     */
    public function testEditUser($type, $userEditFields)
    {
        // $client = static::createClient();
        // $client-> followRedirects(true);
        // UtilFunctionTest::doLogin($client, 'admin', 'admin');

        // UtilFunctionTest::linkTo($client, $this, 'table tr td a#user_list');

        // $location = 'table tr td a#btn_editadmin';
        // $link = $client->getCrawler()->filter($location)->link();
        // $crawler = $client->click($link);

        // //comprueba que vaya a la pagina de edicion de usuarios
        // $this->assertRegExp('/.*\/..\/user\/edit\/.*/', $client->getRequest()->getUri(),
        //     'El usuario ve el listado de usuarios'
        // );

        // //carga el form con los datos editados del usuario
        // $editUserForm = $crawler->selectButton('btn_save')->form($userEditFields);
        // //ejecuta el submit del form
        // $crawler = $client->submit($editUserForm);

        // //comprueba que devuelva una pagina sin error
        // $this->assertTrue($client->getResponse()->isSuccessful());

        // //comprueba que vuelva a la pagina del listado de usuarios
        // $this->assertRegExp('/.*\/..\/user\/list/', $client->getRequest()->getUri(),
        //     'El usuario ve el listado de usuarios'
        // );

        // //volver al inicio
        // UtilFunctionTest::linkTo($client, $this, 'ol li a:contains("Home")');
    }

    /**
     * Test que comprueba que salten alertas de acceso denegado
     */
    public function testAccessDenied()
    {
        // $client = static::createClient();
        // $client-> followRedirects(true);
        // UtilFunctionTest::doLogin($client, 'user1', 'user');
        // $this->assertEquals(0, $client->getCrawler()->filter('table tr td a#user_list')->count(),
        //     'El usuario no ve el enlace a la lista de usuarios' );

        /*
            //link al cual un usuario normal no tiene acceso
            $crawler = $client->request('GET', '/es/user/list');

            $this->assertEquals(403, $client->getResponse()->getStatusCode(),
            'Acceso denegado al usuario (solo entrara un admin)'
            );
        */
    }

    /**
     * DataProvider de usuarios: Contiene un admin, un assessor y un user
     * @return array users
     */
    public function users()
    {
        return array(
            array('type' => 'admin',
                  'user' => array(
                                'admin_assessor_type[username]'                    => 'testadmin',
                                'admin_assessor_type[password][Contraseña]'        => 'test',
                                'admin_assessor_type[password][Repite Contraseña]' => 'test',
                                'admin_assessor_type[name]'                        => 'Test',
                                'admin_assessor_type[surname]'                     => 'User_admin',
                                'admin_assessor_type[phone_number_1]'              => '123456789',
                                'admin_assessor_type[phone_number_2]'              => '123456879',
                                'admin_assessor_type[movile_number_2]'             => '123456879',
                                'admin_assessor_type[movile_number_2]'             => '123456879',
                                'admin_assessor_type[fax]'                         => '123456789',
                                'admin_assessor_type[email_1]'                     => 'testadmin@test.es',
                                'admin_assessor_type[active]'                      => '1',
                                'admin_assessor_type[country]'                     => '1',
                                'admin_assessor_type[region]'                      => 'testregion',
                                'admin_assessor_type[city]'                        => 'testcity',
                                'admin_assessor_type[address]'                     => 'testaddress',
                                'admin_assessor_type[postal_code]'                 => '99999',

                                ),
            ),
            array('type' => 'assessor',
                  'user' => array(
                                'admin_assessor_type[username]'                    => 'testassessor',
                                'admin_assessor_type[password][Contraseña]'        => 'test',
                                'admin_assessor_type[password][Repite Contraseña]' => 'test',
                                'admin_assessor_type[name]'                        => 'Test',
                                'admin_assessor_type[surname]'                     => 'User_assessor',
                                'admin_assessor_type[phone_number_1]'              => '123456789',
                                'admin_assessor_type[phone_number_2]'              => '123456879',
                                'admin_assessor_type[movile_number_2]'             => '123456879',
                                'admin_assessor_type[movile_number_2]'             => '123456879',
                                'admin_assessor_type[fax]'                         => '123456789',
                                'admin_assessor_type[email_1]'                     => 'testassessor@test.es',
                                'admin_assessor_type[active]'                      => '1',
                                'admin_assessor_type[country]'                     => '1',
                                'admin_assessor_type[region]'                      => 'testregion',
                                'admin_assessor_type[city]'                        => 'testcity',
                                'admin_assessor_type[address]'                     => 'testaddress',
                                'admin_assessor_type[postal_code]'                 => '99999',
                                ),
            ),
            array('type' => 'ad',
                  'user' => array(
                                'partner_type[username]'                    => 'testad',
                                'partner_type[password][Contraseña]'        => 'test',
                                'partner_type[password][Repite Contraseña]' => 'test',
                                'partner_type[name]'                        => 'Test',
                                'partner_type[surname]'                     => 'User_ad',
                                'partner_type[phone_number_1]'              => '123456789',
                                'partner_type[phone_number_2]'              => '123456879',
                                'partner_type[movile_number_2]'             => '123456879',
                                'partner_type[movile_number_2]'             => '123456879',
                                'partner_type[fax]'                         => '123456789',
                                'partner_type[email_1]'                     => 'testad@test.es',
                                'partner_type[active]'                      => '1',
                                'partner_type[country]'                     => '1',
                                'partner_type[region]'                      => 'testregion',
                                'partner_type[city]'                        => 'testcity',
                                'partner_type[address]'                     => 'testaddress',
                                'partner_type[postal_code]'                 => '99999',
                                ),
            ),
        );
    }

    /**
     * DataProvider de usuarios editados: Contiene dos campos de email a editar para un admin, un assessor y un user
     * @return array userEditFields
     */
    public function userEditFields()
    {
        return array(
            array('type' => 'admin',
                  'user' => array(
                                'admin_assessor_type[email_1]'    => 'testadmin_edited@test.es',
                                'admin_assessor_type[email_2]'    => 'testadmin_edited@test.com',
                                ),
                ),
            array('type' => 'assessor',
                  'user' => array(
                                'admin_assessor_type[email_1]'    => 'testassessor_edited@test.es',
                                'admin_assessor_type[email_2]'    => 'testassessor_edited@test.com',
                                ),
                ),
        );
    }
}