<?php
namespace AppBundle\Utils;

//  HowToUse(Utils):     
// 	$this->get('utils')->do(something);
// 
class Utils
{
/*
    public static $roles = array('1' => 'ROLE_GOD', '2' => 'ROLE_SUPER_ADMIN', '3' => 'ROLE_ADMIN', '4' => 'ROLE_TOP', '5' => 'ROLE_SUPER_PARTNER', '6' => 'ROLE_PARTNER',
                                 '7' => 'ROLE_COMMERCIAL', '8' => 'ROLE_ADVISER', '9' => 'ROLE_WORKSHOP', '10' => 'ROLE_USER');

    public static $services = array('0' => 'All', '1' => 'SERV_GED', '2' => 'SERV_ADSERVICE_ES', '3' => 'SERV_ASSISTANCE_DIAG_24', '4' => 'SERV_ADSERVICE_PT',
                                    '5' => 'SERV_PHONE_EINA_ECP', '6' => 'SERV_PHONE_EINA_JS', '7' => 'SERV_PHONE_EINA_TECHNODIAG', '8' => 'SERV_ACTIA', '9' => 'SERV_NEXUS');
*/

    public static $roles = array('1' => 'ROLE_GOD', '2' => 'ROLE_SUPER_ADMIN', '3' => 'ROLE_ADMIN', '4' => 'ROLE_TOP', '5' => 'ROLE_SUPER_PARTNER', '6' => 'ROLE_PARTNER',
                                 '7' => 'ROLE_COMMERCIAL', '8' => 'ROLE_ADVISER', '9' => 'ROLE_WORKSHOP', '10' => 'ROLE_USER');

    public static $permission = array('11' => 'ONLY_LIST', '12' => 'ONLY_CREATE');

    public static $services = array('0' => 'All', '13' => 'SERV_GED', '14' => 'SERV_ADSERVICE_ES', '15' => 'SERV_ADSERVICE_PT', '16' => 'SERV_ASSISTANCE_DIAG_24',
                                    '17' => 'SERV_PHONE_EINA_ECP', '18' => 'SERV_PHONE_EINA_JS', '19' => 'SERV_PHONE_EINA_TECHNODIAG',
                                    '20' => 'SERV_ACTIA', '21' => 'SERV_NEXUS');
/*
    TODO: revisar IDs de logos
*/
    public static $logos = array('1' => 'logo_ged.png', '2' => 'logo_adservice.png', '3' => 'logo_assistance-diag-24.png', '4' => 'logo_adservice.png',
                                 '5' => 'logo_ecp.png', '6' => 'logo_phone-eina.png', '7' => 'logo_phone-eina.png', '8' => 'logo_actia.png', '8' => 'logo_nexus.png');

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
    
    public static function getServices($ids=null)
    {
        $array = array();
        if($ids != null)
        {
            foreach ($ids as $id)
            {   
                $array[$id] = self::$services[$id];
            }
            return $array;
        }
        else return self::$services;
    }

    public static function getServicesForRole($role_id)
    {
        if    (in_array($role_id, array(1, 2)))             return array('0' => self::$services[0]);
        elseif(in_array($role_id, array(3, 4, 5, 6, 7, 9))) return array_diff(self::$services, ["All"]);
        elseif(in_array($role_id, array(8, 10)))            return self::$services;
        else return self::$services;
    }

    /** Si el usuario tiene un Servicio asignado devolvemos solo ese servicio,
        sino devolvemos todos los que le permite su Rol
     */
    public static function getFormServices($attr)
    {
        if($attr['tokenService'] != '0')
            return Utils::getServices($attr['tokenService']);
        else
            return Utils::getServicesForRole($attr['role']);
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