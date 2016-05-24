<?php

namespace Adservice\PartnerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use \Adservice\UtilBundle\Tests\Controller\UtilFunctionTest;

class PartnerControllerTest extends WebTestCase {

    protected function setUp() {

    }

    /**
     * Test que comprueba que se cree un partner
     * @dataProvider partners
     */
    public function testNewPartner($partner) {
        $client = static::createClient();
        $client->followRedirects(true);
        //Lleva al usuario desde la pantalla de login hasta la de nuevo partner introducido por dataProvider
        UtilFunctionTest::doLogin($client, 'admin', 'admin');
        UtilFunctionTest::linkTo($client, $this, 'div a#partner_list');
        UtilFunctionTest::linkTo($client, $this, 'div legend a:contains("Nuevo Socio")');

        //comprueba que vuelva a la pagina del listado de partners
        $this->assertRegExp('/.*\/..\/partner\/new\/partner/', $client->getRequest()->getUri(), 'El usuario ve el formulario de nuevo partner');

        //carga el form con los datos del partner
        $newPartnerForm = $client->getCrawler()->selectButton('btn_create')->form($partner);
        //ejecuta el submit del form
        $crawler = $client->submit($newPartnerForm);

        //comprueba que devuelva una pagina sin error
        $this->assertTrue($client->getResponse()->isSuccessful());

        //comprueba que vuelva a la pagina del listado de partners
        $this->assertRegExp('/.*\/..\/partner\/list\/partner/', $client->getRequest()->getUri(), 'El usuario ve el listado de partners');
        // $this->assertGreaterThan(0, $crawler->filter('table tr td:contains("testpartner")')->count(), 'El partners creado esta en la lista');
        //volver al inicio
        UtilFunctionTest::linkTo($client, $this, 'ol li a:contains("Índice")');
    }

    /**
     * Test que comprueba que se edite un partner
     * @dataProvider editPartners
     */
//     public function testEditPartner($editPartners)
//     {
//         $client = static::createClient();
//         $client-> followRedirects(true);
//         UtilFunctionTest::doLogin($client, 'admin', 'admin');

//         $crawler = $client->request('GET', '/es/partner/edit/partner/101');

//         //comprueba que vaya a la pagina de edicion de usuarios
//         $this->assertRegExp('/.*\/..\/partner\/edit\/partner\/.*/', $client->getRequest()->getUri(),
//             'El usuario ve el listado de usuarios'
//         );
// // $ar=fopen("datos.html","a") or die("Problemas en la creacion");
// // fputs($ar,$client->getResponse());
// // fclose($ar);
//         //carga el form con los datos editados del usuario
//         $editUserForm = $crawler->selectButton('btn_save')->form($editPartners);
//         //ejecuta el submit del form
//         $crawler = $client->submit($editUserForm);

//         //comprueba que devuelva una pagina sin error
//         $this->assertTrue($client->getResponse()->isSuccessful());

//         //comprueba que vuelva a la pagina del listado de usuarios
//         $this->assertRegExp('/.*\/..\/partner\/list/', $client->getRequest()->getUri(),
//             'El usuario ve el listado de partners'
//         );

//         //volver al inicio
//         UtilFunctionTest::linkTo($client, $this, 'ol li a:contains("Índice")');
//     }
    /**
     * DataProvider de partners: Contiene un array de partners
     * @return array partners
     */
    public function partners() {
        return array(
            array(
                'partner' => array(
                    'adservice_partnerbundle_partnertype[name]'            => 'testpartner',
                    'adservice_partnerbundle_partnertype[code_partner]'    => substr( microtime(), 2, 8),
                    'adservice_partnerbundle_partnertype[active]'          => '1',
                    'adservice_partnerbundle_partnertype[phone_number_1]'  => '123456789',
                    'adservice_partnerbundle_partnertype[phone_number_2]'  => '123456879',
                    'adservice_partnerbundle_partnertype[mobile_number_1]' => '123456879',
                    'adservice_partnerbundle_partnertype[mobile_number_2]' => '123456879',
                    'adservice_partnerbundle_partnertype[fax]'             => '123456789',
                    'adservice_partnerbundle_partnertype[email_1]'         => 'testpartner@test.es',
                    'adservice_partnerbundle_partnertype[email_2]'         => 'testpartner@test.es',
                    'adservice_partnerbundle_partnertype[country]'         => '1',
                    'adservice_partnerbundle_partnertype[region]'          => 'Region Test',
                    'adservice_partnerbundle_partnertype[city]'            => 'City Test',
                    'adservice_partnerbundle_partnertype[address]'         => 'testaddress',
                    'adservice_partnerbundle_partnertype[postal_code]'     => '99999',
                ),
            ),
        );
    }

    /**
     * DataProvider de partners editados: Contiene dos campos de email a editar para un partner
     * @return array editPartners
     */
    public function editPartners()
    {
        return array(
            array('editPartners' => array(
                    'adservice_partnerbundle_partnertype[email_1]'         => 'testpartner_edited@test.es',
                    'adservice_partnerbundle_partnertype[email_2]'         => 'testpartner_edited@test.es',
                ),
            ),
        );
    }
}


    /**
     * Test que comprueba que se borre un partner
     *//*
    public function testDeletePartner()
    {
        $client = static::createClient();
        $client-> followRedirects(true);
        UtilFunctionTest::doLogin($client, 'admin', 'admin');
        UtilFunctionTest::linkTo($client, $this, 'table tr td a#partner_list');

        $location = 'table tr td a#deletetestpartner';
        $link = $client->getCrawler()->filter($location)->link();
        $crawler = $client->click($link);
 /*TODO
     * la funcion javascript que cambia la url de 'foo' a 'id_partner' no funciona,
     * se envia al controller la funcion deleteUser('foo');
     * ***PHPUnit no soporta JS..
     *    - probar test con CasperJS y recoger resultado
     * *//*

        //comprueba que el link pase de 'delete/foo' a 'delete/{id_partner}'
        $location = 'div#myModal div div div.modal-footer a';
        $crawler = $crawler->filter($location)->attr('href');
        var_dump($crawler);
        die;

        $location = 'div#myModal div div div.modal-footer a';
        $link = $client->getCrawler()->filter($location)->link();
        $crawler = $client->click($link);

/*

        //comprueba que vuelva a la pagina del listado de usuarios
        $this->assertRegExp('/.*\/..\/partner\/list/', $client->getRequest()->getUri(),
            'El usuario ve el listado de partners'
        );

        $this->assertGreaterThan(0, $crawler->filter('table tr td:contains("testpartner_edited@test.es")')->count(),
            'Se ha editado el mail del partner');
*/
        //volver al inicio
/*        UtilFunctionTest::linkTo($client, $this, 'ol li a:contains("Home")');
    }
*/