<?php
namespace Adservice\UtilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\UtilBundle\Entity\Province;

class Provinces extends AbstractFixture implements OrderedFixtureInterface {
    
    public function getOrder(){ return 4; }
    
    public function load(ObjectManager $manager) {
        $provinces = array(
            array('province' => 'Barcelona'             , 'region' => 'Catalunya'  ),
            array('province' => 'Tarragona'             , 'region' => 'Catalunya'  ),
            array('province' => 'Lleida'                , 'region' => 'Catalunya'  ),
            array('province' => 'Girona'                , 'region' => 'Catalunya'  ),
            array('province' => 'Madrid '               , 'region' => 'Madrid'     ),
            array('province' => 'Las Palmas'            , 'region' => 'Canarias'   ),
            array('province' => 'Sta. Cruz de Tenerife' , 'region' => 'Canarias'   ),
        );
        foreach ($provinces as $province) {
            $entidad = new Province();
            $entidad->setProvince($province['province']);
            $entidad->setRegion($this->getReference($province['region']));
            $manager->persist($entidad);
            
            $this->addReference($entidad->getProvince(), $entidad);
        }
        $manager->flush();
    }
}

?>