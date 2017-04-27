<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AppBundle\Entity\Typology
 *
 * @ORM\Table(name="w_typology")
 * @ORM\Entity
 */
class Typology
{
    /**
     * @var integer $id
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var boolean $active
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(name="service", type="json_array", nullable=true)
     */
    private $service;

    /* Construct */
    public function __construct()
    {
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function setActive($active)
    {
        $this->active = $active;
    }

    public function getActive()
    {
        return $this->active;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
    
    public function setService($service)
    {
        $this->service = $service;

        return $this;
    }

    public function getService()
    {
        return $this->service;
    }

    public function __toString()
    {
        return $this->getName();
    }

}
