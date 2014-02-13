<?php
namespace Adservice\SystemBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\SystemBundle\Entity\System;

class Systems extends AbstractFixture implements OrderedFixtureInterface {
    
    public function getOrder(){ return 19; }
    
    public function load(ObjectManager $manager) {
        $systems = array(
            array('name' => 'CARROCERÍA'                    ),
            array('name' => 'CONFORT'                       ),
            array('name' => 'DIRECCION'                     ),
            array('name' => 'ELECTRICIDAD'                  ),
            array('name' => 'FRENOS'                        ),
            array('name' => 'MOTOR DIESEL'                  ),
            array('name' => 'MOTOR GASOLINA'                ),
            array('name' => 'PROGRAMACION Y CODIFICACION'   ),
            array('name' => 'SEGURIDAD'                     ),
            array('name' => 'SUSPENSION'                    ),
            array('name' => 'TRANSMISION'                   ),
            array('name' => 'VARIOS'                        )
        );
        foreach ($systems as $system) {
            $entidad = new System();
            $entidad->setName($system['name']);
            $manager->persist($entidad);
            
            $this->addReference($entidad->getName(), $entidad);
            
        }
        $manager->flush();
    }
}

?>