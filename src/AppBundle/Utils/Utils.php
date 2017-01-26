<?php
namespace AppBundle\Utils;

//  HowToUse(Utils):     
// 	$this->get('utils')->do(something);
// 
class Utils
{
    public static $logos = array('1' => '_ged.png', '2' => '_adservice.png', '3' => '_assistance-diag-24.png', '4' => '_adservice.png',
                                 '5' => '_ecp.png', '6' => '_phone-eina.png', '7' => '_phone-eina.png', '8' => '_phone-eina.png');

    public static $categoryServices = array('1' => 'GED', '2' => 'AD Service ES', '3' => 'Assistance Diag 24 FR', '4' => 'AD Service PT',
                                            '5' => 'Phone Eina ECP', '6' => 'Phone Eina JS', '7' => 'Phone Eina TecnoDiag', '8' => 'Phone Eina Actia');

    public static $roles = array('1' => 'ROLE_GOD', '2' => 'ROLE_SUPER_ADMIN', '3' => 'ROLE_ADMIN', '4' => 'ROLE_TOP', '5' => 'ROLE_SUPER_PARTNER',
                                 '6' => 'ROLE_PARTNER', '7' => 'ROLE_COMMERCIAL', '8' => 'ROLE_ADVISER', '9' => 'ROLE_WORKSHOP', '10' => 'ROLE_USER');


    public static $countries = array('1' => 'spain', '3' => 'france', '4' => 'portugal', '5' => 'england', '6' => 'ireland');

    public static $languages = array('1' => 'es', '2' => 'en', '3' => 'fr', '4' => 'pt');

    public static function getCategoryServices() {
        return self::$categoryServices;
    }

    public static function getRoles() {
        return self::$roles;
    }

    public static function getLogos() {
        return self::$logos;
    }

    public static function getCountries() {
        return self::$countries;
    }

    public static function getLanguages() {
        return self::$languages;
    }

}