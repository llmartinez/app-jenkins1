<?php
namespace Adservice\WorkshopBundle\Tests\Entity;

use Adservice\WorkshopBundle\Entity\Workshop;
use Adservice\UserBundle\Tests\Entity\UserTest;
use Adservice\UtilBundle\Tests\Entity\RegionTest;
use Adservice\UtilBundle\Tests\Entity\ProvinceTest;
use Adservice\PartnerBundle\Tests\Entity\PartnerTest;

class WorkshopTest extends \PHPUnit_Framework_TestCase
{
   protected $workshop;

   public function testSettersWorkshop()
   {
       $workshop = new Workshop();
       $workshop->setName('WorkshopTest');
       $workshop->setActive('1');
       $workshop->setAddress('DirectionTest');
       $workshop->setCity('CityTest');
       $workshop->setConflictive(false);
       $workshop->setCreatedAt(new \DateTime('today'));
       $workshop->setEmail1('test@test.es');
       $workshop->setEmail2('test@test.es');
       $workshop->setEndtestAt(new \DateTime('tomorrow'));
       $workshop->setFax('931112233');
       $workshop->setLowdateAt(new \DateTime('tomorrow'));
       $workshop->setModifiedAt(new \DateTime('today'));
       $workshop->setModifiedBy(UserTest::GetUser());
       $workshop->setMovileNumber1('655112233');
       $workshop->setMovileNumber2('655112233');
       $workshop->setObservationWorkshop('ObservationWorkshopTest');
       $workshop->setObservationAssessor('ObservationAssessorTest');
       $workshop->setObservationAdmin   ('ObservationAdminTest'   );
       $workshop->setPartner(PartnerTest::GetPartner());
       $workshop->setPhoneNumber1('931112233');
       $workshop->setPhoneNumber2('931112233');
       $workshop->setProvince(ProvinceTest::GetProvince());
       $workshop->setRegion(RegionTest::GetRegion());
       $workshop->setTest(false);
       $workshop->setTypology(TypologyTest::GetTypology());
       $workshop->setUpdateAt(new \DateTime('today'));

       $this->workshop = $workshop;
   }

   public function testGettersWorkshop()
   {
       $workshop = $this->workshop;
       return $workshop;
   }

   public static function GetWorkshop()
   {
       $workshop = new Workshop();
       $workshop->setName('WorkshopTest');

       return $workshop;
   }
}