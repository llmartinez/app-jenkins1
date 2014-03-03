<?php
namespace Adservice\CarBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\CarBundle\Entity\Version;

class Versions  extends AbstractFixture implements OrderedFixtureInterface {

    public function getOrder(){ return 32; }

    public function load(ObjectManager $manager) {
        $versions = array(
            array('name' => '1.8_TURBO'     , 'model' => 'A3'           , 'idTecDoc' => '23839' ),
            array('name' => '1.9D TDI-IB'   , 'model' => 'A3'           , 'idTecDoc' => '17398' ),
            array('name' => '1.8 TURBO'     , 'model' => 'A4'           , 'idTecDoc' => '18396' ),
            array('name' => '1.9D TDI - IB' , 'model' => 'A4'           , 'idTecDoc' => '18400' ),
            array('name' => '2.0 - E 200 D' , 'model' => '210_CLASE_E'  , 'idTecDoc' => '8557'  ),
            array('name' => '3.2 - E 320'   , 'model' => '210_CLASE_E'  , 'idTecDoc' => '7834'  ),
            array('name' => '4.2 - E 420'   , 'model' => '210_CLASE_E'  , 'idTecDoc' => '5374'  ),
            array('name' => '1.2 12V'       , 'model' => 'CORDOBA - 6L2', 'idTecDoc' => '20054' ),
            array('name' => '1.4 16V'       , 'model' => 'CORDOBA - 6L2', 'idTecDoc' => '17155' ),
            array('name' => '1.9D SDI'      , 'model' => 'CORDOBA - 6L2', 'idTecDoc' => '17154' ),

        );
        foreach ($versions as $version) {
            $entidad = new Version();
            $entidad->setName    ($version['name']);
            $entidad->setModel   ($this->getReference($version['model']));
            $entidad->setIdTecDoc($version['idTecDoc']);
            $manager->persist($entidad);

            $this->addReference($entidad->getName(), $entidad);
        }
        $manager->flush();
    }
}

?>
