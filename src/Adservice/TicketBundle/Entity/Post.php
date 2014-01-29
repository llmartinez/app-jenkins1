<?php

namespace Adservice\TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adservice\TicketBundle\Entity\Post
 *
 * @ORM\Table(name="post")
 * @ORM\Entity
 */
class Post
{
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
     * @var text $message
     *
     * @ORM\Column(name="message", type="text")
     */
    private $message;

    /**
     * @var string $created_by
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\UserBundle\Entity\User")
     */
    private $created_by;

    /**
     * @var date $created_at
     *
     * @ORM\Column(name="date_created", type="date")
     */
    private $created_at;

    /**
     * @var date $modified_at
     *
     * @ORM\Column(name="date_modified", type="date")
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
     * Set message
     *
     * @param \Adservice\TicketBundle\Entity\Post $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Get message
     *
     * @return text 
     */
    public function getMessage()
    {
        return $this->message;
    }
    
    /**
     * Set $created_by
     *
     * @param \Adservice\UserBundle\Entity\User $created_by
     */
    public function setCreatedBy(\Adservice\UserBundle\Entity\User $created_by)
    {
        $this->created_by = $created_by;
    }

    /**
     * Get created_by
     *
     * @return \Adservice\UserBundle\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     * Set $created_at
     *
     * @param datetime $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * Get created_at
     *
     * @return datetime 
     */
    public function getcreated_at()
    {
        return $this->created_at;
    }

    /**
     * Set modified_by
     *
     * @param \Adservice\UserBundle\Entity\User $modified_by
     */
    public function setModifiedBy(\Adservice\UserBundle\Entity\User $modified_by)
    {
        $this->modified_by = $modified_by;
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
}