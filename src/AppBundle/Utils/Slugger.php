<?php
namespace AppBundle\Utils;
 
//  HowToUse(Slugger):     
// 	$slug = $this->get('slugger')->slugify($post->getTitle()));
class Slugger
{
    static function slugify($string)
    {
        return preg_replace(
            '/[^a-z0-9]/', '-', strtolower(trim(strip_tags($string)))
        );
    }

    /** Elimina espacios, tabuladores y saltos de línia de un string */
    static function noSpaces($string)
    {
        $string = preg_replace('/\s+/', '', $string);  
        return $string;
    }
}