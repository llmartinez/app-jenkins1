<?php
namespace AppBundle\Utils;

//  HowToUse(Utils):     
// 	$this->get('utils')->do(something);
// 
class Utils
{
    public static $roles = array('1' => 'ROLE_GOD', '2' => 'ROLE_SUPER_ADMIN', '3' => 'ROLE_ADMIN', '4' => 'ROLE_TOP', '5' => 'ROLE_SUPER_PARTNER', '6' => 'ROLE_PARTNER',
                                 '7' => 'ROLE_COMMERCIAL', '8' => 'ROLE_ADVISER', '9' => 'ROLE_WORKSHOP', '10' => 'ROLE_USER');

    public static $categoryServices = array('0' => 'All', '1' => 'GED', '2' => 'AD Service ES', '3' => 'Assistance Diag 24 FR', '4' => 'AD Service PT',
                                            '5' => 'Phone Eina ECP', '6' => 'Phone Eina JS', '7' => 'Phone Eina TecnoDiag', '8' => 'Phone Eina Actia');

    public static $logos = array('1' => '_ged.png', '2' => '_adservice.png', '3' => '_assistance-diag-24.png', '4' => '_adservice.png',
                                 '5' => '_ecp.png', '6' => '_phone-eina.png', '7' => '_phone-eina.png', '8' => '_phone-eina.png');

    public static $countries = array('1' => 'spain', '3' => 'france', '4' => 'portugal', '5' => 'uk', '6' => 'ireland');

    public static $languages = array('1' => 'es', '2' => 'en', '3' => 'fr', '4' => 'pt');

    public static function getRoles($id=null)
    {
        if($id) return self::$roles[$id];
        else    return self::$roles;
    }

    // La función getRolesForRole() devuelve todos los roles a los que tiene acceso el Rol introducido (todos menos los indicados en array_diff)
    public static function getRolesForRole($role_id)
    {
        if    (in_array($role_id, array(1)))        return array_diff(self::$roles, ["ROLE_GOD"]);
        elseif(in_array($role_id, array(2, 3)))     return array_diff(self::$roles, ["ROLE_GOD","ROLE_SUPER_ADMIN", "ROLE_USER"]);
        elseif(in_array($role_id, array(4, 5, 6)))  return array_diff(self::$roles, array_diff(self::$roles, ["ROLE_COMMERCIAL"]));
        else return null;
    }
    public static function getCategoryServices($id=null)
    {
        if($id) return self::$categoryServices[$id];
        else    return self::$categoryServices;
    }

    public static function getCategoryServicesForRole($role_id)
    {
        if    (in_array($role_id, array(1, 2)))             return array('0' => self::$categoryServices[0]);
        elseif(in_array($role_id, array(3, 4, 5, 6, 7, 9))) return array_diff(self::$categoryServices, ["All"]);
        elseif(in_array($role_id, array(8, 10)))            return self::$categoryServices;
        else return self::$categoryServices;
    }

    public static function getLogos($id=null)
    {
        if($id) return self::$logos[$id];
        else    return self::$logos;
    }

    public static function getCountries($id=null)
    {
        if($id) return self::$countries[$id];
        else    return self::$countries;
    }

    public static function getLanguages($id=null)
    {
        if($id) return self::$languages[$id];
        else    return self::$languages;
    }
}