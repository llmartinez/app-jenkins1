<?php

namespace Adservice\UsuarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adservice\UsuarioBundle\Entity\Usuario
 *
 * @ORM\Table(name="usuario")
 * @ORM\Entity
 */
class Usuario
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
     * @var string $rol
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\UsuarioBundle\Entity\Rol")
     */
    private $rol;

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
     * Set rol
     *
     * @param \Adservice\UsuarioBundle\Entity\Rol $rol
     */
    public function setRol(\Adservice\UsuarioBundle\Entity\Rol $rol)
    {
        $this->rol = $rol;
    }

    /**
     * Get rol
     *
     * @return string 
     */
    public function getRol()
    {
        return $this->rol;
    }
}