<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
//use Adservice\PartnerBundle\Entity\Partner;
//use Adservice\PartnerBundle\Entity\Shop;
//use Adservice\WorkshopBundle\Entity\Typology;
//use Adservice\WorkshopBundle\Entity\DiagnosisMachine;

/**
 * @ORM\Entity
 * @ORM\Table(name="workshop")
 */
class Workshop {

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
     * @var integer $code_partner
     *
     * @ORM\Column(name="code_partner", type="integer")
     */
    private $code_partner;

    /**
     * @var integer $code_workshop
     *
     * @ORM\Column(name="code_workshop", type="integer")
     */
    private $code_workshop;

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
     *
     * @var string $users
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\User", mappedBy="workshop")
     */
    private $users;

    /**
     * @var string $partner
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Partner", inversedBy="workshops")
     */
    private $partner;

    /**
     * @var string $category_service
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CategoryService")
     */
    private $category_service;

    /**
     * @var string $internal_code
     *
     * @ORM\Column(name="internal_code", type="string", length=255, nullable=true)
     */
    private $internal_code;

    /**
     * @var string $commercial_code
     *
     * @ORM\Column(name="commercial_code", type="string", length=255, nullable=true)
     */
    private $commercial_code;

    /**
     * @var boolean $active
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var boolean $ad_service_plus
     *
     * @ORM\Column(name="ad_service_plus", type="boolean")
     */
    private $ad_service_plus;

    /**
     * @var boolean $test
     *
     * @ORM\Column(name="test", type="boolean", nullable=true)
     */
    private $test;

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
     * @var boolean $haschecks
     *
     * @ORM\Column(name="has_checks", type="boolean", nullable=true)
     */
    private $haschecks;

    /**
     * @var boolean $numchecks
     *
     * @ORM\Column(name="num_checks", type="integer", nullable=true)
     */
    private $numchecks;

    /**
     * @var boolean $infotech
     *
     * @ORM\Column(name="infotech", type="boolean", nullable=true)
     */
    private $infotech;

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
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
//        $this->diagnosis_machines = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function to_json(){
        $json = array('id'  => $this->getId(),
            'code_partner'  => $this->getCodePartner(),
            'code_workshop' => $this->getCodeWorkshop(),
            'name'          => $this->getName());
        return $json;
    }

    /**
     * Set id
     *
     * @param integer $id
     */
    public function setId($id) {
        $this->id = $id;
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

    public function setContactName($contact) {
        $this->contact = $contact;
    }

    public function getContactName() {
        return $this->contact;
    }

    public function setContact($contact) {
        $this->contact = $contact;
    }

    public function getContact() {
        if($this->contact == 'sin-especificar') $contact    = '';
        else $contact    = $this->contact;
        return $contact;
    }

    /**
     * Set internal_code
     *
     * @param boolean $internal_code
     */
    public function setInternalCode($internal_code) {
        $this->internal_code = $internal_code;
    }

    /**
     * Get internal_code
     *
     * @return boolean
     */
    public function getInternalCode() {
        return $this->internal_code;
    }

    /**
     * Set commercial_code
     *
     * @param boolean $commercial_code
     */
    public function setCommercialCode($commercial_code) {
        $this->commercial_code = $commercial_code;
    }

    /**
     * Get commercial_code
     *
     * @return boolean
     */
    public function getCommercialCode() {
        return $this->commercial_code;
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
     * Set ad_service_plus
     *
     * @param boolean $ad_service_plus
     */
    public function setAdServicePlus($ad_service_plus) {
        $this->ad_service_plus = $ad_service_plus;
    }

    /**
     * Get ad_service_plus
     *
     * @return boolean
     */
    public function getAdServicePlus() {
        return $this->ad_service_plus;
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

    /**
     * Set haschecks
     *
     * @param boolean $haschecks
     */
    public function setHasChecks($haschecks) {
        $this->haschecks = $haschecks;
    }

    /**
     * Get haschecks
     *
     * @return boolean
     */
    public function getHasChecks() {
        return $this->haschecks;
    }

    /**
     * Set numchecks
     *
     * @param integer $numchecks
     */
    public function setNumChecks($numchecks) {
        $this->numchecks = $numchecks;
    }

    /**
     * Get numchecks
     *
     * @return integer
     */
    public function getNumChecks() {
        return $this->numchecks;
    }

    /**
     * Set infotech
     *
     * @param boolean $infotech
     */
    public function setInfotech($infotech) {
        $this->infotech = $infotech;
    }

    /**
     * Get infotech
     *
     * @return boolean
     */
    public function getInfotech() {
        return $this->infotech;
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

    public function setPartner(Partner $partner) {
        $this->partner = $partner;
    }

    public function getPartner() {
        return $this->partner;
    }

    /**
     * Set category_service
     *
     * @param string $category_service
     */
    public function setCategoryService(\AppBundle\Entity\CategoryService $category_service) {
        $this->category_service = $category_service;
    }

    /**
     * Get category_service
     *
     * @return string
     */
    public function getCategoryService() {
        return $this->category_service;
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
     * @param string $postal_code
     */
    public function setPostalCode($postal_code)
    {
        $this->postal_code = $postal_code;
    }

    /**
     * Get postal_code
     *
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postal_code;
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
     * Set mobile_number_1
     *
     * @param integer $mobileNumber1
     */
    public function setMobileNumber1($mobileNumber1)
    {
        $this->mobile_number_1 = $mobileNumber1;
    }

    /**
     * Get mobile_number_1
     *
     * @return integer
     */
    public function getMobileNumber1()
    {
        return $this->mobile_number_1;
    }

    /**
     * Set mobile_number_2
     *
     * @param integer $mobileNumber2
     */
    public function setMobileNumber2($mobileNumber2)
    {
        $this->mobile_number_2 = $mobileNumber2;
    }

    /**
     * Get mobile_number_2
     *
     * @return integer
     */
    public function getMobileNumber2()
    {
        return $this->mobile_number_2;
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


    public function __toString() {
        return $this->getName();
    }
}
