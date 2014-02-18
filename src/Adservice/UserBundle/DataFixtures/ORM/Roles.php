<?php
namespace Adservice\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\UserBundle\Entity\Role;

class Roles extends AbstractFixture implements OrderedFixtureInterface {
    
    public function getOrder(){ return 4; }
    
    public function load(ObjectManager $manager) {
        $roles = array(
            array('name' => 'ROLE_ADMIN'    ),
            array('name' => 'ROLE_ASSESSOR' ),
            array('name' => 'ROLE_USER'     )
        );
        foreach ($roles as $role) {
            $entidad = new Role();
            $entidad->setName($role['name']);
            $manager->persist($entidad);
            
            $this->addReference($entidad->getName(), $entidad);
            
        }
        $manager->flush();
    }
}

?>