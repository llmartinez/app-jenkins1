<?php

namespace Adservice\TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adservice\TicketBundle\Entity\Coche
 *
 * @ORM\Table(name="coche")
 * @ORM\Entity
 */
class Coche
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
     * @var integer $gama
     * 
     * @ORM\ManyToOne(targetEntity="\Adservice\TicketBundle\Entity\Gama")
     */
    private $gama;


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
     * Set gama
     * 
     * @param \Adservice\TicketBundle\Entity\Gama $gama
     */
    public function setGama(\Adservice\TicketBundle\Entity\Gama $gama)
    {
        $this->gama = $gama;
    }

    /**
     * Get gama
     *
     * @return string 
     */
    public function getGama()
    {
        return $this->gama;
    }
}