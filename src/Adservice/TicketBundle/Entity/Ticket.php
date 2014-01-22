<?php

namespace Adservice\TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adservice\TicketBundle\Entity\Ticket
 *
 * @ORM\Table(name="ticket")
 * @ORM\Entity
 */
class Ticket {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer $usuario
     * 
     * @ORM\ManyToOne(targetEntity="\Adservice\UsuarioBundle\Entity\Usuario")
     */
    private $usuario;

    /**
     * @var integer $operacion
     * 
     * @ORM\ManyToOne(targetEntity="\Adservice\TicketBundle\Entity\Operacion")
     */
    private $operacion;

    /**
     * @var integer $taller
     * 
     * @ORM\ManyToOne(targetEntity="\Adservice\TicketBundle\Entity\Taller")
     */
    private $taller;

    /**
     * @var integer $coche
     * 
     * @ORM\ManyToOne(targetEntity="\Adservice\TicketBundle\Entity\Coche")
     */
    private $coche;

    /**
     * @var integer $sistema
     * 
     * @ORM\ManyToOne(targetEntity="\Adservice\TicketBundle\Entity\Sistema")
     */
    private $sistema;

    /**
     * @var integer $archivo
     * 
     * @ORM\ManyToOne(targetEntity="\Adservice\TicketBundle\Entity\Archivo")
     */
    private $archivo;

    /**
     * @var integer $fecha
     * 
     * @ORM\Column(name="fecha", type="date")
     */
    private $fecha;

    /**
     * @var string $descripcion
     *
     * @ORM\Column(name="descripcion", type="string", length=255)
     */
    private $descripcion;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
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

    public function getUsuario() {
        return $this->usuario;
    }

    /**
     * Set usuario
     * 
     * @param \Adservice\UsuarioBundle\Entity\Usuario $usuario
     */
    public function setUsuario(\Adservice\UsuarioBundle\Entity\Usuario $usuario) {
        $this->usuario = $usuario;
    }

    public function getOperacion() {
        return $this->operacion;
    }

    /**
     * Set operacion
     * 
     * @param \Adservice\TicketBundle\Entity\Operacion $operacion
     */
    public function setOperacion(\Adservice\TicketBundle\Entity\Operacion $operacion) {
        $this->operacion = $operacion;
    }

    public function getTaller() {
        return $this->taller;
    }

    /**
     * Set taller
     * 
     * @param \Adservice\TicketBundle\Entity\Taller $taller
     */
    public function setTaller(\Adservice\TicketBundle\Entity\Taller $taller) {
        $this->taller = $taller;
    }

    public function getCoche() {
        return $this->coche;
    }

    /**
     * Set coche
     * 
     * @param \Adservice\TicketBundle\Entity\Coche $coche
     */
    public function setCoche(\Adservice\TicketBundle\Entity\Coche $coche) {
        $this->coche = $coche;
    }

    public function getSistema() {
        return $this->sistema;
    }

    /**
     * Set sistema
     * 
     * @param \Adservice\TicketBundle\Entity\Sistema $sistema
     */
    public function setSistema(\Adservice\TicketBundle\Entity\Sistema $sistema) {
        $this->sistema = $sistema;
    }

    public function getArchivo() {
        return $this->archivo;
    }

    /**
     * Set archivo
     * 
     * @param \Adservice\TicketBundle\Entity\Archivo $archivo
     */
    public function setArchivo(\Adservice\TicketBundle\Entity\Archivo $archivo) {
        $this->archivo = $archivo;
    }

}