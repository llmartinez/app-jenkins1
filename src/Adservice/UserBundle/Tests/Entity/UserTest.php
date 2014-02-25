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
        $user->setUsername('testadmin'); 
        $user->setPassword('test');
        $user->setActive('1');
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
        $user->setRegion(RegionTest::GetRegion());
        $user->setWorkshop(WorkshopTest::GetWorkshop());
        $user->setCountry(CountryTest::GetCountry());
        $user->setCreatedAt(new \DateTime('today'));
        $user->setModifiedAt(new \DateTime('today'));
        $user->setModifyBy(UserTest::GetUser());
        $user->setPartner(PartnerTest::GetPartner());
        
        $this->user = $user;
    }
    
    public function testGettersUser()
    {
        $user = $this->user;
        return $user;
    }
    
    public static function GetUser()
    {
        $user = new User();
        $user->setUsername('testadmin'); 
        
        return $user;
    }
}
            