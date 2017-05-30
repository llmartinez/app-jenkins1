<?php
namespace AppBundle\Utils;

//  HowToUse(Slugger):
// 	$slug = $this->get('slugger')->slugify($post->getTitle()));
class Slugger
{
    /** Converts a string to a slug */
    static function slugify($string)
    {
        return preg_replace(
            '/[^a-z0-9]/', '-', strtolower(trim(strip_tags($string)))
        );
    }

    /** Removes spaces, tabs and line breaks from a string */
    static function noSpaces($string)
    {
        $string = preg_replace('/\s+/', '', $string);
        return $string;
    }
}