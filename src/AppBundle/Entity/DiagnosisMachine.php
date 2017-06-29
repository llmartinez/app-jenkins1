<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AppBundle\Entity\DiagnosisMachine
 *
 * @ORM\Table(name="diagnosis_machine")
 * @ORM\Entity
 */
class DiagnosisMachine {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var boolean $active
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

//    /**
//     * @var string $country
//     *
//     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Country")
//     */
//    private $country;

    /**
     * @var string $category_service
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CategoryService")
     */
    private $category_service;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set active
     *
     * @param boolean $active
     */
    public function setActive($active) {
        $this->active = $active;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive() {
        return $this->active;
    }

//    /**
//     * Set country
//     *
//     * @param string $country
//     */
//    public function setCountry(\Adservice\UtilBundle\Entity\Country $country) {
//        $this->country = $country;
//    }
//
//    /**
//     * Get country
//     *
//     * @return string
//     */
//    public function getCountry() {
//        return $this->country;
//    }


    /**
     * Set category_service
     *
     * @param string $category_service
     */
    public function setCategoryService(CategoryService $category_service) {
        $this->category_service = $category_service;
    }

    /**
     * Get category_service
     *
     * @return string
     */
    public function getCategoryService() {
        return $this->category_service;
    }

    public function __toString() {
        return $this->name;
    }

    public function to_json(){
        $json = array('id'           => $this->getId(),
            'name'         => $this->getName());
        return $json;
    }
}