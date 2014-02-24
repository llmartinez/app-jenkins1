<?php

namespace Adservice\UserBundle\Tests\Controller;

class TestFunctions
{
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
     * Hace login en la aplicacion y va a su perfil
     */
    public static function doLogin($client) {
        $crawler = $client->request('GET', '/');
        //carga el form con los datos de login
        $loginForm = $crawler->selectButton('btn_login')->form(array('_username' => 'admin',
                                                                     '_password' => 'admin',
                                                                    ));
        //ejecuta el submit del form
        $crawler = $client->submit($loginForm);
        
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