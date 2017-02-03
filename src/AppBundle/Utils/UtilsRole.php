<?php
namespace AppBundle\Utils;

class UtilsRole
{
    public static $rolesGod = array('1' => 'ROLE_GOD', '2' => 'ROLE_SUPER_ADMIN', '3' => 'ROLE_ADMIN', '4' => 'ROLE_TOP', '5' => 'ROLE_SUPER_PARTNER',
                                    '6' => 'ROLE_PARTNER', '7' => 'ROLE_COMMERCIAL', '8' => 'ROLE_ADVISER', '9' => 'ROLE_WORKSHOP', '10' => 'ROLE_USER');

    public static $rolesAdmin = array('3' => 'ROLE_ADMIN', '4' => 'ROLE_TOP', '5' => 'ROLE_SUPER_PARTNER',
                                      '6' => 'ROLE_PARTNER', '7' => 'ROLE_COMMERCIAL', '8' => 'ROLE_ADVISER', '9' => 'ROLE_WORKSHOP');

    public static $rolesPartner = array('7' => 'ROLE_COMMERCIAL');

    public static $rolesCommercial = array('9' => 'ROLE_WORKSHOP');


    public static function getRolesForROLE_GOD() {
        return self::$rolesGod;
    }

    public static function getRolesForROLE_SUPER_ADMIN() {
        return self::$rolesAdmin;
    }

    public static function getRolesForROLE_ADMIN() {
        return self::$rolesAdmin;
    }
    
    public static function getRolesForROLE_TOP() {
        return self::$rolesPartner;
    }
    
    public static function getRolesForROLE_SUPER_PARTNER() {
        return self::$rolesPartner;
    }
    
    public static function getRolesForROLE_PARTNER() {
        return self::$rolesPartner;
    }
    
    public static function getRolesForROLE_COMMERCIAL() {
        return self::$rolesCommercial;
    }
    
    public static function getRolesForROLE_ADVISER() {
        return null;
    }
    
    public static function getRolesForROLE_WORKSHOP() {
        return null;
    }
    
    public static function getRolesForROLE_USER() {
        return null;
    }
}