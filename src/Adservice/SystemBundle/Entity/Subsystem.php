<?php

namespace Adservice\SystemBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adservice\SystemBundle\Entity\Subsystem
 *
 * @ORM\Table(name="subsystem")
 * @ORM\Entity
 */
class Subsystem
{
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
     * @var string $system
     *
     * @ORM\ManyToOne(targetEntity="Adservice\SystemBundle\Entity\System")
     */
    private $system;


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
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set system
     *
     * @param string $system
     */
    public function setSystem(\Adservice\SystemBundle\Entity\System $system)
    {
        $this->system = $system;
    }

    /**
     * Get system
     *
     * @return string 
     */
    public function getSystem()
    {
        return $this->system;
    }
}