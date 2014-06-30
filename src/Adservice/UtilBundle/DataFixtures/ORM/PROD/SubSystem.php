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
            array('name' => 'AIRBAG'                        , 'system' => 'SEGURIDAD'                   ),
            array('name' => 'ALARMAS'                       , 'system' => 'SEGURIDAD'                   ),
            array('name' => 'ALIMENT. / INYECCION'          , 'system' => 'MOTOR DIESEL'                ),
            array('name' => 'ALIMENTACION / INYECCION'      , 'system' => 'MOTOR GASOLINA'              ),
            array('name' => 'ANTICONTAMINACION'             , 'system' => 'MOTOR DIESEL'                ),
            array('name' => 'ANTICONTAMINACION '            , 'system' => 'MOTOR GASOLINA'              ),
            array('name' => 'ASIENTOS CALEFACTADOS'         , 'system' => 'CONFORT'                     ),
            array('name' => 'ASIENTOS MEMORIZADOS'          , 'system' => 'CONFORT'                     ),
            array('name' => 'AUDIO'                         , 'system' => 'CONFORT'                     ),
            array('name' => 'AYUDA AL APARCAMIENTO'         , 'system' => 'CARROCERÍA'                  ),
            array('name' => 'CAJA DE CAMBIOS'               , 'system' => 'TRANSMISION'                 ),
            array('name' => 'CAPOTA'                        , 'system' => 'CONFORT'                     ),
            array('name' => 'CIERRE CENTRALIZADO'           , 'system' => 'CONFORT'                     ),
            array('name' => 'CINTURONES'                    , 'system' => 'SEGURIDAD'                   ),
            array('name' => 'CLIMATIZACION'                 , 'system' => 'CONFORT'                     ),
            array('name' => 'CONTROL DE PRESION DE RUEDAS'  , 'system' => 'PROGRAMACION Y CODIFICACION' ),
            array('name' => 'COTAS DE CARROCERIA'           , 'system' => 'CARROCERÍA'                  ),
            array('name' => 'ELECTRIC / ELECTRONICA'        , 'system' => 'MOTOR DIESEL'                ),
            array('name' => 'ELECTRICIDAD / ELECTRONICA'    , 'system' => 'MOTOR GASOLINA'              ),
            array('name' => 'ELEMENTOS EXTERIORES'          , 'system' => 'CARROCERÍA'                  ),
            array('name' => 'ELEMENTOS INTERIORES'          , 'system' => 'CARROCERÍA'                  ),
            array('name' => 'ELEVALUNAS'                    , 'system' => 'CONFORT'                     ),
            array('name' => 'EMBRAGUE'                      , 'system' => 'TRANSMISION'                 ),
            array('name' => 'GEST. ELECTRONICA'             , 'system' => 'FRENOS'                      ),
            array('name' => 'GESTI. ELECTRONICA'            , 'system' => 'SUSPENSION'                  ),
            array('name' => 'GESTION ELECTRONICA'           , 'system' => 'DIRECCION'                   ),
            array('name' => 'ILUMINACION'                   , 'system' => 'ELECTRICIDAD'                ),
            array('name' => 'INSTALACION'                   , 'system' => 'ELECTRICIDAD'                ),
            array('name' => 'INSTRUMENTACION'               , 'system' => 'ELECTRICIDAD'                ),
            array('name' => 'JUNTAS HOMOCINETICAS'          , 'system' => 'TRANSMISION'                 ),
            array('name' => 'LIMPIA / LAVAPARABRISAS'       , 'system' => 'CONFORT'                     ),
            array('name' => 'LUNAS'                         , 'system' => 'CARROCERÍA'                  ),
            array('name' => 'MECANICA'                      , 'system' => 'MOTOR GASOLINA'              ),
            array('name' => 'MECANICA DE FRENOS'            , 'system' => 'FRENOS'                      ),
            array('name' => 'MECANICA DE LA DIRECCION'      , 'system' => 'DIRECCION'                   ),
            array('name' => 'MECANICA DE SUSPENSION'        , 'system' => 'SUSPENSION'                  ),
            array('name' => 'MECANICA DIESEL'               , 'system' => 'MOTOR DIESEL'                ),
            array('name' => 'MULTIPLEXADO'                  , 'system' => 'ELECTRICIDAD'                ),
            array('name' => 'NAVEGACION'                    , 'system' => 'CONFORT'                     ),
            array('name' => 'PROG. DE UNIDADES'             , 'system' => 'PROGRAMACION Y CODIFICACION' ),
            array('name' => 'REFRIGERACION'                 , 'system' => 'MOTOR GASOLINA'              ),
            array('name' => 'REFRIGERACION DIESEL'          , 'system' => 'MOTOR DIESEL'                ),
            array('name' => 'RESET DE SERVICIO'             , 'system' => 'PROGRAMACION Y CODIFICACION' ),
            array('name' => 'RESTO DE LA TRANSMISION'       , 'system' => 'TRANSMISION'                 ),
            array('name' => 'SINCR. MANDOS DISTANCIA'       , 'system' => 'PROGRAMACION Y CODIFICACION' ),
            array('name' => 'SISTEMA DE ARRANQUE'           , 'system' => 'ELECTRICIDAD'                ),
            array('name' => 'SISTEMA DE CARGA'              , 'system' => 'ELECTRICIDAD'                ),
            array('name' => 'TECHO SOLAR'                   , 'system' => 'CARROCERÍA'                  ),
            array('name' => 'TRANSPONDER'                   , 'system' => 'SEGURIDAD'                   ),
            array('name' => 'VARIOS '                       , 'system' => 'VARIOS'                      ),
            array('name' => 'VARIOS CARROCERIA'             , 'system' => 'CARROCERÍA'                  )
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