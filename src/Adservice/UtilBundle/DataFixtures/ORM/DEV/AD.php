<?php
namespace Adservice\UtilBundle\DataFixtures\ORM\DEV;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\UtilBundle\DataFixtures\ORM\Data as Data;

class Ads extends AbstractFixture implements OrderedFixtureInterface {

    public function getOrder(){ return 24; }

    public function load(ObjectManager $manager) {
        $type = 'ad';
        $pass = '889qsbiJy2cFLyi4DFjCvgeGgP10lJEigN8sUBQW9NokZ1HjdZxSvnOq1+YpyAbs/zK2A3dfSgOOUbujLUaaiA==';
        $salt = '0d1ce2e99013cb3e15beb27809683af6';
        $workshop = null;
        $partner = 'partner';
        $role = 'ROLE_AD';
        $num = Data::getNumAds();

        $users= Users::loadUsers($manager, $this, $type, $pass, $salt, $workshop, $partner, $role, $num);
    }
}

?>