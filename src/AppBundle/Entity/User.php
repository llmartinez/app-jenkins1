<?php
namespace AppBundle\Entity;
 
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface as UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @UniqueEntity(fields="username", message="error_username_repeated")
 */
class User implements UserInterface, AdvancedUserInterface, \Serializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\ManyToMany(targetEntity="Role")
     * @ORM\JoinTable(name="user_role",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     */
    private $user_role;

    /**
     * @ORM\Column(name="role_id", type="integer")
     */
    private $roleId;

    /**
     * @ORM\Column(name="category_service", type="integer")
     * @Assert\NotBlank()
     */
    private $categoryService;

    /**
     * @ORM\Column(name="language", type="integer")
     * @Assert\NotBlank()
     */
    private $language;

    /**
     * @ORM\Column(name="country", type="integer")
     * @Assert\NotBlank()
     */
    private $country;

    /**
     * @ORM\Column(name="status", type="boolean")
     * @Assert\NotBlank()
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $salt;

    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email1;

    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email2;

    /**
     * @ORM\Column(type="integer", length=11)
     * @Assert\NotBlank()
     */
    private $phoneNumber1;

    /**
     * @ORM\Column(type="integer", length=11)
     */
    private $phoneNumber2;

    /**
     * @ORM\Column(type="integer", length=11)
     */
    private $mobileNumber1;

    /**
     * @ORM\Column(type="integer", length=11)
     */
    private $mobileNumber2;

    /**
     * @ORM\Column(type="integer", length=11)
     */
    private $fax;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $region;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $address;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $postalCode;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(name="modified_at", type="datetime", nullable=true)
     */
    private $modifiedAt;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     */
    private $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="modified_by", referencedColumnName="id", nullable=true)
     */
    private $modifiedBy;

    public function __construct()
    {
        $this->status = '1';
        //$this->salt = md5(uniqid(null, true));
    }

    public function getRoles() {
        return $this->user_role->toArray();
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

    public function getRoleId()
    {
        return $this->roleId;
    }

    public function setRoleId($roleId)
    {
        $this->roleId = $roleId;

        return $this;
    }

    /**
     * Get user_roles
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getUserRole() {
        return $this->user_role;
    }

    public function equals(UserInterface $user) {
        return $this->getUsername() == $user->getUsername();
    }

    public function eraseCredentials()
    {
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        if($this->status == 0) return false;
        else return true;
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            //$this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            //$this->salt
            ) = unserialize($serialized);
    }

    public function getId()
    {
        return $this->id;
    }
    
    public function setCategoryService($categoryService)
    {
        $this->categoryService = $categoryService;

        return $this;
    }

    public function getCategoryService()
    {
        return $this->categoryService;
    }
    
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    public function getCountry()
    {
        return $this->country;
    }
    
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    public function getLanguage()
    {
        return $this->language;
    }

    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }
    public function getUsername()
    {
        return $this->username;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function setEmail1($email1)
    {
        $this->email1 = $email1;

        return $this;
    }

    public function getEmail1()
    {
        return $this->email1;
    }

    public function setEmail2($email2)
    {
        $this->email2 = $email2;

        return $this;
    }

    public function getEmail2()
    {
        return $this->email2;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    public function setModifiedBy($modifiedBy)
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    public function to_json() {

        $json = array('id'       => $this->id,
                      'roleId'   => $this->roleId, 
                      'country'  => $this->country, 
                      'language' => $this->language, 
                      'status'   => $this->status, 
                      'username' => $this->username, 
                      'email'    => $this->email,
                      'categoryService' => $this->categoryService
                      );
        return $json;
    }

    public function __toString()
    {
        return $this->getUsername();
    }

}
