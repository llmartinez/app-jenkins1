<?php
namespace Adservice\UtilBundle\DataFixtures\ORM\DEV;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\UserBundle\Entity\User;
use Adservice\UtilBundle\DataFixtures\ORM\Data as Data;

class Assessors extends AbstractFixture implements OrderedFixtureInterface {

    public function getOrder(){ return 21; }

    public function load(ObjectManager $manager) {
        $type = 'assessor';
        $pass = '6LQcTkIP+7AYn2UQfafPanykBGZtgXgw0JWS3RdS1x5rPy2B9nYmXbPqpwHHMtxOAHuQ18TmUq72C32bXIZT3Q==';
        $salt = '50b62d727a358f509f12c1cdec7dcd9a';
        $workshop = null;
        $partner  = null;
        $role = 'ROLE_ASSESSOR';
        $num = Data::getNumAssessors();

        $users= Users::loadUsers($manager, $this, $type, $pass, $salt, $workshop ,$partner, $role, $num);
    }
}

?>