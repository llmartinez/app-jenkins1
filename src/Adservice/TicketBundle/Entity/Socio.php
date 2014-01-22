<?php

namespace Adservice\TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adservice\TicketBundle\Entity\Socio
 *
 * @ORM\Table(name="socio")
 * @ORM\Entity
 */
class Socio
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
     * @var string $pedidoelec
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\TicketBundle\Entity\Pedidoelec")
     */
    private $pedidoelec;


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
     * Set pedidoelec
     * 
     * @param \Adservice\TicketBundle\Entity\Socio $pedidoelec
     */
    public function setPedidoelec(\Adservice\TicketBundle\Entity\Pedidoelec $pedidoelec) {
        $this->pedidoelec = $pedidoelec;
    }

    public function getPedidoelec() {
        return $this->pedidoelec;
    }
}