<?php
namespace Adservice\ImportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Adservice\ImportBundle\Entity\old_Taller
 *
 * @ORM\Table(name="taller")
 * @ORM\Entity
 */
class old_Taller {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $nombre
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @var string $idGrupo
     *
     * @ORM\Column(name="idGrupo", type="string", length=255)
     */
    private $idGrupo;


    /**
     * @var string $idSocio
     *
     * @ORM\Column(name="idSocio", type="string", length=255)
     */
    private $idSocio;

    /**
     * @var string $direccion
     *
     * @ORM\Column(name="direccion", type="string", length=255)
     */
    private $direccion;

    /**
     * @var string $poblacion
     *
     * @ORM\Column(name="poblacion", type="string", length=255)
     */
    private $poblacion;

    /**
     * @var string $provincia
     *
     * @ORM\Column(name="provincia", type="string", length=255)
     */
    private $provincia;

    /**
     * @var string $tfno
     *
     * @ORM\Column(name="tfno", type="string", length=255)
     */
    private $tfno;

    /**
     * @var string $tfno2
     *
     * @ORM\Column(name="tfno2", type="string", length=255)
     */
    private $tfno2;

    /**
     * @var string $movil
     *
     * @ORM\Column(name="movil", type="string", length=255)
     */
    private $movil;

    /**
     * @var string $movil2
     *
     * @ORM\Column(name="movil2", type="string", length=255)
     */
    private $movil2;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string $email2
     *
     * @ORM\Column(name="email2", type="string", length=255)
     */
    private $email2;

    /**
     * @var string $fax
     *
     * @ORM\Column(name="fax", type="string", length=255)
     */
    private $fax;

    /**
     * @var string $contacto
     *
     * @ORM\Column(name="contacto", type="string", length=255)
     */
    private $contacto;

    /**
     * @var string $info
     *
     * @ORM\Column(name="info", type="string", length=255)
     */
    private $info;

    /**
     * @var string $asesor
     *
     * @ORM\Column(name="asesor", type="string", length=255)
     */
    private $asesor;

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
     * @var string $conflictivo
     *
     * @ORM\Column(name="conflictivo", type="string", length=255)
     */
    private $conflictivo;

    /**
     * @var string $observaciones
     *
     * @ORM\Column(name="observaciones", type="string", length=255)
     */
    private $observaciones;

    /**
     * @var string $active
     *
     * @ORM\Column(name="active", type="string", length=255)
     */
    private $active;

    /**
     * @var string $pruebas
     *
     * @ORM\Column(name="pruebas", type="string", length=255)
     */
    private $pruebas;

    /**
     * @var string $finPrueba
     *
     * @ORM\Column(name="finPrueba", type="string", length=255)
     */
    private $finPrueba;

    /**
     * @var string $tipologia
     *
     * @ORM\Column(name="tipologia", type="string", length=255)
     */
    private $tipologia;




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
     * Set nombre
     *
     * @param string $nombre
     */
    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre() {
        return $this->nombre;
    }

    /**
     * Set idGrupo
     *
     * @param string $idGrupo
     */
    public function setIdGrupo($idGrupo) {
        $this->idGrupo = $idGrupo;
    }

    /**
     * Get idGrupo
     *
     * @return string
     */
    public function getIdGrupo() {
        return $this->idGrupo;
    }

    /**
     * Set idSocio
     *
     * @param string $idSocio
     */
    public function setIdSocio($idSocio) {
        $this->idSocio = $idSocio;
    }

