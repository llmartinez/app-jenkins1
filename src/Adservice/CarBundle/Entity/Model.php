<?php

namespace Adservice\CarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adservice\CarBundle\Entity\Model
 *
 * @ORM\Table(name="model")
 * @ORM\Entity
 */
class Model {
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var integer $brand
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\CarBundle\Entity\Brand")
     */
    private $brand;

    /**
     * @var string $version
     *
     * @ORM\OneToMany(targetEntity="Adservice\CarBundle\Entity\Version", mappedBy="model")
     */
    private $version;

    /**
     * @var integer $idTecDoc
     *
     * @ORM\Column(name="idTecDoc", type="integer")
     */
    private $idTecDoc;


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

    /**
     * Set idTecDoc
     *
     * @param integer $idTecDoc
     */
    public function setIdTecDoc($idTecDoc)
    {
        $this->idTecDoc = $idTecDoc;
    }

    /**
     * Get idTecDoc
     *
     * @return integer
     */
    public function getIdTecDoc()
    {
        return $this->idTecDoc;
    }

    public function __toString() {
        return $this->name;
    }

//    public function jsonSerialize() {
//        return [
//            'id' => $this->getId(),
//            'name' => $this->getName()
//        ];
//    }

    public function to_json(){
        $json = array('id'  => $this->getId(),
                      'name'=> $this->getName());
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