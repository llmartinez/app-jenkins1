<?php

namespace Adservice\WorkshopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adservice\WorkshopBundle\Entity\ADSPlus
 *
 * @ORM\Table(name="talleradsplus")
 * @ORM\Entity
 */
class ADSPlus {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $idTallerADS
     *
     * @ORM\Column(name="idTallerADS", type="integer")
     */
    private $idTallerADS;

    /**
     * @var boolean $altaInicial
     *
     * @ORM\Column(name="altaInicial", type="string", length=255)
     */
    private $altaInicial;

    /**
     * @var string $ultAlta
     *
     * @ORM\Column(name="ultAlta", type="string", length=255)
     */
    private $ultAlta;

    /**
     * @var string $baja
     *
     * @ORM\Column(name="baja", type="string", length=255)
     */
    private $baja;

    /**
     * @var string $contador
     *
     * @ORM\Column(name="contador", type="integer")
     */
    private $contador;

    /**
     * @var boolean $active
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get idTallerADS
     *
     * @return string
     */
    public function getIdTallerADS() {
        return $this->idTallerADS;
    }

    /**
     * Set idTallerADS
     *
     * @param string $idTallerADS
     */
    public function setIdTallerADS($idTallerADS) {
        $this->idTallerADS = $idTallerADS;
    }

    /**
     * Get altaInicial
     *
     * @return string
     */
    public function getAltaInicial() {
        return $this->altaInicial;
    }

    /**
     * Set altaInicial
     *
     * @param string $altaInicial
     */
    public function setAltaInicial($altaInicial) {
        $this->altaInicial = $altaInicial;
    }

    /**
     * Get ultAlta
     *
     * @return string
     */
    public function getUltAlta() {
        return $this->ultAlta;
    }

    /**
     * Set ultAlta
     *
     * @param string $ultAlta
     */
    public function setUltAlta($ultAlta) {
        $this->ultAlta = $ultAlta;
    }

    /**
     * Get baja
     *
     * @return string
     */
    public function getBaja() {
        return $this->baja;
    }

    /**
     * Set baja
     *
     * @param string $baja
     */
    public function setBaja($baja) {
        $this->baja = $baja;
    }

    /**
     * Get contador
     *
     * @return string
     */
    public function getContador() {
        return $this->contador;
    }

    /**
     * Set contador
     *
     * @param string $contador
     */
    public function setContador($contador) {
        $this->contador = $contador;
    }

    /**
     * Set active
     *
     * @param boolean $active
     */
    public function setActive($active) {
        $this->active = $active;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive() {
        return $this->active;
    }

    public function __toString() {
        return $this->id;
    }

}