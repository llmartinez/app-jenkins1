<?php

namespace Adservice\CarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adservice\CarBundle\Entity\Model
 *
 * @ORM\Table(name="Modelo_Vehiculo")
 * @ORM\Entity
 */
class Model {
    /**
     * @var integer $id
     *
     * @ORM\Column(name="Modelo", type="integer")
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
     * @var integer $brand
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\CarBundle\Entity\Brand")
     * @ORM\JoinColumn(name="marca", referencedColumnName="Marca")
     */
    private $brand;

    /**
     * @var string $version
     *
     * @ORM\OneToMany(targetEntity="Adservice\CarBundle\Entity\Version", mappedBy="model")
     * @ORM\JoinColumn(name="version", referencedColumnName="Version")
     */
    private $version;

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

    /**
     * Set brand
     *
     * @param \Adservice\CarBundle\Entity\Brand $brand
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;
    }

    /**
     * Get brand
     *
     * @return integer
     */
    public function getBrand()
    {
        return $this->brand;
    }

    public function __toString() {
        return $this->name;
    }

    public function to_json(){
        $json = array('id'      => $this->getId(),
                      'name'    => $this->getName(),
                      'brand'   => $this->getBrand()->getId());
        return $json;
    }

    public function __construct()
    {
        $this->version = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add version
     *
     * @param Adservice\CarBundle\Entity\Version $version
     */
    public function addVersion(\Adservice\CarBundle\Entity\Version $version)
    {
        $this->version[] = $version;
    }

    /**
     * Get version
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getVersion()
    {
        return $this->version;
    }
}