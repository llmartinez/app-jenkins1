<?php
namespace Adservice\UtilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\UtilBundle\Entity\Country;

class Countries extends AbstractFixture implements OrderedFixtureInterface {
    
    public function getOrder(){ return 2; }
    
    public function load(ObjectManager $manager) {
        $countrys = array(
            array('country' => 'Spain'),
            array('country' => 'England'),
            array('country' => 'France' ),
        );
        foreach ($countrys as $country) {
            $entidad = new Country();
            $entidad->setCountry($country['country']);
            $manager->persist($entidad);
            
            $this->addReference($entidad->getCountry(), $entidad);
        }
        $manager->flush();
    }
}

?>