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
     * @ORM\JoinColumn(name="brand_id", referencedColumnName="Marca")
     */
    private $brand;

    /**
     * @var integer $model
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\CarBundle\Entity\Model")
     * @ORM\JoinColumn(name="model_id", referencedColumnName="Modelo")
     */
    private $model;

    /**
     * @var integer $version
     * @ORM\ManyToOne(targetEntity="\Adservice\CarBundle\Entity\Version")
     * @ORM\JoinColumns({
     *          @ORM\JoinColumn(name="version_id", referencedColumnName="Version"),
     *          @ORM\JoinColumn(name="motor_id", referencedColumnName="Motor")
     *     })

     */
    private $version;

    /**
     * @var integer $year
     *
     * @ORM\Column(name="year", type="string", length=255, nullable=true)
     */
    private $year;

    /**
     * @var string $motor
     * @ORM\Column(name="motor", type="string", length=255)
     */
    private $motor;

    /**
     * @var integer $motorId
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\CarBundle\Entity\Motor")
     * @ORM\JoinColumn(name="motor_id", referencedColumnName="Motor")
     */
    private $motorId;

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
     * @ORM\JoinColumn(name="car_id", referencedColumnName="Car")
     */
    private $ticket;

    /**
     * @var string $origin
     *
     * @ORM\Column(name="origin", type="string", length=50, nullable=true)
     */
    private $origin;


     /**
      * @var integer $variants
      *
      * @ORM\Column(name="variants", type="integer", nullable=true)
      */
    private $variants;

    /**
     * @var string $status
     *
     * @ORM\Column(name="status", type="string", length=50, nullable=true)
     */
    private $status;


    /**
     * Set id
     *
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return int
     */
    public function getMotorId()
    {
        return $this->motorId;
    }

    /**
     * @param int $motorId
     */
    public function setMotorId($motorId)
    {
        $this->motorId = $motorId;
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
    // public function setTicket(\Adservice\TicketBundle\Entity\Ticket $ticket) {
    //     $this->ticket = $ticket;
    // }

    /**
     * Get ticket
     *
     * @return integer
     */
    // public function getTicket() {
    //     return $this->ticket;
    // }

    /**
     * @return string
     */

    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * @param string $origin
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;
    }

    /**
     * @return int
     */
    public function getVariants()
    {
        return $this->variants;
    }

    /**
     * @param int $variants
     */
    public function setVariants($variants)
    {
        $this->variants = $variants;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function isSameCar(Car $otherCar)
    {
        return ($otherCar->getPlateNumber() == $this->getPlateNumber() &&
                $otherCar->getBrand() == $this->getBrand() &&
                $otherCar->getModel() == $this->getMotor() &&
                $otherCar->getVersion() == $this->getVersion() &&
                $otherCar->getMotor() == $this->getMotor());
    }

    public function __toString() {

        $model   = $this->getModel()->getName();
        $brand   = $this->getBrand()->getName();
        if (isset($version)) $version = $this->getVersion()->getName();
        else $version = '';
        return $brand.' '.$model.' '.$version;
    }

     public function to_json(){
         $version = null;
         if($this->getVersion() != null){
             $version = $this->getVersion()->getId();
         }
        $json = array('brandId'             => $this->getModel()->getBrand()->getId(),
                      'modelId'             => $this->getModel()->getId(),
                      'versionId'           => $version,
                      'year'                => $this->getYear(),
                      'motor'               => $this->getMotor(),
                      'kw'                  => $this->getKw(),
                      'cm3'                 => $this->getDisplacement(),
                      'vin'                 => $this->getVin(),
                      'plateNumber'         => $this->getPlateNumber(),
            'carDescription'        => $this->toStringExtended(),
            'origin'                => $this->getOrigin(),
            'variants'              => $this->getVariants(),
            'status'                => $this->getStatus()
            );

        return $json;
    }

    public function toStringExtended(){

        return $this->__toString().' '.$this->getYear().' '.$this->getMotor().' '.$this->getKw().'kw '.$this->getDisplacement().'cm3';
    }
}