<?php
namespace Adservice\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\UserBundle\Entity\User;
use Adservice\UtilBundle\DataFixtures\ORM\Data as Data;


class SuperAdmin extends AbstractFixture implements OrderedFixtureInterface {

    public function getOrder(){ return 11; }

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
            $entidad->setEmail1('mail@mail.es');
            $entidad->setEmail2('mail@mail.com');
            //CREATE/MODIFY
            $entidad->setCreatedAt(new \DateTime());
            $entidad->setModifiedAt(new \DateTime());
            $entidad->addRole       ($this->getReference('ROLE_ADMIN'));

            $manager->persist($entidad);
            $this->addReference('superadmin', $entidad);
            $manager->flush();
    }
}
?>