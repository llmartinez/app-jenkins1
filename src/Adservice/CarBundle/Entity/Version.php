<?php

namespace Adservice\CarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adservice\CarBundle\Entity\Version
 *
 * @ORM\Table(name="version")
 * @ORM\Entity
 */
class Version {
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
     * @var integer $model
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\CarBundle\Entity\Model")
     */
    private $model;

    /**
     * @var integer $idTecDoc
     *
     * @ORM\Column(name="idTecDoc", type="integer")
     */
    private $idTecDoc;

    /**
     * @var string $year
     *
     * @ORM\Column(name="year", type="string", length=255)
     */
    private $year;

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
     * @var string $displacement
     *
     * @ORM\Column(name="displacement", type="string", length=255)
     */
    private $displacement;


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
     * @ORM\ManyToOne(targetEntity="\Adservice\CarBundle\Entity\Model")
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

    /**
     * Set year
     *
     * @param string $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * Get year
     *
     * @return string
     */
    public function getYear()
    {
        return $this->year;
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

    /**
     * Set displacement
     *
     * @param string $displacement
     */
    public function setDisplacement($displacement)
    {
        $this->displacement = $displacement;
    }

    /**
     * Get displacement
     *
     * @return string
     */
    public function getDisplacement()
    {
        return $this->displacement;
    }


//    public function jsonSerialize() {
//        return [
//            'id' => $this->getId(),
//            'name' => $this->getName()
//        ];
//    }

    public function __toString() {
        return $this->name;
    }

    public function to_json(){
        $json = array('id'           => $this->getId(),
                      'name'         => $this->getName(),
                      'year'         => $this->getYear(),
                      'motor'        => $this->getMotor(),
                      'kw'           => $this->getKw(),
                      'displacement' => $this->getDisplacement(),
                      'idTecDoc'     => $this->getIdTecDoc());
        return $json;
    }
}