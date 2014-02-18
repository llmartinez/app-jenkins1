<?php
namespace Adservice\UtilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\UtilBundle\Entity\Region;

class Regions extends AbstractFixture implements OrderedFixtureInterface {
    
    public function getOrder(){ return 3; }
    
    public function load(ObjectManager $manager) {
        $regions = array(
            array('region' => 'Catalunya'),
            array('region' => 'Madrid'),
            array('region' => 'Canarias' ),
        );
        foreach ($regions as $region) {
            $entidad = new Region();
            $entidad->setRegion($region['region']);
            $manager->persist($entidad);
            
            $this->addReference($entidad->getRegion(), $entidad);
        }
        $manager->flush();
    }
}

?>