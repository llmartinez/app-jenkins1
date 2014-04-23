<?php
namespace Adservice\UtilBundle\DataFixtures\ORM\DEV;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\CarBundle\Entity\Model;

class Models  extends AbstractFixture implements OrderedFixtureInterface {

    public function getOrder(){ return 31; }

    public function load(ObjectManager $manager) {
        $models = array(
            array('name' => 'A3'             , 'brand' => 'AUDI'          , 'idTecDoc' => '4955'  ),
            array('name' => 'A4'             , 'brand' => 'AUDI'          , 'idTecDoc' => '5376'  ),
            array('name' => '210_CLASE_E'    , 'brand' => 'MERCEDES BENZ' , 'idTecDoc' => '1314'  ),
            array('name' => 'CORDOBA - 6L2'  , 'brand' => 'SEAT'          , 'idTecDoc' => '4961'  ),
        );
        foreach ($models as $model) {
            $entidad = new Model();
            $entidad->setName    ($model['name']);
            $entidad->setBrand   ($this->getReference($model['brand']));
            $entidad->setIdTecDoc($model['idTecDoc']);
            $manager->persist($entidad);

            $this->addReference($entidad->getName(), $entidad);
        }
        $manager->flush();
    }
}

?>
