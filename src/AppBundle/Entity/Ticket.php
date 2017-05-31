<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\User;
use AppBundle\Entity\Workshop;

/**
 * AppBundle\Entity\Ticket
 *
 * @ORM\Entity
 * @ORM\Table(name="ticket")
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
     * @var integer $pending
     *
     * @ORM\Column(name="pending", type="integer")
     */
    private $pending;


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
     * @param Workshop $workshop
     */
    public function setWorkshop(Workshop $workshop) {
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
     * Set pending
     *
     * @param integer $pending
     */
    public function setPending($pending) {
        $this->pending = $pending;
    }

    /**
     * Get pending
     *
     * @return integer
     */
    public function getPending() {
        return $this->pending;
    }


}
