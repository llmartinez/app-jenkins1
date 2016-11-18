<?php

namespace Adservice\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\ExecutionContext;
use Adservice\UtilBundle\Entity\Country;
use Adservice\UtilBundle\Entity\Language;
use Adservice\UtilBundle\Entity\Region;

/**
 * Adservice\UserBundle\Entity\User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Adservice\UserBundle\Entity\UserRepository")
 */
class User implements UserInterface, AdvancedUserInterface, \Serializable {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $username
     *
     * @ORM\Column(name="username", type="string", length=255)
     */
    private $username;

    /**
     * @var string $password
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string $salt
     *
     * @ORM\Column(name="salt", type="string", length=255)
     */
    private $salt;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string $surname
     *
     * @ORM\Column(name="surname", type="string", length=255)
     */
    private $surname;

    /**
     * @var boolean $active
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var integer $charge
     *
     * @ORM\Column(name="charge", type="integer", nullable=true)
     */
    private $charge;

    /**
     * @var boolean $token
     *
     * @ORM\Column(name="token", type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * se utilizó user_roles para no hacer conflicto al aplicar ->toArray en getRoles()
     * @ORM\ManyToMany(targetEntity="Role")
     * @ORM\JoinTable(name="user_role",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     */
    private $user_role;

    /**
     * @var string $language
     *
     * @ORM\ManyToOne(targetEntity="Adservice\UtilBundle\Entity\Language")
     */
    private $language;

    /**
     * @var string $country_service
     *
     * @ORM\ManyToOne(targetEntity="Adservice\UtilBundle\Entity\CountryService")
     */
    private $country_service;

    /**
     * @var string $category_service
     *
     * @ORM\ManyToOne(targetEntity="Adservice\UserBundle\Entity\CategoryService")
     */
    private $category_service;

    /**
     *
     * @var type
     * @ORM\ManyToOne(targetEntity="Adservice\PartnerBundle\Entity\Partner", inversedBy="users")
     */
    private $partner;

    /**
     *
     * @var type
     * @ORM\ManyToOne(targetEntity="Adservice\PartnerBundle\Entity\Shop", inversedBy="users")
     */
    private $shop;

    /**
     *
     * @var type
     * @ORM\ManyToOne(targetEntity="Adservice\WorkshopBundle\Entity\Workshop", inversedBy="workshops")
     */
    private $workshop;

    /**
     * @var boolean $privacy
     *
     * @ORM\Column(name="privacy", type="boolean")
     */
    private $privacy;

    /**
     * @var boolean $allow_list
     *
     * Permiso del usuario (comercial) para listar talleres
     *
     * @ORM\Column(name="allow_list", type="boolean", options={"default" : 1})
     */
    private $allow_list;

    /**
     * @var boolean $allow_create
     *
     * Permiso del usuario (socio) para crear comerciales
     *
     * @ORM\Column(name="allow_create", type="boolean", options={"default" : 1})
     */
    private $allow_create;

    /**
     * @var boolean $allow_order
     *
     * Permiso del usuario (socio/comercial) para solicitar talleres
     *
     * @ORM\Column(name="allow_order", type="boolean", options={"default" : 1})
     */
    private $allow_order;
//  ____  _____ _____ _____ _____ ____  ____    ______ _____ _____ _____ _____  ____  ____
// / ___|| ____|_   _|_   _| ____|  _ \/ ___|  / / ___| ____|_   _|_   _| ____||  _ \/ ___|
// \___ \|  _|   | |   | | |  _| | |_) \___ \ / / |  _|  _|   | |   | | |  _|  | |_) \___ \
//  ___) | |___  | |   | | | |___|  _ < ___) / /| |_| | |___  | |   | | | |___ |  _ < ___) |
// |____/|_____| |_|   |_| |_____|_| \_\____/_/  \____|_____| |_|   |_| |_____||_| \_\____/


    public function __toString() {
        return $this->getName() . ' ' . $this->getSurname();
    }

