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
     * @ORM\ManyToOne(targetEntity="\Adservice\CarBundle\Entity\Brand", inversedBy="models")
     * @ORM\JoinColumn(name="marca", referencedColumnName="Marca")
     */
    private $brand;

    /**
     * @var string $inicio
     *
     * @ORM\Column(name="Inicio", type="string", length=6, nullable=true)
     */
    private $inicio;

    /**
     * @var string $fin
     *
     * @ORM\Column(name="Fin", type="string", length=6, nullable=true)
     */
    private $fin;

    /**
     * @var string $utilitario
     *
     * @ORM\Column(name="Utilitario", type="boolean")
     */
    private $utilitario;

    /**
     * @var string $comercial
     *
     * @ORM\Column(name="Comercial", type="boolean")
     */
    private $comercial;

    /**
     * @var string $version
     *
     * @ORM\OneToMany(targetEntity="Adservice\CarBundle\Entity\Version", mappedBy="model")
     * @ORM\JoinColumn(name="version", referencedColumnName="Version")
     */
    private $version;


    public function __construct()
    {
        $this->version = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString() {
        return $this->name;
    }

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
     * Set inicio
     *
     * @param string $inicio
     */
    public function setInicio($inicio)
    {
        $this->inicio = $inicio;
    }

    /**
     * Get inicio
     *
     * @return string
     */
    public function getInicio()
    {
        return $this->inicio;
    }

    /**
     * Set fin
     *
     * @param string $fin
     */
    public function setFin($fin)
    {
        $this->fin = $fin;
    }

    /**
     * Get fin
     *
     * @return string
     */
    public function getFin()
    {
        return $this->fin;
    }

    /**
     * Get dateInicio
     *
     * @return string
     */
    public function getDateInicio()
    {
        $inicio = '01-'.substr ($this->inicio, -2).'-'.substr ($this->inicio, 0, -2); //ej. 01-01-2015
        return $this->version;
    }
    /**
     * Get dateFin
     *
     * @return string
     */
    public function getDateFin()
    {
        $fin    = '31-'.substr ($this->fin, -2).'-'.substr ($this->fin, 0, -2); //ej. 31-12-2015
        return $this->version;
    }

    /**
     * Set utilitario
     *
     * @param string $utilitario
     */
    public function setUtilitario($utilitario)
    {
        $this->utilitario = $utilitario;
    }

    /**
     * Get utilitario
     *
     * @return string
     */
    public function getUtilitario()
    {
        return $this->utilitario;
    }

    /**
     * Set comercial
     *
     * @param string $comercial
     */
    public function setComercial($comercial)
    {
        $this->comercial = $comercial;
    }

    /**
     * Get comercial
     *
     * @return string
     */
    public function getComercial()
    {
        return $this->comercial;
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

    public function to_json(){
        $json = array('id'          => $this->getId(),
                      'name'        => $this->getName(),
                      'inicio'      => $this->getInicio(),
                      'fin'         => $this->getFin(),
                      'utilitario'  => $this->getUtilitario(),
                      'comercial'   => $this->getComercial(),
                      'brand'       => $this->getBrand()->getId());
        return $json;
    }
}