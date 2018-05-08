<?php

namespace Adservice\UtilBundle\Services;

use Adservice\CarBundle\Entity\Car;
use Adservice\UtilBundle\Entity\WebservicesHistorical;

class DGTWebservice implements WebserviceInterface
{
    const DGT_URL = 'https://www.ad360.es/api/v1/eina/identificacion/';
    protected $security;
    protected $em;
    protected $token_storage;

    function __construct($security, $em, $token_storage)
    {
        $this->security = $security;
        $this->em = $em;
        $this->token_storage = $token_storage;
    }

    /**
     * Devuelve los resultados que obtenemos del webservice que conecta con la DGT
     *
     * @param $matricula
     * @return mixed
     */
    function getData($matricula)
    {
        //Cogemos las posibles urls de la matricula correspondiente
        $urls = $this->getURLS($matricula);

        foreach ($urls as $url) {

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl,CURLOPT_FAILONERROR,true);
            $results = curl_exec($curl);

            if (!$results) {

                $errors = array('error' => curl_error($curl), 'matricula' => $matricula);
            } else {

                $dataArray = json_decode($results, true);

                //Si el resultado no contiene un error de token es un resultado correcto y lo devolvemos
                if ($dataArray['vin'] != "Token decodification error") {

                    return $results;
                } else {

                    $errors = array('error' => "Token decodification error", 'matricula' => $matricula);
                }
            }

            curl_close($curl);
        }

        //save error to WebservicesHistorical
        return json_encode($errors);
    }

    /**
     * Devuelve las distintas posibles URLs para llamar al webservice
     *
     * @param $matricula
     * @return array
     */
    private function getURLS($matricula)
    {
        $tokens = $this->security->encryptADS($matricula);

        foreach ($tokens as $token){

            $urls[] = self::DGT_URL . $matricula . '/' . $token;
        }

        return $urls;
    }

    /**
     * Transformamos el json que devuelve el webservice en un array.
     * El array estará vacío si hay un error de decodificación del token (especificado en el 'vin')
     * o si no se ha encontrado la matrícula en la DGT.
     * Sino, por cada coincidencia del json, setearemos la información en un Car y lo añadiremos al array.
     * Si no hay error, guardamos la petición del webservice en WebservicesHistorical.
     *
     * @param $data
     * @return array
     */
    public function transformData($data)
    {
        $dataArray = json_decode($data, true);
        $arrayCars = array();

        if (array_key_exists('error', $dataArray)) {

            $this->saveHistorical($dataArray['matricula'], null, $dataArray['error']);
            return array('error' => true, 'cars' => $arrayCars);
        }

        $car = $this->em->getRepository('CarBundle:Car')->findOneBy(array('plateNumber' => $dataArray['matricula']));

        if(!$car){
            $car = new Car();
            $car->setPlateNumber($dataArray['matricula']);
        }

        //if (!isset($dataArray['procedencia'])) return array('error' => false, 'cars' => $arrayCars);

        $this->saveHistorical($dataArray['matricula'], $dataArray['procedencia'], null);

        if (isset($dataArray['vin'])) {

            $car->setVin($dataArray['vin']);
            $car->setOrigin('DGT');

            //if (!isset($dataArray['coincidencias'])) return array('error' => false, 'cars' => $arrayCars);

            $car->setVariants(count($dataArray['coincidencias']));

            foreach ($dataArray['coincidencias'] as $coincidencia) {

                if ($this->checkData($coincidencia)) {

                    $idsArray = explode('/', $coincidencia['id']);
                    $brand = $this->em->getRepository('CarBundle:Brand')->findOneBy(array('id' => $idsArray[0]));
                    $model = $this->em->getRepository('CarBundle:Model')->findOneBy(array('id' => $idsArray[1]));
                    $version = $this->em->getRepository('CarBundle:Version')->findOneBy(array('id' => $idsArray[2]));
                    $car->setBrand($brand);
                    $car->setModel($model);
                    $car->setVersion($version);
                    $car->setKW($version->getKw());
                    $car->setDisplacement($version->getCm3());
                    $car->setMotor($version->getMotor());
                    if (isset($coincidencia['an'])) $car->setYear($coincidencia['an']);

                    $arrayCars[] = $car->to_json();
                }
            }
        }

        return array('error' => false, 'cars' => $arrayCars);
    }

    /**
     * Comprobamos que el webservice nos devuelva los 3 ids necesarios (marca, modelo y versión)
     * y si tenemos los ids del TecDoc en nuestra BD.
     *
     * @param $coincidencia
     * @return bool
     */
    public function checkData($coincidencia){

        if (!isset($coincidencia['id'])) return false;

        $idsArray = explode('/', $coincidencia['id']);

        if (count($idsArray) < 3 OR $idsArray[0] == "") return false;

        $brand = $this->em->getRepository('CarBundle:Brand')->findOneBy(array('id' => $idsArray[0]));
        $model = $this->em->getRepository('CarBundle:Model')->findOneBy(array('id' => $idsArray[1]));
        $version = $this->em->getRepository('CarBundle:Version')->findOneBy(array('id' => $idsArray[2]));

        if ($brand == null OR $model == null OR $version == null) return false;

        return true;
    }

    private function saveHistorical($matricula, $origin = null, $error = null){

        $addToHistorical = new WebservicesHistorical();

        $user = $this->em->getRepository('UserBundle:User')->find($this->token_storage->getToken()->getUser()->getId());

        $addToHistorical->setUser($user);
        $addToHistorical->setPlateNumber($matricula);
        if ($origin != null) {

            if ($origin == "CACHE") $origin = "DGT_".$origin;
            $addToHistorical->setOrigin($origin);
        }
        if ($error != null) {

            $addToHistorical->setOrigin("DGT_ERROR");
            $addToHistorical->setError($error);
        }

        $this->em->persist($addToHistorical);
        $this->em->flush();
    }
}
