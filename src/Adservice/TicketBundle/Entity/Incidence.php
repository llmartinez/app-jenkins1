<?php

namespace Adservice\TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adservice\TicketBundle\Entity\Incidence
 *
 * @ORM\Table(name="incidence")
 * @ORM\Entity
 */
class Incidence
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
}