<?php
namespace Adservice\UtilBundle\DataFixtures\ORM\DEV;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\UtilBundle\DataFixtures\ORM\Data as Data;

class Admins extends AbstractFixture implements OrderedFixtureInterface {

    public function getOrder(){ return 20; }

    public function load(ObjectManager $manager) {
        $type = 'admin';
        $pass = 'mcobJtS8ExG2JAUrEg3VzeLlgyxyyviEr5/uO+vel5stbf3TeHTJxZoHUZqfVHOqON1QNKcm5PXemcz9rW4BZg==';
        $salt = '79bc6981377363689c90b9c7d6962da9';
        $workshop = null;
        $partner  = null;
        $role = 'ROLE_ADMIN';
        $num = Data::getNumAdmins();

        $users= Users::loadUsers($manager, $this, $type, $pass, $salt, $workshop ,$partner, $role, $num);
    }
}

?>