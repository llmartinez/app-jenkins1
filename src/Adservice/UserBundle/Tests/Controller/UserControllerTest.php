<?php

namespace Adservice\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use \Adservice\UtilBundle\Tests\Controller\UtilFunctionTest;

class DefaultControllerTest extends WebTestCase
{
    protected $client;
    
    /**
     * Prepara $client con sesion iniciada como admin y con followRedirects activado
     */
    protected function setUp() {
        $this->client   = static::createClient();
        $this->client   ->followRedirects(true);
        $this->client   = UtilFunctionTest::doLogin($this->client);
    }
    
    /**
     * Prepara $client con sesion iniciada como admin y con followRedirects activado
     * @param Client $client
     * @return Client
     */

    protected static function setClient($client) {
        
        $client = static::createClient();
        $client->followRedirects(true);
        $client = UtilFunctionTest::doLogin($this->client);
        
        return $client;
    }

    /**
     * DataProvider de usuarios: Contiene un admin, un assessor y un user
     * @return array users
     */

    public function users()
    {
        return array(
            array('type' => 'admin',
                  'user' => array(
                                'adservice_userbundle_usertype[username]'                   => 'testadmin', 
                                'adservice_userbundle_usertype[password][Contraseña]'       => 'test',
                                'adservice_userbundle_usertype[password][Repite Contraseña]'=> 'test',
                                'adservice_userbundle_usertype[name]'                       => 'Test',
                                'adservice_userbundle_usertype[surname]'                    => 'User_admin',
                                'adservice_userbundle_usertype[dni]'                        => '99999999T',
                                'adservice_userbundle_usertype[email_1]'                    => 'testadmin@test.es',
                                'adservice_userbundle_usertype[active]'                     => '1',
                                'adservice_userbundle_usertype[region]'                     => '1',
                                'adservice_userbundle_usertype[province]'                   => '1',
                                'adservice_userbundle_usertype[country]'                    => '1',
                                'adservice_userbundle_usertype[partner]'                    => '1',
                      
                                ),
            ),
            array('type' => 'assessor',
                  'user' => array(
                                'adservice_userbundle_usertype[username]'                   => 'testassessor', 
                                'adservice_userbundle_usertype[password][Contraseña]'       => 'test',
                                'adservice_userbundle_usertype[password][Repite Contraseña]'=> 'test',
                                'adservice_userbundle_usertype[name]'                       => 'Test',
                                'adservice_userbundle_usertype[surname]'                    => 'User_assessor',
                                'adservice_userbundle_usertype[dni]'                        => '99999999T',
                                'adservice_userbundle_usertype[email_1]'                    => 'testassessor@test.es',
                                'adservice_userbundle_usertype[active]'                     => '1',
                                'adservice_userbundle_usertype[region]'                     => '1',
                                'adservice_userbundle_usertype[province]'                   => '1',
                                'adservice_userbundle_usertype[country]'                    => '1',
                                'adservice_userbundle_usertype[partner]'                    => '1',
                                ),
            ),
            array('type' => 'user',
                  'user' => array(
                                'adservice_userbundle_usertype[username]'                   => 'testuser', 
                                'adservice_userbundle_usertype[password][Contraseña]'       => 'test',
                                'adservice_userbundle_usertype[password][Repite Contraseña]'=> 'test',
                                'adservice_userbundle_usertype[name]'                       => 'Test',
                                'adservice_userbundle_usertype[surname]'                    => 'User_user',
                                'adservice_userbundle_usertype[dni]'                        => '99999999T',
                                'adservice_userbundle_usertype[email_1]'                    => 'testuser@test.es',
                                'adservice_userbundle_usertype[active]'                     => '1',
                                'adservice_userbundle_usertype[region]'                     => '1',
                                'adservice_userbundle_usertype[province]'                   => '1',
                                'adservice_userbundle_usertype[country]'                    => '1',
                                'adservice_userbundle_usertype[workshop]'                   => '1',
                                ),
            ),
        );
    }

    /**
     * DataProvider de usuarios editados: Contiene dos campos de email a editar para un admin, un assessor y un user
     * @return array userEditFields
     */

