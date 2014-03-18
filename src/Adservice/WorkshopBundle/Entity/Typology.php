<?php

namespace Adservice\WorkshopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adservice\WorkshopBundle\Entity\Typology
 *
 * @ORM\Table(name="typology")
 * @ORM\Entity
 */
class Typology {

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
    private $typology;

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
    public function setTypology($typology) {
        $this->typology = $typology;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getTypology() {
        return $this->typology;
    }


    public function __toString() {
        return $this->getTypology();
    }

}


//namespace Adservice\WorkshopBundle\Entity;
//
//use Doctrine\ORM\Mapping as ORM;
//
///**
// * Adservice\WorkshopBundle\Entity\Typology
// *
// * @ORM\Table(name="typology")
// * @ORM\Entity
// */
//class Typology
//{
//    /**
//     * @var integer $id
//     *
//     * @ORM\Column(name="id", type="integer")
//     * @ORM\Id
//     * @ORM\GeneratedValue(strategy="AUTO")
//     */
//    private $id;
//
//    /**
//     * @var string $typology
//     *
//     * @ORM\Column(name="typology", type="string", length=255)
//     */
//    private $typology;
//
//
//    /**
//     * Get id
//     *
//     * @return integer 
//     */
//    public function getId()
//    {
//        return $this->id;
//    }
//
//    /**
//     * Set typology
//     *
//     * @param string $typology
//     */
//    public function setTypology($typology)
//    {
//        $this->typology = $typology;
//    }
//
//    /**
//     * Get typology
//     *
//     * @return string 
//     */
//    public function getTypology()
//    {
//        return $this->typology;
//    }
//}