<?php

namespace Adservice\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Adservice\PartnerBundle\Entity\Partner;
use Adservice\WorkshopBundle\Entity\Typology;
use Adservice\WorkshopBundle\Entity\DiagnosisMachine;
use Adservice\UtilBundle\Entity\Region;

/**
 * Adservice\OrderBundle\Entity\Workshop
 *
 * @ORM\Table(name="workshopOrder")
 * @ORM\Entity
 */
class WorkshopOrder {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $id_workshop
     *
     * @ORM\Column(name="id_workshop", type="integer", length=255, nullable=true )
     */
    private $id_workshop;

    /**
     * @var string $$wanted_action
     * @ORM\Column(name="wanted_action", type="string", length=255)
     */
    private $wanted_action;

    /**
     * @var string $action
     *
     * @ORM\Column(name="action", type="string", length=255)
     */
    private $action;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string $code_workshop
     *
     * @ORM\Column(name="code_workshop", type="integer", length=255, nullable=true )
     */
    private $code_workshop;

    /**
     * @var string $cif
     * @ORM\Column(name="cif", type="string", length=255, nullable=true)
     */
    private $cif;

    /**
     * @var string $contact_name
     *
     * @ORM\Column(name="contact_name", type="string", length=255)
     */
    private $contact_name;

    /**
     * @var string $contact_surname
     *
     * @ORM\Column(name="contact_surname", type="string", length=255)
     */
    private $contact_surname;

    /**
     *
     * @var string $observation_workshop
     * @ORM\Column(name="observation_workshop", type="string", length=255, nullable=true)
     */
    private $observation_workshop;

    /**
     *
     * @var string $observation_assessor
     * @ORM\Column(name="observation_assessor", type="string", length=255, nullable=true)
     */
    private $observation_assessor;

    /**
     *
     * @var string $observation_admin
     * @ORM\Column(name="observation_admin", type="string", length=255, nullable=true)
     */
    private $observation_admin;

    /**
     * @var string $partner
     *
     * @ORM\ManyToOne(targetEntity="Adservice\PartnerBundle\Entity\Partner", inversedBy="workshops")
     */
    private $partner;

    /**
     * @var string $shop
     *
     * @ORM\ManyToOne(targetEntity="Adservice\PartnerBundle\Entity\Shop", inversedBy="workshops")
     */
    private $shop;

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
     * @var boolean $test
     *
     * @ORM\Column(name="test", type="boolean", nullable=true)
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
     * @ORM\Column(name="update_at", type="datetime", nullable=true)
     */
    private $update_at;

    /**
     * @var datetime $lowdate_at
     *
     * @ORM\Column(name="lowdate_at", type="datetime", nullable=true)
     */
    private $lowdate_at;

    /**
     * @var datetime $endtest_at
     *
     * @ORM\Column(name="endtest_at", type="datetime", nullable=true)
     */
    private $endtest_at;

    /**
     * @var boolean $conflictive
     *
     * @ORM\Column(name="conflictive", type="boolean", nullable=true)
     */
    private $conflictive;

    /**
     * @var string $rejection_reason 
     * @ORM\Column(name="rejection_reason", type="string", length=255, nullable=true)
     */
    private $rejection_reason;

    /**
     * @var integer $tickets
     *
     * @ORM\OneToMany(targetEntity="\Adservice\TicketBundle\Entity\Ticket", mappedBy="workshop")
     */
    private $tickets;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set code_workshop
     *
     * @param integer $code_workshop
     */
    public function setCodeWorkshop($code_workshop) {
        $this->code_workshop = $code_workshop;
    }

    /**
     * Get code_workshop
     *
     * @return integer
     */
    public function getCodeWorkshop() {
        return $this->code_workshop;
    }

    /**
     * Get cif
     *
     * @return string
     */
    public function getCif() {
        return $this->cif;
    }

    /**
     * Set cif
     *
     * @param string $cif
     */
    public function setCif($cif) {
        $this->cif = $cif;
    }

    /**
     * Set contact_name
     *
     * @param string $contact_name
     */
    public function setContactName($contact_name) {
        $this->contact_name = $contact_name;
    }

    /**
     * Get contact_name
     *
     * @return string
     */
    public function getContactName() {
        return $this->contact_name;
    }

