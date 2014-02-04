<?php

namespace Adservice\UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adservice\UtilBundle\Entity\Language
 *
 * @ORM\Table(name="language")
 * @ORM\Entity
 */
class Language {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $language
     *
     * @ORM\Column(name="language", type="string", length=255)
     */
    private $language;

    /**
     * @var string $short_name
     *
     * @ORM\Column(name="short_name", type="string", length=5)
     */
    private $short_name;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set language
     *
     * @param string $language
     */
    public function setLanguage($language) {
        $this->language = $language;
    }

    /**
     * Get language
     *
     * @return string 
     */
    public function getLanguage() {
        return $this->language;
    }

    /**
     * Set language
     *
     * @param string $shortName
     */
    public function setShortName($short_name) {
        $this->short_name = $short_name;
    }

    /**
     * Get language
     *
     * @return string 
     */
    public function getShortName() {
        return $this->short_name;
    }

    public function __toString() {
        return $this->getLanguage();
    }

}
