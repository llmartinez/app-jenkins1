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
     * @var integer $workshop_order_id
     *
     * @ORM\Column(name="workshop_order_id", type="integer")
     * @ORM\Id
     */
    private $workshop_order_id;

    /**
     * @var integer $diagnosis_machine_id
     *
     * @ORM\Column(name="diagnosis_machine_id", type="integer")
     * @ORM\Id
     */
    private $diagnosis_machine_id;


    /**
     * Set workshop_order_id
     *
     * @param string $workshop_order_id
     */
    public function setWorkshopOrderId($workshop_order_id) {
        $this->workshop_order_id = $workshop_order_id;
    }

    /**
     * Get workshop_order_id
     *
     * @return string
     */
    public function getWorkshopOrderId() {
        return $this->workshop_order_id;
    }

    /**
     * Set diagnosis_machine_id
     *
     * @param string $diagnosis_machine_id
     */
    public function setDiagnosisMachineId($diagnosis_machine_id) {
        $this->diagnosis_machine_id = $diagnosis_machine_id;
    }

    /**
     * Get diagnosis_machine_id
     *
     * @return string
     */
    public function getDiagnosisMachineId() {
        return $this->diagnosis_machine_id;
    }
}