    public function userEditFields()
    {
        return array(
            array('type' => 'admin',
                  'user' => array( 
                                'adservice_userbundle_usertype[email_1]'    => 'testadmin_edited@test.es',
                                'adservice_userbundle_usertype[email_2]'    => 'testadmin_edited@test.com',
                                ),
                ),
            array('type' => 'assessor',
                  'user' => array( 
                                'adservice_userbundle_usertype[email_1]'    => 'testassessor_edited@test.es',
                                'adservice_userbundle_usertype[email_2]'    => 'testassessor_edited@test.com',
                                ),
                ),
            array('type' => 'user',
                  'user' => array( 
                                'adservice_userbundle_usertype[email_1]'    => 'testuser_edited@test.es',
                                'adservice_userbundle_usertype[email_2]'    => 'testuser_edited@test.com',
                                ),
                ),
        );
    }

    /**
     * Lleva al usuario desde la pantalla de indice hasta la de nuevo usuario del $type que se introduzca por parametro
     * @param Client $client 
     * @param String $type (admin, assessor, user)
     * @return Client
     */ 

    public function linkToNewTypeUser($client, $type)
    {
        
        $crawler = UtilFunctionTest::linkTo($client, $this, 'table tr td a#user_list');
        $crawler = UtilFunctionTest::linkTo($client, $this, 'table tr td a#user_new');
        $crawler = UtilFunctionTest::linkTo($client, $this, 'table tr td a#type_'.$type);
        
        return $client;
    }  
 
    /**
     * Test que comprueba que se cree un usuario de cada tipo
     * @dataProvider users
     */

    public function testNewUser($type, $user)
    {
        $client = $this->client;
        //$userInfo = $this->userInfo;
        $client = $this->linkToNewTypeUser($client, $type);
        $crawler = $client->getCrawler();

        //carga el form con los datos del usuario
        $newUserForm = $crawler->selectButton('btn_create')->form($user);
        //ejecuta el submit del form
        $crawler = $client->submit($newUserForm);

        //comprueba que devuelva una pagina sin error
        $this->assertTrue($client->getResponse()->isSuccessful());

        //comprueba que vuelva a la pagina del listado de usuarios
        $this->assertRegExp('/.*\/..\/user\/list/', $client->getRequest()->getUri(), 
            'El usuario ve el listado de usuarios'
        );
        $this->assertGreaterThan(0, $crawler->filter('table tr td a#list_username:contains("test'.$type.'")')->count(), 
            'El admin creado esta en la lista'
        );

        //volver al inicio 
        $crawler = UtilFunctionTest::linkTo($client, $this, 'ol li a#home');

        //comprueba que vuelva a la pagina del listado de usuarios
        $this->assertRegExp('/.*\/..\/user\/index/', $client->getRequest()->getUri(), 
            'El usuario ve la pagina principal'
        );
    } 

    /**
     * Test que comprueba que se edite un usuario de cada tipo
     * @dataProvider userEditFields
     */

    public function testEditUser($type, $userEditFields)
    {
        $client = $this->client;
        
        $crawler = UtilFunctionTest::linkTo($client, $this, 'table tr td a#user_list');
            
        $location = 'table tr td a#btn_edittest'.$type;
        $link = $crawler->filter($location)->link();
        $crawler = $client->click($link);

        //comprueba que vaya a la pagina de edicion de usuarios
        $this->assertRegExp('/.*\/..\/user\/edit\/.*/', $client->getRequest()->getUri(), 
            'El usuario ve el listado de usuarios'
        );

        //carga el form con los datos editados del usuario
        $editUserForm = $crawler->selectButton('btn_save')->form($userEditFields);
        //ejecuta el submit del form
        $crawler = $client->submit($editUserForm); 

        //comprueba que devuelva una pagina sin error
        $this->assertTrue($client->getResponse()->isSuccessful());

        //comprueba que vuelva a la pagina del listado de usuarios
        $this->assertRegExp('/.*\/..\/user\/list/', $client->getRequest()->getUri(), 
            'El usuario ve el listado de usuarios'
        );

        $this->assertGreaterThan(0, $crawler->filter('table tr td:contains("test'.$type.'_edited@test.es")')->count(),
            'Se ha editado el mail del usuario "test'.$type.'"');
        //volver al inicio 
        $crawler = UtilFunctionTest::linkTo($client, $this, 'ol li a#home');
    } 

    /**
     * Test que comprueba que salten alertas de acceso denegado
     */

    public function testAccessDenied()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        //carga el form con los datos de login
        $loginForm = $crawler->selectButton('btn_login')->form(array('_username' => 'user1',
                                                                     '_password' => 'user',
                                                                    ));
        //ejecuta el submit del form
        $crawler = $client->submit($loginForm);
        
        $this->assertEquals(0, $crawler->filter('table tr td a#user_list')->count(),
            'El usuario no ve el enlace a la lista de usuarios' );
        