    public function __construct() {
        $this->user_role = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set username
     *
     * @param string $username
     */
    public function setUsername($username) {
        $this->username = $username;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Set salt
     *
     * @param string $salt
     */
    public function setSalt($salt) {
        $this->salt = $salt;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt() {
        return $this->salt;
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
     * Set charge
     *
     * @param boolean $charge
     */
    public function setCharge($charge) {
        $this->charge = $charge;
    }

    /**
     * Get charge
     *
     * @return boolean
     */
    public function getCharge() {
        return $this->charge;
    }

    /**
     * Set token
     *
     * @param integer $token
     */
    public function setToken($token) {
        $this->token = $token;
    }

    /**
     * Get token
     *
     * @return integer
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * Set language
     *
     * @param string $language
     */
    public function setLanguage(\Adservice\UtilBundle\Entity\Language $language) {
        $this->language = $language;
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage() {
        return $this->language;
    }

    /**
     * Set country_service
     *
     * @param string $country_service
     */
    public function setCountryService(\Adservice\UtilBundle\Entity\CountryService $country_service) {
        $this->country_service = $country_service;
    }

    /**
     * Get country_service
     *
     * @return string
     */
    public function getCountryService() {
        return $this->country_service;
    }

    /**
     * Set category_service
     *
     * @param string $category_service
     */
    public function setCategoryService(\Adservice\UserBundle\Entity\CategoryService $category_service) {
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

    public function getName() {
        return $this->name;
    }

    public function getSurname() {
        return $this->surname;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setSurname($surname) {
        $this->surname = $surname;
    }

    public function equals(\Symfony\Component\Security\Core\User\UserInterface $user) {
        return $this->getUsername() == $user->getUsername();
    }

    public function eraseCredentials() {

    }

    public function getRoles() {
        return $this->user_role->toArray(); //IMPORTANTE: el mecanismo de seguridad de Sf2 requiere ésto como un array
    }

    /**
     * Add user_roles
     *
     * @param Role $userRoles
     */
    public function addRole(Role $userRoles) {
        $this->user_role[] = $userRoles;
    }

    public function setUserRoles($roles) {
        $this->user_role = $roles;
    }

    /**
     * Get user_roles
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getUserRole() {
        return $this->user_role;
    }

    public function isAccountNonExpired() {
        return true;
    }

    public function isAccountNonLocked() {
        return true;
    }

    public function isCredentialsNonExpired() {
        return true;
    }

    public function isEnabled() {
        if ($this->active)
            return true;
        else
            return false;
    }

    /**
     * Serializes the content of the current User object
     * @return string
     */
    public function serialize() {
        return \json_encode(
                array($this->id, $this->username, $this->password, $this->salt,
                    $this->active, $this->language, $this->user_role));
    }

    /**
     * Unserializes the given string in the current User object
     * @param serialized
     */
    public function unserialize($serialized) {
        list($this->id, $this->username, $this->password, $this->salt,
                $this->active, $this->language, $this->user_role ) = \json_decode(
                $serialized);
    }

    public function getWorkshop() {
        return $this->workshop;
    }

    public function setWorkshop(\Adservice\WorkshopBundle\Entity\Workshop $workshop) {
        $this->workshop = $workshop;
    }

    public function getPartner() {
        return $this->partner;
    }

    public function setPartner(\Adservice\PartnerBundle\Entity\Partner $partner) {
        $this->partner = $partner;
    }

    public function getShop() {
        return $this->shop;
    }

    public function setShop(\Adservice\PartnerBundle\Entity\Shop $shop) {
        $this->shop = $shop;
    }

    /**
     * Set privacy
     *
     * @param boolean $privacy
     */
    public function setPrivacy($privacy) {
        $this->privacy = $privacy;
    }

    /**
     * Get privacy
     *
     * @return boolean
     */
    public function getPrivacy() {
        return $this->privacy;
    }

    /**
     * Set allow_list
     *
     * @param boolean $allow_list
     */
    public function setAllowList($allow_list) {
        $this->allow_list = $allow_list;
    }

    /**
     * Get allow_list
     *
     * @return boolean
     */
    public function getAllowList() {
        return $this->allow_list;
    }

     /**
     * Set allow_create
     *
     * @param boolean $allow_create
     */
    public function setAllowCreate($allow_create) {
        $this->allow_create = $allow_create;
    }

    /**
     * Get allow_create
     *
     * @return boolean
     */
    public function getAllowCreate() {
        return $this->allow_create;
    }

     /**
     * Set allow_order
     *
     * @param boolean $allow_order
     */
    public function setAllowOrder($allow_order) {
        $this->allow_order = $allow_order;
    }

    /**
     * Get allow_order
     *
     * @return boolean
     */
    public function getAllowOrder() {
        return $this->allow_order;
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
