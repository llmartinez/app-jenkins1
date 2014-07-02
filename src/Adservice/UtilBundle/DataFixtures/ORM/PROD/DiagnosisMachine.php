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
            array( 'name' => 'none'     , 'country' => 'spain'   , 'ref' => 'none_es'),
            array( 'name' => 'none'     , 'country' => 'andorra' , 'ref' => 'none_an'),
            array( 'name' => 'none'     , 'country' => 'france'  , 'ref' => 'none_fr'),
            array( 'name' => 'none'     , 'country' => 'portugal', 'ref' => 'none_pt'),
            array( 'name' => 'Machine 1', 'country' => 'spain'   , 'ref' => ''),
            array( 'name' => 'Machine 2', 'country' => 'spain'   , 'ref' => ''),
            array( 'name' => 'Machine 3', 'country' => 'spain'   , 'ref' => ''),
        );
        foreach ($machines as $machine) {
            $entidad = new DiagnosisMachine();
            $entidad->setName($machine['name']);
            $entidad->setCountry( $this->getReference($machine['country']));
            $entidad->setActive(1);
            $manager->persist($entidad);

            $ref = $machine['ref'];
            if ($ref == '') $ref = $entidad->getName();

            $this->addReference($ref, $entidad);
        }
        $manager->flush();
    }
}

?>
