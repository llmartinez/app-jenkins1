<?php

namespace Adservice\TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adservice\TicketBundle\Entity\Ticket
 *
 * @ORM\Table(name="ticket")
 * @ORM\Entity
 */
class Ticket
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
     * @var string $user
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\UserBundle\Entity\User")
     */
    private $user;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

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
     * @var date $dateCreated
     *
     * @ORM\Column(name="date_created", type="date")
     */
    private $dateCreated;

    /**
     * @var date $dateModified
     *
     * @ORM\Column(name="date_modified", type="date")
     */
    private $dateModified;

    /**
     * @var string $user_modified
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\UserBundle\Entity\User")
     */
    private $userModified;


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
     * Set user
     *
     * @param \Adservice\UserBundle\Entity\User $user
     */
    public function setUser(\Adservice\UserBundle\Entity\User $user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return string 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set status
     *
     * @param \Adservice\TicketBundle\Entity\Status $status
     */
    public function setStatus($status)
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
     * Set dateCreated
     *
     * @param date $dateCreated
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * Get dateCreated
     *
     * @return date 
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set date_modified
     *
     * @param date $dateModified
     */
    public function setDateModified($dateModified)
    {
        $this->dateModified = $dateModified;
    }

    /**
     * Get dateModified
     *
     * @return date 
     */
    public function getDateModified()
    {
        return $this->dateModified;
    }

    /**
     * Set userModified
     *
     * @param \Adservice\UserBundle\Entity\User $userModified
     */
    public function setUserModified(\Adservice\UserBundle\Entity\User $userModified)
    {
        $this->userModified = $userModified;
    }

    /**
     * Get userModified
     *
     * @return string 
     */
    public function getUserModified()
    {
        return $this->userModified;
    }
    
    public function __toString() {
        return $this->title;
    }

}