<?php

namespace Adservice\TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adservice\TicketBundle\Entity\Incidence
 *
 * @ORM\Table(name="incidence")
 * @ORM\Entity(repositoryClass="Adservice\TicketBundle\Entity\IncidenceRepository")
 */
class Incidence {
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $ticket
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\TicketBundle\Entity\Ticket")
     */
    private $ticket;

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
    private $importance;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string $solution
     *
     * @ORM\Column(name="solution", type="string", length=255)
     */
    private $solution;

    /**
     * @var string $owner
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\UserBundle\Entity\User")
     */
    private $owner;

    /**
     * @var integer $workshop
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\WorkshopBundle\Entity\Workshop")
     */
    private $workshop;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set ticket
     *
     * @param \Adservice\TicketBundle\Entity\Ticket $ticket
     */
    public function setTicket(\Adservice\TicketBundle\Entity\Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get ticket
     *
     * @return string
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * Set status
     *
     * @param \Adservice\TicketBundle\Entity\Status $status
     */
    public function setStatus(\Adservice\TicketBundle\Entity\Status $status)
    {
        $this->status = $status;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set importance
     *
     * @param integer $importance
     */
    public function setImportance($importance)
    {
        $this->importance = $importance;
    }

    /**
     * Get importance
     *
     * @return integer
     */
    public function getImportance()
    {
        return $this->importance;
    }

    /**
     * Set description
     *
     * @param string $solution
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
     * Set solution
     *
     * @param string $solution
     */
    public function setSolution($solution)
    {
        $this->solution = $solution;
    }

    /**
     * Get solution
     *
     * @return string
     */
    public function getSolution()
    {
        return $this->solution;
    }

    /**
     * Set owner
     *
     * @param \Adservice\UserBundle\Entity\User $owner
     */
    public function setOwner(\Adservice\UserBundle\Entity\User $owner)
    {
        $this->owner = $owner;
    }

    /**
     * Get owner
     *
     * @return string
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set workshop
     *
     * @param \Adservice\WorkshopBundle\Entity\Workshop $workshop
     */
    public function setWorkshop(\Adservice\WorkshopBundle\Entity\Workshop $workshop)
    {
        $this->workshop = $workshop;
    }

    /**
     * Get workshop
     *
     * @return integer
     */
    public function getWorkshop()
    {
        return $this->workshop;
    }

    /**
     * Set created_at
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    }

    /**
     * Get created_at
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
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
     * Set modified_by
     *
     * @param \Adservice\UserBundle\Entity\User $modifiedBy
     */
    public function setModifiedBy(\Adservice\UserBundle\Entity\User $modifiedBy)
    {
        $this->modified_by = $modifiedBy;
    }

    /**
     * Get modified_by
     *
     * @return string
     */
    public function getModifiedBy()
    {
        return $this->modified_by;
    }

//    public function jsonSerialize() {
//        return [
//            'id'        => $this->getId(),
//            'title'     => $this->getTicket()->getTitle(),
//            'date'      => $this->getModifiedAt()->format('d/m/Y'),
//            'status'    => $this->getStatus()->getName()
//        ];
//    }

    public function to_json(){
        $json = array('id'        => $this->getId(),
                      'title'     => $this->getTicket()->getTitle(),
                      'date'      => $this->getModifiedAt()->format('d/m/Y'),
                      'status'    => $this->getStatus()->getName());
        return $json;
    }
}