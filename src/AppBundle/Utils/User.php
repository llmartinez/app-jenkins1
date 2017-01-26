<?php
namespace AppBundle\Utils;
 
class User
{
    public static $rolesAdmin = array('3' => 'ROLE_ADMIN', '4' => 'ROLE_TOP', '5' => 'ROLE_SUPER_PARTNER',
                                      '6' => 'ROLE_PARTNER', '7' => 'ROLE_COMMERCIAL', '8' => 'ROLE_ADVISER', '9' => 'ROLE_WORKSHOP');

    public static $rolesPartner = array('7' => 'ROLE_COMMERCIAL');

    public static $status = array('0' => 'inactive', '1' => 'active', '2' => 'test');

    public static function getRolesForAdmin() {
        return self::$rolesAdmin;
    }
    
    public static function getRolesForPartner() {
        return self::$rolesPartner;
    }

    public static function getStatus() {
        return self::$status;
    }
}