    /**
     * Set contact_surname
     *
     * @param string $contact_surname
     */
    public function setContactSurname($contact_surname) {
        $this->contact_surname = $contact_surname;
    }

    /**
     * Get contact_surname
     *
     * @return string
     */
    public function getContactSurname() {
        return $this->contact_surname;
    }

    /**
     * Get contact
     *
     * @return string
     */
    public function getContact() {
        $contact = $this->contact_name.' '.$this->contact_surname;
        return $contact;
    }

    /**
     * Set partner
     *
     * @param string $partner
     */
    public function setPartner(\Adservice\PartnerBundle\Entity\Partner $partner) {
        $this->partner = $partner;
    }

    /**
     * Get partner
     *
     * @return string
     */
    public function getPartner() {
        return $this->partner;
    }

    /**
     * Set shop
     *
     * @param  string $shop
     */
    public function setShop(\Adservice\PartnerBundle\Entity\Shop $shop) {
        $this->shop = $shop;
    }

    /**
     * Get shop
     *
     * @return string
     */
    public function getShop() {
        return $this->shop;
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
     * Set test
     *
     * @param boolean $test
     */
    public function setTest($test) {
        $this->test = $test;
    }

    /**
     * Get test
     *
     * @return boolean
     */
    public function getTest() {
        return $this->test;
    }

    /**
     * Set typology
     *
     * @param string $typology
     */
    public function setTypology(\Adservice\WorkshopBundle\Entity\Typology $typology) {
        $this->typology = $typology;
    }

    /**
     * Get typology
     *
     * @return string
     */
    public function getTypology() {
        return $this->typology;
    }

    /**
     * Set update_at
     *
     * @param datetime $updateAt
     */
    public function setUpdateAt($updateAt) {
        $this->update_at = $updateAt;
    }

    /**
     * Get update_at
     *
     * @return datetime
     */
    public function getUpdateAt() {
        return $this->update_at;
    }

    /**
     * Set lowdate_at
     *
     * @param datetime $lowdateAt
     */
    public function setLowdateAt($lowdateAt) {
        $this->lowdate_at = $lowdateAt;
    }

    /**
     * Get lowdate_at
     *
     * @return datetime
     */
    public function getLowdateAt() {
        return $this->lowdate_at;
    }

    /**
     * Set endtest_at
     *
     * @param datetime $endtestAt
     */
    public function setEndtestAt($endtestAt) {
        $this->endtest_at = $endtestAt;
    }

    /**
     * Get endtest_at
     *
     * @return datetime
     */
    public function getEndtestAt() {
        return $this->endtest_at;
    }

    /**
     * Set conflictive
     *
     * @param boolean $conflictive
     */
    public function setConflictive($conflictive) {
        $this->conflictive = $conflictive;
    }

    /**
     * Get conflictive
     *
     * @return boolean
     */
    public function getConflictive() {
        return $this->conflictive;
    }

    public function __toString() {
        return $this->getName();
    }

    public function __construct() {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->diagnosis_machines = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add users
     *
     * @param Adservice\UserBundle\Entity\User $users
     */
    public function addUser(\Adservice\UserBundle\Entity\User $users) {
        $this->users[] = $users;
    }

    /**
     * Get users
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getUsers() {
        return $this->users;
    }

    /**
     * Add tickets
     *
     * @param Adservice\TicketBundle\Entity\Ticket $tickets
     */
    public function addTicket(\Adservice\TicketBundle\Entity\Ticket $tickets) {
        $this->tickets[] = $tickets;
    }

    /**
     * Get tickets
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getTickets() {
        return $this->tickets;
    }

    /**
     * Add user_roles
     *
     * @param Role $userRoles
     */
    public function addDiagnosisMachine(DiagnosisMachine $diagnosis_machine) {
        $this->diagnosis_machines[] = $diagnosis_machine;
    }

    public function setDiagnosisMachine($diagnosis_machines) {
        $this->diagnosis_machines = $diagnosis_machines;
    }

    public function getDiagnosisMachines() {
        return $this->diagnosis_machines;
    }

    public function getObservationWorkshop() {
        return $this->observation_workshop;
    }

    public function getObservationAssessor() {
        return $this->observation_assessor;
    }

    public function getObservationAdmin() {
        return $this->observation_admin;
    }

    public function setObservationWorkshop($observation_workshop) {
        $this->observation_workshop = $observation_workshop;
    }

    public function setObservationAssessor($observation_assessor) {
        $this->observation_assessor = $observation_assessor;
    }

    public function setObservationAdmin($observation_admin) {
        $this->observation_admin = $observation_admin;
    }

    public function getIdWorkshop() {
        return $this->id_workshop;
    }

    public function setIdWorkshop($id_workshop) {
        $this->id_workshop = $id_workshop;
    }

    public function getAction() {
        return $this->action;
    }

    public function setAction($action) {
        $this->action = $action;
    }

    public function getRejectionReason() {
        return $this->rejection_reason;
    }

    public function setRejectionReason($rejection_reason) {
        $this->rejection_reason = $rejection_reason;
    }

    public function getWantedAction() {
        return $this->wanted_action;
    }

    public function setWantedAction($wanted_action) {
        $this->wanted_action = $wanted_action;
    }

//   ____ ___  _   _ _____  _    ____ _____
//  / ___/ _ \| \ | |_   _|/ \  / ___|_   _|
// | |  | | | |  \| | | | / _ \| |     | |
// | |__| |_| | |\  | | |/ ___ \ |___  | |
//  \____\___/|_| \_| |_/_/   \_\____| |_|

    /**
     * @var string $country
     *
     * @ORM\ManyToOne(targetEntity="Adservice\UtilBundle\Entity\Country")
     */
    private $country;

    /**
     * @var string $region
     *
     * @ORM\Column(name="region", type="string")
     */
    private $region;

    /**
     * @var string $city
     *
     * @ORM\Column(name="city", type="string")
     */
    private $city;

    /**
     * @var string $address
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @var string $postal_code
     *
     * @ORM\Column(name="postal_code", type="string", nullable=true)
     */
    private $postal_code;

    /**
     * @var integer $phone_number_1
     *
     * @ORM\Column(name="phone_number_1", type="integer")
     */
    private $phone_number_1;

    /**
     * @var integer $phone_number_2
     *
     * @ORM\Column(name="phone_number_2", type="integer", nullable=true)
     */
    private $phone_number_2;

    /**
     * @var integer $movile_number_1
     *
     * @ORM\Column(name="movile_number_1", type="integer", nullable=true)
     */
    private $movile_number_1;

    /**
     * @var integer $movile_number_2
     *
     * @ORM\Column(name="movile_number_2", type="integer", nullable=true)
     */
    private $movile_number_2;

    /**
     * @var integer $fax
     *
     * @ORM\Column(name="fax", type="integer", nullable=true)
     */
    private $fax;

    /**
     * @var string $email_1
     *
     * @ORM\Column(name="email_1", type="string", length=255, nullable=true)
     */
    private $email_1;

    /**
     * @var string $email_2
     *
     * @ORM\Column(name="email_2", type="string", length=255, nullable=true)
     */
    private $email_2;

//  ___________________________________________________________________
// |___________________________________________________________________|

    /**
     * Set country
     *
     * @param string $country
     */
    public function setCountry(\Adservice\UtilBundle\Entity\Country $country) {
        $this->country = $country;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry() {
        return $this->country;
    }

    /**
     * Set region
     *
     * @param string $region
     */
    public function setRegion($region) {
        $this->region = $region;
    }

    /**
     * Get region
     *
     * @return string
     */
    public function getRegion() {
        return $this->region;
    }

    /**
     * Set city
     *
     * @param string $city
     */
    public function setCity($city) {
        $this->city = $city;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity() {
        return $this->city;
    }

    /**
     * Set address
     *
     * @param string $address
     */
    public function setAddress($address) {
        $this->address = $address;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * Set postal_code
     *
     * @param string $postal_code
     */
    public function setPostalCode($postal_code) {
        $this->postal_code = $postal_code;
    }

    /**
     * Get postal_code
     *
     * @return string
     */
    public function getPostalCode() {
        return $this->postal_code;
    }

    /**
     * Set phone_number_1
     *
     * @param integer $phoneNumber1
     */
    public function setPhoneNumber1($phoneNumber1) {
        $this->phone_number_1 = $phoneNumber1;
    }

    /**
     * Get phone_number_1
     *
     * @return integer
     */
    public function getPhoneNumber1() {
        return $this->phone_number_1;
    }

    /**
     * Set phone_number_2
     *
     * @param integer $phoneNumber2
     */
    public function setPhoneNumber2($phoneNumber2) {
        $this->phone_number_2 = $phoneNumber2;
    }

    /**
     * Get phone_number_2
     *
     * @return integer
     */
    public function getPhoneNumber2() {
        return $this->phone_number_2;
    }

    /**
     * Set movile_number_1
     *
     * @param integer $movileNumber1
     */
    public function setMovileNumber1($movileNumber1) {
        $this->movile_number_1 = $movileNumber1;
    }

    /**
     * Get movile_number_1
     *
     * @return integer
     */
    public function getMovileNumber1() {
        return $this->movile_number_1;
    }

    /**
     * Set movile_number_2
     *
     * @param integer $movileNumber2
     */
    public function setMovileNumber2($movileNumber2) {
        $this->movile_number_2 = $movileNumber2;
    }

    /**
     * Get movile_number_2
     *
     * @return integer
     */
    public function getMovileNumber2() {
        return $this->movile_number_2;
    }

    /**
     * Set fax
     *
     * @param integer $fax
     */
    public function setFax($fax) {
        $this->fax = $fax;
    }

    /**
     * Get fax
     *
     * @return integer
     */
    public function getFax() {
        return $this->fax;
    }

    /**
     * Set email_1
     *
     * @param string $email1
     */
    public function setEmail1($email1) {
        $this->email_1 = $email1;
    }

    /**
     * Get email_1
     *
     * @return string
     */
    public function getEmail1() {
        return $this->email_1;
    }

    /**
     * Set email_2
     *
     * @param string $email2
     */
    public function setEmail2($email2) {
        $this->email_2 = $email2;
    }

    /**
     * Get email_2
     *
     * @return string
     */
    public function getEmail2() {
        return $this->email_2;
    }

//   ____ ____  _____    _  _____ _____    ____  __  ___  ____ ___ _______   __
//  / ___|  _ \| ____|  / \|_   _| ____|  / /  \/  |/ _ \|  _ \_ _|  ___\ \ / /
// | |   | |_) |  _|   / _ \ | | |  _|   / /| |\/| | | | | | | | || |_   \ V /
// | |___|  _ <| |___ / ___ \| | | |___ / / | |  | | |_| | |_| | ||  _|   | |
//  \____|_| \_\_____/_/   \_\_| |_____/_/  |_|  |_|\___/|____/___|_|     |_|

    /**
     * @var datetime $created_at
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $created_at;

    /**
     * @var string $created_by
     *
     * @ORM\ManyToOne(targetEntity="Adservice\UserBundle\Entity\User")
     */
    private $created_by;

    /**
     * @var datetime $modified_at
     *
     * @ORM\Column(name="modified_at", type="datetime")
     */
    private $modified_at;

    /**
     * @var string $modified_by
     *
     * @ORM\ManyToOne(targetEntity="Adservice\UserBundle\Entity\User")
     */
    private $modified_by;

//  ___________________________________________________________________
// |___________________________________________________________________|


    /**
     * Set created_at
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt) {
        $this->created_at = $createdAt;
    }

    /**
     * Get created_at
     *
     * @return datetime
     */
    public function getCreatedAt() {
        return $this->created_at;
    }

    /**
     * Set created_by
     *
     * @param user $created_by
     */
    public function setCreatedBy(\Adservice\UserBundle\Entity\User $user) {
        $this->created_by = $user;
    }

    /**
     * Get created_by
     *
     * @return string
     */
    public function getCreatedBy() {
        return $this->created_by;
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
    public function getModifiedAt() {
        return $this->modified_at;
    }

    /**
     * Set modified_by
     *
     * @param user $modified_by
     */
    public function setModifiedBy(\Adservice\UserBundle\Entity\User $user) {
        $this->modified_by = $user;
    }

    /**
     * Get modified_by
     *
     * @return string
     */
    public function getModifiedBy() {
        return $this->modified_by;
    }
}
