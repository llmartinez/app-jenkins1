<?php
namespace Adservice\UtilBundle\DataFixtures\ORM\PROD;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\WorkshopBundle\Entity\DiagnosisMachine;

class DiagnosisMachines extends AbstractFixture implements OrderedFixtureInterface {

    public function getOrder(){ return 15; }

    public function load(ObjectManager $manager) {
        $machines = array(
            array( 'name' => 'Machine 1', ),
            array( 'name' => 'Machine 2', ),
            array( 'name' => 'Machine 3', ),
        );
        foreach ($machines as $machine) {
            $entidad = new DiagnosisMachine();
            $entidad->setName($machine['name']);
            $manager->persist($entidad);

            $this->addReference($entidad->getName(), $entidad);
        }
        $manager->flush();
    }
}

?>
