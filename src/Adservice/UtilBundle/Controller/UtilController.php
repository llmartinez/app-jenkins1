<?php

namespace Adservice\UtilBundle\Controller;

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
     * Obtiene el Slug de una cadena
     * @param  string $cadena
     * @param  string $separador
     * @return string
     */
    static public function getSlug($cadena, $separador = '-')
    {
        // Código copiado de http://cubiq.org/the-perfect-php-clean-url-generator
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $cadena);
        $slug = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $slug);
        $slug = strtolower(trim($slug, $separador));
        $slug = preg_replace("/[\/_|+ -]+/", $separador, $slug);
        return $slug;
    }

    /**
     * Obtiene el Slug de un username sin usar. Si la cadena pasada por parametro ya existe, se le añade un '-' y un numero.
     * @param  entityManager $em
     * @param  string        $name
     * @return string
     */
    static public function getUsernameUnused($em, $name)
    {
        $slug = UtilController::getSlug($name);
        $unused = 1;
        while($unused != 'unused') {
            $find = $em->getRepository('UserBundle:User')->findOneByUsername($slug);
            if( $find == null) { $unused = 'unused'; }
            else{
                $slug = UtilController::getSlug($name).'-'.$unused;
                $unused++;
            }
        }
        return $slug;
    }

    /**
     * Obtiene el primero Codigo de Socio sin usar
     * @param  entityManager $em
     * @return integer
     */
    static public function getCodePartnerUnused($em)
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
     * @param  Partner       $partner
     * @return integer
     */
    static public function getCodeWorkshopUnused($em, $partner)
    {

        $code   = 1; //Si no hay codigo por parametro se asigna 1
        $unused = 1;

        while($unused != 'unused') {
            $find = $em->getRepository('WorkshopBundle:Workshop')->findOneBy(array('partner' => $partner->getId(), 'code_workshop' => $code));

            if( $find == null) { $unused = 'unused'; } //Si no encuentra el codigo significa que esta disponible y se devuelve
            else               { $code ++;           } //Si el codigo esta en uso, se busca el siguiente
        }
        return $code;
    }


///////////////////////
/* SIN USAR */
///////////////////////


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

}
