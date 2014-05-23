<?php

namespace Adservice\PopupBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use \Adservice\UtilBundle\Tests\Controller\UtilFunctionTest;

class PopupControllerTest extends WebTestCase {

    protected function setUp() {

    }

    /**
     * Test que comprueba que se cree un socio
     * @dataProvider socios
     */
    public function testNewSocio($socio) {
                $client = static::createClient();
        $client->followRedirects(true);
        //Lleva al usuario desde la pantalla de login hasta la de nuevo socio introducido por dataProvider
        UtilFunctionTest::doLogin($client, 'admin', 'admin');
        UtilFunctionTest::linkTo($client, $this, 'div legend a:contains("Nuevo Popup")');

        //comprueba que vuelva a la pagina del listado de socios
        $this->assertRegExp('/.*\/..\/popup\/new', $client->getRequest()->getUri(), 'El usuario ve el formulario de nuevo popup');

        //carga el form con los datos del socio
        $newsocioForm = $client->getCrawler()->selectButton('btn_create')->form($socio);
        //ejecuta el submit del form
        $crawler = $client->submit($newsocioForm);

        //comprueba que devuelva una pagina sin error
        $this->assertTrue($client->getResponse()->isSuccessful());

        //comprueba que vuelva a la pagina del listado de socios
        $this->assertRegExp('/.*\/..\/socio\/list\/socio/', $client->getRequest()->getUri(), 'El usuario ve el listado de socios');
        // $this->assertGreaterThan(0, $crawler->filter('table tr td:contains("testsocio")')->count(), 'El socios creado esta en la lista');
        //volver al inicio
        UtilFunctionTest::linkTo($client, $this, 'ol li a:contains("Índice")');
    }

    /**
     * Test que comprueba que se edite un partner
     * @dataProvider editPartners
     */
    public function testEditPartner($partner)
    {
        $client = static::createClient();
        $client-> followRedirects(true);
        UtilFunctionTest::doLogin($client, 'admin', 'admin');

// $ar=fopen("datos.html","a") or die("Problemas en la creacion");
// fputs($ar,$client->getResponse());
// fclose($ar);
        $crawler = $client->request('GET', '/es/partner/edit/partner/1');

        //comprueba que vaya a la pagina de edicion de usuarios
        $this->assertRegExp('/.*\/..\/partner\/edit\/partner\/.*/', $client->getRequest()->getUri(),
            'El usuario ve el listado de usuarios'
        );

        //carga el form con los datos editados del usuario
        $editUserForm = $crawler->selectButton('btn_save')->form($partner);
        //ejecuta el submit del form
        $crawler = $client->submit($editUserForm);

        //comprueba que devuelva una pagina sin error
        $this->assertTrue($client->getResponse()->isSuccessful());

        //comprueba que vuelva a la pagina del listado de usuarios
        $this->assertRegExp('/.*\/..\/partner\/list/', $client->getRequest()->getUri(),
            'El usuario ve el listado de partners'
        );

        //volver al inicio
        UtilFunctionTest::linkTo($client, $this, 'ol li a:contains("Índice")');
    }
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
                    'adservice_partnerbundle_partnertype[movile_number_2]' => '123456879',
                    'adservice_partnerbundle_partnertype[movile_number_2]' => '123456879',
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
            array('partner' => array(
                    'adservice_partnerbundle_partnertype[email_1]'    => 'testpartner_edited@test.es',
                    'adservice_partnerbundle_partnertype[email_2]'    => 'testpartner_edited@test.com',
                ),
            ),
        );
    }
}