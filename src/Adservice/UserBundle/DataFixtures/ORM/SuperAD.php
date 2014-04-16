<?php
namespace Adservice\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\UserBundle\Entity\User;
use Adservice\UtilBundle\DataFixtures\ORM\Data as Data;

class SuperAd extends AbstractFixture implements OrderedFixtureInterface {

    public function getOrder(){ return 23; }

    public function load(ObjectManager $manager) {

        $type = 'ad';
        $pass = '889qsbiJy2cFLyi4DFjCvgeGgP10lJEigN8sUBQW9NokZ1HjdZxSvnOq1+YpyAbs/zK2A3dfSgOOUbujLUaaiA==';
        $salt = '0d1ce2e99013cb3e15beb27809683af6';
        $entidad = new User();

            $entidad->setUsername   ($type);
            $entidad->setPassword   ($pass);
            $entidad->setSalt       ($salt);
            $entidad->setName       ('super');
            $entidad->setSurname    ($type);
            $entidad->setActive     ('1');

            //CONTACT
            $entidad->setCountry($this->getReference(Data::getCountries()));
            $entidad->setRegion($this->getReference(Data::getRegions($entidad->getCountry()->getCountry())));
            $entidad->setAddress('Address SA');
            $entidad->setPostalCode(Data::getPostalCode());
            $entidad->setPhoneNumber1(Data::getPhone());
            $entidad->setPhoneNumber2(Data::getPhone());
            $entidad->setMovileNumber1(Data::getPhone());
            $entidad->setPhoneNumber2(Data::getPhone());
            $entidad->setFax(Data::getPhone());
            $entidad->setEmail1('dmaya@grupeina.com');
            $entidad->setEmail2('mail@mail.com');
            //CREATE/MODIFY
            $entidad->setCreatedAt(new \DateTime());
            $entidad->setModifiedAt(new \DateTime());
            $entidad->addRole       ($this->getReference('ROLE_SUPER_AD'));

            $manager->persist($entidad);
            $this->addReference('superad', $entidad);
            $manager->flush();
    }
}

?>