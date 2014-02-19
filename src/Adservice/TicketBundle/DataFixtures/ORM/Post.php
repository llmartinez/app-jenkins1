<?php
namespace Adservice\TicketBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\TicketBundle\Entity\Post;
use Adservice\UtilBundle\DataFixtures\ORM\Data as Data;

class Posts extends AbstractFixture implements OrderedFixtureInterface {
    
    public function getOrder(){ return 42; }
    
    public function load(ObjectManager $manager) {
        $numT = Data::getNumTickets();
        
        for($i=1;$i<=$numT;$i++)
        {
            $numP = rand(1, Data::getNumPosts());
            for($j=1;$j<=$numP;$j++)
            {   
                $entidad = new Post();
                $entidad->setTicket     ($this->getReference(Data::getTicketTitle($i)));
                $entidad->setOwner      ($this->getReference(Data::getPostOwner($entidad)));
                $entidad->setModifiedBy ($this->getReference($entidad->getTicket()->getAssignedTo()->getUserName()));
                $entidad->setCreatedAt  (new \DateTime());
                $entidad->setModifiedAt (new \DateTime());
                $entidad->setMessage    (Data::getPostMessage($i,$j));
                $manager->persist($entidad);

                $this->addReference($entidad->getMessage(), $entidad);
            }
        }
        $manager->flush();
    }
    
}

?>