<?php

namespace Adservice\TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\User;

/**
 * Adservice\TicketBundle\Entity\Ticket
 *
 * @ORM\Table(name="ticket")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\TicketRepository")
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
     * @var integer $workshop
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Workshop", inversedBy="tickets")
     */
    private $workshop;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }




    /**
     * Set workshop
     *
     * @param AppBundle\Entity\Workshop $workshop
     */
    public function setWorkshop(AppBundle\Entity\Workshop $workshop) {
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


}
