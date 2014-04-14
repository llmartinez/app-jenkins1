<?php

namespace Adservice\UtilBundle\DataFixtures\ORM;

class Data {

    /*Aqui asignamos el numero de objetos que se crearan por cada entidad.*/
    private static $numAdmins    = 5;
    private static $numAssessors = 35;
    private static $numAds       = 150;
    private static $numPartners  = 150;
    private static $numShops     = 300;
    private static $numWorkshops = 1000;
    private static $numTickets   = 10000;
    private static $numPosts     = 4;

    /**
     * Devuelve el numero de entidades por clase que se crearan.
     *
     * @return integer $num
     */
    public static function getNumAdmins()
    {
        // Numero de entidades por clase de la aplicacion
        return Data::$numAdmins;
    }
    /**
     * Devuelve el numero de entidades por clase que se crearan.
     *
     * @return integer $num
     */
    public static function getNumAssessors()
    {
        // Numero de entidades por clase de la aplicacion
        return Data::$numAssessors;
    }
    /**
     * Devuelve el numero de entidades por clase que se crearan.
     *
     * @return integer $num
     */
    public static function getNumAds()
    {
        // Numero de entidades por clase de la aplicacion
        return Data::$numAds;
    }
    /**
     * Devuelve el numero de partners por clase que se crearan.
     *
     * @return integer $num
     */
    public static function getNumPartners()
    {
        // Numero de partners por clase de la aplicacion
        return Data::$numPartners;
    }
    /**
     * Devuelve el numero de entidades por clase que se crearan.
     *
     * @return integer $num
     */
    public static function getNumShops()
    {
        // Numero de entidades por clase de la aplicacion
        return Data::$numShops;
    }
    /**
     * Devuelve el numero de entidades por clase que se crearan.
     *
     * @return integer $num
     */
    public static function getNumWorkshops()
    {
        // Numero de entidades por clase de la aplicacion
        return Data::$numWorkshops;
    }

    /**
     * Devuelve el numero de tickets por clase que se crearan.
     * Se utiliza tambien en Coches e Incidencias, ya que deben tener el mismo valor
     *
     * @return integer $num
     */
    public static function getNumTickets()
    {
        // Numero de tickets por clase de la aplicacion
        return Data::$numTickets;
    }

    /**
     * Devuelve el numero maximo de posts por ticket que se crearan.
     *
     * @return integer $num
     */
    public static function getNumPosts()
    {
        // Numero maximo de posts por ticket de la aplicacion
        return Data::$numPosts;
    }

/*********************************************************************
 * GENERADOR VALORES UTIL
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

        $countries = array('Spain', 'France');

        return $countries[array_rand($countries)];
    }

    /**
     * Generador aleatorio de regiones.
     *
     * @return string Region aleatorio generado para el usuario.
     */
    public static function getRegions($country)
    {
        // Los lenguages soportados por la aplicación

        $regions = array('Spain'  => array('Catalunya', 'Madrid', 'Canarias'),
                         'France' => array('Borgoña', 'Córcega', 'Isla de Francia')
                        );

        return $regions[$country][array_rand($regions[$country])];
    }

/*********************************************************************
 * GENERADOR VALORES PARTNER
 **********************************************************************/

    /**
     * Generador aleatorio de partners
     *
     * @return string Partner aleatorio.
     */
    public static function getPartner()
    {
        return 'partner'.rand(1, Data::getNumPartners());
    }

    /**
     * Generador aleatorio de tiendas
     *
     * @return string Shop aleatorio.
     */
    public static function getShop()
    {
        return 'shop'.rand(1, Data::getNumShops());
    }

/*********************************************************************
 * GENERADOR VALORES WORKSHOP
 **********************************************************************/

    /**
     * Generador aleatorio de tipologias.
     *
     * @return string Typologies aleatorio generado para el usuario.
     */
    public static function getTypologies()
    {
        // Las tipologias soportadas por la aplicación

        $typologies = array('Autoservice AD', 'Garage AD', 'Carrosserie AD');

        return $typologies[array_rand($typologies)];
    }

    /**
     * Generador aleatorio de workshops
     *
     * @return string Workshop aleatorio.
     */
    public static function getWorkshop()
    {
        return 'workshop'.rand(1, Data::getNumWorkshops());
    }

/*********************************************************************
 * GENERADOR VALORES USUARIO
 **********************************************************************/


    /**
     * Generador aleatorio de admins
     *
     * @return string Admin aleatorio.
     */
    public static function getAdmin()
    {
        return 'admin'.rand(1, Data::getNumAdmins());
    }

    /**
     * Generador aleatorio de assessors
     *
     * @return string Assessor aleatorio.
     */
    public static function getAssessor()
    {
        return 'assessor'.rand(1, Data::getNumAssessors());
    }
    /**
     * Generador aleatorio de users
     *
     * @return string User aleatorio.
     */
    public static function getUser()
    {
        return 'user'.rand(1, Data::getNumWorkshops());
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
            '1.9D TDI - IB', '2.0 - E 200 D', '3.2 - E 320',
            '1.2 12V', '1.4 16V', '1.9D SDI'
        );
        return $versions[array_rand($versions)];
    }

    /**
     * Generador aleatorio de matriculas
     *
     * @return string PlateNumber aleatorio generado para el coche.
     */
    public static function getPlateNumber($i)
    {
        $result = 'T-'.$i.'-TT';
        return $result;
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
        return sprintf('%4s', rand(1990, 2010));
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

    /**
     * Generador aleatorio de la descripcion del ticket
     *
     * @return string Description aleatorio generado para el ticket.
     */
    public static function getTicketDescription($i)
    {
        return 'Test n.'.$i;
    }

    /**
     * Generador aleatorio del post del ticket
     *
     * @return string Post aleatorio generado para el ticket.
     */
    public static function getPostMessage($i, $j)
    {
        return 'Post n.'.$j.' del ticket n.'.$i;
    }

    /**
     * Generador aleatorio del dueño del post
     *
     * @return string Owner aleatorio generado para el post.
     */
    public static function getPostOwner($entidad)
    {
        if($entidad->getTicket()->getAssignedTo() != null)
        {
            if (rand() % 2) {
                return $entidad->getTicket()->getCreatedBy()->getUserName();
            } else {
                return $entidad->getTicket()->getAssignedTo()->getUserName();
            }
        }else{
            return $entidad->getTicket()->getCreatedBy()->getUserName();
        }
    }
    /**
     * Generador aleatorio de la descripcion de la incidencia
     *
     * @return string Description aleatorio generado para la incidencia.
     */
    public static function getDescription($i)
    {
        return 'Descipcion de la incidencia n.'.$i;
    }

    /**
     * Generador aleatorio de la solucion de la incidencia.
     *
     * @return string Solution aleatorio generado para la incidencia.
     */
    public static function getSolution($i)
    {
        return 'Solucion de la incidencia n.'.$i;
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