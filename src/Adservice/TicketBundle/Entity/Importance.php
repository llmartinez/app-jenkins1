<?php

namespace Adservice\TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adservice\TicketBundle\Entity\Importance
 *
 * @ORM\Table(name="importance")
 * @ORM\Entity
 */
class Importance
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
     * @var string $importance
     *
     * @ORM\Column(name="importance", type="string", length=255)
     */
    private $importance;


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
     * Set importance
     *
     * @param string $importance
     */
    public function setImportance($importance)
    {
        $this->importance = $importance;
    }

    /**
     * Get importance
     *
     * @return string
     */
    public function getImportance()
    {
        return $this->importance;
    }

    public function __toString() {
        return $this->getimportance();
    }
}