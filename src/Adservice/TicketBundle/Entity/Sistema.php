<?php

namespace Adservice\TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adservice\TicketBundle\Entity\Sistema
 *
 * @ORM\Table(name="sistema")
 * @ORM\Entity
 */
class Sistema
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
     * @var string $subsistema
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\TicketBundle\Entity\Subsistema")
     */
    private $subsistema;


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
     * Set subsistema
     *
     * @param \Adservice\TicketBundle\Entity\Subsistema $subsistema
     */
    public function setSubsistema(\Adservice\TicketBundle\Entity\Subsistema $subsistema)
    {
        $this->subsistema = $subsistema;
    }

    /**
     * Get subsistema
     *
     * @return string 
     */
    public function getSubsistema()
    {
        return $this->subsistema;
    }
}