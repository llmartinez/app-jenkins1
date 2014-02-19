<?php

namespace Adservice\UtilBundle\DataFixtures\ORM;

class Data {
    /**
     * Devuelve el numero de entidades por clase que se crearan.
     *
     * @return integer $num
     */
    public static function getNum()
    {
        // Numero de entidades por clase de la aplicacion
        return 3;
    }
    
/*********************************************************************
 * GENERADOR VALORES USUARIO
 **********************************************************************/


    /**
     * Generador aleatorio de lenguajes.
     *
     * @return string Language aleatorio generado para el usuario.
     */
    public static function getLanguages()
    {
        // Los lenguages soportados por la aplicación
        
        $languages = array('es_ES', 'en_EN', 'fr_FR');

        return $languages[array_rand($languages)];
    }
    
    /**
     * Generador aleatorio de paises.
     *
     * @return string Country aleatorio generado para el usuario.
     */
    public static function getCountries()
    {
        // Los lenguages soportados por la aplicación
        
        $countries = array('Spain', 'England', 'France');

        return $countries[array_rand($countries)];
    }
    
    /**
     * Generador aleatorio de regiones.
     *
     * @return string Region aleatorio generado para el usuario.
     */
    public static function getRegions()
    {
        // Los lenguages soportados por la aplicación
        
        $regions = array('Catalunya', 'Madrid', 'Canarias');

        return $regions[array_rand($regions)];
    }
    
    /**
     * Generador aleatorio de provincias.
     *
     * @return string Province aleatorio generado para el usuario.
     */
    public static function getProvinces()
    {
        // Los lenguages soportados por la aplicación
        
        $provinces = array('Barcelona', 'Tarragona', 'Lleida', 'Girona', 'Madrid ','Sta. Cruz de Tenerife' );

        return $provinces[array_rand($provinces)];
    }
    
    /**
     * Generador aleatorio de nombres de personas.
     * Aproximadamente genera un 50% de hombres y un 50% de mujeres.
     *
     * @return string Nombre aleatorio generado para el usuario.
     */
    public static function getName()
    {
        // Los nombres más populares en España según el INE
        // Fuente: http://www.ine.es/daco/daco42/nombyapel/nombyapel.htm

        $hombres = array(
            'Antonio', 'José', 'Manuel', 'Francisco', 'Juan', 'David',
            'José Antonio', 'José Luis', 'Jesús', 'Javier', 'Francisco Javier',
            'Carlos', 'Daniel', 'Miguel', 'Rafael', 'Pedro', 'José Manuel',
            'Ángel', 'Alejandro', 'Miguel Ángel', 'José María', 'Fernando',
            'Luis', 'Sergio', 'Pablo', 'Jorge', 'Alberto'
        );
        $mujeres = array(
            'María Carmen', 'María', 'Carmen', 'Josefa', 'Isabel', 'Ana María',
            'María Dolores', 'María Pilar', 'María Teresa', 'Ana', 'Francisca',
            'Laura', 'Antonia', 'Dolores', 'María Angeles', 'Cristina', 'Marta',
            'María José', 'María Isabel', 'Pilar', 'María Luisa', 'Concepción',
            'Lucía', 'Mercedes', 'Manuela', 'Elena', 'Rosa María'
        );

        if (rand() % 2) {
            return $hombres[array_rand($hombres)];
        } else {
            return $mujeres[array_rand($mujeres)];
        }
    }
    
    /**
     * Generador aleatorio de apellidos de personas.
     *
     * @return string Apellido aleatorio generado para el usuario.
     */
    public static function getSurname()
    {
        // Los apellidos más populares en España según el INE
        // Fuente: http://www.ine.es/daco/daco42/nombyapel/nombyapel.htm

        $apellidos = array(
            'García', 'González', 'Rodríguez', 'Fernández', 'López', 'Martínez',
            'Sánchez', 'Pérez', 'Gómez', 'Martín', 'Jiménez', 'Ruiz',
            'Hernández', 'Díaz', 'Moreno', 'Álvarez', 'Muñoz', 'Romero',
            'Alonso', 'Gutiérrez', 'Navarro', 'Torres', 'Domínguez', 'Vázquez',
            'Ramos', 'Gil', 'Ramírez', 'Serrano', 'Blanco', 'Suárez', 'Molina',
            'Morales', 'Ortega', 'Delgado', 'Castro', 'Ortíz', 'Rubio', 'Marín',
            'Sanz', 'Iglesias', 'Nuñez', 'Medina', 'Garrido'
        );

        return $apellidos[array_rand($apellidos)].' '.$apellidos[array_rand($apellidos)];
    }

