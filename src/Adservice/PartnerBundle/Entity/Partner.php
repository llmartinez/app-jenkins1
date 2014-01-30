<?php

namespace Adservice\PartnerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

use Adservice\UserBundle\Entity\User;
use Adservice\UtilBundle\Entity\Region;
use Adservice\UtilBundle\Entity\Province;

/**
 * Adservice\PartnerBundle\Entity\Partner
 *
 * @ORM\Table(name="partner")
 * @ORM\Entity
 */
class Partner{
    
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
     * @var integer $phone_number_1
     *
     * @ORM\Column(name="phone_number_1", type="integer")
     */
    private $phone_number_1;

    /**
     * @var integer $phone_number_2
     *
     * @ORM\Column(name="phone_number_2", type="integer")
     */
    private $phone_number_2;

    /**
     * @var string $email_1
     *
     * @ORM\Column(name="email_1", type="string", length=255)
     */
    private $email_1;

    /**
     * @var string $email_2
     *
     * @ORM\Column(name="email_2", type="string", length=255)
     */
    private $email_2;

    /**
     * @var integer $fax
     *
     * @ORM\Column(name="fax", type="integer")
     */
    private $fax;

    /**
     * @var string $address
     *
     * @ORM\Column(name="address", type="string", length=255)
     */
    private $address;

    /**
     * @var integer $postal_code
     *
     * @ORM\Column(name="postal_code", type="integer")
     */
    private $postal_code;

    /**
     * @var string $region
     *
     * @ORM\ManyToOne(targetEntity="Adservice\UtilBundle\Entity\Region")
     */
    private $region;
    
    /**
     * @var string $province
     *
     * @ORM\ManyToOne(targetEntity="Adservice\UtilBundle\Entity\Province")
     */
    private $province;
    
    /**
     * @var boolean $active
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var datetime $created_at
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $created_at;

    /**
     * @var datetime $modified_at
     *
     * @ORM\Column(name="modified_at", type="datetime")
     */
    private $modified_at;

    /**
     * @var integer $modify_by
     *
     * @ORM\ManyToOne(targetEntity="Adservice\UserBundle\Entity\User")
     */
    private $modify_by;


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
     * Set phone_number_1
     *
     * @param integer $phoneNumber1
     */
    public function setPhoneNumber1($phoneNumber1)
    {
        $this->phone_number_1 = $phoneNumber1;
    }

    /**
     * Get phone_number_1
     *
     * @return integer 
     */
    public function getPhoneNumber1()
    {
        return $this->phone_number_1;
    }

    /**
     * Set phone_number_2
     *
     * @param integer $phoneNumber2
     */
    public function setPhoneNumber2($phoneNumber2)
    {
        $this->phone_number_2 = $phoneNumber2;
    }

    /**
     * Get phone_number_2
     *
     * @return integer 
     */
    public function getPhoneNumber2()
    {
        return $this->phone_number_2;
    }

    /**
     * Set email_1
     *
     * @param string $email1
     */
    public function setEmail1($email1)
    {
        $this->email_1 = $email1;
    }

    /**
     * Get email_1
     *
     * @return string 
     */
    public function getEmail1()
    {
        return $this->email_1;
    }

    /**
     * Set email_2
     *
     * @param string $email2
     */
    public function setEmail2($email2)
    {
        $this->email_2 = $email2;
    }

    /**
     * Get email_2
     *
     * @return string 
     */
    public function getEmail2()
    {
        return $this->email_2;
    }

    /**
     * Set fax
     *
     * @param integer $fax
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
    }

    /**
     * Get fax
     *
     * @return integer 
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set address
     *
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set postal_code
     *
     * @param integer $postalCode
     */
    public function setPostalCode($postalCode)
    {
        $this->postal_code = $postalCode;
    }

    /**
     * Get postal_code
     *
     * @return integer 
     */
    public function getPostalCode()
    {
        return $this->postal_code;
    }

    /**
     * Set region
     *
     * @param string $region
     */
    public function setRegion(\Adservice\UtilBundle\Entity\Region $region) {
        $this->region = $region;
    }

    /**
     * Get region
     *
     * @return string 
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set province
     *
     * @param string $province
     */
    public function setProvince(\Adservice\UtilBundle\Entity\Province $province) {
        $this->province = $province;
    }

    /**
     * Get province
     *
     * @return string 
     */
    public function getProvince()
    {
        return $this->province;
    }
    
    /**
     * Set active
     *
     * @param boolean $active
     */
    public function setActive($active) {
        $this->active = $active;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive() {
        return $this->active;
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
    public function setModifiedAt($modifiedAt) {            
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
     * Set modify_by
     *
     * @param user $modify_by
     */
    public function setModifyBy(\Adservice\UserBundle\Entity\User $user)
    {
        $this->modify_by = $user;
    }

    /**
     * Get modify_by
     *
     * @return integer 
     */
    public function getModifyBy()
    {
        return $this->modify_by;
    }
    
    public function __toString() {
        return $this->getName();
    }
}