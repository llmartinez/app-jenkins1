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
     * @var string $document
     *
     * @ORM\OneToOne(targetEntity="Adservice\UtilBundle\Entity\Document", mappedBy="post")
     */
    private $document;  

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
     * Set $owner
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
     * @return \Adservice\UserBundle\Entity\User
     */
    public function getOwner()
    {
        return $this->owner;
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

    /**
     * Get created_at
     *
     * @return date 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set document
     *
     * @param Adservice\UtilBundle\Entity\Document $document
     */
    public function setDocument(\Adservice\UtilBundle\Entity\Document $document)
    {
        $this->document = $document;
    }

    /**
     * Get document
     *
     * @return Adservice\UtilBundle\Entity\Document 
     */
    public function getDocument()
    {
        return $this->document;
    }
}