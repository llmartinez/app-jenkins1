<?php
namespace Adservice\UtilBundle\Tests\Entity;

use Adservice\UtilBundle\Entity\Province;
use Adservice\UtilBundle\Tests\Entity\RegionTest;

class ProvinceTest extends \PHPUnit_Framework_TestCase
{
   protected $province;
      public function testSettersProvince()
   {
       $province = new Province();
       $province->setProvince('ProvinciaTest');
       $province->setRegion(RegionTest::GetRegion());

       $this->province = $province;
   }

   public function testGettersProvince()
   {
       $province = $this->province;
       return $province;
   }

   public static function GetProvince()
   {
       $province = new Province();
       $province->setProvince('ProvinciaTest');
       $province->setRegion(RegionTest::GetRegion());

       return $province;
   }
}
