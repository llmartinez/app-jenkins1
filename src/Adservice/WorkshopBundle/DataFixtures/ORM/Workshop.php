<?php
namespace Adservice\CarBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\WorkshopBundle\Entity\Workshop;
use Adservice\UtilBundle\DataFixtures\ORM\Data as Data;

class Workshops extends AbstractFixture implements OrderedFixtureInterface {

    public function getOrder(){ return 14; }

    public function load(ObjectManager $manager) {

        $num = Data::getNumWorkshops();

        for($i=1;$i<=$num;$i++)
        {
            $entidad = new Workshop();
            $entidad->setName('workshop'.$i);
            $entidad->setContactName('name'.$i);
            $entidad->setContactSurname('surname'.$i);
            $entidad->setPhoneNumber1(Data::getPhone());
            $entidad->setMovileNumber1(Data::getPhone());
            $entidad->setEmail1('workshop'.$i.'@workshop.es');
            $entidad->setActive('1');
            $entidad->setUpdateAt(new \DateTime());
            $entidad->setLowdateAt(new \DateTime());
            $entidad->setCreatedAt(new \DateTime());
            $entidad->setModifiedAt(new \DateTime());
            $entidad->setCountry($this->getReference(Data::getCountries()));
            $entidad->setRegion($this->getReference(Data::getRegions()));
            $entidad->setProvince($this->getReference(Data::getProvinces()));
            $entidad->setCreatedBy($this->getReference('superadmin'));
            $entidad->setModifiedBy($this->getReference('superadmin'));
            $entidad->setPartner($this->getReference(Data::getPartner()));
            $entidad->setTypology($this->getReference(Data::getTypologies()));
            $manager->persist($entidad);

            $this->addReference($entidad->getName(), $entidad);
        }
        /*TODO
         * Cuando exista el rol superadmin esto sera innecesario, ya que verá todos los usuarios y no solo los de su partner
         */
        $w1=$this->getReference('workshop1');
        $w1->setPartner($this->getReference('partner1'));
        $manager->persist($w1);
        $manager->flush();
    }
}

?>
