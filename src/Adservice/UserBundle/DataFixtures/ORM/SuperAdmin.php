<?php
namespace Adservice\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\UserBundle\Entity\User;


class SuperAdmin extends AbstractFixture implements OrderedFixtureInterface {
    
    public function getOrder(){ return 5; }
    
    public function load(ObjectManager $manager) { 
        
        $type = 'admin';
        $pass = 'mcobJtS8ExG2JAUrEg3VzeLlgyxyyviEr5/uO+vel5stbf3TeHTJxZoHUZqfVHOqON1QNKcm5PXemcz9rW4BZg==';
        $salt = '79bc6981377363689c90b9c7d6962da9';
        $entidad = new User();
        
            $entidad->setUsername   ($type);
            $entidad->setPassword   ($pass);
            $entidad->setSalt       ($salt);
            $entidad->setName       ('super');
            $entidad->setSurname    ($type);
            $entidad->setEmail1     ('test0@'.$type.'.es');
            $entidad->setDni        ('00000000T');
            $entidad->setActive     ('1');
            $entidad->setRegion     ($this->getReference('Catalunya'));
            $entidad->setProvince   ($this->getReference('Barcelona'));
            $entidad->setCreatedAt  (new \DateTime());
            $entidad->setModifiedAt (new \DateTime());
            $entidad->setCountry    ($this->getReference('Spain'));
            $entidad->setLanguage   ($this->getReference('es_ES'));
            $entidad->addRole       ($this->getReference('ROLE_ADMIN'));
            
            $manager->persist($entidad);
            $this->addReference('superadmin', $entidad);
            $manager->flush();
    }
}
?>