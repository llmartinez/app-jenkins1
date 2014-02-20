<?php
namespace Adservice\CarBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\CarBundle\Entity\Brand;

class Brands  extends AbstractFixture implements OrderedFixtureInterface {
    
    public function getOrder(){ return 30; }
    
    public function load(ObjectManager $manager) {
        $brands = array(
            array('name' => 'AUDI'          , 'idTecDoc' => '5'  ),
            array('name' => 'MERCEDES BENZ' , 'idTecDoc' => '74' ),
            array('name' => 'SEAT'          , 'idTecDoc' => '104'  ),

        );
        foreach ($brands as $brand) {
            $entidad = new Brand();
            $entidad->setName    ($brand['name']);
            $entidad->setIdTecDoc($brand['idTecDoc']);
            $manager->persist($entidad);
            
            $this->addReference($entidad->getName(), $entidad);
        }
        $manager->flush();
    }
}

?>
