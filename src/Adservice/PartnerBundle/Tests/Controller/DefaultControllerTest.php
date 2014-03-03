<?php

namespace Adservice\PartnerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use \Adservice\UtilBundle\Tests\Controller\UtilFunctionTest;

class DefaultControllerTest extends WebTestCase {

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
        UtilFunctionTest::linkTo($client, $this, 'table tr td a#partner_list');
        UtilFunctionTest::linkTo($client, $this, 'table tr td a:contains("Crear un partner nuevo")');

        //carga el form con los datos del partner
        $newPartnerForm = $client->getCrawler()->selectButton('btn_create')->form($partner);
        //ejecuta el submit del form
        $crawler = $client->submit($newPartnerForm);

        //comprueba que devuelva una pagina sin error
        $this->assertTrue($client->getResponse()->isSuccessful());

        //comprueba que vuelva a la pagina del listado de partners
        $this->assertRegExp('/.*\/..\/partner\/list/', $client->getRequest()->getUri(), 'El usuario ve el listado de partners');
        $this->assertGreaterThan(0, $crawler->filter('table tr td:contains("testpartner")')->count(), 'El partners creado esta en la lista');

        //volver al inicio
        UtilFunctionTest::linkTo($client, $this, 'ol li a:contains("Home")');
    }

    /**
     * DataProvider de partners: Contiene un array de partners
     * @return array partners
     */
    public function partners() {
        return array(
            array(
                'partner' => array(
                    'adservice_partnerbundle_partnertype[name]'             => 'testpartner',
                    'adservice_partnerbundle_partnertype[phone_number_1]'   => '123456789',
                    'adservice_partnerbundle_partnertype[phone_number_2]'   => '123456879',
                    'adservice_partnerbundle_partnertype[fax]'              => '123456789',
                    'adservice_partnerbundle_partnertype[email_1]'          => 'testadmin@test.es',
                    'adservice_partnerbundle_partnertype[email_2]'          => 'testadmin@test.es',
                    'adservice_partnerbundle_partnertype[address]'          => 'testadmin@test.es',
                    'adservice_partnerbundle_partnertype[postal_code]'      => '00000',
                    'adservice_partnerbundle_partnertype[active]'           => '1',
                    'adservice_partnerbundle_partnertype[region]'           => '1',
                    'adservice_partnerbundle_partnertype[province]'         => '1',
                ),
            ),
        );
    }

}
