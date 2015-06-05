<?php
namespace Adservice\ImportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Adservice\ImportBundle\Entity\old_Marca
 *
 * @ORM\Table(name="talleradsplus")
 * @ORM\Entity
 */
class old_ADSPlus {

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
     * @var string $altaInicial
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
     * @var string $active
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

//  ____  _____ _____ _____ _____ ____  ____    ______ _____ _____ _____ _____  ____  ____
// / ___|| ____|_   _|_   _| ____|  _ \/ ___|  / / ___| ____|_   _|_   _| ____||  _ \/ ___|
// \___ \|  _|   | |   | | |  _| | |_) \___ \ / / |  _|  _|   | |   | | |  _|  | |_) \___ \
//  ___) | |___  | |   | | | |___|  _ < ___) / /| |_| | |___  | |   | | | |___ |  _ < ___) |
// |____/|_____| |_|   |_| |_____|_| \_\____/_/  \____|_____| |_|   |_| |_____||_| \_\____/


    public function __toString() {
        return $this->getNombre();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set idTallerADS
     *
     * @param integer $idTallerADS
     */
    public function setIdTallerADS($idTallerADS) {
        $this->idTallerADS = $idTallerADS;
    }

    /**
     * Get idTallerADS
     *
     * @return integer
     */
    public function getIdTallerADS() {
        return $this->idTallerADS;
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
     * Get altaInicial
     *
     * @return string
     */
    public function getAltaInicial() {
        return $this->altaInicial;
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
     * Get ultAlta
     *
     * @return string
     */
    public function getUltAlta() {
        return $this->ultAlta;
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
     * Get baja
     *
     * @return string
     */
    public function getBaja() {
        return $this->baja;
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
     * Get contador
     *
     * @return string
     */
    public function getContador() {
        return $this->contador;
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
}
