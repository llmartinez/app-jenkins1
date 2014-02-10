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
     * @ORM\Column(name="year", type="integer", length=4)
     */
    private $year;

    /**
     * @var string $vin
     *
     * @ORM\Column(name="vin", type="string", length=255)
     */
    private $vin;

    /**
     * @var string $plateNumber
     *
     * @ORM\Column(name="plateNumber", type="string", length=255)
     */
    private $plateNumber;

    /**
     * @var string $owner
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\UserBundle\Entity\User")
     */
    private $owner;

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
     * Set owner
     *
     * @param \Adservice\UserBundle\Entity\User $owner
     */
    public function setOwner(\Adservice\UserBundle\Entity\User $owner)
    {
        $this->owner = $owner;
    }

    /**
     * Get owner
     *
     * @return string 
     */
    public function getOwner()
    {
        return $this->owner;
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
        
        return $this->plateNumber;
       /* $version = $this->version;
        $model = $version->getModel();
        $brand = $model->getBrand();
        $year = $this->year;
        $car = $brand.' '.$model.' '.$version;
        
        return $car;*/
    }

}