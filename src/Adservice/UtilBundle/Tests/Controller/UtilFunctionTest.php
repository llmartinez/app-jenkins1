<?php

namespace Adservice\UtilBundle\Tests\Controller;

class UtilFunctionTest
{
    /**
     * 
     * @param Crawler $crawler
     * @param Client $client
     * @param String $lang (en|es|fr)
     */
    public static function setLang($crawler, $client, $lang){
        if ($lang == 'es') {
            $select_spanish_link = $crawler->filter('#selectLang a')->eq(1)->link();
            $crawler = $client->click($select_spanish_link);
        }
        
        return $crawler;
    }
    
    /**
     * hace un link y comprueba que redireccione correctamente
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
     * Hace login en la aplicacion y va a su perfil
     * @param client $client
     * @return client
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

