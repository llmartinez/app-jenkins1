<?php

namespace Adservice\PartnerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use \Adservice\UtilBundle\Tests\Controller\UtilFunctionTest;

class ShopControllerTest extends WebTestCase {

    protected function setUp() {

    }

    /**
     * Test que comprueba que se cree un shop
     * @dataProvider shops
     */
    public function testNewShop($shop) {
                $client = static::createClient();
        $client->followRedirects(true);
        //Lleva al usuario desde la pantalla de login hasta la de nuevo shop introducido por dataProvider
        UtilFunctionTest::doLogin($client, 'admin', 'admin');
        UtilFunctionTest::linkTo($client, $this, 'table tr td a#shop_list');
        UtilFunctionTest::linkTo($client, $this, 'div legend a:contains("Nueva Tienda")');

$ar=fopen("zdatos.html","a") or die("Problemas en la creacion");
fputs($ar,$client->getResponse());
fclose($ar);

$ar=fopen("zurl.html","a") or die("Problemas en la creacion");
fputs($ar,$client->getRequest()->getUri());
fclose($ar);

        //comprueba que vuelva a la pagina del listado de shops
        $this->assertRegExp('/.*\/..\/partner\/new\/shop/', $client->getRequest()->getUri(), 'El usuario ve el formulario de nuevo shop');

        //carga el form con los datos del shop
        $newShopForm = $client->getCrawler()->selectButton('btn_create')->form($shop);
        //ejecuta el submit del form
        $crawler = $client->submit($newShopForm);

        //comprueba que devuelva una pagina sin error
        $this->assertTrue($client->getResponse()->isSuccessful());

        //comprueba que vuelva a la pagina del listado de partners
        $this->assertRegExp('/.*\/..\/partner\/list\/shop/', $client->getRequest()->getUri(), 'El usuario ve el listado de shops');
        // $this->assertGreaterThan(0, $crawler->filter('table tr td:contains("testshop")')->count(), 'El partners creado esta en la lista');
        //volver al inicio
        UtilFunctionTest::linkTo($client, $this, 'ol li a:contains("Índice")');
    }

    /**
     * Test que comprueba que se edite un partner
     * @dataProvider editPartners
     */
//     public function testEditPartner($shop)
//     {
//         $client = static::createClient();
//         $client-> followRedirects(true);
//         UtilFunctionTest::doLogin($client, 'admin', 'admin');

// // $ar=fopen("datos.html","a") or die("Problemas en la creacion");
// // fputs($ar,$client->getResponse());
// // fclose($ar);
//         $crawler = $client->request('GET', '/es/partner/edit/partner/1');

//         //comprueba que vaya a la pagina de edicion de usuarios
//         $this->assertRegExp('/.*\/..\/partner\/edit\/partner\/.*/', $client->getRequest()->getUri(),
//             'El usuario ve el listado de usuarios'
//         );

//         //carga el form con los datos editados del usuario
//         $editUserForm = $crawler->selectButton('btn_save')->form($shop);
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
     * DataProvider de shops: Contiene un array de shops
     * @return array shops
     */
    public function shops() {
        return array(
            array(
                'shop' => array(
                    'adservice_partnerbundle_shoptype[name]'            => 'testpartner',
                    'adservice_partnerbundle_shoptype[partner]'         => '1',
                    'adservice_partnerbundle_shoptype[active]'          => '1',
                    'adservice_partnerbundle_shoptype[phone_number_1]'  => '123456789',
                    'adservice_partnerbundle_shoptype[phone_number_2]'  => '123456879',
                    'adservice_partnerbundle_shoptype[movile_number_2]' => '123456879',
                    'adservice_partnerbundle_shoptype[movile_number_2]' => '123456879',
                    'adservice_partnerbundle_shoptype[fax]'             => '123456789',
                    'adservice_partnerbundle_shoptype[email_1]'         => 'testpartner@test.es',
                    'adservice_partnerbundle_shoptype[email_2]'         => 'testpartner@test.es',
                    'adservice_partnerbundle_shoptype[country]'         => '1',
                    'adservice_partnerbundle_shoptype[region]'          => 'Region Test',
                    'adservice_partnerbundle_shoptype[city]'            => 'City Test',
                    'adservice_partnerbundle_shoptype[address]'         => 'testaddress',
                    'adservice_partnerbundle_shoptype[postal_code]'     => '99999',
                ),
            ),
        );
    }

    /**
     * DataProvider de partners editados: Contiene dos campos de email a editar para un partner
     * @return array editPartners
     */
    // public function editPartners()
    // {
    //     return array(
    //         array('partner' => array(
    //                 'adservice_partnerbundle_shoptype[email_1]'    => 'testpartner_edited@test.es',
    //                 'adservice_partnerbundle_shoptype[email_2]'    => 'testpartner_edited@test.com',
    //             ),
    //         ),
    //     );
    // }
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