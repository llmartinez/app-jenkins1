<?php
namespace Adservice\ImportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Adservice\ImportBundle\Entity\old_Coche
 *
 * @ORM\Table(name="coche")
 * @ORM\Entity
 */
class old_Coche {

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
     * @var string $idMMG
     *
     * @ORM\Column(name="idMMG", type="string", length=255)
     */
    private $idMMG;

    /**
     * @var string $ano
     *
     * @ORM\Column(name="ano", type="string", length=255)
     */
    private $ano;

    /**
     * @var string $bastidor
     *
     * @ORM\Column(name="bastidor", type="string", length=255)
     */
    private $bastidor;

    /**
     * @var string $motor
     *
     * @ORM\Column(name="motor", type="string", length=255)
     */
    private $motor;

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
     * Set idMMG
     *
     * @param string $idMMG
     */
    public function setIdMMG($idMMG) {
        $this->idMMG = $idMMG;
    }

    /**
     * Get idMMG
     *
     * @return string
     */
    public function getIdMMG() {
        return $this->idMMG;
    }

    /**
     * Set ano
     *
     * @param string $ano
     */
    public function setAno($ano) {
        $this->ano = $ano;
    }

    /**
     * Get ano
     *
     * @return string
     */
    public function getAno() {
        return $this->ano;
    }

    /**
     * Set bastidor
     *
     * @param string $bastidor
     */
    public function setBastidor($bastidor) {
        $this->bastidor = $bastidor;
    }

    /**
     * Get bastidor
     *
     * @return string
     */
    public function getBastidor() {
        return $this->bastidor;
    }

    /**
     * Set motor
     *
     * @param string $motor
     */
    public function setMotor($motor) {
        $this->motor = $motor;
    }

    /**
     * Get motor
     *
     * @return string
     */
    public function getMotor() {
        return $this->motor;
    }
}
