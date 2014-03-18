<?php

namespace Adservice\TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Adservice\UserBundle\Entity\User;

/**
 * Adservice\TicketBundle\Entity\Ticket
 *
 * @ORM\Table(name="ticket")
 * @ORM\Entity(repositoryClass="Adservice\TicketBundle\Entity\TicketRepository")
 */
class Ticket {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $owner
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\UserBundle\Entity\User")
     */
    private $owner;

    /**
     * @var string $assigned_to
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\UserBundle\Entity\User")
     */
    private $assigned_to;

    /**
     * @var string $blocked_by
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\UserBundle\Entity\User")
     */
    private $blocked_by;

    /**
     * @var integer $workshop
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\WorkshopBundle\Entity\Workshop", inversedBy="tickets")
     */
    private $workshop;

    /**
     * @var integer $status
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\TicketBundle\Entity\Status")
     */
    private $status;

    /**
     * @var integer $importance
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\TicketBundle\Entity\Importance")
     */
    private $importance;

    /**
     * @var string $subsystem
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\TicketBundle\Entity\Subsystem")
     */
    private $subsystem;

    /**
     * @var integer $car
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\CarBundle\Entity\Car", inversedBy="ticket")
     */
    private $car;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string $solution
     *
     * @ORM\Column(name="solution", type="string", length=255, nullable="true")
     */
    private $solution;

    /**
     * @var date $created_at
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $created_at;

    /**
     * @var date $modified_at
     *
     * @ORM\Column(name="modified_at", type="datetime")
     */
    private $modified_at;

    /**
     * @var string $modified_by
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\UserBundle\Entity\User")
     */
    private $modified_by;

    /**
     * @var string $posts
     *
     * @ORM\OneToMany(targetEntity="Adservice\TicketBundle\Entity\Post", mappedBy="ticket")
     */
    private $posts;

    /**
     * @var string $cars
     *
     * @ORM\OneToMany(targetEntity="Adservice\CarBundle\Entity\Car", mappedBy="ticket")
     */
    private $cars;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set owner
     *
     * @param \Adservice\UserBundle\Entity\User $owner
     */
    public function setOwner(\Adservice\UserBundle\Entity\User $owner) {
        $this->owner = $owner;
    }

    /**
     * Get owner
     *
     * @return string
     */
    public function getOwner() {
        return $this->owner;
    }

    /**
     * Set assigned_to
     *
     * @param \Adservice\UserBundle\Entity\User $assigned_to
     */
    public function setAssignedTo(\Adservice\UserBundle\Entity\User $assigned_to = null) {
        $this->assigned_to = $assigned_to;
    }

    /**
     * Get assigned_to
     *
     * @return string
     */
    public function getAssignedTo() {
        return $this->assigned_to;
    }

    /**
     * Set blocked_by
     *
     * @param \Adservice\UserBundle\Entity\User $blocked_by
     */
    public function setBlockedBy(\Adservice\UserBundle\Entity\User $blocked_by = null) {
        $this->blocked_by = $blocked_by;
    }

    /**
     * Get blocked_by
     *
     * @return string
     */
    public function getBlockedBy() {
        return $this->blocked_by;
    }

    /**
     * Set workshop
     *
     * @param \Adservice\WorkshopBundle\Entity\Workshop $workshop
     */
    public function setWorkshop(\Adservice\WorkshopBundle\Entity\Workshop $workshop) {
        $this->workshop = $workshop;
    }

    /**
     * Get workshop
     *
     * @return integer
     */
    public function getWorkshop() {
        return $this->workshop;
    }

    /**
     * Set status
     *
     * @param \Adservice\TicketBundle\Entity\Status $status
     */
    public function setStatus($status) {
        $this->status = $status;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Set importance
     *
     * @param \Adservice\TicketBundle\Entity\Imoprtance $importance
     */
     public function setImportance($importance) {
         $this->importance = $importance;
     }

    /**
     * Get importance
     *
     * @return integer
     */
     public function getImportance() {
         return $this->importance;
     }

    /**
     * Set subsystem
     *
     * @param \Adservice\TicketBundle\Entity\Subsystem $subsystem
     */
    public function setSubsystem(\Adservice\TicketBundle\Entity\SubSystem $subsystem) {
        $this->subsystem = $subsystem;
    }

    /**
     * Get subsystem
     *
     * @return integer
     */
    public function getSubsystem() {
        return $this->subsystem;
    }

    /**
     * Set car
     *
     * @param \Adservice\CarBundle\Entity\Car $car
     *
     */
    public function setCar(\Adservice\CarBundle\Entity\Car $car) {
        $this->car = $car;
    }

    /**
     * Get car
     *
     * @return integer
     */
    public function getCar() {
        return $this->car;
    }

    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set solution
     *
     * @param string $solution
     */
    public function setSolution($solution) {
        $this->solution = $solution;
    }

    /**
     * Get solution
     *
     * @return string
     */
    public function getSolution() {
        return $this->solution;
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
     * @param \Adservice\UserBundle\Entity\User $modified_by
     */
    public function setModifiedBy(\Adservice\UserBundle\Entity\User $modified_by) {
        $this->modified_by = $modified_by;
    }

    /**
     * Get modified_by
     *
     * @return user
     */
    public function getModifiedBy() {
        return $this->modified_by;
    }

    /**
     * Add posts
     *
     * @param Post $posts
     */
    public function addPost($posts) {
        $this->posts[] = $posts;
    }

    /**
     * Get posts
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getPosts() {
        return $this->posts;
    }

    /**
     * Add cars
     *
     * @param Adservice\CarBundle\Entity\Car $cars
     */
    public function addCar(\Adservice\CarBundle\Entity\Car $cars) {
        $this->cars[] = $cars;
    }

    /**
     * Get cars
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getCars() {
        return $this->cars;
    }

    public function __toString() {
        return $this->description;
    }

    public function __construct() {
        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
    }

//    public function jsonSerialize() {
//        return [
//            'id' => $this->getId(),
//            'description' => $this->getDescription(),
//            'workshop' => $this->getWorkshop()->getName(),
//            'date' => $this->getCreatedAt()->format('d/m/Y'),
//        ];
//    }

    /**
     * Parsea los camposa a formato json
     * @return Array
     */
    public function to_json() {
        $car = $this->getCar()->getBrand()." ".$this->getCar()->getModel();

        if ($this->getOwner()->getRoles()[0] == 'ROLE_USER') { $created = 'workshop'; } else { $created = 'assessor'; }

        $json = array('created'     => $created,
                      'id'          => $this->getId(),
                      'date'        => $this->getCreatedAt()->format('d/m/Y'),
                      'car'         => $car,
                      'workshop'    => $this->getWorkshop()->getName(),
                      'description' => $this->getDescription(),
                      );
        return $json;
    }
    /**
     * Parsea los camposa a formato json para el listado de subsystem
     * @return Array
     */
    public function to_json_subsystem() {
/**/
        if (strlen($this->getDescription()) > 20) { $desc = substr($this->getDescription(), 0, 20)."..."; }
        else                                      { $desc = $this->getDescription(); }

        $car = $this->getCar()->getBrand()." ".$this->getCar()->getModel();
        if(strlen($car) > 15) { $car = substr($car, 0, 15)."..."; }

        $json = array('id'          => $this->getId(),
                      'description' => $desc,
                      'car'         => $car,
                      );
        return $json;
    }

}
