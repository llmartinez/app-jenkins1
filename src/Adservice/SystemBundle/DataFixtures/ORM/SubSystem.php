<?php
namespace Adservice\SystemBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\SystemBundle\Entity\Subsystem;

class SubSystems extends AbstractFixture implements OrderedFixtureInterface {
    
    public function getOrder(){ return 20; }
    
    public function load(ObjectManager $manager) {
        $subsystems = array(
            array('name' => 'VARIOS CARROCERIA'         , 'system' => 'CARROCERÍA'                  ),
            array('name' => 'TECHO SOLAR'               , 'system' => 'CARROCERÍA'                  ),
            array('name' => 'CLIMATIZACION'             , 'system' => 'CONFORT'                     ),
            array('name' => 'ELEVALUNAS'                , 'system' => 'CONFORT'                     ),
            array('name' => 'GESTION ELECTRONICA'       , 'system' => 'DIRECCION'                   ),
            array('name' => 'MECANICA DE LA DIRECCION'  , 'system' => 'DIRECCION'                   ),
            array('name' => 'ILUMINACION'               , 'system' => 'ELECTRICIDAD'                ),
            array('name' => 'INSTALACION'               , 'system' => 'ELECTRICIDAD'                ),
            array('name' => 'MECANICA DE FRENOS'        , 'system' => 'FRENOS'                      ),
            array('name' => 'GEST. ELECTRONICA'         , 'system' => 'FRENOS'                      ),
            array('name' => 'ALIMENT. / INYECCION'      , 'system' => 'MOTOR DIESEL'                ),
            array('name' => 'ELECTRIC / ELECTRONICA'    , 'system' => 'MOTOR DIESEL'                ),
            array('name' => 'REFRIGERACION'             , 'system' => 'MOTOR GASOLINA'              ),
            array('name' => 'MECANICA'                  , 'system' => 'MOTOR GASOLINA'              ),
            array('name' => 'PROG. DE UNIDADES'         , 'system' => 'PROGRAMACION Y CODIFICACION' ),
            array('name' => 'RESET DE SERVICIO'         , 'system' => 'PROGRAMACION Y CODIFICACION' ),
            array('name' => 'AIRBAG'                    , 'system' => 'SEGURIDAD'                   ),
            array('name' => 'CINTURONES'                , 'system' => 'SEGURIDAD'                   ),
            array('name' => 'GESTI. ELECTRONICA'        , 'system' => 'SUSPENSION'                  ),
            array('name' => 'MECANICA DE SUSPENSION'    , 'system' => 'SUSPENSION'                  ),
            array('name' => 'CAJA DE CAMBIOS'           , 'system' => 'TRANSMISION'                 ),
            array('name' => 'EMBRAGUE'                  , 'system' => 'TRANSMISION'                 ),
            array('name' => 'VARIOS '                    , 'system' => 'VARIOS'                      )
        );
        foreach ($subsystems as $subsystem) {
            $entidad = new Subsystem();
            $entidad->setName($subsystem['name']);
            $entidad->setSystem($this->getReference($subsystem['system']));
            $manager->persist($entidad);
            
            $this->addReference($entidad->getName(), $entidad);
            
        }
        $manager->flush();
    }
}

?>