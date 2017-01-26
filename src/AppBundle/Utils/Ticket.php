<?php
namespace AppBundle\Utils;
 
class Ticket
{
    public static $importances = array('information', 'advanced_diagnostics');

    public static $states = array('1' => 'open', '2' => 'closed', '3' => 'pending', '4' => 'answered', '5' => 'inactive');

    public static $systems = array('1' => 'CARROCERÍA',
                                   '2' => 'CONFORT',
                                   '3' => 'DIRECCION',
                                   '4' => 'ELECTRICIDAD',
                                   '5' => 'FRENOS',
                                   '6' => 'MOTOR DIESEL',
                                   '7' => 'MOTOR GASOLINA',
                                   '8' => 'PROGRAMACION Y CODIFICACION',
                                   '9' => 'SEGURIDAD',
                                   '10' => 'SUSPENSION',
                                   '11' => 'TRANSMISION',
                                   '12' => 'VARIOS');
 
    public static $subsystems = array(
                            '1' => array(
                                          '10' => 'AYUDA AL APARCAMIENTO',
                                          '17' => 'COTAS DE CARROCERIA',
                                          '20' => 'ELEMENTOS EXTERIORES',
                                          '21' => 'ELEMENTOS INTERIORES',
                                          '32' => 'LUNAS',
                                          '48' => 'TECHO SOLAR',
                                          '51' => 'VARIOS CARROCERIA',
                                        ),
                            '2' => array(
                                          '7'  => 'ASIENTOS CALEFACTADOS',
                                          '8'  => 'ASIENTOS MEMORIZADOS',
                                          '9'  => 'AUDIO',
                                          '12' => 'CAPOTA',
                                          '13' => 'CIERRE CENTRALIZADO',
                                          '15' => 'CLIMATIZACION',
                                          '22' => 'ELEVALUNAS',
                                          '31' => 'LIMPIA / LAVAPARABRISAS',
                                          '39' => 'NAVEGACION',
                                        ),
                            '3' => array(
                                          '26' => 'GESTION ELECTRONICA',
                                          '35' => 'MECANICA DE LA DIRECCION',
                                        ),
                            '4' => array(
                                          '27' => 'ILUMINACION',
                                          '28' => 'INSTALACION',
                                          '29' => 'INSTRUMENTACION',
                                          '38' => 'MULTIPLEXADO',
                                          '46' => 'SISTEMA DE ARRANQUE',
                                          '47' => 'SISTEMA DE CARGA',
                                        ),
                            '5' => array(
                                          '24' => 'GESTION ELECTRONICA',
                                          '34' => 'MECANICA DE FRENOS',
                                        ),
                            '6' => array(
                                          '3'  => 'ALIMENT. / INYECCION',
                                          '5'  => 'ANTICONTAMINACION',
                                          '18' => 'ELECTRIC / ELECTRONICA',
                                          '37' => 'MECANICA DIESEL',
                                          '42' => 'REFRIGERACION DIESEL',
                                        ),
                            '7' => array(
                                          '4'  => 'ALIMENTACION / INYECCION',
                                          '6'  => 'ANTICONTAMINACION',
                                          '19' => 'ELECTRICIDAD / ELECTRONICA',
                                          '33' => 'MECANICA',
                                          '41' => 'REFRIGERACION',
                                        ),
                            '8' => array(
                                          '16' => 'CONTROL DE PRESION DE RUEDAS',
                                          '40' => 'PROG. DE UNIDADES',
                                          '43' => 'RESET DE SERVICIO',
                                          '45' => 'SINCR. MANDOS DISTANCIA',
                                        ),
                            '9' => array(
                                          '1'  => 'AIRBAG',
                                          '2'  => 'ALARMAS',
                                          '14' => 'CINTURONES',
                                          '49' => 'TRANSPONDER',
                                        ),
                            '10' => array(
                                          '25' => 'GESTI. ELECTRONICA',
                                          '36' => 'MECANICA DE SUSPENSION',
                                        ),
                            '11' => array(
                                          '11' => 'CAJA DE CAMBIOS',
                                          '23' => 'EMBRAGUE',
                                          '30' => 'JUNTAS HOMOCINETICAS',
                                          '44' => 'RESTO DE LA TRANSMISION',
                                        ),
                            '12' => array(
                                          '50' => 'VARIOS',
                                        )
                            );

    public static $subsystemsKey = array(
                                          '10' => '1',
                                          '17' => '1',
                                          '20' => '1',
                                          '21' => '1',
                                          '32' => '1',
                                          '48' => '1',
                                          '51' => '1',

                                          '7'  => '2',
                                          '8'  => '2',
                                          '9'  => '2',
                                          '12' => '2',
                                          '13' => '2',
                                          '15' => '2',
                                          '22' => '2',
                                          '31' => '2',
                                          '39' => '2',

                                          '26' => '3',
                                          '35' => '3',

                                          '27' => '4',
                                          '28' => '4',
                                          '29' => '4',
                                          '38' => '4',
                                          '46' => '4',
                                          '47' => '4',

                                          '24' => '5',
                                          '34' => '5',

                                          '3'  => '6',
                                          '5'  => '6',
                                          '18' => '6',
                                          '37' => '6',
                                          '42' => '6',

                                          '4'  => '7',
                                          '6'  => '7',
                                          '19' => '7',
                                          '33' => '7',
                                          '41' => '7',

                                          '16' => '8',
                                          '40' => '8',
                                          '43' => '8',
                                          '45' => '8',

                                          '1'  => '9',
                                          '2'  => '9',
                                          '14' => '9',
                                          '49' => '9',

                                          '25' => '10',
                                          '36' => '10',

                                          '11' => '11',
                                          '23' => '11',
                                          '30' => '11',
                                          '44' => '11',

                                          '50' => '12',
                            );

    public static function getImportances() {
        return self::$importances;
    }

    public static function getStates() {
        return self::$states;
    }

    public static function getSystems() {
        return self::$systems;
    }

    public static function getSubsystems() {
        return self::$subsystems;
    }

    /** Obtiene el id de Sistema a partir de un id de Subsistema */
    public static function getSubsystemKey($id_ss)
    {
        return self::$subsystemsKey[$id_ss];
    }
}