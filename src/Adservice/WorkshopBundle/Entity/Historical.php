<?php

namespace Adservice\WorkshopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adservice\WorkshopBundle\Entity\Historical
 *
 * @ORM\Table(name="historical")
 * @ORM\Entity
 */
class Historical {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer $workshopId
     *
     * @ORM\Column(name="workshop_id", type="integer")
     */
    private $workshopId;

    /**
     * @var integer $partnerId
     *
     * @ORM\Column(name="partner_id", type="integer")
     */
    private $partnerId;

    /**
     * @var datetime $dateOrder
     *
     * @ORM\Column(name="date_order", type="datetime")
     */
    private $dateOrder;

    /**
     * @var integer $status
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;


//  ____  _____ _____ _____ _____ ____  ____    ______ _____ _____ _____ _____  ____  ____
// / ___|| ____|_   _|_   _| ____|  _ \/ ___|  / / ___| ____|_   _|_   _| ____||  _ \/ ___|
// \___ \|  _|   | |   | | |  _| | |_) \___ \ / / |  _|  _|   | |   | | |  _|  | |_) \___ \
//  ___) | |___  | |   | | | |___|  _ < ___) / /| |_| | |___  | |   | | | |___ |  _ < ___) |
// |____/|_____| |_|   |_| |_____|_| \_\____/_/  \____|_____| |_|   |_| |_____||_| \_\____/

    public function __toString() {
        return $this->getName();
    }

   /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set workshopId
     *
     * @param integer $workshopId
     */
    public function setWorkShopId($workshopId) {
        $this->workshopId = $workshopId;
    }

    /**
     * Get workshopId
     *
     * @return integer
     */
    public function getWorkShopId() {
        return $this->workshopId;
    }

    /**
     * Set partnerId
     *
     * @param integer $partnerId
     */
    public function setPartnerId($partnerId) {
        $this->partnerId = $partnerId;
    }

    /**
     * Get partnerId
     *
     * @return integer
     */
    public function getPartnerId() {
        return $this->partnerId;
    }
    
    /**
     * Set dateOrder
     *
     * @param datetime $dateOrder
     */
    public function setDateOrder($dateOrder) {
        $this->dateOrder = $dateOrder;
    }

    /**
     * Get dateOrder
     *
     * @return datetime
     */
    public function getDateOrder() {
        return $this->dateOrder;
    }

    /**
     * Set status
     *
     * @param integer $status
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

}
