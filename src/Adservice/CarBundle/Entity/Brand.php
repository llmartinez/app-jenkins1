<?php

namespace Adservice\CarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adservice\CarBundle\Entity\Brand
 *
 * @ORM\Table(name="Marca_Vehiculo")
 * @ORM\Entity(repositoryClass="Adservice\CarBundle\Repository\BrandRepository")
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
     * @var string $createdAt
     * @ORM\Column(name="created_at", type="string", length=255, nullable=true)
     */
    private $createdAt;

    /**
     * @var string $modifiedAt
     * @ORM\Column(name="modified_at", type="string", length=255, nullable=true)
     */
    private $modifiedAt;

    /**
     * @var boolean $idRecycled
     * @ORM\Column(name="id_recycled", type="boolean", nullable=true)
     */
    private $idRecycled;


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

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param string $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * @param string $modifiedAt
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;
    }

    /**
     * @return boolean
     */
    public function isIdRecycled()
    {
        return $this->idRecycled;
    }

    /**
     * @param boolean $idRecycled
     */
    public function setIdRecycled($idRecycled)
    {
        $this->idRecycled = $idRecycled;
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