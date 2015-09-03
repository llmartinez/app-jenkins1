<?php

namespace Adservice\CarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adservice\CarBundle\Entity\Motor
 *
 * @ORM\Table(name="Motor_Vehiculo")
 * @ORM\Entity
 */
class Motor {
    /**
     * @var integer $id
     *
     * @ORM\Column(name="Motor", type="integer")
     * @ORM\Id
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="Descripcion", type="string", length=255)
     */
    private $name;

    /**
     * @var string $versiones
     *
     * @ORM\OneToMany(targetEntity="Adservice\CarBundle\Entity\Version", mappedBy="motor")
     * @ORM\JoinColumn(name="version", referencedColumnName="Version")
     */
    private $versiones;


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
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function __construct()
    {
        $this->versiones = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add versiones
     *
     * @param Adservice\CarBundle\Entity\Version $versiones
     */
    public function addVersion(\Adservice\CarBundle\Entity\Version $versiones)
    {
        $this->versiones[] = $versiones;
    }

    /**
     * Get versiones
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getVersiones()
    {
        return $this->versiones;
    }

    public function to_json(){
        $json = array('id'  => $this->getId(),
                      'name'=> $this->getName());
        return $json;
    }

    public function __toString() {
        return $this->name;
    }
}