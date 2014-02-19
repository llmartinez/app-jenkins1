<?php
namespace Adservice\TicketBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\TicketBundle\Entity\Incidence;
use Adservice\UtilBundle\DataFixtures\ORM\Data as Data;

class Incidences extends AbstractFixture implements OrderedFixtureInterface {
    
    public function getOrder(){ return 45; }
    
    public function load(ObjectManager $manager) {
        $num = Data::getNumTickets();
        
        for($i=1;$i<=$num;$i++)
        { 
            $ticket = $this->getReference('Test n.'.$i);
            if($ticket->getStatus()->getName() == 'closed') {
                
                $entidad = new Incidence();
                $entidad->setTicket     ($ticket);
                $entidad->setStatus     ($this->getReference($entidad->getTicket()->getStatus()->getName()));
                $entidad->setOwner      ($this->getReference($entidad->getTicket()->getAssignedTo()->getUserName()));
                $entidad->setWorkshop   ($this->getReference($entidad->getTicket()->getWorkshop()->getName()));
                $entidad->setModifiedBy ($this->getReference($entidad->getTicket()->getAssignedTo()->getUserName()));
                $entidad->setImportance (1);
                $entidad->setDescription(Data::getDescription($i));
                $entidad->setSolution   (Data::getSolution($i));
                $entidad->setCreatedAt  (new \DateTime());
                $entidad->setModifiedAt (new \DateTime());
                $manager->persist($entidad);

                $this->addReference($entidad->getSolution(), $entidad);
            }
        }
        $manager->flush();
    }
}

?>