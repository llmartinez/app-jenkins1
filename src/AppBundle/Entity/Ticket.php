<?php
namespace AppBundle\Entity;
 
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
 
/**
 * @ORM\Table(name="ticket")
 * @ORM\Entity
 */
class Ticket
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Workshop")
     * @ORM\JoinColumn(name="workshop_id", referencedColumnName="id")
     */
//todo    private $workshop;

    /**
     * @ORM\ManyToOne(targetEntity="Car")
     * @ORM\JoinColumn(name="car_id", referencedColumnName="id")
     */
//todo    private $car;

    /**
     * @ORM\Column(name="category_service", type="integer")
     */
    private $categoryService;

    /**
     * @ORM\Column(name="country", type="integer")
     */
    private $country;

    /**
     * @ORM\Column(name="language", type="integer")
     */
    private $language;

    /**
     * @ORM\Column(name="status", type="integer")
     */
    private $status;
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $blocked;
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isCall;
    /**
     * @ORM\Column(name="importance", type="integer")
     */
    private $importance;
    /**
     * @ORM\Column(name="subsystem", type="integer", nullable=true)
     */
    private $subsystem;

    /**
     * @ORM\Column(type="string")
     */
    private $description;
 
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $solution;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     */
//todo    private $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="modified_by", referencedColumnName="id")
     */
//todo    private $modifiedBy;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="assigned_to", referencedColumnName="id", nullable=true)
     */
//todo    private $assignedTo;
  
    /**
     * @ORM\OneToMany(
     *      targetEntity="Comment",
     *      mappedBy="ticket",
     *      orphanRemoval=true
     * )
     * @ORM\OrderBy({"createdAt" = "ASC"})
     */
//todo    private $comments;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(name="modified_at", type="datetime", nullable=true)
     */
    private $modifiedAt;

    /**
     * @ORM\Column(name="closed_at", type="datetime", nullable=true)
     */
    private $closedAt;
 

    /* Construct */
    public function __construct()
    {
//todo        $this->comments = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getId();
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
    public function setBlocked($blocked)
    {
        $this->blocked = $blocked;

        return $this;
    }

    public function getBlocked()
    {
        return $this->blocked;
    }
    public function setIsCall($isCall)
    {
        $this->isCall = $isCall;

        return $this;
    }

    public function getIsCall()
    {
        return $this->isCall;
    }
    public function setImportance($importance)
    {
        $this->importance = $importance;

        return $this;
    }

    public function getImportance()
    {
        return $this->importance;
    }
    public function setSubsystem($subsystem)
    {
        $this->subsystem = $subsystem;

        return $this;
    }

    public function getSubsystem()
    {
        return $this->subsystem;
    }
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }
    public function setSolution($solution)
    {
        $this->solution = $solution;

        return $this;
    }

    public function getSolution()
    {
        return $this->solution;
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
    public function setClosedAt($closedAt)
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    public function getClosedAt()
    {
        return $this->closedAt;
    }
}
?>