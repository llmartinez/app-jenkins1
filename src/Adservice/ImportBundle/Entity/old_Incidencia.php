<?php
namespace Adservice\ImportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Adservice\ImportBundle\Entity\old_Incidencia
 *
 * @ORM\Table(name="incidencia")
 * @ORM\Entity
 */
class old_Incidencia {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $asesor
     *
     * @ORM\Column(name="asesor", type="string", length=255)
     */
    private $asesor;

    /**
     * @var string $socio
     *
     * @ORM\Column(name="socio", type="string", length=255)
     */
    private $socio;

    /**
     * @var string $taller
     *
     * @ORM\Column(name="taller", type="string", length=255)
     */
    private $taller;

    /**
     * @var string $id_taller
     *
     * @ORM\Column(name="id_taller", type="integer", length=255)
     */
    private $id_taller;

    /**
     * @var string $coche
     *
     * @ORM\Column(name="coche", type="string", length=255)
     */
    private $coche;

    /**
     * @var string $oper
     *
     * @ORM\Column(name="oper", type="string", length=255)
     */
    private $oper;

    /**
     * @var string $descripcion
     *
     * @ORM\Column(name="descripcion", type="string", length=255)
     */
    private $descripcion;

    /**
     * @var string $seguimiento
     *
     * @ORM\Column(name="seguimiento", type="string", length=255)
     */
    private $seguimiento;

    /**
     * @var string $solucion
     *
     * @ORM\Column(name="solucion", type="string", length=255)
     */
    private $solucion;

    /**
     * @var string $importancia
     *
     * @ORM\Column(name="importancia", type="string", length=255)
     */
    private $importancia;

    /**
     * @var string $fecha
     *
     * @ORM\Column(name="fecha", type="string", length=255)
     */
    private $fecha;

    /**
     * @var string $active
     *
     * @ORM\Column(name="active", type="string", length=255)
     */
    private $active;

//  ____  _____ _____ _____ _____ ____  ____    ______ _____ _____ _____ _____  ____  ____
// / ___|| ____|_   _|_   _| ____|  _ \/ ___|  / / ___| ____|_   _|_   _| ____||  _ \/ ___|
// \___ \|  _|   | |   | | |  _| | |_) \___ \ / / |  _|  _|   | |   | | |  _|  | |_) \___ \
//  ___) | |___  | |   | | | |___|  _ < ___) / /| |_| | |___  | |   | | | |___ |  _ < ___) |
// |____/|_____| |_|   |_| |_____|_| \_\____/_/  \____|_____| |_|   |_| |_____||_| \_\____/


    public function __toString() {
        return $this->getDescripcion();
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
     * Set asesor
     *
     * @param string $asesor
     */
    public function setAsesor($asesor) {
        $this->asesor = $asesor;
    }

    /**
     * Get asesor
     *
     * @return string
     */
    public function getAsesor() {
        return $this->asesor;
    }

    /**
     * Set socio
     *
     * @param string $socio
     */
    public function setSocio($socio) {
        $this->socio = $socio;
    }

    /**
     * Get socio
     *
     * @return string
     */
    public function getSocio() {
        return $this->socio;
    }

    /**
     * Set taller
     *
     * @param string $taller
     */
    public function setTaller($taller) {
        $this->taller = $taller;
    }

    /**
     * Get taller
     *
     * @return string
     */
    public function getTaller() {
        return $this->taller;
    }

    /**
     * Set id_taller
     *
     * @param string $id_taller
     */
    public function setIdTaller($id_taller) {
        $this->id_taller = $id_taller;
    }

    /**
     * Get id_taller
     *
     * @return string
     */
    public function getIdTaller() {
        return $this->id_taller;
    }

    /**
     * Set coche
     *
     * @param string $coche
     */
    public function setCoche($coche) {
        $this->coche = $coche;
    }

    /**
     * Get coche
     *
     * @return string
     */
    public function getCoche() {
        return $this->coche;
    }

    /**
     * Set oper
     *
     * @param string $oper
     */
    public function setOper($oper) {
        $this->oper = $oper;
    }

    /**
     * Get oper
     *
     * @return string
     */
    public function getOper() {
        return $this->oper;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     */
    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion() {
        return $this->descripcion;
    }

    /**
     * Set seguimiento
     *
     * @param string $seguimiento
     */
    public function setSeguimiento($seguimiento) {
        $this->seguimiento = $seguimiento;
    }

    /**
     * Get seguimiento
     *
     * @return string
     */
    public function getSeguimiento() {
        return $this->seguimiento;
    }

    /**
     * Set solucion
     *
     * @param string $solucion
     */
    public function setSolucion($solucion) {
        $this->solucion = $solucion;
    }

    /**
     * Get solucion
     *
     * @return string
     */
    public function getSolucion() {
        return $this->solucion;
    }

    /**
     * Set importancia
     *
     * @param string $importancia
     */
    public function setImportancia($importancia) {
        $this->importancia = $importancia;
    }

    /**
     * Get importancia
     *
     * @return string
     */
    public function getImportancia() {
        return $this->importancia;
    }

    /**
     * Set fecha
     *
     * @param string $fecha
     */
    public function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    /**
     * Get fecha
     *
     * @return string
     */
    public function getFecha() {
        return $this->fecha;
    }

    /**
     * Set active
     *
     * @param string $active
     */
    public function setActive($active) {
        $this->active = $active;
    }

    /**
     * Get active
     *
     * @return string
     */
    public function getActive() {
        return $this->active;
    }
}
