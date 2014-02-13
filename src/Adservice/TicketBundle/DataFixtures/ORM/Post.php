<?php
namespace Adservice\TicketBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\TicketBundle\Entity\Post;

class Posts extends AbstractFixture implements OrderedFixtureInterface {
    
    public function getOrder(){ return 18; }
    
    public function load(ObjectManager $manager) {
        $posts = array(
            array(  'ticket'      => 'Fallo al frenar'          ,
                    'owner'       => 'user1'                    ,
                    'modified_by' => 'user1'                    ,
                    'created_at'  => new \DateTime()            ,
                    'modified_at' => new \DateTime()            ,
                    'message'     => 'Falla algo en las ruedas' ,
                 ),
            array(  'ticket'      => 'Fallo al frenar'          ,
                    'owner'       => 'assessor1'                ,
                    'modified_by' => 'assessor1'                ,
                    'created_at'  => new \DateTime()            ,
                    'modified_at' => new \DateTime()            ,
                    'message'     => 'Enciende el testigo del ABS en la instrumentación. Si tienes que hacer una frenada de emergencia, es el ABS. Sustituye la pieza.' ,
                 ),
            array(  'ticket'      => 'Fallo al frenar'          ,
                    'owner'       => 'user1'                    ,
                    'modified_by' => 'user1'                    ,
                    'created_at'  => new \DateTime()            ,
                    'modified_at' => new \DateTime()            ,
                    'message'     => 'Correcto! era el ABS.'    ,
                 ),
            array(  'ticket'      => 'Fugas de refrigeración'   ,
                    'owner'       => 'user1'                    ,
                    'modified_by' => 'user1'                    ,
                    'created_at'  => new \DateTime()            ,
                    'modified_at' => new \DateTime()            ,
                    'message'     => 'Se producen fugas por la que se escapa el agua o líquido refrigerante.'    ,
                 ),
        );
        foreach ($posts as $post) {
            $entidad = new Post();
            $entidad->setTicket     ($this->getReference($post['ticket']));
            $entidad->setOwner      ($this->getReference($post['owner']));
            $entidad->setModifiedBy ($this->getReference($post['modified_by']));
            $entidad->setCreatedAt  ($post['created_at']);
            $entidad->setModifiedAt ($post['modified_at']);
            $entidad->setMessage    ($post['message']);
            $manager->persist($entidad);
            
            $this->addReference($entidad->getMessage(), $entidad);
            
        }
        $manager->flush();
    }
}

?>