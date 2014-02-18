<?php
namespace Adservice\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\UserBundle\Entity\User;


class Admins extends AbstractFixture implements OrderedFixtureInterface {
    
    public function getOrder(){ return 7; }
    
    public function load(ObjectManager $manager) {
        $type = 'admin';
        $pass = 'mcobJtS8ExG2JAUrEg3VzeLlgyxyyviEr5/uO+vel5stbf3TeHTJxZoHUZqfVHOqON1QNKcm5PXemcz9rW4BZg==';
        $salt = '79bc6981377363689c90b9c7d6962da9';
        $partner = 'partner';
        $workshop = null;
        $role = 'ROLE_ADMIN';
        $num = 3;
        
        $users= Users::loadUsers($manager, $this, $type, $pass, $salt, $partner, $workshop, $role, $num);
    }
}

?>