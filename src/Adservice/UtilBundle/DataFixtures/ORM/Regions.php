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
            array('region' => 'Catalunya'      , 'country' => $this->getReference('Spain')),
            array('region' => 'Madrid'         , 'country' => $this->getReference('Spain')),
            array('region' => 'Canarias'       , 'country' => $this->getReference('Spain')),
            array('region' => 'Borgoña'        , 'country' => $this->getReference('France')),
            array('region' => 'Córcega'        , 'country' => $this->getReference('France')),
            array('region' => 'Isla de Francia', 'country' => $this->getReference('France')),
        );
        foreach ($regions as $region) {
            $entidad = new Region();
            $entidad->setRegion ($region['region' ]);
            $entidad->setCountry($region['country']);
            $manager->persist($entidad);

            $this->addReference($entidad->getRegion(), $entidad);
        }
        $manager->flush();
    }
}

?>