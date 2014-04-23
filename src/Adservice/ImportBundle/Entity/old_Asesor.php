<?php
namespace Adservice\ImportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Adservice\ImportBundle\Entity\old_Asesor
 *
 * @ORM\Table(name="asesor")
 * @ORM\Entity
 */
class old_Asesor {

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
     * @var integer $privilegios
     *
     * @ORM\Column(name="privilegios", type="integer", length=255)
     */
    private $privilegios;

    /**
     * @var boolean $active
     *
     * @ORM\Column(name="active", type="boolean", length=255)
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
     * Set privilegios
     *
     * @param integer $privilegios
     */
    public function setPrivilegios($privilegios) {
        $this->privilegios = $privilegios;
    }

    /**
     * Get privilegios
     *
     * @return integer
     */
    public function getPrivilegios() {
        return $this->privilegios;
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
