<?php
namespace Adservice\TicketBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\TicketBundle\Entity\Ticket;

class Tickets extends AbstractFixture implements OrderedFixtureInterface {
    
    public function getOrder(){ return 17; }
    
    public function load(ObjectManager $manager) {
        $tickets = array(
            array(  'status'      => 'closed'           ,     
                    'workshop'    => 'workshop1'        ,
                    'car'         => 'T-1111-TT'        ,
                    'owner'       => 'user1'            ,
                    'modified_by' => 'user1'            ,
                    'assigned_to' => 'assessor1'        ,
                    'importance'  => '1'                ,
                    'created_at'  => new \DateTime()    ,
                    'modified_at' => new \DateTime()    ,
                    'title'       => 'Fallo al frenar'  ,
                 ),
            array(  'status'      => 'open'             ,     
                    'workshop'    => 'workshop1'        ,
                    'car'         => 'T-1111-TT'        ,
                    'owner'       => 'user1'            ,
                    'modified_by' => 'user1'            ,
                    'assigned_to' => 'assessor1'        ,
                    'importance'  => '1'                ,
                    'created_at'  => new \DateTime()    ,
                    'modified_at' => new \DateTime()    ,
                    'title'       => 'Fugas de refrigeración'  ,
                 ),
        );
        foreach ($tickets as $ticket) {
            $entidad = new Ticket();
            $entidad->setStatus     ($this->getReference($ticket['status']));
            $entidad->setWorkshop   ($this->getReference($ticket['workshop']));
            $entidad->setCar        ($this->getReference($ticket['car']));
            $entidad->setOwner      ($this->getReference($ticket['owner']));
            $entidad->setModifiedBy ($this->getReference($ticket['modified_by']));
            $entidad->setAssignedTo ($this->getReference($ticket['assigned_to']));
            $entidad->setImportance ($ticket['importance']);
            $entidad->setCreatedAt  ($ticket['created_at']);
            $entidad->setModifiedAt ($ticket['modified_at']);
            $entidad->setTitle      ($ticket['title']);
            $manager->persist($entidad);
            
            $this->addReference($entidad->getTitle(), $entidad);
            
        }
        $manager->flush();
    }
}

?>