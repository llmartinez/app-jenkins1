<?php

namespace Adservice\UtilBundle\Tests\Controller;

class UtilFunctionTest
{
    //Usuario y password para el superAdmin (se utilizará para la mayoria de los login del test)
    public static $sa_user = 'admin';
    public static $sa_pass = 'admin';

    /**
     * Cambia el locale
     * @param Crawler $crawler
     * @param client $client
     * @param String $lang (en|es|fr)
     */
    public static function setLang($crawler, $client, $lang){
        if ($lang == 'es') {
            $select_spanish_link = $crawler->filter('#selectLang a')->eq(1)->link();
            $crawler = $client->click($select_spanish_link);
        }
    }

    /**
     * Hace un link y comprueba que redireccione correctamente
     * @param client $client
     * @param this   $_this
     * @param string $location
     * @return crawler
     */
    public static function linkTo($client, $_this, $location)
    {
        $crawler = $client->getCrawler();
        $link = $crawler->filter($location)->link();
        $crawler = $client->click($link);

        $_this->assertEquals(200, $client->getResponse()->getStatusCode(),
        '- Se muestra la pantalla del link a "'.$location.'" (status 200) '
        );
        return $crawler;
    }

    /**
     * Prepara $client haciendo login como $user y cambiando el idioma a 'es'
     * @param client $client
     * @param string $user
     * @param string $pass
     * @return client
     */
    public static function doLogin($client, $user, $pass) {
        //inicia la sesion como $user
        $crawler   = $client->request('GET', '/es/login');
        $loginForm = $crawler->selectButton('btn_login')->form(array('_username' => $user,
                                                                     '_password' => $pass));
        $crawler = $client->submit($loginForm);

        //cambia el ididoma a 'es'
        UtilFunctionTest::setLang($crawler, $client, 'es');

        return $client;
    }
}


/******************************************************
 * CREA UN ARCHIVO TXT CON EL CONTENIDO DE LA PAGINA
 $ar=fopen("datos.html","a") or die("Problemas en la creacion");
 fputs($ar,$client->getResponse());
 fclose($ar);
 */

/******************************************************
 * error para comprobaciones..

    $this->assertEquals(2, 1, "Probar que 1 es igual a 1");


    public function testIsTrue(){
        $this->assertTrue(true);
        $this->assertFalse(false);
    }
 */
