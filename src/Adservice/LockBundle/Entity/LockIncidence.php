<?php
namespace Adservice\LockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Adservice\LockBundle\Entity\lockIncidence
 *
 * @ORM\Table(name="lock_incidence")
 * @ORM\Entity
 */
class LockIncidence {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer $id_socio
     *
     * @ORM\Column(name="id_socio", type="integer")
     */
    private $id_socio;

    /**
     * @var integer $id_taller
     *
     * @ORM\Column(name="id_taller", type="integer")
     */
    private $id_taller;

    /**
     * @var string $asesor
     *
     * @ORM\Column(name="asesor", type="string", length=255, nullable=true)
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
     * @var string $coche
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\LockBundle\Entity\LockCar")
     */
    private $coche;

    /**
     * @var string $oper
     *
     * @ORM\Column(name="oper", type="string", length=255)
     */
    private $oper;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string $tracing
     *
     * @ORM\Column(name="tracing", type="string", length=255)
     */
    private $tracing;

    /**
     * @var string $solution
     *
     * @ORM\Column(name="solution", type="string", length=255)
     */
    private $solution;

    /**
     * @var string $importance
     *
     * @ORM\Column(name="importance", type="string", length=255)
     */
    private $importance;

    /**
     * @var string $date
     *
     * @ORM\Column(name="date", type="string", length=255)
     */
    private $date;

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
        return $this->getDescription();
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
     * Set id_socio
     *
     * @param integer $id_socio
     */
    public function setIdSocio($id_socio) {
        $this->id_socio = $id_socio;
    }

    /**
     * Get id_socio
     *
     * @return integer
     */
    public function getIdSocio() {
        return $this->id_socio;
    }

    /**
     * Set id_taller
     *
     * @param integer $id_taller
     */
    public function setIdTaller($id_taller) {
        $this->id_taller = $id_taller;
    }

    /**
     * Get id_taller
     *
     * @return integer
     */
    public function getIdTaller() {
        return $this->id_taller;
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
     * Set coche
     *
     * @param \Adservice\LockBundle\Entity\LockCar $coche
     */
    public function setCoche(\Adservice\LockBundle\Entity\LockCar $coche) {
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
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set tracing
     *
     * @param string $tracing
     */
    public function setTracing($tracing) {
        $this->tracing = $tracing;
    }

    /**
     * Get tracing
     *
     * @return string
     */
    public function getTracing() {
        return $this->tracing;
    }

    /**
     * Set solution
     *
     * @param string $solution
     */
    public function setSolution($solution) {
        $this->solution = $solution;
    }

    /**
     * Get solution
     *
     * @return string
     */
    public function getSolution() {
        return $this->solution;
    }

    /**
     * Set importance
     *
     * @param string $importance
     */
    public function setImportance($importance) {
        $this->importance = $importance;
    }

    /**
     * Get importance
     *
     * @return string
     */
    public function getImportance() {
        return $this->importance;
    }

    /**
     * Set date
     *
     * @param string $date
     */
    public function setDate($date) {
        $this->date = $date;
    }

    /**
     * Get date
     *
     * @return string
     */
    public function getDate() {
        return $this->date;
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
