<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
// use Symfony\Component\Validator\Constraints as Assert;
// use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use AppBundle\Entity\User;
use AppBundle\Entity\Workshop;
//use AppBundle\Entity\Shop;

/**
 * AppBundle\Entity\Partner
 * @ORM\Entity
 * @ORM\Table(name="partner")
 */
class Partner {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */

    private $id;

    /**
     * @var integer $code_partner
     *
     * @ORM\Column(name="code_partner", type="integer")
     */
    private $code_partner;

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
     *
     * @var string $workshops
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Workshop", mappedBy="partner")
     */
    private $workshops;

    /**
     *
     * @var string $shops
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Shop", mappedBy="partner")
     */
    private $shops;

    /**
     *
     * @var type
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\User", mappedBy="partner")
     */
    private $users;

    /**
     * @var string $category_service
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CategoryService")
     */
    private $category_service;

    /**
     * @var string $cif
     * @ORM\Column(name="cif", type="string", length=255, nullable=true)
     */
    private $cif;

    /**
     * @var string $code_billing
     * @ORM\Column(name="code_billing", type="string", length=255, nullable=true)
     */
    private $code_billing;

    /**
     * @var string $contact
     *
     * @ORM\Column(name="contact", type="string", length=255, nullable=true)
     */
    private $contact;

    /**
     *
     * @var string $observations
     * @ORM\Column(name="observations", type="string", length=255, nullable=true)
     */
    private $observations;

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
     * @ORM\Column(name="postal_code", type="string",nullable=true)
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
     * @var integer $mobile_number_1
     *
     * @ORM\Column(name="mobile_number_1", type="integer", nullable=true)
     */
    private $mobile_number_1;

    /**
     * @var integer $mobile_number_2
     *
     * @ORM\Column(name="mobile_number_2", type="integer", nullable=true)
     */
    private $mobile_number_2;

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

    /**
     * @var datetime $created_at
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $created_at;

    /**
     * @var string $created_by
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     */
    private $modified_by;


//  ____  _____ _____ _____ _____ ____  ____    ______ _____ _____ _____ _____  ____  ____
// / ___|| ____|_   _|_   _| ____|  _ \/ ___|  / / ___| ____|_   _|_   _| ____||  _ \/ ___|
// \___ \|  _|   | |   | | |  _| | |_) \___ \ / / |  _|  _|   | |   | | |  _|  | |_) \___ \
//  ___) | |___  | |   | | | |___|  _ < ___) / /| |_| | |___  | |   | | | |___ |  _ < ___) |
// |____/|_____| |_|   |_| |_____|_| \_\____/_/  \____|_____| |_|   |_| |_____||_| \_\____/

    public function __construct() {
        $this->workshops = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set code_partner
     *
     * @param integer $code_partner
     */
    public function setCodePartner($code_partner) {
        $this->code_partner = $code_partner;
    }

    /**
     * Get code_partner
     *
     * @return integer
     */
    public function getCodePartner() {
        return $this->code_partner;
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
     * Add workshops
     *
     * @param Workshop $workshops
     */
    public function addWorkshop(Workshop $workshops) {
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

    /**
     * Add shop
     *
     * @param Shop $shop
     */
    public function addShop(Shop $shop) {
        $this->shop[] = $shop;
    }

    /**
     * Get shop
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getShop() {
        return $this->shop;
    }

    /**
     * Add users
     *
     * @param AppBundle\Entity\User $users
     */
    public function addUser(\AppBundle\Entity\User $users) {
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
     * Set category_service
     *
     * @param CategoryService $category_service
     */
    public function setCategoryService(CategoryService $category_service) {
        $this->category_service = $category_service;
    }

    /**
     * Get category_service
     *
     * @return CategoryService
     */
    public function getCategoryService() {
        return $this->category_service;
    }


    public function getCif() {
        return $this->cif;
    }

    public function setCif($cif) {
        $this->cif = $cif;
    }

    public function getCodeBilling() {
        return $this->code_billing;
    }

    public function setCodeBilling($code_billing) {
        $this->code_billing = $code_billing;
    }

    /**
     * Set contact
     *
     * @param string $contact
     */
    public function setContact($contact) {
        $this->contact = $contact;
    }

    /**
     * Get contact
     *
     * @return string
     */
    public function getContact() {
        return $this->contact;
    }

    public function getObservations() {
        return $this->observations;
    }

    public function setObservations($observations) {
        $this->observations = $observations;
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
     * Set mobile_number_1
     *
     * @param integer $mobileNumber1
     */
    public function setMobileNumber1($mobileNumber1) {
        $this->mobile_number_1 = $mobileNumber1;
    }

    /**
     * Get mobile_number_1
     *
     * @return integer
     */
    public function getMobileNumber1() {
        return $this->mobile_number_1;
    }

    /**
     * Set mobile_number_2
     *
     * @param integer $mobileNumber2
     */
    public function setMobileNumber2($mobileNumber2) {
        $this->mobile_number_2 = $mobileNumber2;
    }

    /**
     * Get mobile_number_2
     *
     * @return integer
     */
    public function getMobileNumber2() {
        return $this->mobile_number_2;
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
    public function setCreatedBy(\AppBundle\Entity\User $user) {
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
    public function setModifiedBy(\AppBundle\Entity\User $user) {
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

    public function to_json(){
        $json = array('id'           => $this->getId(),
                      'code_partner' => $this->getCodePartner(),
                      'name'         => $this->getName());
        return $json;
    }

    public function __toString() {
        return $this->getName();
    }

}
