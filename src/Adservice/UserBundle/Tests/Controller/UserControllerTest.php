<?php

namespace Adservice\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function generaAdmin()
    {
        $usuario1 = array(  'adservice_userbundle_usertype[username]'                   => 'testAdmin', 
                            'adservice_userbundle_usertype[password][Contraseña]'       => 'test',
                            'adservice_userbundle_usertype[password][Repite Contraseña]'=> 'test',
                            'adservice_userbundle_usertype[name]'                       => 'Test',
                            'adservice_userbundle_usertype[surname]'                    => 'User',
                            'adservice_userbundle_usertype[dni]'                        => '99999999T',
                            'adservice_userbundle_usertype[email_1]'                    => 'user'.uniqid().'@test.es',
                            'adservice_userbundle_usertype[active]'                     => '1',
                            'adservice_userbundle_usertype[region]'                     => '1',
                            'adservice_userbundle_usertype[province]'                   => '1',
                            'adservice_userbundle_usertype[country]'                    => '1',
                            'adservice_userbundle_usertype[partner]'                    => '1',
                        );
        return $usuario1;
//        return array(
//            array($usuario1),
//        );
    }
    
    public function linkToNewTypeUser($type)
    {
        $client = static::createClient();
        $client = LoginControllerTest::doLogin($client, $this);
        $crawler = $client->getCrawler();
        
        $crawler = TestFunctions::linkTo($client, $crawler, $this, 'table tr td a#user_list');
        $crawler = TestFunctions::linkTo($client, $crawler, $this, 'table tr td a#user_new');
        $crawler = TestFunctions::linkTo($client, $crawler, $this, 'table tr td a#type_'.$type);
        
        return $client;
    }    
    
    public function testNewAdmin()
    {
        $client = $this->linkToNewTypeUser('admin');
        $crawler = $client->getCrawler();
        
        //carga el form con los datos del usuario
        $newUserForm = $crawler->selectButton('btn_create')->form($this->generaAdmin());
        //ejecuta el submit del form
        $crawler = $client->submit($newUserForm);
        
        //comprueba que devuelva una pagina sin error
        $this->assertTrue($client->getResponse()->isSuccessful());
        
        //comprueba que vuelva a la pagina del listado de usuarios
        $this->assertRegExp('/.*\/..\/user\/list/', $client->getRequest()->getUri(), 
            'El usuario ve el listado de usuarios'
        );
        $this->assertGreaterThan(0, $crawler->filter('table tr td a#list_username:contains("testAdmin")')->count(), 
            'El admin creado esta en la lista'
        );
        $this->deleteUser($client, $crawler, $this, 'testAdmin');
    } 
    
    public static function deleteUser($client, $crawler, $_this, $user)
    {
        $num_users = $crawler->filter('table tr td a#list_username:contains("'.$user.'")')->count();
       
        $crawler = TestFunctions::linkTo($client, $crawler, $_this, 'table tr td a#btn_delete'.$user);
        
        $link = $crawler->filter('div.modal-content div.modal-footer a#btn_yes')->link();
        $crawler = $client->click($link);
        
        //comprueba que vuelva a la pagina del listado de usuarios
        $_this->assertRegExp('/.*\/..\/user\/delete\/.*/', $client->getRequest()->getUri(), 
            'El usuario ve el listado de usuarios'
        );
        
//        $crawler = TestFunctions::linkTo($client, $crawler, $_this, 'div a#btn_yes');
       
        $_this->assertEquals(0, $crawler->filter('table tr td a#list_username:contains("'.$user.'")')->count(),
        'Se ha borrado el usuario "'.$user.'"'
       );
    }
/*******************************************************************************   
    public function testNewAssessor()
    {
        $client = $this->linkToNewTypeUser('assessor');
    }   
    
    public function testNewUser()
    {
        $client = $this->linkToNewTypeUser('user');
    }
*******************************************************************************/
}
