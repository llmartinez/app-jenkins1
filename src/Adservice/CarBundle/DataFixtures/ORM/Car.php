<?php
namespace Adservice\CarBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\CarBundle\Entity\Car;

class Cars  extends AbstractFixture implements OrderedFixtureInterface {
    
    public function getOrder(){ return 16; }
    
    public function load(ObjectManager $manager) {
        $cars = array(
            array('version'     => '1.8_TURBO'          , 
                  'owner'       => 'user1'              ,
                  'modified_by' => 'user1'              ,
                  'year'        => '2008'               ,
                  'vin'         => '78495126326159487'  ,
                  'plate_number'=> 'B-1593-BB'          ,
                  'created_at'  => new \DateTime()      ,
                  'modified_at' => new \DateTime()      ,
                ),
            array('version'     => '1.8 TURBO'          , 
                  'owner'       => 'user1'              ,
                  'modified_by' => 'user1'              ,
                  'year'        => '2009'               ,
                  'vin'         => '78495126326159487'  ,
                  'plate_number'=> 'B-7849-TB'          ,
                  'created_at'  => new \DateTime()      ,
                  'modified_at' => new \DateTime()      ,
                ),
            array('version'     => '1.9D TDI-IB'        , 
                  'owner'       => 'user2'              ,
                  'modified_by' => 'user2'              ,
                  'year'        => '2010'               ,
                  'vin'         => '78495126326159487'  ,
                  'plate_number'=> 'B-9548-BB'          ,
                  'created_at'  => new \DateTime()      ,
                  'modified_at' => new \DateTime()      ,
                ),
            array('version'     => '1.9D TDI - IB'      , 
                  'owner'       => 'user2'              ,
                  'modified_by' => 'user2'              ,
                  'year'        => '2011'               ,
                  'vin'         => '78495126326159487'  ,
                  'plate_number'=> 'B-3574-TB'          ,
                  'created_at'  => new \DateTime()      ,
                  'modified_at' => new \DateTime()      ,
                ),
            array('version'     => '2.0 - E 200 D'      , 
                  'owner'       => 'user3'              ,
                  'modified_by' => 'user3'              ,
                  'year'        => '2012'               ,
                  'vin'         => '78495126326159487'  ,
                  'plate_number'=> 'B-9865-TB'          ,
                  'created_at'  => new \DateTime()      ,
                  'modified_at' => new \DateTime()      ,
                ),
            array('version'     => '3.2 - E 320'        , 
                  'owner'       => 'user4'              ,
                  'modified_by' => 'user4'              ,
                  'year'        => '2013'               ,
                  'vin'         => '78495126326159487'  ,
                  'plate_number'=> 'B-1258-TB'          ,
                  'created_at'  => new \DateTime()      ,
                  'modified_at' => new \DateTime()      ,
                )
        );
        foreach ($cars as $car) {
            $entidad = new Car();
            $entidad->setVersion    ($this->getReference($car['version']));
            $entidad->setOwner      ($this->getReference($car['owner']));
            $entidad->setModifiedBy ($this->getReference($car['modified_by']));
            $entidad->setYear       ($car['year']);
            $entidad->setVin        ($car['vin']);
            $entidad->setPlateNumber($car['plate_number']);
            $entidad->setCreatedAt  ($car['created_at']);
            $entidad->setModifiedAt ($car['modified_at']);
            $manager->persist($entidad);
            
            $this->addReference($entidad->getPlateNumber(), $entidad);
        }
        $manager->flush();
    }
}

?>
