<?php

namespace Adservice\WorkshopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adservice\WorkshopBundle\Entity\DiagnosisMachine
 *
 * @ORM\Table(name="workshop_diagnosis_machine")
 * @ORM\Entity
 */
class WorkshopDiagnosisMachine
{
    /**
     * @var integer $workshop_id
     *
     * @ORM\Column(name="workshop_id", type="integer")
     * @ORM\Id
     */
    private $workshop_id;

    /**
     * @var integer $diagnosis_machine_id
     *
     * @ORM\Column(name="diagnosis_machine_id", type="integer")
     * @ORM\Id
     */
    private $diagnosis_machine_id;


    /**
     * Set workshop_id
     *
     * @param string $workshop_id
     */
    public function setWorkshopId($workshop_id) {
        $this->workshop_id = $workshop_id;
    }

    /**
     * Get workshop_id
     *
     * @return string
     */
    public function getWorkshopId() {
        return $this->workshop_id;
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