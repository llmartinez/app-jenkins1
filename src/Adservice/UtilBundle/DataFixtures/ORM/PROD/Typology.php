<?php
namespace Adservice\UtilBundle\DataFixtures\ORM\PROD;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\WorkshopBundle\Entity\Typology;

class Typologies extends AbstractFixture implements OrderedFixtureInterface {

    public function getOrder(){ return 14; }

    public function load(ObjectManager $manager) {
        $typologies = array(
            array( 'name' => 'Agent de Marque'  , 'country' => 'france'),
            array( 'name' => 'Autoprimo'        , 'country' => 'france'),
            array( 'name' => 'Autoservice AD'   , 'country' => 'france'),
            array( 'name' => 'Carrosserie AD'   , 'country' => 'france'),
            array( 'name' => 'Distributeur'     , 'country' => 'france'),
            array( 'name' => 'Garage AD'        , 'country' => 'france'),
            array( 'name' => 'Garage AD Expert' , 'country' => 'france'),
            array( 'name' => 'MRA'              , 'country' => 'france'),
            array( 'name' => 'Staff Auto'       , 'country' => 'france'),
            array( 'name' => 'Autres'           , 'country' => 'france'),

            array( 'name' => 'Rodi'                      , 'country' => 'spain'),
            array( 'name' => 'FirstStop Centro Asosciado', 'country' => 'spain'),
            array( 'name' => 'FirstStop Centro Propio'   , 'country' => 'spain'),
            array( 'name' => 'Auto Equip'                , 'country' => 'spain'),
            array( 'name' => 'Otros'                     , 'country' => 'spain'),
        );
        foreach ($typologies as $typology) {
            $entidad = new Typology();
            $entidad->setName($typology['name']);
            $entidad->setCountry($this->getReference($typology['country']));
            $entidad->setActive(1);
            $manager->persist($entidad);

            $this->addReference($entidad->getName(), $entidad);
        }
        $manager->flush();
    }
}

?>
