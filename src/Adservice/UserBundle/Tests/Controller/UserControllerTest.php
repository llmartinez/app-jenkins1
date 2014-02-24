<?php

namespace Adservice\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    protected $client;
    
    protected function setUp() {
        $this->client = static::createClient();
        $this->client->followRedirects(true);
        $this->client = TestFunctions::doLogin($this->client);
    }
    
    public function generaUsers()
    {
        $types = array( 
                    array('type' => 'admin'   , 'value'  => 'partner'),
                    array('type' => 'assessor', 'value'  => 'partner'),
                    array('type' => 'user'    , 'value' => 'workshop'),
            );
        
        for ($i=0;$i<3;$i++)
        {
            $type = $types[$i]['type'];
            $user = array(  'adservice_userbundle_usertype[username]'                    => 'test'.$type, 
                                'adservice_userbundle_usertype[password][Contraseña]'       => 'test',
                                'adservice_userbundle_usertype[password][Repite Contraseña]'=> 'test',
                                'adservice_userbundle_usertype[name]'                       => 'Test',
                                'adservice_userbundle_usertype[surname]'                    => 'User_'.$type,
                                'adservice_userbundle_usertype[dni]'                        => '99999999T',
                                'adservice_userbundle_usertype[email_1]'                    => $type.uniqid().'@test.es',
                                'adservice_userbundle_usertype[active]'                     => '1',
                                'adservice_userbundle_usertype[region]'                     => '1',
                                'adservice_userbundle_usertype[province]'                   => '1',
                                'adservice_userbundle_usertype[country]'                    => '1',
                                'adservice_userbundle_usertype['.$types[$i]['value'].']'    => '1',
                            );
            $userInfo[] = array($user, $type);
        }
            return $userInfo;
    }
    
    public function linkToNewTypeUser($client, $type)
    {
        
        $crawler = TestFunctions::linkTo($client, $this, 'table tr td a#user_list');
        $crawler = TestFunctions::linkTo($client, $this, 'table tr td a#user_new');
        $crawler = TestFunctions::linkTo($client, $this, 'table tr td a#type_'.$type);
        
        return $client;
    }    
    
    public function testNewUser()
    {
        $client = $this->client;
        $userInfo = $this->generaUsers();
        
        foreach ($userInfo as $user) {
            $client = $this->linkToNewTypeUser($client, $user[1]);
            $crawler = $client->getCrawler();

            //carga el form con los datos del usuario
            $newUserForm = $crawler->selectButton('btn_create')->form($user[0]);
            //ejecuta el submit del form
            $crawler = $client->submit($newUserForm);

            //comprueba que devuelva una pagina sin error
            $this->assertTrue($client->getResponse()->isSuccessful());

            //comprueba que vuelva a la pagina del listado de usuarios
            $this->assertRegExp('/.*\/..\/user\/list/', $client->getRequest()->getUri(), 
                'El usuario ve el listado de usuarios'
            );
            $this->assertEquals(0, $crawler->filter('table tr td a#list_username:contains("testAdmin")')->count(), 
                'El admin creado esta en la lista'
            );
            
            //volver al inicio 
            $crawler = TestFunctions::linkTo($client, $this, 'ol li a#home');
        }        
    } 
    
    /*TODO
     * la funcion javascript que cambia la url de 'foo' a 'id_usuario' no funciona,
     * se envia al controller la funcion deleteUser('foo');
     *
    public function testDeleteUser()
    {
        $client = $this->client;
        $crawler = TestFunctions::linkTo($client, $this, 'table tr td a#user_list');
        $userInfo = $this->generaUsers();
        
        foreach ($userInfo as $user) {
            $num_users = $crawler->filter('table tr td a#list_username:contains("test'.$user[1].'")')->count();
            
            $location = 'table tr td a#btn_deletetest'.$user[1];
            $link = $crawler->filter($location)->link();
            $crawler = $client->click($link);
            
            $location = 'div#myModal div div div.modal-footer a#btn_yes';
            $link = $crawler->filter($location)->link();
            //$crawler = $client->click($link);
  //          
    echo ' --> test'.$user[1].' = '.$num_users.' /n ';die;
    
            //comprueba que vuelva a la pagina del listado de usuarios
            $_this->assertRegExp('/.*\/..\/user\/delete\/.* /', $client->getRequest()->getUri(), 
                'El usuario ve el listado de usuarios'
            );
            $_this->assertEquals(0, $crawler->filter('table tr td a#list_username:contains("'.$user.'")')->count(),
            'Se ha borrado el usuario "'.$user.'"'
            );
            //volver al inicio 
            $crawler = TestFunctions::linkTo($client, $crawler, $this, 'ol li a#home');
        }
    }
    */
    protected function tearDown() {
        parent::tearDown();
    }
}
