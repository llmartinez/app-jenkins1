<?php

namespace Adservice\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Adservice\UtilBundle\Entity\Country;
use Adservice\UtilBundle\Entity\Language;
use Adservice\UtilBundle\Entity\Region;
use Adservice\UtilBundle\Entity\Province;
use Adservice\PartnerBundle\Entity\Partner;

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
     * @var string $city
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @var string $phone_number_1
     *
     * @ORM\Column(name="phone_number_1", type="string", length=9, nullable=true)
     */
    private $phone_number_1;

    /**
     * @var string $phone_number_2
     *
     * @ORM\Column(name="phone_number_2", type="string", length=9, nullable=true)
     */
    private $phone_number_2;

    /**
     * @var string $movile_number_1
     *
     * @ORM\Column(name="movile_number_1", type="string", length=9, nullable=true)
     */
    private $movile_number_1;

    /**
     * @var string $movile_number_2
     *
     * @ORM\Column(name="movile_number_2", type="string", length=9, nullable=true)
     */
    private $movile_number_2;

    /**
     * @var string $fax
     *
     * @ORM\Column(name="fax", type="string", length=9, nullable=true)
     */
    private $fax;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email_1", type="string", length=255)
     * @Assert\Email()
     */
    private $email_1;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email_2", type="string", length=255, nullable=true)
     * @Assert\Email()
     */
    private $email_2;

    /**
     * @var string $dni
     *
     * @ORM\Column(name="dni", type="string", length=9)
     */
    private $dni;

    /**
     * @var boolean $active
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

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
     * @var string $workshp
     *
     * @ORM\ManyToOne(targetEntity="Adservice\WorkshopBundle\Entity\Workshop", inversedBy="users")
     */
    private $workshop;

    /**
     * @var string $country
     *
     * @ORM\ManyToOne(targetEntity="Adservice\UtilBundle\Entity\Country")
     */
    private $country;

    /**
     * @var string $language
     *
     * @ORM\ManyToOne(targetEntity="Adservice\UtilBundle\Entity\Language")
     */
    private $language;

    /**
     *
     * @var type
     * @ORM\ManyToOne(targetEntity="Adservice\PartnerBundle\Entity\Partner", inversedBy="users")
     */
    private $partner;

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

    public function getName() {
        return $this->name;
    }

    public function getSurname() {
        return $this->surname;
    }

    public function getCity() {
        return $this->city;
    }

    public function getProvince() {
        return $this->province;
    }

    public function getPhoneNumber1() {
        return $this->phone_number_1;
    }

    public function getPhoneNumber2() {
        return $this->phone_number_2;
    }

    public function getMovileNumber1() {
        return $this->movile_number_1;
    }

    public function getMovileNumber2() {
        return $this->movile_number_2;
    }

    public function getFax() {
        return $this->fax;
    }

    public function getEmail1() {
        return $this->email_1;
    }

    public function getEmail2() {
        return $this->email_2;
    }

    public function getDni() {
        return $this->dni;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setSurname($surname) {
        $this->surname = $surname;
    }

    public function setCity($city) {
        $this->city = $city;
    }

    public function setProvince(\Adservice\UtilBundle\Entity\Province $province) {
        $this->province = $province;
    }

    public function setPhoneNumber1($phone_number_1) {
        $this->phone_number_1 = $phone_number_1;
    }

    public function setPhoneNumber2($phone_number_2) {
        $this->phone_number_2 = $phone_number_2;
    }

    public function setMovileNumber1($movile_number_1) {
        $this->movile_number_1 = $movile_number_1;
    }

    public function setMovileNumber2($movile_number_2) {
        $this->movile_number_2 = $movile_number_2;
    }

    public function setFax($fax) {
        $this->fax = $fax;
    }

    public function setEmail1($email_1) {
        $this->email_1 = $email_1;
    }

    public function setEmail2($email_2) {
        $this->email_2 = $email_2;
    }

    public function setDni($dni) {
        $this->dni = $dni;
    }

    public function equals(\Symfony\Component\Security\Core\User\UserInterface $user) {
        return $this->getUsername() == $user->getUsername();
    }

    public function eraseCredentials() {

    }

    public function getRoles() {
        return $this->user_role->toArray(); //IMPORTANTE: el mecanismo de seguridad de Sf2 requiere ésto como un array 
    }

    public function __toString() {
        return $this->getName() . ' ' . $this->getSurname();
    }

    public function __construct() {
        $this->user_role = new \Doctrine\Common\Collections\ArrayCollection();
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

    public function getRegion() {
        return $this->region;
    }

    public function setRegion(\Adservice\UtilBundle\Entity\Region $region) {
        $this->region = $region;
    }

    public function getWorkshop() {
        return $this->workshop;
    }

    public function setWorkshop(\Adservice\WorkshopBundle\Entity\Workshop $workshop) {
        $this->workshop = $workshop;
    }

    public function getCountry() {
        return $this->country;
    }

    public function setCountry(\Adservice\UtilBundle\Entity\Country $country) {
        $this->country = $country;
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
     * Set modify_by
     *
     * @param user $modify_by
     */
    public function setModifyBy(\Adservice\UserBundle\Entity\User $user) {
        $this->modify_by = $user;
    }

    /**
     * Get modify_by
     *
     * @return integer
     */
    public function getModifyBy() {
        return $this->modify_by;
    }

    public function getPartner() {
        return $this->partner;
    }

    public function setPartner(\Adservice\PartnerBundle\Entity\Partner $partner) {
        $this->partner = $partner;
    }
}