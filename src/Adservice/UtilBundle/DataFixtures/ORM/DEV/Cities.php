<?php
namespace Adservice\UtilBundle\DataFixtures\ORM\DEV;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\UtilBundle\Entity\City;

class Cities extends AbstractFixture implements OrderedFixtureInterface {

    public function getOrder(){ return 4; }

    public function load(ObjectManager $manager) {

        $cities = array(
            array('city' => 'Badalona'   , 'region' => $this->getReference('Catalunya')),
            array('city' => 'Figueres'   , 'region' => $this->getReference('Catalunya')),
            array('city' => 'Barcelona'  , 'region' => $this->getReference('Catalunya')),
            array('city' => 'Madrid '    , 'region' => $this->getReference('Madrid')),
        );
        foreach ($cities as $city) {
            $entidad = new City();
            $entidad->setCity ($city['city' ]);
            $entidad->setRegion($city['region']);
            $manager->persist($entidad);

            $this->addReference($entidad->getCity(), $entidad);
        }
        $manager->flush();
    }
}

?>