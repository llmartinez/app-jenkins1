<?php

namespace Adservice\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Adservice\PartnerBundle\Entity\Partner;
use Adservice\WorkshopBundle\Entity\Typology;
use Adservice\WorkshopBundle\Entity\DiagnosisMachine;
use Adservice\UtilBundle\Entity\Region;
use Adservice\UtilBundle\Entity\Province;

/**
 * Adservice\OrderBundle\Entity\ShopOrder
 *
 * @ORM\Table(name="shopOrder")
 * @ORM\Entity
 */
class ShopOrder {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $id_shop
     *
     * @ORM\Column(name="id_shop", type="integer", length=255, nullable=true)
     */
    private $id_shop;

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
     * @var string $rejection_reason
     * @ORM\Column(name="rejection_reason", type="string", length=255, nullable=true)
     */
    private $rejection_reason;

//  _____ _  __
// |  ___| |/ /
// | |_  | ' /
// |  _| | . \
// |_|   |_|\_\

    /**
     * @var integer $partner
     *
     * @ORM\ManyToOne(targetEntity="Adservice\PartnerBundle\Entity\Partner")
     */
    private $partner;

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


//   ____    _    __  __ ____   ___  ____
//  / ___|  / \  |  \/  |  _ \ / _ \/ ___|
// | |     / _ \ | |\/| | |_) | | | \___ \
// | |___ / ___ \| |  | |  __/| |_| |___) |
//  \____/_/   \_\_|  |_|_|    \___/|____/


    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var boolean $active
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var integer $phone_number_1
     *
     * @ORM\Column(name="phone_number_1", type="integer")
     */
    private $phone_number_1;

    /**
     * @var integer $phone_number_2
     *
     * @ORM\Column(name="phone_number_2", type="integer", nullable="true")
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
     * @ORM\Column(name="email_2", type="string", length=255, nullable="true")
     */
    private $email_2;

    /**
     * @var integer $fax
     *
     * @ORM\Column(name="fax", type="integer", nullable="true")
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

//  __  __    _    ____  ____ ___ _   _  ____
// |  \/  |  / \  |  _ \|  _ \_ _| \ | |/ ___|
// | |\/| | / _ \ | |_) | |_) | ||  \| | |  _
// | |  | |/ ___ \|  __/|  __/| || |\  | |_| |
// |_|  |_/_/   \_\_|   |_|  |___|_| \_|\____|

    /**
     *
     * @var string $workshops
     *
     * @ORM\OneToMany(targetEntity="Adservice\WorkshopBundle\Entity\Workshop", mappedBy="shop")
     */
    private $workshops;


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

//  ____  _____ _____ _____ _____ ____  ____    ______ _____ _____ _____ _____  ____  ____
// / ___|| ____|_   _|_   _| ____|  _ \/ ___|  / / ___| ____|_   _|_   _| ____||  _ \/ ___|
// \___ \|  _|   | |   | | |  _| | |_) \___ \ / / |  _|  _|   | |   | | |  _|  | |_) \___ \
//  ___) | |___  | |   | | | |___|  _ < ___) / /| |_| | |___  | |   | | | |___ |  _ < ___) |
// |____/|_____| |_|   |_| |_____|_| \_\____/_/  \____|_____| |_|   |_| |_____||_| \_\____/

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    public function getIdShop() {
        return $this->id_shop;
    }

    public function setIdShop($id_shop) {
        $this->id_shop = $id_shop;
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


//  ________________________________________________________________________________
// |________________________________________________________________________________|
//
    /**
     * Set partner
     *
     * @param string $partner
     */
    public function setPartner(Partner $partner) {
        $this->partner = $partner;
    }

    /**
     * Get partner
     *
     * @return integer
     */
    public function getPartner() {
        return $this->partner;
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
    public function getregion() {
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
    public function getProvince() {
        return $this->province;
    }

//  ________________________________________________________________________________
// |________________________________________________________________________________|
//
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
     * @param integer $postalCode
     */
    public function setPostalCode($postalCode) {
        $this->postal_code = $postalCode;
    }

    /**
     * Get postal_code
     *
     * @return integer
     */
    public function getPostalCode() {
        return $this->postal_code;
    }

//  ________________________________________________________________________________
// |________________________________________________________________________________|
//
    /**
     * Set created_by
     *
     * @param user created_by
     */
    public function setCreatedBy(\Adservice\UserBundle\Entity\User $user) {
        $this->created_by = $user;
    }

    /**
     * Get modified_by
     *
     * @return string
     */
    public function getCreatedBy() {
        return $this->created_by;
    }

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

//  _____ _   _ _   _  ____ _____ ___ ___  _   _ ____
// |  ___| | | | \ | |/ ___|_   _|_ _/ _ \| \ | / ___|
// | |_  | | | |  \| | |     | |  | | | | |  \| \___ \
// |  _| | |_| | |\  | |___  | |  | | |_| | |\  |___) |
// |_|    \___/|_| \_|\____| |_| |___\___/|_| \_|____/

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
     * Add workshops
     *
     * @param Adservice\WorkshopBundle\Entity\Workshop $workshops
     */
    public function addWorkshop(\Adservice\WorkshopBundle\Entity\Workshop $workshops) {
        $this->workshops[] = $workshops;
    }

    /**
     * Get workshops
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getWorkshops() {
        return $this->workshops;
    }
}
