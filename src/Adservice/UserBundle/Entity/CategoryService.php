<?php

namespace Adservice\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @var string $dis
     *
     * @ORM\Column(name="dis", type="string", length=255, nullable=true)
     */
    private $dis;

    /**
     * @var string $vts
     *
     * @ORM\Column(name="vts", type="string", length=255, nullable=true)
     */
    private $vts;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\ManyToMany(targetEntity="SearchService")
     * @ORM\JoinTable(name="search_service_category_service",
     *     joinColumns={@ORM\JoinColumn(name="category_service_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="search_service_id", referencedColumnName="id")}
     * )
     */
    private $searchServices;

    public function __construct()
    {
        $this->searchServices = new ArrayCollection();
    }


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

    /**
     * Set dis
     *
     * @param string $dis
     */
    public function setDis($dis)
    {
        $this->dis = $dis;
    }

    /**
     * Get dis
     *
     * @return string
     */
    public function getDis()
    {
        return $this->dis;
    }

    /**
     * Set vts
     *
     * @param string $vts
     */
    public function setVts($vts)
    {
        $this->vts = $vts;
    }

    /**
     * Get vts
     *
     * @return string
     */
    public function getVts()
    {
        return $this->vts;
    }
    
    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return ArrayCollection
     */
    public function getSearchServices()
    {
        return $this->searchServices;
    }

    /**
     * @param ArrayCollection $searchServices
     */
    public function setSearchServices($searchServices)
    {
        $this->searchServices = $searchServices;
    }

    /**
     * @param SearchService $searchService
     */
    public function addSearchService(SearchService $searchService)
    {
        $this->searchServices[] = $searchService;
    }

    public function __toString() {
        return $this->getCategoryService();
    }
}