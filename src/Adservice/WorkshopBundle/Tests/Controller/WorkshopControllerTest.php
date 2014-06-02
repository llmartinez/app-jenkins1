<?php

namespace Adservice\WorkshopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use \Adservice\UtilBundle\Tests\Controller\UtilFunctionTest;

class WorkshopControllerTest extends WebTestCase
{
    /**
     * Test de cracion de workshops
     */
   public function testNewWorkshop(){
       $client = static::createClient();
       $client->followRedirects(true);

       UtilFunctionTest::doLogin($client, 'admin', 'admin');
       UtilFunctionTest::linkTo($client, $this, 'div a#workshop_list');
       UtilFunctionTest::linkTo($client, $this, 'div legend a#newWorkshop');
       $crawler = $client->getCrawler();

       //carga el form con los datos del workshop
       $newWorkshopForm = $crawler->selectButton('btn_create')->form();

                  $newWorkshopForm['adservice_workshopbundle_workshoptype[name]']                     = 'Name Workshop1'       ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[code_workshop]']            = substr(microtime(),2,8);
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[cif]']                      = 'cif'                  ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[partner]']                  = 1                      ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[address]']                  = 'adress'               ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[city]']                     = 'calafell'             ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[email_1]']                  = 'email1@email.com'     ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[email_2]']                  = 'email2@email.com'     ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[region]']                   = 1                      ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[phone_number_1]']           = '111111111'            ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[phone_number_2]']           = '222222222'            ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[movile_number_1]']          = '333333333'            ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[movile_number_2]']          = '444444444'            ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[fax]']                      = '555555555'            ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[contact_name]']             = 'contact_name'         ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[contact_surname]']          = 'contact_surname'      ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[observation_workshop]']     = 'observation_workshop' ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[observation_assessor]']     = 'observation_assessor' ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[observation_admin]']        = 'observation_admin'    ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[diagnosis_machines]']       = 1                      ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[active]']                   = 1                      ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[test]']                     = 1                      ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[typology]']                 = 1                      ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[conflictive]']              = 1                      ;
       //ejecuta el submit del form
       $crawler = $client->submit($newWorkshopForm);
    }
}
