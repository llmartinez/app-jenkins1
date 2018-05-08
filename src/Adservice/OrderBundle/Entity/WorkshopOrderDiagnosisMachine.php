<?php

namespace Adservice\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adservice\WorkshopBundle\Entity\DiagnosisMachine
 *
 * @ORM\Table(name="workshop_order_diagnosis_machine")
 * @ORM\Entity
 */
class WorkshopOrderDiagnosisMachine
{
    /**
     * @var integer $workshop_order
     *
     * @ORM\ManyToOne(targetEntity="WorkshopOrder", inversedBy="diagnosis_machines")
     * @ORM\Id
     */
    private $workshop_order;

    /**
     * @var integer $diagnosis_machine
     *
     * @ORM\Column(name="diagnosis_machine_id", type="integer")
     * @ORM\Id
     */
    private $diagnosis_machine;


    /**
     * Set workshop_order_id
     *
     * @param string $workshop_order_id
     */
    public function setWorkshopOrder($workshop_order) {
        $this->workshop_order = $workshop_order;
    }

    /**
     * Get workshop_order_id
     *
     * @return string
     */
    public function getWorkshopOrder() {
        return $this->workshop_order;
    }

    /**
     * Set diagnosis_machine_id
     *
     * @param string $diagnosis_machine_id
     */
    public function setDiagnosisMachine($diagnosis_machine) {
        $this->diagnosis_machine = $diagnosis_machine;
    }

    /**
     * Get diagnosis_machine_id
     *
     * @return string
     */
    public function getDiagnosisMachine() {
        return $this->diagnosis_machine;
    }
}