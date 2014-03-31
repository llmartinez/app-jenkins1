<?php
namespace Adservice\PartnerBundle\Tests\Entity;

use Adservice\PartnerBundle\Entity\Partner;
use Adservice\UserBundle\Tests\Entity\UserTest;
use Adservice\UtilBundle\Tests\Entity\RegionTest;
use Adservice\UtilBundle\Tests\Entity\ProvinceTest;

class PartnerTest extends \PHPUnit_Framework_TestCase
{
   protected $partner;

   public function testSettersPartner()
   {
       $partner = new Partner();
       $partner->setName('PartnerTest');
       $partner->setActive('1');
       $partner->setAddress('DirectionTest');
       $partner->setCreatedAt(new \DateTime('today'));
       $partner->setEmail1('test@test.es');
       $partner->setEmail2('test@test.es');
       $partner->setFax('931112233');
       $partner->setModifiedAt(new \DateTime('today'));
       $partner->setmodifiedBy(UserTest::GetUser());
       $partner->setPhoneNumber1('931112233');
       $partner->setPhoneNumber2('931112233');
       $partner->setPostalCode('08080');
       $partner->setProvince(ProvinceTest::GetProvince());
       $partner->setRegion(RegionTest::GetRegion());

       $this->partner = $partner;
   }

   public function testGettersPartner()
   {
       $partner = $this->partner;
       return $partner;
   }

   public static function GetPartner()
   {
       $partner = new Partner();
       $partner->setName('PartnerTest');

       return $partner;
   }
}