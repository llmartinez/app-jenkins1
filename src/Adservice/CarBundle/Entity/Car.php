<?php

namespace Adservice\CarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adservice\CarBundle\Entity\Car
 *
 * @ORM\Table(name="car")
 * @ORM\Entity
 */
class Car
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
     * @var integer $brand
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\CarBundle\Entity\Brand")
     */
    private $brand;

    /**
     * @var integer $model
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\CarBundle\Entity\Model")
     */
    private $model;

    /**
     * @var integer $version
     * @ORM\ManyToOne(targetEntity="\Adservice\CarBundle\Entity\Version")
     */
    private $version;

    /**
     * @var integer $year
     *
     * @ORM\Column(name="year", type="string", length=255, nullable=true)
     */
    private $year;

    /**
     * @var integer $motor
     *
     * @ORM\Column(name="motor", type="string", length=255, nullable=true)
     */
    private $motor;

    /**
     * @var integer $kW
     *
     * @ORM\Column(name="kW", type="decimal", nullable=true)
     */
    private $kW;

    /**
     * @var integer $displacement
     *
     * @ORM\Column(name="displacement", type="decimal", nullable=true)
     */
    private $displacement;

    /**
     * @var string $vin
     *
     * @ORM\Column(name="vin", type="string", length=255, nullable=true)
     */
    private $vin;

    /**
     * @var string $plateNumber
     *
     * @ORM\Column(name="plateNumber", type="string", length=255, nullable=true)
     */
    private $plateNumber;

    /**
     * @var date $created_at
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $created_at;

    /**
     * @var string $created_by
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\UserBundle\Entity\User")
     */
    private $created_by;

    /**
     * @var date $modified_at
     *
     * @ORM\Column(name="modified_at", type="datetime")
     */
    private $modified_at;

    /**
     * @var string $modified_by
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\UserBundle\Entity\User")
     */
    private $modified_by;

    /**
     * @var integer $ticket
     *
     * @ORM\OneToMany(targetEntity="\Adservice\TicketBundle\Entity\Ticket", mappedBy="car")
     */
    private $ticket;

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
     * Set brand
     *
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
     * Set model
     *
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
     * Set version
     *
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * Get version
     *
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set year
     *
     * @param integer $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * Get year
     *
     * @return integer
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set motor
     *
     * @param integer $motor
     */
    public function setMotor($motor)
    {
        $this->motor = $motor;
    }

    /**
     * Get motor
     *
     * @return integer
     */
    public function getMotor()
    {
        return $this->motor;
    }

    /**
     * Set kW
     *
     * @param integer $kW
     */
    public function setKW($kW)
    {
        $this->kW = $kW;
    }

    /**
     * Get kW
     *
     * @return integer
     */
    public function getKW()
    {
        return $this->kW;
    }

    /**
     * Set displacement
     *
     * @param integer $displacement
     */
    public function setDisplacement($displacement)
    {
        $this->displacement = $displacement;
    }

    /**
     * Get displacement
     *
     * @return integer
     */
    public function getDisplacement()
    {
        return $this->displacement;
    }

    /**
     * Set vin
     *
     * @param string $vin
     */
    public function setVin($vin)
    {
        $this->vin = $vin;
    }

    /**
     * Get vin
     *
     * @return string
     */
    public function getVin()
    {
        return $this->vin;
    }

    /**
     * Set plateNumber
     *
     * @param string $plateNumber
     */
    public function setPlateNumber($plateNumber)
    {
        $this->plateNumber = $plateNumber;
    }

    /**
     * Get plateNumber
     *
     * @return string
     */
    public function getPlateNumber()
    {
        return $this->plateNumber;
    }

    /**
     * Set created_at
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    }

    /**
     * Get created_at
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set created_by
     *
     * @param \Adservice\UserBundle\Entity\User $created_by
     */
    public function setCreatedBy(\Adservice\UserBundle\Entity\User $created_by)
    {
        $this->created_by = $created_by;
    }

    /**
     * Get created_by
     *
     * @return string
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     * Set modified_at
     *
     * @param datetime $modifiedAt
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modified_at = $modifiedAt;
    }

    /**
     * Get modified_at
     *
     * @return datetime
     */
    public function getModifiedAt()
    {
        return $this->modified_at;
    }

    /**
     * Set modified_by
     *
     * @param \Adservice\UserBundle\Entity\User $modified_by
     */
    public function setModifiedBy(\Adservice\UserBundle\Entity\User $modified_by)
    {
        $this->modified_by = $modified_by;
    }

    /**
     * Get modified_by
     *
     * @return string
     */
    public function getModifiedBy()
    {
        return $this->modified_by;
    }

    /**
     * Set ticket
     *
     * @param \Adservice\TicketBundle\Entity\Ticket $ticket
     */
    public function setTicket(\Adservice\TicketBundle\Entity\Ticket $ticket) {
        $this->ticket = $ticket;
    }

    /**
     * Get ticket
     *
     * @return integer
     */
    public function getTicket() {
        return $this->ticket;
    }

    public function __toString() {

        $model   = $this->getModel();
        $brand   = $this->getBrand();
        $version = $this->getVersion();
        return $brand.' '.$model.' '.$version;
    }

}