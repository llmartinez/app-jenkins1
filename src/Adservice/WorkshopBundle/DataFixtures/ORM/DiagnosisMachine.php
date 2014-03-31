<?php
namespace Adservice\WorkshopBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\WorkshopBundle\Entity\Typology;

class DiagnosisMachine extends AbstractFixture implements OrderedFixtureInterface {

    public function getOrder(){ return 13; }

    public function load(ObjectManager $manager) {
        $typologies = array(
            array( 'name' => 'Machine 1', ),
            array( 'name' => 'Machine 2', ),
            array( 'name' => 'Machine 3', ),

        );
        foreach ($typologies as $typology) {
            $entidad = new DiagnosisMachine();
            $entidad->setName($typology['name']);
            $manager->persist($entidad);

            $this->addReference($entidad->getName(), $entidad);
        }
        $manager->flush();
    }
}

?>
