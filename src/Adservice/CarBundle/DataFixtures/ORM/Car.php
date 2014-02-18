<?php
namespace Adservice\CarBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\CarBundle\Entity\Car;

class Cars  extends AbstractFixture implements OrderedFixtureInterface {
    
    public function getOrder(){ return 16; }
    
    
    public function load(ObjectManager $manager) 
    {
        $versions = array('1.8_TURBO', '1.9D TDI-IB', '1.8 TURBO', '1.9D TDI - IB', '2.0 - E 200 D', '3.2 - E 320'); 
    
        for($i=1;$i<=3;$i++)
        {
            $entidad = new Car();
            $entidad->setVersion    ($this->getReference($versions[$i-1]));
            $entidad->setOwner      ($this->getReference('user'.$i));
            $entidad->setModifiedBy ($this->getReference('user'.$i));
            $entidad->setYear       ('2008');
            $entidad->setVin        ($i.$i.$i.$i.$i.$i.$i.$i.$i.$i.$i.$i.$i.$i.$i.$i.$i);
            $entidad->setPlateNumber('T-'.$i.$i.$i.$i.'-TT');
            $entidad->setCreatedAt  (new \DateTime());
            $entidad->setModifiedAt (new \DateTime());
            $manager->persist($entidad);
            
            $this->addReference($entidad->getPlateNumber(), $entidad);
        }
        $manager->flush();
    } 
}

?>
