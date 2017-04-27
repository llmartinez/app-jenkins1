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
 * @UniqueEntity(fields="token", message="error_token_repeated")
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
     * @ORM\Column(name="service", type="json_array", nullable=true)
     */
    private $service;

    /**
     * @ORM\Column(name="restriction", type="json_array", nullable=true)
     */
    private $restriction;

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
     * @ORM\Column(name="status", type="integer")
     * @Assert\NotBlank()
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    private $username;

    /**
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
     * @ORM\Column(type="string", nullable=true, unique=true)
     */
    private $token;

    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email1;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     * @Assert\Email()
     */
    private $email2;

    /**
     * @ORM\Column(type="integer", length=11)
     * @Assert\NotBlank()
     */
    private $phoneNumber1;

    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    private $phoneNumber2;

    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    private $mobileNumber1;

    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    private $mobileNumber2;

    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    private $fax;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $region;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $city;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $postalCode;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(name="modified_at", type="datetime", nullable=true, nullable=true)
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
    /**
     *
     * @var string $partner
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Partner", mappedBy="user")
     */
    private $partner;

    /**
     *
     * @var string $workshop
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Workshop", mappedBy="user")
     */
    private $workshop;

    /**
     *
     * @var string $commercial
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Commercial", mappedBy="user")
     */
    private $commercial;

    public function __construct()
    {
        $this->status = '1';
        $this->createdAt = new \DateTime();
        //$this->salt = md5(uniqid(null, true));
    }

    public function getRoles() {
        return $this->user_role->toArray();
    }
    public function getUserRoles() {
        return $this->user_role;
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
    
    public function setService($service)
    {
        $this->service = $service;

        return $this;
    }

    public function getService()
    {
        return $this->service;
    }
    
    public function setRestriction($restriction)
    {
        $this->restriction = $restriction;

        return $this;
    }

    public function getRestriction()
    {
        return $this->restriction;
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

    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    public function getToken()
    {
        return $this->token;
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

    public function setPhoneNumber1($phoneNumber1)
    {
        $this->phoneNumber1 = $phoneNumber1;

        return $this;
    }

    public function getPhoneNumber1()
    {
        return $this->phoneNumber1;
    }

    public function setPhoneNumber2($phoneNumber2)
    {
        $this->phoneNumber2 = $phoneNumber2;

        return $this;
    }

    public function getPhoneNumber2()
    {
        return $this->phoneNumber2;
    }

    public function setMobileNumber1($mobileNumber1)
    {
        $this->mobileNumber1 = $mobileNumber1;

        return $this;
    }

    public function getMobileNumber1()
    {
        return $this->mobileNumber1;
    }

    public function setMobileNumber2($mobileNumber2)
    {
        $this->mobileNumber2 = $mobileNumber2;

        return $this;
    }

    public function getMobileNumber2()
    {
        return $this->mobileNumber2;
    }

    public function setFax($fax)
    {
        $this->fax = $fax;

        return $this;
    }

    public function getFax()
    {
        return $this->fax;
    }

    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }

    public function getRegion()
    {
        return $this->region;
    }

    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getPostalCode()
    {
        return $this->postalCode;
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

    public function setPartner($partner)
    {
        $this->partner = $partner;

        return $this;
    }

    public function getPartner()
    {
        return $this->partner;
    }

    public function setWorkshop($workshop)
    {
        $this->workshop = $workshop;

        return $this;
    }

    public function getWorkshop()
    {
        return $this->workshop;
    }

    public function setCommercial($commercial)
    {
        $this->commercial = $commercial;

        return $this;
    }

    public function getCommercial()
    {
        return $this->commercial;
    }

    public function to_json() {

        $json = array('id'       => $this->id,
                      'roleId'   => $this->roleId, 
                      'country'  => $this->country, 
                      'language' => $this->language, 
                      'status'   => $this->status, 
                      'username' => $this->username, 
                      'email'    => $this->email,
                      'service'  => $this->service
                      );
        return $json;
    }

    public function __toString()
    {
        return $this->getUsername();
    }

}
