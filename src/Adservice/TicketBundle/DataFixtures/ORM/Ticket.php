<?php
namespace Adservice\TicketBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\TicketBundle\Entity\Ticket;
use Adservice\UtilBundle\DataFixtures\ORM\Data as Data;

class Tickets extends AbstractFixture implements OrderedFixtureInterface {
    
    public function getOrder(){ return 41; }
    
    public function load(ObjectManager $manager) {
        $num = Data::getNumTickets();
        
        for($i=1;$i<=$num;$i++)
        {
            $entidad = new Ticket();
            $entidad->setStatus     ($this->getReference(Data::getStatus()));
            $entidad->setCar        ($this->getReference(Data::getPlateNumber($i)));
            $entidad->setOwner      ($this->getReference(Data::getUser()));
            $entidad->setModifiedBy ($this->getReference($entidad->getOwner()->getUserName()));
            $userWorkshop = $entidad->getOwner()->getWorkshop()->getName();
            $entidad->setWorkshop   ($this->getReference($userWorkshop));
            if (rand() % 2) {
               $entidad->setAssignedTo($this->getReference(Data::getAssessor()));
            }
            $entidad->setImportance (1);
            $entidad->setCreatedAt  (new \DateTime());
            $entidad->setModifiedAt (new \DateTime());
            $entidad->setTitle      ('Test n.'.$i);
            $manager->persist($entidad);
            
            $this->addReference($entidad->getTitle(), $entidad);
            
        }
        $manager->flush();
    }
}

?>