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
     * @var integer $version
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\CarBundle\Entity\Version")
     */
    private $version;

    /**
     * @var integer $year
     *
     * @ORM\Column(name="year", type="integer")
     */
    private $year;

    /**
     * @var integer $vin
     *
     * @ORM\Column(name="vin", type="integer")
     */
    private $vin;

    /**
     * @var integer $plateNumber
     *
     * @ORM\Column(name="plateNumber", type="integer")
     */
    private $plateNumber;

    /**
     * @var string $created_by
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\UserBundle\Entity\User")
     */
    private $created_by;

    /**
     * @var date $created_at
     *
     * @ORM\Column(name="date_created", type="date")
     */
    private $created_at;

    /**
     * @var date $modified_at
     *
     * @ORM\Column(name="date_modified", type="date")
     */
    private $modified_at;

    /**
     * @var string $modified_by
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\UserBundle\Entity\User")
     */
    private $modified_by;
    
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
     * Set version
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\CarBundle\Entity\Version")
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
     * Set vin
     *
     * @param integer $vin
     */
    public function setVin($vin)
    {
        $this->vin = $vin;
    }

    /**
     * Get vin
     *
     * @return integer 
     */
    public function getVin()
    {
        return $this->vin;
    }

    /**
     * Set plateNumber
     *
     * @param integer $plateNumber
     */
    public function setPlateNumber($plateNumber)
    {
        $this->plateNumber = $plateNumber;
    }

    /**
     * Get plateNumber
     *
     * @return integer 
     */
    public function getPlateNumber()
    {
        return $this->plateNumber;
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
    
    public function __toString() {
        $version = $this->getVersion();
        $model = $this->getVersion()->getModel();
        $brand = $model->getBrand();
        $year = $this->year;
        $car = $brand.' '.$model.' '.$version;
        
        return $car;
    }

}