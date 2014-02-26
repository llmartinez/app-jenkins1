<?php
namespace Adservice\UserBundle\Tests\Entity;

use Adservice\UserBundle\Entity\Role;

class RoleTest extends \PHPUnit_Framework_TestCase
{   
    protected $role;
    
    public function testSettersRole()
    {
        $role = new Role();
        $role->setName('RoleTest'); 
        $this->assertEquals('', $role->getId(), "Probar el rol no tenga id (aun no esta asignado)");
        $this->assertEquals('RoleTest', $role, "Probar el rol no tenga id (aun no esta asignado)");
        $this->role = $role;
    }
    
    public static function GetRole()
    {
        $role = new Role();
        $role->setName('RoleTest'); 
        
        return $role;
    }
}