<?php

namespace Adservice\UtilBundle\Controller;

use Adservice\WorkshopBundle\Entity\Historical;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Adservice\UtilBundle\Entity\Region;

class UtilController extends Controller
{
    /**
     * Asigna el usuario que ha creado la clase y la fecha de la creación.
     * @param Class $entity
     * @param Class $user
     * @return Class
     */
    public static function newEntity($entity, $user){
        $entity->setCreatedBy($user);
        $entity->setCreatedAt(new \DateTime(\date("Y-m-d H:i:s")));
        return $entity;
    }

    /**
     * Asigna el usuario que ha modificado la clase y la fecha de la modificación.
     * @param EntityManager $em
     * @param Class $entity
     * @param Bool $auto_flush true: aplica cambios en BBDD
     * @return Bool
     */
    public static function saveEntity($em, $entity, $user, $auto_flush=true)
    {
        $entity->setModifiedBy($user);
        $entity->setModifiedAt(new \DateTime(\date("Y-m-d H:i:s")));
        $em->persist($entity);

        if($auto_flush) $em->flush();
        return true;
    }

    /**
     * Devuelve un array con paises, regiones y ciudades para utilizar para autocompletar los formularios
     * @return array
     */
    public static function getLocations($em)
    {
       $countries = $em->getRepository('UtilBundle:Country')->findAll();
       $regions   = $em->getRepository('UtilBundle:Region' )->findAll();
       $cities    = $em->getRepository('UtilBundle:City'   )->findAll();

       $array = array('countries' => $countries, 'regions' => $regions, 'cities' => $cities);
       return $array;
    }

    /**
     * Obtiene el Slug de una cadena
     * @param  string $cadena
     * @param  string $separador
     * @return string
     */
    static public function getSlug($cadena, $separador = '-')
    {
        // Remove all non url friendly characters with the unaccent function
        $valor = self::sinAcentos($cadena);

        if (function_exists('mb_strtolower')) {
            $valor = mb_strtolower($valor);
        } else {
            $valor = strtolower($valor);
        }

        // Remove all none word characters
        $valor = preg_replace('/\W/', ' ', $valor);

        // More stripping. Replace spaces with dashes
        $valor = strtolower(preg_replace('/[^A-Z^a-z^0-9^\/]+/', $separador,
                            preg_replace('/([a-z\d])([A-Z])/', '\1_\2',
                            preg_replace('/([A-Z]+)([A-Z][a-z])/', '\1_\2',
                            preg_replace('/::/', '/', $valor)))));
        //return trim($valor, $separador);
        return $valor;
    }

