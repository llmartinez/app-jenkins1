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
        if ($lang = 'es') {
            $select_spanish_link = $crawler->filter('#selectLang a')->eq(1)->link();
            $crawler = $client->click($select_spanish_link);
        }
    }
}

