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
            array( 'name' => 'none'     , 'country' => 'spain'   ),
            array( 'name' => 'none'     , 'country' => 'andorra' ),
            array( 'name' => 'none'     , 'country' => 'france'  ),
            array( 'name' => 'none'     , 'country' => 'portugal'),
            array( 'name' => 'Machine 1', 'country' => 'spain'   ),
            array( 'name' => 'Machine 2', 'country' => 'spain'   ),
            array( 'name' => 'Machine 3', 'country' => 'spain'   ),
        );
        foreach ($machines as $machine) {
            $entidad = new DiagnosisMachine();
            $entidad->setName($machine['name']);
            $entidad->setCountry( $this->getReference($machine['country']));
            $entidad->setActive(1);
            $manager->persist($entidad);

            $this->addReference($entidad->getName(), $entidad);
        }
        $manager->flush();
    }
}

?>