    /**
     * Genera un Token aleatorio
     * @return string
     */
    static public function getRandomToken()
    {
          $key = '';
          $keys = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));

          for ($i = 0; $i < 20; $i++) {
              $key .= $keys[array_rand($keys)];
          }

          return $key;
    }

    /**
     * Obtiene el Slug de un username sin usar. Si la cadena pasada por parametro ya existe, se le añade un '-' y un numero.
     * @param  entityManager $em
     * @param  string        $name
     * @return string
     */
    public static function getUsernameUnused($em, $name)
    {
        $separador = '';
        $slug = UtilController::getSlug($name, $separador);
        $unused = 1;
        while($unused != 'unused') {
            $find = $em->getRepository('UserBundle:User')->findOneByUsername($slug);
            if( $find == null) { $unused = 'unused'; }
            else{
                $slug = UtilController::getSlug($name, $separador).$unused;
                $unused = $unused+1;
            }
        }
        return $slug;
    }

    /**
     * Obtiene el primero Codigo de Socio sin usar
     * @param  entityManager $em
     * @return integer
     */
    public static function getCodePartnerUnused($em)
    {
        $code   = 1; //Si no hay codigo por parametro se asigna 1
        $unused = 1;

        while($unused != 'unused') {
            $find = $em->getRepository('PartnerBundle:Partner')->findOneBy(array('code_partner' =>$code));

            if( $find == null) { $unused = 'unused'; } //Si no encuentra el codigo significa que esta disponible y se devuelve
            else               { $code  ++;          } //Si el codigo esta en uso, se busca el siguiente
        }
        return $code;
    }

    /**
     * Obtiene el primero Codigo de Taller sin usar
     * @param  entityManager $em
     * @param  integer       $code_partner
     * @return integer
     */
    public static function getCodeWorkshopUnused($em, $code_partner, $code_workshop=null)
    {
        $code   = 1; //Si no hay codigo por parametro se asigna 1
        $unused = 1;

        if($code_workshop != null )
        {
            $find   = $em->getRepository('WorkshopBundle:Workshop'  )->findOneBy(array('code_partner' => $code_partner, 'code_workshop' => $code_workshop));
            if($find == null) {
                $unused = 'unused';
                $code   = $code_workshop;
            }
        }

        while($unused != 'unused') {
            $find   = $em->getRepository('WorkshopBundle:Workshop'  )->findOneBy(array('code_partner' => $code_partner, 'code_workshop' => $code));
            $find_O = $em->getRepository('OrderBundle:WorkshopOrder')->findOneBy(array('code_partner' => $code_partner, 'code_workshop' => $code));

            if($find == null and $find_O == null) $unused = 'unused'; // Si no encuentra el codigo significa que esta disponible y se devuelve
            else $code ++;                                            // Si el codigo esta en uso, se busca el siguiente
        }

        return $code;
    }

    /**
     * compara el slug de una cadena ($string_slug) con un array de variables ($array),
     * si coincide devuelve $var, sino devuelve $string_slug
     * @param  string $string_slug
     * @param  string $array
     * @return string
     */
    public static function normalizeString($string_slug, $array)
    {
        $return = '';
        foreach ($array as $var) {

            $slug = UtilController::getSlug($var);

            if( $slug == $string_slug ) $return = $slug;
        }

        if( $return == '' ) $return = $string_slug;

        return $return;
    }

    /**
     * Define los campos de contacto de una entidad a partir de los campos de entidad de otra.
     * También elimina espacios en blanco para evitar errores en numeros
     * @param  [type] $entity [description]
     * @param  [type] $data   [description]
     * @return [type]         [description]
     */
    public static function settersContact($entity, $data, $actual_region = '', $actual_city = '')
    {
        $entity->setPhoneNumber1  (UtilController::getSlug($data->getPhoneNumber1() , ''));
        $entity->setPhoneNumber2  (UtilController::getSlug($data->getPhoneNumber2() , ''));
        $entity->setMobileNumber1 (UtilController::getSlug($data->getMobileNumber1(), ''));
        $entity->setMobileNumber2 (UtilController::getSlug($data->getMobileNumber2(), ''));
        $entity->setFax           (UtilController::getSlug($data->getFax()          , ''));
        $entity->setCountry       ($data->getCountry());
        $entity->setAddress       ($data->getAddress());
        $entity->setPostalCode    ($data->getPostalCode());
        $entity->setEmail1        ($data->getEmail1());
        $entity->setEmail2        ($data->getEmail2());

        if($data->getRegion() == '[object Object]') $entity->setRegion($actual_region    );
        else                                        $entity->setRegion($data->getRegion());
        if($data->getCity()   == '[object Object]') $entity->setCity  ($actual_city      );
        else                                        $entity->setCity  ($data->getCity()  );

        return $entity;
    }

    public static function sinAcentos($string)
    {
        if ( ! preg_match('/[\x80-\xff]/', $string) ) {
          return $string;
        }

        if (self::seemsUtf8($string)) {
          $chars = array(
          // Decompositions for Latin-1 Supplement
          chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
          chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
          chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
          chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
          chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
          chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
          chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
          chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
          chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
          chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
          chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
          chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
          chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
          chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
          chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
          chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
          chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
          chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
          chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
          chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
          chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
          chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
          chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
          chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
          chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
          chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
          chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
          chr(195).chr(191) => 'y',
          // Decompositions for Latin Extended-A
          chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
          chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
          chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
          chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
          chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
          chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
          chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
          chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
          chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
          chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
          chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
          chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
          chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
          chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
          chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
          chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
          chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
          chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
          chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
          chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
          chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
          chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
          chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
          chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
          chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
          chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
          chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
          chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
          chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
          chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
          chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
          chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
          chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
          chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
          chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
          chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
          chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
          chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
          chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
          chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
          chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
          chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
          chr(197).chr(148) => 'R', chr(197).chr(149) => 'r',
          chr(197).chr(150) => 'R', chr(197).chr(151) => 'r',
          chr(197).chr(152) => 'R', chr(197).chr(153) => 'r',
          chr(197).chr(154) => 'S', chr(197).chr(155) => 's',
          chr(197).chr(156) => 'S', chr(197).chr(157) => 's',
          chr(197).chr(158) => 'S', chr(197).chr(159) => 's',
          chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
          chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
          chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
          chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
          chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
          chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
          chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
          chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
          chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
          chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
          chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
          chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
          chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
          chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
          chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
          chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
          // Euro Sign
          chr(226).chr(130).chr(172) => 'E',
          // GBP (Pound) Sign
          chr(194).chr(163) => '',
          'Ä' => 'Ae', 'ä' => 'ae', 'Ü' => 'Ue', 'ü' => 'ue',
          'Ö' => 'Oe', 'ö' => 'oe', 'ß' => 'ss',
          // Norwegian characters
          'Å'=>'Aa','Æ'=>'Ae','Ø'=>'O','æ'=>'a','ø'=>'o'
          );

          $string = strtr($string, $chars);
        } else {
          // Assume ISO-8859-1 if not UTF-8
          $chars['in'] = chr(128).chr(131).chr(138).chr(142).chr(154).chr(158)
            .chr(159).chr(162).chr(165).chr(181).chr(192).chr(193).chr(194)
            .chr(195).chr(196).chr(197).chr(199).chr(200).chr(201).chr(202)
            .chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210)
            .chr(211).chr(212).chr(213).chr(214).chr(216).chr(217).chr(218)
            .chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227)
            .chr(228).chr(229).chr(231).chr(232).chr(233).chr(234).chr(235)
            .chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243)
            .chr(244).chr(245).chr(246).chr(248).chr(249).chr(250).chr(251)
            .chr(252).chr(253).chr(255);

          $chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

          $string = strtr($string, $chars['in'], $chars['out']);
          $doubleChars['in'] = array(chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254));
          $doubleChars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
          $string = str_replace($doubleChars['in'], $doubleChars['out'], $string);
        }

        return $string;
    }

    public static function normalizeChars($s) {
        $replace = array(
            'à' => 'a',
            'á' => 'a',
            'â' => 'a',
            'ã' => 'a',
            'ä' => 'a',
            'ą' => 'a',
            'å' => 'a',
            'ā' => 'a',
            'ă' => 'a',
            'ǎ' => 'a',
            'ǻ' => 'a',
            'À' => 'A',
            'Á' => 'A',
            'Â' => 'A',
            'Ã' => 'A',
            'Ä' => 'A',
            'Ą' => 'A',
            'Å' => 'A',
            'Ā' => 'A',
            'Ă' => 'A',
            'Ǎ' => 'A',
            'Ǻ' => 'A',

            'ç' => 'c',
            'ć' => 'c',
            'ĉ' => 'c',
            'ċ' => 'c',
            'č' => 'c',
            'Ç' => 'C',
            'Ć' => 'C',
            'Ĉ' => 'C',
            'Ċ' => 'C',
            'Č' => 'C',

            'ď' => 'd',
            'đ' => 'd',
            'Ð' => 'D',
            'Ď' => 'D',
            'Đ' => 'D',


            'è' => 'e',
            'é' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'ę' => 'e',
            'ē' => 'e',
            'ĕ' => 'e',
            'ė' => 'e',
            'ě' => 'e',
            'È' => 'E',
            'É' => 'E',
            'Ê' => 'E',
            'Ë' => 'E',
            'Ę' => 'E',
            'Ē' => 'E',
            'Ĕ' => 'E',
            'Ė' => 'E',
            'Ě' => 'E',

            'ƒ' => 'f',


            'ĝ' => 'g',
            'ğ' => 'g',
            'ġ' => 'g',
            'ģ' => 'g',
            'Ĝ' => 'G',
            'Ğ' => 'G',
            'Ġ' => 'G',
            'Ģ' => 'G',


            'ĥ' => 'h',
            'ħ' => 'h',
            'Ĥ' => 'H',
            'Ħ' => 'H',

            'ì' => 'i',
            'í' => 'i',
            'î' => 'i',
            'ï' => 'i',
            'ĩ' => 'i',
            'ī' => 'i',
            'ĭ' => 'i',
            'į' => 'i',
            'ſ' => 'i',
            'ǐ' => 'i',
            'Ì' => 'I',
            'Í' => 'I',
            'Î' => 'I',
            'Ï' => 'I',
            'Ĩ' => 'I',
            'Ī' => 'I',
            'Ĭ' => 'I',
            'Į' => 'I',
            'İ' => 'I',
            'Ǐ' => 'I',

            'ĵ' => 'j',
            'Ĵ' => 'J',

            'ķ' => 'k',
            'Ķ' => 'K',


            'ł' => 'l',
            'ĺ' => 'l',
            'ļ' => 'l',
            'ľ' => 'l',
            'ŀ' => 'l',
            'Ł' => 'L',
            'Ĺ' => 'L',
            'Ļ' => 'L',
            'Ľ' => 'L',
            'Ŀ' => 'L',


            'ñ' => 'n',
            'ń' => 'n',
            'ņ' => 'n',
            'ň' => 'n',
            'ŉ' => 'n',
            'Ñ' => 'N',
            'Ń' => 'N',
            'Ņ' => 'N',
            'Ň' => 'N',

            'ò' => 'o',
            'ó' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'ö' => 'o',
            'ð' => 'o',
            'ø' => 'o',
            'ō' => 'o',
            'ŏ' => 'o',
            'ő' => 'o',
            'ơ' => 'o',
            'ǒ' => 'o',
            'ǿ' => 'o',
            'Ò' => 'O',
            'Ó' => 'O',
            'Ô' => 'O',
            'Õ' => 'O',
            'Ö' => 'O',
            'Ø' => 'O',
            'Ō' => 'O',
            'Ŏ' => 'O',
            'Ő' => 'O',
            'Ơ' => 'O',
            'Ǒ' => 'O',
            'Ǿ' => 'O',


            'ŕ' => 'r',
            'ŗ' => 'r',
            'ř' => 'r',
            'Ŕ' => 'R',
            'Ŗ' => 'R',
            'Ř' => 'R',


            'ś' => 's',
            'š' => 's',
            'ŝ' => 's',
            'ş' => 's',
            'Ś' => 'S',
            'Š' => 'S',
            'Ŝ' => 'S',
            'Ş' => 'S',

            'ţ' => 't',
            'ť' => 't',
            'ŧ' => 't',
            'Ţ' => 'T',
            'Ť' => 'T',
            'Ŧ' => 'T',


            'ù' => 'u',
            'ú' => 'u',
            'û' => 'u',
            'ü' => 'u',
            'ũ' => 'u',
            'ū' => 'u',
            'ŭ' => 'u',
            'ů' => 'u',
            'ű' => 'u',
            'ų' => 'u',
            'ư' => 'u',
            'ǔ' => 'u',
            'ǖ' => 'u',
            'ǘ' => 'u',
            'ǚ' => 'u',
            'ǜ' => 'u',
            'Ù' => 'U',
            'Ú' => 'U',
            'Û' => 'U',
            'Ü' => 'U',
            'Ũ' => 'U',
            'Ū' => 'U',
            'Ŭ' => 'U',
            'Ů' => 'U',
            'Ű' => 'U',
            'Ų' => 'U',
            'Ư' => 'U',
            'Ǔ' => 'U',
            'Ǖ' => 'U',
            'Ǘ' => 'U',
            'Ǚ' => 'U',
            'Ǜ' => 'U',


            'ŵ' => 'w',
            'Ŵ' => 'W',

            'ý' => 'y',
            'ÿ' => 'y',
            'ŷ' => 'y',
            'Ý' => 'Y',
            'Ÿ' => 'Y',
            'Ŷ' => 'Y',

            'ż' => 'z',
            'ź' => 'z',
            'ž' => 'z',
            'Ż' => 'Z',
            'Ź' => 'Z',
            'Ž' => 'Z',

            // accentuated ligatures
            'Ǽ' => 'A',
            'ǽ' => 'a',

            '\"' => ''
        );
        return strtr($s, $replace);
    }

    private static function seemsUtf8($string)
    {
      for ($i = 0; $i < strlen($string); $i++) {
        if (ord($string[$i]) < 0x80) continue; # 0bbbbbbb
        elseif ((ord($string[$i]) & 0xE0) == 0xC0) $n=1; # 110bbbbb
        elseif ((ord($string[$i]) & 0xF0) == 0xE0) $n=2; # 1110bbbb
        elseif ((ord($string[$i]) & 0xF8) == 0xF0) $n=3; # 11110bbb
        elseif ((ord($string[$i]) & 0xFC) == 0xF8) $n=4; # 111110bb
        elseif ((ord($string[$i]) & 0xFE) == 0xFC) $n=5; # 1111110b
        else return false; # Does not match any model
        for ($j=0; $j<$n; $j++) { # n bytes matching 10bbbbbb follow ?
          if ((++$i == strlen($string)) || ((ord($string[$i]) & 0xC0) != 0x80))
          return false;
        }
      }
      return true;
    }

