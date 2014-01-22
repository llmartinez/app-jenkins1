<?php

namespace Adservice\TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adservice\TicketBundle\Entity\Taller
 *
 * @ORM\Table(name="taller")
 * @ORM\Entity
 */
class Taller
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
     * @var integer $socio
     * 
     * @ORM\ManyToOne(targetEntity="\Adservice\TicketBundle\Entity\Socio")
     */
    private $socio;


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
     * Set socio
     * 
     * @param \Adservice\TicketBundle\Entity\Socio $socio
     */
    public function setSocio(\Adservice\TicketBundle\Entity\Socio $socio) {
        $this->socio = $socio;
    }

    public function getSocio() {
        return $this->socio;
    }
    
}