<?php
namespace Adservice\TicketBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\TicketBundle\Entity\Incidence;

class Incidences extends AbstractFixture implements OrderedFixtureInterface {
    
    public function getOrder(){ return 21; }
    
    public function load(ObjectManager $manager) {
        $incidences = array(
            array(  'ticket'      => 'Fallo al frenar'  ,
                    'status'      => 'closed'           , 
                    'owner'       => 'assessor1'        ,    
                    'workshop'    => 'workshop1'    ,
                    'modified_by' => 'assessor1'        ,
                    'importance'  => '1'                ,
                    'description' => 'Falla algo en las ruedas, se decidió probar el testigo del ABS en la instrumentación. '                ,
                    'solution'    => 'Cambiar el ABS'   ,
                    'created_at'  => new \DateTime()    ,
                    'modified_at' => new \DateTime()    ,
                 ),
        );
        foreach ($incidences as $incidence) {
            $entidad = new Incidence();
            $entidad->setTicket     ($this->getReference($incidence['ticket']));
            $entidad->setStatus     ($this->getReference($incidence['status']));
            $entidad->setOwner      ($this->getReference($incidence['owner']));
            $entidad->setWorkshop   ($this->getReference($incidence['workshop']));
            $entidad->setModifiedBy ($this->getReference($incidence['modified_by']));
            $entidad->setImportance ($incidence['importance']);
            $entidad->setDescription($incidence['description']);
            $entidad->setSolution   ($incidence['solution']);
            $entidad->setCreatedAt  ($incidence['created_at']);
            $entidad->setModifiedAt ($incidence['modified_at']);
            $manager->persist($entidad);
            
            $this->addReference($entidad->getSolution(), $entidad);
            
        }
        $manager->flush();
    }
}

?>