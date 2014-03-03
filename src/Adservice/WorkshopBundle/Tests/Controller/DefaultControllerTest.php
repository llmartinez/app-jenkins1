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

    
    
//    public function testNewWorksho($workshops){
//    public function testNewWorksho(){
//        $client = static::createClient();
//        $client->followRedirects(true);
//        
//        UtilFunctionTest::doLogin($client, 'admin1', 'admin');
//        $crawler = $client->getCrawler();
//
//        $workshopList = $crawler->filter('table[name=tbl_admin]')->selectLink('Listado de Talleres ADService')->link();
//        $crawler = $client->click($workshopList);
//        
//        $newWorkshop_link = $crawler->filter('div#MainContent')->selectLink('Crear un workshop nuevo')->link();
//        $num_newWorkshopLinks = $crawler->filter('a[id=newWorkshop]')->count();
//        $this->assertEquals(1, $num_newWorkshopLinks, 'Hay un botón "New Workshop" en "workshop/list"');
//        $crawler = $client->click($newWorkshop_link);
//        
//        //carga el form con los datos del workshop
//        $newWorkshopForm = $crawler->selectButton('btn_create')->form(array('adservice_workshopbundle_workshoptype[name]' => 'name'));
//        //ejecuta el submit del form
//        $crawler = $client->submit($newWorkshopForm);
//        
//        
//    }
    
    /**
     * DataProvider de workshops
     * @return array workshops
     */
//    public function workshops() {
        
//        $workshop1 = array('adservice_workshopbundle_workshoptype[name]'                   => 'Name Workshop1',
//                           'adservice_workshopbundle_workshoptype[address]'                => 'adress',
//                           'adservice_workshopbundle_workshoptype[city]'                   => 'calafell',
//                           'adservice_workshopbundle_workshoptype[phone_number_1]'         => '111111111',
//                           'adservice_workshopbundle_workshoptype[phone_number_2]'         => '222222222',
//                           'adservice_workshopbundle_workshoptype[movile_phone_1]'         => '333333333',
//                           'adservice_workshopbundle_workshoptype[movile_phone_2]'         => '444444444',
//                           'adservice_workshopbundle_workshoptype[fax]'                    => '555555555',
//                           'adservice_workshopbundle_workshoptype[contact]'                => 'contact',
//                           'adservice_workshopbundle_workshoptype[email_1]'                => 'email1@email.com',
//                           'adservice_workshopbundle_workshoptype[email_2]'                => 'email2@email.com',
//                           'adservice_workshopbundle_workshoptype[observations]'           => 'observations',
//                           'adservice_workshopbundle_workshoptype[active]'                 => 1,
//                           'adservice_workshopbundle_workshoptype[adservice_plus]'         => 1,
//                           'adservice_workshopbundle_workshoptype[test]'                   => 0,
//                           'adservice_workshopbundle_workshoptype[typology]'               => 2,
//                           'adservice_workshopbundle_workshoptype[partner]'                => 1,
//                           'adservice_workshopbundle_workshoptype[conflictive]'            => 0,
//                           'adservice_workshopbundle_workshoptype[province]'               => 2,
//                           'adservice_workshopbundle_workshoptype[region]'                 => 3
//            );
        
        
//        return array($workshop1);
//    }            

}
