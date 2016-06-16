<?php
namespace Adservice\UtilBundle\DataFixtures\ORM\PROD;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\UserBundle\Entity\User;
use Adservice\UtilBundle\DataFixtures\ORM\Data as Data;


class SuperAdmin extends AbstractFixture implements OrderedFixtureInterface {

    public function getOrder(){ return 11; }

    public function load(ObjectManager $manager) {

        $type = 'superadmin';
        /* password: 8d32ab2e */
        $pass = 'xIZniQZGeZWb2stbebivTJrLN47+bWwJI4CUCm4zjFbiEUnaQPx9x8gCB4LEAoaIx3p2dX+Q1V/b428UswPKxQ==';
        $salt = '297520b4fe92e51de2b96b7311777ba1';

        /* password: admin
        $pass = 'mcobJtS8ExG2JAUrEg3VzeLlgyxyyviEr5/uO+vel5stbf3TeHTJxZoHUZqfVHOqON1QNKcm5PXemcz9rW4BZg==';
        $salt = '79bc6981377363689c90b9c7d6962da9';*/

        /* password: grupeina16
        $pass = 'x4uoLJGpW3o+8BNqNj+EdW9a/sBW5Dr8qHn/NqxhWDtx5+rXyRfr3bRVuIs4cMdPo/zAstrpinia3KaMo8LUbA==';
        $salt = 'f0ca168348b28d18b048632fe3c1ac27';*/

        $entidad = new User();

            $entidad->setUsername   ($type);
            $entidad->setPassword   ($pass);
            $entidad->setSalt       ($salt);
            $entidad->setName       ('GED');
            $entidad->setSurname    ($type);
            $entidad->setActive     ('1');

            //CONTACT
            $entidad->setLanguage($this->getReference('es_ES'));
            $entidad->setCountry($this->getReference('spain'));
            $entidad->setRegion('Barcelona');
            $entidad->setCity('Badalona');
            $entidad->setAddress('Avinguda  Llenguadoc, 29 – POL. IND. BONAVISTA');
            $entidad->setPostalCode(08915);
            $entidad->setPhoneNumber1('97 267 77 70');
            $entidad->setEmail1('test@adserviceticketing.com');
            $entidad->setEmail2('test@adserviceticketing.com');
            //CREATE/MODIFY
            $entidad->setCreatedAt(new \DateTime());
            $entidad->setModifiedAt(new \DateTime());
            $entidad->addRole       ($this->getReference('ROLE_SUPER_ADMIN'));

            $manager->persist($entidad);
            $this->addReference('superadmin', $entidad);
            $manager->flush();
    }
}
?>