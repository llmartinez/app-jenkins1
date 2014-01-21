<?php

namespace Adservice\UserBundle\Entity;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Adservice\UserBundle\Entity\User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity
 */
class User implements UserInterface, AdvancedUserInterface, \Serializable {
//class User implements UserInterface {

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
     * @var boolean $active
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var string $sessionID
     *
     * @ORM\Column(name="sessionID", type="string", length=50)
     */
    private $sessionID;

    /**
     * @var string $language
     *
     * @ORM\Column(name="language", type="string", length=2)
     */
    private $language;

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
     * Set sessionID
     *
     * @param string $sessionID
     */
    public function setSessionID($sessionID) {
        $this->sessionID = $sessionID;
    }

    /**
     * Get sessionID
     *
     * @return string 
     */
    public function getSessionID() {
        return $this->sessionID;
    }

    /**
     * Set language
     *
     * @param string $language
     */
    public function setLanguage($language) {
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

    public function equals(\Symfony\Component\Security\Core\User\UserInterface $user) {
//        return md5($this->getUsername()) == md5($user->getUsername());
        return $this->getUsername() == $user->getUsername();
    }

    public function eraseCredentials() {
        
    }

    public function getRoles() {
//        return array('ROLE_USUARIO');
        return $this->user_role->toArray(); //IMPORTANTE: el mecanismo de seguridad de Sf2 requiere ésto como un array  <--------- ???????????????
    }

    public function __toString() {
        return $this->getUsername();
    }

    public function __construct() {
        $this->user_role = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add user_roles
     *
     * @param Secur\LoginBundle\Entity\Role $userRoles
     */
    public function addRole(\Secur\LoginBundle\Entity\Role $userRoles) {
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
                    $this->active, $this->sessionID, $this->language, $this->user_role));
    }

    /**
     * Unserializes the given string in the current User object
     * @param serialized
     */
    public function unserialize($serialized) {
        list($this->id, $this->username, $this->password, $this->salt,
                $this->active, $this->sessionID, $this->language, $this->user_role ) = \json_decode(
                $serialized);
    }

}
