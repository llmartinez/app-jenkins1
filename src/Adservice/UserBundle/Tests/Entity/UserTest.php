<?php
namespace Adservice\UserBundle\Tests\Entity;

use Adservice\UserBundle\Entity\User;
use Adservice\UtilBundle\Tests\Entity\CountryTest;
use Adservice\UtilBundle\Tests\Entity\RegionTest;
use Adservice\UtilBundle\Tests\Entity\ProvinceTest;
use Adservice\UserBundle\Tests\Entity\RoleTest;
use Adservice\PartnerBundle\Tests\Entity\PartnerTest;
use Adservice\WorkshopBundle\Tests\Entity\WorkshopTest;

class UserTest extends \PHPUnit_Framework_TestCase
{
    protected $user;
    
    public function testSetters()
    {
        $user = new User();
        $user->setUsername('userTest'); 
        $user->setPassword('test');
        $user->setActive('0');
        $this->assertEquals(false, $user->isEnabled(), "Probar que el usuario este deshabilitado (active = 0)");
        $user->setActive('1');
        $this->assertEquals(true,  $user->isEnabled(), "Probar que el usuario este habilitado (active = 1)");
        $user->setName('Test');
        $user->setSurname('User_admin');
        $user->setCity('Badalona');
        $user->setProvince(ProvinceTest::GetProvince());
        $user->setPhoneNumber1('931112233');
        $user->setPhoneNumber2('931112233');
        $user->setMovileNumber1('655112233');
        $user->setMovileNumber2('655112233');
        $user->setFax('931112233');
        $user->setEmail1('test@test.es');
        $user->setEmail2('test@test.es');
        $user->setDni('12345678T');
        $user->addRole(RoleTest::GetRole());
        $this->assertEquals('RoleTest', $user->getUserRole()[0]->getName() , "Probar que tenga el rol: RoleTest");
        $user->setRegion(RegionTest::GetRegion());
        $user->setWorkshop(WorkshopTest::GetWorkshop());
        $user->setCountry(CountryTest::GetCountry());
        $user->setCreatedAt(new \DateTime('today'));
        $this->assertEquals(new \DateTime('today'), $user->getCreatedAt(),   "Probar la fecha de creacion: today");
        $user->setModifiedAt(new \DateTime('today'));
        $this->assertEquals(new \DateTime('today'), $user->getModifiedAt(),  "Probar la fecha de modificacion: today");
        $user->setModifyBy(UserTest::GetUser());
        $this->assertEquals('userTest', $user->getModifyBy()->getUserName(), "Probar el usuario de modificacion: userTest");
        $user->setPartner(PartnerTest::GetPartner());
        
        $this->user = $user;
    }
    
    public static function GetUser()
    {
        $user = new User();
        $user->setUsername('userTest'); 
        $user->addRole(RoleTest::GetRole());
        
        return $user;
    }
}
            