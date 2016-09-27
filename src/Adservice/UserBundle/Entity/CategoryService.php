<?php

namespace Adservice\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adservice\UserBundle\Entity\CategoryService
 *
 * @ORM\Table(name="category_service")
 * @ORM\Entity
 */
class CategoryService
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
     * @var string $category_service
     *
     * @ORM\Column(name="category_service", type="string", length=255)
     */
    private $category_service;

    /**
     * @var string $slug
     *
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;


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
     * Set category_service
     *
     * @param string $category_service
     */
    public function setCategoryService($category_service)
    {
        $this->category_service = $category_service;
    }

    /**
     * Get category_service
     *
     * @return string
     */
    public function getCategoryService()
    {
        return $this->category_service;
    }

    /**
     * Set slug
     *
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    public function __toString() {
        return $this->getCategoryService();
    }
}