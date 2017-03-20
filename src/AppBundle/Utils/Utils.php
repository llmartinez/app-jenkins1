<?php
namespace AppBundle\Utils;

//  HowToUse(Utils):     
// 	$this->get('utils')->do(something);
// 
class Utils
{
    public static $roles = array('1' => 'ROLE_GOD', '2' => 'ROLE_SUPER_ADMIN', '3' => 'ROLE_ADMIN', '4' => 'ROLE_TOP', '5' => 'ROLE_SUPER_PARTNER', '6' => 'ROLE_PARTNER',
                                 '7' => 'ROLE_COMMERCIAL', '8' => 'ROLE_ADVISER', '9' => 'ROLE_WORKSHOP', '10' => 'ROLE_USER');

    public static $restriction = array('11' => 'ROLE_HIDE_LIST', '12' => 'ROLE_HIDE_CREATE', '13' => 'ROLE_HIDE_ASSIGN',
                                       '14' => 'ROLE_HIDE_USER', '15' => 'ROLE_HIDE_REPORT', '16' => 'ROLE_HIDE_ORDER', '17' => 'ROLE_HIDE_POPUP', '18' => 'ROLE_HIDE_TICKET');
                                       /*'19' => 'ROLE_HIDE_19', '20' => 'ROLE_HIDE_20');*/

    public static $services = array('0' => 'All', '21' => 'ROLE_SERV_GED', '22' => 'ROLE_SERV_ADSERVICE_ES', '23' => 'ROLE_SERV_ADSERVICE_PT',
                                    '24' => 'ROLE_SERV_ASSISTANCE_DIAG_24', '25' => 'ROLE_SERV_PHONE_EINA_ECP', '26' => 'ROLE_SERV_PHONE_EINA_JS',
                                    '27' => 'ROLE_SERV_PHONE_EINA_TECHNODIAG', '28' => 'ROLE_SERV_ACTIA', '29' => 'ROLE_SERV_NEXUS');
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

    // La función getRolesForRole() devuelve todos los roles a los que tiene acceso el Rol introducido (todos menos los indicados en array_diff o al revés con doble array_diff)
    public static function getRolesForRole($role_id)
    {
        if    (in_array($role_id, array(1))) return array_diff(self::$roles, ["ROLE_GOD"]);
        elseif(in_array($role_id, array(2))) return array_diff(self::$roles, ["ROLE_GOD","ROLE_SUPER_ADMIN", "ROLE_USER"]);
        elseif(in_array($role_id, array(3))) return array_diff(self::$roles, ["ROLE_GOD","ROLE_SUPER_ADMIN","ROLE_ADMIN", "ROLE_USER"]);
        elseif(in_array($role_id, array(4))) return array_diff(self::$roles, array_diff(self::$roles, ["ROLE_SUPER_PARTNER", "ROLE_PARTNER", "ROLE_COMMERCIAL"]));
        elseif(in_array($role_id, array(5))) return array_diff(self::$roles, array_diff(self::$roles, ["ROLE_PARTNER", "ROLE_COMMERCIAL"]));
        elseif(in_array($role_id, array(6))) return array_diff(self::$roles, array_diff(self::$roles, ["ROLE_COMMERCIAL"]));
        else return null;
    }

    // Si entras en un rol al que no tienes permisos para acceder te devuelve al listado de selección de rol
    public static function hasAccessTo($_this, $role)
    {
        $user_role_id = $_this->get('security.token_storage')->getToken()->getUser()->getRoleId();
        $roles = self::getRolesForRole($user_role_id);
        
        return array_key_exists($role, $roles);
    }
    
    public static function getRestrictions($ids=null)
    {
        $array = array();
        if($ids != null)
        {
            foreach ($ids as $id)
            {   
                $array[$id] = self::$restriction[$id];
            }
            return $array;
        }
        else return self::$restriction;
    }

    // La función getRestrictionsForRole() devuelve todos los roles de restriccion a los que tiene acceso el Rol introducido (solo los indicados en doble array_diff)
    public static function getRestrictionsForRole($role_id)
    {
        if    (in_array($role_id, array(1)))       return self::$restriction;
        elseif(in_array($role_id, array(2, 3)))    return array_diff(self::$restriction, array_diff(self::$roles, ["ROLE_HIDE_LIST", "ROLE_HIDE_CREATE"]));
        elseif(in_array($role_id, array(4, 5, 6))) return array_diff(self::$restriction, array_diff(self::$roles, ["ROLE_HIDE_LIST", "ROLE_HIDE_CREATE"]));
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