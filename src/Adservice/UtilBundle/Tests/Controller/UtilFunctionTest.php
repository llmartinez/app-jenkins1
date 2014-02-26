<?php

namespace Adservice\UtilBundle\Tests\Controller;

class UtilFunctionTest
{
    /**
     * 
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
     * Hace login en la aplicacion 
     * @param client $client
     * @return client
     */
    public static function doLogin($client, $user, $pass) {
        $crawler = $client->request('GET', '/');
        $crawler = $client->getCrawler();
        $loginForm = $crawler->selectButton('btn_login')->form(array('_username' => $user,
                                                                     '_password' => $pass,
                                                                    ));
        //ejecuta el submit del form
        $crawler = $client->submit($loginForm);
        return $client;
    }
    
    /**
     * Prepara $client con followRedirects activado, 
     *                 inicia la sesion como $user y 
     *                 cambia el ididoma a 'es' 
     */
    public static function setClient($client, $user, $pass) {
        $client-> followRedirects(true);
        UtilFunctionTest::doLogin($client, $user, $pass);
        $crawler = $client->getCrawler();
        UtilFunctionTest::setLang($crawler, $client, 'es');
        return $client;
    }
}


/******************************************************
 * CREA UN ARCHIVO TXT CON EL CONTENIDO DE LA PAGINA
 $ar=fopen("datos.txt","a") or die("Problemas en la creacion");
 fputs($ar,$client->getResponse());
 fclose($ar);
 */

/******************************************************
 * error para comprobaciones..
$this->assertEquals(2, 1, "Probar que 1 es igual a 1");
 */
