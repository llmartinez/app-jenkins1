<?php

namespace Adservice\CarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adservice\CarBundle\Entity\Version
 *
 * @ORM\Table(name="Version_Vehiculo")
 * @ORM\Entity
 */
class Version {
    /**
     * @var integer $id
     *
     * @ORM\Column(name="Version", type="integer")
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
     * @var integer $model
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\CarBundle\Entity\Model")
     * @ORM\JoinColumn(name="modelo", referencedColumnName="Modelo")
     */
    private $model;


    /**
     * @var string $motor
     *
     * @ORM\Column(name="motor", type="string", length=255)
     */
    private $motor;

    /**
     * @var string $kw
     *
     * @ORM\Column(name="kw", type="string", length=255)
     */
    private $kw;


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
     * Set model
     *
     * @param \Adservice\CarBundle\Entity\Model $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * Get model
     *
     * @return integer
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set motor
     *
     * @param string $motor
     */
    public function setMotor($motor)
    {
        $this->motor = $motor;
    }

    /**
     * Get motor
     *
     * @return string
     */
    public function getMotor()
    {
        return $this->motor;
    }

    /**
     * Set kw
     *
     * @param string $kw
     */
    public function setKw($kw)
    {
        $this->kw = $kw;
    }

    /**
     * Get kw
     *
     * @return string
     */
    public function getKw()
    {
        return $this->kw;
    }


    public function __toString() {
        return $this->name;
    }

    public function to_json(){
        $json = array('id'           => $this->getId(),
                      'name'         => $this->getName(),
                      'year'         => '',
                      'motor'        => $this->getMotor(),
                      'kw'           => $this->getKw(),
                      'displacement' => '',
                      'model'        => $this->getModel()->getId(),
                      'brand'        => $this->getModel()->getBrand()->getId());
        return $json;
    }
}