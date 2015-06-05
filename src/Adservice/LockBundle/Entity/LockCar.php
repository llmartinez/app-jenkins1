<?php
namespace Adservice\LockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Adservice\LockBundle\Entity\lock_car
 *
 * @ORM\Table(name="lock_car")
 * @ORM\Entity
 */
class LockCar {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $oldId
     *
     * @ORM\Column(name="old_id", type="string", length=255, nullable=true)
     */
    private $oldId;

    /**
     * @var string $version
     *
     * @ORM\Column(name="version", type="string", length=255)
     */
    private $version;

    /**
     * @var string $model
     *
     * @ORM\Column(name="model", type="string", length=255)
     */
    private $model;

    /**
     * @var string $brand
     *
     * @ORM\Column(name="brand", type="string", length=255)
     */
    private $brand;

    /**
     * @var string $year
     *
     * @ORM\Column(name="year", type="string", length=255, nullable=true)
     */
    private $year;

    /**
     * @var string $vin
     *
     * @ORM\Column(name="vin", type="string", length=255, nullable=true)
     */
    private $vin;

    /**
     * @var string $motor
     *
     * @ORM\Column(name="motor", type="string", length=255, nullable=true)
     */
    private $motor;

//  ____  _____ _____ _____ _____ ____  ____    ______ _____ _____ _____ _____  ____  ____
// / ___|| ____|_   _|_   _| ____|  _ \/ ___|  / / ___| ____|_   _|_   _| ____||  _ \/ ___|
// \___ \|  _|   | |   | | |  _| | |_) \___ \ / / |  _|  _|   | |   | | |  _|  | |_) \___ \
//  ___) | |___  | |   | | | |___|  _ < ___) / /| |_| | |___  | |   | | | |___ |  _ < ___) |
// |____/|_____| |_|   |_| |_____|_| \_\____/_/  \____|_____| |_|   |_| |_____||_| \_\____/


    public function __toString() {
        $car = $this->getBrand().' '.$this->getModel().''.$this->getVersion();
        return $car;
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
     * Set oldId
     *
     * @param string $oldId
     */
    public function setOldId($oldId) {
        $this->oldId = $oldId;
    }

    /**
     * Get oldId
     *
     * @return string
     */
    public function getOldId() {
        return $this->oldId;
    }

    /**
     * Set version
     *
     * @param string $version
     */
    public function setVersion($version) {
        $this->version = $version;
    }

    /**
     * Get version
     *
     * @return string
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * Set model
     *
     * @param string $model
     */
    public function setModel($model) {
        $this->model = $model;
    }

    /**
     * Get model
     *
     * @return string
     */
    public function getModel() {
        return $this->model;
    }

    /**
     * Set brand
     *
     * @param string $brand
     */
    public function setBrand($brand) {
        $this->brand = $brand;
    }

    /**
     * Get brand
     *
     * @return string
     */
    public function getBrand() {
        return $this->brand;
    }

    /**
     * Set year
     *
     * @param string $year
     */
    public function setYear($year) {
        $this->year = $year;
    }

    /**
     * Get year
     *
     * @return string
     */
    public function getYear() {
        return $this->year;
    }

    /**
     * Set vin
     *
     * @param string $vin
     */
    public function setVin($vin) {
        $this->vin = $vin;
    }

    /**
     * Get vin
     *
     * @return string
     */
    public function getVin() {
        return $this->vin;
    }

    /**
     * Set motor
     *
     * @param string $motor
     */
    public function setMotor($motor) {
        $this->motor = $motor;
    }

    /**
     * Get motor
     *
     * @return string
     */
    public function getMotor() {
        return $this->motor;
    }
}
