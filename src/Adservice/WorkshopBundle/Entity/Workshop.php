<?php

namespace Adservice\WorkshopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Adservice\PartnerBundle\Entity\Partner;
use Adservice\WorkshopBundle\Entity\Typology;
use Adservice\UtilBundle\Entity\Region;
use Adservice\UtilBundle\Entity\Province;

/**
 * Adservice\WorkshopBundle\Entity\Workshop
 *
 * @ORM\Table(name="workshop")
 * @ORM\Entity
 */
class Workshop
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
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string $address
     *
     * @ORM\Column(name="address", type="string", length=255)
     */
    private $address;

    /**
     * @var string $city
     *
     * @ORM\Column(name="city", type="string", length=255)
     */
    private $city;

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
     * @var integer $movile_phone_1
     *
     * @ORM\Column(name="movile_phone_1", type="integer")
     */
    private $movile_phone_1;

    /**
     * @var integer $movile_phone_2
     *
     * @ORM\Column(name="movile_phone_2", type="integer")
     */
    private $movile_phone_2;

    /**
     * @var integer $fax
     *
     * @ORM\Column(name="fax", type="integer")
     */
    private $fax;

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
     * @var string $contact
     *
     * @ORM\Column(name="contact", type="string", length=255)
     */
    private $contact;

    /**
     * @var string $observations
     *
     * @ORM\Column(name="observations", type="string", length=255)
     */
    private $observations;

    /**
     * @var string $partner
     * 
     * @ORM\ManyToOne(targetEntity="Adservice\PartnerBundle\Entity\Partner", inversedBy="workshops")
     */
    private $partner;
    
    /**
     *
     * @var string $users 
     * 
     * @ORM\OneToMany(targetEntity="Adservice\UserBundle\Entity\User", mappedBy="workshop")
     */
    private $users;

    /**
     * @var boolean $active
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var boolean $adservice_plus
     *
     * @ORM\Column(name="adservice_plus", type="boolean")
     */
    private $adservice_plus;

    /**
     * @var boolean $test
     *
     * @ORM\Column(name="test", type="boolean")
     */
    private $test;

    /**
     * @var string $typology
     *
     * @ORM\ManyToOne(targetEntity="Adservice\WorkshopBundle\Entity\Typology")
     */
    private $typology;

    /**
     * @var datetime $update_at
     *
     * @ORM\Column(name="update_at", type="datetime")
     */
    private $update_at;

    /**
     * @var datetime $lowdate_at
     *
     * @ORM\Column(name="lowdate_at", type="datetime")
     */
    private $lowdate_at;

    /**
     * @var datetime $endtest_at
     *
     * @ORM\Column(name="endtest_at", type="datetime")
     */
    private $endtest_at;

    /**
     * @var boolean $conflictive
     *
     * @ORM\Column(name="conflictive", type="boolean")
     */
    private $conflictive;

    /**
     * @var integer $tickets
     *
     * @ORM\OneToMany(targetEntity="\Adservice\TicketBundle\Entity\Ticket", mappedBy="workshop")
     */
    private $tickets;
    
    /**
     * @var integer $incidences
     *
     * @ORM\OneToMany(targetEntity="\Adservice\TicketBundle\Entity\Incidence", mappedBy="workshop")
     */
    private $incidences;
    
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
     * @var string $modify_by
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
     * Set city
     *
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    public function getRegion() 
    {
        return $this->region;
    }

    public function setRegion(\Adservice\UtilBundle\Entity\Region $region) {
        $this->region = $region;
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
     * Set movile_phone_1
     *
     * @param integer $movilePhone1
     */
    public function setMovilePhone1($movilePhone1)
    {
        $this->movile_phone_1 = $movilePhone1;
    }

    /**
     * Get movile_phone_1
     *
     * @return integer 
     */
    public function getMovilePhone1()
    {
        return $this->movile_phone_1;
    }

    /**
     * Set movile_phone_2
     *
     * @param integer $movilePhone2
     */
    public function setMovilePhone2($movilePhone2)
    {
        $this->movile_phone_2 = $movilePhone2;
    }

    /**
     * Get movile_phone_2
     *
     * @return integer 
     */
    public function getMovilePhone2()
    {
        return $this->movile_phone_2;
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
     * Set contact
     *
     * @param string $contact
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    }

    /**
     * Get contact
     *
     * @return string 
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set observations
     *
     * @param string $observations
     */
    public function setObservations($observations)
    {
        $this->observations = $observations;
    }

    /**
     * Get observations
     *
     * @return string 
     */
    public function getObservations()
    {
        return $this->observations;
    }

    /**
     * Set partner
     *
     * @param string $partner
     */
    public function setPartner(\Adservice\PartnerBundle\Entity\Partner $partner)
    {
        $this->partner = $partner;
    }

    /**
     * Get partner
     *
     * @return string 
     */
    public function getPartner()
    {
        return $this->partner;
    }

    /**
     * Set active
     *
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set adservice_plus
     *
     * @param boolean $adservicePlus
     */
    public function setAdservicePlus($adservicePlus)
    {
        $this->adservice_plus = $adservicePlus;
    }

    /**
     * Get adservice_plus
     *
     * @return boolean 
     */
    public function getAdservicePlus()
    {
        return $this->adservice_plus;
    }

    /**
     * Set test
     *
     * @param boolean $test
     */
    public function setTest($test)
    {
        $this->test = $test;
    }

    /**
     * Get test
     *
     * @return boolean 
     */
    public function getTest()
    {
        return $this->test;
    }

    /**
     * Set typology
     *
     * @param string $typology
     */
    public function setTypology(\Adservice\WorkshopBundle\Entity\Typology $typology)
    {
        $this->typology = $typology;
    }

    /**
     * Get typology
     *
     * @return string 
     */
    public function getTypology()
    {
        return $this->typology;
    }

    /**
     * Set update_at
     *
     * @param datetime $updateAt
     */
    public function setUpdateAt($updateAt)
    {
        $this->update_at = $updateAt;
    }

    /**
     * Get update_at
     *
     * @return datetime 
     */
    public function getUpdateAt()
    {
        return $this->update_at;
    }

    /**
     * Set lowdate_at
     *
     * @param datetime $lowdateAt
     */
    public function setLowdateAt($lowdateAt)
    {
        $this->lowdate_at = $lowdateAt;
    }

    /**
     * Get lowdate_at
     *
     * @return datetime 
     */
    public function getLowdateAt()
    {
        return $this->lowdate_at;
    }

    /**
     * Set endtest_at
     *
     * @param datetime $endtestAt
     */
    public function setEndtestAt($endtestAt)
    {
        $this->endtest_at = $endtestAt;
    }

    /**
     * Get endtest_at
     *
     * @return datetime 
     */
    public function getEndtestAt()
    {
        return $this->endtest_at;
    }

    /**
     * Set conflictive
     *
     * @param boolean $conflictive
     */
    public function setConflictive($conflictive)
    {
        $this->conflictive = $conflictive;
    }

    /**
     * Get conflictive
     *
     * @return boolean 
     */
    public function getConflictive()
    {
        return $this->conflictive;
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
     * @return string 
     */
    public function getModifyBy()
    {
        return $this->modify_by;
    }
    
    public function __toString() {
        return $this->getName();
    }
    
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add users
     *
     * @param Adservice\UserBundle\Entity\User $users
     */
    public function addUser(\Adservice\UserBundle\Entity\User $users)
    {
        $this->users[] = $users;
    }

    /**
     * Get users
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add tickets
     *
     * @param Adservice\TicketBundle\Entity\Ticket $tickets
     */
    public function addTicket(\Adservice\TicketBundle\Entity\Ticket $tickets)
    {
        $this->tickets[] = $tickets;
    }

    /**
     * Get tickets
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getTickets()
    {
        return $this->tickets;
    }
}