    /**
     * Get idSocio
     *
     * @return string
     */
    public function getIdSocio() {
        return $this->idSocio;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     */
    public function setDireccion($direccion) {
        $this->direccion = $direccion;
    }

    /**
     * Get direccion
     *
     * @return string
     */
    public function getDireccion() {
        return $this->direccion;
    }

    /**
     * Set poblacion
     *
     * @param string $poblacion
     */
    public function setPoblacion($poblacion) {
        $this->poblacion = $poblacion;
    }

    /**
     * Get poblacion
     *
     * @return string
     */
    public function getPoblacion() {
        return $this->poblacion;
    }

    /**
     * Set provincia
     *
     * @param string $provincia
     */
    public function setProvincia($provincia) {
        $this->provincia = $provincia;
    }

    /**
     * Get provincia
     *
     * @return string
     */
    public function getProvincia() {
        return $this->provincia;
    }

    /**
     * Set tfno
     *
     * @param string $tfno
     */
    public function setTfno($tfno) {
        $this->tfno = $tfno;
    }

    /**
     * Get tfno
     *
     * @return string
     */
    public function getTfno() {
        return $this->tfno;
    }

    /**
     * Set tfno2
     *
     * @param string $tfno2
     */
    public function setTfno2($tfno2) {
        $this->tfno2 = $tfno2;
    }

    /**
     * Get tfno2
     *
     * @return string
     */
    public function getTfno2() {
        return $this->tfno2;
    }

    /**
     * Set movil
     *
     * @param string $movil
     */
    public function setMovil($movil) {
        $this->movil = $movil;
    }

    /**
     * Get movil
     *
     * @return string
     */
    public function getMovil() {
        return $this->movil;
    }

    /**
     * Set movil2
     *
     * @param string $movil2
     */
    public function setMovil2($movil2) {
        $this->movil2 = $movil2;
    }

    /**
     * Get movil2
     *
     * @return string
     */
    public function getMovil2() {
        return $this->movil2;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set email2
     *
     * @param string $email2
     */
    public function setEmail2($email2) {
        $this->email2 = $email2;
    }

    /**
     * Get email2
     *
     * @return string
     */
    public function getEmail2() {
        return $this->email2;
    }

    /**
     * Set fax
     *
     * @param string $fax
     */
    public function setFax($fax) {
        $this->fax = $fax;
    }

    /**
     * Get fax
     *
     * @return string
     */
    public function getFax() {
        return $this->fax;
    }

    /**
     * Set contacto
     *
     * @param string $contacto
     */
    public function setContacto($contacto) {
        $this->contacto = $contacto;
    }

    /**
     * Get contacto
     *
     * @return string
     */
    public function getContacto() {
        return $this->contacto;
    }

    /**
     * Set info
     *
     * @param string $info
     */
    public function setInfo($info) {
        $this->info = $info;
    }

    /**
     * Get info
     *
     * @return string
     */
    public function getInfo() {
        return $this->info;
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
     * Set altaInicial
     *
     * @param string $altaInicial
     */
    public function setAltInicial($altaInicial) {
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
     * Set conflictivo
     *
     * @param string $conflictivo
     */
    public function setConflictivo($conflictivo) {
        $this->conflictivo = $conflictivo;
    }

    /**
     * Get conflictivo
     *
     * @return string
     */
    public function getConflictivo() {
        return $this->conflictivo;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     */
    public function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;
    }

    /**
     * Get observaciones
     *
     * @return string
     */
    public function getObservaciones() {
        return $this->observaciones;
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

    /**
     * Set pruebas
     *
     * @param string $pruebas
     */
    public function setPruebas($pruebas) {
        $this->pruebas = $pruebas;
    }

    /**
     * Get pruebas
     *
     * @return string
     */
    public function getPruebas() {
        return $this->pruebas;
    }

    /**
     * Set finPruebas
     *
     * @param string $finPruebas
     */
    public function setFinPruebas($finPruebas) {
        $this->finPruebas = $finPruebas;
    }

    /**
     * Get finPruebas
     *
     * @return string
     */
    public function getFinPruebas() {
        return $this->finPruebas;
    }

    /**
     * Set tipologia
     *
     * @param string $tipologia
     */
    public function setTipologia($tipologia) {
        $this->tipologia = $tipologia;
    }

    /**
     * Get tipologia
     *
     * @return string
     */
    public function getTipologia() {
        return $this->tipologia;
    }
}
