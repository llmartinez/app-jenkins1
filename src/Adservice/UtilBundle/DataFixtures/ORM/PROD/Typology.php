<?php
namespace Adservice\UtilBundle\DataFixtures\ORM\PROD;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\WorkshopBundle\Entity\Typology;

class Typologies extends AbstractFixture implements OrderedFixtureInterface {

    public function getOrder(){ return 14; }

    public function load(ObjectManager $manager) {
        $typologies = array(
            array( 'name' => 'Autoservice AD', ),
            array( 'name' => 'Carrosserie AD', ),
            array( 'name' => 'Garage AD'     , ),

        );
        foreach ($typologies as $typology) {
            $entidad = new Typology();
            $entidad->setName($typology['name']);
            $entidad->setCountry($this->getReference('Spain'));
            $entidad->setActive(1);
            $manager->persist($entidad);

            $this->addReference($entidad->getName(), $entidad);
        }
        $manager->flush();
    }
}

?>
