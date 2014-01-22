<?php

namespace Adservice\TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adservice\TicketBundle\Entity\Operacion
 *
 * @ORM\Table(name="operacion")
 * @ORM\Entity
 */
class Operacion
{
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
     * @var integer $groper
     * 
     * @ORM\ManyToOne(targetEntity="\Adservice\TicketBundle\Entity\Groper")
     */
    private $groper;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set groper
     * 
     * @param \Adservice\TicketBundle\Entity\Groper $groper
     */
    public function setGroper(\Adservice\TicketBundle\Entity\Groper $groper) {
        $this->groper = $groper;
    }

    public function getGroper() {
        return $this->groper;
    }
}