<?php
namespace Adservice\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\UserBundle\Entity\User;
use Adservice\UtilBundle\DataFixtures\ORM\Data as Data;

class Users extends AbstractFixture implements OrderedFixtureInterface {

    public function getOrder(){ return 22; }

    public function load(ObjectManager $manager) {
        $type = 'user';
        $pass = 'QeDeEveLRGfrw94I1iCOzs37BWE+xnmQFjkT5EeUTwDjXWRIjQsSYghkc2kAefhuFMTPvnIaplq7xbPOesN22Q==';
        $salt = '84a2646fadd65616d73199e9f1fae1e1';
        $workshop = 'yes';
        $role = 'ROLE_USER';
        $num = Data::getNumUsers();

        $users= $this->loadUsers($manager, $this, $type, $pass, $salt, $workshop, $role, $num);
    }

    public static function loadUsers($manager, $_this, $type, $pass, $salt, $workshop=null, $role, $num)
    {
        for($i=1;$i<=$num;$i++)
        {
            $entidad = new User();
            $entidad->setUsername   ($type.$i);
            $entidad->setPassword   ($pass);
            $entidad->setSalt       ($salt);
            $entidad->setName       (Data::getName());
            $entidad->setSurname    (Data::getSurname());
            $entidad->setEmail1     ('test'.$i.'@'.$type.'.es');
            $entidad->setDni        (Data::getDNI());
            $entidad->setActive     ('1');
            $entidad->setRegion     ($_this->getReference(Data::getRegions()));
            $entidad->setProvince   ($_this->getReference(Data::getProvinces()));
            $entidad->setCreatedAt  (new \DateTime());
            $entidad->setModifiedAt (new \DateTime());
            $entidad->setCountry    ($_this->getReference(Data::getCountries()));
            $entidad->setLanguage   ($_this->getReference(Data::getLanguages()));
            if($workshop != null ) {
                $entidad->setWorkshop($_this->getReference(Data::getWorkshop()));
            }
            $entidad->addRole       ($_this->getReference($role));

            $manager->persist($entidad);
            $_this->addReference($entidad->getUsername(), $entidad);
        }
        $manager->flush();
    }
}

?>