////////////////////////////////////////////////////////////////////////////////////////////
/* SIN USAR */
////////////////////////////////////////////////////////////////////////////////////////////


    // Función auxiliar usada para CIFs y NIFs especiales
    private function getCifSum($cif) {
        $sum = $cif[2] + $cif[4] + $cif[6];

        for ($i = 1; $i < 8; $i += 2) {
            $tmp = (string) (2 * $cif[$i]);

            $tmp = $tmp[0] + ((strlen($tmp) == 2) ? $tmp[1] : 0);

            $sum += $tmp;
        }

        return $sum;
    }

    // Valida CIFs
    public function validateCif($cif) {
        $cif_codes = 'JABCDEFGHI';

        $sum = (string) $this->getCifSum($cif);
        $n = (10 - substr($sum, -1)) % 10;

        if (preg_match('/^[ABCDEFGHJNPQRSUVW]{1}/', $cif)) {
            if (in_array($cif[0], array('A', 'B', 'E', 'H'))) {
                // Numerico
                return ($cif[8] == $n);
            } elseif (in_array($cif[0], array('K', 'P', 'Q', 'S'))) {
                // Letras
                return ($cif[8] == $cif_codes[$n]);
            } else {
                // Alfanumérico
                if (is_numeric($cif[8])) {
                    return ($cif[8] == $n);
                } else {
                    return ($cif[8] == $cif_codes[$n]);
                }
            }
        }

        return false;
    }

    // Valida NIFs (DNIs y NIFs especiales)
    public function validateNif($nif) {
        $nif_codes = 'TRWAGMYFPDXBNJZSQVHLCKE';

        $sum = (string) $this->getCifSum($nif);
        $n = 10 - substr($sum, -1);

        if (preg_match('/^[0-9]{8}[A-Z]{1}$/', $nif)) {
            // DNIs
            $num = substr($nif, 0, 8);

            return ($nif[8] == $nif_codes[$num % 23]);
        } elseif (preg_match('/^[XYZ][0-9]{7}[A-Z]{1}$/', $nif)) {
            // NIEs normales
            $tmp = substr($nif, 1, 7);
            $tmp = strtr(substr($nif, 0, 1), 'XYZ', '012') . $tmp;

            return ($nif[8] == $nif_codes[$tmp % 23]);
        } elseif (preg_match('/^[KLM]{1}/', $nif)) {
            // NIFs especiales
            return ($nif[8] == chr($n + 64));
        } elseif (preg_match('/^[T]{1}[A-Z0-9]{8}$/', $nif)) {
            // NIE extraño
            return true;
        }

        return false;
    }

    public static function saveUserFromWorkshop($workshop,$user_workshop){
        $user_workshop->setPhoneNumber1($workshop->getPhoneNumber1());
        if($workshop->getPhoneNumber1() != null){           $user_workshop->setPhoneNumber1($workshop->getPhoneNumber1());        }
        else{                                               $user_workshop->setPhoneNumber1(null);        }

        if($workshop->getPhoneNumber2() != null){           $user_workshop->setPhoneNumber2($workshop->getPhoneNumber2());        }
        else{                                               $user_workshop->setPhoneNumber2(null);        }

        if($workshop->getMobileNumber1() != null){          $user_workshop->setMobileNumber1($workshop->getMobileNumber1());      }
        else{                                               $user_workshop->setMobileNumber1(null);        }

        if($workshop->getMobileNumber2() != null){          $user_workshop->setMobileNumber2($workshop->getMobileNumber2());      }
        else{                                               $user_workshop->setMobileNumber2(null);        }

        if($workshop->getFax() != null){                    $user_workshop->setFax($workshop->getFax());                          }
        else{                                               $user_workshop->setFax(null);        }

        if($workshop->getEmail1() != null){                 $user_workshop->setEmail1($workshop->getEmail1());                    }
        else{                                               $user_workshop->setEmail1(null);        }

        if($workshop->getEmail2() != null){                 $user_workshop->setEmail2($workshop->getEmail2());                    }
        else{                                               $user_workshop->setEmail2(null);        }

        if($workshop->getCountry() != null){                $user_workshop->setCountry($workshop->getCountry());                  }
        else{                                               $user_workshop->setCountry(null);        }

        if($workshop->getRegion() != null){                 $user_workshop->setRegion($workshop->getRegion());                    }
        else{                                               $user_workshop->setRegion(null);        }

        if($workshop->getCity() != null){                   $user_workshop->setCity($workshop->getCity());                        }
        else{                                               $user_workshop->setCity(null);        }

        if($workshop->getAddress() != null){                $user_workshop->setAddress($workshop->getAddress());                  }
        else{                                               $user_workshop->setAddress(null);        }

        if($workshop->getPostalCode() != null){             $user_workshop->setPostalCode($workshop->getPostalCode());            }
        else{                                               $user_workshop->setPostalCode(null);        }

        return $user_workshop;
    }
    
     /**
     * Genera un historial de cambios del taller
     * @return WorkshopHistory
     */
    public static function createHistorical($em, $workshop, $status) {
        $historical = new Historical();
        $historical->setWorkshopId($workshop->getId());
        $historical->setPartnerId($workshop->getPartner()->getId());
        $historical->setDateOrder(new \DateTime('now'));
        $historical->setStatus($status);


        $em->persist($historical);
        $em->flush();
    }
}
