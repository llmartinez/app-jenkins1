<?php
namespace Adservice\UtilBundle\DataFixtures\ORM\DEV;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\TicketBundle\Entity\Subsystem;

class SubSystems extends AbstractFixture implements OrderedFixtureInterface {

    public function getOrder(){ return 35; }

    public function load(ObjectManager $manager) {
        $subsystems = array(
            array('name' => 'AIRBAG'                    , 'system' => 'SEGURIDAD'                   ),
            array('name' => 'ALARMAS'                   , 'system' => 'CONFORT'                     ),
            array('name' => 'ALIMENT. / INYECCION'      , 'system' => 'MOTOR DIESEL'                ),
            // array('name' => 'ALIMENTACION / INYECCION'  , 'system' => 'CONFORT'                     ),
            // array('name' => 'ANTICONTAMINACION'         , 'system' => 'CONFORT'                     ),
            // array('name' => 'ASIENTOS CALEFACTADOS'     , 'system' => 'CONFORT'                     ),
            // array('name' => 'ASIENTOS MEMORIZADOS'      , 'system' => 'CONFORT'                     ),
            // array('name' => 'AUDIO'                     , 'system' => 'CONFORT'                     ),
            // array('name' => 'AYUDA AL APARCAMIENTO'     , 'system' => 'CONFORT'                     ),
            array('name' => 'CAJA DE CAMBIOS'           , 'system' => 'TRANSMISION'                 ),
            // array('name' => 'ELEVALUNAS'                , 'system' => 'CONFORT'                     ),
            // array('name' => 'ELEVALUNAS'                , 'system' => 'CONFORT'                     ),
            // array('name' => 'ELEVALUNAS'                , 'system' => 'CONFORT'                     ),
            // array('name' => 'ELEVALUNAS'                , 'system' => 'CONFORT'                     ),
            // array('name' => 'ELEVALUNAS'                , 'system' => 'CONFORT'                     ),
            // array('name' => 'ELEVALUNAS'                , 'system' => 'CONFORT'                     ),
            // array('name' => 'ELEVALUNAS'                , 'system' => 'CONFORT'                     ),
            // array('name' => 'ELEVALUNAS'                , 'system' => 'CONFORT'                     ),
            // array('name' => 'ELEVALUNAS'                , 'system' => 'CONFORT'                     ),
            // array('name' => 'ELEVALUNAS'                , 'system' => 'CONFORT'                     ),
            // array('name' => 'ELEVALUNAS'                , 'system' => 'CONFORT'                     ),
            // array('name' => 'ELEVALUNAS'                , 'system' => 'CONFORT'                     ),
            // array('name' => 'ELEVALUNAS'                , 'system' => 'CONFORT'                     ),
            // array('name' => 'ELEVALUNAS'                , 'system' => 'CONFORT'                     ),
            // array('name' => 'ELEVALUNAS'                , 'system' => 'CONFORT'                     ),
            // array('name' => 'ELEVALUNAS'                , 'system' => 'CONFORT'                     ),
            // array('name' => 'ELEVALUNAS'                , 'system' => 'CONFORT'                     ),
            // array('name' => 'ELEVALUNAS'                , 'system' => 'CONFORT'                     ),
            // array('name' => 'ELEVALUNAS'                , 'system' => 'CONFORT'                     ),
            // array('name' => 'ELEVALUNAS'                , 'system' => 'CONFORT'                     ),
            // array('name' => 'ELEVALUNAS'                , 'system' => 'CONFORT'                     ),
            array('name' => 'CINTURONES'                , 'system' => 'SEGURIDAD'                   ),
            array('name' => 'CLIMATIZACION'             , 'system' => 'CONFORT'                     ),
            array('name' => 'ELECTRIC / ELECTRONICA'    , 'system' => 'MOTOR DIESEL'                ),
            array('name' => 'ELEVALUNAS'                , 'system' => 'CONFORT'                     ),
            array('name' => 'EMBRAGUE'                  , 'system' => 'TRANSMISION'                 ),
            array('name' => 'GEST. ELECTRONICA'         , 'system' => 'FRENOS'                      ),
            array('name' => 'GESTI. ELECTRONICA'        , 'system' => 'SUSPENSION'                  ),
            array('name' => 'GESTION ELECTRONICA'       , 'system' => 'DIRECCION'                   ),
            array('name' => 'ILUMINACION'               , 'system' => 'ELECTRICIDAD'                ),
            array('name' => 'INSTALACION'               , 'system' => 'ELECTRICIDAD'                ),
            array('name' => 'MECANICA'                  , 'system' => 'MOTOR GASOLINA'              ),
            array('name' => 'MECANICA DE FRENOS'        , 'system' => 'FRENOS'                      ),
            array('name' => 'MECANICA DE LA DIRECCION'  , 'system' => 'DIRECCION'                   ),
            array('name' => 'MECANICA DE SUSPENSION'    , 'system' => 'SUSPENSION'                  ),
            array('name' => 'PROG. DE UNIDADES'         , 'system' => 'PROGRAMACION Y CODIFICACION' ),
            array('name' => 'REFRIGERACION'             , 'system' => 'MOTOR GASOLINA'              ),
            array('name' => 'RESET DE SERVICIO'         , 'system' => 'PROGRAMACION Y CODIFICACION' ),
            array('name' => 'TECHO SOLAR'               , 'system' => 'CARROCERÍA'                  ),
            array('name' => 'VARIOS '                   , 'system' => 'VARIOS'                      ),
            array('name' => 'VARIOS CARROCERIA'         , 'system' => 'CARROCERÍA'                  )
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