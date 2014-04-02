<?php

namespace Adservice\UtilBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Adservice\UtilBundle\Entity\Region;
use Adservice\UtilBundle\Entity\Province;

class DefaultController extends Controller {

    public function provincesFromRegionAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $petition = $this->getRequest();
        $id_region = $petition->request->get('id_region');

        $provinces = $em->getRepository("UtilBundle:Province")->findBy(array('region' => $id_region));
        foreach ($provinces as $province) {
            $json[] = $province->to_json();
        }
        return new Response(json_encode($json), $status = 200);
    }

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
