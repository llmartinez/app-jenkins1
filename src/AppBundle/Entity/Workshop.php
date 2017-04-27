<?php
namespace AppBundle\Entity;
 
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
 
/**
 * @ORM\Table(name="workshop")
 * @ORM\Entity
 */
class Workshop
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Partner")
     * @ORM\JoinColumn(name="partner_id", referencedColumnName="id")
     */
    private $partner;

    /**
     * @ORM\OneToOne(targetEntity="User", inversedBy="workshop")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    
    /**
     * @ORM\Column(type="integer", length=11)
     * @Assert\NotBlank()
     */
    private $codeWorkshop;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;
    
    /**
     * @ORM\ManyToOne(targetEntity="Typology")
     * @ORM\JoinColumn(name="typology_id", referencedColumnName="id")
     */
    private $typology;
    
    /**
     * @ORM\ManyToOne(targetEntity="Shop")
     * @ORM\JoinColumn(name="shop_id", referencedColumnName="id", nullable=true)
     */
    private $shop;
    
    /**
     * @ORM\ManyToMany(targetEntity="DiagnosisMachine")
     * @ORM\JoinColumn(name="diagnosis_machine_id", referencedColumnName="id", nullable=true)
     */
    private $diagnosisMachine;
 

    /* Construct */
    public function __construct($partner=null)
    {
        $this->partner = $partner;
        $this->diagnosisMachine = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setPartner($partner)
    {
        $this->partner = $partner;

        return $this;
    }

    public function getPartner()
    {
        return $this->partner;
    }

    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getCodeWorkshop()
    {
        return $this->codeWorkshop;
    }

    public function setCodeWorkshop($codeWorkshop)
    {
        $this->codeWorkshop = $codeWorkshop;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setTypology($typology)
    {
        $this->typology = $typology;

        return $this;
    }

    public function getTypology()
    {
        return $this->typology;
    }

    public function setShop($shop)
    {
        $this->shop = $shop;

        return $this;
    }

    public function getShop()
    {
        return $this->shop;
    }

    public function setDiagnosisMachine($diagnosisMachine)
    {
        $this->diagnosisMachine = $diagnosisMachine;

        return $this;
    }

    public function getDiagnosisMachine()
    {
        return $this->diagnosisMachine;
    }

    public function __toString()
    {
        return $this->getPartner()->getCodePartner().' - '.$this->getCodeWorkshop().' '.$this->getName();
    }

}
?>