    /**
     * Generador aleatorio de direcciones postales.
     *
     * @return string         Dirección aleatoria generada para el usuario.
     */
    public static function getDirection()
    {
        $prefijos = array('Calle', 'Avenida', 'Plaza');
        $nombres = array(
            'Lorem', 'Ipsum', 'Sitamet', 'Consectetur', 'Adipiscing',
            'Necsapien', 'Tincidunt', 'Facilisis', 'Nulla', 'Scelerisque',
            'Blandit', 'Ligula', 'Eget', 'Hendrerit', 'Malesuada', 'Enimsit'
        );

        return $prefijos[array_rand($prefijos)].' '.$nombres[array_rand($nombres)].', '.rand(1, 100);
    }

    /**
     * Generador aleatorio de códigos postales
     *
     * @return string Código postal aleatorio generado para el usuario.
     */
    public static function getDNI()
    {
        return sprintf('%08s', rand(10000000, 99999999)).'T';
    }

    /**
     * Generador aleatorio de códigos postales
     *
     * @return string Código postal aleatorio generado para el usuario.
     */
    public static function getPostalCode()
    {
        return sprintf('%02s%03s', rand(10, 52), rand(100, 999));
    }

    /**
     * Generador aleatorio de telefonos
     *
     * @return string Phone aleatorio generado para el usuario.
     */
    public static function getPhone()
    {
        return sprintf('%09s', rand(100000000, 999999999));
    }
    
    
/*********************************************************************
 * GENERADOR VALORES COCHE
 **********************************************************************/
    
    /**
     * Generador aleatorio de versiones de coches
     *
     * @return string Version aleatorio generado para el coche.
     */
    public static function getVersion()
    {
        $versions = array(
            '1.8_TURBO', '1.9D TDI-IB', '1.8 TURBO', 
            '1.9D TDI - IB', '2.0 - E 200 D', '3.2 - E 320'
        );
        return $versions[array_rand($versions)];
    }

    /**
     * Generador aleatorio de matriculas
     *
     * @return string PlateNumber aleatorio generado para el coche.
     */
    public static function getPlateNumber()
    {
        return 'T-'.sprintf('%04s', rand(0000), rand(9999)).'-TT';
    }
    
    /**
     * Generador aleatorio de vin de coches
     *
     * @return integer Vin aleatorio generado para el coche.
     */
    public static function getVin()
    {
        return rand(10000000000000000, 99999999999999999);
    }

    /**
     * Generador aleatorio de año de los coches
     *
     * @return string Year aleatorio generado para el coche.
     */
    public static function getYear()
    {
        return sprintf('%4s', rand(1900, 2010));
    }

        
/*********************************************************************
 * GENERADOR VALORES TICKET
 **********************************************************************/
    
    /**
     * Generador aleatorio de estado del ticket
     *
     * @return string Status aleatorio generado para el ticket.
     */
    public static function getStatus()
    {
        $status = array(
            'open', 'closed'
        );
        return $status[array_rand($status)];
    }
    
/*********************************************************************
 * GENERADOR VALORES SYSTEM
 **********************************************************************/
    /**
     * Generador aleatorio de sistema de las incidencias
     *
     * @return string System aleatorio generado para la incidencia.
     */
//    public static function getSystem()
//    {
//        $system = array(
//            'CARROCERÍA','CONFORT','DIRECCION','ELECTRICIDAD','FRENOS',
//            'MOTOR DIESEL','MOTOR GASOLINA','PROGRAMACION Y CODIFICACION',
//            'SEGURIDAD','SUSPENSION','TRANSMISION','VARIOS'
//        );
//        return $system[array_rand($system)];
//    }
    
    
}