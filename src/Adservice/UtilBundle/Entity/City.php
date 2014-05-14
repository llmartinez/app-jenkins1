<?php

namespace Adservice\UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adservice\UtilBundle\Entity\City
 *
 * @ORM\Table(name="city")
 * @ORM\Entity
 */
class City {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $city
     *
     * @ORM\Column(name="city", type="string", length=255)
     */
    private $city;

    /**
     * @var string $region
     *
     * @ORM\Column(name="region", type="string")
     */
    private $region;


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
     * Set city
     *
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set region
     *
     * @param string $region
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    /**
     * Get region
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }
    
    public function __toString() {
        return $this->getCity();
    }
    
//    public function jsonSerialize() {
//        return [
//            'id' => $this->getId(),
//            'region' => $this->getRegion(),
//            'city' => $this->getCity()
//        ];
//    }
    public function to_json(){
        $json = array('id'      => $this->getId(),
                      'region'  => $this->getRegion(),
                      'city'=> $this->getCity());
        return $json;
    }
}