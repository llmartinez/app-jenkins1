<?php
namespace Adservice\UtilBundle\DataFixtures\ORM\DEV;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\PartnerBundle\Entity\Shop;
use Adservice\UtilBundle\DataFixtures\ORM\Data as Data;

class Shops extends AbstractFixture implements OrderedFixtureInterface {

    public function getOrder(){ return 13; }

    public function load(ObjectManager $manager) {
        $num = Data::getNumShops();

        for($i=1;$i<=$num;$i++)
        {
            $entidad = new Shop();
            $entidad->setName('shop'.$i);
            $entidad->setPartner($this->getReference(Data::getPartner()));
            $entidad->setActive('1');

            //CONTACT
            $entidad->setCountry($this->getReference(Data::getCountries()));
            $entidad->setRegion($this->getReference(Data::getRegions($entidad->getCountry()->getCountry())));
            $entidad->setAddress('Address '.$i);
            $entidad->setPostalCode(Data::getPostalCode());
            $entidad->setPhoneNumber1(Data::getPhone());
            $entidad->setPhoneNumber2(Data::getPhone());
            $entidad->setMovileNumber1(Data::getPhone());
            $entidad->setPhoneNumber2(Data::getPhone());
            $entidad->setFax(Data::getPhone());
            $entidad->setEmail1('mail'.$i.'@mail.es');
            $entidad->setEmail2('mail'.$i.'@mail.com');
            //CREATE/MODIFY
            $entidad->setCreatedAt(new \DateTime());
            $entidad->setModifiedAt(new \DateTime());
            $entidad->setCreatedBy($this->getReference('superadmin'));
            $entidad->setModifiedBy($this->getReference('superadmin'));
            $manager->persist($entidad);

            $this->addReference($entidad->getName(), $entidad);
        }
        $manager->flush();
    }
}

?>
