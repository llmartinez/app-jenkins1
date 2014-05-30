<?php

namespace Adservice\PopupBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use \Adservice\UtilBundle\Tests\Controller\UtilFunctionTest;

class PopupControllerTest extends WebTestCase {

    protected function setUp() {

    }

    /**
     * Test que comprueba que se cree un popup
     * @dataProvider popups
     */
    public function testNewPopup($popup) {
        $client = static::createClient();
        $client->followRedirects(true);
        //Lleva al usuario desde la pantalla de login hasta la de nuevo popup introducido por dataProvider
        UtilFunctionTest::doLogin($client, 'admin', 'admin');
        UtilFunctionTest::linkTo($client, $this, 'div#MainContent tr td a#popup_list');
        UtilFunctionTest::linkTo($client, $this, 'div#MainContent legend a:contains("Nuevo Popup")');

        //comprueba que vuelva a la pagina del listado de popups
        $this->assertRegExp('/.*\/..\/popup\/new/', $client->getRequest()->getUri(), 'El usuario ve el formulario de nuevo popup');

        //carga el form con los datos del popup
        $newpopupForm = $client->getCrawler()->selectButton('btn_create')->form($popup);
        //ejecuta el submit del form
        $crawler = $client->submit($newpopupForm);

        //comprueba que devuelva una pagina sin error
        $this->assertTrue($client->getResponse()->isSuccessful());

        //comprueba que vuelva a la pagina del listado de socios
        $this->assertRegExp('/.*\/..\/popup\/list/', $client->getRequest()->getUri(), 'El usuario ve el listado de popups');
        // $this->assertGreaterThan(0, $crawler->filter('table tr td:contains("testpopup")')->count(), 'El popup creado esta en la lista');
        //volver al inicio
        UtilFunctionTest::linkTo($client, $this, 'ol li a:contains("Índice")');
    }

    /**
     * Test que comprueba que se edite un popup
     * @dataProvider editPopups
     */
    public function testEditPopup($popup)
    {
        $client = static::createClient();
        $client-> followRedirects(true);
        UtilFunctionTest::doLogin($client, 'admin', 'admin');
        UtilFunctionTest::linkTo($client, $this, 'div#MainContent tr td a#popup_list');
        UtilFunctionTest::linkTo($client, $this, 'div#MainContent tr td a');

        //comprueba que vaya a la pagina de edicion de usuarios
        $this->assertRegExp('/.*\/..\/popup\/edit\/.*/', $client->getRequest()->getUri(),
            'El usuario ve el listado de usuarios'
        );

        //carga el form con los datos editados del usuario
        $editPopupForm = $client->getCrawler()->selectButton('btn_edit')->form($popup);
        //ejecuta el submit del form
        $crawler = $client->submit($editPopupForm);

        //comprueba que devuelva una pagina sin error
        $this->assertTrue($client->getResponse()->isSuccessful());

        //comprueba que vuelva a la pagina del listado de usuarios
        $this->assertRegExp('/.*\/..\/popup\/list/', $client->getRequest()->getUri(),
            'El usuario ve el listado de popups'
        );

        //volver al inicio
        UtilFunctionTest::linkTo($client, $this, 'ol li a:contains("Índice")');
    }

    /**
     * DataProvider de popups: Contiene un array de popups
     * @return array popups
     */
    public function popups() {
        return array(
            array(
                'popup' => array(
                    'adservice_popupbundle_popuptype[name]'            => 'testpopup',
                    'adservice_popupbundle_popuptype[description]'     => 'description Test',
                    'adservice_popupbundle_popuptype[role]'            => '1',
                    'adservice_popupbundle_popuptype[country]'         => '1',
                    'adservice_popupbundle_popuptype[active]'          => '1',
                ),
            ),
        );
    }

    /**
     * DataProvider de popups editados: Contiene dos campos de email a editar para un popup
     * @return array editPopups
     */
    public function editPopups()
    {
        return array(
            array('popup' => array(
                    'adservice_popupbundle_popuptype[name]'    => 'testpopup_edited',
                ),
            ),
        );
    }
}