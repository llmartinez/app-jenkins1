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
        
        $this->role = $role;
    }
    
    public function testGettersRole()
    {
        $role = $this->role;
        return $role;
    }
    
    public static function GetRole()
    {
        $role = new Role();
        $role->setName('RoleTest'); 
        
        return $role;
    }
}