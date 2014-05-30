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
     * Test que comprueba que se edite un shop
     * @dataProvider editShops
     */
    public function testEditShop($shop)
    {
        $client = static::createClient();
        $client-> followRedirects(true);
        UtilFunctionTest::doLogin($client, 'admin', 'admin');
        UtilFunctionTest::linkTo($client, $this, 'table tr td a#shop_list');
/****************************************************************************************************/
// $ar=fopen("zdatos.html","a") or die("Problemas en la creacion");
// fputs($ar,$client->getResponse());
// fclose($ar);
// $ar=fopen("zurl.html","a") or die("Problemas en la creacion");
// fputs($ar,$client->getRequest()->getUri());
// fclose($ar);
/****************************************************************************************************/
        UtilFunctionTest::linkTo($client, $this, 'table tr td a#edittestshop');

        //comprueba que vaya a la pagina de edicion de usuarios
        $this->assertRegExp('/.*\/..\/partner\/edit\/shop\/.*/', $client->getRequest()->getUri(),
            'El usuario ve el listado de tiendas'
        );

        //carga el form con los datos editados del usuario
        $editShopForm = $client->getCrawler()->selectButton('btn_edit')->form($shop);
        //ejecuta el submit del form
        $crawler = $client->submit($editShopForm);

        //comprueba que devuelva una pagina sin error
        $this->assertTrue($client->getResponse()->isSuccessful());

        //comprueba que vuelva a la pagina del listado de usuarios
        $this->assertRegExp('/.*\/..\/partner\/list/', $client->getRequest()->getUri(),
            'El usuario ve el listado de tiendas'
        );

        //volver al inicio
        UtilFunctionTest::linkTo($client, $this, 'ol li a:contains("Índice")');
    }

    /**
     * DataProvider de shops: Contiene un array de shops
     * @return array shops
     */
    public function shops() {
        return array(
            array(
                'shop' => array(
                    'adservice_partnerbundle_shoptype[name]'            => 'testshop',
                    'adservice_partnerbundle_shoptype[partner]'         => '1',
                    'adservice_partnerbundle_shoptype[active]'          => '1',
                    'adservice_partnerbundle_shoptype[phone_number_1]'  => '123456789',
                    'adservice_partnerbundle_shoptype[phone_number_2]'  => '123456879',
                    'adservice_partnerbundle_shoptype[movile_number_2]' => '123456879',
                    'adservice_partnerbundle_shoptype[movile_number_2]' => '123456879',
                    'adservice_partnerbundle_shoptype[fax]'             => '123456789',
                    'adservice_partnerbundle_shoptype[email_1]'         => 'testshop@test.es',
                    'adservice_partnerbundle_shoptype[email_2]'         => 'testshop@test.es',
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
     * DataProvider de partners editados: Contiene dos campos de email a editar para un shop
     * @return array editShops
     */
    public function editShops()
    {
        return array(
            array('shop' => array(
                    'adservice_partnerbundle_shoptype[email_1]'    => 'testshop_edited@test.es',
                    'adservice_partnerbundle_shoptype[email_2]'    => 'testshop_edited@test.com',
                ),
            ),
        );
    }
}