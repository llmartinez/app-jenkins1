<?php

namespace Adservice\PopupBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Adservice\UserBundle\Entity\User;
use Adservice\PopupBundle\Entity\PopupRepository;

/**
 * Adservice\PopupBundle\Entity\Popup
 *
 * @ORM\Table(name="popup")
 * @ORM\Entity(repositoryClass="Adservice\PopupBundle\Entity\PopupRepository")
 */
//class Popup implements \Serializable{
class Popup {
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
     * @var string $description
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var datetime $startdate_at
     *
     * @ORM\Column(name="startdate_at", type="datetime")
     */
    private $startdate_at;

    /**
     * @var datetime $enddate_at
     *
     * @ORM\Column(name="enddate_at", type="datetime")
     */
    private $enddate_at;

    /**
     * @var boolean $active
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var datetime $createt_at
     *
     * @ORM\Column(name="createt_at", type="datetime")
     */
    private $created_at;

    /**
     * @var integer $created_by
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
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Get active
     * @return type
     */
    public function getActive(){
        return $this->active;
    }
    
    /**
     * Set active
     * @param boolean $active
     */
    public function setActive($active){
        $this->active = $active;
    }

    /**
     * Set startdate_at
     *
     * @param datetime $startdateAt
     */
    public function setStartdateAt($startdateAt)
    {
        $this->startdate_at = $startdateAt;
    }

    /**
     * Get startdate_at
     *
     * @return datetime 
     */
    public function getStartdateAt()
    {
        return $this->startdate_at;
    }

    /**
     * Set enddate_at
     *
     * @param datetime $enddateAt
     */
    public function setEnddateAt($enddateAt)
    {
        $this->enddate_at = $enddateAt;
    }

    /**
     * Get enddate_at
     *
     * @return datetime 
     */
    public function getEnddateAt()
    {
        return $this->enddate_at;
    }

    /**
     * Set createt_at
     *
     * @param datetime $createtAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    }

    /**
     * Get createt_at
     *
     * @return datetime 
     */
    public function getCreatedAt()
    {
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
     * @return integer
     */
    public function getCreatedBy() {
        return $this->created_by;
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
        
    /**
     * Campos que apareceran al hacer un json de esta clase
     */
//    public function jsonSerialize() {
//        return [
//            'id' => $this->getId(),
//            'name' => $this->getName(),
//            'description' => $this->getDescription()
//        ];
//    }
    
    public function to_json(){
        $json = array('id'          => $this->getId(),
                      'name'        => $this->getName(),
                      'description' => $this->getDescription());
        return $json;
    }
}