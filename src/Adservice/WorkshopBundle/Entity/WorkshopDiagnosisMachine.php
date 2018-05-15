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
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Workshop", inversedBy="diagnosis_machines")
     */
    private $workshop;

    /**
     * @var integer $diagnosis_machine
     *
     * @ORM\Column(name="diagnosis_machine_id", type="integer")
     * @ORM\Id
     */
    private $diagnosis_machine;


    /**
     * Set workshop_id
     *
     * @param string $workshop
     */
    public function setWorkshopId($workshop) {
        $this->workshop = $workshop;
    }

    /**
     * Get workshop_id
     *
     * @return string
     */
    public function getWorkshopId() {
        return $this->workshop;
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