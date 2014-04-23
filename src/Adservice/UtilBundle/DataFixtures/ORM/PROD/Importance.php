<?php
namespace Adservice\UtilBundle\DataFixtures\ORM\PROD;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\TicketBundle\Entity\Importance;

class Importances extends AbstractFixture implements OrderedFixtureInterface {

    public function getOrder(){ return 5; }

    public function load(ObjectManager $manager) {
        $importances = array(
            array('importance' => 'Information'),
            array('importance' => 'Specific information' ),
            array('importance' => 'Diagnosis and repair' ),
            array('importance' => 'Advanced diagnostics' ),
        );
        foreach ($importances as $importance) {
            $entidad = new Importance();
            $entidad->setImportance($importance['importance']);
            $manager->persist($entidad);

            $this->addReference($entidad->getImportance(), $entidad);
        }
        $manager->flush();
    }
}

?>