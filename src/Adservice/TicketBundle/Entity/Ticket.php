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
     * @var integer $status
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\TicketBundle\Entity\Status")
     */
    private $status;

    /**
     * @var integer $importance
     *
     * @ORM\Column(name="importance", type="integer")
     */
    // private $importance;

    /**
     * @var integer $workshop
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\WorkshopBundle\Entity\Workshop", inversedBy="tickets")
     */
    private $workshop;

    /**
     * @var integer $car
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\CarBundle\Entity\Car")
     */
    private $car;

    /**
     * @var string $owner
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\UserBundle\Entity\User")
     */
    private $owner;

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
     * @var string $owner
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\UserBundle\Entity\User")
     */
    private $assigned_to;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

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
     * @param integer $importance
     */
    // public function setImportance($importance) {
    //     $this->importance = $importance;
    // }

    /**
     * Get importance
     *
     * @return integer
     */
    // public function getImportance() {
    //     return $this->importance;
    // }

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
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle() {
        return $this->title;
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
        return $this->title;
    }

    public function __construct() {
        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
    }

//    public function jsonSerialize() {
//        return [
//            'id' => $this->getId(),
//            'title' => $this->getTitle(),
//            'workshop' => $this->getWorkshop()->getName(),
//            'date' => $this->getCreatedAt()->format('d/m/Y'),
//        ];
//    }

    /**
     * Parsea los camposa a formato json
     * @return Array
     */
    public function to_json() {

        $json = array('id'      => $this->getId(),
                      'title'   => $this->getTitle(),
                      'workshop'=> $this->getWorkshop()->getName(),
                      'date'    => $this->getCreatedAt()->format('d/m/Y'));
        return $json;
    }

}
