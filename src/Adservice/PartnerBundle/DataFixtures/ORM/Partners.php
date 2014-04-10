<?php
namespace Adservice\PartnerBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\PartnerBundle\Entity\Partner;
use Adservice\UtilBundle\DataFixtures\ORM\Data as Data;

class Partners extends AbstractFixture implements OrderedFixtureInterface {

    public function getOrder(){ return 12; }

    public function load(ObjectManager $manager) {
        $num = Data::getNumPartners();

        for($i=1;$i<=$num;$i++)
        {
            $entidad = new Partner();
            $entidad->setName('partner'.$i);
            $entidad->setCodePartner($i);
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
