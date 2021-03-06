<?php
namespace Adservice\TicketBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\TicketBundle\Entity\Status;

class States extends AbstractFixture implements OrderedFixtureInterface {
    
    public function getOrder(){ return 12; }
    
    public function load(ObjectManager $manager) {
        $states = array(
            array('name' => 'open'   ),
            array('name' => 'closed' )
        );
        foreach ($states as $state) {
            $entidad = new Status();
            $entidad->setName($state['name']);
            $manager->persist($entidad);
            
            $this->addReference($entidad->getName(), $entidad);
            
        }
        $manager->flush();
    }
}

?>