            //link al cual un usuario normal no tiene acceso
            $crawler = $client->request('GET', '/es/user/list');

            $this->assertEquals(403, $client->getResponse()->getStatusCode(),
            'Acceso denegado al usuario (solo entrara un admin)'
            );  
        
    }

    /*TODO
     * la funcion javascript que cambia la url de 'foo' a 'id_usuario' no funciona,
     * se envia al controller la funcion deleteUser('foo');
    
    public function testDeleteUser()
    {
        $client = $this->client;
        $userInfo = $this->userInfo;
        $crawler = UtilFunctionTest::linkTo($client, $this, 'table tr td a#user_list');
        
        foreach ($userInfo as $user) {
            $num_users = $crawler->filter('table tr td a#list_username:contains("test'.$user[1].'")')->count();


            $location = 'table tr td a#btn_deletetest'.$user[1];
            $link = $crawler->filter($location)->link();
            $crawler = $client->click($link);
            
            $location = 'div#myModal div div div.modal-footer a#btn_yes';
            $link = $crawler->filter($location)->link();
 ----->>>>  //$crawler = $client->click($link);
            
       echo ' --> test'.$user[1].' = '.$num_users;  
       
    
            //comprueba que vuelva a la pagina del listado de usuarios
            $this->assertRegExp('/.*\/..\/user\/delete\/.* /', $client->getRequest()->getUri(), 
                'El usuario ve el listado de usuarios'
            );
            $_this->assertEquals(0, $crawler->filter('table tr td a#list_username:contains("'.$user.'")')->count(),
            'Se ha borrado el usuario "'.$user.'"'
            );
            //volver al inicio 
            //$crawler = UtilFunctionTest::linkTo($client, $crawler, $this, 'ol li a#home');
        }
    }
    */
//    protected function tearDown() {
//        parent::tearDown();
//    }
//}
    
    /**
     * Test que comprueba que se edite el perfil del usuario logeado
     */
    public function testEditProfile()
    {
        $client = $this->client;
        
        $crawler = UtilFunctionTest::linkTo($client, $this, 'table tr td a#profile');
        
        //carga el form con los datos editados del usuario
        $editUserForm = $crawler->selectButton("btn_save")->form( array( 
                                                                'adservice_userbundle_usertype[email_1]'    => 'test_edited@test.es',
                                                                'adservice_userbundle_usertype[email_2]'    => 'test_edited@test.com',
                                                            ));
        //ejecuta el submit del form
        $crawler = $client->submit($editUserForm); 

        //comprueba que devuelva una pagina sin error
        $this->assertTrue($client->getResponse()->isSuccessful());

        //comprueba que vuelva a la pagina del listado de usuarios
        $this->assertRegExp('/.*\/..\/user\/index/', $client->getRequest()->getUri(), 
            'El usuario ve el indice'
        );
        
        $crawler = UtilFunctionTest::linkTo($client, $this, 'table tr td a#profile');
        
        //comprueba que el usuario haya sido editado
        $this->assertEquals('test_edited@test.es', $crawler->filter('input#adservice_userbundle_usertype_email_1')->attr('value'),
                'El usuario ha sido editado');
    }
}

/****************************************************
 * (New User sin DataProvider)
    protected function setUp() {
        $this->client   = static::createClient();
        $this->client   ->followRedirects(true);
        $this->client   = UtilFunctionTest::doLogin($this->client);
        $this->userInfo = $this->generaUsers();
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
            $user = array(  'adservice_userbundle_usertype[username]'                   => 'test'.$type, 
                            'adservice_userbundle_usertype[password][Contraseña]'       => 'test',
                            'adservice_userbundle_usertype[password][Repite Contraseña]'=> 'test',
                            'adservice_userbundle_usertype[name]'                       => 'Test',
                            'adservice_userbundle_usertype[surname]'                    => 'User_'.$type,
                            'adservice_userbundle_usertype[dni]'                        => '99999999T',
                            'adservice_userbundle_usertype[email_1]'                    => 'test'.$type.'@test.es',
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
   
   public function testNewUser($type, $user)
    {
        $client = $this->client;
        $userInfo = $this->userInfo;
        
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
            $crawler = UtilFunctionTest::linkTo($client, $this, 'ol li a#home');
             
            //comprueba que vuelva a la pagina del listado de usuarios
            $this->assertRegExp('/.*\/..\/user\/index/', $client->getRequest()->getUri(), 
                'El usuario ve la pagina principal'
            );
        }        
 */