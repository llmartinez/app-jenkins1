<?php

namespace Adservice\UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adservice\UtilBundle\Entity\Province
 *
 * @ORM\Table(name="province")
 * @ORM\Entity
 */
class Province {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $province
     *
     * @ORM\Column(name="province", type="string", length=255)
     */
    private $province;

    /**
     * @var string $region
     *
     * @ORM\ManyToOne(targetEntity="Adservice\UtilBundle\Entity\Region")
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
     * Set province
     *
     * @param string $province
     */
    public function setProvince($province)
    {
        $this->province = $province;
    }

    /**
     * Get province
     *
     * @return string 
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * Set region
     *
     * @param string $region
     */
    public function setRegion(\Adservice\UtilBundle\Entity\Region $region)
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
        return $this->getProvince();
    }
    
//    public function jsonSerialize() {
//        return [
//            'id' => $this->getId(),
//            'region' => $this->getRegion(),
//            'province' => $this->getProvince()
//        ];
//    }
    public function to_json(){
        $json = array('id'      => $this->getId(),
                      'region'  => $this->getRegion(),
                      'province'=> $this->getProvince());
        return $json;
    }
}