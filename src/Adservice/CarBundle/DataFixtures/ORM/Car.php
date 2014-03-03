<?php
namespace Adservice\CarBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\CarBundle\Entity\Car;
use Adservice\UtilBundle\DataFixtures\ORM\Data as Data;

class Cars  extends AbstractFixture implements OrderedFixtureInterface {

    public function getOrder(){ return 33; }

    public function load(ObjectManager $manager)
    {
        $num = Data::getNumTickets();

        for($i=1;$i<=$num;$i++)
        {
            $entidad = new Car();
            $entidad->setVersion    ($this->getReference(Data::getVersion()));
            $entidad->setOwner      ($this->getReference(Data::getUser()));
            $entidad->setModifiedBy ($this->getReference(Data::getUser()));
            $entidad->setYear       (Data::getYear());
            $entidad->setVin        (Data::getVin());
            $entidad->setPlateNumber(Data::getPlateNumber($i));
            $entidad->setCreatedAt  (new \DateTime());
            $entidad->setModifiedAt (new \DateTime());
            $manager->persist($entidad);

            $this->addReference($entidad->getPlateNumber(), $entidad);
        }
        $manager->flush();
    }
}

?>
