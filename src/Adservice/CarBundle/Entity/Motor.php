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

    public function to_json(){
        $json = array('id'  => $this->getId(),
                      'name'=> $this->getName());
        return $json;
    }

    public function __toString() {
        return $this->name;
    }
}