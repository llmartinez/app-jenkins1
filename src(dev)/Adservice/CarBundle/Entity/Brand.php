<?php

namespace Adservice\CarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adservice\CarBundle\Entity\Brand
 *
 * @ORM\Table(name="Marca_Vehiculo")
 * @ORM\Entity
 */
class Brand {
    /**
     * @var integer $id
     *
     * @ORM\Column(name="Marca", type="integer")
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
     * @var string $models
     *
     * @ORM\OneToMany(targetEntity="Adservice\CarBundle\Entity\Model", mappedBy="brand")
     * @ORM\JoinColumn(name="id", referencedColumnName="Modelo")
     */
    private $models;


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
        $this->models = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add models
     *
     * @param Adservice\CarBundle\Entity\Model $models
     */
    public function addModel(\Adservice\CarBundle\Entity\Model $models)
    {
        $this->models[] = $models;
    }

    /**
     * Get models
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getModels()
    {
        return $this->models;
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