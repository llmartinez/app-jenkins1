<?php
namespace Adservice\UtilBundle\DataFixtures\ORM\PROD;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\UtilBundle\Entity\Region;

class Regions extends AbstractFixture implements OrderedFixtureInterface {

    public function getOrder(){ return 3; }

    public function load(ObjectManager $manager) {

        $regions = array(
            array('region' => 'Andalucía'           , 'country' => $this->getReference('Spain')),
            array('region' => 'Aragón'              , 'country' => $this->getReference('Spain')),
            array('region' => 'Asturias'            , 'country' => $this->getReference('Spain')),
            array('region' => 'Illes Balears'       , 'country' => $this->getReference('Spain')),
            array('region' => 'Canarias'            , 'country' => $this->getReference('Spain')),
            array('region' => 'Cantabria'           , 'country' => $this->getReference('Spain')),
            array('region' => 'Castilla y León'     , 'country' => $this->getReference('Spain')),
            array('region' => 'Castilla - La Mancha', 'country' => $this->getReference('Spain')),
            array('region' => 'Catalunya'           , 'country' => $this->getReference('Spain')),
            array('region' => 'Comunitat Valenciana', 'country' => $this->getReference('Spain')),
            array('region' => 'Extremadura'         , 'country' => $this->getReference('Spain')),
            array('region' => 'Galicia'             , 'country' => $this->getReference('Spain')),
            array('region' => 'Madrid'              , 'country' => $this->getReference('Spain')),
            array('region' => 'Murcia'              , 'country' => $this->getReference('Spain')),
            array('region' => 'Navarra'             , 'country' => $this->getReference('Spain')),
            array('region' => 'País Vasco'          , 'country' => $this->getReference('Spain')),
            array('region' => 'La Rioja'            , 'country' => $this->getReference('Spain')),
            array('region' => 'Ceuta'               , 'country' => $this->getReference('Spain')),
            array('region' => 'Melilla'             , 'country' => $this->getReference('Spain')),

            array('region' => 'Borgoña'             , 'country' => $this->getReference('France')),
            array('region' => 'Córcega'             , 'country' => $this->getReference('France')),
            array('region' => 'Isla de Francia'     , 'country' => $this->getReference('France')),
        );
        foreach ($regions as $region) {
            $entidad = new Region();
            $entidad->setRegion ($region['region']);
            $entidad->setCountry($region['country']);
            $manager->persist($entidad);

            $this->addReference($entidad->getRegion(), $entidad);
        }
        $manager->flush();
    }
}

?>