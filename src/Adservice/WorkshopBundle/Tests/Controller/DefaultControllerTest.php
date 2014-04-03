<?php

namespace Adservice\WorkshopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use \Adservice\UtilBundle\Tests\Controller\UtilFunctionTest;

class DefaultControllerTest extends WebTestCase
{
    /**
     * Test de cracion de workshops
     */
   public function testNewWorkshop(){
       $client = static::createClient();
       $client->followRedirects(true);

       UtilFunctionTest::doLogin($client, 'admin1', 'admin');
       UtilFunctionTest::linkTo($client, $this, 'table tr td a#workshop_list');
       UtilFunctionTest::linkTo($client, $this, 'table tr td a#newWorkshop');
       $crawler = $client->getCrawler();

       //carga el form con los datos del workshop
       $newWorkshopForm = $crawler->selectButton('btn_create')->form();

                  $newWorkshopForm['adservice_workshopbundle_workshoptype[name]']                     = 'Name Workshop1'       ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[cif]']                      = 'cif'                  ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[partner]']                  = 1                      ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[address]']                  = 'adress'               ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[city]']                     = 'calafell'             ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[email_1]']                  = 'email1@email.com'     ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[email_2]']                  = 'email2@email.com'     ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[region]']                   = 3                      ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[province]']                 = 2                      ;
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
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[update_at][date][day]']     = 1                      ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[update_at][date][month]']   = 4                      ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[update_at][date][year]']    = 2014                   ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[update_at][time][hour]']    = 13                     ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[update_at][time][minute]']  = 13                     ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[lowdate_at][date][day]']    = 1                      ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[lowdate_at][date][month]']  = 4                      ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[lowdate_at][date][year]']   = 2014                   ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[lowdate_at][time][hour]']   = 13                     ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[lowdate_at][time][minute]'] = 13                     ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[endtest_at][date][day]']    = 1                      ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[endtest_at][date][month]']  = 4                      ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[endtest_at][date][year]']   = 2014                   ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[endtest_at][time][hour]']   = 13                     ;
                  $newWorkshopForm['adservice_workshopbundle_workshoptype[endtest_at][time][minute]'] = 13                     ;
       //ejecuta el submit del form
       $crawler = $client->submit($newWorkshopForm);
    }
}
