<?php

namespace Adservice\WorkshopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use \Adservice\UtilBundle\Tests\Controller\UtilFunctionTest;

class DefaultControllerTest extends WebTestCase
{
    public function testIsTrue(){
        $this->assertTrue(true);
        $this->assertFalse(false);
    }

    /**
     * Test de cracion de workshops
     * @dataProvider workshops
     */
   public function testNewWorkshop($workshops){
       $client = static::createClient();
       $client->followRedirects(true);

       UtilFunctionTest::doLogin($client, 'admin1', 'admin');
       UtilFunctionTest::linkTo($client, $this, 'table tr td a#workshop_list');
       UtilFunctionTest::linkTo($client, $this, 'table tr td a#newWorkshop');
       UtilFunctionTest::linkTo($client, $this, 'table tr td a#workshop_list');
       $crawler = $client->getCrawler();

       $workshopList = $crawler->filter('table[name=tbl_admin]')->selectLink('Listado de Talleres ADService')->link();
       $crawler = $client->click($workshopList);

       $newWorkshop_link = $crawler->filter('div#MainContent')->selectLink('Crear un workshop nuevo')->link();
       $num_newWorkshopLinks = $crawler->filter('a[id=newWorkshop]')->count();
       $this->assertEquals(1, $num_newWorkshopLinks, 'Hay un botón "New Workshop" en "workshop/list"');
       $crawler = $client->click($newWorkshop_link);

       //carga el form con los datos del workshop
       $newWorkshopForm = $crawler->selectButton('btn_create')->form(array('adservice_workshopbundle_workshoptype[name]' => 'name'));
       //ejecuta el submit del form
       $crawler = $client->submit($newWorkshopForm);
    }

    /**
     * DataProvider de workshops
     * @return array workshops
     */
    public function workshops() {

       $workshop1 = array('adservice_workshopbundle_workshoptype[name]'                   => 'Name Workshop1',
                          'adservice_workshopbundle_workshoptype[cif]'                    => 'cif',
                          'adservice_workshopbundle_workshoptype[partner]'                => 1,
                          'adservice_workshopbundle_workshoptype[address]'                => 'adress',
                          'adservice_workshopbundle_workshoptype[city]'                   => 'calafell',
                          'adservice_workshopbundle_workshoptype[email_1]'                => 'email1@email.com',
                          'adservice_workshopbundle_workshoptype[email_2]'                => 'email2@email.com',
                          'adservice_workshopbundle_workshoptype[region]'                 => 3,
                          'adservice_workshopbundle_workshoptype[province]'               => 2,
                          'adservice_workshopbundle_workshoptype[phone_number_1]'         => '111111111',
                          'adservice_workshopbundle_workshoptype[phone_number_2]'         => '222222222',
                          'adservice_workshopbundle_workshoptype[movile_phone_1]'         => '333333333',
                          'adservice_workshopbundle_workshoptype[movile_phone_2]'         => '444444444',
                          'adservice_workshopbundle_workshoptype[fax]'                    => '555555555',
                          'adservice_workshopbundle_workshoptype[contact]'                => 'contact',
                          'adservice_workshopbundle_workshoptype[observation_workshop]'   => 'observation_workshop',
                          'adservice_workshopbundle_workshoptype[observation_assessor]'   => 'observation_assessor',
                          'adservice_workshopbundle_workshoptype[observation_admin]'      => 'observation_admin',
                          'adservice_workshopbundle_workshoptype[diagnosis_machines]'     => 1,
                          'adservice_workshopbundle_workshoptype[active]'                 => 1,
                          'adservice_workshopbundle_workshoptype[test]'                   => 0,
                          'adservice_workshopbundle_workshoptype[typology]'               => 1,
                          'adservice_workshopbundle_workshoptype[conflictive]'            => 0,

            );

        return array($workshop1);
    }
}
